<?php include("admin-functions.php"); ?>
<?php include("imagetools.lib.php"); ?>
<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if (isset($_SESSION['logged'])) {

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['pid'] tietoturvan vuoksi
  $pid = sanitizeId($_GET['pid']);

  // Tulostetaan virhe, jos kansion id-numero eli fid-arvo on väärä tai sitä ei ole tuotu
  if ($fid < 0 || $pid < 0) {
    echo "<h1>fid- tai pic-arvo ei kelpaa tai sitä ei ole tuotu. Skriptin suoritus lopetetaan</h1>\n";
    die();
  }

  // Haetaan kansion nimi
  $foldername = getFolderName($fid, $con);

  // Haetaan kuvan tiedot
  $pic_data = getPictureData($pid, $con);

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
  echo "</div>\n";

  //Otsikko
  echo "<h1>Kuvan vaihtaminen:</h1>\n";
  printSeparator();

  // Tiedot poistetuista kuvista
  if ($pic_data['pic_src'] != "") echo "Vanha kuvatiedosto 1: ".$pic_data['pic_src']."<br/>";
  if ($pic_data['pic_thumb'] != "") echo "Vanha kuvatiedosto 2: ".$pic_data['pic_thumb']."<br/>";
  if ($pic_data['pic_orig'] != "") echo "Vanha kuvatiedosto 3: ".$pic_data['pic_orig']."<br/>";
  echo "Kansio: ".$foldername."<br/><br/>\n";

  // Poistetaan vanhat kuvat palvelimelta
  // Jos kuvia ei ole enää olemassa, funktio ymmärtää
  // ne onnistuneesti poistetuiksi
  if (!removePictureFiles($pid, $con)) {
      echo "<span class='error'>Vanhan kuvan poisto epäonnistui.</span><br/>\n";
  }

  // Lisätään kuvasta palvelimelle kaksi tai kolme versiota
  $newpaths = addPictureFiles($_FILES,($_POST['saveorig'] == "1"), $fid);

  echo "Väliaikainen tiedosto: ".$newpaths['tempfile']."<br/><br/>\n";
  echo "Tallennetut:<br/>\n";
  if ($_POST['saveorig'] == "1") echo "Alkuperäinen kuva: ".$newpaths['original']."<br/>\n";
  echo "Optimoitu kuva: ".$newpaths['optimized']."<br/>\n";
  echo "Pikkukuva: ".$newpaths['thumbnail']."<br/>\n";

  printSeparator();

  // Testataan voidaanko kuvaa löytää/lukea lataamalla upattu kuva
  echo "<h2>Jos siirto on onnistunut, uuden kuvan pitäisi näkyä tässä:</h2>\n";
  if (is_readable($newpaths['optimized'])) {

    // Näytetään kuva
    echo "<img src='".$newpaths['optimized']."'";
    echo "style='max-width: 640px; max-height: 640px; ";
    echo "vertical-align:text-top; border-top: 1px solid black; border-bottom: 1px solid black'/>\n";

    echo "<h2>Ja pikkukuvan tässä:</h2>\n";

    // Näytetään thumbnail
    echo "<img src='".$newpaths['thumbnail']."' ";
    echo "style='vertical-align:text-top; margin-bottom: 10px;' alt='Thumbnail'/>\n";
    echo "<br/>\n";

    // Päivitetään kuvien hakemistot tietokantaan
    if (!updatePicturePaths($pid, $newpaths['optimized'], $newpaths['thumbnail'], $newpaths['original'], $con))
      echo "<span class='error'>Kuvahakemistojen päivittäminen tietokantaan epäonnistui!</span><br/>\n";

  } else echo "<span class='error'>Uutta kuvaa ei voida lukea.</span><br/>\n";


  printSeparator();

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow bottom'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
  echo "</div>\n";

} else {
  include("unauthorized.php");
}

?>

<?php include("footer.php"); ?>
