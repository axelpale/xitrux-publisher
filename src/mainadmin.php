<?php
  include("header1.php");
?>

<?php
  // Kirjoitetaan javascript ainoastaan kirjautuneelle
  if ($LOGGED) {
?>

<script type="text/javascript">

  function cancelPrompt() {
    document.getElementById("toolprompt").innerHTML = "";
  }

  function showNewFolderPrompt() {
    strprint = "<form action='mainadmin.php' method='get'>\n";
    strprint += "Lisää kansio nimellä: ";
    strprint += "<input type='text' name='newfoldername' value='uusi kansio' maxlength=64 onfocus='this.select()' />\n";
    strprint += "<input type='submit' value='OK' />\n";
    strprint += "<input type='button' onclick='cancelPrompt()' value='Peruuta' />\n";
    strprint += "</form><div class='erottaja'>&nbps;</div>\n\n";
    document.getElementById("toolprompt").innerHTML = strprint;
  }

  function showAddFilterPrompt() {
    strprint = "<form action='mainadmin.php' method='get'>\n";
    strprint += "Valitse suodatin: \n";
    strprint += "<select id='newfilter' name='newfilter'>\n";
    <?php
      // Haetaan kaikki luokat
      $tags = getAvailableTags($con);
      foreach ($tags as $tag) {
        echo "strprint += \"<option value='".$tag."'>".$tag."</option>\\n\";\n";
      }
    ?>
    strprint += "</select>\n";
    strprint += "<input type='submit' value='OK' />\n";
    strprint += "<input type='button' onclick='cancelPrompt()' value='Peruuta' />\n";
    strprint += "</form><div class='erottaja'>&nbps;</div>\n";
    document.getElementById("toolprompt").innerHTML = strprint;
  }

</script>

<!-- Lisätyökalujen piilottamiseen -->
<script src='hiddentools.js'></script>

<?php
  }
?>

<?php include("header2.php"); ?>

