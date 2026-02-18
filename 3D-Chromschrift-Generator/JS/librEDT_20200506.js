/// Seite geladen
var load = function(even)
{
	initText();
	changeClassInterval('inpu', 'PlaceholderHigh', 'PlaceholderLow', 750);
}

var onli = function(even)
{
	console.log('onli');
	// console.log(even);
}

var ofli = function(even)
{
	console.log('ofli');
	// console.log(even);
}

var resi = function(even)
{
	fittPrev();
	prepText();
}

var erro = function(even)
{
	// console.log('erro');
}

window.addEventListener('load',      load);			/// 
window.addEventListener('online',    onli);			/// 
window.addEventListener('offline',   ofli);			/// 
window.addEventListener('resize',    resi);			/// 
// window.addEventListener('error',     erro);			/// 


/// Option "Trägerfolientext in Einzelbuchstaben auseinander gezogen"
var checkBoxOptionPrice = function(textLength)
{
	var triggerRadio = document.getElementById('foiT');
	var checkBox = document.getElementById('trueLength');
	var length = 20;
	
	if(triggerRadio.checked && checkBox.checked)
	{
		opti.truu = true;
		return 9.90 * Math.ceil(textLength / length);
	}
	
	opti.truu = false;
	return 0.0;
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// globale Variablen
var textTrigger = { text: "", trigger: false };


var itemData = {font: 'artB', foil: false, text: ''};

/// FolienObjekt
var foilData = {};

foilData['UNITPRIC'] = 0.00;
foilData['UNITLENG'] = 0;

foilData['getFoilPric'] = function(text)
{
	text = text.replace(new RegExp('\\s', 'g'), '');

	return Math.round(100 * this.UNITPRIC * Math.ceil(text.length / this.UNITLENG)) / 100;
}

var textData = {};

textData['UNITPRIC'] = 0.00;
textData['UNITLENG'] = 0;
textData['UPPECASE'] = false;
textData['LOWECASE'] = false;

textData['getTextPric'] = function(text)
{
	text = text.replace(new RegExp('\\s', 'g'), '');

	return Math.round(100 * this.UNITPRIC * Math.ceil(text.length / this.UNITLENG)) / 100;
}

textData['getTextOutp'] = function(text)
{
	if(this.UPPECASE)
	{
		text = text.toUpperCase();
	}
	else if(this.LOWECASE)
	{
		text = text.toLowerCase();
	}
	
	return text;
}




/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// Ereignissauslöser
var onSiteLoad = function()
{
	
}


var onTextInpu = function(text)
{
	
}


var onFontChan = function()
{
	
}


var onModeChan = function()
{
	
}


var onFormSend = function()
{
	// opti.char = document.getElementById('text').innerHTML;
	
	if(checOpti())
	{
		// opti.char
		
		sendValu('paraId',   opti.id);
		sendValu('paraText', opti.text);
		sendValu('paraChar', opti.char);
		sendValu('paraFoil', opti.foil);
		sendValu('paraTrue', opti.truu);
		sendValu('paraLeng', opti.leng);
		
		document.getElementById('formPara').submit();
	}
	else
	{
		alert('Bitte geben Sie ein Text ein...');
	}
}



/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// Funktionssammlung
var calcTextLeng = function(line)
{
	var symbUppe = [ 'A',  'B',  'C',  'D',  'E',  'F',  'G',  'H',  'I',  'J',  'K',  'L',  'M',  'N',  'O',  'P',  'Q',  'R',  'S',  'T',  'U',  'V',  'W',  'X',  'Y',  'Z',  'Ä',  'Ö',  'Ü'];
	var symbLowe = [ 'a',  'b',  'c',  'd',  'e',  'f',  'g',  'h',  'i',  'j',  'k',  'l',  'm',  'n',  'o',  'p',  'q',  'r',  's',  't',  'u',  'v',  'w',  'x',  'y',  'z',  'ä',  'ö',  'ü'];
	var symbNorm = [ ' ',  '1',  '2',  '3',  '4',  '5',  '6',  '7',  '8',  '9',  '0',  '.',  '-',  '@',  '!',  '?',  '+',  '&',  ':',  '%',  '(',  ')',  '"',  '/',  '='];
	
	/*var artAUppe = [1.13, 1.19, 1.13, 1.19, 1.06, 1.00, 1.19, 1.19, 0.50, 1.04, 1.19, 1.00, 1.50, 1.19, 1.19, 1.13, 1.19, 1.19, 1.13, 1.00, 1.19, 1.08, 1.56, 1.12, 1.13, 1.06, 1.13, 1.22, 1.16];
	var artANorm = [0.49, 0.69, 1.19, 1.13, 1.19, 1.13, 1.13, 1.06, 1.12, 1.13, 1.13, 0.50, 0.75, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.75, 1.04];*/

	var artAUppe = [0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92];
	var artANorm = [0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92, 0.92];
	
	var artBUppe = [2.08, 2.19, 2.10, 2.21, 1.92, 1.82, 2.16, 2.16, 0.96, 1.88, 2.21, 1.87, 2.67, 2.22, 2.16, 2.10, 2.11, 2.16, 2.05, 1.82, 2.05, 1.99, 2.88, 1.99, 2.05, 1.93, 2.06, 2.25, 2.07];
	var artBNorm = [0.88, 1.25, 2.12, 2.06, 2.13, 2.06, 2.06, 1.88, 2.00, 2.06, 2.06, 0.93, 1.37, 2.06, 0.88, 1.88, 1.25, 2.21, 0.92, 0.00, 0.00, 0.00, 0.00, 1.37, 1.88];
	
	var artCUppe = [2.90, 3.07, 2.90, 3.12, 2.67, 2.56, 3.05, 3.07, 1.31, 2.62, 3.08, 2.60, 3.68, 3.12, 3.08, 2.95, 3.00, 3.01, 2.90, 2.56, 2.94, 2.78, 4.01, 2.80, 2.84, 2.73, 2.93, 3.00, 2.94];
	var artCNorm = [1.28, 1.75, 2.99, 2.88, 3.01, 2.88, 2.87, 2.67, 2.82, 2.94, 2.97, 1.27, 1.88, 2.85, 1.23, 2.68, 0.00, 3.12, 1.26, 0.00, 0.00, 0.00, 0.00, 1.88, 2.62];
	
	var artDUppe = [4.15, 4.38, 4.15, 4.47, 3.80, 3.69, 4.33, 4.37, 1.90, 3.76, 4.42, 3.72, 5.36, 4.46, 4.44, 4.16, 4.26, 4.32, 4.12, 3.70, 4.16, 3.97, 5.74, 4.03, 4.03, 3.88, 4.19, 4.45, 4.26];
	var artDLowe = [3.46, 3.46, 3.30, 3.47, 3.29, 2.04, 3.46, 3.58, 1.65, 1.65, 3.36, 1.65, 5.43, 3.47, 3.35, 3.47, 3.46, 2.45, 3.01, 2.10, 3.47, 3.18, 4.90, 3.36, 3.18, 3.07, 3.44, 3.37, 3.44];
	var artDNorm = [1.75, 2.50, 4.25, 4.13, 4.27, 4.13, 4.13, 3.81, 4.00, 4.19, 4.25, 1.82, 2.69, 4.06, 1.75, 3.81, 0.00, 4.42, 1.82, 0.00, 0.00, 0.00, 0.00, 2.69, 3.76];
	
	var artELowe = [1.62, 1.62, 1.54, 1.62, 1.60, 0.96, 1.65, 1.67, 0.80, 0.80, 1.65, 0.80, 2.38, 1.62, 1.65, 1.63, 1.65, 1.03, 1.42, 1.08, 1.66, 1.37, 2.18, 1.51, 1.32, 1.31, 1.63, 1.67, 1.70];
	var artENorm = [0.74, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.75, 0.75, 2.32, 0.88, 1.54, 1.75, 2.14, 0.75, 0.00, 0.00, 0.00, 0.00, 0.75, 0.80];
	
	var artFUppe = [2.84, 2.90, 2.78, 3.07, 2.39, 2.39, 2.90, 3.12, 1.19, 2.39, 2.84, 2.33, 3.92, 3.24, 2.84, 2.79, 3.07, 2.90, 2.67, 2.44, 3.07, 2.73, 4.49, 2.73, 2.67, 2.68, 2.87, 2.85, 3.06];
	var artFNorm = [1.51, 2.88, 2.88, 2.91, 2.88, 2.90, 2.87, 2.91, 2.88, 2.88, 2.87, 1.13, 1.31, 2.94, 1.32, 2.50, 2.43, 3.25, 1.12, 0.00, 0.00, 0.00, 0.00, 1.31, 2.39];
	
	var charSizeList = {	artAUppe: artAUppe, 
							artANorm: artANorm, 
							
							artBUppe: artBUppe, 
							artBNorm: artBNorm,
							
							artCUppe: artCUppe,
							artCNorm: artCNorm,
							
							artDUppe: artDUppe,
							artDLowe: artDLowe,
							artDNorm: artDNorm,
							
							artELowe: artELowe,
							artENorm: artENorm,
							
							artFUppe: artFUppe,
							artFNorm: artFNorm};
	
	var leng = 0.0;
	
	/// Wandele in Groß - Klein Text
	if(opti.uppe)
	{
		line = line.toUpperCase();
	}
	else if(opti.lowe)
	{
		line = line.toLowerCase();
	}
		
	for(var i = 0; i < line.length; i++)
	{
		/// wenn Großschrift oder kein Großschrift und kein Kleinschrift
		if(opti.uppe || !opti.uppe && !opti.lowe)
		{
			var charPosi = symbUppe.indexOf(line[i]);
			
			if(charPosi > -1)
			{
				leng += charSizeList[opti.id + 'Uppe'][charPosi];
				continue;
			}
		}
		
		if(opti.lowe || !opti.uppe && !opti.lowe)
		{
			var charPosi = symbLowe.indexOf(line[i]);
			
			if(charPosi > -1)
			{
				leng += charSizeList[opti.id + 'Lowe'][charPosi];
				continue;
			}
		}
		
		var charPosi = symbNorm.indexOf(line[i]);
			
		if(charPosi > -1)
		{
			// console.log(opti.id + 'Norm' + charPosi);
			leng += charSizeList[opti.id + 'Norm'][charPosi];
		}
	}
	
	// console.log(leng);
	return leng;
}


var filtCharLine = function(line, convCase)
{
	convCase = convCase || false;
	
	/// wenn, wandle in Großschrift, bzw. Kleinschrift um
	if(convCase)
	{
		if(opti.uppe)
		{
			line = line.toUpperCase();
		}
		else if(opti.lowe)
		{
			line = line.toLowerCase();
		}
	}
	
	//var artAExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\/\\=]', 'g');
	var artAExpr = RegExp('[0-9a-zA-Zäöü\\ \\.\\-\\/\\=]', 'g');
	//var artBExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\@\\!\\?\\+\\&\\/\\=]', 'g');
	var artBExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\@\\!\\?\\&\\/\\=]', 'g');
	//var artCExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\&\\/\\=]', 'g');
	var artCExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\@\\?\\+\\&\\/\\=]', 'g');
	//var artDExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\&\\/\\=]', 'g');
	var artDExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\@\\?\\+\\!\\&\\/\\=]', 'g');
	var artEExpr = RegExp('[a-zA-ZÄÖÜäöü\\ \\.\\-\\:\\/\\=]', 'g');
	var artFExpr = RegExp('[0-9a-zA-ZÄÖÜäöü\\ \\.\\-\\&\\@\\!\\?\\+\\&\\%\\(\\)\\"\\/\\=]', 'g');
	
	var reguExpr = {artAExpr: artAExpr, 
					artBExpr: artBExpr, 
					artCExpr: artCExpr, 
					artDExpr: artDExpr,
					artEExpr: artEExpr,
					artFExpr: artFExpr};
	
	var charList = line.match(reguExpr[opti.id + 'Expr']);
	
	line = ''
	
	if(charList)
	{
		line = charList.join('');
	}

	return line;
}


