<?php

  // Käynnistetään sessio kirjautumista varten
  session_start();

  // Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
  include("public-functions.php");

  $con = korg_connect();

  if($_SESSION['fails'] < 5) {

    if(strspn($_POST['username'],"abcdefghijklmnopqrstuvxyzåäöABCDEFGHIJKLMNOPQRSTUVXYZÅÄÖ-_^1234567890") == strlen($_POST['username'])) {
      if(strspn($_POST['password'],"abcdefghijklmnopqrstuvxyzåäöABCDEFGHIJKLMNOPQRSTUVXYZÅÄÖ-_^1234567890") == strlen($_POST['password'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM korg_users WHERE korg_name='".$username."' ";
        $sql .= "AND korg_code=AES_ENCRYPT('".$password."','315420v4')";
        $result = mysql_query($sql, $con);

        if(mysql_num_rows($result) == 1) {
          $row = mysql_fetch_array($result);
          $_SESSION['userid'] = $row['user_id'];
          $_SESSION['user'] = $row['korg_name'];
          $_SESSION['logged'] = "logged";
          $_SESSION['fails'] = 0; // Virheellisten kirjautumisten määrä
          $_SESSION['filters'] = ""; // Asettaa kansiohallinnan suodatusoletuksen
          $_SESSION['lastupload'] = ""; // Viimeisimmän lisätyn kuvan hakemisto

          $sql = "UPDATE korg_users SET times_logged=(times_logged+1), prev_login='".date("Y-m-d H:i:s")."' ";
          $sql = $sql."WHERE user_id=".$row['user_id'];
          mysql_query($sql, $con);

        }
      }
    }
  }

  mysql_close($con);

  header( 'Location: logged.php');
?>
