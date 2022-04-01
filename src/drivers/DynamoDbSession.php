<?php

namespace pixelandtonic\dynamodb\drivers;

use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\Session;

class DynamoDbSession extends Session
{
    /**
     * @var DynamoDBConnection|string|array the DynamoDB [[Connection]] object or the application component ID of the DynamoDB [[Connection]].
     * This can also be an array that is used to create a DynamoDB [[Connection]] instance in case you do not want to configure
     * a DynamoDB connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a DynamoDB [[Connection]] object.
     */
    public DynamoDBConnection|string|array $dynamoDb = 'dynamoDb';
    public string $dataAttribute = 'data';

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);

        parent::init();
    }

    /**
     * @inheritDoc
     */
    public function getUseCustomStorage(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function readSession($id): string
    {
        $item = $this->dynamoDb->getItem($id);

        return $item[$this->dataAttribute] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function writeSession($id, $data): bool
    {
        return $this->dynamoDb->updateItem($id, [
            $this->dataAttribute => $data,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function destroySession($id): bool
    {
        return $this->dynamoDb->deleteItem($id);
    }

    /**
     * @inheritDoc
     */
    public function gcSession($maxLifetime): bool
    {
        $this->dynamoDb->deleteExpired();

        return true;
    }
}
