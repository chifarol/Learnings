<!-- @format -->

### Form Method Spoong

- HTML forms only allow for `GET` or `POST`, so if you want any other sort of verb, you’ll need to do **form method spoofing**,
- This is done by adding a hidden `<input>` with `name="_method"` with the `value` of "PUT", "PATCH", or "DELETE"
- OR use `@method()` with the `value` of "PUT", "PATCH", or "DELETE"

```php
<form action="/tasks/5" method="POST">
 <input type="hidden" name="_method" value="DELETE">

 // OR
 @method('DELETE')
 </form>
```

#### CSRF Protection

- all routes in Laravel except “read-only” routes ( those using `GET`, `HEAD`, or `OPTIONS` ) are protected against Cross-Site Request Forgery (CSRF) attacks

```php
<form action="/tasks/5" method="POST">
 @csrf

 </form>
```

- this is done by requiring a token (generated at the start of every session),
- the `@csrf` directive renders the token in the form as `<input name="_token" value="..the token..">`
- every non–read-only route compares the submitted \_token against the session token
- A **cross-site request forgery** is when one website pretends to be another. The goal is for someone to hijack your users’ access to your website by submitting forms from their website to your website via the logged-in user’s browser.
- Laravel will check the `X-CSRF-TOKEN and X-XSRF-TOKEN` headers on every request, and valid tokens passed there will mark the CSRF protection as satisfied
