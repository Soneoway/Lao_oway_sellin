<?php
class ToolController extends My_Controller_Action
{   

    ##------------------imei Lot print---------------------##
    public function imeiLotAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'index-imei-lot.php';
    }
    public function addImeiLotAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'add-imei-lot.php';
    }

    public function cornStockAction(){
        require_once 'tools' . DIRECTORY_SEPARATOR . 'corn.php';
    }
    public function delImeiLotAction()
    {
        $id = $this->getRequest()->getParam('id');

        $imeilot = new Application_Model_ImeiLot();
        $where = $imeilot->getAdapter()->quoteInto('lot_sn = ?', $id);
        $update = array(
          'status_imei' => 0
      );
        $imeilot->update($update,$where);
        
        $this->_redirect('/tool/imei-lot');

    }

    public function printImeiLotAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-imei-lot.php';
    }

    public function adjustImeiAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR. 'adjust-imei' . DIRECTORY_SEPARATOR . 'adjust-imei.php';
    }

    // Runing Activate Date Time

    public function checkActivateAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'checkActivate.php';
    }

    ##-------------------------------------------###

    public function epPrivilegesPositionListAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'ep-privileges-position-list.php';
    }

    public function epPrivilegesPositionCreateAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'ep-privileges-position-create.php';
    }

    public function epPrivilegesPositionSaveAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'ep-privileges-position-save.php';
    }

    public function newQuotaManageDistributorAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'new-quota-manage-distributor.php';
    }
    public function quotaManageAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'quota-manage.php';
    }
    public function quotaManageDistributorAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'quota-manage-distributor.php';
    }
    public function testAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'test.php';
    }
    public function addQuotaDistributorAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'add-quota-distributor.php';
    }
    public function newAddQuotaDistributorAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'new-add-quota-distributor.php';
    }
    public function newImportQuotaDistributorAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'new-import-quota-distributor.php';
    }
    public function materailInOutReportAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'materail-in-out-report.php';
    }
    private $distributor_service = array(8059, 2124, 2123, 2125, 2126, 2127, 8805, 8884);

    function exportAction(){

        set_time_limit(0);

        error_reporting(0);

        ini_set('display_error', 0);

        $QGood = new Application_Model_Good();

        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
        $goods = $QGood->fetchAll($where, 'desc');
        $this->view->goods = $goods;

        $date = array();

        $QTiming = new Application_Model_Market();
        foreach ($goods as $good){
            for ($i=1; $i<31; $i++){
                $tm = ($i<10 ? '0' : '').$i;
                $sel = $QTiming->count_out_imei2('2014-06-'.$tm, '2014-06-'.$tm, $good->id);
                $date[$i][$good->id] = $sel;
            }
        }


        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            '',
        );

        for ($i=1; $i<31; $i++){
            $tm = ($i<10 ? '0' : '').$i;
            $heads[] = '2014-06-'.$tm;
        }

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();

        $alpha    = 'A';
        $index    = 1;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;


        foreach($goods as $item){
            $alpha    = 'A';
            $sheet->setCellValue($alpha++.$index, $item['desc']);


            for ($i=1; $i<31; $i++){

                if (isset($date[$i][$item['id']]) and $date[$i][$item['id']])
                    $sheet->setCellValue($alpha++.$index, $date[$i][$item['id']]);
                else
                    $sheet->setCellValue($alpha++.$index, 0);

            }

            $index++;
        }

        $filename = 'SELL_IN_'.date('d/m/Y H:i:s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;

    }

    function export2Action(){

        set_time_limit(0);

        error_reporting(0);

        ini_set('display_error', 0);

        $QGood = new Application_Model_Good();

        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
        $goods = $QGood->fetchAll($where, 'desc');
        $this->view->goods = $goods;

        $date = $data = array();

        $start = '2014-04-01';

        while (strtotime($start)<strtotime('2014-06-30')){
            $date[] = array(
                $start, date('Y-m-d', strtotime('+6 days', strtotime($start)))
            );
            $start = date('Y-m-d', strtotime('+1 week', strtotime($start)));
        }

        $QTiming = new Application_Model_Market();
        foreach ($goods as $good){
            foreach ($date as $d){
                $sel = $QTiming->count_out_imei2($d[0], $d[1], $good->id);
                $data[$d[0]][$good->id] = $sel;
            }
        }


        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            '',
        );

        foreach ($date as $d){
            $heads[] = $d[0] . ' - '. $d[1];
        }

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();

        $alpha    = 'A';
        $index    = 1;
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;


        foreach($goods as $item){
            $alpha    = 'A';
            $sheet->setCellValue($alpha++.$index, $item['desc']);


            foreach ($date as $d){

                if (isset($data[$d[0]][$item['id']]) and $data[$d[0]][$item['id']])
                    $sheet->setCellValue($alpha++.$index, $data[$d[0]][$item['id']]);
                else
                    $sheet->setCellValue($alpha++.$index, 0);

            }

            $index++;
        }

        $filename = 'SELL_IN_WEEK_'.date('d/m/Y H:i:s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');

        exit;

    }

    public function imeiAction(){

    }

    public function bvgAction(){

    }

    public function poAction(){

    }

    public function imeiEditAction()
    {
        require_once 'tools'.DIRECTORY_SEPARATOR.'imei-edit'.DIRECTORY_SEPARATOR.'list.php';
    }

    public function imeiEditCreateAction()
    {
        require_once 'tools'.DIRECTORY_SEPARATOR.'imei-edit'.DIRECTORY_SEPARATOR.'create.php';
    }

    public function imeiEditSaveAction()
    {
        require_once 'tools'.DIRECTORY_SEPARATOR.'imei-edit'.DIRECTORY_SEPARATOR.'save.php';
    }

    ##------------------dashbord---------------------###

    public function dashboardAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'dashboard.php';
    }
    public function olddataAction()
    {
        require_once 'tools' .DIRECTORY_SEPARATOR. 'index-old-data.php';
    }

    public function addimeiolddataAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'add_imei_olddata.php';
    }

    public function unsetAction()
    {
        $id = $this->getRequest()->getParam('id');
        $imei = $this->getRequest()->getParam('imei');
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();

        $Qimei      = new Application_Model_Imei();
        $where = $Qimei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $update = array(
          'old_data' => null,
      );
        $Qimei->update($update,$where);
        
        $this->_redirect('/tool/olddata');

    }
    public function unset2Action()
    {
       $list_imei = $this->getRequest()->getParam('imei');
       foreach ($list_imei as $id) {
           $Qimei      = new Application_Model_Imei();
           $where = $Qimei->getAdapter()->quoteInto('imei_sn = ?', $id);
           $update = array(
             'old_data' => null,
         );
           $Qimei->update($update,$where);
       }
       $this->_redirect('/tool/olddata');

   }
    ##------------------lock imei---------------------##
   public function lockimeiAction()
   {
    require_once 'tools' . DIRECTORY_SEPARATOR . 'index-lock-imei.php';
}
public function addLockimeiAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'add-lock-imei.php';
}
public function delAction()
{
        // $id = $this->getRequest()->getParam('id');
    $imei = $this->getRequest()->getParam('imei');

    $lockimei = new Application_Model_LockImei();
    $where = $lockimei->getAdapter()->quoteInto('imei_log = ?', $imei);
        // $update = array(
        //           'status_imei' => 0
        // );
    $lockimei->delete($where);

    $this->_redirect('/tool/lockimei');

}
public function del2Action()
{
    $list_id = $this->getRequest()->getParam('id');

    foreach ($list_id as $id) {
        $lockimei = new Application_Model_LockImei();
        $where = $lockimei->getAdapter()->quoteInto('id = ?', $id);
        $update = array(
          'status_imei' => 0
      );
        $lockimei->update($update,$where);
    }
    $this->_redirect('/tool/lockimei');

}
    ##-------------------------------------------###


public function trackingAction(){
   if(!empty($this->getRequest()->getParam('tracking_key'))){
       $this->view->tracking_key = $this->getRequest()->getParam('tracking_key');
       $this->view->check_by = $this->getRequest()->getParam('check_by');
       if($this->view->check_by == 'imei'){

        try{
            $ImeiInfo = new Application_Model_Imei();
            $this->view->info      = $info = $ImeiInfo->getImeiRecord($this->view->tracking_key);

            $warehouse = new Application_Model_Warehouse();
            $this->view->warehouse = $warehouse->getWarehouseRecord($info['warehouse_id']);

            $good = new Application_Model_Good();
            $this->view->good_info = $good->getGoodRecord($info['good_id']);

            $good_color = new Application_Model_GoodColor();
            $this->view->good_color_info = $good_color->getGoodRecord($info['good_color']);

            $market = new Application_Model_Market();
            $this->view->market = $market->getRecordBySN($info['sales_sn']);
        }catch (Exception $e) {
            echo $e;
        }

        if($info['status'] == '1'){
            $this->view->exists = 'YES';
        }else{
            $this->view->exists = 'NO';
        }

        $distributor = new Application_Model_Distributor();
        if($info['distributor_id'] != '' && $info['sales_sn'] != '' && $info['out_price'] > 0.00 ){
            $this->view->distributor = $distributor->getDistributorRecord($info['distributor_id']);
            $this->view->distributor = $this->view->distributor['title'];
        }else{
            $this->view->distributor = 'N/A';
        }

        $key = $this->view->tracking_key;
        $k   = $distributor->getStoreRecord($key);
        if($k){
            $store_info = $k['store_name']." | ";
            $store_info .= $k['sold_by']." | ";
            $store_info .= $k['sold_on'];
            $this->view->store = $store_info;
        }else{
            $this->view->store = 'N/A';
        }
    }
}
}


public function rangeInvoiceAction()
{
    $sort           = $this->getRequest()->getParam('sort', 'add_time');
    $desc           = $this->getRequest()->getParam('desc', 1);
    $page           = $this->getRequest()->getParam('page', 1);

    $type           = $this->getRequest()->getParam('type');
    $service_id     = $this->getRequest()->getParam('service_id');
    $warehouse_id   = $this->getRequest()->getParam('warehouse_id');


    $export  = $this->getRequest()->getParam('export', 0);

    $limit = NULL;
    $total = 0;

    $params = array(
        'type'              => $type,
        'service_id'        => $service_id,
        'warehouse_id'      => $warehouse_id
    );

    $QRange          = new Application_Model_Range();
    $QService        = new Application_Model_Service();
    $ranges = $QRange->fetchPagination($page, $limit, $total, $params);



    $this->view->ranges = $ranges;
    $service         = $QService->get_cache_service();
    $QWarehouse      = new Application_Model_Warehouse();
    $warehouse       = $QWarehouse->get_cache();

    $params['sort'] = $sort;
    $params['desc'] = $desc;

    $this->view->service = $service;
    $this->view->warehouse = $warehouse;
    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->url    = HOST.'tool/range-invoice/'.( $params ? '?'.http_build_query($params).'&' : '?' );
    $this->view->offset = $limit*($page-1);

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    if($this->getRequest()->isXmlHttpRequest()) {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setRender('partials/list');
    }

}

public function rangeCreateAction()
{
    $QRange = new Application_Model_Range();
    $id     = $this->getRequest()->getParam('id');

    $rangeRowset = $QRange->find($id);
    $range       = $rangeRowset->current();
    $QService    = new Application_Model_Service();
    $service     = $QService->get_cache_service();

    $QInvoicePrefix = new Application_Model_InvoicePrefix();
    $QRangeHistory  = new Application_Model_RangeHistory();
    $whereInvoicePrefix = array();
    $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type = ?' , 2);
    $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);

    $invoice_prefix = $QInvoicePrefix->fetchAll($whereInvoicePrefix);

    if(isset($invoicePrefix) and $invoicePrefix)
        $invoice_prefix = $invoice_prefix->toArray();

    $QWarehouse = new Application_Model_Warehouse();
    $warehouse = $QWarehouse->get_cache();

    if ($this->getRequest()->getMethod() == 'POST'){
        $flashMessenger = $this->_helper->flashMessenger;
        try{
            $object_type = $this->getRequest()->getParam('object_type');
            $object_id   = $this->getRequest()->getParam('service');
            $total       =  trim($this->getRequest()->getParam('quantity'));
            $first_input = $this->getRequest()->getParam('from');
            $last_input  = $this->getRequest()->getParam('to');
            $invoice_prefix = $this->getRequest()->getParam('invoice_prefix');

            $currentTime = date('Y-m-d H:i:s');
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $user_id     = $userStorage->id;


            if(empty($total) || empty($first_input) || empty($last_input))
               throw new exception ('Invalid input');

           if($last_input < $first_input)
            throw new exception ('Invalid input');

        $data = array(
            'object_id'   => $object_id,
            'object_type' => $object_type,
            'total'       => $total,
            'first_input' => $first_input,
            'last_input'  => $last_input,
            'status'      => 1,
            'invoice_prefix' => $invoice_prefix,
            'object_type' => 2
        );

        if($range)
        {
            $where = $QRange->getAdapter()->quoteInto('id = ? '  , $id);
            $QRange->update($data, $where);

                    //thêm range history
            $data = array(
                'object_id'      => $object_id,
                'object_type'    => $object_type,
                'total'          => $total,
                'first_input'    => $first_input,
                'last_input'     => $last_input,
                'add_time'       => $currentTime,
                'add_by'         => $user_id,
                'invoice_prefix' => $invoice_prefix

            );

            $QRangeHistory->insert($data);
        }
        else
        {
            $QRange->insert($data);
        }

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'tool/range-invoice');

    }
    catch(exception $e)
    {
        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST.'tool/range-invoice');
    }
}




$this->view->invoice_prefix = $invoice_prefix;
$this->view->service        = $service;
$this->view->warehouse      = $warehouse;
$this->view->range          = $range;
}

public function soAction()
{

}



public function financialAction()
{
    $from = $this->getRequest()->getParam('from');
    $to   = $this->getRequest()->getParam('to');

    $export_distributor = $this->getRequest()->getParam('export_distributor', 0);
    $export_warehouse   = $this->getRequest()->getParam('export_warehouse', 0);
    $type               = $this->getRequest()->getParam('type', 1);

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter( array(
        'from' => $from,
        'to'   => $to,
        'type' => $type,
    ));

    if ($export_distributor == 1) {
        $this->_export_distributor($params);

        exit;
    }

    if ($export_warehouse == 1) {
        $this->_export_warehouse($params);

        exit;
    }
}

public function satCheckAction(){
    if ($this->getRequest()->getMethod() == 'POST'){


        $date = $this->getRequest()->getParam('date');


        $end = strtotime( $date.' 00:00:00' );

        $i=0;

        $result = false;

        $start = $first = strtotime( '2014-03-15 00:00:00' );

        while ($start < $end){


            $start = strtotime("+$i week", $first);


            if (date('Y-m-d', $start) == date('Y-m-d', $end)){
                $result = true;
                break;
            }


            $i+=2;
        }


        $this->view->result = $result;
        $this->view->date = $date;
    }
}

public function sqlAction(){
    $flashMessenger = $this->_helper->flashMessenger;

    if ($this->getRequest()->getMethod() == 'POST'){

        $sql = $this->getRequest()->getParam('sql');

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con,"utf8");

        try {

            mysqli_multi_query($con,$sql);

            $error = mysqli_error($con);
            if ($error)
                $flashMessenger->setNamespace('error')->addMessage($error);
            else
                $flashMessenger->setNamespace('success')->addMessage('Done!');

        } catch (Exception $e){
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }



        $this->_redirect('/tool/sql');
    }

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $messages_success = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;
}

    // AJAX func
    // Check IMEI every time user input IMEI to IMEI OUT WH textarea by barcode scanner
