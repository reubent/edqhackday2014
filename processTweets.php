<?php
include_once 'library/config.php';

$redis = new TwitterRedis(REDIS_SERVER);

while ($item = $redis->getOneFromPostQueue()) {
	if ($item === -1) {
		print "Nothing in queue...\n";
		sleep(1);
		continue;
	}
	print "Got item {$item['id']} from queue...";
	processItem($item);
}

function processItem($item) {
	print_r($item);
}