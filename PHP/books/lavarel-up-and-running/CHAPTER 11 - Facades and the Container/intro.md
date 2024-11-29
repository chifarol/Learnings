<!-- @format -->

## Introduction

The container is a simple tool you can use to bind and resolve concrete instances of classes and interfaces
Other ALisases include:
• Application container
• IoC (inversion of control) container
• Service container
• DI (dependency injection) container

## Dependency injection

- Dependency injection means that, rather than being instantiated “newed up” within a class e.g `new Class($post, 2)`, each class’s dependencies will be injected in from the outside.

- This most commonly occurs with **constructor injection** ( an object’s dependencies is injected as the object is created.)

```php
class UserMailer
{
    protected $mailer;
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    public function welcome($user)
    {
        return $this->mailer->mail($user->email, 'Welcome!');
    }
}

$mailer = new MailgunMailer($mailgunKey, $mailgunSecret, $mailgunOptions);
// constructor injection
$userMailer = new UserMailer($mailer);

$userMailer->welcome($user);
```

- But there’s also **setter injection**, where the class exposes a method specifically for injecting a given dependency, and **method injection**, where one or more methods expect their dependencies to be injected when they’re called

## Spinning Concrete Instance From Container

### The `app()` Global Helper

- This is the simplest way to get an object out of the container
- It creates an instance of this class and returns it for you
- It accepts a string which can be
  - Class or Interface Name e.g Post::class
  - FQCN (e.g App/Model/Post)
  - Registered Class alias e.g "cache"

```php
$logger = app(Logger::class)
```

### Other Ways

If you're inside a service provider

- `$this->app('FQCN')`

if you have an instance of the container via `$container = app()`

- `$app->make('FQCN')`
- `$app['FQCN']`

## Teaching the Container How to Resolve Classes (Binding Classes to the Container) via app()

Binding a class to Laravel’s container is essentially telling the container, “If a developer asks for an instance of Logger, here’s the code to run to instantiate one with the correct parameters and dependencies and then return it correctly.”

### Binding to a Closure

- The recommended way, binding to the container via any service provider’s `register()` method
- Service Provider can be created via `php artiisan make:provider MyProvider`
- Service providers `register()` is automatically called and registered when Laravel is bootstrapped and running

```php
// In any service provider (maybe LoggerServiceProvider)
public function register(): void
{
    $this->app->bind(MailgunMailer::class, function ($app) {
        return new MailgunMailer(env("mailgunKey"), env("mailgunSecret"), env("mailgunOptions"));
    });
    $this->app->bind(UserMailer::class, function ($app) {
        $mailer = $app->make("mailgun")
        return new UserMailer($mailer)
    });
}
```

- **Aliasing classes and strings**

```php
// In any service provider (maybe LoggerServiceProvider)
public function register(): void
{
    //....
    $this->app->alias(MailgunMailer::class, "mailgun");
    $this->app->alias(UserMailer::class, "user-mailer");
}
```

- **Binding an existing class instance to the container**

```php
public function register(): void
{
    $logger = new Logger('\log\path\here', 'error');
    $this->app->instance(Logger::class, $logger);

}
```

- **Binding a singleton to the container**

```php
public function register(): void
{
    $this->app->singleton(Logger::class, function () {
        return new Logger('\log\path\here', 'error');
    });
}
```

## Contextual Binding

Sometimes you need to change how to resolve an interface depending on the context

```php
// In a service provider
public function register(): void
{
 $this->app->when(FileWrangler::class)
 ->needs(Interfaces\Logger::class)
 ->give(Loggers\Syslog::class);

 $this->app->when(Jobs\SendWelcomeEmail::class)
 ->needs(Interfaces\Logger::class)
 ->give(Loggers\PaperTrail::class);
}

```

### Passing Unresolvable Constructor Parameters Using `makeWith()`

if your class accepts a dynamic value/parameter in its constructor that can't be passed in Service Provider register() method, use the `makeWith()`

```php
class Foo
{
    public function __construct($bar)
    {
    // ...
    }
}
$foo = $this->app->makeWith( Foo::class, ['bar' => 'value'] );
```

Manually calling a class method using the container’s call() method

```php
class Foo
{
    public function bar($parameter1) {}
}

// Calls the 'bar' method on 'Foo' with a first parameter of 'value'
app()->call('Foo@bar', ['parameter1' => 'value']);

```
