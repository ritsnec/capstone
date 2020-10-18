<?php
include('configure.php');
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <title>Mailbox</title>
    </head>
    <body>
        <div class="content"></div>
<?php
// check if the user is logged in
if(isset($_SESSION['username'])) {
    // list the messages in a table
    // fetch the sent and received messages
    $rcvdMsg = mysqli_query($link, 'select mailbox.id, mailbox.message, mailbox.subject, mailbox.timestamp, mailbox.tag, users.id userid, users.username from mailbox, users where mailbox.recipient="'.$_SESSION['userid'].'" and users.id=mailbox.sender order by mailbox.timestamp desc');
    $sentMsg = mysqli_query($link, 'select mailbox.id, mailbox.message, mailbox.subject, mailbox.timestamp, mailbox.tag, users.id userid, users.username from mailbox, users where mailbox.sender="'.$_SESSION['userid'].'" and users.id=mailbox.recipient order by mailbox.timestamp desc');
?>
    <div class="container" style="padding:10%">
    <div class="float-right">
    <a href="create_msg.php" class="link_create_msg">Write new message</a><br />
    </div>
    <div class="row">
    <h3>Received messages (<?php echo intval(mysqli_num_rows($rcvdMsg)); ?>):</h3>

    <div class="container" style="padding:20px;">

<?php
    // display the list of received messages
    while($dn1 = mysqli_fetch_array($rcvdMsg)){
?>
        <div class="row">
        <div class="col"><b><?php echo htmlentities($dn1['subject'], ENT_QUOTES, 'UTF-8'); ?></b></div>
        <div class="col"><?php echo htmlentities($dn1['username'], ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="col"><?php echo date('Y/m/d H:i:s' ,$dn1['timestamp']); ?></div>
        <div class="col">
<?php
        $cipher = "aes-256-gcm";
        $ivlen  = openssl_cipher_iv_length($cipher);
        $key    = getKey($_SESSION['userid'], $dn1['userid']);
        $method = openssl_get_cipher_methods();

        if (in_array($cipher, $method)) {
              $c    = base64_decode($dn1['message']);
              $iv   = substr($c, 0, $ivlen);
              $hmac = substr($c, $ivlen, $sha2len=32);
              $ciphertext_raw = substr($c, $ivlen+$sha2len);
              $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
              $tag_decoded=base64_decode($dn1['tag']);

              if (!hash_equals($hmac, $calcmac)) {	//PHP 5.6+ timing attack safe comparison
                  $decrypted = "Message decryption integrity failed.";
              }
              else {
                  $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv, $tag_decoded);
              }
        }
        else $decrypted = "Decryption algorithm unsupported.";

        echo $decrypted;
        ?>
    </div>
</div>

<?php
    }
    //If there is no received message we notice it
    if(intval(mysqli_num_rows($rcvdMsg))==0){
?>
        <div class="row">
        <div class="col" colspan="4" class="center">You have no received messages.</div>
        </div>
<?php
    }
?>
    </div>
    <br />
    <h3>Sent messages (<?php echo intval(mysqli_num_rows($sentMsg)); ?>):</h3>
    <div class="container" style="padding:20px;">

<?php
    // display the list of sent messages
    while($dn2 = mysqli_fetch_array($sentMsg)){
?>
    <div class="row">
    <div class="col"><b><?php echo htmlentities($dn2['subject'], ENT_QUOTES, 'UTF-8'); ?></b></div>
    <div class="col"><?php echo htmlentities($dn2['username'], ENT_QUOTES, 'UTF-8'); ?></div>
    <div class="col"><?php echo date('Y/m/d H:i:s' ,$dn2['timestamp']); ?></div>
    <div class="col">
<?php
        $cipher = "aes-256-gcm";
        $ivlen  = openssl_cipher_iv_length($cipher);
        $key    = getKey($_SESSION['userid'], $dn2['userid']);
        $method = openssl_get_cipher_methods();

        if (in_array($cipher, $method)) {
          $c    = base64_decode($dn2['message']);
          $iv   = substr($c, 0, $ivlen);
          $hmac = substr($c, $ivlen, $sha2len=32);
          $ciphertext_raw = substr($c, $ivlen+$sha2len);
          $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
          $tag_decoded=base64_decode($dn2['tag']);

          if (!hash_equals($hmac, $calcmac)) {	//PHP 5.6+ timing attack safe comparison
              $decrypted = "Message decryption integrity failed.";
          }
          else {
              $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv, $tag_decoded);
          }
        }
        else $decrypted = "Decryption algorithm unsupported.";

        echo $decrypted;
?>

    </div>
    </div>
<?php
    }
    //If there is no sent message we notice it
    if(intval(mysqli_num_rows($sentMsg))==0){
?>
      <div class="row">
      <div class="col" colspan="4" class="center">You have no sent messages.</div>
      </div>
<?php
    }
?>

<?php
}
else {
	echo 'You must be logged in to access this page.';
}
?>
</div>
</div>
<div class="float-right"><a href="index.php">Home</a></div>

</div>
</body>
</html>
