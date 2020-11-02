<?php

return [
    'id' => 'yii2-dynamodb-test-app',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'runtimePath' => dirname(__DIR__, 2) . '/runtime',
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'cache' => [
            'class' => \pixelandtonic\dynamodb\drivers\DynamoDbCache::class,
            'table' => 'cache-test',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
        ],
        'session' => [
            'class' => \pixelandtonic\dynamodb\drivers\DynamoDbSession::class,
            'table' => 'session-test',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
        ],
        'queue' => [
            'class' => \pixelandtonic\dynamodb\drivers\DynamoDbQueue::class,
            'table' => 'queue-test',
            'key' => 'local',
            'secret' => 'local',
            'region' => 'local',
            'endpoint' => 'http://localhost:8000',
            'keyPrefix' => 'queue-prefix:',
        ],
        'request' => [
            'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
];
