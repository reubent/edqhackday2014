<?php

/**
 * Description of TwitterReader
 *
 * @author reuben
 */
class TwitterReader extends OauthPhirehose {
	/** @var TwitterRedis */
	private $_redis;
	/**
	 * First response looks like this:
	 *    $data=array('friends'=>array(123,2334,9876));
	 *
	 * Each tweet of your friends looks like:
	 *   [id] => 1011234124121
	 *   [text] =>  (the tweet)
	 *   [user] => array( the user who tweeted )
	 *   [entities] => array ( urls, etc. )
	 *
	 * Every 30 seconds we get the keep-alive message, where $status is empty.
	 *
	 * When the user adds a friend we get one of these:
	 *    [event] => follow
	 *    [source] => Array(   my user   )
	 *    [created_at] => Tue May 24 13:02:25 +0000 2011
	 *    [target] => Array  (the user now being followed)
	 *
	 * @param string $status
	 */
	public function enqueueStatus($status) {
		$data = json_decode($status, true);
		if (!isset($data['id'])) {
			print date("Y-m-d H:i:s (") . strlen($status) . "): This is NOT a tweet!\n";
			return;
		}
		$this->_redis->set("queue__".time()."__".$data['id'], $status, 3600);
		echo date("Y-m-d H:i:s (") . strlen($status) . "):" . $data['id'] ." " .$data['user']['name'] . ": ".  $data['text'] . "\n";
	}

	public function __construct($username, $password, $method, TwitterRedis $redis) {
		$this->_redis = $redis;
		parent::__construct($username, $password, $method);
	}

}
