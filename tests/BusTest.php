<?php

namespace VSHF\Bus\Tests;

use PHPUnit\Framework\Attributes\CoversFunction;
use VSHF\Bus\Bus;
use PHPUnit\Framework\TestCase;
use VSHF\Bus\CommandInterface;
use VSHF\Bus\CommandHandlerInterface;
use VSHF\Bus\Middleware;
use VSHF\Bus\Tests\dummy\MyAltOtherCommandCommandHandler;
use VSHF\Bus\Tests\dummy\MyCommand;
use VSHF\Bus\Tests\dummy\MyMiddleware;
use VSHF\Bus\Tests\dummy\MyOtherCommand;

#[CoversFunction('getCommandSubscriptions')]
#[CoversFunction('getMiddlewares')]
#[CoversFunction('subscribe')]
#[CoversFunction('addMiddleware')]
#[CoversFunction('sortArrayByPriority')]
#[CoversFunction('dispatch')]

class BusTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSubscriptions(): void
    {
        $bus = new Bus();
        $this->assertIsArray($bus->getCommandSubscriptions());
    }

    /**
     * @return void
     */
    public function testGetMiddlewares(): void
    {
        $bus = new Bus();
        $this->assertIsArray($bus->getMiddlewares());
    }

    /**
     * @return void
     */
    public function testSubscribe(): void
    {
        $bus     = new Bus();
        $command = \Mockery::mock(CommandInterface::class);
        $handler = \Mockery::mock(CommandHandlerInterface::class);

        $this->assertNull($bus->subscribeCommand(get_class($command), get_class($handler)));
        $this->assertContains(get_class($handler), $bus->getCommandSubscriptions());
        $this->assertArrayHasKey(get_class($command), $bus->getCommandSubscriptions());

    }

    /**
     * @return void
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
     */

    public function testAddMiddlewarePriority(): void
    {
        $bus = new Bus();
        $this->assertNull($bus->addMiddleware(Middleware::class));
        $this->assertNull($bus->addMiddleware('AnotherMiddleware', 99));
        $this->assertNull($bus->addMiddleware('YetAnotherMiddleware'));
        $this->assertContains(Middleware::class, $bus->getMiddlewares());
        $this->assertContains('AnotherMiddleware', $bus->getMiddlewares());
        $this->assertContains('YetAnotherMiddleware', $bus->getMiddlewares());
        $this->assertCount(3, $bus->getMiddlewares());
        $this->assertSame($bus->getMiddlewares(), [Middleware::class, 'YetAnotherMiddleware', 'AnotherMiddleware']);
    }

    /**
     * @return void
     */
    public function testDispatchAuto(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $this->assertTrue($bus->dispatch($command));
    }

    /**
     * @return void
     */
    public function testDispatchSubscribed(): void
    {
        $bus     = new Bus();
        $command = new MyOtherCommand();
        $bus->subscribeCommand(MyOtherCommand::class, MyAltOtherCommandCommandHandler::class);
        $this->assertTrue($bus->dispatch($command));
    }

    /**
     * @return void
     */
    public function testDispatchWithMiddleware(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $bus->addMiddleware(MyMiddleware::class);
        $this->assertTrue($bus->dispatch($command));
        $this->assertTrue(MyMiddleware::$beforeCalled);
        $this->assertTrue(MyMiddleware::$afterCalled);
    }

    /**
     * @return void
     */
    public function testDispatchWithMiddlewareBlocking(): void
    {
        $bus     = new Bus();
        $command = new MyCommand();
        $bus->addMiddleware(MyMiddleware::class);
        MyMiddleware::$mustBlock = true;
        $this->assertFalse($bus->dispatch($command));
    }

    public function setUp(): void
    {
        MyMiddleware::$beforeCalled = false;
        MyMiddleware::$afterCalled  = false;
        MyMiddleware::$mustBlock  = false;
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }
}
