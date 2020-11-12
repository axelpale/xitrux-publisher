<?php

  // Käynnistetään sessio kirjautumista varten ////Huom! Sessio saattaa jäädä päälle vaikka selaimen sulkee(?)
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  // Käyttäjän statuksen tarkastaminen
  $LOGGED = false;
  if ($_SESSION['logged'] == "logged") $LOGGED = true;

  if ($LOGGED) {

    include("admin-functions.php");

    $con = korg_connect();

    // fid = fold_id = kansion id-numero
    // Tarkistetaan GET['fid'] tietoturvan vuoksi
    $fid = sanitizeId($_GET['fid']);
    $pid = sanitizeId($_GET['pid']);

    // Tietojen päivittäminen
    movePictureTop($fid,$pid,$con);

    // Close connection
    $con = null;
  }

  header( 'Location: foldadmin.php?fid='.$fid.'#'.$pid );
?>
