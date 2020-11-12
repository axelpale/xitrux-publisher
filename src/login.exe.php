<?php

  // Käynnistetään sessio kirjautumista varten
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  $con = korg_connect();

  $alphabet = "abcdefghijklmnopqrstuvxyzåäöABCDEFGHIJKLMNOPQRSTUVXYZÅÄÖ-_^1234567890";

  if ($_SESSION['fails'] < 5) {

    if (strspn($_POST['username'], $alphabet) == strlen($_POST['username'])) {
      if (strspn($_POST['password'], $alphabet) == strlen($_POST['password'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM korg_users WHERE korg_name='".$username."' ";
        $sql .= "AND korg_code=AES_ENCRYPT('".$password."','315420v4')";
        $row = korg_get_row($sql, $con);

        if ($row !== false) {
          $_SESSION['userid'] = $row['user_id'];
          $_SESSION['user'] = $row['korg_name'];
          $_SESSION['logged'] = true;
          $_SESSION['fails'] = 0; // Virheellisten kirjautumisten määrä
          $_SESSION['filters'] = ""; // Asettaa kansiohallinnan suodatusoletuksen
          $_SESSION['lastupload'] = ""; // Viimeisimmän lisätyn kuvan hakemisto

          // Increase times logged in
          $sql = "UPDATE korg_users SET times_logged=(times_logged+1), prev_login='".date("Y-m-d H:i:s")."' ";
          $sql = $sql."WHERE user_id=".$row['user_id'];
          korg_update($sql, $con);
        }
      }
    }
  }

  // Close connection
  $con = null;

  header( 'Location: logged.php');
?>
