<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use tests\app\SimpleTestJob;
use tests\TestCase;
use yii\base\InvalidArgumentException;

class QueueTest extends TestCase
{
    public function testInit(): void
    {
        // Arrange
        $queue = static::getQueue();

        // Assert
        $this->assertEquals('id', static::getQueue()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('job', static::getQueue()->dataAttribute);
        $this->assertEquals('queue-test', static::getQueue()->dynamoDb->tableName);

    }

    // public function testPushMessage(): void
    // {
    //     // Arrange
    //     $queue = new DynamoDbQueue(static::getQueue());
    //     $job = new SimpleTestJob();
    //
    //     // Act
    //     $id = $queue->push($job);
    //
    //     // Assert
    //     $this->assertNotNull($id);
    //     $this->assertStringContainsString('queue-prefix#', $id);
    // }

    // public function testStatusThrowsErrorWhenNotFound(): void
    // {
    //     // Arrange
    //     $queue = new DynamoDbQueue(static::getQueue());
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
    // public function testStatusWaiting(): void
    // {
    //     // Arrange
    //     $queue = new DynamoDbQueue(static::getQueue());
    //     $job = new SimpleTestJob();
    //     $id = $queue->push($job);
    //
    //     // Act
    //     $status = $queue->status($id);
    //
    //     // Assert
    //     $this->assertEquals(1, $status);
    // }
}
