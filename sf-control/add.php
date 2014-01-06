<?php
	require_once( '../sf-load.php' );
	sf_validate_auth_cookie();
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<title>Add</title>
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-extend/themes/stuff/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-includes/genericons.css">
	<script type="text/javascript" src="<?php echo HOME; ?>sf-extend/themes/stuff/script.js"></script>
</head>

<body class="add">
<div style="text-align:center; margin: 4em;"><h1 id="main-title"><a href="<?php echo HOME; ?>">stuff</a></h1></div>

<?php if ( is_null( $_POST['status'] ) ) { ?>
	<form name="add" class="add" action="<?php $PHP_SELF ?>" method="post">
		<fieldset>
			<legend>Add Stuff</legend>
			<div class="above-below15 above30 clear">
				<label for="text" class="placeholder active">Title</label>
				<input type="text" name="title" id="title" tabindex="1" class="av-text" value="">
			</div>
			<div class="above-below15">
				<label for="url" class="placeholder ">Link</label>
				<input type="text" name="url" id="url" tabindex="2" class="av-password" value="">
			</div>
			<div class="above-below15">
				<label for="description" class="placeholder">Description</label>
				<textarea name="description" id="description" tabindex="3"></textarea>
			</div>
			<div class="above-below15">
				<label for="tags" class="placeholder ">Tags</label>
				<input type="text" name="tags" id="tags" tabindex="4" class="av-password" value="">
			</div>
			<input type="submit" value="Add" class="button float-left no-transform" tabindex="5">
			<input name="status" type="hidden" value="update">
			<input name="qs" type="hidden" value="<?php echo getenv("QUERY_STRING"); ?>"> 
			<input name="datecreated" type="hidden" value="<?php echo date('Y-m-d H:i:s'); ?>">
		</fieldset>
	</form>

<?php } elseif ( $_POST['status'] == 'update' ) {
	$allowed_fields = array( 'title', 'url', 'description', 'datecreated');

	foreach ( $allowed_fields as $field )
		$set_values[] = "$field = '" . mysql_escape_string( $_POST["$field"] ) . "'";

	$set_values = join(', ', $set_values);

	// Add the link
	$sfdb->query( "INSERT INTO sf_links SET $set_values" );
	$link_id = $sfdb->insert_id;

	if ( $link_id > 0 ) {
		if ( isset( $_POST['tags'] ) )
			$tags = explode( ',', $_POST['tags'] );

		// Make sure each tag exists in the database
		foreach( $tags as $tag ) {
			$sfdb->query( "INSERT IGNORE INTO sf_tag SET name = '" . mysql_escape_string( trim( $tag ) ) . "';" );
			$tag_id = $sfdb->get_var( "SELECT tag_id FROM sf_tag WHERE name = '" . mysql_escape_string( trim( $tag ) ) . "';" );
			$tag_ids["$tag"] = $tag_id;
		}

		foreach ( $tag_ids as $k => $v )
			$tagmap_values[] = "($link_id, $v)";

		$tagmap_values_joined = join(', ', $tagmap_values);
		if ( !empty( $tagmap_values_joined ) )
			$results = $sfdb->query( "INSERT INTO sf_tagmap (link_id, tag_id) VALUES $tagmap_values_joined" );

		/*
			$sfdb->vardump( $results );
			echo '<pre>'; var_dump( $tag_ids ); echo '</pre>';
			echo '<pre>'; var_dump( $_POST ); echo '</pre>';
		*/

		// Check the db and print the updates
		$dbcheck = $sfdb->get_row( "SELECT * FROM sf_links WHERE id = " . $link_id );
	  $datecreated = strtotime( $dbcheck->datecreated );
	  $datemod = strftime( "%b %e", $datecreated );
	  $sincewhen  = time_since( $datecreated );
	  echo "<p style=\"text-align:center;\"><abbr class=\"date\" title=\"$sincewhen\">$datemod</abbr> " . 
			"<!--<a href=\"sf-control/edit.php?id=$dbcheck->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .
			"<a href=\"sf-control/edit.php?id=$dbcheck->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> -->" .
			"<a href=\"$dbcheck->url\" title=\"$dbcheck->title\">$dbcheck->title</a> ";
	  if ( '' != $dbcheck->description ) { echo "&mdash; $dbcheck->description\n"; }

	  function get_tag_names( $list ) {
			global $sfdb;
			foreach ( $list as $i )
	  		$output[] = $sfdb->get_var( "SELECT name FROM sf_tag WHERE tag_id = $i" );

			return $output;
	  }

		if ( !empty( $tags_to_add ) )
			echo '<br />Tags: ' . implode( ', ', array_values( get_tag_names( $tags_to_add ) ) );

		echo '</p>'; ?>

		<p class="center"><a href="<?php echo $dbcheck->url; ?>">continue</a></p>
	<?php }

} else { echo "<p>Error</p>"; } ?>

<footer>
	<a href="/">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	 &bull; <a href="<?php echo HOME; ?>sf-control/add.php" accesskey="a">add</a>
	 &bull; <a href="<?php echo HOME; ?>sf-control/edit.php" accesskey="e">edit</a>
	<!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
	as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
