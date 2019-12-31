<?php

namespace mccallister\dynamodb\cache\driver;

use Aws\DynamoDb\DynamoDbClient;
use mccallister\dynamodb\helpers\DynamoDBHelper;
use yii\caching\CacheInterface;

class Cache implements CacheInterface
{
    /**
     * DynamoDB table name to use for the cache.
     *
     * @var string
     */
    public $tableName;

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

    public function init()
    {
        $this->client = (new DynamoDBHelper())->getClient();

        parent::init();
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
        throw new \Exception('not yet implemented');
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
        throw new \Exception('not yet implemented');
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
