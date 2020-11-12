<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php

if($LOGGED) {

  $sql = "SELECT times_logged FROM korg_users WHERE user_id=".$_SESSION['userid'];
  $result = mysql_query($sql, $con);
  $row = mysql_fetch_array($result);

  echo "<h1>Terve ".$_SESSION['user'].", kirjautumisesi onnistui!</h1>\n";
  echo "<div class='linkrow bottom'>\n";
  echo "Tämä on ".$row['times_logged'].". kirjautumiskertasi kohteet.orgiin.\n";
  echo "</div>\n";
} else {
  if(isset($_SESSION['fails'])) $_SESSION['fails'] = $_SESSION['fails'] + 1;
  else $_SESSION['fails'] = 1;

  // Nukutaan aika joka riippuu exponentiaalisesti epäonnistuneiden kirjautumiskertojen lukumäärästä
  // Tämä siis ainoastaan virheellisen kirjautumisen yhteydessä
  sleep(8*$_SESSION['fails']);

  if($_SESSION['fails'] < 5) {
    echo "<h1>Kirjautuminen epäonnistunut</h1>\n";
    echo "<div class='linkrow bottom'>\n";
    echo "Käyttäjätunnus tai salasana oli väärä. Yritä [<a href='login.php'>uudelleen</a>].\n";
    echo "</div>\n";
  } else {
    echo "<h1>Kirjautuminen epäonnistunut viiteen kertaan!</h1>\n";
    echo "<div class='linkrow bottom'>\n";
    echo "Jos sinulla on tarvetta hallinnoida sivustoa ota yhteys webmasteriin.<br/>\n";
    echo "</div>\n";
  }
}

?>

<?php include("footer.php"); ?>
