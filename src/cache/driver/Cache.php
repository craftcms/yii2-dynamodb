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
        // TODO: Implement buildKey() method.
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * @inheritDoc
     */
    public function exists($key)
    {
        // TODO: Implement exists() method.
    }

    /**
     * @inheritDoc
     */
    public function multiGet($keys)
    {
        // TODO: Implement multiGet() method.
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $duration = null, $dependency = null)
    {
        // TODO: Implement set() method.
    }

    /**
     * @inheritDoc
     */
    public function multiSet($items, $duration = 0, $dependency = null)
    {
        // TODO: Implement multiSet() method.
    }

    /**
     * @inheritDoc
     */
    public function add($key, $value, $duration = 0, $dependency = null)
    {
        // TODO: Implement add() method.
    }

    /**
     * @inheritDoc
     */
    public function multiAdd($items, $duration = 0, $dependency = null)
    {
        // TODO: Implement multiAdd() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        // TODO: Implement flush() method.
    }

    /**
     * @inheritDoc
     */
    public function getOrSet($key, $callable, $duration = null, $dependency = null)
    {
        // TODO: Implement getOrSet() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}
