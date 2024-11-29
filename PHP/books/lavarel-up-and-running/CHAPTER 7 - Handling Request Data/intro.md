<!-- @format -->

# Introduction

- The most common tool for accessing user data is injecting an instance of
  the `Illuminate\Http\Request` object
- The alternative is the global `request()` helper e.g `request('firstName')` is
  a shortcut to `request()->input('firstName')` and `request()->has()` is same as `Request::has()`

  ```php
  // injecting Illuminate\Http\Request
  Route::post('form', function (Illuminate\Http\Request $request) {
  var_dump( $request->all())
  });

  //OR

  // The request() helper function
  Route::post('form', function () {
  echo request("name")
  });

  ```

1. `$request->all()`

- gives you an array containing all of the input the user has provided, **from every source**

2. `$request->except([key])`

- provides the same output as $request->all(), but you can choose one or more fields to exclude

  ```php
  Route::post('post-route', function (Request $request) {
  var_dump($request->except('_token'));
  });

  ```

3. `$request->only([key])`

- inverse of `$request->except()`, supplies only the specified fields in an array

  ```php
  Route::post('post-route', function (Request $request) {
  var_dump($request->only(['firstName', 'utm']));
  });

  ```

4. `$request->has(key)`

- detect whether a particular piece of user input is present (even it it doesn't have a value), **regardless of whether the input actually has a value in it**

  ```php
  if ($request->has('referrer')) {
  // Do some analytics work
  }

  ```

5. `$request->>missing(key)`

- inverse of `$request->has()`, returns true if value was not provided in the request
  ```php
  if ($request->missing('referrer')) {
  // Do something
  }
  ```

6. `$request->whenHas(key, fnx, fnx)`

- The first closure parameter is returned when the field exists, and the second is returned when it doesn’t
  ```php
  $referrer = $request->whenHas('referrer', function($referrer) {
      return $referrer;
  }, function() {
      return 'default';
  });
  ```

7. `$request->filled(key)`

- check if field **really** has a value
  ```php
  if ($request->filled('referrer')) {
  // Do some analytics work
  }
  ```

8. `$request->whenFilled(key)`

- similar to `whenHas()`

9. `$request->mergeIfMissing(key,value)`

- add a field to the request when it is not present
- useful when dealing with checkboxes
  ```php
  // POST route at /post-route
  $shouldSend = $request->mergeIfMissing('send_newsletter', 0);
  ```

10. `$request->input(key, defaultValue)`

- returns the value of a single field unlike all(), except(), only() that return an array
- the second parameter is the default value, if value is not present
  ```php
  Route::post('post-route', function (Request $request) {
  $userName = $request->input('name', 'Matt');
  });
  ```

11. `$request->method()`

- returns the http Verb
  ```php
  $method = $request->method();
  // post
  ```

12. `$request->isMethod(methodName)`

- checks whether value matches the specified verb
  ```php
  if ($request->isMethod('patch')) {
  // Do something if request method is PATCH
  }
  ```

13. `$request->integer()`, `->float()`, `->string()`, and `->enum()`

- cast value to integer, float, string or enum
  ```php
  echo(is_int($request->integer('some_integer')));
  ```

14. `$request->dump()` and `->dd()`

- you can dump the whole request by not passing any parameters or dump only selected fields
- **$request->dump()** dumps and then continues
- **$request->dd()** dumps and stops execution of the script

## Array Input

- use the “dot” notation to indicate the steps of digging into the array structure

```php
<!-- GET route form view at /employees/create -->
<form method="post" action="/employees/">
 @csrf
 <input type="text" name="employees[0][firstName]">
 <input type="text" name="employees[0][lastName]">
 <input type="text" name="employees[1][firstName]">
 <input type="text" name="employees[1][lastName]">
 <input type="submit">
</form>
// POST route at /employees
Route::post('employees', function (Request $request) {
 $employeeZeroFirstName = $request->input('employees.0.firstName');
 $allLastNames = $request->input('employees.*.lastName');
 $employeeOne = $request->input('employees.1');
 var_dump($employeeZeroFirstname, $allLastNames, $employeeOne);
});
```

## JSON Input (and $request->json())

- Laravel is smart enough to pull user data from GET, POST, or JSON
- But you can use `$request->json()` if you want to just be more explicit
- Also if the `POST` doesn’t have the correct `application/json` headers, `$request->input()` won’t pick it up as **JSON**

```php

```

## Route Data & Route Segments

There are two primary ways you’ll get data from the URL: via Request objects and via route parameters.

**From Request**

- Each group of characters after the domain in a URL is called a segment
- that segments are returned on a 1-based index
- e.g http://www.myapp.com/users/15/edit $segments = ["users", "15", `"edit"]

```php
// http://www.myapp.com/users/15/edit
// $segments = ["users", "15", `"edit"]
echo $request->segment(2)
// "15"
```

**From Route Parameters**
The other primary way we get data about the URL is from route parameters, which are injected into the controller method

```php
// routes/web.php
Route::get('users/{id}', function ($id) {
 // If the user visits myapp.com/users/15/, $id will equal 15
});

