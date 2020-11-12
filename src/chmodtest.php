<?php include("header1.php"); ?>
<?php include("header2.php"); ?>

<h1>Kohteet.org</h1>
<div class="erottaja"></div>

<?php

if($_SESSION['logged'] == logged) {

$hakemisto = "images/upload/uploadlog.txt";

/* chmod notes from w3Schools
The mode parameter consists of four numbers:

    * The first number is always zero
    * The second number specifies permissions for the owner
    * The third number specifies permissions for the owner's user group
    * The fourth number specifies permissions for everybody else

Possible values (to set multiple permissions, add up the following numbers):

    * 1 = execute permissions
    * 2 = write permissions
    * 4 = read permissions
*/


//$tiedosto = $_GET['filename'];
//$mod = $_GET['mod'];
//$modlen = strlen($mod);

//if(strspn($mod,"1234567890") == $modlen) {

//if($modlen != 4) $mod = "0".$mod;
//if(strlen($mod) == 4) {

//if(chmod($hakemisto.$tiedosto, 0644)) echo "Tiedoston ".$tiedosto." muutos onnistui tilaan [644]<br/>hakemistossa ".$hakemisto;
//else echo "Tiedoston ".$tiedosto." muutos epäonnistui.";

if(chmod($hakemisto, 0722)) echo "Tiedoston ".$hakemisto." muutos onnistui tilaan [722].<br/>\n";
else echo "Tiedoston ".$hakemisto." muutos epäonnistui.";

//} else echo "Mod-arvo virheellinen.";

//} else echo "Mod-arvo virheellinen.";

} else {
	include("unauthorized.php");		
}

?>

<div class="erottaja"></div>

<?php include("footer.php"); ?>