<?php
if ($LOGGED) {

  // Uuden kansion lisääminen
  if (isset($_GET['newfoldername'])) {
    $sql = "INSERT INTO korg_folds(fold_id,fold_name,fold_created,fold_hidden) VALUES(";
    $sql = $sql."DEFAULT,'".$_GET['newfoldername']."','".date("Y-m-d H:i:s")."',1)";

    if (korg_insert($sql, $con) == 0) {
      echo "Virhe! Uutta kansiota ei voitu luoda.";
    }

    // Tyhjennetään attribuutit jotta uutta kansiota ei tehdä jos sivu päivitetään
    emptyGets();
  }

  // Suodattimen lisääminen
  if (isset($_GET['newfilter'])) {
    $_SESSION['filters'] .= " ".$_GET['newfilter'];

    //Tyhjennetään attribuutit jotta samaa suodinta ei lisätä jos sivu päivitetään
    emptyGets();
  }

  // Suodattimen tyhjennys, ei suodatusta
  // Suodattimen lisääminen
  if (isset($_GET['clear'])) {
    if ($_GET['clear'] == '1') {
      $_SESSION['filters'] = "";
    }

    //Tyhjennetään attribuutit jotta suodinta ei tyhjennetä uudestaan jos sivu päivitetään
    emptyGets();
  }

  // Suodattimet
  // Määrävät minkä luokan kansioita näytetään
  $filters_str = $_SESSION['filters'];
  // pidsStringToArray sopii myös tagien palotteluun taulukoksi
  $filters_array = pidsStringToArray($filters_str);

  // Jos suodattimia on määritetty aloitetaan suodatus
  // $filtering toimii enable-arvona lauseille myöhemmin
  $filtering = ($filters_str != "");

  // Jos GET(show) on määritelty
  // Tämä tarkastelu on tällä hetkellä turhaa mutta
  // tulevaisuudessa tämän yhteyteen tulee mahdollisesti
  // muuttujien tarkastelua ja filtteröintiä
  $SHOW = "basic";
  if (isset($_GET['show'])) {
    if ($_GET['show'] == "system") $SHOW = "system";
    if ($_GET['show'] == "all") $SHOW = "all";
  }

  echo "<h1>Kuvakansiot -";
  // Jos suodattimia on määritetty, tulostetaan ne otsikkoon
  if ($filtering) {
    foreach ($filters_array as $filter) {
      echo " ".$filter;
    }
  } else {
    echo " Kaikki";
  }
  if ($SHOW == "system") echo " - Systeemikansiot";
  echo "</h1>\n";


  // Muokkaustyökalut
  echo "<div class='linkrow'>\n";
  echo "[<a onclick='showNewFolderPrompt()'>Lisää kansio</a>]\n";
  echo "[<a onclick='showAddFilterPrompt()'>Lisää suodatin</a>]\n";
  if ($filtering) echo "[<a href='mainadmin.php?clear=1'>Tyhjennä suodatukset</a>]\n";
  echo " - [<a href='http://www.kohteet.org:2082/'>cPanel-hallinta</a>]\n";
  echo hiddentoolsStart();
  echo "[<a href='mainadmin.php?show=system'>Näytä systeemikansiot</a>]\n";
  echo "[<a href='mainadmin.php?show=basic'>Näytä peruskansiot</a>]\n";
  echo "[<a href='mainadmin.php?show=all'>Näytä kaikki</a>]\n";
  echo hiddentoolsEnd();
  echo "</div>\n";
  echo "<div id='toolprompt'></div>\n";

  // Listataan kaikki kansiot filttereitä noudattaen.
  $sql = "";
  if ($filtering) {
    $sql = "SELECT folds.fold_id,folds.fold_name,folds.fold_hidden ";
    $sql .= "FROM korg_folds AS folds, ";
    $sql .= "(SELECT fold_id FROM korg_tags_folds WHERE tag IN (";

    // Liitetään tagit sql-lauseeseen
    $filtercount = count($filters_array);
    for ($i = 0; $i < $filtercount; $i++) {
      if ($i != 0) $sql .= ",'".$filter."'";
      else $sql .= "'".$filter."'";
    }

    $sql .= ")) AS tags ";
    $sql .= "WHERE folds.fold_id = tags.fold_id ";
    $sql .= "ORDER BY folds.fold_id DESC";
  } else {
    if ($SHOW == "basic") { // Näytetään peruskansiot (ei systeemikansioita)
      $sql = "SELECT fold_id,fold_name,fold_hidden FROM korg_folds WHERE fold_system=0 ORDER BY fold_id DESC";
    } else {
      if ($SHOW == "system") { // Näytetään vain systeemikansiot
        $sql = "SELECT fold_id,fold_name,fold_hidden FROM korg_folds WHERE fold_system=1 ORDER BY fold_id DESC";
      } else {
        if ($SHOW == "all") { // Näytetään perus- ja systeemikansiot (eli kaikki)
          $sql = "SELECT fold_id,fold_name,fold_hidden FROM korg_folds ORDER BY fold_id DESC";
        } else {
          // Sama kuin $SHOW == "basic" eli näytetään vain peruskansiot.
          $sql = "SELECT fold_id,fold_name,fold_hidden FROM korg_folds WHERE fold_system=0 ORDER BY fold_id DESC";
        }
      }
    }
  }

  // Suoritetaan muodostettu kysely
  $rows = korg_get_rows($sql, $con);
  $rowcount = count($rows);

  // Navigaatioon tarvittavia
  // $first kertoo kuinka mones kyselystä palautunut objekti näytetään
  // $amount kertoo kuinka monta objektia tällä sivulla näytetään
  $first = 0;
  $amount = ADMIN_FOLDERS_PER_PAGE;
  if (isset($_GET["first"])) $first = (int)$_GET["first"];
  if (isset($_GET["amount"])) $amount = (int)$_GET["amount"];
  // Näiden lisäksi on saattaa olla annettu esim $_GET['fid']
  // joka on sen objektin id, jonka tulee näkyä sivulla.
  if (isset($_GET["fid"])) {
    // Tarkistetaan GET['fid'] tietoturvan vuoksi
    $fid = sanitizeId($_GET['fid']);

    // Etsitään kuinka mones annettu kansio on.
    for ($i = 0; $i < $rowcount; $i++) {
      $row = $rows[$i];
      if ($row['fold_id'] == $fid) {
        $first = $i - ($i % $amount);
        break;
      }
    }
  }

  if ($rowcount > 0) {

    // Kuvanavigaatio. Esim [1-9]
    echo "<div class='linkrow top none'>\n";
    for ($i = 0; $i < $rowcount; $i = $i + $amount) {
      if ($i == $first) { // Jos ollaan kyseisessä sivulla niin ei tehdä linkkiä
        echo "<b>[".($i + 1)."-";
        if ($i + $amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i + $amount;
        echo "]</b>\n";
      } else {
        echo "[<a href='mainadmin.php?first=".$i."'>".($i + 1)."-";
        if ($i + $amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i + $amount;
        echo "</a>]\n";
      }
    }
    echo "</div>\n";

    echo "<div class='itembrowser'>\n\n";
    for ($i = 0; $i < ($rowcount - $first) && $i < $amount; $i++) {
      $item = $rows[$first + $i];
      echo "<div class='browseritem'>\n";
      echo "[<a href='foldadmin.php?fid=".$item['fold_id']."' >";
      echo $item['fold_name'];
      echo "</a>]";
      if ($item['fold_hidden'] == "1") echo " (piilotettu)";
      echo "<br/>\n";

      echo "<a href='foldadmin.php?fid=".$item['fold_id']."' >";
      echo "<img ";
      if ($item['fold_hidden'] == "1") echo "class='hiddenimage' ";
      echo "src='";

      // Jos indeksikuvaa ei vielä ole, niin näytetään oletuskuva
      $indexsrc = getIndexImageThumb($item['fold_id'], $con);
      if ($indexsrc != "") echo $indexsrc;
      else echo EMPTY_FOLDER_SRC;

      echo "' alt='".$item['fold_name']."' />";
      echo "</a>\n</div>\n\n";

      // Lopetetaan kelluminen joka kolmannella rivillä
      if (($i+1) % 3 == 0 || ($i+1) == ($rowcount - $first) || ($i+1) == $amount) echo "<br class='stopfloat'/>\n\n";

    }

    echo "</div>\n\n";

    // Kuvanavigaatio. Esim [1-9]
    echo "<div class='linkrow bottom none'>\n";
    for ($i = 0; $i < $rowcount; $i = $i + $amount) {
      if ($i == $first) { // Jos ollaan kyseisessä sivulla niin ei tehdä linkkiä
        echo "<b>[".($i + 1)."-";
        if ($i + $amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i + $amount;
        echo "]</b>\n";
      } else {
        echo "[<a href='mainadmin.php?first=".$i."'>".($i + 1)."-";
        if ($i + $amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i + $amount;
        echo "</a>]\n";
      }
    }
    echo "</div>\n";

  } else {
    echo "<div class='count'>Yhteensä 0 kansiota.</div>\n";
    printSeparator();
  }

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
