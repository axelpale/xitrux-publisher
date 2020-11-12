<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if (isset($_SESSION['logged'])) {

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['pid'] tietoturvan vuoksi
  $pid = sanitizeId($_GET['pid']);

  // Haetaan kansion nimi
  $folder_name = getFolderName($fid,$con);

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."'>\n";
  echo "Takaisin kansioon ".$foldername."</a>]\n";
  echo "</div>\n";

  // Otsikko
  echo "<h1>Kuvatiedoston vaihtaminen:</h1>\n";
  printSeparator();

  // Tarkastetaan onko sivulle syötetty väärät arvot
  if ($fid >= 0 && $pid >= 0) {
    // Tiedostoa kysyvä lomake.
    // HUOM pid-arvo on määritetty, joten uuden kuvan lisäämisen sijaan
    // vanha, jo tietokannassa oleva kuvatieto vain
    // päivitetään, uusi kuvatiedosto lisätään ja vanha
    // poistetaan
    echo "<form action='picchanger.php?fid=".$fid."&pid=".$pid."' method='post' enctype='multipart/form-data'>\n";
    echo "<label for='file'>Uusi kuvatiedosto:</label>\n";
    echo "<input type='file' name='file' id='file' /><br/>\n";
    echo "<label for='saveorig'>Tallenna myös alkuperäinen:</label>\n";
    echo "<input type='checkbox' name='saveorig' id='saveorig' value='1' />\n";
    echo "<br/><div style='margin-top: 10px'>\n";
    echo "<input type='submit' name='submit' value='Vaihda' />\n";
    echo "<input type='button' onclick='window.location=\"foldadmin.php?fid=".$fid."\"' value='Peruuta'>\n";
    echo "</div>\n</form>\n";
  } else echo "<h1>FID- tai PID-arvoa ei tunnistettu!</h1>\n";

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
