<?php
	include("header1.php");
	include("admin-functions.php");

/*// Seuraavan kuvan id-numero
function getNextPicInFold($pid, $fid) {

	$pids_string = getFolderPids($fold_id,$connection);
	$pids_array = pidsStringToArray($pids_string);
	$pos = array_search($pic_id,$pids_array);
	
	//seuraavan kuvan sijainti
	$new_index = $pos + 1;

	//palautetaan
	return $pids_array[ $new_index ];
}*/

?>

<script type="text/javascript">
	
	// Kuvan alapuolelle ilmestyvien lisätietojen näyttäminen
	function showHiddenInfo() {
		document.getElementById("hiddeninfo").style.display="block";
		document.getElementById("showhideinfo").innerHTML="[<a onclick='hideHiddenInfo()'>piilota lisätiedot</a>]";
	}
	// Kuvan alapuolelle ilmestyvien lisätietojen piilottaminen
	function hideHiddenInfo() {
		document.getElementById("hiddeninfo").style.display="none";
		document.getElementById("showhideinfo").innerHTML="[<a onclick='showHiddenInfo()'>näytä lisätiedot</a>]";
	}

</script>

<!-- Lisätyökalujen piilottamiseen -->
<script src='hiddentools.js'></script>

<?php include("header2.php"); ?>

