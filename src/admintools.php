<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if($LOGGED) {

	echo "<h1>Hallinnointityökalut</h1>\n";
	printSeparator();

	echo "<div>\n";
	echo "<a href='project.php'>\n";
	echo "<img src='images/mainmenu/blog10-black.gif' alt='Projektiloki' />\n";
	echo "</a>[<a href='project.php'>\n";
	echo "Projektiloki</a>]\n";
	echo "</div>\n";

	printSeparator();

} else {
	include("unauthorized.php");
}

?>

<?php include("footer.php"); ?>