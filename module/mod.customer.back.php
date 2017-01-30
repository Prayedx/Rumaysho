<?php
//controller
if($_SESSION['Admine']['LevelID']>2){ header("Location:".ADMIN_HOST);exit;}
$PageSize = 50;
$PageDisplay = 9;
require_once(FUNCTION_PATH . "/function_paging.php");
$customer->beginRec = $beginRec;
$customer->endRec = $intPageSize;
$iconSection = get_icon_section($this->thisPage);
if(!isset($_SESSION['TxtMsg'])) $_SESSION['TxtMsg'] = array("status"=>"","text"=>"","field"=>"");

if($this->subActionPage=="" || $this->subActionPage=="page"){
	$pageTitle = "Daftar Pelanggan";
	$pageName = "customer";
	
	$filterKey = (isset($_SESSION['searchCustomer'])) ? $_SESSION['searchCustomer'] : "";
	if($filterKey!=""){
		if(intval($filterKey)>14122940){
			$customer->customerId = $filterKey-14122940;
		}
		//object //field
		$customer->customerName = $filterKey;
		$customer->customerEmail = $filterKey;
		//objek// method
		$data 	= $customer->select_customer("search");
		$count 	= $customer->select_customer("countSearch");
	}
	else{
		if(isset($_SESSION['searchCustomer'])) unset($_SESSION['searchCustomer']);
		
		$data 	= $customer->select_customer();
		$count 	= $customer->select_customer("count");
	}
	pagingShow($count,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
}
elseif($this->subActionPage=="deposit"){
	$pageTitle 	= "Deposit Pelanggan";
	$pageName 	= "customer-deposit";
	
	$statInit 	= array('new'=>0,'pending'=>0,'success'=>0,'cancel'=>0);

	$statDeposit 	= $customer->select_total_deposit(); 
	$summaryDeposit = array_merge($statInit,$statDeposit);
	$totalDeposit 	= $customer->get_total_deposit();
	$totalTrans 	= $bisnismu->get_total_ppob_transaction();
	$currentDeposit = $totalDeposit-$totalTrans;
		
	if(isset($_SESSION['filterDepoKey'])){
		$data 	= $customer->select_customer_deposit("filterDeposit");
		$count 	= $customer->select_customer_deposit("countFilterDeposit");
	}
	else{
		if(isset($_SESSION['filterDepoKey'])) unset($_SESSION['filterDepoKey']);
		if(isset($_SESSION['filterDepoType'])) unset($_SESSION['filterDepoType']);
		if(isset($_SESSION['filterBank'])) unset($_SESSION['filterBank']);
		if(isset($_SESSION['filterStatus'])) unset($_SESSION['filterStatus']);

		$data 	= $customer->select_customer_deposit();
		$count 	= $customer->select_customer_deposit("count");
	}

	pagingShow($count,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
}
elseif($this->subActionPage=="detail"){
	$customer->customerId = intval($this->dataPage);
	$bisnismu->customerId = $customer->customerId;
	$detailCustomer = $customer->select_customer("byId");
	$lastBlock = $customer->select_customer_block("last");
	$customerSaldo = $customer->get_member_saldo($customer->customerId);
	$totalDeposit = $customer->select_customer_deposit("totalByCustomer");
	if(count($detailCustomer)==0){
		header("Location:".ADMIN_HOST."/".$this->thisPage);exit;
	}
	
	$pageTitle = "Detail Pelanggan";
	$pageName = "customer-detail";
	
	if($this->subDataPage==""){
		$bisnismu->transCreateDate = date('Y-m');
		//$totalDeposit = $customer->statistic_member_deposit("totalByCustomer");
		$totalTrans = $bisnismu->statistic_ppob_transaction("totalByCustomer");
		$totalTransThisMonth = $bisnismu->statistic_ppob_transaction("totalTransThisMonth");
		$currentFee = $bisnismu->statistic_ppob_transaction("totalFeeByCustomer");
		$totalFee 	= $bisnismu->statistic_ppob_transaction("feeByCustomer");
		$totalFeeReceive = $totalFee;
		$totalSaldo = $customer->get_member_saldo($customer->customerId);		
		$depositMember = $totalDeposit;
		$subPage = $pageName."-profile";
	}
	elseif($this->subDataPage=="mutasi"){
		if($this->subsubDataPage==""){
			header("Location:".ADMIN_HOST."/customer/detail/".$this->dataPage."/mutasi/".date('Y-m'));
			exit;
		}
		if(isset($this->subsubDataPage) && $this->subsubDataPage!=""){
			$arrDY = explode("-",$this->subsubDataPage);
			$mYear = $arrDY[0];
			$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
			if(intval($mYear)<2015 || intval($mMonth)>12){
				header("Location:".ADMIN_HOST."/customer/detail/".$this->dataPage."/mutasi/".date('Y-m'));
				exit;
			}
		}
		
		if(count($detailCustomer)>0){
			$lastMonth = date('Y-m',strtotime("+1 month",strtotime($this->subsubDataPage)));
			$balance = $customer->get_last_saldo($customer->customerId,$lastMonth);
			
			$yStart = substr($detailCustomer['customer_create_date'],0,4);
			$mStart = substr($detailCustomer['customer_create_date'],5,2);
			
			$countMonth = ((intval(date('Y'))-$yStart)*12) + (intval(date('m'))-intval($mStart)) + 1 ;
			$statInit = array();
			for($i=0;$i<=$countMonth-1;$i++){
				$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
				$statInit[$statIndex] = 0;
			}
			//p($statInit);exit;
			
			$memberId = $detailCustomer['customer_id']+14122940;
			$customer->customerCreateDate = $this->subsubDataPage;
			$lastSaldo = $customer->get_last_saldo($customer->customerId,$this->subsubDataPage); //echo($lastSaldo);exit;
			$saldo = array();
			if(substr($detailCustomer['customer_create_date'],0,7)!=$this->subsubDataPage){
				$saldo[0]['data_id'] = 0;
				$saldo[0]['datetime'] = $this->subsubDataPage."-01 00:00:00";
				$saldo[0]['date'] = date_indo($saldo[0]['datetime'],"datetime");
				$saldo[0]['DB'] = 0;
				$saldo[0]['CR'] = number($lastSaldo);
				$saldo[0]['info'] = "Saldo ".arrMonths("id")[intval(substr($this->subsubDataPage,-2))-2]." ".substr($this->subsubDataPage,0,4);
				$saldo[0]['status'] = "success";
				//$saldo[0]['type'] = "CR";
			}
			
			$dataDeposit = $customer->select_customer_deposit("byCustomerMutasi");//p($dataDeposit);exit;
			$deposit = array();
			if(count($dataDeposit)>0){
				for($i=0;$i<count($dataDeposit);$i++){
					$deposit[$i]['data_id'] = $dataDeposit[$i]['data_id'];
					$deposit[$i]['datetime'] = $dataDeposit[$i]['date'];
					$deposit[$i]['date'] = date_indo($dataDeposit[$i]['date'],"datetime");
					$deposit[$i]['DB'] = 0;
					$deposit[$i]['CR'] = number($dataDeposit[$i]['amount']);
					$deposit[$i]['info'] = "DEPOSIT ".strtoupper($dataDeposit[$i]['info']);
					$deposit[$i]['status'] = $dataDeposit[$i]['status'];
					//$deposit[$i]['type'] = "CR";
				}
			}
			//p($deposit);
			
			$bisnismu->transCreateDate = $this->subsubDataPage;
			$dataTrans = $bisnismu->select_ppob_transaction("byCustomerMutasi"); //p($dataTrans);exit;
			$trans = array();
			if(count($dataTrans)>0){
				for($i=0;$i<count($dataTrans);$i++){
					$trans[$i]['data_id'] = $dataTrans[$i]['trans_id'];
					$trans[$i]['datetime'] = $dataTrans[$i]['trans_create_date'];
					$trans[$i]['date'] = date_indo($dataTrans[$i]['trans_create_date'],"datetime");
					$trans[$i]['DB'] = number($dataTrans[$i]['trans_price']);
					$trans[$i]['CR'] = number($dataTrans[$i]['trans_fee']);
					$trans[$i]['info'] = ucwords($dataTrans[$i]['trans_type'])." ".$dataTrans[$i]['ppob_product_name']." nomor: ".$dataTrans[$i]['trans_customer_number'];
					$trans[$i]['status'] = $dataTrans[$i]['trans_status'];
					//$trans[$i]['type'] = "DB";
				}
			}
			//p($trans);
			
			if(count($deposit)>0 || count($trans)>0){ 
				$result = array_merge($deposit,$trans);
				$result = array_merge($result,$saldo);
				
				/*$dataFee = $bisnismu->get_fee_receive($customer->customerId); //p($dataFee);exit;
				if(count($dataFee)>0){
					$fee = array();
					for($i=0;$i<count($dataFee);$i++){
						$fee[$i]['data_id'] = 0;
						$fee[$i]['datetime'] = date('Y-m-d 00:00:00',strtotime("+1 month",strtotime($dataFee[$i]['MONTH']."-01")));
						$fee[$i]['date'] = date_indo(date('Y-m-d 00:00:00',strtotime("+1 month",strtotime($dataFee[$i]['MONTH']."-01"))),"datetime");
						$fee[$i]['amount'] = number($dataFee[$i]['TOTAL_FEE']);
						$fee[$i]['info'] = "Komisi bulan ".arrMonths("id")[intval(substr($dataFee[$i]['MONTH'],-2))-1]." ".substr($dataFee[$i]['MONTH'],0,4);
						$fee[$i]['status'] = "success";
						$fee[$i]['type'] = "CR";
					}
					$result = array_merge($result,$fee);
				}*/
				
				usort($result, function($a, $b) {
				  return (strtotime($a['datetime']) < strtotime($b['datetime'])) ? 1 : -1;
				});
				
			}else{
				$result = array();
			}
			//p($result);exit;
		}
		else{
			$result = array();
		}
		$subPage = $pageName."-mutasi";
	}
	elseif($this->subDataPage=="deposit"){
		$data = $customer->select_customer_deposit("byCustomer");
		$countDeposit = $customer->select_customer_deposit("countByCustomer");
		$dataBank = $setting->select_bank('active');

		pagingShow($countDeposit,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
		$subPage = $pageName."-deposit";
	}
	elseif($this->subDataPage=="fee"){
		$bisnismu->beginRec = $beginRec;
		$bisnismu->endRec = $intPageSize;
		
		if(isset($this->subsubDataPage) && $this->subsubDataPage!=""){
			$arrDY = explode("-",$this->subsubDataPage);
			$mYear = $arrDY[0];
			$mMonth = isset($arrDY[1])? $arrDY[1]: 13;
			if(intval($mYear)<2015 || intval($mMonth)>12){
				header("Location:".ADMIN_HOST."/customer/detail/".$this->dataPage."/fee/".date('Y-m'));
				exit;
			}
			
		}
		
		$yStart = substr($detailCustomer['customer_create_date'],0,4);
		$mStart = substr($detailCustomer['customer_create_date'],5,2);
		
		$countMonth = ((intval(date('Y'))-$yStart)*12) + (intval(date('m'))-intval($mStart)) + 1 ;
		$statInit = array();
		for($i=0;$i<=$countMonth-1;$i++){
			$statIndex = date('Y-m',strtotime("-".$i." month",strtotime(date('Y-m'))));
			$statInit[$statIndex] = 0;
		}
		$bisnismu->transCreateDate = date('Y-m');
		$currentFee = $bisnismu->statistic_ppob_transaction("totalFeeByCustomer");
		$totalFee 	= $bisnismu->statistic_ppob_transaction("feeByCustomer");
		$totalFeeReceive = $totalFee-$currentFee;
		
		$bisnismu->transCreateDate = $this->subsubDataPage;
		$data = $bisnismu->select_ppob_transaction("feeByCustomer");
		$countFee = $bisnismu->select_ppob_transaction("countFeeByCustomer");
		pagingShow($countFee,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
		$subPage = $pageName."-fee";
	}	
	elseif($this->subDataPage=="statistik"){
		
		$subPage = $pageName."-statistik";
	}
	else{
		header("Location:".ADMIN_HOST."/customer/detail/".$customer->customerId);
		exit;
	}
}


if(isset($_POST['changePass'])){
	$pass1 = $_POST['newPass1'];
	$pass2 = $_POST['newPass2'];
	if($pass1 != $pass2){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Password tidak cocok.");
	}
	else{
		if(trim($pass1)==""){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Password tidak boleh kosong.");
		}
		elseif(strlen($pass1)<6){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Password minimal 6 karakter.");
		}
		elseif($pass1=="123456"){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Sistem tidak mengijinkan password <strong>123456</strong>");
		}
		else{
			//$customer->customerPassword = secure($pass1);
			$customer->customerPassword = $pass1;
			$customer->update_customer("byField","customer_password",$customer->customerPassword);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Pergantian password baru berhasil.");
			header("Location:".$_SERVER['HTTP_REFERER']);
			exit;
		}
	}
}


if(isset($_POST['updateStatus'])){
	$customer->customerStatus = $customer->cbStatus = $_POST['cbStatus'];
	$customer->cbReason = nl2br($_POST['cbReason']);
	$customer->cbAdmin = $_SESSION['Admine']['ID'];
	
	if($customer->cbReason==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Alasan ganti status pelanggan wajib diisi.");
	}
	else{
		$customer->update_customer("byField","customer_status",$customer->customerStatus);
		$customer->insert_customer_block();
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Status Pelanggan telah diganti menjadi <strong>".strtoupper($customer->customerStatus)."</strong>.");
		header("Location:".$_SERVER['HTTP_REFERER']);
		exit;
	}	
}

if(isset($_POST['updateDeposit'])){
	$customer->userId = $_SESSION['Admine']['ID'];
	$customer->depositUpdateDate = date('Y-m-d H:i:s');
	$customer->depositStatus = security($_POST['depositStatus']);
	$customer->depositUpdateText = security($_POST['depositUpdateText']);
	$customer->depositId = security($_POST['depositIdUpdate']);
	$customerName = security($_POST['depositNameUpdate']);
	$amount = security($_POST['depositAmountUpdate']);
	
	$customer->update_deposit("adminUpdate");
	
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Deposit <strong>".$customerName."</strong> sebesar <strong>Rp ".number($amount)."</strong> berhasil ditambahkan.");
	
	//GCM push notif 
	
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
	
}

//filter customer
if(isset($_POST['filterCustomer'])){
	$filterKey = ($_POST['filterKey']!="") ? security($_POST['filterKey']) : "";
	$_SESSION['searchCustomer'] = $filterKey;
	header("Location:".ADMIN_HOST."/customer");
	exit;
}
if(isset($_POST['resetFilterCustomer'])){
	if(isset($_SESSION['searchCustomer'])) unset($_SESSION['searchCustomer']);
	header("Location:".ADMIN_HOST."/customer");
	exit;
}

//filter deposit
if(isset($_POST['filterDeposit'])) {
	$_SESSION['filterDepoKey'] 		= (!empty($_POST['filterDepoKey'])) ? security($_POST['filterDepoKey']) : "";
	$_SESSION['filterDepoType'] 	= (!empty($_POST['filterDepoType'])) ? security($_POST['filterDepoType']) : "";
	$_SESSION['filterBank'] 		= (!empty($_POST['filterBank'])) ? security($_POST['filterBank']) : "";
	$_SESSION['filterStatus'] 		= (!empty($_POST['filterStatus'])) ? security($_POST['filterStatus']) : "";

	header("Location:".ADMIN_HOST."/customer/deposit");
	exit;
}

if(isset($_POST['resetFilterDeposit'])) {
	if(isset($_SESSION['filterDepoKey'])) unset($_SESSION['filterDepoKey']);
	if(isset($_SESSION['filterDepoType'])) unset($_SESSION['filterDepoType']);
	if(isset($_SESSION['filterBank'])) unset($_SESSION['filterBank']);
	if(isset($_SESSION['filterStatus'])) unset($_SESSION['filterStatus']);

	header("Location:".ADMIN_HOST."/customer/deposit");
	exit;
}

if(isset($_POST['addDeposit'])) {
	$setting->bankId = security($_POST['depositBank']);
	$detailBank = $setting->select_bank('byId');

	$customer->customerId 			= $this->dataPage;
	$customer->userId 				= $_SESSION['Admine']['ID'];
	$customer->depositAmount 		= security(str_replace('.', '', $_POST['depositTotal']));
	$customer->depositBankTo 		= $detailBank['bank_name'];
	$customer->depositBankToAcc 	= $detailBank['bank_account'];
	$customer->depositBankToNumber 	= $detailBank['bank_number'];
	$customer->depositStatus 		= 'pending';

	if($customer->insert_deposit()) {
		header("Location:".ADMIN_HOST."/customer/detail/".$this->dataPage."/deposit");
		exit;
	}
}

?>