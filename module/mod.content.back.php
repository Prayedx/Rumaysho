<?php 


$sectionModule = strtolower(className($detailSection['section_name']));
if(file_exists(MODULE_PATH."/".MODULE.$sectionModule.AREA)){
	require_once(MODULE_PATH."/".MODULE.$sectionModule.AREA);
}
else{
	$PageSize = 25;
	$PageDisplay = 9;
	require_once(FUNCTION_PATH . "/function_paging.php");
	$content->beginRec = $beginRec;
	$content->endRec = $intPageSize;
	$content->sectionId = $detailSection['section_id'];
	$content->userId = $_SESSION['Admine']['ID'];
	$category->sectionId = $content->sectionId;
	$iconSection = get_icon_section($this->thisPage);
	define("LINK_LIST",ADMIN_HOST."/".$sectionAlias);
	define("LINK_ADD",ADMIN_HOST."/".$sectionAlias."/add");
	define("LINK_EDIT",ADMIN_HOST."/".$sectionAlias."/edit");
	define("LINK_DETAIL",ADMIN_HOST."/".$sectionAlias."/detail");

	$dataCat = $category->select_category("",$category->sectionId);
	//p($dataCat);exit;

	if($this->subActionPage=="" || $this->subActionPage=="page"){
		$pageName = CONTENT;
		$pageTitle = "Arsip ".$sectionName;
		$dataContent = $content->select_content("",$content->sectionId);
		$countContent = $content->select_content("count",$content->sectionId);
		pagingShow($countContent,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
	}
	elseif($category->is_category($category->sectionId,$this->subActionPage)){
		$category->catAlias = $this->subActionPage;
		$cat = $category->select_category("byAlias",$category->sectionId);
		$pageName = CONTENT;
		$pageTitle = "Arsip ".$sectionName. " - ".$cat['cat_name'];

		$cats = $cat['cat_id'];
		if($category->is_parent($cats)){
			$cats = $category->get_id_sub($cat['cat_id']);
		}

		$dataContent = $content->select_content("",$content->sectionId,$cats);//p($cats,1);
		$countContent = $content->select_content("count",$content->sectionId,$cats);
		pagingShow($countContent,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
	}
	elseif($this->subActionPage=="add"){
		$pageName = CONTENT."-".$this->subActionPage;
		$pageTitle = "Tambah ".$sectionName;
	}
	elseif($this->subActionPage=="edit"){
		$pageName = CONTENT."-".$this->subActionPage;
		$pageTitle = "Edit ".$sectionName;
		$content->contentId = $this->dataPage;
		$detailContent = $content->select_content("byId",$content->sectionId);
		$contentImage = $media->get_primary_image($content->sectionId,$content->contentId);
	}
	elseif($this->subActionPage=="detail"){
		$pageName = CONTENT."-".$this->subActionPage;
		$pageTitle = "Detail ".$sectionName;
		$content->contentId = $this->dataPage;
		$detailContent = $content->select_content("byId",$content->sectionId);
		$contentTags = explode(",",$detailContent['content_tags']);
		$contentImage = $media->get_primary_image($content->sectionId,$content->contentId,"imgDetail");
		
	}
	elseif($this->subActionPage=="category"){
		$pageName = "category";
		$pageTitle = "Kategori ".$sectionName;
		$category->sectionId = $content->sectionId;
	}
	else{
		$pageName = "404";
		$pageTitle = $sectionName;
	}
}

if(isset($_POST['addContent'])){
	$content->catId = security($_POST['contentCat']);
    $content->contentName = security($_POST['contentName']);
	$content->contentAlias = generate_alias($content->contentName);
    $content->contentDesc = security($_POST['contentDesc']);
	$content->contentStatus = security($_POST['contentStatus']);
	$content->contentTags = security($_POST['contentTags']);
    $content->contentPublishDate = $_POST['publishDate'];

	if($content->contentPublishDate == ""){
		$content->contentPublishDate = date("Y-m-d H:i:s");
	}

    $notifType = security($_POST['notifType']);
    $notifText = security($_POST['notifText']);	
	$content->contentHits = 0;
	
	if($content->contentName==""){
		$error['contentName'] = "Silahkan masukkan judul ".$detailSection['section_name'];
	}
	if($content->contentDesc==""){
		$error['contentDesc'] = "Silahkan masukkan keterangan ".$detailSection['section_name'];
	}
	
	if(isset($_FILES['contentImage']) && $_FILES['contentImage']['name']!=""){
		$media->sectionId = $content->sectionId;
		$media->mediaType 	= "image";
		$media->mediaStatus = "1";
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$media->mediaName 	= $content->contentName;
		$media->mediaAlias 	= generate_alias($media->mediaName);
		$media->mediaDesc 	= "";
		$media->mediaSize	= get_size($_FILES['contentImage']['size']);
		$media->mediaPrimary= "1";
		$arrExt 			= explode(".", $_FILES["contentImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$media->mediaValue 	= strtolower($detailSection['section_alias'])."-".time().".".$imgExt;
		
		if($_FILES['contentImage']['size'] > 8000000){
			$error['contentImage'] = "Maksimal File Gambar 8 MB.";
		}
		if(!in_array($_FILES["contentImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$error['contentImage'] = "Format gambar yang diijinkan .jpg, .png dan .gif";
		}
	}
	
	if(!isset($error)){
		$content->insert_content();
		if(isset($_FILES['contentImage'])){
			$media->dataId = $content->lastInsertId;
			$media->insert_media();
			move_uploaded_file($_FILES['contentImage']['tmp_name'], $upload_directory.$media->mediaValue);		
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penambahan Data <strong>\"".$content->contentName."\"</strong> berhasil.");
		header("Location:".LINK_DETAIL."/".$content->lastInsertId);
		exit;
	}
}

if(isset($_POST['editContent'])){
	$content->catId = security($_POST['contentCat']);
    $content->contentName = security($_POST['contentName']);
	$content->contentAlias = generate_alias($content->contentName);
    $content->contentDesc = security($_POST['contentDesc']);
	$content->contentStatus = security($_POST['contentStatus']);
	$content->contentTags = security($_POST['contentTags']);
    $content->contentPublishDate = $_POST['publishDate'];
    $notifType = security($_POST['notifType']);
    $notifText = security($_POST['notifText']);	
	$content->contentHits = 0;
	$media->mediaValue = $contentImage['media_value'];

	if($content->contentPublishDate == ""){
		$content->contentPublishDate = date("Y-m-d H:i:s");
	}else{
		$content->contentPublishDate = date_format(date_create_from_format("d M Y - h:i A",$content->contentPublishDate),'Y-m-d H:i:s');
	}

	if($content->contentName==""){
		$error['contentName'] = "Silahkan masukkan judul ".$detailSection['section_name'];
	}
	if($content->contentDesc==""){
		$error['contentDesc'] = "Silahkan masukkan keterangan ".$detailSection['section_name'];
	}
	
	if(isset($_FILES['contentImage']) && $_FILES['contentImage']['name']!=""){
		$media->sectionId 	= $content->sectionId;
		$media->mediaType 	= "image";
		$media->mediaStatus = "1";
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$media->mediaName 	= $content->contentName;
		$media->mediaAlias 	= generate_alias($media->mediaName);
		$media->mediaDesc 	= "";
		$media->mediaSize	= get_size($_FILES['contentImage']['size']);
		$media->mediaPrimary= "1";
		$arrExt 			= explode(".", $_FILES["contentImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$media->mediaValue 	= strtolower($detailSection['section_alias'])."-".time().".".$imgExt;
		
		if($_FILES['contentImage']['size'] > 8000000){
			$error['contentImage'] = "Maksimal File Gambar 8 MB.";
		}
		if(!in_array($_FILES["contentImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$error['contentImage'] = "Format gambar yang diijinkan .jpg, .png dan .gif";
		}
	}

	if(!isset($error)){
		$content->update_content();
		if(isset($_FILES['contentImage']) && $_FILES['contentImage']['name']!="" ){
			$media->dataId = $detailContent['content_id'];
			if(!isset($contentImage['media_id'])){
				$media->insert_media();
			}
			else{
				$media->mediaId = $contentImage['media_id'];
				$media->mediaDesc = $contentImage['media_desc'];
				$media->update_media();
				if($contentImage['media_value']!="" && $contentImage['media_value']!="noimage.png"){
					if(file_exists(MEDIA_IMAGE_PATH."/".$contentImage['media_value']))
						unlink(MEDIA_IMAGE_PATH."/".$contentImage['media_value']);
				}
			}
			move_uploaded_file($_FILES['contentImage']['tmp_name'], $upload_directory.$media->mediaValue);		
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data <strong>\"".$content->contentName."\"</strong> berhasil diperbarui.");
		header("Location:".LINK_DETAIL."/".$detailContent['content_id']);
		exit;
	}
}


if(isset($_POST['delContent'])){
	$content->contentId = $_POST['contentIdDel'];
	$content->contentName = $_POST['contentNameDel'];
	$content->delete_content();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data <strong>\"".$content->contentName."\"</strong> berhasil dihapus.");
	header("Location:".LINK_LIST); 
	exit;
}


if(isset($_POST['addCat'])){
	$category->form_add_category();
}

if(isset($_POST['editCat'])){
	$category->form_edit_category();
}

if(isset($_POST['updateCatOrder'])){
	$category->form_update_order_category();
}

if(isset($_POST['delCat'])){
	$category->form_delete_category();
}



//
//if(isset($_POST['addInfoMU']) || isset($_POST['editInfoMU'])){
//	if(isset($_POST['addInfoMU'])){ $act="add";}
//	if(isset($_POST['editInfoMU'])){ $act="edit";}
//
//	$infomu->catId = $thisCat['cat_root'];
//	$infomu->infomuName = security($_POST['infomuName']);
//	$infomu->infomuAlias = generate_alias($infomu->infomuName);
//	$infomu->infomuDesc = security($_POST['infomuDesc']);
//	$infomu->infomuStatus = $_POST['infomuStatus'];
//	$infomu->infomuAuthor = security($_POST['infomuAuthor']);
//
//
//
//	if(isset($_FILES['infomuImage'])){
//		$media->sectionId 	= INFOMU_ID;
//		$media->albumName 	= $infomu->infomuName;
//		$media->albumAlias 	= $infomu->infomuAlias;
//		$media->mediaDesc 	= "";
//		$media->mediaType 	= "image";
//		$media->mediaStatus = "1";
//		$upload_directory 	= "media/image/";
//		$allowedExt 		= array("png","jpg","jpeg","gif");
//		$arrExt 			= explode(".", $_FILES["infomuImage"]["name"]);
//		$imgExt 			= strtolower(end($arrExt));
//		$media->mediaValue 	= uniqid().".".$imgExt;
//		$media->mediaName 	= $infomu->infomuAlias;
//		$media->mediaAlias 	= generate_alias($media->mediaName);
//		$media->mediaPrimary= "1";
//
//		if($_FILES['infomuImage']['name']!=""){
//			//move_uploaded_file($_FILES['infomuImage']['tmp_name'], $upload_directory.$media->mediaValue);
//			resize_image("infomuImage",500,335,$media->mediaValue);
//			if($act=="add"){
//				$media->albumId = $infomu->albumId	= $media->insert_media_album();
//				$media->insert_media();
//			}
//			if($act=="edit"){
//				$media->albumId = $data['album_id'];
//				$detailMedia = $media->select_media("byAlbumId","image");
//				$media->mediaId = $detailMedia[0]['media_id'];
//				$media->update_media("byField","media_value",$media->mediaValue);
//			}
//		}
//	}
//
//	if(isset($_POST['catInfoMU'])){
//		$infomu->catId = $_POST['catInfoMU'];
//	}
//
//	if($infomu->infomuName=="" || $infomu->infomuDesc==""){
//		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Please completed the form below.");
//	}
//	elseif($act=="add" && $infomu->is_data_exists($infomu->infomuName,$infomu->catId)){
//		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Content <strong>\"".$infomu->infomuName."</strong>\" is already exists.");
//	}
//	else{
//		if($act=="add"){
//			$lastInsertId = $infomu->insert_infomu();
//
//			if(isset($_POST['notifHandle'])){
//				$notif->sectionId = INFOMU_ID;
//				$notif->contentId = $lastInsertId;
//				$notif->notifHandle = $_POST['notifHandle'];
//				$notif->notifTitle = "InfoMU - ".ucwords(str_replace("-"," ",$this->actionPage));
//				$notif->notifText = $_POST['notifText'];
//				$notif->notifAuthor = $_SESSION['Admine']['Name'];
//				$notif->notifStatus = "0";
//				if($notif->notifHandle!="0"){
//					if($notif->notifText==""){
//						$notif->notifText = $infomu->infomuName;
//					}
//					$notif->insert_notif();
//					$dataGcm = array( 'message' => array(
//													"title"=>$notif->notifTitle,
//													"text"=>$notif->notifText,
//													"cat"=>"null",
//													"type"=>$notif->notifHandle,
//													"contentId"=>$notif->contentId,
//													"section"=>"infomu"
//													) );
//					//$dataCustomer = $customer->select_registered_customer();
//					$dataSlice = array();
//					//$dataSlice = array_chunk($dataCustomer,1000);
//					$dataSlice[0] = array("APA91bFbwaUNJAOB5lKiPLFSfLaynTbTNougUq-Ugjjsv-Z5zyK8v-DToONHSQVNXbrNAqVtmiwAYI-rkFyLupLXe7-U9-EDmzEnx6P_w-Qmni4dFVv6p0_E6-sSgLYuIIOVafR5prtd0hOk2S4a01UHCJ01QDgRIA",					"APA91bFVvLJrMgbrUiFUCKFusDQI5tkGsyX_EMxluid8sLhUoVPORJz8GJOnWZFLUttqlwfdqoBfT7Q3rAjTuYZdFd977CIwLYT2ZokJo7B37_e2XfMftslyIgQzwDYtUZwHnLHtp1OKarLSCrVuOKGsijdG6qMAqw",	"APA91bGmK9U3wrhWrueDyMBN2Ro8B7AyYqPOKYiPvSbjC7Db6Pd0oq6TOaEnFET8n4HjZgE2uieFKjqkVBWlcNIh2i2_XQWAe0zVVTSEgSCHw_romN2eDEqQSZyo1HCZKBglA6SrdJrOMo38G4d9-niU4AM9yw2y6A","APA91bHdDduWZz-t6jMBBwsHtHHtmEn99NjvVKTsTsAjKGLsQ_Xs0z2Z7ojXRaBwVOR-ZiHuDr8HPzIhpEKc7kNugu0BQs2jlofkU7exSXj91SiDf0eEecLI17n4OvIX6xgUf1SBttVxn4NH_AKAfW3YhHwB3vZ-5g","APA91bFOPcqy3JVur6CGPrsr_VwX0Ua94UKqdPsGzoXXP5CUXo2txEFqB4JInsqH2-FkMYhFS48DEQjd5Ea4mgAWYvPOn2AhNsyBgIXTZf1nOt0LX85AxYLOL3bX1aqAa-EmC4bktYhyn5_xKX3bqA9AxQ3NBX8fDA","APA91bEHxgLOOTAS224hLoMWk8Qe4HIVBUqK0-pjUgN7tJdEY6X82twPctXVQ4we5fIM1wESjQwjgALhLekQB1zakIUJCeJt6-Qq-hBupmzs_trqvO4PBhXKJELUnmRb4NjwtnWdE9ScWiNYEi3T70XxIbXSOdEeSQ","APA91bFbwaUNJAOB5lKiPLFSfLaynTbTNougUq-Ugjjsv-Z5zyK8v-DToONHSQVNXbrNAqVtmiwAYI-rkFyLupLXe7-U9-EDmzEnx6P_w-Qmni4dFVv6p0_E6-sSgLYuIIOVafR5prtd0hOk2S4a01UHCJ01QDgRIA","APA91bGzCRZg10HpgToUoWy9P5pxSKdFScox2HhkE1hQtiHWtNnai0SmkqnYCw05tFP56ES275BsvoDqNeHNsUX9v7IkmqjwAXuTwZD1C7wJ-M_6bkLa7FMQEFaKzdRlPOtrNXsMf0MRrt_G8oQxO4egUmdfRYMybg");
//
//
//					/*for($i=0;$i<$dataSlice;$i++){
//						$dataToSend = $dataSlice[$i];
//						$notif->sendGoogleCloudMessage($dataGcm, $dataToSend);
//					}*/
//
//				}
//			}
//
//			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"New ".ucwords($thisCat['cat_name'])." successfully added.");
//		}
//		if($act=="edit"){
//			$infomu->albumId = $data['album_id'];
//			$infomu->update_infomu();
//			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data ".ucwords($thisCat['cat_name'])." successfully updated.");
//		}
//		header("Location:".SITE_HOST."/infomu/".$thisCat['cat_alias']); exit;
//	}
//
//}

?>