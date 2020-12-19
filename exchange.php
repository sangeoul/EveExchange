<head>
<script data-ad-client="ca-pub-7625490600882004" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>


1 PLEX 

<table border=1s>
	<tr>
		<td>Sell</td>
		<td id="PLEX_sell"></td>
	</tr>
	<tr>
		<td>Buy</td>
		<td id="PLEX_buy"></td>
	</tr>
	<tr>
		<td>Average(yesterday)</td>
		<td id="PLEX_average"></td>
	</tr>

</table>

<?php




include $_SERVER['DOCUMENT_ROOT']."/DBconfig.php";
$SSLauth;

function dbset(){
global $dbcon,$servername,$username,$password,$dbname;

	
	
	$dbcon=new mysqli($servername,$username,$password,$dbname);
	
	if($dbcon->connect_error){
		die("Connection Failed<br>".$dbcon->connect_error);
	}
}

dbset();

echo("<script>\n var sellprice=".readPLEX("sell").";\n var buyprice=".readPLEX("buy").";\n var average=".readPLEX("average").";\n</script>");


function readPLEX($type){

	global $dbcon;
	global $SSLauth;
	
	$return=0;
	$SSLauth=true;


	if($type=="sell"){


		$itemcurl= curl_init();
		
		curl_setopt($itemcurl, CURLOPT_SSL_VERIFYPEER, $SSLauth); 
		curl_setopt($itemcurl,CURLOPT_HTTPGET,true);
		curl_setopt($itemcurl,CURLOPT_URL,"https://esi.evetech.net/latest/markets/10000002/orders/?datasource=tranquility&order_type=sell&type_id=44992");
		curl_setopt($itemcurl,CURLOPT_RETURNTRANSFER,true);

		$curl_response=curl_exec($itemcurl);
		curl_close($itemcurl);

		$arr=json_decode($curl_response,true);
		

		for($return=$arr[0]['price'],$i=0;$i<sizeof($arr);$i++){
			if($return>$arr[$i]['price']){
				$return=$arr[$i]['price'];
			}

		}
	}	

	else if($type=="buy"){

		$itemcurl= curl_init();
		curl_setopt($itemcurl, CURLOPT_SSL_VERIFYPEER, $SSLauth); 
		curl_setopt($itemcurl,CURLOPT_HTTPGET,true);
		curl_setopt($itemcurl,CURLOPT_URL,"https://esi.evetech.net/latest/markets/10000002/orders/?datasource=tranquility&order_type=buy&type_id=44992");
		curl_setopt($itemcurl,CURLOPT_RETURNTRANSFER,true);

		$curl_response=curl_exec($itemcurl);
		curl_close($itemcurl);

		$arr=json_decode($curl_response,true);
		

		for($return=$arr[0]['price'],$i=0;$i<sizeof($arr);$i++){
			if($return<$arr[$i]['price']){
				$return=$arr[$i]['price'];
			}

		}
	}
	
	else if($type=="average" || $type=="avg"){

		$itemcurl= curl_init();
		curl_setopt($itemcurl, CURLOPT_SSL_VERIFYPEER, $SSLauth); 
		curl_setopt($itemcurl,CURLOPT_HTTPGET,true);
		curl_setopt($itemcurl,CURLOPT_URL,"https://esi.evetech.net/latest/markets/10000002/history/?datasource=tranquility&type_id=44992");
		curl_setopt($itemcurl,CURLOPT_RETURNTRANSFER,true);

		$curl_response=curl_exec($itemcurl);
		curl_close($itemcurl);

		$arr=json_decode($curl_response,true);
		

		$return=$arr[(sizeof($arr)-1)]['average'];

	}
	
	return $return;
}

// https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=P1fKbSLJWlPufXTKpSIJIZUUhZnpWGJU&searchdate=20190906&data=AP01

function readUSD(){

	global $dbcon;

	$itemcurl= curl_init();
	curl_setopt($itemcurl, CURLOPT_SSL_VERIFYPEER, $SSLauth); 
	curl_setopt($itemcurl,CURLOPT_HTTPGET,true);
	curl_setopt($itemcurl,CURLOPT_URL,"https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=P1fKbSLJWlPufXTKpSIJIZUUhZnpWGJU&searchdate=".date("Ymd")."&data=AP01");
	curl_setopt($itemcurl,CURLOPT_RETURNTRANSFER,true);

	$curl_response=curl_exec($itemcurl);
	curl_close($itemcurl);

	$arr=json_decode($curl_response,true);

	$dbresult=$dbcon->query("select * from Exchange where type=21 order by time desc");
	
	$return_arr=$dbresult->fetch_array();
	

	if($arr==null){
		$return=$return_arr['price'];
	}

	else {
		for($i=0;$i<sizeof($arr);$i++){
			if($arr[$i]['cur_unit']=="USD"){
				$return=$arr[$i]['deal_bas_r'];
				break;
			}
		}
		if(strtotime($return_arr['time']."+1 hours")<strtotime("now")){
			$qr="insert into Exchange (price,type) values (".str_replace(",", "",$return).",21);";
			$dbcon->query($qr);
			echo("<br>".$qr."<br>");
		}
	}

	return $return;	
}

echo(readUSD());

?>

<script>

document.getElementById("PLEX_sell").innerHTML=numberWithCommas(sellprice);
document.getElementById("PLEX_buy").innerHTML=numberWithCommas(buyprice);
document.getElementById("PLEX_average").innerHTML=numberWithCommas(average);


function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}


</script>