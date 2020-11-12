<?php

// Imaging. Thanks for zorroswordsman @ php.net
class Imaging {

    // Variables
    private $img_input;
    private $img_output;
    private $img_src;
    private $format;
    private $quality = 80;
    private $x_input;
    private $y_input;
    private $x_output;
    private $y_output;
    private $resize = FALSE;

    // Set image
    public function setImg($img) {

        // Find format
        $ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION));

        // JPEG image
        if (is_file($img) && ($ext == "JPG" OR $ext == "JPEG")) {

            $this->format = $ext;
            $this->img_input = ImageCreateFromJPEG($img);
            $this->img_src = $img;

        }

        // PNG image
        elseif (is_file($img) && $ext == "PNG") {

            $this->format = $ext;
            $this->img_input = ImageCreateFromPNG($img);
            $this->img_src = $img;

        }

        // GIF image
        elseif (is_file($img) && $ext == "GIF") {

            $this->format = $ext;
            $this->img_input = ImageCreateFromGIF($img);
            $this->img_src = $img;

        }

        // Get dimensions
        $this->x_input = imagesx($this->img_input);
        $this->y_input = imagesy($this->img_input);

    }

    // Set maximum image size (pixels)
    public function setSize($size = 100) {

        // Resize
        if ($this->x_input > $size || $this->y_input > $size) {

            // Wide
            if ($this->x_input >= $this->y_input) {

                $this->x_output = $size;
                $this->y_output = ($this->x_output / $this->x_input) * $this->y_input;

            }

            // Tall
            else {

                $this->y_output = $size;
                $this->x_output = ($this->y_output / $this->y_input) * $this->x_input;

            }

            // Ready
            $this->resize = TRUE;

        }

        // Don't resize
        else { $this->resize = FALSE; }

    }

    // Set image quality (JPEG only)
    public function setQuality($quality) {

        if (is_int($quality)) {

            $this->quality = $quality;

        }

    }

    // Save image
    public function saveImg($path) {

        // Resize
        if ($this->resize) {

            $this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output);
            ImageCopyResampled($this->img_output, $this->img_input, 0, 0, 0, 0, $this->x_output, $this->y_output, $this->x_input, $this->y_input);

        }

        // Save JPEG
        if ($this->format == "JPG" OR $this->format == "JPEG") {

            if ($this->resize) { imageJPEG($this->img_output, $path, $this->quality); }
            else { copy($this->img_src, $path); }

        }

        // Save PNG
        elseif ($this->format == "PNG") {

            if ($this->resize) { imagePNG($this->img_output, $path); }
            else { copy($this->img_src, $path); }

        }

        // Save GIF
        elseif ($this->format == "GIF") {

            if ($this->resize) { imageGIF($this->img_output, $path); }
            else { copy($this->img_src, $path); }

        }

    }

    // Get width
    public function getWidth() {

        return $this->x_input;

    }

    // Get height
    public function getHeight() {

        return $this->y_input;

    }

    // Clear image cache
    public function clearCache() {

        @ImageDestroy($this->img_input);
        @ImageDestroy($this->img_output);

    }

}


// Funktio removePictureFiles: Poistaa kuvaan liittyvät kuvatiedostot palvelimelta
// Parametrit: Kuvan id-numero ja MySQL-yhteys
// Palauttaa true jos poisto onnistui, muutoin false;
function removePictureFiles($pic_id,$con) {
  // Haetaan poistettavan kuvan kuvatiedostot
  // Niitä saattaa olla kolme: alkuperäinen, optimoitu ja pikkukuva
  $pic_data = getPictureData($pic_id, $con);

  // Suoritetaan tiedoston palvelimelta poistava funktio
  // Jos tiedostoa ei löydy niin functio palauttaa arvon true.
  if (removeResource($pic_data['pic_src'])
  && removeResource($pic_data['pic_orig'])
  && removeResource($pic_data['pic_thumb'])) {
    return true;
  }

  return false;
}

