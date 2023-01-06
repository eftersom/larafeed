<p align="center">
  <kbd><img src="https://github.com/eftersom/larafeed/raw/main/public/images/logo.jpg"></kbd>
</p>

## System Requirements

- PHP >= 7.3
- Laravel >= 6.* (through to 9.*)
## Introduction

Larafeed is an open source package for use within [Laravel](https://laravel.com) applications. This package focuses on allowing users to add an RSS feed reader to an existing Laravel application. Usable by multiple users within the same application.

<p align="center">
  <kbd><img src="https://github.com/eftersom/larafeed/blob/main/public/images/larafeedpageexample.png"></kbd>
</p>

Adding and removing feeds is simple! Search for a feed and if it's not right for you click on the 'x' to remove it.

A user can also share and view other user feeds within the same application.

## Initial Setup

#### Composer
Install by running the below command:

```composer require eftersom/larafeed```

#### Publishing Public Package Files
Once that's installed, run: 

```php artisan vendor:publish```

Choose ```larafeed-public```

This will install styles and images from the package and ensure they are useable in your project.

#### Running Migrations

Your migrations should be readily available, run: 

```php artisan migrate```

If something goes wrong, then rollback your migration and refer to the next section.

#### Optional: Publish Migrations

The current feed structure should work for most projects without the need to change the database. However, if you have a different database setup, for instance maybe you're not using uuids for your user id. Then you will also need to run the following command: 

```php artisan vendor:publish```

Then choose the option: 

```larafeed-migrations```

This will copy the required migrations from the larafeed package into the main application database/migrations folder. From here you must change the migrations to suit your needs. As in the previous example, if you're using a big int increments id instead of uuid for your user id you can change the type for the feed_user in {date_string_identifier}_create_feed_user_table.php like so: 

```$table->uuid('user_id')->index()``` -> ```$table->unsignedBigInteger('user_id')->index()```

And then run ```php artisan migrate``` as you otherwise would have done. This would in this example prevent foreign key constraint issues.

#### Set-up Complete!

And as far as initial set-up, that's it! You're ready to go.

## About Other Feed Types

Adding another feed type such as Atom to the feed application should be relatively simple. 

Publish your Larafeed config file and then add the feed type to the config array. It's important to get the values here correct for fetching
data from our Dom Document, Elements and Nodes. 

For example:

```
    'atom-example' => [
        'namespace' => null,
        'type'      => 'atom',
        'version'   => '1.0',
        'query'     => '/atom',
    ],
```

The rest involves a little bit of coding. For example, support the Atom type by adding a new Feed and Entry object to the 
```src\Feed and src\Feed\Entry``` folders e.g. ```Atom.php and AtomEntry.php```.

#### How to Add Feed Types

There are two options (and probably more besides.)

1- Add Feed and Entry classes to your main project and reference them in the published larafeed.php config. 

2- OR fork Larafeed and add the classes to your forked project.

## Adding Entry and Feed Classes to Main Project

#### Create Feed and Entry classes

First, add your classes to your main project(not the package), this can be anywhere that makes sense to your project, but for the example we will add these new feed classes to the folder ```app\Packages\Larafeed```.

You will need two classes: 

```app\Packages\Larafeed\Atom.php``` with the namespace: ```Eftersom\Larafeed\Feed```

```app\Packages\Larafeed\Entry\AtomEntry.php``` with the namespace: ```Eftersom\Larafeed\Feed\Entry```

It's important to include the -correct- namespaces so that composer can autoload our new Entry and Feed types correctly and so that the package will know where to look for them.

Remember in this example we're adding Atom feeds to the main application and not the package itself.

#### Important: Tell composer how to find the new package files

In your MAIN application composer.json your autoload specification should appear as below:

```
    "autoload": {
        "classmap": [
            "app/Packages/Larafeed/"
        ]
    },
```

Run: 

```composer dump-autoload```

#### Add your new type to the accepted-types config

Finally, all you need to do is add your new Feed type to the 'accepted-types' config as outlined further down in this document. You will need to publish the package config to your main application config files. 

```php artisan vendor:publish```

And choose ```larafeed-config```.

#### Feed Type Installation Complete!

And that's it! You should now be able to add any Feed type to the package using the above steps.

To see this in action in a fresh Laravel install, take a look at: 

https://github.com/eftersom/larafeed-example

In this blank basic Laravel install you can see how Atom feeds can be added to the project for the package to discover. Take a look at the app/Packages folder, composer.json and also the larafeed.php config file found within the main application. It really is as simple as that!

## Forking Larafeed

If you would prefer not to complete the previous steps then forking is always an option.

Add extra feed types to the package by forking it and then include further feed types directly within the forked package. Forking is a clean way to change the package whilst also allowing the merging of additional updates from the package author/authors.

## Changing your accepted-types configuration

Don't forget to change your Larafeed configuration by adding the new Atom accepted type and that's it! You're ready to go, the service for Larafeed should handle the new feed type dependant on how you've configured your Entry and your Feed objects.

```
    'accepted_types' => [
        'rss-20' => [
            'namespace' => null,
            'type'      => 'rss',
            'version'   => '2.0',
            'query'     => '/rss',
        ],
        'atom' => [
            'namespace' => 'http://www.w3.org/2005/Atom',
            'type'      => 'atom',
            'version'   => '1.0',
            'query'     => '/namespace:feed',
        ],
    ],
```

## Updates

The current version of the package is listed at the top of this document.

### Planned updates are as follows: 

- Vueification of the frontend. Optimisation, utilising vuex component loading for quicker page load times.
- Larafeed-social: share feeds and allow a user to set their feed to public or private. Currently all user feeds are public to all web facing users.
- Allow users to have multiple feeds.
- Include more feed types in base package.

## License

Larafeed is open source, please refer to [MIT license](license).
