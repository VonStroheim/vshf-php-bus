<?php

namespace VSHF\Bus\Tests;

use VSHF\Bus\Bus;
use PHPUnit\Framework\TestCase;
use VSHF\Bus\CommandInterface;
use VSHF\Bus\HandlerInterface;
use VSHF\Bus\Middleware;
use VSHF\Bus\Tests\dummy\MyAltOtherCommandHandler;
use VSHF\Bus\Tests\dummy\MyCommand;
use VSHF\Bus\Tests\dummy\MyOtherCommand;

class BusTest extends TestCase
{

    private $middleware;

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::getSubscriptions
     */
    public function testGetSubscriptions(): void
    {
        $bus = new Bus();
        $this->assertIsArray($bus->getSubscriptions());
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::getMiddlewares
     */
    public function testGetMiddlewares(): void
    {
        $bus = new Bus();
        $this->assertIsArray($bus->getMiddlewares());
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::subscribe
     */
    public function testSubscribe(): void
    {
        $bus     = new Bus();
        $command = \Mockery::mock(CommandInterface::class);
        $handler = \Mockery::mock(HandlerInterface::class);

        $this->assertNull($bus->subscribe(get_class($command), get_class($handler)));
        $this->assertContains(get_class($handler), $bus->getSubscriptions());
        $this->assertArrayHasKey(get_class($command), $bus->getSubscriptions());

    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::addMiddleware
     */
    public function testAddMiddleware(): void
    {
        $bus = new Bus();
        $this->assertNull($bus->addMiddleware(Middleware::class));
        $this->assertContains(Middleware::class, $bus->getMiddlewares());
        $this->assertCount(1, $bus->getMiddlewares());
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::dispatch
     */
    public function testDispatchAuto(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $this->assertTrue($bus->dispatch($command));
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::dispatch
     */
    public function testDispatchSubscribed(): void
    {
        $bus     = new Bus();
        $command = new MyOtherCommand();
        $bus->subscribe(MyOtherCommand::class, MyAltOtherCommandHandler::class);
        $this->assertTrue($bus->dispatch($command));
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::dispatch
     */
    public function testDispatchWithMiddleware(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $this->middleware->allows('isNext')->andReturnTrue();
        $bus->addMiddleware(Middleware::class);

        $this->assertTrue($bus->dispatch($command));
    }

    /**
     * @return void
     * @covers \VSHF\Bus\Bus::dispatch
     */
    public function testDispatchWithMiddlewareBlocking(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $this->middleware->expects('isNext')->andReturnFalse();
        $bus->addMiddleware(Middleware::class);

        $this->assertFalse($bus->dispatch($command));
    }

    public function setUp(): void
    {
        $this->middleware = \Mockery::mock('overload:VSHF\Bus\Middleware');
        $this->middleware->allows('before')->andReturnNull();
        $this->middleware->allows('after')->andReturnNull();
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }
}
