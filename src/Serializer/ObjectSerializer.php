<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Serializer;

class ObjectSerializer implements SerializerInterface
{
    /**
     * Format Output To Object
     *
     * @param  string $response
     * @return mixed
     */
    public function format($response)
    {
        return json_decode($response);
    }
}
