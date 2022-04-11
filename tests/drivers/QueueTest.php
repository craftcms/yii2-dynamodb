<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use tests\app\SimpleTestJob;
use tests\TestCase;
use yii\base\InvalidArgumentException;
use yii\queue\Queue;

class QueueTest extends TestCase
{
    public function testInit(): void
    {
        // Assert
        $this->assertEquals('pk', static::getQueue()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('job', static::getQueue()->dataAttribute);
        $this->assertEquals('queue-test', static::getQueue()->dynamoDb->tableName);

    }

    public function testPushMessage(): void
    {
        // Arrange
        $job = new SimpleTestJob();

        // Act
        $id = static::getQueue()->push($job);
        $item = static::getQueue()->dynamoDb->getItem($id);
        $status = static::getQueue()->status($id);

        // Assert
        $this->assertNotNull($id);
        $this->assertStringStartsWith('queue-prefix#', $item['pk']);
        $this->assertEquals(Queue::STATUS_WAITING, $status);
    }

    public function testRun(): void
    {
        // Arrange
        $job = new SimpleTestJob();

        // Act
        $id = static::getQueue()->push($job);
        $item = static::getQueue()->dynamoDb->getItem($id);

        static::getQueue()->execute(
            $id,
            $item['job'],
            $item['ttr'],
            1,
            null
        );

        $status = static::getQueue()->status($id);

        $this->assertEquals(Queue::STATUS_DONE, $status);
    }
}
