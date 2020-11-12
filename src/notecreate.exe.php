<?php

  // Uuden muistiinpanon lisääminen

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

    // nid = note_id = muistiinpanon id-numero
    // Tarkistetaan GET['nid'] tietoturvan vuoksi
    $nid = sanitizeId($_GET['nid']);

    // Muistiinpanon lisääminen
    $sql = "INSERT INTO korg_notes(note_id,note_body,note_created,note_edited) VALUES(";
    $sql = $sql."DEFAULT,'".$_POST['newnotebody']."','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')";

    $rowsInserted = korg_insert($sql, $con)
    if ($rowsInserted == 0) {
      die("Virhe! Uutta muistiinpanoa ei voitu luoda.");
    }

    // Close connection
    $con = null;
  }

  header( 'Location: project.php' );
