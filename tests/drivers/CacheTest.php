<?php

namespace tests\cache\driver;

use pixelandtonic\dynamodb\drivers\DynamoDbCache;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function testFlush()
    {
        // Arrange
        $key1 = uniqid('testing-flush-');
        $key2 = uniqid('testing-flush-2');
        $cache = new DynamoDbCache($this->getCache());
        $cache->set($key1, ['some' => 'value']);
        $cache->set($key2, ['another' => 'value']);

        // Act
        $cache->flush();

        // Assert
        $this->assertFalse($cache->exists($key1));
        $this->assertFalse($cache->exists($key2));
    }


    public function testDeleteValue()
    {
        // Arrange
        $key = uniqid('testing-delete-');
        $cache = new DynamoDbCache($this->getCache());
        $cache->set($key, ['some' => 'value']);

        // Act
        $deleted = $cache->delete($key);

        // Assert
        $this->assertTrue($deleted);
        $this->assertFalse($cache->exists($key));
    }

    public function testExists()
    {
        // Arrange
        $key = uniqid('testing-exists-');
        $cache = new DynamoDbCache($this->getCache());

        // Act
        $cache->set($key, ['some' => 'value']);
        $exists = $cache->exists($key);
        $doesNotExist = $cache->exists('nothing');

        // Assert
        $this->assertTrue($exists);
        $this->assertFalse($doesNotExist);
    }

    public function testSetValue()
    {
        // Arrange
        $key = uniqid('testing-set-');
        $cache = new DynamoDbCache($this->getCache());

        // Act
        $saved = $cache->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGetValue()
    {
        $key = uniqid('testing-get-');
        $cache = new DynamoDbCache($this->getCache());
        $encoded = ['some' => 'value'];
        $cache->set($key, ['some' => 'value']);

        // Act
        $data = $cache->get($key);

        // Assert
        $this->assertEquals($encoded, $data);
    }
}
