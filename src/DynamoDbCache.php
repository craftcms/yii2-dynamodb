<?php

namespace pixelandtonic\dynamodb;

use Aws\DynamoDb\Exception\DynamoDbException;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;

/**
 * @property mixed $value
 */
class DynamoDbCache extends Cache
{
    public DynamoDBConnection|string|array $dynamoDb = 'dynamoDb';
    public string $dataAttribute = 'data';

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
        $this->dynamoDb->tableName = $this->dynamoDb->tableName ?? 'cache';

        if ($this->keyPrefix) {
            throw new InvalidConfigException('The `keyPrefix` property is not supported. Use `DynamoDbConnection::$formatKey` instead.');
        }
    }

    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        try {
            $result = $this->dynamoDb->getItem($key);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to get cache value: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return $result[$this->dataAttribute] ?? false;
    }

    /**
     * @inheritDoc
     */
    protected function setValue($key, $value, $duration): bool
    {
        $data = [
            $this->dataAttribute => $value,
        ];

        if ($duration) {
            $data[$this->dynamoDb->ttlAttribute] = $duration + time();
        }

        try {
            $this->dynamoDb->updateItem($key, $data);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to set cache value: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function addValue($key, $value, $duration): bool
    {
        return $this->setValue($key, $value, $duration);
    }

    /**
     * @inheritDoc
     */
    protected function deleteValue($key): bool
    {
        try {
            $this->dynamoDb->deleteItem($key);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to delete cache value: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function flushValues(): bool
    {
        try {
            $this->dynamoDb->deleteMany();
        } catch (DynamoDbException $e) {
            Yii::error("Unable to flush values: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return true;
    }
}
