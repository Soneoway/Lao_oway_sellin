    <?php
// start : confirm section 
    $tmp = $this->getRequest()->getParam('id',0);

    if (isset($tmp) && $tmp != 0) {
        $QSales = new Application_Model_Market();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $where = $QSales->getAdapter()->quoteInto('sn = ?', $tmp);
        $data = array('confirm_so' => 1);
        
        $flashMessenger = $this->_helper->flashMessenger;
        try {
            $QSales->update($data, $where);

            //todo log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');

            $info = 'Confirm : Sales order number: ' . $tmp;

            $QLog = new Application_Model_Log();

            $QLog->insert(array(
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ));

            $flashMessenger->setNamespace('success')->addMessage('Confirm Sale Order : Done!');
        } 
        catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
        }
        $this->_redirect('/sales');
    }
    // end : confirm section 
    
    $sort              = $this->getRequest()->getParam('sort', 'p.id');
    $desc              = $this->getRequest()->getParam('desc', 1);
    $page              = $this->getRequest()->getParam('page', 1);
    $sn                = $this->getRequest()->getParam('sn');
    $distributor_name  = $this->getRequest()->getParam('distributor_name');
    $d_id              = $this->getRequest()->getParam('d_id');
    $good_id           = $this->getRequest()->getParam('good_id');
    $good_color        = $this->getRequest()->getParam('good_color');
    $num               = $this->getRequest()->getParam('num');
    $price             = $this->getRequest()->getParam('price');
    $pay_time          = $this->getRequest()->getParam('payment', 0);
    $outmysql_time     = $this->getRequest()->getParam('outmysql_time', 0);
    $created_at_to     = $this->getRequest()->getParam('created_at_to', date('d/m/Y'));
    $created_at_from   = $this->getRequest()->getParam('created_at_from', date('d/m/Y', strtotime('-1 day')));
    $invoice_time_from = $this->getRequest()->getParam('invoice_time_from');
    $invoice_time_to   = $this->getRequest()->getParam('invoice_time_to');
    $finance_confirm_time_from  = $this->getRequest()->getParam('finance_confirm_time_from');
    $finance_confirm_time_to    = $this->getRequest()->getParam('finance_confirm_time_to');
    $cat_id            = $this->getRequest()->getParam('cat_id');
    $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
    $type              = $this->getRequest()->getParam('type');
    $text              = $this->getRequest()->getParam('text');
    $status            = $this->getRequest()->getParam('status', 1);
    $export            = $this->getRequest()->getParam('export', 0);
    $tags              = $this->getRequest()->getParam('tags');
    $invoice_number    = $this->getRequest()->getParam('invoice_number');
    $user_id           = $this->getRequest()->getParam('user_id');
    $area_id           = $this->getRequest()->getParam('area_id');
    $region_id         = $this->getRequest()->getParam('region_id');
    $district_id       = $this->getRequest()->getParam('district_id');
    $distributor_po    = $this->getRequest()->getParam('distributor_po');
    $campaign_id       = $this->getRequest()->getParam('campaign_id');
    $distributor_ka    = $this->getRequest()->getParam('distributor_ka');
    $cancel            = $this->getRequest()->getParam('cancel');

    if ($tags and is_array($tags))
        $tags = $tags;
    else
        $tags = null;

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter(array(
            'sn'                 => $sn,
            'distributor_name'   => $distributor_name,
            'd_id'               => $d_id,
            'good_id'            => $good_id,
            'good_color'         => $good_color,
            'num'                => $num,
            'price'              => $price,
            'total'              => $total,
            'cat_id'             => $cat_id,
            'warehouse_id'       => $warehouse_id,
            'status'             => $status,
            'text'               => $text,
            'type'               => $type,
            'tags'               => $tags,
            'invoice_time_from'  => $invoice_time_from,
            'invoice_time_to'    => $invoice_time_to,
            'finance_confirm_time_from'  => $finance_confirm_time_from,
            'finance_confirm_time_to'    => $finance_confirm_time_to,
            'invoice_number'     => $invoice_number,
            'user_id'            => $user_id,
            'area_id'            => $area_id,
            'region_id'          => $region_id,
            'district_id'        => $district_id,
            'distributor_po'     => $distributor_po,
            'campaign_id'        => $campaign_id,
            'distributor_ka'     => $distributor_ka,
            'cancel'     => $cancel
    ));

    $params['created_at_from'] = $created_at_from;
    $params['created_at_to'] = $created_at_to;

    $params['isbacks'] = false;
    $params['group_sn'] = true;

    if ($pay_time)
        $params['payment'] = true;

    if ($outmysql_time)
        $params['outmysql_time'] = true;

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {
        $params['create_user_id'] = $userStorage->hr_id;
    }

    $QGood          = new Application_Model_Good();
    $QGoodColor     = new Application_Model_GoodColor();
    $QMarket        = new Application_Model_Market();
    $QDistributor   = new Application_Model_Distributor();
    $QGoodCategory  = new Application_Model_GoodCategory();
    $QWarehouse     = new Application_Model_Warehouse();
    $QStaff         = new Application_Model_Staff();
    $QArea          = new Application_Model_Area();
    $QRegion        = new Application_Model_RegionalMarket();
    $QDistributorPo = new Application_Model_DistributorPo();
    $QMarketProduct = new Application_Model_MarketProduct();
    $QCampaign      = new Application_Model_Campaign();

    $this->view->po_list = $QDistributorPo->fetchAll(null, 'created_at DESC');

    $goods             = $QGood->get_cache();
    $goodColors        = $QGoodColor->get_cache();
    $distributors      = $QDistributor->get_cache();
    $distributors2     = $QDistributor->get_cache2();
    $distributor_ka    = $QDistributor->get_cacheKA();
    $good_categories   = $QGoodCategory->get_cache();
    $warehouses_cached = $QWarehouse->get_cache();
    $campaign          = $QCampaign->get_cache();


    $staffs_cached     = $QStaff->get_cache();

    if (isset($export) && $export)
    {
        // My_Report::preventExport();
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(0);
        ini_set('display_error', 0);

        if ($export == 2)
        { //export for finance: use to import to account system

            $params['sales_type'] = array(
                1,
                2,
                3); //1: For Retailer;2: For Demo;3: For Staffs
            $params['group_sn']       = false;
            $params['no_accessories'] = true;
            $params['canceled']       = -1;
            $params['export']         = $export;

            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $this->_exportExcel2($markets_sn);
        } elseif ($export == 3) { //export for finance: use to tax report
            $params['sales_type'] = array(
                1,
                2,
                3); //1: For Retailer;2: For Demo;3: For Staffs
            $params['group_sn']         = true;
            /*$params['no_accessories'] = true;*/
            $params['canceled']         = -1;
            $params['export']           = $export;

            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);


            $this->_exportExcel3($markets_sn);

        } elseif ($export == 4) { //IMEI Export
            if (My_Report::preventExport()) exit;

            $params['group_sn']       = 0;
            $params['get_imei']       = 1;
            $params['no_accessories'] = true;
            $params['canceled']       = -1;
            $params['export']         = $export;

            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $this->_exportExcel4($markets_sn);

        } elseif ($export == 5) { //Campaign Export
            // if (My_Report::preventExport()) exit;
            $params['get_imei'] = 1;
            $params['group_sn'] = false;
            $params['export']   = $export;
            $params['campaign_id']    = $campaign_id;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
           
            $this->_exportCampaignExcel($markets_sn);

        } elseif ($export == 6) {
            $params['sales_type'] = array(
                1,
                2,
                3); //1: For Retailer;2: For Demo;3: For Staffs
            $params['group_sn']         = true;
            $params['canceled']         = -1;
            $params['export']           = $export;
            $params['get_imei']         = 1;

            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

            $this->_exportCampaignExcel($markets_sn);

        } elseif ($export == 7) {
            $params['campaign_id'] = false;
            $params['group_sn'] = false;
            $params['export'] = $export;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $tmp = explode('FROM', $markets_sn);
            $sql = $tmp[0].", SUM( ROUND((p.total/1.07),2) ) as sum_total FROM ".$tmp[1]." GROUP BY p.invoice_number";

           
            $this->_exportExcelOutputVat($sql);

        } elseif ($export == 8) {
            $params['campaign_id'] = false;
            $params['group_sn'] = false;
            $params['export'] = $export;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);
            $tmp = explode('FROM', $markets_sn);
            $sql = $tmp[0].", SUM(p.total) as sum_total FROM ".$tmp[1]." GROUP BY p.sn";

            //print_r($sql);die;
            $this->_exportExcelOrderStatus($sql);

        } elseif ($export == 9) {
            $params['campaign_id'] = false;
            $params['group_sn'] = false;
            $params['export'] = $export;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

            $tmp = explode('FROM', $markets_sn);

            $sql = $tmp[0].", SUM(p.num) as sum_num, SUM(p.price) as sum_price, SUM(p.total) as sum_total FROM ".$tmp[1]." GROUP BY d.region, p.good_id, p.good_color";
            //print_r($sql);die;
            $this->_exportExcelByProvince($sql);

        } elseif ($export == 10) {
            $params['campaign_id'] = false;
            $params['group_sn'] = false;
            $params['export'] = $export;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

            $tmp = explode('FROM', $markets_sn);
            $sql = $tmp[0].", SUM(p.num) as sum_num, SUM(p.total) as sum_total FROM ".$tmp[1]." AND p.cat_id = 11 GROUP BY p.sn";
            //print_r($sql);die;
            $this->_exportExcelByModel($sql);

        } else {
            $params['campaign_id'] = false;
            $params['group_sn'] = false;
            $params['export'] = $export;
            $markets_sn = $QMarket->fetchPagination($page, null, $total, $params);

            //print_r($markets_sn);die;
            $this->_exportExcel($markets_sn);
        }
    }

    $params['sort'] = $sort;
    $params['desc'] = $desc;
    $params['get_fields'] = array(
        'sn',
        'd_id',
        'pay_time',
        'shipping_yes_time',
        'outmysql_time',
        'warehouse_id',
        'status',
        'add_time',
        'canceled',
        'last_updated_at'
    );

    $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);


    $markets_sn_array = array();

    foreach ($markets_sn as $k => $v)
    {
        $markets_sn_array[$k] = $v;
        $markets_sn_array[$k]['discount'] = $QMarketProduct->getDiscount($v['sn']);
    }

    unset($params['get_fields']);
    unset($params['isbacks']);
    unset($params['group_sn']);

    $this->view->areas = $QArea->get_cache();

    if ($area_id)
    {
        $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area_id);
        $regions = $QRegion->fetchAll($where, 'name');

        $regions_arr = array();

        foreach ($regions as $key => $value)
            $regions_arr[$value['id']] = $value['name'];

        $this->view->regions = $regions_arr;
    }

    if ($region_id)
    {
        $where = $QRegion->getAdapter()->quoteInto('parent = ?', $region_id);
        $region_search = $QRegion->fetchAll($where);

        $this->view->districts = array();

        foreach ($region_search as $_region)
        {
            $this->view->districts[$_region['id']] = $_region['name'];
        }
    }

    $this->view->goods             = $goods;
    $this->view->goodColors        = $goodColors;
    $this->view->markets_sn        = $markets_sn_array;
    $this->view->distributors      = $distributors;
    $this->view->distributors2     = $distributors2;
    $this->view->good_categories   = $good_categories;
    $this->view->warehouses_cached = $warehouses_cached;
    $this->view->staffs_cached     = $staffs_cached;
    $this->view->campaign          = $campaign;
    $this->view->distributor_ka    = $distributor_ka;

    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->url    = HOST . 'sales/' . ($params ? '?' . http_build_query($params) .
        '&' : '?');

    $this->view->offset = $limit * ($page - 1);

    $flashMessenger               = $this->_helper->flashMessenger;
    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;

    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    if ($this->getRequest()->isXmlHttpRequest())
    {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setRender('partials/list');
    }
