<?php

namespace tests;

use yii\helpers\ArrayHelper;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $config = require __DIR__ . '/config/app.php';

        $this->mockWebApplication($config);

        parent::setUp();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication(array $config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'yii2-dynamodb-test-app',
            'basePath' => __DIR__,
            'vendorPath' => dirname(__DIR__) . '/vendor',
        ], $config));
    }

    /**
     * Mocks web application
     *
     * @param array $config
     * @param string $appClass
     */
    protected function mockWebApplication(array $config = [], $appClass = '\yii\web\Application')
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

    protected function getClient()
    {
        return \Yii::$app->getCache();
    }
}
