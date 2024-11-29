<!-- @format -->

# Facades

There are two trademark features of facades:

- first, they’re all available in the global **namespace** e,g (`\Log` is an alias to `\Illuminate\Support\Facades\Log`)
- second, they use **static methods to access nonstatic resources** so you dont have to do `new ClassName()`

```php
$logger = app('log');
$logger->alert('Something has gone wrong!');

// Can be alternatively called as
Log::alert('Something has gone wrong!');
```

## Creating a Facade

- run `php artisan make:facade MyFacade`

```php
class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'My-FQCN';
        // OR
        return 'my-alias';
    }
}
```

## How Facades Work

- Every facade has a single method: getFacadeAccessor().
- This defines the key that Laravel should use to resolve any class instance from the container i.e `app('alias')`

```php
Cache::get('key');
// Is the same as...
app('cache')->get('key');
```

- [facades documentation page](https://oreil.ly/IRsgc)

### Real-Time Facades

with real-time facades you can simply prefix your class’s FQCN with `Facades\`

```php
namespace App;
class Charts
{
    public function burndown()
    {
    // ...
    }
}


Facades\App\Charts::burndown()
```
