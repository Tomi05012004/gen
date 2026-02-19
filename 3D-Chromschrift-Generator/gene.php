<?php
session_start();

$phpMailerVersion = "PHPMailer-6.10.0";
$path = "../{$phpMailerVersion}/{$phpMailerVersion}";
require $path.'/src/Exception.php';
require $path.'/src/PHPMailer.php';
require $path.'/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

require_once("conf.php");
require_once("face_old.php");

$face = new face("auto"); 

$version = 3;

// Define order counter file path
define('ORDER_COUNTER_FILE', 'PHP/order_counter.json');
define('ORDER_ERROR_MESSAGE', '<h3>Es ist ein Fehler beim Erstellen der Bestellnummer aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie unseren Support.</h3>');
define('INITIAL_ORDER_COUNTER', 1000);

conf :: init();
/// prüfe ob Mobilgerät

conf :: setInstanz($mail);

/// prüfe ob Mobilgerät
$opti = conf :: $opti;
$checkBox = conf :: $checkBox;
$fram = (!empty($_GET["fram"]) && $_GET["fram"] == "t") ? (true) : (false);

/// ob mobile Seite
$mobi = conf :: checMobi();
/// welches Modul
$site = (empty($_GET["mod"])) ? ("edt") : ($_GET["mod"]);
/// 
$itemPosi = -1;

/// Sanitization helper function for XSS prevention
function sanitize_input($data) {
	if (is_array($data)) {
		return array_map('sanitize_input', $data);
	}
	if ($data === null) {
		return '';
	}
	return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/// Email validation and sanitization function
function sanitize_email($email) {
	if ($email === null) {
		return '';
	}
	$email = trim($email);
	// Validate email format
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Sanitize for safe output
		return htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
	}
	return '';
}

/// Boolean sanitization function
function sanitize_boolean($value) {
	if ($value === null) {
		return false;
	}
	$result = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	return $result !== null ? $result : false;
}

/// Numeric sanitization function
function sanitize_numeric($value) {
	if ($value === null) {
		return 0.0;
	}
	$cleaned = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$validated = filter_var($cleaned, FILTER_VALIDATE_FLOAT);
	return $validated !== false ? $validated : 0.0;
}

