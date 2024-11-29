<!-- @format -->

# Authorization and Roles

- Laravel enables you to define roles/abilities, and
- check for those roles using a few primary verbs: **can**, **cannot**, **allows**, and **denies**

## Defining Authorization Rules

- The default location for defining authorization rules is in the boot() method of the `AuthServiceProvider`
- There are 2 major ways to define abilities/roles, Policy vs Open definition

### Policies

- Policy allows you to define authorization rules for **a particular** Eloquent model

1. run `php artisan make:policy ContactPolicy`
2. Every method in the Poicy class is mapped as an ability key (e.g update-contact)

   - The currently authenticated user will be passed automatically as the first parameter, and all parameters after that will be the object(s) you’re checking for access to (here, the contact)

   ```php
   class ContactPolicy
   {
       public function update($user, $contact)
       {
           return $user->id == $contact->user_id;
       }
   }

   ```

3. update the `$policies` property in AuthServiceProvider
   Laravel tries to “guess” e.g it’ll apply the `PostPolicy` to your `Post` model automatically but you can define this r/ship explicitly

   ```php
   class AuthServiceProvider extends ServiceProvider
   {
       protected $policies = [
           Contact::class => ContactPolicy::class,
       ];

   }
   ```

### Open definition

- This allows you to associate roles/ability names with a something, whether an Eloquent models or not

```php
class AuthServiceProvider extends ServiceProvider
{
public function boot(): void
{
    Gate::define('update-contact', function ($user, $contact) {
        return $user->id == $contact->user_id;
    });
    // you could also use a class and method instead of a closure
    gate()->define('update-contact', 'ContactACLChecker@updateContact');
}
}
```

- First parameter is the role name, the usual convention is {verb}-{modelName} e.g create-contact, update-contact
- Second parameter is the Closure,

## Checking Abilities using Gate Facade

- The simplest way to check for ability.role is to use the `Gate` facade
- You can use `allows()`, `denies()` and `check()` methods

```php
// Definition
Gate::define('add-contact-to-group', function ($user, $contact, $group) {
    return $user->id == $contact->user_id && $user->id == $group->user_id;
});

// Usage
if (Gate::denies('add-contact-to-group', [$contact, $group])) {
    abort(403);
}
```

### Checking Authorization of Policies or

- For policies, You can run `Gate::allows('update', $contact)`, it will automatically check the `ContactPolicy@update` method for authorization
- If there’s a policy defined for a resource type, the Gate facade will use the **first parameter** to figure out which method to check on the policy. e.g update-

```php
if (Gate::denies('update', $contact)) {
    abort(403);
}

// Gate if you don't have an explicit instance
if (! Gate::check('create', Contact::class)) {
    abort(403);
}
```

- Additionally, there’s a policy() helper that allows you to retrieve a policy class and
  run its methods:

```php
if (policy($contact)->update($user, $contact)) {
 // Do stuff
}
```

## Checking Authorization from User instance

- You can also check authorization fron the $user object directly
- `Authorizable` trait on the User class provides four methods: `$user->can()`, `→canAny()`, `->cant()`, and `->cannot()`

```php
// User
if ($user->can('update', $contact)) {
 // Do stuff
}
```

- use `forUser()` to check authorization for a user that's not currently authenticated user

```php
if (Gate::forUser($user)->denies('create-contact')) {
    abort(403);
}
```

## Intercepting/Overriding Checks

- E.g you can decide to override authorization checks and give "super-admins" a free pass
- In `AuthServiceProvider`, where you’re already defining your abilities, you can also add a `before()` check that runs before all the others and can optionally override

```php
Gate::before(function ($user, $ability) {
    if ($user->isOwner()) {
        return true;
    }
});
Gate::before("update-contact", function ($user, $ability) {
    if ($user->isOwner()) {
        return true;
    }
});
```

### Resource Gates

The `resource()` method makes it possible to apply the four most common gates, `view`, `create`, `update`, and `delete`, to a single resource at once:

```php
Gate::resource('photos', 'App\Policies\PhotoPolicy');

```

This is equivalent to defining the following:

```php
Gate::define('photos.view', 'App\Policies\PhotoPolicy@view');
Gate::define('photos.create', 'App\Policies\PhotoPolicy@create');
Gate::define('photos.update', 'App\Policies\PhotoPolicy@update');
Gate::define('photos.delete', 'App\Policies\PhotoPolicy@delete');
```

## Authorization Middleware

- If you want to authorize entire routes, you can use the Authorize middleware
- The middleware has a shortcut of `can`

```php
Route::get('people/create', function () {
 // Create a person
})->middleware('can:create-person');

// equivalent of
Gate::allows("create-person")


Route::get('people/{person}/edit', function () {
 // Edit person
})->middleware('can:edit,person');

// equivalent of
Gate::allows("edit-contact",$person)

```

## Controller Authorization

The parent `App\Http\Controllers\Controller` class in Laravel imports the `AuthorizesRequests` trait

- This provides three methods for authorization: `authorize()`, `authorizeForUser()`, and `authorizeResource()`.
- We can change
  From this:

```php
public function edit(Contact $contact)
{
 if (Gate::cannot('update-contact', $contact)) {
 abort(403);
 }
 return view('contacts.edit', ['contact' => $contact]);
}
```

To this:

```php
public function edit(Contact $contact)
{
 $this->authorize('update-contact', $contact);
 return view('contacts.edit', ['contact' => $contact]);
}
```

- For `authorizeForUser()`

```php
$this->authorizeForUser($user, 'update-contact', $contact);
```

- `authorizeResource()`, called once in the controller constructor, maps a predefined
  set of authorization rules to each of the RESTful controller methods in that controller

```php

```

```php

```

```php

```

```php

```

```php

```
