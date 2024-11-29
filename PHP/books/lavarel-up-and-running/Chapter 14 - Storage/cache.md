<!-- @format -->

## - Methods Available on Cache Instances

Let’s take a look at the methods you can call on a Cache instance:

- cache()->get($key, $fallbackValue),
- cache()->pull($key, $fallbackValue)
- get() makes it easy to retrieve the value for any given key. pull() is the same as
- get() except it removes the cached value after retrieving it.
- cache()->put($key, $value, $secondsOrExpiration)
- put() sets the value of the specified key for a given number of seconds. If you’d prefer setting an expiration date/time instead of a number of seconds, you can pass a Carbon object as the third parameter:
- cache()->put('key', 'value', now()->addDay());

- cache()->add($key, $value)
- add() is similar to put(), except if the value already exists, add() won’t set it. Also, the method returns a Boolean indicating whether or not the value was actually added: $someDate = now();
- cache()->add('someDate', $someDate); // returns true

```php
$someOtherDate = now()->addHour();
```

- cache()->add('someDate', $someOtherDate); // returns false
- cache()->forever($key, $value)
- forever() saves a value to the cache for a specific key; it’s the same as put(), except the values will never expire (until they’re removed with forget()).
- cache()->has($key) has() returns a Boolean indicating whether or not there’s a value at the provided key.
- cache()->remember($key, $seconds, $closure),
- cache()->rememberForever($key, $closure)
- remember() provides a single method to handle a very common flow: look up whether a value exists in the cache for a certain key, and if it doesn’t, get that value somehow, save it to the cache, and return it.
- remember() lets you provide a key to look up, the numb
- remember() lets you provide a key to look up, the number of seconds it should be saved for, and a closure to define how to look it up, in case the key has no value set. rememberForever() is the same, except it doesn’t need you to set the number of seconds it should be saved for. Take a look at the following example to see a common user scenario for remember():
  // Either returns the value cached at "users" or gets "User::all()",
  // caches it at "users", and returns it

```php
$users = cache()->remember('users', 7200, function () {
 return User::all();
});
```

- cache()->increment($key, $amount), cache()->decrement($key, $amount)
- increment() and decrement() allow you to increment and decrement integer values in the cache. If there is no value at the given key, it’ll be treated as if it were 0, and if you pass a second parameter to increment or decrement, it’ll increment or decrement by that amount instead of by 1.
- cache()->forget($key), cache()->flush() forget() works just like Session’s forget() method: pass it a key and it’ll wipe that key’s value. flush() wipes the entire cache.
