<!-- Register a new user. -->
<?php
include('configure.php');
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="assets/style.css" rel="stylesheet" title="Style" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<title>Sign up</title>
	</head>
	<body>
<?php
// check if the form has been sent
if(isset($_POST['username'], $_POST['password'], $_POST['passverif'], $_POST['email']) and $_POST['username'] != '')
{
	// check if the two passwords are identical
	$errors = [];
	if($_POST['password'] == $_POST['passverif'])
	{
		// check if the chosen password is strong enough.
		if(checkPassword($_POST['password'], $errors))
		{
			// check if the email form is valid
			if(preg_match('#^(([a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+\.?)*[a-z0-9!\#$%&\\\'*+/=?^_`{|}~-]+)@(([a-z0-9-_]+\.?)*[a-z0-9-_]+)\.[a-z]{2,}$#i', $_POST['email']))
			{
				// protect the variables
				$username = mysqli_real_escape_string($link, $_POST['username']);
				$password = mysqli_real_escape_string($link, $_POST['password']);
				$email	  = mysqli_real_escape_string($link, $_POST['email']);
				$salt	  = (string)rand(10000, 99999);	     // generate a five digit salt
				$password = hash("sha512", $salt.$password); // compute the hash of salt concatenated to password
				// check if there is no other user with the same username
				$dn = mysqli_num_rows(mysqli_query($link, 'select id from users where username="'.$username.'"'));
				if($dn == 0)
				{
					// Save the informations to the database
					if(mysqli_query($link, 'insert into users(username, password, email, salt) values ("'.$username.'", "'.$password.'", "'.$email.'","'.$salt.'")'))
					{
						// Dont display the form
						$form = false;
?>
<div class="container">
<div class="message">You have been successfully signed up. You may log in now.<br />
<a href="login.php">Login</a></div>
</div>
<?php
					}
					else
					{
						// An error occurred
						$form	= true;
						$message = 'An error occurred while signing up.';
					}
				}
				else
				{
					// Username is not available
					$form	= true;
					$message = 'The username is already in use, please choose another one.';
				}
			}
			else
			{
				// Email is not valid
				$form	= true;
				$message = 'The email adresss is invalid.';
			}
		}
		else
		{
			// Password is too weak
			$form	= true;
			$message = '';
			foreach ($errors as $item)
				$message = $message.$item."<BR>";
		}
	}
	else
	{
		// Passwords are not identical
		$form	 = true;
		$message = 'The passwords are not identical.';
	}
}
else
{
	$form = true;
}
if ($form) {
	//Display a message
	if(isset($message)) echo '<div class="message">'.$message.'</div>';

	//Display the form again
?>
<div class="container" style="padding:15% ">

	<h4>Please fill in the following form to sign up:</h4>
	<form action="sign_up.php" method="post">
	<div class="form-group">
			<div class="center">
				<label for="username">Username</label><input type="text" class="form-control" name="username" value="<?php if(isset($_POST['username'])){echo htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
				<label for="password">Password<span class="small"> (10 characters min.)</span></label><input type="password" class="form-control" name="password" /><br />
				<label for="passverif">Password<span class="small"> (verification)</span></label><input type="password" class="form-control" name="passverif" /><br />
				<label for="email">Email</label><input type="email" class="form-control" name="email" value="<?php if(isset($_POST['email'])){echo htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');} ?>" /><br />
				<button type="submit" class="btn btn-primary">SIGN UP</button>

			</div>

	</div>
	</form>
	<div class="float-right"><a href="index.php">Home</a></div>
</div>
<?php
}
?>
</div>
</body>
</html>
