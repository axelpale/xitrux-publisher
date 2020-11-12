<?php

##########################################################
# Sivuston julkisissa osiossa tarvittavia php-funktioita #
##########################################################
##########################################################

// Jos kuvatiedostoa ei löydy, näytetään virhekuva
define("PIC_NOT_FOUND", "images/missing.png");

// Jos kansio on tyhjä, näytetään indeksikuvana tämä
define("EMPTY_FOLDER_SRC", "images/empty.gif");

// Tulosteetaan erotin
function printSeparator() {
	echo "<div class='erottaja'>&nbsp;</div>\n";
}

// Yhdistetään MySQL-tietokantaan
function korg_connect() {
	// MySQL-yhteys
	$con = @mysql_connect("localhost", DB_USER, DB_PASS);
	if (!$con) { die('Could not connect: '.mysql_error()); }
	mysql_select_db(DB_NAME, $con);
	mysql_query("SET NAMES 'utf8'"); // Yhteys UTF8

	return $con;
}

// Hiddentoolsien alkutagit
function hiddentoolsStart() {
	echo "<div id='hiddentools' class='hiddentools'>\n";
}

// Hiddentoolsien lopputagit
function hiddentoolsEnd() {
	echo "</div>\n";
	echo "<div id='showhide' class='showhide'>\n";
	echo "<img id='showhidebutton' src='images/more00.gif' onclick='showHiddentools()' />\n";
	echo "<img src='images/less00.gif' style='display: none' />"; // less00.gif esilataus
	echo "</div>\n";
}

// Haetaan kansion nimi
function getFolderName($fold_id, $con) {
	$sql = "SELECT fold_name FROM korg_folds WHERE fold_id=".$fold_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return "&#60;unknown&#62;";

	$item = mysql_fetch_array($result);
	return $item['fold_name'];
}

// Haetaan kansion sisältämä kuvatieto stringinä
function getFolderPids($fold_id, $con) {
	$sql = "SELECT pids FROM korg_folds WHERE fold_id=".$fold_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return "&#60;unknown&#62;";

	$item = mysql_fetch_array($result);
	return $item['pids'];
}

// Tulostaa annetun kansion luokittelut välilyöntierotteisena
function printFolderTags($fold_id, $con) {
	$tags = "";
	$sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fold_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) $tags .= " ei luokittelua";
	else {
		while($tag = mysql_fetch_array($result)) {
			$tags .= " ".$tag['tag'];
		}
	}

	return $tags;
}

// Haetaan kuvan kaikki tiedot assosiatiiviseen jonoon
function getPictureData($pic_id, $con) {
	$sql = "SELECT * FROM korg_pics WHERE pic_id=".$pic_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return array();

	$picturearray = mysql_fetch_array($result);
	return $picturearray;
}

// Haetaan kuvatiedosto
function getImageSrc($pic_id, $con) {
	$sql = "SELECT pic_src FROM korg_pics WHERE pic_id=".$pic_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return "&#60;unknown&#62;";

	$srcarray = mysql_fetch_array($result);
	return $srcarray['pic_src'];
}

// Haetaan kuvan pikkukuva
function getImageThumb($pic_id, $con) {
	$sql = "SELECT pic_src, pic_thumb FROM korg_pics WHERE pic_id=".$pic_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return "&#60;unknown&#62;";

	$srcarray = mysql_fetch_array($result);
	return validateSrc($srcarray['pic_thumb'],$srcarray['pic_src']);
}

// Palauttaa sattumanvaraisen kuvan annetusta kansiosta
function getRandomImageSrc($fold_id, $connection) {

	$sql = "SELECT pic_src FROM korg_pics WHERE fold_id=".$fold_id;
	$result = mysql_query($sql, $connection);

	$rowcount = mysql_num_rows($result);
	$src = "";

	if($rowcount > 0) {
		mysql_data_seek($result, time() % $rowcount);
		$src_array = mysql_fetch_array($result);
		$src = $src_array['pic_src'];
	}

	return $src;
}

