<?php

namespace pixelandtonic\dynamodb;

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
        parent::init();
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
        $iniTimeout = $this->getTimeout();
        $this->setTimeout($iniTimeout);
    }

    public function setTimeout($value): void
    {
        parent::setTimeout($value);
        $this->dynamoDb->ttl = $value;
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
        $data = $item[$this->dataAttribute] ?? '';
        $ttl = $item[$this->dynamoDb->ttlAttribute] ?? null;

        if ($ttl && $ttl <= time()) {
            $this->destroySession($id);

            return '';
        }

        return $data;
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
