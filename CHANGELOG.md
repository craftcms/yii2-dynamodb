# Release Notes for Yii2 DynamoDB

## 2.0.0 - 2022-04-22

## Changed

- Composer packages has moved from `pixelandtonic/yii2-dynamodb` to `craftcms/dynamodb`.
- Namespaces have changed from `pixelandtonic\dynamodb\drivers\*` to `crafcms\dynamodb\*.`
- All components now share a `dynamoDb` property, which is a `craftcms\dynamodb\DynamoDbConnection` instance or configuration.
- Replaced `pixelandtonic\dynamodb\WithDynamoDbClient` with `craftcms\dynamodb\DynamoDbConnection`. Several properties have changed.
- Replaced `pixelandtonic\dynamodb\WithDynamoDbClient::keyPrefix` with `craftcms\dynamodb\DynamoDbConnection::formatKey`.
- A key prefix is no longer added by default to any components. You may add this to any using `craftcms\dynamodb\DynamoDbConnection::formatKey`.
- The default partition key is now `PK`, not `id`.

## Added

- Added `craftcms\dynamodb\DynamoDbConnection::partitionKeyAttribute` and `craftcms\dynamodb\DynamoDbConnection::sortKeyAttribute` to allow for compound keys.
- Added `craftcms\dynamodb\DynamoDbConnection::consistentRead`
- Added `craftcms\dynamodb\DynamoDbConnection::batchConfig`
- Added [TTL support](https://docs.aws.amazon.com/amazondynamodb/latest/developerguide/TTL.html) via `craftcms\dynamodb\DynamoDbConnection::ttl` and `craftcms\dynamodb\DynamoDbConnection::ttlAttribute`.
- Added automatic TTL and garbage collection to the session component.

## Fixed

- Fixed an issue where an `AWS_REGION` would not automatically get used if present.
- Fixed an issue where the session component would not register itself as a session handler.

## 1.0.0 - 2020-11-03

## Added

- Initial version with drivers for cache, queue, and session components.
