<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication; // 👈 IMPORTANTE

    protected function setUp(): void
    {
        parent::setUp();
        // Fuerza SQLite en memoria para TODOS los tests
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
    }
}
