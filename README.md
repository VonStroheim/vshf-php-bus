## VSHF PHP Command Bus

A very simple command bus for PHP

## Usage
Instantiate the Bus instance:
```php
$bus = new \VSHF\Bus\Bus();
```

To execute a command:
```php
$command = new MyCommand($someParamsIfAny);

$bus->dispatch($command);
```

The command class must have a corresponding handler class in the same directory/namespace.

For example, if I have a custom command *MyCommand.php*, there must be a *MyCommandHandler.php* in the same directory/namespace for the bus to call for.

## Middleware

To register a middleware class:
```php
$bus->addMiddleware(MyMiddleware::class);
```

The middleware class must implement *MiddlewareInterface*. It can run code both inside the *before* and *after* methods.

Inside the *before* code, if provided, a call to *next* method must be performed at the end, **unless the execution of the command needs to be prevented**.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/MIT)