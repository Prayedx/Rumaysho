<?php 
$iconSection = get_icon_section($this->thisPage);
if($this->subActionPage=="customer"){
	$pageName = "report-customer";
	$pageTitle = "Laporan Pelanggan";
	
	if($this->dataPage==""){
		header("Location:".ADMIN_HOST."/report/customer/".date('Y-m'));
		exit;
	}
	if(isset($this->dataPage) && $this->dataPage!=""){
		$arrDY = explode("-",$this->dataPage);
		$mYear = $arrDY[0];
		$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
		if(intval($mYear)<2015 || intval($mMonth)>12){
			header("Location:".ADMIN_HOST."/report/customer/".date('Y-m'));
			exit;
		}
	}
	
	$countMonth = ((intval(date('Y'))-2015)*12) + (intval(date('m'))-5) + 1 ;
	$arrMonth = array();
	for($i=0;$i<=$countMonth-1;$i++){
		$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
		$arrMonth[$statIndex] = 0;
	}
	
	$dateStart = $this->dataPage."-01";
	$dateEnd = date('Y-m-t',strtotime($dateStart));
	$dataCustomer = $customer->select_customer_report("registration",$dateStart,$dateEnd);
	$countCustomer = $customer->select_customer_report("countRegistration");
	$totalCustomer = $customer->select_customer("count");
	$statInit = array();
	for($i=1;$i<=intval(substr($dateEnd,-2));$i++){
		$statInit[substr($dateStart,0,8).substr("0".$i,-2)] = 0;
	}
	//p($statInit);
	$data = array();
	foreach($dataCustomer as $key=>$val){
		$data[$val['DATE']] = $val['TOTAL'];
	}
	//p($dataCustomer);exit;
	$dataReport = array_merge($statInit,$data);
	//p($dataReport);exit;
	$dataToShow = "";
	$i = 1;
	foreach($dataReport as $key=>$val){
		$dataToShow .= "{x: '".$key."', y:".$val."}";
		$dataToShow .= ($i<count($dataReport))?",":"";
		$i++;
	}
	//echo $dataToShow;exit;
	//{x: '2015-06-01', y:3},
}
elseif($this->subActionPage=="deposit"){
	$pageName = "report-deposit";
	$pageTitle = "Laporan Deposit";
	
	if($this->dataPage==""){
		header("Location:".ADMIN_HOST."/report/deposit/".date('Y-m'));
		exit;
	}
	if(isset($this->dataPage) && $this->dataPage!=""){
		$arrDY = explode("-",$this->dataPage);
		$mYear = $arrDY[0];
		$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
		if(intval($mYear)<2015 || intval($mMonth)>12){
			header("Location:".ADMIN_HOST."/report/deposit/".date('Y-m'));
			exit;
		}
	}
	
	$countMonth = ((intval(date('Y'))-2015)*12) + (intval(date('m'))-5) + 1 ;
	$arrMonth = array();
	for($i=0;$i<=$countMonth-1;$i++){
		$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
		$arrMonth[$statIndex] = 0;
	}
	
	$totalDeposit = $customer->get_total_deposit();
	$totalTrans = $bisnismu->get_total_ppob_transaction();
	$currentDeposit = $totalDeposit-$totalTrans;
	$currentTrans = $bisnismu->get_total_ppob_transaction(date('Y-m'));
	
	$thisMonth = $this->dataPage;
	$lastDay = date('t',strtotime($thisMonth));
	$arrDay1 = array();
	$arrDay2 = array();
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$arrDay2[$this->dataPage."-".$day] = "0";
	}
	
	$depositStatistic = $customer->deposit_statistic($this->dataPage."-01",$this->dataPage."-".substr("0".$lastDay,-2));
	for($i=0;$i<count($depositStatistic);$i++){
		if(isset($depositStatistic[$i])){
			$arrDay2[$depositStatistic[$i]['DATE']] = $depositStatistic[$i]['TOTAL_DEPOSIT'];
		}
	}
	
	$ppobStat = "";
	$depositStat = "";
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$depositStat .= "{period: '".$this->dataPage."-".$day."', Rp: ".$arrDay2[$this->dataPage."-".$day]."}";
		if($i<$lastDay) $depositStat .= ",";
	}
	//echo $depositStat;exit;
	//echo $depositStat2;exit;
}
elseif($this->subActionPage=="transaction"){
	$pageName = "report-transaction";
	$pageTitle = "Laporan Transaksi";
	
	if($this->dataPage==""){
		header("Location:".ADMIN_HOST."/report/transaction/".date('Y-m'));
		exit;
	}
	if(isset($this->dataPage) && $this->dataPage!=""){
		$arrDY = explode("-",$this->dataPage);
		$mYear = $arrDY[0];
		$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
		if(intval($mYear)<2015 || intval($mMonth)>12){
			header("Location:".ADMIN_HOST."/report/transaction/".date('Y-m'));
			exit;
		}
	}
	
	$countMonth = ((intval(date('Y'))-2015)*12) + (intval(date('m'))-5) + 1 ;
	$arrMonth = array();
	for($i=0;$i<=$countMonth-1;$i++){
		$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
		$arrMonth[$statIndex] = 0;
	}
	
	$totalDeposit = $customer->get_total_deposit();
	$totalTrans = $bisnismu->get_total_ppob_transaction();
	$currentDeposit = $totalDeposit-$totalTrans;
	$currentTrans = $bisnismu->get_total_ppob_transaction(date('Y-m'));
	
	$thisMonth = $this->dataPage;
	$lastDay = date('t',strtotime($thisMonth));
	$arrDay1 = array();
	$arrDay2 = array();
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$arrDay1[$this->dataPage."-".$day] = "0";
	}
	
	$ppobStatistic = $bisnismu->ppob_statistic($this->dataPage."-01",$this->dataPage."-".substr("0".$lastDay,-2));
	for($i=0;$i<count($ppobStatistic);$i++){
		if(isset($ppobStatistic[$i])){
			$arrDay1[$ppobStatistic[$i]['DATE']] = $ppobStatistic[$i]['TOTAL_TRX'];
		}
	}
	
	$ppobStat = "";
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$ppobStat .= "{period: '".$this->dataPage."-".$day."', Rp: ".$arrDay1[$this->dataPage."-".$day]."}";
		if($i<$lastDay) $ppobStat .= ",";
	}
	//echo $depositStat;exit;
	//echo $depositStat2;exit;
}
elseif($this->subActionPage=="fee"){
	$pageName = "report-fee";
	$pageTitle = "Laporan Komisi";
	
	if($this->dataPage==""){
		header("Location:".ADMIN_HOST."/report/fee/".date('Y-m'));
		exit;
	}
	if(isset($this->dataPage) && $this->dataPage!=""){
		$arrDY = explode("-",$this->dataPage);
		$mYear = $arrDY[0];
		$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
		if(intval($mYear)<2015 || intval($mMonth)>12){
			header("Location:".ADMIN_HOST."/report/fee/".date('Y-m'));
			exit;
		}
	}
	
	$countMonth = ((intval(date('Y'))-2015)*12) + (intval(date('m'))-5) + 1 ;
	$arrMonth = array();
	for($i=0;$i<=$countMonth-1;$i++){
		$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
		$arrMonth[$statIndex] = 0;
	}
	
	$totalDeposit = $customer->get_total_deposit();
	$totalTrans = $bisnismu->get_total_ppob_transaction();
	$currentDeposit = $totalDeposit-$totalTrans;
	$currentTrans = $bisnismu->get_total_ppob_transaction(date('Y-m'));
	
	$thisMonth = $this->dataPage;
	$lastDay = date('t',strtotime($thisMonth));
	$arrDay1 = array();
	$arrDay2 = array();
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$arrDay1[$this->dataPage."-".$day] = "0";
	}
	
	$ppobFeeStatistic = $bisnismu->ppob_fee_statistic($this->dataPage."-01",$this->dataPage."-".substr("0".$lastDay,-2));
	for($i=0;$i<count($ppobFeeStatistic);$i++){
		if(isset($ppobFeeStatistic[$i])){
			$arrDay1[$ppobFeeStatistic[$i]['DATE']] = $ppobFeeStatistic[$i]['TOTAL_FEE'];
		}
	}
	
	$ppobFeeStat = "";
	for($i=1;$i<=$lastDay;$i++){
		$day = substr("0".$i,-2);
		$ppobFeeStat .= "{period: '".$this->dataPage."-".$day."', Rp: ".$arrDay1[$this->dataPage."-".$day]."}";
		if($i<$lastDay) $ppobFeeStat .= ",";
	}
}
elseif($this->subActionPage=="profit"){
	$pageName = "report-profit";
	$pageTitle = "Laporan Laba Rugi";
	
	if($this->dataPage==""){
		header("Location:".ADMIN_HOST."/report/profit/".date('Y-m'));
		exit;
	}
	
	if(isset($this->dataPage) && $this->dataPage!=""){
		$arrDY = explode("-",$this->dataPage);
		$mYear = $arrDY[0];
		$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
		if(intval($mYear)<2015 || intval($mMonth)>12){
			header("Location:".ADMIN_HOST."/report/profit/".date('Y-m'));
			exit;
		}
	}
	
	$countMonth = ((intval(date('Y'))-2015)*12) + (intval(date('m'))-5) + 1 ;
	$arrMonth = array();
	for($i=0;$i<=$countMonth-1;$i++){
		$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
		$arrMonth[$statIndex] = 0;
	}
	
	$dateStart = $this->dataPage."-01";
	$dateEnd = date('Y-m-t',strtotime($dateStart));
	$arrDepositSuccess = array();
	$arrDepositAmount = array();
	$arrTransCount = array();
	$arrTransAmount = array();
	$arrFeeCount = array();
	$arrFeeAmount = array();
	$arrProfitAmount = array();
	for($i=1;$i<=intval(substr($dateEnd,-2));$i++){ 
		$arrDepositSuccess[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrDepositAmount[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrTransCount[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrTransAmount[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrFeeCount[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrFeeAmount[$this->dataPage."-".substr("0".$i,-2)] = 0;
		$arrProfitAmount[$this->dataPage."-".substr("0".$i,-2)] = 0;
	}
	
	$countDepositSuccess = $customer->select_customer_report("countDepositSuccess",$dateStart,$dateEnd); // p($countDepositSuccess);exit;
	if(count($countDepositSuccess)>0){
		foreach($countDepositSuccess as $key=>$val){
			$arrDepositSuccess[$val['DATE']] = $val['TOTAL'];
			$arrDepositAmount[$val['DATE']] = $val['AMOUNT'];
		}
	}
	
	$ppobStatistic = $bisnismu->ppob_statistic($dateStart,$dateEnd); //p($ppobStatistic);exit;
	if(count($ppobStatistic)>0){
		foreach($ppobStatistic as $key=>$val){
			$arrTransAmount[$val['DATE']] = $val['TOTAL_TRX'];
			$arrTransCount[$val['DATE']] = $val['TOTAL'];
		}
	}
	
	$ppobFeeStatistic = $bisnismu->ppob_fee_statistic($dateStart,$dateEnd); //p($ppobStatistic);exit;
	if(count($ppobFeeStatistic)>0){
		foreach($ppobFeeStatistic as $key=>$val){
			$arrFeeAmount[$val['DATE']] = $val['TOTAL_FEE'];
			$arrFeeCount[$val['DATE']] = $val['TOTAL'];
		}
	}
	
	$ppobProfitStatistic = $bisnismu->ppob_profit_statistic($dateStart,$dateEnd);
	if(count($ppobProfitStatistic)>0){
		foreach($ppobProfitStatistic as $key=>$val){
			$arrProfitAmount[$val['DATE']] = $val['PROFIT'];
		}
	}
	//p($ppobProfitStatistic); exit;
	
	//p($arrTransAmount);exit;
	
	$profitPembelianMonth = $bisnismu->get_profit("pembelian",$dateStart,$dateEnd);
	$profitPembayaranMonth = $bisnismu->get_profit("pembayaran",$dateStart,$dateEnd);
	$totalProfitMonth = $profitPembelianMonth+$profitPembayaranMonth;
	$profitPembelian = $bisnismu->get_profit("pembelian");
	$profitPembayaran = $bisnismu->get_profit("pembayaran");

	$totalProfit = $profitPembelian+$profitPembayaran;
	$totalDeposit = $customer->get_total_deposit();
	$totalTrans = $bisnismu->get_total_ppob_transaction();
	$currentDeposit = $totalDeposit-$totalTrans;
	$totalModal = $bisnismu->get_total_ppob_modal();
	$currentFee = $bisnismu->get_current_fee();
	
	$totalSaldo = "Rp. 0";
	if($_SERVER['HTTP_HOST']!="localhost"){
		require_once(CLASS_PATH."/bimasakti.class.php");
		$bimasakti = new Bimasakti();
		$bimasakti->mode = "production";
		$xmlData = $bimasakti->set_xml_data("balance");
		$dataResult = $bimasakti->send_xml_data($xmlData,"0");
		if($dataResult['status']=="1"){
			$response = xmlrpc_decode($dataResult['text'],null); //print_r($response);exit;
			$dataResponse = $bimasakti->parse_xml_response("balance",$response); //print_r($dataResponse);exit;
			$totalSaldo = "Rp. ".number($dataResponse['saldo']);
		}
	}
}
else{
	header("Location:".ADMIN_HOST."/report/customer");
	exit;
}

?>