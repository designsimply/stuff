<?php require_once( 'sf-load.php' ); ?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
		<title>stuff</title>
		<link rel="stylesheet" href="<?php echo HOME; ?>sf-extend/themes/stuff/style.css" type="text/css" media="all">
		<link rel="stylesheet" href="<?php echo HOME; ?>sf-includes/genericons.css">
		<script type="text/javascript" src="<?php echo HOME; ?>sf-extend/themes/stuff/script.js"></script>
</head>

<body class="search list <?php if ( sf_user_can_edit() ) { echo "loggedin"; } ?>">
<div style="text-align:center;"><?php sf_get_search_form(); ?></div>

<?php
	extract($_GET);
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
	$total = $sfdb->num_rows;
	$sql .= " LIMIT $start, $limit";
	$result = $sfdb->get_results( $sql ) or die( "Couldn't execute the query." );

	echo "<p style=\"text-align: center;]\">";
	if ( $paged > 0 )
		echo "<a href=\"?q=$q&paged=$paged&limit=$limit\" alt=\"previous\"><span class=\"genericon genericon-previous\"></span>previous</a> ";

	if ( $total > ($paged+1) * $limit )
		echo "<a href=\"?q=$q&paged=$next&limit=$limit\" alt=\"next\">next <span class=\"genericon genericon-next\"></span></a> ";

	echo "</p>";

	// Disply search results
	if ( $total > 0 ) {
		$lower = ($paged * $limit) + 1;
		$upper = ($paged + 1) * $limit;
		if ( $total < $upper ) { $upper = $total; }

		$search_result  = "<ul class=\"list-with-dates\">\n";
		$search_result .= "<li><strong>Search results $lower to $upper of $total</strong></li>";
		foreach ( $result as $row ) {
			$row = (array) $row;
			//$row = str_ireplace( $q, "<span style=\"background-color: yellow\">$q</span>", $row );
			$row['title'] = str_ireplace( $q, "<span style=\"background-color: yellow\">$q</span>", $row['title'] );
			$row['description'] = str_ireplace( $q, "<span style=\"background-color: yellow\">$q</span>", $row['description'] );

			// Display search results
			if ('' != $qurl && $url==$qurl) { $search_result .= '<li class="exactmatch" style="background-color:#ffff99;">'; } else { $search_result .= '<li>'; }
			if ( sf_user_can_edit() ) { $search_result .= 
				/*"<a href=\"sf-control/edit.php?id=$row->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .*/
				"<a href=\"sf-control/edit.php?id=$row[id]\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> ";
			}
			/*
			$search_result .= "\n<form id=\"sf_delete\" name=\"delete_$id\" action=\"".HOME."sf-control/edit.php?cat=$ncat\" method=\"post\" style=\"display: inline;\">\n\t<input name=\"status\" type=\"hidden\" value=\"delete\" />\n\t<input name=\"id\" type=\"hidden\" value=\"$id\" />\n";
			if (isset($ncat)) {$search_result .= "\t<input name=\"cat\" type=\"hidden\" value=\"$ncat\" />\n";}
			if (isset($nsubcat)) {$search_result .= "\t<input name=\"subcat\" type=\"hidden\" value=\"$nsubcat\" />\n";}
			if (isset($nsubcat1)) {$search_result .= "\t<input name=\"nsubcat1\" type=\"hidden\" value=\"$nsubcat1\" />\n";}
			if (isset($nsubcat2)) {$search_result .= "\t<input name=\"nsubcat2\" type=\"hidden\" value=\"$nsubcat2\" />\n";}
			$search_result .= "\t<a class=\"designsimply designsimply-delete\" onclick=\"return confirm('Are you sure you want to delete $title?');document.getElementById('sf_delete').submit();\"></a>\n</form> ";
			*/
			$lastmod = strtotime( $row['lastmodified'] );
			$datemod = strftime( "%d %b %Y", $lastmod );
			$sincewhen  = time_since( $lastmod );
			$search_result .= "<abbr class=\"date\" title=\"$datemod\">$sincewhen</abbr> ";
			if ($row['cat'])     {$search_result .= "<a href=\"".HOME."home/".strtolower($row['cat'])."\">$row[cat]</a> &rarr; ";}
			if ($row['subcat'])  {$search_result .= "<a href=\"".HOME."home/".strtolower($row['cat'])."/".strtolower($row['subcat'])."\">$row[subcat]</a> &rarr; ";}
			if ($row['subcat1']) {$search_result .= "<a href=\"".HOME."home/".strtolower($row['cat'])."/".strtolower($row['subcat'])."/".strtolower($row['subcat1'])."\">$row[subcat1]</a> &rarr; ";}
			if ($row['subcat2']) {$search_result .= "<a href=\"".HOME."home/".strtolower($row['cat'])."/".strtolower($row['subcat'])."/".strtolower($row['subcat1'])."/".strtolower($row['subcat2'])."\">$row[subcat2]</a> &rarr; ";}
			if ($row['url'])     {$search_result .= "<a href=\"$row[url]\">$row[title]</a>";} else {$search_result .= "$row[title]";}
			if ($row['description']) {$search_result .= " - $row[description]</li>\n";} else {$search_result .= "</li>\n";}
		}
		$search_result .= "</ul>\n";

	 	echo $search_result;
} else {
 	echo "<center><p>A search for \"$q\" returned no results.</p></center>";
} ?>

<footer>
	<a href="<?php echo HOME; ?>" accesskey="h">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	<!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
	as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
