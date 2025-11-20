# VSHF PHP Command Bus

A lightweight and expressive Command/Query Bus for PHP.

## Installation

```bash
composer require vshf/php-command-bus
```

## Usage

Instantiate the Bus instance:

```php
$bus = new \VSHF\Bus\Bus();
```

### Commands

Dispatching a command:

```php
$command = new MyCommand($someParamsIfAny);

$bus->dispatch($command);
```

Each command must have a corresponding handler in the same namespace:

```
MyCommand.php
MyCommandHandler.php
```

### Queries

The bus supports query handlers via ```ask()```:

```php
$result = $bus->ask(new GetUserQuery($id));
```

Query handlers must implement ```QueryHandlerInterface```, and the query class must declare its expected result type:

```php
public function getResultType(): string
{
    return User::class;
}
```

The bus validates the returned type at runtime.

## Middleware

Registering a middleware:

```php
$bus->addMiddleware(MyMiddleware::class);
```

The middleware class must implement *MiddlewareInterface*.

```php
class MyMiddleware implements \VSHF\Bus\MiddlewareInterface {

    public function before() : void
    {
        //  You can access:
        //  $this->$command or $this->query
        //  $this->agent_type
        //  $this->agent_id
        
        $this->next(); // Omit this to stop command/query execution.
    }
    
    public function after() : void
    {
        //  Runs after the handler.
        //
        //  You can access:
        //  $this->$command or $this->query
        //  $this->agent_type
        //  $this->agent_id
    }
}
```

### Middleware Priority

Higher values = later execution:

```php
$bus->addMiddleware(MyMiddleware::class, 99);
```

## Subscriptions

Handlers can be explicitly registered:

```php
$bus->subscribeCommand(MyCommand::class, MyCommandHandler::class);
$bus->subscribeQuery(GetUserQuery::class, GetUserQueryHandler::class);
```

Retrieve them:

```php
$bus->getCommandSubscriptions();
$bus->getQuerySubscriptions();
```

## Breaking Changes (v2.0)
Version 2.0 introduces several important updates:

### 1. Middleware ```after()``` now receives the handler result

Old:

```php
public function after(): void
```

New:
```php
public function after($result = null): void
```

All middleware implementations must update their method signature.

### 2. Full Query Bus Support

A complete query pipeline has been added:

- New method: ask()
- Query subscriptions
- Query handler interfaces
- Result type validation
- Middleware support for queries 

Older patterns are not compatible.

### 3. ```getSubscriptions()``` removed
Replaced by explicit methods:

- ```getCommandSubscriptions()```
- ```getQuerySubscriptions()```

# Changelog

## [2.0.0] — 2025-11-20

Major release with breaking changes and new features.

### Added
- **Query Bus Support**
    - New `ask()` method.
    - Introduced `QueryInterface` and `QueryHandlerInterface`.
    - Added query subscriptions via `subscribeQuery()`.
    - Query handlers validated against the expected return type using `getResultType()`.

- **Middleware Enhancements**
    - Middleware `after()` now receives the handler result.
    - Middleware is executed for both commands and queries.

- **New Introspection Methods**
    - `getCommandSubscriptions()`
    - `getQuerySubscriptions()`
    - `getMiddlewares()` (sorted by priority)

### Changed (Breaking)
- **Middleware signature change**
    - `after()` must now be declared as:
      ```php
      public function after($result = null): void
      ```

### Removed
- Implicit query behavior from v1 (no longer compatible).
- Deprecated `getSubscriptions()`.

### Migration Notes
- Update all middleware implementations to the new `after($result = null)` signature.
- Replace any use of `getSubscriptions()` with:
    - `getCommandSubscriptions()`
    - `getQuerySubscriptions()`

# License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/MIT)