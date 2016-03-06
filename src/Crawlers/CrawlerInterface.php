<?php namespace TwitterOAuth\Crawlers;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

interface CrawlerInterface
{

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
     */
    public function send(array $params);

    /**
     * Get response info
     *
     * @param string|null $key
     * @return mixed
     */
    public function getInfo($key = null);

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
    public function getHeaders($key = null);
}
