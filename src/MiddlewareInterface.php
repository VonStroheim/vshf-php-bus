<?php

namespace VSHF\Bus;

/**
 * Class MiddlewareInterface
 *
 * @package VSHF
 */
interface MiddlewareInterface
{
    /**
     * The function that allows the command/query execution to continue
     *
     * @return void
     */
    public function next(): void;

    /**
     * Checks if the command/query execution must continue
     *
     * @return bool
     */
    public function isNext(): bool;

    /**
     * Side effects to run before the command/query execution
     *
     * @return void
     */
    public function before(): void;

    /**
     * Side effects to run after the command/query execution
     *
     * @param mixed|null $result
     * @return void
     */
    public function after(mixed $result = null): void;
}
