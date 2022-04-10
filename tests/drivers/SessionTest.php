<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbSession;
use tests\TestCase;
use Yii;

class SessionTest extends TestCase
{
    public function testInit(): void
    {
        $this->assertEquals('id', static::getSession()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('data', static::getSession()->dataAttribute);
        $this->assertEquals('session-test', static::getSession()->dynamoDb->tableName);
    }

    public function testFlash(): void
    {
        static::getSession()->setFlash('test-flash', 'test-value');
        $this->assertEquals(
            'test-value',
            static::getSession()->getFlash('test-flash', null, true),
        );

        // Should be deleted after preceding getFlash
        $this->assertEquals(
            null,
            static::getSession()->getFlash('test-flash'),
        );
    }

    public function testReadSession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);

        // Act
        static::getSession()->writeSession($id, 'some-session');
        $data = static::getSession()->readSession($id);

        // Assert
        $this->assertEquals('some-session', $data);
    }

    public function testDestroySession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);

        // Act
        static::getSession()->writeSession($id, 'some-session');
        $deleted = static::getSession()->destroySession($id);

        // Assert
        $this->assertTrue($deleted);
    }

    public function testWriteSession(): void
    {
        // Arrange
        $id = uniqid('testing-write-session-', true);

        // Act
        $stored = static::getSession()->writeSession($id, 'some-session');

        // Assert
        $this->assertTrue($stored);
    }

    public function testGarbageCollection(): void
    {
        $id = uniqid('testing-gc-session-', true);

        static::getSession()->writeSession($id, 'some-session');
        sleep(static::getSession()->dynamoDb->ttl + 1);
        static::getSession()->gcSession(0);
        $data = static::getSession()->readSession($id);

        $this->assertEquals('', $data);
    }
}
