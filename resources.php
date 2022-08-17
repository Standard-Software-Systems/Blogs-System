<?php
	// Set default timezone
	date_default_timezone_set('America/New_York');

    // Set domain
    $domain = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Create DB object
    $db = new mysqli($db['host'], $db['user'], $db['password'], $db['name']);
	// Create unique paste ID
    function createPasteID() {
        global $db;
        
        do {
            $repeat = false;
            $id = generateRandomString(8);
            $check = $db->query("SELECT id FROM blogs WHERE id = '$id'");

            if($check->num_rows != 0) {
                $repeat = true;
            }

            else {
                return $id;
            }
        } while($repeat = true);
    }

	function console_log($output, $with_script_tags = true) {
			$js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
			if ($with_script_tags) {
				$js_code = '<script>' . $js_code . '</script>';
			}
			echo $js_code;
	}
	// Create random string
    function generateRandomString($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	// Check string to see if contains flag words
	function checkForFlags($str) {
		global $site;
		$isFlagged = false;
		$flaggedWord = null;
		
		$str = strtolower($str);
		foreach($site['flagWords'] as $word) {
			if(stripos($str, $word) !== false) {
				$isFlagged = true;
				$flaggedWord = $word;
			}
		}

		return [$isFlagged, $flaggedWord];
	}

    // API function
	function apiRequest($url, $post=FALSE, $headers=array()) {
		$ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		$response = curl_exec($ch);


		if($post)
		    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

		$headers[] = 'Accept: application/json';

		if(session('access_token'))
		  $headers[] = 'Authorization: Bearer ' . session('access_token');

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		return json_decode($response);
  	}

	// Get function
	function get($key, $default=NULL) {
		return array_key_exists($key, $_GET) ? $_GET[$key] : $default;
	}

	// Session function
	function session($key, $default=NULL) {
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
	}

	function sendEmbed($title, $url, $fields) {
		global $site;

		$timestamp = date("c", strtotime("now"));

		$json = json_encode([
			"username" => "Paste Now",

			"embeds" => [
				[
					"title" => $title,
					"type" => "rich",
					"url" => $url,
					"timestamp" => $timestamp,
					"footer" => [
						"icon_url" => $site['logo'],
					],
					"fields" => $fields,
				],
			],
		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		$ch = curl_init($site['webhook']);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}
?>