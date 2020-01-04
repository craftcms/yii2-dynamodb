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
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
        ],
        'session' => [
            'class' => \pixelandtonic\dynamodb\drivers\Session::class,
            'table' => 'session-test',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
        ],
        'queue' => [
            'class' => \pixelandtonic\dynamodb\drivers\Queue::class,
            'table' => 'queue-test',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
            'prefix' => 'queue-prefix-',
        ],
        'request' => [
            'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
];
