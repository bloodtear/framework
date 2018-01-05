<?php

  // 跳转用
  function go($path) {
    $path = trim($path, '/');
    $url = ROOT_URL . "?$path";
    header("Location: " . $url);
    exit;
  }










?>
