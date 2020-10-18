<?php
// start the session
session_start();
$host = "localhost";
$usrname = "root";
$pwd = "root";
$db = "capstone_db";
$port = "8889";


// Create connection
$link = new mysqli($host, $usrname, $pwd, $db, $port);	// connect to the db

// Check connection
if ($link->connect_error) {
  die("Connection failed: " . $link->connect_error);
}

// checkPassword: check if the password is strong enough
function checkPassword($pwd, &$errors) {
	$errors_init = $errors;

	if (strlen($pwd) < 10) $errors[] = "Password must be at least 10 characters long!";
	if (!preg_match("#[0-9]+#", $pwd)) $errors[] = "Password must include at least one number!";
	if (!preg_match("#[a-z]+#", $pwd)) $errors[] .= "Password must include at least one lowercase letter!";
	if (!preg_match("#[A-Z]+#", $pwd)) $errors[] .= "Password must include at least one uppercase letter!";
	if (!preg_match("#\W+#", $pwd)) $errors[] .= "Password must include at least one symbol!";

	return ($errors == $errors_init);
}

// getKey: retrieve password for encrypted messages in database
// $user1 and $user2: the users that the message belongs to
function getKey($user1, $user2) {
	global $link;

	//Message DataBase. Access data cryptography hardcoded.
	$cipher = "aes-256-gcm";
	$ivlen  = openssl_cipher_iv_length($cipher);
  $iv		= base64_decode("zT/PiCvCiUYtd96Pwogrwp1Br0lGLd7PiCvCicKdbUs");
	$dbkey  = base64_decode("5AIQwo8fWMKaUxDI9R9YwppTwqPCmlPCo5emwo8fWOg"); // hardcoded random key with 256 bits
  $tag    = Null;
	if ($user1 > $user2) {
		$tmp = $user1;
		$user1 = $user2;
		$user2 = $tmp;
	};

	$method = openssl_get_cipher_methods();
	if (in_array($cipher, $method)) {
		$key = base64_encode(openssl_random_pseudo_bytes(32)); // generate a new random key (256 bits) in case it is the first message
    $tag    = null;
    $encrypted_key = openssl_encrypt($key, $cipher, $dbkey, 0, $iv, $tag);
		$req = mysqli_query($link, 'select * from messagekeys where user1="'.$user1.'" and user2="'.$user2.'"');
		$dn  = mysqli_num_rows($req);
		$dat = mysqli_fetch_array($req);

    $tag_encoded = base64_encode($tag);
		// check if it is the first message between the two users
		if ($dn == 0) {
    mysqli_query($link, 'insert into messagekeys(user1, user2, mskey, tag) values ('.$user1.', "'.$user2.'", "'.$encrypted_key.'", "'.$tag_encoded.'")');
    }
    else {

			$cryp_key = $dat['mskey'];
      $tag1 = base64_decode($dat['tag']);
      $key = openssl_decrypt($cryp_key, $cipher, $dbkey, 0, $iv, $tag1);
		}
    return $key;
	}
	else return false;
}


?>
