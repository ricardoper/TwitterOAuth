<?php namespace TwitterOAuth\OAuth;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

use TwitterOAuth\Crawlers\CurlCrawler;
use TwitterOAuth\Crawlers\CrawlerInterface;
use TwitterOAuth\Serializers\ArraySerializer;
use TwitterOAuth\Serializers\SerializerInterface;
use TwitterOAuth\Exceptions\TwitterOAuthException;

abstract class OAuthAbstract implements OAuthInterface
{

    /**
     * Twitter API Credentials
     *
     * @var array
     */
    protected $credentials = null;

    /**
     * Crawler Instance
     *
     * @var CrawlerInterface
     */
    protected $crawler = null;

    /**
     * Serializer Instance
     *
     * @var SerializerInterface
     */
    protected $serializer = null;


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

            $this->validateCredentials();

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
     *
     * @return $this
     */
    public function setCredentials(array $credentials)
    {
        $defaults = [
            'consumerKey' => null,
            'consumerSecret' => null,
            'oauthToken' => null,
            'oauthTokenSecret' => null,
        ];

        $this->credentials = array_replace($defaults, $credentials);

        $this->validateCredentials();

        unset($credentials, $defaults);

        return $this;
    }

    /**
     * Get OAuth Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * Set Crawler Instance
     *
     * @param CrawlerInterface $crawler
     * @return $this
     */
    public function setCrawler(CrawlerInterface $crawler)
    {
        $this->crawler = $crawler;

        unset($crawler);

        return $this;
    }

    /**
     * Get Crawler Instance
     *
     * @return CrawlerInterface
     */
    public function getCrawler()
    {
        return $this->crawler;
    }

    /**
     * Set Serializer Instance
     *
     * @param SerializerInterface $serializer
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        unset($serializer);

        return $this;
    }

    /**
     * Get Serializer Instance
     *
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }


    /**
     * Boot Dependencies Container
     */
    protected function bootDependencies()
    {
        // CrawlerInterface //
        $this->crawler = new CurlCrawler();

        // SerializerInterface //
        $this->serializer = new ArraySerializer();
    }

    /**
     * Credentials Validator
     *
     * @return bool
     * @throws TwitterOAuthException
     */
    protected function validateCredentials()
    {
        $required = [
            'consumerKey' => null,
            'consumerSecret' => null,
        ];


        $notFound = [];

        foreach ($required as $key) {
            if (!array_key_exists($key, $this->credentials)) {
                $notFound[] = $key;
            }
        }


        if (!empty($notFound)) {
            throw new TwitterOAuthException('Missing Credentials Field(s): ' . implode($notFound), 100);
        }

        return true;
    }
}
