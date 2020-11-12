<?php
  include("header1.php");
  include("admin-functions.php");
?>

<script type="text/javascript">

  function cancelPrompt() {
    document.getElementById("toolprompt").innerHTML="";
  }

  function showChangeNamePrompt(fid,oldname) {
    strprint = "<form action='foldadmin.php' method='get'>";
    strprint += "Kansion uusi nimi: <input type='hidden' name='fid' value='"+fid+"' />";
    strprint += "<input type='text' name='newfoldname' value='"+oldname+"' maxlength=64 onfocus='this.select()' />";
    strprint += "<input type='submit' value='OK' />";
    strprint += "<input type='button' onclick='cancelPrompt()' value='Peruuta' />";
    strprint += "</form><div class='erottaja'>&nbsp;</div>";
    document.getElementById("toolprompt").innerHTML=strprint;
  }

</script>

<!-- Lisätyökalujen piilottamiseen -->
<script src='hiddentools.js'></script>

<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // Tarkistetaan GET tietoturvan vuoksi
  $fid = sanitizeId($_GET['fid']);

  //Kansion nimen muuttaminen
  //// $_GET['newfoldname'] täytyy tarkistaa!!!
  if (isset($_GET['newfoldname'])) {
    if (!updateFolderName($fid, $_GET['newfoldname'], $con)) echo "Virhe! Kansion nimeä ei voitu vaihtaa.";

    // Attribuutit tyhjennetään
    setGets("?fid=".$fid);
  } else

  // Kuvan liikuttaminen ylöspäin
  if (isset($_GET['moveup'])) {
    $moveup = sanitizeId($_GET['moveup']);
    if ($moveup >= 0) {
      if (!moveImageUp($fid, $moveup, $con)) echo "Kuvan liikuttaminen ylöspäin epäonnistui.<br/>\n";
    }

    // Tyhjennetään moveup-attribuutti, jotta päivitettäessä kuva ei liikkuisi uudestaan
    //setGets("?fid=".$fid);
  } else

  // Kuvan liikuttaminen alaspäin
  if (isset($_GET['movedown'])) {
    $movedown = sanitizeId($_GET['movedown']);
    if ($movedown >= 0) {
      if (!moveImageDown($fid, $movedown, $con)) echo "Kuvan liikuttaminen alaspäin epäonnistui.<br/>\n";
    }

    // Tyhjennetään movedown-attribuutti, jotta päivitettäessä kuva ei liikkuisi uudestaan
    //setGets("?fid=".$fid);
  } else

  // Kuvan piilottaminen
  if (isset($_GET['hide'])) {
    $hide = sanitizeId($_GET['hide']);
    if ($hide >= 0) {
      if (!hideImage($hide, $con)) echo "Kuvan piilottaminen epäonnistui.<br/>\n";
    }

    // Tyhjennetään hide-attribuutti, jotta päivitettäessä kuvaa ei turhaan piilotettaisi uudestaan
    setGets("?fid=".$fid);
  } else

  // Kuvan paljastaminen
  if (isset($_GET['unhide'])) {
    $unhide = sanitizeId($_GET['unhide']);
    if ($unhide >= 0) {
      if (!unhideImage($unhide, $con)) echo "Kuvan paljastaminen epäonnistui.<br/>\n";
    }

    // Tyhjennetään unhide-attribuutti, jotta päivitettäessä kuvaa ei turhaan paljastettaisi uudestaan
    setGets("?fid=".$fid);
  } else

  // Kansion piilottaminen
  if (isset($_GET['hidefold'])) {
    $hide = $_GET['hidefold']; //sanitizeId($_GET['hidefold']);
    if (!setHideFolder($fid, $hide, $con)) echo "Kansion piilottaminen/paljastaminen epäonnistui.<br/>\n";

    // Tyhjennetään hidefold-attribuutti, jotta päivitettäessä kansiota ei turhaan piilotettaisi/paljastettaisi uudestaan
    //setGets("?fid=".$fid);
  }

  // Kansion muuttaminen systeemikansioksi ja takaisin
  if (isset($_GET['makesysfold'])) {
    $makesys = $_GET['makesysfold'];
    if (!setSystemFolder($fid, $makesys, $con)) echo "Kansion muuttaminen systeemi/normaalikansioksi epäonnistui.<br/>\n";

    // Tyhjennetään hidefold-attribuutti, jotta päivitettäessä kansiota ei turhaan piilotettaisi/paljastettaisi uudestaan
    //setGets("?fid=".$fid);
  }

  //Linkki takaisin kansiolistaukseen
  echo "<div class='linkrow top'>\n";
  echo "[<a href='mainadmin.php?fid=".$fid."'>Takaisin kansioihin</a>]\n";
  echo "</div>\n";

  // Kuvajärjestys, joka kertoo missä järjestyksessä kuvat näytetään.
  // Järjestyksessä ensimmäinen kuva on kansion ns. kansikuva
  $picorder = array();

  // Jos fid-arvoa ei ole määritetty, fid = 0, jotta sivu näyttäisi jotain järkevää vaikka arvoa ei anneta.
  if (!isset($fid)) $fid = "0";


  // Kuvanavigaatioon tarvittavia
  // $first kertoo kuinka mones kyselystä palautunut kuva näytetään
  // $amount kertoo kuinka monta kuvaa tällä sivulla näytetään
  $first = 0;
  $amount = ADMIN_IMAGES_PER_PAGE;
  if (isset($_GET["first"])) $first = (int)$_GET["first"];
  if (isset($_GET["amount"])) $amount = (int)$_GET["amount"];
  // Näiden lisäksi on saattaa olla annettu $_GET['pid']
  // joka on sen kuvan id, joka tulee näkyä sivulla.
  if (isset($_GET["pid"])) {
    // Tarkistetaan GET['pid'] tietoturvan vuoksi
    $pid = sanitizeId($_GET['pid']);

    $id_pos = getPicturePosition($fid,$pid,$con);
    $first = $id_pos - ($id_pos % $amount); // Pyöristää luvun alempaan amountiin
  }

  // Haetaan kansion nimi ja kuvajärjestys
  $sql = "SELECT fold_name, pids, fold_hidden, fold_system FROM korg_folds WHERE fold_id=".$fid;
  $folder_info = korg_get_row($sql, $con);

  if ($folder_info === false) {
    echo "<h1>Kansion nimeä ei löytynyt</h1>\n";
  } else {

    // Hajotetaan stringinä oleva kuvajärjestys taulukkoon yksittäisiksi pic_id-numeroiksi
    $picorder = explode(" ", trim($folder_info['pids']));

    // Kansion nimi
    echo "<h1>Kansio: ".$folder_info['fold_name'];
    if ($folder_info['fold_hidden'] == 1) echo " (piilotettu)";
    if ($folder_info['fold_system'] == 1) echo " (systeemikansio)";
    echo "</h1>\n";

    // Kansion luokittelu
    echo "<h2>Luokittelu:";
    echo printFolderTags($fid, $con);
    echo "</h2>\n\n";

    // Kansion muokkaustyökalut
    echo "<div class='linkrow'>\n";
    echo "[<a href='picupload.php?fid=".$fid."'>Lisää kuva</a>]\n";
    echo "[<a href='foldertagedit.php?fid=".$fid."'>Muuta luokittelua</a>]\n";
    echo "[<a onclick='showChangeNamePrompt(".$fid.",\"".$folder_info['fold_name']."\")'>Muuta kansion nimeä</a>]\n";
    echo "[<a href='delprompt.php?fid=".$fid."&type=fold'>Poista tämä kansio</a>]\n";
    echo "[<a href='foldadmin.php?fid=".$fid."&hidefold=";
    if ($folder_info['fold_hidden'] == "1") {
      echo "0'>Paljasta kansio</a>]\n";
    } else {
      echo "1'>Piilota kansio</a>]\n";
    }
    echo hiddentoolsStart();
    echo "[<a href='foldadmin.php?fid=".$fid."&makesysfold=";
    if ($folder_info['fold_system'] == "1") {
      echo "0'>Muuta peruskansioksi</a>]\n";
    } else {
      echo "1'>Muuta systeemikansioksi</a>]\n";
    }
    echo hiddentoolsEnd();
    echo "</div>\n\n";

    // Jos suoritetaan toiminto, tähän avautuu toiminnon hyväksymislomake
    echo "<div id='toolprompt'></div>\n\n";

    // Lasketaan saadut rivit eli löytyneitten kuvien määrä
    if ($picorder[0] == "") {
      $rowcount = 0;
    } else {
      $rowcount = count($picorder);
    }

    // Jos rivejä on enemmän kuin nolla, listataan kuvat
    if ($rowcount > 0) {

      // Kuvanavigaatio. Esim [1-9]
      echo "<div class='linkrow top none'>\n";
      for ($i = 0; $i < $rowcount; $i = $i + $amount) {
        if ($i == $first) { // Jos ollaan kyseisessä sivulla niin ei tehdä linkkiä
          echo "<b>[".($i+1)."-";
          if ($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
          else echo $i+$amount;
          echo "]</b>\n";
        } else {
          echo "[<a href='foldadmin.php?fid=".$fid."&first=".$i."'>".($i+1)."-";
          if ($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
          else echo $i+$amount;
          echo "</a>]\n";
        }
      }
      echo "</div>\n";

      // Alue, jolle pikkukuvat ilmestyvät
      echo "<div class='itembrowser'>\n\n";
      // $first kertoo ensimmäisen näytettävän kuvan ja $amount näytettävien kuvien määrän
      for ($i = 0; $i < ($rowcount - $first) && $i < $amount; $i++) {

        if ($i % 3 == 0) echo "<div class='row'>\n\n";

        $picindex = $i+$first;

        $sql = "SELECT pic_id,pic_name,pic_caption,pic_src,pic_thumb,pic_hidden ";
        $sql .= "FROM korg_pics WHERE pic_id=".$picorder[$picindex];
        $item = korg_get_row($sql, $con);

        echo "<div class='browseritem";
        if ($picindex==0) echo " index"; // Jos indeksikuva niin fontti punaisella
        echo "'>\n\n";

        echo "[<a href='picadmin.php?fid=".$fid."&pid=".$item['pic_id']."' name='".$item['pic_id']."' >";
        if ($item['pic_name'] != "") echo $item['pic_name'];
        else echo basename($item['pic_src']);
        echo "</a>]\n";
        //$filesize = (int)((@filesize($item['pic_src'])/1024)); // Ei saa heittää virhettä vaikka kuva puuttuisikin
        //echo " (".$filesize."kt)"; // Kuvan koko esm 105kt
        if ($item['pic_hidden'] == "1") echo " (piilotettu)";
        if ($picindex==0) echo " (index)"; // Jos indeksikuva niin kirjoitetaan teksti

        echo "<br/>\n<a href='picadmin.php?fid=".$fid."&pid=".$item['pic_id']."' >\n";
        echo "<img class='";
        if ($picindex==0) {
          echo "indeximage"; // Jos indeksikuva niin reunus punaisella
        } else {
          if ($item['pic_hidden'] == "1") {
            echo "hiddenimage";
          }
        }
        // validateSrc tarkistaa onko tiedosto olemassa ja palauttaa oletuskuvan jollei.
        echo "' src='".validateSrc($item['pic_thumb'],$item['pic_src'])."' alt='".$item['pic_name']."' />\n";
        echo "</a>\n\n";

        // Työkalut
        echo "<div class='tools'>\n";
        echo "<a href='foldadmin.php?fid=".$fid."&pid=".$item['pic_id']."&moveup=".$picindex."'>\n";
        echo "<img src='images/left01.gif' alt='Liikuta kuvaa vasemmalle' /></a>&nbsp;\n";
        echo "<a href='picmovetop.exe.php?fid=".$fid."&pid=".$item['pic_id']."'>\n";
        echo "<img src='images/movetop01.gif' alt='Liikuta ensimmäiseksi' /></a>&nbsp;\n";
        echo "<a href='picedit.php?fid=".$fid."&pid=".$item['pic_id']."'>\n";
        echo "<img src='images/info01.gif' alt='Muokkaa tietoja' /></a>&nbsp;\n";
        echo "<a href='delprompt.php?fid=".$fid."&type=pic&pid=".$item['pic_id']."'>\n";
        echo "<img src='images/delete00.gif' alt='Poista kuva' /></a>&nbsp;\n";
        if ($item['pic_hidden'] == 1) {
          echo "<a href='foldadmin.php?fid=".$fid."&unhide=".$item['pic_id']."'>\n";
          echo "<img src='images/unhide01.gif' alt='Paljasta kuva' /></a>&nbsp;\n";
        } else {
          echo "<a href='foldadmin.php?fid=".$fid."&hide=".$item['pic_id']."'>\n";
          echo "<img src='images/hide00.gif' alt='Piilota kuva' /></a>&nbsp;\n";
        }
        echo "<a href='foldadmin.php?fid=".$fid."&pid=".$item['pic_id']."&movedown=".$picindex."'>\n";
        echo "<img src='images/right00.gif' alt='Liikuta kuvaa oikealle' /></a>\n";
        echo "</div>\n</div>\n\n";

        // Lopetetaan kelluminen joka kolmannella rivillä tai kuvien lopussa
        if (($i+1) % 3 == 0 || ($picindex+1) == $rowcount ) {
          echo "<br class='stopfloat'/></div>\n\n";
        }
      }
      echo "</div>\n\n";

      // Kuvanavigaatio. Esim [1-9]
      echo "<div class='linkrow bottom none'>\n";
      for ($i = 0; $i < $rowcount; $i = $i + $amount) {
        if ($i == $first) {
          echo "<b>[".($i+1)."-";
          if ($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
          else echo $i+$amount;
          echo "]</b>\n";
        } else {
          echo "[<a href='foldadmin.php?fid=".$fid."&first=".$i."'>".($i+1)."-";
          if ($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
          else echo $i+$amount;
          echo "</a>]\n";
        }
      }
      echo "</div>\n";

    } else {
      // Jos kansio on tyhjä, näytetään kuvien lukumäärä eli 0
      echo "<div class='count'>Yhteensä 0 kuvaa.</div>\n";
    }

    //Linkki takaisin kansiolistaukseen
    echo "<div class='linkrow bottom'>\n";
    echo "[<a href='mainadmin.php?fid=".$fid."'>Takaisin kansioihin</a>]\n";
    echo "</div>\n";
  }

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
