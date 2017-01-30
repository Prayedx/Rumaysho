<?php
$PageSize = 50;
$PageDisplay = 9;
require_once(FUNCTION_PATH . "/function_paging.php");
$bisnismu->beginRec = $beginRec;
$bisnismu->endRec = $intPageSize;
$bisnismu->sectionId = $detailSection['section_id'];
$bisnismu->userId = $_SESSION['Admine']['ID'];
$category->sectionId = $bisnismu->sectionId;
$iconSection = get_icon_section($this->thisPage);
define("LINK_LIST", ADMIN_HOST . "/" . $sectionAlias);
define("LINK_ADD", ADMIN_HOST . "/" . $sectionAlias . "/add");
define("LINK_EDIT", ADMIN_HOST . "/" . $sectionAlias . "/edit");
define("LINK_DETAIL", ADMIN_HOST . "/" . $sectionAlias . "/detail");
require_once(CLASS_PATH . "/bimasakti.class.php");
$bimasakti = new Bimasakti();
require_once(CLASS_PATH . "/idbiller.class.php");
$idbiller = new IdBiller();

$sectionName = "PPOB";
$dataCatParent = $bisnismu->select_ppob_category("parent0", $bisnismu->sectionId);
$dataCat = $bisnismu->select_ppob_category("", $bisnismu->sectionId);
$dataVendor = $bisnismu->select_ppob_vendor();
$arrProductModel = array('pulsa', 'game', 'pln_pascabayar', 'pln_prabayar', 'pln_nontaglist', 'telkom', 'tv', 'multi_finance', 'pdam', 'pdam_surabaya', 'telepon_pascabayar', 'asuransi', 'kartu_kredit');
$totalDeposit = $customer->get_total_deposit();
$totalTrans = $bisnismu->get_total_ppob_transaction();
$currentDeposit = $totalDeposit - $totalTrans;
$currentTrans = $bisnismu->get_total_ppob_transaction(date('Y-m'));

if (!isset($_SERVER['HTTP_REFERER']))
    $_SERVER['HTTP_REFERER'] = "";