/// verarbete POST Daten
if($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["acti"]))
{
	
	if($_POST["acti"] == "add")
	{
		if(intval($_POST["posi"]) > -1)
		{
			conf :: ediItem(intval($_POST["posi"]), "font", sanitize_input($_POST["font"] ?? ''));
			conf :: ediItem(intval($_POST["posi"]), "text", sanitize_input($_POST["text"] ?? ''));
			conf :: ediItem(intval($_POST["posi"]), "char", sanitize_input($_POST["char"] ?? ''));
			conf :: ediItem(intval($_POST["posi"]), "foil", sanitize_boolean($_POST["foil"] ?? false));
			conf :: ediItem(intval($_POST["posi"]), "true", sanitize_boolean($_POST["true"] ?? false));
			conf :: ediItem(intval($_POST["posi"]), "foilLength", sanitize_numeric($_POST["leng"] ?? 0));
		}
		else
		{
			$font = sanitize_input($_POST["font"] ?? '');
			$text = sanitize_input($_POST["text"] ?? '');
			$char = sanitize_input($_POST["char"] ?? '');
			$foil = sanitize_boolean($_POST["foil"] ?? false);
			$true = sanitize_boolean($_POST["true"] ?? false);
			$leng = sanitize_numeric($_POST["leng"] ?? 0);
			conf :: addItem($font, $text, $char, $foil, $true, $leng);
		}
		
		/// leite an sich selbst weiter (um Informationsdoppelsenden zu vermeiden)
		header("Location: {$_SERVER["REQUEST_URI"]}");
	}
	else if($_POST["acti"] == "num")
	{
		conf :: ediItem(intval($_POST["posi"]), "coun", intval($_POST["coun"]));
		/// leite an sich selbst weiter (um Informationsdoppelsenden zu vermeiden)
		header("Location: {$_SERVER["REQUEST_URI"]}");
	}
	else if($_POST["acti"] == "del")
	{
		conf :: delItem(intval($_POST["posi"]));
		/// leite an sich selbst weiter (um Informationsdoppelsenden zu vermeiden)
		header("Location: {$_SERVER["REQUEST_URI"]}");
	}
	/// Vaersandsland geändert
	else if($_POST["acti"] == "lnd")
	{
		conf :: setLandPosi(intval($_POST["land"]));
		$landData = conf :: getLandData();
		$rapidProcessing = sanitize_boolean($_POST["rapidProcessing"] ?? false);
		conf :: setOrderOptionValue("order.options-rapid.processing", $rapidProcessing);
		
		// Sanitize user data
		$firm = sanitize_input($_POST["firm"] ?? '');
		$fnam = sanitize_input($_POST["fnam"] ?? '');
		$lnam = sanitize_input($_POST["lnam"] ?? '');
		$stre = sanitize_input($_POST["stre"] ?? '');
		$hous = sanitize_input($_POST["hous"] ?? '');
		$post = sanitize_input($_POST["post"] ?? '');
		$city = sanitize_input($_POST["city"] ?? '');
		$phon = sanitize_input($_POST["phon"] ?? '');
		$emai = sanitize_email($_POST["emai"] ?? '');
		$comm = sanitize_input($_POST["comm"] ?? '');
		$paym = sanitize_input($_POST["paym"] ?? '');
		
		conf :: setUserData($firm, $fnam, $lnam, $stre, $hous, $post, $city, $landData["LAND"], $phon, $emai, $comm, $paym);
		/// leite an sich selbst weiter (um Informationsdoppelsenden zu vermeiden)
		header("Location: {$_SERVER["REQUEST_URI"]}");
	}
	/// Artikel gekauft
	else if($_POST["acti"] == "usr")
	{
		// SICHERHEIT: E-Mail-Adresse validieren
		if (empty($_POST["emai"]) || !filter_var($_POST["emai"], FILTER_VALIDATE_EMAIL)) {
			die("<h3>FEHLER: Bitte geben Sie eine gültige E-Mail-Adresse ein.</h3>");
		}
		
		// SICHERHEIT: Pflichtfelder prüfen
		if (empty($_POST["fnam"]) || empty($_POST["stre"]) || empty($_POST["post"]) || empty($_POST["city"])) {
			die("<h3>FEHLER: Bitte füllen Sie alle Pflichtfelder aus.</h3>");
		}

		// --- ZÄHLER LOGIK START ---
		$counterFile = ORDER_COUNTER_FILE;
		$startCount = INITIAL_ORDER_COUNTER; // Startwert falls Datei leer

		// Zähler laden mit File-Locking für atomare Read-Modify-Write Operation
		$count = $startCount;
		$fp = fopen($counterFile, 'c+');
		if ($fp === false) {
			error_log("Fehler beim Öffnen der Counter-Datei: " . $counterFile);
			die(ORDER_ERROR_MESSAGE);
		}
		
		// Exklusiver Lock für die gesamte Operation
		if (!flock($fp, LOCK_EX)) {
			error_log("Fehler beim Sperren der Counter-Datei");
			fclose($fp);
			die(ORDER_ERROR_MESSAGE);
		}
		
		// Lese aktuellen Zählerstand
		$fileStat = fstat($fp);
		if ($fileStat !== false && $fileStat['size'] > 0) {
			$jsonContent = fread($fp, $fileStat['size']);
			if ($jsonContent !== false && !empty($jsonContent)) {
				$data = json_decode($jsonContent, true);
				if (json_last_error() === JSON_ERROR_NONE && isset($data['counter'])) {
					$count = intval($data['counter']);
				} else {
					error_log("JSON-Dekodierungsfehler in Counter-Datei: " . json_last_error_msg());
					flock($fp, LOCK_UN);
					fclose($fp);
					die(ORDER_ERROR_MESSAGE);
				}
			}
		}

		// Hochzählen
		$count++;
		
		// Speichern mit atomarer Operation
		$newData = json_encode(['counter' => $count]);
		if ($newData === false) {
			error_log("Fehler beim JSON-Kodieren des Counter-Werts: " . json_last_error_msg());
			flock($fp, LOCK_UN);
			fclose($fp);
			die(ORDER_ERROR_MESSAGE);
		}
		
		if (ftruncate($fp, 0) === false || rewind($fp) === false) {
			error_log("Fehler beim Vorbereiten der Counter-Datei für Schreibvorgang");
			flock($fp, LOCK_UN);
			fclose($fp);
			die(ORDER_ERROR_MESSAGE);
		}
		
		$writeResult = fwrite($fp, $newData);
		if ($writeResult === false) {
			error_log("Fehler beim Schreiben der Counter-Datei");
			flock($fp, LOCK_UN);
			fclose($fp);
			die(ORDER_ERROR_MESSAGE);
		}
		
		// Lock freigeben und Datei schließen
		flock($fp, LOCK_UN);
		fclose($fp);

		// Auftragsnummer generieren (z.B. C-1001)
$orderID = "C-" . $count;
		
		// Sanitize user data
		$landData = conf :: getLandData($_POST["firm"]);
		$rapidProcessing = sanitize_boolean($_POST["rapidProcessing"] ?? false);
		conf :: setOrderOptionValue("order.options-rapid.processing", $rapidProcessing);
		
		$firm = sanitize_input($_POST["firm"] ?? '');
		$fnam = sanitize_input($_POST["fnam"] ?? '');
		$lnam = sanitize_input($_POST["lnam"] ?? '');
		$stre = sanitize_input($_POST["stre"] ?? '');
		$hous = sanitize_input($_POST["hous"] ?? '');
		$post = sanitize_input($_POST["post"] ?? '');
		$city = sanitize_input($_POST["city"] ?? '');
		$phon = sanitize_input($_POST["phon"] ?? '');
		$emai = sanitize_email($_POST["emai"] ?? '');
		$comm = sanitize_input($_POST["comm"] ?? '');
		$paym = sanitize_input($_POST["paym"] ?? '');
		
		// User hinzufügen (Daten speichern)
		conf :: setUserData($firm, $fnam, $lnam, $stre, $hous, $post, $city, $landData["LAND"], $phon, $emai, $comm, $paym);
		
		// WICHTIG: Hier übergeben wir die $orderID an die Mail-Funktion!
		if(!conf :: senConfMess($orderID))
		{
			die("<h3>Bei dem Versenden des E-Mails ist ein Fehler aufgetreten. Bitte versuchen Sie die Seite neu zu laden oder kontaktieren Sie unsere Supportdienst.</h3>");
		}
		
		/// leite an PayPal weiter (falls kein PayPal Funktion wird übersprungen)
		conf :: redPaypSite();
		/// leite an sich selbst weiter (um Informationsdoppelsenden zu vermeiden)
		header("Location: {$_SERVER["REQUEST_URI"]}");
	}
	/// Artikel bearbeiten
	else if($_POST["acti"] == "edi")
	{
		$itemPosi = intval($_POST["posi"]);
	}
}



