<?php

  // Käynnistetään sessio kirjautumista varten
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  // Käyttäjän statuksen tarkastaminen
  $LOGGED = false;
  if($_SESSION['logged'] == "logged") $LOGGED = true;

  if($LOGGED) {

    include("admin-functions.php");

    $con = korg_connect();

    // fid = fold_id = kansion id-numero
    // Tarkistetaan GET['fid'] tietoturvan vuoksi
    $fid = sanitizeId($_GET['fid']);
    $pid = sanitizeId($_GET['pid']);
    $nfid = sanitizeId($_POST['newfolder']);

    if($nfid > 0 && $fid > 0 && $pid > 0) {

      removeFromFolder($fid, $pid, $con);

      addToFolder($nfid, $pid, $con);

      updatePictureFolder($pid, $nfid, $con);

    }

    mysql_close($con);

  }

  header( 'Location: picadmin.php?fid='.$nfid.'&pid='.$pid );
?>
