<?php

// 定义路径区域


// APP可进行route
defined('APP') or define('APP', 'app');

// 需要说明的是： index.php文件中的start函数位于 www/framework/portal.php中
// 所以需要设置ROOT-PATH为上一级目录
defined('ROOT_PATH') or define('ROOT_PATH', dirname(__FILE__) . "/.."); 
defined('FRAMEWORK_PATH') or define('FRAMEWORK_PATH', ROOT_PATH.'/framework/');
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH.'/vendor/');
defined('APP_PATH') or define('APP_PATH', ROOT_PATH.'/' . APP . '/');


defined('ROOT_URL') or define('ROOT_URL', './');
defined('VENDOR_URL') or define('VENDOR_URL', ROOT_URL .'vendor');
defined('APP_URL') or define('APP_URL', ROOT_URL . APP );


// 导入Framework各个模块
include_once(FRAMEWORK_PATH . 'Logging.php');
include_once(FRAMEWORK_PATH . 'Tpl.php');
include_once(FRAMEWORK_PATH . 'helper.php');
include_once(FRAMEWORK_PATH . 'Portal.php');







?>
