<?php require_once( 'sf-load.php' ); ?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<title>stuff</title>
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-extend/themes/stuff/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-includes/genericons.css">
	<script type="text/javascript" src="<?php echo HOME; ?>sf-extend/themes/stuff/script.js"></script>
</head>

<body class="list <?php if ( sf_user_can_edit() ) { echo "loggedin"; } ?>">
<div style="text-align:center;"><?php sf_get_search_form(); ?></div>

<?php 
$paged = $_GET['paged'];
$limit = $_GET['limit'];

if ( $paged < 1 )
	$paged = 1;

if ( $limit < 1 ) {
	$limit = 20;
} else if ( $limit > 200 ) {
	$limit = 200;
}

$start = 0;
$paged -= 1;
$next = $paged + 2;
$start = $paged * $limit;

echo "<p style=\"text-align: center;]\">";
if ( $paged > 0 )
	echo "<a href=\"" . HOME . "latest.php?paged=$paged&limit=$limit\" alt=\"previous\"><span class=\"genericon genericon-previous\"></span>previous</a> ";

echo "<a href=\"" . HOME . "latest.php?paged=$next&limit=$limit\" alt=\"next\">next <span class=\"genericon genericon-next\"></span></a> ";
echo "</p>";

$result = $sfdb->get_results( "SELECT * FROM sf_links ORDER BY datecreated DESC LIMIT $start, $limit" );
foreach ( $result as $row ) {
	$lastmod = strtotime( $row->lastmodified );
	$datemod = strftime( "%b %e", $lastmod );
	$sincewhen  = time_since( $lastmod );
	$i = '';
	if ( sf_user_can_edit() ) { $i .= 
		/*"<a href=\"sf-control/edit.php?id=$row->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .*/
		"<a href=\"sf-control/edit.php?id=$row->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> ";
	}
	$i .= "<abbr class=\"date\" title=\"$sincewhen\">$datemod</abbr> " . 
		"<a href=\"$row->url\" title=\"$row->title\">$row->title</a> ";
		if ( '' != $row->description ) { $i .= "&mdash; $row->description\n"; }
	$items[] = $i;
}

echo '<ul>';
foreach ( $items as $item ) {
	echo "<li>$item</li>";
}
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
