<?php

// Xitrux website settings / Xitrux sivuston asetukset

// Website title / sivuston nimi
define("SITE_TITLE", "Kohteet.org" );
define("SITE_TITLE_SLOGAN", "Toimintaa &#38; tumpelointia" );
define("SITE_TITLE_SHOW_SLOGAN", TRUE );

// MySQL database connection / tietokannan yhteys
define("DB_NAME", "xitruxdb" );
define("DB_USER", "xitruxuser" );
define("DB_PASS", "xitruxpass" );

// Meta-information / metatiedot
define("SITE_META_DESCRIPTION", "Valokuvia ja kertomuksia Nilan ja c8h11no2:n epäilyttävästä toiminnasta." );
define("SITE_META_KEYWORDS", "kohteet,ue,urban,exploration,valokuvaus,toiminta,seikkailu,kertomus,nila,c8h11no2,akseli,palen" );

// Website icon / sivuston kuvake
define("SITE_ICON_PATH", "images/korglogo00.gif" );

// Visitor counter / kävijälaskuri
define("ENABLE_VISITOR_COUNTER", TRUE );
define("VIEW_VISITOR_COUNTER_PUBLIC", TRUE );
define("VIEW_VISITOR_COUNTER_PRIVATE", TRUE );
define("TEXT_VISITOR_COUNTER_A", "kohteet.orgissa vierailtu" );
define("TEXT_VISITOR_COUNTER_B", "kertaa" );

// Mainpage counter / etusivun latauslaskuri
define("ENABLE_MAINPAGE_COUNTER", TRUE );
define("VIEW_MAINPAGE_COUNTER_PUBLIC", FALSE );
define("VIEW_MAINPAGE_COUNTER_PRIVATE", TRUE );
define("TEXT_MAINPAGE_COUNTER_A", "etusivu ladattu" );
define("TEXT_MAINPAGE_COUNTER_B", "kertaa" );

// Pageload counter / sivujen latauslaskuri
define("ENABLE_PAGELOAD_COUNTER", TRUE );
define("VIEW_PAGELOAD_COUNTER_PUBLIC", FALSE );
define("VIEW_PAGELOAD_COUNTER_PRIVATE", TRUE );
define("TEXT_PAGELOAD_COUNTER_A", "sivuja ladattu" );
define("TEXT_PAGELOAD_COUNTER_B", "kertaa" );

// User login info at the bottom right of the layout
// Käyttäjän kirjautumistiedot ruudun alhaalla oikealla
define("VIEW_USER_NAME", TRUE );
define("VIEW_USER_LOGIN", TRUE );
define("VIEW_USER_LOGOUT", TRUE );
define("TEXT_USER_NAME_TITLE", "Käyttäjä:" );
define("TEXT_USER_LOGOUT", "kirjaudu ulos" );
define("TEXT_USER_LOGIN", "kirjaudu" );


// Warning about old browser
define("ENABLE_BROWSER_WARNING", TRUE );
define("BROWSER_WARNING_TEXT", "<div>Tämä on nykyaikainen sivusto eikä näin ollen tue vanhoja ja rajottuneita Internet Explorer -selaimia.<br/>Hanki kunnon selain esimerkiksi <a class='under' href='http://www.mozilla.com/en-US/'>täältä</a>.</div>" );

// Banner images
define("ENABLE_BANNER_RIGHT", TRUE );
define("BANNER_RIGHT_DEFAULT_PATH", "images/upload/fid32/banner19.jpg");
define("ENABLE_BANNER_RIGHT_RANDOM", TRUE );
define("BANNER_RIGHT_RANDOM_FOLDER_ID", 32 );

// Control panel
define("ADMIN_FOLDERS_PER_PAGE", 15 );
define("ADMIN_IMAGES_PER_PAGE", 15 );
