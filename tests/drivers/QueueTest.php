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
        // Arrange
        $queue = static::getQueue();

        // Assert
        $this->assertEquals('pk', static::getQueue()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('job', static::getQueue()->dataAttribute);
        $this->assertEquals('queue-test', static::getQueue()->dynamoDb->tableName);

    }

    public function testPushMessage(): void
    {
        // Arrange
        $queue = static::getQueue();
        $job = new SimpleTestJob();

        // Act
        $id = $queue->push($job);
        $item = static::getQueue()->dynamoDb->getItem($id);
        $status = $queue->status($id);

        // Assert
        $this->assertNotNull($id);
        $this->assertStringStartsWith('queue-prefix#', $item['pk']);
        $this->assertEquals(Queue::STATUS_WAITING, $status);
    }

    // public function testStatusThrowsErrorWhenNotFound(): void
    // {
    //     // Arrange
    //     $queue = static::getQueue();
    //     $id = 'something-not-found';
    //
    //     // Assert
    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("Unknown message ID: $id.");
    //
    //     // Act
    //     $queue->status($id);
    // }
    //
}
