<?php 
$PageSize = 25;
$PageDisplay = 9;
require_once(FUNCTION_PATH . "/function_paging.php");
$product->beginRec = $beginRec;
$product->endRec = $intPageSize;
$product->sectionId = $detailSection['section_id'];
$product->userId = $_SESSION['Admine']['ID'];
$category->sectionId = $product->sectionId;
$iconSection = get_icon_section($this->thisPage);
define("LINK_LIST",ADMIN_HOST."/".$sectionAlias);
define("LINK_ADD",ADMIN_HOST."/".$sectionAlias."/add");
define("LINK_EDIT",ADMIN_HOST."/".$sectionAlias."/edit");
define("LINK_DETAIL",ADMIN_HOST."/".$sectionAlias."/detail");
$sectionName = "TokoMU";
$dataCat = $category->select_category("",$category->sectionId);
$dataVendor = $product->select_vendor();

if($this->subActionPage=="" || $this->subActionPage=="page"){
	$pageName = "product";
	$pageTitle = "Daftar Produk ".$sectionName;
	$dataProduct = $product->select_product("",$product->sectionId);
	$countProduct = $product->select_product("count",$product->sectionId);
	pagingShow($countProduct,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
}
elseif($this->subActionPage=="add"){
	$pageName = "product-".$this->subActionPage;
	$pageTitle = "Tambah Produk ".$sectionName;
}
elseif($this->subActionPage=="edit"){
	$pageName = "product-".$this->subActionPage;
	$pageTitle = "Edit Produk ".$sectionName;
	$product->productId = $this->dataPage;
	$detailProduct = $product->select_product("byId",$product->sectionId);
	$productImage = $media->get_primary_image($product->sectionId,$product->productId);
}
elseif($this->subActionPage=="detail"){
	$pageName = "product-".$this->subActionPage;
	$pageTitle = "Detail Produk ".$sectionName;
	$product->productId = $this->dataPage;
	$detailProduct = $product->select_product("byId",$product->sectionId);
	//$productTags = explode(",",$detailProduct['product_tags']);
	$productImage = $media->get_primary_image($product->sectionId,$product->productId,"imgDetail");
	
	$media->sectionId = $product->sectionId;
	$media->dataId = $product->productId;
	$dataImage = $media->select_media("","image");
	
}
elseif($this->subActionPage=="category"){
	$pageName = "category";
	$pageTitle = "Kategori ".$sectionName;
	$category->sectionId = $product->sectionId;
}
elseif($this->subActionPage=="vendor"){
	if($this->dataPage=="" || $this->dataPage=="page"){
		$pageName = "product-vendor";
		$pageTitle = "Vendor ".$sectionName;
		$dataVendor = $product->select_vendor();
		$countVendor = $product->select_vendor("count");
		pagingShow($countVendor,$pgtogo,$intPageNumber, $intPageSize, $intPageDisplay);
	}
	elseif($this->dataPage=="add"){
		$pageName = "product-vendor-add";
		$pageTitle = "Vendor ".$sectionName;
	}
	elseif($this->dataPage=="edit"){
		$pageName = "product-vendor-edit";
		$pageTitle = "Vendor ".$sectionName;
		$product->vendorId = $this->subDataPage;
		$detailVendor = $product->select_vendor("byId");
	}
	elseif($this->dataPage=="detail"){
		$pageName = "product-vendor-detail";
		$pageTitle = "Detail Vendor ".$sectionName;
		$product->vendorId = $this->subDataPage;
		$detailVendor = $product->select_vendor("byId");
	}
	else{
		header("Location:".ADMIN_HOST."/".$this->actionPage."/".$this->subActionPage);
		exit;
	}
}
elseif($this->subActionPage=="shipping"){
	if($this->dataPage==""){
		$pageName = "product-shipping";
		$pageTitle = "Daftar Expedisi";
		$dataExpedisi = $product->select_shipping_expedisi("active");
	}
	elseif($this->dataPage=="add"){
		$pageName = "product-shipping-add";
		$pageTitle = "Tambah Expedisi";
	}
	elseif($this->dataPage=="edit"){
		$pageName = "product-shipping-edit";
		$pageTitle = "Edit Expedisi";
		$product->expedisiId = intval($this->subDataPage);
		$detailExpedisi = $product->select_shipping_expedisi("byId");
	}
	elseif($this->dataPage=="detail"){
		$product->expedisiId = intval($this->subDataPage);
		$detailExpedisi = $product->select_shipping_expedisi("byId");
		$dataShipping = $product->select_shipping_price();//p($dataShipping);exit;
		$pageName = "product-shipping-detail";
		$pageTitle = "Tarif Harga ".$dataShipping[0]['expedisi_name'];
	}
	elseif($this->dataPage=="add"){
		
	}
}
else{
	$pageName = "404";
	$pageTitle = $sectionName;
}


if(isset($_POST['addVendor']) || isset($_POST['editVendor'])){
	if(isset($_POST['addVendor'])){ $act = "add";}
	if(isset($_POST['editVendor'])){ $act = "edit";}
	
	$product->vendorName 	= security($_POST['vendorName']); 
    $product->vendorCp 		= security($_POST['vendorCp']); 
    $product->vendorEmail 	= security($_POST['vendorEmail']); 
    $product->vendorPhone 	= security($_POST['vendorPhone']); 
	$product->vendorAddress = security($_POST['vendorAddress']); 
    $product->vendorWebsite	= security($_POST['vendorWebsite']); 
    $product->vendorDesc 	= security($_POST['vendorDesc']); 
    $product->vendorStatus 	= security($_POST['vendorStatus']); 
	
	if($product->vendorName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi nama Vendor.");
	}
	elseif($product->vendorCp==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi Contact Person Vendor.");
	}
	elseif($product->vendorEmail==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi email Vendor.");
	}
	elseif($product->vendorPhone==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi Telp / HP Vendor.");
	}
	else{
		if($act=="add"){
			$product->insert_vendor();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penambahan data vendor <strong>".$product->vendorName."</strong> berhasil.");
			header("Location:".LINK_LIST."/vendor");
			exit;
		}
		
		if($act=="edit"){
			$product->update_vendor();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data vendor <strong>".$product->vendorName."</strong> telah diperbarui.");
			header("Location:".LINK_LIST."/vendor");
			exit;
		}
	}
	
}

if(isset($_POST['delVendor'])){
	$product->vendorId = $_POST['vendorIdDel'];
	$product->vendorName = $_POST['vendorNameDel'];
	$product->delete_vendor();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data vendor <strong>".$product->vendorName."</strong> telah dihapus.");
	header("Location:".LINK_LIST."/vendor");
	exit;
}




if(isset($_POST['addProduct'])){
	$product->catId 			= security($_POST['productCat']);
	$product->vendorId 			= security($_POST['productVendor']);
    $product->productName 		= security($_POST['productName']);
	$product->productAlias 		= generate_alias($product->productName);
    $product->productDesc 		= security($_POST['productDesc']);
	$product->productStatus 	= security($_POST['productStatus']);
	$product->productCode 		= security($_POST['productCode']);
	$product->productPrice 		= security(str_replace(".","",$_POST['productPrice']));
	$product->productPriceOld 	= security(str_replace(".","",$_POST['productPriceOld']));
	$product->productDiscount 	= security($_POST['productDiscount']);
	$product->productLabel	 	= security($_POST['productLabel']);
	//$product->productStockReady	= security($_POST['productStockReady']);
	//$product->productOption		= intval($_POST['productOption']);
	$product->productStockReady = "ready";
	$product->productOption 	= "0";
	//$product->productTags = security($_POST['productTags']);
    //$product->productPublishDate = $_POST['publishDate'];
    $notifType = security($_POST['notifType']);
    $notifText = security($_POST['notifText']);	
	$product->productHits = 0;
	
	if($product->productName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan nama produk.");
	}
	elseif($product->productCode==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan kode produk.");
	}
	elseif($product->productDesc==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan keterangan produk.");
	}
	elseif($product->productPrice==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan harga produk.");
	}
	
	if(isset($_FILES['productImage']) && $_FILES['productImage']['name']!=""){
		$media->sectionId = $product->sectionId;
		$media->mediaType 	= "image";
		$media->mediaStatus = "1";
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$media->mediaName 	= $product->productName;
		$media->mediaAlias 	= generate_alias($media->mediaName);
		$media->mediaDesc 	= "";
		$media->mediaSize	= get_size($_FILES['productImage']['size']);
		$media->mediaPrimary= "1";
		$arrExt 			= explode(".", $_FILES["productImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$media->mediaValue 	= strtolower($detailSection['section_name'])."-".time().".".$imgExt;
		
		if($_FILES['productImage']['size'] > 8000000){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Maksimal File Gambar 8 MB.");
		}
		if(!in_array($_FILES["productImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Format gambar yang diijinkan .jpg, .png dan .gif");
		}
	}
	
	if($_SESSION['TxtMsg']['status']!="0"){
		$product->insert_product();
		if(isset($_FILES['productImage']) && $_FILES['productImage']['name']!=""){
			$media->dataId = $product->lastInsertId;
			$media->insert_media();
			move_uploaded_file($_FILES['productImage']['tmp_name'], $upload_directory.$media->mediaValue);		
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penambahan Data <strong>\"".$product->productName."\"</strong> berhasil.");
		header("Location:".LINK_DETAIL."/".$product->lastInsertId);
		exit;
	}
}

if(isset($_POST['editProduct'])){
	if(isset($_POST['addProduct'])){ $act="add";}
	if(isset($_POST['editProduct'])){ $act="edit";}
	
	$product->catId = security($_POST['productCat']);
    $product->productName = security($_POST['productName']);
	$product->productAlias = generate_alias($product->productName);
    $product->productDesc = security($_POST['productDesc']);
	$product->productStatus = security($_POST['productStatus']);
	$product->productTags = security($_POST['productTags']);
    $product->productPublishDate = $_POST['publishDate'];
    $notifType = security($_POST['notifType']);
    $notifText = security($_POST['notifText']);	
	$product->productHits = 0;
	$media->mediaValue = $productImage['media_value'];
	
	if($product->productName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan nama produk.");
	}
	elseif($product->productCode==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan kode produk.");
	}
	elseif($product->productDesc==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan keterangan produk.");
	}
	elseif($product->productPrice==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan masukkan harga produk.");
	}
	
	if(isset($_FILES['productImage']) && $_FILES['productImage']['name']!=""){
		$media->sectionId 	= $product->sectionId;
		$media->mediaType 	= "image";
		$media->mediaStatus = "1";
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$media->mediaName 	= $product->productName;
		$media->mediaAlias 	= generate_alias($media->mediaName);
		$media->mediaDesc 	= "";
		$media->mediaSize	= get_size($_FILES['productImage']['size']);
		$media->mediaPrimary= "1";
		$arrExt 			= explode(".", $_FILES["productImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$media->mediaValue 	= strtolower($detailSection['section_name'])."-".time().".".$imgExt;
		
		if($_FILES['productImage']['size'] > 8000000){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Maksimal File Gambar 8 MB.");
		}
		if(!in_array($_FILES["productImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Format gambar yang diijinkan .jpg, .png dan .gif");
		}
	}

	if($_SESSION['TxtMsg']['status']!="0"){
		$product->update_product();
		if(isset($_FILES['productImage']) && $_FILES['productImage']['name']!="" ){
			$media->dataId = $detailProduct['product_id'];
			if(!isset($productImage['media_id'])){
				$media->insert_media();
			}
			else{
				$media->mediaId = $productImage['media_id'];
				$media->mediaDesc = $productImage['media_desc'];
				$media->update_media();
				if($productImage['media_value']!="" && $productImage['media_value']!="noimage.png"){
					if(file_exists(MEDIA_IMAGE_PATH."/".$productImage['media_value']))
						unlink(MEDIA_IMAGE_PATH."/".$productImage['media_value']);
				}
			}
			move_uploaded_file($_FILES['productImage']['tmp_name'], $upload_directory.$media->mediaValue);
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data <strong>\"".$product->productName."\"</strong> berhasil diperbarui.");
		header("Location:".LINK_DETAIL."/".$detailProduct['product_id']);
		exit;
	}
}


if(isset($_POST['delProduct'])){
	$product->productId = $_POST['productIdDel'];
	$product->productName = $_POST['productNameDel'];
	$product->delete_product();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data <strong>\"".$product->productName."\"</strong> berhasil dihapus.");
	header("Location:".LINK_LIST); 
	exit;
}


//MEDIA GALLERY

if(isset($_POST['editMediaImage'])){
	$media->mediaId = intval($_POST['mediaIdEdit']);
	$detailImage = $media->select_media("byId");
	$mediaName = intval($_POST['mediaNameEdit']);
	if($media->mediaName==""){
		$media->mediaName = $detailImage['media_name'];
	}	
	$media->sectionId = $detailImage['section_id'];
	$media->dataId = $detailImage['data_id'];
	$media->mediaDesc = security(nl2br($_POST['mediaDesc']));
	$media->mediaName = security($_POST['mediaName']);
	$media->mediaAlias = generate_alias($media->mediaName);
	$media->mediaType = "image";
	$media->mediaValue = $detailImage['media_value'];
	$media->mediaSize = $detailImage['media_size'];
	$media->mediaStatus = $detailImage['media_status'];
	
	if(isset($_POST['mediaPrimary']) && $_POST['mediaPrimary']=="1"){
		$media->update_media("flushPrimary");
		$media->mediaPrimary = $_POST['mediaPrimary'];
	}
	else{
		$media->mediaPrimary = $datailImage['media_primary'];
	}
	
	$media->update_media();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Gambar <strong>\"".$media->mediaName."\"</strong> berhasil diperbarui.");
	header("Location:".$_SERVER['HTTP_REFERER']); 
	exit;	
}

if(isset($_POST['delMediaImage'])){
	$media->mediaId = $_POST['mediaIdDel'];
	$media->mediaName = $_POST['mediaNameDel'];
	$detailImage = $media->select_media("byId");
	$media->delete_media();
	if($detailImage['media_primary']=="1"){
		$media->sectionId = $product->sectionId;
		$media->dataId = $product->productId;
		$lastImage = $media->get_last_image();
		$media->mediaId = $lastImage['media_id'];
		$media->update_media("byField","media_primary","1");
	}
	
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Gambar <strong>\"".$media->mediaName."\"</strong> telah dihapus.");
	unlink(MEDIA_IMAGE_PATH."/".$media->mediaName);
	header("Location:".$_SERVER['HTTP_REFERER']); 
	exit;
}


//SHIPPING EXPEDISI


if(isset($_POST['addExpedisi'])){
	$product->expedisiName = security($_POST['expedisiName']);
	$product->expedisiPhone = security($_POST['expedisiPhone']);
	$product->expedisiStatus = security($_POST['expedisiStatus']);
	
	if($product->expedisiName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi nama expedisi.");
	}
	elseif($product->expedisiPhone==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi no.telepon expedisi.");
	}
	
	if(isset($_FILES['expedisiImage']) && $_FILES['expedisiImage']['name']!=""){
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$arrExt 			= explode(".", $_FILES["expedisiImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$product->expedisiImage	= "expedisi-".time().".".$imgExt;
		
		if($_FILES['expedisiImage']['size'] > 8000000){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Maksimal File Gambar 8 MB.");
		}
		if(!in_array($_FILES["expedisiImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Format gambar yang diijinkan .jpg, .png dan .gif");
		}
	}
	
	if($_SESSION['TxtMsg']['status']!="0"){
		$product->insert_shipping_expedisi();
		if(isset($_FILES['expedisiImage']) && $_FILES['expedisiImage']['name']!=""){
			move_uploaded_file($_FILES['expedisiImage']['tmp_name'], $upload_directory.$product->expedisiImage);		
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Penambahan Data Expedisi <strong>\"".$product->expedisiName."\"</strong> berhasil.");
		header("Location:".ADMIN_HOST."/".$this->actionPage."/".$this->subActionPage."/detail/".$product->lastInsertId);
		exit;
	}
}


if(isset($_POST['editExpedisi'])){
	$product->expedisiName = security($_POST['expedisiName']);
	$product->expedisiPhone = security($_POST['expedisiPhone']);
	$product->expedisiStatus = security($_POST['expedisiStatus']);
	$product->expedisiImage = $detailExpedisi['expedisi_image'];
	if($product->expedisiName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi nama expedisi.");
	}
	elseif($product->expedisiPhone==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Silahkan isi no.telepon expedisi.");
	}
	
	if(isset($_FILES['expedisiImage']) && $_FILES['expedisiImage']['name']!=""){
		$upload_directory 	= "media/image/";
		$allowedExt 		= array("png","jpg","jpeg","gif");
		$mimeType			= array("image/jpg","image/png","image/gif");
		$arrExt 			= explode(".", $_FILES["expedisiImage"]["name"]);
		$imgExt 			= strtolower(end($arrExt));
		$product->expedisiImage	= "expedisi-".time().".".$imgExt;
		
		if($_FILES['expedisiImage']['size'] > 8000000){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Maksimal File Gambar 8 MB.");
		}
		if(!in_array($_FILES["expedisiImage"]["type"],$mimeType) && !in_array($imgExt,$allowedExt)){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Format gambar yang diijinkan .jpg, .png dan .gif");
		}
	}
	
	if($_SESSION['TxtMsg']['status']!="0"){
		$product->update_shipping_expedisi();
		if(isset($_FILES['expedisiImage']) && $_FILES['expedisiImage']['name']!=""){
			move_uploaded_file($_FILES['expedisiImage']['tmp_name'], SITE_PATH."/".$upload_directory.$product->expedisiImage);
		}
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Expedisi <strong>\"".$product->expedisiName."\"</strong> berhasil diperharui.");
		header("Location:".ADMIN_HOST."/".$this->actionPage."/shipping");
		exit;
	}
}


if(isset($_POST['delExpedisi'])){
	$product->expedisiId = $_POST['expedisiIdDel'];
	$product->expedisiName = $_POST['expedisiNameDel'];
	$product->delete_shipping_expedisi();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Expedisi <strong>\"".$product->expedisiName."\"</strong> telah dihapus.");
	header("Location:".ADMIN_HOST."/tokomu/shipping");
	exit;
}



//SHIPPING PRICE



if(isset($_POST['delShipping'])){
	$product->shippingId = $_POST['shippingIdDel'];
	$shippingName = $_POST['shippingNameDel'];
	$product->delete_shipping_price();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Shipping <strong>\"".$shippingName."\"</strong> telah dihapus.");
	header("Location:".$_SERVER['HTTP_REFERER']);
	exit;
}

//CATEGORY

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