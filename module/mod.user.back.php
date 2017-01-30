<?php
if(!isset($_SESSION['TxtMsg'])) $_SESSION['TxtMsg'] = array("status"=>"","text"=>"");
$iconSection = get_icon_section($this->thisPage);
if($this->subActionPage==""){
	$pageName = "user";
	$pageTitle = "Administrator List";
	$user->userLevelId = $_SESSION['Admine']['LevelID'];
	$dataUser = $user->select_user("");	
}
elseif($this->subActionPage=="logout"){
	unset($_SESSION['Admine']);
	header("Location:".ADMIN_HOST);
	exit;
}
elseif($this->subActionPage=="detail"){
	$user->userId = intval($this->dataPage);
	$detailUser = $user->select_user("byId");
	$pageName = "user-detail";
	$pageTitle = "Profil ".$detailUser['user_name'];
}
elseif($this->subActionPage=="add"){
	$pageName = "user-add";
	$pageTitle = "Tambah Administrator";
}
elseif($this->subActionPage=="edit"){
	$user->userId = intval($this->dataPage);
	$detailUser = $user->select_user("byId");
	$pageName = "user-edit";
	$pageTitle = "Edit Administrator ".$detailUser['user_name'];
}
else{
	header("Location:".ADMIN_HOST."/user");exit;
}

if(isset($_POST['addUserLevel'])){
	$user->userLevelName = security($_POST['userLevelName']);
	
	if($user->userLevelName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Anda Belum Mengisi Nama Level User.");
	}else{
		$isExists = $user->select_user_level("byName");
		if(count($isExists)>0){
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Nama Level User sudah terdaftar.");
		}else{
			$user->insert_user_level();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Level User baru berhasil ditambahkan.");
		}
	}
	header("Location:" . $_SERVER['HTTP_REFERER']);
    exit;
}

if(isset($_POST['addUser']) || isset($_POST['editUser'])){ 
	if(isset($_POST['addUser'])){ $act = "add";}
	if(isset($_POST['editUser'])){ $act = "edit";}
	
	$user->userName = security($_POST['userName']);
	$user->userUsername = $user->userEmail = trim($_POST['userEmail']);
	$user->userPassword = secure($_POST['userPassword']);
	$user->userStatus = security($_POST['userStatus']);
	$user->userLevelId = "2";
	$user->userPhoto = "nophoto.jpg";		
	
	if(isset($_FILES['userPhoto']) && $_FILES["userPhoto"]["name"]!=""){		
		if($_FILES['userPhoto']['size'] > 8000000){
			$errFileSize = "Maksimal file size 8 Mb.";
			$errorFlag = "1";
		}
		
		$upload_directory = "media/image/";
		$documentExt = array("jpg", "jpeg", "png", "gif");
		$arrExt = explode(".", $_FILES["userPhoto"]["name"]);
		$imgExt = end($arrExt);
		$user->userPhoto = "photo-".generate_alias($user->userName)."-".time().".".$imgExt;
		
		if ((($_FILES["userPhoto"]["type"] != "image/gif") 
			|| ($_FILES["userPhoto"]["type"] != "image/png")
			|| ($_FILES["userPhoto"]["type"] != "image/jpg")) 
			&& !in_array(strtolower($imgExt), $documentExt)) {
			$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Format file <strong>".$imgExt."</strong> tidak diijinkan.");
		}
		move_uploaded_file($_FILES['userPhoto']['tmp_name'], $upload_directory.$user->userPhoto);
	}
	
	if($user->userName==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Anda belum mengisi nama ".$detailUser['user_level_name'].".");
	}
	elseif($user->userEmail==""){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Anda belum mengisi email.");
	}
	elseif(count($user->select_user("byUsername"))>"0" && $act=="add"){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Email Anda sudah terdaftar.");
	}
	else{
		if($act=="add"){
			$lastUserId = $user->insert_user();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data ".$this->userName." berhasil ditambahkan.");
			header("Location:".ADMIN_HOST."/user");
			exit;
		}
		if($act=="edit"){
			if(trim($_POST['userPassword'])==""){
				$user->userPassword = $detailUser['user_password'];
			}
			$user->update_user();
			$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Data ".$detailUser['user_name']." berhasil diperbaharui.");
			header("Location:".ADMIN_HOST."/user");
			exit;
		}
	}
}

if(isset($_POST['delUser'])){
	$user->userId = $_POST['userIdDel'];
	$user->userName = $_POST['userNameDel'];
	$user->delete_user();
	$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Administrator <strong>".$user->userName."</strong> telah dihapus.");
	header("Location:".ADMIN_HOST."/user");
	exit;
}


if(isset($_POST['changePassword'])){
	
	if(trim($_POST['userPassword']=="")){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Password tidak boleh kosong.");
	}
	elseif(strlen(trim($_POST['userPassword']))<6){
		$_SESSION['TxtMsg'] = array("status"=>"0","text"=>"Password minimal 6 karakter.");
	}
	else{
		$user->userPassword = secure($_POST['userPassword']);
		$user->update_user("byField","user_password",$user->userPassword);
		$_SESSION['TxtMsg'] = array("status"=>"1","text"=>"Perubahan Password baru berhasil.");
	}
}

?>