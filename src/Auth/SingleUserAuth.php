<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

namespace TwitterOAuth\Auth;

class SingleUserAuth extends AuthAbstract
{
    /**
     * Expects the follow parameters:
     *   - consumer_key         Twitter API key               * Required
     *   - consumer_secret      Twitter API secret            * Required
     *   - oauth_token          Twitter Access token
     *   - oauth_token_secret   Twitter Access token secret
     */
    protected $requiredCredentials = array(
        'consumer_key',
        'consumer_secret',
    );

    protected $urls = array(
        'domain' => 'https://api.twitter.com/',
        'upload' => 'https://upload.twitter.com/',
        'api' => '1.1/',
    );


    /**
     * Gets the Twitter Access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        if (empty($this->credentials['oauth_token'])) {
            return null;
        }

        return $this->credentials['oauth_token'];
    }

    /**
     * Gets the Twitter Access token secret
     *
     * @return null|string
     */
    public function getAccessTokenSecret()
    {
        if (empty($this->credentials['oauth_token_secret'])) {
            return null;
        }

        return $this->credentials['oauth_token_secret'];
    }

    /**
     * Send a POST call to Twitter API via OAuth
     *
     * @param string $call Twitter resource string
     * @param array $postParams POST parameters to send
     * @param array $getParams GET parameters to send
     * @return mixed  Output with selected format
     * @throws \TwitterOAuth\Exception\TwitterException
     */
    public function post($call, array $postParams = null, array $getParams = null)
    {
        $this->resetCallState();

        $this->call = $call;

        $this->method = 'POST';

        if ($postParams !== null && is_array($postParams)) {
            $this->postParams = $postParams;
        }

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        $response = $this->getResponse();

        $response['body'] = $this->findExceptions($response);

        $this->headers = $response['headers'];

        unset($call, $postParams, $getParams);

        return $this->serializer->format($response['body']);
    }

    /**
     * Send a POST call with media upload to Twitter API via OAuth
     *
     * @param string $call Twitter resource string
     * @param string $filename File location to upload
     * @return mixed  Output with selected format
     * @throws \TwitterOAuth\Exception\CurlException
     * @throws \TwitterOAuth\Exception\TwitterException
     */
    public function postMedia($call, $filename)
    {
        $this->resetCallState();

        $this->call = $call;

        $this->method = 'POST';

        $this->withMedia = true;

        $mimeBoundary = sha1($call . microtime());

        $params = array(
            'post' => $this->buildMultipart($mimeBoundary, $filename),
            'headers' => $this->buildUploadMediaHeader($mimeBoundary),
        );

        $response = $this->curl->send($this->getUrl(), $params);

        $obj = json_decode($response['body']);

        if (!$obj || !isset($obj->token_type) || $obj->token_type != 'bearer') {
            $this->findExceptions($response);
        }

        $this->headers = $response['headers'];

        $this->withMedia = null;

        unset($call, $filename, $mimeBoundary, $params, $obj);

        return $this->serializer->format($response['body']);
    }


    /**
     * Getting OAuth parameters to be used in request headers
     *
     * @return array  OAuth parameters
     */
    protected function getOauthParameters()
    {
        $time = time();

        return array(
            'oauth_consumer_key' => $this->getConsumerKey(),
            'oauth_nonce' => trim(base64_encode($time), '='),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $time,
            'oauth_token' => $this->getAccessToken(),
            'oauth_version' => '1.0'
        );
    }

    /**
     * Converting all parameters agetrrays to a single string with encoded values
     *
     * @return string  Single string with encoded values
     */
    protected function getRequestString()
    {
        $params = array_merge($this->getParams, $this->postParams, $this->getOauthParameters());

        $params = $this->curl->getParams($params);

        return rawurlencode($params);
    }

    /**
     * Getting OAuth signature base string
     *
     * @return string  OAuth signature base string
     */
    protected function getSignatureBaseString()
    {
        $method = strtoupper($this->method);

        $url = rawurlencode($this->getUrl());

        return $method . '&' . $url . '&' . $this->getRequestString();
    }

    /**
     * Getting a signing key
     *
     * @return string  Signing key
     */
    protected function getSigningKey()
    {
        return $this->getConsumerSecret() . '&' . $this->getAccessTokenSecret();
    }

    /**
     * Calculating the signature
     *
     * @return string  Signature
     */
    protected function calculateSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getSignatureBaseString(), $this->getSigningKey(), true));
    }

    /**
     * Converting OAuth parameters array to a single string with encoded values
     *
     * @return string  Single string with encoded values
     */
    protected function getOauthString()
    {
        $oauth = array_merge($this->getOauthParameters(), array('oauth_signature' => $this->calculateSignature()));

        ksort($oauth);

        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        $oauth = implode(', ', $values);

        unset($values, $key, $value);

        return $oauth;
    }

    /**
     * Building request HTTP headers
     *
     * @return array  HTTP headers
     */
    protected function buildRequestHeader()
    {
        return array(
            'Authorization: OAuth ' . $this->getOauthString(),
            'Expect:'
        );
    }

    /**
     * Building upload media headers
     *
     * @param string $mimeBoundary MIME boundary ID
     * @return array  HTTP headers
     */
    protected function buildUploadMediaHeader($mimeBoundary)
    {
        return array(
            'Authorization: OAuth ' . $this->getOauthString(),
            'Content-Type: multipart/form-data; boundary=' . $mimeBoundary,
            'Expect:'
        );
    }
}
