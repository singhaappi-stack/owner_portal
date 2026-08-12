<?php
/* 
    Objective: Util class
    Author: David Chung
    LUD: [07 May 2023] Init

*/

abstract class util{

	// public $redisHandler = null;

	public $loggerCurrentTime = null;
    public function __construct() {
		// // redis handler
		// require_once ROOT.'/class/func/redis.php';
		// $this->redisHandler = new redisHandler();
	}

	function getClientIp(): ?string
	{
		// If your proxy sets this header, and you trust it:
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Can be a comma-separated list: client, proxy1, proxy2...
			$parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$ip = trim($parts[0]);
			if (filter_var($ip, FILTER_VALIDATE_IP,
				FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
				return $ip;
			}
		}

		// Fallback to direct remote address
		if (!empty($_SERVER['REMOTE_ADDR']) &&
			filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return null;
	}

	function sql_datetime_to_display_datetime($datetime){
		return date('d M Y H:i:s', strtotime($datetime));
	}

	function sql_datetime($datetime = null){
		if ($datetime)
			return date('Y-m-d H:i:s', strtotime($datetime));
		else 
			return date('Y-m-d H:i:s');
	}

	function sql_date($date = null){
		if ($date)
			return date('Y-m-d', strtotime($date));
		else 
			return date('Y-m-d');
	}

    function random_str($len) {
        // generate random string
        // $len: the random string length
        
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randString = '';
        for ($i = 0; $i < $len; $i++) {
            $randString .= $characters[rand(0, strlen($characters)-1)];
        }
        
        return str_pad($randString, $len, '0', STR_PAD_RIGHT);
    }

	function random_int($len = 6) {
        // generate random string
        // $len: the random string length
        
        $characters = '123456789';
        $res = '';
        for ($i = 0; $i < $len; $i++) {
            $res .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $res;
    }

	function random_letter($len = 6){
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$res = '';
		for ($i = 0; $i < $len; $i++) {
			$res .= $characters[random_int(0, strlen($characters) - 1)];
		}
		
		return $res;
	}

    function parse_percent($val){
        return number_format((float)$val*100, 2, '.', '');;
    }

	function dataEncrypt($plaintext){
		// Generate a random nonce (IV)
		$nonce = random_bytes(12); // GCM standard recommends a 12-byte nonce
		$tag = random_bytes(16); // 16-byte tag

		// Encrypt the data
		$ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', base64_decode(ENCRYPTION_KEY), OPENSSL_RAW_DATA, $nonce, $tag);
	
		// Return the nonce, ciphertext, and tag as a base64-encoded string
		return base64_encode($nonce . $tag . $ciphertext);
	}
	
	function dataDecrypt($ciphertext_b64){
		// Decode the base64 encoded string
		$data = base64_decode($ciphertext_b64);

		// Extract the nonce, tag, and ciphertext
		$nonce = substr($data, 0, 12); // First 12 bytes are the nonce
		$tag = substr($data, 12, 16);   // Next 16 bytes are the tag
		$ciphertext = substr($data, 28); // Remaining bytes are the ciphertext
	
		// Decrypt the data
		return openssl_decrypt($ciphertext, 'aes-256-gcm', base64_decode(ENCRYPTION_KEY), OPENSSL_RAW_DATA, $nonce, $tag);
	}


	function parsePageInfo($counta, $current_page, $count_per_page){
		if ($counta === 0 || $count_per_page === 0 ){
			$total_page = 0;
		} else {
			$total_page = ceil($counta/ $count_per_page);
		}
		return [
			"total_count" => $counta,
			"total_page" => $total_page,
			"current_page" => $current_page, 
			"count_per_page" => $count_per_page
		];
	}
	
	// Old function 
	function set_array_key_value($array,$key){
		$new_array = [];
		foreach($array as $k=>$i){
			$new_array[$i[$key]] = $i;
		}
		return $new_array;
	}

	function get_page_from_count($total_size,$page,$per_page,$page_base_uri){
		if($page == 0){
			$page = '1';
		}
		$total_record = $total_size;
		$page_per_page = $per_page;
		$page = $page - 1;
		$page_start = $page * $page_per_page;
		$page_end = ($page+1) * $page_per_page;
		// foreach($result_array as $k=>$i){
		// 	if($k >= $page_start && $k<$page_end){
		// 		$final_array[$counter] = $i;
		// 		$counter = $counter +1;
		// 	}
		// }
		$current_page = ($page+1);
		$page_offset = '5';
		$page_size_array = [];
		$xcount=0;
		$max_offset = $current_page + $page_offset;
		$i = $current_page - $page_offset;
		if(is_array($total_size)){
			$x_total_size = 0;
			$b = 0;
			foreach($total_size as $k=>$i){
				$i_ori_page = $i['ori_page'];
				$i_per_page = $i['per_page'];
				$x_total_size = $i_ori_page+$x_total_size;
				for($b;$b<=$x_total_size;$b++){
					if($b>0 && $b<$max_offset){
						$page_size_array[$xcount] = $b;
						$xcount = $xcount + 1;
					}
				}
			}
		} else {
			$total_size = ceil($total_size / $page_per_page);
			for($i;$i<=$total_size;$i++){
				if($i>0 && $i<$max_offset){
					$page_size_array[$xcount] = $i;
					$xcount = $xcount + 1;
				}
			}
		}

		// check conjuntor
		if(strpos($page_base_uri, '?')>-1){
			$conjuntor = "&";
		} else {
			$conjuntor = "?";
		}
		$final_array['page_size_array'] = $page_size_array;
		$final_array['current_page'] = $current_page;
		$final_array['next_page'] = $current_page+1;
		$final_array['previous_page'] = $current_page-1;
		$final_array['page_per_page'] = $page_per_page;
		$final_array['max_page'] = $total_size;
		$final_array['total_record'] = $total_record;
		$final_array['page_base_uri'] = $page_base_uri;
		$final_array['conjuntor'] = $conjuntor;
		return $final_array;
	}

	function errorTrace($t){
		$res['message'] = $t->getMessage();

		$traceArray[0] = ["location" => $t->getFile() . " (" . $t->getLine() . ")"];

		foreach ($t->getTrace() AS $k => $trace){
			$funcStr = $trace['function'] . "(";
			$tA = [];
			if(isset($trace['args'])){
				foreach($trace['args'] as $args){
	
					// print_r($args);
					array_push($tA, !is_object($args) ?strval($args) : gettype($args));
					// $funcStr .= $args;
				}
				$funcStr .= implode(",", $tA);
				$funcStr .=")";
			}
			
			array_push($traceArray, [
				"location" => $trace['file'] . " (" . $trace['line'] . ")",
				"function" => $funcStr
			]);
		}
		$res['traceArray'] = $traceArray;
		return $res;
	}

	function userAgentToBrowser($userAgent){
		if (preg_match('/(Edge)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches) || preg_match('/(Edg)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches)) {
			$browserName = $matches[1];
		} elseif (preg_match('/(Chrome)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches)) {
			$browserName = $matches[1];
		} elseif (preg_match('/(Firefox)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches)) {
			$browserName = $matches[1];
		} elseif (preg_match('/(Safari)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches)) {
			$browserName = $matches[1];
		} elseif (preg_match('/(Opera)[\/\s](\d+(\.\d+)*)/', $userAgent, $matches)) {
			$browserName = $matches[1];
		} else {
			$browserName = "Undefined Browser";
		}

		return $browserName;
	}

	function getOS($user_agent) { 

		$os_platform  = "Unknown OS Platform";
	
		$os_array     = array(
							  '/windows nt 10/i'      =>  'Windows 10',
							  '/windows nt 6.3/i'     =>  'Windows 8.1',
							  '/windows nt 6.2/i'     =>  'Windows 8',
							  '/windows nt 6.1/i'     =>  'Windows 7',
							  '/windows nt 6.0/i'     =>  'Windows Vista',
							  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
							  '/windows nt 5.1/i'     =>  'Windows XP',
							  '/windows xp/i'         =>  'Windows XP',
							  '/windows nt 5.0/i'     =>  'Windows 2000',
							  '/windows me/i'         =>  'Windows ME',
							  '/win98/i'              =>  'Windows 98',
							  '/win95/i'              =>  'Windows 95',
							  '/win16/i'              =>  'Windows 3.11',
							  '/macintosh|mac os x/i' =>  'Mac OS X',
							  '/mac_powerpc/i'        =>  'Mac OS 9',
							  '/linux/i'              =>  'Linux',
							  '/ubuntu/i'             =>  'Ubuntu',
							  '/iphone/i'             =>  'iPhone',
							  '/ipod/i'               =>  'iPod',
							  '/ipad/i'               =>  'iPad',
							  '/android/i'            =>  'Android',
							  '/blackberry/i'         =>  'BlackBerry',
							  '/webos/i'              =>  'Mobile'
						);
	
		foreach ($os_array as $regex => $value)
			if (preg_match($regex, $user_agent))
				$os_platform = $value;
	
		return $os_platform;
	}

	function formatDate($dateString) {
		$date = DateTime::createFromFormat('!m/d/Y', $dateString);

		if (!$date) {
			$date = DateTime::createFromFormat('!Y/m/d', $dateString);
		}
		if (!$date) {
			$date = DateTime::createFromFormat('!Y-m-d', $dateString);
		}
		if (!$date) {
			$date = DateTime::createFromFormat('!d-m-Y', $dateString);
		}
		if (!$date) {
			return null;
		}
		return $date->format('Y-m-d');
	}
	
	function handleJsonDecode($val){
		json_decode($val);
		if (json_last_error() === JSON_ERROR_NONE)
			return json_decode($val, true);
		else 
			return  null;
	}

	function handleJsonEncode($val){
		json_encode($val);
		if (json_last_error() === JSON_ERROR_NONE)
			return json_encode($val);
		else 
			return  null;
	}

	function parseToCamelCase(string $input): string {
        // Split the input by underscores
        $parts = explode('_', $input);
        // Capitalize the first letter of each part
        $camelCase = array_map('ucfirst', $parts);
        // Join the parts together
        return implode('', $camelCase);
    }

	function formatMinutesToHourMin($minutes) {
		try {
			if ($minutes === null || $minutes === ''){
				return '';
			}
			$sign = $minutes < 0 ? '-' : '';
			$minutes = abs($minutes);
			$hours = floor($minutes / 60);
			$mins = $minutes % 60;
			return sprintf("%s%d:%02d", $sign, $hours, $mins);
		} catch (Exception $e) {
			return '';
		}
	}

	function numberToLetter($number) {
		$result = '';
		while ($number > 0) {
			$remainder = ($number - 1) % 26;
			$result = chr(65 + $remainder) . $result;
			$number = intdiv($number - 1, 26);
		}
		return $result;
	}

	function translateDayOfWeekToJapanese($dayOfWeek) {
		$days = [
			0 => '日', // Sunday
			1 => '月', // Monday
			2 => '火', // Tuesday
			3 => '水', // Wednesday
			4 => '木', // Thursday
			5 => '金', // Friday
			6 => '土'  // Saturday
		];
		return $days[$dayOfWeek] ?? '';
	}

	function getDateRangeByMonth($yearMonth){
	    // Create a DateTime object for the first day of the given month
		$startDate = new DateTime($yearMonth . '-01');

		// Create a DateTime object for the last day of the given month
		$endDate = clone $startDate; // Clone the start date
		$endDate->modify('last day of this month');

		return  $this->getDateRange($startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
	}

	function getDateRange($startDate, $endDate) {
		$dateArray = [];

		// Create DateTime objects from the input date strings
		$start = new DateTime($startDate);
		$end = new DateTime($endDate);
		
		// Ensure the end date is inclusive
		$end->modify('+1 day');

		// Loop through the date range and populate the array
		for ($date = $start; $date < $end; $date->modify('+1 day')) {
			$dateArray[] = $date->format('Y-m-d');
		}

		return $dateArray;
	}

	function isWithinLastMinutes($dateTimeString, $minutes = 1) {
		// Create a DateTime object from the given datetime string
		$targetTime = new DateTime($dateTimeString);
		
		// Get the current time
		$currentTime = new DateTime();
		
		// Calculate the time 15 minutes ago
		$timeLimit = clone $currentTime;
		$timeLimit->modify("-$minutes minutes");

		// Check if the target time is within the last 15 minutes
		return ($targetTime >= $timeLimit && $targetTime <= $currentTime);
	}

	function generateDateList($year, $month, $includeFirstDateOfNextMonth = false) {
        $dateList = [];
        $numDays = date('t', strtotime("$year-$month-01"));
        for ($i = 1; $i <= $numDays; $i++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $i);
            $dateList[] = $date;
        }
        if ($includeFirstDateOfNextMonth) {
            $nextMonth = $month + 1;
            $nextYear = $year;
            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear++;
            }
            $dateList[] = sprintf('%04d-%02d-01', $nextYear, $nextMonth);
        }
        return $dateList;
    }


	function generateExternalLoginLink($staff_id, $url){
		$url = MASTER_DOMAIN . "api/external/externalLogin?login_str=" . urlencode($this->dataEncrypt($staff_id . "|$url|" . $this->random_str(16)));
		return $url;
	}

	function parseDateToDateStr($dateString) {
		// Create a DateTime object from the date string
		$date = new DateTime($dateString);

		// Format the date with Chinese characters for year, month, and day
		$dateStr = $date->format('Y年n月j日');

		return $dateStr;
	}

	function getFinanceYear(string $dateStr): int {
		[$y, $m, $_d] = explode('-', $dateStr);
		$y = (int)$y;
		$m = (int)$m;
		return ($m >= 4) ? $y : ($y - 1);
	}

	function getFinanceYearMonthList($year){
		// Initialize an array to hold the finance year-months
		$yearMonthList = [];

		// Loop from April (month 4) to March (month 3) of the next year
		for ($offset = 0; $offset < 12; $offset++) {
			// Calculate the month number (1-12)
			$month = ($offset + 3) % 12 + 1; // Start from April (4)

			// If the month is less than 4, it belongs to the next year
			$currentYear = $year;
			if ($month < 4) {
				$currentYear++;
			}

			// Format the month as "MM"
			$monthFormatted = str_pad($month, 2, '0', STR_PAD_LEFT);
			
			// Add the formatted finance year-month to the list
			$yearMonthList[] = "{$currentYear}-{$monthFormatted}";
		}

		return $yearMonthList;
	}

	function getMonthList($start, $end) {
		$startDate = new DateTime($start);
		$endDate = new DateTime($end);
		$interval = new DateInterval('P1M');
		$daterange = new DatePeriod($startDate, $interval, $endDate, DatePeriod::INCLUDE_END_DATE);
		
		$months = [];
		foreach($daterange as $date) {
			$months[] = $date->format('Y-m');
		}
		return $months;
	}

	function convertImageToDataImage($filePath, $maxWidth = 400){
		list($width, $height, $type) = getimagesize($filePath);
        
        // Calculate the new dimensions
        if ($width > $maxWidth) {
            $ratio = $width / $height;
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $ratio;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }
        
        // Create a new image resource
        $srcImage = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
            case 'jpg':
                $srcImage = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($filePath);
                break;
            default:
                throw new Exception("Unsupported image type.");
        }

        // Create a new true color image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Resize and resample
        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Get the output buffer
        ob_start();
        imagejpeg($dstImage);  // Output to buffer
        $imageData = ob_get_contents();
        ob_end_clean();

        // Convert to Base64
        $dataUrl = 'data:image/jpeg;base64,' . base64_encode($imageData);

        // Free memory
        imagedestroy($srcImage);
        imagedestroy($dstImage);

		return $dataUrl;
	}

	function formatMoney($amount, $decimals = 0) {
		// Convert the amount to a float
		$num = floatval($amount);
		
		// Check if the number is finite
		if (!is_finite($num)) return null;

		// Format the number with the specified decimals and add commas
		return number_format($num, $decimals, '.', ',');
	}

	function uploadFile($file, $path, $filename = null){
		$maxBytes = 30 * 1024 * 1024; // 30MB

		// 1) 檢查目標資料夾
		if (!is_dir(ROOT . $path)) {
			if (!mkdir(ROOT . $path, 0775, true)) {
				return false;
			}
		}

		// 2) 基本檢查：是否有上傳
		if (!isset($file['error']) || !isset($file['tmp_name']) || !isset($file['size'])) {
			return false;
		}

		// 3) 檢查上傳錯誤
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return false;
		}

		// 4) 限制大小
		if ((int)$file['size'] > $maxBytes) {
			return false;
		}

		// 5) 檢查 tmp 檔是否為合法上傳檔
		if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
			return false;
		}

		// 6) 決定檔名（避免檔名穿越/亂碼）
		$originalName = $file['name'] ?? '';
		$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
		$orgFile = pathinfo($originalName, PATHINFO_FILENAME); 
		if ($ext === '') $ext = 'bin';

		if ($filename === null || trim($filename) === '') {
			$safeName = $orgFile. "_" . date("YmdHis", time()) . ".$ext";
		} else {
			// 給檔名：只取基本字串 + 補副檔名
			// 你也可以改成不強制副檔名
			$safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$filename);
			if ($safeBase === '') $safeBase = uniqid('upload_', true);
			$safeName = $safeBase."_". $this->sql_date() . '_' . $ext;
		}

		// 7) 組合目標檔案路徑
		$filePath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $safeName;

		// 8) 移動檔案到目標位置
		if (!move_uploaded_file($file['tmp_name'], ROOT.$filePath)) {
			return false;
		}

		return $filePath;
	}

