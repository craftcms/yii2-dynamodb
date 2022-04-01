<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\WriteRequestBatch;
use JetBrains\PhpStorm\Pure;
use Yii;
use yii\base\Component;
use yii\base\InvalidArgumentException;

/**
 *
 * @property callable $credentials
 * @property null|string $region
 */
class DynamoDbConnection extends Component
{
    public string $tableName;
    public string $partitionKey = 'id';
    public ?string $partitionKeyPrefix = null;
    public ?string $sortKey = null;
    public bool $consistentRead = true;
    public array $batchConfig = [];
    public ?DynamoDbClient $client = null;
    public string $version = 'latest';
    public ?int $ttl = null;
    public string $ttlAttribute = 'ttl';
    public Marshaler $marshaler;

    /**
     * @var string|null $endpoint Useful for using DAX, local dev
     */
    public ?string $endpoint = null;

    private ?string $_region = null;
    private $_credentials;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        $this->marshaler = $this->getMarshaler();
        $this->client = $this->getClient();
    }

    public function getItem($key): ?array
    {
        try {
            $result = $this->client->getItem([
                'TableName'      => $this->tableName,
                'Key'            => $this->formatKey($key),
                'ConsistentRead' => $this->consistentRead,
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to get item: {$e->getMessage()}");
            return null;
        }

        $item = $result['Item'] ?? null;

        return $item ? $this->marshaler->unmarshalItem($item) : null;
    }

    public function updateItem($key, $data = null): bool
    {
        $attributes = [];

        if ($data) {
            $attributes += $data;
        }

        if ($this->ttl) {
            $attributes += [
                $this->ttlAttribute => time() + $this->ttl,
            ];
        }

        try {
            return (bool) $this->client->updateItem([
                'TableName'        => $this->tableName,
                'Key'              => $this->formatKey($key),
                'AttributeUpdates' => $this->_marshalAttributeValues($attributes),
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to update item: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteItem($key): bool
    {
        try {
            return (bool) $this->client->deleteItem([
                'TableName' => $this->tableName,
                'Key'       => $this->formatKey($key),
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to delete item: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteExpired(): void
    {
        $scan = $this->client->getPaginator('Scan', [
            'TableName' => $this->tableName,
            'AttributesToGet' => [$this->partitionKey],
            'ScanFilter' => [
                $this->ttlAttribute => [
                    'ComparisonOperator' => 'LT',
                    'AttributeValueList' => [
                        $this->marshaler->marshalValue(time()),
                    ],
                ],
            ],
        ]);

        // Create a WriteRequestBatch for deleting the expired items
        $batch = new WriteRequestBatch($this->client, $this->batchConfig);

        // Perform Scan and BatchWriteItem (delete) operations as needed
        foreach ($scan->search('Items') as $item) {
            $batch->delete(
                [$this->partitionKey => $item[$this->partitionKey]],
                $this->tableName,
            );
        }

        // Delete any remaining items that were not auto-flushed
        $batch->flush();
    }

    protected function formatKey($key): array
    {
        $sortKey = null;

        if (is_array($key)) {
            if (!$this->sortKey) {
                throw new InvalidArgumentException('A sort key must be provided to use compound keys.');
            }

            if (count($key) !== 2) {
                throw new InvalidArgumentException('Compound keys must be an array with exactly 2 elements.');
            }

            [$partitionKey, $sortKey] = $key;
        } else {
            $partitionKey = $key;
        }

        $partitionKey = "{$this->partitionKeyPrefix}$partitionKey";
        $keyValue = [
            $this->partitionKey => $partitionKey,
        ];

        if ($sortKey && $this->sortKey) {
            $keyValue[$this->sortKey] = $sortKey;
        }

        return $this->marshaler->marshalItem($keyValue);
    }

    protected function getClient(): DynamoDbClient
    {
        if ($this->client) {
            return $this->client;
        }

        $config = array_filter([
            'credentials' => $this->getCredentials(),
            'region' => $this->getRegion(),
            'version' => $this->version,
            'endpoint' => $this->endpoint,
        ]);

        $this->client = new DynamoDbClient($config);

        return $this->client;
    }

    protected function getCredentials(): callable
    {
        return $this->_credentials ?? CredentialProvider::defaultProvider();
    }

    protected function setCredentials($credentials): void
    {
        if (is_array($credentials)) {
            $credentials = new Credentials(
                $credentials['key'],
                $credentials['secret'],
                $credentials['token'] ?? null,
                $credentials['expires'] ?? null,
            );
        }

        $this->_credentials = $credentials;
    }

    protected function setRegion(string $region): void
    {
        $this->_region = $region;
    }

    protected function getRegion(): ?string
    {
        return $this->_region ?? (getenv('AWS_REGION') ?: null) ?? (getenv('AWS_DEFAULT_REGION') ?: null);
    }

    #[Pure] protected function getMarshaler(): Marshaler
    {
        return $this->marshaler ?? new Marshaler();
    }

    private function _marshalAttributeValues(array $values): array
    {
        return array_map(fn($value) => [
            'Value' => $this->marshaler->marshalValue($value),
        ], $values);
    }
}
