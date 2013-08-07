# Laravel Setting

Persistent configuration settings for Laravel - Create, Read, Update and Delete settings stored in files using JSON.

This package was the result of me not being able to save new settings to config files in a more persistent way.

This package was designed not to replace the config solution currently offered by Laravel but rather complement it and be used in unison with it.

By default the data is stored in app_path().'/storage/meta/setting.json' but this can eaisly be changed either in the config file or on the fly realtime.

This package also provides a fallback for the Laravel Config facade, you can set it in the config, if the key is not found in the json file it will look it up in the Config facade.

## Installation
Require this package in your composer.json:

    "philf/setting": "dev-master"

And add the ServiceProvider to the providers array in app/config/app.php

    'Philf\Setting\SettingServiceProvider',

## Usage

Config

    return array(
    'path'     => app_path().'/storage/meta',
    'filename' => 'setting.json',
    'fallback' => true,
    );

It's simple to use - just think arrays :)

set is an alias for put so you can use either

Single dimension

    set:     Setting::set('name', 'Phil')
    get:     Setting::get('name')
    forget:  Setting::forget('name')
    has:     Setting::has('name')

Multi dimensional

    set:     Setting::set('names.firstName', 'Phil');
    set:     Setting::set('names.surname', 'F');
        or
    set:     Setting::set('names', array('firstName' => 'Phil', 'surname' => 'F'));
    get:     Setting::get('names.firstName')
    forget:  Setting::forget('names.surname'))
    has:     Setting::has('names.firstName')

Using a different path (make sure the path exists and is writable) *

    Setting::path(app_path().'/storage/meta/sub')->set('names2', array('firstName' => 'Phil', 'surname' => 'F'));

Using a different filename

    Setting::filename('setting2.json')->set('names2', array('firstName' => 'Phil', 'surname' => 'F'));

Using both a different path and filename (make sure the path exists and is writable)

    Setting::path(app_path().'/storage/meta/sub')->filename('dummy.json')->set('names2', array('firstName' => 'Phil', 'surname' => 'F'));

## License

Laravel Setting is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
