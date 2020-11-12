</head>
<body>

<div class="supcontainer">

	<?php
		// Varoitus vanhasta selaimesta
		if( ENABLE_IEXPLORER_WARNING ) {
	?>

	<div class="errorheader">
		<script type="text/javascript">
			//<!-- vanhemmille selaimille .P
			if (navigator.appName=="Microsoft Internet Explorer") {
				if(parseFloat(navigator.appVersion) < 7) {
					document.write("<?php echo BROWSER_WARNING_TEXT; ?>");
				}
			}
			//-->
		</script>
	</div>
	
	<?php
		} // Varoitus vanhasta selaimesta päättyy
	?>

	<div class="container">
		<div class="mainheader">
			<div class="banner">
				<div class="bannerimageleft">

					<!-- latausindikaattori -->
					<!--div id="loading" class="loader ready">
						<img src="images/loading.gif"/>
					</div-->

					<!-- latausindikaattorin ohjaus -->
					<!--script type="text/javascript">
						document.getElementById("loading").className = "loader";
						window.onload=function(){
							document.getElementById("loading").className = "loader ready";
						}
					</script-->

					&nbsp;
				</div>
				
<?php
	// Right banner image enable
	if( ENABLE_BANNER_RIGHT ) {

		echo "<div class=\"bannerimageright\" style=\"background-image: url('";
	
		if( ENABLE_BANNER_RIGHT_RANDOM ) {
			// Random banner image
			echo getRandomImageSrc( BANNER_RIGHT_RANDOM_FOLDER_ID, $con );
		} else {
			// Default banner image
			echo BANNER_RIGHT_DEFAULT_PATH;
		}
	
		echo "');\" >&nbsp;\n";
		echo "</div>\n";
	}
?>

			</div>
		</div>

		<!-- Menut -->
		<?php
			// Näytetään filleri ainoastaan julkisella puolella
			if($LOGGED) {
				echo "<div class='mainmenu'>\n";
				include("usermenu.php");
			}
			else {
				echo "<div class='mainmenu filler'>\n";
				include("publicmenu.php");
			}
			echo "<div style='clear:left;'></div>\n"; // Lopettaa kuvakkeiden kellumisen ja antaa mainmenulle korkeuden
			echo "</div>\n";
		?>

		<div class="mainbody">
