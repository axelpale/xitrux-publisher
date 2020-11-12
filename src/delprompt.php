<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if (isset($_SESSION['logged'])) {

  //GET: fid= [n] & type= fold/pic & [pid= [n] ]

  // Tarkistetaan GET tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['pid'] tietoturvan vuoksi
  $pid = sanitizeId($_GET['pid']);

  // Poistettavan kohteen tyyppi
  $type = $_GET['type'];

  // Jos poistettavana on kansio
  if ($type == "fold") {

    $foldername = getFolderName($fid, $con);

    // Linkki takaisin kansionäkymään
    echo "<div class='linkrow top'>\n";
    echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
    echo "</div>\n";

    //Otsikko
    echo "<h1>Poista kansio '".$foldername."' ja kaikki sen sisältö?</h1>\n";

    echo "<div class='linkrow bottom'>\n";
    echo "[<a href='eraser.php?fid=".$fid."&type=fold'>";
    echo "Haluan poistaa</a>] - \n";
    echo "[<a href='foldadmin.php?fid=".$fid."'>";
    echo "Peruuta</a>]\n</div>\n";

  } else if ($type == "pic") {

    //Varmistetaan PID-arvo
    if (!isset($pid)) { echo "GET-arvoa 'pid' ei ole määritelty. Kuvaa ei voida poistaa.\n"; die(); }

    //Haetaan poistettavan kuvan nimi
    $sql = "SELECT korg_pics.pic_name AS pic_name, ";
    $sql .= "korg_pics.pic_src AS pic_src, korg_folds.fold_name ";
    $sql .= "AS fold_name FROM korg_pics, korg_folds ";
    $sql .= "WHERE korg_pics.pic_id=".$pid." AND korg_pics.fold_id=korg_folds.fold_id";
    $namearray = korg_get_row($sql, $con);

    // Linkki takaisin kansionäkymään
    echo "<div class='linkrow top'>\n";
    echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$namearray['fold_name']."</a>]\n";
    echo "</div>\n";

    // Otsikko
    echo "<h1>Poista kuva '";
    if ($namearray['pic_name'] != "") {
      echo $namearray['pic_name'];
    } else {
      echo basename($namearray['pic_src']);
    }
    echo "'?</h1>\n";

    // Valinnat. Tässä ei ole form-painikkeita, koska ne aiheuttivat seuraavan sivun lataamisen kahteen kertaan
    echo "<div class='linkrow bottom'>\n";
    echo "[<a href='eraser.php?fid=".$fid."&type=pic&pid=".$pid."'>";
    echo "Haluan poistaa</a>] - \n";
    echo "[<a href='foldadmin.php?fid=".$fid."'>";
    echo "Peruuta</a>]\n</div>\n";

  } else echo "GET-arvoa 'type' ei oltu määritetty tai sen arvo on väärä.\n";

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
