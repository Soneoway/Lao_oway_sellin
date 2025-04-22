<?php

set_time_limit(0);

$this->_helper->layout()->disableLayout(true);
$this->_helper->viewRenderer->setNoRender(true);

date_default_timezone_set("Asia/Bangkok");

try {
    $db = Zend_Registry::get('db');
    if ($this->getRequest()->getMethod() == 'POST') {

        $db->beginTransaction();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $color_id = $this->getRequest()->getParam('color_id');

        $type = $this->getRequest()->getParam('type');

        $bucket_name = $this->getRequest()->getParam('bucket_name');

        if(!in_array($type, ['normal','qr', 'all'])){
            echo json_encode(['status' => 400, 'message' => 'Error Data']);
            exit();
        }

        switch ($type) {
            case 'normal':
                    
                $count_corrent = $this->getRequest()->getParam('count_corrent');
                $count_incorrent = $this->getRequest()->getParam('count_incorrent');

                $data_corrent = $this->getRequest()->getParam('data_corrent');
                $data_incorrent = $this->getRequest()->getParam('data_incorrent');
                break;

            case 'qr':
                
                $count_corrent = $this->getRequest()->getParam('count_corrent_qr');
                $count_incorrent = $this->getRequest()->getParam('count_incorrent_qr');

                $data_corrent = $this->getRequest()->getParam('data_corrent_qr');
                $data_incorrent = $this->getRequest()->getParam('data_incorrent_qr');
                break;

            case 'all':
                
                $count_corrent = $this->getRequest()->getParam('count_corrent') + $this->getRequest()->getParam('count_corrent_qr');
                $count_incorrent = $count_incorrent = $this->getRequest()->getParam('count_incorrent') + $this->getRequest()->getParam('count_incorrent_qr');

                $data_corrent = $this->getRequest()->getParam('data_corrent') . PHP_EOL . $this->getRequest()->getParam('data_corrent_qr');
                $data_incorrent = $this->getRequest()->getParam('data_incorrent') . $this->getRequest()->getParam('data_incorrent_qr');
                break;
        }

        $dateTimeNow = date("Y-m-d H:i:s");

        $QLCIW = new Application_Model_LogCheckImeiWarehouse();
        $QImei = new Application_Model_Imei();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $w_notinput = substr_count($data_incorrent, 'No IMEI');
        $w_notinput = $w_notinput + substr_count($data_incorrent, 'Error Data');
        $w_notinput = $w_notinput + substr_count($data_incorrent, 'No IMEI Input.');

        $w_notformat = substr_count($data_incorrent, 'Wrong Format');
        $w_duplicate = substr_count($data_incorrent, 'Duplicate Checking');
        $w_duplicate = $w_duplicate + substr_count($data_incorrent, 'Duplicated IMEIs');

        $w_notfound = substr_count($data_incorrent, 'Not found');
        $w_notwh = substr_count($data_incorrent, 'Not In WH');
        $w_notproduct = substr_count($data_incorrent, 'Not In Product');
        $w_notcolor = substr_count($data_incorrent, 'Not In Color');
        $w_exported = substr_count($data_incorrent, 'Exported');
        $w_unableexport = substr_count($data_incorrent, 'Unable to export');

        $QCWL = new Application_Model_CheckWarehouseLine();

        $get_total_storage = $QCWL->getTotalStorage($warehouse_id,$good_id,$color_id);

        $data = array(
            'warehouse_id' => $warehouse_id,
            'good_id' => $good_id,
            'color_id' => $color_id,
            'total_qty' => $get_total_storage,
            'check_qty' => $count_corrent + $count_incorrent,
            'correct' => $count_corrent,
            'incorrect' => $count_incorrent,
            'w_notformat' => $w_notinput + $w_notformat,
            'w_duplicate' => $w_duplicate,
            'w_notfound' => $w_notfound,
            'w_notwh' => $w_notwh,
            'w_notproduct' => $w_notproduct,
            'w_notcolor' => $w_notcolor,
            'w_exported' => $w_exported,
            'w_unableexport' => $w_unableexport,
            'at_date' => $dateTimeNow,
            'at_by' => $userStorage->id
            );

        if($QLCIW->insert($data)){

            $data = explode(PHP_EOL, $data_corrent);

            $arrImei = array();
            foreach ($data as $key) {
                if(strlen(intval($key)) == 15){
                    array_push($arrImei, $key);
                }
            }

            if(count($arrImei) > 0){

                $QImei = new Application_Model_Imei();
                $where = $QImei->getAdapter()->quoteInto('imei_sn in (?)', $arrImei);

                $update_data = array(
                                'bucket_name' => $bucket_name,
                                'bucket_by' => $userStorage->id,
                                'bucket_date' => $dateTimeNow
                                );
                
                if($QImei->update($update_data, $where)){
                    
                    $db->commit();
                    echo json_encode(['status' => 200, 'message' => 'Save Done.']);
                    exit();

                }

            }else{

                $db->commit();
                echo json_encode(['status' => 200, 'message' => 'Save Done.']);
                exit();
            }
            
        }

        $db->rollBack();
        
        echo json_encode(['status' => 400, 'message' => 'Can not save log.']);

    }

}catch(exception $e) {

    $db->rollBack();

    echo json_encode(['status' => 400, 'message' => 'Can not save log : ' . $e->getMessage()]);
    exit();
}

?>