// Tarkistetaan löytyykö tiedosto, jos ei löydy näytetään missing.png
/*function validateSrc($file_src) {
	if( is_file($file_src) ) {
		return $file_src;
	} else return PIC_NOT_FOUND;
}*/

// Tarkistetaan löytyykö ensimmäinen tiedosto, jos ei löydy näytetään toinen.
// Jollei toinenkaan löydy näytetään missing.png.
function validateSrc($file_src, $file2_src) {
	if( is_file($file_src) ) {
		return $file_src;
	} else
	if( is_file($file2_src) ) {
		return $file2_src;
	}
	return PIC_NOT_FOUND;
}

// Haetaan kansion pids-arvosta ensimmäinen numero,
// jos ei löydy palautetaan tyhjä "".
function getIndexImagePid($fold_id, $connection) {
	$pids_string = getFolderPids($fold_id,$connection);
	$pids_array = pidsStringToArray($pids_string);
	if(count($pids_array) > 0) {
		if(strlen($pids_array[0]) > 0)
			return $pids_array[0];
	}
	return "";
}

// Haetaan kansion ensimmäisen kuvan tiedosto,
// jos ei löydy palautetaan tyhjä "".
function getIndexImageSrc($fold_id, $connection) {
	$pids_string = getFolderPids($fold_id,$connection);
	$pids_array = pidsStringToArray($pids_string);
	$image_src = "";
	if(count($pids_array) > 0) {
		if(strlen($pids_array[0]) > 0)
			$image_src = validateSrc(getImageSrc($pids_array[0],$connection),"");
	}
	return $image_src;
}

// Funktio getIndexImageThumb: Hakee indeksikuvan pikkukuvan
// Parametrit: kansion id-numero (int $fold_id), mysql yhteys (Connection $connection)
// Palauttaa pikkukuvan, ison kuvan tai oletuskuvan riippuen tiedostojen löytymisestä
// Palauttaa tyhjän jos indeksikuvaa ei ole vielä luotu
function getIndexImageThumb($fold_id, $connection) {
	$pids_string = getFolderPids($fold_id,$connection);
	$pids_array = pidsStringToArray($pids_string);
	$image_src = "";
	if(count($pids_array) > 0) {
		if(strlen($pids_array[0]) > 0)
			$image_src = getImageThumb($pids_array[0],$connection);
	}
	return $image_src;
}

// Funktio getPicturePosition: Palauttaa annetun id-numeron järjestysnumeron kansiossa
// Parametrit: kansion id-numero (int $fold_id), kuvan id-numero (int $pic_id), yhteys.
// Palauttaa järjestysnumeron kokonaislukuna. Jos lukua ei löydy, palauttaa nollan.
function getPicturePosition($fold_id,$pic_id,$connection) {
	$pids_string = getFolderPids($fold_id,$connection);
	$pids_array = pidsStringToArray($pids_string);
	$pos = array_search($pic_id,$pids_array);

	if($pos === "" || $pos === FALSE) return 0;
	return $pos;
}

######################################################################
# Muuttaa string-muotoisen, välilyöntierotteisen pids-arvon arrayksi #
######################################################################

function pidsStringToArray($pids_string) {

	$pids_array = array();

	$pids_raw = explode(" ",trim($pids_string));

	foreach($pids_raw as $element) {
		if($element != "" && $element != " ")
			array_push($pids_array,$element);
	}

	return $pids_array;
}

// Tarkistaa ID-numeron. Jos annettu numero kelpaa palautetaan annettu numero, muuten palautetaan -1.
function sanitizeId($id) {
	$fixed = -1;
	if(isset($id)) {
		if(strspn($id,"0123456789") == strlen($id))
			$fixed = $id;
	}
	return $fixed;
}

// Tyhjentää GET-attribuutit
function emptyGets() {
	echo "<script type='text/javascript'>location.search=''</script>\n";
}

