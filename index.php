<?php

include_once("framework/Portal.php");

$portal = Portal::instance();  //主启动函数

$portal->init();

$portal->run();



