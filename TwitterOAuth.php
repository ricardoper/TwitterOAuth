<?php

namespace TwitterOAuth;

class TwitterOAuth
{
    protected $url = 'https://api.twitter.com/1.1/';

    protected $format = 'json';

    protected $config = array();

    protected $params = array();


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

    public function get($call, $params = null, $format = null)
    {
        if (!is_null($format)) {
            $this->format = $format;
        }

        if (!is_null($params) && is_array($params)) {
            $this->params = $params;
        }

        return $this->processRequest($call);
    }

    protected function buildUrl($call)
    {
        return $this->url . $call . '.' . $this->format;
    }

    protected function getUrl($call)
    {
        $url = $this->buildUrl($call);

        $params = $this->params;

        $r = '';

        ksort($params);

        foreach ($params as $key => $value) {
            $r .= '&' . $key . '=' . $value;
        }

        unset($params, $key, $value);

        return $url . ((empty($r)) ? '' : '?') . $r;
    }

    protected function getOauthRequest()
    {
        $time = time();

        return array(
            'oauth_consumer_key' => $this->config['consumer_key'],
            'oauth_nonce' => $time,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->config['oauth_token'],
            'oauth_timestamp' => $time,
            'oauth_version' => '1.0'
        );
    }

    protected function createBase($method, $call, $oauth)
    {
        $url = $this->buildUrl($call);

        $params = array_merge($this->params, $oauth);

        $r = array();

        ksort($params);

        foreach ($params as $key => $value) {
            $r[] = $key . '=' . rawurlencode($value);
        }

        unset($params, $key, $value);

        return $method . '&' . rawurlencode($url) . '&' . rawurlencode(implode('&', $r));
    }

    protected function buildSignature($base)
    {
        $ckey = rawurlencode($this->config['consumer_secret']) . '&' .
            rawurlencode($this->config['oauth_token_secret']);

        return base64_encode(hash_hmac('sha1', $base, $ckey, true));
    }

    protected function buildHeaders($oauth, $sign)
    {
        $oauth = array_merge($oauth, array('oauth_signature' => $sign));

        $r = 'Authorization: OAuth ';

        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = $key . '="' . rawurlencode($value) . '"';
        }

        $r .= implode(', ', $values);

        unset($values, $key, $value);

        return array(
            $r,
            'Expect:'
        );
    }

    protected function sendRequest($url, $headers, $postfields = null)
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        if (!is_null($postfields)) {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        }

        $c = curl_init();

        curl_setopt_array($c, $options);

        $response = curl_exec($c);

        curl_close($c);

        unset($options, $c);

        return $response;
    }

    protected function processRequest($call, $method = 'GET', $postfields = null)
    {
        $url = $this->getUrl($call);

        $oauth = $this->getOauthRequest();

        $base = $this->createBase($method, $call, $oauth);

        $sign = $this->buildSignature($base);

        $headers = $this->buildHeaders($oauth, $sign);

        unset($oauth, $base, $sign);

        return $this->sendRequest($url, $headers, $postfields);
    }
}
