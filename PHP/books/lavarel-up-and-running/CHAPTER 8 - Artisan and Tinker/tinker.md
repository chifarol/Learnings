<!-- @format -->

# Tinker

Tinker is an REPL, or (read–evaluate–print loop)
We start the REPL with `php artisan tinker` and are then presented with a blank prompt (>>>)

```
$ php artisan tinker
>>> $user = new App\User;
=> App\User: {}
>>> $user->email = 'matt@mattstauffer.com';
=> "matt@mattstauffer.com"
>>> $user->password = bcrypt('superSecret');
=> "$2y$10$TWPGBC7e8d1bvJ1q5kv.VDUGfYDnE9gANl4mleuB3htIY2dxcQfQ5"
>>> $user->save();
=> true

```
