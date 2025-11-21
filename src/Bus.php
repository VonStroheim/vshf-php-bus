<?php

namespace VSHF\Bus;

/**
 * Class Bus
 *
 * @package VSHF
 */
class Bus implements BusInterface
{
    private array $commandSubscriptions = [];

    private array $querySubscriptions = [];

    private array $middlewares = [];

    public const string AGENT_SYSTEM = 'system';
    public const string AGENT_USER   = 'user';
    public const string AGENT_APP    = 'app';


    public function subscribeCommand(string $commandClassName, string $handlerClassName): void
    {
        $this->commandSubscriptions[ $commandClassName ] = $handlerClassName;
    }

    public function subscribeQuery(string $queryClassName, string $handlerClassName): void
    {
        $this->querySubscriptions[ $queryClassName ] = $handlerClassName;
    }


    /**
     * @param string $middlewareClassName
     * @param int    $queue Indicates the desired order (priority) of execution.
     *                      If there is already a Middleware with the same priority,
     *                      the latter will
     */
    public function addMiddleware(string $middlewareClassName, int $queue = 0): void
    {
        if (isset($this->middlewares[ $middlewareClassName ])) {
            throw new \UnexpectedValueException(sprintf('Middleware %s is already registered', $middlewareClassName));
        }
        $this->middlewares[ $middlewareClassName ] = $queue;
    }

    /**
     * @param CommandInterface $command
     * @param string           $agent_type The type dispatcher (can be the system or user or API token or else)
     * @param string|null      $agent_id   the ID of the dispatcher
     *
     * @return bool TRUE if the command is dispatched, FALSE otherwise
     */
    public function dispatch(CommandInterface $command, string $agent_type = self::AGENT_SYSTEM, string $agent_id = null): bool
    {
        $commandName = get_class($command);
        $handlerName = $commandName . 'Handler';
        if (!class_exists($handlerName)) {
            if (isset($this->commandSubscriptions[ $commandName ]) && class_exists($this->commandSubscriptions[ $commandName ])) {
                $handlerName = $this->commandSubscriptions[ $commandName ];
            } else {
                throw new \InvalidArgumentException(sprintf('Handler class %s not found for command %s', $handlerName, $commandName));
            }
        }
        $handler = new $handlerName();

        if ($handler instanceof CommandHandlerInterface) {

            $sortedMiddlewares = self::sortArrayByPriority($this->middlewares);

            foreach ($sortedMiddlewares as $middlewareName) {
                if (!class_exists($middlewareName)) {
                    continue;
                }
                $middleware = new $middlewareName($command, $agent_type, $agent_id);
                if (!$middleware instanceof MiddlewareInterface) {
                    continue;
                }
                $middleware->before();

                if (!$middleware->isNext()) {
                    // Preventing the command execution
                    return false;
                }
            }

            $handler->dispatch($command);

            foreach ($sortedMiddlewares as $middlewareName) {
                if (!class_exists($middlewareName)) {
                    continue;
                }
                $middleware = new $middlewareName($command, $agent_type, $agent_id);
                if (!$middleware instanceof MiddlewareInterface) {
                    continue;
                }
                $middleware->after();
            }

            return true;

        }

        throw new \InvalidArgumentException(sprintf('Class %s not an instance of Handler for command %s', $handlerName, $commandName));
    }

    /**
     * @return array
     */
    public function getCommandSubscriptions(): array
    {
        return $this->commandSubscriptions;
    }

    /**
     * @return array
     */
    public function getQuerySubscriptions(): array
    {
        return $this->querySubscriptions;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return self::sortArrayByPriority($this->middlewares);
    }

    /**
     * @param $array
     * @return array
     */
    private static function sortArrayByPriority($array): array
    {
        $priorities = [];
        $strings    = [];

        // Separate the priorities and strings into two separate arrays
        foreach ($array as $string => $priority) {
            $priorities[] = $priority;
            $strings[]    = $string;
        }

        // Sort the strings based on their corresponding priorities
        array_multisort($priorities, $strings);

        // Return the sorted array of strings
        return $strings;
    }

    /**
     * @template TResult
     * @param QueryInterface<TResult> $query
     * @return TResult|null
     */
    public function ask(QueryInterface $query, string $agent_type = self::AGENT_SYSTEM, string $agent_id = null)
    {
        $queryName = get_class($query);
        $handlerName = $queryName . 'Handler';
        if (!class_exists($handlerName)) {
            if (isset($this->querySubscriptions[ $queryName ]) && class_exists($this->querySubscriptions[ $queryName ])) {
                $handlerName = $this->querySubscriptions[ $queryName ];
            } else {
                throw new \InvalidArgumentException(sprintf('Handler class %s not found for query %s', $handlerName, $queryName));
            }
        }
        $handler = new $handlerName();

        if ($handler instanceof QueryHandlerInterface) {

            $sortedMiddlewares = self::sortArrayByPriority($this->middlewares);

            foreach ($sortedMiddlewares as $middlewareName) {
                if (!class_exists($middlewareName)) {
                    continue;
                }
                $middleware = new $middlewareName($query, $agent_type, $agent_id);
                if (!$middleware instanceof MiddlewareInterface) {
                    continue;
                }
                $middleware->before();

                if (!$middleware->isNext()) {
                    // Preventing the command execution
                    return null;
                }
            }

            $result = $handler->ask($query);

            $expectedType = $query->getResultType();

            $isValidType = match($expectedType) {
                'array' => is_array($result),
                'string' => is_string($result),
                'int', 'integer' => is_int($result),
                'float', 'double' => is_float($result),
                'bool', 'boolean' => is_bool($result),
                'null' => is_null($result),
                'mixed' => true,
                default => $result instanceof $expectedType
            };

            if (!$isValidType) {
                $actualType = is_object($result)
                    ? get_class($result)
                    : gettype($result);

                throw new \RuntimeException(
                    "Query Handler returned wrong type. Expected {$expectedType}, got {$actualType}"
                );
            }

            foreach ($sortedMiddlewares as $middlewareName) {
                if (!class_exists($middlewareName)) {
                    continue;
                }
                $middleware = new $middlewareName($query, $agent_type, $agent_id);
                if (!$middleware instanceof MiddlewareInterface) {
                    continue;
                }
                $middleware->after($result);
            }

            return $result;

        }

        throw new \InvalidArgumentException(sprintf('Class %s not an instance of Handler for query %s', $handlerName, $queryName));
    }
}
