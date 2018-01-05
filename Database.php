<?php

class Database {

  private $database = '';
 

  public static $instance;


  private function __construct(){
    
  }
  
  public static function instance (){
    if (empty(self::$instance)) {
      self::$instance = new Database();
    }
    return self::$instance();
  }

























}











?>
