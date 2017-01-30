<?php 
if($_SESSION['Admine']['LevelID']>2){ header("Location:".SITE_HOST);exit;}
if($this->actionPage==""){
	header("Location:".SITE_HOST."/statistic/sales");
	exit;
}
/*elseif($this->actionPage=="aplikasimu"){
	if(!isset($_SESSION['yearSales'])){
		$_SESSION['yearSales'] = date('Y');
	}
	if(isset($_POST['yearSales'])){
		$_SESSION['yearSales'] = $_POST['selectYearSales'];
		header("Location:".$_SERVER['HTTP_REFERER']);exit;
	}
	$firstOrder = $transaction->get_first_order();
	$dataSales  = $transaction->select_sales("monthlyInYear",$_SESSION['yearSales']);
	$totalSales = $transaction->select_sales("yearly",$_SESSION['yearSales']);
	
	$pageName = "statistic-aplikasimu";
	$pageTitle = "Statistics : AplikasiMU Sales";
}*/
elseif($this->actionPage=="sales"){
	if(!isset($_SESSION['yearSales'])){
		$_SESSION['yearSales'] = date('Y');
	}
	if(isset($_POST['yearSales'])){
		$_SESSION['yearSales'] = $_POST['selectYearSales'];
		header("Location:".$_SERVER['HTTP_REFERER']);exit;
	}
	$firstOrder = $transaction->get_first_order();
	$dataSales  = $transaction->select_sales("monthlyInYear",$_SESSION['yearSales']);
	$totalSales = $transaction->select_sales("totalSales");
	
	$pageName = "statistic-sales";
	$pageTitle = "Statistics : Sales";
}
elseif($this->actionPage=="subscribe"){
	if(!isset($_SESSION['yearSales'])){
		$_SESSION['yearSales'] = date('Y');
	}
	if(isset($_POST['yearSales'])){
		$_SESSION['yearSales'] = $_POST['selectYearSales'];
		header("Location:".$_SERVER['HTTP_REFERER']);exit;
	}
	$firstOrder = $transaction->get_first_order();
	$dataSales  = $transaction->select_subscribe("monthlyInYear",$_SESSION['yearSales']);
	$totalSales = $transaction->select_subscribe("yearly",$_SESSION['yearSales']);
	
	$pageName = "statistic-subscribe";
	$pageTitle = "Statistics : Subscribe";
}
else{
	header("Location:".SITE_HOST."/statistic/sales");
	exit;
}



?>