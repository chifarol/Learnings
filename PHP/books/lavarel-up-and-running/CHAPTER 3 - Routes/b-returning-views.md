<!-- @format -->

### Returning Views

```php
Route::get('/', function () {
 return view('home');
});

```

##### Passing variables to views

```php
Route::get('tasks', function () {
 return view('tasks.index')
 ->with('tasks', Task::all());
});

<div> {{count(task)}} </div>

```

#### Returning Simple Routes Directly with `Route::view()`

```php
// Returns resources/views/welcome.blade.php
Route::view('/', 'welcome');
// Passing simple data to Route::view()
Route::view('/', 'welcome', ['User' => 'Michael']);

```

#### Using View Composers to Share Variables with Every View

```php
view()->share('variableName', 'variableValue');
```
