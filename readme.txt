=== Nevobo Feed ===
Contributors: Masselink
Tags: nevobo, feed, rss, competitie, volleybal, sport
Requires at least: 3.1
Donate link: http://masselink.net/projecten/nevobo-feed
Tested up to: 5.3
Stable tag: 5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Toon de standen, uitslagen en programma feeds in de juiste theme-stijl op je wordpress site.

== Description ==
LET OP! Gebruik het admin paneel op de plugin te configureren

Toon de standen, uitslagen en programma feeds van de nevobo site in de juiste theme-stijl op je wordpress site.

[nevobo feed="url van de feed"]

De plugin detecteert zelf welke feed het betreft. Plaats de shortcode ergens op de wordpress site.
Via het admin paneel zijn algemene instellingen te wijzigen. Indien in de shortcodes ook waarden worden gespecificeerd hebben deze voorrang op de instellingen in het dashboard.

voorbeeld: [nevobo feed="https://api.nevobo.nl/export/team/CKL7W0D/heren/1/programma.rss" aantal=3 sporthal=1 nevobo_maps=1]

== Installation ==

1. Installeer door in wordpress een nieuwe plugin te zoeken met als naam "Nevobo feed"
2. Activeer de plugin in het 'Plugins' menu in WordPress
3. Gebruik de shortcode [nevobo feed=<url van de feed>] in je site

== Frequently Asked Questions ==

= Sporthal =
sporthal=1 (zet sporthal aan in het programma overzicht)
= Plaats =
plaats=1 (zet plaatsen aan in het programma overzicht)
= Aantal =
aantal=x (limiteer het aantal items)
= Cache =
cache=x (0 = schakel de cache uit, 1 = kwartier, 2 = 30 minuten, 3 = 45 minuten etc)
= ical =
ical=0 (0 schakel de ical link uit)
= sets =
sets=1 (toon setstanden door op een plaatje te hooveren)
=vereniging=
vereniging=<verenigingsnaam>: De plugin markeert de regel bij de stand, en de naam bij het programma en de uitslagen met de class="nevobo_highlight".
=nevobo_maps=
Schakel de route link uit.

= Welke uitslagen kan ik tonen? =
Het is mogelijk de uitslagen van de poule of de uitslagen van het team weer te geven.
De gekozen feed bepaald de getoonde uitslagen

== Screenshots ==

Er zijn geen screenshots beschikbaar. Klik <a href="https://masselink.net/projecten/nevobo-feed" taget="_blank">hier</a> om de plugin in werking te zien. Ook <a href="http://krekkers.nl" taget="_blank">Krekkers.nl</a> maakt gebruik van de plugin.

== Changelog ==

= 3.3 =
* Update: compatible with Wordpress 5.3
* Update: check new version: https://wordpress.org/plugins/nevobo-api/


= 3.1.1 =
* Fix: removed unnecessary check (thanks for the heads-up Jos Knippen)

= 3.1.0 =
* Fix: Pageload error when selecting incorrect feed
* Fix: Error handling routine
* New: Nicer error handling
* New: 'programma' and 'resultaten' feeds based on play location are available now

= 3.0.1 =
* Nevobo changed the 'uitslagen' feed. This version supports the new format

= 3.0.0 =
* Season 2016-2017 support (new API)
* Program date fix
* SimplePie Support instead of Magpie
* Dropped Caching parameter (using global WP)
* Optimized Regex
* New Admin page design
* Fixed: Using Nonces
* Fixed: link for ical feed
* Fixed: Allowing Direct File Access to plugin files
* Fixed: Input sanitizing user data

= 2.2.1 =
* No more donation ads (please use the donate button)
* Possibillity to select how often ads are shown.
* Sets in a different colomn (Tekst or hoover image)
* Plugin tested in WP 4.4.1
* CSS Styling adjustment

= 2.1.1 =
* Bugfix: $param['highlight_color'] and $param['image_set'] missing (thx: rvanderven)
* Default color & image set

= 2.1.0 =
* Google Adsense Support! (Random ad from nevobo-feed included)
* Table bugfix

= 2.0.0 =
* Minor bugfixes
* Google Maps navigation
* New Icon pack

= 1.7.7 =
* Code Cleanup
* Bugfix: Showing highlight name above the program.

= 1.7.6 =
* Bugfix: City regex change to support place and state f.e. Nijmegen GLD (Thx: Jydis/Toesj)

= 1.7.5 =
* Bugfix: Team highlight parameter conflict resolved

= 1.7.1 =
* Bugfix: Shortcode settings not working (spotted by Ronald)

= 1.7 =
* Wordpress 4.1 Stable
* Depricated Extract function replaced by shortcode_atts (Thanks: Wilmar den Ouden)

= 1.6 =
* Maximale lengte van verenigingsnamen. (Mouseover-tooltip voor volledige naam)
* Code Cleanup

= 1.5.1 =
* Wordpress 4.0 support

= 1.5 =
* Bepaal de highlightkleur nu zelf vanuit het admin menu
* Betere afbeeldingen (Sets/Cal)
* Bugfixes met parameters
* Bugfix met Sporthal en plaats
* Voorbereiding voor GoogleMaps links

= 1.4.3 =
* RSS special characters fix. (Thanks: Bossdwarf)

= 1.4.2 =
* fix waarbij de set afbeelding werd getoond zelfs als deze uit gezet was.

= 1.4.1 =
* fix voor veranderingen door de editor met &, &amp; en &amp;amp;

= 1.4 =
* Instellingen configureerbaar in het dashboard.
* Team highlighting

= 1.3.4 =
* Set standen worden nu getoond als er met de muis over het plaatje wordt gehoverd
* Nieuwe indeling in de tabbelen voor programma en uitslagen.
* ical link veranderd automatisch naar een team of poule programma
* Nieuwe graphics

= 1.3.0 =
* Bugfix als de teamnaam een "-" bevat zoals Set-Up'65. (Regular expression herschreven)
* Team accentuering. (afwijkende css styling voor de verenigng teams)

= 1.2.2 =
* Toon ical link voor het volledige programma om te importeren in je eigen agenda.

= 1.2 =
* De Nevobo stuurt uitslagen ook als deze nog niet bekend zijn. De plugin geeft nu de status "Uitslag nog niet bekend"

= 1.1 =
* Caching parameter per 15 minuten.

= 1.0.3 =
* Kleine Fix. Debug stond nog aan

= 1.0.2 =
* Kleine fix voor als er geen uitslag bekend is
* Kleine optimalizatie & Backlink

= 1.0.1 =
* Kleine fix in de uitlijning van de tabelheaders. (staan nu niet meer tegen elkaar)

= 1.0 =
* Bugfix in Uitslagen

= 0.9b =
* Eerste release

== Upgrade Notice ==

= 3.3 =
* Update: compatible with Wordpress 5.3
* Update: check new version: https://wordpress.org/plugins/nevobo-api/

== Donations ==

Het maken (en bijhouden) van plugins kost veel tijd. Donaties als waardering dan ook van harte welkom.
Zo kan ik in mijn (spaarzame) vrijetijd een biertje of kopje koffie drinken.

<a href="masselink.net/projecten/nevobo-feed">klik hier om naar de website te gaan en te doneren</a>