var checOpti = function()
{
	var checText = opti.text.trim();
	
	if(opti.id && checText && (opti.foil == 0 || opti.foil == 1))
	{
		return true;
	}
	
	return false;
}


var sendValu = function(idTo, valu)
{
	var elem = document.getElementById(idTo)
	
	if(elem)
	{
		elem.value = valu;
	}
}
/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// Befehlsammlung



/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/


var opti = {id: '', cost: 0, pric: 0, size: 0, uppe: 0, lowe: 0, ital: 0, numb: 0, symb: '', foil: 0, text: '', char: '', truu: false, leng: 0.0};

var initText = function()
{
	/// passe das Vorschaufenstergröße anchor
	fittPrev();
	/// wähle erste Schriftart aus
	seleText(document.getElementById(itemData.font));
	/// wähle Tragefolieoption aus
	if(itemData.foil)
	{
		/// schalte Folieoption ein
		document.getElementById('foiT').click();
	}
	else
	{
		/// schalte Folieoption aus
		document.getElementById('foiF').click();
	}
	/// lese Tragefolieoptionkosten ein
	opti['cost'] = parseFloat(document.getElementById('artZcost').value);
	/// zeige Preis und Schriftzuglänge
	showCost();
}

var fittPrev = function()
{
	var elemWindSize = readWindSize();
	var elemHeadSize = readElemSize('head');
	var elemMainSize = readElemSize('main');
	
	var elemMain = document.getElementById('main');
	var elemPrev = document.getElementById('prev');
	var elemFont = document.getElementById('font');
	// elemCont.style.
	// console.log(offsPrev)
	/// berechne Abstand Rechts und Links
	var offsPrev = (elemWindSize.w - elemMainSize.w) / 2;
	
	/// setze Abstand Rechts und Links
	elemPrev.style.right = offsPrev + 'px';
	elemPrev.style.left  = offsPrev + 'px';
	
	var elemPrevSize = readElemSize('prev');
	elemPrev.style.height = (elemPrevSize.w / 2.5) + 'px';
	elemFont.style.marginTop = (elemPrevSize.w / 2.5) + 'px';
	elemPrev.style.top = elemHeadSize.h + 'px';
}

