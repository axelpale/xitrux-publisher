<?php
	// Lisätään tarvittavat funkkarit
	include("public-functions.php");

	//Avataan yhteys
	$con = korg_connect();

	//GET: tag fid
	// Muutetaan muuttujanimet. Nimet a-loppuisia, jotta eivät
	// sekottuisi muiden samanimisten muuttujien kanssa (ei välttämättä mahdollista)
	// Tarkistetaan GET['fid'] tietoturvan vuoksi
	$fida = sanitizeId($_GET['fid']);

	// Tarkistetaan GET['tag'] tietoturvan vuoksi
	$taga = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);

	//Tarkastetaan onko lisätty tagi jo tietokannassa
	$sql = "SELECT tag FROM korg_tags WHERE tag=";
	$sql .= '"'.$taga.'"';
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) { // jos tagia ei löydy niin lisätään sellainen
		$sql = "INSERT INTO korg_tags(tag) VALUES('".$taga."')";
		if(!mysql_query($sql, $con)) echo "Luokan lisääminen epäonnistui! \n";
	}
		
	// Tarkastetaan onko lisättävä tagi jo liitetty kansioon
	$sql = "SELECT tag FROM korg_tags_folds WHERE tag=";
	$sql .= '"'.$taga.'" AND fold_id='.$fida;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) { // jos tagia ei ole liitetty kansioon niin lisätään liitos
		//Lisätään kansioon fid luokka tag
		$sql = "INSERT INTO korg_tags_folds(tag,fold_id) VALUES('".$taga."',".$fida.")";
		mysql_query($sql);
	}

	//Tulostetaan luokittelu poistolinkkien kanssa
	$sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fida;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) echo " ei luokittelua";
	else {
		while($tag = mysql_fetch_array($result)) {
			echo " ".$tag['tag']."<a class='del' onclick='eraseTag(";
			echo '"'.$tag['tag'].'",'.$fida;
			echo ")' style='font-size: small'>[poista]</a>";
		}
	}

	//Suljetaan yhteys
	mysql_close($con);

?>