/// lokalisiere Seite und bereite Variablen vor
$itemEditTrue = "";
switch ($site)
{
	case "edt":
		$itemList = conf :: getItemList();
		$foilData = $face -> getFoilData();
		$itemEditText = ($itemPosi > -1) ? ($itemList[$itemPosi]["text"]) : ("");
		$itemEditFont = ($itemPosi > -1) ? ($itemList[$itemPosi]["font"]) : ("artA");
		$itemEditFoil = ($itemPosi > -1) ? (($itemList[$itemPosi]["foil"]) ? ("true") : ("false")) : ("false");
		$itemEditTrue = ($itemPosi > -1) ? (($itemList[$itemPosi]["true"] == "true") ? ("document.getElementById('{$checkBox[0]["name"]}').click()") : ("")) : ("");
		break;
	case "bsk":
		$itemList = conf :: getItemList();
		$landList = conf :: getLandList();
		$landData = conf :: getLandData();
		$userData = conf :: getUserData();
		$imprText = conf :: getImprText();
		$orderOptions = conf :: getOrderOptions();			/// Bestellungsoptionen
		$orderOptionsJSON = json_encode($orderOptions);		
		$orderOptionsChecked = array();						/// Checked

		foreach($orderOptions as $name => $orderOption)
		{
			$orderOptionsChecked[$name] = $orderOption["value"] ? "checked" : "";
		}

		break;
	case "buy":
		$paydSucc = (isset($_GET["pay"]) && $_GET["pay"] == "suc") ? (true) : (false);
		conf :: cleItemData();
		break;
	default:
		$prodList = conf :: getProdList();
}





echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<!-- Meta -->
		<meta charset = 'utf-8'>
		<link rel="icon" type="image/x-icon" href="favicon.ico">
EOT;

/// wenn Mobilgerät sende zusatz Metainformation
if($mobi)
{
	echo <<<EOT
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
EOT;
}

/// $site
if($site == "edt")
{
	echo <<<EOT
		<!-- JavaScripts -->
		<script src = 'JS/librEDT.js?{$version}'></script>
		<script>
			foilData.UNITPRIC = {$foilData["PRIC"]};
			foilData.UNITLENG = {$foilData["LENG"]};
		</script>
EOT;
	
	if($itemPosi > -1)
	{
		echo <<<EOT
		<script>
			itemData.font = '{$itemEditFont}';
			itemData.foil = {$itemEditFoil};
			itemData.text = '{$itemEditText}';
			textTrigger.text = '{$itemEditText}';
			
		</script>
EOT;
	}
}
else if($site == "bsk")
{
	echo <<<EOT
		<!-- JavaScripts -->
		<script src = 'JS/librBSK.js?{$version}'></script>
EOT;
	if(is_array($itemList) && count($itemList) > 0)
	{
		echo <<<EOT
		<script>
			shopData.shipCost = {$landData["cost"]};
EOT;

		foreach($itemList as $itemPosi => $item)
		{
			echo <<<EOT
			itemData[{$itemPosi}] = {$item["pricsumm"]};
EOT;
		}
		
		if($userData["paym"] == "prepay")
		{
			echo <<<EOT
			shopData.paymMeth = 'PrePay';
EOT;
		}
		else if($userData["paym"] == "paypal")
		{
			echo <<<EOT
			shopData.paymMeth = 'PayPal';
EOT;
		}
		
		echo <<<EOT
			orderOptions = {$orderOptionsJSON};
		</script>
EOT;
	}
}


/// wenn Mobilgerät schlate CSS für Mobilgerät
if($mobi)
{
	echo <<<EOT
		<!-- StyleSheets -->
		<link href = 'CSS/stylMobi.css?{$version}' rel = 'stylesheet' type = 'text/css'>
EOT;
}
/// andernfalls schlate CSS für Desktop
else
{
	echo <<<EOT
		<link href = 'CSS/stylDesk.css?{$version}' rel = 'stylesheet' type = 'text/css'>
		
EOT;
}


echo <<<EOT
		<!-- Fonts -->
		<link href = 'CSS/font.css?{$version}' rel = 'stylesheet' type = 'text/css'>
	</head>
EOT;


/// Kopfbereich
echo <<<EOT
	<body>
		<div id = head>
			<div class = 'headImag'><a href = 'http://www.chrombeschriftung.de' target = '_self'><img  src = 'IMG/logo.png'></a></div>
			<div style="color:white; font-size: 20px; padding-bottom:14px"> <b>ChromSchrift-Generator</b></div>
			<div style="color:white; font-size: 16px; padding-bottom:20px">
				<b><u>Chrombuchstaben-Autobeschriftung mit unserem Generator</u></b>
				<p>Bei uns ist die Bestellung einer preiswerten Chrombeschriftung leicht und unkompliziert.</br>Nutzen Sie hierzu unseren online Chrombeschriftungs-Generator unten.</p>
				<p>Hier können Sie zwischen verschiedenen Größen, Groß- und Kleinbuchstaben,<br>kursiven und geraden Schriftarten auswählen.</p>
				<p>Ein weiterer Vorteil ist die Vorschau, auch ohne Bestellung können Sie sofort sehen,<br>wie lang die von Ihnen eingegebene Beschriftung wird, wie sie aussieht und was sie kostet.<br>Die Chrombeschriftung ist wetterfest und für die Waschanlage geeignet!</p>
				<p style = "margin-bottom: 2px;"><u>Ihre Vorteile:</u></p>
				<ul style = "margin-top: 0px;">
					<li>Alle Chrombuchstaben sind "selbstklebend",</li>
					<li>Jeder Text wird zusammenhängend geliefert (wenn Sie die Trägerfolienanbringung auswählen),</li>
					<li>Ein Qualitäts-Chrombuchstabe kostet nur ab 1,25 €,</li>
					<li>Ohne Anbringung auf Trägerfolie (einzeln verpackt),</li>
					<li>Kostenlose Lieferung innerhalb Deutschlands,</li>
					<li>Sie zahlen garantiert nicht mehr als angezeigt.</li>
				</ul>
				<p>Probieren Sie es jetzt aus......<br>Geben Sie jetzt gleich unten Ihren Text ein, ganz einfach.</p>
			</div>
		</div>
EOT;

