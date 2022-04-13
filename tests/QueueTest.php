<?php

namespace tests;

use tests\app\SimpleTestJob;
use yii\base\InvalidConfigException;
use yii\queue\Queue;

class QueueTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     */
    public function testInit(): void
    {
        // Assert
        $this->assertEquals('pk', static::getQueue()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('job', static::getQueue()->dataAttribute);
        $this->assertEquals('queue-test', static::getQueue()->dynamoDb->tableName);

    }

    /**
     * @throws InvalidConfigException
     */
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

    /**
     * @throws InvalidConfigException
     */
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
