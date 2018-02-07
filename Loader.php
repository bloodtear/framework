<?php

class Loader {

  public static function load($file) {
    $notfound = FRAMEWORK_PATH . "404notfound.html";
    if (!file_exists($file)) {
      Logging::e("ERROR", "404 not found : $class_file");
      include($notfound);
      exit;
    }
    include_once($file);
  }


}
