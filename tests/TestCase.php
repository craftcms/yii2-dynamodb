<?php

namespace tests;

use pixelandtonic\dynamodb\drivers\DynamoDbCache;
use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use pixelandtonic\dynamodb\drivers\DynamoDbSession;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getDynamoDb(): DynamoDbConnection
    {
        return \Yii::$app->getDynamoDb();
    }

    protected function getCache(): DynamoDbCache
    {
        return \Yii::$app->getCache();
    }

    protected function getSession(): DynamoDbSession
    {
        return \Yii::$app->getSession();
    }

    protected function getQueue(string $id = 'queue'): DynamoDbQueue
    {
        return \Yii::$app->get($id);
    }
}
