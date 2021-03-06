<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dsession {
	var $obj;

    function dsession() {
		$this->obj = new Redis;
		include DT_ROOT.'/file/config/redis.inc.php';
		$num = count($RedisServer);		
		$key = $num == 1 ? 0 : abs(crc32($GLOBALS['DT_IP']))%$num;
		$this->obj->connect($RedisServer[$key]['host'], $RedisServer[$key]['port']);

		if(DT_DOMAIN) @ini_set('session.cookie_domain', '.'.DT_DOMAIN);
    	session_set_save_handler(array(&$this,'open'), array(&$this,'close'), array(&$this,'read'), array(&$this,'write'), array(&$this,'destroy'), array(&$this,'gc'));
		session_cache_limiter('private, must-revalidate');
		session_start();
		header("cache-control: private");
    }

	function open($path, $name) {
		return true;
	}

	function close() {
		return true;
	}

	function read($sid) {
		return $this->obj->get($sid);
	}

	function write($sid, $data) {
		return $this->obj->setex($sid, 1800, $data);
	}

	function destroy($sid) {
	     return $this->obj->delete($sid);
	}

	function gc() {
	    return true;
	}
}
?>