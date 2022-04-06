<?php
use pixelandtonic\dynamodb\drivers\DynamoDbCache;
use pixelandtonic\dynamodb\drivers\DynamoDbConnection;
use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use pixelandtonic\dynamodb\drivers\DynamoDbSession;

$dynamoDbConfig = [
    'class' => DynamoDbConnection::class,
    'endpoint' => 'http://localhost:8000',
    'region' => 'local',
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
                'ttl' => 60,
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
            'dynamoDb' => [
                'ttl' => 60,
                'tableName' => 'session-test',
                'partitionKeyAttribute' => 'id',
            ] + $dynamoDbConfig,
        ],
        'queue' => [
            'class' => DynamoDbQueue::class,
            'dynamoDb' => [
                'tableName' => 'queue-test',
                'formatKey' => static fn($key) => "queue-prefix#$key"
            ] + $dynamoDbConfig,
        ],
        'request' => [
            'cookieValidationKey' => 'dipUyxo0rv924WuhjmEk',
            'scriptFile' => __DIR__ . '/index.php',
            'scriptUrl' => '/index.php',
        ],
    ],
];
