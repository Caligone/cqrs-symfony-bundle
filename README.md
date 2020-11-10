# CQRS Symfony bundle

:warning: Early stage / Far away from production-ready bundle :warning:

## Installation

1. Add the bundle to your `composer.json` file
```
{
    "repositories": [{
            "type": "vcs",
            "url": "https://github.com/Caligone/cqrs-symfony-bundle"
    }],
    "require": {
        "caligone/symfony-cqrs": "@dev",
    }
}
```

2. Install the dependency with `composer install`

3. Enable the bundle by adding the following line to your `bundles.php` file
```
  CQRS\CQRSBundle::class => ['all' => true],
```

4. Enable the route loader by adding the following lines to your `routes.yaml` file
```
app_commands:
    resource: .
    type: commands
```
