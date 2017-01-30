<?php 

$PageSize 		= 50;
$PageDisplay 	= 9;

require_once(FUNCTION_PATH . "/function_paging.php");

$pesan->beginRec 	= $beginRec;
$pesan->endRec 		= $intPageSize;
$iconSection 		= get_icon_section($this->thisPage);

if($this->subActionPage == "" || $this->subActionPage == "page") {
	$pageName 	= 'pesan';
	$pageTitle 	= "Pesan";

	if(isset($_SESSION['filterPesanKey'])) {
		$data 	= $pesan->select_pesan("filter");
		$count 	= $pesan->select_pesan("countFilter");
	}
	else {
		$data 	= $pesan->select_pesan();
		$count 	= $pesan->select_pesan("count");
	}

	pagingShow($count, $pgtogo ,$intPageNumber, $intPageSize, $intPageDisplay);
}
elseif($this->subActionPage == 'conversation') {
	$pesan->pesanId = intval($this->dataPage);
	$data 			= $pesan->select_conversation();
		
	if(count($data) == 0) {
		header("Location:".ADMIN_HOST."/".$this->thisPage);exit;
	}

	$pageName 	= 'pesan-conversation';
	$pageTitle 	= "Conversation";

	if(isset($_POST['btnMsg'])) {
		$in 	= $pesan->insert_conversation();
		$status = $pesan->change_status('terjawab');

		if($in && $status) {
			$customer->customerId = $data[0]['idCustomer'];
			$dataCustomer 	= $customer->select_customer('byId');

	    	$mailTo 	= $dataCustomer['customer_email'];
			$subject 	= "Pesan ".APP_NAME;
			$body 		= "<p>Terdapat pesan balasan dari Admin ".APP_NAME.". Silahkan buka aplikasi ".APP_NAME." anda.</p>";

			$headers 	= 'MIME-Version: 1.0' . "\r\n";
			$headers 	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers 	.= 'From: '.$dataCustomer['customer_name'].' <no_reply@rumaysho.com>' . "\r\n";
			$headers 	.= 'To: '.$dataCustomer['customer_name'].' <' . $dataCustomer['customer_email'] . '>' . "\r\n";
			
			mail($mailTo, $subject, $body, $headers);
			$dataGcm = array('title'=>"Kenalilah Islam",
				'message' => array(
					"title"=>"Kenalilah Islam",
					"text"=> "Anda mendapatkan pesan baru dari Admin",
					"cat"=>"null",
					"type"=>"actionbar",
					"contentId"=>$pesan->pesanId,
					"section"=>"pesan"
				)
			);

			$notif->sendGoogleCloudMessage($dataGcm, array($dataCustomer['customer_reg_id']));
			header("Location:".ADMIN_HOST."/".$this->thisPage."/".$this->subActionPage."/".$this->dataPage);exit;
		}

	}
	elseif(!empty($_POST['close'])) {
		$closed  = $pesan->change_status('closed');

		if($closed) {
			header("Location:".ADMIN_HOST."/".$this->thisPage."/".$this->subActionPage."/".$this->dataPage);exit;
		}
	}
}
else{
	$pageName 	= "404";
	$pageTitle 	= $sectionName;
}

//filter pesan
if(isset($_POST['filterPesan'])) {
	$_SESSION['filterPesanKey'] 	= (!empty($_POST['filterPesanKey'])) ? security($_POST['filterPesanKey']) : "";
	$_SESSION['filterPesanType'] 	= (!empty($_POST['filterPesanType'])) ? security($_POST['filterPesanType']) : "";
	$_SESSION['filterDivisi'] 		= (!empty($_POST['filterDivisi'])) ? security($_POST['filterDivisi']) : "";
	$_SESSION['filterStatus'] 		= (!empty($_POST['filterStatus'])) ? security($_POST['filterStatus']) : "";

	header("Location:".ADMIN_HOST."/pesan");
	exit;
}

if(isset($_POST['resetFilterPesan'])) {
	if(isset($_SESSION['filterPesanKey'])) unset($_SESSION['filterPesanKey']);
	if(isset($_SESSION['filterPesanType'])) unset($_SESSION['filterPesanType']);
	if(isset($_SESSION['filterDivisi'])) unset($_SESSION['filterDivisi']);
	if(isset($_SESSION['filterStatus'])) unset($_SESSION['filterStatus']);

	header("Location:".ADMIN_HOST."/pesan");
	exit;
}