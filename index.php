<!-- Home page. App starts here. -->
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

        <title>Home</title>
    </head>
    <body>
        <div class="container" style="padding:15% " >
        <?php
        //Display a welcome message and, if the user is logged in, display the username
        ?>
        Hello
        <membername>
        <?php
        if(isset($_SESSION['username'])) {
	         echo ' '.htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');
         }
        ?>
        </membername>!

        Welcome to Secure messaging system.<br /><br />
        <?php
        //If the user is logged in, display links to see the list of users, his/her pms and a link to log out
        if (isset($_SESSION['username'])) {
	         echo 'Please see here for a <a href="users.php">list of all users</a> you can send a message to.<br /><br />';
           echo '<a href="create_msg.php" class="link_create_msg">Write new message</a><br />';
	         //We display the links
        ?>
          <a href="mailbox.php" class="link_create_msg">Mailbox</a>
          <br /><br />
          <a href="logout.php">Logout</a>
          <?php
        }
        else {
          //Otherwise, display a link to sign up / log in
          ?>
          <div class="row">
          <div class="col">
          <form action="login.php">
          <button type="botton"  class="btn btn-primary">Login</button>
          </form>
          </div>
          <div class="col">
          <form action="sign_up.php">
          <button type="botton" class="btn btn-primary">Signup</button>
          </form>
          </div>
          </div>
          <?php
        }
          ?>
		</div>
	</body>
</html>
