# Autoseed

## v0.0.1

Caution : This package is in alpha version !

## Description

Autoseed is a package to provide a new artisan command :

php artisan autoseed:generate

Prior to execute this command, you have to generate your models
When it's done, you can launch the command.

This package will generate for you :
- factories files
- seeders files
- an updated DatabaseSeeder.php file
- a call to php artisan dump-autoload command (this command is integrated inthis package)
- a call to php artisan db:seed command to generate fake datas for you

This package use Faker library from François Zaninotto.

## Limitations

Remember that this is currently an alpha version.

In this version, number of generate items are 10.
If you want regenerate the files or use again the command, you should deletes files generated in database/factories and database/seeds directory and remove lines added in DatabaseSeeder.php

## Futures versions

- Add config files (for settings table and fieds to ignore)
- Add parameters to determine how many items must be generated
- much more with your comments after use

