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
		$this->_redis->incrBy($key, $by);
	}

	public function addToSet($key, $value) {
		if (!$this->isConnected()) {
			$this->connect($this->_server);
		}
		return $this->_redis->sAdd($key, $value);
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
		$key = $this->_redis->sPop("PostQueue");
		if (false !== $entry = $this->_redis->get($key)) {
			$this->_redis->remove($key);
			return json_decode($entry, true);
		}
		return false;
	}
}
