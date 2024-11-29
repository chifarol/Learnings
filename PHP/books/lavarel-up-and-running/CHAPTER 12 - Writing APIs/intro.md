<!-- @format -->

# Introduction

## API Controllers

- For organisation sake, Controllers are usually ouner `Api/` folder

```php
php artisan make:controller Api/DogController --api

// generates index(), create(), show(), update()
```

## JSON Responses

- if you echo/return an Eloquent results collection, it’ll automatically convert itself to JSON (using the \_\_toString() magic method)

## Sending Headers in Responses

Any header whose name starts with "X-" is a header that’s not in the HTTP spec. It might be entirely made up (e.g., `X-How-Much-Matt-Loves-This-Page`)

```php
Route::get('dogs', function () {
    return response(Dog::all())->header('X-Greatness-Index', 12);
});
```

## Reading headers in reuquests

- use `$request->header('name-of-header')`

## Result Sorting, with direction control e.g. ?sort=name,-weight

```php
// Handles /dogs?sort=name and /dogs?sort=-name
Route::get('dogs', function (Request $request) {
   // Grab the query parameter and turn it into an array exploded by ,
    $sorts = explode(',', $request->input('sort', ''));
    // Create a query
    $query = Dog::query();
    // Add the sorts one by one
    foreach ($sorts as $sortColumn) {
        $sortDirection = str_starts_with($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $query->orderBy($sortColumn, $sortDirection);
    }
    // Return
    return $query->paginate(20);
});
```

### Filtering API Results e.g ?filter=breed:chihuahua,color:brown.

```php
// Handles /dogs?sort=name and /dogs?sort=-name
Route::get('dogs', function (Request $request) {
    $query = Dog::query();
    $query->when(request()->filled('filter'), function ($query) {
        $filters = explode(',', request('filter'));
        foreach ($filters as $filter) {
            [$criteria, $value] = explode(':', $filter);
            $query->where($criteria, $value);
        }
        return $query;
    });

});
```

## Transforming Results

- Relying on Eloquent’s JSON serialization means we return every field on every model.
- Though Eloquent provides a few convenience tools for defining which fields to show when you’re serializing an array e.g
  - `->select()` method
  - `$hidden` property
  - `$visible` property
  - overwriting the `toArray()` function on your model, crafting a custom output format.

## API-specific transformers

- Eloquent **API resources**, which are structures that define how to transform an Eloquent object (or a collection of Eloquent objects) of a given class to API results.
- For example, your `Dog` Eloquent model now has a Dog resource whose responsibility it is to translate each instance of Dog to the appropriate **Dog-shaped** API response object
