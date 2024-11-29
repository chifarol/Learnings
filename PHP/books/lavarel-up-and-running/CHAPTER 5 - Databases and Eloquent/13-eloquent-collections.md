<!-- @format -->

# Eloquent Collections

Query calls in Eloquent that has the potential to return multiple rows, **instead of an array** they’ll come packaged in an Eloquent **collection**

- There are many features what make collections better than plain arrays, they are like **arrays on steroids**

## Introducing the base collection (Illuminate\Support\Collection)

- To use in non-laravel projects run `composer require illuminate/collections`
- There are more than **60 methods** available in the Collection class
- They allow for iteration, so you can pass them to foreach
- they allow for both **arrow** & **array** access, so **if they’re keyed** you can try `$a = $collection['a']`

```php
//  create a collection
$collection = collect();
$collection->push(1);
// OR
$collection = collect([1, 2, 3]);
$user = collect(["name"=>"John","sex"=>"m"]);

// access element
$firstItem = $collection[0];
$name = $user['name'];
$sex = $user->name;

// filter array
$evens = $collection
 ->filter(function ($item) {
 return $item % 2 == 0;
 })

// reverse filter array
$odds = $collection->reject(function ($item) {
 return $item % 2 === 0;
});

// chain methods
$sum = $collection
 ->filter(function ($item) {
 return $item % 2 == 0;
 })->map(function ($item) {
 return $item * 10;
 })->sum();
```

## Lazy collections

Lazy collections leverage the power of PHP generators to process very large datasets while keeping the memory usage of your app very low

- You can use lazy collections with your Eloquent models with the `cursor()` method

```php
// instead of
$verifiedContacts = App\Contact::all()->filter(function ($contact) {
 return $contact->isVerified();
});

// do this
$verifiedContacts = Contact::cursor()->filter(function ($contact) {
 return $contact->isVerified();
});
```

## Add custom methods to collection

- E.g you could create a custom **OrderCollection** that extends **Illuminate\Database\Eloquent\Collection**,
- You can add your own custom methods e.g summarize a finiancial report

```php
class OrderCollection extends Collection
{
    public function sumBillableAmount()
    {
    return $this->reduce(function ($carry, $order) {
        return $carry + ($order->billable ? $order->amount : 0);
    }, 0);
    }
}

class Order extends Model
{
    public function newCollection(array $models = [])
    {
        return new OrderCollection($models);
    }
}

$orders = Order::all();
$billableAmount = $orders->sumBillableAmount();
```

## Eloquent Serialization

Serialization is what happens when you take something complex—an array or an object—and convert it to a **string/JSON** (In a web-based context, that string is often **JSON**)

- Collections also have `toArray()` and `toJson()`,

```php
$contactArray = Contact::first()->toArray();
$contactJson = Contact::first()->toJson();
$contactsArray = Contact::all()->toArray();
$contactsJson = Contact::all()->toJson();
```

- cast an Eloquent instance or collection to a string

```php
($userJSON = (string)$contact;)
```

## Returning models directly from route methods

- Laravel’s router eventually converts everything that route methods return to a string/JSON
- If you return the result of an Eloquent call in a controller, it will be automatically cast to a string/JSON,

```php
// routes/web.php
Route::get('api/contacts', function () {
 return Contact::all();
});
Route::get('api/contacts/{id}', function ($id) {
 return Contact::findOrFail($id);
});

```

## Returning models directly from route methods

- Laravel’s router eventually converts everything that route methods return to a string/JSON
- If you return the result of an Eloquent call in a controller, it will be automatically cast to a string/JSON,

```php
// routes/web.php
Route::get('api/contacts', function () {
 return Contact::all();
});
Route::get('api/contacts/{id}', function ($id) {
 return Contact::findOrFail($id);
});

```

## Hiding/Showing attributes from JSON

- It’s very common to use JSON returns in APIs
- set the Model's `public $hidden=[]` property to hide any attributes every time you cast to JSON
- set the Model's `public $visible=[]` property to show only specified attributes every time you cast to JSON
- set the Model's `protected $appends = [];` property to show only custom attributes every time you cast to JSON

