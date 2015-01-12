<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Auth;

use TwitterOAuth\Common\Curl;
use TwitterOAuth\Serializer\SerializerInterface;
use TwitterOAuth\Exception\TwitterException;
use TwitterOAuth\Exception\FileNotFoundException;
use TwitterOAuth\Exception\FileNotReadableException;
use TwitterOAuth\Exception\UnsupportedMimeException;
use TwitterOAuth\Exception\MissingCredentialsException;

abstract class AuthAbstract
{
    const EOL = "\r\n";

    protected $credentials = array();
    protected $serializer = null;
    protected $curl = null;

    protected $call = null;
    protected $method = null;
    protected $withMedia = null;
    protected $getParams = array();
    protected $postParams = array();

    protected $headers = null;


    /**
     * Authentication Base
     *
     * @param array $credentials Credentials Array
     * @param SerializerInterface $serializer Output Serializer
     * @throws MissingCredentialsException
     */
    public function __construct(array $credentials, SerializerInterface $serializer)
    {
        $this->validateCredentials($credentials);

        $this->credentials = $credentials;

        $this->serializer = $serializer;

        $this->curl = new Curl();

        unset($credentials, $serializer);
    }

    /**
     * Gets the Twitter API key
     *
     * @return null|string
     */
    public function getConsumerKey()
    {
        if (empty($this->credentials['consumer_key'])) {
            return null;
        }

        return $this->credentials['consumer_key'];
    }

    /**
     * Gets the Twitter API secret
     *
     * @return null|string
     */
    public function getConsumerSecret()
    {
        if (empty($this->credentials['consumer_secret'])) {
            return null;
        }

        return $this->credentials['consumer_secret'];
    }

    /**
     * Gets Serializer
     *
     * @return null|SerializerInterface
     */
    public function getSerializer()
    {
        if (empty($this->serializer)) {
            return null;
        }

        return $this->serializer;
    }

    /**
     * Get response headers
     *
     * @param null $key
     * @return array|string|false
     */
    public function getHeaders($key = null)
    {
        if ($key === null) {
            return $this->headers;
        }

        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        }

        return false;
    }

    /**
     * Send a GET call to Twitter API via OAuth
     *
     * @param string $call Twitter resource string
     * @param array $getParams GET parameters to send
     * @return mixed  Output with selected format
     * @throws TwitterException
     */
    public function get($call, array $getParams = null)
    {
        $this->resetCallState();

        $this->call = $call;

        $this->method = 'GET';

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        $response = $this->getResponse();

        $response['body'] = $this->findExceptions($response);

        $this->headers = $response['headers'];

        unset($call, $getParams);

        return $this->serializer->format($response['body']);
    }


    /**
     * Validate Credentials Array
     *
     * @param $credentials
     * @throws MissingCredentialsException
     */
    protected function validateCredentials($credentials)
    {
        $credentials = array_filter($credentials);

        $keys = array_keys($credentials);

        $diff = array_diff($this->requiredCredentials, $keys);

        if (!empty($diff)) {
            throw new MissingCredentialsException('Missing Credentials: ' . implode($diff, ', '));
        }

        unset($credentials, $keys, $diff);
    }

    /**
     * Getting full URL from a Twitter resource
     *
     * @return string  Full URL
     */
    protected function getUrl()
    {
        $domain = $this->urls['domain'];

        $apiVersion = $this->urls['api'];

        $jsonExt = '.json';

        if (isset($this->withMedia) && $this->withMedia === true) {
            $domain = $this->urls['upload'];
        }

        if ($this->call === 'oauth/request_token' || $this->call === 'oauth/access_token') {
            $apiVersion = '';
            $jsonExt = '';
        }

        return $domain . $apiVersion . $this->call . $jsonExt;
    }

    /**
     * Returns raw response body
     *
     * @return array
     * @throws \TwitterOAuth\Exception\CurlException
     */
    protected function getResponse()
    {
        $url = $this->getUrl();

        $params = array(
            'get' => $this->getParams,
            'post' => $this->postParams,
            'headers' => $this->buildRequestHeader(),
        );

        return $this->curl->send($url, $params);
    }

    /**
     * Processing Twitter Exceptions in case of error
     *
     * @param array $response Raw response
     * @return string
     * @throws TwitterException
     */
    protected function findExceptions($response)
    {
        $response = $response['body'];

        $data = json_decode($response, true);

        if (isset($response[0]) && $response[0] !== '{' && $response[0] !== '[' && !$data) {
            if (strpos($response, 'oauth_token=') !== false) {
                parse_str($response, $data);
            }

            if (empty($data) || !is_array($data)) {
                throw new TwitterException($response, 0);
            }

            return json_encode($data);
        }

        if (!empty($data['errors']) || !empty($data['error'])) {
            if (!empty($data['errors'])) {
                $data = current($data['errors']);
            }

            if (empty($data['message']) && !empty($data['error'])) {
                $data['message'] = $data['error'];
            }

            if (!isset($data['code']) || empty($data['code'])) {
                $data['code'] = 0;
            }

            throw new TwitterException($data['message'], $data['code']);
        }

        unset($data);

        return $response;
    }

    /**
     * Build a multipart message
     *
     * @param string $mimeBoundary MIME boundary ID
     * @param string $filename File location
     * @return string  Multipart message
     */
    protected function buildMultipart($mimeBoundary, $filename)
    {
        $binary = $this->getBinaryFile($filename);

        $details = pathinfo($filename);

        $type = $this->supportedMimes($details['extension']);

        $data = '--' . $mimeBoundary . static::EOL;
        $data .= 'Content-Disposition: form-data; name="media"; filename="' . $details['basename'] . '"' . static::EOL;
        $data .= 'Content-Type: application/octet-stream' . static::EOL . static::EOL;
        $data .= $binary . static::EOL;
        $data .= '--' . $mimeBoundary . '--' . static::EOL . static::EOL;

        unset($mimeBoundary, $filename, $binary, $details, $type);

        return $data;
    }

    /**
     * Twitter supported MIME types for media upload
     *
     * @param string $mime File extension
     * @return mixed  MIME type
     * @throws UnsupportedMimeException
     */
    protected function supportedMimes($mime)
    {
        $mimes = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
        );

        if (isset($mimes[$mime])) {
            return $mimes[$mime];
        }

        throw new UnsupportedMimeException;
    }

    /**
     * Get binary data of a file
     *
     * @param string $filename File location
     * @return string
     * @throws FileNotFoundException
     * @throws FileNotReadableException
     */
    protected function getBinaryFile($filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException;
        }

        if (!is_readable($filename)) {
            throw new FileNotReadableException;
        }

        ob_start();

        readfile($filename);

        $binary = ob_get_contents();

        ob_end_clean();

        unset($filename);

        return $binary;
    }

    /**
     * Reset Call State
     */
    protected function resetCallState()
    {
        $this->call = null;
        $this->method = null;
        $this->withMedia = null;
        $this->getParams = array();
        $this->postParams = array();
        $this->headers = null;
    }
}
