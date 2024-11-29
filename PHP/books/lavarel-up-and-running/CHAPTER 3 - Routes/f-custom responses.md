<!-- @format -->

# Other Responses

- apart from **views**, **redirects**, and **aborts** you can return HTTP responses like strings, json, trigger downloads, streams etc
- use the `response()` global helper or the `Response` facade

### response()->make()

```php
Route::post('form', function () {
    return response()->make("Hello, World!", 302, [
         // headers
         ])
});
```

### response()->json() and ->jsonp()

- pass your JSON-able content (**arrays**, **collections**, etc) to the `json()` method
- It’s just like make(), except it `json_encodes` your content.

```php
Route::post('form', function () {
    return response()->>json(User::all(), 302, [
         // headers
         ])
});
```

### response()->download(), ->streamDownload(), and ->file()

1. `->download()`

- - To send a file for the end user to download, pass either an `SplFileInfo` instance or a filename string
- - optional second parameter of the download filename

```php
//  send a file that’s at file501751.pdf and rename it, as it’s sent, to myFile.pdf
Route::post('download-pdf', function () {
  return response()->download('file501751.pdf', 'myFile.pdf')
});
```

2. `->file() `

- - display the same file in the browser (if it’s a PDF or an image or something else the browser can handle)
- - takes the same parameters as `response->download()`

```php
Route::post('download-pdf', function () {
  return response()->download('file501751.pdf', 'myFile.pdf')
});
```

3. `->>streamDownload().`

- - If you want to make some content from an external service available as a download without having to write it directly to your server’s disk
- - This method expects as parameters a closure that echoes a string, a filename, and optionally an array of headers

```php
return response()->streamDownload(function () {
 echo DocumentService::file('myFile')->getContent();
}, 'myFile.pdf');
```

## Custom response macros

- You can also create your own custom response types using macros.
- This allows you to define a series of modifications to make to the response and its provided content.

```php
class AppServiceProvider
{
  public function boot()
  {
    Response::macro('myJson', function ($content) {
      return response(json_encode($content))->withHeaders(['Content-Type' => 'application/json']);
    });
  }

}
```

Then, we can use it just like we would use the predefined json() macro:

```php
return response()->myJson(['name' => 'Sangeetha']);

```

## Custom responses using `responsable` interface

- If you’d like to customize how you’re sending responses and a macro doesn’t offer enough space or enough organization, or if you want any of your objects to be capable of being returned as a “response” with their own logic of how to be displayed

```php
use Illuminate\Contracts\Support\Responsable;
class GroupDonationDashboard implements Responsable
{
  public function __construct($group)
  {
    $this->group = $group;
  }
  public function budgetThisYear()
  {
  // ...
  }
  public function giftsThisYear()
  {
  // ...
  }
  public function toResponse()
  {
    return view('groups.dashboard')
  ->with('annual_budget', $this->budgetThisYear())
  ->with('annual_gifts_received', $this->giftsThisYear());
  }
}
```

And use it as

```php
class GroupController
{
  public function index(Group $group)
  {
    return new GroupDonationsDashboard($group);
  }
}
```
