<?php

// 此框架为单入口框架，
// 入口位置为 /root/index.php    
// 框架位置为 /root/framework    后续考虑移动位置
// 逻辑位置为 /root/app          后续可以进行route处理，针对不同的域名调用不同地app
// 插件位置为 /root/vendor       后续可以调整位置

include_once("Route.php");

class Portal{

  static $instance;

  public static function instance(){
    if (empty(self::$instance)) {
      self::$instance = new Portal();
    }
    return self::$instance;
  }

  private function __construct(){
    Route::instance()->route_host();
  }

  private function init() {
    include_once("config.php");
    (!isset($_SESSION)) ? session_start() : 1;
    Logging::set_log_path(dirname(__FILE__) . "/../" . APP . "/logs/");
  }

  private function execute ($controller, $action) {
    // 核心处理函数
    try {
      $class = new ReflectionClass($controller);  // 获取类
      $instance = $class->newInstance();          // 获取实例
      $func = $class->getMethod($action);         // 获取函数名
      
      // 在这里如果已知类名，函数名
      // 完全可以手动 
      // $c = new Class;
      // $c->func();
      // 但是具体差异是
      // 自身实例化的是封装后的类和函数
      // ReflectionClass是开封地，可以调用更多的函数
      // 实话是……这样逼格更高啊！
      $result = '';

      // 前置函数运行,主要运行user_login鉴定函数
      if ($class->hasMethod("pretreat")) {
        $pretreat = $class->getMethod("pretreat");
        if (!$pretreat->isStatic() && $pretreat->isPublic()) {
          $pretreat->invoke($instance);
        }
      }

      // 判断是否非静态类并且是公共函数
      if (!$func->isStatic() && $func->isPublic()) {
        $result = $func->invoke($instance);
      }

      // 后置函数运行
      if ($class->hasMethod("posttreat")) {
        $posttreat = $class->getMethod("posttreat");
        if (!$posttreat->isStatic() && $posttreat->isPublic()) {
          $posttreat->invoke($instance);
        }
      }

    }catch(Exception $e) {
      record_error($e);
    }
    return $result;
  }

  public function start(){
    // 初始化
    $this->init();

    // 获取请求
    $request = Request::instance();
    // 拆分请求
    list($path, $controller, $action) = $this->parse_query($request);
    // 加载class文件
    $this->load(APP_PATH . "controller/". $path . "/" . $controller . ".php");

    // 核心处理
    $data = $this->execute($controller, $action);
  
    $reponse = Reponse::instance($data);

    $reponse->send();

    //Logging::l("reponse", json_encode($reponse));
    //echo json_encode($reponse);

   
  }

  // 拆分query_string函数, 供index.php使用
  private function parse_query($request) {
    $query = $request->server['QUERY_STRING'];
    $qaction = $request->request["action"];

    $path = '';
    $controller = '';
    $action = '';

    if ($qaction) { // 兼容?action=xxx.xxx.xxx.xxx&factor=xx&....形态，可用于api形式
      $q = explode(".", $qaction);
      $l = count($q);

      $controller = $q[$l - 2];
      $action = $q[$l - 1];
      unset($q[$l - 1]);
      unset($q[$l - 2]);

      $path = implode("/", $q);
    }else {
      // 根据之前的逻辑，可以按照如下格式进行反馈
      // 示例： ?path1/path2/path3/.../controller/action&factor=xxx&factor=yyy
      // 以&为拆分，前面称为逻辑区，后面称为参数区
      // 逻辑区最后两位为controller和action,对应class及function
      // 逻辑区前面的内容均为路径，如果没有默认为底层
      // 如果controller及action均缺省，则自动补全index/index
      // 如果action缺省，则自动补全controller/index
      // 参数区不做描述

      $q = explode("&", $query);      // 提取逻辑区域
      $logical_area = $q[0];
      $logical_area = rtrim($logical_area,"/"); // 处理类似于?index/index 或者 ?path / controller / action ?情况

      
      $area = explode("/", $logical_area);// 拆分逻辑区域
      $length = count($area);// 逻辑区域长度

      // 进行补全和拆分
      if ($length == 1 && $area[0] == null) {   // 如果为空，则补充为index/index
        $controller = 'index';
        $action = 'index';
      }else if ( $length < 2) {                 // 如果只有一个controller,则补充为controller/index
        $controller = $area[$length - 1];
        $action = 'index';
      }else {
        $controller = $area[$length - 2];
        $action = $area[$length - 1];

        unset($area[$length - 1]);
        unset($area[$length - 2]);
        $path = implode("/", $area);
      }
    }
    Logging::p("PORTAL", "$path || $controller || $action");
    return array($path, ucfirst($controller . "_controller"), $action);
  }


  // 加载file
  private function load ($class_file) {
    // 判断文件是否404
    $notfound = FRAMEWORK_PATH . "404notfound.html";
    if (!file_exists($class_file)) {
      Logging::e("ERROR", "404 not found : $class_file");
      include_once($notfound);
      exit;
    }
    // 导入php文件
    include_once($class_file);
  }


}


