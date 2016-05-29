<?php namespace TwitterOAuth\OAuth;

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2016
 */

interface OAuthInterface
{

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
    public function setCredentials(array $credentials);
}
