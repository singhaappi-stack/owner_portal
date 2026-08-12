<?php
require_once ROOT. '/class/func/util.class.php';
class paramBuilder extends util
{
	public function param($param = null){
		return $this->paramBuilder([$param])[$param['key']] ?? null; 
	}
	
	public function getHeader(){
		$headers = apache_request_headers();
		return $headers;
	}

	public function getParam(){
		switch ($_SERVER['REQUEST_METHOD']){
			case 'GET':
				$body = null;
				$query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
				if(isset($query)){
					parse_str($query, $output);
					$body = $output;
				}
				break;
			case 'POST':
				$body = $_POST;
				break;
			case 'PUT':
			case 'PATCH':
			case 'DELETE':
				$body = json_decode(file_get_contents('php://input'), true);
				break;
			default:
				return null;
		}

		return $body;
	}

    public function paramBuilder(Array $paramList = null, $body = null)
    {
		if(!$body)
			$body = $this->getParam();

		$res = [];
		foreach ($paramList as $param){
            $key = $param['key'];
			$requestKey = isset($param['requestKey']) ? $param['requestKey'] : $key; // allow null
			$type = isset($param['type']) ? $param['type'] : 'string';
			$request = isset($param['request']) ? $param['request'] : null;
			$isCompulsory = isset($param['isCompulsory']) ? $param['isCompulsory'] : false;
			$defaultValue = isset($param['defaultValue']) ? $param['defaultValue'] : null;
			$allowBlank = isset($param['allowBlank']) ? $param['allowBlank'] : true;
			$paramJson = isset($param['paramJson']) ? $param['paramJson'] : null;

			switch ($request){
				case 'session':
					if (isset($_SESSION[$requestKey])){
						$res[$key] = $this->parse($_SESSION[$requestKey], $type);
					}
					break;
				case 'header':
					if (isset($_SERVER["HTTP_".strtoupper($requestKey)])){
						$res[$key] = $this->parse($_SERVER["HTTP_".strtoupper($requestKey)], $type);
					}
					break;
				case 'self':
					$res[$key] = $defaultValue;
					break;
				case 'file':
					if (isset($_FILES[$requestKey])){
						$res[$key] = $_FILES[$requestKey];
					}

					// Check the file type
					if(isset($param['type'])){
						$res[$key] = $this->parse($res[$key], $type);
					}
					break;
				default:
					if (isset($body[$requestKey])){
						$res[$key] = $this->parse($body[$requestKey], $type);
					}

					if($isCompulsory && (!isset($res[$key]) || !$res[$key])){
						(ENV != "DEV" && ENV != "LOCAL") ? new apiReturn(400, 4001) : new apiReturn(400, 4001,["key_for_dev" => $requestKey]);
					}

					if(!$allowBlank && $res[$key] === ''){
						(ENV != "DEV" && ENV != "LOCAL") ? new apiReturn(400, 4001) : new apiReturn(400, 4001,["key_for_dev" => $requestKey]);
					}
					break;
			}

			if (!isset($res[$key]) && $defaultValue){
				$res[$key] = $defaultValue;
			}

			// for json parameter
			if($paramJson && is_array($res[$key])){
                foreach($res[$key] as $k => $v){
                    $res[$key][$k] = $this->paramBuilder($paramJson, $v);
				}
			}
		}
		
        return $res;
    }

