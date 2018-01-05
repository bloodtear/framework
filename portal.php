<?php

// 此框架为单入口框架，
// 入口位置为 /root/index.php    
// 框架位置为 /root/framework    后续考虑移动位置
// 逻辑位置为 /root/app          后续可以进行route处理，针对不同的域名调用不同地app
// 插件位置为 /root/vendor       后续可以调整位置

include_once("config.php");

function start(){

  session_start();

  // 拆分url
  list($path, $controller, $action) = parse_query();

  // 补充后缀
  $controller .= "_controller";
  $action .= "_action";

  // 不管解析是否成功，都需要把此次访问动作记录下来
  Logging::p("PORTAL", "$path / $controller / $action");

  // 获取controller逻辑处理位置
  $class_file = APP_PATH . "controller/". $path . $controller . ".php" ;

  // 判断文件是否404
  $notfound = FRAMEWORK_PATH . "404notfound.html";
  if (!file_exists($class_file)) {
    include_once($notfound);
    exit;
  }

  // 导入php文件
  include_once($class_file);

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


    // 前置函数运行,主要运行user_login鉴定函数
    if ($class->hasMethod("pretreat")) {
      $pretreat = $class->getMethod("pretreat");
      if (!$pretreat->isStatic() && $pretreat->isPublic()) {
        $result = $pretreat->invoke($instance);
      }
    }

    // 判断是否非静态类并且是公共函数
    if (!$func->isStatic() && $func->isPublic()) {
      $result = $func->invoke($instance);
      if (!empty($result)){
        echo json_encode($result);
      }
    }

    // 后置函数运行
    if ($class->hasMethod("posttreat")) {
      $posttreat = $class->getMethod("posttreat");
      if (!$posttreat->isStatic() && $posttreat->isPublic()) {
        $result = $posttreat->invoke($instance);
      }
    }

  }catch(Exception $e) {
    record_error($e);
  }
}





?>
