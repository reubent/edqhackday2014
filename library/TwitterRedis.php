<?php
/**
 * Description of Redis
 *
 * @author reuben
 */
class TwitterRedis {
	/**
	 * @var \Redis
	 */
	private $_redis;

	private $_server;

	public function __construct($server) {
		$this->_server = $server;
		$this->connect();
	}

	public function connect() {
		if (!$this->isConnected()) {
			$this->_redis = new \Redis();
			if ($this->_redis->connect($this->_server, 6379, 10)) {
				return true;
			}
			throw new \Exception("Can't connect to redis server on {$this->_server}");
		}
		return true;
	}

	public function isConnected() {
		if ($this->_redis instanceof \Redis) {
			try {
				if ($this->_redis->ping() == "+PONG") {
					return true;
				}
			} catch (\RedisException $e) {
				return false;
			}
		}
		return false;
	}

	public function set($key, $value, $expiration) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$this->_redis->setex($key, (int) $expiration, $value);
	}

	public function addToPostQueue($key, $value) {
		$this->set($key, $value, 3600);
		$this->addToSet("postQueue", $key);
	}

	public function increment($key, $by = 1) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		if ($this->_redis->exists("key")) {
			$this->_redis->incrBy($key, $by);
		} else {
			$this->_redis->set($key, $by, 7200);
		}
	}

	public function addToSet($key, $value) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->sAdd($key, $value);
	}

	public function getCategoryList() {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$categories = [];
		foreach ($this->_redis->sscan("categories", $key) as $category) {
			$categories[] = $category;
		}
		return $categories;
	}
	
	public function removeFromSet($key, $value) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->sRemove($key, $value);
	}

	public function sizeOfSet($key) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->sCard($key);
	}

	public function setTTL($key, $expires) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$this->_redis->setex($key, $expires);
	}

	public function getOneFromPostQueue() {
		$key = $this->_redis->sPop("postQueue");
		if (false !== $entry = $this->_redis->get($key)) {
			$this->_redis->del($key);
			return json_decode($entry, true);
		}
		return -1;
	}

	public function addOrIncrCategoryMember($category, $member) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$category = strtolower($category);
		$member = stem($member, STEM_ENGLISH);
		if (!$this->_redis->sIsMember("categories", $category)) {
			$this->_redis->sAdd("categories", $category);
			$this->_redis->zAdd("zCategories", 1, $category);
		} else {
			$this->_redis->zIncrBy("zCategories", 1, $category);
		}
		$sKey = "categoryS$category";
		$zKey = "category$category";
		if (!$this->_redis->sIsMember($sKey, $member)) {
			$this->_redis->sAdd($sKey, $member);
			$this->_redis->zAdd($zKey, 1, $member);
		} else {
			$this->_redis->zIncrBy($zKey, 1, $member);
		}
	}

	public function getKeys($pattern) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->keys($pattern);
	}

	public function get($key) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->get($key);
	}

	public function getSet($key) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$out = [];
		$ref = null;
		foreach ($this->_redis->sscan($key, $ref) as $word) {
			$out[] = @json_decode($word, true);
		}
		return $out;
	}

	public function deleteKey($key) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$this->_redis->del($key);
	}	

	public function getScoresForWord($word) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		$word = stem($word, STEM_ENGLISH);
		$key = null;
		$result = [];
		foreach ($this->_redis->sscan("categories", $key) as $category) {
			$score = $this->_redis->zScore("category".$category, $word);
			if ($score > 0) {
				if (!isset($result[$category])) {
					$result[$category] = 0;
				}
				$result[$category] += $score;
			}
		}
		foreach ($result as $cat => &$score) {
			$quotient = $this->_redis->zScore("zCategories", $cat);
			if ($quotient > 0) {
				$score /= $quotient;
			}
		}
		return $result;
	}
}
