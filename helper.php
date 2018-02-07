<?php

  // 跳转用
  function go($path) {
    $path = trim($path, '/');
    $url = ROOT_URL . "?$path";
    Logging::l("REDIRECT", $url);
    header("Location: " . $url);
    exit;
  }

  function get_request($name, $default = null){
    if (isset($_REQUEST[$name])) {
      return $_REQUEST[$name];
    } else {
      return $default;
    }
  }

  function get_session($name, $default = null){
    if (isset($_SESSION[$name])) {
      return $_SESSION[$name];
    } else {
      return $default;
    }
  }


