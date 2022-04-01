<?php

namespace pixelandtonic\dynamodb\drivers;

use Yii;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\di\Instance;

class DynamoDbCache extends Cache
{
    /**
     * @var DynamoDBConnection|string|array the DynamoDB [[Connection]] object or the application component ID of the DynamoDB [[Connection]].
     * This can also be an array that is used to create a DynamoDB [[Connection]] instance in case you do not want do configure
     * a DynamoDB connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a DynamoDB [[Connection]] object.
     */
    public DynamoDBConnection|string|array $dynamoDb = 'dynamoDb';

    public string $dataAttribute = 'data';

    /**
     * Initializes the DynamoDb Cache component.
     * This method will initialize the [[dynamoDb]] property to make sure it refers to a valid DynamoDb connection.
     * @throws InvalidConfigException if [[dynamoDb]] is invalid.
     */
    public function init(): void
    {
        parent::init();
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
    }

    // /**
    //  * @inheritDoc
    //  */
    // public function buildKey($key)
    // {
    //     return $this->
    // }

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
