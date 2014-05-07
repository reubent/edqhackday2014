<?php
require_once('../vendor/phirehose/lib/OauthPhirehose.php');
require_once('../library/TwitterRedis.php');
require_once('../library/TwitterReader.php');

//These are the application key and secret
//You can create an application, and then get this info, from https://dev.twitter.com/apps
//(They are under OAuth Settings, called "Consumer key" and "Consumer secret")
define('TWITTER_CONSUMER_KEY', 'pssl18UUCw37gpiexQlpVUXlO');
define('TWITTER_CONSUMER_SECRET', 'zsN2P95keD2WZBM0aqpm1Y22d9mGRX8XRALnfzBlQRAVh0zMs5');

//These are the user's token and secret
//You can get this from https://dev.twitter.com/apps, under the "Your access token"
//section for your app.
define('OAUTH_TOKEN', '14912388-fTFkDYnBti8HYTMg6Rlpw872cfs4SrcnzIeXoFlBa');
define('OAUTH_SECRET', 'lgCoMKjg7UQT3cDswYEXLe3y31CXyzssrn6Uw2LszRHkW');

define('REDIS_SERVER', 'localhost');

