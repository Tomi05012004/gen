/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// allgemeine Ereignisse
var load = function(even)
{
	/// wähle Zahlungsmethode aus
	document.getElementById('paymMeth' + shopData.paymMeth).click();
	
	/// prüfe Einkaufsformular auf richtigkeit
	if(checUserForm('formUser'))
	{
		/// zeige AGB und Kaufenknopf
		document.getElementById('impr').style.display = 'block';
	}
	else
	{
		/// verstecke AGB und Kaufenknopf
		document.getElementById('impr').style.display = 'block';
	}
}

var onli = function(even)
{
	console.log('onli');
	// console.log(even);
}

var ofli = function(even)
{

}

var resi = function(even)
{
	
}

var erro = function(even)
{
	
}

window.addEventListener('load',      load);			/// 
window.addEventListener('online',    onli);			/// 
window.addEventListener('offline',   ofli);			/// 
window.addEventListener('resize',    resi);			/// 
window.addEventListener('error',     erro);			/// 



/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// globale Variablen
/// Daten des Internethändlers
var shopData = {paymMeth: 'PrePay', currency: '€', shipCost: 2.90, taxxInde: 0.19, prePay: {overCost: 0.00, discInde: -0.05}, payPal: {overCost: 0.00, discInde: 0.00}};
/// Daten der hinzugefügten Artikeln
var itemData = {};




/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/



/***************************************************************************************************
*
***************************************************************************************************/
var orderOptions = {};

var getOrderOptionPrice = function(name, calculate)
{
	if(typeof calculate == 'undefined') { calculate = false; }

	if(typeof orderOptions[name] != 'undefined')
		return orderOptions[name].price * (!calculate || orderOptions[name].value ? 1 : 0);

	return NULL;
}

var setOrderOptionValue = function(name, value)
{
	if(typeof orderOptions[name] != 'undefined')
		orderOptions[name].value = value;
}

var showTotalBill = function()
{
	var payMethodElements = document.querySelectorAll('[name=paymMeth]');

	for(var i = 0; i < payMethodElements.length; i++)
	{
		if(payMethodElements[i].checked)
		{
			onPaymMethChan(payMethodElements[i]);
			document.querySelector('[name=rapidProcessing]').value = document.querySelector('[name="order.options-rapid.processing"]').checked ? 'true' : 'false';
			break;
		}
	}
}
/***************************************************************************************************
*
***************************************************************************************************/


/// Ereignissauslöser
var onPaymMethChan = function(elem)
{
	var shipCost = calcShipCost();
	var summCost = calcSummCost();
	var overCost = calcOverCost(elem.value);
	var orderOptionsPrice = getOrderOptionPrice('order.options-rapid.processing', true);
	var discCost = calcDiscCost(elem.value, shipCost + summCost + overCost + orderOptionsPrice)
	var totaCost = shipCost + summCost + overCost + discCost + orderOptionsPrice;
	var taxxCost = calcTaxxCost(totaCost);
	
	showCost('paymShipCost', shipCost);
	showCost('paymOverCost', overCost);
	showCost('paymDiscCost', discCost);
	showCost('paymTotaCost', totaCost);
	showCost('paymTaxxCost', taxxCost);
	
	var userFormElem = document.getElementById('formUser');
	
	if(userFormElem)
	{
		var userFormElemList = userFormElem.children;
		
		for(userElemInde in userFormElemList)
		{
			if(userFormElemList[userElemInde].name == 'paym')
			{
				userFormElemList[userElemInde].value = elem.value;
				
				break;
			}
		}
	}
}


var onShipLandChan = function(elem)
{
	var userFormElem = document.getElementById('formUser');
	
	if(userFormElem)
	{
		var userFormElemList = userFormElem.children;
		
		for(userElemInde in userFormElemList)
		{
			if(userFormElemList[userElemInde].name == 'acti')
			{
				userFormElemList[userElemInde].value = 'lnd';
			}
			else if(userFormElemList[userElemInde].name == 'land')
			{
				userFormElemList[userElemInde].value = elem.value;
			}
		}
	}
	
	userFormElem.action = 'gene.php?mod=bsk';
	userFormElem.submit();
}


var onUserInfoInpu = function(elem, info)
{
	var userFormElem = document.getElementById('formUser');
	
	if(userFormElem)
	{
		userFormElemList = userFormElem.children;
		
		for(userElemInde in userFormElemList)
		{
			if(userFormElemList[userElemInde].name == info)
			{
				userFormElemList[userElemInde].value = elem.value;
				
				if(checUserForm('formUser'))
				{
					/// zeige AGB und Kaufenknopf
					document.getElementById('impr').style.display = 'block';
				}
				else
				{
					/// verstecke AGB und Kaufenknopf
					document.getElementById('impr').style.display = 'block';
				}
				
				break;
			}
		}
	}
}


var onUserInfoSubm = function(elem)
{
	if(checUserForm('formUser', true))
	{
		var acceImprElem = document.getElementById('acceImpr');
		var cbDataProt = document.getElementById('cbDataProt');
		var userFormElem = document.getElementById('formUser');

		if(userFormElem && acceImprElem.checked && cbDataProt.checked)
		{
			userFormElem.submit();
		}
		else if(!acceImprElem.checked)
		{
			alert('Bitte akzeptieren Sie die AGB´s.');
		}
    else if(!cbDataProt.checked)
		{
			alert('Bitte akzeptieren Sie die Datenschutzerklärung.');
		}
	}
	else
	{
		// alert('Bitte füllen Sie alle erforderlichen Felder korrekt aus!');
	}
}


