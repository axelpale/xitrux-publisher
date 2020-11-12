<?php
	// Käynnistetään sessio kirjautumista varten
	session_start();
	
	// Sivuston vakiot
	include_once( "config.php" );

	// Sivuston julkisella puolella käytettävät php-funktiot, esim printSeparator()
	include_once( "public-functions.php" );

	// Sivulla käytetty mysql-yhteys (suljetaan footer.phpssä)
	$con = korg_connect();

	// Käyttäjän statuksen tarkastaminen
	$LOGGED = false;
	if($_SESSION['logged'] == "logged") $LOGGED = true;
	
	// Kävijälaskuri
	if( ENABLE_VISITOR_COUNTER ) {
		if( !isset($_SESSION['oldvisitor']) ) {
			$_SESSION['oldvisitor'] = 1;
			addVisitor(1,$con);
		}
	}
	
	// Page load counter, sivujen latausmäärän laskuri
	if( ENABLE_PAGELOAD_COUNTER ) {
		addPageload(1,$con);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php
	echo "<title>".SITE_TITLE;
	if(SITE_TITLE_SHOW_SLOGAN) {
		echo " - ".SITE_TITLE_SLOGAN;
	}
	echo "</title>\n";
?>

<?php
	echo "<meta name=\"description\" content=\"".SITE_META_DESCRIPTION."\" />\n";
	echo "<meta name=\"keywords\" content=\"".SITE_META_KEYWORDS."\" />\n";
?>

<link rel="shortcut icon" href="<?php echo SITE_ICON_PATH; ?>" type="image/x-icon" /> 
<link rel="icon" href="<?php echo SITE_ICON_PATH; ?>" type="image/x-icon" />

<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link rel="stylesheet" type="text/css" media="screen" href="structure.css" />
<link rel="stylesheet" type="text/css" media="screen" href="header.css" />
<link rel="stylesheet" type="text/css" media="screen" href="mainmenu.css" />
<link rel="stylesheet" type="text/css" media="screen" href="body.css" />
<link rel="stylesheet" type="text/css" media="screen" href="footer.css" />
