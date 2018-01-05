<?php

// 拆分query_string函数, 供index.php使用
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




// 拆分query_string函数, 供ajax.php使用
function ajax_parse_query() {
  $action = get_request("action");

  $area = explode(".", $action);
  $length = count($area);

  $path = '';
  $controller = '';
  $action = '';

  $controller = $area[$length - 2];
  $action = $area[$length - 1];

  unset($area[$length - 1]);
  unset($area[$length - 2]);

  $path = implode("/", $area);
  
  return array($path, ucfirst($controller), $action);
}















?>
