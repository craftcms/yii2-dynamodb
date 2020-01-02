<?php

return [
    'id' => 'yii2-dynamodb-test-app',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'runtimePath' => dirname(dirname(__DIR__)) . '/runtime',
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'cache' => [
            'class' => \pixelandtonic\dynamodb\drivers\Cache::class,
            'table' => 'cache-test',
            'tableKeyAttribute' => 'key',
            'tableValueAttribute' => 'value',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
        ],
        'request' => [
            'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
];
