<?php

namespace tests\cache\driver;

use mccallister\dynamodb\helpers\DynamoDBHelper;
use tests\TestCase;

class CacheTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $client = (new DynamoDBHelper())->getClient([
            'endpoint' => 'http://localhost:8000',
            'credentials' => [
                'key' => 'local',
                'secret' => 'local',
            ],
            'region' => 'us-east-1',
            'version' => 'latest',
        ]);

        $client->createTable([
            'TableName' => 'queue-test-table',
            'KeySchema' => [
                [
                    'AttributeName' => 'key',
                    'KeyType' => 'HASH'
                ],
            ],
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'key',
                    'AttributeType' => 'S'
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 10,
                'WriteCapacityUnits' => 10
            ]
        ]);

        parent::setUp();
    }

    public function testSet()
    {
        $this->assertTrue(true);
    }
}
