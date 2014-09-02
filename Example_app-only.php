<?php

require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';


use TwitterOAuth\TwitterOAuth;

date_default_timezone_set('UTC');


/**
 * Array with the OAuth tokens provided by Twitter when you create application
 *
 * output_format - Optional - Values: text|json|array|object - Default: object
 */
$config = array(
    'consumer_key'       => '137465134', // API key
    'consumer_secret'    => '1374651fs', // API secret
    'oauth_token'        => '', // not needed for app only
    'oauth_token_secret' => '',
    'output_format'      => 'object'
);

/**
 * Instantiate TwitterOAuth class with set tokens
 */
$connection = new TwitterOAuth($config);


// -------------------------------------------------------------

// Get an application-only token
// more info: https://dev.twitter.com/docs/auth/application-only-auth
 
$bearer_token = $connection->getBearerToken();

// You can store the bearer-token locally (database, file) and re-use it
//    $connection->setBearerToken($token);

// You can revoke / invalidate a bearer-token with
//    $connection->invalidateBearerToken($token);


$params = array(
    'screen_name' => 'twitter',
    'count' => 2,
    'exclude_replies' => true
);

$response = $connection->get('statuses/user_timeline', $params);

var_dump($response);

