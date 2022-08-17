<?php
    // Set Discord URLs
    $authorizeURL = 'https://discordapp.com/api/oauth2/authorize';
    $tokenURL = 'https://discordapp.com/api/oauth2/token';
    $apiURLBase = 'https://discordapp.com/api/users/@me';

    // Start session
    session_start();

	// If logout is sent
	if(isset($_POST['logout'])) {
		session_unset();
		session_destroy();
		session_write_close();
		setcookie(session_name(),'',0,'/');
	}

    // If need to exchange code
	if(get('code')) {
		// Define domain (with stripped trailing slash and Discord code)
		$patterns = [
			"/(.*?)\/index.php$/",
			"/(.*?)\/$/",
			"/(.*?)\/\?code=(.*?)$/"
		];
		$domainSplit = preg_replace($patterns, '$1', $domain);

		// Create API request
		$token = apiRequest($tokenURL, array(
			"grant_type" => "authorization_code",
			'client_id' => $discord['client'],
			'client_secret' => $discord['secret'],
			'redirect_uri' => $domainSplit,
			'code' => get('code')
		));

		// Create logout token and set access token
		$logout_token = $token->access_token;
		$_SESSION['access_token'] = $token->access_token;

		// Reload page
		header('Location: '.$_SERVER['PHP_SELF']);
	}

	// If token has been exchanged, a user is authenticated
	else if(session('access_token') && !isset($_SESSION['id'])) {
		// Get user information from API
		$swapUser = apiRequest($apiURLBase);

		// Assign SESSION variables
		$_SESSION['username'] = $swapUser->username;
		$_SESSION['discriminator'] = $swapUser->discriminator;
		$_SESSION['avatar'] = "https://cdn.discordapp.com/avatars/".$swapUser->id."/".$swapUser->avatar;
		$_SESSION['id'] = $swapUser->id;
		$_SESSION['email'] = $swapUser->email;

		$user = $db->query("SELECT name, avatar, discriminator FROM users WHERE discordID = ".$_SESSION['id']);

		// If not already in database
		if($user->num_rows == 0) {
			$date = date("Y-m-d h:i:s A");
			$addUser = $db->prepare("INSERT INTO users (discordID, banned, name, discriminator, avatar, email, joinDate) VALUES (?, 0, ?, ?, ?, ?, ?)");
			$addUser->bind_param("isssss", $_SESSION['id'], $_SESSION['username'], $_SESSION['discriminator'], $_SESSION['avatar'], $_SESSION['email'], $date);
			$addUser->execute();
		}

		// If in database
		else {
			$user = $user->fetch_assoc();

			// If user information doesn't match
			if($user['name'] != $_SESSION['username'] || $user['avatar'] != $_SESSION['avatar'] || $user['discriminator'] != $_SESSION['discriminator']) {
				$updateUser = $db->prepare("UPDATE users SET name = ?, discriminator = ?, avatar = ? WHERE discordID = ?");
				$updateUser->bind_param("sssi", $_SESSION['username'], $_SESSION['discriminator'], $_SESSION['avatar'], $_SESSION['id']);
				$updateUser->execute();
			}
		}
	}

	// If need to send to Discord for login
	else if(!isset($_SESSION['id']) && isset($_POST['login'])) {
		// Define domain (with stripped trailing slash)
		$patterns = [
			"/(.*?)\/index.php$/",
			"/(.*?)\/$/",
		];
		$domainSplit = preg_replace($patterns, '$1', $domain);

		// Create parameters
		$params = array(
			'client_id' => $discord['client'],
			'redirect_uri' => $domainSplit,
			'response_type' => 'code',
			'scope' => 'identify guilds guilds.join email'
		);

		// Redirect the user to Discord's authorization page
		header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
		die();
	}

	if(isset($_SESSION['id'])) {
		// Grab user
		$user = $db->query("SELECT banned FROM users WHERE discordID = ".$_SESSION['id']);
		$user = $user->fetch_assoc();

		// See if they're banned
		if($user['banned'] == true) {
			echo "You have been banned from this site.";
			die();
		}

		// If user is an admin
		elseif(in_array($_SESSION['id'], $site['admins'])) {
			$_SESSION['admin'] = true;
		}

		// If user isn't an admin
		else {
			$_SESSION['admin'] = false;
		}
	}
?>