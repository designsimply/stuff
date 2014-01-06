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

<?php	extract($_GET);
	if (is_null($orderby))
			$orderby = "cat,subcat,subcat1,subcat2,title,description";

	$q = stripslashes( $q );
	if ( substr( $q, 0, 1 ) == '/' )
		$q = substr( $q, 1 );

	// Build query for a list of search terms to highlight
	$sql = "SELECT * FROM sf_links WHERE 
		title LIKE '%$q%' 
		OR description LIKE '%$q%' 
		OR cat LIKE '%$q%'
		OR subcat LIKE '%$q%'
		OR subcat1 LIKE '%$q%'
		OR subcat2 LIKE '%$q%'
	";
	if (isset($surl) && 'y' == $surl ) { $sql .= " OR url LIKE '%$q%' "; } 
	$sql .= " ORDER BY $orderby";

	$result = $sfdb->get_results( $sql ) or die( "Couldn't execute the query." );
	$num_rows = $sfdb->num_rows;

	// Disply search results
	if ( $num_rows > 0 ) {
		echo "<div class=\"container\">Results found: $num_rows</div>";

		$search_result = "<ul>\n";
		foreach ( $result as $row ) {
			// Hightlight searched text
			$id = $row->id;
			$ntitle = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->title);
			$ndesc = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->description);
			$ndesc2 = preg_replace("/a href=(.*)<font style='background-color: yellow'>$q<\/font>(.*)>/i", "a href=$1$q$2>",$ndesc);
			$ncat = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->cat);
			$nsubcat = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->subcat);
			$nsubcat1 = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->subcat1);
			$nsubcat2 = preg_replace("/$q/i", "<font style='background-color: yellow'>$q</font>", $row->subcat2);

			// Display search results
			if ('' != $qurl && $url==$qurl) { $search_result .= '<li class="exactmatch" style="background-color:#ffff99;">'; } else { $search_result .= '<li>'; }
			if ( sf_user_can_edit() ) { $search_result .= 
				/*"<a href=\"sf-control/edit.php?id=$row->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .*/
				"<a href=\"sf-control/edit.php?id=$row->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> ";
			}
			//$search_result .= '<a class="designsimply designsimply-edit" href="'.HOME.'sf-control/edit.php?id='.$id.'"></a> ';
			//$search_result .= "\n<form id=\"sf_delete\" name=\"delete_$id\" action=\"".HOME."sf-control/edit.php?cat=$ncat\" method=\"post\" style=\"display: inline;\">\n\t<input name=\"status\" type=\"hidden\" value=\"delete\" />\n\t<input name=\"id\" type=\"hidden\" value=\"$id\" />\n";
			if (isset($ncat)) {$search_result .= "\t<input name=\"cat\" type=\"hidden\" value=\"$ncat\" />\n";}
			if (isset($nsubcat)) {$search_result .= "\t<input name=\"subcat\" type=\"hidden\" value=\"$nsubcat\" />\n";}
			if (isset($nsubcat1)) {$search_result .= "\t<input name=\"nsubcat1\" type=\"hidden\" value=\"$nsubcat1\" />\n";}
			if (isset($nsubcat2)) {$search_result .= "\t<input name=\"nsubcat2\" type=\"hidden\" value=\"$nsubcat2\" />\n";}
			$search_result .= "\t<a class=\"designsimply designsimply-delete\" onclick=\"return confirm('Are you sure you want to delete $title?');document.getElementById('sf_delete').submit();\"></a>\n</form> ";

			if ($ncat) {$search_result .= "<a href=\"".HOME."home/".strtolower($ncat)."\">$ncat</a> > ";}
			if ($nsubcat) {$search_result .= "<a href=\"".HOME."home/".strtolower($ncat)."/".strtolower($nsubcat)."\">$nsubcat</a> > ";}
			if ($nsubcat1) {$search_result .= "<a href=\"".HOME."home/".strtolower($ncat)."/".strtolower($nsubcat)."/".strtolower($nsubcat1)."\">$nsubcat1</a> > ";}
			if ($nsubcat1) {$search_result .= "<a href=\"".HOME."home/".strtolower($ncat)."/".strtolower($nsubcat)."/".strtolower($nsubcat1)."/".strtolower($nsubcat2)."\">$nsubcat2</a> > ";}
			if ($url) {$search_result .= "<a href=\"$url\">$ntitle</a>";} else {$search_result .= "$ntitle";}
			if ($ndesc) {$search_result .= " - $ndesc</li>\n";} else {$search_result .= "</li>\n";}
		}
		$search_result .= "</ul>\n";

	 	echo $search_result;
} else {
 	echo "<center><p>A search for \"$q\" returned 0 results.</p></center>";
} ?>

<footer>
	<a href="<?php echo HOME; ?>" accesskey="h">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	<!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
	as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
