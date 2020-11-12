<?php include("header1.php"); ?>

<script type="text/javascript">

function validateRequired(field) {
  with(field) {
    if(value == null || value == "") {
      return false;
    } else {
      return true;
    }
  }
}

function validateForm(thisform) {
  with(thisform) {
    if(!validateRequired(username)) {
      username.focus();
      return false;
    } else
    if(!validateRequired(password)) {
      password.focus();
      return false;
    }
  }

  return true;
}

</script>

<?php include("header2.php"); ?>

<?php

if($LOGGED) echo "KIRJAUTUNUT\n";
if($_SESSION['fails'] < 5) {
  echo "<h1>Kirjaudu sisään:</h1>\n";
  printSeparator();
  echo "<form action='login.exe.php' onsubmit='return validateForm(this)' method='post'>\n";

  echo "Käyttäjätunnus:<br/>\n";
  echo "<input type='text' id='namefield' name='username' value='' maxlength=16 onfocus='this.select()' /><br/><br/>\n";

  echo "Salasana:<br/>\n";
  echo "<input type='password' id='codefield' name='password' value='' maxlength=16 onfocus='this.select()' /><br/><br/>\n";
  echo "<input type='submit' value='Kirjaudu' />\n";

  echo "</form>\n";

  echo "<script type='text/javascript'>\n";
  echo "<!--\n";
  echo "document.getElementById('namefield').focus();\n";
  echo "//-->\n";
  echo "</script>\n";

} else {
  echo "<h1>Kirjautuminen epäonnistunut viiteen kertaan!</h1>\n";
  echo "Jos sinulla on tarvetta hallinnoida sivustoa ota yhteys webmasteriin.<br/>\n";
}

?>

<?php include("footer.php"); ?>