```

## Uploaded Files

- use `$request->file(input_name)` to access files uploaded via **enctype/multipart-formdata**
- files uploaded via FormData do not appear in `$request->input(file_input_name)`
- `$request->hasFile('profile_picture')` checks whether a file exists
- `$request->file('profile_picture')->isValid()` checks whether the file uploaded successfully

```php
if ( $request->hasFile('profile_picture') && $request->file('profile_picture')->isValid() ) {
    $path = $request->profile_picture->store('profiles', 's3');
    auth()->user()->profile_picture = $path;
    auth()->user()->save();
}
```

Other file related methods include

2. getMimeType()
3. store($path, $storageDisk = default disk)
4. storeAs($path, $newName, $storageDisk = default disk)
5. storePublicly($path, $storageDisk = default disk)
6. storePubliclyAs($path, $newName, $storageDisk = default disk)
7. move($directory, $newName = null)
8. getClientOriginalName()
9. getClientOriginalExtension()
10. getClientMimeType()
11. guessClientExtension()
12. getClientSize()
13. getError()
14. isValid()
15. guessExtension()

## Validation

There are two primary options for validating user data:

1. using the `validate()` method on the Request object
2. Manual validation

### `validate()`

- supports both **pipe syntax** and **array syntax**
- pipe syntax: 'fieldname': 'rule|otherRule|anotherRule'
- array syntax: 'fieldname': ['rule', 'otherRule', 'anotherRule']

- if the data isn’t valid, it throws a ValidationException
- If the data is valid, the `validate()` method ends and we can move on with the con‐ troller method, saved and can now be returned by `->validated()` method
- `->validated()` returns an array while
- `->safe()` returns an object that gives you access to all(), only(), and except() methods

```php
Route::get('users/{id}', function ($id) {
    $request->validate([
        'title' => 'required|unique:recipes|max:125',
        'body' => 'required'
    ]);
    $arr = $request->validated();
    $arr = $validator->validated()

    $result = $validator->safe()->only(['name','slug'])
});
```

### Manual validation

you can manually create a `Validator` instance using the Validator facade and check for success or failure

```php
Route::get('users/{id}', function ($id) {
    $validator = Validator::make($request->all(), [
        'title' => 'required|unique:recipes|max:125',
        'body' => 'required'
    ]);
    if ($validator->fails()) {
        return redirect('recipes/create')
        ->withErrors($validator)
        ->withInput();
    }
});
```

## Custom Rule Objects

To create a custom rule,

1. run `php artisan make:rule RuleName`
2. in the RuleName class, the `validate(attributeName, valueToValidate, runOnFailure)` method should accept an attribute name as the first parameter, the user-provided value as the second, and the third a closure that you’ll when the validation fails

- you can use **:attribute** as a placeholder in your message for the attribute name

```php
class AllowedEmailDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if(! in_array(Str::after($value, '@'), ['tighten.co'])){
            $fail('The :attribute field is not from an allowed email provider.');
        }
    }
}
```

# Creating a Form Request

You can create a new form request from the command line:

```php

php artisan make:request CreateCommentRequest
// You now have a form request object available at app/Http/Requests/CreateCommentRequest.php.


namespace App\Http\Requests;
use App\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
class CreateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $blogPostId = $this->route('blogPost');
            return auth()->check() && BlogPost::where('id', $blogPostId)->where('user_id', auth()->id())->exists();
        }
        public function rules(): array
        {
            return [
            'body' => 'required|max:1000',
            ];
        }
}


//
Route::post('comments', function (App\Http\Requests\CreateCommentRequest $request) {
 // Store comment
});
```

- `rules()`, which needs to return an array of validation rules for this request.
- The second (optional) method is `authorize()`, if this returns true, the user is authorized to perform this request, and if false, the user is rejected
