<?php

class SalesController extends My_Controller_Action
{
    public function indexAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function stockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'stock.php';
    }

    public function stockStorageAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'stock-storage.php';
    }

    public function createAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create.php';
    }

    public function createStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-stock.php';
    }

    public function saveStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-stock.php';
    }

    public function createExcelAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-excel.php';
    }

    public function createSimAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-sim.php';
    }

    public function createTgddAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-tgdd.php';
    }

    public function viewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'view.php';
    }

    public function viewStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'view-stock.php';
    }

    public function saveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save.php';
    }

    public function saveExcelAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-excel.php';
    }

    public function saveSimAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-sim.php';
    }

    public function saveTgddAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-tgdd.php';
    }

    public function mouLogAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'mou-log.php';
    }

    public function returnAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return.php';
    }

    public function returnViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-view.php';
    }

    public function saveReturnAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-return.php';
    }

    public function distributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor.php';
    }

    public function distributorMassUploadAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload.php';
    }

    public function distributorMassUploadSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-save.php';
    }

    public function distributorMassUploadVtaAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-vta.php';
    }

    public function distributorMassUploadVtaSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'distributor-mass-upload-vta-save.php';
    }

    public function createDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'create-distributor.php';
    }

    public function saveDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'save-distributor.php';
    }

    public function deleteDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'delete-distributor.php';
    }

    public function undeleteDistributorAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'undelete-distributor.php';
    }

    public function returnListAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'return-list.php';
    }

    public function delAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del.php';
    }

    public function delStockAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del-stock.php';
    }

    

    public function printSaleAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'print-sale.php';
    }

    public function targetAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target.php';
    }

    public function targetSaveAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-save.php';
    }

    public function targetCheckAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-check.php';
    }

    public function targetViewAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-view.php';
    }

    public function targetUpdateAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'target-update.php';
    }

    public function delPoAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'del-po.php';
    }

    /**
     * List of model and quantity to check price protection
     * @return [type] [description]
     */
    public function priceProtectionAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'price-protection.php';
    }

    /**
     * Tách đơn hàng thành 2 đơn nhỏ - (giữ một phần đơn gốc + tạo 1 đơn mới)
     * @return [type] [description]
     */
    public function splitAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'split.php';
    }

    /**
     * Tách đơn hàng thành 2 đơn nhỏ - (giữ một phần đơn gốc + tạo 1 đơn mới)
     * @return [type] [description]
     */
    public function splitGetOrderAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'split-get-order.php';
    }

    /**
     * Tách đơn hàng thành 2 đơn nhỏ - (giữ một phần đơn gốc + tạo 1 đơn mới)
     * @return [type] [description]
     */
    public function splitActAction()
    {
        require_once 'sales' . DIRECTORY_SEPARATOR . 'split-act.php';
    }

    /**
     * Function saveAPI
     * Action sales/save và sales/save-excel sẽ dùng chung API này
     *     để thuận tiện việc nhập và trả dữ liệu
     * @param  array  $params - mảng tất cả các tham số cần thiết
     * @return array
     */
    private function saveAPI($params = array())
    {
        //print_r($_POST);
        //die;
        $market_general_id    = isset($params['market_general_id']) ? $params['market_general_id'] : null;
        $ids                  = isset($params['ids']) ? $params['ids'] : null;
        $cat_ids              = isset($params['cat_id']) ? $params['cat_id'] : null;
        $good_ids             = isset($params['good_id']) ? $params['good_id'] : null;
        $good_colors          = isset($params['good_color']) ? $params['good_color'] : null;
        $nums                 = isset($params['num']) ? $params['num'] : null;
        $prices               = isset($params['price']) ? $params['price'] : null;
        $totals               = isset($params['total']) ? $params['total'] : null;
        $texts                = isset($params['text']) ? $params['text'] : null;
        $distributor_id       = isset($params['distributor_id']) ? $params['distributor_id'] : null;
        $warehouse_id         = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;
        $salesman             = isset($params['salesman']) ? $params['salesman'] : null;
        $type                 = isset($params['type']) ? $params['type'] : null;
        $sale_off_percent     = isset($params['sale_off_percent']) ? $params['sale_off_percent'] : null;
        $sn                   = isset($params['sn']) ? $params['sn'] : null;
        $isbatch              = isset($params['isbatch']) ? $params['isbatch'] : null;
        $life_time            = isset($params['life_time']) ? $params['life_time'] : null;
        $service_id           = isset($params['service_id']) ? $params['service_id'] : null;
        $ids_bvg              = isset($params['ids_bvg']) ? $params['ids_bvg'] : null;
        $good_ids_bvg         = isset($params['good_id_bvg']) ? $params['good_id_bvg'] : null;
        $nums_bvg             = isset($params['num_bvg']) ? $params['num_bvg'] : null;
        $prices_bvg           = isset($params['price_bvg']) ? $params['price_bvg'] : null;
        $totals_bvg           = isset($params['total_bvg']) ? $params['total_bvg'] : null;
        $joint                = isset($params['joint']) ? $params['joint'] : null;
        $ids_discount         = isset($params['ids_discount']) ? $params['ids_discount'] : null;
        $joint_discount       = isset($params['joint_discount']) ? $params['joint_discount'] : null;
        $prices_discount      = isset($params['prices_discount']) ? $params['prices_discount'] : null;
        $bvg_imei             = isset($params['bvg_imei']) ? $params['bvg_imei'] : null;
        $userStorage          = Zend_Auth::getInstance()->getStorage()->read();
        $distributor_po       = isset($params['distributor_po']) ? $params['distributor_po'] : null;
        $invoice              = isset($params['invoice_data']) ? $params['invoice_data'] : null;
        $gift_id              = isset($params['gift_id']) ? $params['gift_id'] : null;
        $include_shipping_fee = isset($params['include_shipping_fee']) ? $params['include_shipping_fee'] : 0;
        $user_uncheck         = isset($params['user_uncheck']) ? $params['user_uncheck'] : 0;
        $campaign             = isset($params['campaign']) ? $params['campaign'] : null;
        $payment_method       = isset($params['payment_method']) ? $params['payment_method'] : null;
        $invoice_number_data  = isset($params['invoice_number_data']) ? $params['invoice_number_data'] : null;

        $currentTime          = date('Y-m-d H:i:s');

        //Thông tin nhân viên mua máy
        $market_general_data  = isset($params['market_general_data']) ? $params['market_general_data'] : null;
        $id_staffs            = isset($params['id_staff']) ? $params['id_staff'] : null;
        $cmnd_staff_ingames   = isset($params['cmnd_staff_ingame']) ? $params['cmnd_staff_ingame'] : null;
        $product_color_keys   = isset($params['product_color_key']) ? $params['product_color_key'] : null;
        $staff_nums           = isset($params['staff_num']) ? $params['staff_num'] : null;
        $shipment_id          = isset($params['shipment_id']) ? $params['shipment_id'] : null;
        $for_partner          = isset($params['for_partner']) ? $params['for_partner'] : NULL;

        //Tanong Add New credit_id 2016/02/26
        $credit_id            = isset($params['credit_id']) ? $params['credit_id'] : NULL;
        $creditnote_data      = isset($params['creditnote_data']) ? $params['creditnote_data'] : null;

        //Tanong Delivery Address
        $delivery_address      = isset($params['delivery_address']) ? $params['delivery_address'] : null;
        //Tanong Delivery Fee
        $delivery_fee      = isset($params['delivery_fee']) ? $params['delivery_fee'] : null;

        $customer_id      = isset($params['customer_id']) ? $params['customer_id'] : null;
        $customer_name      = isset($params['customer_name']) ? $params['customer_name'] : null;
        $customer_tax_number      = isset($params['customer_tax_number']) ? $params['customer_tax_number'] : null;
        $customer_tax_address      = isset($params['customer_tax_address']) ? $params['customer_tax_address'] : null;
        $rank      = isset($params['rank']) ? $params['rank'] : null;

        //print_r($creditnote_data);die;
        /**
         * @author: buu.pham
         * market fee - tính giá ship
         * nếu giá trị đơn hàng - bảo vệ giá - chiết khấu < giá tối thiểu => cộng tiền ship
         *     >= giá tối thiểu => tiền ship = 0
         */
        $s_total_price = 0; // tổng giá đơn hàng để tính tiền ship

        //check can edit lifetime
        $QExceptionCase = new Application_Model_ExceptionCase();
        $where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
            'LIFETIME_EXCEPTION');
        $lifetime_exception = $QExceptionCase->fetchRow($where);

        $exception_case = null;
        if (isset($lifetime_exception) and $lifetime_exception['value'])
        {
            eval(json_decode($lifetime_exception['value']));
            $exception_case = isset($data_exception) ? $data_exception : null;
        }

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))
            or ($exception_case and in_array($userStorage->id, $exception_case)))
            $life_time_editable = 1;
        else
            $life_time_editable = 0;

        if ($life_time_editable and $life_time <= 0)
        {
            return array(
                'code' => -1,
                'message' => 'Invalid lifetime, please try again!',
                );
        }
        //end of check can edit lifetime
        //
        if (!$type)
            return array(
                'code' => -1,
                'message' => 'Invalid type, please try again!',
                );

        if (isset($ids_bvg) and $ids_bvg and isset($ids_discount) and $ids_discount)
        {
            return array(
                'code' => -1,
                'message' => 'Multiple type, please select only one discount!',
                );
        }

        // check PO Number
        if (!empty($distributor_po))
        {
            $QDistributorPo = new Application_Model_DistributorPo();
            $po_check = $QDistributorPo->find($distributor_po);
            $po_check = $po_check->current();

            if (!$po_check)
                return array(
                    'code' => -6,
                    'message' => 'Invalid PO Number',
                    );
        }

        //Tanong
        if (!$sn){
            $sn = date('YmdHis') . substr(microtime(), 2, 4);

            $sn_ref=$this->getSalesOrderNo_Ref($sn);
            //$sn_ref="";
        }

        $QLog = new Application_Model_Log();
        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        
        

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $goods_cache = $QGood->get_cache();
        $good_colors_cache = $QGoodColor->get_cache();
        try
        {
            //customer_brandshop
            if($rank==10){
                $QCustomerBrandShop   = new Application_Model_CustomerBrandShop();
                $CustomerBrandShop = $QCustomerBrandShop->chkCustomerBrandshop($customer_name,$customer_tax_number);
                if(!$CustomerBrandShop){
                    $customer_id='';
                }

                $data_customer = array();
                $data_customer['customer_name'] = $customer_name;
                $data_customer['tax_number'] = $customer_tax_number;
                $data_customer['address_tax'] = $customer_tax_address;
                if($customer_id !='')
                { //update
                    $data_customer['update_date']   = $currentTime;
                    $data_customer['update_by']  = $userStorage->id;
                    $where = $QCustomerBrandShop->getAdapter()->quoteInto('customer_id = ?', $customer_id);
                    $QCustomerBrandShop->update($data_customer, $where);
                }else{ //insert
                    $data_customer['status'] = 1;
                    $data_customer['create_date']   = $currentTime;
                    $data_customer['create_by']  = $userStorage->id;
                    $customer_id = $QCustomerBrandShop->insert($data_customer);
                }
            }
            //print_r($data_customer);die;

            $QMarketDeduction = new Application_Model_MarketDeduction();
            //get old ids
            $old_ids = $error_ids = null;

            if ($sn)
            {
                $where = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
                $old_sales = $QMarketDeduction->fetchAll($where);

                if ($old_sales)
                {
                    foreach ($old_sales as $sale)
                    {
                        $old_ids[] = $sale->id;
                    }
                }
            }

            if (isset($ids_discount) and is_array($ids_discount))
            {

                $total_price_discount = 0;
                $price_discount_joint = 0;
                $discounts = array();
                $discount = array('d_id' => $distributor_id, );
                $price_discount_joint = $QMarketDeduction->getPrice($discount);

                foreach ($ids_discount as $k => $id)
                {
                    if (isset($joint_discount[$k]) and $joint_discount[$k] and isset($prices_discount[$k]) and
                        $prices_discount[$k])
                    {

                        if ((intval($price_discount_joint[$joint_discount[$k]]) - intval($prices_discount[$k])) < 0)
                        {
                            throw new Exception(' Your discount is larger than limited ');
                        }

                        $data = array(
                            'price'             => My_Number::floatval( $prices_discount[$k] ),
                            'd_id'              => intval( $distributor_id ),
                            'joint_circular_id' => intval( $joint_discount[$k] ),
                            );

                        // tổng giá
                        $s_total_price -= My_Number::floatval( $prices_discount[$k] );

                        if ($id)
                        { //update
                            $where = $QMarketDeduction->getAdapter()->quoteInto('id = ?', $id);
                            $QMarketDeduction->update($data, $where);
                        } else
                        {
                            $data['add_time']   = $currentTime;
                            $data['sn']         = $sn;
                            $QMarketDeduction->insert($data);
                        }
                    }
                }

                //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info = 'Discount Sale : ';
                $info .= $sn;

                $QLog->insert(array(
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => $currentTime,
                    ));
            }

            if ($sn)
            {
                if ($ids_discount)
                {
                    $newIds = $ids_discount ? $ids_discount : array();
                    $removed_sales_ids = array_diff($old_ids, $newIds);
                    if ($removed_sales_ids)
                    {
                        $where = $QMarketDeduction->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
                        $QMarketDeduction->delete($where);
                    }
                }
            }

        }
        catch (exception $e)
        {
            return array(
                'code' => -3,
                'message' => 'Cannot save, please try again!' . $e->getMessage(),
            );
        }

        try
        {
            $QMarketProduct = new Application_Model_MarketProduct();
            $QBVGIMEI = new Application_Model_BvgImei();

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

            if (isset($ids_bvg) and is_array($ids_bvg))
            {

                foreach ($ids_bvg as $k => $id)
                {
                    if (isset($good_ids_bvg[$k]) and $good_ids_bvg[$k] and isset($nums_bvg[$k]) and
                        $nums_bvg[$k] and isset($prices_bvg[$k]) and $prices_bvg[$k] and isset($totals_bvg[$k]) and
                        $totals_bvg[$k])
                    {
                        // them viec kiem tra imei do co fai duoc bao ve gia cho dai ly do khong
                        $list_imei = explode(',', $bvg_imei[$k]);
                        $where     = array();
                        $where[]   = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
                        $where[]   = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                        $where[]   = $QBVGIMEI->getAdapter()->quoteInto('d_id = ?', $distributor_id);

                        $imeiListChecked = $QBVGIMEI->fetchAll($where);

                        if ($imeiListChecked->count() != count($list_imei)){
                            throw new Exception('List BVG IMEI is invalid');
                        }
                        // End of them viec kiem tra imei do co fai duoc bao ve gia cho dai ly do khong

                        $data = array(
                            'good_id'      => intval( $good_ids_bvg[$k] ),
                            'num'          => intval( $nums_bvg[$k] ),
                            'price'        => My_Number::floatval( $prices_bvg[$k] ),
                            'total'        => My_Number::floatval( $totals_bvg[$k] ),
                            'd_id'         => intval( $distributor_id ),
                            'warehouse_id' => intval( $warehouse_id ),
                            'joint'        => intval( $joint[$k] ),
                            'text'         => trim($texts[$k] ? $texts[$k] : '')
                            );

                        // tổng giá
                        $s_total_price -= My_Number::floatval( $totals_bvg[$k] );

                        if ($id)
                        { //update
                            $where     = $QMarketProduct->getAdapter()->quoteInto('id = ?', $id);
                            $QMarketProduct->update($data, $where);
                            $list_imei = explode(',', $bvg_imei[$k]);
                            $data      = array('bvg_market_product_id' => $id);
                            $where     = array();
                            $where[]   = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
                            $where[]   = $QBVGIMEI->getAdapter()->quoteInto('bvg_payment_confirmed_at is NULL', null);
                            $QBVGIMEI->update($data, $where);
                        } else
                        {
                            $data['add_time'] = date('Y-m-d H:i:s');
                            $data['user_id']  = $userStorage->id;
                            $data['sn']       = $sn;
                            $id_bvg           = $QMarketProduct->insert($data);
                            $list_imei        = explode(',', $bvg_imei[$k]);
                            $data             = array('bvg_market_product_id' => $id_bvg);
                            $where            = array();
                            $where[]          = $QBVGIMEI->getAdapter()->quoteInto('id in ( ? )', $list_imei);
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
                    'info'      => $info,
                    'user_id'   => $userStorage->id,
                    'ip_address'=> $ip,
                    'time'      => $currentTime,
                    ));

            }


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
        catch (exception $e)
        {
            return array(
                'code' => -3,
                'message' => 'Cannot save, please try again!' . $e->getMessage(),
            );
        }

        if (is_array($ids))
        {

            try
            {
                $QDistributor = new Application_Model_Distributor();
                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                $array_good_color = array();
                $distributor = $QDistributor->fetchRow($where);
                $rank = $distributor['rank'];

                $QMarket = new Application_Model_Market();

                /* TODO insert market general */
                $QMarketGeneral = new Application_Model_MarketGeneral();
                $data           = array(
                    'sn'                => $sn,
                    'price_clas'        => intval( $rank ),
                    'd_id'              => intval( $distributor_id ),
                    'warehouse_id'      => intval( $warehouse_id ),
                    'isbatch'           => intval( $isbatch ),
                    'salesman'          => intval( $salesman ),
                    'type'              => intval( $type ),
                    'service'           => intval( $service_id ),
                );


                if ($market_general_id) {
                    $whereMarketGeneral = $QMarketGeneral->getAdapter()->quoteInto('id = ?', $market_general_id);
                    $QMarketGeneral->update($data, $whereMarketGeneral);
                } else {
                    $data['add_time']   = $currentTime;
                    $data['user_id']    = $userStorage->id;
                    $newMarketGeneralId = $QMarketGeneral->insert($data);

                }
                /* TODO End of insert market general */



                if (!isset($ids_bvg))
                {
                    $QMarketProduct = new Application_Model_MarketProduct();
                    $where = $QMarketProduct->getAdapter()->quoteInto('sn = ? ', $sn);
                    $QMarketProduct->delete($where);
                }

                if (!isset($ids_discount))
                {
                    $QMarketProduct = new Application_Model_MarketDeduction();
                    $where = $QMarketProduct->getAdapter()->quoteInto('sn = ? ', $sn);
                    $QMarketProduct->delete($where);
                }

                //My_Lock::setStatus($sn , 0 , array());

                //get old ids
                $old_ids = $error_ids = null;
                if ($sn)
                {
                    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                    $old_sales = $QMarket->fetchAll($where);

                    if ($old_sales)
                    {
                        foreach ($old_sales as $sale)
                        {
                            $old_ids[] = $sale->id;

                            if ($sale['pay_time'] or $sale['shipping_yes_time'] or $sale['outmysql_time'])
                                $error_ids[] = $sale->id;
                        }
                    }
                }

                if ($error_ids)
                {
                    return array(
                        'code' => -5,
                        'message' => 'This order was confirmed!',
                        'sn' => $sn,
                        );
                }


                // Nhân viên mua máy
                if($type == FOR_STAFFS AND in_array($distributor_id, array(OPPO_STAFF,OPPO_INGAME)) AND intval($for_partner) == 2 )
                {
                    // Neu la don hang thanh ly thi kiem tra
                    if($shipment_id){
                        foreach($ids as $k =>  $v){
                            $where_shipment_s   = array();
                            $GoodShipmentPhone  = new Application_Model_GoodShipmentPhone();
                            $where_shipment_s[] = $GoodShipmentPhone->getAdapter()->quoteInto('good_shipment_id = ?', $market_general_data['shipment_id']);
                            $where_shipment_s[] = $GoodShipmentPhone->getAdapter()->quoteInto('good_id = ?', $good_ids[$k]);
                            $data_s             = $GoodShipmentPhone->fetchRow($where_shipment_s);

                            if(!$data_s){
                                return array(
                                    'code'    => -5,
                                    'message' => 'Chỉ được chọn mặt hàng trong lô máy.!!!!!!!!!!!',
                                );
                            }
                        }
                    }

                    $QStaffOrder = new Application_Model_StaffOrder();
                    $count_40 = array();
                    if(count($product_color_keys) == 0){
                        return array(
                            'code'    => -5,
                            'message' => 'Vui lòng chọn Nhân viên mua máy',
                        );
                    }


                    //Kiểm tra số lượng sản phẩm có phù hợp ko?
                    foreach($cat_ids as $k => $v){
                        $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                        $check_num = 0;
                        foreach($product_color_keys as $_k => $_v){
                            if( $_v == $_product_color_key ){
                                $check_num += $staff_nums[$_k];
                            }
                        }

                        if($check_num != $nums[$k]){
                            return array(
                                'code'    => -5,
                                'message' => 'Số lượng sản phẩm đặt mua và số lượng sản phẩm cho nhân viên không đúng'.$nums[$k].'-'.$check_num,
                            );
                        }

                    }

                    if($distributor_id == OPPO_STAFF)
                    {

                        if($for_partner == 2){

                            foreach($cat_ids as $k => $v){
                                if($sale_off_percent[$k] == 40){
                                    $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                                    foreach($product_color_keys as $_k => $_v){
                                        if( $_v == $_product_color_key ){
                                            if( isset($count_40[$id_staffs[$_k]]) ){
                                                $count_40[$id_staffs[$_k]] += intval($staff_nums[$_k]);

                                            }else{
                                                $count_40[$id_staffs[$_k]] = intval($staff_nums[$_k]);
                                            }
                                        }
                                    }
                                }
                            }

                            foreach($count_40 as $_staff_id => $num){
                                if($num > 1){
                                    return array(
                                        'code'    => -5,
                                        'message' => 'Nhân viên mỗi lần chỉ được mua 1 máy 40%',
                                    );
                                }else{
                                    $check = $QStaffOrder->checkStaffBuyProduct($_staff_id,1,NULL,$sn);
                                    if($check['status'] == 0){
                                        return array(
                                            'code'    => -5,
                                            'message' => $check['message'],
                                        );
                                    }
                                }
                            }
                        }
                    }
                    elseif($distributor_id == OPPO_INGAME){
                        foreach($cat_ids as $k => $v){
                            if($sale_off_percent[$k] == 40){
                                $_product_color_key = $good_ids[$k].'_'.$good_colors[$k];
                                foreach($product_color_keys as $_k => $_v){
                                    if( $_v == $_product_color_key ){
                                        $cmnd = trim($cmnd_staff_ingames[$_k]);

                                        if($cmnd == ''){
                                            return array(
                                                'code'    => -5,
                                                'message' => 'Please insert ID number',
                                            );
                                        }

                                        if( isset($count_40[$cmnd]) ){
                                            $count_40[$cmnd] += intval($staff_nums[$_k]);
                                        }else{
                                            $count_40[$cmnd] = intval($staff_nums[$_k]);
                                        }
                                    }
                                }
                            }
                        }

                        foreach($count_40 as $_id_number => $num){
                            if($num > 1){
                                return array(
                                    'code'    => -5,
                                    'message' => 'Nhân viên mỗi lần chỉ được mua 1 máy 40%',
                                );
                            }else{
                                $check = $QStaffOrder->checkStaffIngameBuyProduct($_id_number,1,NULL,$sn);
                                if($check['status'] == 0){
                                    return array(
                                        'code'    => -5,
                                        'message' => $check['message'],
                                    );
                                }
                            }
                        }

                    }

                }


                if (isset($invoice) and $invoice)
                {
                    $QCustomer = new Application_Model_Customer();

                    $data = array(
                        'name'         => $invoice['customer_name'],
                        'company'      => $invoice['company'],
                        'address'      => $invoice['customer_address'],
                        'office_id'    => intval($invoice['office']),
                        'tax_code'     => $invoice['tax_code'],
                        'invoice_type' => $invoice['invoice'],
                        'add_time'     => $currentTime,
                        'sn'           => $sn,
                        'service_nvmm' => intval($invoice['service_nvmm']),
                        'warehouse_nvmm' => intval($invoice['warehouse_nvmm']),
                        );


                    if (!$id)
                        $QCustomer->insert($data);
                    else
                    {
                        $where = $QCustomer->getAdapter()->quoteInto('id = ?', $id);
                        $QCustomer->update($data, $where);
                    }

                }

                $missing_stock = array();

                if (isset($ids) and $ids)
                {
                    $resultSet = $QMarket->find($ids[0]);
                    $market_current = $resultSet->current();
                    if (isset($market_current) and $market_current)
                    {
                        $date_curent = $market_current['add_time'];
                        $distributor_id_current = $market_current['d_id'];
                        $current_time  = date('H:i:s');
                        //kiểm tra không cho đổi đại lý
                        if($distributor_id != $distributor_id_current && $current_time > TIME_LIMIT_ORDER)
                        {
                            throw new Exception("Sorry, you can't change distributor for this order.Please remove order and create again.");
                        }
                    }
                }


                foreach ($ids as $k => $id)
                {

                    if (isset($cat_ids[$k]) and $cat_ids[$k] and isset($good_ids[$k]) and $good_ids[$k] and
                        isset($good_colors[$k]) and $good_colors[$k] and isset($nums[$k]) and $nums[$k] and
                        isset($prices[$k]))
                    {

                        $total2 = 0;

                        if ($cat_ids[$k] == PHONE_CAT_ID)
                        {
                            if(in_array($good_ids[$k] . '_' . $good_colors[$k] , $array_good_color))
                            {
                                throw new Exception("Sorry, your input is dublicated, Model : " . $goods_cache[$good_ids[$k]] . " - " . $good_colors_cache[$good_colors[$k]]);
                            }
                            $array_good_color[] = $good_ids[$k] . '_' . $good_colors[$k];
                        }

                        $storageParams = array(
                            'warehouse_id'  => $warehouse_id,
                            'cat_id'        => $cat_ids[$k],
                            'good_id'       => $good_ids[$k],
                            'good_color_id' => $good_colors[$k],
                            );

                        // truong hop edit lai
                        if ($id)
                            $storageParams['current_order_id'] = $id;

                        $storageParams['not_get_ilike_bad_count'] = $storageParams['not_get_digital_bad_count'] =
                            $storageParams['not_get_imei_bad_count'] = $storageParams['not_get_damage_product_count'] =
                            $storageParams['not_get_total'] = $storageParams['not_order'] = true;

                        if ($cat_ids[$k] == PHONE_CAT_ID)
                        {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                                $storageParams['not_get_product_count'] = true;
                        } elseif ($cat_ids[$k] == ACCESS_CAT_ID)
                        {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                                $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_ids[$k] == DIGITAL_CAT_ID)
                        {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_product_count'] =
                                $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_ids[$k] == ILIKE_CAT_ID)
                        {
                            $storageParams['not_get_digital_count'] = $storageParams['not_get_product_count'] =
                                $storageParams['not_get_imei_count'] = true;
                        }

                        $storage = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                        $current_order = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                        $current_change_order = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;
                        if ($cat_ids[$k]==PHONE_CAT_ID and $type==FOR_DEMO){
                            $current_order = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                            $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                        }

                        $current_storage = 0;

                        if (isset($storage[0]) and $storage[0])
                        {
                            switch ($cat_ids[$k]){
                                case DIGITAL_CAT_ID:
                                    $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                                    break;
                                case PHONE_CAT_ID:
                                    $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                    if ($type==FOR_DEMO){
                                        $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                    }
                                    break;
                                case ILIKE_CAT_ID:
                                    $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                                    break;
                                case ACCESS_CAT_ID:
                                    $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                                    break;

                            }
                        }

                        if (($current_storage - $current_order - $current_change_order) < $nums[$k])
                        {
                            $missing_stock[] = array(
                                'warehouse_id'    => $warehouse_id,
                                'cat_id'          => $cat_ids[$k],
                                'good_id'         => $good_ids[$k],
                                'good_color_id'   => $good_colors[$k],
                                'current_storage' => $current_storage,
                                'current_order'   => $current_order,
                                );
                        }

                        $tem_total = (isset($totals[$k]) and $totals[$k]) ? $totals[$k] : 0;
                        //print_r($tem_total);die;
                        if($delivery_fee==''){
                            $delivery_fee=0;
                        }
                        $data = array(
                            'market_general_id'=> ($market_general_id ? $market_general_id : (isset($newMarketGeneralId) ? $newMarketGeneralId : 0)),
                            'cat_id'           => intval( $cat_ids[$k] ),
                            'good_id'          => intval( $good_ids[$k] ),
                            'good_color'       => intval( $good_colors[$k] ),
                            'num'              => intval( $nums[$k] ),
                            'price'            => My_Number::floatval( $prices[$k] ),
                            'total'            => My_Number::floatval( $tem_total ),
                            'text'             => (isset($texts[$k]) ? $texts[$k] : null),
                            'price_clas'       => intval( $rank ),
                            'd_id'             => intval( $distributor_id ),
                            'warehouse_id'     => intval( $warehouse_id ),
                            'isbatch'          => intval( $isbatch ),
                            'salesman'         => intval( $salesman ),
                            'type'             => intval( $type ),
                            'service'          => intval( $service_id ),
                            'sale_off_percent' => intval( $sale_off_percent[$k] ),
                            'campaign'         => intval($campaign[$k]),
                            'last_updated_at'  => $currentTime,
                            'payment_method'   => $payment_method,
                            'for_partner'      => $for_partner,
                            'credit_id'        => $credit_id,
                            'delivery_address' => $delivery_address,
                            'delivery_fee'     => $delivery_fee
                            
                        );

                        if($customer_id !=''){
                           $data['customer_id'] = $customer_id; 
                           $data['customer_tax_address'] = $customer_tax_address;
                        }

                        if(isset($invoice_number_data) and $invoice_number_data)
                        {
                            // số hóa đơn khi save mass upload
                            $invoice_number = unserialize($invoice_number_data);
                            if(is_array($invoice_number) and $invoice_number)
                            {
                                $data['invoice_number'] = $invoice_number['invoice_number'] ? $invoice_number['invoice_number'] : '';
                                $data['invoice_sign']   = $invoice_number['invoice_sign'] ? $invoice_number['invoice_sign'] : '';
                                $data['invoice_time']   = $invoice_number['invoice_time'] ? $invoice_number['invoice_time'] : '';
                                $data['text']           = 'Đơn hàng tồn kho tặng kèm sim';

                                $data['shipping_yes_time'] = $currentTime;
                                $data['pay_time']          = $currentTime;
                                $data['shipping_yes_id']   = 1;
                                $data['pay_user']          = 1;
                                $data['outmysql_time']     = $currentTime;
                                $data['outmysql_user']     = 1;
                                $data['campaign']          = 99;

                            }

                        }

                        // tổng giá
                        $s_total_price += My_Number::floatval( $tem_total );

                        ///đơn hàng tặng cho nhân viên
                        if (isset($gift_id) and $gift_id)
                        {
                            $data['office'] = $gift_id;
                        }

                        if (isset($invoice) and $invoice)
                        {
                            $data['office']             = intval($invoice['office']);
                            $data['warehouse_nvmm']     = intval($invoice['warehouse_nvmm']);
                            $data['service']            = intval($invoice['service_nvmm']);
                        }
                        //Tanong
                        //total

                        if (!empty($distributor_po))
                            $data['po_id'] = $distributor_po;
                        else
                            $data['po_id'] = null;

                        if ($life_time_editable and $life_time) {
                            if ($life_time <= 0 || $life_time > 5 || !is_numeric($life_time)) $life_time = 2;
                            $data['life_time'] = $life_time * 24 * 60 * 60;
                        }

                        //print_r($data);die;
                        if ($id)
                        { //update
                            $where = $QMarket->getAdapter()->quoteInto('id = ?', $id);
                            $QMarket->update($data, $where);
                        }else{ //insert

                            if (isset($date_curent) and $date_curent)
                                $date_time_add = $date_curent;
                            else
                                $date_time_add = $currentTime;

                            $data['add_time'] = $date_time_add;
                            $data['user_id']  = $userStorage->id;
                            $data['sn']       = $sn;
                            $data['sn_ref']   = $sn_ref;
                                        
                            $data['print_no'] = ($QMarket->get_print_no_max($sn)) + 1;
                            $id = $QMarket->insert($data);

                            //Tanong New Sales Order Format 20160312 1305
                            /*
                            if ($sn!=''){
                                //print_r($sn);die;
                                $this->getSalesOrderNo_Ref($sn);
                            }
                            */
                        }

                        //Tanong
                        $Total_Amount=$QMarket->LoadSalesOrderAmount($distributor_id);
                        $total_amount_all = $Total_Amount[0]['total_acmount'];
                        $total_ptice = $data['total'];
                        $delivery_fee_all = $Total_Amount[0]['delivery_fee'];
                        
                        if ($delivery_fee_all >0 && $total_amount_all>=10000)
                        { //update delivery_fee =0 all sales order in 1 day when sales order amount all 1 day >10000
                         //   print_r($Total_Amount);
                         // echo 'chk_total_amount_all >>'.$total_amount_all;
                         // echo 'delivery_fee_all >>'.$delivery_fee_all;
                         // die;
                            $data_delivery = array(
                                'delivery_fee' => 0,
                            );

                            //$data['delivery_fee'] = 0;
                            $where = $QMarket->getAdapter()->quoteInto("(DATE_FORMAT(add_time,'%Y%m%d')=DATE_FORMAT(NOW(),'%Y%m%d'))", null);
                            $where = $QMarket->getAdapter()->quoteInto('d_id = ?', $distributor_id);
                           // $QMarket->update($data_delivery, $where);
                        }

                        

                    }
                }
        
                //delete old sales
                if ($this->getRequest()->getParam('sn'))
                {
                    if ($old_ids)
                    {
                        $removed_sales_ids = array_diff($old_ids, $ids);
                        if ($removed_sales_ids)
                        {
                            $where = $QMarket->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
                            $QMarket->delete($where);
                        }
                    }
                    $info = 'Batch EditSale order number: Sale order number： ';
                } else
                {
                    $info = 'Batch addSale order number: Sale order number： ';
                }

                // NHAN VIEN MUA MAY
                if($type == FOR_STAFFS AND in_array($distributor_id, array(OPPO_STAFF,OPPO_INGAME))){
                    $QStaffOrder = new Application_Model_StaffOrder();
                    $resultStaffOrder = $QStaffOrder->save($params,$sn);
                    if($resultStaffOrder['status'] < 0){
                        return array(
                            'code' => -5,
                            'message' => $resultStaffOrder['message']
                        );
                    }
                }

                // Neu co nhung don khuyen mai massupload thi khong can kiem tra stock
                if(!$invoice_number_data)
                {
                    if ($missing_stock)
                        throw new Exception(1);
                }


                //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info .= $sn;

                $QLog->insert(array(
                    'info'       => $info,
                    'user_id'    => $userStorage->id,
                    'ip_address' => $ip,
                    'time'       => $currentTime,
                ));

                //commit
                $db->commit();

                $result = array(
                    'code' => 1,
                    'message' => 'Done!',
                    'sn' => $sn,
                );

                if (isset($id) && $id) $result['id'] = $id;

                return $result;

            }
            catch (exception $e)
            {
                $db->rollback();

                if ($e->getMessage() == 1)
                    return array(
                        'code' => -2,
                        'message' => 'The stock available is not enough!',
                        );
                else
                    return array(
                        'code' => -3,
                        'message' => 'Cannot save, please try again! ' . $e->getCode()."/".$e->getMessage(),
                        );
            }

        } else
        {
            $db->rollback();
            return array(
                'code' => -4,
                'message' => 'Cannot save, please try again!',
                );
        }
    }

    /**
     * Tạo đơn hàng từ Stock tết
     * @param array $params
     * @return integer
     */
    private function saveAPIStock($params = array())
    {
        $ids              = isset($params['ids']) ? $params['ids'] : null;
        $cat_ids          = isset($params['cat_id']) ? $params['cat_id'] : null;
        $good_ids         = isset($params['good_id']) ? $params['good_id'] : null;
        $good_colors      = isset($params['good_color']) ? $params['good_color'] : null;
        $nums             = isset($params['num']) ? $params['num'] : null;
        $prices           = isset($params['price']) ? $params['price'] : null;
        $totals           = isset($params['total']) ? $params['total'] : null;
        $texts            = isset($params['text']) ? $params['text'] : null;
        $distributor_id   = isset($params['distributor_id']) ? $params['distributor_id'] : null;
        $warehouse_id     = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;
        $salesman         = isset($params['salesman']) ? $params['salesman'] : null;
        $type             = isset($params['type']) ? $params['type'] : null;
        $sale_off_percent = isset($params['sale_off_percent']) ? $params['sale_off_percent'] : null;
        $sn               = isset($params['sn']) ? $params['sn'] : null;
        $isbatch          = isset($params['isbatch']) ? $params['isbatch'] : null;
        $life_time        = isset($params['life_time']) ? $params['life_time'] : null;
        $userStorage      = Zend_Auth::getInstance()->getStorage()->read();
        $gift_id          = isset($params['gift_id']) ? $params['gift_id'] : null;
        $campaign         = isset($params['campaign']) ? $params['campaign'] : null;
        $imei_list        = isset($params['imei_list']) ? $params['imei_list'] : null;
        $currentTime      = date('Y-m-d H:i:s');

        //check can edit lifetime
        $QExceptionCase = new Application_Model_ExceptionCase();
        $where = $QExceptionCase->getAdapter()->quoteInto('name = ?',
            'LIFETIME_EXCEPTION');
        $lifetime_exception = $QExceptionCase->fetchRow($where);

        $exception_case = null;
        if (isset($lifetime_exception) and $lifetime_exception['value']) {
            eval(json_decode($lifetime_exception['value']));
            $exception_case = isset($data_exception) ? $data_exception : null;
        }

        if (My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID, SUPER_SALES_ADMIN))
            or ($exception_case and in_array($userStorage->id, $exception_case)))
            $life_time_editable = 1;
        else
            $life_time_editable = 0;

        if ($life_time_editable and $life_time <= 0) {
            return array(
                'code' => -1,
                'message' => 'Invalid lifetime, please try again!',
            );
        }
        //end of check can edit lifetime
        //
        if ($type != 1)
            return array(
                'code' => -1,
                'message' => 'Invalid type, please try again!',
            );

        $QLog         = new Application_Model_Log();
        $QGood        = new Application_Model_Good();
        $QGoodColor   = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QMarket      = new Application_Model_MarketStock();

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        if (!$sn)
            $sn = date('YmdHis') . substr(microtime(), 2, 4);

        $goods_cache = $QGood->get_cache();
        $good_colors_cache = $QGoodColor->get_cache();

        if (!is_array($ids)) {
            $db->rollback();
            return array(
                'code' => -4,
                'message' => 'Cannot save, please try again!',
            );
        }

        try {
            $where            = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
            $distributor      = $QDistributor->fetchRow($where);
            $rank             = $distributor['rank'];
            $array_good_color = array();

            //get old ids
            $old_ids = $error_ids = null;
            if ($sn) {
                $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
                $old_sales = $QMarket->fetchAll($where);

                if ($old_sales) {
                    foreach ($old_sales as $sale) {
                        $old_ids[] = $sale->id;

                        if ($sale['pay_time'] or $sale['shipping_yes_time'] or $sale['outmysql_time'])
                            $error_ids[] = $sale->id;
                    }
                }
            }

            if ($error_ids) {
                return array(
                    'code' => -5,
                    'message' => 'This order was confirmed!',
                    'sn' => $sn,
                );
            }

            $missing_stock = array();

            if (isset($ids) and $ids) {
                $resultSet = $QMarket->find($ids[0]);
                $market_current = $resultSet->current();

                if (isset($market_current) and $market_current) {
                    $date_curent = $market_current['add_time'];
                    $distributor_id_current = $market_current['d_id'];
                    $current_time  = date('H:i:s');
                    //kiểm tra không cho đổi đại lý
                    if($distributor_id != $distributor_id_current && $current_time > TIME_LIMIT_ORDER)
                        throw new Exception("Sorry, you can't change distributor for this order.Please remove order and create again.");
                }
            }

            // format imei list
            $imei_list = trim($imei_list);
            $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
            $imei_list = explode("\n", $imei_list);
            $imei_list = array_filter($imei_list);

            $imei_list_text = implode(',', $imei_list);

            if (!count($imei_list))
                throw new Exception("IMEI List cannot blank");

            $total_quantity = 0;
            $total_value = 0;

            $QImeiStock = new Application_Model_ImeiStock();
            $where = $QImeiStock->getAdapter()->quoteInto('market_stock_sn = ?', $sn);
            $QImeiStock->delete($where);

            foreach ($ids as $k => $id) {
                if ( !(
                    isset($cat_ids[$k]) and $cat_ids[$k] and isset($good_ids[$k]) and $good_ids[$k] and
                    isset($good_colors[$k]) and $good_colors[$k] and isset($nums[$k]) and $nums[$k] and
                    isset($prices[$k])
                ) ) continue;

                $total2 = 0;

                if ($cat_ids[$k] == PHONE_CAT_ID) {
                    if(in_array($good_ids[$k] . '_' . $good_colors[$k] , $array_good_color))
                        throw new Exception("Sorry, your input is dublicated, Model : " . $goods_cache[$good_ids[$k]] . " - " . $good_colors_cache[$good_colors[$k]]);

                    $array_good_color[] = $good_ids[$k] . '_' . $good_colors[$k];
                }

                $total_quantity += intval( $nums[$k] );
                $total_value += My_Number::floatval( $prices[$k] ) * intval( $nums[$k] );

                // check stock
                $db->query("SET @result := 0;");

                $db->query("CALL sp_check_stock_storage (?, ?, ?, ?, ?, @result);", array(
                    $nums[$k],
                    $distributor_id,
                    $good_ids[$k],
                    $good_colors[$k],
                    $imei_list_text,
                ));

                $stmt = $db->query("SELECT @result");
                $result = $stmt->fetchAll();

                if (!isset($result[0]['@result']) || $result[0]['@result'] != 1)
                    throw new Exception("Tồn kho không đủ, hoặc số lượng IMEI khác với số trên chi tiết đơn hàng.");

                $tem_total = (isset($totals[$k]) and $totals[$k]) ? $totals[$k] : 0;

                $data = array(
                    'market_general_id' => 0,
                    'cat_id'            => intval( $cat_ids[$k] ),
                    'good_id'           => intval( $good_ids[$k] ),
                    'good_color'        => intval( $good_colors[$k] ),
                    'num'               => intval( $nums[$k] ),
                    'price'             => My_Number::floatval( $prices[$k] ),
                    'total'             => My_Number::floatval( $tem_total ),
                    'text'              => (isset($texts[$k]) ? My_String::trim($texts[$k]) : null),
                    'price_clas'        => intval( $rank ),
                    'd_id'              => intval( $distributor_id ),
                    'warehouse_id'      => intval( $warehouse_id ),
                    'isbatch'           => intval( $isbatch ),
                    'salesman'          => intval( $salesman ),
                    'type'              => intval( $type ),
                    'sale_off_percent'  => intval( $sale_off_percent[$k] ),
                    'campaign'          => 16,
                    'last_updated_at'   => $currentTime,
                );

                if ($life_time_editable and $life_time) {
                    if ($life_time <= 0 || $life_time > 5 || !is_numeric($life_time)) $life_time = 2;
                    $data['life_time'] = $life_time * 24 * 60 * 60;
                }

                if ($id) { //update
                    $where = $QMarket->getAdapter()->quoteInto('id = ?', $id);
                    $QMarket->update($data, $where);

                } else { //insert

                    if (isset($date_curent) and $date_curent)
                        $date_time_add = $date_curent;
                    else
                        $date_time_add = $currentTime;

                    $data['add_time'] = $date_time_add;
                    $data['user_id']  = $userStorage->id;
                    $data['sn']       = $sn;
                    $data['print_no'] = ($QMarket->get_print_no_max($sn)) + 1;
                    $id = $QMarket->insert($data);
                }
            }

            if ($total_quantity != count($imei_list))
                throw new Exception("Số lượng IMEI trong list khác với tổng số lượng trong chi tiết đơn hàng.");

            // if ($total_value >= 20*1000*1000)
                // throw new Exception("Đơn hàng phải dưới 20 triệu");

            //delete old sales
            if ($this->getRequest()->getParam('sn')) {
                if ($old_ids) {
                    $removed_sales_ids = array_diff($old_ids, $ids);
                    if ($removed_sales_ids) {
                        $where = $QMarket->getAdapter()->quoteInto('id IN (?)', $removed_sales_ids);
                        $QMarket->delete($where);
                    }
                }
                $info = 'Edit Stock Order SN number: ';
            } else {
                $info = 'Create Stock Order SN number: ';
            }

            // insert imei
            foreach ($imei_list as $key => $value) {
                $imei_data = array(
                    'imei_sn'         => $value,
                    'distributor_id'  => intval($distributor_id),
                    'out_date'        => date('Y-m-d H:i:s'),
                    'market_stock_sn' => $sn,
                );

                $QImeiStock->insert($imei_data);
            }

            $sql = "update imei_stock, imei
                SET imei_stock.good_id=imei.good_id
                , imei_stock.good_color=imei.good_color
                WHERE imei_stock.imei_sn=imei.imei_sn
                AND imei_stock.imei_sn IN (".implode(',', $imei_list).");";
            $db->query($sql);

            $sql = "update imei_stock, market_stock
                SET imei_stock.market_stock_id=market_stock.id
                WHERE imei_stock.market_stock_sn=market_stock.sn
                AND imei_stock.good_id=market_stock.good_id
                AND imei_stock.good_color=market_stock.good_color;
                AND imei_stock.imei_sn IN (".implode(',', $imei_list).");";
            $db->query($sql);

            //todo log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info .= $sn;

            $QLog->insert(array(
                'info'       => $info,
                'user_id'    => $userStorage->id,
                'ip_address' => $ip,
                'time'       => $currentTime,
            ));

            //commit
            $db->commit();

            $result = array(
                'code' => 1,
                'message' => 'Done!',
                'sn' => $sn,
            );

            if (isset($id) && $id) $result['id'] = $id;

            return $result;
        }
        catch (exception $e)
        {
            $db->rollback();

            if ($e->getMessage() == 1) {
                return array(
                    'code' => -2,
                    'message' => 'The stock available is not enough!',
                );
            } else {
                return array(
                    'code' => -3,
                    'message' => 'Cannot save, please try again! ' . $e->getCode()."/".$e->getMessage(),
                );
            }
        }
    }

    private function _exportReturnSaleExcel($data)
    {
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        require_once 'PHPExcel.php';
        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize' => '64MB');
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $PHPExcel = new PHPExcel();
        $heads = array(
            'No.',
            'SALE ORDER NUMBER',
            'RETAILER NAME',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'ORDER TIME',
            'WAREHOUSE',
            'Text',
        );

        //var_dump($data);exit;
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


        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QMarket = new Application_Model_Market();
        $QDistributor = new Application_Model_Distributor();
        $QGoodCategory = new Application_Model_GoodCategory();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();
        $warehouses_cached = $QWarehouse->get_cache();


        $i = 1;
        $markets = array();

        foreach ($data as $key => $m)
        {
            $where = $QMarket->getAdapter()->quoteInto('sn = ?', $m['sn']);
            $markets[$m['sn']] = $QMarket->fetchAll($where);
        }

        foreach ($data as $item)
        {
            $alpha = 'A';
            $sheet->setCellValue($alpha++ . $index, $i++);
            $sheet->getCell($alpha++ . $index)->setValueExplicit($item['sn_ref'],
                PHPExcel_Cell_DataType::TYPE_STRING);

            //$this->distributors[$m['d_id']]
            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']];
            else
                $distributor = '';

            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';

            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $sheet->setCellValue($alpha++ . $index, $distributor);
            $sheet->setCellValue($alpha++ . $index, @$item['price_1']);
            $sheet->setCellValue($alpha++ . $index, @$item['price_2']);
            $sheet->setCellValue($alpha++ . $index, $item['total_qty']);
            $sheet->setCellValue($alpha++ . $index, @$item['price_4']);
            $sheet->setCellValue($alpha++ . $index, $item['total_price']);
            $sheet->setCellValue($alpha++ . $index, $item['add_time']);
            $sheet->setCellValue($alpha++ . $index, isset($warehouses_cached[ $item['warehouse_id'] ]) ? $warehouses_cached[ $item['warehouse_id'] ] : '');
            $sheet->setCellValue($alpha++ . $index, $item['text']);
            $index++;


            foreach ($markets[$item['sn']] as $key => $value)
            {
                $alpha = 'A';
                $sheet->setCellValue($alpha++ . $index, $i++);
                //  $sheet->setCellValue($alpha++.$index, $value['sn']);

                if (isset($goods) && isset($goods[$value['good_id']]))
                    $good_name = $goods[$value['good_id']];
                if (isset($goodColors) && isset($goodColors[$value['good_color']]))
                    $good_color = $goodColors[$value['good_color']];

                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, $good_name);
                $sheet->setCellValue($alpha++ . $index, $good_color);
                $sheet->setCellValue($alpha++ . $index, $value['num']);
                $sheet->setCellValue($alpha++ . $index, @$value['price']);
                $sheet->setCellValue($alpha++ . $index, $value['total']);
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $sheet->setCellValue($alpha++ . $index, '');
                $index++;


            }

        }

        $filename = 'List_Return_Order_' . date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');

        $objWriter->save('php://output');
        exit;
    }

    private function _exportCampaignExcel($sql)
    {

        require_once 'ExcelWriterXML.php';

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(E_ALL);
        ini_set('display_error', 1);

        $filename = 'List_Campaign_Sales_' . date('YmdHis') . '.xml';
        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();
        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales');

        $heads = array(
            'SALE ORDER NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'INVOICE NUMBER',
            'INVOICE PREFIX',
            'WAREHOUSE',
            'OUT OF WAREHOUSE',
            'STATUS',
            'ORDER TIME',
            'ORDER TYPE',
            'ORDER DESCRIPTION',
            'CAMPAIGN NAME',
            'CAMPAIGN_ID'
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item)
        {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QWarehouse = new Application_Model_Warehouse();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $QCampaign = new Application_Model_Campaign();

        $goods          = $QGood->get_cache();
        $goodColors     = $QGoodColor->get_cache();
        $distributors   = $QDistributor->get_all_cache();
        $warehouses     = $QWarehouse->get_cache();
        $invoice_prefixs= $QInvoicePrefix->get_cache();
        $campaign       = $QCampaign->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
            $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $sql);

        $i = 2;

        while ($item = mysqli_fetch_assoc($result)) {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['sn']);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']]['title'];
            else
                $distributor = '';

            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check invoice number
            $invoice_number = isset($item['invoice_number']) ? $item['invoice_number'] : 'x';
            $invoice_prefix = isset($invoice_prefixs[$item['invoice_sign']]) ? $invoice_prefixs[$item['invoice_sign']] : 'x';
            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';
            $campaign_name = isset($campaign[$item['campaign']]) ? $campaign[$item['campaign']] : 'x';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $good_name = $good_color = '';
            if (isset($goods) && isset($goods[$item['good_id']]))
                $good_name = $goods[$item['good_id']];
            if (isset($goodColors) && isset($goodColors[$item['good_color']]))
                $good_color = $goodColors[$item['good_color']];

            $warehouse = isset($warehouses[$item['warehouse_id']]) ? $warehouses[$item['warehouse_id']] : '';
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['d_id']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $distributor);
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::Area) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::Province) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::District) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_name);
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_color);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['num']);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['price']);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['total']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_number);
            $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_prefix);
            $sheet->stdOutSheetColumn('String', $i, $j++, $warehouse);
            $sheet->stdOutSheetColumn('String', $i, $j++, $out);
            $sheet->stdOutSheetColumn('String', $i, $j++, $status);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['add_time']);

            if ($item['type'] == 1) //for retailer

                $type = 'For Retailer';
            elseif ($item['type'] == 2) //for demo

                $type = 'For Demo';
            elseif ($item['type'] == 3) //for staffs

                $type = 'For Staffs';
            elseif ($item['type'] == 4) //for lending

                $type = 'For Lending';

            $sheet->stdOutSheetColumn('String', $i, $j++, $type);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['text'] ? $item['text'] : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, $campaign_name ? $campaign_name : '');
            $sheet->stdOutSheetRowEnd();

            $i++;

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }

    private function _exportExcel($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Sell out List - '.date('d-m-Y H-i-s').'.csv';
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
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'Company NAME',
            'MST NAME',
            'BRANCH TYPE',
            'BRANCH NUMBER',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'PRODUCT TYPE',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SET OFF (%)',
            'UNIT PRICE EX',
            'TOTAL EX',
            'DELIVERY FEE',
            'UNIT PRICE',
            'TOTAL',
            'PAID DATETIME',
            'SHIPPING DATETIME',
            'WAREHOUSE',
            'STOCKOUT DATETIME',
            'STATUS',
            'SALE ORDER TIME',
            'CUSTOMER ORDER TYPE',
            'ORDER DESCRIPTION');

        fputcsv($output, $heads);

