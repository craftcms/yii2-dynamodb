<?php

namespace pixelandtonic\dynamodb\drivers;

use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\Session;

class DynamoDbSession extends Session
{
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
        return (bool) $this->dynamoDb->updateItem($id, [
            $this->dataAttribute => $data,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function destroySession($id): bool
    {
        return (bool) $this->dynamoDb->deleteItem($id);
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
