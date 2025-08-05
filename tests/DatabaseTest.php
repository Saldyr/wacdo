<?php
// tests/DatabaseTest.php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../lib/Database.php';

class DatabaseTest extends TestCase
{
    public function testConnection()
    {
        $db = Database::connect();
        $this->assertInstanceOf(\PDO::class, $db);
    }
}