var inpuText = function(elem, inpuFlag)
{
	if(!elem)
	{
		return;
	}
	
	if(typeof(inpuFlag) == 'undefined') { var inpuFlag = true;}
	
	opti.text = filtCharLine(elem.value)
	
	/// Speichere Text in Zwischenablage
	if(inpuFlag) { textTrigger.text = opti.text; }
	
	
	if(elem.value != opti.text) { elem.value = opti.text; }
	
	/// zeige Text im Vorschau
	prepText();
	/// zeige Preis
	showCost();
	/// zeige Schriftzuglänge
	// showLeng();
}

var optiText = function(elem)
{
	if(!elem)
	{
		return;
	}
	
	opti['foil'] = parseInt(elem.value);
	/// zeige Preis und Schriftzuglänge
	showCost();
}

var seleText = function(elem)
{
	if(!elem)
	{
		return;
	}
	
	opti['id'] = elem.id;
	/// lese Schriftartoptionen ein
	for(prop in opti)
	{
		var elemOpti = document.getElementById(elem.id + prop);
		
		if(elemOpti)
		{
			if(parseFloat(elemOpti.value) == parseInt(elemOpti.value))
			{
				opti[prop] = parseInt(elemOpti.value);
			}
			else if(elemOpti.value != String(parseFloat(elemOpti.value)))
			{
				opti[prop] = elemOpti.value;
			}
			else
			{
				opti[prop] = parseFloat(elemOpti.value);
			}
		}
	}
	/// ändere Stil
	var elemList = document.getElementsByClassName('tile');
	
	if(elemList)
	{
		for(var i = 0; i < elemList.length; i++)
		{
			elemList[i].style.backgroundColor = '';
		}
	}
	
	if(elem)
	{
		elem.style.backgroundColor = 'rgba(180,180,196,1)';
	}
	
	document.getElementById('inpu').value = textTrigger.text;
	
	inpuText(document.getElementById('inpu'), false);
	/// zeige Text im Vorschau
	prepText();
	/// zeige Preis und Schriftzuglänge
	showCost();
	// showLeng();
	/// zeige Preis und Buchstabenhöhe des Schriftes
	sendFontInfo(elem.id);
}


