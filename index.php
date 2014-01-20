<?php require_once( 'sf-load.php' ); ?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<title>stuff</title>
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-extend/themes/stuff/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-includes/genericons.css">
	<script type="text/javascript" src="<?php echo HOME; ?>sf-extend/themes/stuff/script.js"></script>
</head>

<body class="list <?php if ( sf_user_can_edit() ) { echo "loggedin"; } if ( empty( $_GET ) ) { echo " home"; } ?>">
<div style="text-align:center;"><?php sf_get_search_form(); ?></div>

<?php 
if ( empty( $_GET ) ) {
	$result = $sfdb->get_results( "SELECT cat FROM sf_links GROUP BY cat ORDER BY cat" );
	foreach ( $result as $row ) {
		if ( ! empty( $row->cat ) ) 
			$categories[] = "<a href=\"index.php?cat=$row->cat\">$row->cat</a><br />\n";

	}

	echo '<ul class="columns">';
	foreach ( $categories as $v )
		echo "<li>$v</li>";

	echo "</ul>";
} else {
extract($_GET);
echo '<div class="container">';

// Get a list of subcategories
if (is_null($subcat2)) {$next = "subcat2";}
if (is_null($subcat1)) {$next = "subcat1";}
if (is_null($subcat))  {$next = "subcat";}
if (!$subcat2 and !$subcat1 and !$subcat) {
	$sql = "SELECT $next FROM sf_links WHERE lower(cat) = '".strtolower($cat)."' AND subcat IS NOT NULL ";
} else if ( empty($subcat2) and empty($subcat1) and isset($subcat) ) {
	$sql = "SELECT DISTINCT $next FROM sf_links WHERE lower(cat) = '".strtolower($cat)."' AND lower(subcat) = '".strtolower($subcat)."' AND subcat1 IS NOT NULL";
} else if (!$subcat2 and $subcat1 and $subcat) {
	$sql = "SELECT $next FROM sf_links WHERE lower(cat) = '".strtolower($cat)."' AND lower(subcat) = '".strtolower($subcat)."' AND lower(subcat1) = '".strtolower($subcat1)."' AND subcat2 IS NOT NULL ";
} else if (isset($subcat2)) {
	$sql = "select $next from sf_links where lower(cat) = '".strtolower($cat)."' AND lower(subcat) = '".strtolower($subcat)."' AND lower(subcat1) = '".strtolower($subcat1)."' AND lower(subcat2) = '".strtolower($subcat2)."'";
} 
$sql .= " GROUP BY $next ORDER BY $next";

if (isset($subcat2)) {skip;} else {
	//echo '<pre>'; echo $sql; echo '</pre>';
	$result = $sfdb->get_results( $sql ); //or die ( "No results found." );
	$num_rows = $sfdb->num_rows;
}

// Breadcrumb
echo "<p class=\"breadcrumb\"><a href=\"".HOME."\">Home</a>";

if ( isset( $cat ) ) { $b_cat = "<a href=\"".HOME."index.php?cat=$cat\">".ucwords($cat)."</a>"; }
if ( isset( $subcat ) ) {$b_subcat = "<a href=\"".HOME."index.php?cat=$cat&subcat=$subcat\">$subcat</a>";}
if ($subcat1) {$b_subcat1 = "<a href=\"".HOME."index.php?cat=$cat&subcat=$subcat&subcat1=$subcat1\">$subcat1</a>";}
if ($subcat2) {$b_subcat2 = "<a href=\"".HOME."index.php?cat=$cat&subcat=$subcat&subcat1=$subcat1&subcat2=$subcat2\">$subcat2</a>";}
if (!$subcat2 and !$subcat1 and !$subcat) {echo " > ".ucwords($cat);}
if (!$subcat2 and !$subcat1 and $subcat) {echo " > ".ucwords($b_cat)." > ".ucwords($subcat);}
if (is_null($subcat2) and $subcat1 and $subcat) {echo " > ".ucwords($b_cat)." > ".ucwords($b_subcat)." > ".ucwords($subcat1);}
if (isset($subcat2) and isset($subcat1) and isset($subcat)) {echo " > ".ucwords($b_cat)." > ".ucwords($b_subcat)." > ".ucwords($b_subcat1)." > ".ucwords($subcat2);}
parse_str($_SERVER['QUERY_STRING'],$qs);
if (is_null($qs[orderby])) {$qs[orderby]='datecreated';}
if ($qs[sort]!='ASC') {$qs[sort]='ASC';} else {$qs[sort]='DESC';}
foreach ($qs as $k=>$v) {$qstring[]="$k=$v";}
$qs = implode('&',$qstring);
echo "<a href=\"".$_SERVER['PHP_SELF']."?$qs\" alt=\"sort\"><span class=\"genericon genericon-sort\"></span></a> ";
echo "</p>";

if ( isset( $result) ) {
	foreach ( $result as $row ) {
		if ( $row->cat )  { $categories[] = "<a href=\"".HOME."home?cat=$cat/\">$row->cat</a><br>\n"; }
		if ($next=="subcat")  {if ($row->subcat)  {$categories[] = "<a href=\"".HOME."index.php?cat=$cat&subcat=$row->subcat\">$row->subcat</a><br>\n";}}
		if ($next=="subcat1") {if ($row->subcat1) {$categories[] = "<a href=\"".HOME."index.php?cat=$cat&subcat=$subcat&subcat1=$row->subcat1\">$row->subcat1</a><br>\n";}}
		if ($next=="subcat2") {if ($row->subcat2) {$categories[] = "<a href=\"".HOME."index.php?cat=$cat&subcat=$subcat&subcat1=$subcat1&subcat2=$row->subcat2\">$row->subcat2</a><br>\n";}}
	}
}

// Debugging values
$total = count($categories);
$cols = 6;
$rows_per_col_tmp = $num_rows / $cols;
$rows_per_col = floor($rows_per_col_tmp);
$remainder = fmod($total, $cols);
if ($remainder>0) {$rows_per_col+=1;} 

// Display next categories
if ( isset( $categories ) ) { ?>
	<table cellpadding="0" cellspacing="2" style="padding-bottom: 1em;">
	<tr>
	<?php 
	// echo "<tr><td colspan=4>cols = $cols, rows_per_col = $rows_per_col, total = $total, remainder = $remainder</td></tr>"; // for debugging:
	if ($rows_per_col>0) {
		for ($x=0;$x<$cols;$x++) {
				echo "<td valign=top style=\"padding-left: .5em; border-left: 1px solid #E5ECF9;\">";
				for ($i=$rows_per_col*$x;$i<$rows_per_col*($x+1);$i++) {echo "$categories[$i]";}
				// for ($i=$rows_per_col*$x;$i<$rows_per_col*($x+1);$i++) {echo "$i of $total - $categories[$i]";}
				if ($x<$cols-1) {echo "</td><td> </td>\n";}
		}
	} elseif (is_null($rows_per_col) and $remainder>0) {
		for ($j=$remainder;$j>0;$j--) {$tmp = $total - $j; echo "$categories[$tmp]";}
	}
		// for ($j=$remainder;$j>0;$j--) {$tmp = $total - $j; echo "$tmp of $total - $categories[$tmp]";}
 echo "</td>\n";
 echo "</tr>\n";
 echo "</table>\n";
 }

// Get links
$sql = "SELECT * FROM sf_links WHERE cat = '$cat' ";
if (is_null($subcat) and is_null($subcat1) and is_null($subcat2))  { $sql .= "AND subcat IS NULL"; }
if (isset($subcat) and is_null($subcat1) and is_null($subcat2))  { $sql .= "AND subcat = '$subcat' AND subcat1 IS NULL"; }
if (isset($subcat) and isset($subcat1) and empty($subcat2))  { $sql .= "AND subcat = '$subcat' AND subcat1 = '$subcat1' AND subcat2 IS NULL"; }
if (isset($subcat) and isset($subcat1) and isset($subcat2))  { $sql .= "AND subcat = '$subcat' AND subcat1 = '$subcat1' AND subcat2 = '$subcat2'"; }
if (is_null($orderby)) {$orderby='title';}
$sql .= " ORDER BY $orderby";
if (isset($sort)) {$sql .= " $sort";}
$result = $sfdb->get_results( $sql ); //or die ( "Couldn't execute the category query." );
$totalresults = $sfdb->num_rows; echo "<p>$totalresults result"; echo ($totalresults != 1 ? 's' : ''); echo '</p>';

echo '</div> <!-- container -->';

// Print links
if ( isset ( $result ) ) {
	date_default_timezone_set( 'UTC' );

	echo "<ul>\n";
	foreach ( $result as $row ) {
		echo '<li>';
		if ('lastmodified' == $orderby) {
			echo time_since( strtotime( $row->lastmodified ) );
		} elseif ('datecreated' == $orderby) {
			echo time_since( strtotime( $row->datecreated ) );
		}
		if ( sf_user_can_edit() ) {
			//echo "<a href=\"".HOME."sf-control/edit.php?id=$row->id\" class=\"delete\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> ";
			echo "<a href=\"".HOME."sf-control/edit.php?id=$row->id\" class=\"edit\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> ";
		}
		if ( !empty( $row->url ) ) { echo "<a href=\"$row->url\" class=\"item\">$row->title</a>"; } else { echo "$row->title"; }
		if ( !empty( $row->description ) ) { echo " - $row->description\n"; }
		echo "</li>\n";
	}
	echo "</ul>\n";
}
}

$sql = "SELECT name FROM sf_tag LIMIT 80";
$result = $sfdb->get_results( $sql ) or die ( "No tags found." );
$total = $sfdb->num_rows;

if ( $total > 0 ) {
	echo '<p>&nbsp;</p>';
	echo "<ul class=\"columns\">\n";
	foreach ( $result as $row ) {
		if ( !empty( $row->name ) )
			echo '<li><a href="' . HOME . 'tag/?tag=' . $row->name. '" class="item">' . $row->name . '</a></li>';
	
	}
	echo "</ul>\n";
} ?>

<footer>
	<a href="<?php echo HOME; ?>" accesskey="h">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	<!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
	as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
