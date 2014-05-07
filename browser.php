<?php

require_once 'library/config.php';
define("WANTED_CATEGORIES", 4);
$redis = new TwitterRedis(REDIS_SERVER);
$out = [
	'categories' => [],
	'tweetsPerCategory' => [],
	'scores' => [],
	'tokensInCategory' => []
];
//if ($redis->get("DataWaiting") < 1) {
//	print json_encode([ "error" => "No new data"]);
//	exit;
//}

$redis->deleteKey("DataWaiting");

$categories = $redis->getCategoryList();
$scores = json_decode($redis->get("ScoredCategories"), true);
$matchedCategories = json_decode($redis->get("MatchedCategories"), true);
if (count($matchedCategories) == 0) {
	$out['error'] = "No data!";
	print json_encode($out); exit;
}
$summarisedOut = [];
$builtCategories = [];
asort($scores);

foreach ($categories as $category) {
	$out['tokensInCategory'][$category] = $redis->sizeOfSet("categoryS".$category);
	if (!in_array($category, array_keys($matchedCategories))) {
		$summarisedOut[$category] = $category;
	} else {
		$builtCategories[$category] = $category;
		$out['tweetsPerCategory'][$category] = $matchedCategories[$category];
	}
	if (!isset($scores[$category])) {
		$out['scores'][$category] = 0;
	} else {
		$out['scores'][$category] = $scores[$category];
	}
}
asort($out['scores']);
asort($out['tweetsPerCategory']);
$counter = 0;
$notIn = [];
foreach ($builtCategories as $category) {
	if (++$counter < WANTED_CATEGORIES) {
		$out['categories'][$category] = $redis->getSet("tweets_in_".$category);
		$not = $redis->getSet("not_in_".$category);
		if (is_array($not)) {
			$notIn = array_merge($notIn, $not);
		}
	} else {
		unset($builtCategories[$category]);
	}
}
$out['categories']['other'] = $notIn;
header("Content-type: application/json");
print json_encode($out);
