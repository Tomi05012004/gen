<?php

/*
Chromschriften-Generator
*/

require_once("PHP/mobiDete.php");

class conf
{
	public static $checkBox = array(array(	"name" 		=> "trueLength",
											"value" 	=> 9.90,
											"checked" 	=> false));

	public static $prodList = array();
	public static $opti = array();
	/// Liste der gekauften Artikeln
	// public static $itemList = (empty($_SESSION["itemList"])) ? (array()) : ($_SESSION["itemList"]);

	/// Mobilegrätte
	public static $mobi = false;
	/// Seite in IFrame
	public static $fram = false;


	/// Warenkorb
	public static $itemTotaPric = 0.00;


  public static $newMail;
	function setInstanz($mail){
    self::$newMail = $mail;
    }


	private static $mobiDete;

	public static function init()
	{
		self::$mobiDete = new Mobile_Detect();

		self::$prodList[0]["idid"] = "artA";
		self::$prodList[0]["titl"] = "Großbuchstaben";
		self::$prodList[0]["size"] = 1;
		self::$prodList[0]["pric"] = 0.95;
		self::$prodList[0]["uppe"] = 1;
		self::$prodList[0]["lowe"] = 0;
		self::$prodList[0]["ital"] = 0;
		self::$prodList[0]["imag"] = "IMG/GB1pre.png";
		self::$prodList[0]["numb"] = 1;
		self::$prodList[0]["symb"] = ".-";

		self::$prodList[1]["idid"] = "artB";
		self::$prodList[1]["titl"] = "Großbuchstaben";
		self::$prodList[1]["size"] = 2;
		self::$prodList[1]["pric"] = 1.15;
		self::$prodList[1]["uppe"] = 1;
		self::$prodList[1]["lowe"] = 0;
		self::$prodList[1]["ital"] = 0;
		self::$prodList[1]["imag"] = "IMG/GB2pre.png";
		self::$prodList[1]["numb"] = 1;
		self::$prodList[1]["symb"] = ".-@!?+&";

		self::$prodList[2]["idid"] = "artC";
		self::$prodList[2]["titl"] = "Großbuchstaben";
		self::$prodList[2]["size"] = 3;
		self::$prodList[2]["pric"] = 1.65;
		self::$prodList[2]["uppe"] = 1;
		self::$prodList[2]["lowe"] = 0;
		self::$prodList[2]["ital"] = 0;
		self::$prodList[2]["imag"] = "IMG/GB3pre.png";
		self::$prodList[2]["numb"] = 1;
		self::$prodList[2]["symb"] = ".-";

		self::$prodList[3]["idid"] = "artD";
		self::$prodList[3]["titl"] = "Groß- und Kleinbuchstaben";
		self::$prodList[3]["size"] = 5.6;
		self::$prodList[3]["pric"] = 3.4;
		self::$prodList[3]["uppe"] = 0;
		self::$prodList[3]["lowe"] = 0;
		self::$prodList[3]["ital"] = 0;
		self::$prodList[3]["imag"] = "IMG/GB56pre.png";
		self::$prodList[3]["numb"] = 1;
		self::$prodList[3]["symb"] = ".-";

		self::$prodList[4]["idid"] = "artE";
		self::$prodList[4]["titl"] = "Kleinbuchstaben";
		self::$prodList[4]["size"] = 2.5;
		self::$prodList[4]["pric"] = 1.25;
		self::$prodList[4]["uppe"] = 0;
		self::$prodList[4]["lowe"] = 1;
		self::$prodList[4]["ital"] = 0;
		self::$prodList[4]["imag"] = "IMG/GBKl25pre.png";
		self::$prodList[4]["numb"] = 0;
		self::$prodList[4]["symb"] = "-.:";

		self::$prodList[5]["idid"] = "artF";
		self::$prodList[5]["titl"] = "Kursive Buchstaben";
		self::$prodList[5]["size"] = 2.6;
		self::$prodList[5]["pric"] = 1.45;
		self::$prodList[5]["uppe"] = 1;
		self::$prodList[5]["lowe"] = 0;
		self::$prodList[5]["ital"] = 1;
		self::$prodList[5]["imag"] = "IMG/GBK26pre.png";
		self::$prodList[5]["numb"] = 1;
		self::$prodList[5]["symb"] = ".-@!?+&%()\"";

		self::$opti["cost"] = 4.9;
		self::$opti["leng"] = 20;

		self :: getOrderOptions();
	}

	public static function checMobi()
	{
		return self::$mobiDete -> isMobile();
	}

	public static function getProdList()
	{
		$respProdList = self::$prodList;

		for($i = 0; $i < count($respProdList); $i++)
		{
			$respProdList[$i]["SIZE"] = number_format($respProdList[$i]["size"], 1, ",", " ");
			$respProdList[$i]["PRIC"] = number_format($respProdList[$i]["pric"], 2, ",", " ");
		}

		return self::$prodList;
	}


	/// hole Bestellungsoptionen (gelten für die Bestellung sind Artikel bzw Versand unabhängig)
	/// diese Funktion soll vor der Verwendung andere Funktionen aufgerufen werden, da die Werte in Session setzt
	public static function getOrderOptions($calculate = false)
	{
		if(!isset($_SESSION))
			session_start();

		if(!isset($_SESSION["OrderOptions"]["order.options-rapid.processing"]))
			$_SESSION["OrderOptions"]["order.options-rapid.processing"] = false;										/// initialisiere Wert der Option

		$orderOptions = array();

		$orderOptions["order.options-rapid.processing"] = array
		(
			"value" => $_SESSION["OrderOptions"]["order.options-rapid.processing"],										/// Wert der Option
			"price" => 4.90 * (!$calculate || $_SESSION["OrderOptions"]["order.options-rapid.processing"] ? 1 : 0)		/// Preis der Option
		);

		return $orderOptions;
	}

	/// hole Bestellungsoption nach Name
	public static function getOrderOption($name)
	{
		$orderOptions = self :: getOrderOptions();

		if(isset($orderOptions[$name]))
			return $orderOptions[$name];

		return NULL;
	}

	/// hole Preis der Bestellungsoption nach Name
	public static function getOrderOptionPrice($name, $calculate = false)
	{
		$orderOptions = self :: getOrderOptions($calculate);

		if(isset($orderOptions[$name]))
			return $orderOptions[$name]["price"];

		return NULL;
	}

