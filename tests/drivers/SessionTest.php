<?php

namespace tests\drivers;

use tests\TestCase;

class SessionTest extends TestCase
{
    public function testInit()
    {
        $session = $this->getSession();

        $this->assertEquals('id', $session->tableIdAttribute);
        $this->assertEquals('data', $session->tableDataAttribute);
        $this->assertEquals('session-test', $session->table);
    }

    public function testReadSession()
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

    public function testDestroySession()
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


    public function testWriteSession()
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