var sendFontInfo = function(id)
{
	sendValuHtml(id + 'titl', 'fontInfoTitl', 0);
	sendValuHtml(id + 'pric', 'fontInfoPric', 4);
	sendValuHtml(id + 'size', 'fontInfoSize', 0);
}


var sendValuHtml = function(idFr, idTo, leng)
{
	var sourElem = document.getElementById(idFr);
	var targElem = document.getElementById(idTo);
	
	if(sourElem && targElem)
	{
		targElem.innerHTML = sourElem.value.replace('.', ',');
		if(targElem.innerHTML.length < leng){targElem.innerHTML = targElem.innerHTML + '0';}
	}
}


var prepText = function()
{
	/// Groß- Kleinschreibung
	if(opti.uppe)
	{
		text = opti.text.toUpperCase();
	}
	else if(opti.lowe)
	{
		text = opti.text.toLowerCase();
	}
	else
	{
		text = opti.text;
	}
	/// Schriftgröße
	// document.getElementById('shad').style.fontSize = (38 * opti.size) + 'px';
	// document.getElementById('text').style.fontSize = (38 * opti.size) + 'px';
	// document.getElementById('meas').style.fontSize = (78 * opti.size) + 'px';
	/// Kursiveinstellung
	if(opti.ital)
	{
		document.getElementById('shad').style.fontFamily = "geneCurs";
		document.getElementById('text').style.fontFamily = "geneCurs";
		
		/*document.getElementById('shad').style.fontStyle = 'italic';
		document.getElementById('text').style.fontStyle = 'italic';
		
		document.getElementById('shad').style.fontWeight = '400';
		document.getElementById('text').style.fontWeight = '400';*/
	}
	else
	{
		document.getElementById('shad').style.fontFamily = "genePrin";
		document.getElementById('text').style.fontFamily = "genePrin";

		/*document.getElementById('shad').style.fontStyle = '';
		document.getElementById('text').style.fontStyle = '';
		
		document.getElementById('shad').style.fontWeight = '800';
		document.getElementById('text').style.fontWeight = '800';*/
	}
	
	opti.char = text;
	/// übernehme Text
	document.getElementById('shad').innerHTML = text;
	document.getElementById('text').innerHTML = text;
	// document.getElementById('meas').innerHTML = text;
	/// setze Vergrößerungsfaktor zurück
	actuZoom = miniZoom;
	showPrev();
}

