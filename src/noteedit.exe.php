<?php

  // Käynnistetään sessio kirjautumista varten ////Huom! Sessio saattaa jäädä päälle vaikka selaimen sulkee(?)
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  // Käyttäjän statuksen tarkastaminen
  $LOGGED = false;
  if (isset($_SESSION['logged'])) $LOGGED = true;

  if ($LOGGED) {

    include("admin-functions.php");

    $con = korg_connect();

    // nid = note_id = muistiinpanon id-numero
    // Tarkistetaan GET['nid'] tietoturvan vuoksi
    $nid = sanitizeId($_GET['nid']);

    // Tietojen päivittäminen
    updateNoteBody($nid, $_POST['newnotebody'], $con);

    // Close connection
    $con = null;
  }

  header( 'Location: project.php#'.$nid );
?>
