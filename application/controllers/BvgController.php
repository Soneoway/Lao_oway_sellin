<?php
class BvgController extends My_Controller_Action
{
    function indexAction()
    {

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $imei_sn = $this->getRequest()->getParam('imei_sn');
        $sales_sn = $this->getRequest()->getParam('sales_sn');
        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $d_id = $this->getRequest()->getParam('d_id');
        $status = $this->getRequest()->getParam('status');
        $approve = $this->getRequest()->getParam('approve');

        $page = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $sort = $this->getRequest()->getParam('sort', '');
        $desc = $this->getRequest()->getParam('desc', 1);
        $export = $this->getRequest()->getParam('export');
        $total = 0;



        $QDistributor = new Application_Model_Distributor();
        $this->view->distributorsCached = $QDistributor->get_cache();

        $QGood = new Application_Model_Good();
        $this->view->goodsCached = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->goodColorsCached = $QGoodColor->get_cache();

        $QJointCircular = new Application_Model_JointCircular();
        $this->view->jointCircularCached = $QJointCircular->get_cache();
        

        $QBvgProduct = new Application_Model_BvgProduct();
        $paramsProduct      = array(
            'IA' => 1
        );
        $products = $QBvgProduct->fetchPagination($page,null,$total,$paramsProduct);

        $QBvgProduct = new Application_Model_BvgProduct();

        if(isset($good_id) and $good_id)
        {
            $bvgrowset = $QBvgProduct->find($good_id);
            $bvg_product = $bvgrowset->current();
        }

        $params = array(
            'imei_sn' => $imei_sn,
            'sales_sn' => $sales_sn,
            'joint_circular_id' => $joint_circular_id,
            'product_id' => $good_id,
            'good_id' => isset($bvg_product['good_id']) ? $bvg_product['good_id'] : null,
            'price' => isset($bvg_product['price']) ? $bvg_product['price'] : null,
            'd_id' => $d_id,
            'status' => $status,
            'sort' => $sort,
            'desc' => $desc,
            'market_product' => 1
        );



        $this->view->products = $products;

        $QBvgImei = new Application_Model_BvgImei();

        if ($approve) {
            $params['not_get_total'] = true;
            $listIds = $QBvgImei->fetchPagination(1, null, $total, $params);
            if ($listIds) {
                $result = $QBvgImei->approve($listIds);
                if ($result['code'] != 0)
                    $flashMessenger->setNamespace('error')->addMessage($result['message']);
                else
                    $flashMessenger->setNamespace('success')->addMessage('Done');

                $this->redirect('/bvg');
            }
        }

        $list = $QBvgImei->fetchPagination($page, $limit, $total, $params);

        if (isset($export) and $export)
        {   //export for imei
            $params['market_product'] = 1;
            $list = $QBvgImei->fetchPagination($page, null, $total, $params);
            $this->_export_bvg_imei($list);
        }


        $this->view->list = $list;
        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->page = $page;
        $this->view->offset = $limit * ($page - 1);
        $this->view->params = $params;
        $this->view->sort = $sort;
        $this->view->desc = $desc;
        $this->view->url = HOST . 'bvg' . ($params ? '?' . http_build_query($params) . '&' : '?');
        $this->view->messages_success = $messages_success;
        $this->view->messages = $messages;
    }

    function createAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success = $flashMessenger->setNamespace('success')->getMessages();

        $id = $this->getRequest()->getParam('id');

        $QBvgImei = new Application_Model_BvgImei();
        if ($id) {
            $whereBvgImei = $QBvgImei->getAdapter()->quoteInto('id = ?', $id);
            $info = $QBvgImei->fetchRow($whereBvgImei);

            if (!$info) {
                $flashMessenger->setNamespace('error')->addMessage('Please choose correct IMEI!');
                $this->_redirect('/bvg');
            }

            $this->view->info = $info;

        }

        $QDistributor = new Application_Model_Distributor();
        $QJointCircular = new Application_Model_JointCircular();
        $this->view->distributorsCached = $QDistributor->get_cache();
        $this->view->jointCircular = $QJointCircular->fetchAll();

