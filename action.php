<?php

// 此框架为单入口框架，
// 入口位置为 /root/index.php    
// 框架位置为 /root/framework    后续考虑移动位置
// 逻辑位置为 /root/app          后续可以进行route处理，针对不同的域名调用不同地app
// 插件位置为 /root/vendor       后续可以调整位置


function action(){

  session_start();

  // 拆分url
  list($path, $controller, $action) = ajax_parse_query();

  // 补充后缀
  $controller .= "_controller";
  $action .= "_ajax";

  // 不管解析是否成功，都需要把此次访问动作记录下来
  Logging::p("ACTION", "$path / $controller / $action");

  // 获取controller逻辑处理位置
  $class_file = APP_PATH . "controller/". $path . "/" . $controller . ".php" ;

  Logging::p("class_file", $class_file);
  // 判断文件是否404
  $notfound = FRAMEWORK_PATH . "404notfound.html";

  if (empty(file_exists($class_file))) {
    Logging::p("class_file404", 404);
    include($notfound);

    return;
  }

  // 导入php文件
  include_once($class_file);

  // 核心处理函数
  try {
    $class = new ReflectionClass($controller);  // 获取类
    $instance = $class->newInstance();          // 获取实例
    $func = $class->getMethod($action);         // 获取函数名
   
    // 判断是否非静态类并且是公共函数
    if (!$func->isStatic() && $func->isPublic()) {
      $result = $func->invoke($instance);
      if (!empty($result)){
        echo json_encode($result);
      }
    }

  }catch(Exception $e) {
    record_error($e);
  }
}





?>
