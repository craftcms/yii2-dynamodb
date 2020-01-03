<?php

namespace tests\drivers;

use tests\TestCase;

class QueueTest extends TestCase
{
    public function testInit()
    {
        $queue = $this->getQueue();

        $this->assertEquals('id', $queue->tableIdAttribute);
        $this->assertEquals('data', $queue->tableDataAttribute);
        $this->assertEquals('queue-test', $queue->table);
    }

}
