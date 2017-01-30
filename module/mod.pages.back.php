<?php 
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
	$pageName = "pages";
	$pageTitle = "Arsip Halaman Statik";
	$dataContent = $content->select_content("",$content->sectionId);
	$countContent = $content->select_content("count",$content->sectionId);
	pagingShow($countContent,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
}
elseif($this->subActionPage=="add"){
	$pageName = "pages-".$this->subActionPage;
	$pageTitle = "Tambah ".$sectionName;
}
elseif($this->subActionPage=="edit"){
	$pageName = "pages-".$this->subActionPage;
	$pageTitle = "Edit ".$sectionName;
	$content->contentId = $this->dataPage;
	$detailContent = $content->select_content("byId",$content->sectionId);
	$contentImage = $media->get_primary_image($content->sectionId,$content->contentId);
}
elseif($this->subActionPage=="detail"){
	$pageName = "pages-".$this->subActionPage;
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


if(isset($_POST['addContent'])){
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
		$media->mediaValue 	= strtolower($detailSection['section_name'])."-".time().".".$imgExt;
		
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
		$media->mediaValue 	= strtolower($detailSection['section_name'])."-".time().".".$imgExt;
		
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


?>