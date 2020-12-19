<html>
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-168278394-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());\n
  
  gtag('config', 'UA-168278394-1');
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script data-ad-client="ca-pub-7625490600882004" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>

<title>EVE 환율</title>
</head>
<style>

td.calcinput{

	width:200px;
}

td.calcresult{
	color:rgba(16,86,16,1);
	font-size:18px;
	width:270px;
	height:50px;

}
input{

	font-size:20px;
	height:28px;
}


td{
	border:1px solid black;
}

.showValue{

	font-size:40px;
	color:rgba(50,200,50,1);
	text-align:right;
	border:0px;
}


th{
	border:2px solid black;
}
</style>

<body>



<table>
<tr><td>

	<table>
	<tr>
		<th colspan=2>The Forge PLEX Market</th>
	</tr>

	<tr>
		<td width=180>Sell Price</td>
		<td id='plex_sell' width=140><img src="./images/loading.gif" height=16></td>
	</tr>
	<tr>
		<td>Buy Price</td>
		<td id='plex_buy'><img src="./images/loading.gif" height=16></td>
	</tr>
	<tr>
		<td>Average (Yesterday)</td>
		<td id='plex_average'><img src="./images/loading.gif" height=16></td>
	</tr>
	</table>

</td><td>
	<table>
	<tr>
		<th colspan=2>USD Exchange rate</th>
	</tr>

	<tr>
		<td>Exchange rate<br>(매매기준율)</td>
		<td id='USD_rate' width=100><img src="./images/loading.gif" height=16></td>
	</tr>

	</table>
</td>
<td>
	<table>
	<tr>
		<th>PLEX Package</th>
	</tr>

	<tr>
		<td>
			<select id='plexpack'>
				<option value='p110'>110 PLEX / ＄ 4.99</option>
				<option value='p240'>240 PLEX / ＄ 9.99</option>
				<option value='p500' selected=true>500 PLEX / ＄ 19.99</option>
				<option value='p1100'>1100 PLEX / ＄ 39.99</option>
				<option value='p2860'>2860 PLEX / ＄ 99.99</option>
				<option value='p7430'>7430 PLEX / ＄ 249.99</option>
				<option value='p15400'>15400 PLEX / ＄ 499.99</option>
			</select>
		</td>
	</tr>

	</table>	
</td>
</tr>
</table>
<br><br>
<div id='calculator' hidden>
<table class>
<tr>
	<th colspan=2> <span style="font-size:30px;">현재 교환율</span> </th>
</tr>
<tr>
	<td  class="showValue">
		<span id='ISKper10kKRW' class="showValue">
		</span>
	</td>
	<td valign=bottom style="border:0px;">
		Mil ISK / 1만원
	</td>
</tr>
<tr>
	<td class="showValue">
		<span id='KRWper1bISK' class="showValue">
		</span>		
	</td>
	<td valign=bottom style="border:0px;">
		￦ / 1 Bil ISK
	</td>
</tr>
</table>
<br>
<table>
<tr>
	<td>KRW</td>
	<td>USD</td>
	<td>PLEX</td>
	<td>ISK(Average)</td>
</tr><tr>
	<td class='calcinput'><input type=number step=1 id="input_KRW" value=0></td>
	<td class='calcinput'><input type=number step=0.01 id="input_USD" value=0></td>
	<td class='calcinput'><input type=number step=1 id="input_PLEX" value=0></td>
	<td class='calcinput'><input type=number step=0.01 id="input_ISK" value=0></td>
</tr>
<tr>
	<td class='calcresult' id="result_KRW"></td>
	<td class='calcresult' id="result_USD"></td>
	<td class='calcresult' id="result_PLEX"></td>
	<td class='calcresult' id="result_ISK"></td>
</table>

</div>
</body>
</html>

<script language='javascript'>


var rate_obj;
var xmlhttp = new XMLHttpRequest();
var url = "./exchange_rate.php";

var PLEXPACK;

