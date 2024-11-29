<!-- @format -->

# Error Handling in APIs

## Error Handling in Controller

```php
try {
    // some code
} catch (Exception $e) {
    return response()->json([
        'success' => false,
        'message' => $e->getMessage()
        ], 500);
}
```

## Custom Error Handling (Catch All)

- In `app/Exceptions/Handler.php`

```php
public function render($request, Exception $exception)
{
    if ($request->is('api/*')  || $request->wantsJson()) {   //add Accept: application/json in request
        return $this->handleApiException($request, $exception);
    } else {
        $retval = parent::render($request, $exception);
    }

    return $retval;
}
private function handleApiException($request, Exception $exception)
{
    $exception = $this->prepareException($exception);

    if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
        $exception = $exception->getResponse();
    }

    if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        $exception = $this->unauthenticated($request, $exception);
    }

    if ($exception instanceof \Illuminate\Validation\ValidationException) {
        $exception = $this->convertValidationExceptionToResponse($exception, $request);
    }

    return $this->customApiResponse($exception);
}

private function customApiResponse($exception)
{
    if (method_exists($exception, 'getStatusCode')) {
        $statusCode = $exception->getStatusCode();
    } else {
        $statusCode = 500;
    }

    $response = [];

    switch ($statusCode) {
        case 401:
            $response['message'] = 'Unauthorized';
            break;
        case 403:
            $response['message'] = 'Forbidden';
            break;
        case 404:
            $response['message'] = 'Not Found';
            break;
        case 405:
            $response['message'] = 'Method Not Allowed';
            break;
        case 422:
            $response['message'] = $exception->original['message'];
            $response['errors'] = $exception->original['errors'];
            break;
        default:
            $response['message'] = ($statusCode == 500) ? 'Whoops, looks like something went wrong' : $exception->getMessage();
            break;
    }

    if (config('app.debug')) {
        $response['trace'] = $exception->getTrace();
        $response['code'] = $exception->getCode();
    }

    $response['status'] = $statusCode;

    return response()->json($response, $statusCode);
}
```

## 404 Responses

- You can also customize the default 404 or "route not-found "fallback response for calls with a JSON content type
- To do so, add a Route::fallback() call to your API

```php
// routes/api.php
Route::fallback(function () {
    return response()->json(['message' => 'Route Not Found'], 404);
})->name('api.fallback.404');
```

### Triggering the Fallback Route

- If you want to customize which route is returned when Laravel catches “not found” exceptions
- Do so in App\Exceptions\Handler.php

```php
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
public function register(): void
{
    $this->renderable(function (NotFoundHttpException $e, Request $request) {
        if ($request->isJson()) {
            return Route::respondWithRoute('api.fallback.404');
        }
    });
}

```
