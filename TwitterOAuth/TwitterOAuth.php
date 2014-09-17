<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2013
 */

namespace TwitterOAuth;

use TwitterOAuth\Exception\TwitterException;

class TwitterOAuth
{
    protected $url = 'https://api.twitter.com/1.1/';

    protected $auth_url = 'https://api.twitter.com/';

    protected $outputFormats = array('text', 'json', 'array', 'object');

    protected $defaultFormat = 'object';

    protected $config = array();

    protected $call = '';

    protected $method = 'GET';

    protected $getParams = array();

    protected $postParams = array();

    protected $encoded_bearer_credentials = null;

    protected $bearer_access_token = null;

    protected $headers = null;

    protected $response = null;

    /**
     * Prepare a new conection with Twitter API via OAuth
     *
     * The application ``consumer_key`` and ``consumer_key_secret`` are required 
     * for most actions, unless when using application-only authentication with a bearer-token.
     * The ``oauth_token`` and ``oauth_token_secret`` are required for user type actions.
     *
     * @param array $config Configuration array with OAuth access data
     */
    public function __construct(array $config)
    {
        $required = array(
            'consumer_key' => '',
            'consumer_secret' => '',
        );

        if (count(array_intersect_key($required, $config)) !== count($required)) {
            throw new \Exception('Missing parameters in configuration array');
        }

        if (!isset($config['output_format']) || !in_array($config['output_format'], $this->outputFormats)) {
            $config['output_format'] = $this->defaultFormat;
        }

        $this->config = $config;

        unset($required, $config);
    }

    /**
     * Send a GET call to Twitter API via OAuth
     *
     * @param string $call Twitter resource string
     * @param array $getParams GET parameters to send
     * @return mixed Output with selected format
     */
    public function get($call, array $getParams = null)
    {
        $this->call = $call;

        $this->method = 'GET';
        $this->resetParams();

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        return $this->sendRequest();
    }

    /**
     * Send a POST call to Twitter API via OAuth
     *
     * @param string $call Twitter resource string
     * @param array $postParams POST parameters to send
     * @param array $getParams GET parameters to send
     * @return mixed Output with selected format
     */
    public function post($call, array $postParams = null, array $getParams = null)
    {
        $this->call = $call;

        $this->method = 'POST';
        $this->resetParams();

        if ($postParams !== null && is_array($postParams)) {
            $this->postParams = $postParams;
        }

        if ($getParams !== null && is_array($getParams)) {
            $this->getParams = $getParams;
        }

        return $this->sendRequest();
    }

    protected function resetParams() {
        $this->headers = null;
        $this->response = null;

        $this->postParams = array();
        $this->getParams = array();
    }

    /**
     * Returns raw response body
     *
     * @return string Single string with encoded values
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Returns response headers as array.
     * This can be useful to avoid extra requests for rate-limit info
     *   x-rate-limit-limit      (max request per period)
     *   x-rate-limit-remaining  (remaining this period)
     *   x-rate-limit-reset      (start of next period, UTC timestamp)
     *
     * @return array with http_code and header lines
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Converting parameters array to a single string with encoded values
     *
     * @param array $params Input parameters
     * @return string Single string with encoded values
     */
    protected function getParams(array $params)
    {
        $r = '';

        ksort($params);

        foreach ($params as $key => $value) {
            $r .= '&' . $key . '=' . rawurlencode($value);
        }

        unset($params, $key, $value);

        return trim($r, '&');
    }

    /**
     * Getting full URL from a Twitter resource
     *
     * @param bool $withParams If true then parameters will be outputted
     * @return string Full URL
     */
    protected function getUrl($withParams = false)
    {
        $getParams = '';

        if ($withParams === true) {
            $getParams = $this->getParams($this->getParams);

            if (!empty($getParams)) {
                $getParams = '?' . $getParams;
            }
        }

        if ($this->encoded_bearer_credentials && !$this->bearer_access_token) {
            $url = $this->auth_url . $this->call;
        } else {
            $url = $this->url . $this->call . '.json' . $getParams;
        }

        return $url;
    }

