<!-- Main menu -->

<a class="mainmenuitem home" href="/main.php"><span>KOTI</span></a>
<a class="mainmenuitem pics" href="/foldbrowser.php"><span>KUVAT</span></a>
<a class="mainmenuitem site" href="/korginfo.php"><span>HÄ?</span></a>
<a class="mainmenuitem net" href="/links.php"><span>LINKIT</span></a>

<div class="latestupdate">Viimeisin päivitys:
<?php 
	echo getCleanDate(getSiteUpdate(1,$con)); // 1 on kohteet.org sivuston site_id
?>
</div>
