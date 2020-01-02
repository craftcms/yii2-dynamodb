<?php

namespace tests\cache\driver;

use pixelandtonic\dynamodb\drivers\Cache;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function testExistsReturnsFalseWhenNotFound()
    {
        // Arrange
        $key = uniqid('testing-exists-');
        $client = $this->getClient();
        $cache = new Cache($client);

        // Act
        $doesNotExist = $cache->exists($key);

        // Assert
        $this->assertFalse($doesNotExist);
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

        // Assert
        $this->assertTrue($exists);
    }

    public function testSet()
    {
        // Arrange
        $key = uniqid('testing-set-');
        $cache = new Cache($this->getClient());

        // Act
        $saved = $cache->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGet()
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
