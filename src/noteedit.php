<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if($LOGGED) {

	// nid = note_id = muistiinpanon id-numero
	// Tarkistetaan GET['nid'] tietoturvan vuoksi
	$nid = sanitizeId($_GET['nid']);

	// Haetaan muistiinpanon tiedot
	$notedata = getNoteData($nid,$con);

	// Linkki takaisin muistiinpanoihin
	echo "<div class='linkrow top'>\n";
	echo "[<a href='project.php#".$nid."'>\n";
	echo "Takaisin muistiinpanoihin</a>]\n";
	echo "</div>\n";

	// Otsikko
	echo "<h1>Muistiinpanon '".$notedata['note_created']."' muokkaaminen</h1>\n";
	printSeparator();

	// Muutoslomake
	echo "<form action='noteedit.exe.php?nid=".$nid."' method='post'>\n";
	echo "Muistiinpano: <br/>\n";
	echo "<textarea name='newnotebody' rows='8' cols='60'>";
	echo $notedata['note_body'];
	echo "</textarea><br/>\n";
	echo "<input type='submit' value='OK' />\n";
	echo "<input type='button' onclick='window.location=\"project.php#".$nid."\"' value='Peruuta' />\n";
	echo "</form>\n\n";

	printSeparator();

} else {
	include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