if ($this->subActionPage == "" || $this->subActionPage == "page") {
    if (isset($_SESSION['filterKey'])) {
        header("Location:" . ADMIN_HOST . "/bisnismu/filter/" . $_SESSION['filterKey'] . "/" . $_SESSION['filterCat'] . "/" . $_SESSION['filterVendor']);
        exit;
    }
    $pageName = "bisnismu";
    $pageTitle = "Daftar Produk " . $sectionName;
    $dataProduct = $bisnismu->select_ppob_product("");//p($dataProduct,1);
    $countProduct = $bisnismu->select_ppob_product("count");
    pagingShow($countProduct, $pgtogo, $intPageNumber, $intPageSize, $intPageDisplay);
} elseif ($this->subActionPage == "filter") {
    if (strpos($_SERVER['HTTP_REFERER'], "bisnismu") == 0 ||
        strpos($_SERVER['HTTP_REFERER'], "bisnismu/transaction") != 0
    ) {
        unset($_SESSION['filterKey']);
        unset($_SESSION['filterCat']);
        unset($_SESSION['filterVendor']);
        header("Location:" . ADMIN_HOST . "/bisnismu");
        exit;
    }
    if (!isset($_SESSION['filterKey'])) {
        header("Locatioin:" . ADMIN_HOST);
        exit;
    }
    $pageName = "bisnismu";
    $pageTitle = "Filter Produk " . $sectionName;

    $bisnismu->productName = $this->dataPage;
    $bisnismu->catId = $this->subDataPage;
    $bisnismu->vendorId = $this->subsubDataPage;

    if ($bisnismu->productName == "all")
        $bisnismu->productName = "";
    if ($bisnismu->catId == "all")
        $bisnismu->catId = "";
    if ($bisnismu->vendorId == "all")
        $bisnismu->vendorId = "";

    $dataProduct = $bisnismu->select_ppob_product("filter", $bisnismu->catId);
    $countProduct = $bisnismu->select_ppob_product("countFilter", $bisnismu->catId);

} elseif ($this->subActionPage == "add") {
    $pageName = "bisnismu-" . $this->subActionPage;
    $pageTitle = "Tambah Produk " . $sectionName;
} elseif ($this->subActionPage == "edit") {
    $pageName = "bisnismu-" . $this->subActionPage;
    $pageTitle = "Edit Produk " . $sectionName;
    $bisnismu->productId = $this->dataPage;
    $detailProduct = $bisnismu->select_ppob_product("byId", $bisnismu->sectionId);
    $productImage = $media->get_primary_image($bisnismu->sectionId, $bisnismu->productId);
} elseif ($this->subActionPage == "detail") {
    $pageName = "bisnismu-" . $this->subActionPage;
    $pageTitle = "Detail Produk " . $sectionName;
    $bisnismu->productId = $this->dataPage;
    $detailProduct = $bisnismu->select_ppob_product("byId", $bisnismu->sectionId);
    $productImage = $media->get_primary_image($bisnismu->sectionId, $bisnismu->productId, "imgDetail");

    $media->sectionId = $bisnismu->sectionId;
    $media->dataId = $bisnismu->productId;
    $dataImage = $media->select_media("", "image");

} elseif ($this->subActionPage == "fee") {
    $pageName = "bisnismu-" . $this->subActionPage;
    $pageTitle = "Daftar Komisi";

} elseif ($this->subActionPage == "profit") {
    $pageName = "bisnismu-" . $this->subActionPage;
    $pageTitle = "Laba Rugi Transaksi";

    $countMonth = ((intval(date('Y')) - 2015) * 12) + (intval(date('m')) - 5) + 1;
    $monthSelected = (isset($_SESSION['monthSelected'])) ? $_SESSION['monthSelected'] : "";
    if ($monthSelected != "") {
        $bisnismu->transCreateDate = $monthSelected;
        $dateStart = $monthSelected . "-01";
        $dateEnd = date("Y-m-t", strtotime($dateStart));
        $profitPembelianMonth = $bisnismu->get_profit("pembelian", $dateStart, $dateEnd);
        $profitPembayaranMonth = $bisnismu->get_profit("pembayaran", $dateStart, $dateEnd);
        $profitPembelian = $bisnismu->get_profit("pembelian");
        $profitPembayaran = $bisnismu->get_profit("pembayaran");
        $dataTrans = $bisnismu->select_ppob_transaction("successByMonth"); //p($dataProfit);exit;
        $countTrans = $bisnismu->select_ppob_transaction("countSuccessByMonth");
    } else {
        if (isset($_SESSION['monthSelected'])) unset($_SESSION['monthSelected']);
        $profitPembelian = $bisnismu->get_profit("pembelian");
        $profitPembayaran = $bisnismu->get_profit("pembayaran");
        $profitPembelianMonth = $profitPembelian;
        $profitPembayaranMonth = $profitPembayaran;
        $dataTrans = $bisnismu->select_ppob_transaction("success"); //p($dataProfit);exit;
        $countTrans = $bisnismu->select_ppob_transaction("countSuccess");
    }
    pagingShow($countTrans, $pgtogo, $intPageNumber, $intPageSize, $intPageDisplay);
} elseif ($this->subActionPage == "transaction") {
    if (isset($_POST['filterTrans'])) {
        $filterTransKey = security($_POST['filterTransKey']);
        $filterTransType = security($_POST['filterTransType']);
        $filterTransStatus = security($_POST['filterTransStatus']);
        $filterTransCat = security($_POST['filterTransCat']);

        if ($filterTransKey != "" || $filterTransStatus != "" || $filterTransCat != "") {
            $_SESSION['filterTransKey'] = $filterTransKey;
            $_SESSION['filterTransType'] = $filterTransType;
            $_SESSION['filterTransStatus'] = $filterTransStatus;
            $_SESSION['filterTransCat'] = $filterTransCat;
        } else {
            if (isset($_SESSION['filterTransKey'])) unset($_SESSION['filterTransKey']);
            if (isset($_SESSION['filterTransType'])) unset($_SESSION['filterTransType']);
            if (isset($_SESSION['filterTransStatus'])) unset($_SESSION['filterTransStatus']);
            if (isset($_SESSION['filterTransCat'])) unset($_SESSION['filterTransCat']);
        }
        header("Location:" . ADMIN_HOST . "/bisnismu/transaction");
        exit;
    }

    if (isset($_POST['resetFilterTrans'])) {
        unset($_SESSION['filterTransKey']);
        unset($_SESSION['filterTransType']);
        unset($_SESSION['filterTransStatus']);
        unset($_SESSION['filterTransCat']);
        header("Location:" . ADMIN_HOST . "/bisnismu/transaction");
        exit;
    }

    if (strpos($_SERVER['HTTP_REFERER'], "bisnismu/transaction") == 0) {
        if (isset($_SESSION['filterTransKey'])) unset($_SESSION['filterTransKey']);
        if (isset($_SESSION['filterTransType'])) unset($_SESSION['filterTransType']);
        if (isset($_SESSION['filterTransStatus'])) unset($_SESSION['filterTransStatus']);
        if (isset($_SESSION['filterTransCat'])) unset($_SESSION['filterTransCat']);
    }

    $totalSaldo = "Rp. 0";
    if ($_SERVER['HTTP_HOST'] != "localhost") {
        require_once(CLASS_PATH . "/bimasakti.class.php");
        $bimasakti = new Bimasakti();
        $bimasakti->mode = "production";
        $xmlData = $bimasakti->set_xml_data("balance");
        $dataResult = $bimasakti->send_xml_data($xmlData, "0");
        if ($dataResult['status'] == "1") {
            $response = xmlrpc_decode($dataResult['text'], null); //print_r($response);exit;
            $dataResponse = $bimasakti->parse_xml_response("balance", $response); //print_r($dataResponse);exit;
            $totalSaldo = "Rp. " . number($dataResponse['saldo']);
        }
    }

    if ($this->dataPage == "" || $this->dataPage == "page") {
        $pageName = "bisnismu-transaction";
        $pageTitle = "Daftar Transaksi " . $sectionName;
        if (!isset($_SESSION['filterTransKey'])) {
            $dataTrans = $bisnismu->select_ppob_transaction("");//p($dataTrans,1);
            $countTrans = $bisnismu->select_ppob_transaction("count");
            pagingShow($countTrans, $pgtogo, $intPageNumber, $intPageSize, $intPageDisplay);
        } else {
            $dataTrans = $bisnismu->select_ppob_transaction("search");
            $countTrans = $bisnismu->select_ppob_transaction("countSearch");

            $totalTransSearch = $bisnismu->select_ppob_transaction("totalTransFilter");
            $totalNew = 0;
            $totalSuccess = 0;
            $totalPending = 0;
            $totalFailed = 0;
            if (isset($totalTransSearch['new'])) {
                $totalNew = $totalTransSearch['new'];
            }
            if (isset($totalTransSearch['success'])) {
                $totalSuccess = $totalTransSearch['success'];
            }
            if (isset($totalTransSearch['pending'])) {
                $totalPending = $totalTransSearch['pending'];
            }
            if (isset($totalTransSearch['failed'])) {
                $totalFailed = $totalTransSearch['failed'];
            }

            pagingShow($countTrans, $pgtogo, $intPageNumber, $intPageSize, $intPageDisplay);
            if (isset($_SESSION['filterTransType']) && $_SESSION['filterTransType'] == "transMemberId" && count($dataTrans) > 0) {
                $customer->customerId = $dataTrans[0]['customer_id'];
                $bisnismu->customerId = $dataTrans[0]['customer_id'];
                $detailCustomer = $customer->select_customer("byId");
                $customerDeposit = $customer->get_total_customer_deposit($customer->customerId);
                $customerSaldo = $customer->get_member_saldo($customer->customerId);
                $bisnismu->transStatus = "success";
                $transSuccess = $bisnismu->customer_transaction("byStatusCustomer");
                $bisnismu->transStatus = "pending";
                $transPending = $bisnismu->customer_transaction("byStatusCustomer");
                //$bisnismu->transStatus = "failed";
                //$transFailed = $bisnismu->customer_transaction("byStatusCustomer");
            }
        }
    } elseif ($this->dataPage == "detail") {
        $pageName = "bisnismu-transaction-detail";
        $pageTitle = "Detail Transaksi " . $sectionName;
        $bisnismu->transId = intval($this->subDataPage);
        $detailTrans = $bisnismu->select_ppob_transaction("byId");
        $bisnismu->productCode = $detailTrans['ppob_product_code'];
        $model = $bisnismu->get_product_type("byCode");
        $parseResponse = array();

        if ($detailTrans['trans_respon_msg'] != "") {
            $xmlData = xmlrpc_decode($detailTrans['trans_respon_msg'], null);
            $parseResponse = $bimasakti->parse_xml_response($model, $xmlData);
        }

        if (count($detailTrans) == 0) {
            header("Location:" . ADMIN_HOST . "/" . $this->actionPage . "/" . $this->subActionPage);
            exit;
        }
    } else {
        header("Location:" . ADMIN_HOST . "/" . $this->actionPage . "/" . $this->subActionPage);
        exit;
    }

} elseif ($this->subActionPage == "category") {
    $pageName = "bisnismu-category";
    $pageTitle = "Kategori " . $sectionName;
    $category->sectionId = $bisnismu->sectionId;
} elseif ($this->subActionPage == "vendor") {
    if ($this->dataPage == "" || $this->dataPage == "page") {
        $pageName = "bisnismu-vendor";
        $pageTitle = "Vendor " . $sectionName;
        $dataVendor = $bisnismu->select_ppob_vendor();
        $countVendor = $bisnismu->select_ppob_vendor("count");
        pagingShow($countVendor, $pgtogo, $intPageNumber, $intPageSize, $intPageDisplay);
    } elseif ($this->dataPage == "add") {
        $pageName = "bisnismu-vendor-add";
        $pageTitle = "Tambah Vendor " . $sectionName;
    } elseif ($this->dataPage == "edit") {
        $pageName = "bisnismu-vendor-edit";
        $pageTitle = "Edit Vendor " . $sectionName;
        $bisnismu->vendorId = $this->subDataPage;
        $detailVendor = $bisnismu->select_ppob_vendor("byId");
    } elseif ($this->dataPage == "detail") {
        $pageName = "bisnismu-vendor-detail";
        $pageTitle = "Detail Vendor " . $sectionName;
        $bisnismu->vendorId = $this->subDataPage;
        $detailVendor = $bisnismu->select_ppob_vendor("byId");
    } else {
        header("Location:" . ADMIN_HOST . "/" . $this->actionPage . "/" . $this->subActionPage);
        exit;
    }
} else {
    $pageName = "404";
    $pageTitle = $sectionName;
}


