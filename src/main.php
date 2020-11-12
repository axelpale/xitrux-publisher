<?php
	include("header1.php");
	
	// Mainpage load counter, etusivun latausten laskuri
	if( ENABLE_MAINPAGE_COUNTER ) {
		addMainload(1,$con);
	}
	
	include("header2.php");
?>

<h1>Uusin sähellys</h1>

<?php

	// Listataan uusin, joka sisältää indeksikuvan
	$sql = "SELECT fold_id, fold_name, pids, fold_issued FROM korg_folds WHERE fold_hidden=0 ORDER BY fold_issued DESC";
	$result = mysql_query($sql, $con);
	$fold_array[0] = mysql_fetch_array($result);
	if(strlen($fold_array[0]['pids']) < 1) {
		$fold_array[0] = mysql_fetch_array($result);
		if(strlen($fold_array[0]['pids']) < 1) {
			$fold_array[0] = mysql_fetch_array($result);
		}
	}
	//$fold_array[2] = mysql_fetch_array($result);

	printSeparator();

	foreach($fold_array as $row) {
		echo "<div class='picbrowser'>\n";
		//echo "<div class='tags'>Luokittelu:".printFolderTags($row['fold_id'], $con)."</div>\n";
		$pic_array = getPictureData( getIndexImagePid($row['fold_id'],$con) , $con);

		echo "<div class='heading'><div class='leftside'>\n";
		echo "<h1>[<a href='picbrowser.php?fid=".$row['fold_id']."'>";
		echo $row['fold_name'];
		echo "</a>]</h1>\n</div>\n";
		echo "<div class='rightside'>\n";
		echo "<div class='issued'>Julkaistu ".getCleanDate($row['fold_issued']);
		echo "</div></div>\n";
		echo "<div class='stopfloat'></div>\n";
		echo "</div>\n";

		echo "<div class='picture'>\n";
		echo "<a href='picbrowser.php?fid=".$row['fold_id']."'>\n";
		echo "<img src='".$pic_array['pic_src']."' alt='".$row['fold_name']."' />\n";
		//echo "<h2>".substr($row['fold_created'],8,2).".".substr($row['fold_created'],5,2).". - ".$row['fold_name']." (luokittelu:";
		//echo printFolderTags($row['fold_id'], $con);
		//echo ")</h2>\n";
		echo "</a></div>\n";
		echo "<div class='caption'>\n";
		echo nl2br($pic_array['pic_caption'])."\n";
		echo "</div></div>\n";
	}
	
	echo "<div class='linkrow right bottom'>\n";
	echo "[<a href='foldbrowser.php'>Lisää korggauksia...</a>]\n";
	echo "</div>\n";
	
?>

<?php include("footer.php"); ?>
