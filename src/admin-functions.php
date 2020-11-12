<?php

include_once("db.php");

#######################################################
# Sivuston hallintaosiossa tarvittavia php-funktioita #
#######################################################
#######################################################

#########################
# Vakioita ja muuttujia #
#########################

// Virhekoodit
define("DEFAULT_VALUE",       0);
define("DIR_SUCCESS",         1);
define("DIR_EXISTS" ,        11);
define("DIR_CHMOD_ERROR",    13);
define("DIR_CREATION_ERROR", 10);

// Viimeisin virhekoodi
$latest_error = DEFAULT_VALUE;

// Viimeisin upload virheilmoitus
// Tämän voisi tehdä myös virhekoodeina
$latest_upload_message = "";

// Upattavan tiedoston suurin sallittu koko (tavua)
define("MAX_FILE_SIZE", 5000000); // 5 MB

// Uppaushakemisto, eli paikka, johon kuvat upataan.
define("UPLOAD_DIRECTORY", "images/upload/");

################################
# Kansion pids-kentän päivitys #
################################

// Päivitetään kansion pids-tieto, jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePids($fold_id, $pids, $con) {
  $sql = "UPDATE korg_folds SET pids=\"".$pids."\" WHERE fold_id=".$fold_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

##########################################################
# Kansion pids-kentän päivitys array- eli jonomuotoisena #
##########################################################

// Päivitetään kansion pids-tieto, jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePidsArray($fold_id, $pids_array, $con) {
  $pids_string = pidsArrayToString($pids_array);
  if (!updatePids($fold_id, $pids_string, $con)) return false;
  return true;
}

########################################################
# Kansion fold_name-kentän päivitys eli nimen päivitys #
########################################################

// Päivitetään kansion nimi, jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updateFolderName($fold_id, $newname, $con) {
  $sql = "UPDATE korg_folds SET fold_name=\"".$newname."\" WHERE fold_id=".$fold_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

############################################
# Kuvan nimen eli pic_name-kentän päivitys #
############################################

// Päivitetään kuvan nimi, jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePictureName($pic_id, $newname, $con) {
  $sql = "UPDATE korg_pics SET pic_name=\"".$newname."\" WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

########################################################
# Kuvan tiedostohakemiston eli pic_src-kentän päivitys #
########################################################

// Päivitetään kuvan tiedostohakemisto. Jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePictureSrc($pic_id, $newsrc, $con) {
  $sql = "UPDATE korg_pics SET pic_src=\"".$newsrc."\" WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

############################################
# Kuvan kaikkien kuvatietojen päivitys eli #
# pic_src, pic_thumb, pic_orig #############
# Palauttaa TRUE jos päivitys onnistui #####
# muulloin FALSE ###########################
############################################

