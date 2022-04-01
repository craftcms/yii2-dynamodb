<?php

namespace pixelandtonic\dynamodb\drivers;

use Aws\DynamoDb\SessionConnectionConfigTrait;
use Aws\DynamoDb\SessionHandler;
use yii\di\Instance;
use yii\web\Session;

class DynamoDbSession extends Session
{
    use SessionConnectionConfigTrait;

    /**
     * @var DynamoDBConnection|string|array the DynamoDB [[Connection]] object or the application component ID of the DynamoDB [[Connection]].
     * This can also be an array that is used to create a DynamoDB [[Connection]] instance in case you do not want do configure
     * a DynamoDB connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a DynamoDB [[Connection]] object.
     */
    public $dynamoDb = 'dynamoDb';

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        $this->dynamoDb = Instance::ensure($this->dynamoDb, DynamoDbConnection::class);
        $this->handler = SessionHandler::fromClient($this->dynamoDb->client, [
            // 'session_lifetime' => 30,
        ]);
        parent::init();
    }

    public function getUseCustomStorage(): bool
    {
        return true;
    }
}
