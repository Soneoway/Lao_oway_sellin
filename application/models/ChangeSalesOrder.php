<?php
class Application_Model_ChangeSalesOrder extends Zend_Db_Table_Abstract
{
	protected $_name = 'change_sales_order';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        //$select->joinInner(array('product' => 'change_sales_product'), 'p.id = product.changed_id', array('cat_id', 'good_id','good_color'));

        $select->joinLeft(array('csp' => 'change_sales_product'), 'p.id = csp.changed_id', array('total_qty_product'=>'SUM(csp.num)', 'total_qty_product_receive'=>'SUM(csp.receive)','cat_id', 'good_id','good_color'));

        $select->joinLeft(array('csl' => 'change_sales_list'), 'p.id = csl.changed_id', array('csl.detail','csl.note','csl.salesman_id'));

        $select->joinLeft(array('s' => 'staff'), 'p.delete_by = s.id', array('firstname', 'lastname'));

        $select->joinLeft(array('bow' => 'borrowing_list'), 'bow.id = p.borrowing_id', array('rq'));

        $select->group('p.changed_sn');

        if(in_array($userStorage->group_id,array(107,109))){
            $select->joinLeft(array('wh' => 'warehouse'),'wh.id = p.new_id',array());
            $select->where('wh.show_kerry =?',1);
        }


