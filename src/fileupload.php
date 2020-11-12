<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['pid'] tietoturvan vuoksi
  $pid = sanitizeId($_GET['pid']);

  // Haetaan kansion nimi
  //$folder_name = getFolderName($fid, $con);

  // Haetaan kuvan tiedot
  $picdata = getPictureData($pid, $con);

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='picadmin.php?fid=".$fid."&pid=".$pid."'>Takaisin kuvaan ";
  if ($picdata['pic_name'] != "") echo $picdata['pic_name'];
  else echo basename($picdata['pic_src']);
  echo "</a>]\n";
  echo "</div>\n";

  // Otsikko
  echo "<h1>Lisämateriaalin lisääminen</h1>\n";
  printSeparator();

  // Tiedostoa kysyvä lomake
  if ($_SESSION['lastupload'] != "") { // Näyttää viimeisimmän lisäyksen. Tämä helpottaa pitkien settien lisäämistä
    echo "<div style='margin-bottom: 5px;'>Viimeisin lataus: ".$_SESSION['lastupload']."</div>\n";
  }
  echo "<form action='fileuploader.php?fid=".$fid."&pid=".$pid."' method='post' enctype='multipart/form-data'>\n";
  echo "<label for='file'>Lisää tiedosto:</label>\n";
  echo "<input type='file' name='file' id='file' style='width: 24em' />\n";
  echo "<br/><div style='margin-top: 10px'>\n";
  echo "<input type='submit' name='submit' value='OK' />\n";
  echo "<a href='foldadmin.php?fid=".$fid."'>\n";
  echo "<input type='button' value='Peruuta'></a></div>\n";
  echo "</form>\n";

  printSeparator();

  echo "<div>HUOM! Tiedoston maksimikoko on 20MB.</div>\n\n";

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
