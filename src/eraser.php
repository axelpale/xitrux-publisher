<?php

include("admin-functions.php");
include("header1.php");
include("header2.php");

if (isset($_SESSION['logged'])) {

  // Tämä tiedosto poistaa palvelimelta kansion taikka kuvan sekä niihin liittyvät tietokantatiedot
  // GET: fid= [n] & type= fold/pic & [pid= [n] ]

  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  // Poistettavan kohteen tyyppi
  $type = $_GET['type'];

  // Jos poistettavana on kansio
  if ($type == "fold") {

    // Haetaan poistettavan kansion nimi
    $foldername = getFolderName($fid, $con);

    // hakemisto
    $directory = "images/upload/fid".$fid;

    // Linkki takaisin kansionäkymään
    echo "<div class='linkrow top'>\n";
    echo "[<a href='mainadmin.php'>Takaisin kansioihin</a>]\n";
    echo "</div>\n";

    // Suoritetaan hakemiston palvelimelta poistava funktio
    if (removeResource($directory)) {

      // Poistetaan hakemiston sisältämät kuvat tietokannasta
      $sql = "DELETE FROM korg_pics WHERE fold_id=".$fid;
      if (korg_delete($sql, $con) == 0) {

        // Poistetaan hakemisto tietokannasta. Huomaa että ensin poistetaan kuvat ja sitten vasta kansio
        $sql = "DELETE FROM korg_folds WHERE fold_id=".$fid;
        if (korg_delete($sql, $con) == 0) {
          echo "<h1>Kansion poisto onnistui</h1>\n";
          printSeparator();
        }
        else echo "Kansio poistettiin palvelimelta mutta poisto tietokannasta epäonnistui. Kuvien poisto onnistui.<br/>\n";

      }
      else echo "Kansio kuvineen poistettiin palvelimelta mutta poisto tietokannasta epäonnistui.<br/>\n";

    } else { echo "Kansion poisto sisältöineen palvelimelta epäonnistui. Tietokantatietoja ei näin ollen poistettu.<br/>\n"; }

    echo "Hakemisto: ".$directory."<br/>\n";
    echo "Kansionimi: ".$foldername."\n";

    // Linkki takaisin kansionäkymään
    echo "<div class='linkrow bottom'>\n";
    echo "[<a href='mainadmin.php'>Takaisin kansioihin</a>]\n";
    echo "</div>\n";

  } else {
    // Jos poistettavana on tiedosto
    if ($type == "pic") {

      // Tarkistetaan GET['pid'] tietoturvan vuoksi
      $pid = sanitizeId($_GET['pid']);

      // Haetaan poistettavan kuvan kuvatiedostot
      // Niitä saattaa olla kolme: alkuperäinen, optimoitu ja pikkukuva
      $pic_data = getPictureData($pid, $con);

      // Haetaan kansion nimi tietokannasta, jotta tiedetään mistä kansiosta kuva poistetaan
      $foldername = getFolderName($fid, $con);

      // Linkki takaisin kansionäkymään
      echo "<div class='linkrow top'>\n";
      echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
      echo "</div>\n";

      if ($pic_data['pic_src'] != "") echo "Kuvatiedosto 1: ".$pic_data['pic_src']."<br/>";
      if ($pic_data['pic_thumb'] != "") echo "Kuvatiedosto 2: ".$pic_data['pic_thumb']."<br/>";
      if ($pic_data['pic_orig'] != "") echo "Kuvatiedosto 3: ".$pic_data['pic_orig']."<br/>";
      echo "Kansio: ".$foldername."<br/><br/>\n";

      // Suoritetaan tiedoston palvelimelta poistava funktio
      // Jos tiedostoa ei löydy niin functio palauttaa arvon true, jolloin kuvatieto poistuu tietokannasta
      if (removeResource($pic_data['pic_src'])
      && removeResource($pic_data['pic_orig'])
      && removeResource($pic_data['pic_thumb'])) {

        echo "Kuvan poisto palvelimelta onnistui.<br/>\n";

        // Poistetaan kuva tietokannasta
        $sql = "DELETE FROM korg_pics WHERE pic_id=".$pid;
        if (korg_delete($sql, $con) == 0) {
          echo "Kuvan poisto tietokannasta onnistui.<br/>\n";
          // Poistetaan pic_id myös kansion tiedoista
          if (removeFromFolder($fid, $pid, $con)) {
            echo "Kuvan poisto kansion tiedoista onnistui.<br/>\n";
          } else {
            echo "Kuvan poisto kansion tiedoista epäonnistui.<br/>\n";
          }
        } else {
          echo "Kuva poistettiin palvelimelta mutta poisto tietokannasta epäonnistui.<br/>\n";
        }
      } else {
        echo "Kuvan poisto palvelimelta epäonnistui. Kuvaa ei näinollen poistettu tietokannastakaan.<br/>\n";
      }

      // Linkki takaisin kansionäkymään
      echo "<div class='linkrow bottom'>\n";
      echo "[<a href='foldadmin.php?fid=".$fid."'>Takaisin kansioon ".$foldername."</a>]\n";
      echo "</div>\n";

    } else {
      echo "GET-arvo 'type' on määrittelemätön tai väärä.\n";
    }
  }
} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