function updatePicturePaths($pic_id, $newsrc, $newthumb, $neworig, $con) {
  $sql = "UPDATE korg_pics SET pic_src=\"".$newsrc."\", pic_thumb=\"".$newthumb."\", pic_orig=\"".$neworig."\"  WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

###############################################################
# Kuvan lisämateriaalihakemiston eli pic_link-kentän päivitys #
###############################################################

// Päivitetään kuvan lisämateriaalihakemisto. Jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePictureLink($pic_id, $newlink, $con) {
  $sql = "UPDATE korg_pics SET pic_link=\"".$newlink."\" WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

################################################################
# Kuvan pikkukuvan, thumbnailin eli pic_thumb-kentän päivitys  #
# Jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE #
################################################################

function updatePictureThumb($pic_id, $newthumb, $con) {
  $sql = "UPDATE korg_pics SET pic_thumb=\"".$newthumb."\" WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

#####################################################
# Kuvan kuvatekstin eli pic_caption-kentän päivitys #
#####################################################

// Päivitetään kuvateksti, jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updatePictureCaption($pic_id, $newcaption, $con) {
  $sql = "UPDATE korg_pics SET pic_caption=\"".$newcaption."\" WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

############################################################
# Kuvan kansiotiedon eli korg_pics.fold_id-kentän päivitys #
# Palauttaa TRUE jos päivitys onnistui #####################
############################################################

function updatePictureFolder($pic_id, $newfolder, $con) {
  $sql = "UPDATE korg_pics SET fold_id=".$newfolder." WHERE pic_id=".$pic_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

########################################################
# Muistiinpanon sisällön eli note_body-kentän päivitys #
# Samalla päivitetään note_edited-kenttä oleellisesti ##
########################################################

// Päivitetään muistiinpanon note_body. Jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updateNoteBody($note_id, $newbody, $con) {
  $sql = "UPDATE korg_notes SET note_body=\"".$newbody."\",note_edited='".date("Y-m-d H:i:s")."' WHERE note_id=".$note_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

####################################################################
# Vaihtaa muistiinpanon merkkausta eli muuttaa note_marked-kenttää #
####################################################################

// Päivitetään muistiinpanon note_marked. Jos päivitys epäonnistui funktio palauttaa FALSE muuten TRUE
function updateNoteMarked($note_id, $marked, $con) {
  $sql = "UPDATE korg_notes SET note_marked=";
  if ($marked == '1') $sql .= "1";
  else $sql .= "0";
  $sql .= " WHERE note_id=".$note_id;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

#######################################
# Poistaa muistiinpanon tietokannasta #
#######################################

// Poistetaan muistiinpano. Jos poisto epäonnistui funktio palauttaa FALSE muuten TRUE
function deleteNote($note_id, $con) {
  $sql = "DELETE FROM korg_notes WHERE note_id=".$note_id;
  if (korg_delete($sql, $con) == 0) return false;
  return true;
}

###############################
# Viimeisimmän virheen selite #
###############################

// Kertoo suomeksi mitä viimeisin virhekoodi tarkoittaa.
function getError() {
  $feedback = "";
  switch($latest_error) {
    case DEFAULT_VALUE: $feedback = ""; break; //"Virheitä ei tapahtunut.\n"; break;
    case DIR_SUCCESS: $feedback = "Hakemiston luominen onnistui.\n"; break;
    case DIR_EXISTS: $feedback = "Hakemisto on jo olemassa.\n"; break;
    case DIR_CHMOD_ERROR: $feedback = "Hakemiston oikeuksien muutos epäonnistui. Hakemisto ei välttämättä toimi FTP-yhteydessä.\n"; break;
    case DIR_CREATION_ERROR: $feedback = "Hakemiston luominen epäonnistui.\n"; break;
    default: $feedback = "Unknown error.\n";
  }
  return $feedback."<br/>\n";
}

###################################
# Viimeisimmän uppauksen ilmoitus #
###################################

function getUploadMessage() {
  global $latest_upload_message;
  return $latest_upload_message;
}

function setUploadMessage($newmessage) {
  global $latest_upload_message;
  $latest_upload_message = $newmessage;
}

#########################################################################
# Funktio, joka luo uuden kansion upload-hakemistoon (UPLOAD_DIRECTORY) #
#########################################################################

// Kansion lisäävä funktio. Jos lisäys onnistuu tai kansio olemassa, palauttaa TRUE muulloin FALSE
function createImageFolder($fold_id) {
  // Luotava hakemisto
  $uusi = UPLOAD_DIRECTORY."fid".$fold_id;

  // Testataan onko hakemisto jo olemassa
  if (is_dir($uusi)) {
    $latest_error = DIR_EXISTS;
    return true;
  } else {
    // Jos hakemisto ei ole olemassa luodaan se.
    if (mkdir($uusi)) {
      if (chmod($uusi, 0777)) {
        $latest_error = DIR_SUCCESS;
        return true;
      } else {
        $latest_error = DIR_CHMOD_ERROR;
        return false;
      }
    } else {
      $latest_error = DIR_CREATION_ERROR;
      return false;
    }
  }
  return false;
}

############################
# Lisää kuvan palvelimelle # // Turha tällä hetkellä
############################

function uploadImage($files, $fold_id) {

  // Ilmoitukset ladataan tähän
  $upload_message = "";

  // Lopullinen tiedostonimi hakemistoineen. Tämä arvo palautetaan
  $newfilename = "";

  // Minne kuvat kopioidaan
  $hakemisto = UPLOAD_DIRECTORY."fid".$fold_id."/";

  if (isset($files)) {
    if ($files["file"]["error"] > 0) {
      $latest_upload_message .= "<h1>Lataus epäonnistui. ";
      //Virhekoodit
      switch($files["file"]["error"]) {
        case 1:
          $upload_message .= "Tiedostokoko ylittää php.ini-tiedostossa määritellyn koon.<br/>\n";
          break;
        case 2:
          $upload_message .= "Tiedostokoko ylittää HTML-lomakkeessa määritellyn koon.<br/>\n";
          break;
        case 3:
          $upload_message .= "Tiedoston siirto keskeytyi. Yritä <a href='picupload.php?fid=".$fold_id."'>uudelleen</a>.";
          break;
        case 4:
          $upload_message .= "Tiedostoa ei annettu. Yritä <a href='picupload.php?fid=".$fold_id."'>uudelleen</a>.";
          break;
        case 6:
          $upload_message .= "Väliaikaishakemistoa ei löydetty.<br/>\n";
          break;
        case 7:
          $upload_message .= "Kohdehakemistoon ei voida kirjoittaa.<br/>\n";
          break;
        case 8:
          $upload_message .= "File upload stopped by extension.<br/>\n";
          break;
        default:
          $upload_message .= "Tunnistamaton virhe. Virhekoodi: ".$files["file"]["error"];
      }
      $upload_message .= "</h1>\n";
    } else
    if (($files["file"]["type"] == "image/gif") // Testataan kuvan tiedostotyyppi
    || ($files["file"]["type"] == "image/jpeg")
    || ($files["file"]["type"] == "image/png")
    || ($files["file"]["type"] == "image/pjpeg")) {
      if ($files["file"]["size"] < MAX_FILE_SIZE) {
        if ($files["file"]["error"] > 0) {
          $upload_message .= "Return Code: " . $files["file"]["error"] . "<br />";
        } else {

          // Alkuperäinen nimi
          $newfilename = $hakemisto . $files["file"]["name"];

          // Jos tiedosto löytyy jo hakemistosta, täytyy uusi tiedosto nimetä toisin
          if (file_exists($newfilename)) {
            $upload_message .= $files["file"]["name"] . " löytyy jo hakemistosta ".$hakemisto.". Vaihdetaan tiedoston nimeä.<br/>\n";
            $acount = 2; // Ensimmäinen luku joka lisätään jos tiedosto löytyy jo hakemistosta

            // Kaksi seuraavaa riviä suoritetaan loopin ulkopuolella, sillä
            // muulloin tiedostot nimetään esim kiss-2-3-4.jpg
            $pathinfo = pathinfo($newfilename);
            $plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
            do {
              $newfilename = $hakemisto .$plainname."-".$acount.".".$pathinfo['extension'];
              $acount++;
            } while(file_exists($newfilename));
          }

          // Siirretään tiedosto väliaikaiskansiosta lopulliseen kansioon
          move_uploaded_file($files["file"]["tmp_name"], $newfilename);
          $upload_message .= "Tallenettu: " . $newfilename ."<br/>\n";

          // Tulostetaan kuvatiedoston tiedot
          $upload_message .= "Alkuperäinen: " . $files["file"]["name"] . "<br />";
          $upload_message .= "Tyyppi: " . $files["file"]["type"] . "<br />";
          $upload_message .= "Koko: " . ($files["file"]["size"] / 1024) . " Kb<br />";
          $upload_message .= "Väliaikaistiedosto: " . $files["file"]["tmp_name"] . "<br />";

          // Muutetaan tiedoston oikeudet siten että tiedosto näkyisi kaikille
          if (chmod($newfilename, 0644))
            $upload_message .= "Kuvatiedostolle oikeudet: 0644<br/>\n";
          else
            $upload_message .= "Oikeuksien muutos epäonnistui. Tiedosto ei välttämättä näy FTP-yhteydessä.<br/>\n";

          // Kirjoitetaan uppaustiedot lokiin
          $aika  = date("j.m-Y, H:i:s");
          $tolog = ($aika . " ||| " . $newfilename . " ||| " . $files["file"]["size"] . "t ||| " . $files["file"]["type"] . "\n");
          $log   = @fopen(UPLOAD_DIRECTORY."uploadlog.txt", "a");

          @fwrite($log, $tolog);
          @fclose($log);

        }
      } else {
        $upload_message .= "<h1>Tiedosto on liian suuri. Maksimikoko on ".MAX_FILE_SIZE." kt.</h1>\n";
      }
    } else $upload_message .= "<h1>Tiedostoa on väärää tiedostotyyppiä. Vain JPEG-, PNG- ja GIF-kuvat kelpaavat.</h1>\n";
  } else $upload_message .= "<h1>Tiedostoa ei annettu. Lataus epäonnistui!</h1>\n";

  setUploadMessage($upload_message);

  return $newfilename;
}

###############################################
# Palauttaa annetun kansion upload-hakemiston #
###############################################

function getUploadDir($fold_id) {
  return UPLOAD_DIRECTORY."fid".$fold_id."/";
}

#####################################################################
# Tietokannassa olevien kuvien pic_id-numerot jonona kansion mukaan #
#####################################################################

// Haetaan tietokannassa olevien kuvien idt ja palautetaan ne jonona
function getPictureIds($fold_id, $con) {
  $sql = "SELECT pic_id FROM korg_pics WHERE fold_id=".$fold_id;
  $rows = korg_get_rows($sql, $con);
  $pictureids = array();

  foreach ($rows as $row) {
    array_push($pictureids, $row['pic_id']);
  }

  return $pictureids;
}

#######################################################################
# Haetaan kansion tietoihin talletetut pic_id-numerot eli pids-kenttä #
# ja pätkitään se jonoksi #############################################
#######################################################################

// Haetaan kansion tiedoissa olevat kuvaidt ja tallennetaan ne toiseen jonoon
function getPidsArray($fold_id, $con) {
  $sql = "SELECT pids FROM korg_folds WHERE fold_id=".$fold_id;
  $row = korg_get_row($sql, $con);
  $pids_raw = array();
  $pids_final = array();

  if ($row !== false) {
    $pids_raw = explode(" ", trim($row['pids']));

    // Jos pids on tyhjä merkkijono niin jonoon tulee yksi alkio "".
    // Poistetaan kaikki tyhjät alkiot ja sijoitetaan tulos muuttujaan $pids_fold_final
    foreach ($pids_raw as $element) {
      if ($element != "" && $element != " ")
        array_push($pids_final, $element);
    }
  }

  return $pids_final;
}

######################################################################
# Muuttaa array-muotoiset pic_id:t välilyöntierotteiseksi stringiksi #
######################################################################

function pidsArrayToString($pids_array) {
  $pids_string = "";

  foreach ($pids_array as $element) {
    if ($element != "" && $element != " ")
      $pids_string .= $element." ";
  }

  return trim($pids_string);
}

######################################################
# Tiedoston/hakemiston palvelimelta poistava funktio #
######################################################

// Sourcecode of the removeResource-function from http://fi.php.net/manual/
// Returns true if removed successfully or not found, false in other cases
function removeResource( $_target ) {

  //file?
  if ( is_file($_target) ) {
    if ( is_writable($_target) ) {
        if ( @unlink($_target) ) {
            return true;
        }
    }
    return false;
  }

  //dir?
  if ( is_dir($_target) ) {
    if ( is_writeable($_target) ) {
        foreach( new DirectoryIterator($_target) as $_res ) {
            if ( $_res->isDot() ) {
                unset($_res);
                continue;
            }

            if ( $_res->isFile() ) {
                removeResource( $_res->getPathName() );
            } elseif ( $_res->isDir() ) {
                removeResource( $_res->getRealPath() );
            }
            unset($_res);
        }

        if ( @rmdir($_target) ) {
            return true;
        }
    }
    return false;
  }

  return true;
}

##################################################
# Poistaa kuvan pic_id:n kansion kentästä 'pids' #
##################################################

function removeFromFolder($fold_id, $pic_id, $con) {
  // Haetaan kansion pids-kenttä
  // Rikotaan pids-kenttä jonoksi

  $pids_array = getPidsArray($fold_id, $con);
  $pids_array_new = array();

  // Poistetaan jonosta se alkio joka == $pic_id
  foreach ($pids_array as $element) {
    if ($element != $pic_id) array_push($pids_array_new, $element);
  }

  // Kootaan jonosta taas string
  $pids_string = pidsArrayToString($pids_array_new);

  // Talletetaan saatu string kansion pids-kentän päälle
  if (updatePids($fold_id, $pids_string, $con)) return true;

  return false;
}

#######################################
# Lisää kansioon yhden pic_id-numeron #
#######################################

function addToFolder($fold_id, $pic_id, $con) {

  // Poistetaan uudesta kansiosta mahdollinen samalla
  // pic_id-numerolla varustettu kuva
  removeFromFolder($fold_id, $pic_id, $con);

  // Vanhat pids-numerot
  $oldpids = getFolderPids($fold_id, $con);

  // Uudet pids-numerot
  $newpids = "";

  // Muodostetaan uusi pids-arvo
  if (strlen($oldpids) < 1) {// jos tyhjä
    $newpids = $pic_id;
  } else {
    $newpids = $oldpids." ".$pic_id;
  }

  // Päivitetään pids-arvo
  if (updatePids($fold_id, $newpids, $con)) return true;

  return false;
}

##################################################
# Siirtää kuvaa kansiossa yhden pykälän ylöspäin #
##################################################

function moveImageUp($fold_id, $pic_index, $con) {
  $pids_array = getPidsArray($fold_id, $con);
  $array_lenght = count($pids_array);

  if ($pic_index != 0 && $pic_index < $array_lenght) {
    $buffer_array = array_splice($pids_array, $pic_index, 1);
    array_splice($pids_array, $pic_index-1, 0, $buffer_array);

    if (!updatePidsArray($fold_id, $pids_array, $con)) return false;
  }

  return true;
}

##################################################
# Siirtää kuvaa kansiossa yhden pykälän alaspäin #
##################################################

function moveImageDown($fold_id, $pic_index, $con) {
  $pids_array = getPidsArray($fold_id, $con);
  $array_lenght = count($pids_array);

  if (($pic_index + 1) > 0 && ($pic_index + 1) < $array_lenght) {
    $buffer_array = array_splice($pids_array, $pic_index, 1);
    array_splice($pids_array, $pic_index+1, 0, $buffer_array);

    if (!updatePidsArray($fold_id, $pids_array, $con)) return false;
  }

  return true;
}

################################
# Siirtää kuvan kansion alkuun #
################################

function movePictureTop($fold_id, $pic_id, $con) {
  $pids_array = getPidsArray($fold_id, $con);
  $array_lenght = count($pids_array);

  $pic_index = array_search($pic_id,$pids_array);

  if ($pic_index >= 0 && $pic_index < $array_lenght) {
    //$buffer_array sisältää yhden alkion eli siirrettävän numeron
    $buffer_array = array_splice($pids_array, $pic_index, 1);
    array_splice($pids_array, 0, 0, $buffer_array);

    if (!updatePidsArray($fold_id, $pids_array, $con)) return false;
  }

  return true;
}

#####################################################################################
# Piilottaa kuvan julkiselta puolelta eli muuttaa kuvan pic_hidden arvoksi TRUE (1) #
#####################################################################################

function hideImage($pic_index, $con) {
  $sql = "UPDATE korg_pics SET pic_hidden=1 WHERE pic_id=".$pic_index;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

#############################################################################
# Paljastaa piilotetun kuvan eli muuttaa kuvan pic_hidden arvoksi FALSE (0) #
#############################################################################

function unhideImage($pic_index, $con) {
  $sql = "UPDATE korg_pics SET pic_hidden=0 WHERE pic_id=".$pic_index;
  if (korg_update($sql, $con) == 0) return false;
  return true;
}

#################################################################################
# Piilottaa/paljastaa kansion julkiselta puolelta eli muuttaa fold_hidden arvoa #
#################################################################################

function setHideFolder($fold_id, $hiding, $con) {
  if ($hiding == "0") {
    // Päivitetään samalla kansion julkaisuaika ja sivuston päivitysaika
    $sql = "UPDATE korg_folds SET fold_hidden=0,fold_issued='".date("Y-m-d H:i:s")."' WHERE fold_id=".$fold_id."; ";
    if (korg_update($sql, $con) == 0) return false;

    $sql = "UPDATE korg_site SET site_update='".date("Y-m-d H:i:s")."' WHERE site_id=1;";
    korg_update($sql, $con);

    return true;
  } else {
    if ($hiding == "1") {
      $sql = "UPDATE korg_folds SET fold_hidden=1 WHERE fold_id=".$fold_id;
      if (korg_update($sql, $con) == 0) {
        return false;
      } else {
        return true;
      }
    }
  }

  return false;
}

############################################################################
# Muuttaa kansion systeemi/normaalikansioksi eli muuttaa fold_system arvoa #
############################################################################

function setSystemFolder($fold_id, $makesys, $con) {
  if ($makesys == "0") {
    $sql = "UPDATE korg_folds SET fold_system=0 WHERE fold_id=".$fold_id;
    if (korg_update($sql, $con) == 0) return false;

    return true;
  } else {
    if ($makesys == "1") {
      $sql = "UPDATE korg_folds SET fold_system=1 WHERE fold_id=".$fold_id;
      if (korg_update($sql, $con) == 0) {
        return false;
      } else {
        return true;
      }
    }
  }

  return false;
}
