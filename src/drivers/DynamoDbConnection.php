<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\WriteRequestBatch;
use Closure;
use Exception;
use Yii;
use yii\base\Component;

class DynamoDbConnection extends Component
{
    /** @var string Name of table */
    public string $tableName;

    /** @var string Name of hash key in table. Default: "id" */
    public string $hashKey = 'id';

    public ?string $sortKey = null;

    /** @var string Name of the data attribute in table. Default: "data" */
    public string $dataAttribute = 'data';

    /** @var string Type of the data attribute in table. Possible values: "S", "B", "N". Default: "S" */
    public string $dataAttributeType = 'S';

    /** @var bool Whether to use consistent reads */
    public bool $consistentRead = true;

    /** @var array Batch options used for garbage collection */
    public array $batchConfig = [];

    /** @var integer Max time (s) to wait for lock acquisition */
    public int $maxLockWaitTime = 10;

    /** @var integer Min time (µs) to wait between lock attempts */
    public int $minLockRetryMicrotime = 10000;

    /** @var integer Max time (µs) to wait between lock attempts */
    public int $maxLockRetryMicrotime = 50000;

    public ?string $endpoint = null;
    public ?DynamoDbClient $client = null;
    public string $version = 'latest';
    public ?int $ttl = null;
    public string $ttlAttribute = 'ttl';
    public ?string $keyPrefix = null;

    private ?string $_region = null;
    private $_credentials;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();
        $this->client = $this->getClient();
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

            // useful for using DAX, local dev
            'endpoint' => $this->endpoint,
        ]);

        $this->client = new DynamoDbClient($config);

        return $this->client;
    }

    /**
     * @throws Exception
     */
    public function read($key, $locking = false): ?array
    {
        if ($locking) {
            return $this->_readWithPessimisticLock($key);
        }

        try {
            // Execute a GetItem command to retrieve the item.
            $result = $this->client->getItem([
                'TableName'      => $this->tableName,
                'Key'            => $this->formatKey($key),
                'ConsistentRead' => $this->consistentRead,
            ]);

            // Get the item values
            return  $result['Item'] ?? [];
        } catch (DynamoDbException $e) {

            // TODO: why?
            return null;
        }
    }

    /**
     * @param string $key
     * @param null|mixed $data The data to write to the data attribute. If `null`, no data will be written,
     *                         but ttl will still be updated.
     * @return bool Whether write was successful
     */
    public function write(string $key, mixed $data): bool
    {
        $attributes = [
            'lock' => ['Action' => 'DELETE'],
        ];

        if ($this->ttl) {
            $attributes[$this->ttlAttribute] = time() + $this->ttl;
        }

        if ($data !== null) {
            if ($data) {
                $attributes[$this->dataAttribute] = [
                    'Value' => [
                        $this->dataAttributeType => $data
                    ]
                ];
            } else {
                $attributes[$this->dataAttribute] = ['Action' => 'DELETE'];
            }
        }

        try {
            return (bool) $this->client->updateItem([
                'TableName'        => $this->tableName,
                'Key'              => $this->formatKey($key),
                'AttributeUpdates' => $attributes,
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Error writing “{$key}”: {$e->getMessage()}");
            return false;
        }
    }

    public function delete($key): bool
    {
        try {
            return (bool) $this->client->deleteItem([
                'TableName' => $this->tableName,
                'Key'       => $this->formatKey($key),
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Error deleting “{$key}”: {$e->getMessage()}");
            return false;
        }
    }

    public function deleteExpired(): void
    {
        // Create a Scan iterator for finding expired items
        $scan = $this->client->getPaginator('Scan', [
            'TableName' => $this->tableName,
            'AttributesToGet' => [$this->hashKey],
            'ScanFilter' => [
                $this->ttlAttribute => [
                    'ComparisonOperator' => 'LT',
                    'AttributeValueList' => [['N' => (string) time()]],
                ],
                'lock' => [
                    'ComparisonOperator' => 'NULL',
                ]
            ],
        ]);

        // Create a WriteRequestBatch for deleting the expired items
        $batch = new WriteRequestBatch($this->client, $this->batchConfig);

        // Perform Scan and BatchWriteItem (delete) operations as needed
        foreach ($scan->search('Items') as $item) {
            $batch->delete(
                [$this->hashKey => $item[$this->hashKey]],
                $this->tableName
            );
        }

        // Delete any remaining items that were not auto-flushed
        $batch->flush();
    }

    protected function formatKey(string $key): array
    {
        return [
            $this->hashKey => ['S' => "{$this->keyPrefix}$key"]
        ];
    }

    /**
     * @throws Exception
     */
    private function _readWithPessimisticLock(string $key): ?array
    {
        // Create the params for the UpdateItem operation so that a lock can be
        // set and item returned (via ReturnValues) in a one, atomic operation.
        $params = [
            'TableName'        => $this->tableName,
            'Key'              => $this->formatKey($key),
            'Expected'         => ['lock' => ['Exists' => false]],
            'AttributeUpdates' => ['lock' => ['Value' => ['N' => '1']]],
            'ReturnValues'     => 'ALL_NEW',
        ];

        // Acquire the lock and fetch the item data.
        $timeout  = time() + $this->maxLockWaitTime;
        while (true) {
            try {
                $result = $this->client->updateItem($params);
                return $result['Attributes'] ?? null;
            } catch (DynamoDbException $e) {
                if (
                    $e->getAwsErrorCode() === 'ConditionalCheckFailedException' &&
                    time() < $timeout
                ) {
                    usleep(random_int(
                        $this->minLockRetryMicrotime,
                        $this->maxLockRetryMicrotime
                    ));
                } else {
                    break;
                }
            }
        }

        return null;
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
}
