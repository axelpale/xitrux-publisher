<?php
  // Lisätään tarvittavat funkkarit
  include("public-functions.php");

  //Avataan yhteys
  $con = korg_connect();

  //GET: tag fid
  // Muutetaan muuttujanimet. Nimet a-loppuisia, jotta eivät
  // sekottuisi muiden samanimisten muuttujien kanssa (ei välttämättä mahdollista)
  // Tarkistetaan GET['fid'] tietoturvan vuoksi
  $fida = sanitizeId($_GET['fid']);

  // Tarkistetaan GET['tag'] tietoturvan vuoksi
  $taga = filter_var($_GET['tag'], FILTER_SANITIZE_STRING);

  //Tarkastetaan onko lisätty tagi jo tietokannassa
  $sql = "SELECT tag FROM korg_tags WHERE tag=";
  $sql .= '"'.$taga.'"';
  $row = korg_get_row($sql, $con);
  if (count($row) == 0) { // jos tagia ei löydy niin lisätään sellainen
    $sql = "INSERT INTO korg_tags(tag) VALUES('".$taga."')";
    $rowsInserted = korg_insert($sql, $con);
    if ($rowsInserted == 0) {
      echo "Luokan lisääminen epäonnistui! \n";
    }
  }

  // Tarkastetaan onko lisättävä tagi jo liitetty kansioon
  $sql = "SELECT tag FROM korg_tags_folds WHERE tag=";
  $sql .= '"'.$taga.'" AND fold_id='.$fida;
  $row = korg_get_row($sql, $con);
  if (count($row) == 0) {
    // Jos tagia ei ole liitetty kansioon niin lisätään liitos.
    // Lisätään kansioon fid luokka tag
    $sql = "INSERT INTO korg_tags_folds(tag,fold_id) VALUES('".$taga."',".$fida.")";
    korg_insert($sql, $con);
  }

  // Tulostetaan luokittelu poistolinkkien kanssa
  $sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$fida;
  $rows = korg_get_rows($sql, $con);
  if (count($rows) == 0) {
    echo " ei luokittelua";
  } else {
    foreach ($rows as $tag) {
      echo " ".$tag['tag']."<a class='del' onclick='eraseTag(";
      echo '"'.$tag['tag'].'",'.$fida;
      echo ")' style='font-size: small'>[poista]</a>";
    }
  }

  // Suljetaan yhteys
  $con = null;
