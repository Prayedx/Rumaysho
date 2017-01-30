<?php 
if($_SESSION['Admine']['LevelID']>2){ header("Location:".SITE_HOST);exit;}
if(!isset($_SESSION['TxtMsg'])) $_SESSION['TxtMsg'] = array("status"=>"","text"=>"","field"=>"");

$iconSection = get_icon_section($this->thisPage);

if($this->subActionPage==""){
	$pageName = "setting";
	$pageTitle = "CMS Configuration";
}
elseif($this->subActionPage=="provider"){
	$dataProvider = $setting->select_provider();
	$pageName = "setting-provider";
	$pageTitle = "Konfigurasi Provider";
}
elseif($this->subActionPage=="bank"){
	$dataBank = $setting->select_bank();
	$pageName = "setting-bank";
	$pageTitle = "Pengaturan Bank";
}
elseif($this->subActionPage=="apk"){
	if($this->dataPage==""){
		$dataApk = $setting->select_apk();
		$pageName = "setting-apk";
		$pageTitle = "Update APK";
	}
	elseif($this->dataPage=="add"){
		$data = $setting->select_apk("byId");
		$pageName = "setting-apk-add";
		$pageTitle = "Upload New APK";
	}
	elseif($this->dataPage=="edit"){
		$setting->apkId = intval($this->subDataPage);
		$data = $setting->select_apk("byId");
		$pageName = "setting-apk-edit";
		$pageTitle = "Edit APK";
	}
}
elseif($this->subActionPage=="calendar"){
	$pageName = "setting-calendar";
	$pageTitle = "Calendar";
}

if(isset($_POST['saveSetting'])){
	if(count($_POST)>1){
		foreach($_POST as $post=>$data){
			if($post!="saveSetting"){
				if($setting->select_setting("isFieldExists",$post)===true){
					$setting->update_setting($post,$data);
				}
			}
		}
	}
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"New configuration successfully updated.","field"=>"");
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
}

if(isset($_POST['updateProvider'])){
	for($i=0;$i<count($_POST['providerCode']);$i++){
		$setting->providerId 	= $_POST['providerId'][$i];
		$setting->providerCode 	= $_POST['providerCode'][$i];
		$setting->providerName 	= $_POST['providerName'][$i];
		$setting->providerStatus = $_POST['providerStatus'][$i];
		$setting->update_provider();
	}
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data Provider successfully updated.","field"=>"");
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
}

if(isset($_POST['addNewAPK']) || isset($_POST['editAPK'])){
	if(isset($_POST['addNewAPK'])){ $act = "add";}
	if(isset($_POST['editAPK'])){ $act = "edit";}
	//p($_POST);p($_FILES);exit;
	$setting->apkName = security($_POST['apkName']);
	$setting->apkVersion = security($_POST['apkVersion']);
	$setting->apkDesc = security($_POST['apkDesc']);
	$setting->apkKey = "";
	$setting->apkStatus = intval($_POST['apkStatus']);
	$setting->apkStatusLock = intval($_POST['apkStatusLock']);
	
	if(isset($_FILES['apkImage'])){ 
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$arrExt 			= explode(".", $_FILES["apkImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$setting->apkImage	= uniqid().time().".".$imgExt;
		
		if($_FILES['apkImage']['name']!=""){
			move_uploaded_file($_FILES['apkImage']['tmp_name'], $upload_directory.$setting->apkImage);
		}
		else{
			if($act=="edit"){
				$setting->apkImage = $data['apk_image'];
			}
		}
	}

	if(isset($_FILES['apkFile'])){ 
		$upload_directory 	= "media/document/";
		$allowedExt 		= array("apk");
		$arrExt 			= explode(".", $_FILES["apkFile"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$setting->apkFile	= generate_alias($setting->apkName).".".$imgExt;
		$setting->apkSize	= get_size($_FILES["apkFile"]["size"]);
		move_uploaded_file($_FILES['apkFile']['tmp_name'], $upload_directory.$setting->apkFile);
		
		if($act=="add"){			
			$setting->apkId = $setting->insert_apk();
			$apkFile = MEDIA_DOCUMENT_PATH."/".$setting->apkFile;
			$setting->apkKey = md5_file($apkFile);
			$setting->update_apk("byField","apk_key",$setting->apkKey);			
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Upload New APK Successfully.","field"=>"");
		}
		if($act=="edit"){
			$setting->update_apk();
			$apkFile = MEDIA_DOCUMENT_PATH."/".$setting->apkFile;
			$setting->apkKey = md5_file($apkFile);
			$setting->update_apk("byField","apk_key",$setting->apkKey);
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"APK Successfully Updated.","field"=>"");
		}
		header("Location:".SITE_HOST."/setting/apk");
		exit;

	}
	else{
		if($act=="add"){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Error upload file.","field"=>"");
		}
		if($act=="edit"){
			$setting->apkFile = $data['apk_file'];
			$setting->apkSize = $data['apk_size'];
			$setting->update_apk();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"APK Successfully Updated.","field"=>"");
			header("Location:".SITE_HOST."/setting/apk");
			exit;
		}
	}
	
}



if(isset($_POST['saveBank'])){
	for($i=0;$i<count($_POST['bankId']);$i++){
		$setting->bankId 		= $_POST['bankId'][$i];
		$setting->bankName 		= security($_POST['bankName'][$i]);
		$setting->bankAccount 	= security($_POST['bankAccount'][$i]);
		$setting->bankNumber	= security($_POST['bankNumber'][$i]);
		$setting->bankBranch	= security($_POST['bankBranch'][$i]);
		$setting->bankStatus 	= security($_POST['bankStatus'][$i]);
		$setting->bankOrder 	= security($_POST['bankOrder'][$i]);
		$setting->bankImage 	= security($_POST['bankImage'][$i]);
		
		$setting->update_bank();
	}
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>$pageTitle." telah diperbarui.","field"=>"");
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
}


?>