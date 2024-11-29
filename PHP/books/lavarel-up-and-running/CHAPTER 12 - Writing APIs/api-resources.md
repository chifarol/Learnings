<!-- @format -->

# API Resources

- A resource class represents a single model that needs to be transformed into a JSON structure.
- Eloquent **API resources**, which are structures that define how to transform an Eloquent object (or a collection of Eloquent objects) of a given class to API results.
- For example, your `Dog` Eloquent model now has a Dog resource whose responsibility it is to translate each instance of Dog to the appropriate **Dog-shaped** API response object

## Creating a Resource Class

1. run `php artisan make:resource Dog` which creates a new class in app/Http/Resources/Dog.php

- `toArray()` method has access to two important data,
- `Request` object
- the Eloquent object being transformed by calling its properties and methods on `$this`

```php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class Dog extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id' => $this->id,
        'name' => $this->name,
        'breed' => $this->breed,
        ];
    }
}
```

Using the simple Dog resource

```php
use App\Dog;
use App\Http\Resources\Dog as DogResource;
Route::get('dogs/{dogId}', function ($dogId) {
 return new DogResource(Dog::find($dogId));
});

```

## Resource Collections

You can process multiple instances of a model using an API resource’s `collection()` method,

```php
Route::get('dogs', function () {
    return DogResource::collection(Dog::all());
});
```

- You can also have a dedicated collection Resource

```php
php artisan make:resource Dog --collection
 // OR
php artisan make:resource DogCollection
```

```php
class DogCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'links' => [
            'self' => route('dogs.index'),
            ],
        ];
    }
}
```

## Nesting Relationships

```php
public function toArray(Request $request): array
{
    return [
        'name' => $this->name,
        'breed' => $this->breed,
        // Throws error if not eager loaded
        'friends' => Dog::collection($this->friends),
        // Checks if eager-loaded before returning results
        'bones' => BoneResource::collection($this->whenLoaded('bones')),
    ];
}
```

- Remember to eager load the related field using `with()`

```php
return new DogResource(Dog::with('friends')->find($dogId));
```

### Conditionally Applying Attributes

You can also specify that certain attributes in your response should only be applied when a particular test is satisfied

```php
public function toArray(Request $request): array
{
    return [
        'name' => $this->name,
        'breed' => $this->breed,
        'rating' => $this->when(Auth::user()->canSeeRatings(), 12)
    ]
}
```

## How Laravel Knows Which Model a Resource is tied to

- Laravel uses a convention where the Model is assumed to be the collection's class name without the trailing **Collection** or **Resource** portion of the class name
- You can however explicitly declare the **Model** via the `$collects` property of your resource collection:

```php
class UserCollection extends ResourceCollection
{
    public $collects = Member::class;
}
```
