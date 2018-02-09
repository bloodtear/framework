<?php

namespace framework;

// 此框架为单入口框架，
// 入口位置为 /root/index.php    
// 框架位置为 /root/framework    后续考虑移动位置
// 逻辑位置为 /root/app          后续可以进行route处理，针对不同的域名调用不同地app
// 插件位置为 /root/vendor       后续可以调整位置

include_once("Route.php");
include_once("Loader.php");

class Portal{

  public static $instance;

  public static function instance(){
    if (empty(self::$instance)) {
      self::$instance = new Portal();
    }
    return self::$instance;
  }

  private function __construct(){
    Route::instance()->route_host();
  }

  public function init() {
    include_once("config.php");
    (!isset($_SESSION)) ? session_start() : 1;
    Logging::set_log_path(dirname(__FILE__) . "/../" . APP . "/logs/");

    \framework\Loader::init();
  }

  private function execute ($dispatch) {
    list($controller, $action, $class_file, $method) = $dispatch;

    //Loader::load($class_file);

    try {
      Logging::l("PORTAL", "controller : $controller");
      $class    = new \ReflectionClass($controller);  // 获取类
      $instance = $class->newInstance();          // 获取实例
      $func     = $class->getMethod($action);         // 获取函数名

      $result = '';

      // 前置函数运行,主要运行user_login鉴定函数，原则上不输出,仅针对GET作处理
      if ($class->hasMethod("pretreat") && $method == 'GET') {
        $pretreat = $class->getMethod("pretreat");
        if (!$pretreat->isStatic() && $pretreat->isPublic()) {
          $pretreat->invoke($instance);
        }
      }

      // 判断是否非静态类并且是公共函数
      if (!$func->isStatic() && $func->isPublic()) {
        $result = $func->invoke($instance);
      }

      // 后置函数运行,原则上不输出，仅针对GET作处理
      if ($class->hasMethod("posttreat") && $method == 'GET') {
        $posttreat = $class->getMethod("posttreat");
        if (!$posttreat->isStatic() && $posttreat->isPublic()) {
          $posttreat->invoke($instance);
        }
      }

    }catch(Exception $e) {
      $error = $e->__toString();
      echo $error;
      Logging::e("ERROR", $error);
    }
    return $result;
  }

  public function run(){
    Logging::e("HOOK", "<----------------- portal start ------------------>");
    Loader::init();
    
    Logging::e("HOOK", "<----------------- Request start ------------------>");
    $request  = Request::instance();

    Logging::e("HOOK", "<----------------- parse_query start ------------------>");
    $dispatch = $request->parse_query();

    Logging::e("HOOK", "<----------------- execute start ------------------>");
    $data     = $this->execute($dispatch);
  
    Logging::e("HOOK", "<----------------- reponse start ------------------>");
    $reponse  = Reponse::instance($data);
    $reponse->send();
  }

}


