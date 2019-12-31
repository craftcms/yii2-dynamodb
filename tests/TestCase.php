<?php

namespace tests;

use Aws\DynamoDb\DynamoDbClient;
use pixelandtonic\dynamodb\helpers\DynamoDBHelper;

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
            'region' => 'docker',
            'version' => 'latest',
        ]);
    }
}
