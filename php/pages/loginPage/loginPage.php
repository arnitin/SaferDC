#!/usr/local/bin/php
<?php
$message = $_GET['m'];

if($message == 'error'){
	echo "
		<script type='text/javascript'>
		alert('Error: Either user does not exist, or email/password combination is wrong.');
		</script>
	";
}else if($message == 'registered')
{
	echo "
		<script type='text/javascript'>
		alert('Success: User has been registered!');
		</script>
	";
}

$type = $_POST['type'];

if($type == 'log'){
	//Login
	$email = $_POST['email'];
		$password = $_POST['password'];
	$encrypted_password = sha1($password);
	
	//DB
	$connection = oci_connect(
		$db_username = 'nar',
		$db_password = 'abcd1234',
		$db_connection_string = '//oracle.cise.ufl.edu/orcl'
	);
	$statement = oci_parse($connection, "SELECT NAME,EMAIL_ID FROM DB_USER WHERE EMAIL_ID = '$email' AND PASSWORD = '$encrypted_password'");
	oci_execute($statement);
	
	$row = oci_fetch_object($statement);
	if($row->EMAIL_ID == null){
		echo "Error: Not registered or Email/Password combination invalid.";
	}else{
		echo "Welcome ".$row->NAME.".";
	}
	oci_free_statement($statement);
	oci_close($connection);
	
	//echo "Type: $type<BR>Data: $email - $password<BR>Encrypted password: $encrypted_password<BR>Redirecction via ajax to: [url_TBD]";
}else if($type == 'reg'){
	//Registration
	$name = $_POST['name'];
	$address = $_POST['address'];
	$email = $_POST['email'];
		$password = $_POST['password'];
	$encrypted_password = sha1($password);
	
	//DB
	$connection = oci_connect(
		$username = 'nar',
		$password = 'abcd1234',
		$connection_string = '//oracle.cise.ufl.edu/orcl'
	);
	$statement = oci_parse($connection, "INSERT INTO DB_USER VALUES ('$name',1,'$encrypted_password','$email','$address')");
	oci_execute($statement);
	oci_commit($connection);
	oci_free_statement($statement);
	oci_close($connection);
	
	echo "Type: $type/ Data: $name - $address - $email - $password<BR>Encrypted password: $encrypted_password<BR>Redirecction via ajax to: [url_TBD]";
}
?>