    /**
     * Getting OAuth parameters to be used in request headers
     *
     * @return array OAuth parameters
     */
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

    /**
     * Converting all parameters arrays to a single string with encoded values
     *
     * @return string Single string with encoded values
     */
    protected function getRequestString()
    {
        $params = array_merge($this->getParams, $this->postParams, $this->getOauthParameters());

        $params = $this->getParams($params);

        return rawurlencode($params);
    }

    /**
     * Getting OAuth signature base string
     *
     * @return string OAuth signature base string
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
     * @return string Signing key
     */
    protected function getSigningKey()
    {
        return $this->config['consumer_secret'] . '&' . $this->config['oauth_token_secret'];
    }

    /**
     * Calculating the signature
     *
     * @return string Signature
     */
    protected function calculateSignature()
    {
        return base64_encode(hash_hmac('sha1', $this->getSignatureBaseString(), $this->getSigningKey(), true));
    }

    /**
     * Converting OAuth parameters array to a single string with encoded values
     *
     * @return string Single string with encoded values
     */
    protected function getOauthString()
    {
        // User-keys check moved here for app-only token support
        $required = array(
            'oauth_token' => '',
            'oauth_token_secret' => ''
        );
        if (count(array_intersect_key($required, $this->config)) !== count($required)) {
            throw new \Exception('Missing parameters in configuration array');
        }

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
     * @return array HTTP headers
     */
    protected function buildRequestHeader()
    {
        if ($this->encoded_bearer_credentials) {
            if ($this->bearer_access_token) {
                return array( 
                    "Authorization: Bearer " . $this->bearer_access_token
                );
            } else {
                return array(
                    "Authorization: Basic " . $this->encoded_bearer_credentials, 
                    "Content-Type: application/x-www-form-urlencoded;charset=UTF-8"
                );
            }
        } else {
            // OAuth headers
            return array(
                'Authorization: OAuth ' . $this->getOauthString(),
                'Expect:'
            );
        }
    }

    /**
     * Processing Twitter Exceptions in case of error
     *
     * @param string $type Depends of response format (array|object)
     * @param mixed $ex Exceptions
     * @throws Exception\TwitterException
     */
    protected function processExceptions($type, $ex)
    {
        switch ($type) {
            case 'array':
                foreach ($ex['errors'] as $error) {
                    throw new TwitterException($error['message'], $error['code']);
                }

                break;

            default:
                foreach ($ex->errors as $error) {
                    throw new TwitterException($error->message, $error->code);
                }
        }

        unset($type, $ex, $error);
    }

    /**
     * Outputs the response in the selected format
     *
     * @param string $response
     * @return mixed
     */
    protected function processOutput($response)
    {
        $format = $this->config['output_format'];

        switch ($format) {
            case 'text':
                if (substr($response, 2, 6) == 'errors') {
                    $response = json_decode($response);

                    $this->processExceptions('object', $response);
                }

                break;

            case 'json':
                if (!headers_sent()) {
                    header('Cache-Control: no-cache, must-revalidate');
                    header('Expires: Tue, 19 May 1981 00:00:00 GMT');
                    header('Content-type: application/json');
                }

                if (substr($response, 2, 6) == 'errors') {
                    $response = json_decode($response);

                    $this->processExceptions('object', $response);
                }

                break;

            case 'array':
                $response = json_decode($response, true);

                if (isset($response['errors'])) {
                    $this->processExceptions('array', $response);
                }

                break;

            default:
                $response = json_decode($response);

                if (isset($response->errors)) {
                    $this->processExceptions('object', $response);
                }
        }

        unset($format);

        return $response;
    }

    /**
     * Process curl headers to array
     */
    protected function processCurlHeaders($headerContent)
    {
        $this->headers = array();

        // Split the string on every "double" new line (multiple headers).
        $arrRequests = explode("\r\n\r\n", $headerContent);

        // Loop of response headers. The "count() -1" is to 
        // skip an empty row for the extra line break before the body of the response.
        for ($index = 0; $foo = count($arrRequests) -1, $index < $foo; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $this->headers[$index]['http_code'] = $line;
                else
                {
                    list ($key, $value) = explode(': ', $line);
                    $this->headers[$index][$key] = $value;
                }
            }
        }
    }


