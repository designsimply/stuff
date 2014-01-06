<?php
require_once( '../sf-load.php' );
sf_validate_auth_cookie();

/*
if ( sf_validate_auth_cookie() ) {
	echo 'Already logged in.';
} else {
	echo 'Login failed.';
}
*/

sf_validate_auth_cookie();

echo 'test';
