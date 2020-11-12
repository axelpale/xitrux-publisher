<?php include("header1.php"); ?>

<script type="text/javascript">

	var index = 0;
	var is_empty = false;
	var piccount = 0;

<?php
	// Tarkistetaan GET tietoturvan vuoksi
	$fid = sanitizeId($_GET['fid']);

	// Kuvajärjestys, joka kertoo missä järjestyksessä kuvat näytetään.
	// Järjestyksessä ensimmäinen kuva on kansion ns. kansikuva
	$picorder = array();
	
	// Kansion nimi
	$foldername = "";

	//Haetaan kansion nimi ja kuvajärjestys
	$sql = "SELECT fold_name, pids FROM korg_folds WHERE fold_id=".$fid." AND fold_hidden=0";
	$result = mysql_query($sql, $con);
	if(mysql_num_rows($result) != 0) {
		$folder_info = mysql_fetch_array($result);

		// Kansion nimi
		$foldername = $folder_info['fold_name'];

		// Hajotetaan stringinä oleva kuvajärjestys taulukkoon yksittäisiksi pic_id-numeroiksi
		$picorder = pidsStringToArray($folder_info['pids']);

		//// Kansion luokittelua saattaa tulla vielä tarvitsemaan
		// Kansion luokittelu
		/*echo "<h2>Luokittelu:";
		
		$sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fid;
		$result = mysql_query($sql, $con);
		if(mysql_num_rows($result) == 0) echo " ei luokittelua";
		else {
			while($tag = mysql_fetch_array($result)) {
				echo " ".$tag['tag'];
			}
		}
		echo "</h2>\n";*/

		// Lasketaan saadut rivit eli löytyneitten kuvien määrä
		// Poistetaan ensimmäinen kuva, sillä se on kansion indeksikuva, jota ei haluta näyttää kuvaselaimessa
		// Jos kansiossa ei ole muuta kuin indeksikuva rivimääräksi tulee tällöin nolla
		/////array_shift($picorder);
		$rowcount = count($picorder);

		// Alustetaan JavaScriptin kuvat-taulukko, johon tallennetaan kuvien sijainnit
		echo "var srcs = new Array();\n";
		echo "var origs = new Array();\n";
		echo "var names = new Array();\n";
		echo "var captions = new Array();\n\n";

		$j = 0; // Tallennuspaikan indeksi. Jos kuva on salattu niin tämä ei kasva.
		for ($i=0; $i<$rowcount; $i++) {

			$sql = "SELECT pic_name,pic_caption,pic_src,pic_orig FROM korg_pics WHERE pic_id=".$picorder[$i]." AND pic_hidden=0";
			$result = mysql_query($sql, $con);

			if(mysql_num_rows($result) > 0) {
				$item = mysql_fetch_array($result);

				// Vaihdetaan rivinvaihdot toimivaan muotoon
				$captiontext = str_replace("\r\n","<br/>",str_replace("\"","&quot;",$item['pic_caption']));

				// Tallennetaan tiedot JS-taulukkoon
				echo "srcs[".$j."] = \"".$item['pic_src']."\";\n";
				echo "origs[".$j."] = \"".$item['pic_orig']."\";\n";
				echo "names[".$j."] = \"".$item['pic_name']."\";\n";
				echo "captions[".$j."] = \"".$captiontext."\";\n\n";

				// Onnistuneen JavaScript-jonoon lisäämisen jälkeen kasvatetaan $j
				$j++;
			}
		}

		echo "piccount = ".$j.";\n\n";
	}
