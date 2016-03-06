<?php namespace TwitterOAuth\Common;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

class Container implements \ArrayAccess
{

    /**
     * Dependencies Container
     *
     * @var array
     */
    protected $c = [];


    /**
     * Sets a parameter or an object.
     *
     * Objects must be defined as Closures.
     *
     * @param string $id The unique identifier for the parameter or object
     * @param mixed $value The value of the parameter or a closure to define an object
     */
    public function offsetSet($id, $value)
    {
        $this->c[$id] = $value;
    }

    /**
     * Gets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     * @return mixed The value of the parameter or an object
     * @throws \InvalidArgumentException if the identifier is not defined
     */
    public function offsetGet($id)
    {
        if (!isset($this->c[$id])) {
            throw new \InvalidArgumentException('Identifier "' . $id . '" is not defined.');
        }

        if (is_object($this->c[$id]) && ($this->c[$id] instanceof \Closure)) {
            return $this->c[$id] = $this->c[$id]();
        }

        return $this->c[$id];
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $id The unique identifier for the parameter or object
     * @return bool
     */
    public function offsetExists($id)
    {
        return isset($this->c[$id]);
    }

    /**
     * Unsets a parameter or an object.
     *
     * @param string $id The unique identifier for the parameter or object
     */
    public function offsetUnset($id)
    {
        unset($this->c[$id]);
    }
}
