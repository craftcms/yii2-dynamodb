<?php

namespace tests;

use yii\caching\CacheInterface;
use yii\web\Session;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getCache(): CacheInterface
    {
        return \Yii::$app->getCache();
    }

    protected function getSession(): Session
    {
        return \Yii::$app->getSession();
    }

    protected function getQueue(string $id = 'queue')
    {
        return \Yii::$app->get($id);
    }
}