        if (isset($params['sn']) and $params['sn'])
            $select->where('p.changed_sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['warehouse_id1']) and $params['warehouse_id1'])
            $select->where('p.old_id LIKE ?', $params['warehouse_id1']);

        if (isset($params['warehouse_id2']) and $params['warehouse_id2'])
            $select->where('p.new_id LIKE ?', $params['warehouse_id2']);

        if (isset($params['distributor_id1']) and $params['distributor_id1'])
            $select->where('p.old_id LIKE ?', '%'.$params['distributor_id1'].'%');

        if (isset($params['distributor_id2']) and $params['distributor_id2'])
            $select->where('p.new_id LIKE ?', '%'.$params['distributor_id2'].'%');

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('csp.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('csp.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('csp.good_color = ?', $params['good_color']);


        if (isset($params['status']) and $params['status'] != '-1')
            $select->where('p.status = ?', $params['status']);

        if (isset($params['cancel']) and $params['cancel']){
            $select->where('p.delete_status = ?', $params['cancel']);
        }
        else{
            $select->where('p.delete_status is null',1);
        }

        if (isset($params['co_type']) and $params['co_type'] != '-1')
            $select->where('p.type = ?', $params['co_type']);

        //date
        if (isset($params['created_at_from']) and $params['created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.created_at) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.created_at) <= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['receive_at_from']) and $params['receive_at_from']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['receive_at_to']) and $params['receive_at_to']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) <= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['confirmed_at_from']) and $params['confirmed_at_from']){
            list( $day, $month, $year ) = explode('/', $params['confirmed_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.confirmed_out_at) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['confirmed_at_to']) and $params['confirmed_at_to']){
            list( $day, $month, $year ) = explode('/', $params['confirmed_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.confirmed_out_at) <= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['rq']) and $params['rq']){
            $select->where('bow.rq like ?', '%'.$params['rq'].'%');
        }

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str = 'p.`'.$params['sort'] . '` ' . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if($limit)
            $select->limitPage($page, $limit);
         // echo $select;die;
        $result = $db->fetchAll($select);

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchPaginationForDetail($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        //$select->joinInner(array('product' => 'change_sales_product'), 'p.id = product.changed_id', array('cat_id', 'good_id','good_color'));

        $select->joinLeft(array('csp' => 'change_sales_product'), 'p.id = csp.changed_id', array('total_qty_product'=>'csp.num', 'total_qty_product_receive'=>'csp.receive','cat_id', 'good_id','good_color'));

        $select->joinLeft(array('csl' => 'change_sales_list'), 'p.id = csl.changed_id', array('csl.detail','csl.note','csl.salesman_id'));
        
        // $select->joinLeft(array('csp' => 'change_sales_product'), 'p.id = csp.changed_id', array('cat_id', 'good_id','good_color'));

        $select->joinLeft(array('gcat' => 'good_category'), 'gcat.id = csp.cat_id', array('cat_name' =>'name'));

        $select->joinLeft(array('gd' => 'good'), 'gd.id = csp.good_id', array('good_name' =>'name'));

        $select->joinLeft(array('b' => 'brand'),'b.id = gd.brand_id',array('brand_name' => 'b.name'));

        $select->joinLeft(array('gcol' => 'good_color'), 'gcol.id = csp.good_color', array('color_name' =>'name'));

        $select->joinLeft(array('s' => 'staff'), 'p.delete_by = s.id', array('firstname', 'lastname'));

    // Add imei report list
        $select->joinLeft(array('ci' => 'change_sales_imei'), 'ci.changed_sales_product_id = csp.id ', array('imei_change_sales' => 'imei', 'total_price' => 'SUM(ci.price)','price' => 'ci.price'));

        if (isset($params['sn']) and $params['sn'])
            $select->where('p.changed_sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['warehouse_id1']) and $params['warehouse_id1'])
            $select->where('p.old_id LIKE ?', $params['warehouse_id1']);

        if (isset($params['warehouse_id2']) and $params['warehouse_id2'])
            $select->where('p.new_id LIKE ?', $params['warehouse_id2']);

        if (isset($params['distributor_id1']) and $params['distributor_id1'])
            $select->where('p.old_id LIKE ?', '%'.$params['distributor_id1'].'%');

        if (isset($params['distributor_id2']) and $params['distributor_id2'])
            $select->where('p.new_id LIKE ?', '%'.$params['distributor_id2'].'%');

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('csp.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('csp.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('csp.good_color = ?', $params['good_color']);


        if (isset($params['status']) and $params['status'] != '-1')
            $select->where('p.status = ?', $params['status']);

        if (isset($params['cancel']) and $params['cancel']){
            $select->where('p.delete_status = ?', $params['cancel']);
        }
        else{
            $select->where('p.delete_status is null',1);
        }

        if (isset($params['co_type']) and $params['co_type'] != '-1')
            $select->where('p.type = ?', $params['co_type']);

        //date
        if (isset($params['created_at_from']) and $params['created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.created_at) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.created_at) <= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['receive_at_from']) and $params['receive_at_from']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['receive_at_to']) and $params['receive_at_to']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) <= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['confirmed_at_from']) and $params['confirmed_at_from']){
            list( $day, $month, $year ) = explode('/', $params['confirmed_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.confirmed_out_at) >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['confirmed_at_to']) and $params['confirmed_at_to']){
            list( $day, $month, $year ) = explode('/', $params['confirmed_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.confirmed_out_at) <= ?', $year.'-'.$month.'-'.$day);
        }

        // Case For Export For Finance
        if(isset($params['for_finance']) and $params['for_finance']){
            $select->group('csp.id');
            // $select->group('ci.price');
            $select->group('csp.good_id');
            $select->group('csp.good_color');
        }

        $select->order('p.changed_sn DESC');


        if($limit)
            $select->limitPage($page, $limit);
         // echo $select;die;
        $result = $db->fetchAll($select);

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchPaginationWarrantyCO($params, &$totalCO){
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));           


        $select->joinLeft(array('csp' => 'change_sales_product'), 'p.id = csp.changed_id', array('total_qty_product'=>'SUM(csp.num)', 'total_qty_product_receive'=>'SUM(csp.receive)','cat_id', 'good_id','good_color','status'));
        
        $select->where('p.old_id  = ?', 6);
        $select->where('p.new_id  = ?', 98);
        $select->where('p.status  = ?', 4);

        if (isset($params['co']) and $params['co'])
            $select->where('p.changed_sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['co'].'%');

         //date
        if (isset($params['created_at_from_co']) and $params['created_at_from_co']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from_co']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to_co']) and $params['created_at_to_co']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to_co']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('date(p.completed_date) <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }
        
        $select->group('p.changed_sn');


        $result = $db->fetchAll($select);

        $totalCO = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }    

    function export_change_sales_order($data)
    {
        //$data = $this->check_imei_have_sales_out($warehouse_id,$good_id);
        //print_r($data);die;
      //  $this->_helper->layout->disableLayout();
    //    $this->_helper->viewRenderer->setNoRender(true);
        $QStaff = new Application_Model_Staff();
        $staff_cached = $QStaff->get_cache();

        $QDistributor = new Application_Model_Distributor();
        $distributor_cached = $QDistributor->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $warehouse_cached = $QWarehouse->get_cache();


        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_change_sales_order_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());

        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        //$path = $file_path.'/'.$filename;
        //$output = fopen($path, 'w+');
        //echo "\xEF\xBB\xBF"; // UTF-8 BOM

       echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
       $output = fopen('php://output', 'w');

       $statuses = unserialize(CHANGE_ORDER_STATUS);

       $heads = array(
        '#',
        'Changed SN',
        'Type',
        'SENT',
        'RECEIVED',
        'Status',
        'Old',
        'New',
        'Return to distributor name',
        'Created by',
        'created_at' => 'Created at',
        'completed_date' => 'Receive at',      
        'confirmed_out_at' => 'Confirmed out at',
        'salesman_id' => 'Sales Man',
        'detail' => 'Details',
        'note' => 'Note'
    );
       fputcsv($output, $heads);

       $i = 1;

       foreach($data as $item) 
       {
           $sn_ref = $item['sn_ref'];
           if($sn_ref==''){
            $sn_ref = $item['changed_sn'];
        }

        if($item['type']=="1"){
            $co_type="Normal";
        }else if($item['type']=="2"){
            $co_type="APK";
        }else if($item['type']=="5"){
            $co_type="DEMO";
        }

        $is_changed_wh = ( $item['is_changed_wh']==1 ? ('WH: ' . ( isset( $warehouse_cached[$item['old_id']] ) ? $warehouse_cached[$item['old_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['old_id']] ) ? $distributor_cached[$item['old_id']] : '' ) ) ) ;

        $is_changed_wh2 = ( $item['is_changed_wh']==1 ? ('WH: ' . ( isset( $warehouse_cached[$item['new_id']] ) ? $warehouse_cached[$item['new_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['new_id']] ) ? $distributor_cached[$item['new_id']] : '' ) ) ) ;

        $status =  (isset($statuses[ $item['status'] ]) ? $statuses[ $item['status'] ] : '');
        $created_by =  (isset($staff_cached[ $item['created_by'] ]) ? $staff_cached[ $item['created_by'] ] : '');

        $row = array();
        $row[] = $i;
        $row[] = $sn_ref;
        $row[] = $co_type;
        $row[] = $item['total_qty_product'];
        $row[] = $item['total_qty_product_receive'];
        $row[] = $status;
        $row[] = $is_changed_wh;
        $row[] = $is_changed_wh2;
        $row[] = $distributor_cached[$item['return_to_shop']];

        $row[] = $created_by;
        $row[] = $item['created_at'];
        $row[] = $item['completed_date'];
        $row[] = $item['confirmed_out_at'];
        $row[] = $item['salesman_id'];
        $row[] = $item['detail'];
        $row[] = $item['note'];
        fputcsv($output, $row);
        unset($item);
        unset($row);
        $i +=1;
    }

    unset($data);
    unset($result);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}

function export_change_sales_order_for_detail($data)
{
        //$data = $this->check_imei_have_sales_out($warehouse_id,$good_id);
        //print_r($data);die;
      //  $this->_helper->layout->disableLayout();
    //    $this->_helper->viewRenderer->setNoRender(true);
    $QStaff = new Application_Model_Staff();
    $staff_cached = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributor_cached = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouse_cached = $QWarehouse->get_cache();


    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'export_change_sales_order_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());

    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);

        //$path = $file_path.'/'.$filename;
        //$output = fopen($path, 'w+');
        //echo "\xEF\xBB\xBF"; // UTF-8 BOM

        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');


        $statuses = unserialize(CHANGE_ORDER_STATUS);

        $heads = array(
            '#',
            'Changed SN',
            'Type',
            'SENT',
            'RECEIVED',
            'Status',
            'Old',
            'New',
            'Return to shop name' => 'Return to shop name',
            'Created by',
            'created_at' => 'Created at',
            'completed_date' => 'Receive at',
            'confirmed_out_at' => 'Confitmed out at',
            'Category',
            'Product Name',
            'Color Name',
            'IMEI' => 'IMEI',
            'Cancel Or Not',
            'salesman_id' => 'Sales Man',
            'detail' => 'Details',
            'note' => 'Note'
        );
        fputcsv($output, $heads);

        $i = 1;
        
        foreach($data as $item) 
        {
           $sn_ref = $item['sn_ref'];
           if($sn_ref==''){
            $sn_ref = $item['changed_sn'];
        }

        if($item['type']=="1"){
            $co_type="Normal";
        }else if($item['type']=="2"){
            $co_type="APK";
        }else if($item['type']=="5"){
            $co_type="DEMO";
        }

        $is_changed_wh = ( $item['is_changed_wh']==1 ? ('WH: ' . ( isset( $warehouse_cached[$item['old_id']] ) ? $warehouse_cached[$item['old_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['old_id']] ) ? $distributor_cached[$item['old_id']] : '' ) ) ) ;

        $is_changed_wh2 = ( $item['is_changed_wh']==1 ? ('WH: ' . ( isset( $warehouse_cached[$item['new_id']] ) ? $warehouse_cached[$item['new_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['new_id']] ) ? $distributor_cached[$item['new_id']] : '' ) ) ) ;

        $status =  (isset($statuses[ $item['status'] ]) ? $statuses[ $item['status'] ] : '');
        $created_by =  (isset($staff_cached[ $item['created_by'] ]) ? $staff_cached[ $item['created_by'] ] : '');

        $row = array();
        $row[] = $i;
        $row[] = $sn_ref;
        $row[] = $co_type;
        $row[] = $item['total_qty_product'];
        $row[] = $item['total_qty_product_receive'];
        $row[] = $status;
        $row[] = $is_changed_wh;
        $row[] = $is_changed_wh2;
        $row[] = $distributor_cached[$item['return_to_shop']];

        $row[] = $created_by;
        $row[] = $item['created_at'];
        $row[] = $item['completed_date'];
        $row[] = $item['confirmed_out_at'];

        $row[] = $item['cat_name'];
        $row[] = $item['brand_name'].' '.$item['good_name'];
        $row[] = $item['color_name'];
        $row[] = $item['imei_change_sales'];

        $cancel = 'Not Cancel';
        if(isset($item['delete_status']) && $item['delete_status'] == 1){
            $cancel = 'Canceled';
        }
        $row[] = $cancel;

        $row[] = $item['salesman_id'];
        $row[] = $item['detail'];
        $row[] = $item['note'];

        fputcsv($output, $row);
        unset($item);
        unset($row);
        $i +=1;
    }

    unset($data);
    unset($result);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}


function export_change_Sales_for_fianace($data)
{

    $QStaff = new Application_Model_Staff();
    $staff_cached = $QStaff->get_cache();

    $QDistributor = new Application_Model_Distributor();
    $distributor_cached = $QDistributor->get_cache();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouse_cached = $QWarehouse->get_cache();
    $warehouse_cached2 = $QWarehouse->get_cache2();

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'Export_Change_Sales_Order_For_Finance_' . date('YmdHis') .'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());

    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/warehouse/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);

        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        $output = fopen('php://output', 'w');


        $statuses = unserialize(CHANGE_ORDER_STATUS);

        $heads = array(
            '#',
            'Changed SN',
            'Type',
            'SENT',
            'RECEIVED',
            'Status',
            'WH Code And Name ( OUT )',
            'WH Code And Name ( IN )',
            'Old(change out)',
            'New(change in)',
            'Return to shop name' => 'Return to shop name',
            'Created by',
            'created_at' => 'Created at',
            'completed_date' => 'Receive at',
            'confirmed_out_at' => 'Confitmed out at',
            'Category',
            'PRODUCT NAME（产品名称）',
            // 'Color Name',
            'Quality' => 'Quality',
            'Price',
            'Total Price' => 'Total Price',
            'Cancel Or Not',
            'salesman_id' => 'Sales Man',
            'detail' => 'Details',
            'note' => 'Note'
        );
        fputcsv($output, $heads);

        $i = 1;
        
        foreach($data as $item) 
        {
           $sn_ref = $item['sn_ref'];
           if($sn_ref==''){
            $sn_ref = $item['changed_sn'];
        }

        if($item['type']=="1"){
            $co_type="Normal";
        }else if($item['type']=="2"){
            $co_type="APK";
        }else if($item['type']=="5"){
            $co_type="DEMO";
        }

        $is_changed_wh = ( $item['is_changed_wh']==1 ? (( isset( $warehouse_cached[$item['old_id']] ) ? $warehouse_cached[$item['old_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['old_id']] ) ? $distributor_cached[$item['old_id']] : '' ) ) ) ;

        $is_changed_wh2 = ( $item['is_changed_wh']==1 ? (( isset( $warehouse_cached[$item['new_id']] ) ? $warehouse_cached[$item['new_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['new_id']] ) ? $distributor_cached[$item['new_id']] : '' ) ) ) ;

        $is_changed_wh3 = ( $item['is_changed_wh']==1 ? (( isset( $warehouse_cached2[$item['old_id']] ) ? $warehouse_cached2[$item['old_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['old_id']] ) ? $distributor_cached[$item['old_id']] : '' ) ) ) ;

        $is_changed_wh4 = ( $item['is_changed_wh']==1 ? (( isset( $warehouse_cached2[$item['new_id']] ) ? $warehouse_cached2[$item['new_id']] : '' ) ) : ( 'Retailer:' . ( isset( $distributor_cached[$item['new_id']] ) ? $distributor_cached[$item['new_id']] : '' ) ) ) ;

        $status =  (isset($statuses[ $item['status'] ]) ? $statuses[ $item['status'] ] : '');
        $created_by =  (isset($staff_cached[ $item['created_by'] ]) ? $staff_cached[ $item['created_by'] ] : '');

        $row = array();
        $row[] = $i;
        $row[] = $sn_ref;
        $row[] = $co_type;
        $row[] = $item['total_qty_product'];
        $row[] = $item['total_qty_product_receive'];
        $row[] = $status;

        $row[] = $is_changed_wh3;
        $row[] = $is_changed_wh4;

        $row[] = $is_changed_wh;
        $row[] = $is_changed_wh2;
        $row[] = $distributor_cached[$item['return_to_shop']];

        $row[] = $created_by;

            $created = '';
            if($item['created_at']) {
                $created = date('Y-m-d', strtotime($item['created_at']));
            }

            $completed = '';
            if($item['completed_date']) {
                $completed = date('Y-m-d', strtotime($item['completed_date']));
            }

            $confirmed_out = '';
            if($item['confirmed_out_at']) {
                $confirmed_out = date('Y-m-d',strtotime($item['confirmed_out_at']));
            }

        $row[] = $created;
        $row[] = $completed;
        $row[] = $confirmed_out;

        $row[] = $item['cat_name'];
        $row[] = $item['brand_name'].' '.$item['good_name'].' '.$item['color_name'];
        // $row[] = $item['color_name'];
        $row[] = $item['total_qty_product_receive'];
        $row[] = number_format($item['price'],0);
        $row[] = number_format($item['total_price'],0);


        $cancel = 'Not Cancel';
        if(isset($item['delete_status']) && $item['delete_status'] == 1){
            $cancel = 'Canceled';
        }
        $row[] = $cancel;

        $row[] = $item['salesman_id'];
        $row[] = $item['detail'];
        $row[] = $item['note'];

        fputcsv($output, $row);
        unset($item);
        unset($row);
        $i +=1;
    }

    unset($data);
    unset($result);

    fclose($output);

    ob_flush();
    ob_start();
    while (@ob_end_flush());

    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;

    $file = fopen($path, 'r');
    $content = fread($file, filesize($path));
    var_dump(filesize($path));
    var_dump($content);

    exit;
}

function getDataBorrowingByID($id){

    $db = Zend_Registry::get('db');

    $select = $db->select()
    ->from(array('cso' => $this->_name), array('cso.*'));
    $select->joinLeft(array('bl' => 'borrowing_list'), 'bl.id = cso.borrowing_id', array('bl.*'));
    $select->joinLeft(array('hrs' => 'oppohr.oppo_staff'), 'hrs.staff_code = bl.code', array("TRIM(CONCAT(hrs.firstname_th,' ',hrs.lastname_th))AS fullname", "tel_number"));

    $select->where('cso.id = ?',$id);

        // echo $select;
    $result = $db->fetchRow($select);
    return $result;

}

function getDetailDeliveryByCo($co){

    $db = Zend_Registry::get('db');

    $select = $db->select()->from(array('p' => $this->_name), array('p.*','sn' => 'p.changed_sn'));

    $select->joinLeft(array('csp' => 'change_sales_product'), 'p.id = csp.changed_id', array('total_qty_product'=>'csp.num', 'total_qty_product_receive'=>'csp.receive','cat_id', 'good_id','good_color'));
    $select->joinLeft(array('gcat' => 'good_category'), 'gcat.id = csp.cat_id', array('cat_name' =>'name'));

    $select->joinLeft(array('gd' => 'good'), 'gd.id = csp.good_id', array('good_name' =>'name'));

    $select->joinLeft(array('gcol' => 'good_color'), 'gcol.id = csp.good_color', array('color_name' =>'name'));

    $select->joinLeft(array('s_created' => 'staff'), 'p.created_by = s_created.id', array("TRIM(CONCAT(s_created.firstname,' ',s_created.lastname))AS fullname_created"));
    $select->joinLeft(array('s_scanned_out' => 'staff'), 'p.scanned_out_by = s_scanned_out.id', array("TRIM(CONCAT(s_scanned_out.firstname,' ',s_scanned_out.lastname))AS fullname_scannedout"));
    $select->joinLeft(array('s_confirmed_out' => 'staff'), 'p.confirmed_out_by = s_confirmed_out.id', array("TRIM(CONCAT(s_confirmed_out.firstname,' ',s_confirmed_out.lastname))AS fullname_confirmedout"));
    $select->joinLeft(array('s_scanned_in' => 'staff'), 'p.scanned_in_by = s_scanned_in.id', array("TRIM(CONCAT(s_scanned_in.firstname,' ',s_scanned_in.lastname))AS fullname_scannedin"));
    $select->joinLeft(array('s_completed' => 'staff'), 'p.completed_user = s_completed.id', array("TRIM(CONCAT(s_completed.firstname,' ',s_completed.lastname))AS fullname_completed"));

    $select->joinLeft(array('w_old' => 'warehouse'), 'w_old.id = p.old_id', array('from_warehouse' =>'w_old.name'));
    $select->joinLeft(array('w_new' => 'warehouse'), 'w_new.id = p.new_id', array('to_warehouse' =>'w_new.name'));

    $select->where('p.sn_ref = ?',$co);
        $select->where('p.status = ?','2');//on change

        // echo $select;die;
        $result = $db->fetchRow($select);
        return $result;

    }

    function getImeiCo($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('cso' => $this->_name), array('csi.imei'));
        $select->joinLeft(array('csp' => 'change_sales_product'), 'csp.changed_id = cso.id', array());
        $select->joinLeft(array('csi' => 'change_sales_imei'), 'csi.changed_sales_product_id = csp.id', array());

        $select->where('cso.id = ?',$id);

        // echo $select;
        $result = $db->fetchAll($select);
        return $result;

    }

    function getCoBySn($sn){
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name), array('*'));

        $select->where('p.changed_sn = ?',$sn);

        // echo $select;
        $result = $db->fetchRow($select);
        return $result;
    }
}
