<!-- @format -->

# Eloquent Events

Eloquent models fire events out into the void of your application every time certain actions happen, regardless of whether you’re listening

- we bind it in the `boot()` method of **AppServiceProvider**
- we attach listener with `Modelname::eventName()` method
- E.g attach a listener triggered when a new Contact is created

```php
class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $thirdPartyService = new SomeThirdPartyService;
        Contact::creating(function ($contact) use ($thirdPartyService) {
            try {
                $thirdPartyService->addContact($contact);
            } catch (Exception $e) {
                Log::error('Failed adding contact to ThirdPartyService; canceled.');
                return false; // Cancels Eloquent create()
            }
        });
    }
}
```
