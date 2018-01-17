<?php



class Cache{

  public static $instance;
  private $redis;
  
  public static function instance() {
    if (empty(self::$instance)) {
      self::$instance = new Cache();
    }  
    return self::$instance;
  }

  private function __construct() {
    $this->redis = new Redis();
    $this->redis->connect(REDIS_HOST, REDIS_PORT);
  }

  public function set($n, $v) {
    $this->redis->set($n, $v);
  }

  public function get($n) {
    return  $this->redis->get($n);
  }

  public function list_push($n, $v) {
    $this->redis->lpush($n, $v);
  }

  public function list_all($n) {
    return  $this->redis->lrange($n, 0, -1);
  }
















}













?>
