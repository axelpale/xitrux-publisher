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
    include("imagetools.lib.php");

    $con = korg_connect();

    // fid = fold_id = kansion id-numero
    // Tarkistetaan GET['fid'] tietoturvan vuoksi
    $fid = sanitizeId($_GET['fid']);
    $pid = sanitizeId($_GET['pid']);

    // Haetaan kuvan sijainti
    $filename = getImageSrc($pid, $con);

    // Tehdään kuvasta 200px kokoinen thumbnail
    $pathinfo = pathinfo($filename);
    $plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
    $thumbfile = $pathinfo['dirname']."/".$plainname."-thumb.".$pathinfo['extension'];
    if(file_exists($thumbfile)) { // Jos tämän niminen tiedosto on jo olemassa
      $exists_number = 2;
      do {
        $thumbfile = $pathinfo['dirname']."/".$plainname."-".$exists_number."-thumb.".$pathinfo['extension'];
        $exists_number++;
      } while(file_exists($thumbfile));
    }
    $thumb = new Imaging;
    $thumb->setImg($filename);
    $thumb->setQuality(80);
    $thumb->setSize(200);
    $thumb->saveImg($thumbfile);
    $thumb->clearCache();

    // Päivitetään kuvan pic_thumb-tieto
    updatePictureThumb($pid,$thumbfile,$con);

    mysql_close($con);

  }

  header( 'Location: picadmin.php?fid='.$fid.'&pid='.$pid );
?>