var showCost = function()
{
	
	// console.log('showCost()');
	var charNumb = opti.text.length - (opti.text.split(" ").length - 1);
	var charCost = charNumb * opti.pric + opti.foil * foilData.getFoilPric(opti.text);
	
	var attachFoilPrice = opti.foil * foilData.getFoilPric(opti.text);
	var trueLengthPrice = checkBoxOptionPrice(charNumb);
	var charPrice = charNumb * opti.pric;
	var totalPrice = attachFoilPrice + trueLengthPrice + charPrice;
	// console.log(attachFoilPrice);
	
	
	var infoAttachFoilPrice = charNumb ? 4.90 * Math.ceil(charNumb / 20) : 4.90;
	var infoTrueLengthPrice = charNumb ? 9.90 * Math.ceil(charNumb / 20) : 9.90;
	// document.getElementById('foilPric').innerHTML = foilData.getFoilPric(opti.text).toFixed(2).replace(".", ",");
	// document.getElementById('pric').innerHTML = charCost.toFixed(2).replace(".", ",");
	
	document.getElementById('foilPric').innerHTML = parseInt(attachFoilPrice) ? attachFoilPrice.toFixed(2).replace(".", ",") : infoAttachFoilPrice.toFixed(2).replace(".", ",");
	document.getElementById('truePrice').innerHTML = parseInt(trueLengthPrice) ? trueLengthPrice.toFixed(2).replace(".", ",") : infoTrueLengthPrice.toFixed(2).replace(".", ",");
	document.getElementById('pric').innerHTML = totalPrice.toFixed(2).replace(".", ",");
	
	var charLeng = calcTextLeng(opti.text); //showLeng();
	
	opti.leng = charLeng;
	document.getElementById('leng').innerHTML = charLeng.toFixed(2).replace(".", ",");
}

