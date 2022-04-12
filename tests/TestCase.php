<?php

namespace tests;

use pixelandtonic\dynamodb\DynamoDbQueue;
use Yii;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\web\Session;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static function getCache(): CacheInterface
    {
        return Yii::$app->getCache();
    }

    protected static function getSession(): Session
    {
        return Yii::$app->getSession();
    }

    /**
     * @throws InvalidConfigException
     */
    protected static function getQueue(string $id = 'queue'): DynamoDbQueue
    {
        return Yii::$app->get($id);
    }
}
