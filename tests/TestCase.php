<?php

namespace tests;

use yii\helpers\ArrayHelper;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $config = require __DIR__ . '/config/app.php';

        $this->mockApplication($config);

        parent::setUp();
    }

    /**
     * Mocks web application
     *
     * @param array $config
     * @param string $appClass
     */
    protected function mockApplication(array $config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'yii2-dynamodb-test-app',
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ]
        ], $config));
    }

    protected function getCache()
    {
        return \Yii::$app->getCache();
    }

    protected function getSession()
    {
        return \Yii::$app->getSession();
    }
}
