<!-- @format -->

# Service Providers

- Almost all of Laravel’s bootstrap code is separated into something Laravel calls **service providers**
- Service providers e.g **AuthServiceProvider** are normally located at `app/Providers` directory
- A service provider is a class that encapsulates logic that various parts of your application need to run to bootstrap their core functionality
- **App\Providers\AppServiceProvider**, which is a bit of a catchall

- **AuthServiceProvider** that bootstraps all of the registrations necessary for Laravel’s **authentication** system
- **RouteServiceProvider** that bootstraps all of the registrations necessary for Laravel’s **routing** system e.g middleware groups
- Service providers have two important methods: `boot()` and `register()`. There’s also a `DeferrableProvider` interface that you might choose to use

## We to use providers

- Dependency Injection is the most common use
- if you want to register a class in the container and teach Laravel how to resolve that class, **you would create a service provider just for that piece of functionality**.

#### ` register()`

- When Laravel is bootstrapped, First, **all** of the service providers’ `register()` methods are called
- This is where you’ll want to bind classes and aliases to the container.
- don’t do anything in register() that relies on the entire application being bootstrapped

#### ` boot()`

- Secondly, **all** of the service providers’ `boot()` methods are called
- You can now do any other bootstrapping here, like binding event listeners or defining routes
- anything that may rely on the entire Laravel application having been bootstrapped.

#### `DeferrableProvider` interface

- If your service provider is only going to register bindings in the container (i.e., teach the container how to resolve a given class or interface), but not perform any other bootstrapping, you can “defer” its registrations,
- which means they won’t run unless one of their bindings is explicitly requested from the container
- This can speed up your application’s average time to bootstrap.

- If you want to defer your service provider’s registrations, first implement the `Illuminate\Contracts\Support\DeferrableProvider` interface
- which means you define a `provides()` method that returns a list of bindings the provider provides

```php
use Illuminate\Contracts\Support\DeferrableProvider;
class GitHubServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides()
    {
        return [
            GitHubClient::class,
        ];
    }
}
```
