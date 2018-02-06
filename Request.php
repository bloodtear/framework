<?php

class Request {

  static $instance;

  public static function instance(){
    if (empty(self::$instance)) {
      self::$instance = new Request();
    }
    return self::$instance;
  }

  private function __construct(){
    $this->server = $_SERVER;
    $this->request = $_REQUEST;
  }

}
