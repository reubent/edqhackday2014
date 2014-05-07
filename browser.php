<?php

require_once 'library/config.php';
define("WANTED_CATEGORIES", 4);
$redis = new TwitterRedis(REDIS_SERVER);
$out = [
	'categories' => [],
	'tweetsPerCategory' => [],
	'scores' => []
];
if ($redis->get("DataWaiting") < 1) {
	print json_encode([ "error" => "No new data"]);
	exit;
}

$redis->deleteKey("DataWaiting");

$categories = $redis->getCategoryList();
$scores = json_decode($redis->get("ScoredCategories"), true);
$matchedCategories = json_decode($redis->get("MatchedCategories"), true);
$summarisedOut = [];
$builtCategories = [];
asort($scores);

foreach ($categories as $category) {
	if (!in_array($category, array_keys($matchedCategories))) {
		$summarisedOut[$category] = $category;
	} else {
		$builtCategories[$category] = $category;
		$out['tweetsPerCategory'][$category] = $matchedCategories[$category];
	}
	if (isset($scores[$category])) {
		$out['scores'][$category] = 0;
	}
}
asort($out['scores']);
asort($out['tweetsPerCategory']);
$counter = 0;
$notIn = [];
foreach ($builtCategories as $category) {
	if (++$counter <= WANTED_CATEGORIES) {
		$out['categories'][$category] = json_decode($redis->get("tweets_in_".$category, true));
		$notIn = array_merge($notIn, json_decode($redis->get("not_in_".$category, true)));
	} else {
		unset($builtCategories[$category]);
	}
}

print json_encode($out);
