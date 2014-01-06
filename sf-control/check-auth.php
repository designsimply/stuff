<?php
/**
 * Stuff Auth Test
 *
 * Login page to authenticate, register, reset or remind about passwords.
 *
 * @package Stuff
 */

require_once( '../sf-load.php' );
sf_validate_auth_cookie();

$now = time(); 
$salt = 'STUFF';

list( $identifier, $token ) = explode( ':', $_COOKIE['stuff'] );
//var_dump( $_COOKIE['stuff'] );
echo '<br>'.$identifier;
echo '<br>'.$token;

if ( ctype_alnum( $identifier ) && ctype_alnum( $token ) ) {
	$clean['identifier'] = $identifier; 
	$clean['token'] = $token;
} else {
	echo 'Error';
}

$esc_identifier = mysql_real_escape_string( $clean['identifier'] );
$user = $db->get_row( "SELECT user_login, user_token, user_timeout FROM sf_user WHERE user_identifier = '$esc_identifier'" );
$db->debug();
//echo '<br>user token = '.$user->user_token;
//echo '<br>clean[token]= ' . $clean['token'];
//echo '<br>'; var_dump( $user );

if ( $clean['token'] != $user->user_token ) {
	echo 'Login failed. (wrong token)';

} elseif ( $now > $user->user_timeout ) {
	echo 'Login failed. (timeout)';

} elseif ( $clean['identifier'] != sha1( $salt . sha1( $user->user_login . $salt ) ) ) {
	echo 'Login failed. (invalid identifier)';

} else {
	echo 'Successful login.';

}

