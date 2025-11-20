<?php

namespace VSHF\Bus;

/**
 * Class CommandHandlerInterface
 *
 * @package VSHF
 */
interface CommandHandlerInterface
{
    /**
     * @param CommandInterface $command
     *
     * @return void
     */
    public function dispatch(CommandInterface $command): void;
}