        if ($this->getRequest()->isPost()) {


            $imei_sn = $this->getRequest()->getParam('imei_sn');
            $d_id = $this->getRequest()->getParam('d_id');
            $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
            $sales_sn = $this->getRequest()->getParam('sales_sn');
            $price = $this->getRequest()->getParam('price');
            $invoice_number = $this->getRequest()->getParam('invoice_number');
            $invoice_sign = $this->getRequest()->getParam('invoice_sign');

            if ($imei_sn and $d_id and $joint_circular_id) {
                $params = array(
                    'id' => $id,
                    'imei_sn' => $imei_sn,
                    'd_id' => $d_id,
                    'joint_circular_id' => $joint_circular_id,
                    'sales_sn' => $sales_sn,
                    'price' => $price,
                    'invoice_number' => $invoice_number,
                    'invoice_sign' => $invoice_sign,

                    // check data
                    'check_imei_info' => true,
                    'check_status' => true,
                    'check_dealer' => true,
                    'get_invoice' => true,
                    'get_price' => true,
                );
                $result = $QBvgImei->save($params);

                // check
                if ($result['code'] != 0)
                    $flashMessenger->setNamespace('error')->addMessage($result['message']);
                else
                    $flashMessenger->setNamespace('success')->addMessage('Done');
            }
            $this->_redirect('/bvg');
        }
        $this->view->messages_success = $messages_success;
        $this->view->messages = $messages;
    }

    function massAction()
    {
        $QJointCircular = new Application_Model_JointCircular();
        $this->view->jointCircular = $QJointCircular->fetchAll();

        $QJointType = new Application_Model_JointType();


        $this->view->joint_type = $QJointType->get_cache();

        $QGood          = new Application_Model_Good();
        $whereGood      = array();
        $whereGood[]    = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);
        $good = $QGood->fetchAll($whereGood)->toArray();
        $this->view->good = $good;

    }

    function massAccessoriesAction()
    {
        $QJointCircular = new Application_Model_JointCircular();
        $this->view->jointCircular = $QJointCircular->fetchAll();

        $QJointType = new Application_Model_JointType();


        $this->view->joint_type = $QJointType->get_cache();

        $QGood          = new Application_Model_Good();
        $whereGood      = array();
        $whereGood[]    = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);
        $good = $QGood->fetchAll($whereGood)->toArray();
        $this->view->good = $good;

    }

    //**Love is not true**//
    function jointCircularAction()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $total = 0;
        $params = array();

        $QJointCircular = new Application_Model_JointCircular();
        $joint = $QJointCircular->fetchPagination($page, $limit, $total, $params);

        $this->view->joint = $joint;

        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST . 'group/' . ($params ? '?' . http_build_query($params) . '&' : '?');
        $this->view->offset = $limit * ($page - 1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $messages;
    }

    function massSelloutAction()
    {

        $QJointCircular = new Application_Model_JointCircular();
        $where = $QJointCircular->getAdapter()->quoteInto('type = ?', BVG_SUPPORT_SELL_OUT);
        $this->view->jointCircular = $QJointCircular->fetchAll($where);
        $QDistributor = new Application_Model_Distributor();
        $this->view->distributor = $QDistributor->get_cache();

    }

    /*bvg cu*/
    function massBvgOldAction()
    {

    }

    /*save*/
    function massBvgOldSaveAction()
    {
        $this->_helper->layout->disableLayout();
        {
            if ($this->getRequest()->getMethod() == 'POST') {
                define('MASS_ROW_START', 2);
                define('MASS_DISTRIBUTOR_ID', 1);
                define('MASS_GOOD', 2);
                define('MASS_PRICE', 3);
                define('MASS_NUMBER', 4);
                define('MASS_TOTAL', 5);
                define('MASS_DATE', 7);
                define('MASS_INVOICE_NUMBER', 6);
                define('MASS_JOINT_CIRCULAR', 8);
                $text = 'Old bvg';
                $number_order = 0;
                set_time_limit(0);
                ini_set('memory_limit', -1);
                $progress = new My_File_Progress('parent.set_progress');
                $progress->flush(0);
                $upload = new Zend_File_Transfer();
                $uniqid = uniqid('', true);
                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                if (function_exists('finfo_file'))
                    $upload->addValidator('MimeType', false, array(
                        'application/vnd.ms-excel',
                        'application/excel',
                        'application/x-excel',
                        'application/x-msexcel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));

                $upload->addValidator('Extension', false, 'xls,xlsx');

                $upload->addValidator('FilesSize', false, array('max' => '20MB'));

                $upload->addValidator('ExcludeExtension', false, 'php,sh');

                $upload->addValidator('Count', false, 1);

                $uniqid = uniqid();

                $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' .
                    DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'upload' .
                    DIRECTORY_SEPARATOR . 'bvg_save_out' . DIRECTORY_SEPARATOR . $uniqid;

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

                } else {
                    try {


                        $upload->receive();
                        $error_imei = array();
                        $path_info = pathinfo($upload->getFileName());
                        $filename = $path_info['filename'];
                        $extension = $path_info['extension'];

                        $old_name = $filename . '.' . $extension;
                        $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                        if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                            rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                        } else {
                            $new_name = $old_name;
                        }

                        $QMarket_product = new Application_Model_MarketProduct();
                        $QBvgImei = new Application_Model_BvgImei2();
                        $QGood = new Application_Model_Good();

                        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                        $QFileLog = new Application_Model_FileUploadLog();

                        $data = array(
                            'staff_id' => $userStorage->id,
                            'folder' => $uniqid,
                            'filename' => $new_name,
                            'type' => 'mass BVG old nhu cai noi',
                            'real_file_name' => $filename . '.' . $extension,
                            'uploaded_at' => time(),
                        );

                        $log_id = $QFileLog->insert($data);

                        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
                        $error_list = array();

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
                        $total_order_row = $highestRow - MASS_ROW_START + 1;
                        $number_of_imei = 0;
                        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

                        for ($i = MASS_ROW_START; $i <= $highestRow; $i++) {

                            $d_id = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_DISTRIBUTOR_ID, $i)
                                ->getValue());
                            $good_id = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_GOOD, $i)
                                ->getValue());
                            $price = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_PRICE, $i)
                                ->getValue());
                            $num = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_NUMBER, $i)
                                ->getValue());
                            $total = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_TOTAL, $i)
                                ->getValue());
                            $invoice_number = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_INVOICE_NUMBER, $i)
                                ->getValue());
                            $date = trim($objWorksheet
                                ->getCellByColumnAndRow(MASS_DATE, $i)
                                ->getValue());
                            $joint_circular_id = trim($objWorksheet->getCellByColumnAndRow(MASS_JOINT_CIRCULAR, $i)->getValue());

                            $Good_rowset = $QGood->find($good_id);
                            $goods = $Good_rowset->current();


                            $sn = My_Sale_Order::generateSn();

                            $data = array(
                                'sn' => $sn,
                                'user_id' => $userStorage->id,
                                'd_id' => $d_id,
                                'num' => $num,
                                'price' => $price,
                                'total' => $total,
                                'good_id' => $good_id,
                                'joint' => $joint_circular_id,
                                'text' => trim($text),
                                'add_time' => date('YYYY-MM-DD')

                            );

                            $id_market_product = $QMarket_product->insert($data);

                            //update list imei
                            $where = array();
                            $where[] = $QBvgImei->getAdapter()->quoteInto('d_id = ? ', $d_id);
                            $where[] = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?', $joint_circular_id);
                            $where[] = $QBvgImei->getAdapter()->quoteInto('good_id = ?', $good_id);

                            $list_imei = $QBvgImei->fetchAll($where);
                            if ($list_imei->count() != $num) {
                                $error_list[] = array('Imei is limited', $i);
                                continue;
                            }

                            $data_imei = array(
                                'bvg_market_product_id' => $id_market_product,
                                'bvg_payment_confirmed_at' => $date,
                            );

                            $QBvgImei->update($data_imei, $where);

                            $number_order++;

                            $percent = round($number_order * 100 / $total_order_row, 1);
                            $progress->flush($percent);
                        }
                    } catch (exception $e) {
                        var_dump($e);
                        exit;
                    }
                }
                $progress->flush(0);
            }
        }
    }

    function saveMassSellOutAction()
    {
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->getMethod() == 'POST') {

            define('MASS_BVG_SAVE_OUT_LIST_ROW_START', 2);
            define('MASS_BVG_SAVE_OUT_COL_DEALER', 2);
            define('MASS_BVG_SAVE_OUT_COL_PRICE', 1);
            define('MASS_BVG_SAVE_OUT_COL_IMEI', 0);
            set_time_limit(0);
            ini_set('memory_limit', -1);
            $progress = new My_File_Progress('parent.set_progress');
            $progress->flush(0);
            $upload = new Zend_File_Transfer();
            $uniqid = uniqid('', true);
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            if (function_exists('finfo_file'))
                $upload->addValidator('MimeType', false, array(
                    'application/vnd.ms-excel',
                    'application/excel',
                    'application/x-excel',
                    'application/x-msexcel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));

            $upload->addValidator('Extension', false, 'xls,xlsx');

            $upload->addValidator('FilesSize', false, array('max' => '20MB'));

            $upload->addValidator('ExcludeExtension', false, 'php,sh');

            $upload->addValidator('Count', false, 1);

            $uniqid = uniqid();

            $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'upload' .
                DIRECTORY_SEPARATOR . 'bvg_save_out' . DIRECTORY_SEPARATOR . $uniqid;

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

            } else {
                try {
                    $upload->receive();
                    $error_imei = array();
                    $path_info = pathinfo($upload->getFileName());
                    $filename = $path_info['filename'];
                    $extension = $path_info['extension'];
                    $QImei = new Application_Model_Imei();
                    $QBvgSelloutImei = new Application_Model_BvgSelloutImei();

                    $old_name = $filename . '.' . $extension;
                    $new_name = 'UPLOAD-' . md5($filename . uniqid('', true)) . '.' . $extension;

                    if (is_file($uploaded_dir . DIRECTORY_SEPARATOR . $old_name)) {
                        rename($uploaded_dir . DIRECTORY_SEPARATOR . $old_name, $uploaded_dir . DIRECTORY_SEPARATOR . $new_name);
                    } else {
                        $new_name = $old_name;
                    }

                    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                    $QFileLog = new Application_Model_FileUploadLog();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'mass BVG upload',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');

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
                    $total_order_row = $highestRow - MASS_BVG_SAVE_OUT_LIST_ROW_START + 1;
                    $number_of_imei = 0;
                    $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
                    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5


                    for ($i = MASS_BVG_SAVE_OUT_LIST_ROW_START; $i <= $highestRow; $i++) {

                        $imei_sn = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_SAVE_OUT_COL_IMEI, $i)
                            ->getValue());

                        $where = array();
                        $where[] = $QBvgSelloutImei->getAdapter()->quoteInto('imei_sn = ? ', $imei_sn);
                        $where[] = $QBvgSelloutImei->getAdapter()->quoteInto('joint_circular_id = ?', $joint_circular_id);
                        $result = $QBvgSelloutImei->fetchRow($where);

                        if (!empty($result)) {
                            continue;
                        }


                        $d_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_SAVE_OUT_COL_DEALER, $i)
                            ->getValue());

                        $price = intval(trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_SAVE_OUT_COL_PRICE, $i)
                            ->getValue()));


                        $resultSet = $QImei->find($imei_sn);
                        $imei = $resultSet->current();

                        if (empty($imei))
                            $error_imei[] = $imei_sn;


                        $data = array(
                            'imei_sn' => $imei_sn,
                            'joint_circular_id' => $joint_circular_id,
                            'd_id' => $d_id,
                            'price' => $price,
                            'good_id' => $imei['good_id'],
                            'good_color' => $imei['good_color'],
                            'created_at' => date('Y-m-d h:i:s')
                        );

                        $QBvgSelloutImei->insert($data);

                        $number_of_imei++;

                        $percent = round($number_of_imei * 100 / $total_order_row, 1);
                        $progress->flush($percent);
                    }

                } catch (exception $e) {
                    $this->view->error = $e->getMessage();
                }
            }
            $progress->flush(0);
        }
    }

    function inc_vat($num){
           return number_format(($num*1.07),2);
    }

    function saveMassAccessoriesAction()
    {
        $this->_helper->layout->disableLayout();

        //print_r($_POST);die;
        if ($this->getRequest()->getMethod() == 'POST') {
            define('MASS_BVG_LIST_ROW_START', 2);
            define('MASS_BVG_LIST_COL_INVOICE_NUMBER', 0);
            define('MASS_BVG_LIST_COL_PRODUCT_CODE', 1);
            define('MASS_BVG_LIST_COL_PRODUCT_NAME', 2);
            define('MASS_BVG_LIST_COL_PRODUCT_COLOR', 3);
            define('MASS_BVG_LIST_COL_QTY', 4);
            define('MASS_BVG_LIST_COL_PRICE', 5);
            define('MASS_BVG_LIST_COL_OUT_PRICE', 6);
            define('MASS_BVG_LIST_COL_NEW_PRICE', 7);
            define('MASS_BVG_LIST_COL_REMARK', 8);

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

            } else {
                try {
                    
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
                    $QFileLog = new Application_Model_FileUploadLog();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'mass BVG upload',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $number_of_order = 0;
                    $error_list = array();
                    $success_list = array();
                    $listBvgByProduct = array();

                    $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');

                    $QImei    = new Application_Model_Imei();
                    $QBvgImei = new Application_Model_BvgImei();
                    $QBvgProduct = new Application_Model_BvgProduct();

                    $whereBvgProduct = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', $joint_circular_id);
                    $BvgProduct = $QBvgProduct->fetchAll($whereBvgProduct);
                    if ($BvgProduct->count()) {
                        foreach ($BvgProduct as $item) {
                            $listBvgByProduct[$item['good_id']] = $item['price'];
                        }
                    }

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

                        $invoice_number = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_INVOICE_NUMBER, $i)
                            ->getValue());

                        $product_code = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_CODE, $i)
                            ->getValue());

                        $product_name = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_NAME, $i)
                            ->getValue());

                        $product_color = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR, $i)
                            ->getValue());

                        $qty = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_QTY, $i)
                            ->getValue());

                        $price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PRICE, $i)
                            ->getValue());
                        
                        $out_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_OUT_PRICE, $i)
                            ->getValue());

                        $new_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_NEW_PRICE, $i)
                            ->getValue());

                        $remark = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_REMARK, $i)
                            ->getValue());

                        //Tanong
                        
                        if($invoice_date==''){
                            $invoice_date=null;
                        }
                        

                        $chk_accs = $this->check_accessories($invoice_number,$product_code,$product_color);
                        //print_r($chk_accs);die;
                        if(is_array($chk_accs)>0){
                            //print_r($chk_accs);
                            $imei_sn=1;
                            $d_id=$chk_accs['d_id'];
                            $good_id=$chk_accs['good_id'];
                            $good_color=$chk_accs['good_color'];
                            $invoice_date=$chk_accs['invoice_time'];
                            $cat_id=$chk_accs['cat_id'];
                            $sales_sn=$chk_accs['sn'];
                            if($out_price==''){
                                $out_price=$chk_accs['price'];
                            }

                           $bvg_price = $price;
                           $total_price = ($bvg_price*$qty);

                           //echo $this->inc_vat($total_price);
                           //die;

                            //insert into bvg_imei
                            $key_sn = $d_id.$sn;
                            $data = array(
                                'imei_sn' => $imei_sn,
                                'd_id' => $d_id,
                                'good_id' => $good_id,
                                'good_color' => $good_color,
                                'qty' => $qty,
                                'joint_circular_id' => $joint_circular_id,
                                'check_imei_info' => true,
                                'check_status' => true,
                                'check_dealer' => false,
                                'get_invoice' => false,
                                'get_price' => false,
                                'listBvgByProduct' => $listBvgByProduct,
                                'sales_sn' => $sales_sn,
                                'invoice_number' => $invoice_number,
                                'invoice_date' => $invoice_date,
                                'price' => $bvg_price,
                                'out_price' => $out_price,
                                'total_price' => $total_price,
                                'sn' => $key_sn,
                                'create_by' => $userStorage->id,
                                'remark' => $remark,
                                'cat_id' => $cat_id
                            );

                            //$data['cat_id']=12;

                            $data_sn[$d_id][0] = $key_sn;
                           // print_r($data);die;
                            $result = $QBvgImei->save_accessories($data);
                        }else{
                            
                            $data_error['invoice_number'] = $invoice_number;
                            $data_error['product_code'] = $product_code;
                            $data_error['product_name'] = $product_name;
                            $data_error['product_color'] = $product_color;
                            $data_error['message'] = "Good is not existed in System or Not Sales Out";
                            $error_list[] = $data_error;
                        }

                        //print_r($result);die;
                        //$data['dealer_name'] = $dealer_name;
                        $status = $result['code'];
                        if ($result['code'] == 0) {
                            $success_list[] = $data;                           
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }

                        $number_of_order++;
                        $percent = round($number_of_order * 100 / $total_order_row, 1);
                        $progress->flush($percent);
                    }

                    //print_r($data_sn);die;
                    foreach($data_sn as $key => $val){
                        //echo "$v";
                        $d_id = $key;
                        $key_sn = $val[0];
                        $creditnote_sn='';

                        if($key_sn!='' && $status==0)
                        {

                            $creditnote_sn = $this->getProtectionPriceNo_Ref($db,$key_sn);

                            $create_date = date('Y-m-d H:i:s');
                            $data = array(
                                'distributor_id' => $d_id,
                                'create_by' => $userStorage->id,
                                'create_date' => $create_date,
                                'creditnote_type' => 'CP',
                                'total_amount' => 0,
                                'use_total' => 0,
                                'balance_total' => 0,
                                'status' => 1,
                                'creditnote_sn' => $creditnote_sn,
                                'sn' => $key_sn
                            );

                            $QCreditNote = new Application_Model_CreditNote();
                            $QCreditNote->insert($data);

                            if($creditnote_sn==''){
                                $sError='Cannot Import Credit Note.';
                                $this->view->error = $sError;
                                $db->rollback();
                                $progress->flush(0);
                                return;
                            }

                            $data = array(
                                'create_date' => $create_date,
                                'creditnote_sn' => $creditnote_sn
                            );
                            $whereBvgImei = $QBvgImei->getAdapter()->quoteInto('sn = ?', $key_sn);
                            //$whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('(creditnote_sn IS NULL OR creditnote_sn='')', null);
                            $QBvgImei->update($data, $whereBvgImei);

                            //$db->query("CALL gen_product_price('CP',".$key_sn.",10)");
              

                            $db->query("CALL gen_product_price (?, ?, ?);", array(
                                'CP',
                                $userStorage->id,
                                $key_sn,
                            ));
                            
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

            // get product list
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_INVOICE_NUMBER, 1, 'INVOICE_NUMBER');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_CODE, 1, 'PRODUCT_CODE');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_NAME, 1, 'PRODUCT_NAME');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRODUCT_COLOR, 1, 'PRODUCT_COLOR');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_QTY, 1, 'QTY');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_PRICE, 1, 'PRICE');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_OUT_PRICE, 1, 'OUT_PRICE');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_NEW_PRICE, 1, 'NEW_PRICE');
            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_REMARK, 1, 'REMARK');

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
                        $objWriter->save($new_file_dir);
                    }
                    // END IF // xuất file excel các order lỗi

                    $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                    $QFileLog->update($data, $where);

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
                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
                
            }// end of check file

           // unlink(APPLICATION_PATH . '/../public/files/mou/lock');

           // $progress->flush(0);
        } // end of check POST
    }


    function saveMassAction()
    {
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->getMethod() == 'POST') {
            define('MASS_BVG_LIST_ROW_START', 2);
            define('MASS_BVG_LIST_COL_DEALER', 0);
            define('MASS_BVG_LIST_COL_IMEI', 1);
            define('MASS_BVG_LIST_COL_INVOICE_NUMBER', 2);
            define('MASS_BVG_LIST_COL_PRICE', 3);
            define('MASS_BVG_LIST_COL_OUT_PRICE', 4);
            define('MASS_BVG_LIST_COL_REMARK', 5);
            define('MASS_BVG_LIST_COL_DEALER_NAME', 6);
            //define('MASS_BVG_LIST_COL_INVOICE_DATE', 4);

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

            } else {
                try {
                    
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
                    $QFileLog = new Application_Model_FileUploadLog();

                    $data = array(
                        'staff_id' => $userStorage->id,
                        'folder' => $uniqid,
                        'filename' => $new_name,
                        'type' => 'mass BVG upload',
                        'real_file_name' => $filename . '.' . $extension,
                        'uploaded_at' => time(),
                    );

                    $log_id = $QFileLog->insert($data);

                    $number_of_order = 0;
                    $error_list = array();
                    $success_list = array();
                    $listBvgByProduct = array();

                    $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');

                    $QImei    = new Application_Model_Imei();
                    $QBvgImei = new Application_Model_BvgImei();
                    $QBvgProduct = new Application_Model_BvgProduct();

                    $whereBvgProduct = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', $joint_circular_id);
                    $BvgProduct = $QBvgProduct->fetchAll($whereBvgProduct);
                    if ($BvgProduct->count()) {
                        foreach ($BvgProduct as $item) {
                            $listBvgByProduct[$item['good_id']] = $item['price'];
                        }
                    }

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


                        $d_id = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i)
                            ->getValue());

                        $imei_sn = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_IMEI, $i)
                            ->getValue());                      

                        $invoice_number = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_INVOICE_NUMBER, $i)
                            ->getValue());

                        $price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_PRICE, $i)
                            ->getValue());

                        $out_price = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_OUT_PRICE, $i)
                            ->getValue());

                        $remark = trim($objWorksheet
                            ->getCellByColumnAndRow(MASS_BVG_LIST_COL_REMARK, $i)
                            ->getValue());

                        //Tanong
                        
                        if($invoice_date==''){
                            $invoice_date=null;
                        }
                        if($out_price==''){
                            $out_price=null;
                        }

                        
                        if($out_price!=null){
                            $currentTime          = date('Y-m-d H:i:s');
                            $data_Imei = array(
                                'distributor_id' => $d_id,
                                'out_price' => $out_price,
                                'price_date' => $currentTime,
                                'out_user' => $userStorage->id,
                                'remark' => 'CP['.$d_id.']['.$out_price.']'.'-'.$remark
                            );
                            //$whereImei         = array();
                            $whereImei[]      = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                           // $whereImei[]      = $QImei->getAdapter()->quoteInto('out_price <=0', null);
                           // $whereImei[]      = $QImei->getAdapter()->quoteInto('sales_sn is null', null);
                            $QImei->update($data_Imei, $whereImei);
                        }

                        if($this->check_imei($imei_sn,$d_id)>0){
   
                            //insert into bvg_imei
                            $key_sn = $d_id.$sn;
                            $data = array(
                                'imei_sn' => $imei_sn,
                                'd_id' => $d_id,
                                'joint_circular_id' => $joint_circular_id,
                                'check_imei_info' => true,
                                'check_status' => true,
                                'check_dealer' => false,
                                'get_invoice' => false,
                                'get_price' => false,
                                'listBvgByProduct' => $listBvgByProduct,
                                'invoice_number' => $invoice_number,
                                'invoice_date' => $invoice_date,
                                'price' => $price,
                                'out_price' => $out_price,
                                'sn' => $key_sn,
                                'create_by' => $userStorage->id,
                                'remark' => $remark
                            );

                            if($imei_sn !=''){
                                $data['cat_id']=11;
                            }

                            $data_sn[$d_id][0] = $key_sn;
                            $result = $QBvgImei->save($data);
                        }else{
                            
                            $data_error['d_id'] = $d_id;
                            $data_error['dealer_name'] = $dealer_name;
                            $data_error['imei_sn'] = $imei_sn;
                            $data_error['message'] = "IMEI is not existed in System or Not Sales Out";
                            $error_list[] = $data_error;
                        }

                        //print_r($result);die;
                        //$data['dealer_name'] = $dealer_name;
                        $status = $result['code'];
                        if ($result['code'] == 0) {
                            $success_list[] = $data;                           
                        } else {
                            $data['message'] = $result['message'];
                            $error_list[] = $data;
                        }

                        $number_of_order++;
                        $percent = round($number_of_order * 100 / $total_order_row, 1);
                        $progress->flush($percent);
                    }

                    //print_r($data_sn);die;
                    foreach($data_sn as $key => $val){
                        //echo "$v";
                        $d_id = $key;
                        $key_sn = $val[0];
                        $creditnote_sn='';

                        if($key_sn!='' && $status==0)
                        {

                            $creditnote_sn = $this->getProtectionPriceNo_Ref($db,$key_sn);

                            $create_date = date('Y-m-d H:i:s');
                            $data = array(
                                'distributor_id' => $d_id,
                                'create_by' => $userStorage->id,
                                'create_date' => $create_date,
                                'creditnote_type' => 'CP',
                                'total_amount' => 0,
                                'use_total' => 0,
                                'balance_total' => 0,
                                'status' => 0,
                                'chanel' => 'price_protection',
                                'creditnote_sn' => $creditnote_sn,
                                'sn' => $key_sn
                            );

                            $QCreditNote = new Application_Model_CreditNote();
                            $QCreditNote->insert($data);

                            if($creditnote_sn==''){
                                $sError='Cannot Import Credit Note.';
                                $this->view->error = $sError;
                                $db->rollback();
                                $progress->flush(0);
                                return;
                            }

                            $data = array(
                                'create_date' => $create_date,
                                'creditnote_sn' => $creditnote_sn
                            );
                            $whereBvgImei = $QBvgImei->getAdapter()->quoteInto('sn = ?', $key_sn);
                            //$whereBvgImei[] = $QBvgImei->getAdapter()->quoteInto('(creditnote_sn IS NULL OR creditnote_sn='')', null);
                            $QBvgImei->update($data, $whereBvgImei);

                            //$db->query("CALL gen_product_price('CP',".$key_sn.",10)");
              
                            $db->query("CALL gen_product_price (?, ?, ?);", array(
                                'CP',
                                $userStorage->id,
                                $key_sn,
                            ));
                            
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
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, 1, 'ID');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER_NAME, 1, 'Dealer');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_IMEI, 1, 'IMEI');
                        $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_IMEI + 1, 1, 'Reason');


                        // các dòng lỗi
                        $i = 2;
                        foreach ($error_list as $key => $row) {
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER, $i, $row['d_id']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_DEALER_NAME, $i, $row['dealer_name']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_IMEI, $i, $row['imei_sn']);
                            $objWorksheet_out->setCellValueByColumnAndRow(MASS_BVG_LIST_COL_IMEI + 1, $i, $row['message']);
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
                        $objWriter->save($new_file_dir);
                    }
                    // END IF // xuất file excel các order lỗi

                    $where = $QFileLog->getAdapter()->quoteInto('id = ?', $log_id);
                    $QFileLog->update($data, $where);

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
                } // end of Try
                catch (Exception $e) {
                    $db->rollback();
                    $this->view->error = $e->getMessage();
                    $progress->flush(0);
                }
            }// end of check file

           // unlink(APPLICATION_PATH . '/../public/files/mou/lock');

            
        } // end of check POST
    }

    public function getBalanceAction()
    {
        $d_id = $this->getRequest()->getParam('d_id');
        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: application/json');

        if (!$d_id)
            exit(json_encode(array('code' => '-1', 'error' => 'Invalid params.')));

        $QBvgImei = new Application_Model_BvgImei();
        $resultBvgImei = $QBvgImei->getBalance(array(
            'd_id' => $d_id,
            'joint_circular_id' => $joint_circular_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
        ));


        // lay first data
        $QBvgDistributor = new Application_Model_BvgDistributor();
        $resultBvgDistributor = $QBvgDistributor->getBalance(array(
            'd_id' => $d_id,
        ));


        exit(json_encode(array(
            'code' => 0,
            'data' => array(
                'bvg' => $resultBvgImei,
                'bvg_first' => $resultBvgDistributor,
            ),
        )));
    }
    /*rollback imei*/
    public function rollbackAction()
    {
        $id = $this->getRequest()->getParam('id');
        $note = $this->getRequest()->getParam('reason');
        $QBvgImei    = new Application_Model_BvgImei();
        $QBvgImeiLog = new Application_Model_BvgImeiLog();
        $sn = $this->getRequest()->getParam('sn');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id = $userStorage->id;


        $imei_rowset = $QBvgImei->find($id);
        $imei = $imei_rowset->current();

        header('Content-Type: application/json');

        try
        {
            $where = array();

            if (!$id)
                exit(json_encode(array('code' => '-1', 'error' => 'Invalid imei 404.')));

            if (!$imei)
                exit(json_encode(array('code' => '-1', 'error' => 'Invalid imei.')));

            if (!$note)
                exit(json_encode(array('code' => '-1', 'error' => 'Please insert reason')));

            if (isset($imei['payment_confirmed_at']) and $imei['payment_confirmed_at'])
                exit(json_encode(array('code' => '-1', 'error' => 'Imei is not confirmed by fiannace')));

            $where  = array();
            $where[] = $QBvgImei->getAdapter()->quoteInto('id = ?' , $id);
            $where[] = $QBvgImei->getAdapter()->quoteInto('bvg_payment_confirmed_at is not null' , null);

            $data = array(
                'bvg_payment_confirmed_at' => null,
            );

            $QBvgImei->update($data , $where);

            $datalog = array(
                'imei_sn' => $imei['imei_sn'],
                'type' => 3,
                'note' => trim($note),
                'sn' => trim($sn),
                'created_at' => date('Y-m-d h:i:s'),
                'created_by' => $user_id,
            );

            $QBvgImeiLog->insert($datalog);

            exit(json_encode(array('code' => '1', 'message' => 'success')));
        }

        catch(exception $e)
        {
            exit(json_encode(array('code' => '-1', 'error' => $e->getMessage())));
        }
    }
    /*confirm imei is bvg*/
    public function confirmPaymentAction()
    {
        $id = $this->getRequest()->getParam('id');
        $note = $this->getRequest()->getParam('reason');
        $date = $this->getRequest()->getParam('date');
        $QBvgImei    = new Application_Model_BvgImei();
        $QBvgImeiLog = new Application_Model_BvgImeiLog();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id = $userStorage->id;


        $imei_rowset = $QBvgImei->find($id);
        $imei = $imei_rowset->current();

        header('Content-Type: application/json');

        try
        {
            $where = array();

            if (!$id)
                exit(json_encode(array('code' => '-1', 'error' => 'Invalid imei 404.')));

            if (!$imei)
                exit(json_encode(array('code' => '-1', 'error' => 'Invalid imei.')));

            if (!$note)
                exit(json_encode(array('code' => '-1', 'error' => 'Please insert reason')));

            if (!$date)
                exit(json_encode(array('code' => '-1', 'error' => 'Please insert date')));

            if(isset($date) and $date)
            {
                $temp = explode('/' , $date);
                $temp = $temp[2] . '-' . $temp[1] . '-' . $temp[0] . ' 00:00:00';
                $date = trim($temp);
            }



            if (isset($imei['payment_confirmed_at']) and $imei['payment_confirmed_at'])
                exit(json_encode(array('code' => '-1', 'error' => 'Imei is not confirmed by fiannace')));

            $where  = array();
            $where[] = $QBvgImei->getAdapter()->quoteInto('id = ?' , $id);
            $where[] = $QBvgImei->getAdapter()->quoteInto('bvg_payment_confirmed_at is null' , null);

            $data = array(
                'bvg_payment_confirmed_at' => $date,
            );

            $QBvgImei->update($data , $where);

            $datalog = array(
                'imei_sn' => $imei['imei_sn'],
                'type' => 3,
                'note' => trim($note),
                'created_at' => date('Y-m-d h:i:s'),
                'created_by' => $user_id,
            );

            $QBvgImeiLog->insert($datalog);

            exit(json_encode(array('code' => '1', 'message' => 'success')));
        }

        catch(exception $e)
        {
            exit(json_encode(array('code' => '-1', 'error' => $e->getMessage())));
        }
    }

    public function getImeiAction()
    {
        $d_id = $this->getRequest()->getParam('d_id');
        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');
        $num = $this->getRequest()->getParam('num');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        header('Content-Type: application/json');

        if (!$d_id)
            exit(json_encode(array('code' => '-1', 'error' => 'Invalid params.')));

        $QBvgImei = new Application_Model_BvgImei();
        $resultBvgImei['data'] = $QBvgImei->getImei(array(
            'd_id' => $d_id,
            'joint_circular_id' => $joint_circular_id,
            'good_id' => $good_id,
            'num' => $num
        ));



        //ok
        $resultBvgImei['result'] = 1;
        $total_imei = count($resultBvgImei['data']);
        // không đủ imei
        if($total_imei < $num)
            $resultBvgImei['result'] = 3;
        if(empty($resultBvgImei['data']))
        {
            $resultBvgImei['result'] = 4;
        }

        exit(json_encode($resultBvgImei));
    }

    function approveAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $flashMessenger     = $this->_helper->flashMessenger;

        $id = $this->getRequest()->getParam('id');
        if (!$id){
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->redirect('/bvg');
        }

        $QBvgImei = new Application_Model_BvgImei();
        $result = $QBvgImei->approve(array($id));

        if ($result['code'] != 0)
            $flashMessenger->setNamespace('error')->addMessage($result['message']);
        else
            $flashMessenger->setNamespace('success')->addMessage('Done');

        $this->redirect('/bvg');
    }


    public function reportAction()
    {
        $order_from        = $this->getRequest()->getParam('order_from');
        $order_to          = $this->getRequest()->getParam('order_to');
        $invoice_from      = $this->getRequest()->getParam('invoice_from', date('01/m/Y'));
        $invoice_to        = $this->getRequest()->getParam('invoice_to', date('d/m/Y'));
        $sales_sn          = $this->getRequest()->getParam('sales_sn');
        $imei_sn           = $this->getRequest()->getParam('imei_sn');
        $invoice_number    = $this->getRequest()->getParam('invoice_number');
        $good_id           = $this->getRequest()->getParam('good_id');
        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
        $d_id              = $this->getRequest()->getParam('d_id');
        $store_code        = $this->getRequest()->getParam('store_code');
        $warehouse_id      = $this->getRequest()->getParam('warehouse_id');
        $export            = $this->getRequest()->getParam('export', 0);
        $export_follow     = $this->getRequest()->getParam('export_follow', 0);
        $export_all        = $this->getRequest()->getParam('export_all' , 0);
        $export_total      = $this->getRequest()->getParam('export_total' , 0);
        $export_discount   = $this->getRequest()->getParam('export_discount' , 0);
        $joint_type        = $this->getRequest()->getParam('joint_type' , 0);
        
        $QDistributor   = new Application_Model_Distributor();
        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QJointCircular = new Application_Model_JointCircular();
        $QWarehouse     = new Application_Model_Warehouse();
        $QJointType     = new Application_Model_JointType();

        $this->view->distributorsCached  = $QDistributor->get_cache();
        $this->view->goodsCached         = $QGood->get_cache();
        $this->view->goodColorsCached    = $QGoodColor->get_cache();
        $this->view->jointCircularCached = $QJointCircular->get_cache();
        $this->view->warehouse_cache     = $QWarehouse->get_cache();
        $this->view->joint_type          = $QJointType->get_cache();

        $joint_discount = $joint_incentive = array();
        $joint = $QJointCircular->fetchAll();
        foreach($joint as $k => $v)
        {
            if($v['type'] == DISCOUNT_TYPE_CK)
                $joint_discount[$v['id']] = $v['name'];

            if($v['type'] == DISCOUNT_TYPE_DIAMOND)
                $joint_incentive[$v['id']] = $v['name'];
        }

        $QBvgProduct = new Application_Model_BvgProduct();
        $page = 1;
        $total = 0;
        $params = array();
        $products = $QBvgProduct->fetchPagination($page,null,$total,$params);

        $this->view->products = $products;


        $params = array(
            'order_from'        => $order_from,
            'order_to'          => $order_to,
            'invoice_from'      => $invoice_from,
            'invoice_to'        => $invoice_to,
            'sales_sn'          => $sales_sn,
            'imei_sn'           => $imei_sn,
            'good_id'           => $good_id,
            'joint_circular_id' => $joint_circular_id,
            'invoice_number'    => $invoice_number,
            'd_id'              => $d_id,
            'store_code'        => $store_code,
            'warehouse_id'      => $warehouse_id,
            'joint_type'        => $joint_type
        );

        $this->view->params = $params;
        $this->view->joint_incentive = $joint_incentive;
        $this->view->joint_discount = $joint_discount;

        if ($export) {
            $this->_export_xml_bvg($params);
            exit;
        }

        if($export_all)
        {
            $this->_export_bvg_all($params);
            exit;
        }

        if($export_discount == 1)
        {
            $this->_export_discount_incentive($params);
            exit;
        }

        if($export_discount == 2)
        {
            $this->_export_discount_2($params);
            exit;
        }

        if ($export_follow) {
            $this->_export_xml_bvg_follow($params);
            exit;
        }

        if($export_total)
        {
            $this->_export_general($params);
            exit;
        }

    }

    private function _export_xml_bvg($params)
    {

            require_once 'ExcelWriterXML.php';
            set_time_limit(0);
            ini_set('memory_limit', -1);
            error_reporting(~E_ALL);
            ini_set("display_errors", 0);

            $filename = 'Report_BVG_model ' . date('Ymd_His') . '.xml';

            $xml = new ExcelWriterXML($filename);
            $xml->docAuthor('OPPO Vietnam');

            $xml->sendHeaders();

            $xml->stdOutStart();
            //////////////////////////////////////////////
            $sheet = $xml->addSheet('BVG');
            ////////////////////////////////////////////
            $heads = array(
                'Dealer ID',
                'Store Code',
                'Dealer Name',
                'Order SN',
                'Warehouse',
                'Product Name',
                'Invoice Number',
                'Invoice Time',
                'Total Value',
                'This Order Value',
                'Remain Value (After this order)',
                'Total Remain',
                'Type'
            );

            $sheet->stdOutSheetStart();

            ////////////////////////////////////////////
            $QJointCircular = new Application_Model_JointCircular();
            $QBvgProduct = new Application_Model_BvgProduct();
            $QGood = new Application_Model_Good();
            $QWarehouse = new Application_Model_Warehouse();
            $good_cache = $QGood->get_cache();
            $warehouse_cache = $QWarehouse->get_cache();

            //get bvg product information
            if(isset($params['good_id']) and $params['good_id'])
            {
                $resultSet = $QBvgProduct->find($params['good_id']);
                $goods = $resultSet->current();
                $params['joint_circular_id'] = $goods['joint_id'];
                $params['good_id'] = $goods['good_id'];
            }

            $db = Zend_Registry::get('db');

            // danh sách tất cả thông tư
            $where = array();

            //if (isset($params['joint_circular_id']) && $params['joint_circular_id'])
              //  $where[] = $QJointCircular->getAdapter()->quoteInto('id = ?', $params['joint_circular_id']);

            $joints = $QJointCircular->fetchAll($where);

            $i = 2;
            // duyệt từng thông tư
            foreach ($joints as $_key => $_joint) {
                // lấy các sản phẩm đc bvg trong mỗi thông tư
                $where = array();
                $where[] = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', intval($_joint['id']));

                if (isset($params['good_id']) && $params['good_id'])
                    $where[] = $QBvgProduct->getAdapter()->quoteInto('good_id = ?', $params['good_id']);

                $products = $QBvgProduct->fetchAll($where);



                // xuất bảng riêng cho từng sản phẩm
                foreach ($products as $_product_key => $_product) {
                    $select = $db->select()
                        ->distinct()
                        ->from(array('m' => 'market_product'),
                            array(
                                'm.type',
                                'm.d_id',
                                'm.sn',
                                'm.warehouse_id',
                                'm.invoice_number',
                                'm.invoice_time',
                                'm.total',
                                'joint_total' => 'get_total_bvg_by_joint(m.d_id, m.joint, m.good_id)',
                                'joint_by_order' => 'get_bvg_by_order(m.d_id, m.joint, m.good_id, m.sn)',
                                'joint_remain' => 'get_bvg_by_order_remain(m.d_id, m.joint, m.good_id, m.sn)',
                                'joint_total_after' => 'get_total_bvg_by_joint_after(m.d_id, m.joint, m.good_id)',
                            )
                        )
                        ->join(array('j' => 'joint_circular'), 'j.id=m.joint', array('j.name'))
                        ->join(array('d' => 'distributor'), 'd.id=m.d_id', array('d.title', 'd.store_code'))
                        ->where('m.add_time IS NOT NULL AND m.add_time <> 0')
                        ->where('m.good_id = ?', intval($_product['good_id']))
                        ->where('m.joint = ?', intval($_joint['id']))
                        ->where('m.status = ?', 1)
                       // ->where('m.invoice_number is not null',1)
                        ->where('m.canceled IS NULL OR m.canceled = ?', 0);
                       // ->order('m.invoice_time ASC');



                    if (isset($params['invoice_from']) && DateTime::createFromFormat('d/m/Y', $params['invoice_from']))
                        $select->where('m.add_time >= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_from'])->format('Y-m-d 00:00:00'));

                    if (isset($params['invoice_to']) && DateTime::createFromFormat('d/m/Y', $params['invoice_to']))
                        $select->where('m.add_time <= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_to'])->format('Y-m-d 23:59:59'));

                    if (isset($params['d_id']) && $params['d_id'])
                        $select->where('m.d_id = ?', $params['d_id']);

                    if (isset($params['sales_sn']) && $params['sales_sn'])
                        $select->where('m.sn = ?', $params['sales_sn']);

                    if (isset($params['joint_circular_id']) && $params['joint_circular_id'])
                        $select->where('m.joint = ?', $params['joint_circular_id']);

                    if (isset($params['good_id']) && $params['good_id'])
                        $select->where('m.good_id = ?', $params['good_id']);

                    if (isset($params['invoice_number']) && $params['invoice_number'])
                        $select->where('m.invoice_number = ?', $params['invoice_number']);

                    if (isset($params['warehouse_id']) && $params['warehouse_id'])
                        $select->where('m.warehouse_id = ?', $params['warehouse_id']);

                    $orders = $db->fetchAll($select);

                    $product_name = isset($good_cache[$_product['good_id']]) ? $good_cache[$_product['good_id']] : 'x';
                    $product_name .= sprintf(" (%s)", number_format($_product['price'], 0, '.', ','));



                    // tiêu đề
                    $sheet->stdOutSheetRowStart($i);
                    foreach ($heads as $k => $item) {
                        $sheet->stdOutSheetColumn('String', $i, $k + 1, $item);
                    }
                    $sheet->stdOutSheetRowEnd();

                    $i++;

                    foreach ($orders as $_order_key => $_order) {
                        $j = 1;
                        $sheet->stdOutSheetRowStart($i);

                        $dealer_id = $_order['d_id'];
                        $store_code = $_order['store_code'];
                        $title = $_order['title'];
                        $sn = $_order['sn'];
                        $invoice_number = isset($_order['invoice_number']) ? $_order['invoice_number'] : '';
                        $invoice_time = isset($_order['invoice_time']) ? date('d/m/Y', strtotime($_order['invoice_time'])) : '';
                        $joint_total = $_order['joint_total'];
                        $joint_by_order = $_order['joint_by_order'];
                        $joint_remain = $_order['joint_remain'];
                        $type = $_order['type'] == 1 ? 'Bvg cấn trừ' : 'Bvg chuyển trả';
                        $joint_total_after = $_order['joint_total_after'] ? $_order['joint_total_after'] : 0;
                        $warehouse_name = isset($warehouse_cache[$_order['warehouse_id']]) ? $warehouse_cache[$_order['warehouse_id']] : '';

                        $sheet->stdOutSheetColumn('String', $i, $j++, $dealer_id);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $store_code);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $title);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $sn);
                        $sheet->stdOutSheetColumn('String' ,$i, $j++, $warehouse_name);
                        $sheet->stdOutSheetColumn('String' ,$i, $j++ , $product_name);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_number);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_time);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $joint_total);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $joint_by_order);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $joint_remain);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $joint_total_after);
                        $sheet->stdOutSheetColumn('String', $i, $j++, $type);

                        $sheet->stdOutSheetRowEnd();
                        $i++;
                    } // end dealer/order

                    $sheet->stdOutSheetRowStart($i);
                    $sheet->stdOutSheetColumn('String', $i++, 1, '');
                    $sheet->stdOutSheetRowEnd();

                    unset($select);
                    unset($orders);
                } // end product

                $sheet->stdOutSheetRowStart($i);
                $sheet->stdOutSheetColumn('String', $i++, 1, '');
                $sheet->stdOutSheetRowEnd();

            } // end joint

            $sheet->stdOutSheetEnd();
            $xml->stdOutEnd();
            exit;
        }

    public function bvgCreateAction()
    {

        $flashMessenger = $this->_helper->flashMessenger;
        $sn = $this->getRequest()->getParam('sn');
        $QStaff = new Application_Model_Staff();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $back_url = $this->getRequest()->getParam('back_url');



        $QMarketProduct = new Application_Model_MarketProduct();
        $MaketBvgKa = new Application_Model_MarketBvgKa();
        $QIMEI = new Application_Model_BvgImei();
        $QBVG = new Application_Model_BvgProduct();
        $QMarketDeduction = new Application_Model_MarketDeduction();
        $QGood = new Application_Model_Good();
        $QBvgAccessories = new Application_Model_BvgAccessories();


        $QJoint = new Application_Model_JointCircular();

        $where = $QJoint->getAdapter()->quoteInto('type in (?)', array(1,3,5));
        $result_joint = $QJoint->fetchAll($where);
        $joint = $joint_accessories =  array();

        foreach ($result_joint as $item)
        {

            if($item['type'] == JOINT_TYPE_BVG_ACCESSORIES)
            {
                $joint_accessories[$item->id] = $item->name;
            }
            elseif(in_array($item['type'] , array(My_Discount::DISCOUNT_TYPE_BVG_KA , My_Discount::DISCOUNT_TYPE_BVG)))
            {
                $joint[$item->id] = $item->name;
            }
        }

        if(isset($sn) and $sn)
        {

            $where = array();
            $where[] = $MaketBvgKa->getAdapter()->quoteInto('sn = ? ' , $sn);
            $result  = $MaketBvgKa->fetchRow($where);




            if(empty($result))
            {
                $flashMessenger->setNamespace('error')->addMessage("Invalid SN!");
                $this->_redirect(HOST.'bvg/list-payment');
            }

            if(!empty($result) and $result['approved_at'])
            {
                $flashMessenger->setNamespace('error')->addMessage("This request can not update!");
                $this->_redirect(HOST.'bvg/list-payment');
            }

            $this->view->market = $result;

            $QMarket_product = new Application_Model_MarketProduct();
            $QIMEI = new Application_Model_BvgImei();
            $QBVG = new Application_Model_BvgProduct();
            $QMarketDeduction = new Application_Model_MarketDeduction();

            $where = array();

            $bvg_accessories =  $data = $imeis =  array();

            $where[] = $QMarket_product->getAdapter()->quoteInto('sn = ?', $sn);
            $sales   = $QMarket_product->fetchAll($where);





            foreach ($sales as $k => $sale) {

                if($sale['type'] and in_array($sale['type'] , array(My_Discount::BVG , My_Discount::BVGPayment)))
                {
                    $where = $QBVG->getAdapter()->quoteInto('joint_id = ?', $sale->joint);
                    $bvg   = $QBVG->fetchAll($where, 'good_id');

                    $QGood = new Application_Model_Good();
                    $where = $QGood->getAdapter()->quoteInto('id in (?) ', $bvg->toArray());
                    $goods = $QGood->fetchAll($where, 'name');
                    $where = $QIMEI->getAdapter()->quoteInto('bvg_market_product_id = ? ', $sale->
                    id);
                    $imei = $QIMEI->fetchAll($where);
                    $data[$k]['goods'] = $goods;
                    $data[$k]['sale']  = $sale;
                    $imeis[$k]         = $imei;
                }

                elseif($sale['type'] and $sale['type'] == My_Discount::BVGAccessories)
                {
                    $params = array(
                        'joint_id' => $sale['joint'],
                        'good_id'  => $sale['good_id'],
                        'bvg_market_product_id' => $sale['id']
                    );


                    $limit = null;
                    $total = 0;
                    $page  = 1;

                    $bvg_accessories[$k] = $QBvgAccessories->fetchPagination($page,$limit,$total,$params);

                }

            }

            $where = array();
            $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
            $sales = $QMarketDeduction->fetchAll($where);

            $this->view->bvg_accessories = $bvg_accessories ? $bvg_accessories : null;
            $this->view->sales_discount = $sales ? $sales : null;
            $this->view->sales_product = $data ? $data : null;
            $this->view->imei = $imeis ? $imeis : null;
        }



        $this->view->joint = $joint;
        $this->view->joint_accessories = $joint_accessories;

        $whereGood = $QGood->getAdapter()->quoteInto('cat_id = ?' , PHONE_CAT_ID);
        $good = $QGood->fetchAll($whereGood);
        $list_good = $QGood->get_cache();
        foreach($good as $k=>$v)
        {
            $list_good[$v['id']] = $v['name'];
        }

        $this->view->goods = $list_good;
        $this->view->goods_cache = $QGood->get_cache();

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->get_all();

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->fetchAll(null, 'name');

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

    }

    public function bvgSaveAction()
    {
        $sn                   = $this->getRequest()->getParam('sn');
        $ids_bvg              = $this->getRequest()->getParam('ids_bvg');
        $ids_bvg_accessories  = $this->getRequest()->getParam('ids_bvg_accessories');
        $good_ids_bvg         = $this->getRequest()->getParam('good_id_bvg');
        $nums_bvg             = $this->getRequest()->getParam('num_bvg');
        $prices_bvg           = $this->getRequest()->getParam('price_bvg');
        $totals_bvg           = $this->getRequest()->getParam('total_bvg');
        $joint                = $this->getRequest()->getParam('joint');
        $joint_accessories    = $this->getRequest()->getParam('joint_accessories');
        //$bvg_imei             = $this->getRequest()->getParam('ids_bvg_imei');
        $userStorage          = Zend_Auth::getInstance()->getStorage()->read();
        $distributor_id       = $this->getRequest()->getParam('distributor_id');
        $warehouse_id         = $this->getRequest()->getParam('warehouse_id');
        $note                 = $this->getRequest()->getParam('note');
        $date                 = $this->getRequest()->getParam('date');


        
       

        try
        {
            $QMarketProduct  = new Application_Model_MarketProduct();
            $QBVGIMEI        = new Application_Model_BvgImei();
            $QBvgAccessories = new Application_Model_BvgAccessories();
            $QLog            = new Application_Model_Log();
            $QMarketBvgKa    = new Application_Model_MarketBvgKa();
            $db = Zend_Registry::get('db');
            $db->beginTransaction();
            $flashMessenger = $this->_helper->flashMessenger;
            set_time_limit(0);
            //get old ids
            $old_ids = $error_ids = null;
            if ($sn)
            {
                $where = $QMarketProduct->getAdapter()->quoteInto('sn = ?', $sn);
                $old_sales = $QMarketProduct->fetchAll($where);

                if ($old_sales)
                {
                    foreach ($old_sales as $sale)
                    {
                        $old_ids[] = $sale->id;
                    }
                }
            }

            if(isset($ids_bvg_accessories) and is_array($ids_bvg_accessories))
            {
                if(empty($sn))
                {
                    $sn_ka = My_Sale_Order::generateSn();

                }

                foreach($ids_bvg_accessories as $k => $id)
                {
                    if (isset($good_ids_bvg[$k]) and $good_ids_bvg[$k] and isset($nums_bvg[$k]) and
                        $nums_bvg[$k] and isset($totals_bvg[$k]) and
                        $totals_bvg[$k]) {

                        $data_bvg_ka  = array(
                            'num'   => intval( $nums_bvg[$k] ),
                            'total' => My_Number::floatval( $totals_bvg[$k] ),
                            'd_id'  => $distributor_id,
                            'note'  => $note,
                            'warehouse_id' => intval($warehouse_id),
                            'joint' => intval( $joint_accessories[$k]),
                            'date'  => date('Y-m-d h:i:s'),
                            'good_id' => intval( $good_ids_bvg[$k] ),
                        );



                        if(isset($sn) and $sn)
                        {
                            $where     = array();
                            $where[]   = $QMarketBvgKa->getAdapter()->quoteInto('sn = ?' , $sn);
                            $where[]   = $QMarketBvgKa->getAdapter()->quoteInto('approved_by <> ?'  , null );
                            $market_ka = $QMarketBvgKa->fetchRow($where);


                            $QMarketBvgKa->update($data_bvg_ka, $where);
                            $whereMakretProduct = array();
                            $whereMakretProduct[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?' , $sn);
                            $market_product = $QMarketProduct->fetchRow($whereMakretProduct);
                            $id_bvg = $market_product['id'];
                        }
                        else
                        {
                            $data_bvg_ka['sn'] = $sn_ka;
                            $data_bvg_ka['num'] = intval($nums_bvg);
                            $data_bvg_ka['total'] = My_Number::floatval( $totals_bvg[$k] );
                            $data_bvg_ka['created_at'] = date('Y-m-d h:i:s');
                            $data_bvg_ka['created_by'] = $userStorage->id;
                            $QMarketBvgKa->insert($data_bvg_ka);
                        }


                        /*Type 3 BVG Phụ kiện*/
                        $data = array(
                            'good_id'      => intval( $good_ids_bvg[$k] ),
                            'num'          => intval( $nums_bvg[$k] ),
                            'price'        => My_Number::floatval( $prices_bvg[$k] ),
                            'total'        => My_Number::floatval( $totals_bvg[$k] ),
                            'd_id'         => intval( $distributor_id ),
                            'warehouse_id' => intval( $warehouse_id ),
                            'joint'        => intval( $joint_accessories[$k]),
                            'type'         => 3,
                        );



                        if (isset($sn) and $sn)
                        { //update
                            $where     = array();
                            $where     = $QMarketProduct->getAdapter()->quoteInto('sn = ?', $sn);
                            $QMarketProduct->update($data, $where);
                        } else
                        {
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $data['user_id']  = $userStorage->id;
                            $data['sn']       = $sn_ka ? $sn_ka : $sn;
                            $id_bvg           = $QMarketProduct->insert($data);
                        }

                        $data = array(
                            'bvg_market_product_id' => $id_bvg
                        );



                        $where = array();

                        $where[]   = $QBvgAccessories->getAdapter()->quoteInto('good_id  = ? ', $good_ids_bvg[$k]);
                        $where[]   = $QBvgAccessories->getAdapter()->quoteInto('joint_id = ? ', $joint_accessories[$k]);
                        $QBvgAccessories->update($data, $where);



                    }
                    else
                    {
                        throw new exception('Information is not fill enough');
                    }
                }

                if ($sn)
                {
                    if ($old_ids)
                    {
                        $newIds = $ids_bvg_accessories ? $ids_bvg_accessories : array();
                        $removed_sales_ids = array_diff($old_ids, $newIds);

                        if ($removed_sales_ids)
                        {
                            $where = $QMarketProduct->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
                            $QMarketProduct->delete($where);

                            $where = $QBvgAccessories->getAdapter()->quoteInto('bvg_market_product_id IN (?)', $removed_sales_ids);
                            $data             = array('bvg_market_product_id' => NULL);
                            $QBvgAccessories->update($data, $where);
                        }
                    }
                }

            }




            if (isset($ids_bvg) and is_array($ids_bvg))
            {

                if (!$sn)
                    $sn_ka = My_Sale_Order::generateSn();


                set_time_limit(0);
                foreach ($ids_bvg as $k => $id)
                {
                    if (isset($good_ids_bvg[$k]) and $good_ids_bvg[$k] and isset($nums_bvg[$k]) and
                        $nums_bvg[$k] and isset($prices_bvg[$k]) and $prices_bvg[$k] and isset($totals_bvg[$k]) and
                        $totals_bvg[$k])
                    {
                        $data_bvg_ka  = array(
                            'num'   => intval( $nums_bvg[$k] ),
                            'total' => My_Number::floatval( $totals_bvg[$k] ),
                            'd_id'  => $distributor_id,
                            'note'  => $note,
                            'warehouse_id' => intval($warehouse_id),
                            'joint' => intval( $joint[$k] ),
                            'date'  => $date,
                            'good_id' => intval( $good_ids_bvg[$k] ),
                        );

                       

                        if(isset($sn) and $sn)
                        {
                            $where = array();
                            $where[] = $QMarketBvgKa->getAdapter()->quoteInto('sn = ?' , $sn);
                            $where[] = $QMarketBvgKa->getAdapter()->quoteInto('approved_by <> ?'  , null );
                            $market_ka = $QMarketBvgKa->fetchRow($where);

                            $QMarketBvgKa->update($data_bvg_ka, $where);
                            $whereMakretProduct = array();
                            $whereMakretProduct[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?' , $sn);
                            $market_product = $QMarketProduct->fetchRow($whereMakretProduct);
                            $id_bvg = $market_product['id'];

                        }

                        else
                        {
                            $data_bvg_ka['sn'] = $sn_ka;
                            $data_bvg_ka['num'] = intval($nums_bvg);
                            $data_bvg_ka['total'] = My_Number::floatval( $totals_bvg[$k] );
                            $data_bvg_ka['created_at'] = date('Y-m-d h:i:s');
                            $data_bvg_ka['created_by'] = $userStorage->id;
                            $QMarketBvgKa->insert($data_bvg_ka);
                        }




                        /*Type 2 BVG chuyen tra*/
                        $data = array(
                            'good_id'      => intval( $good_ids_bvg[$k] ),
                            'num'          => intval( $nums_bvg[$k] ),
                            'price'        => My_Number::floatval( $prices_bvg[$k] ),
                            'total'        => My_Number::floatval( $totals_bvg[$k] ),
                            'd_id'         => intval( $distributor_id ),
                            'warehouse_id' => intval( $warehouse_id ),
                            'joint'        => intval( $joint[$k] ),
                            'type'         => 2,
                        );



                        if (isset($sn) and $sn)
                        { //update
                            $where     = $QMarketProduct->getAdapter()->quoteInto('id = ?', $id);
                            $QMarketProduct->update($data, $where);
                            $list_imei = explode(',', $bvg_imei[$k]);
                            $data      = array('bvg_market_product_id' => $id);
                            $where     = array();
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('good_id = ? ', intval( $good_ids_bvg[$k] ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('d_id = ? ', intval( $distributor_id ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('joint_circular_id = ? ', intval( $joint[$k] ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                            $QBVGIMEI->update($data, $where);
                        } else
                        {
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $data['user_id']  = $userStorage->id;
                            $data['sn']       = $sn_ka ? $sn_ka : $sn;
                            $id_bvg           = $QMarketProduct->insert($data);
                            $list_imei        = explode(',', $bvg_imei[$k]);
                            $data             = array('bvg_market_product_id' => $id_bvg);
                            $where            = array();
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('good_id = ? ', intval( $good_ids_bvg[$k] ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('d_id = ? ', intval( $distributor_id ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('joint_circular_id = ? ', intval( $joint[$k] ));
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                            $QBVGIMEI->update($data, $where);
                        }
                    }
                }



                //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info = 'BVG : ';
                $info .= $sn;

                $QLog->insert(array(
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ));

                if ($sn)
                {
                    if ($old_ids)
                    {
                        $newIds = $ids_bvg ? $ids_bvg : array();
                        $removed_sales_ids = array_diff($old_ids, $newIds);

                        if ($removed_sales_ids)
                        {
                            $where = $QMarketProduct->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
                            $QMarketProduct->delete($where);

                            $where = $QBVGIMEI->getAdapter()->quoteInto('bvg_market_product_id IN (?)', $removed_sales_ids);
                            $data             = array('bvg_market_product_id' => NULL);
                            $QBVGIMEI->update($data, $where);
                        }
                    }
                }

            }




            $db->commit();
            $flashMessenger->setNamespace('success')->addMessage("Done!");

        }
        catch (exception $e)
        {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }

        $this->_redirect(HOST . 'bvg/list-payment');
    }

    public function invoiceAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', '200M');
        $this->_helper->layout->disableLayout();
        $sn = $this->getRequest()->getParam('sn');
        $QMarket = new Application_Model_Market();
        $QMarketProduct = new Application_Model_MarketProduct();
        $MarketBVGKA  = new Application_Model_MarketBvgKa();
        $QGood = new Application_Model_Good();
        $QJoint = new Application_Model_JointCircular();
        $QDistributor = new Application_Model_Distributor();
        $total_price = $QMarket->getPrice($sn);
        $total_price_bvg = $QMarketProduct->getPrice($sn);
        $total = intval($total_price) - intval($total_price_bvg);
        $type = $this->getRequest()->getParam('type');
        $QInvoiceNumber = new Application_Model_InvoiceNumber();
        $QMarketDeduction = new Application_Model_MarketDeduction();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $this->view->total = $total;

      

        $where =array();
        $where[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?' , $sn);
        $market  = $QMarketProduct->fetchRow($where);

      

        $warehouse_id = $market['warehouse_id'];
        $distributor = $QDistributor->find($market['d_id']);
        $distributor = $distributor->current();

        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->find($warehouse_id);
        $warehouse  = $warehouses->current();
        $this->view->warehouse = $warehouse;

        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $invoice_prefix = $QInvoicePrefix->fetchAll();



        $mk_bvg_joint = array();
        $params = array(
            'sn' => $sn,
            'isbacks' => 0,
            'group_good' => 1,
        );
        // My_Image_Barcode::renderNoCode($sn);
        $params['group_sn'] = 1;

        $tmp = $QGood->fetchAll();
        $all_goods = array();
        foreach ($tmp as $k => $v) {
            $all_goods[$v['id']] = $v;
        }
        $this->view->all_goods = $all_goods;
        $this->view->goods_cached = $QGood->get_cache();

        $tmp_joint = $QJoint->fetchAll();

        $joint = array();
        foreach ($tmp_joint as $k => $v) {
            $joint[$v['id']] = $v['name'];
        }



        foreach ($joint as $k => $v) {
            $params['joint'] = $k;
            $mk_bvg_joint[$k] = $QMarketProduct->fetchPagination(1, null, $total2, $params);
            if(isset( $mk_bvg_joint[$k]))
            {
                break;
            }
        }

        //set invoice time
        if (!$market['invoice_time']) {
            $data = array('invoice_time' => date('Y-m-d H:i:s'));
            $where = $QMarketProduct->getAdapter()->quoteInto('sn = ?', $sn);
            $QMarketProduct->update($data, $where);
        }

        //in don hang bao ve gia va bang ke hang hoa
        if ($type == 'bkhh') {
            $total_price = $QMarket->getPrice($sn);

            $mk_bvg_joint = array();
            $params = array(
                'sn' => $sn,
                'isbacks' => 0,
                'group_good' => 1,
            );
            $params['group_sn'] = 1;
            $QJoint = new Application_Model_JointCircular();

            $where = $QJoint->getAdapter()->quoteInto('type in (?)', array(1,3,5));
            $result_joint = $QJoint->fetchAll($where);
            $joint = array();

            foreach ($result_joint as $item)
            {
                $joint[$item->id] = $item->name;
            }
            foreach ($joint as $k => $v) {
                $params['joint'] = $k;
                $mk_bvg_joint[$k] = $QMarketProduct->fetchPagination(1, null, $total2, $params);
            }

            $type_discount = $QMarketProduct->getDiscount($sn);
            $this->view->type_discount = $type_discount;
            //TH chiet khau

            switch($type_discount)
            {
                //chiet khau
                case 2 :
                {
                    $whereMarketDeduction = array();
                    $whereMarketDeduction[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
                    $market_product = $QMarketDeduction->fetchRow($whereMarketDeduction);
                    if (!$market_product['invoice_number']) {
                        $invoice_number = $QInvoiceNumber->getLastId($warehouse_id);
                    } else {
                        $invoice_number = $market_product['invoice_number'];
                    }

                    $this->view->bvg = 1;
                    $this->view->joint = $joint;
                    $this->view->total = $QMarketProduct->getPrice($sn);
                }
                    break;


                default :
                    {
                        $whereMarketProduct = array();
                        $whereMarketProduct[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?', $sn);
                        $market_product = $QMarketProduct->fetchRow($whereMarketProduct);
                        if (!$market_product['invoice_number']) {
                            $invoice_number = $QInvoiceNumber->getLastId($warehouse_id);
                        } else {
                            $invoice_number = $market_product['invoice_number'];
                        }
                        if($market_product['type'] == My_Discount::BVGAccessories)
                        {
                            $this->view->accessories = 1;
                        }
                        $this->view->bvg = 1;
                        $total_price_bvg = $QMarketProduct->getPrice($sn);
                        $total = intval($total_price) - intval($total_price_bvg);
                        $this->view->total = $total;
                        $this->view->joint = $joint;
                    }
                    break;

            }
        }



        $this->view->invoice_number = $invoice_number;
        $this->view->joint = $joint;
        $this->view->invoice_prefix = INVOICE_OPPO_SIGN;
        $this->view->warehouse_id = $warehouse_id;
        $this->view->distributor = $distributor;
        $this->view->sn = $market['sn'];
        $this->view->market = $market;

        if(isset($mk_bvg_joint) and $mk_bvg_joint)
            $this->view->market_product = $mk_bvg_joint;

        switch($warehouse_id)
        {


            
            default:
            {
                    $whereInvoicePrefix   = array();
                    $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('warehouse_id = ?' , $warehouse_id);
                    $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);
                    $invoice_prefix       = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

                    $this->view->invoice_prefix = $invoice_prefix;
                    $this->_helper->viewRenderer('warehouse/invoice-laser', null, true);
            }
               
                break;
        }

    }

    public function listGoodAction()
    {

        $this->_helper->layout->disableLayout();
        $sn = $this->getRequest()->getParam('sn');
        $QGood = new Application_Model_Good();
        $QMarket = new Application_Model_Market();
        $QMarketProduct = new Application_Model_MarketProduct();
        $QImei = new Application_Model_BvgImei();
        $QJoint = new Application_Model_JointCircular();
        $QDistributor = new Application_Model_Distributor();
        $QBvgAccessories = new Application_Model_BvgAccessories();
        $joint = array();
        $tmp_joint = $QJoint->fetchAll();
        $where = array();
        $where[] = $QMarketProduct->getAdapter()->quoteInto('sn = ?' , $sn);
        $market  = $QMarketProduct->fetchRow($where);

        $joint = array();
        foreach ($tmp_joint as $k => $v) {
            $joint[$v['id']] = $v['name'];
        }
        $params = array();
        $params['sn'] = $market['sn'];
        $total2 = 0;
        $mk_bvg_joint = array();
        $params['group_sn'] = 1;
        foreach ($joint as $k => $v) {
            $params['joint'] = $k;
            $mk_bvg_joint[$k] = $QMarketProduct->fetchPagination(1, null, $total2, $params);
        }



        unset($params['group_sn']);
        unset($params['sort']);
        $invoice_time = '';
        $imeis = array();
        foreach ($mk_bvg_joint as $k => $bvg) {

            if(isset($bvg['type']) and in_array($bvg['type'] , array(My_Discount::BVGPayment, My_Discount::BVG)))
            {
                foreach ($bvg as $k => $v) {
                    $where = array();
                    $where[] = $QImei->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                    $where[] = $QImei->getAdapter()->quoteInto('bvg_market_product_id = ?', $v['id']);

                    $params = array(
                        'good_id'               => $v['good_id'],
                        'bvg_market_product_id' => $v['id'],
                        'print'                 => 1
                    );
                    $total = 0;

                    $imeis[$v['id']] = $QImei->fetchPagination(1, null , $total, $params);
                    $invoice_time = $v['invoice_time'];
                    $this->_helper->viewRenderer('warehouse/list-good', null, true);
                }
            }
            elseif(isset($bvg['type']) and $bvg['type'] ==  My_Discount::BVGAccessories)
            {
                foreach ($bvg as $k => $v) {

                    $where = array();
                    $where[] = $QBvgAccessories->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                    $where[] = $QBvgAccessories->getAdapter()->quoteInto('bvg_market_product_id = ?', $v['id']);

                    $params = array(
                        'good_id'               => $v['good_id'],
                        'bvg_market_product_id' => $v['id']
                    );

                    $total = 0;


                    $imeis[$v['id']] = $QBvgAccessories->fetchPagination(1, null , $total, $params);
                    $invoice_time    = $v['invoice_time'];
                    $this->_helper->viewRenderer('warehouse/partials/invoice/list-good-accessories.pthml', null, true);
                }
            }


        }

        unset($params['sn']);
        $this->view->imeis = $imeis;


        $tmp = $QGood->fetchAll();
        $all_goods = array();
        foreach ($tmp as $k => $v) {
            $all_goods[$v['id']] = $v;
        }

        $add_time = $market['add_time'];
        if (!$market['invoice_time']) {
            $data = array('invoice_time' => date('Y-m-d H:i:s'));
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
            $QMarket->update($data, $where);
        }

        $warehouse_id = $market['warehouse_id'];

        $distributor = $QDistributor->find($market['d_id']);
        $distributor = $distributor->current();

        if ($distributor) {
            $this->view->distributor = $distributor;
        }
        $this->view->all_goods = $all_goods;
        $this->view->goods_cached = $QGood->get_cache();
        $this->view->invoice_time = $invoice_time;
        $this->view->add_time = strtotime($add_time);
        $this->view->warehouse = $warehouse_id;
        $this->view->sn = $sn;
        $this->view->invoice = $market['invoice_number'];
        $this->view->invoice_time = $market['invoice_time'];

        $this->view->market_product = $mk_bvg_joint;
        $this->view->joint = $joint;

        $this->_helper->viewRenderer('warehouse/list-good', null, true);

    }

    public function confirmBvgAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn = $this->getRequest()->getParam('sn');
        $QStaff = new Application_Model_Staff();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $back_url = $this->getRequest()->getParam('back_url');

        set_time_limit(0);
        ini_set('memory_limit', '200M');

        $QMarketProduct = new Application_Model_MarketProduct();
        $QMaketBvgKa = new Application_Model_MarketBvgKa();
        $QIMEI = new Application_Model_BvgImei();
        $QBVG = new Application_Model_BvgProduct();
        $QMarketDeduction = new Application_Model_MarketDeduction();
        $QGood = new Application_Model_Good();

        $QJoint = new Application_Model_JointCircular();

        $where = $QJoint->getAdapter()->quoteInto('type  = ?', 3);
        $result_joint = $QJoint->fetchAll($where);
        $joint = array();

        foreach ($result_joint as $item)
        {
            $joint[$item->id] = $item->name;
        }

        if(isset($sn) and $sn)
        {
            $where = array();
            $data = array();
            $imeis = array();


            $where[] = $QMaketBvgKa->getAdapter()->quoteInto('sn = ?', $sn);
            $sales   = $QMaketBvgKa->fetchRow($where);
            $market_product = $QMarketProduct->fetchRow($where);

            $where = $QIMEI->getAdapter()->quoteInto('bvg_market_product_id = ? ', $market_product['id']);
            $imei  = $QIMEI->fetchAll($where);
            $this->view->market = $sales;
            $this->view->imei = $imei;

        }

        if($this->getRequest()->isPost()) {

                try {

                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();

                    $where = array();

                    $where[] = $QMaketBvgKa->getAdapter()->quoteInto('sn = ? ' , $sn);
                    $where[] = $QMaketBvgKa->getAdapter()->quoteInto('approved_at is not null' , null);
                    $result  = $QMaketBvgKa->fetchRow($where);

                    if(!empty($result))
                    {
                        throw new Exception("This request was approved");
                    }

                    $date = date('Y-m-d h:i:s');



                    $data = array(
                       'approved_at' => $date,
                       'approved_by' => $userStorage->id,
                    );

                    $where = array();
                    $where[] = $QMaketBvgKa->getAdapter()->quoteInto('sn = ?' , $sn);
                    $where[] = $QMaketBvgKa->getAdapter()->quoteInto('approved_at is null', null);
                    $QMaketBvgKa->update($data, $where);

                    //Update market deduction
                    $QMarketDeduction = new Application_Model_MarketDeduction();
                    $QBvgAccessories  = new Application_Model_BvgAccessories();
                    $QBVG = new Application_Model_BvgImei();
                    $QMarketBVG = new Application_Model_MarketProduct();
                    $whereBVG   = array();
                    $whereBVG[] = $QMarketBVG->getAdapter()->quoteInto('sn = ?', $sn);
                    $listBVG    = $QMarketBVG->fetchAll($whereBVG, 'id');



                    if (isset($listBVG) and $listBVG) {
                        $list_imei = array();

                        foreach ($listBVG as $k => $v)

                            if($v['type'] and in_array($v['type'] , array(My_Discount::BVGPayment, My_Discount::BVG)))
                            {
                                $list_imei[] = $v['id'];

                                if (count($list_imei)) {
                                    $whereIMEI = $QBVG->getAdapter()->quoteInto('bvg_market_product_id in (?)', $list_imei);

                                    $listImei = $QBVG->fetchAll($whereIMEI, 'id');

                                    if (count($listImei))
                                        $QBVG->update(array('bvg_payment_confirmed_at' => $date), $whereIMEI);
                                }

                                $whereMarketDeduction = array();
                                $whereMarketDeduction[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
                                $whereMarketDeduction[] = $QMarketDeduction->getAdapter()->quoteInto('d_id = ?',
                                    $sales['d_id']);
                                $QMarketDeduction->update(array('payment_confirmed_at' => $date), $whereMarketDeduction);
                            }
                        elseif($v['type'] and $v['type'] == My_Discount::BVGAccessories)
                        {
                            $list_bvg_accessories[] = $v['id'];
                            if (count($list_bvg_accessories)) {

                                $where_accessories = $QBvgAccessories->getAdapter()->quoteInto('bvg_market_product_id in (?)', $list_bvg_accessories);

                                $listAccessories = $QBvgAccessories->fetchAll($where_accessories, 'id');

                                if (count($listAccessories))
                                    $QBvgAccessories->update(array('bvg_payment_confirmed_at' => $date), $where_accessories);
                            }
                            else{
                                throw new exception('Error when loading');
                            }
                        }

                    }

                    $db->commit();
                    $flashMessenger->setNamespace('success')->addMessage('Done!');
                    $this->_redirect('/bvg/list-payment');

                }
                catch(exception $e)
                {
                    $db->rollback();
                    $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!' .
                        $e->getMessage());
                    $this->_redirect('/bvg/list-payment');
                }

            }




        $this->view->joint = $joint;
        $this->view->goods = $QGood->get_cache();

        $discount_bvg = $QMarketProduct->getPriceDiscount(array('d_id' => $sales['d_id']));
        $this->view->discount_bvg = $discount_bvg;

        $QDistributor = new Application_Model_Distributor();
        $distributorRowset = $QDistributor->find($sales['d_id']);
        $distributor = $distributorRowset->current();

        $QJoint = new Application_Model_JointCircular();
        $jointRowset  = $QJoint->find($sales['joint']);
        $joint = $jointRowset->current();

        $this->view->joint  = $joint['name'];
        $this->view->distributor = $distributor['title'];

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->get_cache();

        $detailBVG = $QMarketProduct->getDetailBVG($sn);
        $this->view->detailBVG = $detailBVG;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;
    }

    public function listPaymentAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $messages_success = $flashMessenger->setNamespace('success')->getMessages();

        $imei_sn = $this->getRequest()->getParam('imei_sn');
        $sales_sn = $this->getRequest()->getParam('sales_sn');
        $joint_circular_id = $this->getRequest()->getParam('joint_circular_id');
        $good_id = $this->getRequest()->getParam('good_id');
        $d_id = $this->getRequest()->getParam('d_id');
        $status = $this->getRequest()->getParam('status');
        $approve = $this->getRequest()->getParam('approve');

        $page = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $sort = $this->getRequest()->getParam('sort', '');
        $desc = $this->getRequest()->getParam('desc', 1);
        $export = $this->getRequest()->getParam('export');
        $total = 0;

        $params = array(
            'imei_sn' => $imei_sn,
            'sales_sn' => $sales_sn,
            'joint_circular_id' => $joint_circular_id,
            'good_id' => $good_id,
            'd_id' => $d_id,
            'status' => $status,
            'sort' => $sort,
            'desc' => $desc,



        );
        $params['group_sn'] = true;

        $params['get_fields'] = array(
            'sn',
            'd_id',
            'warehouse_id',
            'add_time',
        );

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributorsCached = $QDistributor->get_cache();

        $QGood = new Application_Model_Good();
        $this->view->goodsCached = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->goodColorsCached = $QGoodColor->get_cache();

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouse = $QWarehouse->get_cache();

        $QJointCircular = new Application_Model_JointCircular();

        $joint_list = array();
        $joint = $QJointCircular->fetchAll(null, 'name');


        foreach ($joint as $k => $v)
            {
                $joint_list[$v['id']] = $v['name'];
            }


        $this->view->jointCircularCached = $joint_list;
        
        $QMarketBVGKa = new Application_Model_MarketBvgKa();

        $QDistributor = new Application_Model_Distributor();

        $this->view->distributors = $QDistributor->get_cache();

        $list = $QMarketBVGKa->fetchPagination($page, $limit, $total, $params);

        if (isset($export) && $export) {
            set_time_limit(0);
            ini_set('memory_limit', '-1');
            error_reporting(0);
            ini_set('display_error', 0);
            $this->_export_bvg_list_payment($params);
            exit;
        }

        $this->view->list               = $list;
        $this->view->limit              = $limit;
        $this->view->total              = $total;
        $this->view->page               = $page;
        $this->view->offset             = $limit*($page-1);
        $this->view->params             = $params;
        $this->view->sort               = $sort;
        $this->view->desc               = $desc;
        $this->view->url                = HOST.'bvg/list-payment'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->messages_success   = $messages_success;
        $this->view->messages           = $messages;
    }


    private function _export_bvg_list_payment($params)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            '#',
            'Dealer ID',
            'Store Code',
            'Dealer Name',
            'TenNguoiMua',
            'MST',
            'SN',
            'Created_at',
            'Number',
            'Total',
            'Invoice Number',
            'Invoice Printed At',
            'GiaTriDonHangHD',
            'DoanhSoChuaThueHD',
            'ThueGTGTHD'

        );
        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();
        $QMarket_invoice_price = new Application_Model_MarketInvoicePriceSn();
        $alpha = 'A';
        $index = 1;

        foreach ($heads as $key)
        {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }

        $index = 2;
        $intCount = 1;
        $QGood = new Application_Model_Good();
        $good_cache = $QGood->get_cache();

        $db =  $db = Zend_Registry::get('db');

        $sql = "SELECT SQL_CALC_FOUND_ROWS DISTINCT
                            p.id,
                            SUM(m.num)AS `total_qty`,
                            SUM(m.total)AS `total_price`,
                            `p`.`warehouse_id`,
                            `p`.*, `d`.`name`,
                            `d`.`title`,
                            `d`.`mst_sn`,
                            `d`.`unames`,
                            `d`.id as distributor_id,
                            `d`.store_code,
                            `m`.`invoice_number`,
                            `m`.`invoice_time`,
                            `m`.`add_time`
                        FROM
                            `market_bvg_ka` AS `p`
                        LEFT JOIN `distributor` AS `d` ON d.id = p.d_id
                        INNER JOIN `market_product` as m ON p.`sn` = m.`sn`
                        GROUP BY
                            `p`.`sn`
                        ORDER BY
                            `created_at` DESC";

        $data = $db->fetchAll($sql);


        try
        {
            if ($data)
                foreach ($data as $item)
                {

                    $dealer_id      = $item['distributor_id'];
                    $store_code     = $item['store_code'];
                    $title          = $item['title'];
                    $total          = $item['total_qty'];
                    $total_price    = $item['total_price'];
                    $sn = isset($item['sn']) ? $item['sn'] : 'x';
                    $invoice_number = isset($item['invoice_number']) ? $item['invoice_number'] : 'x';
                    $invoice_time = isset($item['invoice_time']) ? $item['invoice_time'] : '';
                    $created_at = isset($item['add_time']) ? $item['add_time'] : '';

                    $where = array();
                    $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('sn = ?', $item['sn']);
                    $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('invoice_number = ?',
                        $item['invoice_number']);
                    $invoice_price = $QMarket_invoice_price->fetchRow($where);



                    $alpha = 'A';
                    $sheet->setCellValue($alpha++ . $index, $intCount++);
                    $sheet->setCellValue($alpha++ . $index, $dealer_id);
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($store_code,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index, $title);
                    $sheet->setCellValue($alpha++ . $index, trim($item['unames'] ? $item['unames'] : ''));
                    $sheet->setCellValue($alpha++ . $index, trim($item['mst_sn'] ? $item['mst_sn'] : ''));
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($sn,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index , $created_at);
                    $sheet->setCellValue($alpha++ . $index, $total);
                    $sheet->setCellValue($alpha++ . $index, $total_price);
                    $sheet->setCellValue($alpha++ . $index, $invoice_number);
                    $sheet->setCellValue($alpha++ . $index , $invoice_time);
                    $sheet->setCellValue($alpha++ . $index , number_format($invoice_price['total_invoice_after_vat']));
                    $sheet->setCellValue($alpha++ . $index , number_format($invoice_price['total_invoice_price']));
                    $sheet->setCellValue($alpha++ . $index , number_format($invoice_price['total_invoice_vat']));


                    $index++;
                }
        }
        catch (exception $e)
        {
            exit;
        }

        $filename = 'List BVG Payement ' . date('d-m-Y H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }
    /*discount incentive*/
    private function _export_discount_incentive($params)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
        '#',
            'Dealer ID',
            'Store Code',
            'Dealer Name',
            'Total Value',
            'Total Discount',
            'Total_Remain Order',
            'Total Remain',
            'Payment confirmed at',
            'SN'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();
        $alpha = 'A';
        $index = 1;

        foreach ($heads as $key)
        {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }

        $index = 2;
        $intCount = 1;
        $QGood = new Application_Model_Good();
        $good_cache = $QGood->get_cache();
        $db = Zend_Registry::get('db');

        if(isset($params['invoice_from']) and $params['invoice_from'] and isset($params['invoice_to']) and $params['invoice_to'])
        {
            $temp = explode('/' , $params['invoice_from']);
            $from = $temp[2] . '-' . $temp[1] . '-' . $temp[0] . ' 00:00:00';
            $from = trim($from);

            $temp1 = explode('/' , $params['invoice_to']);
            $to    = $temp1[2] . '-' . $temp1[1] . '-' . $temp1[0] . ' 23:59:59';
            $to    = trim($to);
        }

        $discount_joint_incentive = isset($params['joint_circular_id']) ? $params['joint_circular_id'] : '14';

        $sql = "SELECT
                        p.distributor_id,
                        m.payment_confirmed_at,
                        m.sn,
                        d.`store_code`,
                        d.`name`,
                        d.title,
                        get_total_discount_by_joint_before(p.distributor_id,$discount_joint_incentive) AS total,
                        get_total_discount_by_joint(p.distributor_id, $discount_joint_incentive, '2014-01-01' , '$from') AS total_discount,
                        get_total_discount_by_joint_remain(
                            p.distributor_id,
                            $discount_joint_incentive,
                            '$from',
                            '$to'
                        )AS total_remain_order,
                        FLOOR(
                            IF(
                                get_total_discount_by_joint_before(p.distributor_id,$discount_joint_incentive) - get_total_discount_by_joint(p.distributor_id, $discount_joint_incentive ,'2014-01-01' , '$from')= 0,
                                0,
                                FLOOR(
                                    get_total_discount_by_joint_before(p.distributor_id,$discount_joint_incentive) - get_total_discount_by_joint(p.distributor_id, $discount_joint_incentive , '2014-01-01' , '$from')
                                )
                            )
                        )AS total_remain
                    FROM
                        incentive_distributor AS p
                    INNER JOIN distributor AS d ON p.distributor_id = d.id
                    LEFT JOIN market_deduction AS m ON p.distributor_id = m.d_id AND m.`payment_confirmed_at` is not null
                    AND
                     m.joint_circular_id = $discount_joint_incentive
                    WHERE p.joint_id = $discount_joint_incentive
                    GROUP BY
                        p.distributor_id,
                        p.joint_id";

        $data = $db->fetchAll($sql);


        try
        {
            if ($data)
                foreach ($data as $item)
                {

                    $dealer_id      = $item['distributor_id'];
                    $store_code     = $item['store_code'];
                    $title          = $item['title'];
                    $total          = $item['total'];
                    $total_discount = $item['total_discount'];
                    $total_remain_order = $item['total_remain_order'];
                    $total_remain   = intval($total - $total_discount - $total_remain_order);
                    $date = $item['payment_confirmed_at'] ?  date('d-m-Y h:i:s', strtotime($item['payment_confirmed_at'])) : 'x';
                    $sn = isset($item['sn']) ? $item['sn'] : 'x';


                    $alpha = 'A';
                    $sheet->setCellValue($alpha++ . $index, $intCount++);
                    $sheet->setCellValue($alpha++ . $index, $dealer_id);
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($store_code,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index, $title);
                    $sheet->setCellValue($alpha++ . $index, $total);
                    $sheet->setCellValue($alpha++ . $index, $total_discount);
                    $sheet->setCellValue($alpha++ . $index, $total_remain_order);
                    $sheet->setCellValue($alpha++ . $index, $total_remain);
                    $sheet->setCellValue($alpha++ . $index , $date);
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($sn,
                        PHPExcel_Cell_DataType::TYPE_STRING);


                    $index++;
                }
        }
        catch (exception $e)
        {

            exit;
        }

        $filename = 'Discount_incentive ' . date('d-m-Y H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }


   



    private function _export_discount_2($params)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            '#',
            'Dealer ID',
            'Store Code',
            'Dealer Name',
            'Warehouse Name',
            'Total Value',
            'Total Discount',
            'Total_Remain Order',
            'Total Remain',
            'SN',
            'Invoice_number',
            'Invoice Time',
            'Invoice HD Number',
            'Invoice HD Time'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();
        $alpha = 'A';
        $index = 1;


        foreach ($heads as $key)
        {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }

        $index = 2;
        $intCount = 1;
        $QGood = new Application_Model_Good();
        $QMarketDeduction = new Application_Model_MarketDeduction();
        $good_cache = $QGood->get_cache();
        $QWarehouse = new Application_Model_Warehouse();
        $warehouses = $QWarehouse->get_cache();

        $db = Zend_Registry::get('db');

        if(isset($params['invoice_from']) and $params['invoice_from'] and isset($params['invoice_to']) and $params['invoice_to'])
        {
            $temp = explode('/' , $params['invoice_from']);
            $from = $temp[2] . '-' . $temp[1] . '-' . $temp[0] . ' 00:00:00';
            $from = trim($from);

            $temp1 = explode('/' , $params['invoice_to']);
            $to    = $temp1[2] . '-' . $temp1[1] . '-' . $temp1[0] . ' 23:59:59';
            $to    = trim($to);
        }

        $discount_joint = ($params['joint_circular_id']) ? $params['joint_circular_id'] : '46';

        $sql = "SELECT
                    p.d_id,
                    d.`name`,
                    d.`store_code`,
                    d.title,
                    sum(p.total_discount)AS total,
                    get_total_discount_by_joint(p.d_id, $discount_joint , '2014-01-01' , '$from') AS total_discount,
                    get_total_discount_by_joint_remain(p.d_id , $discount_joint , '$from' , '$to') as total_remain_order
                FROM
                    deduction_sales_sn AS p
                INNER JOIN distributor AS d ON p.d_id = d.id
                WHERE p.joint_circular_id = $discount_joint
                GROUP BY
                    p.d_id , p.joint_circular_id";

        $data = $db->fetchAll($sql);

        $sql = "SELECT
                    p.`sn`,
                    p.`d_id`,
                    p.`invoice_number`,
                    p.`invoice_time`,
                    m.`joint_circular_id`,
                    m.`invoice_number` as invoice_number_discount,
                    m.`invoice_time` as invoice_time_discount,
                    p.`warehouse_id`
                FROM
                    market_deduction AS m
                INNER JOIN market AS p ON p.sn = m.sn
                WHERE p.shipping_yes_time is not null
                and p.`pay_time` is not null
                and p.`canceled` = 0
                and m.joint_circular_id = $discount_joint
                ";

        $data_market = $db->fetchAll($sql);

        $_market = array();

        foreach($data_market as $k => $v)
        {
            $_market[$v['d_id']]['sn'] = $v['sn'];
            $_market[$v['d_id']]['invoice_number']            = $v['invoice_number'];
            $_market[$v['d_id']]['invoice_time']              = $v['invoice_time'];
            $_market[$v['d_id']]['invoice_number_discount']   = $v['invoice_number_discount'];
            $_market[$v['d_id']]['invoice_time_discount']     = $v['invoice_time_discount'];
            $_market[$v['d_id']]['warehouse']                 = $v['warehouse_id'];
        }



        try
        {
            if ($data)
                foreach ($data as $item)
                {

                    $dealer_id      = $item['d_id'];
                    $store_code     = $item['store_code'];
                    $title          = $item['title'];
                    $total          = $item['total'];
                    $total_discount = $item['total_discount'];
                    $total_remain   = intval($item['total'] - $item['total_discount'] - $item['total_remain_order']);
                    $total_remain_order = $item['total_remain_order'] ;

                    $sn = $invoice_number = $invoice_time = $invoice_number_discount = $invoice_time_discount =  0;

                    if(isset($_market[$item['d_id']]) and $_market[$item['d_id']])
                    {
                        $warehouse = '';
                        $sn             =  $_market[$item['d_id']]['sn'];
                        $invoice_number =  $_market[$item['d_id']]['invoice_number'];
                        $invoice_time   =  $_market[$item['d_id']]['invoice_time'];
                        $invoice_number_discount =  $_market[$item['d_id']]['invoice_number_discount'] ? $_market[$item['d_id']]['invoice_number_discount'] : 0;
                        $invoice_time_discount   =  $_market[$item['d_id']]['invoice_time_discount'] ? $_market[$item['d_id']]['invoice_time_discount'] : 0;
                        $warehouse      = $_market[$item['d_id']]['warehouse'] ? $_market[$item['d_id']]['warehouse'] : '';
                    }



                    $alpha = 'A';
                    $sheet->setCellValue($alpha++ . $index, $intCount++);
                    $sheet->setCellValue($alpha++ . $index, $dealer_id);
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($store_code,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index, $title);
                    $sheet->setCellValue($alpha++ . $index, isset($warehouse) ? $warehouses[$warehouse] : '');
                    $sheet->setCellValue($alpha++ . $index, $total);
                    $sheet->setCellValue($alpha++ . $index, $total_discount);
                    $sheet->setCellValue($alpha++ . $index, $total_remain_order);
                    $sheet->setCellValue($alpha++ . $index,  $total_remain >= 0 ? $total_remain : 0 );
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($sn ? $sn : 0,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index,  $invoice_number ? $invoice_number : 0);
                    $sheet->setCellValue($alpha++ . $index,  $invoice_time ? date('d-m-Y h:i:s' , strtotime($invoice_time)) : 0);
                    $sheet->setCellValue($alpha++ . $index,  $invoice_number_discount ? $invoice_number_discount : 0);
                    $sheet->setCellValue($alpha++ . $index,  $invoice_time_discount ? date('d-m-Y h:i:s' , strtotime($invoice_time_discount)) : 0);
                    $index++;
                }
        }
        catch (exception $e)
        {
            var_dump($e);
            exit;
        }

        $filename = 'Discount_ck_2% ' . date('d-m-Y H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }

     private function _export_general($params)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            '#',
            'Product ID',
            'Product Name',
            'Price',
            'Total Discount',
            'Total Remain Order',
            'Total Value',
            'Total Remain'
        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();
        $alpha = 'A';
        $index = 1;

        $QJointCircular = new Application_Model_JointCircular();
        $QBvgProduct    = new Application_Model_BvgProduct();
        $QGood          = new Application_Model_Good();
        $good_cache     = $QGood->get_cache();

        foreach ($heads as $key)
        {
            $sheet->setCellValue($alpha . $index, $key);
            $alpha++;
        }

        $index = 2;
        $intCount = 1;
      
        $db = Zend_Registry::get('db');

        $from = DateTime::createFromFormat('d/m/Y', $params['invoice_from'])->format('Y-m-d 00:00:00');
        $to   = DateTime::createFromFormat('d/m/Y', $params['invoice_to'])->format('Y-m-d 23:59:59');
 
        $select = $db->select()
            ->distinct()
            ->from(array('p' => 'bvg_imei'),
                array(
                    'p.price',
                    'p.good_id',
                    'p.d_id',
                    'total' => 'SUM(p.price)',
                    'total_time' => "report_general( p.joint_circular_id, p.good_id , '$from' , '$to')"
                )
            )
            ->join(array('j' => 'joint_circular'), 'j.id = p.joint_circular_id', array('j.name' , 'j.type'))
            ->group('p.joint_circular_id')
            ->group('p.good_id');
        
        $select->where('j.type = ?' , JOINT_TYPE_BVG);

      
        $data = $db->fetchAll($select);


        try
        {
            if ($data)
                foreach ($data as $item)
                {

                    $good_id = isset($item['good_id']) ? $item['good_id'] : '';
                    $good_name =  isset($good_cache[$item['good_id']]) ? $good_cache[$item['good_id']] : '';
                    $price = isset($item['price']) ? $item['price'] : '';

                    $joint_total = $item['total'];
                    $joint_total_time    = trim($item['total_time']);
                    $temp = explode(',',$joint_total_time);
                    $joint_before   = $temp[0] ? $temp[0] : 0;
                    $joint_after    = $temp[1] ? $temp[1] : 0;
                    $joint_total_after  = intval($joint_total - intval($joint_before + $joint_after));



                    $alpha = 'A';
                    $sheet->setCellValue($alpha++ . $index, $intCount++);
                    $sheet->setCellValue($alpha++ . $index, $good_id);
                    $sheet->getCell($alpha++ . $index)->setValueExplicit($good_name,
                        PHPExcel_Cell_DataType::TYPE_STRING);
                    $sheet->setCellValue($alpha++ . $index, $price);
                    $sheet->setCellValue($alpha++ . $index, $joint_total);
                    $sheet->setCellValue($alpha++ . $index, $joint_before);
                    $sheet->setCellValue($alpha++ . $index, $joint_after);
                    $sheet->setCellValue($alpha++ . $index, $joint_total_after);


                    $index++;
                }
        }
        catch (exception $e)
        {

            exit;
        }

        $filename = 'Export_General_' . date('d-m-Y H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }




    private function _export_bvg_all($params)
    {
        require_once 'ExcelWriterXML.php';

        $filename = 'Report_BVG_All ' . date('Ymd_His') . '.xml';

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO');

        $xml->sendHeaders();

        $xml->stdOutStart();
        //////////////////////////////////////////////
        $sheet = $xml->addSheet('BVG');
        ////////////////////////////////////////////
        $heads = array(
            'Dealer ID',
            'Store Code',
            'Dealer Name',
            'Good',
            'Total Discount',
            'Total Remain order',
            'Total Value',
            'Total Remain'
        );

        $sheet->stdOutSheetStart();

        ////////////////////////////////////////////
        $QJointCircular = new Application_Model_JointCircular();
        $QBvgProduct = new Application_Model_BvgProduct();
        $QGood = new Application_Model_Good();
        $good_cache = $QGood->get_cache();

        //get bvg product information
        if(isset($params['good_id']) and $params['good_id'])
        {
            $resultSet = $QBvgProduct->find($params['good_id']);
            $goods = $resultSet->current();
            $params['joint_circular_id'] = $goods['joint_id'];
            $params['good_id'] = $goods['good_id'];
        }



        $db = Zend_Registry::get('db');
        // danh sách tất cả thông tư
        $where = array();

        if (isset($params['joint_circular_id']) && $params['joint_circular_id'])
            $where[] = $QJointCircular->getAdapter()->quoteInto('id = ?', $params['joint_circular_id']);

        $joints = $QJointCircular->fetchAll($where);

        $i = 2;
        // duyệt từng thông tư
        foreach ($joints as $_key => $_joint) {
            // lấy các sản phẩm đc bvg trong mỗi thông tư
            $where = array();
            $where[] = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', intval($_joint['id']));

            if (isset($params['good_id']) && $params['good_id'])
                $where[] = $QBvgProduct->getAdapter()->quoteInto('good_id = ?', $params['good_id']);

            $products = $QBvgProduct->fetchAll($where);

            $sheet->stdOutSheetRowStart($i);
            $sheet->stdOutSheetColumn('String', $i++, 1, '--------------------------------------------');
            $sheet->stdOutSheetRowEnd();

            $sheet->stdOutSheetRowStart($i);
            $sheet->stdOutSheetColumn('String', $i, 1, isset($_joint['name']) ? $_joint['name'] : 'x');
            $sheet->stdOutSheetRowEnd();

            $i++;

            $from = DateTime::createFromFormat('d/m/Y', $params['invoice_from'])->format('Y-m-d 00:00:00');
            $to   = DateTime::createFromFormat('d/m/Y', $params['invoice_to'])->format('Y-m-d 23:59:59');

            // xuất bảng riêng cho từng sản phẩm
            foreach ($products as $_product_key => $_product) {

               $select = $db->select()
                    ->distinct()
                    ->from(array('p' => 'bvg_imei'),
                        array(
                            'p.good_id',
                            'p.d_id',
                            'p.price',
                            'joint_time_before' => "get_total_bvg_by_time(p.d_id, p.joint_circular_id, p.good_id , '2014-01-01', '$from')",
                            'joint_time_after' => "get_total_bvg_by_time(p.d_id, p.joint_circular_id, p.good_id , '$from' , '$to')",
                            'joint_total' => 'get_total_bvg_by_joint(p.d_id, p.joint_circular_id, p.good_id)',
                            'joint_total_after' => 'get_total_bvg_by_joint_after(p.d_id, p.joint_circular_id, p.good_id)'
                        )
                    )
                    ->join(array('j' => 'joint_circular'), 'j.id = p.joint_circular_id', array('j.name'))
                    ->join(array('d' => 'distributor'), 'd.id = p.d_id', array('d.title', 'd.store_code'))
                    ->where('p.good_id = ?', intval($_product['good_id']))
                    ->where('p.joint_circular_id = ?', intval($_joint['id']));


                $orders = $db->fetchAll($select);
                $product_name = isset($good_cache[ $_product['good_id'] ]) ? $good_cache[ $_product['good_id'] ] : 'x';
                $product_name .= sprintf(" (%s)", number_format($_product['price'], 0, '.', ','));
                $sheet->stdOutSheetRowStart($i);
                $sheet->stdOutSheetColumn('String', $i, 1, $product_name);
                $sheet->stdOutSheetRowEnd();
                $i++;
                // tiêu đề
                $sheet->stdOutSheetRowStart($i);
                foreach ($heads as $k => $item) {
                    $sheet->stdOutSheetColumn('String', $i, $k + 1, $item);
                }
                $sheet->stdOutSheetRowEnd();

                $i++;

                foreach ($orders as $_order_key => $_order) {
                    $j = 1;
                    $sheet->stdOutSheetRowStart($i);

                    $good_id        = $_order['good_id'];
                    $dealer_id      = $_order['d_id'];
                    $store_code     = $_order['store_code'];
                    $title          = $_order['title'];
                    $joint_total    = $_order['joint_total'];
                    $joint_before   = $_order['joint_time_before'] ? $_order['joint_time_before'] : 0;
                    $joint_after    = $_order['joint_time_after'] ? $_order['joint_time_after'] : 0;
                    $joint_total_after  = intval($joint_total - ($joint_before + $joint_after));
                   // $joint_total_after  = $_order['joint_total_after'] ? $_order['joint_total_after'] : 0;

                    $sheet->stdOutSheetColumn('String', $i, $j++, $dealer_id);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $store_code);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $title);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $good_cache[$good_id] . ' - ' . number_format($_product['price'], 0, '.', ','));
                    $sheet->stdOutSheetColumn('String', $i, $j++, $joint_total);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $joint_before);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $joint_after);
                    $sheet->stdOutSheetColumn('String', $i, $j++, $joint_total_after);
                    $sheet->stdOutSheetRowEnd();
                    $i++;
                } // end dealer/order

                $sheet->stdOutSheetRowStart($i);
                $sheet->stdOutSheetColumn('String', $i++, 1, '');
                $sheet->stdOutSheetRowEnd();

                unset($select);
                unset($orders);
            } // end product

            $sheet->stdOutSheetRowStart($i);
            $sheet->stdOutSheetColumn('String', $i++, 1, '');
            $sheet->stdOutSheetRowEnd();

        } // end joint

        $sheet->stdOutSheetEnd();
        $xml->stdOutEnd();
        exit;

    }

    /*
     * Export BVG IMEI
    */
    private function _export_bvg_imei($data)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();

        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(~E_ALL);
        ini_set("display_errors", 0);

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $heads = array(
            'IMEI',
            'DISTRIBUTOR_ID',
            'DISTRIBUTOR_NAME',
            'CODE',
            'JOINT',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'PRICE',
            'BVG PAYMENT APPROVED',
            'SN',
            'INVOICE NUMBER',
            'INVOIDE TIME',
            'INVOICE SIGN'
        );


        $alpha = 'A';
        $index = 1;
        foreach ($heads as $k => $item)
            $sheet->setCellValue($alpha++.$index, $item);

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QJoint = new Application_Model_JointCircular();
        $QMarketProduct = new Application_Model_MarketProduct();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $invoice_prefix = $QInvoicePrefix->get_cache();
        $joint = $QJoint->get_cache();

        $goods = $QGood->get_cache();
        $goods_color = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache2();
        $index = 2;
        foreach($data as $k => $item)
        {

            $store_code = '';

            if (isset($distributors) && isset($distributors[$item['d_id']])) {
                $distributor = $distributors[$item['d_id']];
                $store_code  = $distributor['code'] ? $distributor['code'] : '';

            }

            $good_name = '';
            if (isset($goods) && isset($goods[$item['good_id']]))
                $good_name = $goods[$item['good_id']];

             if (isset($goods_color) && isset($goods_color[$item['good_color']]))
                  $good_color = $goods_color[$item['good_color']];

            $Ngay_chung_thuc = isset($item['bvg_payment_confirmed_at']) ? date('d/m/Y', strtotime($item['bvg_payment_confirmed_at'])) : 'x';
            $invoice_number  = isset($item['invoice']) ? $item['invoice'] : '';
            $invoice_time    = isset($item['invoice_time']) ? date('d/m/Y', strtotime($item['invoice_time'])) : 'x';
            $invoice_sign    = isset($item['invoice_sign']) ? $invoice_prefix[$item['invoice_sign']] : 'x';
            $sn              = 'x';
            if(isset($item['sales_sn']) and isset($item['invoice_time']))
                $sn = $item['sales_sn'];

            $alpha = 'A';

            $sheet->getCell($alpha++.$index)->setValueExplicit($item['imei_sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index, $item['d_id'] ? $item['d_id'] : '');
            $sheet->getCell($alpha++.$index)->setValueExplicit($distributor['title'] ? $distributor['title'] : '',
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index, $store_code ? $store_code : '');
            $sheet->setCellValue($alpha++.$index, $joint[$item['joint_circular_id']] ? $joint[$item['joint_circular_id']] : '');
            $sheet->setCellValue($alpha++.$index,  $good_name ? $good_name : '');
            $sheet->setCellValue($alpha++.$index,  $good_color ? $good_color : '');
            $sheet->setCellValue($alpha++.$index,  My_Number::product_price($item['price']));
            $sheet->setCellValue($alpha++.$index,  $Ngay_chung_thuc ? $Ngay_chung_thuc : '');
            $sheet->getCell($alpha++.$index)->setValueExplicit($sn,
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index,  $invoice_number ? $invoice_number : '');
            $sheet->setCellValue($alpha++.$index,  $invoice_time ? $invoice_time : '');
            $sheet->setCellValue($alpha++.$index,  $invoice_sign ? $invoice_sign : '');
            $index++;
        }

        $filename = '_EXPORT_BVG_IMEI_' . date('Y-m-d H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');
        exit;
    }

    private function _export_xml_bvg_follow($params)
    {
        require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();

        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(~E_ALL);
        ini_set("display_errors", 0);

        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();

        $QJointCircular = new Application_Model_JointCircular();
        $QBvgProduct = new Application_Model_BvgProduct();
        $QGood = new Application_Model_Good();
        $good_cache = $QGood->get_cache();
        ////////////////////////////////////////////
        $heads = array(
            "Dealer ID",
            "Store Code",
            "Dealer Name",
            "Order SN",
            "Invoice Number",
            "Invoice Time",
            "After Price Protection",
            "Price Protection Value",
            "Total - Before Price Protection",

        );

        $alpha = 'A';
        $index = 1;
        foreach ($heads as $k => $item)
            $sheet->setCellValue($alpha++.$index, $item);

        $products = $QBvgProduct->fetchAll();

        // lưu thứ tự các sản phẩm
        $product_list = array();

        foreach ($products as $_key => $_product) {
            $sheet->setCellValue($alpha.$index,
                isset($good_cache[ $_product['good_id'] ]) ? ($good_cache[ $_product['good_id'] ] . "\r\n(" . number_format($_product['price'], 0, '.', ',') . ')') : '#');

            $product_list[$_product['joint_id'] . '_' .$_product['good_id']] = $alpha;
            $alpha++;
        }

        $index++;
        ////////////////////////////////////////////
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->distinct()
            ->from(array('m' => 'market'), array(
                'm.d_id',
                'm.sn',
                'm.invoice_number',
                'm.invoice_time',
                'after'  => 'get_value_before_bvg_by_sn(m.sn) - get_bvg_by_sn(m.sn)',
                'bvg'    => 'get_bvg_by_sn(m.sn)',
                'before' => 'get_value_before_bvg_by_sn(m.sn)'
            ))
            ->join(array('p' => 'market_product'), 'p.sn=m.sn', array('invoice_number_bvg' => 'invoice_number'))
            ->join(array('d' => 'distributor'), 'd.id=m.d_id', array('d.title', 'd.store_code'))
            ->where('m.status = ? AND p.status = ?', 1)
            ->where('(p.canceled IS NULL OR p.canceled = ?) AND (m.canceled IS NULL OR m.canceled = ?)', 0)
            ->group('p.sn')
            ->order('p.invoice_time ASC');

        if (isset($params['d_id']) && $params['d_id'])
            $select->where('m.d_id = ?', $params['d_id']);

        if (isset($params['sales_sn']) && $params['sales_sn'])
            $select->where('m.sn = ?', $params['sales_sn']);

        if (isset($params['joint_circular_id']) && $params['joint_circular_id'])
            $select->where('p.joint = ?', $params['joint_circular_id']);

        if (isset($params['good_id']) && $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['invoice_number']) && $params['invoice_number'])
            $select->where('m.invoice_number = ?', $params['invoice_number']);

        if (isset($params['invoice_from']) && DateTime::createFromFormat('d/m/Y', $params['invoice_from']))
            $select->where('m.add_time >= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_from'])->format('Y-m-d 00:00:00'));

        if (isset($params['invoice_to']) && DateTime::createFromFormat('d/m/Y', $params['invoice_to']))
            $select->where('m.add_time <= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_to'])->format('Y-m-d 23:59:59'));

        if (isset($params['warehouse_id']) && $params['warehouse_id'])
            $select->where('m.warehouse_id = ?', $params['warehouse_id']);

        $result = $db->fetchAll($select);

        // lưu dòng của các sn để lúc sau điền mấy cột sản phẩm theo cho dễ
        $sn_list = array();

        foreach ($result as $key => $item) {
            $alpha = 'A';
            $sheet->setCellValue($alpha++.$index, $item['d_id']);
            $sheet->setCellValue($alpha++.$index, $item['store_code']);
            $sheet->setCellValue($alpha++.$index, $item['title']);

            $sheet->getCell($alpha++.$index)->setValueExplicit($item['sn'],
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['invoice_number_bvg'],
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit(date('d/m/Y', strtotime($item['invoice_time'])),
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['after'],
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['bvg'],
                PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['before'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            $sn_list[ $item['sn'] ] = $index;
            $index++;
        }

        $select = $db->select()
            ->distinct()
            ->from(array('m' => 'market'), array(
                'm.sn'
            ))
            ->join(array('p' => 'market_product'), 'p.sn=m.sn', array('p.price', 'p.num', 'p.total'))
            ->join(array('b' => 'bvg_product'), 'p.joint=b.joint_id AND p.good_id=b.good_id', array('b.joint_id', 'b.good_id'))
            ->where('m.status = ? AND p.status = ?', 1)
            ->where('(p.canceled IS NULL OR p.canceled = ?) AND (m.canceled IS NULL OR m.canceled = ?)', 0)
            ->group(array('m.sn', 'b.joint_id', 'b.good_id'))
            ->order('m.invoice_time ASC');

        if (isset($params['d_id']) && $params['d_id'])
            $select->where('m.d_id = ?', $params['d_id']);

        if (isset($params['sales_sn']) && $params['sales_sn'])
            $select->where('m.sn = ?', $params['sales_sn']);

        if (isset($params['joint_circular_id']) && $params['joint_circular_id'])
            $select->where('p.joint = ?', $params['joint_circular_id']);

        if (isset($params['good_id']) && $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['invoice_number']) && $params['invoice_number'])
            $select->where('m.invoice_number = ?', $params['invoice_number']);

        if (isset($params['invoice_from']) && DateTime::createFromFormat('d/m/Y', $params['invoice_from']))
            $select->where('m.invoice_time >= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_from'])->format('Y-m-d 00:00:00'));

        if (isset($params['invoice_to']) && DateTime::createFromFormat('d/m/Y', $params['invoice_to']))
            $select->where('m.invoice_time <= ?', DateTime::createFromFormat('d/m/Y', $params['invoice_to'])->format('Y-m-d 23:59:59'));

        if (isset($params['warehouse_id']) && $params['warehouse_id'])
            $select->where('m.warehouse_id = ?', $params['warehouse_id']);

        $result = $db->fetchAll($select);

        foreach ($result as $key => $item) {
            try {
                $sheet->getCell(
                    $product_list[ $item['joint_id'].'_'.$item['good_id'] ]
                    .$sn_list[ $item['sn'] ]
                )->setValueExplicit(
                    $item['total'],
                    PHPExcel_Cell_DataType::TYPE_STRING
                );
            } catch (Exception $e) {
            }
            
        }

        $filename = 'Report_BVG_follow ' . date('Y-m-d H-i-s');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');
        exit;
    }

    //Tanong Get SalesOrderNoRef 20160313 1155
    //$this->get_credit_note_sn($d_id,$userStorage->id,$joint_circular_id,$imei_sn);
    public function get_credit_note_sn($distributor_id,$user_id,$joint_circular_id,$imei_sn)
    {
    try {
            $db = Zend_Registry::get('db');
            if($imei_sn==''){$imei_sn=0;}
            $stmt = $db->prepare("CALL gen_credit_note_sn_new_protection_price('".$distributor_id."','".$user_id."',".$joint_circular_id.",'".$imei_sn."')");
            $stmt->execute();
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
    }

    //Tanong Get PONo Ref 20160313 1155
    public function getProtectionPriceNo_Ref($db,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('CP',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    //Tanong Get PONo Ref 20160313 1155
    public function getProtectionPriceNo_Ref_Manual($sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $stmt = $db->query("CALL gen_running_no_ref_reward('CP',".$sn.")");
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create CP Mannual, please try again!');
        }
        return $sn_ref;
    }


    //Tanong bvg-create-protection-price-manual
    function bvgCreateProtectionPriceManualAction()
    {

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->get_all();

    }

    public function getReward_CreateNoteNo_Ref($db,$distributor_id,$user_id,$sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";
        try {
            $stmt = $db->prepare("CALL gen_running_no_ref_reward('CN',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $creditnote_sn = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e);
        }
        return $creditnote_sn;
    }

    function bvgProtectionPriceSaveAction()
    {
        //print_r($_POST);die; 
        $flashMessenger = $this->_helper->flashMessenger;
        $this->_helper->layout->disableLayout();


        if ($this->getRequest()->getMethod() == 'POST') {   
            $distributor_id = $this->getRequest()->getParam('distributor_id');
            $creditnote_type = $this->getRequest()->getParam('creditnote_type');
            $chanel = $this->getRequest()->getParam('chanel');
            $credit_status = $this->getRequest()->getParam('credit_status');
            $sales_order = $this->getRequest()->getParam('sales_order');

            $price = $this->getRequest()->getParam('price');
            //$QBvgImei = new Application_Model_BvgImei();
            $QCreditNote = new Application_Model_CreditNote();

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            //Tanong

            $sn = date('YmdHis') . substr(microtime(), 2, 4);
            try {
                if($sn!=''){
                    $db = Zend_Registry::get('db');
                    $db->beginTransaction();
                    $create_date = date('Y-m-d H:i:s');


                    if($creditnote_type=='CP'){
                        $creditnote_sn = $this->getProtectionPriceNo_Ref($db,$sn);
                    }else if($creditnote_type=='CN'){
                        $key_cn = date('YmdHis') . substr(microtime(), 2, 4);
                        $creditnote_sn = $this->getReward_CreateNoteNo_Ref($db,$distributor_id,$userStorage->id,$key_cn);
                    }else{
                        $db->rollback();
                        $flashMessenger->setNamespace('error')->addMessage('Cannot Save Protection Price, please try again!');
                    }
                    
                    $data = array(
                        'distributor_id' => $distributor_id,
                        'create_by' => $userStorage->id,
                        'create_date' => $create_date,
                        'creditnote_type' => $creditnote_type,
                        'chanel' => $chanel,
                        'sales_order' => $sales_order,
                        'total_amount' => $price,
                        'use_total' => 0,
                        'balance_total' => $price,
                        'status' => $credit_status,
                        'creditnote_sn' => $creditnote_sn,
                        'sn' => $sn
                    );

                  //  print_r($data);die;
                    $QCreditNote->insert($data);
                    
                    //commit
                    $db->commit();
                   // die;
                    //$this->_redirect(($back_url ? $back_url : HOST . 'bvg/bvg-create-protection-price-manual'));

                    
                    //$this->_redirect((HOST . 'bvg/bvg-create-protection-price-manual'));
                    //$this->_redirect('/bvg/bvg-create-protection-price-manual');
                    $this->_redirect(($back_url ? $back_url : HOST . 'bvg/bvg-create-protection-price-manual'));
                }
                //$stmt->execute();
            }catch (exception $e) {
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot Save Protection Price, please try again!');
                $this->_redirect(($back_url ? $back_url : HOST . 'bvg/bvg-create-protection-price-manual'));
                //$this->_redirect('/bvg/bvg-create-protection-price-manual');
            }

            //$this->_redirect('/bvg/bvg-create-protection-price-manual');
        }
    }


    function check_imei($imei_sn=null, $distributor_id=null)
    {
        $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('i'=> 'imei'),array('COUNT( i.imei_sn)'))
            ->where('i.imei_sn = ?', $imei_sn)
            ->where('i.distributor_id = ?', $distributor_id);
            //->where('i.sales_sn is not null', null);
            //->where('i.out_price >0', null);
            $total = $db->fetchOne($select);
        return $total;
    }

    function check_accessories($invoice_number,$good_code,$good_color)
    {
        $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('m'=> 'market'),array('m.sn','m.invoice_number','m.d_id','m.good_id','m.good_color','m.price','m.invoice_time','m.cat_id'))
            ->joinLeft(array('g' => 'good'), 'm.good_id=g.id', array('g.name'))
            ->joinLeft(array('gc' => 'good_color'), 'm.good_color=gc.id', array('gc.name'))
            ->where('m.invoice_number = ?', $invoice_number)
            ->where('g.name= ?', $good_code)
            ->where('gc.name= ?', $good_color);

            $total = $db->fetchRow($select);
        return $total;
    }

}
