<!-- @format -->

## Blade Service Injection

There are 3 primary types of data we’re most likely to inject into a view

1. collections of data to iterate over
2. single objects that we’re displaying on the page
3. services that generate data or views

- Blade Service Injection makes it easy to make certain data or functionality available to every instance of a view, without having to inject it via the route definition every time.

```php
Route::get('backend/sales', function (AnalyticsService $analytics) {
 return view('backend.sales-graphs')
 ->with('analytics', $analytics);
});

// in view
<div class="finances-display">
 {{ $analytics->getBalance() }} / {{ $analytics->getBudget() }}
</div>
```

Instead of doing this, we can inject the service using the `@inject` method

```php
// in view
@inject('analytics', 'App\Services\Analytics')
<div class="finances-display">
 {{ $analytics->getBalance() }} / {{ $analytics->getBudget() }}
</div>
```