if($site == "edt")
{
	echo <<<EOT
		<div id = 'main'>
			<!-- Vorschaubild -->
			<div id = 'prev' >
				<div id = 'cont' class = 'cont'><img id = 'imag' class = 'imag' src = 'IMG/carB.jpg'></div>
				<pre id = 'shad' class = 'shad'></pre>
				<pre id = 'text' class = 'text'></pre>
				<pre id = 'meas' style = 'position: absolute; z-index: -100; color: #171717;'></pre>
			</div>
			
			
			
			<!-- Schriftauswahl -->
			<div id = 'font'>
				<div class = 'inpu'>
					<p style = "padding-top: 15px; margin: 0px; font-size: 10px ">Abweichungen in Darstellung und Größe möglich</p>
					<input id = 'inpu' type = 'text' name = 'inpu' value = '{$itemEditText}' autocomplete = 'off' oninput = 'inpuText(this);' placeholder = 'Bitte geben Sie Ihren Text hier ein.....'>
				</div>
EOT;

	foreach(conf::$prodList as $prod)
	{
		$pric = number_format($prod["pric"], 2, ",", " ");
		$size = number_format($prod["size"], 1, ",", " ");
		
		echo <<<EOT
				<div id = '{$prod["idid"]}' class = 'tile' onclick = 'seleText(this);' title = '{$prod["titl"]}. Größe: {$size} cm. Preis pro Buchstabe: {$pric} €'>
					<div class = 'imag'><img src = '{$prod["imag"]}'></div>
					<!--div>{$prod["titl"]}</div>
					<div>Größe: {$size} cm</div>
					<div>Preis pro Buchstabe: {$pric} €</div-->
					<input id = '{$prod["idid"]}titl' type = 'hidden' value = '{$prod["titl"]}'>
					<input id = '{$prod["idid"]}pric' type = 'hidden' value = '{$prod["pric"]}'>
					<input id = '{$prod["idid"]}size' type = 'hidden' value = '{$prod["size"]}'>
					<input id = '{$prod["idid"]}uppe' type = 'hidden' value = '{$prod["uppe"]}'>
					<input id = '{$prod["idid"]}lowe' type = 'hidden' value = '{$prod["lowe"]}'>
					<input id = '{$prod["idid"]}ital' type = 'hidden' value = '{$prod["ital"]}'>
					<input id = '{$prod["idid"]}numb' type = 'hidden' value = '{$prod["numb"]}'>
					<input id = '{$prod["idid"]}symb' type = 'hidden' value = '{$prod["symb"]}'>
				</div>
EOT;
	}
	
	
	
	echo <<<EOT
				<div style = 'clear: both;'></div>
				
				<div class = 'fontInfo'>
					<div id = 'fontInfoTitl' class = 'dispInliBlock'>Großbuchtaben</div>
					<div class = 'dispInliBlock'>Größe: <span id = 'fontInfoSize'>214</span> cm. Preis pro Buchstabe: <span id = 'fontInfoPric'>22</span> €</div>
				</div>
			</div>
			
			<!-- Information -->
			<div id = 'info'>
				<div class = 'sele' style = "max-width: 360px;">
					<span class = 'fo18'>Optionen:</span>
					<ul>
						<li><label><input id = 'foiF' type = 'radio' name = 'foil' value = '0' onclick = "optiText(this); document.getElementById('trueLengthList').style.visibility = 'hidden';">Ohne Anbringung auf Trägerfolie</label></li>
						<br>
						<li><label><input id = 'foiT' type = 'radio' name = 'foil' value = '1' onclick = "optiText(this); document.getElementById('trueLengthList').style.visibility = '';">Mit Anbringung auf Trägerfolie (zzgl. <span id = 'foilPric'>4.90</span> €)</label></li>
					</ul>
					
					<ul id = "trueLengthList">
						<li style = "font-weight: 400; font-size: 13px; white-space: pre;"><label><input id = '{$checkBox[0]["name"]}' type = 'checkbox' name = '' value = '1' onclick = 'optiText(this);'>Trägerfolientext in Einzelbuchstaben auseinander gezogen (zzgl. <span id = 'truePrice'>9.90</span> €)</label></li>
					</ul>
					
					<script>{$itemEditTrue}</script>
					
					<input id = 'artZcost' type = 'hidden' value = '{$opti["cost"]}'>
					<span class = 'fo18'>Schriftzuglänge: </span>
					<span id = 'leng' class = 'fo18'>12,20</span>
					<span class = 'fo18'> cm</span>
				</div>
				<div class = 'plac'></div>
				<div class = 'conf'>
					<div>
						<span>Ihr Preis:</span><br>
						<span id = 'pric'>13,30</span><span> Euro</span><br>
						<span>Preis inkl. MwSt.</span><br>
					</div>
					<button onclick = 'onFormSend();'>Speichern und weiter</button>
					
EOT;
	
	if(is_array($itemList) && count($itemList) > 0)
	{
		echo <<<EOT
					<form action = 'gene.php?mod=bsk' method = 'post'>
						<button onclick = 'this.form.submit()'>Warenkorb</button>
					</form>
EOT;
	}

	echo <<<EOT
					<p style = "background-color: rgba(232,232,232,1.0);">Sie können später weitere Schriftzüge hinzufügen.</p>
				</div>
			</div>
			
			<!-- Form -->
			<form id = 'formPara' action = 'gene.php?mod=bsk' method = 'post'>
				<input id = 'paraId'   type = 'hidden' name = 'font'   value = ''>
				<input id = 'paraText' type = 'hidden' name = 'text' value = ''>
				<input id = 'paraChar' type = 'hidden' name = 'char' value = ''>
				<input id = 'paraFoil' type = 'hidden' name = 'foil' value = ''>
				<input id = 'paraTrue' type = 'hidden' name = 'true' value = ''>
				<input id = 'paraLeng' type = 'hidden' name = 'leng' value = ''>
				<input type = 'hidden' name = 'acti' value = 'add'>
				<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
			</form>
		</div>
EOT;
}
else if($site == "bsk")
{
	echo <<<EOT
		<div id = 'main'>
			<div class = "itemList">
EOT;

	if(is_array($itemList) && count($itemList) > 0)
	{
		if($mobi)
		{
			foreach($itemList as $itemPosi => $item)
			{
				echo <<<EOT
				<div class = 'itemTabl'>
					<div class = 'itemLabe'>Ihr Text: </div>
					<div class = 'itemValu'>{$item["CHAR"]}</div>
					<div style = 'clear: both'>
					<div class = 'itemLabe'>Produkt: </div>
					<div class = 'itemValu'>{$item["TITL"]}</div>
					<div style = 'clear: both'>
					<div class = 'itemLabe'>Größe: </div>
					<div class = 'itemValu'>{$item["SIZE"]}</div>
					<div style = 'clear: both'>
					<div class = 'itemLabe'>Folie: </div>
					<div class = 'itemValu'>{$item["FOIL"]}</div>
					<div class = 'itemLabe'>Wahre Länge: </div>
					<div class = 'itemValu'>{$item["TRUE"]}</div>
					<div style = 'clear: both'>
					<div class = 'itemLabe'></div>
					<!-- div class = 'itemLabe'>Anzahl: </div -->
					<div class = 'itemValu'>
						<!--form id = 'numItem' action = 'gene.php?mod=bsk' method = 'post'>
							<select name = 'coun' onchange = 'this.form.submit();'-->
EOT;

					/*for($i = 1; $i <= 5; $i++)
					{
						$atriSele = ($item["coun"] == $i) ? ("selected") : ("");
						
						echo <<<EOT
								<option value = '{$i}' {$atriSele}>{$i}</option>
EOT;
					}
					
					for($i = 10; $i <= 50; $i += 10)
					{
						$atriSele = ($item["coun"] == $i) ? ("selected") : ("");
						
						echo <<<EOT
								<option value = '{$i}' {$atriSele}>{$i}</option>
EOT;
					}*/
				
					echo <<<EOT
							<!-- /select>
							<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
							<input type = 'hidden' name = 'acti' value = 'num'>
						</form-->
					</div>
					<div style = 'clear: both'>
					<div class = 'itemLabe'>Preis: </div>
					<div class = 'itemValu'>{$item["PRICSUMM"]} €</div>
					<div style = 'clear: both'>
					
					<div class = 'itemActi'>
						<form  id = 'ediItem' action = 'gene.php?mod=edt' method = 'post'>
								<button class = '_buttEdit' onclick = 'onItemEdit(this); return false;'>ändern</button>
								<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
								<input type = 'hidden' name = 'acti' value = 'edi'>
						</form>
						<form id = 'delItem' action = 'gene.php?mod=bsk' method = 'post'>
								<button class = '_buttDele' onclick = 'onItemDele(this); return false;'>löschen<!--img  src = 'IMG/dele.png'--></button>
								<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
								<input type = 'hidden' name = 'acti' value = 'del'>
						</form>
					</div>
				</div>
EOT;
			}
		}
		else
		{
			echo <<<EOT
				<table class = 'itemTabl'>
					<tr>
						<th class = 'itemLabe'>Ihr Text</th>
						<th class = 'itemLabe'></th>
						<th class = 'itemLabe'>Produkt</th>
						<th class = 'itemLabe'>Größe</th>
						<th class = 'itemLabe'>Folie</th>
						<th class = 'itemLabe'>Wahre Länge</th>
						<!--th class = 'itemLabe'></th-->
						<th class = 'itemLabe'>Anzahl</th>
						<th class = 'itemLabe'></th>
						<th class = 'itemLabe'>Preis</th>
					</tr>
EOT;
			
			foreach($itemList as $itemPosi => $item)
			{
				echo <<<EOT
					<tr>
						<td class = 'itemValu'><div>{$item["CHAR"]}</div></td>
						<td class = 'itemValu'>
							<form  id = 'ediItem' action = 'gene.php?mod=edt' method = 'post'>
								<button class = '_buttEdit' onclick = 'onItemEdit(this); return false;'>ändern</button>
								<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
								<input type = 'hidden' name = 'acti' value = 'edi'>
							</form>
						</td>
						<td class = 'itemValu'>{$item["TITL"]}</td>
						<td class = 'itemValu'>{$item["SIZE"]} cm</td>
						<td class = 'itemValu'>{$item["FOIL"]}</td>
						<td class = 'itemValu'>{$item["TRUE"]}</td>
						<td class = 'itemValu'>
							<form id = 'numItem' action = 'gene.php?mod=bsk' method = 'post'>
								<select name = 'coun' onchange = 'this.form.submit();'>
EOT;
/// Anzahl der Schriftzüge
				for($i = 1; $i < 10; $i++)
				{
					$atriSele = ($item["coun"] == $i) ? ("selected") : ("");
						
					echo <<<EOT
									<option value = '{$i}' {$atriSele}>{$i}</option>
EOT;
				}
				
				for($i = 10; $i <= 50; $i += 10)
				{
					$atriSele = ($item["coun"] == $i) ? ("selected") : ("");
						
					echo <<<EOT
									<option value = '{$i}' {$atriSele}>{$i}</option>
EOT;
				}
				
				
				echo <<<EOT
								</select>
								<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
								<input type = 'hidden' name = 'acti' value = 'num'>
							</form>
						</td>
						<td class = 'itemValu'>
							<form id = 'delItem' action = 'gene.php?mod=bsk' method = 'post'>
								<button class = '_buttDele' onclick = 'onItemDele(this); return false;'>löschen<!--img  src = 'IMG/dele.png'--></button>
								<input type = 'hidden' name = 'posi' value = '{$itemPosi}'>
								<input type = 'hidden' name = 'acti' value = 'del'>
							</form>
						</td>
						<td class = 'itemValu'>{$item["PRICSUMM"]} €</td>
					</tr>
EOT;
			}
		}
			
		echo <<<EOT
				</table>	
EOT;
	}
	else
	{
		echo <<<EOT
				<div>
					<div style = 'padding: 16px; text-align: center; font-size: 32px; font-weight: 600;'>Keine Artikel vorhanden. Bitte geben Sie zuerst Ihren Wunschschriftzug ein.</div>
				</div>
EOT;
	}

	$shippingLanId = conf :: getShippingLandId();
	$freeShipping = conf :: isFreeShipping();
	$freeShippingMessageHtml = "";
  $totalCost = conf :: getTotaPric() + conf :: getOthePric();

	if($shippingLanId == 3 && $freeShipping == false){
      $freeShippingMessageHtml = "<div style = \"color: red; text-align: center;\">10€ Mindestbestellwert nicht erreicht</div>";
	}
  else if($shippingLanId != 3 && $totalCost < 9.99){
      $freeShippingMessageHtml = "<div style = \"color: red; text-align: center;\">10€ Mindestbestellwert nicht erreicht (+2.90€)</div>";
  }

	echo <<<EOT
			</div>
			<div class = 'overPaym'>

				<div id = 'paymShipCost' class = 'overValu'>0,00 €</div>
				<div class = 'overLabe'>Versandkosten:</div>
				{$freeShippingMessageHtml}
				<div id = 'paymOverCost' class = 'overValu' style = "display: none;">0,00 €</div>
				<div class = 'overLabe' style = "display: none;">PayPal-Gebühr:</div>
				<div id = 'paymDiscCost' class = 'overValu'>0,00 €</div>
				<div class = 'overLabe'>Rabatt:</div>
				<div id = 'paymTotaCost' class = 'overValu'>0,00 €</div>
				<div class = 'overLabe'>Gesamt:</div>
				<div id = 'paymTaxxCost' class = 'overValu'>0,00 €</div>
				<div class = 'overLabe'>darin enthaltene MwSt.:</div>
			</div>
			<div class = 'paymMeth'>
				<div align="center" style = "line-height: 44px; padding: 0 12%; text-align: left;">
					<!--span><label><input id = 'paymMethPrePay' type = 'radio' name = 'paymMeth' value = 'prepay' onclick = 'onPaymMethChan(this);'>Vorkasse (-5%)</label></span-->
					<span><label><input id = 'paymMethPrePay' type = 'radio' name = 'paymMeth' value = 'prepay' onclick = 'onPaymMethChan(this);'>Vorkassezahlung auf unser Bankkonto (-5%)</label></span>
					<br>
					<span><label><input id = 'paymMethPayPal' type = 'radio' name = 'paymMeth' value = 'paypal' onclick = 'onPaymMethChan(this);'>PayPal</label></span>
				</div>
				<div style = "line-height: 44px; padding: 0 12%; text-align: left;">
					<span><label><input type = 'checkbox' name = 'order.options-rapid.processing' value = 'true' {$orderOptionsChecked["order.options-rapid.processing"]} onclick = "setOrderOptionValue(this.name, this.checked); showTotalBill();">Schnell-Bearbeitung € 4,90</label></span>
				</div>
				<div>
					<form id = 'addItem' action = 'gene.php?mod=edt' method = 'post'>
						<input type = 'hidden' name = '' value = ''>
						<button style = 'font-size: 20px; background-color: rgba(32,216,0,1.0); border-radius: 4px; padding-left:15px; padding-right:15px'>...noch einen Schriftzug hinzufügen</button>
					</form>
				</div>
			</div>
		</div>
EOT;

	$shipShowAtri = (is_array($itemList) && count($itemList) > 0) ? ("") : ("style = 'display: none;'");

	echo <<<EOT
		<div id = 'ship' {$shipShowAtri}>
			<div>
			<!--form action = 'gene.php?mod=buy' method = 'post'-->
			<div>
				<div class = 'shipLabe'>Firma (optional): </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["firm"]}' class = 'inpuNumA' oninput = 'onUserInfoInpu(this, "firm");'>
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>Vor- und Nachname: <span style = 'color: red'>*</span> </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["fnam"]}' class = 'inpuNumA' oninput = 'onUserInfoInpu(this, "fnam");'>
					<!--input type = 'text' name = '' value = '{$userData["lnam"]}' class = 'inpuNumB' oninput = 'onUserInfoInpu(this, "lnam");'-->
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>Straße und Hausnr.: <span style = 'color: red'>*</span> </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["stre"]}' class = 'inpuNumA' oninput = 'onUserInfoInpu(this, "stre");'>
					<!--input type = 'text' name = '' value = '{$userData["hous"]}' class = 'inpuNumD' oninput = 'onUserInfoInpu(this, "hous");'-->
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>PLZ und Ort: <span style = 'color: red'>*</span> </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["post"]}' class = 'inpuNumD' oninput = 'onUserInfoInpu(this, "post");'>
					<input type = 'text' name = '' value = '{$userData["city"]}' class = 'inpuNumC' oninput = 'onUserInfoInpu(this, "city");'>
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>Land: <span style = 'color: red'>*</span> </div>
				<div class = 'shipValu'>
					<form action = 'gene.php?mod=bsk' method = 'post'>
						<input type = 'hidden' name = 'acti' value = 'lnd'>
						<select  class = 'inpuNumA' name = 'land' onchange = 'onUserInfoInpu(this, "land"); onShipLandChan(this);'>
