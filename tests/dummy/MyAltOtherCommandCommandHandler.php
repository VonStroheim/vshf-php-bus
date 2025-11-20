<?php

namespace VSHF\Bus\Tests\dummy;

use VSHF\Bus\CommandInterface;

/**
 * Dummy Alternative Command Handler for testing
 */
class MyAltOtherCommandCommandHandler implements \VSHF\Bus\CommandHandlerInterface
{
    public function dispatch(CommandInterface $command): void
    {
        // doing nothing
    }
}
