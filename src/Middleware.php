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

    protected CommandInterface $command;

    protected ?string $agent_type;

    protected ?string $agent_id;

    /**
     * @param CommandInterface $command
     * @param string|NULL      $agent_type
     * @param string|NULL      $agent_id
     */
    public function __construct(
        CommandInterface $command,
        string           $agent_type = null,
        string           $agent_id = null
    ) {
        $this->command    = $command;
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

    public function after(): void
    {
    }
}