EOT;
	
	foreach($landList as $landPosi => $land)
	{
		$atriSele = ($landPosi == $landData["land"]) ? ("selected") : ("");
		
		echo <<<EOT
							<option value = '{$landPosi}' {$atriSele}>{$land["LAND"]}</option>
EOT;
	}

	$rapidProcessingValue = $orderOptions["order.options-rapid.processing"]["value"] ? "true" : "false";
	
	echo <<<EOT
						</select>
					</form>
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>Telefonnummer: </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["phon"]}' class = 'inpuNumA' oninput = 'onUserInfoInpu(this, "phon");'>
				</div>
			</div>
			<div>
				<div class = 'shipLabe'>E-Mail: <span style = 'color: red'>*</span> </div>
				<div class = 'shipValu'>
					<input type = 'text' name = '' value = '{$userData["emai"]}' class = 'inpuNumA' oninput = 'onUserInfoInpu(this, "emai");'>
				</div>
			</div>
			<!--/form-->
			<form id = 'formUser' action = 'gene.php?mod=buy' method = 'post'>
				<input type = 'hidden' name = 'acti' value = 'usr'>
				<input type = 'hidden' name = 'firm' value = '{$userData["firm"]}'>
				<input type = 'hidden' name = 'fnam' value = '{$userData["fnam"]}'>
				<input type = 'hidden' name = 'lnam' value = '{$userData["lnam"]}'>
				<input type = 'hidden' name = 'stre' value = '{$userData["stre"]}'>
				<input type = 'hidden' name = 'hous' value = '{$userData["hous"]}'>
				<input type = 'hidden' name = 'post' value = '{$userData["post"]}'>
				<input type = 'hidden' name = 'city' value = '{$userData["city"]}'>
				<input type = 'hidden' name = 'land' value = '{$landData["LAND"]}'>
				<input type = 'hidden' name = 'phon' value = '{$userData["phon"]}'>
				<input type = 'hidden' name = 'emai' value = '{$userData["emai"]}'>
				<input type = 'hidden' name = 'comm' value = '{$userData["comm"]}'>
				<input type = 'hidden' name = 'paym' value = '{$userData["paym"]}'>
				<input type = 'hidden' name = 'rapidProcessing' value = '{$rapidProcessingValue}'>
			</form>
			</div>
			<div id = 'impr' style = 'display: block;'>
				
				
				
				
				
				
				<div class = 'imprUserComm'>
					<div class = ''>Hier können Sie zusätzliche Angaben zu Ihrer Bestellung machen.</div>
					<textarea class = '' name="comments" cols="70" rows="3"  oninput = 'onUserInfoInpu(this, "comm");'>{$userData["comm"]}</textarea>
				</div>
				
				<div class = 'imprUserText'>
					<div class="sub">Widerrufsbelehrung für Verbraucher: </div>
					<textarea class="agbArea" name="agbArea" readonly="readonly" cols="70" rows="7	">{$imprText}</textarea>
				</div>
				
				<div class = 'imprUserChec' style="text-align: left; padding: 10px 70px 10px 80px;">
					<input id = 'acceImpr' type="checkbox" style="margin-left: 0px;">
					<span>Ich habe die Widerrufsbelehrung gelesen und akzeptiere die <a style = "text-decoration:underline" target="_blank" href="http://shop.strato.de/epages/61026690.sf/de_AT/?ObjectPath=/Shops/61026690/Categories/TermsAndConditions"> Allgemeinen Geschäftsbedingungen</a></span>

          <div style="margin-top: 10px;">
            <input id = 'cbDataProt' type="checkbox" style="margin-left: 0px;">
					  <span>
              Ich habe die <a href="http://shop.strato.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/PrivacyPolicy" target="_blank">Datenschutzerklärung</a> zur Kenntnis genommen.<br/>
              Ich stimme einer elektronischen Speicherung und Verarbeitung meiner eingegebenen Daten zur Beantwortung meiner Anfrage zu.<br/>
              Hinweis: Die Einwilligung kann jederzeit für die Zukunft per E-Mail an  <a href="http://shop.strato.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/Kontaktformular" target="_blank">kontakt@chrombeschriftung.de</a>  widerrufen werden.
            </span>
          </div>
        </div>
				
				<div  class = 'imprUserSubm'>
					<button id = 'buttOrde' style = 'font-size: 22px;' onclick = 'onUserInfoSubm(this);'>Kostenpflichtig bestellen</button>
				</div>
				
				
				
				
				
				
				
				
			</div>
			
			<div style = 'clear: both;'></div>
		</div>
