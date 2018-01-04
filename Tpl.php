<?php

// 前端模板
// 虽然VUE已经解决了大部分的需求，但是还是需要有自己的前端框架
// 主要解决以下问题
// ×× 页面可加载头部和尾部
// ×× 页面可以加载指定的前端tpl页面
// ×× 页面对应的css和js都自动加载
// ×× 可以在controller/action中提取参数，输出到tpl页面中
// ×× 使用固定符号可以直接输出变量，如 {{$varname}} 不需要再 echo $varname;了

class Tpl {

  private $header = '';
  private $footer = '';
  private $data = array();

  public static $instance;

  public static function instance (){
    if (!isset(self::$instance)){
      self::$instance = new Tpl();
    }
    return self::$instance;

  }


  // 创建实例时候指明导入的header和footer
  public function __construct($header, $footer){

    $this->header = $header;
    $this->footer = $footer;
    
  }

  // 展示函数，需要指明body位置
  public function view($body){
    $header = $this->header;
    $footer = $this->footer;

    $this->load($header);
    $this->load($body);
    $this->load($footer);

  }


  // 加载include文件，需要指明加载文件位置不需要后缀
  private function load($path){
    $path = trim($path, "/");
    $path = explode("/", $path);
    $length = count($path);

    // 获取文件名称
    $filename = $path[$length - 1];
    
    // 获取路径
    unset($path[$length - 1]);
    $path = (empty($path) ? '' : implode("/", $path));

    // 拼接字符串
    $tplfile = rtrim(APP . "tpl" . "/" . $path, "/") ."/" . $filename . ".html";
    $jsfile =  rtrim(APP . "js" . "/" . $path, "/") ."/" . $filename . ".js";
    $cssfile =  rtrim(APP . "css" . "/" . $path, "/") ."/" . $filename . ".js";

    $this->include_file($cssfile);
    $this->include_file($tplfile, true);
    $this->include_file($jsfile);

  }

  // 注意：此函数只能适用于string和array形式
  public function set($name, $data) {
    if(!is_string($name)){ 
      Logging::e("ERROR", "Tpl set faild: $name is not string.");
      return;
    }
    $mdata = $this->data;
    $mdata[$name] = $data;
    $this->data = $mdata;

  }


  // 加载函数， 默认不输出log错误，js和css非必须存在
  // 如果有{:$varname} 则替换为 < ?php echo $varname; ? >
  private function include_file($file, $log = false){
    if (file_exists($file)) {
      $mdata = $this->data; // 提取内部变量


      $contents = $this->read_file($file);
      $contents = $this->import_data($contents);
      $contents = $this->replace($contents);  // 转译变量
      $tempfile = $this->write_file($contents);

      include($tempfile);   // 最终还是只能include,因为不仅有输出，还有php脚本

    }else if ($log){
      Logging::e("TPL", "$file load failed.");
    }

  }


  // 转译函数, 注意：此函数只能适用于String形式，{:$varname} 其他形式则无法进行替换
  private function replace ($input) {
      $pattern = '/({:\$)(.*)(})/';
      $replace = '<?php echo \$$2 ;?>'; // 坑爹的网站，在php.net才发现可以把pattern分解成()()()的格式，并且在replace里使用$1 $2 $3替代
      return preg_replace($pattern, $replace, $input);
  }


  // 导入内部变量, 注意：此函数只能适用于string和array形式 其他形式无法导入
  private function import_data($contents){
    if (!empty($this->data)) { 
      Logging::l('mdata', json_encode($this->data));
      $import = '<?php ';
      foreach ($this->data as $k => $v) {
        $import .= $this->assign($k, $v);
      }
      $import .= " ?>";
    }
    return $import . $contents;

  }

  // 核心赋值函数
  private function assign($k, $v) {
    $ret = '';
    if (is_string($v) ) { // 字符串直接输出
      $ret = "\$$k = '$v';";
    }else if (is_numeric($v)) { // 数字直接输出
      $ret = "\$$k = $v;";
    }else if (is_array($v)){
      foreach ($v as $kk => $vv) { // 数组做递归
        $k_new = $k . "['$kk']";
        $ret .= $this->assign($k_new, $vv);
      }
    }
    return $ret;

  }


  // 读文件
  private function read_file($file){
      $f = fopen($file, "r");
      $contents = fread($f, filesize($file)); // 提取原始文件
      fclose($f);
      return $contents;

  }


  // 写临时文件
  private function write_file($contents){
      Logging::l("contents", $contents);
      $tempfile = rtrim(APP, "/"). "/.tempfile";  // 创建临时文件
      touch($tempfile);
      $f = fopen($tempfile, "w");
      fwrite($f, $contents);      // 写入临时文件
      fclose($f);
      return $tempfile;

  }


}







?>
