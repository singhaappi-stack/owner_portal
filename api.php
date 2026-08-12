<?php
require_once 'class/func/baseModel.class.php';

class api extends baseModel
{
	public $RM;
	public $RMG;
	public $device_uid;

	public function __construct(){
		parent::__construct('./api'); //	 定義 html 檔案路徑		
		$this->init();
	}

	public function init(){
		// middleWare
		// new accessRight();
		
		// if ($_SESSION && $_SESSION['staff'] && $_SESSION['staff']['permission_list']){
		// 	$permission_list = array_column($_SESSION['staff']['permission_list'], "module");
		// 	$permission_list[] = "account";
		// 	$permission_list[] = "general";
		// 	$permission_list[] = "external";
		// 	$permission_list[] = "attendance";
		// 	$request_mode = str_replace(".php", "" , $this->mode);
		// 	if(in_array($request_mode, $permission_list)){
		// 		// success 
		// 	} else {
		// 		new apiReturn(401);
		// 	}
		// }
		$this->try_for_php_file($this->mode);
	}

	function try_for_php_file($mode){
		
		if (isset($_SERVER['HTTP_APPI']) && $_SERVER['HTTP_APPI'] == 'Y'){
			ini_set('display_errors', '1');
			ini_set('display_startup_errors', '1'); 
			error_reporting(E_ALL);
		}
		
		$php_file = 'api/' . $mode.".php";
		if (file_exists($php_file)) {
			unset($_REQUEST['mode']);
			unset($_REQUEST['submode']);
			require_once $php_file;
			new routing($this);
		} else {
			new apiReturn(404);
		}
	}

}
$HOME = new api;


