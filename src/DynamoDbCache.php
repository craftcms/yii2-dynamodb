<?php

namespace pixelandtonic\dynamodb;

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

        if ($this->keyPrefix) {
            throw new InvalidConfigException('The `keyPrefix` property is not supported. Use `DynamoDbConnection::$formatKey` instead.');
        }
    }

    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        $result = $this->dynamoDb->getItem($key);

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
            $data[$this->dynamoDb->ttlAttribute] = $duration;
        }

        return (bool) $this->dynamoDb->updateItem($key, $data);
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
        return (bool) $this->dynamoDb->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    protected function flushValues(): bool
    {
        $this->dynamoDb->deleteMany();

        return true;
    }
}
