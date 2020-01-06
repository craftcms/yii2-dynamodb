<?php

namespace tests;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getCache()
    {
        return \Yii::$app->getCache();
    }

    protected function getSession()
    {
        return \Yii::$app->getSession();
    }

    protected function getQueue(string $id = 'queue')
    {
        return \Yii::$app->get($id);
    }
}