	function prevent_injection($data){

		// if(isset($_REQUEST['mode'])){
		// 	if($_REQUEST['mode']=='file' || $_REQUEST['submode'] == "validatePassword"){
		// 		$pass = 'Y';
		// 		return $pass;	
		// 	}
		// }
		// $pass = 'Y';

		// $sql_injection_patterns = array(
		// 	// "/--/",
		// 	// "/;/",
		// 	"/'/",
		// 	"/\"/",
		// 	"/\*/",
		// 	// "/\(/",
		// 	// "/\)/",
		// 	// "/select/i",
		// 	// "/union/i",
		// 	// "/from/i",
		// 	// "/where/i"
		// );
		
		// foreach ($sql_injection_patterns as $pattern) {
		// 	if (preg_match($pattern, $val)) {
		// 		// Handle SQL injection attempt
		// 		new apiReturn(400, 400060);
		// 	}
		// }

		$val = "";
		
		if($data && is_array($data)){
			foreach($data as $k=>$i){
				if(is_array($i)){
				// do nothing
				} else {
					$i = preg_replace('/\s+/', '', $i);
					$val = $val.$i;
				}
			}
		}

		if(is_array($val)){
			// do nothing
		} else {
			if($val != strip_tags($val)){
				// is HTML contain
				new apiReturn(400, 400060);
			}
		}

		$pos = strpos(strtolower('x'.$val), 'deletefrom');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		
		$pos = strpos(strtolower('x'.$val), 'select*from');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		
		$pos = strpos(strtolower('x'.$val), 'truncatetable');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		/*foreach($_REQUEST as $k=>$i){
			$_REQUEST[$k] = htmlspecialchars($i, ENT_QUOTES, 'UTF-8');
		}*/
		$pos = strpos(strtolower('x'.$val), '<script>');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), 'onerrormprompt');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), 'onerrorm=prompt');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), 'document.write');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), 'document.');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), 'xmlhttpequest');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		
		$pos = strpos(strtolower('x'.$val), 'http.o');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		
		$pos = strpos(strtolower('x'.$val), 'http.s');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), '<?php');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), '?>');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), '?>');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), '1=1');
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), "'='");
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
		$pos = strpos(strtolower('x'.$val), "alert(");
		if ($pos === false) {
			// did nth
		} else {
		    new apiReturn(400, 400060);
		}
	}

    function parse($val, $parsingType){ 
        switch ($parsingType){
            case 'yearMonth':
                $val = $this->parseYearMonth($val);
                break;
            case 'int':
                $val = $this->parseInt($val);
                break;
            case 'datetime':
                $val = $this->parseDatetime($val);
                break;
            case 'timestamp':
                $val = $this->parseTimestamp($val);
                break;
            case 'date':
                $val = $this->parseDate($val);
                break;
            case 'time':
                $val = $this->parseTime($val);
                break;
            case 'NY':
                $val = $this->parseNY($val);
                break;
			case 'NYNA':
				$val = $this->parseNYNA($val);
				break;
            case 'intArr':
                $val = $this->parseIntArr($val);
                break;
            case 'arr':
                $val = $this->parseArr($val);
                break;
            case 'json':
                $val = $this->parseJson($val);
                break;
            case 'email':
                $val = $this->parseEmail($val);
                break;
            case 'float':
                $val = $this->parseFloat($val);
                break;
			case 'filter':
				$val = $this->parseFilter($val);
				break;
			case 'excel':
				$val = $this->paramExcelFile($val);
				break;
			case 'encryptedString':
				$val = $val ? $this->dataDecrypt($val) : $val;
				break;
			case 'encryptString':
				$val = $val ? $this->dataEncrypt($val) : $val;
				break;
			case 'encryptArr':
				$val = str_replace("\n", ",", $val);

				$val = $this->parseArr($val);
				foreach($val as $k => $v){
					$val[$k] = $v ? $this->dataEncrypt($v) : $v;
				}
				break;
			case 'obj':
				$val = $val;
				break;
			case 'chineseString':
				$val = $this->parseString($val);
				break;
			case 'hkic':
				$val = $this->parseString($val);
				$val = $val ? $this->dataEncrypt(strtoupper($val)) : $val;
				break;
            default:
            case 'string':
            case 'varchar':
				$val = $this->parseString($val);
				// only allow english and number for input
				$regex = '/^[^!@#\$%\^&\*\(\)\';"a-zA-Z0-9@ ]*$/';
                // do nothing
                break;
        }

		// Injection Checking
		$this->prevent_injection($val);

        return $val;
    }

	private function parseString($val){
		$val = ltrim($val); 
		$val = rtrim($val);

		return $val;
	}

    private function parseInt($val){
		$val = preg_replace("/[^0-9\.]/", "", $val );
		
		if (trim($val) == ''){
			return null;
		}else{
			$val = intval($val);
		}
		return $val;
	}

	private function parseDatetime($val){
		$arr = explode(" ", $val);
		if (sizeof($arr) < 2){
			$arr = explode("T", $val);	
		}

		if (sizeof($arr) < 2){
			return null;;
		}

		$date = $this->parseDate($arr[0]);
		if ($date == null)
			return null;;

		$time = $this->parseTime($arr[1]);
		if ($time == null)
			$time = null;

		return $date . " " . $time;
	}

	private function parseTimestamp($val){
		$arr = explode(" ", $val);
		if (sizeof($arr) < 2){
			$arr = explode("T", $val);	
		}

		if (sizeof($arr) < 2){
			return null;;
		}

		$date = $this->parseDate($arr[0]);
		if ($date == null)
			return null;;

		$time = $this->parseTime($arr[1]);
		if ($time == null)
			return null;

		return $date . " " . $time;
	}

	private function parseDate($val){
		$arr = explode("-", $val);

		if (sizeof($arr) != 3)
			return null;

		if (!checkdate(intval($arr[1]),intval($arr[2]),intval($arr[0])))
			return null;

		return $val;
	}

	private function parseTime($val){
		$arr = explode(":", $val);

		if (sizeof($arr) == 2)
			array_push($arr, "00");

		if (sizeof($arr) != 3)	
			return null;
	
		foreach ($arr as $t){
			$t = floatval($t);
			if ($t <0 || $t > 60)
				return null;
		}

		return $val;
	}

	private function parseNY($val){
		if($val == 'Y')
			return 'Y';
		else if ($val == 'N')
			return 'N';
		else 
			return null;
	}

	private function parseNYNA($val){
		if($val == 'Y')
			return 'Y';
		else if ($val == 'N')
			return 'N';
		else if ($val == 'NA')
			return 'NA';
		else 
			return null;
	}

	private function parseIntArr($val){
		if(is_array($val)){
			$tr = $val;
		}else{
			$val = ltrim($val, '[');
			$val = rtrim($val, ']');
			$tr = explode(",", $val);
		}

		$res = [];
		foreach($tr as $val){
			if (!is_null($val) && $val != ''){
				array_push($res, $this->parseFloat($val));
			}
		}
		return $res;
	}

	private function parseJson($val){
		json_decode($val);

   		if (json_last_error() === JSON_ERROR_NONE)
		   	return json_decode($val, true);
		else 
			return  null;
	}

	private function parseArr($val){
		if(is_array($val))
			return $val;

		$val = ltrim($val, '[');
		$val = rtrim($val, ']');
		$tr = str_getcsv($val, ",", "\"", "\\");
		$res = [];
		foreach($tr as $val){
			if(isset($val))
				$val = trim($val);
				$val = $this->parseString($val);

			if ($val && $val !=''){
				array_push($res, $val);
			}
		}
		return $res;
	}

	private function parseYearMonth($val){
		// Check pattern YYYY-MM
		if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $val)) {
			return null;
		}
		return $val;
	}

	private function parseFloat($val){
		$val = preg_replace("/[^0-9\.]/", "", $val );
		try {
			if($val === ''){
				return null;
			}else{
				return floatval($val);
			}
		} catch(Exception $e) {
			return null;
		}
	}
	
	private function parseEmail($val){
		if(!filter_var($val, FILTER_VALIDATE_EMAIL)) {
			return null;
		} else {
			return $val;
		}
	}

	public function parseFilter($filter_json){
		$filter_json = $this->parseJson($filter_json);
		$res = null;
		if(isset($filter_json)){
			foreach ($filter_json as $i){
				$key = $this->parseString($i['key']);
				$value = $i['value'];
	
				if(!$key) continue;

				$filter_val = $this->parseJson($value);
				if(is_array($filter_val)){
					// Array
					$filter_val = $this->parseIntArr($value);
				}else{
					// String
					$filter_val = $this->parseString($value);
				}
				
				$res["filter|$key"] = $filter_val;
			}
		}
		return $res;
	}

	private function paramExcelFile($val){
		if(empty($val)){
			new apiReturn(400, 400044);
		}

		for ($i = 0; $i < sizeof($val['name']); $i++){
			if(!in_array($val['type'][$i], ["application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application/vnd.ms-excel"])){
				new apiReturn(400, 400044);
			}
		}

		return $val;
	}
}