var showLeng = function()
{
	var symbUppe = [ 'A',  'B',  'C',  'D',  'E',  'F',  'G',  'H',  'I',  'J',  'K',  'L',  'M',  'N',  'O',  'P',  'Q',  'R',  'S',  'T',  'U',  'V',  'W',  'X',  'Y',  'Z',  'Ä',  'Ö',  'Ü'];
	var symbLowe = [ 'a',  'b',  'c',  'd',  'e',  'f',  'g',  'h',  'i',  'j',  'k',  'l',  'm',  'n',  'o',  'p',  'q',  'r',  's',  't',  'u',  'v',  'w',  'x',  'y',  'z',  'ä',  'ö',  'ü'];
	var symbNorm = [ ' ',  '1',  '2',  '3',  '4',  '5',  '6',  '7',  '8',  '9',  '0',  '.',  '-',  '@',  '!',  '?',  '+',  '&',  ':',  '%',  '(',  ')',  '"'];
	
	var artAUppe = [1.13, 1.19, 1.13, 1.19, 1.06, 1.00, 1.19, 1.19, 0.50, 1.04, 1.19, 1.00, 1.50, 1.19, 1.19, 1.13, 1.19, 1.19, 1.13, 1.00, 1.19, 1.08, 1.56, 1.12, 1.13, 1.06, 1.13, 1.22, 1.16];
	var artANorm = [0.49, 0.69, 1.19, 1.13, 1.19, 1.13, 1.13, 1.06, 1.12, 1.13, 1.13, 0.50, 0.75, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00];

	var artBUppe = [2.08, 2.19, 2.10, 2.21, 1.92, 1.82, 2.16, 2.16, 0.96, 1.88, 2.21, 1.87, 2.67, 2.22, 2.16, 2.10, 2.11, 2.16, 2.05, 1.82, 2.05, 1.99, 2.88, 1.99, 2.05, 1.93, 2.06, 2.25, 2.07];
	var artBNorm = [0.88, 1.25, 2.12, 2.06, 2.13, 2.06, 2.06, 1.88, 2.00, 2.06, 2.06, 0.93, 1.37, 2.06, 0.88, 1.88, 1.25, 2.21, 0.92, 0.00, 0.00, 0.00, 0.00];
	
	var artCUppe = [2.90, 3.07, 2.90, 3.12, 2.67, 2.56, 3.05, 3.07, 1.31, 2.62, 3.08, 2.60, 3.68, 3.12, 3.08, 2.95, 3.00, 3.01, 2.90, 2.56, 2.94, 2.78, 4.01, 2.80, 2.84, 2.73, 2.93, 3.00, 2.94];
	var artCNorm = [1.28, 1.75, 2.99, 2.88, 3.01, 2.88, 2.87, 2.67, 2.82, 2.94, 2.97, 1.27, 1.88, 2.85, 1.23, 2.68, 0.00, 3.12, 1.26, 0.00, 0.00, 0.00, 0.00];
	
	var artDUppe = [4.15, 4.38, 4.15, 4.47, 3.80, 3.69, 4.33, 4.37, 1.90, 3.76, 4.42, 3.72, 5.36, 4.46, 4.44, 4.16, 4.26, 4.32, 4.12, 3.70, 4.16, 3.97, 5.74, 4.03, 4.03, 3.88, 4.19, 4.45, 4.26];
	var artDLowe = [3.46, 3.46, 3.30, 3.47, 3.29, 2.04, 3.46, 3.58, 1.65, 1.65, 3.36, 1.65, 5.43, 3.47, 3.35, 3.47, 3.46, 2.45, 3.01, 2.10, 3.47, 3.18, 4.90, 3.36, 3.18, 3.07, 3.44, 3.37, 3.44];
	var artDNorm = [1.75, 2.50, 4.25, 4.13, 4.27, 4.13, 4.13, 3.81, 4.00, 4.19, 4.25, 1.82, 2.69, 4.06, 1.75, 3.81, 0.00, 4.42, 1.82, 0.00, 0.00, 0.00, 0.00];
	
	var artELowe = [1.62, 1.62, 1.54, 1.62, 1.60, 0.96, 1.65, 1.67, 0.80, 0.80, 1.65, 0.80, 2.38, 1.62, 1.65, 1.63, 1.65, 1.03, 1.42, 1.08, 1.66, 1.37, 2.18, 1.51, 1.32, 1.31, 1.63, 1.67, 1.70];
	var artENorm = [0.74, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.75, 0.75, 2.32, 0.88, 1.54, 1.75, 2.14, 0.75, 0.00, 0.00, 0.00, 0.00];
	
	var artFUppe = [2.84, 2.90, 2.78, 3.07, 2.39, 2.39, 2.90, 3.12, 1.19, 2.39, 2.84, 2.33, 3.92, 3.24, 2.84, 2.79, 3.07, 2.90, 2.67, 2.44, 3.07, 2.73, 4.49, 2.73, 2.67, 2.68, 2.87, 2.85, 3.06];
	var artFNorm = [1.51, 2.88, 2.88, 2.91, 2.88, 2.90, 2.87, 2.91, 2.88, 2.88, 2.87, 1.13, 1.31, 2.94, 1.32, 2.50, 2.43, 3.25, 1.12, 0.00, 0.00, 0.00, 0.00];
	
	var charSizeList = {	artAUppe: artAUppe, 
							artANorm: artANorm, 
							
							artBUppe: artBUppe, 
							artBNorm: artBNorm,
							
							artCUppe: artCUppe,
							artCNorm: artCNorm,
							
							artDUppe: artDUppe,
							artDLowe: artDLowe,
							artDNorm: artDNorm,
							
							artELowe: artELowe,
							artENorm: artENorm,
							
							artFUppe: artFUppe,
							artFNorm: artFNorm};
	
	
	var text = document.getElementById('text').innerHTML;
	
	var leng = 0.0;
	
	for(var i = 0; i < text.length; i++)
	{
		/// wenn Großschrift oder kein Großschrift und kein Kleinschrift
		if(opti.uppe || !opti.uppe && !opti.lowe)
		{
			var charPosi = symbUppe.indexOf(text[i]);
			
			if(charPosi > -1)
			{
				leng += charSizeList[opti.id + 'Uppe'][charPosi];
				continue;
			}
		}
		
		if(opti.lowe || !opti.uppe && !opti.lowe)
		{
			var charPosi = symbLowe.indexOf(text[i]);
			
			if(charPosi > -1)
			{
				leng += charSizeList[opti.id + 'Lowe'][charPosi];
				continue;
			}
		}
		
		var charPosi = symbNorm.indexOf(text[i]);
			
		if(charPosi > -1)
		{
			// console.log(opti.id + 'Norm' + charPosi);
			leng += charSizeList[opti.id + 'Norm'][charPosi];
		}
	}
	
	// console.log(leng);
	return leng;
}

