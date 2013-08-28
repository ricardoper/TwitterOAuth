<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Dirk Luijk <dirk@luijkwebcreations.nl>
 * @copyright 2013
 */

namespace TwitterOAuth\Exception;

class TwitterException extends \Exception
{
    public function __toString()
    {
        return "Twitter API Response: [{$this->code}] {$this->message} (" . __CLASS__ . ") ";
    }
}
