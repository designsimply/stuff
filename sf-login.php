<?php
/**
 * Stuff Log In Page
 *
 * Log in page to authenticate, register, reset or remind about passwords.
 *
 * @package Stuff
 */

require_once( 'sf-load.php' );

/**
 * Output log in page header.
 */
function login_header() { ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Stuff Log In</title>
	<link rel="stylesheet" id="admin-css"  href="<?php echo HOME; ?>sf-control/css/admin.css" type="text/css" media="all" />
	<meta name="robots" content="noindex,nofollow" />
</head>
<body class="login">
<div class="login-container">
<?php } // end of login_header()

/**
 * Output log in form.
 */
function login_form() { 
	$output = <<<END
	<h1>Stuff</h1>
	<form name="login-form" class="login-form" action="sf-login.php" method="post">
		<fieldset>
			<legend>Secure Login</legend>
			<div class="login-field above-below15 above30 clear">
				<label for="user" class="placeholder active">Username</label>
				<input type="text" name="user" id="user" tabindex="1" class="av-text" value="">
			</div>
			<div class="login-field above-below15">
				<label for="pass" class="placeholder ">Password</label>
				<input type="password" name="pass" id="pass" tabindex="2" class="av-password" value="">
			</div>
			<input type="submit" value="sign in" class="button float-left no-transform" tabindex="3">
			<label for="stay-signed-in">stay signed in</label>
			<input type="checkbox" title="Select to stay signed in 7 days" name="stay-signed-in" id="stay-signed-in" value="1" tabindex="9">\n
END;
	foreach( $_GET as $k => $v ) {
		$output .= "\t\t\t" . '<input type="hidden" name="' . $k . '" value="' . $v . '" />' . "\n";
	}
	$output .= "\t\t</fieldset>\n\t</form>\n\t";
	$output .= '<p class="back">';
	$output .= '<a href="' . HOME . '">&larr; Home</a></p>';
	return $output;
} // end of login_form()

/**
 * Output log in page footer.
 */
function login_footer() { ?>
</div>
</body>
</html>
<?php } // end of login_footer()

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
switch ($action) {

case 'login' :
default:
	login_header();
	// If a username is present, do it to it.
	if ( ! empty( $_POST['user'] ) && sf_authenticate($_POST['user'], $_POST['pass']) ) {
		sf_set_auth_cookie($_POST['user'], $_POST['remember']);
		$data['url']   = $_POST['url'];
		$data['title'] = $_POST['title'];
		$data['desc']  = $_POST['desc'];
		$qs = http_build_query( $data );
		echo 'You are now logged in. <a href="'. HOME . 'post.php?' . $qs . '">Continue</a>.';
		if ( ! empty( $qs ) ) {
			header( 'Location: ' . HOME . 'post.php?' . $qs );
		} else {
			header( 'Location: ' . HOME . 'latest.php' );
		}

	// If ia username is present but the cookie doesn't validate, error out.
	} elseif ( ! empty( $_POST['user'] ) && ! sf_validate_auth_cookie() ) {
		echo 'Login failed.';
		echo ' <a href="' . HOME . 'sf-login.php">Try again</a>.';
		echo login_form();

	// Try logging in.
	} else {
		echo login_form();
	}
	login_footer();
break;
} // end action switch
?>
