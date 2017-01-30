<?php 
$PageSize = 50;
$PageDisplay = 9;
require_once(FUNCTION_PATH . "/function_paging.php");
$mutasi->beginRec = $beginRec;
$mutasi->endRec = $intPageSize;
$mutasi->sectionId = $detailSection['section_id'];
$iconSection = get_icon_section($this->thisPage);
if(!isset($_SESSION['TxtMsg'])) $_SESSION['TxtMsg'] = array("status"=>"","text"=>"","field"=>"");

if(isset($_POST['updateMutasi'])){
	$mutasiId = security($_POST['mutasiIdUpdate']);
	$mutasiInfo = security($_POST['mutasiInfo']);
	$mutasiNote = security($_POST['mutasiNote']);
	if($mutasiInfo!=""){
		$mutasi->mutasiId = $mutasiId;
		$mutasi->mutasiComment = $mutasiInfo."@@".$mutasiNote;
		$mutasi->update_mutasi_comment();
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data Mutasi berhasil di perharui.");
	}
	header("Location:".ADMIN_HOST."/mutasi");
	exit;
}


if($this->subActionPage=="" || $this->subActionPage=="page"){
	$pageName = "mutasi";
	$pageTitle = "Arsip Mutasi Bank";
	$totalMutasi = $mutasi->select_mutasi("totalByBank");
	$total = array();
	$sumMutasi = 0;

	if(!empty($totalMutasi)) {
		foreach ($totalMutasi as $key => $val) {
			$total[$val['BANK']] = $val['TOTAL'];
			$sumMutasi += $val['TOTAL'];
		}
	}

	if(isset($_SESSION['filterMutasiKey'])){
		$dataMutasi 	= $mutasi->select_mutasi("filterMutasi");
		$countMutasi 	= $mutasi->select_mutasi("countFilterMutasi");
	}
	else{
		if(isset($_SESSION['filterMutasiKey'])) unset($_SESSION['filterMutasiKey']);
		if(isset($_SESSION['filterMutasiType'])) unset($_SESSION['filterMutasiType']);
		if(isset($_SESSION['filterBank'])) unset($_SESSION['filterBank']);

		$dataMutasi 	= $mutasi->select_mutasi();
		$countMutasi 	= $mutasi->select_mutasi("count");
	}

	pagingShow($countMutasi,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
}
else{
	$pageName = "404";
	$pageTitle = $sectionName;
}

//filter deposit
if(isset($_POST['filterMutasi'])) {
	$_SESSION['filterMutasiKey'] 	= (!empty($_POST['filterMutasiKey'])) ? security($_POST['filterMutasiKey']) : "";
	$_SESSION['filterMutasiType'] 	= (!empty($_POST['filterMutasiType'])) ? security($_POST['filterMutasiType']) : "";
	$_SESSION['filterBank'] 		= (!empty($_POST['filterBank'])) ? security($_POST['filterBank']) : "";

	header("Location:".ADMIN_HOST."/mutasi");
	exit;
}

if(isset($_POST['resetFilterMutasi'])) {
	if(isset($_SESSION['filterMutasiKey'])) unset($_SESSION['filterMutasiKey']);
	if(isset($_SESSION['filterMutasiType'])) unset($_SESSION['filterMutasiType']);
	if(isset($_SESSION['filterBank'])) unset($_SESSION['filterBank']);

	header("Location:".ADMIN_HOST."/mutasi");
	exit;
}


?>