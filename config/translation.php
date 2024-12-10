<?php
return [
    'name' => 'Translation',

    /*
    |--------------------------------------------------------------------------
    | Translations table name
    |--------------------------------------------------------------------------
    |
    | Database table name.
    |
    */
    'table' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Translations manager
    |--------------------------------------------------------------------------
    |
    | The translation loader that overrides default Laravel
    | translation loader.
    |
    */
    'manager' => \App\Utilities\TranslationLoaderManager::class,

    /*
    |--------------------------------------------------------------------------
    | The default driver
    |--------------------------------------------------------------------------
    |
    | The translation loader driver to be used. If you want to disable
    | package Database driver, set this value to null, this will ignore
    | package Database driver and fallback to native FileLoader.
    |
    */
    'driver' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Preferred order
    |--------------------------------------------------------------------------
    |
    | By default, Laravel uses its native FileLoader to load translations.
    | Since this package adds an extra database driver to load translations.
    |
    | This package will load translations from both sources, and then
    | decides which source to return translations from (depending on the value
    | of this parameter).
    |
    | Options:
    | 1. database, loads translations from the database (if they exist in the
    |    database) first, then fallback to native Laravel FileLoader.
    |
    | 2. native, loads translations from the native Laravel FileLoader,
    |    then fallback to the database loader if necessary.
    |
    */
    'preferred_loader' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Translation drivers
    |--------------------------------------------------------------------------
    |
    | By default, this package is configured to use at most one
    | extra translation loader in addition to the native Laravel FileLoader.
    |
    */
    'drivers' => [
        'default' => [
            'loader' => \App\Utilities\Drivers\Database::class,
            'entity' => \App\Models\Translation::class,
        ],
    ],
];
