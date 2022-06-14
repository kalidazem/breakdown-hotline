<?php

namespace BreakdownHotline\Tests\Unit\DependencyInjection;

use BreakdownHotline\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use stdClass;

class DependencyInjectionTest extends TestCase
{
    /**
     * Container
     *
     * @var Container
     */
    private $container;

    protected   function setUp(): void
    {
        $this->container = new Container();
    }


    protected function tearDown(): void
    {
        $this->container = null;
    }

    function test_service()
    {
        $this->container['std_service'] = $this->container->service(function (Container $container) {
            return new stdClass();
        });

        $std_service = $this->container['std_service'];

        $this->assertInstanceOf('stdClass', $std_service);
    }


    function test_service_with_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectDeprecationMessage('Service definition is not a Closure or invokable object.');

        $this->container->service('bar');
    }
}
