<?php

namespace tests;

use pixelandtonic\dynamodb\drivers\DynamoDbCache;
use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use pixelandtonic\dynamodb\drivers\DynamoDbSession;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static function getDynamoDb(): DynamoDbConnection
    {
        return \Yii::$app->getDynamoDb();
    }

    protected static function getCache(): DynamoDbCache
    {
        return \Yii::$app->getCache();
    }

    protected static function getSession(): DynamoDbSession
    {
        return \Yii::$app->getSession();
    }

    protected static function getQueue(string $id = 'queue'): DynamoDbQueue
    {
        return \Yii::$app->get($id);
    }
}