	/// hole Wert der Bestellungsoption nach Name
	public static function getOrderOptionValue($name)
	{
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION["OrderOptions"][$name]))
			return $_SESSION["OrderOptions"][$name];

		return NULL;
	}

	/// setze Wert der Bestellungsoption nach Name
	public static function setOrderOptionValue($name, $value)
	{
		if(!isset($_SESSION))
			session_start();

		if(isset($_SESSION["OrderOptions"][$name]))
			$_SESSION["OrderOptions"][$name] = (boolean)$value;
	}






	/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/// Warenkorbfunktionen
	public static function addItem($font, $text, $char, $foil, $true, $leng, $coun = 1)
	{
		$char = str_replace("&amp;", "&", $char);

		if(empty($_SESSION["itemList"]))
		{
			$_SESSION["itemList"] = array();
		}
		/// font - idid, text - user text, char - prepared text, foil - foil option, coun - item number
		$_SESSION["itemList"][] = array("font" => $font, "text" => $text, "char" => $char, "foil" => $foil, "true" => $true, "foilLength" => $leng, "coun" => $coun);
	}

	public static function ediItem($posi, $inde, $valu)
	{
		if(empty($_SESSION["itemList"][$posi]))
		{
			return;
		}

		echo $_SESSION["itemList"][$posi][$inde];

		$_SESSION["itemList"][$posi][$inde] = $valu;
	}

	public static function delItem($posi)
	{
		if(empty($_SESSION["itemList"][$posi]))
		{
			return;
		}

		unset($_SESSION["itemList"][$posi]);
	}

	public static function getItemList()
	{
		if(empty($_SESSION["itemList"]))
		{
			return false;
		}

		$_SESSION["itemList"] = array_values($_SESSION["itemList"]);
		$respItemList = array();

		self :: $itemTotaPric = 0.00;

		for($i = 0; $i < count($_SESSION["itemList"]); $i++)
		{
			$font = $_SESSION["itemList"][$i]["font"];
			$text = $_SESSION["itemList"][$i]["text"];
			$char = $_SESSION["itemList"][$i]["char"] = str_replace("&amp;", "&", $_SESSION["itemList"][$i]["char"]);
			$foil = $_SESSION["itemList"][$i]["foil"];
			$true = $_SESSION["itemList"][$i]["true"];
			$foilLength = $_SESSION["itemList"][$i]["foilLength"];
			$coun = $_SESSION["itemList"][$i]["coun"];

			$prodTitl = self :: getItemValu($font, "titl");
			$prodSize = self :: getItemValu($font, "size");
			$prodPric = self :: getItemValu($font, "pric");
			$foilPric = self::$opti["cost"];
			$foilLeng = self::$opti["leng"];

			$respItemList[$i] = $_SESSION["itemList"][$i];

			$respItemList[$i]["TEXT"] = $_SESSION["itemList"][$i]["text"];

			$respItemList[$i]["CHAR"] = $_SESSION["itemList"][$i]["char"];

			$respItemList[$i]["TITL"] = self :: getItemValu($_SESSION["itemList"][$i]["font"], "titl");



			/// Buchstabenhöhe als Text
			$respItemList[$i]["size"] = $prodSize;
			$respItemList[$i]["SIZE"] = number_format($respItemList[$i]["size"], 1, ",", " ");

			/// Folieoption als Text
			$respItemList[$i]["foil"] = $foil;
			$respItemList[$i]["FOIL"] = ($respItemList[$i]["foil"]) ? ("Ja") : ("Nein");

			/// Folieoption als Text
			$respItemList[$i]["true"] = $true == "true";
			$respItemList[$i]["TRUE"] = ($respItemList[$i]["true"]) ? ("Ja") : ("Nein");

			/// Anzahl der Schriftzüge
			$respItemList[$i]["coun"] = $coun;
			$respItemList[$i]["COUN"] = strval($respItemList[$i]["coun"]);

			/// Anzahl der Buchstaben im Schriftzug
			$respItemList[$i]["leng"] = self :: getTextLeng($char);
			// $respItemList[$i]["leng"] = self :: getTextLeng($text);
			$respItemList[$i]["LENG"] = strval($respItemList[$i]["leng"]);

			/// Price pro Schriftzug
			$respItemList[$i]["pricchar"] = self :: getTextPric($char, $prodPric);
			// $respItemList[$i]["pricchar"] = self :: getTextPric($text, $prodPric);
			$respItemList[$i]["PRICCHAR"] = number_format($respItemList[$i]["pricchar"], 2, ",", " ");

			/// Price pro Folie
			$respItemList[$i]["pricfoil"] = ($respItemList[$i]["foil"]) ? (self :: getFoilPric($char, $foilPric, 20)) : (0.00);
			$respItemList[$i]["PRICFOIL"] = number_format($respItemList[$i]["pricfoil"], 2, ",", " ");

			/// Preis pro WahreLänge
			$respItemList[$i]["prictrue"] = ($respItemList[$i]["true"] == "true") ? (self :: getFoilPric($char, 9.9, 20)) : (0.00);
			$respItemList[$i]["PRICTRUE"] = number_format($respItemList[$i]["prictrue"], 2, ",", " ");

			/// Gesamtpreis pro Schriftzug
			$respItemList[$i]["pricitem"] = $respItemList[$i]["pricchar"] + $respItemList[$i]["pricfoil"] + $respItemList[$i]["prictrue"];
			$respItemList[$i]["PRICITEM"] = number_format($respItemList[$i]["pricitem"], 2, ",", " ");

			/// Gesamtpreis für Artikel
			$respItemList[$i]["pricsumm"] = $respItemList[$i]["coun"] * ($respItemList[$i]["pricchar"] + $respItemList[$i]["pricfoil"] + $respItemList[$i]["prictrue"]);
			$respItemList[$i]["PRICSUMM"] = number_format($respItemList[$i]["pricsumm"], 2, ",", " ");

			$respItemList[$i]["FOILLENGTH"] = number_format($foilLength, 2, ",", " ");

			/// aufsummiere Gesamtpreis
			self :: $itemTotaPric += $respItemList[$i]["pricsumm"];
		}

		// var_dump($respItemList);die();

		return $respItemList;
	}


	public static function getTotaPric()
	{
		self :: getItemList();

		return self :: $itemTotaPric + self :: getOrderOptionPrice("order.options-rapid.processing", true);
	}


	public static function getDiscPric()
	{
		$landData = self :: getLandData();
		$totaPric = self :: getTotaPric();
		$userData = self :: getUserData();

		if($userData["paym"] == "prepay")
		{
			return round(5 * ($landData["cost"] + $totaPric)) / 100;
		}
		else
		{
			return 0.00;
		}
	}

	/// Zusatzgebühren
	public static function getOthePric()
	{
		$userData = self :: getUserData();

		if($userData["paym"] == "paypal")
		{
			return 0.00;
		}
		else
		{
			return 0.00;
		}
	}


	public function getMerkSteu($summ)
	{
		return round(($summ * 19 / 119) * 100) / 100;
	}


	private static function getItemValu($font, $inde)
	{
		foreach(self :: $prodList as $prod)
		{
			if($prod["idid"] == $font)
			{
				return $prod[$inde];
			}
		}
	}

	/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/// Versandfunktionen
	public static function getLandList()
	{
		$landList = array();

		$landList[0]['LAND']  = "Belgien";						$landList[0]['cost']  = 6.90;				$landList[0]["freeShippingAbove"]  = 9999;
		$landList[1]['LAND']  = "Bulgarien";					$landList[1]['cost']  = 6.90;				$landList[1]["freeShippingAbove"]  = 9999;
		$landList[2]['LAND']  = "Dänemark";						$landList[2]['cost']  = 6.90;				$landList[2]["freeShippingAbove"]  = 9999;
		$landList[3]['LAND']  = "Deutschland";				$landList[3]['cost']  = 2.90;				$landList[3]["freeShippingAbove"]  = 9.99;
		$landList[4]['LAND']  = "Estland";						$landList[4]['cost']  = 6.90;				$landList[4]["freeShippingAbove"]  = 9999;
		$landList[5]['LAND']  = "Finnland";						$landList[5]['cost']  = 6.90;				$landList[5]["freeShippingAbove"]  = 9999;
		$landList[6]['LAND']  = "Frankreich";					$landList[6]['cost']  = 6.90;				$landList[6]["freeShippingAbove"]  = 9999;
		$landList[7]['LAND']  = "Griechenland";				$landList[7]['cost']  = 9.90;				$landList[7]["freeShippingAbove"]  = 9999;
		$landList[8]['LAND']  = "Irland";					  	$landList[8]['cost']  = 6.90;				$landList[8]["freeShippingAbove"]  = 9999;
		$landList[9]['LAND']  = "Italien";						$landList[9]['cost']  = 6.90;				$landList[9]["freeShippingAbove"]  = 9999;
		$landList[10]['LAND'] = "Lettland";						$landList[10]['cost'] = 6.90;				$landList[10]["freeShippingAbove"] = 9999;
		$landList[11]['LAND'] = "Litauen";						$landList[11]['cost'] = 6.90;				$landList[11]["freeShippingAbove"] = 9999;
		$landList[12]['LAND'] = "Luxemburg";					$landList[12]['cost'] = 6.90;				$landList[12]["freeShippingAbove"] = 9999;
		$landList[13]['LAND'] = "Malta";					  	$landList[13]['cost'] = 6.90;				$landList[13]["freeShippingAbove"] = 9999;
		$landList[14]['LAND'] = "Niederlande";				$landList[14]['cost'] = 6.90;				$landList[14]["freeShippingAbove"] = 9999;
		$landList[15]['LAND'] = "Norway";					    $landList[15]['cost'] = 6.90;				$landList[15]["freeShippingAbove"] = 9999;
		$landList[16]['LAND'] = "Österreich";					$landList[16]['cost'] = 6.90;				$landList[16]["freeShippingAbove"] = 9999;
		$landList[17]['LAND'] = "Polen";					  	$landList[17]['cost'] = 6.90;				$landList[17]["freeShippingAbove"] = 9999;
		$landList[18]['LAND'] = "Portugal";						$landList[18]['cost'] = 6.90;				$landList[18]["freeShippingAbove"] = 9999;
		$landList[19]['LAND'] = "Rumänien";						$landList[19]['cost'] = 6.90;				$landList[19]["freeShippingAbove"] = 9999;
		$landList[20]['LAND'] = "Schweden";						$landList[20]['cost'] = 6.90;				$landList[20]["freeShippingAbove"] = 9999;
		$landList[21]['LAND'] = "Schweiz";						$landList[21]['cost'] = 6.90;				$landList[21]["freeShippingAbove"] = 9999;
		$landList[22]['LAND'] = "Slowakei";						$landList[22]['cost'] = 6.90;				$landList[22]["freeShippingAbove"] = 9999;
		$landList[23]['LAND'] = "Slowenien";					$landList[23]['cost'] = 6.90;				$landList[23]["freeShippingAbove"] = 9999;
		$landList[24]['LAND'] = "Spanien";						$landList[24]['cost'] = 6.90;				$landList[24]["freeShippingAbove"] = 9999;
		$landList[25]['LAND'] = "Tschechische Republik";		$landList[25]['cost'] = 6.90;				$landList[25]["freeShippingAbove"] = 9999;
		$landList[26]['LAND'] = "Ungarn";						        $landList[26]['cost'] = 6.90;				$landList[26]["freeShippingAbove"] = 9999;
		$landList[27]['LAND'] = "Vereinigtes Königreich";		$landList[27]['cost'] = 6.90;				$landList[27]["freeShippingAbove"] = 9999;
		$landList[28]['LAND'] = "Zypern";						        $landList[28]['cost'] = 6.90;				$landList[28]["freeShippingAbove"] = 9999;
		$landList[29]['LAND'] = "other Countries";	        $landList[29]['cost'] = 6.90;				$landList[29]["freeShippingAbove"] = 9999;

		return $landList;
	}

	public static function getLandData()
	{
		if(isset($_SESSION["shipData"]["land"]))
			$landPosi = $_SESSION["shipData"]["land"];
		else
			$landPosi = 3;

		//$userData = self :: getUserData();
		$landList = self :: getLandList();

		$totalCost = self :: getTotaPric() + self :: getOthePric();

		/*if($userData["paym"] == "prepay")
			$totalCost = 95 * $totalCost / 100;*/

		$shippingCost = $landList[$landPosi]["cost"];

		/// prüfe, ob Gratisversand grenze überschritten war
		if (self :: isFreeShipping()/*$totalCost > $landList[$landPosi]["freeShippingAbove"]*/)
			$shippingCost = 0.00;
    else if($landPosi != 3 && $totalCost < 9.99){
      $shippingCost += 2.90;
    }

		$respLandList = array();
		$respLandList["LAND"] = $landList[$landPosi]["LAND"];
		$respLandList["land"] = $landPosi;
		$respLandList["COST"] = number_format($shippingCost, 2, ",", " ");
		$respLandList["cost"] = $shippingCost;

		return $respLandList;
	}

	public static function isFreeShipping()
	{
		/// Versandland Id
		if(isset($_SESSION["shipData"]["land"]))
			$landPosi = $_SESSION["shipData"]["land"];
		else
			$landPosi = 3;

		$landList = self :: getLandList();

		$totalCost = self :: getTotaPric() + self :: getOthePric() - self :: getOrderOptionPrice("order.options-rapid.processing", true);

		/// prüfe, ob Gratisversand grenze überschritten war
		if ($totalCost > $landList[$landPosi]["freeShippingAbove"])
			return true;

		return false;
	}

	public static function getShippingLandId()
	{
		$landPosi = 3;
		/// Versandland Id
		if(isset($_SESSION["shipData"]["land"]))
			$landPosi = $_SESSION["shipData"]["land"];

		return $landPosi;
	}

	public static function setLandPosi($landPosi)
	{
		$_SESSION["shipData"]["land"] = $landPosi;
	}


	public static function getImprText()
	{
		$impr = "Widerrufsrecht
Sie haben das Recht, binnen dreißig Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen.
Die Widerrufsfrist beträgt dreißig Tage ab dem Tag, an dem Sie oder ein von Ihnen benannter Dritter, der nicht der Beförderer ist, die erste Ware in Besitz genommen haben bzw. hat. Um Ihr Widerrufsrecht auszuüben, müssen Sie uns ( Michael Brüggemann, Talblick 13 31848 Bad Münder, Telefon: 05042- 50 72- 57, Fax: 05042- 50 72- 53, E-Mail: kontakt@chrombeschriftung.de ) mittels einer eindeutigen Erklärung (z. B. ein mit der Post versandter Brief, Telefax oder E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren. Sie können dafür das beigefügte Muster-Widerrufsformular verwenden, das jedoch nicht vorgeschrieben ist.
Zur Wahrung der Widerrufsfrist reicht es aus, dass Sie die Mitteilung über die Ausübung des Widerrufsrechts vor Ablauf der Widerrufsfrist absenden.
Folgen des Widerrufs
Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten haben, einschließlich der Lieferkosten (mit Ausnahme der zusätzlichen Kosten, die sich daraus ergeben, dass Sie eine andere Art der Lieferung als die von uns angebotene, günstigste Standardlieferung gewählt haben), unverzüglich und spätestens binnen vierzehn Tagen ab dem Tag zurückzuzahlen, an dem die Mitteilung über Ihren Widerruf dieses Vertrags bei uns eingegangen ist. Für diese Rückzahlung verwenden wir dasselbe Zahlungsmittel, das Sie bei der ursprünglichen Transaktion eingesetzt haben, es sei denn, mit Ihnen wurde ausdrücklich etwas anderes vereinbart; in keinem Fall werden Ihnen wegen dieser Rückzahlung Entgelte berechnet. Wir können die Rückzahlung verweigern, bis wir die Waren wieder zurückerhalten haben oder bis Sie den Nachweis erbracht haben, dass Sie die Waren zurückgesandt haben, je nachdem, welches der frühere Zeitpunkt ist.
Sie haben die Waren unverzüglich und in jedem Fall spätestens binnen dreißig Tagen ab dem Tag, an dem Sie uns über den Widerruf dieses Vertrags unterrichten, an uns zurückzusenden oder zu übergeben. Die Frist ist gewahrt, wenn Sie die Waren vor Ablauf der Frist von dreißig Tagen absenden. Sie tragen die unmittelbaren Kosten der Rücksendung der Waren. Sie müssen für einen etwaigen Wertverlust der Waren nur aufkommen, wenn dieser Wertverlust auf einen zur Prüfung der Beschaffenheit, Eigenschaften und Funktionsweise der Waren nicht notwendigen Umgang mit ihnen zurückzuführen ist.
Das Widerrufsrecht besteht nicht bei Fernabsatzverträgen zur Lieferung von Waren, die nicht vorgefertigt sind und für deren Herstellung eine individuelle Auswahl oder Bestimmung durch den Verbraucher maßgeblich ist oder die eindeutig auf die persönlichen Bedürfnisse zugeschnitten sind.";

		return $impr;
	}


	/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/// Benutzerfunktionen
	public static function setUserData($firm, $fnam, $lnam, $stre, $hous, $post, $city, $land, $phon, $emai, $comm, $paym)
	{
		$_SESSION["userData"]["firm"] = $firm;
		$_SESSION["userData"]["fnam"] = $fnam;
		$_SESSION["userData"]["lnam"] = $lnam;
		$_SESSION["userData"]["stre"] = $stre;
		$_SESSION["userData"]["hous"] = $hous;
		$_SESSION["userData"]["post"] = $post;
		$_SESSION["userData"]["city"] = $city;
		$_SESSION["userData"]["land"] = $land;
		$_SESSION["userData"]["phon"] = $phon;
		$_SESSION["userData"]["emai"] = $emai;
		$_SESSION["userData"]["comm"] = $comm;
		$_SESSION["userData"]["paym"] = $paym;
	}


	public static function getUserData()
	{
		$userData = array();
		if(isset($_SESSION["userData"]) && is_array($_SESSION["userData"]))
		{
			$userData["firm"] = $_SESSION["userData"]["firm"];
			$userData["fnam"] = $_SESSION["userData"]["fnam"];
			$userData["lnam"] = $_SESSION["userData"]["lnam"];
			$userData["stre"] = $_SESSION["userData"]["stre"];
			$userData["hous"] = $_SESSION["userData"]["hous"];
			$userData["post"] = $_SESSION["userData"]["post"];
			$userData["city"] = $_SESSION["userData"]["city"];
			$userData["land"] = $_SESSION["userData"]["land"];
			$userData["phon"] = $_SESSION["userData"]["phon"];
			$userData["emai"] = $_SESSION["userData"]["emai"];
			$userData["comm"] = $_SESSION["userData"]["comm"];
			$userData["paym"] = $_SESSION["userData"]["paym"];
		}
		else
		{
			$userData["firm"] = "";
			$userData["fnam"] = "";
			$userData["lnam"] = "";
			$userData["stre"] = "";
			$userData["hous"] = "";
			$userData["post"] = "";
			$userData["city"] = "";
			$userData["land"] = "";
			$userData["phon"] = "";
			$userData["emai"] = "";
			$userData["comm"] = "";
			$userData["paym"] = "";
		}

		return $userData;
	}


	public static function getVendData()
	{
		$vendData["firm"] = "MBD-ChromShop";
		$vendData["fnam"] = "";
		$vendData["lnam"] = "";
		$vendData["stre"] = "";
		$vendData["hous"] = "";
		$vendData["post"] = "";
		$vendData["city"] = "";
		$vendData["land"] = "";
		$vendData["phon"] = "";
		$vendData["emai"] = "bestellung@chrombeschriftung.de";
		$vendData["comm"] = "";
		$vendData["paym"] = "";

		return $vendData;
	}



	/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/// E-Mail
	public static function senConfMess()
	{
		$vendData = self :: getVendData();
		$userData = self :: getUserData();
		$shipData = self :: getLandData();
		$prodDataList = self :: getProdList();
		$itemDataList = self :: getItemList();

		$landData = self :: getLandData();
		$totaPric = number_format(self :: getTotaPric() + $landData["cost"] - self :: getDiscPric() + self :: getOthePric(), 2, ",", " ");
		$mestPric = number_format(self :: getMerkSteu(self :: getTotaPric() + $landData["cost"] - self :: getDiscPric() + self :: getOthePric()), 2, ",", " ");
		$discPric = number_format(self :: getDiscPric(), 2, ",", " ");
		$othePric = number_format(self :: getOthePric(), 2, ",", " ");
		$rapidProcessingPrice = number_format(self :: getOrderOptionPrice("order.options-rapid.processing", true), 2, ",", " ");

		$nl = "\r\n";

		$head = "";
		$head .= "MIME-Version: 1.0{$nl}";
		$head .= "From: {$vendData["firm"]} <{$vendData["emai"]}>{$nl}";
		$head .= "Content-type: text/html; charset=utf-8{$nl}";
		$head .= "X-Mailer: PHP/".phpversion();


		/// Emailheader für Käufer
		$userHeader = "";
		$userHeader .= "MIME-Version: 1.0{$nl}";
		$userHeader .= "From: {$vendData["firm"]} <{$vendData["emai"]}>{$nl}";
		$userHeader .= "Content-type: text/html; charset=utf-8{$nl}";
		$userHeader .= "X-Mailer: PHP/".phpversion();

		/// Emailheader für Verkäufer
		$vendorHeader = "";
		$vendorHeader .= "MIME-Version: 1.0{$nl}";
		$vendorHeader .= "From: {$userData["fnam"]} {$userData["lnam"]} <{$userData["emai"]}>{$nl}";
		$vendorHeader .= "Content-type: text/html; charset=utf-8{$nl}";
		$vendorHeader .= "X-Mailer: PHP/".phpversion();



		$nl = "<br>";

		/// E-Mail BODY
		$mailBody = "";

		foreach($itemDataList as $itemData)
		{
			$mailBody .= "<hr>{$nl}";
			$mailBody .= "<table style=' width: 500px;'>";
			$mailBody .= "<tr>";
			$mailBody .= "<td style =' width: 400px;'>Produkt: {$itemData["TITL"]}, {$itemData["SIZE"]} cm{$nl}</td>";
			$mailBody .= "<td style='font-size: 20px; text-align: right;'>Preis</td>";
			$mailBody .= "</tr>";
			$mailBody .= "<tr>";
			$mailBody .= "<td>Bestellte Buchstaben: {$itemData["CHAR"]}{$nl}</td>";
			$mailBody .= "<td style='font-size: 20px; text-align: right;'>{$itemData["PRICSUMM"]} €{$nl}</td>";
			$mailBody .= "</tr>";
			$mailBody .= "<tr>";
			$mailBody .= "<td>Anzahl der Buchstaben: {$itemData["LENG"]}{$nl}</td>";
			$mailBody .= "</tr>";
			$mailBody .= "<tr>";
			$mailBody .= "<td>Schriftzüge: {$itemData["COUN"]}{$nl}</td>";
			$mailBody .= "</tr>";
			$mailBody .= "<tr>";
			$mailBody .= "<td>Anbringung auf Trägerfolie: {$itemData["FOIL"]}{$nl}</td>";
			$mailBody .= "</tr>";
			$mailBody .= "<tr>";
			$mailBody .= "<td>Wahre Länge: {$itemData["TRUE"]}{$nl}</td>";
			$mailBody .= "</tr>";

			if($itemData["true"] == "true")
			{
				$mailBody .= "<tr>";
				$mailBody .= "<td>Schriftzeichen:</td>";
				$mailBody .= "</tr>";
				$mailBody .= "<tr>";
				$mailBody .= "<td><pre>{$itemData["CHAR"]}</pre></td>";
				$mailBody .= "</tr>";
			}

			if($itemData["true"] == "true" || $itemData["foil"])
			{
				$mailBody .= "<tr>";
				$mailBody .= "<td>Schriftzuglänge: {$itemData["FOILLENGTH"]} cm</td>";
				$mailBody .= "</tr>";
			}


			$mailBody .= "</table>";


		}

		$shippingLanId = conf :: getShippingLandId();
		$freeShipping = conf :: isFreeShipping();
		$freeShippingMessageHtml = "";

		if($shippingLanId == 3 && $freeShipping == false)
			$freeShippingMessageHtml = "<div style = \"color: red; text-align: right;\">10€ Mindestbestellwert nicht erreicht</div>";

		$mailBody .= "<hr>{$nl}";
		$mailBody .= "<table style=' width: 500px;'>";
		$mailBody .= "<tr>";
		$mailBody .= "<td>Versandkosten: </td>";
		$mailBody .= "<td style=' text-align: right; font-size: 20px;'>&nbsp;&nbsp;{$shipData["COST"]} €{$nl}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "<td colspan = '2'>$freeShippingMessageHtml</td>";
		$mailBody .= "</tr>";

		if($userData["paym"] == "prepay")
		{
			$mailBody .= "<tr>";
			$mailBody .= "<td style=' width: 400px;'>Rabatt: </td>";
			$mailBody .= "<td style=' text-align: right; font-size: 20px;'>-{$discPric} €{$nl}</td>";
			$mailBody .= "</tr>";
		}
		else if($userData["paym"] == "paypal")
		{
			/*$mailBody .= "<tr>";
			$mailBody .= "<td style=' width: 400px;'>Paypal-Gebühren:</td>";
			$mailBody .= "<td style=' text-align: right; font-size: 20px;'>{$othePric} €{$nl}</td>";
			$mailBody .= "</tr>";*/
		}

		if(self :: getOrderOptionValue("order.options-rapid.processing"))
		{
			$mailBody .= "<tr>";
			$mailBody .= "	<td style=' width: 400px;'>Schnell-Bearbeitung: </td>";
			$mailBody .= "	<td style=' text-align: right; font-size: 20px;'>{$rapidProcessingPrice} €{$nl}</td>";
			$mailBody .= "</tr>";
		}

		$mailBody .= "</table>";


		$mailBody .= "{$nl}";

		$mailBody .= "<hr>{$nl}";
		$mailBody .= "<table style=' width: 500px;'>";
		$mailBody .= "<tr>";
		$mailBody .= "<td style=' width: 400px;'>Gesamtpreis:</td>";
		$mailBody .= "<td style=' text-align: right; font-size: 20px;'>{$totaPric} €{$nl}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "<td style=' width: 400px;'></td>";
		$mailBody .= "<td style=' width: 200px; text-align: right; font-size: 11px;'>(inkl. {$mestPric} € MwSt.){$nl}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "</table>";
		$mailBody .= "{$nl}";


		$mailBody .= "<hr>{$nl}";
		$mailBody .= "<table style=' width: 500px;'>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=' width: 400px;' colspan='2'><BIG><b>Wir liefern an folgende Adresse:{$nl}<b></BIG></td>";
		$mailBody .= "	<td style=''></td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>Firma: </td>";
		$mailBody .= "	<td style=''>{$userData["firm"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>Name: </td>";
		$mailBody .= "	<td style=''>{$userData["fnam"]} {$userData["lnam"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style='vertical-align: top;'>Anschrift:	</td>";
		$mailBody .= "	<td style=''>{$userData["stre"]} {$userData["hous"]}<br>{$userData["post"]} {$userData["city"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>Land: </td>";
		$mailBody .= "	<td style=''>{$userData["land"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>Telefon: </td>";
		$mailBody .= "	<td style=''>{$userData["phon"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>E-Mail: </td>";
		$mailBody .= "	<td style=''>{$userData["emai"]}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "<tr>";
		$mailBody .= "	<td style=''>{$nl}</td>";
		$mailBody .= "	<td style=''>{$nl}</td>";
		$mailBody .= "</tr>";
		$mailBody .= "</table>";



		/// E-Mail HEAD
		/// Käufer
		$userHead = "";
		$userHead .= "Hallo {$userData["fnam"]} {$userData["lnam"]},{$nl}";
		$userHead .= "{$nl}";
		$userHead .= "vielen Dank für Ihre Bestellung im MBD-Chromshop. Diese Artikel wurden bestellt:{$nl}";
		$userHead .= "{$nl}";

		/// Händler
		$vendHead = "";
		$vendHead .= "Hallo {$vendData["firm"]},{$nl}";
		$vendHead .= "{$nl}";
		$vendHead .= "folgende Artikel wurden bestellt:{$nl}";
		$vendHead .= "{$nl}";

		/// E-Mail FOOT
		/// Käufer
		$userFoot = "";

		if($userData["comm"])
		{
			$userFoot .= "Sie haben die folgenden zusätzlichen Daten zu Ihrer Bestellung angegeben:{$nl}";
			$userFoot .= "{$userData["comm"]}{$nl}";
			$userFoot .= "{$nl}";
		}

		if($userData["paym"] == "prepay")
		{
			//$userFoot .= "----------------------------------------------------------------------------{$nl}";
			$userFoot .= "<hr>{$nl}";
			$userFoot .= "BITTE ZAHLEN SIE PER VORKASSE AUF UNSER KONTO:{$nl}";
			$userFoot .= "MBD-Brüggemann{$nl}";
			$userFoot .= "IBAN: DE63254501100000807511{$nl}";
			$userFoot .= "BIC: NOLADE21SWB{$nl}";
			$userFoot .= "WICHTIG: Bitte geben Sie bei der Überweisung Ihren Bestellernamen an, Danke.{$nl}";
			$userFoot .= "{$nl}";
		}
		else if($userData["paym"] == "paypal")
		{
			//$userFoot .= "----------------------------------------------------------------------------{$nl}";
			$userFoot .= "<hr>{$nl}";
			$userFoot .= "FALLS BETRAG NOCH NICHT BEGLICHEN WURDE, ZAHLEN BITTE SIE PER PAYPAL AUF UNSER KONTO: {$vendData["emai"]}{$nl}";
			// $userFoot .= "E-Mail: {$nl}";
			$userFoot .= "{$nl}";
		}

		//$userFoot .= "----------------------------------------------------------------------------{$nl}";
		$userFoot .= "<hr>{$nl}<p><span>Eine Rechnung bekommen Sie mit der Lieferung.</span></p><br /><hr />";
		$userFoot .= "Mit freundlichen Grüßen{$nl}";
		$userFoot .= "MBD-Chromshop{$nl}";
		$userFoot .= "www.chrombeschriftung.de{$nl}";
		$userFoot .= "{$nl}";
		$userFoot .= "<hr>{$nl}";
		$userFoot .= <<<EOT
<p><strong><a href = "https://www.chrombeschriftung.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/PrivacyPolicy">Datenschutzerklärung</a></strong></p>
<p>oder <i>https://www.chrombeschriftung.de/epages/61026690.sf/de_DE/?ObjectPath=/Shops/61026690/Categories/PrivacyPolicy</i></p>

<p><span style=""><span style=""><strong>Widerrufsrecht</strong></span></span></p>
<br />
<p class=""><span style=""><span style="">Widerrufsbelehrung<br /> f&uuml;r Verbraucher </span></span></p>
<br />
<p><span style=""><span style="">Sie<br /> haben das Recht, binnen vierzehn Tagen ohne Angabe von<br /> Gr&uuml;nden diesen Vertrag zu widerrufen.<br /><br /> Die Widerrufsfrist betr&auml;gt vierzehn Tage ab dem Tag, an dem<br /> Sie oder ein von Ihnen benannter Dritter, der nicht der<br /> Bef&ouml;rderer ist, die Waren in Besitz genommen haben bzw. hat.Um Ihr Widerrufsrecht<br /> auszu&uuml;ben, m&uuml;ssen Sie uns (Michael Br&uuml;ggemann, Talblick 13,<br /> 31848 Bad M&uuml;nder, Telefon: 05042- 50 72- 57, Fax: 05042- 50<br /> 72- 53, E-Mail: <a class="moz-txt-link-abbreviated" href="mailto:kontakt@chrombeschriftung.de">kontakt@chrombeschriftung.de</a>) mittels einer<br /> eindeutigen Erkl&auml;rung (z. B. ein mit der Post versandter<br /> Brief, Telefax oder E-Mail) &uuml;ber Ihren Entschluss,
diesen<br /> Vertrag zu widerrufen, informieren. </span><span style="">Sie k&ouml;nnen daf&uuml;r das beigef&uuml;gte<br /> Muster-Widerrufsformular verwenden, das jedoch nicht<br /> vorgeschrieben ist.</span><span style="">Zur Wahrung der<br /> Widerrufsfrist reicht es aus, dass Sie die Mitteilung &uuml;ber<br /> die Aus&uuml;bung des Widerrufsrechts vor Ablauf der<br /> Widerrufsfrist absenden. </span></span></p>
<br />
<p><span style=""><span style=""><strong>Folgen<br /> des Widerrufs</strong></span></span></p>
<br />
<p class=""><span style=""><span style="">Wenn<br /> Sie diesen Vertrag widerrufen, haben wir Ihnen alle<br /> Zahlungen, die wir von Ihnen erhalten haben, einschlie&szlig;lich<br /> der Lieferkosten (mit Ausnahme der zus&auml;tzlichen Kosten, die<br /> sich daraus ergeben, dass Sie eine andere Art der Lieferung<br /> als die von uns angebotene, g&uuml;nstigste Standardlieferung<br /> gew&auml;hlt haben), unverz&uuml;glich und sp&auml;testens binnen vierzehn<br /> Tagen ab dem Tag zur&uuml;ckzuzahlen, an dem die Mitteilung &uuml;ber<br /> Ihren Widerruf dieses Vertrags bei uns eingegangen ist. </span></span></p>
<br />
<p><span style=""><span style="">F&uuml;r<br /> diese R&uuml;ckzahlung verwenden wir dasselbe Zahlungsmittel, das<br /> Sie bei der urspr&uuml;nglichen Transaktion eingesetzt haben, es<br /> sei denn, mit Ihnen wurde ausdr&uuml;cklich etwas anderes<br /> vereinbart; in keinem Fall werden Ihnen wegen dieser<br /> R&uuml;ckzahlung Entgelte berechnet. </span></span></p>
<br />
<p><span style=""><span style="">Wir<br /> k&ouml;nnen die R&uuml;ckzahlung verweigern, bis wir die Waren wieder<br /> zur&uuml;ckerhalten haben oder bis Sie den Nachweis erbracht<br /> haben, dass Sie die Waren zur&uuml;ckgesandt haben, je nachdem,<br /> welches der fr&uuml;here Zeitpunkt ist.</span><br /><span style="">Sie haben die Waren<br /> unverz&uuml;glich und in jedem Fall sp&auml;testens binnen vierzehn<br /> Tagen ab dem Tag, an dem Sie uns &uuml;ber den Widerruf dieses<br /> Vertrags unterrichten, an uns zur&uuml;ckzusenden oder zu<br /> &uuml;bergeben. </span></span></p>
<br />
<p><span style=""><span style="">Die<br /> Frist ist gewahrt, wenn Sie die Waren vor Ablauf der Frist<br /> von vierzehn Tagen absenden. </span></span></p>
<br />
<p><span style=""><span style="">Sie<br /> tragen die unmittelbaren Kosten der R&uuml;cksendung der Waren. </span></span></p>
<br />
<p><span style=""><span style="">Sie<br /> m&uuml;ssen f&uuml;r einen etwaigen Wertverlust der Waren nur<br /> aufkommen, wenn dieser Wertverlust auf einen zur Pr&uuml;fung der<br /> Beschaffenheit, Eigenschaften und Funktionsweise der Waren<br /> nicht notwendigen Umgang mit ihnen zur&uuml;ckzuf&uuml;hren ist. </span></span></p>
<br />
<p><span style=""><span style="">Das<br /> Widerrufsrecht besteht nicht bei Fernabsatzvertr&auml;gen zur<br /> Lieferung von Waren, die nicht vorgefertigt sind und f&uuml;r<br /> deren Herstellung eine individuelle Auswahl oder Bestimmung<br /> durch den Verbraucher ma&szlig;geblich ist oder die eindeutig auf<br /> die pers&ouml;nlichen Bed&uuml;rfnisse zugeschnitten sind, zB.:<br /> Anbringung der Schriftzeichen auf Tr&auml;gerfolien oder<br /> Kennzeichenhalter mit Ihrem vorgegebenen Text.</span></span></p>
<br />
<p><span style=""><span style="">Das<br /> Widerrufsformular (PDF) finden Sie hier:</span></span></p>
<br />
<p><span style=""><a href="http://www.mbd-chromshop.de/Widerrufsformular.pdf">Formular<br /> f&uuml;r Ihren Widerruf</a></span></p>
<br />
<p><span style=""><span style=""><strong>Hinweis</strong></span></span></p>
<br />
<p><span style=""><span style="">F&uuml;r<br /> alle Eink&auml;ufe gew&auml;hren wir Ihnen neben dem gesetzlichen<br /> Widerrufsrecht von 14 Tagen, ein (freiwilliges)<br /> R&uuml;ckgaberecht von 30 Tagen ab Warenerhalt.<br /><br /> Ausgenommen hiervon ist die individuell hergestellte<br /> Grabbeschriftung!<br /></span></span></p>
<br /><hr />
<p><span style="">Die AGB finden Sie HIER: <a href="http://www.chrombeschriftung.de/epages/61026690.sf/?ObjectPath=/Shops/61026690/Categories/TermsAndConditions">Nutzungs.-<br /> und Vertragsbedingungen (AGB)</a></span></p>
<br />
EOT;


		/// Händler
		$vendFoot = "";

		if($userData["comm"])
		{
			$vendFoot .= "<hr>{$nl}";
			$vendFoot .= "Der Kunde hat folgende Daten zu seiner Bestellung angegeben:{$nl}";
			$vendFoot .= "{$userData["comm"]}{$nl}";
			$vendFoot .= "{$nl}";
		}

		if($userData["paym"] == "prepay")
		{
			//$vendFoot .= "----------------------------------------------------------------------------{$nl}";
			$vendFoot .= "<hr>{$nl}";
			$vendFoot .= "Zahlungsweise: Vorkasse{$nl}";
			$vendFoot .= "{$nl}";
		}
		else if($userData["paym"] == "paypal")
		{
			//$vendFoot .= "----------------------------------------------------------------------------{$nl}";
			$vendFoot .= "<hr>{$nl}";
			$vendFoot .= "Zahlungsweise: PayPal{$nl}";
			$vendFoot .= "{$nl}";
		}

		// $vendFoot .= "----------------------------------------------------------------------------{$nl}";
		// $vendFoot .= "<hr>{$nl}";
		// $vendFoot .= "Mit freundlichen Grüßen{$nl}";
		// $vendFoot .= "MBD-Chromshop{$nl}";
		// $vendFoot .= "www.chrombeschriftung.de{$nl}";
		// $vendFoot .= "{$nl}";

		$userSubj = "Ihre Beschriftung wurde bestellt";

		$vendSubj = "Neuer Bestellvorgang";






		/// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		$urlChain = array_slice(explode("/", (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on" || $_SERVER["SERVER_PORT"] == 443 ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"]), 0, -1);
        $urlChain[] = "..";
        $urlChain[] = "Portal";
        $urlChain[] = "put.php";

		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => implode("/", $urlChain),
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 7,
			CURLOPT_POSTFIELDS => http_build_query(array("description" => $mailBody . $vendFoot, "agent" => "Chromschrift", "price" => str_replace(",", ".", $totaPric)))
		);

		$curl = curl_init();
		curl_setopt_array($curl, $options);
		$result = curl_exec($curl);
		curl_close($curl);
		/// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




		/*echo "<h1>Kundenemail</h1>";
		echo "<hr>";
		echo $userHead;
		echo $mailBody;
		echo $userFoot;
		echo "<hr>";

		echo "<h1>Betreiberemail</h1>";
		echo "<hr>";
		echo $vendHead;
		echo $mailBody;
		echo $vendFoot;
		echo "<hr>";

		die();*/

		/***************************************************************************************************
		* Email redundanz
		***************************************************************************************************/
		/*$certifyEmail = "21264mb@gmail.com";		/// Benachrichtigungsemailadresse
		$emailSubject = $vendSubj;
		$emailMessage = $vendHead . $mailBody . $vendFoot;
		$emailHeaders = array();
		$emailHeaders[] = "MIME-Version: 1.0";
		$emailHeaders[] = "From: {$userData["fnam"]} {$userData["lnam"]} <{$userData["emai"]}>";
		$emailHeaders[] = "Content-type: text/html; charset=utf-8";
		$emailHeaders[] = "X-Mailer: PHP/".phpversion();


		mail($certifyEmail, $emailSubject, $emailMessage, implode("\n", $emailHeaders));*/
		/**************************************************************************************************/

    //Mail an User
    $mailHost = "smtp.strato.de";
    $mailuser = "bestellung@chrombeschriftung.de";       //Username Mailversand
    $mailfrom = "bestellung@chrombeschriftung.de";         //Absendermail
    $mailfromPass = "MBDim140212";                     //Passwort Mailversand
    $mailLang = "de";                                 //Sprache Mailversand
    $replayMail = "bestellung@chrombeschriftung.de";                          //Antwortmail
    $MailCharSet = "utf-8";                           //Charset Mail
    $MailPort = 587;                                  //Port Mail
    $MailSMTP_Secure = "tls";                         //Mail Security
    $MailEncoding = "quoted-printable";               //Mail Codierung
    $mailTo = $userData["emai"];
    $distributor_mail = "bestellung@chrombeschriftung.de";
    $distributor_name = "MBD-ChromShop";
    $mailBody = $userHead . $mailBody . $userFoot;
    $mailSubj = $userSubj;

    self :: $newMail -> IsSMTP();                                         //es wird SMTP benutzt, also ein anderer Mailserver (außerhalb)
    self :: $newMail -> SMTPDebug = 0;

    /**********************************************************************************************************************************************

    SMTP::DEBUG_OFF (0): Disable debugging (you can also leave this out completely, 0 is the default).
    SMTP::DEBUG_CLIENT (1): Output messages sent by the client.
    SMTP::DEBUG_SERVER (2): as 1, plus responses received from the server (this is the most useful setting).
    SMTP::DEBUG_CONNECTION (3): as 2, plus more information about the initial connection - this level can help diagnose STARTTLS failures.
    SMTP::DEBUG_LOWLEVEL (4): as 3, plus even lower-level information, very verbose, don't use for debugging SMTP, only low-level problems.

    **********************************************************************************************************************************************/

     $isSuccessed = false;


    self :: $newMail ->  Host = $mailHost;
    self :: $newMail ->  SMTPAuth = true;
    self :: $newMail ->  Username = $mailuser;
    self :: $newMail ->  Password = $mailfromPass;
    self :: $newMail ->  SMTPSecure = $MailSMTP_Secure;
    self :: $newMail ->  Port = $MailPort;
    self :: $newMail ->  isHTML( true );

  	self :: $newMail ->  CharSet = $MailCharSet;
  	self :: $newMail ->  SetLanguage($mailLang);
  	self :: $newMail ->  Encoding = $MailEncoding;
    self :: $newMail ->  AddAddress($mailTo);

  	self :: $newMail ->  From     = $mailfrom;
  	self :: $newMail ->  FromName = $distributor_name;
  	self :: $newMail ->  AddReplyTo($replayMail, $distributor_name);
  	self :: $newMail ->  Subject 	= $mailSubj;
  	self :: $newMail ->  Body 	= $mailBody;
    self :: $newMail ->  addAttachment("../AGB.pdf", "AGB", "base64", "application/pdf");
    self :: $newMail ->  addAttachment("../Datenschutz.pdf", "Datenschutz", "base64", "application/pdf");
    self :: $newMail ->  addAttachment("../Widerruf.pdf", "Widerruf", "base64", "application/pdf");

    try{
      self :: $newMail ->  Send();
      $isSuccessed = true;

      self :: $newMail ->  clearAddresses();
      self :: $newMail ->  clearReplyTos();
      self :: $newMail ->  clearAllRecipients();
      self :: $newMail ->  clearAttachments();
      self :: $newMail ->  clearCustomHeaders();
    }
    catch(Exception $e){
      error_log("error mail");
      error_log($e->getMessage());
      self :: $newMail ->  clearReplyTos();
      self :: $newMail ->  clearAddresses();
      self :: $newMail ->  clearAllRecipients();
      self :: $newMail ->  clearAttachments();
      self :: $newMail ->  clearCustomHeaders();
    }


  //Mail an Betreiber
  $mailTo = $vendData["emai"];
  //$mailTo = "igor.papeta@psygonis.de";
  $mailBody = $vendHead . $mailBody . $vendFoot;
  $mailSubj = $vendSubj;

	self :: $newMail ->  CharSet=$MailCharSet;
	self :: $newMail ->  SetLanguage($mailLang);
	self :: $newMail ->  Encoding = $MailEncoding;
  self :: $newMail ->  AddAddress($mailTo);

	self :: $newMail ->  From     = $mailfrom;
	self :: $newMail ->  FromName = $distributor_name;
	self :: $newMail ->  AddReplyTo($replayMail, $distributor_name);
	self :: $newMail ->  Subject 	= $mailSubj;
	self :: $newMail ->  Body 	= $mailBody;

  try{
    self :: $newMail ->  Send();
    $isSuccessed = true;

    self :: $newMail ->  clearAddresses();
    self :: $newMail ->  clearReplyTos();
    self :: $newMail ->  clearAllRecipients();
    self :: $newMail ->  clearAttachments();
    self :: $newMail ->  clearCustomHeaders();
  }
  catch(Exception $e){
    error_log("error mail");
    error_log($e->getMessage());
    self :: $newMail ->  clearReplyTos();
    self :: $newMail ->  clearAddresses();
    self :: $newMail ->  clearAllRecipients();
    self :: $newMail ->  clearAttachments();
    self :: $newMail ->  clearCustomHeaders();
  }

		/*$isSuccessed = false;
		$repitition = 5;

		for($iterator = 0; $iterator < $repitition; $iterator++)
		{
			sleep(1);

			if(mail($vendData["emai"], $vendSubj, $vendHead . $mailBody . $vendFoot, $vendorHeader))
			{
				for($jterator = 0; $jterator < $repitition; $jterator++)
				{
					sleep(1);

					if(mail($userData["emai"], $userSubj, $userHead . $mailBody . $userFoot, $userHeader))
					{
						$isSuccessed = true;
						break;	/// Benutzeremail wurde verschickt
					}
				}

				break;	/// Betreiberemail wurde verschickt
			}
		}*/

		return $isSuccessed;
	}


	public static function redPaypSite()
	{
		$userData = self :: getUserData();

		if($userData["paym"] == "paypal")
		{
			$vendData = self :: getVendData();
			$landData = self :: getLandData();
			$totaPric = number_format(self :: getTotaPric() + $landData["cost"] - self :: getDiscPric() + self :: getOthePric(), 2, ".", "");

			$url = "https://www.paypal.com/cgi-bin/webscr";
			$paypParaList = array();
			/// PayPal Befehl
			$paypParaList["cmd"]         	= "_xclick";
			/// Händler E-Mail
			$paypParaList["business"]    	= $vendData["emai"];
			/// Artikelnummer
			$paypParaList["item_number"] 	= "";
			/// Artikelname
			$paypParaList["item_name"]   	= "Bestellung bei MBD Chromshop";
			/// Artikelpreis
			$paypParaList["amount"]			= "{$totaPric}";
			/// Währung
			$paypParaList["currency_code"]  = "EUR";
			/// Erfolgsurl
			$paypParaList["return"]			= "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . "&pay=suc";
			/// Abbruchsurl
			$paypParaList["cancel_return"] 	= "http://" . $_SERVER["HTTP_HOST"] . strtok($_SERVER["REQUEST_URI"], "?");

			echo "<!DOCTYPE html><html>";
			echo "<head><script>window.onload = function(){document.getElementById('form').submit();}</script></head>";
			echo "<body><form id = 'form' action = '{$url}' method = 'post'>";
			foreach($paypParaList as $paypInde => $paypPara)
			{
				echo "<input type = 'hidden' name = '{$paypInde}' value = '{$paypPara}'>";
			}
			echo "</form></body>";
			echo "</html>";
			exit();
		}
		else
		{
			return;
		}
	}


	/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
	/// Funktionssamlung

	/// Entfernt alle Tabulatoren und Leerzeichen aus Text
	private static function getTrimText($text)
	{
		// return preg_replace("/\t+/", "", preg_replace("/\s+/", "", $text));
		return str_replace(" ", "", $text);
	}

	private static function getTextLeng($text)
	{
		// $text = self :: getTrimText($text);
		return strlen(str_replace("ß", "s", str_replace("ä", "a", str_replace("ö", "o", str_replace("ü", "u", str_replace("Ä", "A", str_replace("Ö", "O", str_replace("Ü", "U", self :: getTrimText($text)))))))));
		// return strlen(self :: getTrimText($text));
	}

	private static function getTextPric($text, $pric)
	{
		return $pric * self :: getTextLeng($text);
	}

	public static function getFoilPric($text, $pric, $leng)
	{
		return $pric * ceil(self :: getTextLeng($text) / $leng);
	}

	public static function cleItemData()
	{
		unset($_SESSION["itemList"]);
	}
}
?>