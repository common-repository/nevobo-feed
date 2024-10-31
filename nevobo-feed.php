<?php
if (!defined('ABSPATH')) {
	exit;
}
/*
Plugin Name: Nevobo Feed
Plugin URI: http://masselink.net/projecten/nevobo-feed
Description: Toon de RSS feeds van de Nevobo volleybal competitie in stijl op je website. Gebruik shortcode: [nevobo feed="url"]
Version: 3.3
Author: Harold Masselink
Author URI: http://masselink.net
*/

define('nevobo_feed_versie', '3.3');
add_shortcode('nevobo', 'nevobo_shortcode');
add_action('admin_menu', 'nevobo_admin_create');
add_action('wp_enqueue_scripts', 'nevobo_feed_stylesheet');
add_action('admin_init', 'nevobo_settings');

// Nevobo Feed shortcode toevoegen
function nevobo_shortcode($paras = "", $content = "") {
	$param = shortcode_atts(array(
		'feed' => get_option('feed'),
		'aantal' => get_option('aantal'),
		'sporthal' => get_option('sporthal'),
		'plaats' => get_option('plaats'),
		'vereniging' => get_option('vereniging'),
		'ical' => get_option('ical'),
		'sets' => get_option('sets'),
		'naamlengte_prog' => get_option('naamlengte_prog'),
		'naamlengte_uitslag' => get_option('naamlengte_uitslag'),
		'naamlengte_stand' => get_option('naamlengte_stand'),
		'nevobo_maps' => get_option('nevobo_maps'),
		'maps_home' => get_option('maps_home'),
		'image_set' => get_option('image_set'),
		'highlight_color' => get_option('highlight_color'),
	), $paras);

	return get_nevobo($param['feed'], $param['aantal'], $param['sporthal'], $param['plaats'], $param['vereniging'], $param['ical'], $param['sets'], $param['naamlengte_prog'], $param['naamlengte_uitslag'], $param['naamlengte_stand'], $param['nevobo_maps'], $param['maps_home'], $param['image_set'], $param['highlight_color']);
}

function nevobo_feed($feed, $add_paras) {
	$aantal = get_feed_parameters($add_paras, "aantal");
	$sporthal = get_feed_parameters($add_paras, "sporthal");
	$plaats = get_feed_parameters($add_paras, "plaats");
	$vereniging = get_feed_parameters($add_paras, "vereniging");
	$ical = get_feed_parameters($add_paras, "ical");
	$sets = get_feed_parameters($add_paras, "sets");
	$naamlengte_prog = get_feed_parameters($add_paras, "naamlengte_prog");
	$naamlengte_uitslag = get_feed_parameters($add_paras, "naamlengte_uitslag");
	$naamlengte_stand = get_feed_parameters($add_paras, "naamlengte_stand");
	$nevobo_maps = get_feed_parameters($add_paras, "nevobo_maps");
	$maps_home = get_feed_parameters($add_paras, "maps_home");
	$image_set = get_feed_parameters($add_paras, "image_set");
	$highlight_color = get_feed_parameters($add_paras, "highlight_color");
	echo get_nevobo($feed, $aantal, $sporthal, $plaats, $vereniging, $ical, $sets, $naamlengte_prog, $naamlengte_uitslag, $naamlengte_stand, $nevobo_maps, $maps_home, $image_set, $highlight_color);

	return;
}

