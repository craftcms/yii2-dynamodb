# Yii2 DynamoDB Cache and Queue Driver Implementation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![Total Downloads](https://img.shields.io/packagist/dt/pixelandtonic/yii2-dynamodb.svg?style=flat-square)](https://packagist.org/packages/pixelandtonic/yii2-dynamodb)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/pixelandtonic/yii2-dynamodb/run-tests?label=tests)](https://github.com/pixelandtonic/yii2-dynamodb/actions?query=workflow%3Arun-tests+branch%3Amaster)

Easily use DynamoDB as a queue or cache using this library in your Yii2 or Craft CMS projects.    

## Installation

You can install the package via composer:

```bash
composer require pixelandtonic/yii2-dynamodb
```

## Usage

This package provides two drivers for DynamoDB; queue and cache.

### Cache Component

#### Create DynamoDB Table

TODO

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
            'endpoint' => 'http://localhost:8000', // optional: used for local development or when using DAX
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
            'endpoint' => 'http://localhost:8000', // optional: used for local development or when using DAX
            'key' => '<key>', // optional: defaults to AWS_ACCESS_KEY_ID env var
            'secret' => '<secret>', // optional: defaults to AWS_SECRET_ACCESS_KEY env var
            'region' => '<region>', // optional: defaults to AWS_REGION env var
        ],
    ],
];
```

### Testing

``` bash
composer test
```

### Security

If you discover any security related issues, please email jason@craftcms.com instead of using the issue tracker.

## Credits

- [Jason McCallister](https://github.com/jasonmccallister)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
