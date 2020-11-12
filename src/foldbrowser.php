<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php

  $filtering = FALSE;

  if(isset($_GET['filters'])) {
    // Suodattimet
    // Määrävät minkä luokan kansioita näytetään
    $filters_str = $_GET['filters'];
    // pidsStringToArray sopii myös tagien palotteluun taulukoksi
    $filters_array = pidsStringToArray($filters_str);

    $filtering = TRUE;
  }

  // Listataan kaikki kansiot filttereitä noudattaen.
  $sql = "";
  if($filtering) {
    $sql = "SELECT folds.fold_id,folds.fold_name,folds.fold_issued ";
    $sql .= "FROM korg_folds AS folds, ";
    $sql .= "(SELECT fold_id FROM korg_tags_folds WHERE tag IN (";

    // Liitetään tagit sql-lauseeseen
    $filtercount = count($filters_array);
    for($i=0; $i < $filtercount; $i++) {
      if($i != 0) $sql .= ",'".$filters_array[$i]."'";
      else $sql .= "'".$filters_array[$i]."'";
    }

    $sql .= ")) AS tags ";
    $sql .= "WHERE folds.fold_id = tags.fold_id AND folds.fold_hidden = 0 ";
    $sql .= "ORDER BY folds.fold_issued DESC";

  } else {
    // Listataan kaikki julkiset kansiot
    $sql = "SELECT fold_id,fold_name,fold_issued FROM korg_folds WHERE fold_hidden=0 ORDER BY fold_issued DESC";
  }

  $result = mysql_query($sql, $con);
  $rowcount = mysql_num_rows($result);

  echo "<h1>Kuvat</h1>\n";
  printSeparator();
  /*
  // Otsikko
  echo "<h1>Kuvat -";
  // Jos suodattimia on määritetty, tulostetaan ne otsikkoon
  if($filtering) {
    foreach($filters_array as $filter) {
      echo " ".$filter;
    }
  } else echo " Kaikki";
  echo "</h1>\n\n";


  // Muokkaustyökalut
  echo "<div class='linkrow'>\n";
  echo "[<a href='foldbrowser.php'>Kaikki</a>]\n";
  echo " - ";
  echo "[<a href='foldbrowser.php?filters=UE'>UE</a>]\n";
  echo "[<a href='foldbrowser.php?filters=Forssa'>Forssa</a>]\n";
  echo "[<a href='foldbrowser.php?filters=Tampere'>Tampere</a>]\n";
  echo " - ";
  echo "[<a href='foldbrowser.php?filters=2007'>2007</a>]\n";
  echo "[<a href='foldbrowser.php?filters=2008'>2008</a>]\n";
  echo "[<a href='foldbrowser.php?filters=2009'>2009</a>]\n";
  echo "</div>\n";
  */


  // Kansiot
  if($rowcount != 0) {

    echo "<div class='itembrowser public'>\n\n";
    for($i=0; $i<$rowcount; $i++) {

      $item = mysql_fetch_array($result);
      echo "<div class='browseritem public'>\n";
      echo "<a class='thumb' href='picbrowser.php?fid=".$item['fold_id']."'>";
      echo "<img src='";

      // Haetaan kuvan tiedot
      $indeximage = getPictureData(getIndexImagePid($item['fold_id'],$con),$con);

      // Jos indeksikuvaa ei vielä ole, niin näytetään oletuskuva
      $indexsrc = validateSrc($indeximage['pic_thumb'],$indeximage['pic_src']);
      if($indexsrc != PIC_NOT_FOUND) echo $indexsrc;
      else echo EMPTY_FOLDER_SRC;
      echo "' alt='".$item['fold_name']."' />\n</a>\n";

      echo "<h2>[<a href='picbrowser.php?fid=".$item['fold_id']."'>";
      echo $item['fold_name']."</a>]</h2>\n";

      echo "<div class='issued'>\n";
      echo "Julkaistu ".getCleanDate($item['fold_issued'])." - ";
      echo printFolderTags($item['fold_id'],$con);
      echo "</div>\n";

      echo "<p>\n".nl2br($indeximage['pic_caption']);
      echo "</p>\n</div>\n\n";

    }
    echo "</div>\n\n";

    echo "<br class='stopfloat'/>\n\n";
  }

  echo "<span>Yhteensä ".$rowcount." kansiota.</span>\n";

  printSeparator();

?>

<?php include("footer.php"); ?>
