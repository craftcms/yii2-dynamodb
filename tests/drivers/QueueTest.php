<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\Queue;
use tests\app\SimpleTestJob;
use tests\TestCase;

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
        $queue = new Queue($this->getQueue());
        $job = new SimpleTestJob();

        // Act
        $id = $queue->push($job);

        // Assert
        $this->assertNotNull($id);
    }

    public function testStatus()
    {
        // Arrange
        $queue = new Queue($this->getQueue());
        $job = new SimpleTestJob();
        $id = $queue->push($job);

        // Act
        $status = $queue->status($id);

        // Assert
        $this->assertNotNull($status);
    }


}
