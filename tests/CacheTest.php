<?php

namespace tests\cache;

use Aws\Result;
use tests\TestCase;

class CacheTest extends TestCase
{
    public function testFlush(): void
    {
        // Arrange
        $key1 = uniqid('testing-flush-', true);
        $key2 = uniqid('testing-flush-2', true);
        static::getCache()->set($key1, ['some' => 'value']);
        static::getCache()->set($key2, ['another' => 'value']);

        // Act
        static::getCache()->flush();

        // Assert
        $this->assertFalse(static::getCache()->exists($key1));
        $this->assertFalse(static::getCache()->exists($key2));
    }

    public function testDeleteValue(): void
    {
        // Arrange
        $key = uniqid('testing-delete-', true);
        static::getCache()->set($key, ['some' => 'value']);

        // Act
        $deleted = static::getCache()->delete($key);

        // Assert
        $this->assertTrue($deleted);
        $this->assertFalse(static::getCache()->exists($key));
    }

    public function testExists(): void
    {
        // Arrange
        $key = uniqid('testing-exists-', true);

        // Act
        static::getCache()->set($key, ['some' => 'value']);
        $exists = static::getCache()->exists($key);
        $doesNotExist = static::getCache()->exists('nothing');

        // Assert
        $this->assertTrue($exists);
        $this->assertFalse($doesNotExist);
    }

    public function testSetValue(): void
    {
        // Arrange
        $key = uniqid('testing-set-', true);

        // Act
        $saved = static::getCache()->set($key, ['some' => 'value']);

        // Assert
        $this->assertTrue($saved);
    }

    public function testGetValue(): void
    {
        $key = uniqid('testing-get-', true);
        $encoded = ['some' => 'value'];
        static::getCache()->set($key, ['some' => 'value']);

        // Act
        $data = static::getCache()->get($key);

        // Assert
        $this->assertEquals($encoded, $data);
    }

    public function testPutItem(): void
    {
        // Arrange
        $key = uniqid('testing-put-', true);

        // Act
        $result = static::getCache()->dynamoDb->putItem([
            'pk'  => uniqid('testing-put-', true),
            'sk' => uniqid('testing-put-', true),
            'some' => 'value'
        ]);

        // Assert
        $this->assertInstanceOf(Result::class, $result);
    }
}
