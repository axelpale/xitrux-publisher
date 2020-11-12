<?php include("header1.php"); ?>

<script type="text/javascript">

  function cancelPrompt() {
    document.getElementById("toolprompt").innerHTML="";
  }

  function showNewNotePrompt() {
    strprint = "<form action='notecreate.exe.php' method='post'>\n";
    strprint += "Lisää muistiinpano: <br/>\n";
    strprint += "<textarea id='newnotebody' name='newnotebody' rows='6' cols='60'>\n";
    strprint += "</textarea><br/>\n<input type='submit' value='Lisää' />\n";
    strprint += "<input type='button' onclick='cancelPrompt()' value='Peruuta' />\n";
    strprint += "</form><div class='erottaja'>&nbps;</div>\n\n";
    document.getElementById("toolprompt").innerHTML=strprint;

    document.getElementById("newnotebody").focus();
  }

</script>

<script src='hiddentools.js'></script>

<?php include("header2.php"); ?>

<?php
if($LOGGED) {

  echo "<h1>Projektin muistiinpanot</h1>\n";

  // Muokkaustyökalut
  echo "<div class='linkrow'>\n";
  echo "[<a onclick='showNewNotePrompt()'>Lisää muistiinpano</a>]\n";
  //echo "[<a href='project.php?show=marked'>Näytä merkatut muistiinpanot</a>]\n";
  //echo hiddentoolsStart();
  //echo "[<a href='project.php?show=unmarked'>Näytä merkkaamattomat</a>]\n";
  //echo "[<a href='project.php?show=all'>Näytä kaikki</a>]\n";
  //echo hiddentoolsEnd();
  echo "</div>\n";
  echo "<div id='toolprompt'></div>\n";

  // Listataan kaikki muistiinpanot taulusta korg_note
  $sql = "SELECT note_id,note_body,note_created,note_edited,note_marked FROM korg_notes";
  //if($_GET['show'] == "marked") $sql .= " WHERE note_marked=1";
  //else if($_GET['show'] != "all") $sql .= " WHERE note_marked=0";
  // if($_GET['show'] == "unmarked") $sql .= " WHERE note_marked=0";
  $sql .= " ORDER BY note_marked ASC, note_edited DESC";
  $result = mysql_query($sql, $con);

  $first = 0;
  $amount = 10;
  if(isset($_GET["first"])) $first = (int)$_GET["first"];
  if(isset($_GET["amount"])) $amount = (int)$_GET["amount"];

  $rowcount = mysql_num_rows($result);
  if($rowcount > 0 && $amount > 0) {

    // Muistiinpanonavigaatio. Esim [1-10]
    echo "<div class='linkrow top none'>\n";
    for($i=0; $i < $rowcount; $i = $i + $amount) {
      if($i == $first) {
        echo "<b>[".($i+1)."-";
        if($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i+$amount;
        echo "]</b>\n";
      } else {
        echo "[<a href='project.php?first=".$i."'>".($i+1)."-";
        if($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i+$amount;
        echo "</a>]\n";
      }
    }
    echo "</div>\n";

    if(!@mysql_data_seek($result, $first)) echo "Muistiinpanojen näyttäminen ei onnistunut.<br/>\n";

    echo "<div class='itembrowser public'>\n\n";
    for($i=0; $i < ($rowcount - $first) && $i < $amount; $i++) {

      $item = mysql_fetch_array($result);
      echo "<div class='browseritem public";
      if($item['note_marked'] == 1) echo " marked"; // Merkatut muistiinpanot
      echo "'>\n";

      echo "<h2><a name='".$item['note_id']."'></a>";
      echo getCleanDateTime($item['note_created'])."</h2>\n";

      //Säästetään neljä seuraavaa riviä siltä varalta jos halutaan näyttää linkkien lisäyspäivämäärä
      echo "<div class='issued'>\n";
      echo "Muokattu ".getCleanDateTime($item['note_edited']);
      echo "</div>\n";

      echo "<p>\n".nl2br($item['note_body']);
      echo "</p>\n";

      echo "<div class='linkrow top none'>\n";
      echo "[<a href='noteedit.php?nid=".$item['note_id']."'>Muokkaa</a>]\n";
      if($item['note_marked'] == 1)
        echo "[<a href='notemarker.exe.php?nid=".$item['note_id']."&marked=0'>Poista merkintä</a>]\n";
      else
        echo "[<a href='notemarker.exe.php?nid=".$item['note_id']."&marked=1'>Merkkaa</a>]\n";
      echo "[<a href='notedelete.php?nid=".$item['note_id']."'>Poista</a>]\n";
      echo "</div>\n";

      echo "</div>\n\n";

    }
    echo "</div>\n\n";

    echo "<br class='stopfloat'/>\n\n";

    // Muistiinpanonavigaatio. Esim [1-10]
    echo "<div class='linkrow top'>\n";
    for($i=0; $i < $rowcount; $i = $i + $amount) {
      if($i == $first) {
        echo "<b>[".($i+1)."-";
        if($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i+$amount;
        echo "]</b>\n";
      } else {
        echo "[<a href='project.php?first=".$i."'>".($i+1)."-";
        if($i+$amount > $rowcount) echo $rowcount; // Jotta viimeinen järjestysluku olisi oikein
        else echo $i+$amount;
        echo "</a>]\n";
      }
    }
    echo "</div>\n";

  } else {
    echo "<span>Yhteensä 0 näytettävää muistiinpanoa.</span>\n";
    printSeparator();
  }

?>

<b>//Blogi</b>
<p>
081106<br/>
Eilen tuli taiteiltua yltäkyllin, tänään homma sujui itse koodin parissa. Kourallinen uusia ominaisuuksia ilmestyi, kuten kävijälaskuri, kansioiden julkaisupäivämäärä, sivuston viimeisin päivityskerta ja kirjautumislomakkeen tarkistus. Gaigenlaista. Nyt uneja..
<br/><br/>
081105<br/>
Hyvin tyytyväinen uuteen banneri-filleri designiin .) .) .) Nukkuun - - >
<br/><br/>
081101<br/>
Viikonloppu takana, tästä se lähtee. Esittelin sivuston viikonlopun aikana Samille ja palaute oli kerrassaan positiivista .) Käytiin läpi pieni testaussessio, jonka aikana ilmeni mitä monimuitoisempaa huomiota ja uusia näkökulmia. Samalla korjaantui bugi jos toinenkin. Loistavaa! Asensimme Samin koneelle VSO Image Resizerin, joka oli ilmaisohjelmaksi unelmaa parempi massakuvaeditori. Kuvasetit pienentyivät hetkessä ja älyttömän helposti. Ohjelmaan sai tehtyä myös oman profiilin johon kaikki pienennysasetukset ja muut tallentuivat. Lähes liian helppoa. Käytimme testisetissä kuvakokoa 800x600, josta on hyvin mahdollisesti vakiintumassa oletuskoko. Tiedostot olivat luokkaa 100kt kuvalta. Jos 100kt alkaa tuntumaan myöhemmin liian suurelta, kuvien koko on helposti pudotettavissa lukemiin 640x480. Lukema on optimaalinen, koska tuleva kuvaselaussysteemi näyttää isommatkin kuvat tässä koossa. Jompi kumpi .P Tulevaisuudessa mahdollinen upattaessa kuvia automaattisesti pienentävä skripti/ohjelma hoitaa sitten koko hommelin. Tämä vielä jääköön haaveiluksi... Sivuston rakentteen divittäminen lähti tänään käyntiin vauhdilla. Nyt kun divejä osaa vihdoin käyttää oikein, koko sivuston graafinen kyhääminen helpottuu moninkertaisesti. Tää on hyvä, tää on hyvä.
<br/><br/>
081030<br/>
Bläh. Eilen meni kovaa, tänään on vuorostaan tökkinyt. PNG-kuvien toimimattomuus IE:n kanssa saa pitkäpinnaisemmankin kundin hulluksi. Ratkaisu on koko IE 6 tuen heivaaminen pöydän alle. Mitä turhia alkaa hiillostaa omaa päätään sen takia kun tyhmät ihmiset käyttää tyhmää IEtä. Doh, anyway aamulla tein ainakin pikkukuvakkeet ja muokkasin hieman tiedostonimiä parempaan kuntoon. Jotain sentään. Leikkiminen css:n ja javascriptin kanssa on alkamassa. Löysin hienoja sivustoja, joilla on paljon seikkaperäisiä artikkeleita aiheesta. Niin paljon opittavaa, niin paljon juttuja, liikaa liikaa tuntuu siltä. Eteenkin yksi aihe jäi nopealla vilkaisulla mieleeni. Modernit div-tagit. Ehkä korgin lähitulevaisuudessa koko rungon vanhentuneet tablet voi vihdoin heittää mäkeen ja käyttää niiden tilalla divejä. Erityisesti diveillä kikkailemisessa mulla on paljon opittavaa. Whoah, uneja....
<br/><br/>
081029<br/>
Tiukka päivä takana, huh. Yli 12h koodaamista ja suunnittelua ottaa voimille. Mutta intoa ja oivalluksia löytyi tänään ennätyksellisiä määriä. Inkscape on aivan MAHTAVA ohjelma. Menukuvakkeet syntyivät hetkessä ja tavalla, jonka helppoutta en ollut uskoa. Aamun sähläykset GIMPin kanssa sai pään lähelle ratkeamista, mihin pieni vilkaisu Inkscapen ominaisuuksista toi suuren helpotuksen. Niin helppoa, monipuolista ja vaivatonta. On se GIMPikin aivan OK ohjelma, sopivien työtapojen saavuttamiseksi asetuksia kuitenkin joutuu säätämään ja työskentely on melko hidasta. Hidasta, eteenkin verrattuna Adoben tuoteeseen. No, ei väliä homma toimii. Tämän päivän voisi laskea korgin ulkonäöllisen kehityksen ensimmäiseksi voitoksi. Tän projektin suhteen mulla on nyt erittäin hyvä fiilis, ÖITÄ! .)~
<br/><br/>
081028 klo 19<br/>
Heihou! Kuvatiedoston vaihtaminen valmistui, myös etusivun kuvateksti vaihtuu niinkuin sen pitääkin. Tässä tulee olemaan aivan tajuttomasti viilausta ja pikkunäpertelyä. Perfektionistin unelmapainajainen .) Nyt ruokaamurojajotainmitätunkeanaamaan..
<br/><br/>
081028 klo 13<br/>
Alkaa pikkuhiljaa näyttää hyvältä. Etusivu valmistuu teknisesti tuota pikaa. Etusivua varten täyttyy tehdä muutama uusi julkinen funktio, mutta ei mitään kummempaa. Ehkäpä materiaalin sisääminen voi alkaa jo tulevana viikonloppuna. Ulkoasussa on tietysty rutkasti päivitettävää kaikkine grafiikoineen ja säätöineen mutta se on vaan mukava pikkujuttu.
<br/><br/>
081027<br/>
Huoh, aamulla oli tiukkaa huomata ettei ftp-yhteys toimi ja että palvelimelle on iskenyt Turkkilainen hakkeriryhmä. Mutta ei se mitään, EWH:n väki osaa asiansa ja tietojen palauttaminen sujui nopeasti. Ainoastaan viime iltaiset rävellykset kansion piilottamisen kanssa olivat kadonneet. Päivä kuitenkin sujui muuten ongelmitta, uusina ominaisuuksina tänään syntyi jo mainittu kansioden piilottaminen ja sen lisäksi tärkeä kuvaselain. Kuvaselain vaatii vielä paljon viilausta ja ominaisuuksia siitä huolimatta että tänään kuvaselain on jo aivan käyttökunnossa. Saa nähdä mitä huomenna kikkaillaan.
<br/><br/>
</p>

<div class="erottaja"></div>

<?php
} else {
  include("unauthorized.php");
}
?>

<?php include("footer.php"); ?>
