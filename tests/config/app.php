<?php
use pixelandtonic\dynamodb\DynamoDbCache;
use pixelandtonic\dynamodb\DynamoDbConnection;
use pixelandtonic\dynamodb\DynamoDbQueue;
use pixelandtonic\dynamodb\DynamoDbSession;

$dynamoDbConfig = [
    'class' => DynamoDbConnection::class,
    'endpoint' => 'http://localhost:8000',
    'region' => 'local',
    'ttl' => 1,
    'credentials' => [
        'key' => 'local',
        'secret' => 'local',
    ]
];

return [
    'id' => 'yii2-dynamodb-test-app',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'runtimePath' => dirname(__DIR__, 2) . '/runtime',
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'dynamoDb' => $dynamoDbConfig,
        'cache' => [
            'class' => DynamoDbCache::class,
            'dynamoDb' => [
                'tableName' => 'cache-test',
                'sortKeyAttribute' => 'sk',
                'formatKey' => static function($key) {
                    return [
                        'pk' => substr(md5(Yii::$app->id), 0, 5),
                        'sk' => $key,
                    ];
                }
            ] + $dynamoDbConfig,
        ],
        'session' => [
            'class' => DynamoDbSession::class,
            'timeout' => 1,
            'allowGc' => true,
            'dynamoDb' => [
                'tableName' => 'session-test',
                'partitionKeyAttribute' => 'id',
            ] + $dynamoDbConfig,
        ],
        'queue' => [
            'class' => DynamoDbQueue::class,
            'dataAttribute' => 'job',
            'dynamoDb' => [
                'tableName' => 'queue-test',
                'formatKey' => static fn($key) => "queue-prefix#$key",
                'ttl' => null,
            ] + $dynamoDbConfig,
        ],
        'request' => [
            'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
];
