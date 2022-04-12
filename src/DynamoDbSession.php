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
     * @var bool Whether to allow garbage collection. Most often this should be false,
     *           and DynamoDB's ttl settings should be used.
     */
    public bool $allowGc = false;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
        $this->setTimeout($this->getTimeout());
    }

    public function setTimeout($value): void
    {
        parent::setTimeout($value);

        // Prevent premature set from constructor
        if ($this->dynamoDb instanceof DynamoDbConnection) {
            $this->dynamoDb->ttl = $value;
        }
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
        if ($this->allowGc) {
            $this->dynamoDb->deleteExpired();
        }

        return true;
    }
}
