<?php

namespace tests\drivers;

use pixelandtonic\dynamodb\drivers\DynamoDbSession;
use tests\TestCase;

class SessionTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->getSession()->dynamoDb->scanDelete();
    }

    public function testInit(): void
    {
        /** @var DynamoDbSession $session */
        $session = $this->getSession();

        $this->assertEquals('id', $session->dynamoDb->partitionKeyAttribute);
        $this->assertEquals('data', $session->dataAttribute);
        $this->assertEquals('session-test', $session->dynamoDb->tableName);
    }

    public function testReadSession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);
        $session = $this->getSession();

        // Act
        $session->writeSession($id, 'some-session');
        $data = $session->readSession($id);

        // Assert
        $this->assertEquals('some-session', $data);
    }

    public function testDestroySession(): void
    {
        // Arrange
        $id = uniqid('testing-destroy-session-', true);
        $session = $this->getSession();

        // Act
        $session->writeSession($id, 'some-session');
        $deleted = $session->destroySession($id);

        // Assert
        $this->assertTrue($deleted);
    }


    public function testWriteSession(): void
    {
        // Arrange
        $id = uniqid('testing-write-session-', true);
        $session = $this->getSession();

        // Act
        $stored = $session->writeSession($id, 'some-session');

        // Assert
        $this->assertTrue($stored);
    }
}