//VENDOR


if (isset($_POST['addVendor']) || isset($_POST['editVendor'])) {
    if (isset($_POST['addVendor'])) {
        $act = "add";
    }
    if (isset($_POST['editVendor'])) {
        $act = "edit";
    }

    $bisnismu->vendorName = security($_POST['vendorName']);
    $bisnismu->vendorCp = security($_POST['vendorCp']);
    $bisnismu->vendorEmail = security($_POST['vendorEmail']);
    $bisnismu->vendorPhone = security($_POST['vendorPhone']);
    $bisnismu->vendorAddress = security($_POST['vendorAddress']);
    $bisnismu->vendorWebsite = security($_POST['vendorWebsite']);
    $bisnismu->vendorDesc = security($_POST['vendorDesc']);
    $bisnismu->vendorStatus = security($_POST['vendorStatus']);

    if ($bisnismu->vendorName == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan isi nama Vendor.");
    } elseif ($bisnismu->vendorCp == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan isi Contact Person Vendor.");
    } elseif ($bisnismu->vendorEmail == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan isi email Vendor.");
    } elseif ($bisnismu->vendorPhone == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan isi Telp / HP Vendor.");
    } else {
        if ($act == "add") {
            $bisnismu->insert_ppob_vendor();
            $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Penambahan data vendor <strong>" . $product->vendorName . "</strong> berhasil.");
            header("Location:" . LINK_LIST . "/vendor");
            exit;
        }

        if ($act == "edit") {
            $bisnismu->update_ppob_vendor();
            $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Data vendor <strong>" . $product->vendorName . "</strong> telah diperbarui.");
            header("Location:" . LINK_LIST . "/vendor");
            exit;
        }
    }

}

