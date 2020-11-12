<?php
  include("admin-functions.php");
  include("imagetools.lib.php");
  include("header1.php");
  include("header2.php");
?>

<?php
if($LOGGED) {

  $PICTURE_DEFAULT_NAME = "";

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Haetaan kansionimi ja kuvien id-numerot
  $foldername = getFolderName($fid,$con);
  $folderpids = getFolderPids($fid,$con);

  // Virheilmoitus jos kuvien uppaus ei onnistu
  $uploaderror = "Kuvaa ei ole määritetty tai se on liian suuri (max ";
  $uploaderror .= MAX_FILE_SIZE." tavua). Yritä <a href='picupload.php?fid=";
  $uploaderror .= $fid."'>uudelleen</a>.";

  // Lisätään kuvasta palvelimelle kaksi tai kolme versiota
  $newpaths = addPictureFiles($_FILES,($_POST['saveorig'] == "1"),$fid) or die($uploaderror);

  // Tulosteet
  // Tietojenkäsittelyn tulosteet kerätään nyt alkuvaiheessa,
  // sillä yläreunan linkit ovat riippuvaisia tietojenkäsittelyn
  // tuloksesta.
  $prints1 = "";
  $prints2 = "";

  $prints1 .= "Väliaikainen tiedosto: ".$newpaths['tempfile']."<br/><br/>\n";
  $prints1 .= "Tallennetut:<br/>\n";
  if($_POST['saveorig'] == "1") $prints1 .= "Alkuperäinen kuva: ".$newpaths['original']."<br/>\n";
  $prints1 .= "Optimoitu kuva: ".$newpaths['optimized']."<br/>\n";
  $prints1 .= "Pikkukuva: ".$newpaths['thumbnail']."<br/>\n";

  $prints1 .= "<div class='erottaja'>&nbsp;</div>\n";

  $hakemisto = getUploadDir($fid);
  $kuvan_id = 0;

  // Testataan voidaanko kuvaa löytää/lukea lataamalla upattu kuva
  $prints1 .= "<h2>Jos siirto on onnistunut, kuvan pitäisi näkyä tässä:</h2>\n";
  if(is_readable($newpaths['optimized'])) {

    // Päivitetään käyttäjän tieto 'viimeisin lataus',
    // joka näkyy kun uutta kuvaa upataan
    $_SESSION['lastupload'] = $newpaths['optimized'];

    // Näytetään kuva
    $prints1 .= "<img src='".$newpaths['optimized']."' ";
    $prints1 .= "style='max-width: 640px; max-height: 640px; ";
    $prints1 .= "vertical-align:text-top; border-top: 1px solid black; ";
    $prints1 .= "border-bottom: 1px solid black; margin-bottom: 10px;' ";
    $prints1 .= "alt='Optimized image' />\n";

    $prints1 .= "<h2>Ja pikkukuvan tässä:</h2>\n";

    // Näytetään thumbnail
    $prints1 .= "<img src='".$newpaths['thumbnail']."' ";
    $prints1 .= "style='vertical-align:text-top; margin-bottom: 10px;' alt='Thumbnail'/>\n";
    $prints1 .= "<br/>\n";

    // Lisätään kuva tietokantaan
    $sql = "INSERT INTO korg_pics(pic_id,pic_name,pic_src,pic_thumb,pic_orig,fold_id) VALUES(";
    $sql = $sql."DEFAULT,'".$PICTURE_DEFAULT_NAME."','".$newpaths['optimized']."','".$newpaths['thumbnail']."','".$newpaths['original']."',".$fid.")";
    if(!mysql_query($sql, $con))
      $prints1 .= "Virhe! Kuvaa ei voitu lisätä tietokantaan. Error: ".mysql_error()."<br/>\n";

    // Jos kuvan tietokantaan lisääminen onnistui, lisätään kuvan pic_id-numero kansion pids-numeroihin
    // pids-numerot määrittävät kuvien järjestyksen niin kansionmuokkausympäristössä kuin julkisessa kuvaselauksessakin
    else {

      // Uuden kuvan pic_id
      $kuvan_id = mysql_insert_id($con); // palauttaa viimeisimmän lisätyn auto_increment-numeron

      // Päivitetään pids-arvo
      if(!addToFolder($fid, $kuvan_id, $con)) {
        $prints1 .= "Virhe! Kuvan pic_id-numeroa ei voitu lisätä kansion tietoihin. Error: ".mysql_error()."<br/>\n";
      }

      // Tarkistus, jossa vertaillaan tähän kansioon liittyviä kuvia
      // ja kansion pids-numerorypästä. Jos löydetään eroja, puuttuvat
      // numerot lisätään pids-ryppääseen.

      // Haetaan tietokannassa olevien kuvien pic_id-arvot
      $pids_pics = getPictureIds($fid,$con);

      // Haetaan kansion tiedoissa olevat kuvaidt pids-kentästä
      $pids_fold = getPidsArray($fid,$con);

      // Vertaillaan näitä jonoja keskenään
      $diff1 = array_diff($pids_pics, $pids_fold); //Alkiot, jotka puuttuvat jonosta $pids_fold
      $diff2 = array_diff($pids_fold, $pids_pics); //Alkiot, jotka puuttuvat jonosta $pids_pics

      //Jos eroja löytyi niin
      if(count($diff1) != 0) {
        // Lisätään kansiosta puuttuvat kuvaidt kansion tietoihin
        foreach($diff1 as $id_number) {
          array_push($pids_fold, $id_number);
        }

        $newpids = "";

        foreach($pids_fold as $id_number) {
          $newpids .= $id_number." ";
        }

        if(!updatePids($fid, $newpids, $con)) {
          $prints1 .= "Virhe! pic_id-numeroita ei voitu päivittää kansion tietoihin. <br/>\n";
          $prints1 .= "Error: ".mysql_error()."<br/>\n";
        }
      }

      if(count($diff2) != 0) {
        // Näytetään käyttäjälle virheilmoitus
        $prints1 .= "Varoitus! Kansio sisältää kuvanumeroita, joita ei ole enää olemassa.<br/>\n";

        //// kysytään halutaanko kuvaid poistaa kansion tiedoista
        //// Jos halutaan niin
          //// Poistetaan kuvaid kansion tiedoista
      }

    }

  } else $prints1 .= "Kuvaa ei voida lukea.\n";

  // Asetetaan kuvatieto nulliksi
  // jottei sivua uppaamalla pystytä
  // lisäämään samaa kuvaa moneen kertaan
  $_FILES = NULL;

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."&pid=".$kuvan_id."'>Takaisin kansioon ".$foldername."</a>] - \n";
  if($kuvan_id > 0) echo "[<a href='picedit.php?fid=".$fid."&pid=".$kuvan_id."'>Muokkaa kuvan tietoja</a>] - \n";
  echo "[<a href='picupload.php?fid=".$fid."' style=''>Lisää seuraava kuva</a>]\n";
  echo "</div>\n";

  //Otsikko
  echo "<h1>Kuvan lisääminen kansioon '".$foldername."'</h1>\n";
  printSeparator();

  // Tulostetaan tietojen käsittelyn aikaiset tulosteet
  echo $prints1;

  //Linkit takaisin
  echo "<div class='linkrow bottom'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."&pid=".$kuvan_id."'>Takaisin kansioon ".$foldername."</a>] - \n";
  if($kuvan_id > 0) echo "[<a href='picedit.php?fid=".$fid."&pid=".$kuvan_id."'>Muokkaa kuvan tietoja</a>] - \n";
  echo "[<a href='picupload.php?fid=".$fid."' style=''>Lisää seuraava kuva</a>]\n";
  echo "</div>\n";

} else {
  include("unauthorized.php");
}

?>

<?php include("footer.php"); ?>
