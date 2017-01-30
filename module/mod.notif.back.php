<?php

//if($_SESSION['Admine']['LevelID']>2){ header("Location:".ADMIN_HOST);exit;}
$iconSection = get_icon_section($this->thisPage);
$PageSize = 25;
$PageDisplay = 9;
require_once(FUNCTION_PATH . "/function_paging.php");
$notif->beginRec = $beginRec;
$notif->endRec = $intPageSize;
if(!isset($_SESSION['TxtMsg'])) $_SESSION['TxtMsg'] = array("status"=>"","text"=>"");

if($this->subActionPage=="" || $this->subActionPage=="page"){
	$pageName = "notif";
	$pageTitle = "Daftar Notifikasi";
	$data = $notif->select_notif();
	$count = $notif->select_notif("count");
	pagingShow($count,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);

}

elseif($this->subActionPage=="add"){
	$pageName = "notif-add";
	$pageTitle = "Kirim Notifikasi";
}

if(isset($_POST['addNotif'])){
	$notif->notifTitle = security($_POST['notifTitle']);
	$notif->notifText = security($_POST['notifText']);
	$notif->notifHandle = security($_POST['notifHandle']);
	$notif->sectionId = 0;
	$notif->contentId = 0;
	$notif->notifAuthor = $_SESSION['Admine']['Name'];
	$notif->notifSendDate = date('Y-m-d H:i:s');
	$notif->notifStatus = "sent";
	
	if($notif->notifTitle=="" || $notif->notifText=="" || $notif->notifHandle==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan melengkapi form notifikasi.");
	}
	else{
		$notif->insert_notif();
		
		$dataGcm = array('title'=>$notif->notifTitle,
						 'message' => array(
										"title"=>$notif->notifTitle,
										"text"=> strip_tags($notif->notifText),
										"cat"=>"null",
										"type"=>$notif->notifHandle,
										"contentId"=>$notif->contentId,
										"section"=>$notif->sectionId
										) 
						);
		
		$dataCustomer = $customer->select_registered_customer();
		$dataRegId = array();
		if(count($dataCustomer)>0){
			if(!function_exists("array_column")){
				function array_column($array,$column_name){
					return array_map(function($element) use($column_name){return $element[$column_name];}, $array);			
				}			
			}
			$dataRegId = array_column($dataCustomer, 'customer_reg_id');
		}
		
		/*if(count($dataCustomer)>0){
			$dataRegId = array();
			foreach($dataCustomer as $key=>$val){
				$dataRegId[] = $val['customer_reg_id'];
			}
		}*/
		
		$dataSlice = array();
		$dataSlice = array_chunk($dataRegId,1000);
		for($i=0;$i<count($dataSlice);$i++){
			$dataToSend = $dataSlice[$i];
			$notif->sendGoogleCloudMessage($dataGcm, $dataToSend);
		}
		
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Notifikasi telah dikirim.");
		header("Location:".ADMIN_HOST."/notification");
		exit;
	}
}

?>