<?php
if($LOGGED) {

	// Tarkistetaan GET tietoturvan vuoksi
	$fid = sanitizeId($_GET['fid']);

	// Tarkistetaan GET['pid'] tietoturvan vuoksi
	$pid = sanitizeId($_GET['pid']);

	// Kuvan piilottaminen
	if(isset($_GET['hide'])) {
		$hide = sanitizeId($_GET['hide']);
		if($hide >= 0) {
			if(!hideImage($hide, $con)) echo "Kuvan piilottaminen epäonnistui.<br/>\n";
		}

		// Tyhjennetään hide-attribuutti, jotta päivitettäessä kuvaa ei turhaan piilotettaisi uudestaan
		setGets("?fid=".$fid);
	} else

	// Kuvan paljastaminen
	if(isset($_GET['unhide'])) {
		$unhide = sanitizeId($_GET['unhide']);
		if($unhide >= 0) {
			if(!unhideImage($unhide, $con)) echo "Kuvan paljastaminen epäonnistui.<br/>\n";
		}

		// Tyhjennetään unhide-attribuutti, jotta päivitettäessä kuvaa ei turhaan paljastettaisi uudestaan
		setGets("?fid=".$fid);
	} else

	// Kuvan nimen ja kuvatekstin päivittäminen
	if(isset($_GET['picupdate'])) {
		$picupdate = sanitizeId($_GET['picupdate']);
		if($picupdate >= 0) {
			if(!updatePictureName($picupdate, $_POST['newpicname'], $con)) echo "Kuvan nimen vaihtaminen epäonnistui.<br/>\n";
			if(!updatePictureCaption($picupdate, $_POST['newcaption'], $con)) echo "Kuvatekstin vaihtaminen epäonnistui.<br/>\n";
		}

		// Tyhjennetään picupdate-attribuutti, jotta päivitettäessä kuvatietoja ei turhaan päivitetä uudestaan
		setGets("?fid=".$fid);
	} else

	// Jos fid-arvoa ei ole määritetty, fid = 0, jotta sivu näyttäisi jotain järkevää vaikka arvoa ei anneta.
	if(!isset($fid)) $fid = "0";

	// Haetaan kansion nimi
	$foldername = getFolderName($fid,$con);

	// Haetaan kuvan tiedot
	$picdata = getPictureData($pid,$con);

	// Linkit taaksepäin
	echo "<div class='linkrow top'>\n";
	echo "[<a href='mainadmin.php'>Takaisin kansioihin</a>]\n ";
	echo "[<a href='foldadmin.php?fid=".$fid."&pid=".$pid."'>".$foldername."</a>]\n";
	echo "</div>\n\n";

	// Kuvan nimi
	//echo "<span style='font-size: 10px'>Kuvan nimi:</span>\n";
	echo "<h1>";
	if($picdata['pic_name'] != "") echo $picdata['pic_name'];
	else echo basename($picdata['pic_src']);
	if($picdata['pic_hidden'] == "1") echo " (piilotettu)";
	echo "</h1>\n\n";

	// Kuvatyökalut ovat tässä yleisen siisteyden johdosta
	echo "<div class='linkrow'>\n";
	echo "[<a href='picedit.php?fid=".$fid."&pid=".$picdata['pic_id']."'>muokkaa kuvan tietoja</a>] \n";
	echo "[<a href='delprompt.php?fid=".$fid."&type=pic&pid=".$picdata['pic_id']."'>poista kuva</a>] \n";
	if($picdata['pic_hidden'] == 1) {
		echo "[<a class='toollink' href='foldadmin.php?fid=".$fid."&unhide=".$picdata['pic_id']."'>paljasta kuva</a>] \n";
	} else {
		echo "[<a class='toollink' href='foldadmin.php?fid=".$fid."&hide=".$picdata['pic_id']."'>piilota kuva</a>] \n";
	}
	echo "[<a href='fileupload.php?fid=".$fid."&pid=".$pid."'>tuo lisämateriaalia</a>] \n";
	echo hiddentoolsStart();
	echo "[<a href='picchfile.php?fid=".$fid."&pid=".$picdata['pic_id']."'>vaihda kuvatiedosto</a>] \n";
	echo "[<a href='picchfold.php?fid=".$fid."&pid=".$picdata['pic_id']."'>siirrä toiseen kansioon</a>] \n";
	echo "<span id='showhideinfo'>\n";
	echo "[<a onclick='showHiddenInfo()'>näytä lisätiedot</a>]\n";
	echo "</span> \n\n";
	if($picdata['pic_thumb'] == "")
		echo "[<a href='picmakethumb.exe.php?fid=".$fid."&pid=".$picdata['pic_id']."'>tee pikkukuva</a>] \n";
	echo hiddentoolsEnd();
	echo "</div>\n\n";

	// Näytetään kuva ja kuvateksti
	echo "<div class='picbrowser'>\n";
	echo "<a href='";
	echo validateSrc($picdata['pic_orig'],$picdata['pic_src']);
	echo "' target='_blank'>\n";
	echo "<img src='".$picdata['pic_src']."' ";
	echo "style='border: 0px solid black;'/>\n";
	echo "</a>\n";
	// Kuvateksti
	echo "<div class='caption'>\n";
	if($picdata['pic_caption'] != "") {
		echo nl2br($picdata['pic_caption'])."\n<br/><br/>\n";
	}
	// Lisämateriaali
	if($picdata['pic_link'] != "") {
		echo "Kuvalle on annettu lisämateriaalia:<br/>\n";
		echo "<a href='".$picdata['pic_link']."' target='_blank'>".$picdata['pic_link']."</a>\n";
	}
	echo "</div>\n\n";
	echo "</div>\n\n";

	// Kuvat lisätiedot (oletuksena piilossa. Ilmestyvät Näytä lisätiedot -linkistä
	echo "<div id='hiddeninfo'>";
	echo "<div class='caption'>Kuvan tiedot:</div>\n";
	echo "<table>\n";
	echo "<tr><td>Kuvanumero</td>\n<td>".$picdata['pic_id']."</td></tr>\n";
	echo "<tr><td>Nimi</td>\n<td>".$picdata['pic_name']."</td></tr>\n";
	echo "<tr><td>Kuvateksti</td>\n<td>".nl2br($picdata['pic_caption'])."</td></tr>\n";
	echo "<tr><td>Tiedosto</td>\n<td>".$picdata['pic_src']."</td></tr>\n";
	echo "<tr><td>Pikkukuva</td>\n<td>".$picdata['pic_thumb']."</td></tr>\n";
	echo "<tr><td>Suurkuva</td>\n<td>".$picdata['pic_orig']."</td></tr>\n";
	echo "<tr><td>Lisämateriaali</td>\n<td>".$picdata['pic_link']."</td></tr>\n";
	echo "<tr><td>Kansionumero</td>\n<td>".$picdata['fold_id']."</td></tr>\n";
	echo "<tr><td>Näkyvyys</td>\n<td>";
	if($picdata['pic_hidden'] == "1") echo "Piilotettu";
	else echo "Julkinen";
	echo "</td></tr>\n";
	echo "</table>\n\n";
	echo "</div>";

	// Linkit taaksepäin
	echo "<div class='linkrow bottom'>\n";
	echo "[<a href='mainadmin.php'>Takaisin kansioihin</a>]\n ";
	echo "[<a href='foldadmin.php?fid=".$fid."&pid=".$pid."'>".$foldername."</a>]\n";
	echo "</div>\n\n";

} else {
	include("unauthorized.php");		
}
?>

<?php include("footer.php"); ?>