if (isset($_POST['delVendor'])) {
    $bisnismu->vendorId = $_POST['vendorIdDel'];
    $bisnismu->vendorName = $_POST['vendorNameDel'];
    $bisnismu->delete_ppob_vendor();
    $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Data vendor <strong>" . $product->vendorName . "</strong> telah dihapus.");
    header("Location:" . LINK_LIST . "/vendor");
    exit;
}

//PRODUCT

if (isset($_POST['addProduct']) || isset($_POST['editProduct'])) { //p($_POST);exit;
    if (isset($_POST['addProduct'])) {
        $act = "add";
    }
    if (isset($_POST['editProduct'])) {
        $act = "edit";
    }

    $bisnismu->catId = security($_POST['productCat']);
    $bisnismu->vendorId = security($_POST['productVendor']);
    $bisnismu->productName = security($_POST['productName']);
    $bisnismu->productAlias = generate_alias($bisnismu->productName);
    $bisnismu->productCode = security($_POST['productCode']);
    $bisnismu->productDesc = security($_POST['productDesc']);
    $bisnismu->productNominal = str_replace(".", "", $_POST['productNominal']);
    $bisnismu->productModal = str_replace(".", "", $_POST['productModal']);
    $bisnismu->productPrice = str_replace(".", "", $_POST['productPrice']);
    $bisnismu->productAdminBank = str_replace(".", "", $_POST['productAdminBank']);
    $bisnismu->productType = security($_POST['productType']);
    $bisnismu->productFee = str_replace(".", "", $_POST['productFee']);
    $bisnismu->productStatus = security($_POST['productStatus']);

    if ($bisnismu->productName == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan masukkan nama produk.");
    } elseif ($bisnismu->productCode == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan masukkan kode produk.");
    } elseif ($bisnismu->productDesc == "") {
        $_SESSION['TxtMsg'] = array("status" => "0", "text" => "Silahkan masukkan keterangan produk.");
    } else {
        if (isset($_FILES['contentImage']) && $_FILES['contentImage']['name'] != "") {
            $media->sectionId = $bisnismu->sectionId;
            $media->mediaType = "image";
            $media->mediaStatus = "1";
            $upload_directory = "media/image/";
            $allowedExt = array("png", "jpg", "jpeg", "gif");
            $mimeType = array("image/jpg", "image/png", "image/gif");
            $media->mediaName = $bisnismu->productName;
            $media->mediaAlias = generate_alias($media->mediaName);
            $media->mediaDesc = "";
            $media->mediaSize = get_size($_FILES['contentImage']['size']);
            $media->mediaPrimary = "1";
            $arrExt = explode(".", $_FILES["contentImage"]["name"]);
            $imgExt = strtolower(end($arrExt));
            $media->mediaValue = strtolower($detailSection['section_name']) . "-" . time() . "." . $imgExt;

            if ($_FILES['contentImage']['size'] > 8000000) {
                $error['contentImage'] = "Maksimal File Gambar 8 MB.";
            }
            if (!in_array($_FILES["contentImage"]["type"], $mimeType) && !in_array($imgExt, $allowedExt)) {
                $error['contentImage'] = "Format gambar yang diijinkan .jpg, .png dan .gif";
            }
        }

        if ($act == "add") {
            $bisnismu->insert_ppob_product();
            if (isset($_FILES['contentImage'])) {
                $media->dataId = $bisnismu->lastInsertId;
                $media->insert_media();
                move_uploaded_file($_FILES['contentImage']['tmp_name'], $upload_directory . $media->mediaValue);
            }

            $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Penambahan produk baru berhasil.");
            //header("Location:".LINK_DETAIL."/".$bisnismu->lastInsertId);
            header("Location:" . LINK_LIST);
            exit;
        }
        if ($act == "edit") {
            $bisnismu->update_ppob_product();
            if (isset($_FILES['contentImage']) && $_FILES['contentImage']['name'] != "") {
                $media->dataId = $bisnismu->productId;
                if (!isset($productImage['media_id'])) {
                    $media->insert_media();
                } else {
                    $media->mediaId = $productImage['media_id'];
                    $media->mediaDesc = $productImage['media_desc'];
                    $media->update_media();
                    if ($productImage['media_value'] != "" && $productImage['media_value'] != "noimage.png") {
                        if (file_exists(MEDIA_IMAGE_PATH . "/" . $productImage['media_value']))
                            unlink(MEDIA_IMAGE_PATH . "/" . $productImage['media_value']);
                    }
                }
                move_uploaded_file($_FILES['contentImage']['tmp_name'], $upload_directory . $media->mediaValue);
            }
            $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Produk <strong>" . $bisnismu->productName . "</strong> telah diperbarui.");
            //header("Location:".LINK_DETAIL."/".$bisnismu->productId);
            header("Location:" . LINK_LIST);
            exit;
        }
    }
}


