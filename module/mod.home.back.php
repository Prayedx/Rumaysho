<?php
//$user->direct_login();
require_once(CLASS_PATH."/bimasakti.class.php");
$bimasakti = new Bimasakti();

$iconSection 	= get_icon_section();
$pageTitle 		= "Dashboard";
$pageName 		= "home";

$countBeritamu 	= $content->select_content("count", PENGUMUMAN_ID);

$totalDeposit 	= $customer->get_total_deposit();
$totalTrans 	= $bisnismu->get_total_ppob_transaction();
$currentDeposit = $totalDeposit-$totalTrans;
$currentTrans 	= $bisnismu->get_total_ppob_transaction(date('Y-m'));


$totalSaldo = "Rp. 0";

if($_SERVER['HTTP_HOST'] != "localhost") 
{
	$bimasakti->mode 	= "production";
	$xmlData 			= $bimasakti->set_xml_data("balance");
	$dataResult 		= $bimasakti->send_xml_data($xmlData, "0");

	if($dataResult['status']=="1") {
		$response 		= xmlrpc_decode($dataResult['text'],null);
		$dataResponse 	= $bimasakti->parse_xml_response("balance", $response);
		$totalSaldo 	= "Rp. ".number($dataResponse['saldo']);
	}
}

//IDB
$totalSaldoIDB = "Rp. 0";
if($_SERVER['HTTP_HOST'] != "localhost")
{
    require_once(CLASS_PATH."/idbiller.class.php");
    $idbiller = new IdBiller();

    $idbiller->mode 		= "production";
    $idbiller->methodName 	= "BalanceService";

    $dataResult = $idbiller->send_data();
    if ($dataResult['status'] == "1") {
        $response 			   = json_decode($dataResult['text'], true);
        $totalSaldoIDB 		   = "Rp. ".number($response['Saldo']);
    }
}
$thisMonth 	= date('Y-m');
$lastDay 	= date('t', strtotime($thisMonth));

$arrDay1 		= 
$arrDay2 		= 
$arrDayTot1 	= 
$arrDayTot2 	= 
$arrTime1 		= 
$arrTime2 		= 
$arrTimeTot1 	= 
$arrTimeTot2 	= 
	array();

/*STATISTIK HARIAN*/

for($i=1; $i<=24 ; $i++) { 
	$arrTime1[$i] 		= "0";
	$arrTime2[$i] 		= "0";
	$arrTimeTot1[$i] 	= "0";
	$arrTimeTot2[$i] 	= "0";
}

$ppobDayStatistic = $bisnismu->ppob_day_statistic();

if(!empty($ppobDayStatistic)) {
	foreach($ppobDayStatistic as $val) {
		$arrTime1[$val['TIME']] 	= $val['TOTAL_TRX'];
		$arrTimeTot1[$val['TIME']] 	= $val['TOTAL'];
	}
}

$depositDayStatistic = $customer->deposit_day_statistic();

if(!empty($depositDayStatistic)) {
	foreach($depositDayStatistic as $val) {
		$arrTime2[$val['TIME']] 	= $val['TOTAL_DEPOSIT'];
		$arrTimeTot2[$val['TIME']] 	= $val['TOTAL'];
	}
}

$ppobTimeStat 		= "";
$depositTimeStat 	= "";

for($i=1; $i<=24 ; $i++) { 
	$ppobTimeStat 		.= "{period: '".$i.".00', Rp: ".$arrTime1[$i].", Transaksi:'".$arrTimeTot1[$i]."'}";
	$depositTimeStat 	.= "{period: '".$i.".00', Rp: ".$arrTime2[$i].", Transaksi:'".$arrTimeTot2[$i]."'}";
	
	if($i<24) $ppobTimeStat .= ",";
	if($i<24) $depositTimeStat .= ",";
}

/*STATISTIK BULANAN*/

for($i=1; $i<=$lastDay; $i++) {
	$day = substr("0".$i, -2);

	$arrDay1[date('Y-m-').$day] 	= "0";
	$arrDay2[date('Y-m-').$day] 	= "0";
	$arrDayTot1[date('Y-m-').$day] 	= "0";
	$arrDayTot2[date('Y-m-').$day] 	= "0";
}

$ppobStatistic = $bisnismu->ppob_statistic(date('Y-m-01'),date('Y-m-').substr("0".$lastDay,-2));

for($i=0; $i<count($ppobStatistic); $i++) {
	if(isset($ppobStatistic[$i])) {
		$arrDay1[$ppobStatistic[$i]['DATE']]	= $ppobStatistic[$i]['TOTAL_TRX'];
		$arrDayTot1[$ppobStatistic[$i]['DATE']] = $ppobStatistic[$i]['TOTAL'];
	}
}

$depositStatistic = $customer->deposit_statistic(date('Y-m-01'),date('Y-m-').substr("0".$lastDay,-2));

for($i=0; $i<count($depositStatistic); $i++) {
	if(isset($depositStatistic[$i])) {
		$arrDay2[$depositStatistic[$i]['DATE']]		= $depositStatistic[$i]['TOTAL_DEPOSIT'];
		$arrDayTot2[$depositStatistic[$i]['DATE']]	= $depositStatistic[$i]['TOTAL'];
	}
}

$ppobStat 		= "";
$depositStat 	= "";

for($i=1; $i<=$lastDay; $i++) {
	$day = substr("0".$i,-2);

	$ppobStat 		.= "{period: '".date('Y-m-').$day."', Rp: ".$arrDay1[date('Y-m-').$day].", Transaksi:'".$arrDayTot1[date('Y-m-').$day]."'}";
	$depositStat 	.= "{period: '".date('Y-m-').$day."', Rp: ".$arrDay2[date('Y-m-').$day].", Transaksi:'".$arrDayTot2[date('Y-m-').$day]."'}";
	
	if($i<$lastDay) $ppobStat .= ",";
	if($i<$lastDay) $depositStat .= ",";
}

?>