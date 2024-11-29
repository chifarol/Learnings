<!-- @format -->

# Introduction

- **Authentication** means verifying who someone is and allowing them to act as that person in your system
- **Authorization** means determining whether the authenticated user is allowed (authorized) to perform a specific behavior

The `App\User` class itself is simple, but it extends the `Illuminate\Foundation\Auth\User` class, which pulls in several traits, so if you want to create another type of user class try extend same parent class

## `auth()` Global Helper and Auth Facade

- `auth()` global helper is the easiest way to interact with the status of the authenti‐ cated user throughout your app
- You can also use the `Auth` facade.
- Or, inject an instance of `Illuminate\Auth\AuthManager` and get the same functionality

- `auth()->check()`: returns true if user is logged
- `auth()->guest()`: returns true if user is not logged
- `auth()->user()`: return logged in user object
- `auth()->id()`: return logged in user id

## Attempting a user authentication

- this used by the `AuthenticatesUsers` trait

```php
if (auth()->attempt([
 'email' => request()->input('email'),
 'password' => request()->input('password'),
])) {
 // Handle the successful login
}

```

## Attempting a user authentication with a “remember me”

- pass a boolean as the 2nd parameter

```php
if (auth()->attempt([
 'email' => request()->input('email'),
 'password' => request()->input('password'),
], request()->filled('remember'))) {
 // Handle the successful login
}
```

### check whether the current user was authenticated by a remember token

- you may want to prevent certain higher-sensitivity features from being accessible by remember token; instead, you can require users to reenter their passwords.
- use `auth()->viaRemember()`, returns a boolean if current user was authenticated via a remember
  token

## Manually Authenticating Users

- There are contexts where it’s valuable for you to be able to choose to log a user in on your own
- There are four methods that make this possible

```php
// with user ID
auth()->loginUsingId(5);
// with user object or any other object that implements the Illuminate\Contracts\Auth\Authenticatable interface
auth()->login($user);
// once with user detaails
auth()->once(['username' => 'mattstauffer']);
// once with user ID
auth()->onceUsingId(5);
```

## Manually Logging Out a User

- use `auth()->logout()`

## Invalidating Sessions on Other Devices

- use `auth()->logoutOtherDevices($password)`

## Auth Middleware

Laravel comes with auth middleware we need out of the box

```php
protected $middlewareAliases = [
 'auth' => \App\Http\Middleware\Authenticate::class,
 'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
 'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
 'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
 'can' => \Illuminate\Auth\Middleware\Authorize::class,
 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
 'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
 'signed' => \App\Http\Middleware\ValidateSignature::class,
 'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
 'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
];

// in routes/web.php

Route::middleware('auth')->group(function () {
 Route::get('account', [AccountController::class, 'dashboard']);
});
Route::get('login', [LoginController::class, 'getLogin'])->middleware('guest');

```

Six of the default route middleware are authentication-related:

- `auth`
  Restricts route access to authenticated users
- `auth.basic`
  Restricts access to authenticated users using HTTP Basic Authentication
- `auth.session`
  Makes routes viable to be disabled using Auth::logoutOtherDevices
- `can`
  Used for authorizing user access to given routes
- `guest`
  Rstricts access to unauthenticated users
- `password.confirm`
  Requires users to have recently reconfirmed their password

## Email Verification

## Guards

- Guard defines how to look up a given user against the request
- Every aspect of Laravel’s authentication system is routed through something called a **guard**
- The guards are defined in `config/auth.php`
- The “default” guard is the one that will be used any time you use any auth features without specifying a guard, usually "session"

  Each guard is a combination of two pieces:

- a **driver** that defines how it persists and retrieves the authentication state (for example, **session** for web, **token** for apis), and
- a **provider** that allows you to get a user by certain criteria (for example, users)

For entirely api-based routes it's pertinent to chnage the default to "token"

```php
// config/auth.php
'defaults' => [
 'guard' => 'web', // Change the default here
 'passwords' => 'users',
],
```

Your can also specify the guard explicitly

```php
$apiUser = auth()->guard('api')->user();

```

## Adding a New Guard

You can add a new guard at any time in cong/auth.php, in the auth.guards setting:
E.g you have a publishing platform where printers accounts are separate from user accounts and they need to be authenticated separately

```php
'guards' => [
    'printers' => [
    'driver' => 'session',
    'provider' => 'printers',
 ],
],

```

## Closure Request Guards

To register a closure request guard, call `viaRequest()` in the boot() method of your `app/Providers/AuthServiceProvider`

- The `viaRequest()` auth method makes it possible to define a guard (named in the first parameter) using just a closure (defined in the second parameter) that takes the HTTP request and returns the appropriate user

```php
public function boot(): void
{
 Auth::viaRequest('token-hash', function ($request) {
 return Printer::where('token-hash', $request->token)->first();
 });
}

```

## Creating a Custom User Provider

Custom providers can be defined in cong/auth.php, there’s an auth.providers section

```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\User::class,
    ],
    'printers' => [
        'driver' => 'eloquent',
        'model' => App\Printer::class,
    ],
],

// now you can differentiate with auth()->guard('users') and auth()->guard('trainees')
```

- now you can differentiate using `auth()->guard('users')` and `auth()->guard('trainees')`
