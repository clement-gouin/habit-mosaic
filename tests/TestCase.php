<?php

namespace Tests;

use App\Objects\Statistics;
use App\Services\Mosaic\MosaicService;
use Carbon\Carbon;
use Event;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    private array $mockInstances = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->freezeTime();

        Event::fake();
        Mail::fake();
        Queue::fake();
        Storage::fake();

        $this->mockInstances = [];

        $this->mockAllServices();
    }

    protected function mockAllServices(): void
    {
        $classes = ClassFinder::getClassesInNamespace('App\Services', ClassFinder::RECURSIVE_MODE);

        foreach ($classes as $class) {
            $this->getMock($class);
        }
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

    /**
     * @template T
     *
     * @param  class-string<T>  $className
     * @return T|Mockery\MockInterface
     */
    protected function getMock(string $className): Mockery\MockInterface
    {
        if (! isset($this->mockInstances[$className])) {
            $this->mockInstances[$className] = $this->mock($className);

            $this->app->instance($className, $this->mockInstances[$className]);
        }

        return $this->mockInstances[$className];
    }

    /**
     * @param  class-string<MosaicService>  $className
     */
    protected function mockMosaicServiceStatistics(string $className, ?Model $value = null, ?Statistics $statistics = null, int $times = 1): void
    {
        $this->getMock($className)
            ->expects('getStatistics')
            ->with($value ? self::modelArg($value) : Mockery::any())
            ->andReturn($statistics ?? Statistics::fromDataCollection(collect()))
            ->times($times);
    }

    protected static function modelArg(Model $value): Mockery\Matcher\Closure
    {
        return Mockery::on(fn (mixed $arg) => $arg instanceof Model && $arg->getAttribute('id') === $value->getAttribute('id'));
    }

    protected static function dateArg(Carbon $value): Mockery\Matcher\Closure
    {
        return Mockery::on(fn (mixed $arg) => $value->eq($arg));
    }
}
