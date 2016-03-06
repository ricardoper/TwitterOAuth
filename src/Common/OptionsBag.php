<?php namespace TwitterOAuth\Common;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

abstract class OptionsBag
{

    /**
     * Options Array
     *
     * @var array
     */
    protected $options = [];


    /**
     * Set an key and value to options array
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function setOption($key, $value)
    {
        $this->arraySet($this->options, $key, $value);

        unset($key, $value);
    }

    /**
     * Set an array with keys and values to options array
     *
     * @param  array $assoc
     * @return void
     */
    public function setOptions(array $assoc)
    {
        foreach ((array)$assoc as $key => $value) {
            $this->arraySet($this->options, $key, $value);
        }

        unset($assoc);
    }

    /**
     * Get options values from an key
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return $this->arrayGet($this->options, $key, $default);
    }

    /**
     * Get options values from an array of keys
     *
     * @param array $assoc
     * @param mixed $default
     * @return array
     */
    public function getOptions(array $assoc, $default = null)
    {
        $out = [];

        foreach ((array)$assoc as $key) {
            $out[$key] = $this->arrayGet($this->options, $key, $default);
        }

        unset($keys, $key);

        return $out;
    }

    /**
     * Check if exist key in options array
     *
     * @param string $key
     * @return bool
     */
    public function hasOption($key)
    {
        return $this->arrayHas($this->options, $key);
    }

    /**
     * Check if exist keys in options array
     *
     * @param array $keys
     * @return array
     */
    public function hasOptions(array $keys)
    {
        $out = [];

        foreach ((array)$keys as $key) {
            $out[$key] = $this->arrayHas($this->options, $key);
        }

        unset($keys, $key);

        return $out;
    }

    /**
     * Delete key from options array
     *
     * @param string $key
     */
    public function delOption($key)
    {
        $this->arrayDel($this->options, $key);

        unset($key);
    }

    /**
     * Delete keys from options array
     *
     * @param array $keys
     */
    public function delOptions(array $keys)
    {
        foreach ((array)$keys as $key) {
            $this->arrayDel($this->options, $key);
        }

        unset($keys, $key);
    }


    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * NOTE: Code Based On Laravel Framework Helpers ( https://laravel.com/ )
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $value
     * @return array
     */
    protected function arraySet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $key = array_shift($keys);

        if (is_array($value)) {
            $array[$key] = array_replace_recursive($array[$key], $value);
        } else {
            $array[$key] = $value;
        }

        unset($key, $keys, $value);

        return $array;
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * NOTE: Code Based On Laravel Framework Helpers ( https://laravel.com/ )
     *
     * @param  array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    protected function arrayGet($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                unset($array, $key, $segment);

                return $default;
            }

            $array = $array[$segment];
        }

        unset($key, $default, $segment);

        return $array;
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * NOTE: Code Based On Laravel Framework Helpers ( https://laravel.com/ )
     *
     * @param  array $array
     * @param  string $key
     * @return bool
     */
    protected function arrayHas($array, $key)
    {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                unset($array, $key, $segment);

                return false;
            }

            $array = $array[$segment];
        }

        unset($array, $key, $segment);

        return true;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * NOTE: Code Based On Laravel Framework Helpers ( https://laravel.com/ )
     *
     * @param  array $array
     * @param  array|string $keys
     * @return void
     */
    protected function arrayDel(&$array, $keys)
    {
        $original =& $array;

        foreach ((array)$keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }

            $key = array_shift($parts);

            unset($array[$key]);

            $array =& $original;
        }

        unset($keys, $key, $parts, $part);
    }
}
