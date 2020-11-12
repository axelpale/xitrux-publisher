<?php

  // Lisätään tarvittavat funkkarit
  include("public-functions.php");

  //Avataan yhteys
  $con = korg_connect();

  // GET: tag fid

  // Poistetaan kansiosta fid luokka tag
  $sql = "DELETE FROM korg_tags_folds WHERE tag='".$_GET['tag']."' AND fold_id=".$_GET['fid'];
  korg_delete($sql, $con);

  // Tulostetaan saatu luokittelu poistolinkkien kanssa
  $sql = "SELECT tag FROM korg_tags_folds WHERE fold_id=".$_GET['fid'];
  $rows = korg_get_rows($sql, $con);

  if (count($rows) == 0) {
    echo " ei luokittelua";
  } else {
    foreach ($rows as $tag) {
      echo " ".$tag['tag']."<a class='del' onclick='eraseTag(";
      echo '"'.$tag['tag'].'",'.$_GET['fid'];
      echo ")' style='font-size: small'>[poista]</a>";
    }
  }

  // Suljetaan yhteys
  $con = null;
