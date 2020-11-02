<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbQueue;
use tests\app\SimpleTestJob;
use tests\TestCase;
use yii\base\InvalidArgumentException;

class QueueTest extends TestCase
{
    public function testInit()
    {
        // Arrange
        $queue = $this->getQueue();

        // Assert
        $this->assertEquals('id', $queue->tableIdAttribute);
        $this->assertEquals('data', $queue->tableDataAttribute);
        $this->assertEquals('queue-test', $queue->table);
    }

    public function testPushMessage()
    {
        // Arrange
        $queue = new DynamoDbQueue($this->getQueue());
        $job = new SimpleTestJob();

        // Act
        $id = $queue->push($job);

        // Assert
        $this->assertNotNull($id);
        $this->assertStringContainsString('queue-prefix', $id);
    }

    public function testStatusThrowsErrorWhenNotFound()
    {
        // Arrange
        $queue = new DynamoDbQueue($this->getQueue());
        $id = 'something-not-found';

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown message ID: $id.");

        // Act
        $queue->status($id);
    }

    public function testStatusWaiting()
    {
        // Arrange
        $queue = new DynamoDbQueue($this->getQueue());
        $job = new SimpleTestJob();
        $id = $queue->push($job);

        // Act
        $status = $queue->status($id);

        // Assert
        $this->assertEquals(1, $status);
    }
}
