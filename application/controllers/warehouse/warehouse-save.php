<?php
$flashMessenger = $this->_helper->flashMessenger;

if ($this->getRequest()->getMethod() == 'POST') {
    $id                 = $this->getRequest()->getParam('id');
    $name               = $this->getRequest()->getParam('name', '');
    $warehouse_type     = $this->getRequest()->getParam('warehouse_type', '');
    $company_id         = $this->getRequest()->getParam('company_id', '');
    $show_kerry         = $this->getRequest()->getParam('show_kerry','2');

    // $area               = $this->getRequest()->getParam('area');

    $provience          = $this->getRequest()->getParam('provience');
    $warehouse_level    = $this->getRequest()->getParam('warehouse_level');
    $address            = $this->getRequest()->getParam('address');
    $store_affili       = $this->getRequest()->getParam('store_affili');
    $markettable        = $this->getRequest()->getParam('markettable');
    $exter_serial       = $this->getRequest()->getParam('exter_serial');
    $leader             = $this->getRequest()->getParam('leader');
    $remark             = $this->getRequest()->getParam('remark');
    $tel                = $this->getRequest()->getParam('tel');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QRegionalMarket = new Application_Model_RegionalMarket();
    $QClientCode = new Application_Model_ClientCode();

    $cusCode        = $QClientCode->find(2);
    $insertCode     = $cusCode[0]['next_code'];
    
    if ($name == '') {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Warehouse, Please and try agian !.');
        $this->_redirect(HOST.'warehouse/warehouse-create');
    }

    // $data = array_filter( array(
    //     'name'     => $name,
    //     'warehouse_type'     => $warehouse_type,
    //     'company_id'     => $company_id,
    //     'show_kerry'     => $show_kerry,    //1= show ;  2 = no show
    //     'area_id'              => $area,
    //     'province_id'             => $provience
    // ));
    $where   = array();
    $where[] = $QRegionalMarket->getAdapter()->quoteInto('id =?',$provience);
    $where[] = $QRegionalMarket->getAdapter()->quoteInto('parent =?',0);
    $area    = $QRegionalMarket->fetchAll($where)->toArray();

    // print_r($area); die();

    $data = array_filter(array(
            'code'                  => $insertCode,
            'name'                  => $name,
            'warehouse_type'        => $warehouse_type,
            'company_id'            => $company_id,
            'show_kerry'            => $show_kerry,
            'area_id'               => $area[0]['area_id'],
            'province_id'           => $provience,
            'level'                 => $warehouse_level,
            'address'               => $address,
            'affiliation'           => $store_affili,
            'market_table'          => $markettable,
            'external_serial'       => $exter_serial,
            'leader'                => $leader,
            'phone'                 => $tel,
            'remark'                => $remark,
            'created_at'            => date('Y-m-d H:i:s'),
            'created_by'            => $userStorage->id
    ));

    //print_r($data);die;
    $QWarehouse = new Application_Model_Warehouse();

    if ($id) { // updated

        $data['updated_at'] = date('Y-m-d H:i:S');
        $data['updated_by'] = $userStorage->id;

        $where = $QWarehouse->getAdapter()->quoteInto('id = ?', $id);
        $QWarehouse->update($data, $where);
    } else { // create new

        $id = $QWarehouse->insert($data);

         if($id) {
                $next_code = $insertCode + 1; 

                $data_code = array(
                    'last_code'     => $insertCode,
                    'next_code'     => $next_code,
                    'updated_at'    => date('Y-m-d H:i:s')
                );

                $where = $QClientCode->getAdapter()->quoteInto('id = ?', 2);
                $QClientCode->update($data_code,$where);
            }
    }

    //remove cache
    $cache = Zend_Registry::get('cache');
    $cache->remove('warehouse_cache');

    $flashMessenger->setNamespace('success')->addMessage('Success');
    $this->_redirect(HOST.'warehouse/list');

} else {
    $flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
    $this->_redirect(HOST.'warehouse/warehouse-create');
}