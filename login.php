<!-- Authenticate a registered user. -->
<?php
include('configure.php');

$username = "";
$password = "";
//if the session is active redirect to the landing page
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['username'], $_POST['password'])) {
			$username = mysqli_real_escape_string($link, $_POST['username']);
			$password = $_POST['password'];

      // fetch the password of the user
      $req = mysqli_query($link, 'select password,id,salt from users where username="'.$username.'"');
      $dn  = mysqli_fetch_array($req);
      if ($dn == NULL) {
        echo ("Incorrect Username");
      }
      else {
        $password = (hash("sha512", $dn['salt'].$password)); // salt the password and hash it
		    // compare the salted password hash with the real one, and check if the user exists
        if ($dn['password'] == $password and mysqli_num_rows($req)>0) {
	         // save the user name in the session username and the user Id in the session userid
			     $_SESSION['username'] = $_POST['username'];
			     $_SESSION['userid'] = $dn['id'];
           header("Location: index.php");
        }
        else {
          // Otherwise, the credentials are incorrect
          echo("Incorrect password!");
        }
      }
}

?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <title>Login</title>
    </head>
    <body>

		<div class="container" style="padding:15%">
			<form action="login.php" method="post">
        <div class="form-group">
				<div class="center">
					<label for="username">Username</label><input type="text" class="form-control" name="username" id="username" value="<?php echo($username); ?>" /><br />
					<label for="password">Password</label><input type="password" class="form-control" name="password" id="password" /><br />
					<button type="submit" class="btn btn-primary">LOGIN</button>
				</div>
			</form>
		</div>
        <div class="foot" class="float-right"><a href="index.php">Home</a></div>
</div>
  </body>
</html>
