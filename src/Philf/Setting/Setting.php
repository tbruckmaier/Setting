<?php namespace Philf\Setting;

/*
 * ---------------------------------------------
 * | Do not remove!!!!                         |
 * |                                           |
 * | @package   PhoenixCore                    |
 * | @version   2.0                            |
 * | @develper  Phil F (http://www.Weztec.com) |
 * | @author    Phoenix Development Team       |
 * | @license   Free to all                    |
 * | @copyright 2013 Phoenix Group             |
 * | @link      http://www.phoenix-core.com    |
 * ---------------------------------------------
 *
 * Example syntax:
 * use Setting (If you are using namespaces)
 *
 * Single dimension
 * set:         Setting::set('name', 'Phil'))
 * get:         Setting::get('name')
 * forget:      Setting::forget('name')
 * has:         Setting::has('name')
 *
 * Multi dimensional
 * set:         Setting::set('names' , array('firstName' => 'Phil', 'surname' => 'F'))
 * setArray:    Setting::setArray(array('firstName' => 'Phil', 'surname' => 'F'))
 * get:         Setting::get('names.firstName')
 * forget:      Setting::forget('names.surname'))
 * has:         Setting::has('names.firstName')
 *
 * Clear:
 * clear:        Setting::clear()
 *
 * Using a different path (make sure the path exists and is writable) *
 * Setting::path('setting2.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 *
 * Using a different filename
 * Setting::filename('setting2.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 *
 * Using both a different path and filename (make sure the path exists and is writable)
 * Setting::path(app_path().'/storage/meta/sub')->filename('dummy.json')->set(array('names2' => array('firstName' => 'Phil', 'surname' => 'F')));
 */

/**
 * Class Setting
 * @package Philf\Setting
 */
class Setting{

    /**
     * The path to the file
     * @var string
     */
    protected $path;

    /**
     * The filename used to store the config
     * @var string
     */
    protected $filename;

    /**
     * The class working array
     * @var array
     */
    protected $settings;

    /**
     * Create the Setting instance
     * @param string $path      The path to the file
     * @param string $filename  The filename
     * @param interfaces\FallbackInterface $fallback
     */
    public function __construct($path, $filename, $fallback = null)
    {
        $this->path     = $path;
        $this->filename = $filename;
        $this->fallback = $fallback;

        // Load the file and store the contents in $this->settings
        $this->load($this->path, $this->filename);
    }

    /**
     * Set the path to the file to use
     * @param  string $path The path to the file
     * @return \Philf\Setting\Setting
     */
    public function path($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the filename to use
     * @param  string $filename The filename
     * @return \Philf\Setting\Setting
     */
    public function filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get a value and return it
     * @param  string $searchKey String using dot notation
     * @return Mixed             The value(s) found
     */
    public function get($searchKey = null)
    {
        if(empty($searchKey))
            return $this->settings;

        $default = microtime(true);

        if($default != $this->array_get($this->settings, $searchKey, $default))
        {
            return $this->array_get($this->settings, $searchKey);
        }

        if(!is_null($this->fallback) and $this->fallback->fallbackHas($searchKey))
        {
            return $this->fallback->fallbackGet($searchKey);
        }

        return null;
    }

     /**
     * Store the passed value in to the json file
     * @param $key
     * @param  mixed $value The value(s) to be stored
     * @return void
     */
    public function set($key, $value)
    {
        $this->array_set($this->settings,$key,$value);
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Forget the value(s) currently stored
     * @param  mixed $deleteKey The value(s) to be removed (dot notation)
     * @return void
     */
    public function forget($deleteKey)
    {
        $this->array_forget($this->settings,$deleteKey);
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * Check to see if the value exists
     * @param  string  $searchKey The key to search for
     * @return boolean            True: found - False not found
     */
    public function has($searchKey)
    {
        $default = microtime(true);

        if($default == $this->array_get($this->settings, $searchKey, $default) and !is_null($this->fallback))
        {
            return $this->fallback->fallbackHas($searchKey);
        }
        return $default != $this->array_get($this->settings, $searchKey, $default);
    }

    /**
     * Load the file in to $this->settings so values can be used immediately
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return \Philf\Setting\Setting
     */
    public function load($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;

        if (is_file($this->path.'/'.$this->filename))
        {
            $this->settings = json_decode(file_get_contents($this->path.'/'.$this->filename), true);
        }
        else
        {
            $this->settings = array();
        }

        return $this;
    }

    /**
     * Save the file
     * @param  string $path     The path to be used
     * @param  string $filename The filename to be used
     * @return void
     */
    public function save($path = null, $filename = null)
    {
        $this->path     = isset($path) ? $path : $this->path;
        $this->filename = isset($filename) ? $filename : $this->filename;

        $fh = fopen($this->path.'/'.$this->filename, 'w+');
        fwrite($fh, json_encode($this->settings, JSON_UNESCAPED_UNICODE));
        fclose($fh);
    }

    /**
     * Clears the JSON Config file
     */
    public function clear()
    {
        $this->settings = array();
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }

    /**
     * This will mass assign data to the Setting
     * @param array $data
     */
    public function setArray(array $data){
        foreach ($data as $key => $value)
        {
            $this->array_set($this->settings,$key,$value);
        }
        $this->save($this->path, $this->filename);
        $this->load($this->path, $this->filename);
    }
    protected function array_set(&$array, $key, $value)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) or ! is_array($array[$key]))
            {
                $array[$key] = array();
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

     /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    protected function array_get($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment)
        {
            if ( ! is_array($array) or ! array_key_exists($segment, $array))
            {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }

    /**
     * Remove an array item from a given array using "dot" notation.
     *
     * @param  array   $array
     * @param  string  $key
     * @return void
     */
    protected function array_forget(&$array, $key)
    {
        $keys = explode('.', $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            if ( ! isset($array[$key]) or ! is_array($array[$key]))
            {
                return;
            }

            $array =& $array[$key];
        }

        unset($array[array_shift($keys)]);
    }
}