public function checkImeiOutAction()
{
    if(!$this->getRequest()->isXmlHttpRequest()) {
        $this->_redirect( ( HOST.'warehouse/out' ) );
    } else {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $imei = $this->getRequest()->getParam('imei');

            if (!$imei) { // kiểm có hay ko có dữ liệu gửi lên
                echo -1;
                exit;
            } elseif (!preg_match('/^[a-zA-Z0-9]{13,17}$/', $imei)) { // kiểm format
                echo -2;
                exit;
            } else { // kiểm trong bảng IMEI
                $QImei = new Application_Model_Imei();

                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $imeis = $QImei->fetchRow($where);

                if (!$imeis || count($imeis) == 0) {
                    echo 0;
                    exit;
                } else {
                    // check is return
                    if (!$imeis['return_sn'] || $imeis['return_sn'] == '') {
                        echo 1;
                        exit;
                    } else {
                        $QImeiReturn = new Application_Model_ImeiReturn();
                        $where = array();
                        $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $imeis['return_sn']);
                        $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $imei);
                        $return = $QImeiReturn->fetchRow($where);

                        if ( !$return || $return['back_sale'] == 1 ) {
                            echo 1;
                            exit;
                        } else {
                            echo -3;
                            exit;
                        }
                    }
                }
            }

            exit;
        }
    }


    // AJAX func
    // Check IMEI status/info by IMEI tool
    public function checkImeiAction()
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect( ( HOST.'tool/imei' ) );
        } else {
            $imei = trim($this->getRequest()->getParam('value', ''));
            $imei = explode("\n", $imei);

            if (count($imei) == 1 && $imei[0] == '') {
                echo 'No Input Data';
                exit;
            }


            if (is_array($imei)) {
                $limit = LIMITATION*5;

                $bk_imei = $imei;

                $imei = array_values(array_unique($imei));

                $page = $this->getRequest()->getParam('page', 1);

                // $imei = array_unique($imei);
                $total = count($imei);
                $offset = ($page - 1) * $limit;
                $imei = array_slice($imei, $offset, $limit);

                $result = array();

                $QGood = new Application_Model_Good();
                $goods = $QGood->get_cache();

                $QGoodColor = new Application_Model_GoodColor();
                $good_colors = $QGoodColor->get_cache();

                foreach ($imei as $key => $value) {
                    $info = array();
                    $return = $this->checkImei(trim($value), $info);

                    if ($return == -1) {
                        $result[$value] = 'wrong format';
                    } elseif ($return == -2) {
                        $result[$value] = 'not exists';
                    } else {
                        $result[$value] = $info;

                    }
                }

                $QStaff = new Application_Model_Staff();
                $this->view->staffs = $QStaff->get_cache();

                $QDistributor = new Application_Model_Distributor();
                $this->view->distributors = $QDistributor->get_cache2();

                $QGood = new Application_Model_Good();
                $this->view->goods = $QGood->get_cache();

                $QGoodColor = new Application_Model_GoodColor();
                $this->view->good_colors = $QGoodColor->get_cache();

                $QWarehouse = new Application_Model_Warehouse();
                $this->view->warehouses = $QWarehouse->get_cache();
                //Tanong
                //print_r($result);
                $this->view->url = HOST.'tool/imei'.'?';
                $this->view->offset = $offset;
                $this->view->limit  = $limit;
                $this->view->total  = $total;
                $this->view->page   = $page;
                $this->view->res    = $result;

                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setRender('partials/imei');

            }
        }
    }



    // AJAX func
    // Check BVG status/info by IMEI tool
    public function checkBvgAction()
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect( ( HOST.'tool/imei' ) );
        } else {
            $imei = trim($this->getRequest()->getParam('value', ''));
            $imei = explode("\n", $imei);

            if (count($imei) == 1 && $imei[0] == '') {
                echo 'No Input Data';
                exit;
            }


            if (is_array($imei)) {
                $limit = LIMITATION*5;


                $page = $this->getRequest()->getParam('page', 1);

                // $imei = array_unique($imei);
                $total = count($imei);
                $offset = ($page - 1) * $limit;
                $imei = array_slice($imei, $offset, $limit);

                $result = array();

                $QGood = new Application_Model_Good();
                $goods = $QGood->get_cache();

                $QGoodColor = new Application_Model_GoodColor();
                $good_colors = $QGoodColor->get_cache();

                foreach ($imei as $key => $value) {
                    $info = array();
                    $return = $this->checkBvg(trim($value), $info);

                    if ($return == -1) {
                        $result[$value] = 'wrong format';
                    } elseif ($return == -2) {
                        $result[$value] = 'not exists';
                    } else {
                        $result[$value] = $info;

                    }
                }

                $QStaff = new Application_Model_Staff();
                $this->view->staffs = $QStaff->get_cache();

                $QDistributor = new Application_Model_Distributor();
                $this->view->distributors = $QDistributor->get_cache2();

                $QGood = new Application_Model_Good();
                $this->view->goods = $QGood->get_cache();

                $QGoodColor = new Application_Model_GoodColor();
                $this->view->good_colors = $QGoodColor->get_cache();

                $QWarehouse = new Application_Model_Warehouse();
                $this->view->warehouses = $QWarehouse->get_cache();
                //Tanong
                //print_r($result);
                $this->view->url = HOST.'tool/bvg'.'?';
                $this->view->offset = $offset;
                $this->view->limit  = $limit;
                $this->view->total  = $total;
                $this->view->page   = $page;
                $this->view->res    = $result;

                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setRender('partials/bvg');

            }
        }
    }

    public function exportExcelAction()
    {
        $imei = trim($this->getRequest()->getParam('imei', ''));
        $imei = explode("\n", $imei);

        if (count($imei) == 1 && $imei[0] == '') {
            echo 'No Input Data';
            exit;
        }

        $bk_imei = $imei;

        $imei = array_values(array_unique($imei));

        if (is_array($imei)) {

            ini_set("memory_limit", "-1");

            set_time_limit(0);

            error_reporting(0);

            ini_set('display_error', 0);


            $filename = 'List_Imei_Checked_Export_'.date('d/m/Y');

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            $heads = array(
                'Imei SN',
                'Model',
                'Color',
                'Out Pirce',
                // 'Order Type',
                'Product Type',
                'Store Code',
                'Store Name',
                'Distributor Code',
                'Distributor Name',
                'Area',
                'Provience',
                'Warehouse',
                'SO',
                'IN Date',
                'OUT Date',
                'Activated Date',
                'Purchase SN',
                'Sales SN',
                'Invoice Number',
                'CN For return',
                'Invoice Date',
                // 'Status',
                'Change ORDER SN',
                // 'AREA',
                // 'FINANCE GROUP',
                // 'O-GUARD'
            );

            fputcsv($output, $heads);

            $QArea = new Application_Model_Area();

            $QGood = new Application_Model_Good();
            $good = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $good_color = $QGoodColor->get_cache();

            $QDistributor    = new Application_Model_Distributor();
            $distributor =  $QDistributor->get_cache();
            $distributor_cache = $QDistributor->get_cache2();

            $QWarehouse    = new Application_Model_Warehouse();
            $warehouses =   $QWarehouse->get_cache();

            $QMarket = new Application_Model_Market();
            $QBvgSellOut = new Application_Model_BvgSelloutImei();

            $QRegionalmarket = new Application_Model_RegionalMarket();
            $regional_market = $QRegionalmarket->get_cache();

            $QJoint_circular = new Application_Model_JointCircular();
            $joint = $QJoint_circular->fetchAll();
            $joint_array = array();
            foreach($joint as $k=>$v)
            {
                $joint_array[$v['id']] = $v['name'];
            }

            $grand_e1 = array(81,82,83,110,111,112);
            $grand_e2 = array(85,86,87,115,88,89,116,117);
            $grand_e3 = array(90,91,92,93,113);
            $grand_e4 = array(94,95,96);
            $grand_e5 = array(97,109);
            $grand_w1 = array(98,99,100,101,102,114);
            $grand_w2 = array(103,104,105);
            $grand_w3 = array(106,107,108);

            foreach ($imei as $value) {
                $value = trim($value);
                $info = array();
                
                $return = $this->checkImei($value, $info);
                $O_GUARD = "";
                $O_GUARD = $this->checkImei_O_GUARD(trim($value));
                $row = array();

                $row[] = $value;

                $m = '';
                switch ($return){
                    case -1:
                    $m = 'wrong format';
                    break;
                    case -2:
                    $m = 'not exists';
                    break;
                }

                $market = [];
                $sn_ref = '';
                $order_type = '';
                $excel_area_name = '';
                $excel_area_id = '';
                $grand_area = '';
                $excel_area_name = '';

                $invoice_number = $invoice_time = $special_discount = $price_product_relative = $price_export_relative = 0;
                if ( isset($info['imei']) ) {
                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('id = ?', $info['imei']['sales_id']);
                    $market  = $QMarket->fetchRow($where);

                    if ($market) {
                        $sn_ref                 = $market['sn_ref'];
                        $invoice_number         = $market['invoice_number'];
                        $invoice_time           = $market['invoice_time'];
                        $special_discount       = $market['spc_discount'];
                        $price_product_relative = $market['price'];

                        if(isset($market['sale_off_percent']) and $market['sale_off_percent'] > 0){
                        }

                        $price_export_relative  = intval($market['total'] / $market['num']) ? intval($market['total'] / $market['num']) : 0;
                        switch ( $market['type'] ) {
                            case '1':
                            $order_type = 'Retailer';
                            break;
                            case '2':
                            $order_type = 'APK';
                            break;
                            case '3':
                            $order_type = 'Staff';
                            break;
                            case '5':
                            $order_type = 'DEMO';
                            break;    
                            default:

                            break;
                        }

                        $excel_area_name = My_Region::getValue($distributor_cache[$market['d_id']]['district'], My_Region::Area);

                        $excel_area_id = My_Region::getValue($distributor_cache[$market['d_id']]['district'], My_Region::Area, My_Region::ID);

                        if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
                        else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
                        else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
                        else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
                        else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
                        else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
                        else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
                        else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
                        else { $grand_area = $excel_area_name; }   
                    }
                }

                $QStore = new Application_Model_Store();
                
                $where1 = $QStore->getAdapter()->quoteInto('id = ?',$info['imei']['store_id']);
                $store_detials = $QStore->fetchRow($where1);

                $where = $QDistributor->getAdapter()->quoteInto('id = ?',$info['imei']['distributor_id']);
                $distributor_data = $QDistributor->fetchRow($where);

                $where2 = $QRegionalmarket->getAdapter()->quoteInto('id =?',$distributor_data['region']);
                $regional_market_data = $QRegionalmarket->fetchRow($where2);

                $where3 = $QArea->getAdapter()->quoteInto('id = ?',$regional_market_data['area_id']);
                $area_data = $QArea->fetchRow($where3);



                // print_r($distributor_data); exit;



                $percent = $special_discount*0.01;

                switch ($special_discount) {
                    case '1':
                    $percent = 0.01;
                    break;
                    case '2':
                    $percent = 0.02;
                    break;
                    case '3':
                    $percent = 0.03;
                    break;
                    case '5':
                    $percent = 0.05;
                    break;
                }

                $sum_bvg_price = $info['sum_bvg_price'];
                $sum_special_discount = number_format(($price_product_relative)*$percent,2);


                $good_name = ( isset($info['imei']['good_id']) and isset($good[$info['imei']['good_id']]) ) ? $good[$info['imei']['good_id']] : $m;

                $row[] = $good_name;

                $good_color_name = ( isset($info['imei']['good_color']) and isset($good_color[$info['imei']['good_color']]) ) ? $good_color[$info['imei']['good_color']] : '';
                $row[] = $good_color_name;

                $row[] = isset($price_product_relative) ? $price_product_relative : '';

                // $row[] = isset($price_product_relative) ? $price_product_relative-$sum_special_discount-$sum_bvg_price : '';


                if(isset($info['imei']['type']))
                {
                    switch ( $info['imei']['type'] ) {
                        case '1':
                        $imei_type = 'Normal';
                        break;
                        case '3':
                        $imei_type = 'Staff';
                        break;
                        case '5':
                        $imei_type = 'DEMO';
                        break;    
                        default:

                        break;
                    }

                }
                $row[] = isset($imei_type) ? $imei_type : '';

                $row[] = $store_detials->store_code;  // Store Code
                $row[] = $store_detials->name;  // Store Name

                $distributor_name = ( isset($info['imei']['distributor_id']) and isset($distributor[$info['imei']['distributor_id']]) ) ? $distributor[$info['imei']['distributor_id']] : '';
                $row[] = $distributor_cache[$info['imei']['distributor_id']]['d_code'];
                $row[] = $distributor_cache[$info['imei']['distributor_id']]['title'];

                $row[] = $area_data['name'];
                $row[] = $regional_market[$distributor_data['region']];

                $imei2 = ( isset($info['imei']['warehouse_id']) and isset($warehouses[$info['imei']['warehouse_id']]) ) ? $warehouses[$info['imei']['warehouse_id']] : '';
                $row[] = $imei2;

                $row[] = isset($sn_ref) ? $sn_ref : 'x';

                $row[] = isset($info['imei']['into_date']) ? $info['imei']['into_date'] : '';
                $row[] = isset($info['imei']['out_date']) ? $info['imei']['out_date'] : '';
                $row[] = isset($info['imei']['activated_date']) ? $info['imei']['activated_date'] : '';
                $row[] = isset($info['imei']['po_sn']) ? ('="'.$info['imei']['po_sn'].'"') : '';
                $row[] = isset($info['imei']['sales_sn']) ? ('="'.$info['imei']['sales_sn'].'"') : '';

                $row[] = isset($invoice_number) ? $invoice_number : 'x';
                $row[] = $info['return']['creditnote_sn'];

                $invoice_time = date('d/m/Y H:i:s', strtotime($invoice_time) );
                $row[] = isset($invoice_time) ? $invoice_time : 'x';


                // $row[] = $status;


                $QImei = new Application_Model_Imei();
                $getCo = $QImei->getCoByImei($value);

                $row[] = isset($getCo['sn_ref']) ? $getCo['sn_ref'] : 'x';
                // $row[] = $excel_area_name;
                // $row[] = $distributor_cache[$info['imei']['distributor_id']]['finance_group'];
                // $row[] = $O_GUARD;
                fputcsv($output, $row);
            }


        }

        exit;
    }

    public function exportExcelKarnAction()
    {
        $imei = trim($this->getRequest()->getParam('imei', ''));
        $imei = explode("\n", $imei);

        if (count($imei) == 1 && $imei[0] == '') {
            echo 'No Input Data';
            exit;
        }

        $bk_imei = $imei;

        $imei = array_values(array_unique($imei));

        if (is_array($imei)) {

            ini_set("memory_limit", "-1");

            set_time_limit(0);

            error_reporting(0);

            ini_set('display_error', 0);


            $filename = 'List_Imei_Checked_Export_Excel_'.date('d/m/Y');

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            $heads = array(
                /*'No.',*/
                'Imei SN',
                'Model',
                'Color',
                // 'Out Pirce Net',
                'Order Type',
                'Product Type',
                'Store Code',
                'Store Name',
                'Distributor Code',
                'Distributor Name',
                'Warehouse',
                'Invoice Number',
                // 'FINANCE GROUP',
                // 'O-GUARD'
            );

            fputcsv($output, $heads);

            $QGood = new Application_Model_Good();
            $good = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $good_color = $QGoodColor->get_cache();

            $QDistributor    = new Application_Model_Distributor();
            $distributor =  $QDistributor->get_cache();
            $distributor_cache = $QDistributor->get_cache2();

            $QWarehouse    = new Application_Model_Warehouse();
            $warehouses =   $QWarehouse->get_cache();

            $QMarket = new Application_Model_Market();
            $QBvgSellOut = new Application_Model_BvgSelloutImei();

            $QJoint_circular = new Application_Model_JointCircular();
            $joint = $QJoint_circular->fetchAll();
            $joint_array = array();
            foreach($joint as $k=>$v)
            {
                $joint_array[$v['id']] = $v['name'];
            }

            foreach ($imei as $value) {
                $value = trim($value);
                $info = array();
                $return = $this->checkImei($value, $info);
                $O_GUARD = "";
                $O_GUARD = $this->checkImei_O_GUARD(trim($value));
                $row =   array();

                $row[] = $value;

                $m = '';
                switch ($return){
                    case -1:
                    $m = 'wrong format';
                    break;
                    case -2:
                    $m = 'not exists';
                    break;
                }

                $market = [];
                $sn_ref = '';
                $order_type = '';

                $invoice_number = $invoice_time = $special_discount = $price_product_relative = $price_export_relative = 0;
                if ( isset($info['imei']) ) {
                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('id = ?', $info['imei']['sales_id']);
                    $market  = $QMarket->fetchRow($where);

                    if ($market) {
                        $sn_ref                 = $market['sn_ref'];
                        $invoice_number         = $market['invoice_number'];
                        $invoice_time           = $market['invoice_time'];
                        $special_discount       = $market['spc_discount'];
                        $price_product_relative = $market['price'];

                        if(isset($market['sale_off_percent']) and $market['sale_off_percent'] > 0){
                            $price_product_relative = (($price_product_relative * $market['sale_off_percent'])/100);
                        }
                        
                        $price_export_relative  = intval($market['total'] / $market['num']) ? intval($market['total'] / $market['num']) : 0;
                        switch ( $market['type'] ) {
                            case '1':
                            $order_type = 'Retailer';
                            break;
                            case '2':
                            $order_type = 'APK';
                            break;
                            case '3':
                            $order_type = 'Staff';
                            break;
                            case '5':
                            $order_type = 'DEMO';
                            break;    
                            default:

                            break;
                        }

                    }
                }

                $percent = $special_discount*0.01;

                switch ($special_discount) {
                    case '1':
                    $percent = 0.01;
                    break;
                    case '2':
                    $percent = 0.02;
                    break;
                    case '3':
                    $percent = 0.03;
                    break;
                    case '5':
                    $percent = 0.05;
                    break;
                }

                
                
                $QStore = new Application_Model_Store();
                
                $where1 = $QStore->getAdapter()->quoteInto('id = ?',$info['imei']['store_id']);
                $store_detials = $QStore->fetchRow($where1);

                $sum_special_discount = number_format($info['sum_bvg_price']*$percent,2);

                $good_name = ( isset($info['imei']['good_id']) and isset($good[$info['imei']['good_id']]) ) ? $good[$info['imei']['good_id']] : $m;

                $row[] = $good_name;

                $good_color_name = ( isset($info['imei']['good_color']) and isset($good_color[$info['imei']['good_color']]) ) ? $good_color[$info['imei']['good_color']] : '';
                $row[] = $good_color_name;

                // $row[] = isset($price_product_relative) ? $price_product_relative-$sum_special_discount : '';

                $row[] = isset($order_type) ? $order_type : '';

                if(isset($info['imei']['type']))
                {
                    //$row[] = $info['imei']['type'];
                    switch ( $info['imei']['type'] ) {
                        case '1':
                        $imei_type = 'Normal';
                        break;
                        case '2':
                        $imei_type = 'APK';
                        break;
                        case '3':
                        $imei_type = 'STAFF';
                        break;
                        case '5':
                        $imei_type = 'DEMO';
                        break;    
                        default:

                        break;
                    }

                }
                $row[] = isset($imei_type) ? $imei_type : '';

                
                $row[] = $store_detials->store_code;
                $row[] = $store_detials->name;

                $distributor_name = ( isset($info['imei']['distributor_id']) and isset($distributor[$info['imei']['distributor_id']]) ) ? $distributor[$info['imei']['distributor_id']] : '';
                $row[] = $distributor_cache[$info['imei']['distributor_id']]['d_code'];
                $row[] = $distributor_cache[$info['imei']['distributor_id']]['title'];

                $imei2 = ( isset($info['imei']['warehouse_id']) and isset($warehouses[$info['imei']['warehouse_id']]) ) ? $warehouses[$info['imei']['warehouse_id']] : '';
                $row[] = $imei2;
                $row[] = isset($invoice_number) ? $invoice_number : 'x';

                fputcsv($output, $row);
            }


        }

        exit;
    }

    public function exportExcelCloseAction()
    {
        $imei = trim($this->getRequest()->getParam('imei', ''));
        $imei = explode("\n", $imei);

        if (count($imei) == 1 && $imei[0] == '') {
            echo 'No Input Data';
            exit;
        }

        if (is_array($imei)) {

            ini_set("memory_limit", "-1");

            set_time_limit(0);

            error_reporting(0);

            ini_set('display_error', 0);


            $filename = 'List_Imei_Checked_In_'.date('d/m/Y');

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            $heads = array(
                /*'No.',*/
                'Imei SN',
                'CN For return',
                'CN For Price Protection',
                'Product',
                'Model',
                'Color',
                'IN Date',
                'OUT Date',
                'Activated Date',
                'Purchase SN',
                'Sales SN',
                'Round Protection Price',
                'Protection Price',
                'Distributor ID',
                'Distributor Code',
                'Distributor',
                'Warehouse',
                'SO',
                'Invoice Number',
                'Invoice Date',
                'Out Pirce',
                'Actual Price',
                'Order Type',
                'Type',
                'Status',
                'Joint Circular',
                'Imported_Date',
                'Change ORDER SN',
                'Good_ID',
                'Good_Color_ID',
                'Distributor Group'
            );

            fputcsv($output, $heads);

            $QGood = new Application_Model_Good();
            $good = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $good_color = $QGoodColor->get_cache();

            $QDistributor    = new Application_Model_Distributor();
            $distributor =  $QDistributor->get_cache();

            $QWarehouse    = new Application_Model_Warehouse();
            $warehouses =   $QWarehouse->get_cache();

            $QMarket = new Application_Model_Market();
            $QBvgSellOut = new Application_Model_BvgSelloutImei();

            $QJoint_circular = new Application_Model_JointCircular();
            $joint = $QJoint_circular->fetchAll();
            $joint_array = array();
            foreach($joint as $k=>$v)
            {
                $joint_array[$v['id']] = $v['name'];
            }

            $db = Zend_Registry::get('db');
            $sql =    "SELECT   
            i.imei_sn,
            i.type,
            CASE i.type
            WHEN 1 THEN 'normal'
            WHEN 2 THEN 'demo'
            WHEN 5 THEN 'apk'
            END AS imei_type,
            CASE i.status
            WHEN 1 THEN 'OK'
            WHEN 2 THEN 'processing'
            WHEN 4 THEN 'lost'
            WHEN 5 THEN 'on changing'
            END AS imei_status,
            mr.creditnote_sn AS CN_For_return,
            (SELECT bi.creditnote_sn FROM bvg_imei bi
              WHERE imei_sn IN (i.imei_sn) 
              AND bi.d_id = i.distributor_id
              AND bi.sales_sn = i.sales_sn
              AND bi.good_id = i.good_id 
              AND bi.good_color = i.good_color  order by bi.create_date desc limit 1,1)AS CN_For_Price_Protection,
            g.id AS good_id,
            c.id AS color_id,
            g.name AS good_name,
            g.desc AS product_detail_name,
            c.name AS color,
            w.id AS warehouse_id,
            w.name AS imei2,
            i.into_date,
            i.out_date,
            i.activated_date,
            p.sn_ref AS PurchaseSN,
            p.mysql_time AS PurchaseDate,
            m.sn_ref,
            m.invoice_number,
            m.invoice_time AS invoice_date,
            IFNULL(m.spc_discount, 0)as spc_discount,
            CASE m.type
            WHEN 1 THEN 'Retailer'
            WHEN 2 THEN 'For Demo'
            WHEN 3 THEN 'For Staff'
            WHEN 4 THEN 'For Lending'
            WHEN 5 THEN 'For APK'
            END AS Order_Type,
            i.out_price,
            m.price AS Actual_Price,
            i.return_sn,
            mr.sn_ref AS return_ref,
            dis.id AS Distributor_ID,
            dis.store_code,
            dis.title AS Distributor,
            dis.finance_group,
            dg.group_name,
            rm.name AS district,
            rm2.name AS province,
            a.name AS area_name
            FROM
            imei AS i
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color AND i.distributor_id=m.d_id 
            LEFT JOIN market AS mr ON mr.sn = i.return_sn AND mr.good_id = i.good_id AND mr.good_color = i.good_color AND i.distributor_id=mr.d_id AND m.isbacks=1
            LEFT JOIN purchase_order AS p ON p.sn = i.po_sn
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id
            LEFT JOIN distributor_group AS dg  ON dis.group_id = dg.group_id
            LEFT JOIN warehouse AS w  ON i.warehouse_id = w.id  
            LEFT JOIN hr.regional_market AS rm     ON dis.district = rm.id 
            LEFT JOIN hr.regional_market AS rm2     ON dis.region = rm2.id 
            LEFT JOIN hr.area AS a     ON rm2.area_id = a.id 
            WHERE 1=1 
            AND i.imei_sn IN(".join(',',$imei).")
            GROUP BY i.imei_sn,i.distributor_id,i.sales_sn,i.good_id,i.good_color
            ORDER BY i.imei_sn
            ;";
            $stmt = $db->query($sql);
            $data =  $stmt->fetchAll();

            foreach ($data as $value) {
                $imei_sn = trim($value['imei_sn']);

                $row =   array();

                $row[] = $imei_sn;

                $row[] = $value['CN_For_return'];
                $row[] = $value['CN_For_Price_Protection'];

                $row[] = $value['product_detail_name'];
                $row[] = $value['good_name'];
                $row[] = $value['color'];

                $row[] = $value['into_date'];
                $row[] = $value['out_date'];
                $row[] = $value['activated_date'];
                $row[] = $value['PurchaseSN'];
                $row[] = $value['sn_ref'];

                $QBvgImei = new Application_Model_BvgImei();

                $where_bvg = [];
                $where_bvg[] = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                $where_bvg[] = $QBvgImei->getAdapter()->quoteInto('d_id = ?', $value['Distributor_ID']);
                $imei_bvg_all = $QBvgImei->fetchALL($where_bvg);

                $sum_bvg_price = 0;
                $round_bvg = 0;
                foreach ($imei_bvg_all as $key) {
                    $sum_bvg_price = $sum_bvg_price+$key['price'];
                    $round_bvg++;
                }

                $row[] = $round_bvg;
                $row[] = $sum_bvg_price;

                $row[] = $value['Distributor_ID'];
                $row[] = $value['store_code'];
                $row[] = $value['Distributor'];

                $row[] = $value['imei2'];

                $row[] = isset($value['sn_ref']) ? $value['sn_ref'] : 'x';
                $row[] = isset($value['invoice_number']) ? $value['invoice_number'] : 'x';
                $invoice_time = date('d/m/Y H:i:s', strtotime($value['invoice_date']) );
                $row[] = isset($value['invoice_date']) ? $value['invoice_date'] : 'x';
                $row[] = isset($price_product_relative) ? $price_product_relative : '';
                $row[] = isset($price_export_relative) ? $price_export_relative : 0;
                $row[] = $value['Order_Type'];
                $row[] = $value['imei_type'];

                $row[] = $value['imei_status'];

                $where_imei = $QBvgSellOut->getAdapter()->quoteInto('imei_sn = ? ' , trim($imei_sn));
                $result_imei_sellout = $QBvgSellOut->fetchRow($where_imei);
                $row[] = isset($joint_array[$result_imei_sellout['joint_circular_id']]) ? $joint_array[$result_imei_sellout['joint_circular_id']] : '';
                $row[] = isset($result_imei_sellout['created_at']) ?  date('d-m-Y h:i:s' , strtotime($result_imei_sellout['created_at'])) : '';

                $QImei = new Application_Model_Imei();
                $getCo = $QImei->getCoByImei($imei_sn);

                $row[] = isset($getCo['sn_ref']) ? $getCo['sn_ref'] : 'x';

                $row[] = $value['good_id'];
                $row[] = $value['color_id'];

                if($value['Distributor_ID']){
                    $distributor_group = $QDistributor->getDistributorGroup($value['Distributor_ID']);
                    $row[] = $distributor_group['group_name'];
                }else{
                    $row[] = '';
                }

                fputcsv($output, $row);
            }


        }

        exit;
    }

    public function exportExcelBvgAction()
    {
        $imei = trim($this->getRequest()->getParam('imei', ''));
        $imei = explode("\n", $imei);

        if (count($imei) == 1 && $imei[0] == '') {
            echo 'No Input Data';
            exit;
        }

        if (is_array($imei)) {

            ini_set("memory_limit", "-1");

            set_time_limit(0);

            error_reporting(0);

            ini_set('display_error', 0);


            $filename = 'List_Imei_Bvg_Checked_In_'.date('d/m/Y');

            // output headers so that the file is downloaded rather than displayed
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename='.$filename.'.csv');
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            // create a file pointer connected to the output stream
            $output = fopen('php://output', 'w');

            $heads = array(
                'IMEI',
                'Model',
                'Color',
                'SO',
                'Invoice',
                'Distributor Name',
                'Round Protection Price',
                'Time',
                'CP',
                'Old Price',
                'Protection Price',
                'New Price',
                'Remark'
            );

            fputcsv($output, $heads);

            $QGood = new Application_Model_Good();
            $good = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $good_color = $QGoodColor->get_cache();

            $QDistributor    = new Application_Model_Distributor();
            $distributor =  $QDistributor->get_cache();

            $QWarehouse    = new Application_Model_Warehouse();
            $warehouses =   $QWarehouse->get_cache();

            foreach ($imei as $value) {
                $value = trim($value);
                $info = array();
                $return = $this->checkBvg($value, $info);

                $count_num = count($info['get_bvg_from_tools']);

                foreach ($info['get_bvg_from_tools'] as $key) {

                    $row =   array();

                    $row[] = $value;

                    $m = '';
                    switch ($return){
                        case -1:
                        $m = 'wrong format';
                        break;
                        case -2:
                        $m = 'not exists';
                        break;
                    }

                    $good_name = ( isset($info['imei']['good_id']) and isset($good[$info['imei']['good_id']]) ) ? $good[$info['imei']['good_id']] : $m;
                    $row[] = $good_name;

                    $good_color_name = ( isset($info['imei']['good_color']) and isset($good_color[$info['imei']['good_color']]) ) ? $good_color[$info['imei']['good_color']] : '';
                    $row[] = $good_color_name;

                    $row[] = $key['sn_ref'];
                    $row[] = $key['invoice_number'];

                    $distributor_name = ( isset($info['imei']['distributor_id']) and isset($distributor[$info['imei']['distributor_id']]) ) ? $distributor[$info['imei']['distributor_id']] : '';
                    $row[] = $distributor_name;

                    $row[] = $count_num;
                    $row[] = $key['create_date'];
                    $row[] = $key['creditnote_sn'];
                    $row[] = $key['invoice_price'];
                    $row[] = $key['price'];
                    $row[] = number_format($key['invoice_price'] - $key['price'],2);
                    $row[] = $key['remark'];

                    $count_num--;
                    
                    fputcsv($output, $row);
                }
            }


        }

        exit;
    }

    // AJAX func
    // Get Purchase order status
    public function checkPoAction()
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect( ( HOST.'tool/po' ) );
        } else {
            $pos = trim($this->getRequest()->getParam('value', ''));
            $pos = explode("\n", $pos);

            if (count($pos) == 1 && $pos[0] == '') {
                echo 'No Input Data';
                exit;
            }

            if (is_array($pos)) {

                // $pos = array_unique($pos);
                $limit = LIMITATION*5;
                $page = $this->getRequest()->getParam('page', 1);
                $total = count($pos);
                $offset = ($page - 1) * $limit;
                $pos = array_slice($pos, $offset, $limit);

                $result = array();

                foreach ($pos as $key => $value) {
                    $info = array();

                    $po = $this->checkPo($value);

                    if ($po === -1) {
                        $result[$value] = 'not exists';
                    }  else {
                        $result[$value] = $po;

                    }
                }

                $QStaff = new Application_Model_Staff();
                $this->view->staffs = $QStaff->get_cache();

                $this->view->url = HOST.'tool/po'.'?';
                $this->view->offset = $offset;
                $this->view->limit  = $limit;
                $this->view->total  = $total;
                $this->view->page   = $page;
                $this->view->res = $result;
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setRender('partials/po');
            }
        }
    }

    // AJAX func
    // Get Sales Order status
    public function checkSoAction()
    {
        if(!$this->getRequest()->isXmlHttpRequest()) {
            $this->_redirect( ( HOST.'tool/so' ) );
        } else {
            $pos = trim($this->getRequest()->getParam('value', ''));
            $pos = explode("\n", $pos);

            if (count($pos) == 1 && $pos[0] == '') {
                echo 'No Input Data';
                exit;
            }

            if (is_array($pos)) {

                // $pos = array_unique($pos);
                $limit = LIMITATION*5;
                $page = $this->getRequest()->getParam('page', 1);
                $total = count($pos);
                $offset = ($page - 1) * $limit;
                $pos = array_slice($pos, $offset, $limit);

                $result = array();

                foreach ($pos as $key => $value) {
                    $info = array();

                    $po = $this->checkSo($value);

                    if ($po === -1) {
                        $result[$value] = 'not exists';
                    }  else {

                        $result[$value] = $po;
                    }
                }

            }

            $QStaff = new Application_Model_Staff();
            $this->view->staffs = $QStaff->get_cache();

            $this->view->url = HOST.'tool/so'.'?';
            $this->view->offset = $offset;
            $this->view->limit  = $limit;
            $this->view->total  = $total;
            $this->view->page   = $page;
            $this->view->res = $result;
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setRender('partials/so');
        }
    }

    public function invoiceAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
    }

    public function invoicePrintAction()
    {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        if (!$userStorage || !isset($userStorage->id)) $this->_redirect(HOST);
        //
        $this->_helper->layout->disableLayout();

        $name             = $this->getRequest()->getParam('name');
        $address          = $this->getRequest()->getParam('address');
        $delivery_address = $this->getRequest()->getParam('delivery_address');
        $tax              = $this->getRequest()->getParam('tax');
        $invoice_number   = $this->getRequest()->getParam('invoice_number');

        $product  = $this->getRequest()->getParam('product');
        $unit     = $this->getRequest()->getParam('unit');
        $quantity = $this->getRequest()->getParam('quantity');
        $price    = $this->getRequest()->getParam('price');
        $total    = $this->getRequest()->getParam('total');
        $ck = $this->getRequest()->getParam('ck');

        $sn = My_Sale_Order::generateSn();
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {
            // validate

            if (!$product || !is_array($product)) throw new Exception("Product is required", 3);
            if (!$unit || !is_array($unit)) throw new Exception("Unit is required", 3);
            if (!$quantity || !is_array($quantity)) throw new Exception("Quantity is required", 4);
            if (!$price || !is_array($price)) throw new Exception("Price is required", 5);

            if (isset($invoice_number) and $invoice_number) {
               if(!preg_match('/^[0-9]{7}$/', $invoice_number))
                throw new Exception("Invalid invoice number");
        }

        $name = trim($name);
        $address = trim($address);
        $delivery_address = trim($delivery_address);
        $tax = trim($tax);



        foreach ($product as $k => $p) {
            $product[$k] = trim($product[$k]);
            if (empty($p)) throw new Exception("Product is required in line " . ($k+1), 9);
                // if (!isset($unit[$k]) || empty($unit[$k])) throw new Exception("Unit is required in line " . ($k+1), 10);
                // if (!isset($quantity[$k]) || empty($quantity[$k]) || !is_numeric($quantity[$k])) throw new Exception("Quantity is required in line " . ($k+1), 11);
                // if (!isset($price[$k]) || empty($price[$k]) || !is_numeric($price[$k])) throw new Exception("Price is required in line " . ($k+1), 12);
                //  if (!isset($total[$k]) || empty($total[$k]) || !is_numeric($total[$k])) throw new Exception("Total is required in line " . ($k+1), 13);
        }

            // save data

        $QCustomOrder = new Application_Model_CustomOrder();

        $data = array(
            'sn'               => $sn,
            'customer_name'    => $name,
            'address'          => $address,
            'delivery_address' => $delivery_address,
            'tax'              => $tax,
            'created_at'       => date('Y-m-d H:i:s'),
            'created_by'       => $userStorage->id,
        );

        $id = $QCustomOrder->insert($data);

        $QCustomOrderDetail = new Application_Model_CustomOrderDetail();

        foreach ($product as $k => $p) {
            $data = array(
                'custom_order_id' => $id,
                'product_name'    => $p,
                'unit'            => !empty($unit[$k]) ? $unit[$k] : null,
                'quantity'        => !empty($quantity[$k]) ? $quantity[$k] : null,
                'price'           => !empty($price[$k]) ? $price[$k] : null,
                'total'           => !empty($total[$k]) ? $total[$k] : null,
            );

            $QCustomOrderDetail->insert($data);
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
        $flashMessenger = $this->_helper->flashMessenger;
        echo sprintf("[%s] %s", $e->getCode(), $e->getMessage());

        exit;
    }

    $this->view->name             = $name;
    $this->view->address          = $address;
    $this->view->delivery_address = $delivery_address;
    $this->view->tax              = $tax;


    $this->view->product  = $product;
    $this->view->unit     = $unit;
    $this->view->quantity = $quantity;
    $this->view->price    = $price;
    $this->view->total    = $total;
    $this->view->ck      = $ck;
    $this->view->invoice_prefix = INVOICE_OPPO_SIGN;

    $this->view->sn = $sn;
        // My_Image_Barcode::renderNoCode($sn);
    $QInvoiceNumber = new Application_Model_InvoiceNumber();

    if(empty($invoice_number))
    {
        $invoice_number = $QInvoiceNumber->getLastId(1);
    }
    $this->view->invoice_number = $invoice_number;
    $this->view->invoice_prefix = INVOICE_OPPO_SIGN;


}

    //Tanong
private function checkImei_O_GUARD($imei) 
{
    $QOguard = new Application_Model_Oguard();
    $where = $QOguard->getAdapter()->quoteInto('imei_sn = ?', $imei);
    $imei_res = $QOguard->fetchRow($where);

    if (!$imei_res){
        return "";
    }
    return "Y";
}

//first Function khuan
function checkModelAction(){
    if(!$this->getRequest()->isXmlHttpRequest()) {
        $this->_redirect( ( HOST.'tool/imei' ) );
    } else {
        $imei = trim($this->getRequest()->getParam('value', ''));
        $imei = explode("\n", $imei);

        if (count($imei) == 1 && $imei[0] == '') {
            echo 'No Input Data';
            exit;
        }


        if (is_array($imei)) {

            $QGood = new Application_Model_Good();
            $goods = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $good_colors = $QGoodColor->get_cache();

            $return = $this->checkModel($imei, $info);


            $QStaff = new Application_Model_Staff();
            $this->view->staffs = $QStaff->get_cache();

            $QDistributor = new Application_Model_Distributor();
            $this->view->distributors = $QDistributor->get_cache2();

            $QGood = new Application_Model_Good();
            $this->view->goods = $QGood->get_cache();

            $QGoodColor = new Application_Model_GoodColor();
            $this->view->good_colors = $QGoodColor->get_cache();

            $QWarehouse = new Application_Model_Warehouse();
            $this->view->warehouses = $QWarehouse->get_cache();
                //Tanong
                 //print_r($info);
            $this->view->url = HOST.'tool/imei'.'?';
            $this->view->offset = $offset;
            $this->view->limit  = $limit;
            $this->view->total  = $total;
            $this->view->page   = $page;
            $this->view->res    = $info;

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setRender('partials/model-imei');

        }
    }
}

private function checkModel($imei, &$info){
    $QImei = new Application_Model_Imei();
    $res = $QImei->checkimei($imei);
    $info = $res;
}


private function checkImei($imei, &$info) {

        // Kiểm tra định dạng
    // if (!preg_match('/^[0-9]{15}$/', $imei)) {  Old is only check phone IMEI
    if (!preg_match('/^[a-zA-Z0-9]{13,30}$/', $imei)) {
            return -1; // sai định dạng
        }

        $QImei = new Application_Model_Imei();
        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $res = $QImei->fetchRow($where);

        if (!$res) {

            return -2; // không có trong hệ thống

        } else {

            $info['imei'] = $res;

            // print_r($res);

            if (!empty($res['po_sn'])) {
                $QPo = new Application_Model_Po();
                $where = $QPo->getAdapter()->quoteInto('sn = ?', $res['po_sn']);
                $po = $QPo->fetchRow($where);
                $info['po'] = $po;
            }

            //check imei timing or not
            $QImei = new Application_Model_Imei();
            $check_timing = $QImei->checkTiminginHr($imei);
            if($check_timing) {
                $info['timing'] = $check_timing;
            }

            //check imei lock in Table
            $QImei = new Application_Model_Imei();
            $check_lock = $QImei->checkimeilock($imei);
            if($check_lock) {
                $info['lock'] = $check_lock;
            }

            $QImeiReturn = new Application_Model_ImeiReturn();
            $where = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $imei);
            $imei_return = $QImeiReturn->fetchRow($where, 'back_warehouse_at');

            $QImeiChangeDistributor = new Application_Model_ChangeImeiDistibutor();
            $where = $QImeiChangeDistributor->getAdapter()->quoteInto('imei_sn = ?', $imei);
            $change_distributor = $QImeiChangeDistributor->fetchRow($where);
            $info['change_distributor'] = $change_distributor;

            $QStore = new Application_Model_Store();

            if(!empty($res['store_id'])) {
                $where_store = $QStore->getAdapter()->quoteInto('id =?',$res['store_id']);
                $store_arr = $QStore->fetchRow($where_store);
                $info['store'] = $store_arr;
            }

            if ($imei_return) {
             $info['return'] = $imei_return;

             $QMarket = new Application_Model_Market();
             $where = $QMarket->getAdapter()->quoteInto('sn = ?', $res['return_sn']);
             $so = $QMarket->fetchRow($where);
             $info['return_ref'] = $so;


         }

         if (!empty($res['sales_sn'])) {
            $QMarket = new Application_Model_Market();
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $res['sales_sn']);
            $so = $QMarket->fetchRow($where);
            $info['so'] = $so;
        }

        $QBvgImei = new Application_Model_BvgImei();
            // $where = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
            // $imei_Bvg = $QBvgImei->fetchRow($where);

        $db = Zend_Registry::get('db');
        $sql_bvg_imei = "SELECT * FROM `bvg_imei` 
        WHERE imei_sn = '" . $imei . "' and d_id = '" . $info['imei']['distributor_id'] . "'
        ORDER BY create_date DESC";
        $imei_Bvg = $db->fetchRow($sql_bvg_imei);

        $where_bvg = [];
        $where_bvg[] = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $where_bvg[] = $QBvgImei->getAdapter()->quoteInto('d_id = ?', $info['imei']['distributor_id']);
        $imei_bvg_all = $QBvgImei->fetchALL($where_bvg);

        $sum_bvg_price = 0;
        $round_bvg = 0;
        foreach ($imei_bvg_all as $key) {
            $sum_bvg_price = $sum_bvg_price+$key['price'];
            $round_bvg++;
        }

        $info['sum_bvg_price'] = $sum_bvg_price;
        $info['round_bvg'] = $round_bvg;

            //pond
        $db = Zend_Registry::get('db');

            /*$sql = "SELECT c.sn_ref,c.completed_date
                    FROM `change_sales_order` c 
                    WHERE c.`changed_sn` IN(SELECT changed_sn FROM `change_sales_imei` WHERE imei='".$imei."')
                    ORDER BY c.`completed_date` DESC limit 1";
            $result = $db->fetchRow($sql);
            $info['co']=$result;*/

            $sql = "SELECT c.sn_ref,c.completed_date
            FROM `change_sales_order` c 
            WHERE c.`changed_sn` IN(SELECT changed_sn FROM `imei` WHERE imei_sn='".$imei."')
                ORDER BY c.`completed_date` DESC limit 1";
                $result = $db->fetchRow($sql);
                $info['co']=$result;

                if (!empty($imei_Bvg['creditnote_sn'])) {
                 $info['price_protection'] = $imei_Bvg;
             } 


         }

     }

     private function checkBvg($imei, &$info) {

        // Kiểm tra định dạng
        if (!preg_match('/^[0-9]{15}$/', $imei)) {
            return -1; // sai định dạng
        }

        $QImei = new Application_Model_Imei();
        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $res = $QImei->fetchRow($where);

        if (!$res) {
            return -2; // không có trong hệ thống
        } else {
            $info['imei'] = $res;

            if($info['imei']['distributor_id']){
                $QBvgImei = new Application_Model_BvgImei();

                $get_bvg_from_tools = $QBvgImei->getBvgFromTools($info['imei']['distributor_id'],$imei);

                $info['get_bvg_from_tools'] = $get_bvg_from_tools;
            }else{
                $info['get_bvg_from_tools'] = array();
            }

        }

    }

    private function checkPo($sn)
    {
        $QPo = new Application_Model_Po();
        $where = $QPo->getAdapter()->quoteInto('sn = ?', $sn);
        $po = $QPo->fetchRow($where);

        if (!$po) {
            return -1;
        }

        return $po;
    }

    private function checkSo($sn)
    {
        $QMarket = new Application_Model_Market();
        $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
        $so = $QMarket->fetchRow($where);

        if (!$so) {
            return -1;
        }

        return $so;
    }

    private function _export_distributor($params = array())
    {
        set_time_limit(0);
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'Report_Distributor_Model_'.date('YmdHis');

        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $heads = array(
            'STT',
            'Khach hang',
            'Ma KH',
            'Region',
            'Area',
        );

        // Lấy danh sách các model máy
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
        $goods = $QGood->fetchAll($where);

        // add các tên máy để làm tiêu đề cột
        foreach ($goods as $g_id => $g) {
            $heads[] = $g['name'];
        }

        $heads[] = 'Tong';

        fputcsv($output, $heads);

        if (isset($params['from']) && $params['from'] && isset($params['to']) && $params['to']) {
            $from = explode('/', $params['from']);
            $from = $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00';
            $to = explode('/', $params['to']);
            $to = $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59';
        } elseif (isset($params['from']) && $params['from']) {
            $from = explode('/', $params['from']);
            $from = $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00';
            $to = date('Y-m-d 23:59:59');
        } elseif (isset($params['to']) && $params['to']) {
            $to = explode('/', $params['to']);
            $to = $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59';
            $from = date_sub(date_create(), new DateInterval('P30D'))->format('Y-m-d 00:00:00');
        } else {
            $from = date_sub(date_create(), new DateInterval('P30D'))->format('Y-m-d 00:00:00');
            $to = date('Y-m-d 23:59:59');
        }

        // Lấy danh sách distributor
        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->fetchAll();

        $QRegion = new Application_Model_Region();
        $regions_cached = $QRegion->get_cache();
        $regions_cached2 = $QRegion->get_cache2();

        $QArea = new Application_Model_Area();
        $areas_cached = $QArea->get_cache();

        $i = 0;
        $db = Zend_Registry::get('db');

        // Lấy danh sách distributor/model
        $sql = "SELECT m.d_id, m.good_id, SUM(m.num) AS `res_total` FROM market m
        INNER JOIN good g
        ON g.id=m.good_id
        AND g.cat_id = ?
        AND m.add_time >= ?
        AND m.add_time <= ?
        AND m.outmysql_time <> 0
        AND m.outmysql_time IS NOT NULL
        AND m.shipping_yes_time <> 0
        AND m.shipping_yes_time IS NOT NULL
        AND m.pay_time <> 0
        AND m.pay_time IS NOT NULL
        WHERE
        m.type = ?
        GROUP BY m.d_id, m.good_id";

        $result = $db->query($sql, array(PHONE_CAT_ID, $from, $to, $params['type']));

        $d_m_list = array();

        foreach ($result as $r_id => $r_v) {
            if ( ! isset( $d_m_list[ $r_v['d_id'] ] ) ) {
                $d_m_list[ $r_v['d_id'] ] = array();
            }

            $d_m_list[ $r_v['d_id'] ][] = array(
                'good_id'   => $r_v['good_id'],
                'res_total' => $r_v['res_total'],
            );
        }

        // duyệt từng distributor, mỗi distributor là 1 dòng trong file excel
        foreach ($distributors as $distributor) {
            $region_id = $distributor->region;
            $line = array();
            $line[] = ++$i;
            $line[] = $distributor['title'];
            $line[] = $distributor['store_code'];
            $line[] = (isset($regions_cached[$region_id]) ? $regions_cached[$region_id] : '');
            $region = isset($regions_cached2[$region_id]) ? $regions_cached2[$region_id] : null;
            $area = '';
            if ($region){
                $area_id = $region['area_id'];
                $area = (isset($areas_cached[$area_id]) ? $areas_cached[$area_id] : '');
            }

            $line[] = $area;

            $_num_ = count($line);

            $total = 0;

            foreach ($d_m_list[$distributor['id']] as $r_k => $r_v) {
                $alpha    = $_num_;

                foreach ($goods as $g_id => $good) {
                    if ($good['id'] == $r_v['good_id']) {
                        $line[$alpha] = $r_v['res_total'];
                        $total += $r_v['res_total'];
                        break;
                    }

                    $alpha++;
                }
            }

            for($j = $_num_; $j < count($goods)+$_num_; $j++) {
                if (!isset($line[$j])) {
                    $line[$j] = '';
                }
            }

            ksort($line);

            $line[] = $total;

            fputcsv($output, $line);
        }

        exit;
    }

    private function _export_warehouse($params = array())
    {
        set_time_limit(0);
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'Report warehouse-model - '.date('Y-m-d H-i-s');

        // output headers so that the file is downloaded rather than displayed
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        $heads = array(
            'STT',
            'Kho',
            'Model',
            'Xuat ban NV',
            'Xuat noi bo',
            'Xuat khac',
            'Ton kho',
        );

        fputcsv($output, $heads);

        // Lấy danh sách các model máy
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
        $goods = $QGood->fetchAll($where);

        if (isset($params['from']) && $params['from'] && isset($params['to']) && $params['to']) {
            $from = explode('/', $params['from']);
            $from = $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00';
            $to = explode('/', $params['to']);
            $to = $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59';
        } elseif (isset($params['from']) && $params['from']) {
            $from = explode('/', $params['from']);
            $from = $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00';
            $to = date('Y-m-d 23:59:59');
        } elseif (isset($params['to']) && $params['to']) {
            $to = explode('/', $params['to']);
            $to = $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59';
            $from = date_sub(date_create(), new DateInterval('P30D'))->format('Y-m-d 00:00:00');
        } else {
            $from = date_sub(date_create(), new DateInterval('P30D'))->format('Y-m-d 00:00:00');
            $to = date('Y-m-d 23:59:59');
        }

        $i = 0;
        $db = Zend_Registry::get('db');

        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->get_cache();

        $sql = "SELECT m.warehouse_id, m.good_id, m.type, SUM(m.num) AS `res_total` FROM `market` m
        INNER JOIN good g
        ON g.id=m.good_id
        AND g.cat_id = ?
        AND m.add_time >= ?
        AND m.add_time <= ?
        AND m.outmysql_time <> 0
        AND m.outmysql_time IS NOT NULL
        AND m.shipping_yes_time <> 0
        AND m.shipping_yes_time IS NOT NULL
        AND m.pay_time <> 0
        AND m.pay_time IS NOT NULL
        WHERE
        m.type = ?
        GROUP BY m.warehouse_id, m.good_id, m.type";
        $result = $db->query($sql, array(PHONE_CAT_ID, $from, $to, $params['type']));

        $w_m_list = array();

        foreach ($result as $r_id => $r_v) {
            if ( ! isset( $w_m_list[ $r_v['warehouse_id'] ] ) ) {
                $w_m_list[ $r_v['warehouse_id'] ] = array();
            }

            if ( ! isset( $w_m_list[ $r_v['warehouse_id'] ][ $r_v['good_id'] ] ) ) {
                $w_m_list[ $r_v['warehouse_id'] ][ $r_v['good_id'] ] = array();
            }

            $w_m_list[ $r_v['warehouse_id'] ][ $r_v['good_id'] ][] = array(
                'type'      => $r_v['type'],
                'res_total' => $r_v['res_total'],
            );
        }

        $page = 1;
        $limit = NULL;
        $total = 0;

        foreach ($warehouses as $w_k => $w_v) {
            foreach ($goods as $g_k => $g_v) {
                $line = array();
                $line[] = ++$i;
                $line[] = $w_v;
                $line[] = $g_v['name'];

                foreach ($w_m_list[ $w_k ][ $g_v['id'] ] as $r_k => $r_v) {
                    switch ($r_v['type']) {
                        case '1': // retailer
                        $line[5] = $r_v['res_total'];
                        break;
                        case '2': // demo
                        $line[4] = $r_v['res_total'];
                        break;
                        case '3': // staff
                        $line[3] = $r_v['res_total'];
                        break;
                        default:

                        break;
                    }
                }

                for($j = 3; $j <= 5; $j++) {
                    if (!isset($line[$j])) {
                        $line[$j] = 0;
                    }
                }

                ksort($line);

                $storage = $QGood->fetchPaginationStorage(
                    $page, $limit, $total,
                    array(
                        'warehouse_id' => $w_k,
                        'good_id' => $g_v['id'],
                    )
                );

                $total = 0;

                foreach ($storage as $s_k => $model) {
                    $total += $model['imei_count'];
                }

                $line[] = $total;

                fputcsv($output, $line);
            }
        }

        exit;
    }

    public function updateImeiDistributorAction()
    {
        $distributor_service_arr = $this->distributor_service;
        $QDistributor = new Application_Model_Distributor();
        $this->view->distributor_list = $QDistributor->get_cache();
    }

    public function updateImeiDistributorSaveAction()
    {
        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $this->_helper->layout->disableLayout();

            $imeis = $this->getRequest()->getParam('imeis');
            $distributor = $this->getRequest()->getParam('distributor');
            $distributor_service_arr = $this->distributor_service;

            if (!$distributor || !is_numeric($distributor))
                throw new Exception("Invalid Distributor");

            $QDistributor = new Application_Model_Distributor();
            $where = $QDistributor->getAdapter()->quoteInto('id = ?', intval($distributor));
            $distributor_check = $QDistributor->fetchRow($where);

            if(!$distributor_check) throw new Exception("Invalid distributor");

            $imei_from_service = array();
            $imei_list = trim($imeis);
            $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
            $imei_list = explode("\n", $imei_list);
            $imei_list = array_filter($imei_list);

            $QImei = new Application_Model_Imei();
            $where = array();
            $where[] = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei_list);
            $where[] = $QImei->getAdapter()->quoteInto('distributor_id IN (?)', $distributor_service_arr);
            $imei_check = $QImei->fetchAll($where);

            if (!$imei_check) throw new Exception("No IMEI from OPPO Service");

            foreach ($imei_check as $key => $value)
                $imei_from_service[] = $value['imei_sn'];

            if (count($imei_from_service) > 10) throw new Exception("Nhập tối đa 10 IMEI trong 1 lần");

            $data = array('distributor_id' => intval($distributor));
            $where = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei_from_service);
            $QImei->update($data, $where);

            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $info = sprintf('Change distributor - IMEIs (%s) - Distributor (%s)-(%s)', implode(',', $imei_from_service), $distributor, $distributor_check['title']);
            $QLog = new Application_Model_Log();
            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $db->commit();

            $this->view->result = 'success';
            $this->view->message = 'Done - ' . $info;
        } catch (Exception $e) {
            $db->rollback();
            $this->view->error = $e->getMessage();
        }

    }

    public function scanoutAction()
    {
        $user         = $this->getRequest()->getParam('user');
        $scan_at_from = $this->getRequest()->getParam('scan_at_from');
        $scan_at_to   = $this->getRequest()->getParam('scan_at_to');
        $warehouse_id   = $this->getRequest()->getParam('warehouse_id');
        $date_now = date('Y-m-d'); 
       // $warehouse_id = 1 ;
        $params = array(
            'user'            => $user,
            'invoice_time_from'    => $scan_at_from,
            'invoice_time_to'      => $scan_at_to,
            'date_now'        => $date_now,
            'warehouse_id' => $warehouse_id        );

        $QMarket = new Application_Model_Market(); 
        $QStaff = new Application_Model_Staff();
        $QWarehouse     = new Application_Model_Warehouse();
        $warehouses_cached = $QWarehouse->get_cache();

        $ScanMarketUnit = $QMarket->fetchScanOut($page, $limit, $total, $params);
        $UnitProduct = $QMarket->fetchScanOutProduct($page, $limit, $total, $params);
        $StaffScanOut = $QMarket->StaffScanOut($params);



        $this->view->staffs = $QStaff->get_cache();
        $this->view->ScanMarketUnit = $ScanMarketUnit;
        $this->view->UnitProduct = $UnitProduct;
        $this->view->SelectStaff = $StaffScanOut;
        $this->view->warehouses_cached = $warehouses_cached;
        $this->view->params = $params;


        
        $this->_helper->viewRenderer->setRender('scanout');

    }
    public function blackListDistributorAction(){
        $sort               = $this->getRequest()->getParam('sort', 'add_time');
        $desc               = $this->getRequest()->getParam('desc', 1);
        $page               = $this->getRequest()->getParam('page', 1);
        $reason_id          = $this->getRequest()->getParam('reason');
        $distributor        = $this->getRequest()->getParam('distributor');
        $dis_id             = $this->getRequest()->getParam('dis_id');
        $type               = $this->getRequest()->getParam('type');

        $limit              = 30;
        $total              = 0;
        $params             = array(
            'reason_id' => $reason_id,
            'distributor' => $distributor,
            'dis_id' => $dis_id,
            'type' => $type
        );

        $QDistributor       = new Application_Model_Distributor();
        $QBlackListReason   = new Application_Model_BlackListReason();
        $blacklist          = $QDistributor->fetchBlackList($page, $limit, $total, $params);

        $reason = $QBlackListReason->get_cache();
        
        $this->view->blacklist = $blacklist;

        $this->view->reason   = $reason;
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'tool/black-list-distributor'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }
    public function blackListDistributorLogAction(){
        $sort               = $this->getRequest()->getParam('sort', 'add_time');
        $desc               = $this->getRequest()->getParam('desc', 1);
        $page               = $this->getRequest()->getParam('page', 1);
        $reason_id          = $this->getRequest()->getParam('reason');
        $distributor        = $this->getRequest()->getParam('distributor');
        $dis_id             = $this->getRequest()->getParam('dis_id');
        $type               = $this->getRequest()->getParam('type');

        $limit              = 30;
        $total              = 0;
        $params             = array(
            'reason_id' => $reason_id,
            'distributor' => $distributor,
            'dis_id' => $dis_id,
            'type' => $type
        );

        $QDistributorBlackListLog       = new Application_Model_DistributorBlackListLog();
        $QBlackListReason   = new Application_Model_BlackListReason();
        $blacklist          = $QDistributorBlackListLog->fetchBlackListLog($page, $limit, $total, $params);

        $reason = $QBlackListReason->get_cache();
        
        $this->view->blacklist = $blacklist;

        $this->view->reason   = $reason;
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'tool/black-list-distributor-log'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }
    public function addBlackListDistributorAction(){

        $QDistributor               = new Application_Model_Distributor();
        $QBlackListReason           = new Application_Model_BlackListReason();
        $QDistributorBlackList      = new Application_Model_DistributorBlackList();
        // $blacklist                  = $QDistributor->blackListDistributor();
        // $reason                     = $QBlackListReason->get_cache();
        // $this->view->blacklist      = $blacklist;
        // $this->view->reason         = $reason;

        if ($this->getRequest()->getMethod() == 'POST'){


            $flashMessenger = $this->_helper->flashMessenger;

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            try{
                $type           = $this->getRequest()->getParam('type');
                $d_id           = $this->getRequest()->getParam('d_id');
                $remark         = $this->getRequest()->getParam('remark');
                $text_remark    = $this->getRequest()->getParam('text_remark');

                $currentTime    = date('Y-m-d H:i:s');
                $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                $user_id        = $userStorage->id;

                $data = array(
                    'type'          => $type,
                    'd_id'          => $d_id,
                    'remark'        => $remark,
                    'text_remark'   => $text_remark,
                    'created_by'    => $user_id,
                    'created_at'    => $currentTime
                );
                $QDistributorBlackList->insert($data);
                $data_up = array(
                    'black_list_distributor'    => '1',
                );

                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
                $QDistributor->update($data_up,$where);

                $db->commit();

                $flashMessenger->setNamespace('success')->addMessage('Success');
                $this->_redirect(HOST.'tool/black-list-distributor');

            }catch(exception $e){

                $db->rollBack();

                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'tool/black-list-distributor');
            }
        }

        $reason                     = $QBlackListReason->get_cache();
        $this->view->reason         = $reason;
        // print_r($blacklist);
    }

    public function unBlackListAction(){

        $d_id           = $this->getRequest()->getParam('d_id');

        $QDistributor = new Application_Model_Distributor();
        $QDistributorBlackList = new Application_Model_DistributorBlackList();
        $QDistributorBlackListLog = new Application_Model_DistributorBlackListLog();

        $flashMessenger = $this->_helper->flashMessenger;

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try{

            $currentTime    = date('Y-m-d H:i:s');
            $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
            $user_id        = $userStorage->id;

            $where = $QDistributorBlackList->getAdapter()->quoteInto('d_id = ?', $d_id);
            $BlackList = $QDistributorBlackList->fetchAll($where);

            foreach ($BlackList as $key) {
              $data = array(
               'type'               =>$key['type'],
               'd_id'               =>$key['d_id'],
               'remark'             =>$key['remark'],
               'text_remark'        =>$key['text_remark'],
               'black_by'           =>$key['created_by'],
               'black_date_at'      =>$key['created_at'],
               'unblack_by'         =>$user_id, 
               'unblack_date_at'    =>$currentTime
           );
              $QDistributorBlackListLog->insert($data);
          }    

          $QDistributorBlackList->delete($where);

          $data_up = array('black_list_distributor'    => NULL);
          $where = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
          $QDistributor->update($data_up,$where);

          $db->commit();

          $flashMessenger->setNamespace('success')->addMessage('Unblack Success');
          $this->_redirect(HOST.'tool/black-list-distributor');

      }catch(exception $e){

        $db->rollBack();

        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST.'tool/black-list-distributor');
    }

        // print_r($blacklist);
}

