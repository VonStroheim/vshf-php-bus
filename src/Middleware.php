<?php

namespace VSHF\Bus;

/**
 * Class Middleware
 *
 * @package VSHF
 */
abstract class Middleware implements MiddlewareInterface
{
    private bool $next = false;

    protected CommandInterface|QueryInterface $commandOrQuery;

    protected ?string $agent_type;

    protected ?string $agent_id;

    /**
     * @param CommandInterface|QueryInterface $commandOrQuery
     * @param string|NULL      $agent_type
     * @param string|NULL      $agent_id
     */
    public function __construct(
        CommandInterface|QueryInterface $commandOrQuery,
        string           $agent_type = null,
        string           $agent_id = null
    ) {
        $this->commandOrQuery    = $commandOrQuery;
        $this->agent_type = $agent_type;
        $this->agent_id   = $agent_id;
    }

    public function next(): void
    {
        $this->next = true;
    }

    public function isNext(): bool
    {
        return $this->next;
    }

    public function before(): void
    {
        $this->next();
    }

    /**
     * @param mixed|null $result
     * @return void
     */
    public function after(mixed $result = null): void
    {
    }
}