EOT;




}
else if($site = "buy")
{
	if($paydSucc)
	{
		echo <<<EOT
		<div id = 'main'>
			<div id = 'than'>
				<h1 style = 'padding: 16px; background-color: rgba(204,204,204,1.0); text-align: center;'>Die PayPal - Zahlung war erfolgreich.</h1>
				<h1 style = 'padding: 16px; background-color: rgba(204,204,204,1.0); text-align: center;'>Ihre Bestellung wurde erfolgreich entgegen genommen.<br>In kürze erhalten Sie eine E-Mail mit Ihren Bestelldaten.</h1>
        <h3 style = 'color: #00ff00; text-align: center;'>Wenn keine E-Mail kommt, prüfen Sie bitte Spam Ordner.</h3>
			</div>
		</div>
EOT;
	}
	else
	{
		echo <<<EOT
		<div id = 'main'>
			<div id = 'than'>
				<h1 style = 'padding: 16px; background-color: rgba(204,204,204,1.0); text-align: center;'>Ihre Bestellung wurde erfolgreich entgegen genommen.<br>In kürze erhalten Sie eine E-Mail mit Ihren Bestelldaten.</h1>
        <h3 style = 'color: #00ff00; text-align: center;'>Wenn keine E-Mail kommt, prüfen Sie bitte Spam Ordner.</h3>
			</div>
		</div>
EOT;
	}
	
}

