<!-- @format -->

#### Redirects

- There are 2 common ways to generate a redirect

- 1. via the `redirect()` global helper here

```php
// Using the global helper to generate a redirect response
Route::get('redirect-with-helper', function () {
 return redirect()->to('login');
});

// Using the global helper shortcut
Route::get('redirect-with-helper-shortcut', function () {
 return redirect('login');
});
```

- 2. via the `Redirect` facade

```php
// Using the facade to generate a redirect response
Route::get('redirect-with-facade', function () {
 return Redirect::to('login');
});

// Using the Route::redirect shortcut
Route::redirect('redirect-by-route', 'login','302');
```

#### More on redirects

##### redirect()->to()

```php
// method signature, to($to = null, $status = 302, $headers = [], $secure = null)
return redirect()->to('home');

// Or same, using the shortcut:
return redirect('home');
```

##### redirect()->route()

- same as the `to()` method, but points to a particular **route name**

```php
//  method signature, route($to = null, $parameters = [], $status = 302, $headers = [])
Route::get('redirect', function () {
 return redirect()->route('conferences.index', ['conference' => 99]);
 // OR use to_route()
 return redirect()->to_route('conferences.index', ['conference' => 99]);
});
```

##### redirect()->back()

- There’s also a global shortcut for this: `back()`.
- simply redirects the user to whatever page they came from

```php
//  method signature, route($to = null, $parameters = [], $status = 302, $headers = [])
Route::get('redirect', function () {
 return redirect()->route('conferences.index', ['conference' => 99]);
 // OR use to_route()
 return redirect()->to_route('conferences.index', ['conference' => 99]);
});
```

##### redirect()->with()

- Allows you pass / flash data to the session
- either an array of keys and values or a single key and value
- accepts fluent method **chains** e.g ->route(...)->with([...])

```php
Route::get('redirect-with-key-value', function () {
 return redirect('dashboard')
 ->with('error', true);
});
Route::get('redirect-with-array', function () {
 return redirect('dashboard')
 ->with(['error' => true, 'message' => 'Whoops!']);
})
```

##### withInput()

- You can also use `withInput()` to flash user form inputs very common with validation errors
- accepts fluent method **chains** e.g ->route(...)->withInput([...])

```php
Route::post('form', function () {
 return redirect('form')
 ->withInput()
 ->with(['error' => true, 'message' => 'Whoops!']);
});

// on the view file, get the flashed input with old()
<input
    name="username"
    value="<?= old('username', 'Default username instructions here') ?>"
>
```

##### withErrors()

- `withErrors()` automatically shares an `$errors` array with the views
- also accepts fluent method **chains**

##### Aborting the Request

- There are a few globally available methods
- 1.  abort()
- 2.  abort_if()
- 3.  abort_unless()

```php
Route::post('something-you-cant-do', function (Illuminate\Http\Request $request) {
 abort(403, 'You cannot do that!');
 abort_unless($request->has('magicToken'), 403);
 abort_if($request->user()->isBanned, 403);
});
```

#### Other Redirect Methods

1. `refresh()` - Redirects to the same page the user is currently on.

2. `away("...")` - Allows for redirecting to an external URL without the default URL validation.

3. `secure()` - Like `to()` with the secure parameter set to "true".

4. `action()` - Allows you to link to a controller and method in one of two ways:

- - as a string `redirect()->action('MyController@myMethod')` or
- - as a tuple `redirect()->action([MyController::class, 'myMethod'])`.

5. `guest()` - Used internally by the authentication system (discussed in Chapter 9); when a user visits a route they’re not authenticated for, this captures the “intended” route and then redirects the user (usually to a login page)

6. `intended()` - Also used internally by the authentication system; after a successful authentication, this grabs the “intended” URL stored by the guest() method and redirects the user there