function get_nevobo($feed, $aantal, $sporthal, $plaats, $vereniging, $ical, $sets, $naamlengte_prog, $naamlengte_uitslag, $naamlengte_stand, $nevobo_maps, $maps_home, $image_set, $highlight_color) {

	if (is_null($sporthal) || ($sporthal == "")) {
		$sporthal = get_option('sporthal') ?: '0';
	}
	if (is_null($plaats) || ($plaats == "")) {
		$plaats = get_option('plaats') ?: '';
	}
	if (is_null($vereniging) || ($vereniging == "")) {
		$vereniging = get_option('vereniging') ?: '';
	}
	if (is_null($ical) || ($ical == "")) {
		$ical = get_option('ical') ?: '';
	}
	if (is_null($sets) || ($sets == "")) {
		$sets = get_option('sets') ?: '';
	}
	if (is_null($naamlengte_prog) || ($naamlengte_prog == "")) {
		$naamlengte_prog = get_option('naamlengte_prog') ?: '30';
	}
	if (is_null($naamlengte_uitslag) || ($naamlengte_uitslag == "")) {
		$naamlengte_uitslag = get_option('naamlengte_uitslag') ?: '30';
	}
	if (is_null($naamlengte_stand) || ($naamlengte_stand == "")) {
		$naamlengte_stand = get_option('naamlengte_stand') ?: '30';
	}
	if (is_null($nevobo_maps) || ($nevobo_maps == "")) {
		$nevobo_maps = get_option('nevobo_maps') ?: '';
	}
	if (is_null($maps_home) || ($maps_home == "")) {
		$maps_home = get_option('maps_home') ?: '';
	}
	if (is_null($image_set) || ($image_set == "")) {
		$image_set = get_option('image_set') ?: 'grijs';
	}
	if (is_null($highlight_color) || ($highlight_color == "")) {
		$highlight_color = get_option('highlight_color') ?: '';
	}

	//bepaal het feed type om verschillende stijlen te gebruiken | 1-stand, 2-uitslagen, 3-programma
	$feedtype = 'Onbekend';
	if (stristr($feed, 'stand.rss')) {
		$feedtype = "Standen";
	}
	if (stristr($feed, 'resultaten.rss')) {
		$feedtype = "Uitslagen";
	}
	if (stristr($feed, 'programma.rss')) {
		$feedtype = "Programma";
	}

	$code = "<!-- Start Nevobo Feed " . nevobo_feed_versie . " | Werkmodus: " . $feedtype . " | Door Harold Masselink -->";
	$code .= "<span class='nevobofeed'>";

	@include_once(ABSPATH . WPINC . '/feed.php');

	// SimplePie Settings
	$rss = fetch_feed($feed);

	if (is_wp_error($rss)) { $code .= nevobo_feed_fout("De opgegeven Feed kan niet worden verwerkt.", "Nevobo Feed"); }
	 else {
		// Start of processing loop -----------------------------------------------------------------
		//rss table headers | 1-stand, 2-uitslagen, 3-Programma
		switch ($feedtype) {
			case "Onbekend":
				$code .= nevobo_feed_fout("Het type feed kan niet worden bepaald. Betreft het wel een Nevobo feed?", "Nevobo Feed");
				break;
			case "Standen":
				$rss->enable_order_by_date(false);
				$rss->handle_content_type();
				if (empty($aantal)) {
					$aantal = get_option('standen_aantal');
				}
				if (empty($aantal)) {
					$aantal = 12;
				}
				$code .= "<table class='nevobofeed'>";
				$code .= "<thead><tr><th>#</th><th style='min-width: 150px;'>Team </th><th>Wedstr.</th><th>Punten </th></tr></thead><tbody>";
				$code .= "<tr>";
				$items = $rss->get_items(0, $aantal);
				foreach ($items as $item) {
					$standen = explode("<br />", $item->get_description());
					$i = 0;
					$len = count($standen);
					foreach ($standen as $stand) {
						if (($i == 0) OR ($i == ($len - 1))) {
							$i++;
							continue;
						}
						$i++;
						$regex = "#([0-9]?[0-9]). ([^\,]+), wedstr: ([^\,]+), punten: ([^<,]+)#";
						preg_match($regex, $stand, $groep);
						$plek = $groep[1];
						$ploeg = $groep[2];
						$wedstrijden = $groep[3];
						$punten = $groep[4];
						if ($vereniging != "") {
							if (stristr($ploeg, $vereniging)) {
								$code .= "<tr style='color:" . $highlight_color . "'>";
							} else {
								$code .= "<tr>";
							}
						}
						$code .= "<td>" . $plek . "</td><td title='" . $ploeg . "' style='max-width:" . $naamlengte_stand . "px;'>" . $ploeg . "</td><td>" . $wedstrijden . "</td><td>" . $punten . "</td></tr>";
						if ($i > $aantal) {
							break;
						}
					}

				}
				$code .= "</tr>";
				break;
			case "Uitslagen":
				$rss->enable_order_by_date(false);
				$rss->handle_content_type();
				if (empty($aantal)) {
					$aantal = get_option('uitslagen_aantal');
				}
				if (empty($aantal)) {
					$aantal = 6;
				}
				$code .= "<table class='nevobofeed'>";
				$code .= "<thead><tr><th>Datum</th><th style='min-width: 150px;'>Thuisploeg </th><th></th><th style='min-width: 150px;'>Uitploeg</th><th td style='text-align: center'>Resultaat ";
				if (($sets == "1") || ($sets == "2")) {
					$code .= "<th>Sets</th>";
				}
				$code .= "</tr></thead><tbody>";
				$items = $rss->get_items(0, $aantal);
				foreach ($items as $item) {
					//Datum
					$code .= "<tr>";
					$regex = "#([0-9][0-9]) ([^ ]+) ([0-9][0-9][0-9][0-9]) - ([0-9][0-9]:[0-9][0-9])#";
					preg_match($regex, $item->get_date('d M Y - H:i'), $groep);
					$dag = $groep[1];
					$maand = get_dutch_date($groep[2]);
					$jaar = $groep[3];
					$code .= "<td>" . $dag . " " . $maand . "." . "</td>";
					//wedstrijd gegevens
					$regex = "#Wedstrijd: ([\w\W\s\S\d\D]+) - ([\w\W\s\S\d\D]+), Uitslag: ([\d])-([\d]), Setstanden: ([\w\W\s\S\d\D]+)#";
					preg_match($regex, $item->get_description(), $groep);
					$thuisploeg = $groep[1];
					$uitploeg = $groep[2];
					$uitslag = $groep[3] . "-" . $groep[4];
					$setstanden = $groep[5];

					$check = "<td title='" . $thuisploeg . "' style='max-width:" . $naamlengte_uitslag . "px;'>";

					if ($vereniging != "") {
						if (stristr($thuisploeg, $vereniging)) {
							$check .= "<span style='color:" . $highlight_color . "'>";
						}
					}
					$check .= $thuisploeg;
					if ($vereniging != "") {
						if (stristr($thuisploeg, $vereniging)) {
							$check .= "</span>";
						}
					}
					$check .= "</td><td> - </td>";

					$check .= "<td title='" . $uitploeg . "' style='max-width:" . $naamlengte_uitslag . "px;'>";
					if ($vereniging != "") {
						if (stristr($uitploeg, $vereniging)) {
							$check .= "<span style='color:" . $highlight_color . "'>";
						}
					}
					$check .= $uitploeg;
					if ($vereniging != "") {
						if (stristr($uitploeg, $vereniging)) {
							$check .= "</span>";
						}
					}
					$check .= "</td><td style='text-align: center'>";
					if ($uitslag == "") {
						$check .= "onbekend";
					} else {
						$check .= $uitslag;
					}
					$check .= "</td>";

					if ($sets != '0') {
						if ($sets == '1') {
							$check .= "<td style='text-align: center'>";
							if ($setstanden != '') {
								$check .= "<img src='" . plugins_url("/images/" . $image_set . "_sets.png", __FILE__) . "' title='" . $setstanden . "'>";
							}
							$check .= "</td>";
						}
						if ($sets == '2') {
							$check .= "<td style='text-align: left'>" . $setstanden;
						}
					}

					if (stristr($check, "geen uitslagen")) {
						$code .= "<td><br>Er zijn nog geen uitslagen bekend<br><br></td><td></td><td></td>";
					} else {
						$code .= $check;
					}
					$code .= "</tr>";
				}
				break;
			case "Programma":
				$rss->enable_order_by_date(false);
				$rss->handle_content_type();
				if (empty($aantal)) {
					$aantal = get_option('programma_aantal');
				}
				if (empty($aantal)) {
					$aantal = 6;
				}
				$code .= "<table class='nevobofeed'>";
				$code .= "<thead><tr><th>Datum</th><th>Tijd</th><th style='min-width: 150px;'>Thuisploeg </th><th></th><th style='min-width: 150px;'>Uitploeg</th>";
				if ($sporthal == 1) {
					$code .= "<th>Sporthal</th>";
				}
				if ($plaats == 1) {
					$code .= "<th>Plaats</th>";
				}

				if ($nevobo_maps == 1) {
					$code .= "<th>Route</th>";
				}
				$code .= "</tr></thead><tbody>";
				// Loopje voor alle items
				$items = $rss->get_items(0, $aantal);

				foreach ($items as $item) {
					preg_match("#(.*) (.*): ([a-zA-Z0-9 ].*) - ([a-zA-Z0-9 ].*)#", $item->get_title(), $groep);
					$datum = $groep[1];
					$tijd = $groep[2];
					if ($datum == null) continue;
					$thuisploeg = $groep[3];
					$uitploeg = $groep[4];
					$code .= "<tr><td>" . $datum . "</td><td>" . $tijd . "</td>";
					$code .= "<td title='" . $thuisploeg . "' style='max-width:" . $naamlengte_prog . "px;'>";
					if ($vereniging != "") {
						if (stristr($thuisploeg, $vereniging)) {
							$code .= "<span style='color:" . $highlight_color . "'>";
						}
					}
					$code .= $thuisploeg;
					if ($vereniging != "") {
						if (stristr($thuisploeg, $vereniging)) {
							$code .= "</span>";
						}
					}
					$code .= "</td><td> - </td>";

					$code .= "<td title='" . $uitploeg . "' style='max-width:" . $naamlengte_prog . "px;'>";
					if ($vereniging != "") {
						if (stristr($uitploeg, $vereniging)) {
							$code .= "<span style='color:" . $highlight_color . "'>";
						}
					}
					$code .= $uitploeg;
					if ($vereniging != "") {
						if (stristr($uitploeg, $vereniging)) {
							$code .= "</span>";
						}
					}
					$code .= "</td>";

					//Speellocatie toevoegen
					if ($sporthal == 1 || $plaats == 1 || $nevobo_maps == 1) {
						$regex = "#Wedstrijd: (.*), Datum: (.*), (.*), Speellocatie: (.*), (.*), ([^\s]+) (.*)#";
						preg_match($regex, $item->get_description(), $groep);
						if ($sporthal == 1) {
							$code .= "<td>" . $groep[4] . "</td>";
						}
						if ($plaats == 1) {
							$code .= "<td>" . $groep[7] . "</td>";
						}
						if ($nevobo_maps == 1) {
							$route = "https://maps.google.com?saddr=" . $maps_home . "&daddr=" . $groep[5] . "+" . $groep[6] . "+" . $groep[7];
							str_replace(' ', '+', $route);
							$code .= "<td style='text-align: center'> <a href='" . $route . "' target='_blank'><img src='" . plugins_url("/images/" . $image_set . "_loc.png", __FILE__) . "'></a></td>";
						}
					}
					$code .= "</tr>";
				}
				break;
		}
		if ($feedtype != "Onbekend") { $code .= "</tbody></table>"; }
		if (($feedtype == 'Programma') && ($ical == 1)) {
			$icalfeed = str_replace(".rss", ".ics", $feed);
			if (stristr($icalfeed, "poule")) {
				$code .= "<div class='nevobofeed' style=\"font-size: smaller\"><img src='" . plugins_url("/images/" . $image_set . "_ical.png", __FILE__) . "'> <a href='" . $icalfeed . "'>Voeg het volledige programma van de poule aan je agenda toe</a></div><br /><br />";
			} else {
				$code .= "<div class='nevobofeed' style=\"font-size: smaller\"><img src='" . plugins_url("/images/" . $image_set . "_ical.png", __FILE__) . "'> <a href='" . $icalfeed . "'>Voeg het volledige programma van het team aan je agenda toe</a></div><br /><br />";
			}

		}

			if ($sets == '1') {
				$code .= "<div class='nevobofeed' style=\"font-size: smaller\"> <img src='" . plugins_url("/images/" . $image_set . "_sets.png", __FILE__) . "'> Wacht met je muis op de afbeelding om de setstanden te zien<br /><br /></div>";
			}
	}

	// Stop of processing loop -----------------------------------------------------------------
	$code .= "</span>";
	$code .= "<!-- Einde Nevobo Feed " . nevobo_feed_versie . " | http://www.masselink.net/projecten/nevobo-feed -->";

	return $code;
}

