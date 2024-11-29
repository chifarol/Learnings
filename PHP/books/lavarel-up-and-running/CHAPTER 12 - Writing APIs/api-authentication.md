<!-- @format -->

# Sanctum

Sanctum is an API authentication system for Laravel,

- It helps in generating simple tokens to authenticate users (personal-access-tokens)

1. Sanctum comes preinstalled with new Laravel projects. If your project doesn’t have it, run

```
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

2. Make sure your User model uses the `HasApiTokens` trait

```php
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

3. go to the **app/Http/Kernel.php** file and replace the `api` array inside the `middlewareGroups` array with the following code:

```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

4. Assign token on successful login or authentication

```php
$token = $user->createToken('apiToken')->plainTextToken;
// OR
$user->createToken($request->device_name)->plainTextToken;
$res = [
    'user' => $user,
    'token' => $token
];

return response($res, 201);
```

5. Protect routes. For any routes you want to protect with Sanctum, attach the `auth:sanctum` middleware:

```php
Route::get('clips', function () {
    return view('clips.index', ['clips' => Clip::all()]);
})->middleware('auth:sanctum');
```

```php

```

```php

```
