<?php namespace TwitterOAuth\Exceptions;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

class TwitterException extends \Exception
{

    /**
     * String representation of the exception
     *
     * @return string
     */
    public function __toString()
    {
        return 'Twitter Response: [' . $this->code . '] ' . $this->message;
    }
}