function nevobo_admin_create() {
	add_menu_page('Nevobo-feed', 'Nevobo feed', 'manage_options', __FILE__, 'nevobo_settings_page', plugins_url('/images/nevobo_icon.png', __FILE__));
	add_submenu_page(__FILE__, 'Over_Masselink', 'Over Masselink.net', 'manage_options', __FILE__ . '/over-masselink', 'over_masselink');
}

/**
 *
 */
function nevobo_settings_page() {
	if (is_admin()) {
		if (isset($_POST['nevobofeed_submit'])) {
			if (!empty($_POST['nevobofeed_submit']) && check_admin_referer('nevobofeed_submit', 'nevobo_nonce_field')) {
				date_default_timezone_set("Europe/Amsterdam");
				$save_error = '';

				//Vereniging
				$vereniging = sanitize_text_field($_POST['vereniging']);
				$vereniging = substr($vereniging, 0, 30);
				update_option('vereniging', $vereniging);

				//High_light Color
				$highlight_color = sanitize_text_field($_POST['highlight_color']);
				$highlight_color = substr($highlight_color, 0, 7);
				update_option('highlight_color', $highlight_color);

				//Image Set
				$image_set = sanitize_text_field($_POST['image_set']);
				$image_set_array = array('grijs', 'geel', 'rood', 'lichtblauw');
				If (in_array($image_set, $image_set_array)) {
					update_option('image_set', $image_set);
				} else {
					update_option('image_set', 'grijs');
					$save_error = 'Afbeelindingen set';
				}

				//naamlengte_stand
				$naamlengte_stand = sanitize_text_field($_POST['naamlengte_stand']);
				If ((is_numeric($naamlengte_stand)) or (($naamlengte_stand == null))) {
					update_option('naamlengte_stand', $naamlengte_stand);
				} else {
					update_option('naamlengte_stand', '');
					$save_error = 'naamlengte_stand';
				}

				//Standen_aantal
				$standen_aantal = sanitize_text_field($_POST['standen_aantal']);
				If ((is_numeric($standen_aantal)) or (($standen_aantal == null))) {
					update_option('standen_aantal', $standen_aantal);
				} else {
					update_option('standen_aantal', '');
					$save_error = 'standen_aantal';
				}

				//naamlengte_prog
				$naamlengte_prog = sanitize_text_field($_POST['naamlengte_prog']);
				If ((is_numeric($naamlengte_prog)) or (($naamlengte_prog == null))) {
					update_option('naamlengte_prog', $naamlengte_prog);
				} else {
					update_option('naamlengte_prog', '');
					$save_error = 'naamlengte_prog';
				}

				//programma_aantal
				$programma_aantal = sanitize_text_field($_POST['programma_aantal']);
				If ((is_numeric($programma_aantal)) or (($programma_aantal == null))) {
					update_option('programma_aantal', $programma_aantal);
				} else {
					update_option('programma_aantal', '');
					$save_error = 'programma_aantal';
				}

				//Sporthal
				$sporthal = sanitize_text_field($_POST['sporthal']);
				$sporthal_array = array('', '1');
				If (in_array($sporthal, $sporthal_array)) {
					update_option('sporthal', $sporthal);
				} else {
					update_option('sporthal', '');
					$save_error = 'Sporthal';
				}

				//Plaats
				$plaats = sanitize_text_field($_POST['plaats']);
				$plaats_array = array('', '1');
				If (in_array($plaats, $plaats_array)) {
					update_option('plaats', $plaats);
				} else {
					update_option('plaats', '');
					$save_error = 'plaats';
				}

				//ical
				$ical = sanitize_text_field($_POST['ical']);
				$ical_array = array('', '1');
				If (in_array($ical, $ical_array)) {
					update_option('ical', $ical);
				} else {
					update_option('ical', '');
					$save_error = 'ical';
				}

				//naamlengte_uitslag
				$naamlengte_uitslag = sanitize_text_field($_POST['naamlengte_uitslag']);
				If ((is_numeric($naamlengte_uitslag)) or (($naamlengte_uitslag == null))) {
					update_option('naamlengte_uitslag', $naamlengte_uitslag);
				} else {
					update_option('$naamlengte_uitslag', '');
					$save_error = '$naamlengte_uitslag';
				}

				//uitslagen_aantal
				$uitslagen_aantal = sanitize_text_field($_POST['uitslagen_aantal']);
				If ((is_numeric($uitslagen_aantal)) or (($uitslagen_aantal == null))) {
					update_option('uitslagen_aantal', $uitslagen_aantal);
				} else {
					update_option('uitslagen_aantal', '');
					$save_error = 'uitslagen_aantal';
				}

				//sets
				$sets = sanitize_text_field($_POST['sets']);
				$sets_array = array('', '1', '2');
				If (in_array($sets, $sets_array)) {
					update_option('sets', $sets);
				} else {
					update_option('sets', '');
					$save_error = 'sets';
				}

				//nevobo_maps
				$nevobo_maps = sanitize_text_field($_POST['nevobo_maps']);
				$maps_array = array('', '1');
				If (in_array($nevobo_maps, $maps_array)) {
					update_option('nevobo_maps', $nevobo_maps);
				} else {
					update_option('nevobo_maps', '');
					$save_error = 'nevobo_maps';
				}

				//maps_home
				$maps_home = sanitize_text_field($_POST['maps_home']);
				$maps_home = substr($maps_home, 0, 50);
				update_option('maps_home', $maps_home);

				echo '<div class="save_message" style="text-align: center;"><h2>Instellingen opgeslagen: ' . date("H:i:s") . '</h2></div>';
				if ($save_error != '') {
					echo $save_error;
					echo '<div style="text-align: center;color:#ff0000;">Er zijn incorrecte instellingen gevonden, deze instellingen zijn naar standaard gezet.</div>';
				}
			} else {
				wp_die('Security check fail');
			}
		}

		// Store Settings end---------------------------

		?>
		<span class="nevoboadmin">
		<div>
			<h1>
				<img src=" <?php echo plugins_url('/images/nevobo_icon.png', __FILE__) ?>"> Nevobo-feed Plugin v<?php echo nevobo_feed_versie; ?> Instellingen
				<img src=" <?php echo plugins_url('/images/nevobo_icon.png', __FILE__) ?>"></h1>
		</div>
			<form method="post">
				<?php settings_fields('nevobo-feed-groep'); ?>
				<?php do_settings_sections('nevobo-feed-groep'); ?>
				<table width="70%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td colspan="3"><h2>Algemene instellingen</h2></td>
					</tr>
					<tr>
						<td>
							<div class="label">Vereniging</div>
						</td>
						<td>
							<input width="50px" type="text" name="vereniging" value="<?php echo get_option('vereniging'); ?>" size="20"/>
						</td>
						<td width="41%">(gedeeltelijke) naam van een vereniging om extra te onderscheiden. voorbeeld: Krekkers<br/><br/>
						</td>
					</tr>
					<tr>
						<td>
							<div class="label">Highlight Kleur</div>
						</td>
						<td width="30%">
							<input type="text" class="colorpicker" name="highlight_color" value="<?php echo get_option('highlight_color'); ?>" maxlength="7"/>
						</td>
						<td width="41%">Geef de highlight kleur op voor de gespecificeerde verenigingsnaam. Bijvoorbeeld #FF0000<br/><br/>
						</td>
					</tr>
					<tr>
						<td>
							<div class="label">Afbeelindgen set</div>
						</td>
						<td>
							<?php $image_set = get_option('image_set'); ?>
							<select name="image_set" id="image_set">
								<option value="grijs" <?php if ($image_set == "grijs")
									echo "selected" ?>>Grijs
								</option>
								<option value="geel" <?php if ($image_set == "geel")
									echo "selected" ?>>Geel
								</option>
								<option value="rood" <?php if ($image_set == "rood")
									echo "selected" ?>>Rood
								</option>
								<option value="lichtblauw" <?php if ($image_set == "lichtblauw")
									echo "selected" ?>>Licht Blauw
								</option>
							</select>
						</td>
						<td width="41%">Kies een afbeeldingen set voor Ical/Sets en Loc<br/><br/></td>
					</tr>
					<tr>
						<td colspan="3"><h2>Stand instellingen</h2></td>
					</tr>
					<tr>
						<td>
							<div class="label">Maximale verenigingsnaam breedte in pixels (zonder px)</div>
						</td>
						<td width="30%">
							<input type="text" name="naamlengte_stand" value="<?php echo get_option('naamlengte_stand'); ?>"/>
						</td>
						<td width="41%">Geef de maximale vereniging naamlengte op in px. De afgekapte naam zal herkenbaar zijn door de ...<br/><br/>
						</td>
					</tr>
					<tr>
						<td>
							<div class="label">Aantal regels</div>
						</td>
						<td width="30%">
							<input type="text" name="standen_aantal" value="<?php echo get_option('standen_aantal'); ?>" size="2"/>
						</td>
						<td width="41%">Aantal regels in het standoverzicht. standaard: 12<br/><br/></td>
					</tr>
					<tr>
						<td colspan="3"><h2>Programma instellingen</h2></td>
					</tr>
					<tr>
						<td>
							<div class="label">Maximale verenigingsnaam breedte in pixels (zonder px)</div>
						</td>
						<td width="30%">
							<input type="text" name="naamlengte_prog" value="<?php echo get_option('naamlengte_prog'); ?>"/>
						</td>
						<td width="41%">Geef de maximale vereniging naamlengte op in px. De afgekapte naam zal herkenbaar zijn door de ...<br/><br/>
						</td>
					</tr>
					<tr>
						<td>
							<div class="label">Aantal regels</div>
						</td>
						<td width="30%">
							<input type="text" name="programma_aantal" value="<?php echo get_option('programma_aantal'); ?>" size="2"/>
						</td>
						<td width="41%">Aantal regels in het programmaoverzicht. standaard: 6<br/><br/></td>
					</tr>
					<tr>
						<td>
								<div class="label">Sporthal</div>
							</div>
						</td>
						<?php $sporthal = get_option('sporthal'); ?>
						<td width="30%">
							<select name="sporthal" id="sporthal">
								<option value="1" <?php if ($sporthal == "1")
									echo "selected" ?>>Tonen
								</option>
								<option value="" <?php if ($sporthal == "")
									echo "selected" ?>>verbergen
								</option>
							</select></td>
						<td width="41%">Toon de Sporthalnaam.<br/><br/></td>
					</tr>
					<tr>
						<td>
							<div class="label">
								<div class="label">Plaats</div>
							</div>
						</td>
						<?php $plaats = get_option('plaats'); ?>
						<td width="30%"><select name="plaats" id="plaats">
								<option value="1" <?php if ($plaats == "1")
									echo "selected" ?>>Tonen
								</option>
								<option value="" <?php if ($plaats == "")
									echo "selected" ?>>verbergen
								</option>
							</select></td>
						<td width="41%">Toon de plaats.</td>
					</tr>
					<tr>
						<td>
							<div class="label">
								<div class="label">iCal Link</div>
							</div>
						</td>
						<?php $ical = get_option('ical'); ?>
						<td width="30%"><select name="ical" id="ical">
								<option value="1" <?php if ($ical == "1")
									echo "selected" ?>>Tonen
								</option>
								<option value="" <?php if ($ical == "")
									echo "selected" ?>>verbergen
								</option>
							</select></td>
						<td width="41%">Toon de iCal link onder het programma<br/><br/></td>
					</tr>
										<tr>
						<td colspan="3"><h4>Google Maps instellingen</h4></td>
					</tr>
					<tr>
						<td>
							<div class="label">
								<div class="label">Google Maps route tonen</div>
							</div>
						</td>
						<?php $nevobo_maps = get_option('nevobo_maps'); ?>
						<td width="30%"><select name="nevobo_maps" id="nevobo_maps">
								<option value="1" <?php if ($nevobo_maps == "1")
									echo "selected" ?>>Tonen
								</option>
								<option value="" <?php if ($nevobo_maps == "")
									echo "selected" ?>>verbergen
								</option>
							</select></td>
						<td width="41%">Toon de de locatie pin. Na een klik zal de route worden getoond<br/><br/></td>
					</tr>
					<tr>
						<td><span class="label">Thuis locatie adres (Sporthal)</span></td>
						<?php $maps_home = get_option('maps_home'); ?>
						<td>
							<input type="text" name="maps_home" value="<?php echo $maps_home; ?>" size="20"/>
						</td>
						<td>De thuis speellocatie. Zal gebruikt worden als vertrekpunt van de route.<br/><br/></td>
					</tr>
					<tr>
						<td colspan="3"><h2>Uitslagen instellingen</h2></td>
					</tr>
					<tr>
						<td>
							<div class="label">Maximale verenigingsnaam breedte in pixels (zonder px)</div>
						</td>
						<td width="30%">
							<input type="text" name="naamlengte_uitslag" value="<?php echo get_option('naamlengte_uitslag'); ?>"/>
						</td>
						<td width="41%">Geef de maximale vereniging naamlengte op in px. De afgekapte naam zal herkenbaar zijn door de ...<br/><br/>
						</td>
					</tr>
					<tr>
						<td><span class="label">Aantal regels</span></td>
						<td>
							<input type="text" name="uitslagen_aantal" value="<?php echo get_option('uitslagen_aantal'); ?>" size="2"/>
						</td>
						<td>Aantal regels in het uitslagenoverzicht. standaard: 6<br/><br/></td>
					</tr>
					<tr>
						<td>
							<div class="label">
								<div class="label">Setsstanden (mouse-over bij afbeelding)</div>
							</div>
						</td>
						<?php $sets = get_option('sets'); ?>
						<td><select name="sets" id="sets">
								<option value="2" <?php if ($sets == "2")
									echo "selected" ?>>Tekst
								</option>
								<option value="1" <?php if ($sets == "1")
									echo "selected" ?>>Afbeelding
								</option>
								<option value="" <?php if ($sets == "")
									echo "selected" ?>>verbergen
								</option>
							</select></td>
						<td>Toon de setstanden pictogram met de setstanden.<br/><br/></td>
					</tr>
					<tr>
						<td colspan="3"><br/><br/>
				<input class='button-primary' type='submit' name="nevobofeed_submit" value="Opslaan">
							<?php wp_nonce_field('nevobofeed_submit', 'nevobo_nonce_field'); ?>
						</td>
					</tr>
					</table>
			</form>
		</span>
		<?php
	}
}

