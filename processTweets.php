<?php
include_once 'library/config.php';

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
		if (substr($word, 0, 1 == "@")) {
			print "\t$word is a username - skipping\n";
			continue;
		}
		foreach ($redis->getScoresForWord($word) as $category => $score) {
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
}