public function listDistributorAction(){
    $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);
    $QDistributor               = new Application_Model_Distributor();
    $QBlackListReason           = new Application_Model_BlackListReason();
    $QDistributorBlackList      = new Application_Model_DistributorBlackList();  

    $rank                       = $this->getRequest()->getParam('rank', 0);

    $blacklist                  = $QDistributor->blackListDistributor($rank );


    $reason                     = $QBlackListReason->get_cache();
    $this->view->blacklist      = $blacklist;
    $this->view->reason         = $reason;

}

public function listDistributorNewAction(){
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $QDistributor               = new Application_Model_Distributor();  

    $rank                       = $this->getRequest()->getParam('rank', 0);

    $text                       = $this->getRequest()->getParam('text', 0);

    $field = ['id','title','store_code'];

    $blacklist                  = $QDistributor->blackListDistributor($rank,$text,$field);

    echo json_encode($blacklist);
}

public function listDistributorForceSaleAction(){
    $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);
    $QDistributor               = new Application_Model_Distributor();
    $QBlackListReason           = new Application_Model_BlackListReason();
    $QDistributorBlackList      = new Application_Model_DistributorBlackList();  

    $rank                       = $this->getRequest()->getParam('rank');

    $blacklist                  = $QDistributor->blackListDistributor($rank );


    $reason                     = $QBlackListReason->get_cache();
    $this->view->blacklist      = $blacklist;
    $this->view->reason         = $reason;

}
public function listDistributorForQuotaAction(){
    $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);
    $QDistributor               = new Application_Model_Distributor();
    $QBlackListReason           = new Application_Model_BlackListReason();
    $QDistributorBlackList      = new Application_Model_DistributorBlackList();  

    $rank                       = $this->getRequest()->getParam('rank');

    $blacklist                  = $QDistributor->blackListDistributor($rank );


    $reason                     = $QBlackListReason->get_cache();
    $this->view->blacklist      = $blacklist;
    $this->view->reason         = $reason;

}

