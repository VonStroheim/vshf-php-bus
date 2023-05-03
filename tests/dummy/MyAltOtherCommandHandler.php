<?php

namespace VSHF\Bus\Tests\dummy;

use VSHF\Bus\CommandInterface;

/**
 * Dummy Alternative Command Handler for testing
 */
class MyAltOtherCommandHandler implements \VSHF\Bus\HandlerInterface {
    public function dispatch(CommandInterface $command): void
    {
        // doing nothing
    }
}