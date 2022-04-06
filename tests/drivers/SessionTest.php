<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbSession;
use tests\TestCase;
use Yii;

class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->getSession()->dynamoDb->scanDelete();
    }

    public function testInit(): void
    {
        $this->assertEquals('id', $this->getSession()->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('data', $this->getSession()->dataAttribute);
        $this->assertEquals('session-test', $this->getSession()->dynamoDb->tableName);
    }

    public function testFlash()
    {
        $this->getSession()->setFlash('test-flash', 'test-value');
        $this->assertEquals(
            'test-value',
            $this->getSession()->getFlash('test-flash'),
        );
    }

    public function testReadSession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);

        // Act
        $this->getSession()->writeSession($id, 'some-session');
        $data = $this->getSession()->readSession($id);

        // Assert
        $this->assertEquals('some-session', $data);
    }

    public function testDestroySession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);

        // Act
        $this->getSession()->writeSession($id, 'some-session');
        $deleted = $this->getSession()->destroySession($id);

        // Assert
        $this->assertTrue($deleted);
    }

    public function testWriteSession(): void
    {
        // Arrange
        $id = uniqid('testing-write-session-', true);

        // Act
        $stored = $this->getSession()->writeSession($id, 'some-session');

        // Assert
        $this->assertTrue($stored);
    }

    public function testGarbageCollection(): void
    {
        $id = uniqid('testing-gc-session-', true);

        $this->getSession()->writeSession($id, 'some-session');
        sleep($this->getSession()->dynamoDb->ttl + 1);
        $this->getSession()->gcSession(0);
        $data = $this->getSession()->readSession($id);

        $this->assertEquals('', $data);
    }
}