if (isset($_POST['delProduct'])) {
    $bisnismu->productId = security($_POST['productIdDel']);
    $productName = security($_POST['productNameDel']);
    $bisnismu->delete_ppob_product();
    $_SESSION['TxtMsg'] = array("status" => "1", "text" => "Produk <strong>" . $productName . "</strong> telah dihapus.");
    header("Location:" . $_SERVER['HTTP_REFERER']);
    exit;
}


//CATEGORY

if (isset($_POST['addCat'])) {
    $bisnismu->form_add_ppob_category();
}

if (isset($_POST['editCat'])) {
    $bisnismu->form_edit_ppob_category();
}

if (isset($_POST['updateCatOrder'])) {
    $bisnismu->form_update_order_ppob_category();
}

if (isset($_POST['delCat'])) {
    $bisnismu->form_delete_ppob_category();
}


if (isset($_POST['filterProduct'])) {
    $filterKey = security($_POST['filterKey']);
    $filterCat = security($_POST['filterCat']);
    $filterVendor = security($_POST['filterVendor']);
    if ($filterKey == "") $filterKey = "all";
    if ($filterKey == "all" && $filterCat == "all" && $filterVendor == "all") {
        header("Location:" . ADMIN_HOST . "/bisnismu");
        exit;
    }
    $_SESSION['filterKey'] = $filterKey;
    $_SESSION['filterCat'] = $filterCat;
    $_SESSION['filterVendor'] = $filterVendor;
    header("Location:" . ADMIN_HOST . "/bisnismu/filter/" . $filterKey . "/" . $filterCat . "/" . $filterVendor);
    exit;
}