public function listWarehousesAction(){
    $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);
    $QWarehouses     = new Application_Model_Warehouse();

    $warehouses = $QWarehouses->get_cache();
        // echo "<pre>";
        // print_r($warehouses);die;

    $this->view->warehouses     = $warehouses;
}

public function listProductAction(){
    $this->_helper->layout->disableLayout();
    $cat_id = $this->getRequest()->getParam('cat_id');
        // $this->_helper->viewRenderer->setNoRender(true);
    $QGood = new Application_Model_Good();

    if($cat_id){
        if($cat_id ==11){
            $goods = $QGood->fetchAll(
                $QGood->select()
                ->where('cat_id = ?', $cat_id)
                ->where('del = ?', 0)
                ->order('add_time DESC')
                , 'name');
        }else{
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
            $goods = $QGood->fetchAll($where, 'name');
        }    
                // print_r($goods);die;
                // $goods = json_encode(array('goods' => $goods->toArray()));
                // echo $goods;
        $this->view->goods = $goods;
                // exit;
    } else {
        echo json_encode(array());
        exit;
    }


}
public function listProductGiftAction(){
    $this->_helper->layout->disableLayout();
    $cat_id = $this->getRequest()->getParam('cat_id');
        // $this->_helper->viewRenderer->setNoRender(true);
    $QGood = new Application_Model_Good();

    if($cat_id){
        if($cat_id ==11){
            $goods = $QGood->fetchAll(
                $QGood->select()
                ->where('cat_id = ?', $cat_id)
                ->where('del = ?', 0)
                ->order('add_time DESC')
                , 'name');
        }else{
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
            $goods = $QGood->fetchAll($where, 'name');
        }    
                // print_r($goods);die;
                // $goods = json_encode(array('goods' => $goods->toArray()));
                // echo $goods;
        $this->view->goods = $goods;
                // exit;
    } else {
        echo json_encode(array());
        exit;
    }


}

public function addQuotaAction(){

    $db = Zend_Registry::get('db');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QDistributor               = new Application_Model_Distributor();
    $QQuota                     = new Application_Model_Quota();
    $QGoodCategory              = new Application_Model_GoodCategory();
    $QArea                      = new Application_Model_Area();
    $QWarehouse                 = new Application_Model_Warehouse();
    $d_id                       = $this->getRequest()->getParam('quota');
    $distributor                  = $QDistributor->get_cache();

        // $reason                     = $QBlackListReason->get_cache();
        // $this->view->blacklist      = $blacklist;
        // $this->view->reason         = $reason;
    $where = array();
    $select_group = $db->select()
    ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
    ->where('u.user_id=?',$userStorage->id);
    $result_group = $db->fetchAll($select_group);
    $warehouse_id = "";
    if ($result_group){
        foreach ($result_group as $to) {
            $warehouse_id .= $to['warehouse_id'].',';
        }

        $where[] = $QWarehouse->getAdapter()->quoteInto('id in('.rtrim($warehouse_id, ',').')', null);
    }
    $this->view->warehouses = $QWarehouse->fetchAll($where, 'name');

    $data = array();
    if ($d_id) {

        $where = array();
        $where[] = $QQuota->getAdapter()->quoteInto('d_id = ?',$d_id);
        $quotas = $QQuota->fetchAll($where);

        foreach ($quotas as $k => $quota) {
                //get goods
            $QGood = new Application_Model_Good();
            $where = $QGood->getAdapter()->quoteInto('cat_id = ? ', $quota->cat_id);
            $goods = $QGood->fetchAll($where, 'name');

            $data[$k]['goods'] = $goods;

                //get goods color
            $where = $QGood->getAdapter()->quoteInto('id = ?', $quota->good_id);
            $good = $QGood->fetchRow($where);

            $aColor = array_filter(explode(',', $good->color));
            if ($aColor) {
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);
                $colors = $QGoodColor->fetchAll($where);
                $data[$k]['colors'] = $colors;
            }


            $data[$k]['goods'] = $goods;
            $data[$k]['quota'] = $quota;
        }



        $this->view->quota   = $data;
        $this->view->distributor  =$distributor;

    } 


            // print_r($QArea->areaForQuota());
    $good_categories   = $QGoodCategory->get_cache();
    $this->view->good_categories   = $good_categories;
    $this->view->area =  $QArea->areaForQuota();
        // print_r($blacklist);
}

public function addQuotaGroupAction(){

    $db = Zend_Registry::get('db');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QQuota                     = new Application_Model_Quota();
    $QGoodCategory              = new Application_Model_GoodCategory();
    $QArea                      = new Application_Model_Area();
    $QWarehouse                 = new Application_Model_Warehouse();

    $where = array();
    $select_group = $db->select()
    ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
    ->where('u.user_id=?',$userStorage->id);
    $result_group = $db->fetchAll($select_group);
    $warehouse_id = "";
    if ($result_group){
        foreach ($result_group as $to) {
            $warehouse_id .= $to['warehouse_id'].',';
        }

        $where[] = $QWarehouse->getAdapter()->quoteInto('id in('.rtrim($warehouse_id, ',').')', null);
    }
    $this->view->warehouses = $QWarehouse->fetchAll($where, 'name');

    $data = array();
    if ($d_id) {

        $where = array();
        $where[] = $QQuota->getAdapter()->quoteInto('d_id = ?',$d_id);
        $quotas = $QQuota->fetchAll($where);

        foreach ($quotas as $k => $quota) {
                //get goods
            $QGood = new Application_Model_Good();
            $where = $QGood->getAdapter()->quoteInto('cat_id = ? ', $quota->cat_id);
            $goods = $QGood->fetchAll($where, 'name');

            $data[$k]['goods'] = $goods;

                //get goods color
            $where = $QGood->getAdapter()->quoteInto('id = ?', $quota->good_id);
            $good = $QGood->fetchRow($where);

            $aColor = array_filter(explode(',', $good->color));
            if ($aColor) {
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);
                $colors = $QGoodColor->fetchAll($where);
                $data[$k]['colors'] = $colors;
            }


            $data[$k]['goods'] = $goods;
            $data[$k]['quota'] = $quota;
        }



        $this->view->quota   = $data;

    } 


            // print_r($QArea->areaForQuota());
    $good_categories   = $QGoodCategory->get_cache();
    $this->view->good_categories   = $good_categories;
    $this->view->area =  $QArea->areaForQuota();
        // print_r($blacklist);
}

public function viewQuotaAction(){

    $QDistributor               = new Application_Model_Distributor();
    $QQuota                     = new Application_Model_Quota();
    $QGoodCategory              = new Application_Model_GoodCategory();
    $QArea                      = new Application_Model_Area();
    $sn                         = $this->getRequest()->getParam('sn');
    $d_id                       = $this->getRequest()->getParam('quota');

    $distributor                  = $QDistributor->get_cache();
        // $reason                     = $QBlackListReason->get_cache();
        // $this->view->blacklist      = $blacklist;
        // $this->view->reason         = $reason;

    $data = array();
    if ($sn) {


        $quotas = $QQuota->getQuotalist($sn);
            //print_r($quotas);
        if ($quotas[0]['dis_type'] == 1) {
            $data = array(
                'sn'            => $sn,
                'warehouse_id'  => $quotas[0]['warehouse_id'],
                'good_id'       => $quotas[0]['good_id'],
                'good_color'    => $quotas[0]['good_color'],
                'quota_date'    => $quotas[0]['quota_date'],
                'good_type'     => $quotas[0]['good_type']
            );


            //print_r($data);
            $quotas_all = $QQuota->getQuotalistAll($data);
            
            // echo "<pre>";
            // print_r($quotas_all);
            for ($i=1; $i < count($quotas); $i++) { 
                $params['sn']           = $quotas[$i]['sn'];
                $params['good_id']      = $quotas[$i]['good_id'];
                $params['good_color']   = $quotas[$i]['good_color'];
                $params['quota_date']   = $quotas[$i]['quota_date'];
                $params['area_id']      = $quotas[$i]['area_id'];
                $params['good_type']    = $quotas[$i]['good_type'];
                $params['warehouse_id']    = $quotas[$i]['warehouse_id'];

                $quota_area[$quotas[$i]['area_id']] = $QQuota->getQuotalistArea($params);
                $this->view->quota_all          = $quotas_all;
                $this->view->quota_area          = $quota_area;

            }

        }
        $this->view->quota          = $quotas;

        $this->view->distributor    = $distributor;


    } 

            // print_r($QArea->areaForQuota());
    $good_categories   = $QGoodCategory->get_cache();
    $this->view->good_categories   = $good_categories;
    $this->view->area =  $QArea->areaForQuota();
        // print_r($blacklist);
}
    //pond
    // public function saveQuotaAction(){

    //     $QDistributor               = new Application_Model_Distributor();
    //     $QQuota                     = new Application_Model_Quota();
    //     $QQuotaLog                  = new Application_Model_QuotaLog();
    //     $QGoodCategory              = new Application_Model_GoodCategory();

    //     if ($this->getRequest()->getMethod() == 'POST'){


    //         $flashMessenger = $this->_helper->flashMessenger;
    //         try{
    //             $d_id           = $this->getRequest()->getParam('d_id');
    //             $cat_id         = $this->getRequest()->getParam('cat_id');
    //             $good_id        = $this->getRequest()->getParam('good_id');
    //             $good_color     = $this->getRequest()->getParam('good_color');
    //             $quantity       = $this->getRequest()->getParam('quantity');
    //             $quota_item       = $this->getRequest()->getParam('quota_item');

    //             $currentTime    = date('Y-m-d H:i:s');
    //             $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
    //             $user_id        = $userStorage->id;
    //             echo '<pre>';
    //             print_r($_POST);

    //         if ($quota_item) {


    //             for ($i=0; $i < count($quota_item); $i++) {

    //                 if ($quota_item[$i] != 0) {
    //                     $check =  $QQuota->checkQuantity($quota_item[$i],$quantity[$i]);
    //                     $where = $QQuota->getAdapter()->quoteInto('id = ? ',$quota_item[$i]);
    //                     if ($check['sta'] == 1) {
    //                        $data = array(
    //                             'quantity'      => $quantity[$i],
    //                             'update_by'     => $user_id,
    //                             'update_at'     => $currentTime
    //                          );
    //                        $dataLog = array(
    //                             'd_id'          => $d_id,
    //                             'quota_id'      => $quota_item[$i],
    //                             'cat_id'        => $cat_id[$i],
    //                             'good_id'       => $good_id[$i],
    //                             'good_color'    => $good_color[$i],
    //                             'quantity_old'  => $check['quantity_old'],
    //                             'quantity_new'  => $quantity[$i],
    //                             'created_by'    => $user_id,
    //                             'created_at'    => $currentTime,
    //                             'status'        => 1
    //                          );

    //                        echo "update";
    //                        print_r($data);
    //                       $QQuota->update($data,$where);
    //                       $QQuotaLog->insert($dataLog);
    //                    }
    //                 }else if ($quota_item[$i] == 0) {


    //                     $data = array(
    //                             'd_id'          => $d_id,
    //                             'cat_id'        => $cat_id[$i],
    //                             'good_id'       => $good_id[$i],
    //                             'good_color'    => $good_color[$i],
    //                             'quantity'      => $quantity[$i],
    //                             'created_by'    => $user_id,
    //                             'created_at'    => $currentTime
    //                          );
    //                        echo "insert";
    //                        print_r($data);
    //                        $id = $QQuota->insert($data);
    //                  }    

    //                }

    //             }  


    //             $flashMessenger->setNamespace('success')->addMessage('Success');
    //             $this->_redirect(HOST.'tool/quota');

    //         }
    //         catch(exception $e)
    //         {
    //             $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
    //             $this->_redirect(HOST.'tool/quota');
    //         }


    //     }


    //     // print_r($blacklist);
    // }
