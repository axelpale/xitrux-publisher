<?php
	include("header1.php");

	if($LOGGED) {
		echo "<script src='foldertagedit.js'></script>\n";
	}

	include("header2.php");
?>

<?php
if($LOGGED) {

	// Tarkistetaan GET['fid'] tietoturvan vuoksi
	$fid = sanitizeId($_GET['fid']);

	//Haetaan kansion nimi
	$foldername = getFolderName($fid,$con);

	// Linkki takaisin kansionäkymään
	echo "<div class='linkrow top'>\n";
	echo "[<a href='foldadmin.php?fid=".$fid."'>\n";
	echo "Takaisin kansioon ".$foldername."</a>]\n";
	echo "</div>\n";

	//Otsikko
	echo "<h1>Kansion '".$foldername."' luokittelun muuttaminen</h1>\n";

	//Luokittelutiedot
	echo "<h2 class='tags'>Nykyinen luokittelu:<span id='foldertags'>\n";

	//Haetaan kansion luokittelutiedot
	$sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fid;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) echo " ei luokittelua";
	else {
		while($tag = mysql_fetch_array($result)) {
			echo " ".$tag['tag']."<a onclick='eraseTag(";
			echo '"'.$tag['tag'].'",'.$fid;
			echo ")'>[poista]</a>";
		}
	}

	echo "</span></h2>\n";

	printSeparator();

	//Uuden luokan lisäyslomake
	echo "<div style='margin-top: 0.8em'>\n";
	echo "Lisää luokka:<br/>\n";
	echo "<input type='text' id='add_field' name='add_field' maxlenght=64 />\n";

	// Lisää luokka tietokantaan
	// Lisää tehty luokka listaan kuten ennenkin

	echo "<input type='button' onclick='addTag(getElementById("; echo '"add_field"'; echo ").value, ".$fid.")' value='Lisää' />\n";
	echo "</div>\n";

	printSeparator();

	//Luokan lisäyslomake
	echo "<div style='margin-top: 0.8em'>\n";
	echo "Lisää valmis luokka:<br/>\n";
	echo "<select id='add_list' name='add_list'>\n";

	//Haetaan kaikki tietokannan luokat
	$sql = "SELECT tag FROM korg_tags";
	$result = mysql_query($sql, $con);
	while($tag = mysql_fetch_array($result)) {
		echo "<option value='".$tag['tag']."'>".$tag['tag']."</option>\n";
	}
	echo "</select>\n";
	echo "<input type='button' onclick='addTag(getElementById("; echo '"add_list"'; echo ").value, ".$fid.")' value='Lisää' />\n";
	echo "</div>\n";

	/*
	//Luokanpoistolomake
	echo "<form action='' style='margin-top: 0.8em'>\n";
	echo "Poista luokka:\n";
	echo "<select id='remove_list' name='remove_list'>\n";

	//Haetaan kansion tämänhetkiset luokat
	$sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fid;
	$result = mysql_query($sql, $con);
	while($tag = mysql_fetch_array($result)) {
		echo "<option value='".$tag['tag']."'>".$tag['tag']."</option>\n";
	}

	echo "</select>\n";
	echo "<input type='button' onclick='' value='Poista' />\n";
	echo "</form>\n";
	*/

	// Linkki takaisin kansionäkymään
	echo "<div class='linkrow bottom'>\n";
	echo "[<a href='foldadmin.php?fid=".$fid."'>\n";
	echo "Takaisin kansioon ".$foldername."</a>]\n";
	echo "</div>\n";

} else {
	include("unauthorized.php");		
}
?>

<?php include("footer.php"); ?>
