<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // nid = note_id = muistiinpanon id-numero
  // Tarkistetaan GET['nid'] tietoturvan vuoksi
  $nid = sanitizeId($_GET['nid']);

  // Haetaan muistiinpanon tiedot
  $notedata = getNoteData($nid,$con);

  // Linkki takaisin muistiinpanoihin
  echo "<div class='linkrow top'>\n";
  echo "[<a href='project.php#".$nid."'>\n";
  echo "Takaisin muistiinpanoihin</a>]\n";
  echo "</div>\n";

  // Otsikko
  echo "<h1>Poista muistiinpano '".$notedata['note_created']."'?</h1>\n";

  echo "<div class='linkrow bottom'>\n";
  echo "[<a href='notedelete.exe.php?nid=".$nid."'>";
  echo "Haluan poistaa</a>] - \n";
  echo "[<a href='project.php#".$nid."'>";
  echo "Peruuta</a>]\n</div>\n";

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
