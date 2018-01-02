<?php

include_once("Config.php");

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
  $class_file = APP . "controller/". $path . $controller . ".php" ;

  // 判断文件是否404
  $notfound = FRAMEWORK . "404notfound.html";
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
    
    // 判断是否非静态类并且是公共函数
    if (!$func->isStatic() && $func->isPublic()) {
      $result = $func->invoke($instance);
      echo json_encode($result);
    }
  }catch(Exception $e) {
    record_error($e);
  }
}

// 记录异常
function record_error($e){
  $error = $e->__toString();
  echo $error;
  Logging::e("ERROR", $error);
}

// 拆分query_string函数
function parse_query() {
  
  $query = $_SERVER['QUERY_STRING'];
  
  // 根据之前的逻辑，可以按照如下格式进行反馈
  // 示例： ?path1/path2/path3/.../controller/action&factor=xxx&factor=yyy
  // 以&为拆分，前面称为逻辑区，后面称为参数区
  // 逻辑区最后两位为controller和action,对应class及function
  // 逻辑区前面的内容均为路径，如果没有默认为底层
  // 如果controller及action均缺省，则自动补全index/index
  // 如果action缺省，则自动补全controller/index
  // 参数区不做描述

  //var_dump($query);
  //echo "<br>";

  // 提取逻辑区域
  $q = explode("&", $query);
  $logical_area = $q[0];
  $logical_area = rtrim($logical_area,"/"); // 处理类似于?index/index 或者 ?path / controller / action ?情况

  //echo "<br>";
  //var_dump($logical_area);
  //echo "<br>";

  // 拆分逻辑区域
  $area = explode("/", $logical_area);
  
  // 逻辑区域长度
  $length = count($area);

  // 初始化结果
  $path = '';
  $controller = '';
  $action = '';

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

  return array($path, ucfirst($controller), $action);
}




?>
