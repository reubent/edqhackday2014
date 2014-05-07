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





$redis = new TwitterRedis(REDIS_SERVER);

while ($item = $redis->getOneFromPostQueue()) {
	if ($item === -1) {
		print "Nothing in queue...\n";
		sleep(1);
		continue;
	}
	print "Got item {$item['id']} from queue...\n";
	processItem($item, $redis);
}

function processItem($item, TwitterRedis $redis) {
	//remove urls
	$body = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $item['text']);
	// lose other punctuation
	$text = preg_replace("/[\.,\<\>\:\~\#\!\?\-\/\|\"\&]+/", " ", html_entity_decode($body));
	// and split to words
	$words = explode(" ", $text);
	$result = [];
	foreach ($words as $word) {
		if ($word == "") {
			continue;
		}
		print "\tWord is '$word' - will be stemmed as '".stem($word, STEM_ENGLISH)."'\n";
		if (substr($word, 0, 1 == "@")) {
			print "\t$word is a username - skipping\n";
			continue;
		}
		foreach ($redis->getScoresForWord($word) as $category => $score) {
			print "\tFor category $category got score $score\n";
			if (!isset($result[$category])) {
				$result[$category] = $score;
			} else {
				$result[$category] += $score;
			}
		}
	}
	print "Had text:\n$text\n\n";
	print "Score is: \n";
	foreach ($result as $category => $score) {
		print "\t$category => $score\n";
	}
	$toSave = [
		"id" => $item['id'],
		"tweet" => $item,
		"scores" => $result
	];
	$redis->set("P__".$item['id'], json_encode($toSave), 7200);
	summarise($redis);
}

function summarise(TwitterRedis $redis) {
	$scores = []; $matched = [];
	$allCategories = $redis->getCategoryList();
	$tweets = $redis->getKeys("P__*");
	foreach ($tweets as $key) {
		$tweet = json_decode($redis->get($key), true);
		foreach ($tweet['scores'] as $category => $score) {
			if (!isset($scores[$category])) {
				$scores[$category] = $score;
			} else {
				$scores[$category] += $score;
			}
			if ($score == max($tweet['scores'])) {
				"Assigning tweet to category $category\n";
				$redis->addToSet("tweets_in_".$category, json_encode($tweet));
				if (!isset($matched[$category])) {
					$matched[$category] = 1;
				} else {
					$matched[$category]++;
				}
			}
		}
		foreach ($allCategories as $category) {
			if (!isset($tweet['scores'][$category]) || $tweet['scores'][$category] !== max($tweet['scores'])) {
				$redis->addToSet("not_in_".$category, json_encode($tweet));
			}
		}
	}
	foreach ($scores as $category => $score) {
		if (isset($matched[$category])) {
			print "For category $category score is $score in {$matched[$category]} matching tweets\n";
		} else {
			print "For category $category score is $score\n";
		}
	}
	$redis->set("MatchedCategories", json_encode($matched), 7200);
	$redis->set("MatchedCategories_".time(), json_encode($matched), 7200);
	$redis->set("ScoredCategories", json_encode($scores), 7200);
	$redis->increment("DataWaiting");
}