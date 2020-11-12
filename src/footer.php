<?php include_once( "config.php" ); ?>

</div>

<div class="mainfooter">
<div class="footerleft">
<?php
	// Counters
	echo "<span>";

	// Precalculate logics
	$vis_cntr = ( VIEW_VISITOR_COUNTER_PUBLIC && !$LOGGED );
	$vis_cntr = ( $vis_cntr || VIEW_VISITOR_COUNTER_PRIVATE && $LOGGED );

	$main_cntr = ( VIEW_MAINPAGE_COUNTER_PUBLIC && !$LOGGED );
	$main_cntr = ( $main_cntr || VIEW_MAINPAGE_COUNTER_PRIVATE && $LOGGED );

	$page_cntr = ( VIEW_PAGELOAD_COUNTER_PUBLIC && !$LOGGED );
	$page_cntr = ( $page_cntr || VIEW_PAGELOAD_COUNTER_PRIVATE && $LOGGED );

	// Visitor counter view
	if( $vis_cntr ) {
		echo TEXT_VISITOR_COUNTER_A." ".getSiteVisitors(1,$con);
		echo " ".TEXT_VISITOR_COUNTER_B;
	}

	// Delimiter
	if( $vis_cntr && $main_cntr ) {
		echo " | ";
	}

	// Mainpage counter view
	if( $main_cntr ) {
		echo TEXT_MAINPAGE_COUNTER_A." ".getSiteMainload(1,$con);
		echo " ".TEXT_MAINPAGE_COUNTER_B;
	}

	// Delimiter
	if( ( $main_cntr || ( !$main_cntr && $vis_cntr ) ) && $page_cntr ) {
		echo " | ";
	}

	// Pageload counter view
	if( $page_cntr ) {
		echo TEXT_PAGELOAD_COUNTER_A." ".getSitePageload(1,$con);
		echo " ".TEXT_PAGELOAD_COUNTER_B;
	}

	echo "</span>\n";
?>
</div>

<div class="footerright">
<span>
<?php
	if($LOGGED) {
		if( VIEW_USER_NAME ) {
			echo TEXT_USER_NAME_TITLE." ".$_SESSION['user'];
		}
		if( VIEW_USER_NAME && VIEW_USER_LOGOUT ) {
			echo " - ";
		}
		if( VIEW_USER_LOGOUT ) {
			echo "<a class='logout' href='logout.php'>".TEXT_USER_LOGOUT."</a>";
		}
	} else {
		if( VIEW_USER_LOGIN ) {
			echo "<a class='login' href='login.php'>".TEXT_USER_LOGIN."</a>";
		}
	}
?>
</span>
</div>
</div></div>

<div class="authorfooter">
Sisällön kopiointi ja käyttö mihin tahansa tarkoituksiin ehdottomasti <a href="licence.php">sallittu</a>.
</div>

</div>

<!-- Ladataan vaihtuvat menukuvakkeet valmiiksi -->
<img class="preload" src="images/mainmenu/home01-white.gif" alt="Image Preload" />
<img class="preload" src="images/mainmenu/pics00-white.gif" alt="Image Preload" />
<img class="preload" src="images/mainmenu/korg00-white.gif" alt="Image Preload" />
<img class="preload" src="images/mainmenu/irc01-white.gif" alt="Image Preload" />

<?php if($LOGGED) { ?>
<img class="preload" src="images/mainmenu/piced00-white.gif" alt="Image Preload" />
<img class="preload" src="images/mainmenu/blog10-white.gif" alt="Image Preload" />
<img class="preload" src="images/mainmenu/logout00-white.gif" alt="Image Preload" />
<?php } ?>

</body>
</html>

<?php
	// Suljetaan sivuston mysql-yhteys
	$con = null;
?>
