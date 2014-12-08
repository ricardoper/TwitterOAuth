<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Serializer;

interface SerializerInterface
{
    /**
     * Format Output
     *
     * @param  string $response
     * @return mixed
     */
    public function format($response);
}
