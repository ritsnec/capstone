<?php
include('configure.php');
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="assets/style.css" rel="stylesheet" title="Style" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<title>Create New Message</title>
	</head>
	<body>

<?php
//Check if the user is logged in
if (isset($_SESSION['username'])) {
	$form     = true;
	$title   = '';
	$recip   = '';
	$new_message = '';
	//Check if the form has been sent
	if (isset($_POST['title'], $_POST['recip'], $_POST['message'])) {
		$title   = $_POST['title'];
		$recip   = $_POST['recip'];
		$new_message = $_POST['message'];

	if ($_POST['title'] != '' and $_POST['recip'] != '' and $_POST['message'] != '') {
		$title   = $_POST['title'];
		$recip   = $_POST['recip'];
		$new_message = $_POST['message'];
			$dn1 = mysqli_fetch_array(mysqli_query($link, 'select count(id) as recip, id as recipid from users where username="'.$recip.'"'));
			if ($dn1['recip'] == 1) {
				//Check if the recipient is not the actual user
				if ($dn1['recipid'] != $_SESSION['userid']) {
					//We encrypt then send the message
					$cipher = "aes-256-gcm";
					$ivlen  = openssl_cipher_iv_length($cipher);
					$iv     = openssl_random_pseudo_bytes($ivlen);
					$key    = getKey($_SESSION['userid'], $dn1['recipid']);
					$tag    = null;
					$method = openssl_get_cipher_methods();
					if (in_array($cipher, $method)) {
						$iv = openssl_random_pseudo_bytes($ivlen);
						$ciphertext_raw = openssl_encrypt($new_message, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv, $tag);
						$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
						$ciphertext = base64_encode($iv.$hmac.$ciphertext_raw);
						$tag_encoded = base64_encode($tag);    // store $cipher, $hmac and $iv for decryption later
						$query = 'INSERT INTO mailbox (subject, sender, recipient, message,timestamp,tag) values ("'.$title.'", "'.$_SESSION['userid'].'", "'.$dn1['recipid'].'", "'.$ciphertext.'", "'.time().'", "'.$tag_encoded.'")';
						if (mysqli_query($link, $query)) {
?>
		<div class="message">The message has successfully been sent.<br />
		<a href="mailbox.php">Mailbox</a></div>

<?php
							$form = false;
						}
						else $error = 'An error occurred while sending the message.'. mysqli_error($link);//Otherwise, we say that an error occured
					}
					else $error = 'Error while sending the message.';//Otherwise, we say the user cannot send a message to himself
				}
				else $error = 'You cannot send a message to yourself.';//Otherwise, we say the user cannot send a message to himself
			}
			else $error = 'The recipient does not exist.';//Otherwise, we say the recipient does not exists
		}
		else $error = 'Please fill in all of the fields.';//Otherwise, we say a field is empty
	}

	if ($form) {
		//Display a message if necessary
		if (isset($error)) echo '<div class="message">'.$error.'</div>';

		//Display the form
?>
<div class="container" style="padding:5%">
<h1 style="text-align:center">Create New Message</h1>
			<form action="create_msg.php" method="post">
				<h5 style="text-align:center">Please fill the following form to send a message.</h5>
				<label for="recip">Recipient<span class="small"> (Username)</span></label><input class="form-control" type="text" value="<?php echo htmlentities($recip, ENT_QUOTES, 'UTF-8'); ?>" id="recip" name="recip" /><br />
				<label for="title">Title</label><input type="text" class="form-control" value="<?php echo htmlentities($title, ENT_QUOTES, 'UTF-8'); ?>" id="title" name="title" /><br />
				<label for="message">Message</label><textarea cols="40" class="form-control" rows="5" id="message" name="message"><?php echo htmlentities($new_message, ENT_QUOTES, 'UTF-8'); ?></textarea><br />
				<input type="submit" class="form-control" value="Send" />
			</form>
</div>

<?php
	}
}
else echo '<div class="message">You must be logged in to access this page.</div>';
?>
		<div class="foot"><a href="index.php">Home</a></div>
	</body>
</html>
