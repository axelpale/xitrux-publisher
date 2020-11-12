<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<?php
if ($_SESSION['logged'] == "logged") {

  echo "<h1>Kohteet.org</h1>\n";
  printSeparator();

  printSeparator();

} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