public function saveQuotaAction(){

    $QDistributor               = new Application_Model_Distributor();
    $QQuota                     = new Application_Model_Quota();
    $QQuotaLog                  = new Application_Model_LogQuotaTran();
    $QGoodCategory              = new Application_Model_GoodCategory();

    if ($this->getRequest()->getMethod() == 'POST'){

        $sn             = $this->getRequest()->getParam('sn');

        $flashMessenger = $this->_helper->flashMessenger;
        if ($sn) {
            try{

                $all_qty        = $this->getRequest()->getParam('all_qty');
                $all_qty_2      = $this->getRequest()->getParam('all_qty_2');
                $area_id        = $this->getRequest()->getParam('area_id');
                $not_allow_area = $this->getRequest()->getParam('not_allow_area');
                $quota_qty      = $this->getRequest()->getParam('quota_qty');
                $dealer         = $this->getRequest()->getParam('dealer');
                $rank           = $this->getRequest()->getParam('rank');
                $warehouse_id   = $this->getRequest()->getParam('warehouse_id');
                $currentTime    = date('Y-m-d H:i:s');
                $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                $user_id        = $userStorage->id;
                // $quota_d        = explode('/', $quota_date);
                // $quota_date     = $quota_d[2].'-'.$quota_d[1].'-'.$quota_d[0];
                // print_r($_POST);die();
                if ($area_id) {

                //cannel 99 is head quantity all

                    $data_q['created_by']   = $user_id;
                    $data_q['created_at']   = $currentTime;
                    $data_q['quantity']     = $all_qty_2;
                    $data_q['quantity_all'] = $all_qty;
                    $where = array();
                    $where[] = $QQuota->getAdapter()->quoteInto('sn = ?', $sn);
                    $where[] = $QQuota->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                    $where[] = $QQuota->getAdapter()->quoteInto('channel = ?', '99');
                    $QQuota->update($data_q,$where);
                    for ($i=0; $i < count($area_id); $i++) {

                        $data = array(
                            'quantity'      => $quota_qty[$i],
                            'update_by'     => $user_id,
                            'update_at'     => $currentTime, 
                        );

                        if(in_array($area_id[$i], $not_allow_area)){
                            $data['quantity'] = 0;
                            $data['not_allow_area'] = 1;
                        }else{
                            $data['not_allow_area'] = null;
                        }

                        $where = array();
                        $where[] = $QQuota->getAdapter()->quoteInto('sn = ?', $sn);
                        $where[] = $QQuota->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                        $where[] = $QQuota->getAdapter()->quoteInto('area_id = ?', $area_id[$i]);
                        $QQuota->update($data,$where);

                    }  
                }    

            // if ($sn and in_array($rank,array(1,10))) {
                if ($sn and in_array($rank,array(3,2,10,4))) {

                   $data = array(
                    'quantity'      => $all_qty,
                    'update_by'     => $user_id,
                    'update_at'     => $currentTime, 
                );
                   $where[] = $QQuota->getAdapter()->quoteInto('sn = ?', $sn);
                   $where[] = $QQuota->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                   $QQuota->update($data,$where);
               }   
               $flashMessenger->setNamespace('success')->addMessage('Success');
               $this->_redirect(HOST.'tool/quota-manage');

           }
           catch(exception $e)
           {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'tool/quota-manage');
        }
    }else{

        try{

            $rank           = $this->getRequest()->getParam('rank');
            $cat_id         = $this->getRequest()->getParam('cat_id');
            $good_id        = $this->getRequest()->getParam('good_id');
            $good_color     = $this->getRequest()->getParam('good_color');
            $good_type     = $this->getRequest()->getParam('good_type');
            $quota_date     = $this->getRequest()->getParam('quota_date');
            $all_qty        = $this->getRequest()->getParam('all_qty');
            $all_qty_2      = $this->getRequest()->getParam('all_qty_2',NULL);
            $area_id        = $this->getRequest()->getParam('area_id',NULL);
            $not_allow_area = $this->getRequest()->getParam('not_allow_area');
            $quota_qty      = $this->getRequest()->getParam('quota_qty');
            $dealer         = $this->getRequest()->getParam('dealer');
            $warehouse_id   = $this->getRequest()->getParam('warehouse_id');

                //print_r($_POST);die();
            $currentTime    = date('Y-m-d H:i:s');
            $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
            $user_id        = $userStorage->id;
            $quota_d        = explode('/', $quota_date);
            $quota_date     = $quota_d[2].'-'.$quota_d[1].'-'.$quota_d[0];
            $sn = date('YmdHis') . substr(microtime(), 2, 4);
            // if ($rank == 7) {
            if ($rank == 1) {

                $data_q['sn']             = $sn;
                $data_q['cat_id']         = $cat_id;
                $data_q['dis_type']       = $rank;
                $data_q['good_id']        = $good_id;
                $data_q['good_color']     = $good_color;
                $data_q['good_type']      = $good_type;
                $data_q['channel']        = $dealer;
                $data_q['quota_date']     = $quota_date;
                $data_q['created_by']     = $user_id;
                $data_q['created_at']     = $currentTime;
                $data_q['quantity']       = $all_qty_2;
                $data_q['quantity_all']   = $all_qty;
                $data_q['warehouse_id']   = $warehouse_id;

                $id = $QQuota->insert($data_q);


                for ($i=0; $i < count($quota_qty); $i++) {

                    $data = array(
                        'sn'            => $sn,
                        'cat_id'        => $cat_id,
                        'dis_type'      => $rank,
                        'good_id'       => $good_id,
                        'good_color'    => $good_color,
                        'good_type'     => $good_type,
                        'area_id'       => $area_id[$i],
                        'quantity'      => $quota_qty[$i],
                        'created_by'    => $user_id,
                        'created_at'    => $currentTime,
                        'quota_date'    => $quota_date,
                        'warehouse_id'  => $warehouse_id
                    );

                    if(in_array($area_id[$i], $not_allow_area)){
                        $data['quantity'] = 0;
                        $data['not_allow_area'] = 1;
                    }

                    $id = $QQuota->insert($data);

                }  
            }else{
                $data = array(
                    'sn'            => $sn,
                    'cat_id'        => $cat_id,
                    'dis_type'      => $rank,
                    'good_id'       => $good_id,
                    'good_color'    => $good_color,
                    'good_type'     => $good_type,
                    'quantity'      => $all_qty,
                    'created_by'    => $user_id,
                    'created_at'    => $currentTime,
                    'quota_date'    => $quota_date,
                    'warehouse_id'  => $warehouse_id
                );

                $id = $QQuota->insert($data);
            }   

            $flashMessenger->setNamespace('success')->addMessage('Success');
            $this->_redirect(HOST.'tool/quota-manage');

        }
        catch(exception $e)
        {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'tool/quota-manage');
        }

    }
}


        // print_r($blacklist);
}


public function checkQuantityQuotaAction()
{   
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $quota_date   = $this->getRequest()->getParam('quota_date');
    $good_id      = $this->getRequest()->getParam('good_id');
    $good_color   = $this->getRequest()->getParam('good_color');
    $good_type   = $this->getRequest()->getParam('good_type');
    $area         = $this->getRequest()->getParam('area');
    $act         = $this->getRequest()->getParam('act');
    $sn         = $this->getRequest()->getParam('sn');
    $warehouse_id   = $this->getRequest()->getParam('warehouse_id');
        // print_r($_POST);
        // echo $quota_date.'='.$good_id.'='.$good_color;
    $db = Zend_Registry::get('db');
    if ($act == 'all_quota') {

        $select_q = $db->select()
        ->from(array('q' => 'quota_oppo'), array('q.area_id'));
                // $select_q->where('q.dis_type = ?', 1);
                // $select_q->where('q.status = ?',1);
        $select_q->where('q.quantity = ?', 0);
        $select_q->where('q.sn = ?', $sn);
        $select_q->where('q.warehouse_id = ?', $warehouse_id);
        $inArea = $db->fetchAll($select_q);


                // start : old
                // $select_m = $db->select()
                //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
                // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
                // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
                // $select_m->where('a.id in (?)', $inArea );
                // $select_m->where('lq.good_id = ?', $good_id);
                // $select_m->where('lq.good_color = ?', $good_color);

                // $select_m->where('lq.warehouse_id = ?', $warehouse_id);

                // $select_m->where('lq.good_type = ?', $good_type);
                // $select_m->where('dg.group_type_id = ?', 1);

                // // $select_m->where('d.group_id = ?', 1);
                // // $select_m->where('d.quota_channel is null',1);
                // // $select_m->where('(d.rank = 7');
                // // $select_m->Orwhere('d.rank = 8)');
                // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
                // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
        $select_m->where('a.id in (?)', $inArea );
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('dg.group_type_id = ?', 1);

                // $select_m->where('d.group_id = ?', 1);
                // $select_m->where('d.quota_channel is null',1);
                // $select_m->where('(d.rank = 7');
                // $select_m->Orwhere('d.rank = 8)');
        $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
        

        $isSum = $db->fetchOne($select_m);
        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();
    }else if($act == 'ka'){

               // start : old
               //  $select_m = $db->select()
               //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
               //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
               //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

               //  $select_m->where('dg.group_type_id IN (?)', [3]);
               //  // $select_m->orWhere('d.group_id = ?', 3);
               //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
               //  // $select_m->orWhere('d.quota_channel = 1)');
               //  $select_m->where('lq.good_id = ?', $good_id);
               //  $select_m->where('lq.good_color = ?', $good_color);
               //  $select_m->where('lq.good_type = ?', $good_type);

               //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

               //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
               // // echo $select_m;
               //  $isSum = $db->fetchOne($select_m);
               // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [3]);
                // $select_m->orWhere('d.group_id = ?', 3);
                // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
                // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);
        $select_m->where('m.type = ?', $good_type);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
               // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();

    }else if($act == 'operator'){

               // start : old
               //  $select_m = $db->select()
               //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
               //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
               //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

               //  $select_m->where('dg.group_type_id IN (?)', [4]);
               //  // $select_m->orWhere('d.group_id = ?', 3);
               //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
               //  // $select_m->orWhere('d.quota_channel = 1)');
               //  $select_m->where('lq.good_id = ?', $good_id);
               //  $select_m->where('lq.good_color = ?', $good_color);
               //  $select_m->where('lq.good_type = ?', $good_type);

               //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

               //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
               // // echo $select_m;
               //  $isSum = $db->fetchOne($select_m);
               // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [4]);
                // $select_m->orWhere('d.group_id = ?', 3);
                // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
                // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);
        $select_m->where('m.type = ?', $good_type);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
               // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();

    }else if($act == 'kr'){


               // start : old
               //  $select_m = $db->select()
               //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
               //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
               //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

               //  $select_m->where('dg.group_type_id = ?', 2);
               //  // $select_m->orWhere('d.group_id = ?', 2);
               //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
               //  // $select_m->orWhere('d.quota_channel = 1)');
               //  $select_m->where('lq.good_id = ?', $good_id);
               //  $select_m->where('lq.good_color = ?', $good_color);
               //  $select_m->where('lq.good_type = ?', $good_type);

               //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

               //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
               // // echo $select_m;
               //  $isSum = $db->fetchOne($select_m);
               // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id = ?', 2);
                // $select_m->orWhere('d.group_id = ?', 2);
                // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
                // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);
        $select_m->where('m.type = ?', $good_type);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
               // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();

    }else if($act == 'brandshop'){


                   // start : old
                   //  $select_m = $db->select()
                   //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
                   //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
                   //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

                   //  $select_m->where('dg.group_type_id IN (?)', [10,11]);
                   //  // $select_m->orWhere('d.group_id = ?', 2);
                   //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
                   //  // $select_m->orWhere('d.quota_channel = 1)');
                   //  $select_m->where('lq.good_id = ?', $good_id);
                   //  $select_m->where('lq.good_color = ?', $good_color);

                   //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

                   //  $select_m->where('lq.good_type = ?', $good_type);
                   //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
                   // // echo $select_m;
                   //  $isSum = $db->fetchOne($select_m);
                   // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [10,11]);
                    // $select_m->orWhere('d.group_id = ?', 2);
                    // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
                    // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
                   // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();

    }else{

            // start : old
            //     $select_m = $db->select()
            //         ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            //     $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            //     $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            //     $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            //     $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            //     $select_m->where('a.id = ?', $area );
            //     $select_m->where('lq.good_id = ?', $good_id);
            //     $select_m->where('lq.good_color = ?', $good_color);
            //     $select_m->where('lq.good_type = ?', $good_type);

            //     $select_m->where('lq.warehouse_id = ?', $warehouse_id);

            //     // $select_m->where('d.quota_channel is null',1);
            //     $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);


            // $isSum = $db->fetchOne($select_m);
            // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('a.id = ?', $area );
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);
        $select_m->where('m.type = ?', $good_type);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

            // $select_m->where('d.quota_channel is null',1);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);


        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
        echo $num;
        exit();

    }
}

public function disableQuotaAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $flashMessenger             = $this->_helper->flashMessenger;
    $act         = $this->getRequest()->getParam('act');
    $sn         = $this->getRequest()->getParam('sn');

    $currentTime                = date('Y-m-d H:i:s');
    $userStorage                = Zend_Auth::getInstance()->getStorage()->read();
    $user_id                    = $userStorage->id;
    $QQuota                     = new Application_Model_Quota();

    if ($act) {

        try{


            $data = array(
                'update_by'     => $user_id,
                'update_at'     => $currentTime,

            );
            if ($act == 'disable') {
                $data['status'] = 0;
            }else if ($act == 'enable') {
                $data['status'] = 1;
            }

            $where = $QQuota->getAdapter()->quoteInto('sn = ? ',$sn);
            $QQuota->update($data,$where);


            $flashMessenger->setNamespace('success')->addMessage('Success');
            $this->_redirect(HOST.'tool/quota-manage');
        }
        catch(exception $e){
            $flashMessenger->setNamespace('error')->addMessage('Can not Disable');
            $this->_redirect(HOST.'tool/quota-manage');

            
        }


    }

    
}

public function disableQuotaDistributorAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $flashMessenger             = $this->_helper->flashMessenger;
    $act         = $this->getRequest()->getParam('act');
    $sn         = $this->getRequest()->getParam('sn');

    $currentTime                = date('Y-m-d H:i:s');
    $userStorage                = Zend_Auth::getInstance()->getStorage()->read();
    $user_id                    = $userStorage->id;
    $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();

    if ($act) {

        try{
            $data = array(
                'update_by'     => $user_id,
                'update_at'     => $currentTime,          
            );
            if ($act == 'disable') {
                $data['status'] = 0;
            }else if ($act == 'enable') {
                $data['status'] = 1;
            }

            $where = $QQuotaOppoByDistributor->getAdapter()->quoteInto('sn = ? ',$sn);
            $QQuotaOppoByDistributor->update($data,$where);


            $flashMessenger->setNamespace('success')->addMessage('Success');
            $this->_redirect(HOST.'tool/quota-manage-distributor');
        }
        catch(exception $e){
            $flashMessenger->setNamespace('error')->addMessage('Can not Disable');
            $this->_redirect(HOST.'tool/quota-manage-distributor');

            
        }


    }

    
}

public function newDisableQuotaDistributorAction(){

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');
    $act = $this->getRequest()->getParam('act');

    $currentTime = date('Y-m-d H:i:s');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $user_id = $userStorage->id;
    $QNQD = new Application_Model_NewQuotaDistributor();

    if ($act) {

        try{
            $data = array(
                'update_date' => $currentTime,
                'update_by' => $user_id
            );

            if ($act == 'disable') {
                $data['status'] = 0;
            }else if ($act == 'enable') {
                $data['status'] = 1;
            }

            $where = $QNQD->getAdapter()->quoteInto('id = ? ',$id);
            $test = $QNQD->update($data,$where);

            $flashMessenger->setNamespace('success')->addMessage('Success');
            $this->_redirect(HOST.'tool/new-view-quota-distributor?id=' . $id);
        }
        catch(exception $e){
            $flashMessenger->setNamespace('error')->addMessage('Can not Disable');
            $this->_redirect(HOST.'tool/new-view-quota-distributor?id=' . $id);
        }
    }
}

public function functionEditAction()
{
    $flashMessenger = $this->_helper->flashMessenger;

    $so                 = $this->getRequest()->getParam('so');
    $customer_name      = $this->getRequest()->getParam('customer_name');
    $tax                = $this->getRequest()->getParam('tax');
    $add                = $this->getRequest()->getParam('add');
    $customer           = $this->getRequest()->getParam('customer',null);

    $QMarket               = new Application_Model_Market();
    $QCustomerBrandShop    = new Application_Model_CustomerBrandShop();
    if ($this->getRequest()->getMethod() == 'POST'){    
       try{

        if($customer == 1){
            $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('m' => 'market'), array('m.customer_id'));
            $select->where('m.sn_ref = ?',$so);

            $id_customer = $db->fetchOne($select);
            if ($id_customer) {

                $params =array();
                $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $id_customer);

                if (isset($customer_name) and $customer_name) {
                    $params['customer_name'] = $customer_name ;
                }
                if (isset($tax) and $tax) {
                    $params['tax_number'] = $tax ;
                }
                if (isset($add) and $add) {
                    $params['address_tax'] = $add ;
                }
                if ($params) {
                    $QCustomerBrandShop->update($params,$where);


                    $flashMessenger->setNamespace('success')->addMessage('Success');
                    $this->_redirect(HOST.'tool/function-edit');
                }else{
                    $flashMessenger->setNamespace('error')->addMessage('ไม่กรอกข้อมูล');
                    $this->_redirect(HOST.'tool/function-edit');
                }    

            }else{
                $flashMessenger->setNamespace('error')->addMessage('ไม่พบ SO นี้ หรือ ไม่มี id_customer');
                $this->_redirect(HOST.'tool/function-edit');
            }
        }
    }
    catch(exception $e)
    {
        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST.'tool/function-edit');
            // $this->_redirect(HOST.'tool/function-edit');
    }
}

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;
        // die;
        // 
}
public function customerEditAction()
{
    $flashMessenger = $this->_helper->flashMessenger;

    $so                 = $this->getRequest()->getParam('sn');
    $name               = $this->getRequest()->getParam('name');
    $tax                = $this->getRequest()->getParam('tax');
    $add                = $this->getRequest()->getParam('add');
    $customer                = $this->getRequest()->getParam('customer',null);

    $QMarket               = new Application_Model_Market();
    $QCustomerBrandShop    = new Application_Model_CustomerBrandShop();

    try{
        $so_ = explode('\'', $so);

        if($customer == 1){
            $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('m' => 'market'), array('m.customer_id'));
            $select->where('m.sn_ref = ?',$so_[1]);

            $id_customer = $db->fetchOne($select);

            if ($id_customer) {

                $params =array();
                $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $id_customer);

                if (isset($name) and $name) {
                    $params['customer_name'] = $name ;
                }
                if (isset($tax) and $tax) {
                    $params['tax_number'] = $tax ;
                }
                if (isset($add) and $add) {
                    $params['address_tax'] = $add ;
                }
                if ($params) {

                    $QCustomerBrandShop->update($params,$where);
                    echo '1';
                    exit();
                }

            }else{
                echo '2';
                exit();
            }
        }
    }
    catch(exception $e)
    {
        echo '3';
    }


    exit();
        // die;
        // 
}

public function editPoAction()
{
    $flashMessenger = $this->_helper->flashMessenger;

    $sn               = $this->getRequest()->getParam('sn');
    $po               = $this->getRequest()->getParam('po');

    $QTag             = new Application_Model_Tag();
    $QTO              = new Application_Model_TagObject();
    try{

        $sn = str_replace("'", '', $sn);

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('t' => 'tag'), array('id'));
        $select->where('t.name = ?',$po);
        $id_po = $db->fetchOne($select);

        if(!$id_po){

            $data = array(
                'name' => $po
            );

            $id_po = $QTag->insert($data);
        }


        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('to' => 'tag_object'), array('object_id'));
        $select->where('to.object_id = ?',$sn);
        $get_po = $db->fetchOne($select);

        if($get_po){
            $where = $QTO->getAdapter()->quoteInto('object_id = ?', $sn);
            $QTO->update(['tag_id' => $id_po], $where);
        }else{
            $data = array(
                'tag_id' => $id_po,
                'object_id' => $sn,
                'type' => 1
            );
            $QTO->insert($data);
        }

        echo '1';
    }
    catch(exception $e)
    {
        echo '-1';
    }
    exit();
}

public function quotaUploadAction()
{
 $QGoodCategory              = new Application_Model_GoodCategory();
 $good_categories   = $QGoodCategory->get_cache();
 $this->view->good_categories   = $good_categories;
}

    //public function saveUploadQuotaProductAction()
public function oppoUploadQuotaProductsSaveAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'oppo-upload-quota-products-save.php';
}

