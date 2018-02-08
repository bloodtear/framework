<?php

namespace framework;

class Loader {

  public static function init() {
    spl_autoload_register(function ($class) {
      
      Logging::l("LOADER1", $class );
      
      $arr = explode("\\", $class);
      $l = count($arr);
      $class_name = $arr[$l - 1];
      //echo $l;
      $cr = class_exists($class_name);
      \framework\Logging::l("LOADER2", "class_exists $class_name : " .(isset($cr) ? "true" : 'false'));
      $file = implode("/", $arr);
      $file = ROOT_PATH . "/$file.php";
 
      if (file_exists($file)) {
        $r = include_once($file);
      }
      \framework\Logging::l("LOADER3", $file . (isset($r) ? " success" : " failed"));
    });
  }


  public static function load($file) {
    $notfound = FRAMEWORK_PATH . "404notfound.html";
    if (!file_exists($file)) {
      \framework\Logging::e("ERROR", "404 not found : $class_file");
      include($notfound);
      exit;
    }
    include_once($file);
  }


}
