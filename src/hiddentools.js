// Tämä JavaScript-tiedosto lisätään sivulle, joka
// sisältää linkrow-osion sekä piilotettavia lisä-
// työkaluja osion sisällä. Funktiot piilottavat
// ja näyttävät lisätyökalut sekä vaihtavat toi-
// minnon aktivoivan napin ominaisuuksia.

// Lisätyökalujen näyttäminen
function showHiddentools() {
	document.getElementById("hiddentools").style.display="block";
	document.getElementById("showhidebutton").src="images/less00.gif";
	document.getElementById("showhidebutton").onclick = hideHiddentools;
}

// Lisätyökalujen piilottaminen
function hideHiddentools() {
	document.getElementById("hiddentools").style.display="none";
	document.getElementById("showhidebutton").src="images/more00.gif";
	document.getElementById("showhidebutton").onclick = showHiddentools;
}
