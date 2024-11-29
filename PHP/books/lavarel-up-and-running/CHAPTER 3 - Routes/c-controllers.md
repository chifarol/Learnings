<!-- @format -->

- Don't cram all of the application’s logic into the controllers

- A controller’s primary job should be to capture the intent of an HTTP request and pass it on to the rest of the application.

#### Create a controller

```php
// run
php artisan make:controller ExampleController


```

#### Getting User Input

```php
$dataArr = request()->only(['title', 'description'])
$dataArr = request()->get(['title', 'description'])
```

- The set of data each of these methods is pulling from represents all userprovided data, whether from `query` parameters or `POST` values

#### Getting Query params

```php
 $searchTerm = $request->query('search');
```

#### Injecting Dependencies into Controllers

- All controller methods (including the constructors) are resolved out of Laravel’s container

- This means anything you **typehint** (that the container knows how to resolve)
  will be automatically injected.

- With this typehinting we can have an instance of the `Request` object
  instead of using the global `request()` helper

```php
 public function store(Request $request)
{
    Task::create($request->only(['title', 'description']));
    return redirect('tasks');
}
```

##### Quick Tip

- You can always check what routes your current application has available with `route:list`

```php
// use --exclude-vendor so you don’t see all the weird
routes my dependencies register in order for them to operate
php artisan route:list --exclude-vendor
```

#### Resource Controllers

- Laravel has some conventions for all of the routes of a traditional
- It comes with a generator out of the box and a convenience route definition that allows you to bind an entire resource controller at once.

```php
// pass the --resource flag to autogenerate methods for all the basic resource routes like create() and update()
php artisan make:controller ExampleController --resource flag

// routes/web.php
Route::resource('tasks', TaskController::class);
```

#### API Resource Controllers

- When you’re working with RESTful APIs, the list of potential actions on a resource is not the same as it is with an HTML resource controller
- API controllers usually don't have `create()` and `edit()` actions.
- For example, you can send a POST request to an API to create a resource, but you can’t really “show a create form” in an API.

```php
// pass the --api flag when creating a controller
php artisan make:controller MySampleResourceController --api

// routes/web.php
Route::resource('tasks', TaskController::class);
```

#### Single Action Controllers

- There will be times in your applications when a controller should only service a single route
- You can point a single route at a single controller without concern‐
  ing yourself with naming the one method
- The `__invoke()` method is a PHP magic method that allows you to “invoke” an instance of a class, treating it like a function and calling it

```php
// \App\Http\Controllers\UpdateUserAvatar.php
public function __invoke(User $user)
{
 // Update the user's avatar image
}
// routes/web.php
Route::post('users/{user}/update-avatar', UpdateUserAvatar::class);
```

#### Route Model Binding

- There are two kinds of route model binding: implicit and custom (or explicit).

##### Implicit Route Model Binding

- First, name your route parameter something unique to that model (e.g., name it `$conference` instead of `$id`)
- Second, then typehint that parameter in the closure/controller method and use the same variable name there.

```php
Route::get('conferences/{conference}', function (Conference $conference) {
 // $conference->id;
});

```

- Every time this route is visited, the application will assume that whatever is passed into the URL in place of {conference} is an ID that should be used to look up athe Conference record in the database

###### Customizing the Route Key for an Eloquent Model

- the default column Eloquent will look it up by is its primary key (ID)
- To change the column your Eloquent model uses for URL lookups in all your routes, add a method to your model named `getRouteKeyName()`

```php
// Models/User.php
public function getRouteKeyName()
{
 return 'slug';
}
```

##### Customizing the Route Key in a Specic Route

```php
Route::get( 'conferences/{conference:slug}',
    function (Conference $conference) {
       $conference->id;
    }
 );

```

#### Manual / Custom Route Model Binding

To manually configure route model bindings

- add a line to the `boot()` method in `App\Providers\RouteServiceProvider`.

```php
public function boot()
 {
    // Perform the binding
    Route::model('event', Conference::class);
 }
```

- with this, whenever a route has a parameter in its definition named
  `{event}` the route resolver will return an instance of the Conference class with the ID of that URL parameter,

```php
Route::get('events/{event}', function (Conference $event) {
 // $event->id;
});
```

##### Route Caching

- To cache your routes file, you need to be using no route closures/anonymous fxns, all controllers
-

```php
// cache routes
php artisan route:cache

// clear cache
php artisan route:clear
```

**NOTE**:

- Laravel will now match routes against that cached file instead of
  your actual routes/\* files
- Changes to your routes files, and they won’t take effect until you run route:cache again
- Since Git ignores the route cache file by default anyway, consider only using route caching on your production server, and run `php artisan route:cache` every time you deploy new code
