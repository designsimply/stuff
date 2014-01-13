<?php
  require_once( 'sf-load.php' );
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

  <form name="add" class="add" action="<?php echo HOME; ?>sf-control/add.php" method="post">
    <fieldset>
      <legend>Add Stuff</legend>
      <div class="above-below15 above30 clear">
        <label for="text" class="placeholder active">Title</label>
        <input type="text" name="title" id="title" tabindex="1" class="av-text" value="<?php echo $_GET['title']; ?>">
      </div>
      <div class="above-below15">
        <label for="url" class="placeholder ">Link</label>
        <input type="text" name="url" id="url" tabindex="2" class="av-password" value="<?php echo $_GET['url']; ?>">
      </div>
      <div class="above-below15">
        <label for="description" class="placeholder">Description</label>
        <textarea name="description" id="description" tabindex="3"><?php echo $_GET['desc']; ?></textarea>
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
<!--
  <div class="indent">
  <form name="add" action="sf-control/add.php" method="post">
  <input name="url" type="text" value="<?php echo urldecode(utf8_decode($url)); ?>" size="100" /> url<br />
  <input name="title" type="text" value="<?php echo urldecode( utf8_decode( str_ireplace( 'u203A', '9B', str_ireplace( 'u2013', '96', $title ) ) ) ); ?>" size="100" maxlength="100" /> title<br />
  <textarea name="description" cols="75" rows="4" wrap="virtual" id="description" dir="ltr"><?php if ($desc) {echo urldecode(utf8_decode($desc));} ?></textarea><br />
  <input name="tags" type="text" size="100" /> tags<br />
  <input name="cat" type="text" size="20" maxlength="50" value="<?php if (isset($cat)) {echo $cat;} else {echo 'Uncategorized';} ?>" /> &gt; 
  <input name="subcat" type="text" size="20" maxlength="50" value="<?php echo $subcat; ?>" /> &gt; 
  <input name="subcat1" type="text" size="20" maxlength="50" value="<?php echo $subcat1; ?>" /> &gt; 
  <input name="subcat2" type="text" size="20" maxlength="50" value="<?php echo $subcat2; ?>" /> category<br />
  <input name="status" type="hidden" value="update">
  <input name="qs" type="hidden" value="<?php echo getenv("QUERY_STRING"); ?>"> 
  <input name="datecreated" type="hidden" value="<?php echo date('Y-m-d H:i:s'); ?>">
  <input type="submit" name="submit" value="Add URL" />
  </form>
  </div>
-->

<?php
  extract($_GET);
 if (is_null($orderby)) { $orderby = "cat,subcat,subcat1,subcat2,title,description"; }
 $q = stripslashes( $url );
 if ( substr( $q, 0, 1 ) == '/' ) { $q = substr( $q, 1 ); }
 $the_sql = "SELECT * FROM sf_links WHERE 
        title LIKE '%$q%' 
        OR description LIKE '%$q%' 
        OR cat LIKE '%$q%'
        OR subcat LIKE '%$q%'
        OR subcat1 LIKE '%$q%'
        OR subcat2 LIKE '%$q%'
 ";
 $the_sql = "SELECT * FROM sf_links WHERE 
        url ='$q' 
 ";
 $the_sql .= " ORDER BY $orderby LIMIT 25";
 $results = $sfdb->get_results( $the_sql );
 $num_rows = $sfdb->num_rows;
?>

<?php
 // disply search results
if ( $num_rows > 0 ) {
echo "<blockquote>Results found: $num_rows</blockquote>";
$search_result = "<ul>\n";
foreach ( $results as $row ) {
  $lastmod = strtotime( $row->lastmodified );
  $datemod = strftime( "%b %e", $lastmod );
  $sincewhen  = time_since( $lastmod );
  $i = "<abbr class=\"date\" title=\"$datemod\">$sincewhen</abbr> " . 
    "<a href=\"sf-control/edit.php?id=$row->id\" alt=\"delete\"><span class=\"genericon genericon-close\"></span></a> " .
    "<a href=\"sf-control/edit.php?id=$row->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> " .
    "<a href=\"$row->url\" title=\"$row->title\">$row->title</a> ";
    if ( '' != $row->description ) { $i .= "&mdash; $row->description\n";
  }
  $items[] = $i;
  //foreach ( $items as $item )
  //  echo "<li>$item</li>";

    extract( get_object_vars( $row ), EXTR_PREFIX_ALL, 's' );
    // display search results
    if ('' != $qurl && $url==$qurl) { $search_result .= '<li class="exactmatch" style="background-color:#ffff99;">'; } else { $search_result .= '<li>'; }
    $search_result .= "<abbr class=\"date\" title=\"$datemod\">$sincewhen</abbr> <a href=\"sf-control/edit.php?id=$row->id\" alt=\"edit\"><span class=\"genericon genericon-edit\"></span></a> ";
    if ($s_cat) {$stags[] = strtolower( $s_cat ); $search_result .= "<a href=\"/d/home/".strtolower($s_cat)."/\">$s_cat</a>";}
    if ($s_subcat) {$stags[] = strtolower( $s_subcat ); $search_result .= " &rarr; <a href=\"/d/home/".strtolower($s_cat)."/".strtolower($s_subcat)."/\">$s_subcat</a>";}
    if ($s_subcat1) {$stags[] = strtolower( $s_subcats ); $search_result .= " &rarr; <a href=\"home/".strtolower($s_cat)."/".strtolower($s_subcat)."/".strtolower($s_subcat1)."/\">$s_subcat1</a>";}
    if ($s_subcat2) {$stags[] = strtolower( $s_subcats ); $search_result .= " &rarr; <a href=\"home/".strtolower($s_cat)."/".strtolower($s_subcat)."/".strtolower($s_subcat1)."/".strtolower($s_subcat2)."/\">$s_subcat2</a>";}
    $search_result .= ' &rarr; ';
    if ($s_url) {$search_result .= "<a href=\"$s_url\">$s_title</a>";} else {$search_result .= "$s_title";}
    if ($s_desc) {$search_result .= "$s_desc\n";}

    if ( isset( $stags ) ) {
      $suggested_tags = implode( ', ', $stags );
      $search_result .= " Suggested tags: <a name=\"suggested\" onClick=\"document.getElementById('tags').value='$suggested_tags'\">$suggested_tags</a>";
    }

    $search_result .= "</li>\n";
 }
 $search_result .= "</ul>\n";
 echo $search_result; 
}
 else {echo "<p class='center'>No results for \"$q.\"</p>";}
?>

<footer>
  <a href="<?php echo HOME; ?>" accesskey="h">home</a>
   &bull; <a href="<?php echo HOME; ?>latest.php" accesskey="l">latest</a>
  <!-- <?php sf_get_number_rows(); ?> last modified <?php sf_get_last_modified(); ?> 
  as of <?php echo date("Y-m-d"); ?> at <?php echo date("G:i:s T"); ?> -->
</footer>
</body>
</html>
