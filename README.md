# Yii2 DynamoDB Cache, Session, and Queue Driver Implementation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![Total Downloads](https://img.shields.io/packagist/dt/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/pixelandtonic/yii2-dynamodb/run-tests?label=tests)](https://github.com/pixelandtonic/yii2-dynamodb/actions?query=workflow%3Arun-tests+branch%3Amaster)

Easily use DynamoDB as a [cache](https://www.yiiframework.com/doc/guide/2.0/en/caching-overview), [session](https://www.yiiframework.com/doc/guide/2.0/en/runtime-sessions-cookies), or [queue](https://github.com/yiisoft/yii2-queue) using this library in your Yii2 or Craft CMS projects.

> Note: currently Craft supports Yii2 Queue version 2.1.0 so this package is based on version 2.1.0 of yii2-queue.

## Installation

You can install the package via composer:

```bash
composer require pixelandtonic/yii2-dynamodb
```

## Usage

This package provides three drivers for DynamoDB; caching, sessions, and queuing.

### Cache Component

#### Create DynamoDB Table

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
use \pixelandtonic\dynamodb\drivers\DynamoDbCache;

return [
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'cache' => [
            'class' => DynamoDbCache::class,
            'table' => 'my-app-cache-table',
            'tableIdAttribute' => 'id', // optional: defaults to id
            'tableDataAttribute' => 'data', // optional: defaults to data
            'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
            'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
            'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
            'region' => '<region>', // optional: defaults to AWS_REGION env var
        ],
    ],
];
```

### Session Component

#### Create DynamoDB Table

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
use \pixelandtonic\dynamodb\drivers\DynamoDbSession;

return [
    'bootstrap' => [
        'session',
    ],
    'components' => [
        'session' => [
            'class' => DynamoDbSession::class,
            'table' => 'my-app-session-table',
            'tableIdAttribute' => 'id', // optional: defaults to id
            'tableDataAttribute' => 'data', // optional: defaults to data
            'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
            'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
            'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
            'region' => '<region>', // optional: defaults to AWS_REGION env var
        ],
    ],
];
```

### Queue Component

#### Create DynamoDB Table

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
use \pixelandtonic\dynamodb\drivers\DynamoDbQueue;

return [
    'bootstrap' => [
        'queue',
    ],
    'components' => [
        'queue' => [
            'class' => DynamoDbQueue::class,
            'table' => 'my-app-queue-table',
            'tableIdAttribute' => 'id', // optional: defaults to id
            'tableDataAttribute' => 'data', // optional: defaults to data
            'endpoint' => 'http://localhost:8000', // optional: used for local or when using DAX
            'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
            'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
            'region' => '<region>', // optional: defaults to AWS_REGION env var
        ],
    ],
];
```

### Testing

Tests run against local DynamoDB tables using Docker. To run tests, you must run the following:

```bash
docker-compose up -d
make tables
composer test
```

### Security

If you discover any security issues, please email jason@craftcms.com instead of using GitHub issues.

## Credits

- [Jason McCallister](https://github.com/jasonmccallister)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
