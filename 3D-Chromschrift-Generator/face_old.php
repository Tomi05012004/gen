<?php
class face
{
/*-----------------------------------------------------------------------------------------------*/
/// globale Eigenschaften
	private $userDataName;
	private $itemDataName;
	
	private $foilEnab;
	private $foilData = array("PRIC" => 4.90, "LENG" => 20);			/// Folienoptionen Preis pro Länge
	
	private $fontEnab;
	private $fontData;
	
	private $addiEnab;
	private $addiData;
	
	private $mounEnab;
	private $mounData;
	
	private $coloEnab;
	private $coloData;
	
	
	
/*-----------------------------------------------------------------------------------------------*/
/// Folienmethoden
	public function __construct($mode = "auto")
	{
		/// schalte Konfigurationsdateien ein
		switch($mode)
		{
			case "auto":
				/// definire Namen für Session
				$this -> userDataName = "__autoUserData";
				$this -> itemDataName = "__autoItemData";
				/// wähle Module aus
				$this -> foilEnab = true;
				// require_once("PHP/autoConf.php");
				break;
			case "grav":
				/// definire Namen für Session
				$this -> userDataName = "__gravUserData";
				$this -> itemDataName = "__gravItemData";
				/// wähle Module aus
				require_once("PHP/gravConf.php");
				break;
			default:
				break;
		}
		
		if($this -> foilEnab = &$foilOpti){$this -> foilData = &$foilData;}
		if($this -> fontEnab = &$fontOpti){$this -> fontData = &$fontData;}
		if($this -> addiEnab = &$addiOpti){$this -> addiData = &$addiData;}
		if($this -> mounEnab = &$mounOpti){$this -> mounData = &$mounData;}
		if($this -> coloEnab = &$coloOpti){$this -> coloData = &$coloData;}
	}
	
	/// gibt Folienoptionen zurück
	public function getFoilData()
	{
		$resp["PRIC"] = $this -> foilData["PRIC"];
		$resp["LENG"] = $this -> foilData["LENG"];
		$resp["pric"] = $this -> conFloaStri($resp["PRIC"], 2);
		$resp["leng"] = $this -> conFloaStri($resp["LENG"], 0);
		
		return $resp;
	}
	
/*-----------------------------------------------------------------------------------------------*/
/// Hilfsmethoden
	private function conFloaStri($floa, $deci = 2)
	{
		$stri = number_format($floa, $deci, ",", " ");
		
		return $stri;
	}
}
?>