```php
class Contact extends Model
{
 // hidden specified attributes upon serialization
 public $hidden = ['password', 'remember_token'];
 // show only specified attributes upon serialization
 public $visible = ['name', 'email', 'status']
 // show only custom attributes upon serialization
 protected $appends = ['full_name'];
}
```

- use `makeVisible()` to make an attribute visible just for a single call

```php
$array = $user->makeVisible('remember_token')->toArray();
```

## Eloquent Relationships

- Eloquent makes it easy to retrieve related objects (e.g **one-to-one**, **one-to-many** and **many-to-many**) on both ends (hasMany and belongsTo)
- By convention, assuming the primary key on the User Model is id, then the foreign key on the related Model, say Profile should "user_id"
- the model with primary key takes the **has**, the model with foreign key takes **belong** methods

  **Using relationships as query builders**

- if we call the relation method as a **property** ? it will just return the related object
- if we call the relation method as a **method**? it will return a prescoped query builder

## One to one

E.g a user has one profile, a profile belongs to only one user

```php
class User extends Model
{
    // tries to link id on User instance to user_id on Profile
    public function userProfile()
    {
        return $this->hasOne(Profile::class);
        // explicitly specify the foreign key
        return $this->hasOne(Profile::class,"user_id");
    }
}

$user = User::first();
// return related profile instance when used as a property
$profile = $user->userProfile;
// we can also access it with snake_case "user_profile"
$profile = $user->user_profile;

// return related profile query builder when used as a method
$profile = $user->userProfile()->where('bio','like','engineer');
```

```php
class Profile extends Model
{
public function user()
{
    return $this->belongsTo(User::class);
}
}

// access the related object
$profile = Profile::first();
$user = $profile->user;
```

## One to Many

- E.g a Cart instance can have many cart Items

```php
class Cart extends Model
{
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}

// access the related object
$cart = Cart::first();
$cartItems = $cart->items;
```

```php
class Cartitem extends Model
{
public function cart()
{
    return $this->belongsTo(Cart::class);
}
}

// access the related object
$cartItem = Cartitem::first();
$cartId = $cartItem->cart->id;
```

## Many to Many

- E.g a lecturer has many students and also a student can have many lecturers
- many-to-many relationships rely on a **pivot table** that connects the two since a single Lecturer can’t have a student_id column and a single Student can’t have a lecturer_id column
- The **conventional** naming of this table is done by
  - placing the two **singular** table names together,
  - ordered **alphabetically**, and
  - separating them by an **underscore** (\_)
- For example, **lecturer_student** pivot table with `student_id` and `lecturer_id` columns

```php
class Lecturer extends Model
{
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
```

- many to many, the inverse looks exactly the same

```php
class Student extends Model
{
    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class);
    }
}

```

### Eager Loading the Contents of a Relationship

- By default, Eloquent relationships are lazy loaded
- related models are only be loaded once you access them on the model e.g `$user->profile`
- Lazy loading can be memory-intensive esp. when iterating/looping
- use `with()` to eager load one or more related items
- use `withCount()` to eager load the count of model instances ina "model_count" colum

```php
// creates an joint table
$user = User::with(['profile','address'])->get();


// eager load a relationship but not all of the items,
$contacts = Contact::with(['addresses' => function ($query) {
 $query->where('mailable', true);
}])->get();

// Adds a "posts_count" integer to each Author with a count of that
// author's related posts
$authors = Author::withCount('posts')->get();

```

### Lazy eager loading

- You can choose to lazyload but eager load later with `load()`
- to load a relationship only when it **has not already** been loaded use `loadMissing()`

```php
$contacts = Contact::all();
if ($showPhoneNumbers) {
 $contacts->load('phoneNumbers');
 // load a relationship only when it has not already been loaded
 $contacts->loadMissing('phoneNumbers');
}

```

### Prevent Lazy loading

- disable lazy loading for your entire app at once in **AppServiceProvider**
- disable lazy loading for model in the model class

```php
use Illuminate\Database\Eloquent\Model;
public function boot()
{
    Model::preventLazyLoading(! $this->app->isProduction());
}

```

### Selecting only records that have a related item

- use `has()`

