<?php
/**
 * Stuff Login Page
 *
 * Login page to authenticate, register, reset or remind about passwords.
 *
 * @package Stuff
 */

require_once( '../sf-load.php' );
sf_validate_auth_cookie();

$user = $_POST['user'];
$pass = $_POST['pass'];
$email = $_POST['email'];
$hash = '*'; // In case the user is not found
$hash_cost_log2 = 8;
$hash_portable = FALSE;


$hasher = new PasswordHash($hash_cost_log2, $hash_portable);
$hash = $hasher->HashPassword($pass);
if (strlen($hash) < 20)
	fail('Failed to hash new password');
unset($hasher);

if ( isset( $_POST['email'] ) ) {

function fail($pub, $pvt = '')
{
	$msg = $pub;
	if ($pvt !== '')
		$msg .= ": $pvt";
	exit("An error occurred ($msg).\n");
}

$sfdb->query( "INSERT INTO sf_user (user_id, user_login, user_pass, user_email) VALUES (NULL, '$user', '$hash', '$email')" );
$sfdb->debug();

}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php //language_attributes(); ?>>
<head>
        <meta http-equiv="Content-Type" content="<?php //bloginfo('html_type'); ?>; charset=<?php //bloginfo('charset'); ?>" />
        <title><?php //bloginfo('name'); ?> &rsaquo; <?php echo $title; ?></title>
</head>
<body class="login">

<h1>Create user</h1>
<form action="create-user.php" method="POST">
Username:<br>
<input type="text" name="user" size="64"><br>
Password:<br>
<input type="password" name="pass" size="64"><br>
Email:<br>
<input type="text" name="email" size="100"><br>
<input type="submit" value="Create user">
</form>

</body>
</html>