if (isset($_POST['resetFilter'])) {
    unset($_SESSION['filterKey']);
    unset($_SESSION['filterCat']);
    unset($_SESSION['filterVendor']);
    header("Location:" . ADMIN_HOST . "/bisnismu");
    exit;
}

//TRANSACTION

if (isset($_POST['cekDataTrans'])) {
    $tanggal1 = $_POST['tanggal1'];
    $idTrans = $_POST['idTransaksi'];
    $kodeProduk = $_POST['kodeProduk'];
    $idPelanggan = $_POST['idPelanggan'];

    $bisnismu->productCode = $kodeProduk;
    $dataProductT = $bisnismu->select_ppob_product("byCode");

    if (strtolower($dataProductT['vendor_name']) == 'bimasakti') { //cek bms
        $setData = array();
        $setData['tanggal1'] = date('Ymd', strtotime($tanggal1)) . "000000";
        $setData['tanggal2'] = date('Ymd235959');
        $setData['idtransaksi'] = $idTrans;
        $setData['kodeproduk'] = $kodeProduk;
        //$setData['idpelanggan'] = $idPelanggan;
        $setData['idpelanggan'] = "";
        $setData['limit'] = (isset(${$method}['limit'])) ? ${$method}['limit'] : "10";

        $bimasakti->mode = "production";
        $bimasakti->nameFile = $setData['idtransaksi'] . "-datatransaksi";
        $xmlData = $bimasakti->set_xml_data("datatransaksi", $setData, "1");
        $dataResult = $bimasakti->send_xml_data($xmlData, "1");
        if ($dataResult['status'] == "1") {
            $response = xmlrpc_decode($dataResult['text'], null); //print_r($response);exit;
            $dataResponse = $bimasakti->parse_xml_response("datatransaksi", $response);
            //print_r($dataResponse);
            $dataTrans = array();
            for ($i = 0; $i < count($dataResponse) - 9; $i++) {
                $dataTrans[$i] = $bimasakti->parse_datatransaksi($dataResponse['data' . ($i + 1)]);
                $bisnismu->transModal = $dataTrans[$i]['nominal'];
                $bisnismu->transResponCode = $dataTrans[$i]['rc'];
                $bisnismu->transResponRefnum = $dataTrans[$i]['sn'];
                $bisnismu->transResponReqnum = $dataTrans[$i]['idtransaksi'];
                $bisnismu->transResponDate = substr($dataTrans[$i]['date'], 0, 4) . "-" . substr($dataTrans[$i]['date'], 4, 2) . "-" . substr($dataTrans[$i]['date'], 6, 2) . " " . substr($dataTrans[$i]['date'], 8, 2) . ":" . substr($dataTrans[$i]['date'], 10, 2) . ":" . substr($dataTrans[$i]['date'], 12, 2);
                if (intval($bisnismu->transResponCode) == 0) {
                    if (strtolower($dataTrans[$i]['keterangan']) == "sedang diproses") {
                        $bisnismu->transStatus = "pending";
                        $bisnismu->transResponNote = "Transaksi sedang diproses. @@";
                    } else {
                        $bisnismu->transStatus = "success";
                        $bisnismu->transResponNote = "Transaksi Berhasil pada : " . $bisnismu->transResponDate . " @@";
                    }
                } else {
                    $bisnismu->transStatus = "failed";
                    $bisnismu->transResponNote = "Transaksi Gagal. @@";
                }
                $bisnismu->transResponNote .= $dataResponse['data' . ($i + 1)];

                $bisnismu->update_ppob_transaction("datatransaksi");
            }
            //p($dataTrans);
        }

    } elseif (strtolower($dataProductT['vendor_name']) == 'idbiller') {
        $idbiller->mode = "production";
        $idbiller->methodName = "DataTransactionService";

        $setData = array();
        $setData['RefNumber'] = $idTrans;
        $setData['StartDate'] = '';
        $setData['EndDate'] = '';
        $setData['Limit'] = '';

        $dataResult = $idbiller->send_data($setData, "0");

        if ($dataResult['status'] == "1") {
            $response = json_decode($dataResult['text'], true);

            if ($response['Rc'] == '00') {
                $dataTrans = $response['DataTransactions'][0];

                $bisnismu->transModal = $dataTrans['TransPrice'];
                $bisnismu->transResponCode = $dataTrans['TransRc'];
                $bisnismu->transResponRefnum = $dataTrans['TransSn'];
                $bisnismu->transResponReqnum = $dataTrans['RefNumber'];
                $bisnismu->transResponDate = substr($dataTrans['TransDate'], 0, 4) . "-" . substr($dataTrans['TransDate'], 4, 2) . "-" . substr($dataTrans['TransDate'], 6, 2) . " " . substr($dataTrans['TransDate'], 8, 2) . ":" . substr($dataTrans['TransDate'], 10, 2) . ":" . substr($dataTrans['TransDate'], 12, 2);

                if ($bisnismu->transResponCode == '00') {
                    $bisnismu->transStatus = "success";
                    $bisnismu->transResponNote = "Transaksi Berhasil pada : " . $bisnismu->transResponDate . " @@";
                } elseif ($bisnismu->transResponCode == '01') {
                    $bisnismu->transStatus = "pending";
                    $bisnismu->transResponNote = "Transaksi sedang diproses. @@";
                } else {
                    $bisnismu->transStatus = "failed";
                    $bisnismu->transResponNote = "Transaksi Gagal. @@";
                }

                $bisnismu->transResponDate .= $dataTrans['TransDesc'];

                $bisnismu->update_ppob_transaction("datatransaksi");
            }
        }
    }
    header("Location:" . ADMIN_HOST . "/bisnismu/transaction");
    exit;
}