public function FunctionName($value='')
{
 $this->_helper->layout->disableLayout();

 $QFileLog        = new Application_Model_FileUploadLog();
 $QOppoGreenAll   = new Application_Model_OppoAllGreenRewardCn();
 $QCreditNote   = new Application_Model_CreditNote();

 $generateCN         = $_POST["generateCN"];
 $activeCN           = $_POST["activeCN"];
 $key_sn = date('YmdHis') . substr(microtime(), 2, 1);

 if ($this->getRequest()->getMethod() == 'POST') {

    define('MASS_BVG_LIST_ROW_START', 2);
    define('MASS_BVG_LIST_COL_AREA_ID', 0);
    define('MASS_BVG_LIST_COL_QUOTY_QTY', 1);





    set_time_limit(0);
    ini_set('memory_limit', -1);
    $db = Zend_Registry::get('db');

    $progress = new My_File_Progress('parent.set_progress');
    $progress->flush(0);

    $upload = new Zend_File_Transfer();

    $uniqid = uniqid('', true);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
    . DIRECTORY_SEPARATOR . 'mou'
    . DIRECTORY_SEPARATOR . $userStorage->id
    . DIRECTORY_SEPARATOR . $uniqid;

    if (!is_dir($uploaded_dir))
        @mkdir($uploaded_dir, 0777, true);


    $upload->setDestination($uploaded_dir);

    $upload->setValidators(array(
        'Size' => array('min' => 50, 'max' => 10000000),
        'Count' => array('min' => 1, 'max' => 1),
        'Extension' => array('xlsx', 'xls'),
    ));

        if (!$upload->isValid()) { // validate IF
            $errors = $upload->getErrors();
            $sError = null;

            if ($errors and isset($errors[0]))
                switch ($errors[0]) {
                    case 'fileUploadErrorIniSize':
                    $sError = 'File size is too large';
                    break;
                    case 'fileMimeTypeFalse':
                    $sError = 'The file you selected weren\'t the type we were expecting';
                    break;
                    case 'fileExtensionFalse':
                    $sError = 'Please choose a file in XLS or XLSX format.';
                    break;
                    case 'fileCountTooFew':
                    $sError = 'Please choose a PO file (in XLS or XLSX format)';
                    break;
                    case 'fileUploadErrorNoFile':
                    $sError = 'Please choose a PO file (in XLS or XLSX format)';
                    break;
                    case 'fileSizeTooBig':
                    $sError = 'File size is too big';
                    break;
                }

                $this->view->error = $sError;

            }else {
                try{
                    $db->beginTransaction();          
                    $path_info = pathinfo($upload->getFileName());
                    $filename =  $path_info['filename'];
                    $extension = $path_info['extension'];

                    $old_name = $filename . '.' . $extension;
                    $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                    if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                        rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                    } else {
                        $new_name = $old_name;
                    }

                    $upload->addFilter('Rename',
                       array('target' => $uploaded_dir. DIRECTORY_SEPARATOR .$new_name,
                         'overwrite' => true));

                    $upload->receive();
                    chmod($uploaded_dir. DIRECTORY_SEPARATOR .$new_name, 777);


                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'Quota',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $number_of_order = 0;
                    $error_list = array();
                    $success_list = array();
                    $listOppoGreenAll = array();

                    require_once 'PHPExcel.php';
                    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                    $cacheSettings = array('memoryCacheSize' => '8MB');
                    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                    switch ($extension) {
                        case 'xls':
                        $objReader = PHPExcel_IOFactory::createReader('Excel5');
                        break;
                        case 'xlsx':
                        $objReader = PHPExcel_IOFactory::createReader('Excel2007');
                        break;
                        default:
                        throw new Exception("Invalid file extension");
                        break;
                    }

                    $objReader->setReadDataOnly(true);

                    $objPHPExcel = $objReader->load($uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

                    $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
                    $total_order_row = $highestRow - MASS_BVG_LIST_ROW_START + 1;

                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
                    $status=1; $d_id='';   

                    $data_sn=null;
                    $sn = date('YmdHis') . substr(microtime(), 2, 1);
                    for ($i = MASS_BVG_LIST_ROW_START; $i <= $highestRow; $i++) {

                        $round_no = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_AREA_ID, $i)
                            ->getValue());

                        $round_year = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_QUOTY_QTY, $i)
                            ->getValue());

                        
                        $checkAirNumber = substr($air_number,0,1);
                        if ($checkAirNumber != "'")  $air_number = "'".$air_number;
                        
                        $data_row=[];
                        $data_row["round_no"]                 =   $round_no;
                        $data_row["round_year"]               =   $round_year;
                        $data_row["air_number"]               =   $air_number ;
                        $data_row["distributor_id"]           =   $distributor_id;
                        $data_row["distributor_name"]         =   $distributor_name;
                        $data_row["store_id"]                 =   $store_id;
                        $data_row["store_name"]               =   $store_name;
                        $data_row["key_sn"]                   =   $key_sn;
                        $data_row["start_date"]               =   $start_date;
                        $data_row["end_date"]                 =   $end_date;
                        $data_row["shop_type"]                =   $shop_type;
                        $data_row["total_reward_price"]       =   $total_reward_price;
                        $data_row["tax_price"]                =   $tax_price;
                        $data_row["creditnote_price"]         =   $creditnote_price;
                        $data_row["asm_confirm_by"]           =   $asm_confirm_by;
                        $data_row["asm_confirm_date"]         =   $asm_confirm_date;
                        $data_row["confirm_by"]               =   $userStorage->id;
                        $data_row["confirm_date"]             =   date('Y-m-d H:i:s');
                        $data_row["status_cn"]                =   $status_cn;
                        $data_row["reason_remark"]            =   $reason_remark;
                        $data_row["create_date"]              =   date('Y-m-d H:i:s');

                        $data_row_keys = (array_keys($data_row));
                        foreach ($data_row_keys as  $key) {
                            // echo $key ."=".$data_row[$key].'<br>';
                            if(!$data_row[$key])
                            {
                                $data_row[$key]=null;
                            }
                        }

                        if($data_row)
                        {
                            $findDuplicate = null;
                            $findDuplicate = $QOppoGreenAll->Duplicate($data_row["air_number"],$data_row["distributor_id"],$data_row["store_id"] );

                            if ($findDuplicate) {
                                array_push($error_list,$data_row);
                            }
                            else {
                                array_push($success_list,$data_row);
                                $insertData = $QOppoGreenAll->insert($data_row);
                            }
                            $number_of_order++;
                        }
                    }

                    $data = array(
                        'total' => $total_order_row,
                        'failed' => count($error_list),
                        'succeed' => $total_order_row - count($error_list),
                    );

                    // xuất file excel các order lỗi
                    if (is_array($error_list) && count($error_list) > 0) 
                    {

                        $data['error_file_name'] = $d_id.'-'.'FAILED-' . md5(microtime(true) . uniqid('', true)) . '.' . $extension;
                        // xuất excel @@
                        //
                        //$error_file_name = date('YmdHis') . substr(microtime(), 2, 4);
                        //$data['error_file_name'] = 'FAILED-' .$error_file_name.'.' . $extension;

                        $objPHPExcel_out = new PHPExcel();
                        $objPHPExcel_out->createSheet();
                        $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
                        //
                        // get product list
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_NO, 1, 'Round_No');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_YEAR, 1, 'Round_Year');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_AIR_NUMBER, 1, 'Air_number');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_ID, 1, 'Distributor_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_NAME, 1, 'Distributor_Name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_ID, 1, 'Store_id');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_NAME, 1, 'Store_Name');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_KEY_SN, 1, 'Key_sn');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_START_DATE, 1, 'Start_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_END_DATE, 1, 'End_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_SHOP_TYPE, 1, 'Shop_type');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE, 1, 'Total_reward_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TAX_PRICE, 1, 'Tax_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CREDITNOTE_PRICE, 1, 'Creditnote_price');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_BY, 1, 'Asm_confirm_by');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_DATE, 1, 'Asm_confirm_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_BY, 1, 'Confirm_by');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_DATE, 1, 'Confirm_date');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STATUS_CN, 1, 'Status_cn');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_REASON_REMARK, 1, 'Reason_remark');
                        


                        // các dòng lỗi
                        $i = 2;

                        foreach ($error_list as $key => $row) {
                         //    $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i, $row['d_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_NO, $i, $row['round_no']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ROUND_YEAR, $i, $row['round_year']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_AIR_NUMBER, $i, $row['air_number']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_ID, $i, $row['distributor_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DISTRIBUTOR_NAME, $i, $row['distributor_name']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_ID, $i, $row['store_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STORE_NAME, $i, $row['store_name']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_KEY_SN, $i, $row['key_sn']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_START_DATE, $i, $row['start_date']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_END_DATE, $i, $row['end_date']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_SHOP_TYPE, $i, $row['shop_type']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TOTAL_REWARD_PRICE, $i, $row['total_reward_price']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_TAX_PRICE, $i, $row['tax_price']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CREDITNOTE_PRICE, $i, $row['creditnote_price']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_BY, $i, $row['asm_confirm_by']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_ASM_CONFIRM_DATE, $i, $row['asm_confirm_date']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_BY, $i, $row['confirm_by']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_CONFIRM_DATE, $i, $row['confirm_date']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_STATUS_CN, $i, $row['status_cn']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_REASON_REMARK, $i, $row['reason_remark']);
                            $i++;
                        }

                        //
                        switch ($extension) {
                            case 'xls':
                            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel_out);
                            break;
                            case 'xlsx':
                            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
                            break;
                            default:
                            throw new Exception("Invalid file extension");
                            break;
                        }

                        $new_file_dir = $uploaded_dir . DIRECTORY_SEPARATOR . $data['error_file_name'];

                        //Tanong
                       // $objWriter->save($new_file_dir);
                    }

                    
                    // END IF // xuất file excel các order lỗi

                    $data['success_file_name'] = $key_sn;
                    $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                    $QFileLog->update($data, $where);

                    $this->view->success_list = $success_list;
                    $this->view->error_list = $error_list;
                    $this->view->objWorksheet = $objWorksheet;
                    $this->view->number_of_order = $number_of_order;

                    //commit
                    $db->commit();

                    $this->view->error_file = isset($data['error_file_name']) ? (HOST
                        . 'files'
                        . DIRECTORY_SEPARATOR . 'mou'
                        . DIRECTORY_SEPARATOR . $userStorage->id
                        . DIRECTORY_SEPARATOR . $uniqid
                        . DIRECTORY_SEPARATOR . $data['error_file_name']) : false;

                    $progress->flush(100);


                }
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
            }// end of check file
        }

        

       }// end of check POST
   }



   public function saveQuotaDistributorAction()
   {
    $QDistributor               = new Application_Model_Distributor();
    $QQuota                     = new Application_Model_Quota();
    $QQuotaLog                  = new Application_Model_LogQuotaTran();
    $QuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();
    $QGoodCategory              = new Application_Model_GoodCategory();
        // echo "<pre>";

    $flashMessenger = $this->_helper->flashMessenger;
    $db = Zend_Registry::get('db');

    if ($this->getRequest()->getMethod() == 'POST'){


        $sn             = $this->getRequest()->getParam('sn');

        if ($sn) {
            try{
                $db->beginTransaction();
                $type           = $this->getRequest()->getParam('type');
                $cat_id         = $this->getRequest()->getParam('cat_id');
                $warehouses     = $this->getRequest()->getParam('warehouses');
                $good_id        = $this->getRequest()->getParam('good_id');
                $good_color     = $this->getRequest()->getParam('good_color');
                $quota_date     = $this->getRequest()->getParam('quota_date');
                $rank           = $this->getRequest()->getParam('rank');
                $d_id           = $this->getRequest()->getParam('d_id');
                $qty            = $this->getRequest()->getParam('qty');
                $currentTime    = date('Y-m-d H:i:s');
                $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                $user_id        = $userStorage->id;
                // $quota_d        = explode('/', $quota_date);
                // $quota_date     = $quota_d[2].'-'.$quota_d[1].'-'.$quota_d[0];
                
                
                for ($i=0; $i < count($d_id); $i++) { 

                    $data = array(
                        'sn'            => $sn,
                    // 'order_type'    => $type,
                        'warehouse'     => $warehouses,
                        'cat_id'        => $cat_id,
                        'good_id'       => $good_id,
                        'good_color'    => $good_color,
                        'quota_date'    => $quota_date,
                        'channel'       => $rank,
                        'd_id'          => $d_id[$i], 
                        'quantity'      => $qty[$i], 
                    );
                    $QuotaOppoByDistributor->insert($data);


                }



                $db->commit();  
                $flashMessenger->setNamespace('success')->addMessage('Success');
                $this->_redirect(HOST.'tool/view-quota-distributor?sn='.$sn);
                
                
            }
            catch(exception $e)
            {  
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'tool/view-quota-distributor?sn='.$sn);
            }
        }else{


            try{
                $db->beginTransaction();
                $type           = $this->getRequest()->getParam('type');
                $cat_id         = $this->getRequest()->getParam('cat_id');
                $warehouses     = $this->getRequest()->getParam('warehouses');
                $good_id        = $this->getRequest()->getParam('good_id');
                $good_color     = $this->getRequest()->getParam('good_color');
                $quota_date     = $this->getRequest()->getParam('quota_date');
                $rank           = $this->getRequest()->getParam('rank');
                $d_id           = $this->getRequest()->getParam('d_id');
                $qty            = $this->getRequest()->getParam('qty');
                $currentTime    = date('Y-m-d H:i:s');
                $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
                $user_id        = $userStorage->id;
                $quota_d        = explode('/', $quota_date);
                $quota_date     = $quota_d[2].'-'.$quota_d[1].'-'.$quota_d[0];
                $sn = date('YmdHis') . substr(microtime(), 2, 4);

                for ($i=0; $i < count($d_id); $i++) { 

                    $data = array(
                        'sn'            => $sn,
                        'order_type'    => $type,
                        'warehouse'     => $warehouses,
                        'cat_id'        => $cat_id,
                        'good_id'       => $good_id,
                        'good_color'    => $good_color,
                        'quota_date'    => $quota_date,
                        'channel'       => $rank,
                        'd_id'          => $d_id[$i], 
                        'quantity'           => $qty[$i], 
                    );
                    $QuotaOppoByDistributor->insert($data);
                // print_r($data);

                }

                $db->commit();
                $flashMessenger->setNamespace('success')->addMessage('Success');
                $this->_redirect(HOST.'tool/quota-manage-distributor');


            }
            catch(exception $e)
            {
                die($e);
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'tool/quota-manage-distributor');
            }

        }
    }

}

public function newSaveQuotaDistributorAction()
{

    $flashMessenger = $this->_helper->flashMessenger;
    $db = Zend_Registry::get('db');

    if ($this->getRequest()->getMethod() == 'POST'){

        $id = $this->getRequest()->getParam('id');
        $d_id = $this->getRequest()->getParam('d_id');
        $g_id = $this->getRequest()->getParam('g_id');
        $c_id = $this->getRequest()->getParam('c_id');
        $quota_qty = $this->getRequest()->getParam('quota_qty');
        $check_over_quota = $this->getRequest()->getParam('check_over_quota');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $good_type = $this->getRequest()->getParam('good_type');

        $currentTime    = date('Y-m-d H:i:s');
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
        $user_id        = $userStorage->id;

        try{
            $db->beginTransaction();

            $QNQD = new Application_Model_NewQuotaDistributor();
            $QNQDD = new Application_Model_NewQuotaDistributorDetails();
            $QLQTD = new Application_Model_LogQuotaTranDistributor();

            foreach ($c_id as $key_main => $value_main) {

                $where = [];
                $where[] = $db->quoteInto('nqd_id = ?',$id);
                $where[] = $db->quoteInto('good_color = ?',$value_main);
                $where[] = $db->quoteInto('status <> ?',0);

                $data = array(
                    'update_date' => $currentTime,
                    'update_by' => $user_id,
                    'num' => $quota_qty[$key_main]
                );

                $getQuota = $QNQDD->fetchRow($where);
                if(!isset($getQuota['status'])){

                    $num = $quota_qty[$key_main];
                    $status = 0;

                    if(in_array($value_main,$check_over_quota)){
                        $num = 0;
                        $status = 2;
                    }

                    $data = array(
                        'nqd_id' => $id,
                        'good_color' => $value_main,
                        'num' => $num,
                        'status' => 1,
                        'created_date' => $currentTime,
                        'created_by' => $user_id
                    );
                    $QNQDD->insert($data);
                }else{
                    if(!in_array($value_main,$check_over_quota)){
                        $QNQDD->update($data,$where);
                    }
                }

            }

            $where = [];
            $where[] = $db->quoteInto('nqd_id = ?',$id);
            $where[] = $db->quoteInto('good_color in (?)',$check_over_quota);
            $where[] = $db->quoteInto('status <> ?',0);

            $data = array(
                'update_date' => $currentTime,
                'update_by' => $user_id,
                'status' => 2
            );

            $QNQDD->update($data,$where);

                // foreach ($check_over_quota as $key => $value) {
                //     $where = [];
                //     $where[] = $db->quoteInto('warehouse_id = ?',$warehouse_id);
                //     $where[] = $db->quoteInto('d_id = ?',$d_id);
                //     $where[] = $db->quoteInto('good_id = ?',$g_id);
                //     $where[] = $db->quoteInto('good_color = ?',$value);
                //     $where[] = $db->quoteInto('good_type = ?',$good_type);
                //     $where[] = $db->quoteInto('add_time > ?',date('Y-m-d'));

                //     $QLQTD->delete($where);
                // }

            $where = [];
            $where[] = $db->quoteInto('nqd_id = ?',$id);
            $where[] = $db->quoteInto('good_color not in (?)',$check_over_quota);
            $where[] = $db->quoteInto('status <> ?',0);

            $data = array(
                'update_date' => $currentTime,
                'update_by' => $user_id,
                'status' => 1
            );

            $QNQDD->update($data,$where);

            $db->commit();  
            $flashMessenger->setNamespace('success')->addMessage('Success');
            $this->_redirect(HOST.'tool/new-view-quota-distributor?id='.$id);


        }
        catch(exception $e)
        {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'tool/new-view-quota-distributor?id='.$id);
        }
    }

}

public function viewQuotaDistributorAction(){

    $QDistributor               = new Application_Model_Distributor();
    $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();
    $QGoodCategory              = new Application_Model_GoodCategory();
    $QArea                      = new Application_Model_Area();
    $sn                         = $this->getRequest()->getParam('sn');
    $d_id                       = $this->getRequest()->getParam('quota');

    $distributor                  = $QDistributor->get_cache();
        // $reason                     = $QBlackListReason->get_cache();
        // $this->view->blacklist      = $blacklist;
        // $this->view->reason         = $reason;

    $data = array();
    if ($sn) {

        $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();

        $quotas = $QQuotaOppoByDistributor->getQuotalist($sn);

            // echo "<pre>";
            // print_r($quotas);
        $this->view->quota          = $quotas;
        $this->view->distributor    = $distributor;

    } 

            // print_r($QArea->areaForQuota());
    $good_categories   = $QGoodCategory->get_cache();
    $this->view->good_categories   = $good_categories;
    $this->view->area =  $QArea->areaForQuota();
        // print_r($blacklist);
}

public function newViewQuotaDistributorAction(){

    $id = $this->getRequest()->getParam('id');

    $QNQD = new Application_Model_NewQuotaDistributor();
    $QNQDD = new Application_Model_NewQuotaDistributorDetails();

    $QDistributor  = new Application_Model_Distributor();
    $QGoodCategory = new Application_Model_GoodCategory();
    $QWarehouse = new Application_Model_Warehouse();

    $QGood = new Application_Model_Good();

    $quota = array();

    $get_quota = $QNQD->getNewQuotaDistributor($id);

    $array_quota_color = array();
    $array_quota_current = array();
    $good_product_color = array();

    if($get_quota){

        $good_product_color = $QGood->getColorByGood($get_quota['good_id']);

        $array_quota_color_id = array($id);

        $get_quota_details = $QNQDD->getQuotaDetails($array_quota_color_id);

        foreach ($get_quota_details as $key => $value) {
            $array_quota_color[$value['nqd_id']][$value['good_color']] = $value;
        }

        $get_quota_current = $QNQD->getQuotaCurrent($array_quota_color_id);

        foreach ($get_quota_current as $key => $value) {
            if(isset($value['good_color'])){
                $array_quota_current[$value['id']][$value['good_color']] = $value;
            }
        }

    }

    $this->view->get_quota = $get_quota;

    $this->view->good_product_color = $good_product_color;
    $this->view->quota_color = $array_quota_color;
    $this->view->quota_current = $array_quota_current;

    $good_categories = $QGoodCategory->get_cache();
    $this->view->good_categories = $good_categories;

    $distributor = $QDistributor->get_cache();
    $this->view->distributor = $distributor;

    $warehouse = $QWarehouse->get_cache();
    $this->view->warehouse = $warehouse;


}

public function checkQuantityQuotaDistributorAction()
{   
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $quota_date   = $this->getRequest()->getParam('quota_date');
    $good_id      = $this->getRequest()->getParam('good_id');
    $good_color   = $this->getRequest()->getParam('good_color');
    $d_id         = $this->getRequest()->getParam('d_id');
    $warehouse    = $this->getRequest()->getParam('warehouse');
    $act          = $this->getRequest()->getParam('act');
    $sn           = $this->getRequest()->getParam('sn');
        // print_r($_POST);
        // echo $quota_date.'='.$good_id.'='.$good_color;
    $db = Zend_Registry::get('db');
        // if ($act == 'all_quota') {

        //         $select_q = $db->select()
        //         ->from(array('q' => 'quota_oppo'), array('q.area_id'));
        //         // $select_q->where('q.dis_type = ?', 1);
        //         $select_q->where('q.status = ?',1);
        //         $select_q->where('q.quantity = ?', 0);
        //         $select_q->where('q.sn = ?', $sn);
        //         $inArea = $db->fetchAll($select_q);

        //         $select_m = $db->select()
        //             ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
        //         $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
        //         $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        //         $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        //         $select_m->where('a.id in (?)', $inArea );
        //         $select_m->where('lq.good_id = ?', $good_id);
        //         $select_m->where('lq.good_color = ?', $good_color);
        //         $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);


        //     $isSum = $db->fetchOne($select_m);
        //     if ($isSum) {
        //         $num = $isSum;
        //     }else{
        //         $num = 0;
        //     }
        //     echo $num;
        //     exit();
        // }else{


            // start : old
            //     $select_m = $db->select()
            //         ->from(array('lq' => 'log_quota_tran_distributor'), array('sum(lq.num)'));
            //     $select_m->where('lq.d_id = ?', $d_id);
            //     $select_m->where('lq.good_id = ?', $good_id);
            //     $select_m->where('lq.good_color = ?', $good_color);
            //     $select_m->where('lq.warehouse_id = ?', $warehouse);
            //     $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
    
            //     // echo $select_m;
            // $isSum = $db->fetchOne($select_m);
            // end : old

    $select_m = $db->select()
    ->from(array('m' => 'market'), array('sum(m.num)'));
    $select_m->where('m.d_id = ?', $d_id);
    $select_m->where('m.good_id = ?', $good_id);
    $select_m->where('m.good_color = ?', $good_color);
    $select_m->where('m.warehouse_id = ?', $warehouse);
    $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
    
            // echo $select_m;
    $isSum = $db->fetchOne($select_m);


    if ($isSum) {
        $num = $isSum;
    }else{
        $num = 0;
    }
    echo $num;
    exit();

        // }
}
public function saveItemQuotaAction()
{
    $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();

    $quota_date   = $this->getRequest()->getParam('quota_date');
    $good_id      = $this->getRequest()->getParam('good_id');
    $good_color   = $this->getRequest()->getParam('good_color');
    $d_id         = $this->getRequest()->getParam('d_id');
    $warehouse    = $this->getRequest()->getParam('warehouse');
    $act          = $this->getRequest()->getParam('act');
    $sn           = $this->getRequest()->getParam('sn');
    $q_qty        = $this->getRequest()->getParam('q_qty');
    $currentTime    = date('Y-m-d H:i:s');
    $userStorage    = Zend_Auth::getInstance()->getStorage()->read();

    if ($sn) {
       $where = array(); 
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('quota_date = ?', $quota_date);
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('good_id = ?', $good_id);
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('good_color = ?', $good_color);
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('d_id = ?', $d_id);
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('warehouse = ?', $warehouse);
       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('sn = ?', $sn);

       $data = array(
        'quantity'  => $q_qty,
        'update_at' => $currentTime,
        'update_by' => $userStorage->id,
    );

       $QQuotaOppoByDistributor->update($data,$where);

       exit;
   }

}
public function deleteQuotaDistributorAction()
{
    $QQuotaOppoByDistributor    = new Application_Model_QuotaOppoByDistributor();

    $i_id         = $this->getRequest()->getParam('i_id');

    $currentTime    = date('Y-m-d H:i:s');
    $userStorage    = Zend_Auth::getInstance()->getStorage()->read();

    if ($i_id) {
       $where = array(); 

       $where[] = $QQuotaOppoByDistributor->getAdapter()->quoteInto('id = ?', $i_id);

       $data = array(
        'del'  => '1',
        'update_at' => $currentTime,
        'update_by' => $userStorage->id,
    );

       $QQuotaOppoByDistributor->update($data,$where);

       exit;
   }

}