    /**
     *  Send GET or POST requests to Twitter API
     *
     * @throws Exception\TwitterException
     * @return mixed Response output
     */
    protected function sendRequest()
    {
        $url = $this->getUrl(true);

        $header = $this->buildRequestHeader();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        if (!empty($this->postParams)) {
            $options[CURLOPT_POST] = count($this->postParams);
            $options[CURLOPT_POSTFIELDS] = $this->getParams($this->postParams);
        }

        $c = curl_init();

        curl_setopt_array($c, $options);

        $response = curl_exec($c);

        if ($n = curl_errno($c)) { 
            throw new \Exception("cURL error ($n) : ".curl_error($c));
        }

        $header_size = curl_getinfo($c, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $header_size);
        $this->processCurlHeaders($headers);

        $this->response = substr($response, $header_size);

        curl_close($c);

        unset($response, $options, $c);

        if (!in_array($this->response[0], array('{', '['))) {
            throw new TwitterException("($url) ".str_replace(array("\n", "\r", "\t"), '', strip_tags($this->response)), 0);
        }

        return $this->processOutput($this->response);
    }


    /**
     * Set an Application-only bearer-token
     *
     * When set, API-requests will use the app-token 
     * instead of OAuth consumer keys. 
     * https://dev.twitter.com/docs/auth/application-only-auth
     *
     * @throws Exception
     * @param string $token bearer-token
     */
    public function setBearerToken($token = null)
    {
        if (empty($token)) {
            throw new \Exception('Token invalid (empty)');
        }

        $this->generateEncodedBearerCredentials();
        $this->bearer_access_token = $token;
    }

    /**
     *  Get an application-only token from consumer keys
     *
     * @return string Returns access-token on success
     */
    public function getBearerToken()
    {        
        $this->generateEncodedBearerCredentials();

        $this->post('oauth2/token', array('grant_type' => 'client_credentials'));

        $this->bearer_access_token = $this->processTokenResponse('oauth2/token');

        return $this->bearer_access_token;
    }

    /**
     *  Revoke / invalidate an application-only token
     *
     * @param string $token Bearer-token
     * @throws Exception
     * @return string Returns the same token on success
     */
    public function invalidateBearerToken($token = null)
    {
        if (empty($token)) {
            throw new \Exception('Token invalid (empty)');
        }

        $this->generateEncodedBearerCredentials();

        $this->post("oauth2/invalidate_token", array("access_token" => rawurldecode($token)));

        $return_token = $this->processTokenResponse('oauth2/invalidate_token');

        return $return_token;
    }

    /**
     * Generates basic authorization credentials for token request
     *
     */
    protected function generateEncodedBearerCredentials()
    {
        $this->bearer_access_token = null;
        $this->encoded_bearer_credentials = null;

        $bearer_credentials = urlencode($this->config['consumer_key']) . ":" . urlencode($this->config['consumer_secret']);

        $this->encoded_bearer_credentials = base64_encode($bearer_credentials);
    }

    /**
     * Process oauth2 response, returns the bearer-access-token
     *
     * @param string $response
     * @throws Exception\TwitterException
     * @return mixed
     */
    protected function processTokenResponse($path)
    {
        // json-decode raw response (as object)
        $response = json_decode($this->response);

        $token = false;

        switch ($path) {
            case 'oauth2/token':
                if (isset($response->token_type) && $response->token_type == 'bearer') {   
                    $token = $response->access_token;
                }
                break;
            
            case 'oauth2/invalidate_token':
                if (isset($response->access_token)) {   
                    $token = $response->access_token;
                }
                break;            
        }

        unset($response);

        return $token;
    }

}
