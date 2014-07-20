<?php namespace Philf\Setting\interfaces;


/**
 * Class LaravelFallbackInterface
 * @package Philf\Setting\interfaces
 */
class LaravelFallbackInterface implements FallbackInterface {

    /**
     * @param $key
     * @return mixed
     */
    public function fallbackGet($key, $default = null)
    {
        return \App::make('config')->get($key, $default);
    }

    /**
     * @param $key
     * @return bool
     */
    public function fallbackHas($key)
    {
        return \App::make('config')->has($key);
    }
}