function over_masselink() {
	if (is_admin()) {
		?>
		<span class='nevobo_admin'>
			<img src="<?php echo plugins_url('/images/masselink-logo.png', __FILE__) ?>" width="250px">
			<p style="text-align: left;">Deze plugin is er alleen nog voor legacy redenen. kijk op
				<a target="_blank" href="https://masselink.net/projecten/nevobo-feed">https://masselink.net/projecten/nevobo-feed</a> voor meer informatie.<br/><br />
				Er is een nieuwe versie gemaakt door Daan van Deventer: Nevobo API - <a target="_blank" href="https://wordpress.org/plugins/nevobo-api/">https://wordpress.org/plugins/nevobo-api/</a><br/><br/>
				Met vriendelijke groet,<br/><br />
				Harold Masselink<br/>
				www.masselink.net<br/>
			</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			 <input type="hidden" name="cmd" value="_s-xclick" />
			 <input type="hidden" name="hosted_button_id" value="AJ6KTJP6PM32C" />
			 <input type="image" src="https://www.paypalobjects.com/nl_NL/NL/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Doneren met PayPal-knop" />
			 <img alt="" border="0" src="https://www.paypal.com/nl_NL/i/scr/pixel.gif" width="1" height="1" />
			</form>
		</span>
		<?php
	}
}

