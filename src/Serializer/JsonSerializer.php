<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Serializer;

class JsonSerializer implements SerializerInterface
{
    /**
     * Format Output To Json
     *
     * @param  string $response
     * @return mixed
     */
    public function format($response)
    {
        if (!headers_sent()) {
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Tue, 19 May 1981 18:00:00 GMT');
            header('Content-type: application/json; charset=utf-8');
        }

        return $response;
    }
}
