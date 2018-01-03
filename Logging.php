<?php

// 日志格式
// yyyy-mm-dd hh:mm:ss: 毫秒  用户名称 登录ip(公网)   日志等级/日志模块   日志内容
// 2017-12-31 10：10：10 293  xiaoyu  10.15.76.87   D/LOGIN    username : xiaoyu login.

// 日志输出位置
// 需要一个确定地位置，目前定为app_path下的logs文件
// 先找到位置，再创建文件，然后填充内容

include_once(FRAMEWORK . "/Config.php");

class Logging {
	
	public static $path = APP . "logs/";
  public static $instance;


  public static function instance() {
    if (!isset(self::$instance)) {
      self::$instance = new Logging();
    }
    return self::$instance;
    
  }

  // Database
  public static function d($module, $input){
    self::write($module, $input,"D");
  }

  // Portal
  public static function p($module, $input){
    self::write($module, $input,"P");
  }

  // Error
  public static function e($module, $input){
    self::write($module, $input,"E");
  }

  // Tpl
  public static function t($module, $input){
    self::write($module, $input,"T");
  }

  // Log
  public static function l($module, $input){
    self::write($module, $input,"L");
  }


  private static function write($module, $input, $level = "D"){
    // 确定日志根目录
    $path = self::$path;
    if(!file_exists($path)){
      mkdir($path, 0777);
    }

    // 日志年目录
    $year = date("Y");
    $path_year = $path . "$year/";
    if(!file_exists($path_year)){
      mkdir($path_year, 0777);
    }

    // 日志文件
    $file = "Logging-" . date("Y-m-d") . ".txt";
    $file_path = $path_year . $file;


    // 确定log时间
    list($micro, $stamp) = explode(' ', microtime());
    $micro = str_pad(round($micro, 3) * 1000, 3, "0", STR_PAD_RIGHT);   // 取三位并且后缀补零
    $now = date('Y-m-d H:i:s', $stamp) . ":$micro"; 
    //var_dump($now);

    // 确定log的ip
    $host_ip = $_SERVER['REMOTE_ADDR'];
    //echo $host_ip;

    // 确定log的port
    $host_port = $_SERVER['REMOTE_PORT'];
    //echo $host_port;

    $username = 'NOT LOGIN';
    // 确定当前地user
    //$username = !(empty($_SESSION['username'])) ? $_SESSION['username'] : "NOT LOGIN";

    // 整合输出内容
    $output = str_pad("<$now>", 30, ' ', STR_PAD_RIGHT);
    $output .= str_pad("<$host_ip>", 20, ' ', STR_PAD_RIGHT);
    $output .= str_pad("<$host_port>", 8, ' ', STR_PAD_RIGHT);
    $output .= str_pad("$username", 20, ' ', STR_PAD_RIGHT);

    $output .= str_pad("$level/$module", 20, ' ', STR_PAD_RIGHT);    
    if (!is_string($input)) {
      $input = json_encode($input);
    }
    $output .= $input . "\n";

    // 打开&&写入文件
    $file = fopen($file_path, "a");
    fwrite($file, $output);
    fclose($file);
    touch($file_path);

  }













}












?>
