<?php

namespace tests;

use Aws\DynamoDb\DynamoDbClient;
use mccallister\dynamodb\helpers\DynamoDBHelper;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function getClient(): DynamoDbClient
    {
        return (new DynamoDBHelper())->getClient([
            'endpoint' => 'http://localhost:8000',
            'credentials' => [
                'key' => 'local',
                'secret' => 'local',
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);
    }
}
