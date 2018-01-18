<?php

include_once("config.php");

Logging::l("RELEASE", "release start.");
chdir(APP_PATH);
Logging::l("RELEASE", "now pwd is " . getcwd());

$hook = get_request("hook");

$secret = $hook["config"]["secret"];

if ($secret !== 'aabbcc123') {
  Logging::e("RELEASE", "secret is fault.");
  return false;
}

$test = "git pull";
$out = shell_exec($test);
Logging::l("RELEASE", "out is " . $out);






?>
