# DynamoDB Cache, Session, and Queue for Yii 2

[![Latest Version on Packagist](https://img.shields.io/packagist/v/craftcms/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/craftcms/yii2-dynamodb)
[![Total Downloads](https://img.shields.io/packagist/dt/craftcms/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/craftcms/yii2-dynamodb)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/craftcms/yii2-dynamodb/run-tests?label=tests)](https://github.com/pixelandtonic/yii2-dynamodb/actions?query=workflow%3Aci+branch%3Amaster)

Easily use DynamoDB as a [cache](https://www.yiiframework.com/doc/guide/2.0/en/caching-overview), [session](https://www.yiiframework.com/doc/guide/2.0/en/runtime-sessions-cookies), or [queue](https://github.com/yiisoft/yii2-queue) using this library in your Yii2 or Craft CMS projects.

> Note: currently Craft supports Yii2 Queue version 2.3.0 so this package is based on version 2.3.0 of yii2-queue.

## Installation

You can install the package via composer:

```bash
composer require craftcms/yii2-dynamodb
```

## Usage

This package provides three Yii components for DynamoDB: cache, session, and queue.

### Cache Component

#### Create DynamoDB Cache Table

Since DynamoDB is a NoSQL database, the only key you have to specify is the primary key. You can use the following AWS CLI command to generate a table for the cache.

```shell script
aws dynamodb create-table --table-name=my-app-cache-table \
    --attribute-definitions=AttributeName=id,AttributeType=S \
    --key-schema=AttributeName=id,KeyType=HASH \
    --billing-mode=PAY_PER_REQUEST
```

> Note: Since the ID can contain more than numbers, it needs to be specified as a string in DynamoDB.

#### Configure Cache Component

In your `app.php`, configure the `cache` component to use the driver.

```php
use craftcms\dynamodb\DynamoDbCache;

return [
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'cache' => [
            'class' => DynamoDbCache::class,
            'dataAttribute' => 'data', // optional: defaults to data
            'dynamoDb' => [
                'table' => 'my-app-cache-table',
                'partitionKeyAttribute' => 'id', // optional: defaults to 'PK'
                'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
                'region' => '<region>', // optional: defaults to AWS_REGION env var
                'ttl' => 60*60*24, // optional: number of seconds until items are considered expired
                'ttlAttribute' => 'expires' // optional: defaults to 'TTL'
                'credentials' => [
                    'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
                    'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
                ],
            ],
        ],
    ],
];
```

### Session Component

#### Create DynamoDB Session Table

Since DynamoDB is a NoSQL database, the only key you have to specify is the primary key. You can use the following AWS CLI command to generate a table for the session.

```shell script
aws dynamodb create-table --table-name=my-app-session-table \
    --attribute-definitions=AttributeName=id,AttributeType=S \
    --key-schema=AttributeName=id,KeyType=HASH \
    --billing-mode=PAY_PER_REQUEST
```

> Note: Since the ID can contain more than numbers, it needs to be specified as a string in DynamoDB.

#### Configure Session Component

In your `app.php`, configure the `session` component to use the driver.

```php
use craftcms\dynamodb\DynamoDbSession;

return [
    'bootstrap' => [
        'session',
    ],
    'components' => [
        'session' => [
            'class' => DynamoDbSession::class,
            'dataAttribute' => 'data', // optional: defaults to data
            'dynamoDb' => [
                'table' => 'my-app-session-table',
                'partitionKeyAttribute' => 'id', // optional: defaults to 'PK'
                'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
                'region' => '<region>', // optional: defaults to AWS_REGION env var
                'ttl' => 60*60*24, // optional: number of seconds until items are considered expired
                'ttlAttribute' => 'expires' // optional: defaults to 'TTL'
                'credentials' => [
                    'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
                    'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
                ],
            ],
        ],
    ],
];
```

### Queue Component

#### Create DynamoDB Queue Table

Since DynamoDB is a NoSQL database, the only key you have to specify is the primary key. You can use the following AWS CLI command to generate a table for the queue.

```shell script
aws dynamodb create-table --table-name=my-app-queue-table \
    --attribute-definitions=AttributeName=id,AttributeType=S \
    --key-schema=AttributeName=id,KeyType=HASH \
    --billing-mode=PAY_PER_REQUEST
```

> Note: Since the ID can contain more than numbers, it needs to be specified as a string in DynamoDB.

#### Configure Queue Component

In your `app.php`, configure the `queue` component to use the driver.

```php
use craftcms\dynamodb\DynamoDbQueue;

return [
    'bootstrap' => [
        'queue',
    ],
    'components' => [
        'queue' => [
            'class' => DynamoDbQueue::class,
            'dynamoDb' => [
                'table' => 'my-app-queue-table',
                'partitionKeyAttribute' => 'id', // optional: defaults to 'PK'
                'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
                'region' => '<region>', // optional: defaults to AWS_REGION env var
                'ttl' => 60*60*24, // optional: number of seconds until items are considered expired
                'ttlAttribute' => 'expires' // optional: defaults to 'TTL'
                'credentials' => [
                    'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
                    'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
                ],
            ],
        ],
    ],
];
```

### Testing

Tests run against local DynamoDB tables using Docker. To run tests, you must run the following:

1. Ensure Docker is running
2. Start the DynamoDB container in the `docker-compose.yaml` with `docker-compose up -d`
3. Create the DynamoDB tables for the [cache](#create-dynamodb-cache-table), [session](#create-dynamodb-session-table), and [queue](#create-dynamodb-queue-table)
4. Run the test suite with `vendor/bin/phpunit --testdox`

To make the setup and testing easier, you can run the following Composer scripts:

1. `composer run setup`
2. `composer run test`

## Credits

- [Jason McCallister](https://github.com/jasonmccallister)
- [Tim Kelty](https://github.com/timkelty)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
