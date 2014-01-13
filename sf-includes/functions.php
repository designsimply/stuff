<?php
/**
 * Check login information.
 */
function sf_authenticate($username, $password) {
	global $sfdb;

	$esc_username = mysql_real_escape_string( $username );
	$esc_password = mysql_real_escape_string( $password );
	$stored_hash = $sfdb->get_var( "SELECT user_pass FROM sf_user WHERE user_login = '$username'" );
	$hasher = new PasswordHash($hash_cost_log2, $hash_portable);

	if ( ! $hasher->CheckPassword( $password, $stored_hash ) )
		return false;

	unset($hasher);
	return true;
}

/**
 * Sets an authentication cookie.
 */
function sf_set_auth_cookie( $username, $remember = false ) {
	global $sfdb;

	$salt = 'STUFF';
	$identifier = sha1( $salt . sha1( $username . $salt ) );
	$token = sha1( uniqid( mt_rand(), true ) );

	// Set timeout to 7 days if remember me is checked, otherwise set it to one day.
	if ( $remember ) {
		$timeout = time() + 60 * 60 * 24 * 7;
	} else {
		$timeout = time() + 60 * 60 * 24;
	}

	$sfdb->query( "UPDATE sf_user SET user_identifier = '$identifier', user_token = '$token', user_timeout = '$timeout' WHERE user_login = '$username'" );
	//echo "setcookie( 'stuff', \"$identifier:$token\", $timeout, '/' )";
	setcookie( 'stuff', "$identifier:$token", $timeout, '/' );
}

/**
 * Validates the authentication cookie.
 */
function sf_validate_auth_cookie() {
	global $sfdb;

	$salt = 'STUFF';
	list( $identifier, $token, $timeout ) = explode( ':', $_COOKIE['stuff'] );

	if ( ctype_alnum( $identifier ) && ctype_alnum( $token ) )
		$clean['identifier'] = $identifier;
		$clean['token'] = $token;

	$current_time = $sfdb->get_var("SELECT " . $sfdb->sysdate());
	$esc_identifier = mysql_real_escape_string( $clean['identifier'] );
	$user = $sfdb->get_row( "SELECT user_login, user_token, user_timeout FROM sf_user WHERE user_identifier = '$esc_identifier'" );

	if ( $clean['token'] != $user->user_token )
		die( 'Login failed. (wrong token)<br><a href="' . HOME . 'sf-login.php">Try again</a>.' );

	if ( time() > $user->user_timeout ) {
		//die( 'Login failed. (timeout)<br><a href="' . HOME . 'sf-login.php">Try again</a>.' );
		header( 'Location: ' . HOME . 'sf-login.php' . '?' . http_build_query( $_GET ) );
	}

	if ( $clean['identifier'] != sha1( $salt . sha1( $user->user_login . $salt ) ) )
		die( 'Login failed. (invalid identifier)<br><a href="' . HOME . 'sf-login.php">Try again</a>.' );

	// Successful login.
	return true;
}

function sf_user_can_edit() {
	global $sfdb;

	$salt = 'STUFF';
	list( $identifier, $token, $timeout ) = explode( ':', $_COOKIE['stuff'] );

	if ( ctype_alnum( $identifier ) && ctype_alnum( $token ) )
		$clean['identifier'] = $identifier;
		$clean['token'] = $token;

	$current_time = $sfdb->get_var("SELECT " . $sfdb->sysdate());
	$esc_identifier = mysql_real_escape_string( $clean['identifier'] );
	$user = $sfdb->get_row( "SELECT user_login, user_token, user_timeout FROM sf_user WHERE user_identifier = '$esc_identifier'" );

	if ( isset( $user->user_token ) )
		return true;
}

/*
 * Print time since a given date.
 */
function time_since( $date ) {
        date_default_timezone_set( 'UTC' );
        $bit =  array(
                array( 'y', 60 * 60 * 24 * 365 ),
                array( 'm', 60 * 60 * 24 * 30 ),
                array( 'w', 60 * 60 * 24 * 7 ),
                array( 'd', 60 * 60 * 24 ),
                array( 'h', 60 * 60 ),
                array( 'm', 60 ),
                array( 's', 1 ),
        );
        $since = time() - $date;

        for ($i = 0, $j = count($bit); $i < $j; $i++) {
                // Get the first item
                $name = $bit[$i][0];
                $seconds = $bit[$i][1];

                // Find the biggest chunk (if the chunk fits, break)
                if ( ( $count = floor( $since / $seconds ) ) != 0 ) { break; } 
        }
        $output[] = $count . $name;

        if ($i + 1 < $j) {
                // Get the second item
                $name2 = $bit[$i + 1][0];
                $seconds2 = $bit[$i + 1][1];

                // Add a second item if its count is greater than 0
                if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) > 0 ) {
                        $output[] = $count2 . $name2;
                }
        }
        if ($output) { $output[] = 'ago'; }

        return implode( ' ', $output );
}

/**
 * Get total row count
 */
function sf_get_number_rows() {
	global $sfdb;

	$number_rows = $sfdb->get_var( "SELECT count(*) FROM sf_links use index (id);" );
	echo $number_rows;
}

/**
 * Get last modified timestamp from the links table
 */
function sf_get_last_modified() {
	global $sfdb;

	$update_time = $sfdb->get_var( "SELECT MAX(lastmodified) FROM sf_links LIMIT 1;" );
	echo $update_time;
}

/**
 * Print bookmarklet
 */
function sf_the_bookmarklet() {
	$output = "javascript:(function()%7B%20var%20q;%20q=((window.getSelection%20&&%20window.getSelection())%20%7C%7C(document.getSelection%20&&%20document.getSelection())%20%7C%7C(document.selection%20&&document.selection.createRange%20&&document.selection.createRange().text));location.href='".SITEURL.HOME."post.php?url='+encodeURIComponent(location.href)+'&title='+escape(document.title)+'&desc='+escape(q)+escape('');%7D)();";

	return $output;
}

/** 
 * Print search form
 */
function sf_get_search_form() {
	$output = '<form id="main-form" name="f" method="get" action="search.php">
	<h1 id="main-title"><a href="' . HOME . '">stuff</a></h1>
	<label for"q">search</label>
	<input id="q" name="q" autofocus>
	<input name="submit" type="submit" value="Search">
	</form>';

	echo $output;
}
