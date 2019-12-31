<?php

namespace tests\cache\driver;

use Aws\DynamoDb\Exception\DynamoDbException;
use pixelandtonic\dynamodb\cache\driver\Cache;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function setUp(): void
    {
        try {
            $this->getClient()->createTable([
                'TableName' => 'cache-table-test',
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
        } catch (DynamoDbException $e) {
            // TODO make this better
        }

        parent::setUp();
    }

    public function testSettingDurationThrowsException()
    {
        // Arrange
        $key = uniqid('testing-duration-exception-');
        $cache = new Cache($this->getClient());

        // Assert
        $this->expectException(\RuntimeException::class);

        // Act
        $cache->set($key, ['some' => 'value'], 5);
    }


    public function testSet()
    {
        // Arrange
        $key = uniqid('testing-set-');
        $cache = new Cache($this->getClient());

        // Act
        $saved = $cache->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGet()
    {
        $key = uniqid('testing-get-');
        $cache = new Cache($this->getClient());
        $encoded = json_encode(['some' => 'value']);
        $cache->set($key, ['some' => 'value']);

        // Act
        $data = $cache->get($key);

        // Assert
        $this->assertEquals($encoded, $data);
    }
}
