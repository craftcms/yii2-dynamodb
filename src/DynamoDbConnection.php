<?php

namespace pixelandtonic\dynamodb;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\DynamoDb\WriteRequestBatch;
use Aws\Result;
use Closure;
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
    public string $partitionKeyAttribute = 'pk';
    public ?string $sortKeyAttribute = null;
    public bool $consistentRead = true;
    public array $batchConfig = [];
    public ?DynamoDbClient $client = null;
    public string $version = 'latest';
    public string $ttlAttribute = 'ttl';
    public Marshaler $marshaler;
    public Closure|null $formatKey = null;

    /**
     * @var int|null Duration in seconds before an item will expire. Set to `null` for unlimited.
     */
    public ?int $ttl = null;

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

    /**
     * @param string|array $key The key of the item to retrieve
     * @return array|null The item, or `null` if not found
     * @throws DynamoDbException
     */
    public function getItem(string|array $key): ?array
    {
        $result = $this->client->getItem([
            'TableName'      => $this->tableName,
            'Key'            => $this->formatKey($key),
            'ConsistentRead' => $this->consistentRead,
        ]);

        $item = $result['Item'] ?? null;

        return $item ? $this->marshaler->unmarshalItem($item) : null;
    }


    /**
     * @param string|array $key The key of the item to update
     * @param array The item attributes to update
     * @return Result
     * @throws DynamoDbException
     */
    public function updateItem(string|array $key, array $item = []): Result
    {
        return $this->client->updateItem([
            'TableName'        => $this->tableName,
            'Key'              => $this->formatKey($key),
            'AttributeUpdates' => $this->_marshalAttributeValues($this->_addTtl($item)),
            'ReturnValues'     => 'ALL_NEW',
        ]);
    }

    /**
     * @param array $item The item attributes
     * @return Result
     * @throws DynamoDbException
     */
    public function putItem(array $item): Result
    {
        return $this->client->putItem([
            'TableName' => $this->tableName,
            'Item' => $this->marshaler->marshalItem($this->_addTtl($item)),
        ]);
    }

    /**
     * @param string|array $key The key of the item to delete
     * @return Result
     * @throws DynamoDbException
     */
    public function deleteItem(string|array $key): Result
    {
        return $this->client->deleteItem([
            'TableName' => $this->tableName,
            'Key'       => $this->formatKey($key),
        ]);
    }

    /**
     * @return WriteRequestBatch
     * @throws DynamoDbException
     */
    public function deleteExpired(): WriteRequestBatch
    {
        return $this->deleteMany([
            'ScanFilter' => [
                $this->ttlAttribute => [
                    'ComparisonOperator' => 'LT',
                    'AttributeValueList' => [
                        $this->marshaler->marshalValue(time()),
                    ],
                ],
            ]
        ]);
    }

    /**
     * @param array $attributes Attributes to pass to the scan operation
     * @return WriteRequestBatch
     * @throws DynamoDbException
     */
    public function deleteMany(array $attributes = []): WriteRequestBatch
    {
        $items = $this->client->getPaginator('Scan', [
            'TableName' => $this->tableName,
            'AttributesToGet' => array_filter([
                $this->partitionKeyAttribute,
                $this->sortKeyAttribute,
            ]),
        ] + $attributes);

        // Create a WriteRequestBatch for deleting the expired items
        $batch = new WriteRequestBatch($this->client, $this->batchConfig);

        // Perform Scan and BatchWriteItem (delete) operations as needed
        foreach ($items->search('Items') as $item) {
            $batch->delete(
                array_filter([
                    $this->partitionKeyAttribute => $item[$this->partitionKeyAttribute],
                    $this->sortKeyAttribute => $this->sortKeyAttribute ? $item[$this->sortKeyAttribute] : null,
                ]),
                $this->tableName,
            );
        }

        // Delete any remaining items that were not auto-flushed
        $batch->flush();

        return $batch;
    }

    protected function formatKey(string|array $key): array
    {
        if (is_callable($this->formatKey)) {
            $key = call_user_func($this->formatKey, $key);
        }

        if (is_array($key)) {
            $partitionKey = $key[$this->partitionKeyAttribute] ?? null;
            $sortKey = $key[$this->sortKeyAttribute] ?? null;
        } else {
            $partitionKey = $key;
            $sortKey = null;
        }

        if (!$partitionKey) {
            throw new InvalidArgumentException('A partition key is required.');
        }

        if ($sortKey && !$this->sortKeyAttribute) {
            throw new InvalidArgumentException('A sort key attribute must be defined to use compound keys.');
        }

        $value = [
            $this->partitionKeyAttribute => $partitionKey,
        ];

        if ($this->sortKeyAttribute) {
            if ($sortKey === null) {
                throw new InvalidArgumentException('A sort key attribute was specified, but no value was found.');
            }
            $value[$this->sortKeyAttribute] = $sortKey;
        }

        return $this->marshaler->marshalItem($value);
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

    protected function getCredentials(): Credentials|callable
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

    protected function getMarshaler(): Marshaler
    {
        return $this->marshaler ?? new Marshaler();
    }

    private function _marshalAttributeValues(array $values): array
    {
        return array_map(fn($value) => [
            'Value' => $this->marshaler->marshalValue($value),
        ], $values);
    }

    private function _addTtl(array $attributes): array
    {
        if ($this->ttl) {
            $attributes += [
                $this->ttlAttribute => time() + $this->ttl,
            ];
        }

        return $attributes;
    }
}
