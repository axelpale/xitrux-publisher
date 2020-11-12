<?php include("header1.php"); ?>

<?php
  // Alustetaan käyttäjätiedot
  if(isset($_SESSION['logged'])) {
    unset($_SESSION['logged']);
    unset($_SESSION['userid']);
    unset($_SESSION['user']);
    $LOGGED = false; // Määritellään header1.phpssa
  }
?>

<?php include("header2.php"); ?>

<?php

  if(!isset($_SESSION['logged'])) {
    echo "<h1>Uloskirjautuminen onnistui</h1>\n";
    echo "<div class='linkrow bottom'>\n";
    echo "[<a href='main.php'>etusivulle</a>] - \n";
    echo "[<a href='login.php'>kirjaudu uudelleen</a>]\n";
    echo "</div>\n";
  } else {
    echo "<h1>Uloskirjautuminen epäonnistunut</h1>";
    echo "<div class='linkrow bottom'>\n";
    echo "On tapahtunut virhe. Ole ystävällinen ja ilmoita asiasta webmasterille akseli.palen@hotmail.com.";
    echo "</div>\n";
  }

?>

<?php include("footer.php"); ?>