?>

	// Vaihtaa seuraavaan kuvaan
	function next_picture() {
		if(index+1 < piccount) {
			index++;

			document.getElementById('bigimage').src = srcs[index];
			if(origs[index] != "") {
				document.getElementById('zoom').href = origs[index];
			} else {
				document.getElementById('zoom').href = srcs[index];
			}
			document.getElementById('caption').innerHTML = captions[index];
			updateIndex();
		}
	}

	// Vaihtaa ensimmäiseen kuvaan
	function first_picture() {
		index = 0;

		document.getElementById('bigimage').src = srcs[index];
		if(origs[index] != "") {
			document.getElementById('zoom').href = origs[index];
		} else {
			document.getElementById('zoom').href = srcs[index];
		}
		document.getElementById('caption').innerHTML = captions[index];

		updateIndex();
	}

	// Vaihtaa ensimmäiseen kuvaan
	function last_picture() {
		index = piccount - 1;

		document.getElementById('bigimage').src = srcs[index];
		if(origs[index] != "") {
			document.getElementById('zoom').href = origs[index];
		} else {
			document.getElementById('zoom').href = srcs[index];
		}
		document.getElementById('caption').innerHTML = captions[index];

		updateIndex();
	}

	// Vaihtaa edelliseen kuvaan
	function prev_picture() {
		if(index-1 >= 0) {
			index--;

			document.getElementById('bigimage').src = srcs[index];
			if(origs[index] != "") {
				document.getElementById('zoom').href = origs[index];
			} else {
				document.getElementById('zoom').href = srcs[index];
			}
			document.getElementById('caption').innerHTML = captions[index];
			updateIndex();
		}
	}

	// Päivittää kuvanumeroilmaisimen
	function updateIndex() {
		var index_number = index+1;
		document.getElementById('indexnumber').innerHTML = index_number+'/'+piccount;
	}

</script>

<?php include("header2.php"); ?>

<!-- Linkit taaksepäin -->
<div class='linkrow top'>
[<a href='foldbrowser.php'>Takaisin</a>]
</div>

<div class='picbrowser'>

<div class='heading'>
<div class='leftside'><h1>
<?php
	echo $foldername;
?>
 - <span id='indexnumber'></span>
</h1></div>
<div class='rightside'>
<div class='button next' onclick='next_picture()'>&nbsp;</div>
<!-- div class='button last' onclick='last_picture()'>&nbsp;</div -->
<a id='zoom' href='' target='_blank'><div class='button zoom'>&nbsp;</div></a>
<!-- div class='button first' onclick='first_picture()'>&nbsp;</div -->
<div class='button prev' onclick='prev_picture()'>&nbsp;</div>
</div>
<div class='stopfloat'></div>
</div>

<div class='picture'>
<img id='bigimage' src='' alt='Kuvaselain' onclick='next_picture()' />
</div>
<div id='caption' class='caption'>
</div>
</div>

<!-- Linkit taaksepäin -->
<div class='linkrow bottom'>
[<a href='foldbrowser.php'>Takaisin</a>]
</div>

<script type="text/javascript">
	updateIndex();
	document.getElementById('bigimage').src = srcs[0];
	if(origs[0] != "") {
		document.getElementById('zoom').href = origs[0];
	} else {
		document.getElementById('zoom').href = srcs[0];
	}
	document.getElementById('caption').innerHTML = captions[0];
</script>

<?php

	// Linkit taaksepäin
	//echo "<div class='linkrow top'>\n";
	//echo "[<a href='foldbrowser.php'>Takaisin</a>]\n ";
	//echo "</div>\n\n";

	//echo "<div class='picbrowser'>\n";
	//echo "<a href='".$picdata['pic_src']."' target='_blank'>\n";
	//echo "<img id='bigimage' src='' />\n";
	//echo "</a>\n";
	//echo "<div id='caption' class='caption'>\n";
	//echo nl2br($picdata['pic_caption'])."\n";
	//echo "</div></div>\n\n";

	// Linkit taaksepäin
	//echo "<div class='linkrow bottom'>\n";
	//echo "[<a href='foldbrowser.php'>Takaisin</a>]\n ";
	//echo "</div>\n\n";
?>

<?php include("footer.php"); ?>