// Asettaa GET-attribuutit
function setGets($newgets) {
	echo "<script type='text/javascript'>location.search='".$newgets."'</script>\n";
}

// Palauttaa siistityn päivämäärän
function getCleanDate($datetime) {
	$date_time = explode(" ",$datetime);
	$parts = explode("-",$date_time[0]);
	return intval($parts[2]).".".intval($parts[1]).".".$parts[0];
}

// Palauttaa siistityn päivämäärän ja ajan
function getCleanDateTime($datetime) {
	$date_time = explode(" ",$datetime);
	$dateparts = explode("-",$date_time[0]);
	$timeparts = explode(":",$date_time[1]);
	return intval($dateparts[2]).".".intval($dateparts[1]).".".$dateparts[0]." klo ".$timeparts[0].":".$timeparts[1];
}

// Palauttaa sivuston viimeisimmän päivitysajan
function getSiteUpdate($site_id,$connection) {
	$sql = "SELECT site_update FROM korg_site WHERE site_id=".$site_id;
	$result = mysql_query($sql, $connection);
	$updatetime = "";
	if(mysql_num_rows($result) != 0) {
		$timearray = mysql_fetch_array($result);
		$updatetime = $timearray['site_update'];
	}
	return $updatetime;
}

// Palauttaa sivuston vierailijoiden lukumäärän
// Arvo pohjautuu phpn sessionseihin
function getSiteVisitors($site_id,$connection) {
	$sql = "SELECT site_visitors FROM korg_site WHERE site_id=".$site_id;
	$result = mysql_query($sql, $connection);
	$visitors = 1;
	if(mysql_num_rows($result) != 0) {
		$visitarray = mysql_fetch_array($result);
		$visitors = $visitarray['site_visitors'];
	}
	return $visitors;
}

// Function getSitePageload: returns amount of loaded pages of given site
function getSitePageload($site_id, $connection) {
	$sql = "SELECT site_pageload FROM korg_site WHERE site_id=".$site_id;
	$result = mysql_query($sql, $connection);
	$pageload = 1;
	if(mysql_num_rows($result) != 0) {
		$array = mysql_fetch_array($result);
		$pageload = $array['site_pageload'];
	}
	return $pageload;
}

// Function getSiteMainload: returns amount of loaded mainpages of given site
function getSiteMainload($site_id, $connection) {
	$sql = "SELECT site_mainload FROM korg_site WHERE site_id=".$site_id;
	$result = mysql_query($sql, $connection);
	$mainload = 1;
	if(mysql_num_rows($result) != 0) {
		$array = mysql_fetch_array($result);
		$mainload = $array['site_mainload'];
	}
	return $mainload;
}

// Lisää sivuston kävijämäärään yhden
function addVisitor($site_id, $connection) {
	$sql = "UPDATE korg_site SET site_visitors=(site_visitors+1) WHERE site_id=".$site_id;
	@mysql_query($sql, $connection);
	return true;
}

// Function addPageload: adds one to site_pageload. site_pageload represents
//  how many times a page is loaded
function addPageload($site_id, $connection) {
	$sql = "UPDATE korg_site SET site_pageload=(site_pageload+1) WHERE site_id=".$site_id;
	return @mysql_query($sql, $connection);
}

// Function addMainload: adds one to site_mainload. site_mainload represents
//  how many times the mainpage is loaded
function addMainload($site_id, $connection) {
	$sql = "UPDATE korg_site SET site_mainload=(site_mainload+1) WHERE site_id=".$site_id;
	return @mysql_query($sql, $connection);
}

// Palauttaa annetun note_id:n mukaisen muistiinpanon tiedot
function getNoteData($note_id, $con) {
	$sql = "SELECT * FROM korg_notes WHERE note_id=".$note_id;
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) == 0) return array();

	$notearray = mysql_fetch_array($result);
	return $notearray;
}

?>
