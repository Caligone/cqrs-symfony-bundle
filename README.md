# CQRS Symfony bundle

![Continuous Integration](https://github.com/Caligone/cqrs-symfony-bundle/workflows/Continuous%20Integration/badge.svg)

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
        "symfony/messenger": "^5.1",
    }
}
```

2. Install the dependency with `composer install`

3. Enable the bundle by adding the following line to your `bundles.php` file
```php
  CQRS\CQRSBundle::class => ['all' => true],
```

4. Enable the route loaders by adding the following lines to your `routes.yaml` file
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


## Usage

### Command

#### 1. Create the command

```php
namespace App\Article\Command\CreateArticle;

use CQRS\Annotation\Command;
use CQRS\Command\CommandInterface;

/**
 * Create an article
 *
 * @Command()
*/
class CreateArticleCommand implements CommandInterface
{
    protected string $identifier;

    public function __construct()
    {
        $identifier = // Generate a random identifier
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    
    // Add your attributes and getter
}
```


#### 2. Create the command response

```php
namespace App\Article\Command\CreateArticle;

// use App\Article\Command\CreateArticle\Events\ArticleCreated;
use CQRS\Command\CommandResponseInterface;

class CreateArticleCommandResponse implements CommandResponseInterface
{
    protected string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getEvents(): \Generator
    {
        // The events return here will be dispatch in the event bus
        // yield new ArticleCreated($this->identifier);
    }
}
```


#### 3. Create the command handler


```php
namespace App\Article\Command\CreateArticle;

use CQRS\Command\CommandHandlerInterface;

class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __invoke(CreateArticleCommand $command)
    {
        return new CreateArticleCommandResponse($command->getIdentifier());
    }
}
```


#### 4. Test

You can now `POST` on `/create_article` (or any route you configured with the attributes of the `@Command`)


### Query

#### 1. Create the query

```php
namespace App\Article\Query\ReadArticle;

use CQRS\Annotation\Query;
use CQRS\Query\QueryInterface;

/**
 * Read an article
 *
 * @Query(viewModel=ReadArticleViewModel::class)
 */
class ReadArticleQuery implements QueryInterface
{
    protected string $articleId;

    public function __construct(string $articleId)
    {
        $this->articleId = $articleId;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }
}
```


#### 2. Create the view model

```php
namespace App\Article\Query\ReadArticle;

use CQRS\Query\ViewModelInterface;

/**
 * Read an article
*/
class ReadArticleViewModel implements ViewModelInterface
{
    protected string $identifier;

    protected string $title;

    protected string $content;

    public function __construct(string $identifier, string $title, string $content)
    {
        $this->identifier = $identifier;
        $this->title = $title;
        $this->content = $content;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
```

### 3. Create the query handler

```php
namespace App\Article\Query\ReadArticle;

use CQRS\Query\QueryHandlerInterface;

class ReadArticleQueryHandler implements QueryHandlerInterface
{
    public function __invoke(ReadArticleQuery $query)
    {
        // You probably want to fetch rows from database here
        return new ReadArticleViewModel(
            $query->getArticleId(),
            'Awesome article',
            'Lorem ipsum',
        );
    }
}
```


#### 4. Test

You can now `GET` on `/readArticle?articleId=12` (or any route you configured with the attributes of the `@Query`)