function nevobo_settings() {
	//register our settings
	register_setting('nevobo-feed-groep', 'vereniging');
	register_setting('nevobo-feed-groep', 'highlight_color');
	register_setting('nevobo-feed-groep', 'naamlengte_prog');
	register_setting('nevobo-feed-groep', 'naamlengte_uitslag');
	register_setting('nevobo-feed-groep', 'naamlengte_stand');
	register_setting('nevobo-feed-groep', 'image_set');
	register_setting('nevobo-feed-groep', 'plaats');
	register_setting('nevobo-feed-groep', 'sporthal');
	register_setting('nevobo-feed-groep', 'standen_aantal');
	register_setting('nevobo-feed-groep', 'uitslagen_aantal');
	register_setting('nevobo-feed-groep', 'programma_aantal');
	register_setting('nevobo-feed-groep', 'ical');
	register_setting('nevobo-feed-groep', 'sets');
	register_setting('nevobo-feed-groep', 'nevobo_maps');
	register_setting('nevobo-feed-groep', 'maps_home');
}

function color_picker_assets() {
	wp_enqueue_style('wp-color-picker');
	wp_enqueue_script('my-script-handle', plugins_url('scripts/adminstyles.js', __FILE__), array('wp-color-picker'), false, true);
}

function get_dutch_date($month) {
	switch ($month) {
				case 'Mar':
		         return "mrt";
			  case 'May':
		         return "mei";
			  case 'Oct':
		 		     return "okt";
    }
return strtolower($month);
}

add_action('admin_enqueue_scripts', 'color_picker_assets');

function nevobo_feed_stylesheet() {
	wp_register_style('nevobo-feed_style', plugins_url('/style/nevobo-feed.css', __FILE__));
	wp_enqueue_style('nevobo-feed_style');
}

function nevobo_admin_stylesheet() {
	wp_register_style('nevobo-admin_style', plugins_url('/style/nevobo-admin.css', __FILE__));
	wp_enqueue_style('nevobo-admin_style');
}

function nevobo_feed_fout($errorin, $plugin_name) {
	return "<table><tr><td style=\"text-align:center\">" . $plugin_name . ": " . __($errorin) . "\n</td></tr></table>";
 }
