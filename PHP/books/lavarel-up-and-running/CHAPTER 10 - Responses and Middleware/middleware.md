<!-- @format -->

# Middleware

A middleware is that there is a series of layers wrapping around your application, like a multilayer cake or an onion

- every request passes through every middleware layer **on its way into** the application, and then the resulting response passes back through the middleware layers **on its way out** to the end user.

- some of the most powerful uses of middleware come from the fact that they can be nearly the first and the last thing to interact with the request/response cycle

## Middleware Sequence

- The first middleware that’s registered gets first access to a request when it comes in,
- then that request is passed to every other middleware in turn, then to the app
- Then the resulting response is passed **outward** through the middleware, and finally the **first middleware gets last access** to the response when it goes out

## Creating Custom Middleware

1. run `php artisan make:middleware BanDeleteMethod`

   - creates a app/Http/Middleware/BanDeleteMethod.php file

2. Define the `handle()` method

```php
class BanDeleteMethod
{
    public function handle($request, Closure $next)
    {
        if ($request->ip() === '192.168.1.1') {
            return response('BANNED IP ADDRESS!', 403);
        }
        // return $next($request);
        $response = $next($request);

        $response->cookie('visited-our-site', true);

        // Finally, we can release this response to the end user
        return $response;
    }
}

```

3. Register the middleware globally (app/Http/Kenrel.php) or in specific routes

## Understanding middleware’s handle() method

- The `handle()` method represents the processing of both the incoming request and the outgoing response
- it receives the $request, works on it and passes that request to `$next($request)` means handing it off to the rest of the middleware
- The response object is gotten from the `$next($request)`, decorated an returned

## Binding/Registering Middleware

- You can register a middleware in one of two ways: **globally** or for **specific routes**.
- Both bindings happen in `app/Http/Kernel.php`

### Register/Bind Middleware Globally

- Global middleware are applied to every route
- To register a middleware globally, add its class name to the `$middleware` property in `app/Http/Kernel.php`

```php
// app/Http/Kernel.php
protected $middleware = [
 \App\Http\Middleware\TrustProxies::class,
 \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
 \App\Http\Middleware\BanDeleteMethod::class,
];
```

### Register/Bind route middleware

- route middleware are applied on a route-by-route basis.
- route middleware are registered by adding its class name to the `$routeMiddleware` property in `app/Http/Kernel.php`
- It’s similar to adding them to `$middleware`, except we have to give each one a **key/alias** that will be used when applying this middleware to a particular route

```php
// app/Http/Kernel.php
protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'is_admin' => IsAdminMiddleware::class,
    ];

// We can now use this middleware in our route definitions,
Route::get('contacts', [ContactController::class, 'index'])->middleware('ban-delete');
```

### Using middleware groups

- Out of the box, there are two groups: web and api
- E.g Every route in routes/web.php is in the web middleware group and all routes in routes/api.php is in api group
- middleware groups are registered by adding its class name under a key(route-group) inside the `$middlewareGroups` array property in `app/Http/Kernel.php`

```php
// app/Http/Kernel.php
protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'is_admin' => IsAdminMiddleware::class,
    ];

// We can now use this middleware in our route definitions,
Route::get('contacts', [ContactController::class, 'index'])->middleware('ban-delete');
```

### Custome route groups

- Out of the box, there are two groups: web and api that map to routes/web.php
  routes/api.php respectively
- New Route groups can be declared in `App\Providers\RouteServiceProvider`

```php
public const HOME = '/home';
// protected $namespace = 'App\\Http\\Controllers';
public function boot(): void
{
    $this->configureRateLimiting();
    $this->routes(function () {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    });
}
```

## Passing Parameters to Middleware

- you can add as many parameters after `$next`, separated by commas

```php
public function handle(Request $request, Closure $next, $role, $operation): Response
{
 if (auth()->check() && auth()->user()->hasRole($role) && $operation==="view") {

    return $next($request);
 }
 return redirect('login');
}

```

Apply the middleware like:

```php
Route::get('company', function () {
 return view('company.admin');
})->middleware('auth:owner,view');
```