var onItemEdit = function(elem)
{
	elem.form.submit();
}


var onItemDele = function(elem)
{
	var resu = confirm('Möchten Sie wirklich diesen Artikel löschen?');
	
	if(resu)
	{
		elem.form.submit();
		return;
	}
	else
	{
		return;
	}
}




/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// Funktionssammlung
var calcShipCost = function()
{
	var shipCost = shopData.shipCost;
	return shipCost;
}

var calcSummCost = function()
{
	var summCost = 0.0;
	
	for(var itemPosi in itemData)
	{
		if(String(parseInt(itemPosi)) == String(itemPosi))
		{
			summCost += itemData[itemPosi];
		}
	}
	
	return summCost;
}

var calcOverCost = function(paymMeth)
{
	var overCost = (paymMeth == 'prepay') ? (shopData.prePay.overCost) : ((paymMeth == 'paypal') ? (shopData.payPal.overCost) : (0.00));
	
	return overCost;
}

var calcDiscCost = function(paymMeth, totaCost)
{
	var discInde = (paymMeth == 'prepay') ? (shopData.prePay.discInde) : ((paymMeth == 'paypal') ? (shopData.payPal.discInde) : (0.00));
	var discCost = Math.round(100 * (totaCost * Math.abs(discInde)) / 1) / 100;
	
	return -discCost;
}

var calcTaxxCost = function(totaCost)
{
	var taxxCost = Math.round(100 * (totaCost * shopData.taxxInde) /  (1 + shopData.taxxInde)) / 100;
	
	return taxxCost;
}

var checUserForm = function(elid, chec)
{
	chec = chec || false;
	var userFormElem = document.getElementById('formUser');
	
	if(userFormElem)
	{
		userFormElemList = userFormElem.children;
		var requNameList = ['fnam', 'stre', 'post', 'city', 'emai'];
		var requNameNumb = requNameList.length;
		var requNameCoun = 0;
		
		for(var i = 0; i < requNameList.length; i++)
		{
			if(typeof(userFormElemList[requNameList[i]] != 'undefined'))
			{
				if(requNameList[i] == 'emai')
				{
					// var expr = new RegExp(/^[a-zA-Z0-9-_\.]{1,}[@]{1}[a-zA-Z0-9-\.]{1,}[\.]{1}[a-z]{2,}$/);
					var expr = new RegExp(/^[a-zA-Z0-9-_\.]{1,}[@]{1}[a-zA-Z0-9-\.]{1,}[\.]{1}[a-zA-Z]{2,10}$/);
					
					if(expr.test(userFormElemList[requNameList[i]].value))
					{
						/// E-Mail OK
						requNameCoun++;
					}
					else if(chec)
					{
						alert('Bitte geben Sie korrekte E-Mail Adresse ein...');
						
						break;
					}
				}
				else if(userFormElemList[requNameList[i]].value.length > 0)
				{
					requNameCoun++;
				}
				else if(chec)
				{
					if(requNameList[i] == 'fnam'){alert('Bitte geben Sie Ihre Name ein...');}
					else if(requNameList[i] == 'stre'){alert('Bitte geben Sie Ihre Straße und Hausnummer ein...');}
					else if(requNameList[i] == 'city'){alert('Bitte geben Sie Ihre Stadt ein...');}
					else if(requNameList[i] == 'post'){alert('Bitte geben Sie Ihre PLZ ein...');}
					
					break;
				}
			}
		}
		
		if(requNameNumb == requNameCoun)
		{
			return true;
		}
	}
	
	return false;
}



/* var checUserForm = function(elid)
{
	var userFormElem = document.getElementById('formUser');
	
	if(userFormElem)
	{
		userFormElemList = userFormElem.children;
		var requNameList = ['fnam', 'lnam', 'stre', 'hous', 'post', 'city', 'emai'];
		var requNameNumb = requNameList.length;
		var requNameCoun = 0;
		
		for(var i = 0; i < requNameList.length; i++)
		{
			if(typeof(userFormElemList[requNameList[i]] != 'undefined'))
			{
				if(requNameList[i] == 'emai')
				{
					var expr = new RegExp(/^[a-zA-Z0-9-_\.]{1,}[@]{1}[a-zA-Z0-9-\.]{1,}[\.]{1}[a-z]{2,}$/);
					
					if(expr.test(userFormElemList[requNameList[i]].value))
					{
						/// E-Mail OK
						requNameCoun++;
					}
				}
				else if(userFormElemList[requNameList[i]].value.length > 0)
				{
					requNameCoun++;
				}
			}
		}
		
		if(requNameNumb == requNameCoun)
		{
			return true;
		}
	}
	
	return false;
} */

/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/
/// Befehlsammlung
var showCost = function(elid, cost, curr)
{
	var costElem = document.getElementById(elid);
	
	if(costElem)
	{
		costElem.innerHTML = cost.toFixed(2).replace(".", ",");
		
		if(typeof(curr) == 'undefined')
		{
			costElem.innerHTML += ' ' + shopData.currency;
		}
		else
		{
			costElem.innerHTML += ' ' + curr;
		}
	}
}


/*---------------------------------------------------------------------------------------------------------------------------------------------------------*/

