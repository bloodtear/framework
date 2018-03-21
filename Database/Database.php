<?php

namespace framework\Database;

class Database {

  private static $instance;

  private $database = '';
  
  public static function instance (){
    if (empty(self::$instance)) {
      self::$instance = new Database();
    }
    return self::$instance;
  }

  private function __construct(){
    $this->connect();
  }

  // PING函数，判断数据库是否已连接，
  private function ping() {
    
    if (empty($this->database)) {
      \framework\Logging::d('DBPING' , "no database");
      return false;
    }
    
    try {
      //$this->database->query('select 1;');
      $this->database->getAttribute(\PDO::ATTR_SERVER_INFO);
    }catch(PDOException $e) {

      \framework\Logging::d('DBPING' , "no select 1");
      return false;
    }
    return true;
  }

  private function reconnect() {
    if (!$this->ping()) {
      $this->connect();
    }
  }

  // 创建PDO对象
  private function connect() {                
    try {
      $this->database = new \PDO(
        "mysql:dbname=". DB_DBNAME . ";host=" . DB_HOST, 
        DB_USERNAME, 
        DB_PASSWORD,
        array(
          \PDO::ATTR_PERSISTENT => true, // 默认持久连接
          \PDO::ATTR_TIMEOUT => 3
        )); 
      \framework\Logging::d("DB_CONN","Database is reconnected");
    } catch (\PDOException $e) {
      \framework\Logging::e("DBPDO", 'Connection failed: ' . $e->getMessage());
      die('Connection failed: ' . $e->getMessage());
    }
  }

  // query函数
  private function query($query) {
    \framework\Logging::d("QUERY", "$query");
    $this->reconnect();
    $ret = $this->database->query($query);
    if (!$ret) {
      \framework\Logging::e("errorInfo", $this->database->errorInfo());
    }

    return $ret;
  }

  // query_get_all
  public function query_get_all($query) {

    $ret = $this->query($query);

    return $ret->fetchAll(\PDO::FETCH_ASSOC);    //需要加参数，不然会多出编号的数组 默认应该是PDO::FETCH_BOTH
  }
  
  // get_all
  public function get_all($table, $where = '', $addons = '') {
    $where = (empty($where) ? '' : " where $where");
    $addons = (empty($addons) ? '' : " $addons");

    $query = "select * from $table $where $addons;";
    $ret = $this->query($query);

    return $ret->fetchAll(\PDO::FETCH_ASSOC);    //需要加参数，不然会多出编号的数组 默认应该是PDO::FETCH_BOTH
  }

  // get_one
  public function get_one($table, $where = '', $addons = '') {
    $where = (empty($where) ? '' : " where $where");
    $addons = (empty($addons) ? '' : " $addons");

    $query = "select * from $table $where $addons limit 1;";
    $ret = $this->query($query);

    return $ret->fetch(\PDO::FETCH_ASSOC);       // 同get_all
  }

  // insert
  public function insert($table, $data) {
    $columns = '';
    $values = '';

    if(is_array($data)) {
      foreach ($data as $k => $v) {
        $columns .= "$k,";
        $values .= "'$v',";
      }
      $columns = substr($columns, 0, -1);
      $values = substr($values, 0, -1);
    }

    $query = "insert into $table ($columns) values ($values);";
    $ret = $this->query($query);
    return ($this->last_insert_id() ? $this->last_insert_id() : false); // 返回插入id
  }

  // update
  public function update($table, $data, $where) {
    $where = (empty($where) ? '' : " where $where");
    $values = '';

    if(is_array($data)) {
      foreach ($data as $k => $v) {
        $values .= "$k = '$v',";
      }
      $values = substr($values, 0, -1);
    }

    $query = "update $table set {$values} $where;";
    $ret = $this->query($query);
    $count = $ret->rowCount();  
    return $count ? $count : false; // 返回影响行数
  }

  // delete
  public function delete($table, $where) {
    $where = (empty($where) ? '' : " where $where");

    $query = "delete from $table $where;";
    $ret = $this->query($query);
    $count = $ret->rowCount();  
    return $count ? $count : false; // 返回影响行数
  }

  // begintransaction
  public function begin_transaction () {
    $this->reconnect();
    \framework\Logging::d('DATABASE' , "begin_transaction");
    
    return $this->database->beginTransaction();
  }

  // commit 
  public function commit () {
    $this->reconnect();
    \framework\Logging::d('DATABASE' , "commit");
    
    return $this->database->commit();
  }

  // rollback
  public function rollback() {
    $this->reconnect();
    \framework\Logging::d('DATABASE' , "rollback");
    return $this->database->rollback();
  }

  // lastInsertId 
  public function last_insert_id () {
    $this->reconnect();
    \framework\Logging::d('lastinsertid', $this->database->lastInsertId());
    return $this->database->lastInsertId();
  }

  // exec
  public function exec($query) {
    $this->reconnect();
    \framework\Logging::d("EXEC", $query);
    return $this->database->exec($query);
  }

}