private function _exportExcelInOutReport($params) {

    require_once 'PHPExcel.php';
    $PHPExcel = new PHPExcel();
    $QWarehouse = new Application_Model_Warehouse();
    $warehouses = $QWarehouse->get_cache();
    foreach ($params['warehouse_id'] as $value) {

        $warehouse .=  $warehouses[$value]." | ";
    }

    $heads_row1 = array(
        'Warehouse',
        $warehouse    ,'','','','',
        'FROM',               
        'TO'  ,             

    );

    $heads_row2 = array(
        '','',
        '','','','',
        $params['date_from'],
        $params['date_to'], 

    );
    $heads_row3 = array(
        'Mobile Phone','','',
        'Accessories','','',
        'Giftbox','','',
    );
    $heads_row4 = array(
        'DATE',
        'IN','OUT',
        'DATE',
        'IN','OUT',
        'IN','OUT',
    );
    $heads_row5 = array(
        '',
        'QTY','QTY',
        '',
        'QTY','QTY',
        'QTY','QTY',

    );
    $PHPExcel->setActiveSheetIndex(0);
    $sheet    = $PHPExcel->getActiveSheet();

    $alpha    = 'A';
    $index    = 1;

    foreach($heads_row1 as $key) {
        $sheet->setCellValue($alpha.$index, $key);  
        $alpha++;
    }

        // reset value
    $alpha  = 'A';
    $index  = 1;
    $index2  = 2;
    $index3  = 3;
    $index4  = 4;
    $index5  = 5;

    foreach($heads_row2 as $key) {
        $sheet->setCellValue($alpha.$index2, $key);  
        $alpha++;
    }

    $alpha  = 'A';
    foreach($heads_row3 as $key) {
        $sheet->setCellValue($alpha.$index3, $key);  
        $alpha++;
    }
    $alpha  = 'A';
    foreach($heads_row4 as $key) {
        $sheet->setCellValue($alpha.$index4, $key);  
        $alpha++;
    }
    $alpha  = 'A';
    foreach($heads_row5 as $key) {
        $sheet->setCellValue($alpha.$index5, $key);  
        $alpha++;
    }
        // Merge Row 1 
    $sheet->mergeCells('A1:A2');
    $sheet->mergeCells('B1:F2');


        // Merge Row 2 
    $sheet->mergeCells('A3:C3');
    $sheet->mergeCells('D3:F3');
    $sheet->mergeCells('G3:H3');

        // Merge Row 3 

    $sheet->mergeCells('A4:A5');
    $sheet->mergeCells('D4:D5');


    $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'CCCCCC')
        )
    );

    $sheet->getStyle("A1:H5")->applyFromArray($style);

    $styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );


    date_default_timezone_set('UTC');
    if (isset($params['date_from']) and $params['date_from']){
      list( $day, $month, $year ) = explode('/', $params['date_from']);

      if (isset($day) and isset($month) and isset($year) )
         $date = $year.'-'.$month.'-'.$day;

 }

 if (isset($params['date_to']) and $params['date_to']){
  list( $day, $month, $year ) = explode('/', $params['date_to']);

  if (isset($day) and isset($month) and isset($year) )
     $end_date = $year.'-'.$month.'-'.$day;
}

$index = 6;
while (strtotime($date) <= strtotime($end_date)) {
   $in_Phone            = ($params['listPhone'][$date])?$params['listPhone'][$date]:'0';
   $out_Phone           = ($params['listOutPhone'][$date])?$params['listOutPhone'][$date]:'0';
   $in_Accessories      = ($params['listAccessories'][$date])?$params['listAccessories'][$date]:'0';
   $out_Accessories     = ($params['listOutAccessories'][$date])?$params['listOutAccessories'][$date]:'0';
   $co_in_Phone         = ($params['listCoInPhone'][$date])?$params['listCoInPhone'][$date]:'0';
   $co_out_Phone        = ($params['listCoOutPhone'][$date])?$params['listCoOutPhone'][$date]:'0';
   $co_in_Accessories   = ($params['listCoInAccessories'][$date])?$params['listCoInAccessories'][$date]:'0';
   $co_out_Accessories  = ($params['listCoOutAccessories'][$date])?$params['listCoOutAccessories'][$date]:'0';

   $listInAccessories_gift      = ($params['listInAccessories_gift'][$date])?$params['listInAccessories_gift'][$date]:'0';
   $listOutAccessories_gift     = ($params['listOutAccessories_gift'][$date])?$params['listOutAccessories_gift'][$date]:'0';
   $listCoInAccessories_gift    = ($params['listCoInAccessories_gift'][$date])?$params['listCoInAccessories_gift'][$date]:'0';
   $listCoOutAccessories_gift   = ($params['listCoOutAccessories_gift'][$date])?$params['listCoOutAccessories_gift'][$date]:'0';

   $t_in_phone = $in_Phone+$co_in_Phone;
   $t_out_phone = $out_Phone+$co_out_Phone;

   $in_gift =$listInAccessories_gift +$listCoInAccessories_gift;
   $out_gift =$listOutAccessories_gift +$listCoOutAccessories_gift;

   $t_in_Accessories = $in_Accessories+$co_in_Accessories;
   $t_out_Accessories = $out_Accessories+$co_out_Accessories;

   $alpha    = 'A';


   $sheet->setCellValue($alpha++.$index, $date);
   $sheet->setCellValue($alpha++.$index, $t_in_phone);
   $sheet->setCellValue($alpha++.$index, $t_out_phone);

   $sheet->setCellValue($alpha++.$index, $date);
   $sheet->setCellValue($alpha++.$index, $t_in_Accessories);
   $sheet->setCellValue($alpha++.$index, $t_out_Accessories);

   $sheet->setCellValue($alpha++.$index, $in_gift);
   $sheet->setCellValue($alpha++.$index, $out_gift);



   $index++;
   $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
   $phone_in += $t_in_phone;
   $phone_out += $t_out_phone;
   $Accessories_in += $t_in_Accessories;
   $Accessories_out += $t_out_Accessories;

   $GiftboxIn      += $in_gift;
   $GiftboxOut     += $out_gift;
}

$sheet->setCellValue('A'.$index, 'Total');
$sheet->setCellValue('B'.$index, $phone_in);
$sheet->setCellValue('C'.$index, $phone_out);

$sheet->setCellValue('D'.$index, 'Total');
$sheet->setCellValue('E'.$index, $Accessories_in);
$sheet->setCellValue('F'.$index, $Accessories_out);

$sheet->setCellValue('G'.$index, $GiftboxIn);
$sheet->setCellValue('H'.$index, $GiftboxOut);
$sheet->getStyle("A1:H".$index)->applyFromArray($styleArray);

$sheet->getStyle('A'.$index.':H'.$index)->applyFromArray(
    array(
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'CCCCCC')
        )
    )
);
$filename = 'Material In-Out Report-'.date('d/m/Y');

$objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

$objWriter->save('php://output');
exit;
}

public function cloneQuotaAction(){

    try {

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if ($this->getRequest()->getMethod() == 'POST'){

            $clone_date = $this->getRequest()->getParam('clone_date');
            $q_id = $this->getRequest()->getParam('q_id');
            $arrayQuotaID = explode(',', $q_id);

            $QQuota = new Application_Model_Quota();

            $getQuotaList = $QQuota->getQuotaForClone($arrayQuotaID);

            if(!$getQuotaList){
                $flashMessenger = $this->_helper->flashMessenger;

                $flashMessenger->setNamespace('error')->addMessage('Cannot clone quota, not find data active');

                $this->_redirect(HOST . "tool/quota-manage");
            }


            $create_date = date('Y-m-d H:i:s');

            foreach ($getQuotaList as $key) {

                $sn = date('YmdHis') . substr(microtime(), 2, 4);

                $date = DateTime::createFromFormat('d/m/Y', $clone_date);
                $quota_date = $date->format('Y-m-d');

                $data['area'] = $key['area_id'];
                $data['good_id'] = $key['good_id'];
                $data['good_color'] = $key['good_color'];
                $data['good_type'] = $key['good_type'];
                $data['dis_type'] = $key['dis_type'];
                $data['quota_date'] = $key['quota_date'];
                $data['warehouse_id'] = $key['warehouse_id'];
                $data['sn'] = $key['sn'];

                $used_num = $this->getQuotaNum($data);
                $total_num = $key['quantity']-$used_num;

                $arr_data_area = array();

                $quantity_all = null;
                if(isset($key['channel']) && $key['channel'] == '99'){

                    $quantity_all = $total_num;

                    $select_q = $db->select()
                    ->from(array('q' => 'quota_oppo'), array('q.*'));
                    $select_q->where('q.sn = ?', $key['sn']);
                    $inArea = $db->fetchAll($select_q);

                    foreach ($inArea as $key_area => $value_area) {

                        if($value_area['channel'] == '99'){
                            continue;
                        }

                        if($value_area['quantity'] == 0){

                            $data = array(
                                'area_id'        => $value_area['area_id'],
                                'sn'             => $sn,
                                'dis_type'       => $value_area['dis_type'],
                                'channel'        => null,
                                'cat_id'         => $value_area['cat_id'],
                                'good_id'        => $value_area['good_id'],
                                'good_color'     => $value_area['good_color'],
                                'good_type'      => $value_area['good_type'],
                                'warehouse_id'   => $value_area['warehouse_id'],
                                'quantity'       => 0,
                                'quantity_all'   => null,
                                'quota_date'     => $quota_date,
                                'created_at'     => $create_date,
                                'created_by'     => $userStorage->id,
                                'status'         => 1,
                                'not_allow_area' => $value_area['not_allow_area']
                            );

                            array_push($arr_data_area, $data);

                        }else{

                            $data['area'] = $value_area['area_id'];
                            $data['good_id'] = $value_area['good_id'];
                            $data['good_color'] = $value_area['good_color'];
                            $data['good_type'] = $value_area['good_type'];
                            $data['dis_type'] = $value_area['dis_type'];
                            $data['quota_date'] = $value_area['quota_date'];
                            $data['warehouse_id'] = $value_area['warehouse_id'];
                            $data['sn'] = $value_area['sn'];

                            $used_num_sub = $this->getQuotaNum($data);

                            $total_num_sub = $value_area['quantity']-$used_num_sub;

                            $data = array(
                                'area_id'        => $value_area['area_id'],
                                'sn'             => $sn,
                                'dis_type'       => $value_area['dis_type'],
                                'channel'        => null,
                                'cat_id'         => $value_area['cat_id'],
                                'good_id'        => $value_area['good_id'],
                                'good_color'     => $value_area['good_color'],
                                'good_type'      => $value_area['good_type'],
                                'warehouse_id'   => $value_area['warehouse_id'],
                                'quantity'       => $total_num_sub,
                                'quantity_all'   => null,
                                'quota_date'     => $quota_date,
                                'created_at'     => $create_date,
                                'created_by'     => $userStorage->id,
                                'status'         => 1,
                                'not_allow_area' => $value_area['not_allow_area']
                            );
                            $quantity_all += $total_num_sub;
                            array_push($arr_data_area, $data);

                        }
                    }

                }

                $data = array(
                    'area_id'        => $key['area_id'],
                    'sn'             => $sn,
                    'dis_type'       => $key['dis_type'],
                    'channel'        => $key['channel'],
                    'cat_id'         => $key['cat_id'],
                    'good_id'        => $key['good_id'],
                    'good_color'     => $key['good_color'],
                    'good_type'      => $key['good_type'],
                    'warehouse_id'   => $key['warehouse_id'],
                    'quantity'       => $total_num,
                    'quantity_all'   => $quantity_all,
                    'quota_date'     => $quota_date,
                    'created_at'     => $create_date,
                    'created_by'     => $userStorage->id,
                    'status'         => 1,
                    'not_allow_area' => $key['not_allow_area']
                );

                $QQuota->insert($data);

                foreach ($arr_data_area as $key) {
                    $QQuota->insert($key);
                }

            }

            $db->commit();

            $flashMessenger = $this->_helper->flashMessenger;
            $messages = $flashMessenger->setNamespace('success')->addMessage('Clone Done');

            $this->_redirect(HOST . "tool/quota-manage");
        }

    }catch (Exception $e){

        $db->rollback();

        $flashMessenger = $this->_helper->flashMessenger;

        $flashMessenger->setNamespace('error')->addMessage('Cannot clone quota, please try again! /' . $e->getMessage());

        $this->_redirect(HOST . "tool/quota-manage");
    }
}

public function getQuotaNum($data){

    $db = Zend_Registry::get('db');

    $area_id = $data['area'];
    $good_id = $data['good_id'];
    $good_color = $data['good_color'];
    $good_type = $data['good_type'];
    $dis_type = $data['dis_type'];
    $quota_date = $data['quota_date'];
    $sn = $data['sn'];
    $warehouse_id = $data['warehouse_id'];

    $act = '';
    $num = 0;

    switch ($dis_type) {
        case '3':
        $act = 'ka';
        break;
        case '2':
        $act = 'kr';
        break;
        case '1':
        $act = 'all_quota';
        break;
        case '10':
        $act = 'brandshop';
        break;
        case '4':
        $act = "operator";
        break;
    }

    if ($act == 'all_quota') {

        $select_q = $db->select()
        ->from(array('q' => 'quota_oppo'), array('q.area_id'));
            // $select_q->where('q.dis_type = ?', 1);
            // $select_q->where('q.status = ?',1);
        $select_q->where('q.quantity = ?', 0);
        $select_q->where('q.sn = ?', $sn);
        $inArea = $db->fetchAll($select_q);

        if($area_id){
            $inArea = $area_id;
        }


            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
            // $select_m->where('a.id in (?)', $inArea );
            // $select_m->where('lq.good_id = ?', $good_id);
            // $select_m->where('lq.good_color = ?', $good_color);

            // $select_m->where('lq.warehouse_id = ?', $warehouse_id);

            // $select_m->where('lq.good_type = ?', $good_type);
            // $select_m->where('dg.group_type_id = ?', 1);

            // // $select_m->where('d.group_id = ?', 1);
            // // $select_m->where('d.quota_channel is null',1);
            // // $select_m->where('(d.rank = 7');
            // // $select_m->Orwhere('d.rank = 8)');
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
            // // echo $select_m;die;
            // $isSum = $db->fetchOne($select_m);
            // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));
        $select_m->where('a.id in (?)', $inArea );
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('dg.group_type_id = ?', 1);

            // $select_m->where('d.group_id = ?', 1);
            // $select_m->where('d.quota_channel is null',1);
            // $select_m->where('(d.rank = 7');
            // $select_m->Orwhere('d.rank = 8)');
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
            // echo $select_m;die;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }
    }else if($act == 'ka'){

           // start : old
           //  $select_m = $db->select()
           //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
           //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
           //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

           //  $select_m->where('dg.group_type_id IN (?)', [3]);
           //  // $select_m->orWhere('d.group_id = ?', 3);
           //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
           //  // $select_m->orWhere('d.quota_channel = 1)');
           //  $select_m->where('lq.good_id = ?', $good_id);
           //  $select_m->where('lq.good_color = ?', $good_color);

           //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

           //  $select_m->where('lq.good_type = ?', $good_type);
           //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
           // // echo $select_m;
           //  $isSum = $db->fetchOne($select_m);
           // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [3]);
            // $select_m->orWhere('d.group_id = ?', 3);
            // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
            // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
           // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }

    }else if($act == 'operator'){

           // start : old
           //  $select_m = $db->select()
           //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
           //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
           //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

           //  $select_m->where('dg.group_type_id IN (?)', [4]);
           //  // $select_m->orWhere('d.group_id = ?', 3);
           //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
           //  // $select_m->orWhere('d.quota_channel = 1)');
           //  $select_m->where('lq.good_id = ?', $good_id);
           //  $select_m->where('lq.good_color = ?', $good_color);

           //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

           //  $select_m->where('lq.good_type = ?', $good_type);
           //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
           // // echo $select_m;
           //  $isSum = $db->fetchOne($select_m);
           // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [4]);
            // $select_m->orWhere('d.group_id = ?', 3);
            // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
            // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
           // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }

    }else if($act == 'kr'){

           // start : old
           //  $select_m = $db->select()
           //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
           //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
           //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

           //  $select_m->where('dg.group_type_id = ?', 2);
           //  // $select_m->orWhere('d.group_id = ?', 2);
           //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
           //  // $select_m->orWhere('d.quota_channel = 1)');
           //  $select_m->where('lq.good_id = ?', $good_id);
           //  $select_m->where('lq.good_color = ?', $good_color);

           //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

           //  $select_m->where('lq.good_type = ?', $good_type);
           //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
           // // echo $select_m;
           //  $isSum = $db->fetchOne($select_m);
           // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id = ?', 2);
            // $select_m->orWhere('d.group_id = ?', 2);
            // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
            // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
           // echo $select_m;
        $isSum = $db->fetchOne($select_m);


        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }

    }else if($act == 'brandshop'){


           // start : old
           //  $select_m = $db->select()
           //      ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
           //  $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
           //  $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

           //  $select_m->where('dg.group_type_id IN (?)', [10,11]);
           //  // $select_m->orWhere('d.group_id = ?', 2);
           //  // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
           //  // $select_m->orWhere('d.quota_channel = 1)');
           //  $select_m->where('lq.good_id = ?', $good_id);
           //  $select_m->where('lq.good_color = ?', $good_color);

           //  $select_m->where('lq.warehouse_id = ?', $warehouse_id);

           //  $select_m->where('lq.good_type = ?', $good_type);
           //  $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);
           // // echo $select_m;
           //  $isSum = $db->fetchOne($select_m);
           // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('dg.group_type_id IN (?)', [10,11]);
            // $select_m->orWhere('d.group_id = ?', 2);
            // $select_m->where('(d.rank in (?)', array(1,2,5,6) );
            // $select_m->orWhere('d.quota_channel = 1)');
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);
           // echo $select_m;
        $isSum = $db->fetchOne($select_m);

        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }

    }else{

            // start : old
            // $select_m = $db->select()
            //     ->from(array('lq' => 'log_quota_tran'), array('sum(lq.num)'));
            // $select_m->join(array('d' => 'distributor'),'lq.d_id = d.id',array());
            // $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
            // $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
            // $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

            // $select_m->where('a.id = ?', $area );
            // $select_m->where('lq.good_id = ?', $good_id);
            // $select_m->where('lq.good_color = ?', $good_color);

            // $select_m->where('lq.warehouse_id = ?', $warehouse_id);

            // $select_m->where('lq.good_type = ?', $good_type);
            // // $select_m->where('d.quota_channel is null',1);
            // $select_m->where('date_format(lq.add_time,"%Y-%m-%d") = ?',$quota_date);

            // $isSum = $db->fetchOne($select_m);
            // end : old


        $select_m = $db->select()
        ->from(array('m' => 'market'), array('sum(m.num)'));
        $select_m->join(array('d' => 'distributor'),'m.d_id = d.id',array());
        $select_m->join(array('rm' => HR_DB.'.regional_market'),'d.region = rm.id',array());
        $select_m->joinLeft(array('a' => HR_DB.'.area'),'rm.area_id = a.id',array());
        $select_m->joinLeft(array('dg' => 'distributor_group'),'dg.group_id=d.group_id',array('dg.group_type_id'));

        $select_m->where('a.id = ?', $area );
        $select_m->where('m.good_id = ?', $good_id);
        $select_m->where('m.good_color = ?', $good_color);

        $select_m->where('m.warehouse_id = ?', $warehouse_id);

        $select_m->where('m.type = ?', $good_type);
            // $select_m->where('d.quota_channel is null',1);
        $select_m->where('date_format(m.add_time,"%Y-%m-%d") = ?',$quota_date);

        $isSum = $db->fetchOne($select_m);

        if ($isSum) {
            $num = $isSum;
        }else{
            $num = 0;
        }

    }

    return $num;

}

public function blacklistImageAction(){

    $flashMessenger = $this->_helper->flashMessenger;
    $db = Zend_Registry::get('db');

    $d_id = $this->getRequest()->getParam('d_id');
    $this->view->d_id = $d_id;

    $page       = $this->getRequest()->getParam('page',1);
    $limit      = 50;
    $sort       = $this->getRequest()->getParam('sort','type');
    $desc       = $this->getRequest()->getParam('desc', 1);
    $total      = 0;

    $params     = array(
        'd_id'  =>  $d_id,
        'sort'  =>  $sort,
        'desc'  =>  $desc,
    );

    $QUploadBlacklistImage = new Application_Model_UploadBlacklistImage();
    $QDistributor          = new Application_Model_Distributor();
    $list                  = $QUploadBlacklistImage->fetchPagination($page, $limit, $total, $params);
    $where                 = $QDistributor->getAdapter()->quoteInto('id = ?', $d_id);
    $distributor           = $QDistributor->fetchRow($where);
        // print_r($distributor);die;

        // print_r($list);
    $this->view->distributor_data   = $distributor;
    $this->view->list               = $list;
    $this->view->distributor        = $distributor['title'];
    $this->view->limit              = $limit;
    $this->view->total              = $total;
    $this->view->page               = $page;
    $this->view->offset             = $limit*($page-1);
    $this->view->params             = $params;
    $this->view->sort               = $sort;
    $this->view->desc               = $desc;
    $this->view->url                = HOST.'sales/blacklist-image/'.( $params ? '?'.http_build_query($params).'&' : '?' );

}

