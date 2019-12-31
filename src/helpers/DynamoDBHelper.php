<?php

namespace mccallister\dynamodb\helpers;

use Aws\DynamoDb\DynamoDbClient;
use Aws\Sdk;

class DynamoDBHelper
{
    /**
     * Creates a new DynamoDB client using an optional SDK config.
     *
     * @param array $config the SDK config to use.
     * @return DynamoDbClient
     */
    public function getClient(array $config = []): DynamoDbClient
    {
        if (!empty($config)) {
            return (new Sdk($config))->createDynamoDb();
        }

        return (new Sdk([
            'credentials' => [
                'key' => getenv('AWS_ACCESS_KEY_ID'),
                'secret' => getenv('AWS_SECRET_ACCESS_KEY'),
            ],
            'region' => getenv('AWS_REGION'),
            'version' => 'latest',
        ]))->createDynamoDb();
    }
}
