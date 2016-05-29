<?php namespace TwitterOAuth\Crawlers;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

use TwitterOAuth\Exceptions\CrawlerException;

class CurlCrawler extends CrawlerAbstract
{

    /**
     * Request timeout
     *
     * @var int
     */
    protected $timeout = 60;

    /**
     * Request user agent
     *
     * @var int
     */
    protected $ua = 'TwitterOAuth for v1.1 API (https://github.com/ricardoper/TwitterOAuth)';

    /**
     * Response Info
     *
     * @var array
     */
    protected $info = [];

    /**
     * Response Headers
     *
     * @var array
     */
    protected $headers = [];


    /**
     * Crawler constructor
     */
    public function __construct()
    {
        // Load default options for Crawler //
        $this->options = array(
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => 'gzip',
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
            CURLOPT_CAINFO => dirname(__DIR__) . '/Certificates/cacert.pem',
        );
    }

    /**
     * Send a cURL request
     *
     * @param array $params Input Parameters <p><ul>
     * <li><b>url</b>: (string) <em>(Required)</em> Request URL </li>
     * <li><b>get</b>: (array|string) Request GET params </li>
     * <li><b>post</b>: (array|string) Request POST params </li>
     * <li><b>headers</b>: (array) Request headers </li>
     * </ul></p>
     *
     * @return string
     * @throws CrawlerException
     */
    public function send(array $params)
    {
        $options = $this->genOptions($params);

        $c = curl_init();

        curl_setopt_array($c, $options);

        $response = curl_exec($c);

        if (curl_errno($c) !== 0) {
            throw new CrawlerException('cURL: ' . curl_error($c), curl_errno($c));
        }

        $info = curl_getinfo($c);

        curl_close($c);

        $this->setInfo($info);

        $this->setHeaders(substr($response, 0, $info['header_size']));

        $response = substr($response, $info['header_size']);

        unset($params, $options, $c, $info);

        return $response;
    }

    /**
     * Get response info
     *
     * @param string|null $key
     * @return mixed
     */
    public function getInfo($key = null)
    {
        if ($key !== null) {
            if (!empty($this->info[$key])) {
                return $this->info[$key];
            }

            return false;
        }

        return $this->info;
    }

    /**
     * Get response headers
     *
     * This can be useful to avoid extra requests for rate-limit info
     *    x-rate-limit-limit      (max request per period)
     *    x-rate-limit-remaining  (remaining this period)
     *    x-rate-limit-reset      (start of next period, UTC timestamp)
     *
     * @param string|null $key
     * @return mixed
     */
    public function getHeaders($key = null)
    {
        // Process Headers Only In First Get //
        if (!empty($this->headers) && !is_array($this->headers)) {
            $this->processHeaders();
        }


        if ($key !== null) {
            if (!empty($this->headers[$key])) {
                return $this->headers[$key];
            }

            return false;
        }

        return $this->headers;
    }


    /**
     * Generate cURL options array
     *
     * @param array $params Input Parameters <p><ul>
     * <li><b>url</b>: (string) <em>(Required)</em> Request URL </li>
     * <li><b>get</b>: (array|string) Request GET params </li>
     * <li><b>post</b>: (array|string) Request POST params </li>
     * <li><b>headers</b>: (array) Request headers </li>
     * </ul></p>
     *
     * @return array
     */
    protected function genOptions(array $params = [])
    {
        $defaults = [
            'url' => null,
            'get' => [],
            'post' => [],
            'headers' => [],
        ];

        $params = array_replace($defaults, $params);


        // Set Base Options //
        $options = array_replace_recursive(
            $this->options,
            [
                CURLOPT_URL => $params['url'],
                CURLOPT_USERAGENT => $this->ua,
            ]
        );

        // Get Params //
        if (!empty($params['get']) && is_array($params['get'])) {
            $options[CURLOPT_URL] .= '?' . $this->serializeParams($params['get']);
        } elseif (!empty($params['get']) && is_string($params['get'])) {
            $options[CURLOPT_URL] .= '?' . $params['get'];
        }

        // Post Params //
        if (!empty($params['post']) && is_array($params['post'])) {
            $options[CURLOPT_POST] = count($params['post']);
            $options[CURLOPT_POSTFIELDS] = $this->serializeParams($params['post']);
        } elseif (!empty($params['post']) && is_string($params['post'])) {
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $params['post'];
        }

        // Headers //
        if (!empty($params['headers']) && is_array($params['headers'])) {
            $options[CURLOPT_HTTPHEADER] = $params['headers'];
        }

        unset($params, $defaults);

        return $options;
    }

    /**
     * Serialize array params to URL format
     *
     * @param array $params
     * @return string
     */
    protected function serializeParams(array $params)
    {
        $out = '';

        ksort($params);

        foreach ($params as $key => $value) {
            $out .= '&' . $key . '=' . rawurlencode($value);
        }

        unset($params, $key, $value);

        return trim($out, '&');
    }

    /**
     * Set response info
     *
     * @param array $info
     */
    protected function setInfo(array $info)
    {
        $this->info = $info;

        unset($info);
    }

    /**
     * Set response headers
     *
     * @param string $headers
     */
    protected function setHeaders($headers)
    {
        $this->headers = $headers;

        unset($headers);
    }

    /**
     * Process Headers
     */
    protected function processHeaders()
    {
        $out = [];

        $headers = explode("\r\n", trim($this->headers));

        foreach ($headers as $header) {
            if (strpos($header, ':') !== false) {
                $tmp = explode(':', $header);

                $out[strtolower(trim(reset($tmp)))] = strtolower(trim(end($tmp)));
            } else {
                if (!isset($out['http-code'])) {
                    $out['http-code'] = strtolower(trim($header));
                }
            }
        }

        $this->headers = $out;

        unset($headers, $header, $tmp, $out);
    }
}
