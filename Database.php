<?php

class Database {

  private $database = '';
  public static $instance;
  
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
    /*
    if (empty($this->database)) {
      Logging::d('DBPING' , "no database");
      return false;
    }
    */
    try {
      //$this->query('select 1;');
      $this->database->getAttribute(PDO::ATTR_SERVER_INFO);
    }catch(PDOException $e) {

      Logging::d('DBPING' , "no select 1");
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
      $this->database = new PDO(
        "mysql:dbname=". DB_DBNAME . ";host=" . DB_HOST, 
        DB_USERNAME, 
        DB_PASSWORD,
        array(
          PDO::ATTR_PERSISTENT => true, // 默认持久连接
          PDO::ATTR_TIMEOUT => 3
        )); 
      Logging::d("DB_CONN","Database is reconnected");
    } catch (PDOException $e) {
      Logging::e("DBPDO", 'Connection failed: ' . $e->getMessage());
      die('Connection failed: ' . $e->getMessage());
    }
  }

  // query函数
  private function query($query) {
    Logging::d("QUERY", "$query");
    $this->reconnect();
    $ret = $this->database->query($query);
    if (!$ret) {
      Logging::e("errorInfo", $this->database->errorInfo());
    }

    return $ret;
  }

  // get_all
  public function get_all($table, $where = '', $addons = '') {
    $where = (empty($where) ? '' : " where $where");
    $addons = (empty($addons) ? '' : " $addons");

    $query = "select * from $table $where $addons;";
    $ret = $this->query($query);

    return $ret->fetchAll(PDO::FETCH_ASSOC);    //需要加参数，不然会多出编号的数组 默认应该是PDO::FETCH_BOTH
  }

  // get_one
  public function get_one($table, $where = '', $addons = '') {
    $where = (empty($where) ? '' : " where $where");
    $addons = (empty($addons) ? '' : " $addons");

    $query = "select * from $table $where $addons limit 1;";
    $ret = $this->query($query);

    return $ret->fetch(PDO::FETCH_ASSOC);       // 同get_all
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
    return $this->database->beginTransaction();
  }

  // commit 
  public function commit () {
    $this->reconnect();
    return $this->database->commit();
  }

  // rollback
  public function rollback() {
    $this->reconnect();
    return $this->database->rollback();
  }

  // lastInsertId 
  public function last_insert_id () {
    $this->reconnect();
    return $this->database->lastInsertId();
  }

  // exec
  public function exec($query) {
    $this->reconnect();
    Logging::d("EXEC", $query);
    return $this->database->exec($query);
  }

}


// table类，多了一个private参数table, 调用地时候不需要在填写类名了。
class Database_table {
  public static $instance = '';

  public $db = '';
  public $table = '';

 public static function instance (){
    if (empty(self::$instance)) {
      self::$instance = new Database_table($table);
    }
    return self::$instance;
  }

  protected function __construct($table){
    $this->table = $table;
    $this->db = Database::instance();
  }

  public function set_table($table) {
    $this->table = $table;
  }

  // get_all
  public function get_all($where = '', $addons = '') {
    return $this->db->get_all($this->table, $where, $addons);
  }

  // get_one
  public function get_one($where = '', $addons = '') {
    return $this->db->get_one($this->table, $where, $addons);
  }

  // insert
  public function insert($data) {
    return $this->db->insert($this->table, $data);
  }

  // update
  public function update($data, $where) {
    return $this->db->update($this->table, $data, $where);
  }

  // delete
  public function delete($where) {
    return $this->db->delete($this->table, $where);
  }
  

  // begintransaction
  public function begin_transaction () {
    return $this->db->beginTransaction();
  }

  // commit 
  public function commit () {
    return $this->db->commit();
  }

  // rollback
  public function rollback() {
    return $this->db->rollback();
  }

  // lastInsertId 
  public function last_insert_id () {
    return $this->db->last_insert_id();
  }

  // exec
  public function exec($query) {
    Logging::d("EXEC", $query);
    return $this->db->exec($query);
  }








}









?>
