<!-- @format -->

# Section Title: Routers

### Typical methods of Laravel’s resource controllers

| Verb   | URL               | Method    | Name          | Description                                      |
| ------ | ----------------- | --------- | ------------- | ------------------------------------------------ |
| GET    | tasks             | index()   | tasks.index   | Show all tasks                                   |
| GET    | tasks/create      | create()  | tasks.create  | Show the create task form                        |
| POST   | tasks             | store()   | tasks.store   | Accept form submission from the create task form |
| GET    | tasks/{task}      | show()    | tasks.show    | Show one task                                    |
| GET    | tasks/{task}/edit | edit()    | tasks.edit    | Edit one task                                    |
| PUT    | tasks/{task}      | update()  | tasks.update  | Accept form submission from the edit task form   |
| DELETE | tasks/{task}      | destroy() | tasks.destroy | Delete one task                                  |

- where `{task}` is a reference to a model called Tas
- by default the id field is used for model binding
- do `{task:slug}` to use another field for the binding e.g slug

### Route Definitions

- define your web routes in `routes/web.php`
- and API routes in `routes/api.php`

#### Routing with closures

- the simplest way to define a route is to match a path (e.g., /) with a closure

```php
// routes/web.php
Route::get('/', function () {
 return 'Hello, World!';
});
```

- Note that we **return** our content and **don’t** `echo` or `print` it. ( this is to allow it to continue flowing through the response stack and the middleware before it is displayed to the user)

##### Route Verbs

- apart from `get()`, `post()`, `put()`, `delete()`, we also have `any()`, `match()`

```php
Route::any('/', function () {
 // Handle any verb request to this route
});
Route::match(['get', 'post'], '/', function () {
 // Handle GET or POST requests to this route
});

```

#### Routing with controller methods

```php
// Laravel uses a convention commonly known as tuple syntax or callable array syntax
Route::get('/', [WelcomeController::class, 'index']);

// it also supports an older “string” syntax
Route::get('/', 'WelcomeController@index')
```

#### Route Parameters

- You can specify parameters in your URL segments
- These params are eventually passed to your closure

```php

Route::get('users/{id}/{slug}/friends', function ($id,$slug) {
 //
});

// it also supports optional route parameters
Route::get('users/{id?}', function ($id = 'fallbackId') {
 //
});

// also supports REGEX
Route::get('posts/{id}/{slug}', function ($id, $slug) {
 //
})->where(['id' => '[0-9]+', 'slug' => '[A-Za-z]+']);

// also has constraint helpers for REGEX route
Route::get('users/{id}/friends/{friendname}', function ($id, $friendname) {
 //
})->whereNumber('id')->whereAlpha('friendname');
// Other helpers include
    // ->whereAlphaNumeric('name')
    // ->whereUuid('id')
    // ->whereUlid('id')
    // ->whereIn('type', ['acquaintance', 'bestie', 'frenemy'])
```

To infer and use Model binding, type hint the

- Pass the lowercase model name as parameter **and** type hint the Model name in the closure or method

```php

Route::get('users/{user}', function (User $user) {
 //
 $user->id;
});

```

#### Route Names

`url('/')` prefixes with full name of site e.g `http://myapp.com/`

But you can also use named routes use `route()`

```php
route('users.comments.show', [1, 2])

// OR

route('users.comments.show', ['userId' => 1, 'commentId' => 2])
```

```php
Route::get( 'users/{userId}/comments/{commentId}', [CommentController::class, 'show'])
 ->name('users.comments.show');

```

`(resourcePlural.action)` is a common convention when using name()

#### Route Groups

By default, a route group doesn’t actually do anything

```php
Route::group(function () {
    Route::get('hello', function () {
        return 'Hello';
    });
    Route::get('world', function () {
        return 'World';
    });
});
```

##### Middleware

There are many ways of attaching middleware

1.

```php
Route::middleware('auth')->group(function() {
 Route::get('dashboard', function () {
 return view('dashboard');
 });
 Route::get('account', function () {
 return view('account');
 });
});
```

2.

```php
class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        //you can use only() and except() to define which methods will receive that middleware
        $this->middleware('admin-auth')
        ->only('editUsers');
        $this->middleware('team-member')
        ->except('editUsers');
    }
}
```

##### Path Prefixes

```php
Route::prefix('dashboard')->group(function () {
    Route::get('/', function () {
    // Handles the path /dashboard
    });
});

// you can also prefix named routes
Route::name('comments.')->prefix('comments')->group(function () {
    Route::get('{id}', function () {
        // ...
    })->name('show'); // Route named 'users.comments.show'
 });


```

##### Subdomain Routing

```php
Route::domain('api.myapp.com')->group(function () {
 Route::get('/', function () {

});
});

// 5. Parameterized subdomain routing
Route::domain('{accountName}.myapp.com')->group(function () {
 Route::get('/', function ($account) {
 //
 });
 Route::get('users/{id}', function ($accountName, $id) {
 //
 });
});

```

##### Group Controllers

we can use the route group `controller()` method, to avoid having to define the full tuple for every route

```php
Route::controller(UserController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('{id}', 'show');
});

```

##### Fallback Routes

```php
Route::fallback(function () {
 // return '404';
});

```

#### Signed Routes

Many applications regularly send notifications about one-off actions (resetting a password, accepting an invitation, etc.) and provide simple links to take those actions. e.g

e.g `http://myapp.com/invitations/5816/yes?signature=030ab0ef6a8237bd86a8b8`.

To build a signed URL to access a given route, the route must have a `name`:

```php
Route::get('invitations/{invitation}/{answer}', InvitationController::class)
 ->name('invitations');

```

##### Protect access to signed routes

1.

```php
// using the "signed" middleware
Route::get('invitations/{invitation}/{answer}', InvitationController::class)
 ->name('invitations')
 ->middleware('signed');
```

2.

```php
// using the hasValidSignature()
class InvitationController
{
 public function __invoke(Invitation $invitation, $answer, Request $request)
 {
 if (! $request->hasValidSignature()) {
 abort(403);
 }
 //
 }
}

```
