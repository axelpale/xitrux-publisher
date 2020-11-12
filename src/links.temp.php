<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<h1>Linkkejä</h1>

<?php

	printSeparator();

	// Listataan kaikki kuvat kansiosta links
	$sql = "SELECT pic_id,pic_name,pic_src,pic_caption,pic_link ";
	$sql .= "FROM korg_pics WHERE fold_id=33 AND pic_hidden=0 ORDER BY pic_id DESC";
	$result = mysql_query($sql, $con);

	$rowcount = mysql_num_rows($result);
	if($rowcount != 0) {

		echo "<div class='itembrowser public'>\n\n";
		for($i=0; $i<$rowcount; $i++) {

			$item = mysql_fetch_array($result);
			echo "<div class='browseritem public'>\n";
			echo "<a class='thumb' href='".$item['pic_link']."' target='_blank'>";
			echo "<img src='";

			echo validateSrc($item['pic_thumb'],$item['pic_src']);
			echo "' alt='".$item['pic_name']."' />\n</a>\n";

			echo "<h2>[<a href='".$item['pic_link']."' target='_blank'>";
			echo $item['pic_name']."</a>]</h2>\n";

			//Säästetään neljä seuraavaa riviä siltä varalta jos halutaan näyttää linkkien lisäyspäivämäärä
			//echo "<div class='issued'>\n";
			//echo "Julkaistu ".getCleanDate($item['fold_issued'])." - ";
			//echo printFolderTags($item['fold_id'],$con);
			//echo "</div>\n";

			echo "<p>\n".nl2br($item['pic_caption']);
			echo "</p>\n</div>\n\n";

		}
		echo "</div>\n\n";

		echo "<br class='stopfloat'/>\n\n";
	}

	echo "<span>Yhteensä ".$rowcount." kohdetta.</span>\n";

	printSeparator();

?>

<?php include("footer.php"); ?>
