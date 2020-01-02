<?php

namespace tests\cache\driver;

use pixelandtonic\dynamodb\drivers\Cache;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function testFlush()
    {
        // Arrange
        $key1 = uniqid('testing-flush-');
        $key2 = uniqid('testing-flush-2');
        $cache = new Cache($this->getClient());
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
        $client = $this->getClient();
        $cache = new Cache($client);
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
        $client = $this->getClient();
        $cache = new Cache($client);

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
        $cache = new Cache($this->getClient());

        // Act
        $saved = $cache->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGetValue()
    {
        $key = uniqid('testing-get-');
        $cache = new Cache($this->getClient());
        $encoded = ['some' => 'value'];
        $cache->set($key, ['some' => 'value']);

        // Act
        $data = $cache->get($key);

        // Assert
        $this->assertEquals($encoded, $data);
    }
}
