<?php
/* BLT Smarty Web System */
/*
	PHP Version Required : php74+
	SQL Supported : mySQL / mariaDB
	Smarty Version : 3.1.35
	Suggested Server : Apache / nginx / Tomcat
	auJgyJFFWgXC5uvtdYUM 
*/
define('CURRENT_VERSION', '1.0.0');		// system version
define("ORIGIN_ALLOW_ARR", ["localhost","localhost:3001","localhost:3002","localhost:3000", "127.0.0.1"]);

date_default_timezone_set('UTC');
define('ROOT',substr(dirname(__FILE__),0,-8));
define('LIB', ROOT.'/include/');
$server_config = file_get_contents(ROOT."/servermark.config");
define('LANG',@$_SERVER["HTTP_ACCEPT_LANGUAGE"]);

if(isset($_SERVER['HTTP_REFERER'])){
    define('REFERER',$_SERVER['HTTP_REFERER']);
} else {
	define('REFERER','');
}
ini_set('default_socket_timeout', 20);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

if (isset($_SERVER['HTTP_HOST']))
    $http_host = $_SERVER['HTTP_HOST'];
else 
    $http_host = '';

if (isset($_SERVER['SERVER_ADDR']))
    $server_addr = $_SERVER['SERVER_ADDR'];
else 
    $server_addr = '';

// LOGGER
define('LOG_FILE_DIRECTORY', ROOT. "/../../logger/");           // Log File Directory
define('LOG_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('LOG_RETENTION_TIME', 14); // day in unit


define("VALIDATE_EXPIRE_TIME", 60 * 5); // In second
require_once "config_setting.php";

switch (strtolower($server_config)) {
    case 'local':
		require_once "env/local.php";
		break;
    case 'dev':
		require_once "env/dev.php";
		break;
}

foreach ($config_list as $key => $config){
	define($key, $config);
}
?>


