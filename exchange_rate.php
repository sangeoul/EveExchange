
<?php


include $_SERVER['DOCUMENT_ROOT']."/DBconfig.php";


$dbcon;
$SSLauth;

function dbset(){
	global $dbcon,$servername,$username,$password,$dbname;
	$dbcon=new mysqli($servername,$username,$password,$dbname);
	
	if($dbcon->connect_error){
		die("Connection Failed<br>".$dbcon->connect_error);
	}
}

dbset();

//공공데이터포털 Auth Key
$dataAuthKey="P1fKbSLJWlPufXTKpSIJIZUUhZnpWGJU";


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
		//11 셀가
		$dbresult=$dbcon->query("select * from Exchange where type=11 order by time desc");
		$return_arr=$dbresult->fetch_array();

		if(strtotime($return_arr['time']."+1 hours")<strtotime("now")){
			$qr="insert into Exchange (price,type) values (".str_replace(",", "",$return).",11);";
			$dbcon->query($qr);

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
		//12 바이가
		$dbresult=$dbcon->query("select * from Exchange where type=12 order by time desc");
		$return_arr=$dbresult->fetch_array();

		if(strtotime($return_arr['time']."+1 hours")<strtotime("now")){
			$qr="insert into Exchange (price,type) values (".str_replace(",", "",$return).",12);";
			$dbcon->query($qr);

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


function readUSD(){

	global $dbcon;
	$daybefore=0;
	for($daybefore=0;$daybefore<30;$daybefore++){
		$itemcurl= curl_init();
		curl_setopt($itemcurl, CURLOPT_SSL_VERIFYPEER, $SSLauth); 
		curl_setopt($itemcurl,CURLOPT_HTTPGET,true);
		curl_setopt($itemcurl,CURLOPT_URL,"https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey=".$dataAuthKey."&searchdate=".date("Ymd",(time()-($daybefore*24*3600)))."&data=AP01");
		curl_setopt($itemcurl,CURLOPT_RETURNTRANSFER,true);

		$curl_response=curl_exec($itemcurl);
		curl_close($itemcurl);
		if($curl_response!=NULL && strlen($curl_response)>20){
			break;
		}
	}

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
		}
	}
	
	return floatval(str_replace(",","",$return));	
}


$raw_array=array();

$raw_array['PLEX_buy']=readPLEX("buy");
$raw_array['PLEX_sell']=readPLEX("sell");
$raw_array['PLEX_average']=readPLEX("average");
$raw_array['USD']=readUSD();
$raw_array["USD_date"]=date("Ymd",(time()-($daybefore*24*3600)));

header('Content-Type: application/json');
echo json_encode($raw_array);

?>