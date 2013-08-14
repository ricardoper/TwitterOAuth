<?php

namespace TwitterOAuth;

class TwitterOAuth
{
    protected $url = 'https://api.twitter.com/1.1/';

    protected $format = 'json';

    protected $config = array();

    protected $call = '';

    protected $method = 'GET';

    protected $getParams = array();

    protected $postParams = array();


    public function __construct($config)
    {
        $defs = array(
            'consumer_key' => '',
            'consumer_secret' => '',
            'oauth_token' => '',
            'oauth_token_secret' => '',
        );

        $filters = array(
            'consumer_key' => FILTER_SANITIZE_STRING,
            'consumer_secret' => FILTER_SANITIZE_STRING,
            'oauth_token' => FILTER_SANITIZE_STRING,
            'oauth_token_secret' => FILTER_SANITIZE_STRING,
        );

        $this->config = filter_var_array(array_merge($defs, $config), $filters);

        unset($defs, $filters);
    }

    public function get($call, $getParams = null, $format = null)
    {
        $this->call = $call;

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        if ($format !== null) {
            $this->format = $format;
        }

        return $this->sendRequest();
    }

    protected function getParams($params)
    {
        $r = '';

        ksort($params);

        foreach ($params as $key => $value) {
            $r .= '&' . $key . '=' . urlencode($value);
        }

        unset($params, $key, $value);

        return trim($r, '&');
    }

    protected function getUrl($withParams = false)
    {
        $getParams = '';

        if ($withParams === true) {
            $getParams = $this->getParams($this->getParams);

            if (!empty($getParams)) {
                $getParams = '?' . $getParams;
            }
        }

        return $this->url . $this->call . '.' . $this->format . $getParams;
    }

    protected function getOauthParameters()
    {
        $time = time();

        return array(
            'oauth_consumer_key' => $this->config['consumer_key'],
            'oauth_nonce' => trim(base64_encode($time), '='),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => $time,
            'oauth_token' => $this->config['oauth_token'],
            'oauth_version' => '1.0'
        );
    }

    protected function getRequestString()
    {
        $params = array_merge($this->getParams, $this->postParams, $this->getOauthParameters());

        $params = $this->getParams($params);

        return urlencode($params);
    }

    protected function getSignatureBaseString()
    {
        $method = strtoupper($this->method);

        $url = urlencode($this->getUrl());

        return $method . '&' . $url . '&' . $this->getRequestString();
    }

    protected function getSigningKey()
    {
        return $this->config['consumer_secret'] . '&' . $this->config['oauth_token_secret'];
    }

    protected function calculateSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getSignatureBaseString(), $this->getSigningKey(), true));
    }

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

    protected function buildRequestHeader()
    {
        return array(
            'Authorization: OAuth ' . $this->getOauthString(),
            'Expect:'
        );
    }

    protected function sendRequest()
    {
        $url = $this->getUrl(true);

        $header = $this->buildRequestHeader();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        /* if ($postfields !== null) {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        } */

        $c = curl_init();

        curl_setopt_array($c, $options);

        $response = curl_exec($c);

        curl_close($c);

        unset($options, $c);

        return $response;
    }
}
