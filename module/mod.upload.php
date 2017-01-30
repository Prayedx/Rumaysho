<?php
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

require_once("../config.php");
require_once("../function/function_general.php");
require_once("../class/mysql.class.php");
require_once("../class/media.class.php");
$media = new Media();

if (!empty($_FILES)) {
	$fName = $_GET['fName'];
	$arrExt = explode(".",strtolower($_FILES['file']['name'][0]));
	$imgExt = end($arrExt);
    $tempFile = $fName."-".time().".".$imgExt;
    $targetFile =  MEDIA_IMAGE_PATH."/". $tempFile;
	
	$media->sectionId = $_GET['sectionId'];
	$media->dataId = $_GET['dataId'];
	$media->mediaType = "image";
	$media->mediaName = $tempFile;
	$media->mediaAlias = generate_alias($media->mediaName);
	$media->mediaDesc = "";
	$media->mediaValue = $media->mediaName;
	$media->mediaSize = get_size($_FILES['file']['size'][0]);
	
	$countGallery = $media->select_media("count","image");
	$media->mediaPrimary = (intval($countGallery)==0) ? "1" : "0";
	$media->insert_media();
    move_uploaded_file($_FILES['file']['tmp_name'][0],$targetFile);
}

?>