<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Haetaan kansion nimi
  $folder_name = getFolderName($fid, $con);

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
  echo "</div>\n";

  // Otsikko
  echo "<h1>Kuvan lisääminen kansioon '".$folder_name."'</h1>\n";
  printSeparator();

  // Tiedostoa kysyvä lomake
  if ($_SESSION['lastupload'] != "") { // Näyttää viimeisimmän lisäyksen. Tämä helpottaa pitkien settien lisäämistä
    echo "<div style='margin-bottom: 5px;'>Viimeisin lataus: ".$_SESSION['lastupload']."</div>\n";
  }
  echo "<form action='picuploader.php?fid=".$fid."' method='post' enctype='multipart/form-data'>\n";
  echo "<label for='file'>Lisää kuva:</label>\n";
  echo "<input type='file' name='file' id='file' style='width: 24em' /><br/>\n";
  echo "<label for='saveorig'>Tallenna myös alkuperäinen:</label>\n";
  echo "<input type='checkbox' name='saveorig' id='saveorig' value='1' />\n";
  echo "<br/><div style='margin-top: 10px'>\n";
  echo "<input type='submit' name='submit' value='OK' />\n";
  //echo "<input type='submit' name='submit' value='OK, tallenna myös alkuperäinen' />\n";
  echo "<a href='foldadmin.php?fid=".$fid."'>\n";
  echo "<input type='button' value='Peruuta'></a></div>\n";
  echo "</form>\n";

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