class InputValue{
	constructor(brand) {
		this.KRW=0;
		this.USD=0;
		this.PLEX=0;
		this.ISK=0;
	}

}

var currency= new InputValue();

var KRW=$("#input_KRW");
var USD=$("#input_USD");
var PLEX=$("#input_PLEX");
var ISK=$("#input_ISK");

xmlhttp.onreadystatechange = function() {
	if (this.readyState == 4 && this.status == 200) {
		rate_obj= JSON.parse(this.responseText);
		$('#plex_sell').html(NWC(rate_obj.PLEX_sell));
		$('#plex_buy').html(NWC(rate_obj.PLEX_buy));
		$('#plex_average').html(NWC(rate_obj.PLEX_average));
		$('#USD_rate').html(NWC(rate_obj.USD) + " ￦/＄");
		$('#input_KRW').val(0);
		$('#input_USD').val(0);
		$('#input_PLEX').val(0);
		$('#input_ISK').val(0);
		$('#calculator').show();
		SetPLEXpack();
		showValue();
	}
};
xmlhttp.open("GET", url, true);
xmlhttp.send();

function SetPLEXpack(){

	switch($("#plexpack").val()){
		case 'p110':
			PLEXPACK=4.99/110;
		break;
		case 'p240':
			PLEXPACK=9.99/240;
		break;
		case 'p500':
			PLEXPACK=19.99/500;
		break;
		case 'p1100':
			PLEXPACK=39.99/1100;
		break;
		case 'p2860':
			PLEXPACK=99.99/2860;
		break;
		case 'p7430':
			PLEXPACK=249.99/7430;
		break;
		case 'p15400':
			PLEXPACK=499.99/15400;
		break;
		default:
			PLEXPACK=19.99/500;
		break;

	}


}

