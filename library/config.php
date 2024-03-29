<?php
require_once('../vendor/phirehose/lib/OauthPhirehose.php');
require_once('../library/TwitterRedis.php');
require_once('../library/TwitterReader.php');

//These are the application key and secret
//You can create an application, and then get this info, from https://dev.twitter.com/apps
//(They are under OAuth Settings, called "Consumer key" and "Consumer secret")
define('TWITTER_CONSUMER_KEY', '');
define('TWITTER_CONSUMER_SECRET', '');

//These are the user's token and secret
//You can get this from https://dev.twitter.com/apps, under the "Your access token"
//section for your app.
define('OAUTH_TOKEN', '');
define('OAUTH_SECRET', '');

define('REDIS_SERVER', 'localhost');

