<?php

// Tämä skripti paljastaa kuvakansion julkisesti.

#############################
## SCRIPTIN VAKIOT

// Hyväksyttävä päivä
// Skiptin ajaminen sallitaan tämän kellonajan jälkeen
// Aika on muodossa YYYY-MM-DD HH:MM:SS
$RUN_TIME = "2009-05-04 00:00:00";

// Kuvakansion ID-numero
$FOLD_ID = 48; // 48 eli Lähetinasema

// Ilmoitus skritin ajon jälkeen
$RUN_MSG = "Kansio on nyt julki.<br/>";

############################

// Funktio joka yhdistää MySQL-tietokantaan
function korg_connect() {
  // MySQL-yhteys
  $con = @mysql_connect("localhost", DB_USER, DB_PASS);
  if (!$con) { die('Could not connect: '.mysql_error()); }
  @mysql_select_db(DB_NAME, $con);
  @mysql_query("SET NAMES 'utf8'"); // Yhteys UTF8

  return $con;
}

// Paljastaa kansion julkiselta puolelta eli muuttaa fold_hidden arvoa
function releaseFolder($fold_id, $con) {

  // Tarkistetaan onko kansio jo julkaistu
  $sql = "SELECT fold_hidden hidden FROM korg_folds WHERE fold_id=".$fold_id;
  $result = @mysql_query($sql, $con);
  $res_array = @mysql_fetch_array($result);
  $released = $res_array['hidden'];

  // Jos released == 1, kansio on piilossa
  if( $released == 1 ) {

    // Päivitetään kansion julkaisuaika
    $sql = "UPDATE korg_folds SET fold_hidden=0,fold_issued='".date("Y-m-d H:i:s")."' WHERE fold_id=".$fold_id;
    if(!@mysql_query($sql, $con)) return false;

    // Päivitetään sivuston päivitysaika
    $sql = "UPDATE korg_site SET site_update='".date("Y-m-d H:i:s")."' WHERE site_id=1";
    if(!@mysql_query($sql, $con)) return false;

    return true;
  }

  // Jos released == 0, kansio on jo julkistettu
  if( $released == 0 ) {
    return true;
  }

  // Jos released on jotain muuta
  return false;
}

// Convert datetime to timestamp
list($date, $time) = explode(' ', $RUN_TIME);
list($year, $month, $day) = explode('-', $date);
list($hour, $minute, $second) = explode(':', $time);
$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

// Testataan onko tämän hetkinen aika suurempi
if( time() >= $timestamp ) {

  // Ajettava skripti
  $con = korg_connect();
  if( releaseFolder($FOLD_ID, $con) ) {
    // Ilmoitus skriptin onnistuneesta ajamisesta
    echo "Skripti ajettu.<br/>\n";
    echo $RUN_MSG;
  } else {
    // Ilmoitus skriptin epäonnistumisesta
    echo "Skriptin ajo epäonnistui.<br/>\nKoodissa annetut parametrit ";
    echo "eivät täsmää tietokantaan tai yhteyden muodostaminen ei onnistunut.<br/>";
  }

} else {
  // Ilmoitus skriptin epäonnistuneesta ajosta
  echo "Skriptiä EI ajettu.<br/>\n";
  echo "Skriptin ajaminen on sallittua vasta ".$RUN_TIME." tai sen jälkeen.<br/>\n";
  echo "Palvelimen kello on tällä hetkellä ".date("Y-m-d H:i:s").".<br/>\n";
}

?>
