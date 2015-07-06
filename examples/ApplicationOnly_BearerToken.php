<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

require __DIR__ . '/../../../../vendor/autoload.php';

use TwitterOAuth\Auth\ApplicationOnlyAuth;

/**
 * Serializer Namespace
 */
use TwitterOAuth\Serializer\ArraySerializer;


date_default_timezone_set('UTC');


/**
 * Array with the OAuth tokens provided by Twitter
 *   - consumer_key     Twitter API key
 *   - consumer_secret  Twitter API secret
 */
$credentials = array(
    'consumer_key' => 'xvz1evFS4wEEPTGEFPHBog',
    'consumer_secret' => 'L8qq9PZyRg6ieKGEKhZolGC0vJWLw8iEJ88DRdyOg',
);

/**
 * Instantiate ApplicationOnly
 *
 * For different output formats you can set one of available serializers
 * (Array, Json, Object, Text or a custom one)
 */
$serializer = new ArraySerializer();

$auth = new ApplicationOnlyAuth($credentials, $serializer);


/**
 * If you need to store Bearer Token you can get it like this:
 */
$bearerToken = $auth->getBearerToken();

echo 'Bearer Token: ' . $bearerToken . '<hr />';


/**
 * If you have a stored Bearer Token you can use it like this:
 *   -- Uncomment To Test --
 */
//$bearerToken = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA/AAAAAAAAAAAAAAAAAAAA=AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';

//$auth->setBearerToken($bearerToken);


/**
 * Returns a collection of the most recent Tweets posted by the user
 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 */
$params = array(
    'screen_name' => 'ricard0per',
    'count' => 3,
    'exclude_replies' => true
);

/**
 * Send a GET call with set parameters
 */
$response = $auth->get('statuses/user_timeline', $params);

echo '<pre>'; print_r($auth->getHeaders()); echo '</pre>';

echo '<pre>'; print_r($response); echo '</pre><hr />';


/**
 * If you need to Invalidate a Bearer Token you can invalidate it like this:
 */
$status = $auth->invalidateBearerToken();

if ($status === true) {
    echo 'Bearer Token invalidated';
} else {
    echo 'Error invalidating Bearer Token';
}
