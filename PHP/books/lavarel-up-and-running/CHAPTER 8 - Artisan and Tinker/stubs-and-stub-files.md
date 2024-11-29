<!-- @format -->

# Customizing Generator Stubs

Any Artisan commands that generate files (e.g. make:model and make:controller)
use “stub” files that the command then copies and modifies to create the newly generated files. You can customize these stubs in your applications.
To customize the stubs in your applications, run php artisan stub:publish, which will export the stub files into a stub/ directory where you can customize them
