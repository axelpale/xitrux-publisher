<?php

// Class for file uploading
class FileUpload {

	// Suurin mahdollinen koko
	private $MAX_UPLOAD_SIZE = 20000000;

	// Upload directory
	private $upload_dir = "";

	// Lokitiedosto
	private $logfile = "";

	// Väliaikaistiedosto
	private $tempfile = "";

	// Tiedoston tiedot
	private $filename = "";
	private $filetype = "";
	private $filesize = "";

	// Kohdetiedosto
	private $targetfile = "";

	// Onko uppaaminen onnistunut
	private $is_success = FALSE;

	// Asettaa hakemiston johon tiedosto siirretään
	// Jollei hakemistoa ole olemassa, tehdään sellainen
	public function setUploadDir($directory) {

		// Lisätään hakemistopolun perään "/" jollei sitä löydy
		if(substr($directory,-1,1) != "/") {
			$directory .= "/";
		}

		// Luodaan hakemisto, jollei se ole olemassa
		if($this->createDir($directory)) {
			$this->upload_dir = $directory;
			return true;
		}

		// Jos homma kusee
		return false;		
	}
	
	// Tallentaa tiedoston
	public function save($source) {

		// Jos uppauskansiota ei ole olemassa
		// luodaan sellainen
		createImageFolder($fold_id);

		// Lisätään tiedosto
		if(($this->upload_dir != "") && (is_dir($this->upload_dir))
		&& ($source["file"]["error"] == 0) 
		&& ($source["file"]["size"] < $this->MAX_UPLOAD_SIZE)) {

			// Tiedoston tiedot
			$this->filename = $source["file"]["name"];
			$this->filetype = $source["file"]["type"];
			$this->filesize = $source["file"]["size"];

			// Väliaikaistiedosto
			$this->tempfile = $source["file"]["tmp_name"];

			// Kohdetiedosto: $targetfile
			$this->targetfile = $this->upload_dir.$source["file"]["name"];

			// Muutetaan kohdetiedostonimeä jos tiedosto on jo olemassa
			$pathinfo = pathinfo($targetfile);
			$plainname = basename($pathinfo['basename'],".".$pathinfo['extension']);
			if(file_exists($this->targetfile)) { // Jos tämän niminen tiedosto on jo olemassa
				$exists_number = 2;
				do {
					$this->targetfile = $pathinfo['dirname']."/".$plainname."-".$exists_number.".".$pathinfo['extension'];
					$exists_number++;
				} while(file_exists($this->targetfile));
			}

			// Siirretään väliaikainen tiedosto lopulliseen paikkaan
			// ja annetaan sille sopivat oikeudet.
			if(@move_uploaded_file($source["file"]["tmp_name"], $this->targetfile))
				if(@chmod($this->targetfile, 0644))
					$this->is_success = TRUE;

			// Kirjoitetaan lokitiedot
			$this->writeLog();

			// Jos kaikki on hyvin
			return TRUE;
		} 

		// Jos homma kusee
		return FALSE;
	}

	// Luodaan hakemisto
	public function createDir($directory) {
		// Testataan onko hakemisto jo olemassa
		if(is_dir($directory)) {
			return true;
		} else {
			// Jos hakemisto ei ole olemassa luodaan se.
			if(@mkdir($directory)) {
				if(@chmod($directory, 0777)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		return false;
	}

	// Asetetaan logitiedosto
	// Palauttaa true, jos tiedosto on olemassa
	public function setLogfile($logfile_src) {
		if(is_file($logfile_src)) {
			$this->logfile = $logfile_src;
			return true;
		}
		return false;
	}

	public function writeLog() {

		if($this->logfile != "") {
			// Kirjoitetaan uppaustiedot lokiin
			$aika  = date("j.m-Y, H:i:s");
			$tolog = ($aika." ||| ".$this->filename." ||| ".$this->targetfile." ||| ".$this->filesize."t ||| ".$this->filetype."\n");
			$log   = @fopen($this->logfile, "a");

			@fwrite($log, $tolog);
			@fclose($log);
		}
	}

	public function getMaxSize() {
		return $this->MAX_UPLOAD_SIZE;
	}

	public function getUploaded() {
		if($this->is_success) return $this->targetfile;
		return "";
	}

	public function getOriginal() {
		return $this->filename;
	}

	public function printUploadInfo() {
		echo "Alkuperäinen tiedosto: ".$this->filename."<br/>\n";
		echo "Väliaikainen tiedosto: ".$this->tempfile."<br/>\n";
		echo "Tallennettu tiedosto:  ".$this->targetfile."<br/>\n";
		echo "Tiedostotyyppi:        ".$this->filetype."<br/>\n";
		echo "Tiedoston koko:        ".$this->filesize." tavua<br/>\n";
	}

}
