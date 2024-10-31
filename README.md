nevobo-feed
===========

Wordpress Plugin: Toon de RSS feeds van de Nevobo volleybal competitie in stijl op je website. Gebruik shortcode: [nevobo feed="url"]

De plugin detecteert zelf welke feed het betreft. Plaats de shortcode ergens op de wordpress site.
Via het admin paneel zijn algemene instellingen te wijzigen. Indien in de shortcodes ook waarden worden gespecificeerd hebben deze voorrang op de instellingen in het dashboard.

De volgende extra opties zijn mogelijk:

aantal=x
sporthal=1
plaats=1
cache=0
ical=0
sets=1
vereniging='<verenigingsnaam of een deel ervan>'
nevobo_maps=1

Aantal=x: De x is een opgegeven numerieke waarde die het aantal getoonde items limiteerd tot het gewenste aantal.
Sporthal=1: geeft de sporthal waar de wedstrijd wordt gespeeld weer in het programma overzicht. (Alleen bij een programmafeed!)
Plaats=1: geeft de plaats waar de wedstrijd wordt gespeeld weer in het programma overzicht. (Alleen bij een programmafeed!)
Cache=0: Het gebruik van RSS caching uitgeschakeld. Caching is standaard ingesteld op 1 uur. Cache = x. 1 = kwartier, 2 = 30 minuten, 3 = 45 minuten etc.
ical=0: Verberg de ical programma link bij een programma.
sets=0: Verberg de setstanden bij een uitslag
vereniging=<verenigingsnaam>: De plugin markeert de regel bij de stand, en de naam bij het programma en de uitslagen met de class="nevobo_highlight".
nevobo_maps=0: Zet de maps navigatie aan

voorbeeld: [nevobo feed="https://api.nevobo.nl/export/team/CKL7W0D/heren/1/programma.rss" aantal=3 sporthal=1 plaats=1]

Standaard instellingen zijn het admin paneel in te stellen
