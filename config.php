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




defined('DOMAIN_URL') or define('DOMAIN_URL', 'https://www.bloodtear.cn/');
defined('INSTANCE_URL') or define('INSTANCE_URL', basename(dirname(dirname(__FILE__))) . "/" );
defined('ROOT_URL') or define('ROOT_URL', DOMAIN_URL . INSTANCE_URL);
defined('VENDOR_URL') or define('VENDOR_URL', ROOT_URL .'vendor');
defined('APP_URL') or define('APP_URL', ROOT_URL . APP );

// redis
// defined('REDIS_HOST') or define('REDIS_HOST', '127.0.0.1');
defined('REDIS_HOST') or define('REDIS_HOST', '180.76.160.113');
defined('REDIS_PORT') or define('REDIS_PORT', '6379');
defined('REDIS_PWD') or define('REDIS_PWD', 'xiaoyu');




// 导入Framework各个模块
include_once(FRAMEWORK_PATH . 'Logging.php');
include_once(FRAMEWORK_PATH . 'Tpl.php');
include_once(FRAMEWORK_PATH . 'Database.php');
include_once(FRAMEWORK_PATH . 'Cache.php');
include_once(FRAMEWORK_PATH . 'helper.php');
include_once(FRAMEWORK_PATH . 'portal.php');
include_once(FRAMEWORK_PATH . 'action.php');
include_once(FRAMEWORK_PATH . 'parse.php');







?>
