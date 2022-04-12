<?php

namespace pixelandtonic\dynamodb;

use Yii;
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
        $this->dynamoDb->tableName = $this->dynamoDb->tableName ?? 'sessions';
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
        try {
            $item = $this->dynamoDb->getItem($id);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to read session: {$e->getMessage()}", __METHOD__);
            return '';
        }

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
        try {
            $this->dynamoDb->updateItem($id, [
                $this->dataAttribute => $data,
            ]);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to write session session: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroySession($id): bool
    {
        try {
            $this->dynamoDb->deleteItem($id);
        } catch (DynamoDbException $e) {
            Yii::error("Unable to destroy expired session: {$e->getMessage()}", __METHOD__);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function gcSession($maxLifetime): bool
    {
        if ($this->allowGc) {
            try {
                $this->dynamoDb->deleteExpired();
            } catch (DynamoDbException $e) {
                Yii::error("Unable to delete expired sessions: {$e->getMessage()}", __METHOD__);
                return false;
            }
        }

        return true;
    }
}
