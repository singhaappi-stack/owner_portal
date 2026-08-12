<?php
require_once dirname(__FILE__).'/../../include/config.php';
require_once ROOT.'/include/commonHeader.php';
require_once ROOT.'/class/func/util.class.php';
require_once ROOT.'/class/func/apiReturn.class.php';
require_once ROOT.'/class/func/apiRedirect.class.php';
require_once ROOT.'/class/func/db.class.php';

abstract class baseModel extends util{
	public $tpl; 
	//public $DBC;
	public $DB;
	public $DBC;
	public $session;
	public $connectionIp;
	public $mode;
	public $submode;
	public $thirdmode;
	public $home;
	public $lang;
	public $pwapage = 'N';					// spa mode
	public $isMobile = 'N';
	public $rm = 'N';
	public $navObject = array();			// page nave object
	public $refinedHeaders = array(); 		// apache header
	public $editor = 'N'; 					// editor mode

	public function __construct(){
	 	if(isset($_REQUEST['mode']))
            $this->mode = $_REQUEST['mode'];
        if(isset($_REQUEST['submode']))
            $this->submode = $_REQUEST['submode'];

        // 檢查有否惡意的注入代碼
        $this->prevent_injection();

		// 檢查是否排錯模式
		$this->error_display();

		// 數據庫準備

		// SESSION
		require_once ROOT.'/class/func/Session.class.php';
		$this->session = new Session();	

		$this->connectionIp= $this->get_connection_ip();
	}	

	function error_display(){
		if (isset($_REQUEST['error']))
			$error = $_REQUEST['error'];
		else if (isset($_SERVER['HTTP_ERROR']))
			$error = $_SERVER['HTTP_ERROR'];

		if(isset($error)){
			if($error =='Y'){
				error_reporting(E_ALL);
				ini_set('display_errors', 1);		
			}
			if($error =='A'){
				error_reporting(E_ERROR);
				ini_set('display_errors', 1);		
			}
		} else {
			error_reporting(0);
			ini_set('display_errors', 0);		
		}
	}

	function showError(){
		error_reporting(E_ALL);
		ini_set('display_errors', 1);	
	}

	function get_connection_scheme(){
		// http or https
		if(@$_SERVER['HTTP_X_FORWARDED_PROTO']){
			return $_SERVER['HTTP_X_FORWARDED_PROTO'];
		}
		if(@$_SERVER['HTTPS'] == 'on'){
			return 'https';
		}
		if(@$_SERVER['HTTP_X_REQUEST_SCHEME'] == 'https'){
			return 'https';
		}
		if(@$_SERVER['REQUEST_SCHEME']){
			return $_SERVER['REQUEST_SCHEME'];
		}
		return 'http';
	}

	function prevent_injection(){
		if(isset($_REQUEST['mode'])){
		}
		$pass = 'Y';
		$string = "";
		$string_ori = "";
		foreach($_REQUEST as $k=>$i){
			if(is_array($i)){
			// do nothing
			} else {
				$i = preg_replace('/\s+/', '', $i);
				$string = $string.$i;
			}
		}
		foreach($_REQUEST as $k=>$i){
			if(is_array($i)){
			// do nothing
			} else {
				$string_ori = $string_ori.$i;
			}
		}

		if($string_ori != strip_tags($string_ori)){
		  // is HTML coataine
		  $pass = 'N';
		}
		
		$pos = strpos(strtolower('x'.$string), 'deletefrom');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		
		$pos = strpos(strtolower('x'.$string), 'select*from');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		
		$pos = strpos(strtolower('x'.$string), 'truncatetable');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		/*foreach($_REQUEST as $k=>$i){
			$_REQUEST[$k] = htmlspecialchars($i, ENT_QUOTES, 'UTF-8');
		}*/
		$pos = strpos(strtolower('x'.$string), '<script>');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), 'onerrormprompt');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), 'onerrorm=prompt');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), 'document.write');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), 'document.');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), 'xmlhttpequest');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		
		$pos = strpos(strtolower('x'.$string), 'http.o');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		
		$pos = strpos(strtolower('x'.$string), 'http.s');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), '<?php');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), '?>');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), '?>');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), '1=1');
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), "'='");
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}
		$pos = strpos(strtolower('x'.$string), "alert(");
		if ($pos === false) {
			// did nth
		} else {
		    $pass = 'N';
		}

		
		if($pass == 'N'){
			new apiReturn(-904);
		}
	}

	function get_connection_ip(){
		$ip = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		return $ip;
	}

	function escape_string($data){
		return mysqli_real_escape_string($this->DBC->Link_ID,$data);
	}

	function https_handling($https_redirect_on='N'){
		$connection_scheme = $this->get_connection_scheme();
		// clould flare CDN paramter
		if($connection_scheme  == 'http' && $https_redirect_on == 'Y'){
			$script_url = $_SERVER['REQUEST_URI'];
			$http_host = $_SERVER['HTTP_HOST'];
			$script_url = 'https://'.$http_host.$script_url;
			header("Location:".$script_url);
			exit;
		} 
	}
}

?>