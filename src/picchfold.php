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
  echo "<h1>Kuvan siirto toiseen kansioon</h1>\n";
  printSeparator();

  //Siirtolomake
  echo "<form action='picchfold.exe.php?fid=".$fid."&pid=".$pid."' method='post'>\n";
  echo "Siirrä kansioon:<br/>\n";
  echo "<select id='newfolder' name='newfolder'>\n";

  // Haetaan kaikki kansiot
  $sql = "SELECT fold_id,fold_name FROM korg_folds";
  $folders = korg_get_rows($sql, $con);

  foreach ($folders as $fold) {
    echo "<option value='".$fold['fold_id']."'";
    if ($fold['fold_id'] == $fid) echo " selected='selected' ";
    echo ">".$fold['fold_name']."</option>\n";
  }
  echo "</select>\n";
  echo "<input type='submit' value='Siirrä' />\n";
  echo "<input type='button' onclick='window.location=\"picadmin.php?fid=".$fid."&pid=".$pid."\"' value='Peruuta'>\n";
  echo "</form>\n";

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
