<!-- Shows a list of users and their emails. -->
<?php
include('configure.php');
?>


<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="assets/style.css" rel="stylesheet" title="Style" />
		<title>List of users</title>
	</head>
	<head>
<style>
table
{
border-style:solid;
border-width:2px;
border-color:pink;
}
</style>
</head>
<body bgcolor="#EEFDEF">


	<body>
		<div class="content">
			This is the list of members:
				<?php
				// fetch the IDs, usernames and emails of users
				echo "<table border='1'>
				<tr>
				<th>Id</th>
				<th>Username</th>
				<th>email</th>
				</tr>";
				$req = mysqli_query($link, 'select id, username, email from users');
				while ($dnn = mysqli_fetch_array($req)) {
				?>

				<tr>
					<td class="left"><?php echo $dnn['id']; ?></td>
					<td class="left"><?php echo htmlentities($dnn['username'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td class="left"><?php echo htmlentities($dnn['email'], ENT_QUOTES, 'UTF-8'); ?></td>
				</tr>

				<?php
				}
				?>
			</table>
		</div>
		<div class="foot"><a href="index.php">Home</a></div>
	</body>
</html>
