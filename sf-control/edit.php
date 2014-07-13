<?php
	require_once( '../sf-load.php' );
	sf_validate_auth_cookie();
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
	<title>Edit</title>
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-extend/themes/stuff/style.css" type="text/css" media="all">
	<link rel="stylesheet" href="<?php echo HOME; ?>sf-includes/genericons.css">
	<script type="text/javascript" src="<?php echo HOME; ?>sf-extend/themes/stuff/script.js"></script>
	<script type="text/javascript">
	function start() {
		var ref = document.getElementById('referrer');
		ref.value = document.referrer;
	}
	onload = start;
	</script>
</head>

<body>
<div style="text-align:center; margin: 1em;"><h1 id="main-title"><a href="<?php echo HOME; ?>">stuff</a></h1></div>

<?php if ( is_null( $_POST['status'] ) && $_GET['id'] > 0 ) { 
	$link_id = $_GET['id'];

	$link = $sfdb->get_row( "SELECT * FROM sf_links WHERE id = $link_id LIMIT 1" );
	if ( isset( $link->cat ) ) { $suggested_tags_array[] = strtolower( $link->cat ); }
	if ( isset( $link->subcat ) ) { $suggested_tags_array[] = strtolower( $link->subcat ); }
	if ( isset( $link->subcat1 ) ) { $suggested_tags_array[] = strtolower( $link->subcat1 ); }
	if ( isset( $link->subcat2 ) ) { $suggested_tags_array[] = strtolower( $link->subcat2 ); }
	$suggested_tags = implode( ', ', $suggested_tags_array );
	//$db_tags = $sfdb->get_results( "SELECT t.name FROM sf_links l, sf_tag t, sf_tagmap tm WHERE tm.link_id = " . $link->id . " GROUP BY t.name" );
	$db_tags = $sfdb->get_col( "SELECT DISTINCT t.name FROM sf_links l, sf_tag t, sf_tagmap tm WHERE tm.tag_id = t.tag_id AND tm.link_id = " . $link->id );
	foreach ( $db_tags as $tag )
		$tags[] = $tag;

	if ( isset( $tags ) )
		$comma_separated_tags = implode(", ", $tags); ?>

	<form name="edit" class="edit" action="<?php $PHP_SELF ?>" method="post">
		<fieldset>
			<legend>Edit Form</legend>
			<div class="above-below15 above30 clear">
				<label for="text" class="placeholder active">Title</label>
				<input type="text" name="title" id="title" tabindex="1" class="av-text" value="<?php echo $link->title; ?>">
			</div>
			<div class="above-below15">
				<label for="url" class="placeholder ">Link</label>
				<input type="text" name="url" id="url" tabindex="2" class="av-password" value="<?php echo $link->url; ?>">
			</div>
			<div class="above-below15">
				<label for="description" class="placeholder">Description</label>
				<textarea name="description" id="description" tabindex="3"><?php echo $link->description; ?></textarea>
			</div>
			<div class="above-below15 tags">
				<label for="tags" class="placeholder ">Tags</label>
				<input type="text" name="tags" id="tags" tabindex="4" class="av-password" value="<?php echo $comma_separated_tags; ?>">
			</div>
			<div class="above-below15 tags">
				<label for="tags" class="placeholder ">Suggested tags</label>
				<input type="text" name="suggested-tags" id="suggested-tags" value="<?php echo $suggested_tags; ?>" onClick="document.getElementById('tags').value='<?php echo $suggested_tags; ?>'">
			</div>
			<input type="hidden" name="id" value="<?php echo $link->id ?>" />
			<input type="hidden" name="status" value="update">
			<input type="hidden" name="referrer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
			<input type="submit" value="Update" class="button float-left no-transform" tabindex="5">
		</fieldset>
	</form>
	<form name="delete_<?php echo $link->id; ?>" class="delete" action="<?php echo $_SERVER[PHP_SELF]."?".$_SERVER['QUERY_STRING']; ?>" method="post">
		<input type="hidden" name="status" value="delete" />
		<input type="hidden" name="id" value="<?php echo $link->id ?>" />
		<input type="hidden" name="referrer" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
		<input type="submit" value="Delete" onClick="return confirm('Are you sure?')" />
	</form>

<?php } elseif ( $_POST['status'] == "delete" && $_POST[id] > 0 ) {
	$link_id = $_POST['id'];

	//$sql = "START TRANSACTION; SELECT * FROM sf_links WHERE id = $link_id; SELECT * FROM sf_tagmap WHERE link_id = $link_id; COMMIT;";

	$sfdb->query( "START TRANSACTION;" );
	$sfdb->query( "DELETE FROM sf_links WHERE id = $link_id;" );
	$sfdb->query( "DELETE FROM sf_tagmap WHERE link_id = $link_id;" );
	$sfdb->query( "COMMIT;" );
	?>

	<p class="center">Record <?php echo $link_id; ?> has been deleted.</p>
	<p class="center"><a href="<?php echo $_POST['referrer']; ?>">continue</a></p>

<?php } elseif ( $_POST[status] == "update" && $_POST['id'] > 0 ) {
		$link_id = $_POST['id'];
		$allowed_fields = array( 'title', 'url', 'description', 'datecreated');

		foreach ( $allowed_fields as $field ) {
			if ( isset( $_POST["$field"] ) )
				$set_values[] = "$field = '" . mysql_escape_string( $_POST[$field] ) . "'";
		}

		$set_values = implode(', ', $set_values);

		$sfdb->query( "UPDATE sf_links SET $set_values WHERE id = " . mysql_escape_string( $link_id ) );

		if ( !empty( $_POST['tags'] ) ) {
			$tags = explode( ', ', $_POST['tags'] );

			// Add in all the tags from the form
			foreach( $tags as $tag ) {
				if ( ! empty( $tag ) ) {
					$sfdb->query( "INSERT IGNORE INTO sf_tag SET name = '" . mysql_escape_string( trim( $tag ) ) . "';" );
					$tag_id = $sfdb->get_var( "SELECT tag_id FROM sf_tag WHERE name = '" . mysql_escape_string( trim( $tag ) ) . "';" );
					$form_tag_ids[] = $tag_id;
				}
			}
		}

		// Get as list of tags already in the tagmap table
		$db_tag_ids = $sfdb->get_col( "SELECT tag_id FROM sf_tagmap WHERE link_id = " . mysql_escape_string( $link_id ) );

		// Get tags from the form that aren't in the tagmap then add them
		if ( is_array( $form_tag_ids ) ) {
			$tags_to_add = array_diff_assoc( $form_tag_ids, $db_tag_ids );
			foreach ( $tags_to_add as $tag_id )
				$sfdb->query( "INSERT INTO sf_tagmap SET tag_id = $tag_id, link_id = $link_id" );

			$tags_to_delete = array_diff_assoc( $db_tag_ids, $form_tag_ids );
			foreach ( $tags_to_delete as $tag_id )
				$sfdb->query( "DELETE FROM sf_tagmap WHERE tag_id = $tag_id AND link_id = $link_id" );
		}

		// Check the db and print the updates
		$dbcheck = $sfdb->get_row( "SELECT * FROM sf_links WHERE id = " . $link_id );
	  $lastmod = strtotime( $dbcheck->lastmodified );
	  $datemod = strftime( "%b %e", $lastmod );
	  $sincewhen  = time_since( $lastmod );
	  echo "<p style=\"text-align:center;\"><abbr class=\"date\" title=\"$sincewhen\">$datemod</abbr> " . 
			"<!--<a href=\"sf-control/edit.php?id=$dbcheck->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .
			"<a href=\"sf-control/edit.php?id=$dbcheck->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> -->" .
			"<a href=\"$row->url\" title=\"$dbcheck->title\">$dbcheck->title</a> ";
	  if ( '' != $dbcheck->description ) { echo "&mdash; $dbcheck->description\n"; }

	  function get_tag_names( $list ) {
			global $sfdb;
			foreach ( $list as $i )
	  		$output[] = $sfdb->get_var( "SELECT name FROM sf_tag WHERE tag_id = $i" );

			return $output;
	  }

		if ( !empty( $tags_to_add ) )
			echo '<br />Tags added: ' . implode( ', ', array_values( get_tag_names( $tags_to_add ) ) );

		if ( !empty( $tags_to_delete ) )
			echo '<br />Tags removed: ' . implode( ', ', array_values( get_tag_names( $tags_to_delete ) ) );

		echo '</p>';
		?>

		<p class="center"><a href="<?php if ( false === strpos( $_POST['referrer'], 'post.php' ) ) { 
			echo $_POST['referrer'];
		} else {
			echo 'javascript:history.go(-3)'; 
		} ?>">go back</a></p>

<?php } ?>

<footer>
	<a href="<?php echo HOME; ?>" accesskey="h">home</a>
	 &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
	 &bull; <a href="<?php echo HOME; ?>sf-control/add.php" accesskey="a">add</a>
	 &bull; <a href="<?php echo HOME; ?>sf-control/edit.php" accesskey="e">edit</a>
	 &bull; <a href="javascript:<?php echo sf_the_bookmarklet(); ?>" accesskey="s">stuff</a>
	 &bull; <a href="<?php echo HOME; ?>sf-control/bookmarklet.php" accesskey="b">bookmarklet</a>
</footer>
</body>
</html>
