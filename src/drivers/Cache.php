<?php

namespace pixelandtonic\dynamodb\cache\driver;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use yii\caching\CacheInterface;

class Cache implements CacheInterface
{
    /**
     * DynamoDB table name to use for the cache.
     *
     * @var string
     */
    public $tableName = 'cache-table-test';

    /**
     * AWS access key.
     * @var string|null
     */
    public $key;

    /**
     * AWS secret.
     * @var string|null
     */
    public $secret;

    /**
     * region where queue is hosted.
     * @var string
     */
    public $region = '';

    /**
     * API version.
     * @var string
     */
    public $version = 'latest';

    /**
     * DynamoDB client use for making requests.
     *
     * @var DynamoDbClient
     */
    protected $client;

    /**
     * Cache constructor.
     * @param DynamoDbClient $client
     */
    public function __construct(DynamoDbClient $client)
    {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function buildKey($key)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        try {
            $result = $this->client->getItem([
                'ConsistentRead' => true,
                'TableName' => $this->tableName,
                'Key' => [
                    'key' => ['S' => $key],
                ]
            ]);
        } catch (DynamoDbException $e) {
            // TODO log the exception
        }

        return $result['Item']['value']['S'];
    }

    /**
     * @inheritDoc
     */
    public function exists($key)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function multiGet($keys)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $duration = null, $dependency = null)
    {
        if ($duration) {
            throw new \RuntimeException('duration is not currently supported by this driver');
        }

        try {
            $this->client->putItem([
                'TableName' => $this->tableName,
                'Item' => [
                    'key' => ['S' => $key],
                    'value' => ['S' => json_encode($value)],
                ]
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function multiSet($items, $duration = 0, $dependency = null)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function add($key, $value, $duration = 0, $dependency = null)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function multiAdd($items, $duration = 0, $dependency = null)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function getOrSet($key, $callable, $duration = null, $dependency = null)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('not yet implemented');
    }
}
