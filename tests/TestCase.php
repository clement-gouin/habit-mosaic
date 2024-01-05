<?php

namespace Tests;

use Event;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Event::fake();
    }

    protected static function invokeMethod(mixed $object, string $name, mixed ...$args): mixed
    {
        $class = new ReflectionClass(get_class($object));
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    protected static function invokeMethodStatic(string $className, string $name, mixed ...$args): mixed
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs(null, $args);
    }
}
