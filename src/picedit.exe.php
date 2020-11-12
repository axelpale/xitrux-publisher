<?php

  // Käynnistetään sessio käyttäjätunnistusta varten
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
    // Tarkistetaan tuodut tiedot tietoturvan vuoksi
    $fid = sanitizeId($_GET['fid']);
    $pid = sanitizeId($_GET['pid']);

    // Kuvan nimen ja kuvatekstin päivittäminen
    if ($pid >= 0) {
      if (!updatePictureName($pid, $_POST['newpicname'], $con))
        echo "Kuvan nimen vaihtaminen epäonnistui.<br/>\n";
      if (!updatePictureLink($pid, $_POST['newpiclink'], $con))
        echo "Lisämateriaalin vaihtaminen epäonnistui.<br/>\n";
      if (!updatePictureCaption($pid, $_POST['newcaption'], $con))
        echo "Kuvatekstin vaihtaminen epäonnistui.<br/>\n";
    }

    // Close connection
    $con = null;
  }

  header( 'Location: foldadmin.php?fid='.$fid.'&pid='.$pid );
