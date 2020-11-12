# CQRS Symfony bundle

:warning: Early stage / Far away from production-ready bundle :warning:

## Installation

1. Add the bundle and some required dependencies to your `composer.json` file
```json
{
    "repositories": [{
            "type": "vcs",
            "url": "https://github.com/Caligone/cqrs-symfony-bundle"
    }],
    "require": {
        "caligone/symfony-cqrs": "@dev",
        "doctrine/annotations": "^1.11",
        "phpdocumentor/reflection-docblock": "^5.2",
        "ramsey/uuid": "^4.1"
    }
}
```

2. Install the dependency with `composer install`

3. Enable the bundle by adding the following line to your `bundles.php` file
```php
  CQRS\CQRSBundle::class => ['all' => true],
```

4. Enable the route loader by adding the following lines to your `routes.yaml` file
```yaml
app_commands:
    resource: .
    type: commands
app_queries:
    resource: .
    type: queries
```

5. Create the required buses in your messenger.yaml
```yaml
framework:
    messenger:
        transports:
            sync: 'sync://'

        default_bus: command.bus

        buses:
            command.bus:
                middleware:
                    - validation
                    - CQRS\Middleware\EventsDispatcher
            query.bus:
                middleware:
                    - validation
            event.bus:
                default_middleware: allow_no_handlers
                middleware:
                    - validation

        routing:
            CQRS\Command\CommandInterface: sync
            CQRS\Query\QueryInterface: sync
            CQRS\Event\EventInterface: sync
```