// Funktio addPictureFiles: Lisää kuvaan liittyvät kuvatiedostot palvelimelle.
// Tekee upatusta kuvasta kaksi tai kolme erikokoista versiota.
// Parametrit:
// $_FILES muodossa oleva lähdetiedosto,
// BOOLEAN tallennetaanko optimoidun ja pikkukuvan lisäksi alkuperäinen
// INT uppauskansion id-numero.
// Palauttaa taulukon, jossa uusien kuvien hakemistot.
// Palauttaa FALSE, jos uppiminen ei onnistu
function addPictureFiles($source,$saveorig,$fold_id) {

  // Luodaan kansio, johon kuva upataan
  // Kansiot nimetään FID-numeron mukaan (folder id)
  // Esimerkiksi images/upload/fid23
  createImageFolder($fold_id);
  // echo getError(); // Tulostetaan mahdollinen virhe

  // Väliaikaistiedosto
  $tempfile = "";

  // Kohdetiedosto
  $targetfile = "";

  // Alkuperäisen kaltainen tiedosto
  $origfile = "";

  // Thumbnail-tiedosto
  $thumbfile = "";

  // Tallennetaanko alkuperäinen
  //$saveorig = FALSE;
  //if ($_POST['saveorig'] == "1") $saveorig = TRUE;

  // Lisätään kuvat
  if (($source["file"]["error"] == 0)
  && ($source["file"]["size"] < MAX_FILE_SIZE)
  && (($source["file"]["type"] == "image/gif")
  || ($source["file"]["type"] == "image/jpeg")
  || ($source["file"]["type"] == "image/png")
  || ($source["file"]["type"] == "image/pjpeg"))) {

    // Väliaikaistiedosto
    $tempfile = UPLOAD_DIRECTORY."temp/".$source["file"]["name"];
    @move_uploaded_file($source["file"]["tmp_name"], $tempfile) or die("Väliaikaistiedoston luonti epäonnistui.");

    // Kohdetiedosto: $targetfile
    $targetfile = UPLOAD_DIRECTORY."fid".$fold_id."/".$source["file"]["name"];
    $pathinfo = pathinfo($targetfile);
    $plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
    if (file_exists($targetfile)) { // Jos tämän niminen tiedosto on jo olemassa
      $exists_number = 2;
      do {
        $targetfile = $pathinfo['dirname']."/".$plainname."-".$exists_number.".".$pathinfo['extension'];
        $exists_number++;
      } while(file_exists($targetfile));
    }

    // Alkuperäinen tiedosto: $origfile
    if ($saveorig) {
      $pathinfo = pathinfo($targetfile);
      $plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
      $origfile = $pathinfo['dirname']."/".$plainname."-full.".$pathinfo['extension'];
      if (file_exists($origfile)) { // Jos tämän niminen tiedosto on jo olemassa
        $exists_number = 2;
        do {
          $origfile = $pathinfo['dirname']."/".$plainname."-".$exists_number."-full.".$pathinfo['extension'];
          $exists_number++;
        } while(file_exists($origfile));
      }
    }

    // Thumbnail-tiedosto: $thumbfile
    $pathinfo = pathinfo($targetfile);
    $plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
    $thumbfile = $pathinfo['dirname']."/".$plainname."-thumb.".$pathinfo['extension'];
    if (file_exists($thumbfile)) { // Jos tämän niminen tiedosto on jo olemassa
      $exists_number = 2;
      do {
        $thumbfile = $pathinfo['dirname']."/".$plainname."-".$exists_number."-thumb.".$pathinfo['extension'];
        $exists_number++;
      } while(file_exists($thumbfile));
    }


    // Tehdään kuvasta 640px kokoinen optimoitu katselukuva
    // ja samantien 200px kokoinen thumbnail
    $image = new Imaging;
    $image->setImg($tempfile);
    $image->setQuality(88);

    // Tallennetaan alkuperäinen haluttaessa
    if ($saveorig) {
      $image->saveImg($origfile);
      chmod($origfile, 0644);
    }

    // Tallennetaan optimoitu
    $image->setSize(640);
    $image->saveImg($targetfile);
    chmod($targetfile, 0644);

    // Tallennetaan thumbnail
    $image->setSize(200);
    $image->setQuality(80);
    $image->saveImg($thumbfile);
    chmod($thumbfile, 0644);

    // Tyhjennetään objekti
    $image->clearCache();

    // Poistetaan väliaikainen tiedosto
    @unlink($tempfile);

    // Kirjoitetaan uppaustiedot lokiin
    $aika  = date("j.m-Y, H:i:s");
    $tolog = ($aika." ||| ".$targetfile." ||| ".$source["file"]["size"]."t ||| ".$source["file"]["type"]."\n");
    $tolog .= ($aika." ||| ".$thumbfile." ||| ".$source["file"]["size"]."t ||| ".$source["file"]["type"]."\n");
    if ($saveorig)
      $tolog .= ($aika." ||| ".$origfile." ||| ".$source["file"]["size"]."t ||| ".$source["file"]["type"]."\n");
    $log   = @fopen(UPLOAD_DIRECTORY."uploadlog.txt", "a");

    @fwrite($log, $tolog);
    @fclose($log);

    // Palautetaan kuvahakemistot sisältävä taulukko
    return array("tempfile"=>$tempfile,"optimized"=>$targetfile,"thumbnail"=>$thumbfile,"original"=>$origfile);

  }

  return FALSE;
}
