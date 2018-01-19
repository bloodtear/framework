<?php

include_once("Logging.php");

function route() {

  $host = $_SERVER['HTTP_HOST'];
  $file = dirname(__FILE__) . "/../route/" . $host . ".php";
  if (file_exists($file)) {
    logging::l("ROUTE", "route to $file");
    include($file);
    return;
  }else {
    logging::l("ROUTE", "no route");
  }

}


?>
