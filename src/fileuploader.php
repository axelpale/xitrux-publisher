<?php 
	include("admin-functions.php");
	include("fileupload.class.php");
	include("header1.php");
	include("header2.php");
?>

<?php
if($LOGGED) {

	// Tarkistetaan GET['fid'] tietoturvan vuoksi
	$fid = sanitizeId($_GET['fid']);

	// Tarkistetaan GET['pid'] tietoturvan vuoksi
	$pid = sanitizeId($_GET['pid']);

	// Haetaan kansionimi
	$foldername = getFolderName($fid,$con);

	// Linkki takaisin kansionäkymään
	echo "<div class='linkrow top'>\n";
	echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
	echo "[<a href='picadmin.php?fid=".$fid."&pid=".$pid."'>Takaisin kuvaan</a>] \n";
	echo "</div>\n";

	//Otsikko
	echo "<h1>Lisämateriaalin lisääminen</h1>\n";
	printSeparator();

	// Uusi uppaaminen
	$fileupload = new FileUpload;

	// Asetetaan uppaushakemisto ja loggaustiedosto
	$fileupload->setUploadDir(UPLOAD_DIRECTORY."fid".$fid."/");
	$fileupload->setLogfile(UPLOAD_DIRECTORY."uploadlog.txt");
	
	// Virheilmoitus jos kuvien uppaus ei onnistu
	$uploaderror = "Tiedostoa ei ole määritetty tai se on liian suuri (max ";
	$uploaderror .= $fileupload->getMaxSize()." tavua). Yritä <a href='fileupload.php?fid=";
	$uploaderror .= $fid."&pid=".$pid."'>uudelleen</a>.";

	// Tallennetaan tiedosto
	$fileupload->save($_FILES) or die($uploaderror);

	// Tulostetaan uppaustiedot
	echo $fileupload->printUploadInfo();

	printSeparator();

	// Testataan voidaanko tiedostoa löytää/lukea
	if(is_readable($fileupload->getUploaded())) {
		echo "<h2>Siirto on onnistunut</h2>\n";
		echo "[<a href='".$fileupload->getUploaded()."' target='_blank'>Avaa tiedosto</a>]\n";		

		// Päivitetään käyttäjän tieto 'viimeisin lataus',
		// joka näkyy kun uutta kuvaa upataan
		$_SESSION['lastupload'] = $fileupload->getOriginal();

		// Lisätään lisämateriaalitieto kuvaan
		if(!updatePictureLink($pid, $fileupload->getUploaded(), $con))
			echo "Virhe! Tietoa uppauksesta ei voitu päivittää tietokantaan. Error: ".mysql_error()."<br/>\n";

	} else echo "<h2>Siirto epäonnistui, tiedostoa ei voida lukea</h2>\n";

	//Linkit takaisin
	echo "<div class='linkrow bottom'>\n";
	echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>] \n";
	echo "[<a href='picadmin.php?fid=".$fid."&pid=".$pid."'>Takaisin kuvaan</a>] \n";
	echo "</div>\n";

} else {
	include("unauthorized.php");		
}

?>

<?php include("footer.php"); ?>