	function uploadImage($file, $path, $filename = null){
		// Target dimensions (preserve aspect ratio if needed)
		$maxWidth = 300;
		$maxHeight = 300;
		$allowed = ['jpg', 'jpeg', 'png'];
		
		
		$tmpName = $file['tmp_name'];
		$originalName = basename($file['name']);
		$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
		if (!in_array($extension, $allowed)) {
			new apiReturn("404", "File format is not supported. Please use jpg, jpeg, png only");
		}

		

		// Get original size
		list($origWidth, $origHeight) = getimagesize($tmpName);

		// Calculate new size (maintain aspect ratio)
		$ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
		$newWidth = $origWidth * $ratio;
		$newHeight = $origHeight * $ratio;
		
		// Create new image
		$dstImage = imagecreatetruecolor($newWidth, $newHeight);

		// Load source based on type
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				$srcImage = imagecreatefromjpeg($tmpName);
				break;
			case 'png':
				$srcImage = imagecreatefrompng($tmpName);
				imagealphablending($dstImage, false);
				imagesavealpha($dstImage, true);
				break;
			default:
				imagedestroy($dstImage);
				die('Unsupported format.');
		}

		// Resize with high quality
		imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

		// Generate unique filename
		if (!$filename) {
			$filename = $originalName;
		} else {
			$filename .= ".{$extension}";
		}
		$full_path = ROOT . "$path/$filename" ; 

		// Save (quality 90 for JPEG)
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($dstImage, $full_path, 90);
				break;
			case 'png':
				imagepng($dstImage, $full_path);
				break;
		}
		
		// Cleanup
		imagedestroy($srcImage);
		imagedestroy($dstImage);

		return "{$path}/{$filename}";
	}

	

    function getDatesInMonth($yearMonth) {
        $dateArray = [];

        // Create a DateTime object for the first day of the month
        $firstDay = new DateTime($yearMonth . '-01');
        
        // Get the last day of the month
        $lastDay = clone $firstDay;
        $lastDay->modify('last day of this month');

        // Loop through the month and populate the array
        for ($date = $firstDay; $date <= $lastDay; $date->modify('+1 day')) {
            $dateArray[] = $date->format('Y-m-d');
        }

        return $dateArray;
    }
}