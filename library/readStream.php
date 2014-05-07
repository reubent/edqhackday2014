<?php
include_once 'library/config.php';

$redis = new TwitterRedis(REDIS_SERVER);

// Start streaming
$sc = new TwitterReader(OAUTH_TOKEN, OAUTH_SECRET, Phirehose::METHOD_USER, $redis);
$sc->consume();