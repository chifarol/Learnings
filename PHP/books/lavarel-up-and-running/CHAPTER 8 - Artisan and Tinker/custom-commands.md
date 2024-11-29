<!-- @format -->

# Writing Custom Artisan Commands

1. Run

   ```php
   // generates app/Console/Commands/{YourCommandName}.php
   php artisan make:command YourCommandName

   // OR
   php artisan make:command WelcomeNewUsers --command=<appname>:<action>
   ```

2. Define the `handle()` fxn and other properties

   ```php
   /// Run
   php artisan make:command WelcomeNewUsers --command=email:newusers

   class WelcomeNewUsers extends Command
   {
       /**
        * The name and signature of the console command
        *
        * @var string
        */
       protected $signature = 'email:newusers';

       /**
        * The console command description
        *
        * @var string
        */
       protected $description = 'Command description';

       /**
        * Execute the console command.
        */
       public function handle(): void
       {
           User::signedUpThisWeek()->each(function ($user) {
           Mail::to($user)->send(new WelcomeEmail);
           });
       }
   }

   ```

3. Run command
   ```php
   php artisan email:newusers
   // this command will grab every user that signed up this week and send them the welcome email.
   ```

- If you would prefer injecting your mail and user dependencies instead of using facades, you can typehint them in the **command constructor**, and Laravel’s container will inject them for you when the command is instantiated

```php
class WelcomeNewUsers extends Command
{
    public function __construct(UserMailer $userMailer)
    {
        parent::__construct();
        $this->userMailer = $userMailer
    }
    public function handle(): void
    {
        $this->userMailer->welcomeNewUsers();
    }
}
```

- Console commands are seen as being similar to route controllers: they’re not domain classes; they’re traffic cops that just route incoming requests to the correct behavior
- Laravel recommends packaging the application logic into a service class and injecting that service into your command

## Arguments and Options

- The `$signature` property doesn't just contain only the command name, you can also define any arguments and options

```php
protected $signature = 'password:reset {userId} {--sendEmail}';
```

### Arguments

- To define an argument, surround it with braces:

```php
password:reset {userId}
// php artisan email:newusers userId=14
```

- To make the argument optional, add a question mark:

```php
password:reset {userId?}
```

- To make it optional and provide a default, use:

```php
password:reset {userId=1}
```

### Options or flags

Options are similar to arguments, but they’re prefixed with "--" and can be used with no value

- To add a basic option, surround it with braces:

```php
password:reset {userId} {--sendEmail}
```

- If your option requires a value, add an = to its signature:

```php
password:reset {userId} {--password=}
```

- And if you want to pass a default value, add it after the =:

```php
password:reset {userId} {--queue=default}
```

### Array arguments and array options

Both for arguments and for options, if you want to accept an array as input, use the \* character

```php
password:reset {userIds*}
password:reset {--ids=*}

// Argument
php artisan password:reset 1 2 3
// Option
php artisan password:reset --ids=1 --ids=2 --ids=3
```

### Input descriptions

- add a colon and the description text within the curly braces

```php
protected $signature = 'password:reset {userId : The ID of the user} {--sendEmail : Whether to send user an email}';
```

## Using Input

### `argument()` and `arguments()`

How do we now use arguments and options two sets of methods for retrieving the values of arguments and options

```php
// With definition "password:reset {userId}"
php artisan password:reset 5

// returns an assoc array of all arguments
$this->arguments()
// same as arguments()
$this->argument()

// returns userId arg value
$this->argument("userId")
```

### `option()` and `options()`

```php
// With definition "password:reset {userId}"
php artisan password:reset all --userId=5

// returns an assoc array of all options
$this->options()
// same as options()
$this->option()

// returns userId option value
$this->option("userId")
```

## Prompts

- `ask()`
  Prompts the user to enter freeform text:
  ```php
  $email = $this->ask('What is your email address?');
  ```
- `secret()`
  Prompts the user to enter freeform text, but hides the typing with asterisks:
  ```php
  $password = $this->secret('What is the DB password?');
  ```
- `confirm()`
  Prompts the user for a yes/no answer, and returns a Boolean:
  ```php
  if ($this->confirm('Do you want to truncate the tables?')) {
  //
  }
  ```
  All answers except y or Y will be treated as a “no.”
- `anticipate()`
  Prompts the user to enter freeform text, and provides autocomplete suggestions.
  Still allows the user to type whatever they want:
  ```php
  $album = $this->anticipate('What is the best album ever?', [
  "The Joshua Tree", "Pet Sounds", "What's Going On"
  ]);
  ```
- `choice()`
  Prompts the user to choose one of the provided options. The last parameter is the
  default if the user doesn’t choose:
  ```php
  $winner = $this->choice( 'Who is the best football team?', ['Gators', 'Wolverines'], 0);
  ```

## Output

During the execution of your command, you might want to write messages to the user

```php
$this->info('Your command has run successfully.');
```

We also have

- `comment()` (orange),
- `question()` (highlighted teal)
- `error()` (highlighted red)
- `line()` (uncolored)
- `newLine()` (uncolored)

### Table output

```php
$headers = ['Name', 'Email'];
$data = [
 ['Dhriti', 'dhriti@amrit.com'],
 ['Moses', 'moses@gutierez.com'],
];
// Or, you could get similar data from the database:
$data = App\User::all(['name', 'email'])->toArray();

```

```php
$totalUnits = 350;
$this->output->progressStart($totalUnits);

$this->output->progressAdvance(20);
$this->output->progressAdvance(100);
$this->output->progressAdvance(200);

$this->output->progressFinish();
```

## Writing Closure-Based Commands

Dening an Artisan command using a closure in **routes/console.php** file

```php
// routes/console.php
Artisan::command(
 'password:reset {userId} {--sendEmail}',
 function ($userId, $sendEmail) {
 $userId = $this->argument('userId');
 // Do something...
 }
);

```

## Calling Artisan Commands in Other Artisan Commands

You can also call Artisan commands from other commands using

- `$this->call()`, (which is the same as Artisan::call()) or
- `$this->callSilent()`, which is the same but suppresses all output

## Calling Artisan Commands in Normal Code

Artisan commands are designed to be run from the command line but you can also call them from other code

You can either call a command using

- `Artisan::call()`which will return the command’s exit code
- `Artisan::queue()`

```php
Route::get('test-artisan', function () {
    $exitCode = Artisan::call('password:reset', [
        'userId' => 15,
        '--sendEmail' => true,
    ]);

    // OR using string syntax

    Artisan::call('password:reset 15 --sendEmail')
});

```
