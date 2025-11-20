<?php

namespace VSHF\Bus;

/**
 * Class BusInterface
 *
 * @package VSHF
 */
interface BusInterface
{
    /**
     * @param string $commandClassName
     * @param string $handlerClassName
     *
     * @return void
     */
    public function subscribeCommand(string $commandClassName, string $handlerClassName): void;

    /**
     * @param string $queryClassName
     * @param string $handlerClassName
     *
     * @return void
     */
    public function subscribeQuery(string $queryClassName, string $handlerClassName): void;

    /**
     * @param string $middlewareClassName
     * @param int $queue
     * @return void
     */
    public function addMiddleware(string $middlewareClassName, int $queue = 0): void;

    /**
     * @param CommandInterface $command
     *
     * @return bool
     */
    public function dispatch(CommandInterface $command): bool;

    /**
     * @template TResult
     * @param QueryInterface<TResult> $query
     *
     * @return TResult
     */
    public function ask(QueryInterface $query);
}
