# Yii2 DynamoDB Cache, Session, and Queue Driver Implementation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![Total Downloads](https://img.shields.io/packagist/dt/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/pixelandtonic/yii2-dynamodb/run-tests?label=tests)](https://github.com/pixelandtonic/yii2-dynamodb/actions?query=workflow%3Arun-tests+branch%3Amaster)

Easily use DynamoDB as a cache, session, or queue using this library in your Yii2 or Craft CMS projects.

> Note: currently Craft supports Yii2 Queue version 2.1.0 so this package is based on version 2.1.0 of yii2-queue.

## Installation

You can install the package via composer:

```bash
composer require pixelandtonic/yii2-dynamodb
```

## Usage

This package provides three drivers for DynamoDB; caching, sessions, and queuing.

### Cache Component

#### Create DynamoDB Tables

Since DynamoDB is a NoSQL database, the only key you have to specify is the primary key. You can use the following AWS CLI command to generate a table.

```shell script
aws dynamodb create-table --table-name=session-table \
	--attribute-definitions=AttributeName=id,AttributeType=S \
	--key-schema=AttributeName=id,KeyType=HASH \
	--billing-mode=PAY_PER_REQUEST
```

> Note: Since the ID can contain more than numbers, it needs to be specified as a string in DynamoDB.

#### Configure Cache Component

In your `app.php`, configure the `cache` component to use the driver.

```php
return [
    'bootstrap' => [
        'cache',
    ],
    'components' => [
        'cache' => [
            'class' => \pixelandtonic\dynamodb\drivers\Cache::class,
            'table' => 'cache-test',
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

#### Configure Session Component

In your `app.php`, configure the `session` component to use the driver.

```php
return [
    'bootstrap' => [
        'session',
    ],
    'components' => [
        'session' => [
            'class' => \pixelandtonic\dynamodb\drivers\Session::class,
            'table' => 'session-test',
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

Tests run against a local DynamoDB table using Docker. To run tests, you must run the following:

```bash
docker-compose up -d
make tables
composer test
```

### Security

If you discover any security related issues, please email jason@craftcms.com instead of using the issue tracker.

## Credits

- [Jason McCallister](https://github.com/jasonmccallister)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
