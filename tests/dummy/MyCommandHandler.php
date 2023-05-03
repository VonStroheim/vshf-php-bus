<?php

namespace VSHF\Bus\Tests\dummy;

use VSHF\Bus\CommandInterface;

/**
 * Dummy Command Handler for testing
 */
class MyCommandHandler implements \VSHF\Bus\HandlerInterface {
    public function dispatch(CommandInterface $command): void
    {
        // doing nothing
    }
}