```php
// selects only Post instances that has its post_id in Comment
$postsWithComments = Post::has('comments')->get();

//  adjust the criteria further
$postsWithManyComments = Post::has('comments', '>=', 5)->get();

// nest the criteria
$usersWithPhoneBooks = User::has('contacts.phoneNumbers')->get();

// Gets all contacts with a phone number containing the string "867-5309"
$jennyIGotYourNumber = Contact::whereHas('phoneNumbers', function ($query) {
    $query->where('number', 'like', '%867-5309%');
})->get();
// Shortened version of the same code above
$jennyIGotYourNumber = Contact::whereRelation(
 'phoneNumbers',
 'number',
 'like',
'%867-5309')->get();
```

### Retrieve just one item from a hasMany relationship

- use `hasOne()`

```php
class Cart extends Model
{
public function newestItem(): HasOne
{
    return $this->hasOne(CartItem::class)->latestOfMany();
}
// retrieve the oldest item
public function oldestItem(): HasOne
{
    return $this->hasOne(CartItem::class)->oldestOfMany();
}
//  retrieve the item with the minimum or maximum value of any particular column (priority)
public function mostExpensiveItem(): HasOne
{
    return $this->hasOne(CartItem::class)->ofMany('amount', 'max');
}
}
```

### Retrieve nested relationships (Has many through)

- `hasManyThrough()`
- For example where a Group has many Users and each User has many Posts

```php
class Group extends Model
{

public function allPostsFromMembers()
{
    // Newer string-based syntax
    return $this->through('user')->has('post');

    // Traditional syntax
    return $this->hasManyThrough(Post::class, User::class);
}

}
```

### Getting data from the pivot table

```php
class User extends Model
{
    // User model
    public function groups()
    {
        return $this->belongsToMany(Group::class)
        // add created_at timestamps from pivot table GroupUser
        ->withTimestamps()
        // the pivot table will be accessed as "membership" not "group_user"
        ->as('membership');
    }
}

// Using this relationship:
User::first()->groups->each(function ($group) {
    echo sprintf(
    'User joined this group at: %s',
    $group->membership->created_at
    );
});
// the created_at column from pivot table "group_user" was included
// the pivot table is accessed as "membership" not "group_user"
```

### Inserting Related Items

- we can save related items on the fly without having to pass e.g "user_id" into a profile instance
- You must define the relationship in the model for this to work
- use can use `save()`,`saveMany()`,`create()`,`createMany()` on methods that have **has()**
- use `associate()` and `dissociate()` to perform these behaviors on the attached (“child”) item,

**For one-to-one r/ship or parent of one-to-many r/ship**

```php
// one-to-one
$user = User::first();
$profile = new Profile("phone" => "8008675309");
$user->profile()->save($profile)
// Or
$user->profile()->create(["phone" => "8008675309"])

// link parent from child
Profile::first()->user()->associate($user)


// parent of one-to-many
$cart = Cart::find(1);

// unlink parent from child
CartItem::first()->cart()->dissociate($cart)

// link child from parent
$cart->items()->saveMany(
    [
        CartItem::find(1),
        CartItem::find(2)
    ]
)
// can also use createMany
$cart->items()->createMany(...cartitems);
```

- you can use associate() and dissociate() on the method that returns the belongsTo relationship

**For many-to-many r/ship**

- as you save a many-to-many directly, you can add data to pivot table with the **2nd argument**
- you can also use `attach()` and `detach()`, they works with both concrete instances and Ids

```php
$lecturer = Lecturer::first();
$student = Student::first();
// add data to pivot table with the 2nd argument
$lecturer->students()->save($student, ['status' => 'registered']);

// use attach with both ids and instances
$lecturer->students()->attach(
    [ $student, Student::find(2), 3, 4 ],
    ['status' => 'registered']
);

$lecturer->students()->detach([1, 2]);
$lecturer->students()->detach(); // Detaches all contacts
```

- with `toggle()`, if the given ID is currently attached, it will be detached; and if it is currently detached, it will be attached

```php
$lecturer->students()->toggle([1, 3, 5]);
```

- use `updateExistingPivot()` to make changes just to the pivot record

```php
$user->contacts()->updateExistingPivot($contactId, [
 'status' => 'inactive',
]);

```

- use `sync()` to detaching all previous relationships and attaching new ones

```php
$lecturer->students()->sync([1, 3, 5]);
```
