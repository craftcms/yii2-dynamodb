<?php

namespace tests\cache\driver;

use pixelandtonic\dynamodb\drivers\DynamoDbCache;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function testFlush(): void
    {
        // Arrange
        $key1 = uniqid('testing-flush-', true);
        $key2 = uniqid('testing-flush-2', true);
        $cache = static::getCache();
        $cache->set($key1, ['some' => 'value']);
        $cache->set($key2, ['another' => 'value']);

        // Act
        $cache->flush();

        // Assert
        $this->assertFalse($cache->exists($key1));
        $this->assertFalse($cache->exists($key2));
    }

    public function testDeleteValue(): void
    {
        // Arrange
        $key = uniqid('testing-delete-', true);
        $cache = static::getCache();
        $cache->set($key, ['some' => 'value']);

        // Act
        $deleted = $cache->delete($key);

        // Assert
        $this->assertTrue($deleted);
        $this->assertFalse($cache->exists($key));
    }

    public function testExists(): void
    {
        // Arrange
        $key = uniqid('testing-exists-', true);
        $cache = static::getCache();

        // Act
        $cache->set($key, ['some' => 'value']);
        $exists = $cache->exists($key);
        $doesNotExist = $cache->exists('nothing');

        // Assert
        $this->assertTrue($exists);
        $this->assertFalse($doesNotExist);
    }

    public function testSetValue(): void
    {
        // Arrange
        $key = uniqid('testing-set-', true);
        $cache = static::getCache();

        // Act
        $saved = $cache->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGetValue(): void
    {
        $key = uniqid('testing-get-', true);
        $cache = static::getCache();
        $encoded = ['some' => 'value'];
        $cache->set($key, ['some' => 'value']);

        // Act
        $data = $cache->get($key);

        // Assert
        $this->assertEquals($encoded, $data);
    }
}
