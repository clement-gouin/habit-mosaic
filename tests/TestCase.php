<?php

namespace Tests;

use App\Objects\Statistics;
use Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

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

    protected function mockMosaicServiceStatistics(string $className, ?Model $value = null, ?Statistics $statistics = null, int $times = 1): void
    {
        $mosaicServiceMock = $this->mock($className);

        $mosaicServiceMock->expects('getStatistics')
            ->with($value ? Mockery::on(fn (Model $arg) => $arg->getAttribute('id') === $value->getAttribute('id')) : Mockery::any())
            ->andReturn($statistics ?? Statistics::fromDataCollection(collect()))
            ->times($times);

        $this->app->instance($className, $mosaicServiceMock);
    }
}