/*
        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QWarehouse     = new Application_Model_Warehouse();
        $QRegion        = new Application_Model_Region();
        $QArea          = new Application_Model_Area();
        $QMarket        = new Application_Model_Market();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();

        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $distributor_cache = $QDistributor->get_cache2();
        $good_categories   = $QGoodCategory->get_cache();
        $warehouses        = $QWarehouse->get_cache();
        $invoice_prefix    = $QInvoicePrefix->get_cache();
*/
        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QDistributor   = new Application_Model_Distributor();
        $QWarehouse     = new Application_Model_Warehouse();

        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $good_categories   = $QGoodCategory->get_cache();
        $distributor_cache = $QDistributor->get_cache2();
        $warehouses        = $QWarehouse->get_cache();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item) {

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            if ($item['status'] == 1) { $temp_status = 'Actived'; }
            else if ($item['status'] == 2) { $temp_status = 'Expired'; }
            else { $temp_status = 'Expired'; }

            if ($item['type'] == 1) //for retailer
                $type = 'For Retailer';
            elseif ($item['type'] == 2) //for demo
                $type = 'For Demo';
            elseif ($item['type'] == 3) //for staffs
                $type = 'For Staffs';
            elseif ($item['type'] == 4) //for lending
                $type = 'For Lending';

            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = 'สำนักงานใหญ่'; }
            else { $branch_type = 'สาขา'; }

            $row = array();
            $row[] = '="'.$temp_sn.'"';
            $row[] = $item['invoice_number'];
            $row[] = $distributor_cache[$item['d_id']]['code'];
            $row[] = $distributor_cache[$item['d_id']]['title'];
            $row[] = $distributor_cache[$item['d_id']]['unames'];
            $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
            $row[] = $branch_type;
            $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
            $row[] = $item['num'];

            $row[] = $item['sale_off_percent'];
            $row[] = number_format($item['price'] / 1.07, 2);
            $row[] = number_format($item['total'] / 1.07, 2);
            $row[] = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;

            $row[] = number_format($item['price'], 2);
            $row[] = number_format($item['total'], 2);
            $row[] = $item['pay_time'];
            $row[] = $item['shipping_yes_time'];
            $row[] = $warehouses[$item['warehouse_id']];
            $row[] = $item['outmysql_time'];
            $row[] = $temp_status;
            $row[] = $item['add_time'];
            $row[] = $type;
            $row[] = $item['text'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

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

        /*
        // Export old code [From Vietnam]
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(E_ALL);
        ini_set('display_error', 1);

        $filename = 'List_Sales_' . date('YmdHis') . '.xml';

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales');

        $heads = array(
            'SALE ORDER NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'PAYMENT OR NOT',
            'SHIPPING',
            'WAREHOUSE',
            'OUT OF WAREHOUSE',
            'STATUS',
            'ORDER TIME',
            'ORDER TYPE',
            'ORDER DESCRIPTION');

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item)
        {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QDistributor = new Application_Model_Distributor();
        $QWarehouse = new Application_Model_Warehouse();

        $goods = $QGood->get_cache();
        $goodColors = $QGoodColor->get_cache();
        $distributors = $QDistributor->get_all_cache();
        $warehouses = $QWarehouse->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
            $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $sql);

        //print_r($result);die;

        $i = 2;

        while ($item = mysqli_fetch_assoc($result))
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['sn']);

            if (isset($distributors) && isset($distributors[$item['d_id']]))
                $distributor = $distributors[$item['d_id']]['title'];
            else
                $distributor = '';

            //check payment
            isset($item['pay_time']) ? $pay = 'v' : $pay = 'X';

            //check shipping
            if ($item['shipping_yes_time'])
                $shipping = 'v';
            else
                $shipping = 'X';

            //check out_warehouse
            isset($item['outmysql_time']) ? $out = 'v' : $out = 'X';

            if ($item['status'] == 1)
                $status = 'v';
            else
                $status = 'X';

            $good_name = $good_color = '';
            if (isset($goods) && isset($goods[$item['good_id']]))
                $good_name = $goods[$item['good_id']];
            if (isset($goodColors) && isset($goodColors[$item['good_color']]))
                $good_color = $goodColors[$item['good_color']];

            $warehouse = isset($warehouses[ $item['warehouse_id'] ]) ? $warehouses[ $item['warehouse_id'] ] :'';

            $sheet->stdOutSheetColumn('String', $i, $j++, $item['d_id']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $distributor);
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::Area) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::Province) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, isset($item['district']) ?
                My_Region::getValue($item['district'], My_Region::District) : '');
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_name);
            $sheet->stdOutSheetColumn('String', $i, $j++, $good_color);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['num']);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['price']);
            $sheet->stdOutSheetColumn('Number', $i, $j++, $item['total']);
            $sheet->stdOutSheetColumn('String', $i, $j++, $pay);
            $sheet->stdOutSheetColumn('String', $i, $j++, $shipping);
            $sheet->stdOutSheetColumn('String', $i, $j++, $warehouse);
            $sheet->stdOutSheetColumn('String', $i, $j++, $out);
            $sheet->stdOutSheetColumn('String', $i, $j++, $status);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['add_time']);

            if ($item['type'] == 1) //for retailer

                $type = 'For Retailer';
            elseif ($item['type'] == 2) //for demo

                $type = 'For Demo';
            elseif ($item['type'] == 3) //for staffs

                $type = 'For Staffs';
            elseif ($item['type'] == 4) //for lending

                $type = 'For Lending';

            $sheet->stdOutSheetColumn('String', $i, $j++, $type);
            $sheet->stdOutSheetColumn('String', $i, $j++, $item['text'] ? $item['text'] : '');
            $sheet->stdOutSheetRowEnd();

            $i++;

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;*/
    }

    private function _exportExcel2($sql)
    {
        require_once 'ExcelWriterXML.php';

        set_time_limit(0);
        ini_set('memory_limit', -1);
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'List_Sales_' . date('YmdHis') . '.xml';

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales');

        $heads = array(
            'Ma_KH',
            'Nguoi_MH',
            'Dia_Chi',
            'Quyet_So',
            'So_seri',
            'So_hoa_don',
            'Ngay_chung_tu',
            'So_luong',
            'Gia',
            'Tien',
            'Gia_von',
            'Tien_von',
            'Ty_le_chiet_khau',
            'Ma_thue',
            'Tai_khoan_no',
            'Tai_khoan_doanh_thu',
            'Tai_khoan_kho',
            'Tai_khoan_gia_vo',
            'Tai_khoan_chiec_khau',
            'Tai_khoan_thue',
            'Ma_kho',
            'Ma_vat_tu',
            'Dien_giai',
            'Han_thanh_toan',
            );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item)
        {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        $QGood = new Application_Model_Good();
        $QDistributor = new Application_Model_Distributor();
        $QDistributorPo = new Application_Model_DistributorPo();
        $po_cache = $QDistributorPo->get_cache();

        $goods = $QGood->get_cache();
        $distributors = $QDistributor->get_cache2();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
            $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $sql);

        $i = 2;

        while ($item = mysqli_fetch_assoc($result))
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            $store_code = '';

            if (isset($distributors) && isset($distributors[$item['d_id']]))
            {
                $distributor = $distributors[$item['d_id']];

                $store_code = $distributor['store_code'];
            }


            $good_name = '';
            if (isset($goods) && isset($goods[$item['good_id']]))
                $good_name = $goods[$item['good_id']];

            $Quyet_So = '01GTKT3';
            $Ma_kho = '';

            switch ($item['warehouse_id'])
            {
                case 1:
                    $So_seri = 'QQ/14P';
                    $Ma_kho = 'Q4HCM';
                    break;
                case 2:
                    $So_seri = 'HN/14P';
                    $Ma_kho = 'HN';
                    break;
                case 3:
                    $So_seri = 'DN/14P';
                    $Ma_kho = 'DANANG';
                    break;
            }

            $Nguoi_MH = isset($distributors[$item['d_id']]['unames']) ? $distributors[$item['d_id']]['unames'] :
                '';
            $Dia_Chi = isset($distributors[$item['d_id']]['add']) ? $distributors[$item['d_id']]['add'] :
                '';
            $So_hoa_don = $item['invoice_number'];
            $Ngay_chung_tu = date('d/m/Y', strtotime($item['outmysql_time']));
            $So_luong = $item['num'];
            $Gia = round(($item['total'] / 1.1) / $So_luong, 2);
            $Tien = $Gia * $So_luong;
            $Gia_von = '';
            $Tien_von = '';
            $Ty_le_chiet_khau = '';
            $Ma_thue = '10';

            $Tai_khoan_doanh_thu = $Tai_khoan_no = '';
            switch ($item['type'])
            {
                case 1:
                    $Tai_khoan_no = '131A';
                    $Tai_khoan_doanh_thu = '511A';
                    break;
                case 2:
                    $Tai_khoan_no = '131E';
                    $Tai_khoan_doanh_thu = '511E';
                    break;
                case 3:
                    $Tai_khoan_no = '131D';
                    $Tai_khoan_doanh_thu = '511D';
                    break;
            }


            $Tai_khoan_kho = '1561';
            $Tai_khoan_gia_vo = '632';
            $Tai_khoan_chiec_khau = '5321';
            $Tai_khoan_thue = '33311';

            $Ma_vat_tu = $good_name;
            // xem comment bên khai báo hàm
            $Dien_giai = My_Sale_Order::getStockTetNote($item['sn']);

            if (empty($Dien_giai))
            {
                $Dien_giai = isset($po_cache[$item['po_id']]) ? $po_cache[$item['po_id']] : '';
                $Dien_giai .= "\r\n" . $item['text'];
            }

            $Han_thanh_toan = '';

            $sheet->stdOutSheetColumn('String', $i, $j++, $store_code);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Nguoi_MH);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Dia_Chi);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Quyet_So);
            $sheet->stdOutSheetColumn('String', $i, $j++, $So_seri);
            $sheet->stdOutSheetColumn('String', $i, $j++, $So_hoa_don);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay_chung_tu);
            $sheet->stdOutSheetColumn('String', $i, $j++, $So_luong);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Gia);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tien);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Gia_von);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tien_von);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ty_le_chiet_khau);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_thue);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_no);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_doanh_thu);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_kho);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_gia_vo);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_chiec_khau);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Tai_khoan_thue);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_kho);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ma_vat_tu);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Dien_giai);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Han_thanh_toan);
            $sheet->stdOutSheetRowEnd();
            $i++;

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }

    private function _exportExcel3($sql)
    {
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $filename = 'List_Sales_For_Finance_' . date('YmdHis') . '.xml';

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales');

        $heads = array(
            'STT',
            'KyHieuMauHoaDon',
            'KyHieuHoaDon',
            'SoHoaDon',
            'SoDonHang',
            'Ngay',
            'MaKH',
            'TenNguoiMua',
            'MST',
            'MatHang',
            'BVG',
            'SoTienBVG',
            'GiaTriDonHang',
            'DoanhSoChuaThue',
            'ThueGTGT',
            'GiaTriDonHangHD',
            'DoanhSoChuaThueHD',
            'ThueGTGTHD',
            'GhiChu',
            );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k => $item)
        {
            $sheet->stdOutSheetColumn('String', 1, $k + 1, $item);
        }
        $sheet->stdOutSheetRowEnd();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con = mysqli_connect($config['resources']['db']['params']['host'], $config['resources']['db']['params']['username'],
            $config['resources']['db']['params']['password'], $config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con, "utf8");

        $result = mysqli_query($con, $sql);
        $QProductBVG = new Application_Model_MarketProduct();
        $QInvoiceSign = new Application_Model_InvoicePrefix();
        $QMarket_invoice_price = new Application_Model_MarketInvoicePriceSn();
        $QMarket_deduction = new Application_Model_MarketDeduction();
        $prefix = $QInvoiceSign->get_cache();
        $QDistributorPo = new Application_Model_DistributorPo();
        $po_cache = $QDistributorPo->get_cache();


        $i = 2;

        $KyHieuMauHoaDon = '01GTKT3/0';
        $MatHang = 'Điện thoại di động';


        $k = 1;

        while ($item = mysqli_fetch_assoc($result))
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            if (isset($prefix[$item['invoice_sign']]) and $prefix[$item['invoice_sign']])
                $KyHieuHoaDon = $prefix[$item['invoice_sign']];
            else
            {
                //th những số hóa đơn cũ
                switch ($item['warehouse_id'])
                {
                    case 1:
                        $KyHieuHoaDon = 'QQ/14P';
                        break;
                    case 2:
                        $KyHieuHoaDon = 'HN/14P';
                        break;
                    case 3:
                        $KyHieuHoaDon = 'DN/14P';
                        break;
                    default:
                        $KyHieuHoaDon = 'QQ/14P';
                        break;
                }
            }


            $SoHoaDon = $item['invoice_number'];
            $Ngay = ($item['invoice_time'] ? date('d/m/Y', strtotime($item['invoice_time'])) :
                '');
            $SoDonHang = $item['sn'];
            $giaTriDonHang = $item['total_price'];
            $TenNguoiMua = $item['unames'];
            $MaNguoiMua = $item['store_code'];
            $MST = $item['mst_sn'];
            $total_price = $item['total_price'];
            $bvg = '';
            $price_bvg = 0;

            $discount = $QProductBVG->getDiscount($item['sn']);


            if (isset($discount) and $discount)
            {
                $invoice_discount = $QProductBVG->getInvoice($item['sn']);
                $params = array(
                    'sn' => $SoDonHang,
                    'isbacks' => 0,
                    'group_good' => 1,
                    );

                $params['group_sn'] = 1;
                $total2 = 0;


                if ($discount == 1)
                {
                    $bvg = 'BVG';
                    // $discount_result = $QProductBVG->fetchPagination(1, null, $total2, $params);
                } elseif ($discount == 2)
                {
                    $bvg = 'CK';
                    //   $discount_result = $QMarket_deduction->fetchPagination(1, null, $total2, $params);
                }
                $price_bvg = $QProductBVG->getPrice($item['sn']);
            }


            $DoanhSoChuaThue = round($total_price / 1.1, 2);
            $ThueGTGT = round($DoanhSoChuaThue * 0.1, 2);
            $where = array();
            $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('sn = ?', $item['sn']);
            //  $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('invoice_number = ?',
            //    $item['invoice_number']);
            $invoice_price = $QMarket_invoice_price->fetchRow($where);

            // xem comment ở khai báo hàm
            $GhiChu = My_Sale_Order::getStockTetNote($item['sn']);

            if (empty($GhiChu))
            {
                $GhiChu = isset($po_cache[$item['po_id']]) ? $po_cache[$item['po_id']] : '';
                $GhiChu .= "\r\n" . $item['text'];
            }

            $sheet->stdOutSheetColumn('String', $i, $j++, $k);
            $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuMauHoaDon);
            $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuHoaDon);
            $sheet->stdOutSheetColumn('String', $i, $j++, $SoHoaDon);
            $sheet->stdOutSheetColumn('String', $i, $j++, $SoDonHang);
            $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MaNguoiMua);
            $sheet->stdOutSheetColumn('String', $i, $j++, $TenNguoiMua);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MST);
            $sheet->stdOutSheetColumn('String', $i, $j++, $MatHang);
            $sheet->stdOutSheetColumn('String', $i, $j++, $bvg);
            $sheet->stdOutSheetColumn('String', $i, $j++, $price_bvg ? number_format($price_bvg) :
                0);
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($giaTriDonHang));
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($DoanhSoChuaThue));
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($ThueGTGT));
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_after_vat']));
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_price']));
            $sheet->stdOutSheetColumn('String', $i, $j++, number_format($invoice_price['total_invoice_vat']));
            $sheet->stdOutSheetColumn('String', $i, $j++, $GhiChu);
            $sheet->stdOutSheetRowEnd();
            $i++;
            $k++;

            if (isset($discount) and $discount)
            {

                $sheet->stdOutSheetRowStart($i);
                $j = 1;
                $total_price = $price_bvg;
                $DoanhSoChuaThue = round($total_price / 1.1, 2);
                $ThueGTGT = round($DoanhSoChuaThue * 0.1, 2);
                $where = array();
                $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('sn = ?', $item['sn']);
                $where[] = $QMarket_invoice_price->getAdapter()->quoteInto('invoice_number = ?',
                    $discount_result['invoice_number']);
                $invoice_price = $QMarket_invoice_price->fetchRow($where);
                $invoice_price = $invoice_price ? $invoice_price : null;
                $sheet->stdOutSheetColumn('String', $i, $j++, $k);
                $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuMauHoaDon);
                $sheet->stdOutSheetColumn('String', $i, $j++, $KyHieuHoaDon);
                $sheet->stdOutSheetColumn('String', $i, $j++, $invoice_discount);
                $sheet->stdOutSheetColumn('String', $i, $j++, $SoDonHang);
                $sheet->stdOutSheetColumn('String', $i, $j++, $Ngay);
                $sheet->stdOutSheetColumn('String', $i, $j++, $MaNguoiMua);
                $sheet->stdOutSheetColumn('String', $i, $j++, $TenNguoiMua);
                $sheet->stdOutSheetColumn('String', $i, $j++, $MST);
                $sheet->stdOutSheetColumn('String', $i, $j++, $MatHang);
                $sheet->stdOutSheetColumn('String', $i, $j++, $bvg);
                $sheet->stdOutSheetColumn('String', $i, $j++, 0);
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($total_price));
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($DoanhSoChuaThue));
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($ThueGTGT));
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($total_price));
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($DoanhSoChuaThue));
                $sheet->stdOutSheetColumn('String', $i, $j++, '-' . number_format($ThueGTGT));
                $sheet->stdOutSheetColumn('String', $i, $j++, $GhiChu);
                $sheet->stdOutSheetRowEnd();
                $i++;
                $k++;
            }

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }

    private function _exportExcel4($data)
    {
         ////////////////////////////////////////////////////
        /////////////////// KHỞI TẠO ĐỂ XUẤT CSV
        ////////////////////////////////////////////////////
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Sell out List - IMEI - '.date('d-m-Y H-i-s').'.csv';
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
        // echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        // fputs($output, chr(239) . chr(187) . chr(191));

        ////////////////////////////////////////////////////
        /////////////////// TỔNG HỢP DỮ LIỆU
        ////////////////////////////////////////////////////

        $heads = array(
            'SALE ORDER NUMBER',
            'PRODUCT CATEGORY',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'IMEI',
            'WAREHOUSE',
            'RETAILER',
            'CODE',
            'REGION',
            'AREA',
            'OUT TIME',
            'ACTIVATED_DATE',
            'GIA HE THONG',
            'GIA THUC TE',
            'INVOICE NUMBER',
            'INVOICE DATE',
            'INVOICE SIGN',
            'ORDER TYPE',
            'RETURN'
        );

        fputcsv($output, $heads);

        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QDistributor   = new Application_Model_Distributor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QWarehouse     = new Application_Model_Warehouse();
        $QRegion        = new Application_Model_Region();
        $QArea          = new Application_Model_Area();
        $QMarket        = new Application_Model_Market();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();

        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $distributor_cache = $QDistributor->get_cache2();
        $good_categories   = $QGoodCategory->get_cache();
        $warehouses        = $QWarehouse->get_cache();
        $invoice_prefix    = $QInvoicePrefix->get_cache();

        $result = $db->query($data);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item)
        {
             if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            $row = array();
            $row[] = '="'.$temp_sn.'"';
            //$row[] = '="'.$item['sn'].'"';
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
            $row[] = $item['imei_sn'];
            $row[] = isset($warehouses[$item['warehouse_id']])? $warehouses[$item['warehouse_id']] : '';
            $row[] = isset($distributor_cache[ $item['d_id'] ]['title']) ? $distributor_cache[ $item['d_id'] ]['title'] : '';
            $row[] = isset($distributor_cache[ $item['d_id'] ]['code']) ? $distributor_cache[ $item['d_id'] ]['code'] : '';
            $row[] = isset($distributor_cache[ $item['d_id'] ]['district']) ? My_Region::getValue($distributor_cache[ $item['d_id'] ]['district'], My_Region::Area) : '';
            $row[] = isset($distributor_cache[ $item['d_id'] ]['district']) ? My_Region::getValue($distributor_cache[ $item['d_id'] ]['district'], My_Region::Province) : '';
            $row[] = $item['outmysql_time'];
            $row[] = $item['activated_date'];


            if ( isset($item['imei_sn']) ) {

                $price_product_relative = $price_export_relative = $invoice_time = 0;
                $order_type             = '';

                $price_product_relative = $item['price'];
                $price_export_relative  = intval($item['total'] / $item['num']) ? intval($item['total'] / $item['num']) : 0;
                switch ( $item['type'] ) {
                    case '1':
                        $order_type = 'Retailer';
                        break;
                    case '2':
                        $order_type = 'Demo';
                        break;
                    case '3':
                        $order_type = 'Staff';
                        break;
                    default:

                        break;
                }

            }

            $row[] = isset($price_product_relative) ? $price_product_relative : '';
            $row[] = isset($price_export_relative) ? $price_export_relative : 0;
            $row[] = isset($item['invoice_number']) ? $item['invoice_number'] : 'x';
            $row[] = isset($item['invoice_time']) ? $item['invoice_time'] : 'x';
            $row[] = isset($invoice_prefix[$item['invoice_sign']]) ? $invoice_prefix[$item['invoice_sign']] : 'x';
            $row[] = $order_type;
            $row[] = $item['return_sn'] ? $item['return_sn'] : '';

            fputcsv($output, $row);
            unset($item);
            unset($row);
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

    private function _export_target($target)
    {
        set_time_limit(0);
        error_reporting(0);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $tmp_name = md5(uniqid("", true) . microtime(true)) . '.csv';

        $uniqid = uniqid('', true);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if (!$userStorage)
            exit;

        $save_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'sales' .
            DIRECTORY_SEPARATOR . 'target' . DIRECTORY_SEPARATOR . $userStorage->id .
            DIRECTORY_SEPARATOR . $uniqid;

        if (!is_dir($save_dir))
            @mkdir($save_dir, 0777, true);

        $fullpath = $save_dir . DIRECTORY_SEPARATOR . $tmp_name;

        $output = fopen($fullpath, 'w');

        $heads = array(
            'No.',
            'Area',
            'Retailer ID',
            'Retailer Name',
            'Target',
            'Value',
            );

        fputcsv($output, $heads);

        $QArea = new Application_Model_Area();
        $areas = $QArea->get_cache();

        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->get_cache();

        $i = 1;

        foreach ($target as $value)
        {
            $row = array();
            $alpha = 'A';
            $row[] = $i++;
            $row[] = isset($areas[$value['area_id']]) ? $areas[$value['area_id']] : '';
            $row[] = $value['d_id'];
            $row[] = isset($distributors[$value['d_id']]) ? $distributors[$value['d_id']] :
                '';
            $row[] = $value['target'];
            $row[] = $value['total'];

            fputcsv($output, $row);
        }

        fclose($fullpath);

        $filename = 'Target - Distributor - ' . date('d-m-Y H-i-s') . ".csv";
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        header('X-Rack-Cache: miss');
        header('ETag: ' . hash("sha1", microtime(true) . uniqid()));
        header('Content-Type: application/csv; charset=utf-8');
        // header('Expires: 0');
        header('Cache-Control:  max-age=0, no-cache, no-store');
        header('Pragma: no-cache');
        header('Content-Length: ' . filesize($fullpath));

        ob_clean(); // discard any data in the output buffer (if possible)
        flush(); // flush headers (if possible)

        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM

        readfile($fullpath);

        exit;
    }

    public function createOrderAction()
    {

    }

    public function createOrderExcelAction()
    {
        $this->_helper->layout->disableLayout();

        $save_folder = 'importorder';
        $new_file_path = '';
        $requirement = array(
            'Size' => array('min' => 5, 'max' => 5000000),
            'Count' => array('min' => 1, 'max' => 1),
            'Extension' => array('xls', 'xlsx'),
            );

        try
        {
            set_time_limit(0);
            ini_set('memory_limit', -1);

            $file = My_File::get($save_folder, $requirement);

            if (!$file || !count($file))
                throw new Exception("Upload failed");

            $inputFileName = My_File::getDefaultDir() . DIRECTORY_SEPARATOR . $save_folder .
                DIRECTORY_SEPARATOR . $file['folder'] . DIRECTORY_SEPARATOR . $file['filename'];

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        }
        catch (exception $e)
        {
            $this->view->errors = $e->getMessage();
            return;
        }

        //read file
        include 'PHPExcel/IOFactory.php';

        //  Read your Excel workbook
        try
        {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        }
        catch (exception $e)
        {
            $this->view->errors = $e->getMessage();
            return;
        }

        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $db = Zend_Registry::get('db');
        $arrDistributorGood = array();
        $failed_list = array();
        $order_by_distributor = array();

        for ($row = 2; $row <= ($highestRow + 1); $row++)
        {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $rowData = $rowData[0];

            $old_distributor_id = isset($info['d_id']) ? $info['d_id'] : null;
            $old_date = isset($out_date_tmp) ? $out_date_tmp : null;

            if ((empty($rowData[0]) && empty($rowData[1])))
            {

                if (is_array($order_by_distributor) && count($order_by_distributor))
                {
                    try
                    {
                        $db->beginTransaction();
                        $this->_createGroupOrder($order_by_distributor);
                        $db->commit();
                    }
                    catch (exception $e)
                    {
                        $db->rollback();
                        $failed_list[$row] = $e->getMessage();
                    }

                    $order_by_distributor = array();
                }

                break;
            }

            //row data from excel
            $info = array(
                'd_id' => intval(trim($rowData[0])),
                'out_date' => '17/02/2015',
                'region' => trim($rowData[2]),
                'title' => trim($rowData[3]),
                'good_name' => trim($rowData[4]),
                'color_name' => trim($rowData[5]),
                'imei' => trim($rowData[6]),
                'price' => intval(trim($rowData[7])),
                'office' => trim($rowData[11]),
                'note' => trim($rowData[9]),
                );


            // nếu thấy ngày out mới thì tách đơn dù cùng dealer id
            if (!is_null($old_date) && isset($out_date_tmp) && $old_date != $out_date_tmp)
            {
                if (is_array($order_by_distributor) && count($order_by_distributor))
                {
                    try
                    {
                        $db->beginTransaction();
                        $this->_createGroupOrder($order_by_distributor);
                        $db->commit();
                    }
                    catch (exception $e)
                    {
                        $db->rollback();
                        $failed_list[$row] = $e->getMessage();
                    }

                    $order_by_distributor = array();
                }
            }

            // validate
            try
            {
                $QOffice = new Application_Model_Office();
                $office = $QOffice->get_warehouse($info['office']);

                if (!$office)
                    throw new Exception("Wrong warehouse", 3);

                $info['warehouse_id'] = $office;

                if ($info['good_name'] == 'PT5111-Blue')
                {
                    $info['good_id'] = 169;
                    $info['good_color'] = 28;
                    $info['price'] = 0;
                    $info['cat_id'] = ACCESS_CAT_ID;

                } else
                {
                    $this->_validateDataRow($info);
                }

            }
            catch (exception $e)
            {
                $failed_list[$row] = $e->getMessage();
                continue;
            }


            if (!is_null($old_distributor_id) && $info['d_id'] != $old_distributor_id)
            {
                // create Orders by group

                try
                {
                    $db->beginTransaction();
                    unset($info['out_date_tmp']);
                    $this->_createGroupOrder($order_by_distributor);
                    $db->commit();

                }
                catch (exception $e)
                {
                    $db->rollback();

                    $failed_list[$row] = $e->getMessage();
                }

                $order_by_distributor = array();
            }

            // group by Order SN
            $order_by_distributor[] = $info;

        } //end for

        $objPHPExcel_out = new PHPExcel();
        $objPHPExcel_out->createSheet();
        $objWorksheet_out = $objPHPExcel_out->getActiveSheet();
        $index = 1;

        $row = array(
            'ID Dealer',
            'Ngày',
            'KV',
            'Tên cửa hàng',
            'Mã hàng',
            'Màu',
            'IMEI',
            'Đơn giá',
            'Thành Tiền',
            'NOTE',
            'OUT Date',
            'Distributor',
            );

        $objWorksheet_out->fromArray($row, null, 'A' . $index++);

        foreach ($failed_list as $row => $reason)
        {
            //  Read a row of data into an array
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false);
            $rowData = $rowData[0];

            if (isset($rowData['imei']))
                $rowData['imei'] = '\'' . $rowData['imei'];

            $rowData[] = $reason;

            $objWorksheet_out->fromArray($rowData, null, 'A' . $index++);
        } // export error list

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel_out);
        $new_file_dir = My_File::getDefaultDir() . DIRECTORY_SEPARATOR . $save_folder .
            DIRECTORY_SEPARATOR . $file['folder'] . DIRECTORY_SEPARATOR . $file['filename'] .
            '-failed.xlsx';

        $objWriter->save($new_file_dir);

        $this->view->failed_list = $failed_list;
    } //end func create order

    /**
     * Kiểm tra dữ liệu
     * Dealer ID, KV, tên cửa hàng, mã hàng, màu, IMEI có khớp nhau, đúng thực tế hay không
     * kiểm tra IMEI có xuất cho kho office (distributor) này chưa
     * @param  array  $dataRow
     * @return array : code, message
     */
    private function _validateDataRow(array & $dataRow)
    {
        // kiểm tra distributor
        $QDistributor = new Application_Model_Distributor();
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $dataRow['d_id']);
        $distributor = $QDistributor->fetchRow($where);

        if (!$distributor)
            throw new Exception("Invalid Distributor " . $dataRow['d_id'], 1);

        // kiểm tra kho office
        if (!$dataRow['office'])
            throw new Exception("Empty Office", 2);

        // kiểm tra giá trị, định dạng ngày
        $date = DateTime::createFromFormat('d/m/Y', $dataRow['out_date']);

        if (!$date)
            throw new Exception("Invalid date format/value", 5);

        // kiểm tra tên sản phẩm, màu với imei khớp hay không
        $QImei = new Application_Model_Imei();
        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $dataRow['imei']);
        $imei_check = $QImei->fetchRow($where);

        if (!$imei_check)
            throw new Exception("Invalid IMEI " . $dataRow['imei'], 6);

        // kiểm tra product
        $QGood = new Application_Model_Good();
        $good_cache = $QGood->get_cache();

        if (!isset($good_cache[$imei_check['good_id']]) || $good_cache[$imei_check['good_id']] !=
            $dataRow['good_name'])
            throw new Exception("Wrong Product name " . $dataRow['good_name'] .
                ", check again", 7);

        $dataRow['good_id'] = $imei_check['good_id'];

        // kiểm tra color
        $QGoodColor = new Application_Model_GoodColor();
        $color_cache = $QGoodColor->get_cache();

        if (!isset($color_cache[$imei_check['good_color']]) || $color_cache[$imei_check['good_color']] !=
            $dataRow['color_name'])
            throw new Exception("Wrong Product color " . $dataRow['color_name'] .
                ", check again", 8);

        $dataRow['good_color'] = $imei_check['good_color'];

        $dataRow['cat_id'] = PHONE_CAT_ID;

        // kiểm tra kho office
        // $where = $QDistributor->getAdapter()->quoteInto('id = ?', $imei_check['distributor_id']);
        // $distributor_office = $QDistributor->fetchRow($where);

        // if (!$distributor_office || $distributor_office['title'] != $dataRow['office']) throw new Exception("Invalid Office Warehouse", 9);

        return true;
    }

    /**
     * [_createGroupOrder description]
     * @param  [type] $orders [description]
     * @return [type]         [description]
     */
    private function _createGroupOrder($orders)
    {
        $QMarket = new Application_Model_Market();
        $QImei = new Application_Model_Imei();
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $order_quantity = array(); // list đơn hàng để chèn vào bảng market
        $insert_orders = array(); // list đơn hàng để chèn vào bảng market
        $imei_order = array();
        $sn = date('YmdHis') . substr(microtime(), 2, 4);
        $user_id = $userStorage->id;
        $add_time = $shipping_yes_time = $outmysql_time = $pay_time = $price_time = date('Y-m-d H:i:s');
        $out_date = '';
        $d_id = 0;
        $imei_list = array();

        /**
         * tạo list order mới để chèn vào bảng market
         * group và đếm tổng số máy theo model, màu
         */
        foreach ($orders as $key => $order)
        {
            if (!isset($order_quantity[$order['good_id']][$order['good_color']]))
            {
                $out_date = DateTime::createFromFormat('d/m/Y', $order['out_date'])->format('Y-m-d');
                $d_id = $order['d_id'];
                $warehouse_id = $order['warehouse_id'];

                $order_quantity[$order['good_id']][$order['good_color']]['num'] = 0;
                $order_quantity[$order['good_id']][$order['good_color']]['cat_id'] = $order['cat_id'];
                $order_quantity[$order['good_id']][$order['good_color']]['price'] = $order['price'];
                $order_quantity[$order['good_id']][$order['good_color']]['note'] = $order['note'];
            }

            $order_quantity[$order['good_id']][$order['good_color']]['num']++;
            $imei_list[] = $order['imei'];

            if ($order['cat_id'] == PHONE_CAT_ID)
                $imei_order[$order['good_id']][$order['good_color']][] = $order['imei'];
        }

        foreach ($order_quantity as $_good_id => $_good_color)
        {
            foreach ($_good_color as $_color_id => $item)
            {
                $insert_orders[] = array(
                    'sn' => $sn,
                    'd_id' => $d_id,
                    'user_id' => $user_id,
                    'num' => $item['num'],
                    'add_time' => $add_time,
                    'price' => $item['price'],
                    'total' => $item['num'] * $item['price'],
                    'shipping_yes_time' => $shipping_yes_time,
                    'pay_time' => $pay_time,
                    'shipping_yes_id' => $user_id,
                    'pay_user' => $user_id,
                    'outmysql_time' => $out_date,
                    'outmysql_user' => $user_id,
                    'price_time' => $price_time,
                    'good_color' => $_color_id,
                    'good_id' => $_good_id,
                    'cat_id' => $item['cat_id'],
                    'text' => $item['note'] . '--Imported by tool',
                    'isbatch' => 1,
                    'status' => 1,
                    'life_time' => ORDER_TIMELIFE,
                    'type' => FOR_RETAILER,
                    'warehouse_id' => $warehouse_id,
                    'salesman' => $user_id,
                    );
            }
        }


        foreach ($insert_orders as $item)
        {
            //
            // chèn đơn
            $id = $QMarket->insert($item);

            if (!$id)
                throw new Exception("Cannot insert sales order", 1);

            if ($item['cat_id'] == ACCESS_CAT_ID)
                continue;

            // update num trên đơn cũ
            $imei_ = $imei_order[$item['good_id']][$item['good_color']];

            foreach ($imei_ as $imei__)
            {
                $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei__);
                $imei_check = $QImei->fetchRow($where);

                if (!$imei_check)
                    throw new Exception("Cannot find IMEI " . $imei__, 1);

                // trừ stock imei
                $where = array();
                $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $imei_check['sales_sn']);
                $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $item['good_id']);
                $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $item['good_color']);
                $market_check = $QMarket->fetchRow($where);

                if (!$market_check)
                    throw new Exception("Cannot find sales order " . $imei_check['sales_sn'], 1);

                $data = array('num' => ($market_check['num'] - 1));
                $QMarket->update($data, $where);

                if ($item['good_id'] == 168)
                { // N1 mini - có hàng tặng kèm
                    // trừ stock phụ kiện luôn
                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('sn = ?', $market_check['sn']);
                    $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', 169);
                    $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', 28);
                    $market_check = $QMarket->fetchRow($where);

                    if (!$market_check)
                        throw new Exception("Cannot find sales order " . $market_check['sn'], 1);

                    $data = array('num' => ($market_check['num'] - 1));
                    $QMarket->update($data, $where);

                } // end if ; trừ stock phụ kiện

            } // end foreach

            // update bảng imei theo đơn
            $where = $QImei->getAdapter()->quoteInto('imei_sn IN (?)', $imei_);

            $data = array(
                'out_date' => $out_date,
                'distributor_id' => $d_id,
                'sales_sn' => $sn,
                'out_price' => $item['price'],
                'price_date' => $item['outmysql_time'],
                'sales_id' => $id,
                );

            $QImei->update($data, $where);

            //
            //
        } // end foreach $orders array
    }

    /**
     * hoang.hien
     * kiểm tra nếu mà điện thoại chọn mức giảm giá ko có trong bảng giảm giá(good_sale_phone) thì xuất thông báo
     */
    public function checkSalePhoneAction()
    {
        $phone_id             = $this->getRequest()->getParam('phone_id');
        $sale_off_percent     = $this->getRequest()->getParam('sale_off_percent');

        $QCheckSale = new Application_Model_GoodSalePhone();
        $check_sale = $QCheckSale->checkSalePhone(intval($phone_id),intval($sale_off_percent));
        echo $check_sale;
        exit;
    }

    /**
     * [hoang.hien]Thêm view giảm giá
     * Tương ứng với 1 điện thoại thì giảm bao nhiêu %
     */

     public function saleAction(){
        $QSale = new Application_Model_GoodSale();
        $data = $QSale->fetchAll();

        $this->view->data = $data;
     }

     public function saveSaleAction(){

        $id = $this->getRequest()->getParam('id');
        $type = $this->getRequest()->getParam('type');
        $sale = $this->getRequest()->getParam('sale');

        $data = array(
            'type'             => $type,
            'sale'             => $sale
        );

        $QSale = new Application_Model_GoodSale();
        if($id){
            $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
            $QSale->update($data,$where);
            $this->_redirect(HOST . 'sales/edit-sale?id='.$id);
        }
        else{
            $QSale->insert($data);
            $this->_redirect(HOST . 'sales/sale');
        }

     }

     public function editSaleAction(){
        $id = $this->getRequest()->getParam('id');
        $QSale = new Application_Model_GoodSale();
        $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
        $data = $QSale->fetchRow($where);

        $this->view->data = $data;
     }

     public function salePhoneAction(){
        $QSale = new Application_Model_GoodSale();
        $dataSale = $QSale->fetchAll();

        $QGood = new Application_Model_Good();
        $dataGood = $QGood->fetchAll();

        $QSalePhone = new Application_Model_GoodSalePhone();
        $data = $QSalePhone->getAllSalePhone();

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->fetchAll();

        $this->view->dataSale = $dataSale;
        $this->view->dataGood = $dataGood;
        $this->view->data = $data;
     }

     public function deleteSaleAction(){
        $id = $this->getRequest()->getParam('id_sale');
        $QSale = new Application_Model_GoodSale();
        $where = $QSale->getAdapter()->quoteInto('id = ?', $id);
        $QSale->delete($where);
        echo "successful";
        exit;
     }

     public function changeSaleCheckboxAction(){
        $good_id = $this->getRequest()->getParam('good_id');
        $phone_sale_id = $this->getRequest()->getParam('phone_sale_id');
        $check = $this->getRequest()->getParam('check');

        if((int)$check == 1){
            $QSalePhone = new Application_Model_GoodSalePhone();
            $data = array(
                'good_id' => $good_id,
                'good_sale_id' => $phone_sale_id
            );
            $id = $QSalePhone->insert($data);

            echo "successful";
            exit;
        }
        elseif(((int)$check == 0)){
            $QSalePhone = new Application_Model_GoodSalePhone();
            $where[] = $QSalePhone->getAdapter()->quoteInto('good_id = ?', $good_id);
            $where[] = $QSalePhone->getAdapter()->quoteInto('good_sale_id = ?', $phone_sale_id);
            $QSalePhone->delete($where);

            echo "successful";
            exit;
        }
        elseif(((int)$check == 3)){
            $QSalePhone = new Application_Model_GoodSalePhone();
            $data = array(
                'good_id' => $good_id,
                'good_sale_id' => $phone_sale_id
            );
            $id = $QSalePhone->insert($data);
            $this->_redirect(HOST . 'sales/sale-phone');
        }

     }



     /**
     * [hoang.hien]Thêm view lô hàng (bán cho nhân viên)
     * Tương ứng với lô thì có các sản phẩm có giá khác nhau %
     */
     public function shipmentAction(){
        $QShipment = new Application_Model_GoodShipment();
        $dataShipment = $QShipment->fetchAll();
        $this->view->data = $dataShipment;
     }


     public function saveShipmentAction(){
        $id = $this->getRequest()->getParam('id');
        $name = $this->getRequest()->getParam('name');
        $number_shipment = $this->getRequest()->getParam('number_shipment');
        $content = $this->getRequest()->getParam('content');
        $active = $this->getRequest()->getParam('active');
        $check = $this->getRequest()->getParam('check');

        if((int)$check == 1){
            $QShipment = new Application_Model_GoodShipment();
            $data = array(
                'name'      => $name,
                'number'    => $number_shipment,
                'content'   => $content,
                'active'    => $active

            );
            $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
            $QShipment->update($data,$where);
            $this->_redirect(HOST . 'sales/shipment-edit?id='.$id);
        }
        elseif(((int)$check == 0)){
            $QSalePhone = new Application_Model_GoodSalePhone();
            $where[] = $QSalePhone->getAdapter()->quoteInto('good_id = ?', $good_id);
            $where[] = $QSalePhone->getAdapter()->quoteInto('good_sale_id = ?', $phone_sale_id);
            $QSalePhone->delete($where);

            echo "successful";
            exit;
        }
        elseif(((int)$check == 3)){
            $QShipment = new Application_Model_GoodShipment();
            $data = array(
                'name'      => $name,
                'number'    => $number_shipment,
                'content'   => $content,
                'active'    => $active

            );
            $id = $QShipment->insert($data);
            $this->_redirect(HOST . 'sales/shipment');
        }

     }

     public function shipmentEditAction(){
        $id = $this->getRequest()->getParam('id');
        $QShipment = new Application_Model_GoodShipment();
        $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
        $data = $QShipment->fetchRow($where);

        $this->view->data = $data;
     }

     public function deleteShippmentAction(){
        $id = $this->getRequest()->getParam('id_shipment');
        $QShipment = new Application_Model_GoodShipmentPhone();
        $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
        $QShipment->delete($where);
        echo "successful";
        exit;
     }

     public function createShipmentPhoneAction(){
        $id = $this->getRequest()->getParam('id');
        $QShipmentPhone = new Application_Model_GoodShipmentPhone();
        $where = $QShipmentPhone->getAdapter()->quoteInto('id = ?', $id);
        $data = $QShipmentPhone->fetchRow($where);

        $QShipment = new Application_Model_GoodShipment();
        $dataShipment = $QShipment->fetchAll();

        $QGood = new Application_Model_Good();
        $dataGood = $QGood->fetchAll();

        $this->view->dataShipment = $dataShipment;
        $this->view->dataGood = $dataGood;
        $this->view->data = $data;
     }


     public function saveShipmentPhoneAction(){

        $id = $this->getRequest()->getParam('id');
        $id_shipment = $this->getRequest()->getParam('id_shipment');
        $id_good = $this->getRequest()->getParam('id_good');
        $price = $this->getRequest()->getParam('price');
        $type  = $this->getRequest()->getParam('type');
        $data = array(
            'good_shipment_id' => $id_shipment,
            'good_id'          => $id_good,
            'price'            => $price,
            'type'             => intval($type)
        );

        $QShipmentPhone = new Application_Model_GoodShipmentPhone();
        if($id){
            $where = $QShipmentPhone->getAdapter()->quoteInto('id = ?', $id);
            $QShipmentPhone->update($data,$where);
            $this->_redirect(HOST . 'sales/create-shipment-phone?id='.$id);
        }
        else{
            $QShipmentPhone->insert($data);
            $this->_redirect(HOST . 'sales/shipment-details?id='.$id_shipment);
        }

     }

    public function shipmentDetailsAction(){
        $id = $this->getRequest()->getParam('id');
        $QShipment = new Application_Model_GoodShipment();
        $where = $QShipment->getAdapter()->quoteInto('id = ?', $id);
        $data = $QShipment->fetchRow($where);

        $QShipmentPhone = new Application_Model_GoodShipmentPhone();
        $dataPhone = $QShipmentPhone->getShipmentPhone($id);

        $QGood = new Application_Model_Good();

        foreach($dataPhone as $key=>$value){
            $whereGood[] = $QGood->getAdapter()->quoteInto("id NOT IN (?)", (int)$value['id_good']);
        }

        if($dataPhone){
            $dataGood = $QGood->fetchAll($whereGood);
        }
        else{
            $dataGood = $QGood->fetchAll();
        }

        $this->view->dataGood = $dataGood;

        $QShipmentAll = new Application_Model_GoodShipment();
        $dataShipmentAll = $QShipmentAll->fetchAll();

        $QGoodCategory = new Application_Model_GoodCategory();
        $this->view->good_categories = $QGoodCategory->fetchAll();

        $this->view->data = $data;
        $this->view->dataPhone = $dataPhone;
        $this->view->dataShipment = $dataShipmentAll;
     }

    public function checkPriceShipmentAction(){
        $shipment_id = $this->getRequest()->getParam('shipment');
        $good_id     = $this->getRequest()->getParam('good_id');
        $price       = $this->getRequest()->getParam('price');

        if ($shipment_id) {
            $QGoodShipmentPhone = new Application_Model_GoodShipmentPhone();
            $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_id = ?', $good_id);
            $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_shipment_id = ?', $shipment_id);
            $where_shipment[] = $QGoodShipmentPhone->getAdapter()->quoteInto('price = ?', $price);
            $data = $QGoodShipmentPhone->fetchRow($where_shipment);

            if($data){
                echo "true";
                exit;
            }
            else{
                echo "false";
                exit;
            }

        }
        exit;
     }

