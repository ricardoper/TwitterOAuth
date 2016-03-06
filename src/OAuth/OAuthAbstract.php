<?php namespace TwitterOAuth\OAuth;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

use TwitterOAuth\Common\Container;
use TwitterOAuth\Crawlers\CurlCrawler;
use TwitterOAuth\Crawlers\CrawlerInterface;
use TwitterOAuth\Serializers\ArraySerializer;
use TwitterOAuth\Serializers\SerializerInterface;
use TwitterOAuth\Exceptions\TwitterOAuthException;

abstract class OAuthAbstract extends Container
{

    /**
     * Twitter API Credentials
     *
     * @var array
     */
    protected $credentials = null;


    /**
     * OAuthAbstract constructor
     *
     * @param array $credentials OAuth Credentials <p><ul>
     * <li><b>consumerKey</b>: (string) <em>(Required)</em> Twitter API key </li>
     * <li><b>consumerSecret</b>: (string) <em>(Required)</em> Twitter API secret  </li>
     * <li><b>oauthToken</b>: (string) Twitter Access token </li>
     * <li><b>oauthTokenSecret</b>: (string) Twitter Access token secret </li>
     * </ul></p>
     */
    public function __construct(array $credentials = null)
    {
        // OAuth Credentials //
        if ($credentials !== null) {
            $this->setCredentials($credentials);

            unset($credentials);
        }


        // Boot Dependencies //
        $this->bootDependencies();
    }

    /**
     * Set OAuth Credentials
     *
     * @param array $credentials OAuth Credentials <p><ul>
     * <li><b>consumerKey</b>: (string) <em>(Required)</em> Twitter API key </li>
     * <li><b>consumerSecret</b>: (string) <em>(Required)</em> Twitter API secret  </li>
     * <li><b>oauthToken</b>: (string) Twitter Access token </li>
     * <li><b>oauthTokenSecret</b>: (string) Twitter Access token secret </li>
     * </ul></p>
     */
    public function setCredentials(array $credentials)
    {
        $defaults = [
            'consumerKey' => null,
            'consumerSecret' => null,
            'oauthToken' => null,
            'oauthTokenSecret' => null,
        ];

        $this->credentials = array_replace_recursive($defaults, $credentials);

        unset($credentials, $defaults);
    }

    /**
     * Get OAuth Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Set Crawler Closure or Interface
     *
     * @param \Closure|CrawlerInterface $crawler
     * @throws TwitterOAuthException
     */
    public function setCrawler($crawler)
    {
        if (!($crawler instanceof \Closure && !($crawler instanceof CrawlerInterface))) {
            throw new TwitterOAuthException('Crawler Required', 100);
        }

        $this['crawler'] = $crawler;
    }

    /**
     * Get Crawler Object
     *
     * @return CrawlerInterface
     */
    public function getCrawler()
    {
        return $this['crawler'];
    }

    /**
     * Set Serializer Closure or Interface
     *
     * @param \Closure|SerializerInterface $serializer
     * @throws TwitterOAuthException
     */
    public function setSerializer($serializer)
    {
        if (!($serializer instanceof \Closure && !($serializer instanceof SerializerInterface))) {
            throw new TwitterOAuthException('Serializer Required', 101);
        }

        $this['serializer'] = $serializer;
    }

    /**
     * Get Serializer Object
     *
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this['serializer'];
    }


    /**
     * Boot Dependencies Container
     */
    protected function bootDependencies()
    {
        // CrawlerInterface //
        $this['crawler'] = function() {
            return new CurlCrawler();
        };

        // SerializerInterface //
        $this['serializer'] = function() {
            return new ArraySerializer();
        };
    }
}