public function blacklistUploadImageAction()
{

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    if ($this->getRequest()->getMethod() == 'POST'){
      $QUploadBlacklistImage = new Application_Model_UploadBlacklistImage();

      $select        = $this->getRequest()->getParam('select');
      $file          = $this->getRequest()->getParam('photo');
      $d_id          = $this->getRequest()->getParam('d_id');

      for ($i=0; $i <count($select) ; $i++) { 
          $data = array(
            'd_id'          => $d_id,
            'type'          => $select[$i],
            'file_name'     => $file[$select[$i]][$f]
        );

          foreach ($file[$select[$i]] as $k => $p) {
              $data_ex = array(
                'description'=> @$description[$k]
            );
              if($p){
                if (strstr($p, '_located_'))
                    if (strstr($k, 'old_')) {
                      $old_photo_id = str_replace('old_', '', $k);
                      $photo_ids[] = $QUploadBlacklistImage->uploadFile($p, $userStorage, $old_photo_id, $data);
                  } else {
                    $photo_ids[] = $QUploadBlacklistImage->uploadFile($p, $userStorage, null, $data);
                } elseif ($p) {
                    if (strstr($k, 'old_')) {
                        $old_photo_id = str_replace('old_', '', $k);
                        $photo_ids[] = $old_photo_id;
                    }
                }
            }
        }

    }

    $this->_redirect(HOST.'tool/blacklist-image?d_id='.$d_id);

}
}

public function blacklistDeleteImageAction()
{
    $id = $this->getRequest()->getParam('id');
    $d_id = $this->getRequest()->getParam('d_id');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $QUploadBlacklistImage = new Application_Model_UploadBlacklistImage();
    $where = array();
    $where[] = $QUploadBlacklistImage->getAdapter()->quoteInto('id = ?', $id);
    $where[] = $QUploadBlacklistImage->getAdapter()->quoteInto('d_id = ?', $d_id);
    $update = array(
        'status' => 0,
        'updated_date'  => date('Y-m-d H:i:s'),
        'updated_by'    => $userStorage->id
    );
    $QUploadBlacklistImage->update($update,$where);

    $this->_redirect(HOST.'tool/blacklist-image?d_id='.$d_id);


}

    // Service Monthly

public function serviceReportListAction()
{
        //print_r($_GET);//die;
    $this->view->meta_refresh = 300;

    $sort               = $this->getRequest()->getParam('sort');
    $desc               = $this->getRequest()->getParam('desc', 0);
    $page               = $this->getRequest()->getParam('page', 1);

    $data_type              = $this->getRequest()->getParam('data_type');
    $imei2              = $this->getRequest()->getParam('imei2');
    $good_code          = $this->getRequest()->getParam('good_code');
    $start_date         = $this->getRequest()->getParam('start_date');
    $end_date           = $this->getRequest()->getParam('end_date');
    $view_status        = $this->getRequest()->getParam('view_status', 1);
    $export             = $this->getRequest()->getParam('export', 0);

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter(array(
        'data_type'              => $data_type,
        'good_code'              => $good_code,
        'start_date'             => $start_date,
        'end_date'               => $end_date,
        'view_status'            => $view_status,
        'export'                 => $export,
        'action_frm'             => 'list'
    ));

    $QServiceProductList = new Application_Model_ServiceProductList();
    $QServiceStockShopList = new Application_Model_ServiceStockShopList();
    $QServiceProductModelList = new Application_Model_ServiceProductModelList();

    $params['sort'] = $sort;
    $params['desc'] = $desc;
    $get_resule="";

    if (isset($export) && $export) {
        $get_resule = $QServiceProductList->ServiceProductListfetchPagination($page, null, $total, $params);
            //print_r($get_resule);die;
           // $this->_exportExcelWithholdingTax($get_resule);
    }

        //print_r($params);
        if($data_type=="1"){    //รายการสินค้า
           $get_resule = $QServiceProductList->ServiceProductListfetchPagination($page, $limit, $total, $params); 
        }else if($data_type=="2"){  //รายการสินค้าคงเหลือ
           $get_resule = $QServiceStockShopList->ServiceStockShopListfetchPagination($page, $limit, $total, $params); 
        }else if($data_type=="3"){  //รายการสินค้าโมเดล
           $get_resule = $QServiceProductModelList->ServiceProductModelListfetchPagination($page, $limit, $total, $params); 
        }else if($data_type=="4"){  //รายการยอดอะไหล่เข้า
           $get_resule = $QServiceProductModelList->ServiceProductModelListfetchPagination($page, $limit, $total, $params); 
        }else if($data_type=="5"){  //รายการยอดใช้อะไหล่
           $get_resule = $QServiceProductModelList->ServiceProductModelListfetchPagination($page, $limit, $total, $params); 
        }else if($data_type=="6"){  //รายการยอดใช้ ACC
           $get_resule = $QServiceProductModelList->ServiceProductModelListfetchPagination($page, $limit, $total, $params);          
        }else if($data_type=="R01"){ //Report 01 : Inventory / Purchase / Consumption
           $get_resule = null; 
        }else if($data_type=="R02"){ //Report 02 : Inventory by Branch
           $get_resule = null;  
        }else if($data_type=="R03"){ //Report 03 : Turn Over Stock
           $get_resule = null; 
        }else if($data_type=="R04"){ //Report 04 : Stock Aging
           $get_resule = null;  
        }else if($data_type=="R05"){ //Report 05 : Used spare part by ITEM
           $get_resule = null; 
       }

       $get_warehouse = $QServiceStockShopList->getWarehouse();
        //print_r($get_resule);//die;

       $this->view->get_resule     = $get_resule;
       $this->view->distributors      = $distributors;

       $this->view->data_type   = $data_type;
       $this->view->warehouse   = $get_warehouse;
       $this->view->desc   = $desc;
       $this->view->sort   = $sort;
       $this->view->params = $params;
       $this->view->limit  = $limit;
       $this->view->total  = $total;
       $this->view->no_show_brandshop = $no_show_brandshop;
       $this->view->url    = HOST . 'tool/service-report-list/' . ($params ? '?' . http_build_query($params) .
        '&' : '?');
       $this->view->params = $params;
       $this->view->offset = $limit * ($page - 1);

       $flashMessenger = $this->_helper->flashMessenger;
       $messages = $flashMessenger->setNamespace('error')->getMessages();
       $this->view->messages = $messages;

       $messages_success = $flashMessenger->setNamespace('success')->getMessages();
       $this->view->messages_success = $messages_success;

       if ($this->getRequest()->isXmlHttpRequest()) {
        $this->_helper->layout->disableLayout();

        $this->_helper->viewRenderer->setRender('partials/service-report-list');
    }
}

    //Report 01 : Inventory / Purchase / Consumption
public function printInventoryPurchaseConsumptionAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'print-inventory-purchase-consumption.php';
}

    //Report 02 : Inventory by Branch
public function printInventoryByBranchAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'print-inventory-by-branch.php';
}

    //Report 03 : Turn Over Stock
public function printTurnOverStockAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'print-turn-over-stock.php';
}

    //Report 04 : Stock Aging
public function printStockAgingAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'print-stock-aging.php';
}

    //Report 05 : Used spare part by ITEM
public function printUsedSparePartByItemAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'print-used-spare-part-by-item.php';
}

public function serviceProductUploadListAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function serviceStockShopUploadListAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function serviceProductModelUploadListAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function serviceProductStockInUploadAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function serviceProductStockOutUploadAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function serviceProductStockOutAccUploadAction()
{
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
}

public function saveServiceProductUploadListAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-product-upload-list.php';
}

public function saveServiceStockShopUploadListAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-stock-shop-upload-list.php';
}

public function saveServiceProductModelUploadListAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-product-model-upload-list.php';
}

public function saveServiceProductStockInUploadAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-product-stock-in-upload.php';
}

public function saveServiceProductStockOutUploadAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-product-stock-out-upload.php';
}

public function saveServiceProductStockOutAccUploadAction()
{
    require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-product-stock-out-acc-upload.php';
}

private function _exportExcelUsedSparePartByItem($data)
{

        //print_r($data);die;
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $db = Zend_Registry::get('db');
    set_time_limit(0);
    error_reporting(~E_ALL);
    ini_set('display_error', 0);
    ini_set('memory_limit', '200M');
    $filename = 'export_used_spare_part_by_item_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
    while (@ob_end_clean());
    ob_start();
    header('Content-Encoding: UTF-8');
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
    if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $heads = array(
            'Item No',
            'Branch',
            'Current Stock',
            'M-2',
            'M-1',
            'Avg',
            'On Order'
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['good_code'];
            $row[] = $item['imei2'];
            $row[] = $item['current_stock'];
            $row[] = $item['stock_m2'];
            $row[] = $item['stock_m1'];
            $row[] = $item['stock_avg'];
            $row[] = $item['on_hand'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    public function importImei2UploadAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function saveImportImei2UploadListAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-import-imei2-upload-list.php';
    }

    public function importImei2UploadListAction()
    {

        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $imei1              = $this->getRequest()->getParam('imei1');
        $imei2              = $this->getRequest()->getParam('imei2');
        $good_code          = $this->getRequest()->getParam('good_code');
        $start_date         = $this->getRequest()->getParam('start_date');
        $end_date           = $this->getRequest()->getParam('end_date');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'imei1'                  => $imei1,
            'imei2'                  => $imei2,
            'good_code'              => $good_code,
            'start_date'             => $start_date,
            'end_date'               => $end_date,
            'view_status'            => $view_status,
            'export'                 => $export,
            'action_frm'             => 'list'
        ));

        $QImportImei2 = new Application_Model_ImportImei2();

        $params['sort'] = $sort;
        $params['desc'] = $desc;
        $get_resule="";

        if (isset($export) && $export) {
            $get_resule = $QImportImei2->importImei2ListfetchPagination($page, $limit, $total, $params);
            //print_r($get_resule);die;
            $this->_exportImei2($get_resule);
        }else{
            $get_resule = $QImportImei2->importImei2ListfetchPagination($page, $limit, $total, $params);
        }

        //print_r($get_resule);//die;

        $this->view->get_resule     = $get_resule;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'tool/import-imei2-upload-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/import-imei2-list');
        }
    }

    private function _exportImei2($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_imei2_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $heads = array(
            'No',
            'Imei 1',
            'Imei 2',
            'Good Code',
            'Color',
            'Import By',
            'Import Date'
        );

        fputcsv($output, $heads);

        $i = 1;
        foreach($data as $item)
        {

            $row = array();
            $row[] = $i;
            $row[] = $item['imei1'];
            $row[] = $item['imei2'];
            $row[] = $item['good_code'];
            $row[] = $item['color'];
            $row[] = $item['staff_name'];
            $row[] = $item['created_date'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
            $i+=1;
        }

        unset($data);

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


    // Service Weekly

    //Report 01 : Weekly Inventory / Purchase / Consumption
    public function printWeeklyInventoryPurchaseConsumptionAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-weekly-inventory-purchase-consumption.php';
    }

    //Report 02 : Weekly Inventory by Branch
    public function printWeeklyInventoryByBranchAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-weekly-inventory-by-branch.php';
    }

    //Report 03 : Weekly Turn Over Stock
    public function printWeeklyTurnOverStockAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-weekly-turn-over-stock.php';
    }

    //Report 04 : Weekly Stock Aging
    public function printWeeklyStockAgingAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-weekly-stock-aging.php';
    }

    //Report 05 : Weekly Used spare part by ITEM
    public function printWeeklyUsedSparePartByItemAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'print-weekly-used-spare-part-by-item.php';
    }

    public function serviceWeeklyProductUploadListAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function serviceWeeklyStockShopUploadListAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function serviceWeeklyProductModelUploadListAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function serviceWeeklyProductStockInUploadAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function serviceWeeklyProductStockOutUploadAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function serviceWeeklyProductStockOutAccUploadAction()
    {
        //require_once 'sales' . DIRECTORY_SEPARATOR . 'wht-manual-upload-list.php';
    }

    public function saveServiceWeeklyProductUploadListAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-product-upload-list.php';
    }

    public function saveServiceWeeklyStockShopUploadListAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-stock-shop-upload-list.php';
    }

    public function saveServiceWeeklyProductModelUploadListAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-product-model-upload-list.php';
    }

    public function saveServiceWeeklyProductStockInUploadAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-product-stock-in-upload.php';
    }

    public function saveServiceWeeklyProductStockOutUploadAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-product-stock-out-upload.php';
    }

    public function saveServiceWeeklyProductStockOutAccUploadAction()
    {
        require_once 'tools' . DIRECTORY_SEPARATOR . 'save-service-weekly-product-stock-out-acc-upload.php';
    }

    public function serviceWeeklyReportListAction()
    {
        //print_r($_GET);//die;
        $this->view->meta_refresh = 300;

        $sort               = $this->getRequest()->getParam('sort');
        $desc               = $this->getRequest()->getParam('desc', 0);
        $page               = $this->getRequest()->getParam('page', 1);

        $data_type          = $this->getRequest()->getParam('data_type');

        $good_code          = $this->getRequest()->getParam('good_code');
        $start_date         = $this->getRequest()->getParam('start_date');
        $end_date           = $this->getRequest()->getParam('end_date');
        $view_status        = $this->getRequest()->getParam('view_status', 1);
        $export             = $this->getRequest()->getParam('export', 0);

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'data_type'              => $data_type,
            'good_code'              => $good_code,
            'start_date'             => $start_date,
            'end_date'               => $end_date,
            'view_status'            => $view_status,
            'export'                 => $export,
            'action_frm'             => 'list'
        ));

        $QProductList = new Application_Model_ServiceWeeklyProductList();
        $QServiceStockShopList = new Application_Model_ServiceWeeklyStockShopList();
        $QServiceProductModelList = new Application_Model_ServiceWeeklyProductModelList();
        $QServiceWeeklyGoodStockIn = new Application_Model_ServiceWeeklyGoodStockIn();
        $QServiceWeeklyGoodStockOut = new Application_Model_ServiceWeeklyGoodStockOut();
        $QServiceWeeklyGoodStockOutAcc = new Application_Model_ServiceWeeklyGoodStockOutAcc();


        $params['sort'] = $sort;
        $params['desc'] = $desc;
        $get_resule="";

        /*if (isset($export) && $export) {
            $get_resule = $QProductList->ServiceWeeklyProductListfetchPagination($page, null, $total, $params);
            //print_r($get_resule);die;
           // $this->_exportExcelWithholdingTax($get_resule);
        }*/

        //print_r($params);
        if($data_type=="1"){    //รายการสินค้า
           $get_resule = $QProductList->ServiceWeeklyProductListfetchPagination($page, $limit, $total, $params); 
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelWeeklyProductList($get_resule);
        }
        return;
        }else if($data_type=="2"){  //รายการสินค้าคงเหลือ
           $get_resule = $QServiceStockShopList->ServiceWeeklyStockShopListfetchPagination($page, $limit, $total, $params); 
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelServiceWeeklyStockShopList($get_resule);
        }
        return;
        }else if($data_type=="3"){  //รายการสินค้าโมเดล
           $get_resule = $QServiceProductModelList->ServiceWeeklyProductModelListfetchPagination($page, $limit, $total, $params);
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelWeeklyProductList($get_resule);
        }
        return; 
        }else if($data_type=="4"){  //รายการยอดอะไหล่เข้า
           $get_resule = $QServiceWeeklyGoodStockIn->ServiceWeeklyProductStockInListfetchPagination($page, $limit, $total, $params); 
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelServiceWeeklyProductStockInList($get_resule);
        }
        return;
        }else if($data_type=="5"){  //รายการยอดใช้อะไหล่
           $get_resule = $QServiceWeeklyGoodStockOut->ServiceWeeklyProductStockOutListfetchPagination($page, $limit, $total, $params); 
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelServiceWeeklyProductStockOutList($get_resule);
        }
        return;
        }else if($data_type=="6"){  //รายการยอดใช้ ACC
           $get_resule = $QServiceWeeklyGoodStockOutAcc->ServiceWeeklyProductStockOutAccListfetchPagination($page, $limit, $total, $params);  
           if (isset($export) && $export)
           {
                //print_r($get_resule);die;
            $this->_exportExcelServiceWeeklyProductStockOutAccList($get_resule);
        }
        return;        
        }else if($data_type=="R01"){ //Report 01 : Inventory / Purchase / Consumption
           $get_resule = null; 
        }else if($data_type=="R02"){ //Report 02 : Inventory by Branch
           $get_resule = null;  
        }else if($data_type=="R03"){ //Report 03 : Turn Over Stock
           $get_resule = null; 
        }else if($data_type=="R04"){ //Report 04 : Stock Aging
           $get_resule = null;  
        }else if($data_type=="R05"){ //Report 05 : Used spare part by ITEM
           $get_resule = null; 
       }

       $get_warehouse = $QServiceStockShopList->getWeeklyWarehouse();
        print_r($get_resule);//die;

        $this->view->data_type     = $data_type;
        $this->view->get_resule     = $get_resule;
        $this->view->distributors      = $distributors;

        $this->view->warehouse   = $get_warehouse;
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->no_show_brandshop = $no_show_brandshop;
        $this->view->url    = HOST . 'tool/service-weekly-report-list/' . ($params ? '?' . http_build_query($params) .
            '&' : '?');
        $this->view->params = $params;
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();

            $this->_helper->viewRenderer->setRender('partials/service-weekly-report-list');
        }
    }

    
    private function _exportExcelWeeklyUsedSparePartByItem($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'export_weekly_used_spare_part_by_item_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $heads = array(
            'Item No',
            'Branch',
            'Current Stock',
            'W-2',
            'W-1',
            'Avg',
            'On Order'
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['good_code'];
            $row[] = $item['imei2'];
            $row[] = $item['current_stock'];
            $row[] = $item['stock_w2'];
            $row[] = $item['stock_w1'];
            $row[] = $item['stock_avg'];
            $row[] = $item['on_hand'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelOldData($data){

      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      $db = Zend_Registry::get('db');
      set_time_limit(0);
      error_reporting(~E_ALL);
      ini_set('display_error', 0);
      ini_set('memory_limit', '200M');
      $filename = 'Export_Old_data_List_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
      while (@ob_end_clean());
      ob_start();
      header('Content-Encoding: UTF-8');
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename='.$filename);
      $userStorage = Zend_Auth::getInstance()->getStorage()->read();
      $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
      if (!file_exists($file_path))
        mkdir($file_path, 0777, true);
    $path = $file_path.'/'.$filename;
    $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $heads = array(
            'No.',
            'Imei',
            'Distributor',
            'Warehouse',
            'Set OldData By',
            'Set OldData At'
        );

        fputcsv($output, $heads);

        $i = 1;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $i;
            $row[] = $item['imei_sn'];
            $row[] = $item['distributor_name'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['staff_name'];
            $row[] = $item['set_olddata_at'];
            
            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelWeeklyProductList($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = 'exportExcelWeeklyProductList_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'data_date',
            'good_code',
            'good_model',
            'good_name_chinese',
            'good_name_eng',
            'import_price_usd',
            'import_price_bath',
            'retail_price_bath',
            'import_date',
            'import_by',
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['data_date'];
            $row[] = $item['good_code'];
            $row[] = $item['good_model'];
            $row[] = $item['good_name_chinese'];
            $row[] = $item['good_name_eng'];
            $row[] = $item['import_price_usd'];
            $row[] = $item['import_price_bath'];
            $row[] = $item['retail_price_bath'];
            $row[] = $item['import_date'];
            $row[] = $item['import_by'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelServiceWeeklyStockShopList($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = '_exportExcelServiceWeeklyStockShopList_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'data_date',
            'warehouse_name',
            'good_code',
            'good_model',
            'num',
            'price_cost_usd',
            'price_cost_bath',
            'week_of_year',
            'import_date',
            'import_by',
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['data_date'];
            $row[] = $item['warehouse_name'];
            $row[] = $item['good_code'];
            $row[] = $item['good_model'];
            $row[] = $item['num'];
            $row[] = $item['price_cost_usd'];
            $row[] = $item['price_cost_bath'];
            $row[] = $item['week_of_year'];
            $row[] = $item['created_date'];
            $row[] = $item['created_by'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelServiceWeeklyProductStockInList($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = '_exportExcelServiceWeeklyProductStockInList_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'data_date',
            'good_code',
            'num',
            'price_cost_usd',
            'price_cost_bath',
            'price_cost_total_bath',
            'status',
            'week_of_year',
            'import_date',
            'import_by',
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['data_date'];
            $row[] = $item['good_code'];
            $row[] = $item['num'];
            $row[] = $item['price_cost_usd'];
            $row[] = $item['price_cost_bath'];
            $row[] = $item['price_cost_total_bath'];
            $row[] = $item['status'];
            $row[] = $item['week_of_year'];
            $row[] = $item['created_date'];
            $row[] = $item['created_by'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelServiceWeeklyProductStockOutList($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = '_exportExcelServiceWeeklyProductStockOutList_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'data_date',
            'good_code',
            'price_cost_usd',
            'price_cost_bath',
            'inside_num',
            'outside_num',
            'in_out_side_num',
            'inside_total_price',
            'outside_total_price',
            'in_out_side_total_price',
            'status',
            'week_of_year',
            'import_date',
            'import_by',
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['data_date'];
            $row[] = $item['good_code'];
            $row[] = $item['price_cost_usd'];
            $row[] = $item['price_cost_bath'];
            $row[] = $item['inside_num'];
            $row[] = $item['outside_num'];
            $row[] = $item['in_out_side_num'];
            $row[] = $item['inside_total_price'];
            $row[] = $item['outside_total_price'];
            $row[] = $item['in_out_side_total_price'];
            $row[] = $item['status'];
            $row[] = $item['week_of_year'];
            $row[] = $item['created_date'];
            $row[] = $item['created_by'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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

    private function _exportExcelServiceWeeklyProductStockOutAccList($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = '_exportExcelServiceWeeklyProductStockOutAccList_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'data_date',
            'good_code',
            'price_cost_usd',
            'price_cost_bath',
            'num',
            'total_price',
            'status',
            'week_of_year',
            'import_date',
            'import_by',
        );

        fputcsv($output, $heads);

        $i = 2;
        foreach($data as $item)

        {

            $row = array();
            $row[] = $item['data_date'];
            $row[] = $item['good_code'];
            $row[] = $item['price_cost_usd'];
            $row[] = $item['price_cost_bath'];
            $row[] = $item['num'];
            $row[] = $item['total_price'];
            $row[] = $item['status'];
            $row[] = $item['week_of_year'];
            $row[] = $item['created_date'];
            $row[] = $item['created_by'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

        unset($data);

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


    private function _exportimeilock($data)
    {

        //print_r($data);die;
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', '200M');
        $filename = '_Export_Imei_lock_'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);
        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'No.',
            'Imei',
            'Add by',
            'Add Date',
        );
        fputcsv($output, $heads);

        $i = 1 ;
        foreach($data as $item)
        {

            $row = array();
            $row[] = $i;
            $row[] = $item['imei_log'];
            $row[] = $item['username'];
            $row[] = $item['created_date'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
            $i++;
        }


        unset($data);

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

}
