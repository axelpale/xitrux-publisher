<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['pid'] tietoturvan vuoksi
  $pid = sanitizeId($_GET['pid']);

  // Haetaan kuvan tiedot
  $picdata = getPictureData($pid, $con);

  // Haetaan kansion nimi
  $foldername = getFolderName($fid, $con);

  // Linkki takaisin kansionäkymään
  echo "<div class='linkrow top'>\n";
  echo "[<a href='foldadmin.php?fid=".$fid."&pid=".$pid."'>\n";
  echo "Takaisin kansioon ".$foldername."</a>]\n";
  echo "</div>\n";

  // Otsikko
  echo "<h1>Kuvan '";
  if ($picdata['pic_name'] != "") echo $picdata['pic_name'];
  else echo basename($picdata['pic_src']);
  echo "' tietojen muuttaminen</h1>\n";
  printSeparator();

  // Muutoslomake
  echo "<form action='picedit.exe.php?fid=".$fid."&pid=".$pid."' method='post' ";
  echo "style='margin-top: 0.8em; background-image: url(\"".$picdata['pic_thumb']."\"); ";
  echo "background-repeat: no-repeat; background-position: top right;'>\n";
  echo "<table>\n<tr>\n<td>\n";
  echo "Kuvan nimi:\n</td>\n<td>\n";
  echo "<input class='textfield' type='text' name='newpicname' ";
  echo "value='".htmlentities($picdata['pic_name'],ENT_QUOTES)."' maxlength=64 onfocus='this.select()' />";
  echo "\n</td>\n</tr>\n<tr>\n<td>\n";
  echo "Lisämateriaali:\n</td>\n<td>\n";
  echo "<input class='textfield' type='text' name='newpiclink' value='".$picdata['pic_link']."' maxlength=120 onfocus='this.select()' />";
  echo "\n</td>\n</tr>\n<tr>\n<td valign='top' style='padding-top: 4px;'>\n";
  echo "Kuvateksti:\n</td>\n<td>\n";
  echo "<textarea name='newcaption' rows='6' cols='60'>";
  echo $picdata['pic_caption'];
  echo "</textarea>\n</td>\n</tr>\n<tr>\n<td></td><td>\n";
  echo "<input type='submit' value='Päivitä' />\n";
  echo "<input type='button' onclick='window.location=\"foldadmin.php?fid=".$fid."&pid=".$pid."\"' value='Peruuta'>\n";
  echo "</td>\n</tr>\n</table>\n</form>\n";

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