var miniZoom = 0.7;
var actuZoom = miniZoom;

var showPrev = function(trigFull)
{
	trigFull = trigFull || false;
	/// ids = imag, meas, text, shad
	var showBord = {t: 50, r: 150, b: 50, l: 150};
	
	if(readElemSize('cont').w > 800){showBord = {t: 50, r: 100, b: 50, l: 100};}
	else{showBord = {t: 20, r: 50, b: 20, l: 50};}
	
	
	// var relaCoor = {x: 700 / 2901, y: 510 / 1255};
	var relaCoor = {x: 700 / 2901, y: (580 - opti.size * 14) / 1255};
	// var relaCoor = {x: 700 / 2901, y: (580 - opti.size * 36) / 1255};
	
	var imagElem = document.getElementById('imag');
	var textElem = document.getElementById('text');
	var shadElem = document.getElementById('shad');
	
	var PxPerCm = 17;
	
	if     (opti.id == 'artA'){PxPerCm = 20.0;}
	else if(opti.id == 'artB'){PxPerCm = 18.5;}
	else if(opti.id == 'artC'){PxPerCm = 17.5;}
	else if(opti.id == 'artD'){PxPerCm = 13.7;}
	else if(opti.id == 'artE'){PxPerCm = 13.9;}
	else if(opti.id == 'artF'){PxPerCm = 21.0;}
	
	textElem.style.fontSize = PxPerCm * opti.size * actuZoom + 'px';
	shadElem.style.fontSize = PxPerCm * opti.size * actuZoom + 'px';
	
	// var measSize = readElemSize('meas');
	var contSize = readElemSize('cont');
	var textSize = readElemSize('text');
	var imagSize = {w: 2901, h: 1255};
	
	/// verkleinere Anzeige bis Konteinergröße
	if(!trigFull && contSize.w - textSize.w < showBord.l + showBord.r)
	{
		/// Hintergrundbild kann verkleinert werden
		if((actuZoom - 0.1) * imagSize.w > contSize.w && (actuZoom - 0.1) * imagSize.h > contSize.h)
		{
			actuZoom -= 0.1;
			showPrev();
			return;
		}
		/// Hintergrundbild kann nicht verkleinert werden
		else
		{
			actuZoom = Math.max(contSize.w / imagSize.w, contSize.h / imagSize.h)
			showPrev(true);
			return;
		}
	}
	
	if(textSize.w && textSize.h)
	{
		var widtImag = actuZoom * imagSize.w;
		var heigImag = actuZoom * imagSize.h;
		
		/// setze Vergrößerung
		imagElem.style.width  = widtImag + 'px';
		imagElem.style.height = heigImag + 'px';
		/// berechne Abstand des Bildes
		var toppImag;
		var leftImag;
			
		if(trigFull)
		{
			toppImag = heigImag / 2 - contSize.h / 2;
			leftImag = widtImag / 2 - contSize.w / 2;
		}
		else
		{
			toppImag = heigImag * relaCoor.y - contSize.h / 2 + textSize.h / 2;
			leftImag = widtImag * relaCoor.x - contSize.w / 2 + textSize.w / 2;
			/// prüfe ob das Bild
			if(!trigFull && leftImag + contSize.w > widtImag)
			{
				actuZoom = Math.max(contSize.w / imagSize.w, contSize.h / imagSize.h)
				showPrev(true);
				return;
			}
		}
		
		/// setze Abstand des Bildes
		imagElem.style.marginTop  = '-' + toppImag + 'px';
		imagElem.style.marginLeft = '-' + leftImag + 'px';
		/// berechne Abstand des Textes
		toppText = actuZoom * imagSize.h * relaCoor.y - toppImag;
		leftText = actuZoom * imagSize.w * relaCoor.x - leftImag;
		/// setze Abstand des Textes
		textElem.style.top  = toppText + 'px';
		textElem.style.left = leftText + 'px';
		shadElem.style.top  = toppText + 'px';
		shadElem.style.left = leftText + 'px';
	}
	else
	{
		/// setze Vergrößerung
		imagElem.style.width  = (miniZoom * imagSize.w) + 'px';
		imagElem.style.height = (miniZoom * imagSize.h) + 'px';
		/// berechne Abstand
		topp = miniZoom * imagSize.h * relaCoor.y - contSize.h / 2;
		left = miniZoom * imagSize.w * relaCoor.x - contSize.w / 2;
		/// setze Abstand
		imagElem.style.marginTop  = '-' + topp + 'px';
		imagElem.style.marginLeft = '-' + left + 'px';
		
		actuZoom = miniZoom;
	}
	
	while(trigFull && contSize.w - textSize.w < showBord.l + showBord.r + 130)
	{
		textElem.innerHTML = textElem.innerHTML.slice(0, -1);
		shadElem.innerHTML = textElem.innerHTML;
		textSize = readElemSize('text');
	}
}

