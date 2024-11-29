<!-- @format -->

# Polymorphic

- Polymorphic relationship is where we have multiple Eloquent classes corresponding to the same relationship
- E.g an When a `User` is choosing an `Address`, it might be a `PickupStationAddress` or `DoorDeliveryAddress` which are 2 separate tables
  - here the `address` table will contain id, `mainaddress_id`, and `mainaddress_type` ( e.g., "pickupstation" or "doordelivery" ) fields.
- E.g A `User` can add both `Contacts` and `Events` to `Favourites`
  - here the `stars` table will contain id, `starrable_id`, and `starrable_type` (e.g., "contact" or "event") fields.

```php
class Address extends Model
{
    public function mainaddress()
    {
        return $this->morphTo();
    }
}
class PickupStationAddress extends Model
{
    public function address()
    {
        return $this->morphMany(Address::class, 'mainaddress');
    }
}
class DoorDeliveryAddress extends Model
{
    public function address()
    {
        return $this->morphMany(Address::class, 'mainaddress');
    }
}
```

```php
$pickup_station = PickupStation::create($valid_fields);
// instead of creating separately
$address = Address::create([
    'address_type'=>'pickup_station',
    'address_type_id'=>$pickup_station->id
]);

// Do
$pickup_station->address()->create();
// assuming it address is related to Order via order_id
$pickup_station->address()->create(['order_id' => Order::first()->id]);


// get mainAddress
Address::first()->mainaddress()->id
```

## Many-to-many polymorphic

- Imagine Contacts and Events can have many Tags and a Tag can belong to multiple Contacts and Events
- then we'll have a **taggables** table, which will have
  - `tag_id` (e.g Id of tag),
  - `taggable_id` (e.g id of contact/event), and
  - `taggable_type` (e.g "contact"/"event") fields

```php
class Contact extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
class Event extends Model
{
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
class Tag extends Model
{
    public function contacts()
    {
        return $this->morphedByMany(Contact::class, 'taggable');
    }
    public function events()
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }
}
```

```php
$tag = Tag::firstOrCreate(['name' => 'likes-cheese']);
$contact = Contact::first();
$contact->tags()->attach($tag->id);
```

## Child records updating parent record timestamps

- For example, if a CartItem is updated, maybe the Cart it’s connected to should be marked as having been updated as well
- add the the **method name** to the `$touches` array property

```php
class CartItem extends Model
{
    protected $touches = ['cart'];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
```

## List of events Eloquent can fire

• creating
• created
• updating
• updated
• saving
• saved
• deleting
• deleted
• restoring
• restored
• retrieved
