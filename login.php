<? php

define('FACEBOOK_SDK_V4_SRC_DIR', __DIR__ . '/Facebook/');
require_once __DIR__ . '/facebook-php-sdk-v4/src/Facebook/autoload.php';

/*
$fb = new Facebook\Facebook([
  'app_id' => '1710308295910621',
  'app_secret' => 'ed45c3115b32156244bf8a5fb1dd8783',
  'default_graph_version' => 'v2.6',
]);
*/

$fb = new Facebook\Facebook([/* . . . */]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
$loginUrl = $helper->getLoginUrl('login-callback.php', $permissions);

echo '<a href="$loginUrl">Log in with Facebook!</a>';

?>