var checText = function(line)
{
	var expr;
	
	if(opti.numb)
	{
		expr = new RegExp(/^[0-9a-zA-ZäöüÄÖÜ\ ]{1,}$/);
	}
	else
	{
		expr = new RegExp(/^[a-zA-ZäöüÄÖÜ\ ]{1,}$/);
	}
	
	if(expr.test(line) || !line)
	{
		return true;
	}
	
	return false;
}

var readWindSize = function()
{
	// return {w: window.innerWidth, h: window.innerHeight};
	return {w: document.body.clientWidth, h: document.body.clientHeight};
}

var readElemSize = function(id)
{
	var elem = document.getElementById(id);
	
	if(elem)
	{
		return {w: elem.offsetWidth, h: elem.offsetHeight};
	}
	
	return false;
}



var changeClassInterval = function(id, classA, classB, interval)
{
	var element = document.getElementById(id);

	if(element)
	{
		element.className = element.className.trim();
		var classString = ' ' + element.className + ' ';

		if(classString.search(' ' + classA + ' ') != -1)
			element.className = classString.replace(' ' + classA + ' ', ' ' + classB + ' ');
		else if(classString.search(' ' + classB + ' ') != -1)
			element.className = classString.replace(' ' + classB + ' ', ' ' + classA + ' ');
		else
			element.className += element.className.length > 0 ? ' ' + classA : classA;
	}

	setTimeout(function() {changeClassInterval(id, classA, classB, interval);}, interval);
}