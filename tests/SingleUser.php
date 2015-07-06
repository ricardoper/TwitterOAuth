<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

require __DIR__ . '/../../../vendor/autoload.php';

use TwitterOAuth\Auth\SingleUserAuth;
use TwitterOAuth\Serializer\ArraySerializer;


date_default_timezone_set('UTC');

header('Content-Type: text/html; charset=utf-8');

?>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script>
    window.onload = function(){
        $('pre.array')
            .addClass('closed')
            .click(function(){
                $(this).toggleClass('closed');
            });
    }
</script>
<style>
    pre{overflow:hidden;}
    .strip{margin-left:5px;width:90%;vertical-align:top;display:inline-block;word-wrap:break-word;}
    .closed{height:84px;cursor:pointer;}
</style>

<?php

$credentials = array(
    'consumer_key' => 'xvz1evFS4wEEPTGEFPHBog',
    'consumer_secret' => 'L8qq9PZyRg6ieKGEKhZolGC0vJWLw8iEJ88DRdyOg',
    'oauth_token' => 'e98c603b55646a6d22249d9b0096e9af29bafcc2',
    'oauth_token_secret' => '07cfdf42835998375e71b46d96b4488a5c659c2f',
);

$auth = new SingleUserAuth($credentials, new ArraySerializer());

// ==== ==== ==== //

$params = array(
    'screen_name' => 'ricard0per',
    'count' => 3,
    'exclude_replies' => true,
);

$response = $auth->get('statuses/user_timeline', $params);

echo '<strong>statuses/user_timeline</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

$params = array(
    'q' => '#php',
    'count' => 3,
);

$response = $auth->get('search/tweets', $params);

echo '<strong>search/tweets</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

$params = array(
    'screen_name' => 'ricard0per',
    'count' => 10,
);

$response = $auth->get('followers/ids', $params);

echo '<strong>followers/ids</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

$response = $auth->postMedia('media/upload', __DIR__ . '/TwitterUpload.jpg');
$media_id = $response['media_id'];

$params = array(
    'status' => 'This is a media/upload test :: TwitterOAuth - https://github.com/ricardoper/TwitterOAuth/ - ',
    'media_ids' => $media_id,
);

$response = $auth->post('statuses/update', $params);

echo '<strong>statuses/update With Image</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

$params = array(
    'name' => 'List001',
    'mode' => 'private',
    'description' => 'List Test',
);

$response = $auth->post('lists/create', $params);

echo '<strong>lists/create</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

$params = array(
    'owner_screen_name' => 'ricard0per',
    'slug' => 'list001',
);

$response = $auth->post('lists/destroy', $params);

echo '<strong>lists/destroy</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';

// ==== ==== ==== //

/**
 * Reset Connection Without OAuth Tokens
 */
unset($auth, $credentials['oauth_token'], $credentials['oauth_token_secret']);

$auth = new SingleUserAuth($credentials, new ArraySerializer());

// ==== ==== ==== //

$params = array(
    'oauth_callback' => '',
);

$response = $auth->post('oauth/request_token', $params);

echo '<strong>oauth/request_token</strong><br />';
echo '<pre class="array">'; print_r($auth->getHeaders()); echo '</pre>';
echo '<pre class="array">'; print_r($response); echo '</pre><hr />';