/// Fussbereich
echo <<<EOT
		<div id = 'foot'>
			<div style="color: #ffffff; padding-top: 20px;" class = 'headTitl'>3D Autoaufkleber, Chrombuchstaben &  Firmenlogo-Herstellung individuell</div>
			<p>
EOT;
if($mobi)
{
echo <<<EOT
	
	<div>
		<p style = "/* text-align: justify; padding-right: 20px; */">Die verchromte Beschriftung ist oben gewölbt (konvex), die rückseitige Klebefläche ist aber eine gerade Fläche.</p>
		<p style = "/* text-align: justify; padding-right: 20px; padding-left: 20px; */">Hier können Sie nur glänzende CHROM-Schrift bestellen, direkt und schnell.</p>
		<p style = "padding: 0 0 8px 0;/* text-align: justify; padding-left: 20px; */">Alle anderen Größen und Farben bekommen Sie in unserem <a style=" color: #ffffff;" href = 'http://www.chrombeschriftung.de' target = '_self'>MBD-ChromShop</a> unter "Einzelbuchstaben kaufen".</p>
		<p style = "padding: 0 0 8px 0;/* text-align: justify; padding-right: 20px; */">Der Warenkorb bleibt leer.</p>
		<p style = "padding: 0 0 8px 0;/* text-align: justify; padding-right: 20px; padding-left: 20px; */">Lieferzeit ca. 2 Werktage</p>
		<p style = "padding: 0 0 8px 0;/* text-align: justify; padding-right: 20px; padding-left: 20px; */">Versand ist <u>kostenlos</u> in Deutschland<br>Ins Ausland kostet es 6,90 €</p>
		
		<p style = "/* text-align: justify; padding-right: 20px; */">Anbringung auf Trägerfolie bedeutet:</p>
		<p style = "/* text-align: justify; padding-right: 20px; padding-left: 20px; */">Erleichtern Sie sich daß Aufkleben Ihrer Schrift durch unseren Trägerfolien-Service. Wir übertragen Ihren Schriftzug so auf eine Hilfsträgerfolie daß Sie ihn leicht, gleichmäßig und gerade überall aufgeklebt werden können.</p>
		<p style = "padding: 0 0 8px 0;/* text-align: justify; padding-left: 20px; */">Ohne Anbringung auf Trägerfolie, bekommen Sie die eingegebenen Buchstaben einzeln in Polybeutel geliefert!</p>
		<p style = "padding: 0 0 8px 0;/* padding-right: 20px; padding-left: 20px; */"><a style=" color: #ffffff;" href = 'http://www.chrombeschriftung.de' target = '_self'>Hier geht es zurück zum MBD-ChromShop</a></p><p><a style=" color: #ffffff;" href="https://www.chrombeschriftung.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/PrivacyPolicy" target = '_blank'>Datenschutzerklärung</a></p>
	</div>
	
EOT;
}
else
{
	echo <<<EOT
				<hr></hr>
				
				<table>
				<tr>
					<td style = "vertical-align: top; width: 318px;">
						<p style="text-align: justify; padding-right: 20px;">Die verchromte Beschriftung ist oben gewölbt (konvex), die rückseitige Klebefläche ist aber eine gerade Fläche.</p>
					</td>
					<td style = "vertical-align: top; width: 318px;">
						<p style="text-align: justify; padding-right: 20px; padding-left: 20px;">Hier können Sie nur glänzende CHROM-Schrift bestellen, direkt und schnell.</p>
					</td>
					<td style = "vertical-align: top width: 318px;">
						<p style="text-align: justify; padding-left: 20px;">Alle anderen Größen und Farben bekommen Sie in unserem <a style=" color: #ffffff;" href = 'http://www.chrombeschriftung.de' target = '_self'>MBD-ChromShop</a> unter "Einzelbuchstaben kaufen".</p>
					</td>
					
				</tr>	
				</table>
				
				<hr></hr>
				
				<table>
				<tr>
					<td style = "vertical-align: top; width: 318px;">
						<p style="text-align: justify; padding-right: 20px;">Der Warenkorb bleibt leer.</p>
					</td>
					<td style = "vertical-align: top; width: 318px;">
						<p style="text-align: justify; padding-right: 20px; padding-left: 20px;">Lieferzeit ca. 2 Werktage</p>
						<p style="text-align: justify; padding-right: 20px; padding-left: 20px;">Versand ist <u>kostenlos</u> in Deutschland<br>Ins Ausland kostet es 6,90 €</p>
					</td>
					<td style = "vertical-align: top; width: 318px;">
						<p style=" padding-right: 20px; padding-left: 20px;"><a style=" color: #ffffff;" href = 'http://www.chrombeschriftung.de' target = '_self'>Hier geht es zurück zum MBD-ChromShop</a></p><p><a style=" color: #ffffff;" href="https://www.chrombeschriftung.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/PrivacyPolicy" target = '_blank'>Datenschutzerklärung</a></p>
					</td>
				</tr>
				</table>
				
				<hr></hr>
				
				<table>
				<tr>
					<td style = "vertical-align: top; width: 330px;">
						<p style="text-align: justify; padding-right: 20px;">Anbringung auf Trägerfolie bedeutet:</p>
					</td>
					<td style = "vertical-align: top; width: 330px;">
						<p style="text-align: justify; padding-right: 20px; padding-left: 20px;">Erleichtern Sie sich daß Aufkleben Ihrer Schrift durch unseren Trägerfolien-Service. Wir übertragen Ihren Schriftzug so auf eine Hilfsträgerfolie daß Sie ihn leicht, gleichmäßig und gerade überall aufgeklebt werden können.</p>
					</td>
					<td style = "vertical-align: top; width: 318px;">
						<p style="text-align: justify; padding-left: 20px;">Ohne Anbringung auf Trägerfolie, bekommen Sie die eingegebenen Buchstaben einzeln in Polybeutel geliefert!</p>
					</td>
				</tr>
				
				</table>
EOT;
}

	echo <<<EOT
			<img src = "IMG/schild.jpg" style = "max-width: 100%">
		</div>
	</body>
</html>
EOT;
?>