function NWC(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function NWCKR(x) {
    return x.toString().replace(/\B(?=(\d{4})+(?!\d))/g, ",");
}


$(":input").bind('keyup mouseup', function () {
	
    if(KRW.val()!=currency.KRW && KRW.val()!=""){
		currency.KRW=KRW.val();
		calc("KRW");
	}
	
    else if(USD.val()!=currency.USD && USD.val()!=""){
		currency.USD=USD.val();
		calc("USD");
	}
	else if(PLEX.val()!=currency.PLEX && PLEX.val()!=""){
		currency.PLEX=PLEX.val();
		calc("PLEX");
	}
	else if(ISK.val()!=currency.ISK && ISK.val()!=""){
		currency.ISK=ISK.val();
		calc("ISK");
	}
});

$("#plexpack").change(function(){
	SetPLEXpack();

	if(KRW.val()!=""){
		currency.KRW=KRW.val();
		calc("KRW");
	}
	
    else if(USD.val()!=""){
		currency.USD=USD.val();
		calc("USD");
	}
	else if(PLEX.val()!=""){
		currency.PLEX=PLEX.val();
		calc("PLEX");
	}
	else if(ISK.val()!=""){
		currency.ISK=ISK.val();
		calc("ISK");
	}

	showValue();
	
});
function calc(curr){
	
	var transactiontype=rate_obj.PLEX_average;

	

	if(curr=="KRW"){
		

		KRW.val(parseInt(KRW.val()));
		USD.val("");
		PLEX.val("");
		ISK.val("");
		
		$("#result_KRW").html("￦ "+NWCKR(KRW.val()));
		$("#result_USD").html("＄ "+NWC(exchange(KRW.val(),"KRW","USD")));
		$("#result_PLEX").html(NWC(exchange(KRW.val(),"KRW","PLEX")) +" PLEX");
		$("#result_ISK").html(NWC(exchange(KRW.val(),"KRW","ISK")) + " ISK<br>"+NWC(Math.round(exchange(KRW.val(),"KRW","ISK")/1000000)) +" Mil ISK");
		

	}
	else if(curr=="USD"){

	

		KRW.val("");
		USD.val(parseInt(USD.val()*100)/100);
		PLEX.val("");
		ISK.val("");
		$("#result_KRW").html("￦ "+NWCKR(exchange(USD.val(),"USD","KRW")));
		$("#result_USD").html("＄ "+NWC(USD.val()));
		$("#result_PLEX").html(NWC(exchange(USD.val(),"USD","PLEX")) +" PLEX");
		$("#result_ISK").html(NWC(exchange(USD.val(),"USD","ISK")) + " ISK<br>"+NWC(Math.round(exchange(USD.val(),"USD","ISK")/1000000)) +" Mil ISK");
	}
	else if(curr=="PLEX"){
		


		KRW.val("");
		USD.val("");
		PLEX.val(parseInt(PLEX.val()));
		ISK.val("");
		$("#result_KRW").html("￦ "+NWCKR(exchange(PLEX.val(),"PLEX","KRW")));
		$("#result_USD").html("＄ "+NWC(exchange(PLEX.val(),"PLEX","USD")));
		$("#result_PLEX").html(NWC(PLEX.val()) +" PLEX");
		$("#result_ISK").html(NWC(exchange(PLEX.val(),"PLEX","ISK")) + " ISK<br>"+NWC(Math.round(exchange(PLEX.val(),"PLEX","ISK")/1000000)) +" Mil ISK");
	}
	else if(curr=="ISK"){
		
		

		KRW.val("");
		USD.val("");
		PLEX.val("");
		ISK.val(parseInt(ISK.val()*100)/100);

		$("#result_KRW").html("￦ "+NWCKR(exchange(ISK.val(),"ISK","KRW")));
		$("#result_USD").html("＄ "+NWC(exchange(ISK.val(),"ISK","USD")));
		$("#result_PLEX").html(NWC(exchange(ISK.val(),"ISK","PLEX")) +" PLEX");
		$("#result_ISK").html(NWC(ISK.val()) + " ISK<br>"+NWC(Math.round(ISK.val()/1000000)) +" Mil ISK");

	}


	

}

function exchange(number,fromC,toC){

	var cKRW=0,cUSD=0,cPLEX=0,cISK=0;
	var transactiontype=rate_obj.PLEX_average;
	if(fromC=="KRW"){
		cKRW=number;
		
		cUSD=parseInt(cKRW*100/rate_obj.USD)/100;
		cPLEX=parseInt(cUSD*1000/PLEXPACK)/1000;
		cISK=parseInt(cPLEX*100*transactiontype)/100;
	}	
	else if(fromC=="USD"){
	
		cUSD=number;

		cKRW=parseInt(parseInt(cUSD*rate_obj.USD));

		cPLEX=parseInt(cUSD*1000/PLEXPACK)/1000;
		cISK=parseInt(cPLEX*100*transactiontype)/100;

	}
	else if(fromC=="PLEX"){
		cPLEX=number;

		cUSD=parseInt(cPLEX*100*PLEXPACK)/100;
		cKRW=parseInt(parseInt(cUSD*rate_obj.USD));	
		
		cISK=parseInt(cPLEX*100*transactiontype)/100;
	}
	else if(fromC=="ISK"){
		cISK=number;

		cPLEX= parseInt(cISK*1000/transactiontype)/1000;
		cUSD=parseInt(cPLEX*100*PLEXPACK)/100;
		cKRW=parseInt(parseInt(cUSD*rate_obj.USD));	

	}

	switch (toC)
	{
		case "KRW":
			return cKRW;
			break;
		case "USD": 
			return cUSD;
			break;
		case "PLEX": 
			return cPLEX;
			break;
		case "ISK": 
			return cISK;
			break;
	}


}

function showValue(){

	$('#ISKper10kKRW').html(NWC(Math.round(exchange(10000,"KRW","ISK")/100000)/10));
	$('#KRWper1bISK').html(NWCKR(exchange(1000000000,"ISK","KRW")));

}

</script>