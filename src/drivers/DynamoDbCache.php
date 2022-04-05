<?php

namespace pixelandtonic\dynamodb\drivers;

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

        if ($this->keyPrefix) {
            throw new InvalidConfigException('The `keyPrefix` property is not implemented. Use `DynamoDbConnection::$formatKey` instead.');
        }
    }

    /**
     * @inheritDoc
     */
    protected function getValue($key)
    {
        $result = $this->dynamoDb->getItem($key);

        return $result[$this->dataAttribute] ?? null;
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

        return $this->dynamoDb->updateItem($key, $data);
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
        return $this->dynamoDb->deleteItem($key);
    }

    /**
     * @inheritDoc
     */
    protected function flushValues(): bool
    {
        Yii::error('Flush operations are not supported.');
        return false;
    }
}
