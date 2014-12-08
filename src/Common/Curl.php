<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 *
 * Connecting to Twitter API using SSL
 * https://dev.twitter.com/overview/api/ssl
 */

namespace TwitterOAuth\Common;

use TwitterOAuth\Exception\CurlException;

class Curl
{
    /**
     * Send a request
     *
     * @param string $url Request URL
     * @param array $params Configuration array
     * @return array  Headers & Body
     * @throws CurlException
     */
    public function send($url, array $params = array())
    {
        $out = array();

        $default = array(
            'get' => array(),
            'post' => array(),
            'headers' => array(),
            'cookies' => false,
            'gzip' => true,
            'ua' => 0,
        );

        $params = array_merge($default, $params);


        // Get Params //
        if (is_array($params['get']) && !empty($params['get'])) {
            $getParams = $this->getParams($params['get']);

            if (!empty($getParams)) {
                $url = $url . '?' . $getParams;
            }
        } elseif (is_string($params['get']) && !empty($params['get'])) {
            $url = $url . '?' . $params['get'];
        }


        // Curl Options //
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => dirname(__DIR__) . '/Certificates/rootca.pem',
            CURLOPT_USERAGENT => $this->getUserAgent($params['ua']),

            // FOR DEBUG ONLY - PROXY SETTINGS //
            //CURLOPT_PROXY => '127.0.0.1',
            //CURLOPT_PROXYPORT => 8888,
        );


        // Post Params //
        if (is_array($params['post']) && !empty($params['post'])) {
            $options[CURLOPT_POST] = count($params['post']);
            $options[CURLOPT_POSTFIELDS] = $this->getParams($params['post']);
        } elseif (is_string($params['post']) && !empty($params['post'])) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $params['post'];
        }


        // Headers //
        if (is_array($params['headers']) && !empty($params['headers'])) {
            $options[CURLOPT_HTTPHEADER] = $params['headers'];
        }


        // Cookies Filename //
        if ($params['cookies'] !== false) {
            $options[CURLOPT_COOKIEJAR] = $params['cookies'];
            $options[CURLOPT_COOKIEFILE] = $params['cookies'];
        }


        // Gzip & Deflate //
        if ($params['gzip'] === true) {
            $options[CURLOPT_ENCODING] = 'gzip,deflate';
        }


        // Run Curl //
        $c = curl_init();

        curl_setopt_array($c, $options);

        $response = curl_exec($c);

        if (curl_errno($c) !== 0) {
            throw new CurlException(curl_error($c), curl_errno($c));
        }

        $cInfo = curl_getinfo($c);

        curl_close($c);


        // Process Response //
        $out['headers'] = $this->processHeaders(substr($response, 0, $cInfo['header_size']));

        $out['body'] = trim(substr($response, $cInfo['header_size']));

        unset($params, $options, $c, $cInfo, $response);

        return $out;
    }

    /**
     * Converting parameters array to a single string with encoded values
     *
     * @param array $params Input parameters
     * @return string  Single string with encoded values
     */
    public function getParams(array $params)
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
     * Get User Agent ID
     *
     * @param null $key
     *     If null return random UA
     *     If integer then return UA from array index
     * @return string  User Agent ID
     */
    protected function getUserAgent($key = null)
    {
        $ua = array(
            // Chrome //
            'Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1664.3 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.102 Safari/537.36',
            // Firefox //
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:26.0) Gecko/20100101 Firefox/26.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:26.0) Gecko/20100101 Firefox/26.0',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:26.0) Gecko/20100101 Firefox/26.0',
            // IE //
            'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)',
            'Mozilla/5.0 (Windows; U; MSIE 9.0; WIndows NT 9.0))',
            'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0; GTB7.4; InfoPath.2; SV1; .NET CLR 3.3.69573; WOW64)',
        );

        if ($key !== null) {
            return $ua[$key];
        }

        unset($key);

        return $ua[(int)mt_rand(0, count($ua) - 1)];
    }

    /**
     * Returns response headers as array
     *
     * This can be useful to avoid extra requests for rate-limit info
     *    x-rate-limit-limit      (max request per period)
     *    x-rate-limit-remaining  (remaining this period)
     *    x-rate-limit-reset      (start of next period, UTC timestamp)
     *
     * @param array $headers Headers string
     * @return array  Headers array
     */
    protected function processHeaders($headers)
    {
        $out = array();

        $headers = explode("\r\n", trim($headers));

        foreach ($headers as $header) {
            if (strpos($header, ':') !== false) {
                $tmp = explode(':', $header);

                $out[reset($tmp)] = end($tmp);
            } else {
                if (!isset($out['http-code'])) {
                    $out['http-code'] = $header;
                }
            }
        }

        unset($headers, $header, $tmp);

        return $out;
    }
}
