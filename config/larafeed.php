<?php

return [
    'fallback_locale' => 'en',
    
    /*
    |--------------------------------------------------------------------------
    | Base Domain
    |--------------------------------------------------------------------------
    |
    | domain is set to null, Larafeed will reside under the defined base
    | path below. Otherwise, this will be used as the subdomain.
    |
    */

    'domain' => env('LARAFEED_DOMAIN', 'larafeed'),

    /*
    |--------------------------------------------------------------------------
    | Base Path
    |--------------------------------------------------------------------------
    |
    | This is the URI where Larafeed will be accessible from. If the path
    | is set to null, Larafeed will reside under the same path name as
    | the application. Otherwise, this is used as the base path.
    |
    */

    'path' => env('LARAFEED_PATH', 'larafeed'),

    /*
    |--------------------------------------------------------------------------
    | Name
    |--------------------------------------------------------------------------
    |
    | Package name, the name of the package to be displayed on the frontend etc.
    |
    */

    'name' => env('LARAFEED_NAME', 'Larafeed'),

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    |
    | Controls how many items display per page.
    |
    */

    'pagination'  => '6',

    'entry_count' => '5',

    /*
    |--------------------------------------------------------------------------
    | Icon
    |--------------------------------------------------------------------------
    |
    | The package comes with a default icon, but this can be overridden here.
    |
    */

    'favicon' => '/vendor/larafeed/icons/favicon.ico',

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will be attached to every route in Larafeed, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with the list.
    |
    */

    'middleware' => [
        'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    |
    | This is the storage disk Larafeed will use to put file uploads. You may
    | use any of the disks defined in the config/filesystems.php file and
    | you may also change the maximum upload size from its 3MB default.
    |
    */

    'storage_disk' => env('LARAFEED_STORAGE_DISK', 'local'),

    'storage_path' => env('LARAFEED_STORAGE_PATH', 'public/Larafeed'),

    'upload_filesize' => env('LARAFEED_UPLOAD_FILESIZE', 3145728),

    /*
    |--------------------------------------------------------------------------
    | Accepted Types
    |--------------------------------------------------------------------------
    |
    | These are the types of feed that are allowed in our feed reader service. 
    | 
    | Below these accepted types you will also find the config for any additional
    | Feed or Entry classes you wish to include alongside the Larafeed package.
    |
    | Included are commented out options to demonstrate how Atom Feed types can be
    | Added to work alongside the Larafeed package.
    |
    */

    'accepted_types' => [
        'rss-20' => [
            'namespace' => null,
            'type'      => 'rss',
            'version'   => '2.0',
            'query'     => '/rss',
        ],
        /*
        'atom' => [
            'namespace' => 'http://www.w3.org/2005/Atom',
            'type'      => 'atom',
            'version'   => '1.0',
            'query'     => '/namespace:feed',
        ],
        */
    ],
];
