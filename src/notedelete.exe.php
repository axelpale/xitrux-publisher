<?php

  // Käynnistetään sessio kirjautumista varten ////Huom! Sessio saattaa jäädä päälle vaikka selaimen sulkee(?)
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  // Käyttäjän statuksen tarkastaminen
  $LOGGED = false;
  if($_SESSION['logged'] == "logged") $LOGGED = true;

  if($LOGGED) {

    include("admin-functions.php");

    $con = korg_connect();

    // nid = note_id = muistiinpanon id-numero
    // Tarkistetaan GET['nid'] tietoturvan vuoksi
    $nid = sanitizeId($_GET['nid']);

    // Muistiinpanon poistaminen
    deleteNote($nid,$con);

    mysql_close($con);

  }

  header( 'Location: project.php' );
?>
