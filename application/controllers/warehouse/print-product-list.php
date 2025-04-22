<?php
$this->_helper->layout->disableLayout();
$sns = $this->getRequest()->getParam('sn');
$sn_ref = $this->getRequest()->getParam('sn_ref');
//echo $sn_ref[0];
if (is_array($sns) && $sns) {
    $sns = array_unique($sns);

    $QMarket       = new Application_Model_Market();
    $QGood         = new Application_Model_Good();
    $QGoodColor    = new Application_Model_GoodColor();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QDistributor  = new Application_Model_Distributor();
    $QStaff        = new Application_Model_Staff();
    $QService      = new Application_Model_Service();
    $QOffice       = new Application_Model_Office();
    $QWarehouse    = new Application_Model_Warehouse();

    $staffs = $QStaff->get_cache();
    $this->view->warehouse_cached = $QWarehouse->get_cache();
    $this->view->goods            = $QGood->get_cache();
    $this->view->pname            = $QGood->get_name();
    $this->view->goodColors       = $QGoodColor->get_cache();
    $this->view->good_categories  = $QGoodCategory->get_cache();
    $this->view->distributors     = $QDistributor->get_all_cache();
    $this->view->phone_id         = PHONE_CAT_ID;

    $phones = array();
    $accessories = array();
    $info_data = array();

    $db = Zend_Registry::get('db');

  
        $tmp = array();
         
        $count = count($sns);
        for ($i=0; $i <= $count-1; $i++) {
             $no .= $sns[$i].",";
         };
            $sn = rtrim($no,",");
           $snn = "(".$sn.")";
           
    
        // $select = $db->select()
        //     ->from(array('p' => 'market'),
        //     array(
        //         'total_qty' => 'SUM(p.num)','p.good_id','p.good_color'
        //     ))
        //     ->joinLeft(array('ck' => 'checkmoney_paymentorder'), 'ck.sn=p.sn')
        //     ->join(array('d' => 'distributor'), 'd.id=p.d_id')
        //     ->where('p.status = ?', 1)
        //     ->Where('p.sn in ?', $sn )
        //     ->group('p.good_id ,p.good_color');
          $sql = "SELECT `p`.`good_id`, `p`.`good_color`, `p`.`cat_id`, SUM(p.num) AS `total_qty`
                    FROM `market` AS `p` LEFT JOIN `checkmoney_paymentorder` AS `ck` ON ck.sn=p.sn 
                    INNER JOIN `distributor` AS `d` ON d.id=p.d_id
                    WHERE (p.status = 1) AND (p.cat_id = 11) AND
                    (p.sn in".$snn.")
                    GROUP BY p.good_id ,p.good_color"   ;  
                                
        $sql2 = "SELECT `p`.`good_id`, `p`.`good_color`, `p`.`cat_id`, SUM(p.num) AS `total_qty`
                    FROM `market` AS `p` LEFT JOIN `checkmoney_paymentorder` AS `ck` ON ck.sn=p.sn 
                    INNER JOIN `distributor` AS `d` ON d.id=p.d_id
                    WHERE (p.status = 1) AND (p.cat_id = 12) AND
                    (p.sn in".$snn.")
                    GROUP BY p.good_id ,p.good_color"   ; 
        $phones[] = $db->fetchAll($sql);
        $accessories[] = $db->fetchAll($sql2);
        
       
   // end foreach
    
    $this->view->info_data = $info_data;
    $this->view->phones = $phones;
    $this->view->accessories = $accessories;
    if (isset($service) and $service) {

      $services = $QService->get_cache_service();
        $this->view->services = $services[$service];
    }
    if (isset($office) and $office) {
        $officeRowSet = $QOffice->find($office);
        $offices = $officeRowSet->current();
        $this->view->services = $offices;
        $this->view->service = $office;
    }

} else {
    exit;
}
