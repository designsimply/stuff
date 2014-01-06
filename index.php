<?php require_once( 'sf-load.php' ); ?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<title>stuff</title>
	<link rel="stylesheet" href="sf-extend/themes/stuff/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="sf-includes/genericons.css">
	<script type="text/javascript" src="sf-extend/themes/stuff/script.js"></script>
</head>

<body class="home">
<div style="text-align:center;"><?php sf_get_search_form(); ?></div>

<?php 
	$result = $sfdb->get_results( "SELECT cat FROM sf_links GROUP BY cat ORDER BY cat" );
	foreach ( $result as $row ) {
		$categories[] = "<a href=\"home.php?cat=$row->cat\">$row->cat</a><br />\n";
	}

	echo '<ul>';
	foreach ( $categories as $v )
		echo "<li>$v</li>";

	echo "</ul>";
?>

<footer>
	<a href="<?php echo HOME; ?>" accesskey="h">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	<!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
	as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