if (isset($_POST['cekDataTransDetail'])) {
    $tanggal1 = $_POST['tanggal1'];
    $idTrans = $_POST['idTransaksi'];
    $kodeProduk = $_POST['kodeProduk'];
    $idPelanggan = $_POST['idPelanggan'];

    if (!empty($_POST['reqnum'])) {
        $bisnismu->transId = $_POST['transId'];
        $bisnismu->update_ppob_transaction("byField", "trans_respon_reqnum", $_POST['reqnum']);
    }
    $bisnismu->productCode = $kodeProduk;
    $dataProductT = $bisnismu->select_ppob_product("byCode");

    if (strtolower($dataProductT['vendor_name']) == 'bimasakti') { //cek BMS
        $setData = array();

        $setData['tanggal1'] = date('Ymd', strtotime($tanggal1)) . "000000";
        $setData['tanggal2'] = date('Ymd235959');
        $setData['idtransaksi'] = $idTrans;
        $setData['kodeproduk'] = $kodeProduk;
        $setData['idpelanggan'] = $idPelanggan;
        $setData['limit'] = (isset(${$method}['limit'])) ? ${$method}['limit'] : "10";

        $bimasakti->mode = "production";
        $bimasakti->nameFile = $setData['idtransaksi'] . "-datatransaksi";

        $xmlData = $bimasakti->set_xml_data("datatransaksi", $setData, "1");
        $dataResult = $bimasakti->send_xml_data($xmlData, "1");

        if ($dataResult['status'] == "1") {
            $response = xmlrpc_decode($dataResult['text'], null);
            $dataResponse = $bimasakti->parse_xml_response("datatransaksi", $response);

            $dataTrans = array();

            for ($i = 0; $i < count($dataResponse) - 9; $i++) {
                $dataTrans[$i] = $bimasakti->parse_datatransaksi($dataResponse['data' . ($i + 1)]);

                $bisnismu->transModal = $dataTrans[$i]['nominal'];
                $bisnismu->transResponCode = $dataTrans[$i]['rc'];
                $bisnismu->transResponRefnum = $dataTrans[$i]['sn'];
                $bisnismu->transResponReqnum = $dataTrans[$i]['idtransaksi'];
                $bisnismu->transResponDate = substr($dataTrans[$i]['date'], 0, 4) . "-" . substr($dataTrans[$i]['date'], 4, 2) . "-" . substr($dataTrans[$i]['date'], 6, 2) . " " . substr($dataTrans[$i]['date'], 8, 2) . ":" . substr($dataTrans[$i]['date'], 10, 2) . ":" . substr($dataTrans[$i]['date'], 12, 2);

                if (intval($bisnismu->transResponCode) == 0) {
                    if (strtolower($dataTrans[$i]['keterangan']) == "sedang diproses") {
                        $bisnismu->transStatus = "pending";
                        $bisnismu->transResponNote = "Transaksi sedang diproses. @@";
                    } else {
                        $bisnismu->transStatus = "success";
                        $bisnismu->transResponNote = "Transaksi Berhasil pada : " . $bisnismu->transResponDate . " @@";
                    }
                } else {
                    $bisnismu->transStatus = "failed";
                    $bisnismu->transResponNote = "Transaksi Gagal. @@";
                }
                $bisnismu->transResponNote .= $dataResponse['data' . ($i + 1)];

                $bisnismu->update_ppob_transaction("datatransaksi");
            }
        } elseif (strtolower($dataProductT['vendor_name']) == 'idbiller') { //cek IDB
            $idbiller->mode = "production";
            $idbiller->methodName = "DataTransactionService";

            $setData = array();
            $setData['RefNumber'] = $idTrans;
            $setData['StartDate'] = '';
            $setData['EndDate'] = '';
            $setData['Limit'] = '';

            $dataResult = $idbiller->send_data($setData);
            if ($dataResult['status'] == "1") {
                $response = json_decode(($dataResult['text']), true);
                if ($response['Rc'] == '00') {
                    $dataTrans = $response['DataTransactions'][0];

                    $bisnismu->transModal = $dataTrans['TransPrice'];
                    $bisnismu->transResponCode = $dataTrans['TransRc'];
                    $bisnismu->transResponRefnum = $dataTrans['TransSn'];
                    $bisnismu->transResponReqnum = $dataTrans['RefNumber'];
                    $bisnismu->transResponDate = substr($dataTrans['TransDate'], 0, 4) . "-" . substr($dataTrans['TransDate'], 4, 2) . "-" . substr($dataTrans['TransDate'], 6, 2) . " " . substr($dataTrans['TransDate'], 8, 2) . ":" . substr($dataTrans['TransDate'], 10, 2) . ":" . substr($dataTrans['TransDate'], 12, 2);

                    if ($bisnismu->transResponCode == '00') {
                        $bisnismu->transStatus = "success";
                        $bisnismu->transResponNote = "Transaksi Berhasil pada : " . $bisnismu->transResponDate . " @@";
                    } elseif ($bisnismu->transResponCode == '01') {
                        $bisnismu->transStatus = "pending";
                        $bisnismu->transResponNote = "Transaksi sedang diproses. @@";
                    } else {
                        $bisnismu->transStatus = "failed";
                        $bisnismu->transResponNote = "Transaksi Gagal. @@";
                    }

                    $bisnismu->transResponDate .= $dataTrans['TransDesc'];

                    $bisnismu->update_ppob_transaction("datatransaksi");
                }
            }
        }

    }
    header("Location:" . ADMIN_HOST . "/bisnismu/transaction/detail/" . $this->subDataPage);
    exit;
}

if (isset($_POST['cetakUlang'])) {
    $setData['ref2'] = $_POST['reqnum'];
    $xmlData = $bimasakti->set_xml_data("cetakulang", $setData, "1");
    $dataResult = $bimasakti->send_xml_data($xmlData, "1");

    if ($dataResult['status'] == '1') {
        $response = xmlrpc_decode($dataResult['text'], null);
        $dataResponse = $bimasakti->parse_xml_response("datatransaksi", $response);

        if ($dataResponse['data4'] != 33) {
            $bisnismu->transId = $_POST['transId'];
            $bisnismu->update_ppob_transaction("byField", "trans_respon_msg", $dataResult['text']);
        } else {
            echo '
				<script>
					alert("' . $dataResponse['data5'] . '");
					location.href="' . ADMIN_HOST . '/bisnismu/transaction/detail/' . $this->subDataPage . '";
				</script>
			';
            exit;
        }
    }

    header("Location:" . ADMIN_HOST . "/bisnismu/transaction/detail/" . $this->subDataPage);
    exit;
}

if (isset($_POST['submitMonthProfit'])) {
    $_SESSION['monthSelected'] = security($_POST['selectMonth']);
    header("Location:" . ADMIN_HOST . "/bisnismu/profit");
    exit;
}
?>