//Tanong Get SalesOrderNo 20160313 1155
    public function getSalesOrderNo()
    {
        $sales_order_sn='';
    try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL get_sales_order_sn()");
            $stmt->execute();
            $data = $stmt->fetchAll();
            $sales_order_sn= $data[0]['sales_order_sn'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sales_order_sn;
    }

    //Tanong Get SalesOrderNoRef 20160313 1155
    public function getSalesOrderNo_Ref($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('SO',".$sn.")");
            
            //$stmt = $db->prepare("CALL gen_running_no_ref('SO',201603121740314924)");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
            //print_r( $sn_ref);
            //die;

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }

    //Tanong Get SalesOrderNoRef 20160313 1155
    public function getReturnOrderNo_Ref($sn)
    {
    try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('RO',".$sn.")");
            //$stmt = $db->prepare("CALL gen_running_no_ref('SO',201603121740314924)");
            $stmt->execute();
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
    }

     //Tanong For Credit Note 20160311 1155
    public function saveAPICreditNoteAction($distributor_id,$sales_order,$creditnote_data)
    {
        $status_sn='';
        //return;
        //print_r($creditnote_data);die;
    try {
            $db = Zend_Registry::get('db');
            $item_row=0;
            if($creditnote_data=='no_discount')
            {
                $item_row=0;
            }else{
                $item_row = count($creditnote_data['ids_discount_creditnote']);
            }
            $creditnote_sn='';
            if($item_row>0)
            {
                $CreditNote_new = array();$CreditNote_old = array();
                for($i=0;$i<$item_row;$i++){
                    $creditnote_sn=$creditnote_data['ids_discount_creditnote'][$i];
                    $CreditNote_new[] = $creditnote_sn;
                }

                $QCreditNote = new Application_Model_CreditNote();
                $CreditNote = $QCreditNote->getCredit_Note_By_SalesOrder($sales_order,$distributor_id);
                foreach ($CreditNote as $cn){
                    $creditnote_sn = $cn['creditnote_sn'];
                    $CreditNote_old[] = $creditnote_sn;
                }

                $removed_ids = array_diff($CreditNote_old, $CreditNote_new);
                if ($removed_ids)
                {
                    $QCreditNoteTran = new Application_Model_CreditNoteTran();
                    $where   = array();
                    $where[] = $QCreditNoteTran->getAdapter()->quoteInto('sales_order =?', $sales_order);
                    $where[] = $QCreditNoteTran->getAdapter()->quoteInto('distributor_id =?', $distributor_id);
                    $where = $QCreditNoteTran->getAdapter()->quoteInto('creditnote_sn =?', $removed_ids);
                    $QCreditNoteTran->delete($where);
                }

                for($i=0;$i<$item_row;$i++){
                    $creditnote_sn=$creditnote_data['ids_discount_creditnote'][$i];
                    $use_total=$this->decimal_remove_comma($creditnote_data['price_use_discount_creditnote'][$i]);
                    $balance_total=$creditnote_data['price_balance_discount_creditnote'][$i];
                    $sales_order=$creditnote_data['sales_order'];
                    $user_id=$creditnote_data['user_id'];
                    $distributor_id=$creditnote_data['distributor_id'];

                    //$stmt = $db->prepare("CALL update_credit_note_sn('1105','CP590325-00001',100,'201603252045563500','10',0,0)");
                    
                    $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','".$creditnote_sn."',".$use_total.",'".$sales_order."','".$user_id."',0,0)");          
                    $stmt->execute();
                    /*
                    $db->query("CALL update_credit_note_sn('".$distributor_id."','".$creditnote_sn."',".$use_total.",'".$sales_order."','".$user_id."',0,0)");          
                    //$stmt->execute();
                    */
                }
            }else{
                /*    
                $db->query("CALL update_credit_note_sn('".$distributor_id."','no_discount',0,'".$sales_order."','10',0,0)");
                //$stmt->execute();
                */
                //$stmt = $db->prepare("CALL update_credit_note_sn('1105','CP590325-00001',100,'201603252045563500','10',0,0)");
                
                $stmt = $db->prepare("CALL update_credit_note_sn('".$distributor_id."','no_discount',0,'".$sales_order."','10',0,0)");
                $stmt->execute();
                
            }
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note, please try again!');
        }
        return $status_sn;
    }

    function decimal_remove_comma($priceFloat)
    {
        $price = str_replace(",","",$priceFloat);;
        return $price;
    }
     function cal_sale_off_percent($percent_sale_off,$price,$num,$price_total){
          
            if($percent_sale_off>0){
                $price_sale_off=$price_total/$num;
            }else{
                $price_sale_off = $price;
            }
            return $price_sale_off;
         }
          function ext_vat($num){
           return $num/1.07;
        }
        function format_number_4($num){
           return $this->decimal_remove_comma(number_format($num, 4));
        }
         function format_number_2($num){
           return $this->decimal_remove_comma(number_format($num, 2));
        }

    private function _exportExcelOutputVat($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Sell out List - OUTPUT VAT - '.date('d-m-Y H-i-s').'.csv';
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
            'INVOICE CREATE DATE',
            'INVOICE NUMBER',
            'COMPANY NAME',
            'MST NUMBER',
            'BRANCH TYPE',
            'BRANCH NUMBER',
            'TOTAL PRICE (EXCLUDE VAT)',
            'TOTAL VAT',
            'TOTAL PRICE (INCLUDE VAT)',
            'CANCEL OR NOT');

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();
        $result = $db->query($sql);
        $i = 2;

        foreach($result as $item) {
             $product_qty        = $item['num'];
            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = 'สำนักงานใหญ่'; }else { $branch_type = 'สาขา'; }
            if ($item['canceled']== 1){$cancel = 'Canceled';}else{$cancel = 'Not Cancel';}
            $row = array();
            $row[] = $item['invoice_time'];
            $row[] = $item['invoice_number'];
            $row[] = $distributor_cache[$item['d_id']]['unames'];
            $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
            $row[] = $branch_type;
            $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';
/*
            $sum_total = $item['sum_total'] ;
            $product_unit_price_4 = $this->format_number_4($sum_total);
            $product_amount_4 =$this->format_number_2($product_unit_price_4) * $product_qty; 
            
            $row[] = number_format($product_amount_4, 2);
            $row[] = number_format( ($product_amount_4 * 1.07) - str_replace(',' ,'', $product_amount_4), 2 );
            $row[] = number_format( ($product_amount_4 * 1.07) , 2); */
            /*
            $row[] = number_format($item['sum_total'] / 1.07, 2);
            $row[] = number_format( $item['sum_total'] - ( str_replace( ',' ,'', number_format( ($item['sum_total'] / 1.07), 2) ) ), 2 );
            $row[] = number_format($item['sum_total'], 2); 
           */

            $row[] = number_format( $item['sum_total'], 2);
            $row[] = number_format( ($item['sum_total'] * 1.07) - $item['sum_total'], 2 );
            $row[] = number_format( $item['sum_total'] * 1.07, 2); 

            $row[] = $cancel;
            fputcsv($output, $row);
            unset($item);
            unset($row);
        
}
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

    private function _exportExcelOrderStatus($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Order Status - '.date('d-m-Y H-i-s').'.csv';
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
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'TOTAL',
            'PAID DATETIME',
            'SHIPPING DATETIME',
            'STOCKOUT DATETIME',
            'MONEYCHECK DATETIME'
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item) {

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            $row = array();
            $row[] = '="'.$temp_sn.'"';
            $row[] = $item['invoice_number'];
            $row[] = $distributor_cache[$item['d_id']]['code'];
            $row[] = $distributor_cache[$item['d_id']]['title'];
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
            $row[] = number_format($item['sum_total'], 2);
            $row[] = $item['pay_time'];
            $row[] = $item['shipping_yes_time'];
            $row[] = $item['outmysql_time'];
            $row[] = $item['checkmoney_create_at'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

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

private function _exportExcelByProvince($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Sell out By Province - '.date('d-m-Y H-i-s').'.csv';
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
            /*
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'Company NAME',
            'MST NAME',
            'BRANCH TYPE',
            'BRANCH NUMBER',*/
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'PRODUCT TYPE',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'SALES QUANTITY',
            'SET OFF (%)',
            'UNIT PRICE EX',
            'TOTAL EX',
            'DELIVERY FEE',
            'UNIT PRICE',
            'TOTAL',
            /*
            'PAID DATETIME',
            'SHIPPING DATETIME',
            'WAREHOUSE',
            'STOCKOUT DATETIME',
            'STATUS',
            'SALE ORDER TIME',
            'CUSTOMER ORDER TYPE',
            'ORDER DESCRIPTION'*/
            );

        fputcsv($output, $heads);

        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QDistributor   = new Application_Model_Distributor();
        $QWarehouse     = new Application_Model_Warehouse();

        $goods             = $QGood->get_cache();
        $goodColors        = $QGoodColor->get_cache();
        $good_categories   = $QGoodCategory->get_cache();
        $distributor_cache = $QDistributor->get_cache2();
        $warehouses        = $QWarehouse->get_cache();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item) {

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            if ($item['status'] == 1) { $temp_status = 'Actived'; }
            else if ($item['status'] == 2) { $temp_status = 'Expired'; }
            else { $temp_status = 'Expired'; }

            if ($item['type'] == 1) //for retailer
                $type = 'For Retailer';
            elseif ($item['type'] == 2) //for demo
                $type = 'For Demo';
            elseif ($item['type'] == 3) //for staffs
                $type = 'For Staffs';
            elseif ($item['type'] == 4) //for lending
                $type = 'For Lending';

            if ($distributor_cache[$item['d_id']]['parent'] == 0) { $branch_type = 'สำนักงานใหญ่'; }
            else { $branch_type = 'สาขา'; }

            $row = array();
            /*
            $row[] = '="'.$temp_sn.'"';
            $row[] = $item['invoice_number'];
            $row[] = $distributor_cache[$item['d_id']]['code'];
            $row[] = $distributor_cache[$item['d_id']]['title'];
            $row[] = $distributor_cache[$item['d_id']]['unames'];
            $row[] = '="'.$distributor_cache[$item['d_id']]['mst_sn'].'"';
            $row[] = $branch_type;
            $row[] = '="'.$distributor_cache[$item['d_id']]['branch_no'].'"';*/
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
            $row[] = $good_categories[$item['cat_id']];
            $row[] = $goods[$item['good_id']];
            $row[] = $goodColors[$item['good_color']];
            $row[] = $item['sum_num'];

            $row[] = $item['sale_off_percent'];
            $row[] = number_format($item['sum_price'] / 1.07, 2);
            $row[] = number_format($item['sum_total'] / 1.07, 2);
            $row[] = !is_null($item['delivery_fee']) ? $item['delivery_fee'] : 0;

            $row[] = number_format($item['sum_price'], 2);
            $row[] = number_format($item['sum_total'], 2);
            /*
            $row[] = $item['pay_time'];
            $row[] = $item['shipping_yes_time'];
            $row[] = $warehouses[$item['warehouse_id']];
            $row[] = $item['outmysql_time'];
            $row[] = $temp_status;
            $row[] = $item['add_time'];
            $row[] = $type;
            $row[] = $item['text'];*/

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

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

    private function _exportExcelByModel($sql) {

        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'Export By Model - '.date('d-m-Y H-i-s').'.csv';
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
            'SALE ORDER NUMBER',
            'INVOICE NUMBER',
            'RETAILER ID',
            'RETAILER NAME',
            'AREA',
            'PROVINCE',
            'DISTRICT',
            'QUANTITY',
            'TOTAL',
            'PAID DATETIME',
            'SHIPPING DATETIME',
            'STOCKOUT DATETIME'
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $result = $db->query($sql);
        //print_r($result);die;
        $i = 2;

        foreach($result as $item) {

            if (is_null($item['sn_ref']) || $item['sn_ref'] == '') { $temp_sn = $item['sn']; } 
            else { $temp_sn = $item['sn_ref']; }

            $row = array();
            $row[] = '="'.$temp_sn.'"';
            $row[] = $item['invoice_number'];
            $row[] = $distributor_cache[$item['d_id']]['code'];
            $row[] = $distributor_cache[$item['d_id']]['title'];
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Area);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::Province);
            $row[] = My_Region::getValue($distributor_cache[$item['d_id']]['district'], My_Region::District);
            $row[] = number_format($item['sum_num'], 2);
            $row[] = number_format($item['sum_total'], 2);
            $row[] = $item['pay_time'];
            $row[] = $item['shipping_yes_time'];
            $row[] = $item['outmysql_time'];

            fputcsv($output, $row);
            unset($item);
            unset($row);
        }

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
