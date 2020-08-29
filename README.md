# Autoseed

## v0.0.1

Caution : This package is in alpha version !

## Description

Autoseed is a package to provide a new artisan command :

```
php artisan autoseed:generate
```

Obviously, prior to execute this command, you had to set your .env with your database credentials and you have to generate your models with 

```
php artisan migrate
```


When it's done, you can launch the command.

This package will generate for you :
- factories files
- seeders files
- an updated DatabaseSeeder.php file
- a call to php artisan dump-autoload command (this command is integrated inthis package)
- a call to php artisan db:seed command to generate fake datas for you

This package use Faker library from François Zaninotto.

## How it works

Autoseed analyze your database extracts tables names and watch every fields.
Autoseed try to associate automatically field to the good Faker data type bases on field name (ex. 'name' matches to $faker->name, 'title' matches to text(max = 50), etc.)


## Installation

You just need to launch this composer command :

```
composer require happytodev/autoseed
```

## Limitations

Remember that this is currently an alpha version.

In this version, number of generate items are 10.
If you want regenerate the files or use again the command, you should deletes files generated in database/factories and database/seeds directory and remove lines added in DatabaseSeeder.php

## Futures versions

- Add config files (for settings table and fieds to ignore)
- Add parameters to determine how many items must be generated
- More smarter with more fields describe for a good association with faker data
- much more with your comments after use

