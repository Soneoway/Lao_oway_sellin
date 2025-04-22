<?php 
/**
* Invoice finance
*/
class My_Finance_InvoiceNumber
{
    //so luong imei tren 1 hoa don
    const limited_invoice = 22;
    const total_imei_in_row = 24;
    const total_product_per_row = 12;

    public static $no = 0;
    public static $product_per_row = 0;
    public static $imei_per_row = 0;

    //call store procedure
    public static  function split($params)
    {
        $warehouse_id = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;
        $sn = isset($params['sn']) ? $params['sn'] : null;
        $total_row = isset($params['total_row']) ? $params['total_row'] : self::total_product_per_row;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $QInvoicePrefix = new Application_Model_InvoicePrefix();

        $db = Zend_Registry::get('db');
        $result_db = array('code' => 0);

        try
        {

            $invoice_sign   = $QInvoicePrefix->getPrefix($warehouse_id);
            // lấy số hóa đơn mới

            if(!$invoice_sign)
                throw new exception('invalid invoice number');



            $paramsSP = array(
                /* user id  */ $user_id ? $user_id : $userStorage->id,
                /* invoice sign */ $invoice_sign['id'],
                /* sn */ $sn,
                /* total row */ $total_row
            );

          //  var_dump($paramsSP);exit;


            $db->getConnection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
            $stmt = $db->query("CALL sp_insert_invoice_data(?, ?, ?, ?)", $paramsSP);
            $result = $stmt->fetchAll();
            unset($stmt);

            if($result[0] == 0)
                throw new exception('Error when split order');
        }
        catch(exception $e)
        {
            $result_db['code'] = -1;
            $result_db['message'] = $e->getMessage();
        }

        return $result_db;
    }

    public static function pushRow($params)
    {
        $imeis = isset($params['imeis']) ? $params['imeis'] : null;
        $sn = isset($params['sn']) ? $params['sn'] : null;
        $no = isset($params['no']) ? $params['no'] : null;
        $invoice_number = isset($params['invoice_number']) ? $params['invoice_number'] : null;
        $invoice_sign = isset($params['invoice_sign']) ? $params['invoice_sign'] : null;
        $good_id = isset($params['good_id']) ? $params['good_id'] : null;
        $good_color = isset($params['good_color']) ? $params['good_color'] : null;
        $market_id = isset($params['market_id']) ? $params['market_id'] : null;
        $price = isset($params['price']) ? $params['price'] : 0;
        $quantity = isset($params['quantity']) ? $params['quantity'] : 0;
        $total = isset($params['total']) ? $params['total'] : 0;
        $pending = isset($params['pending']) ? $params['pending'] : 0;
        $cat_id = isset($params['cat_id']) ? $params['cat_id'] : null;
        $warehouse_id = isset($params['warehouse_id']) ? $params['warehouse_id'] : null;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id = $userStorage->id;
        $currentTime = date('Y-m-d h:i:s');
        $currentTimeUnix = time();


        try {
            // kiểm tra xem hoa don nay da in chua , co con thieu hay k

            $QMarketInvoice = new Application_Model_MarketInvoice();
            $QMakretInvoiceImei = new Application_Model_MarketInvoiceImei();
            $QInvoiceNumber = new Application_Model_InvoiceNumber();
            $QInvoicePrefix = new Application_Model_InvoicePrefix();

            $data_push = array_filter(array(
                'sn' => $sn,
                'created_by' => $user_id,
                'created_at' => $currentTime,
                'created_at_unix' => $currentTimeUnix,
                'invoice_number' => $invoice_number,
                'invoice_sign' => $invoice_sign,
                'good_id' => $good_id,
                'good_color' => $good_color,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $total,
                'market_id' => $market_id,
                'no' => My_Finance_InvoiceNumber::$no,
                'pending' => $pending
            ));

            if ($quantity > self::total_imei_in_row)
                $data_push['quantity'] = self::total_imei_in_row;

            if ($cat_id and in_array($cat_id, array(PHONE_CAT_ID, DIGITAL_CAT_ID)) and empty($imeis))
                throw new exception('invalid imeis');

            $id = $QMarketInvoice->insert($data_push);

            if (intval($quantity - self::total_imei_in_row) > 0) {

                $paramsInvoice = array(
                    'warehouse_id'   => $warehouse_id,
                    'sn'             => $sn,
                    'invoice_prefix' => null
                );

                $invoice_number = $QInvoiceNumber->getLastIdInvoice($paramsInvoice);
                $invoice_sign   = $QInvoicePrefix->getPrefix($warehouse_id);

                $params['quantity']       = $quantity - self::total_imei_in_row;
                $params['no']             = ++My_Finance_InvoiceNumber::$no;
                $params['imeis']          = array_splice($imeis, 0, $params['quantity']);
                $params['invoice_number'] = $invoice_number ? $invoice_number : 0;
                $params['invoice_sign']   = $invoice_sign ? $invoice_sign : 0;
                self::pushRow($params);
            }


            if (is_array($imeis) and $imeis[0]) {
                // thêm imei vào bảng
                foreach ($imeis as $k => $imei) {
                    $data_imei = array_filter(array(
                        'market_invoice_id' => $id,
                        'imei_sn' => $imei,
                        'good_id' => $good_id,
                        'good_color' => $good_color
                    ));


                    $QMakretInvoiceImei->insert($data_imei);
                }
            }

            return array(
                'code' => 0,
                'message' => 'done'
            );

        } catch (exception $e) {
            return array(
                'code' => -1,
                'message' => $e->getMessage()
            );
        }


    }

    public static function formatInvoice($invoice_number)
    {
        $result = sprintf('%07d', intval($invoice_number));
        return $result;
    }

    public static function devideSn($params)
    {
        $imei            = isset($params['imei']) ? $params['imei'] : null;
        $sn              = isset($params['sn'])   ? $params['sn'] : null;
        $no              = isset($params['no'])   ? $params['no'] : null;
        $invoice_number  = isset($params['invoice_number']) ? $params['invoice_number'] : null;
        $invoice_sign    = isset($params['invoice_sign']) ? $params['invoice_sign'] : null;
        $order_has_phone = isset($params['order_has_phone']) ? $params['order_has_phone'] : null;
        $printed_again   = isset($params['printed_again']) ? $params['printed_again'] : null;

        $QMarketInvoice  = new Application_Model_MarketInvoice();
        $QInvoiceNumber  = new Application_Model_InvoiceNumber();
        $QInvoicePrefix  = new Application_Model_InvoicePrefix();

        $userStorage     = Zend_Auth::getInstance()->getStorage()->read();
        $currenttime     = date('Y-m-d h:i:s');
        $QGood           = new Application_Model_Good();
        $QGoodColor      = new Application_Model_GoodColor();
        $QMarket         = new Application_Model_Market();
        $QImei           = new Application_Model_Imei();
        $QDigitalSn      = new Application_Model_DigitalSn();
        $time            = time();

        try
        {
            $db = Zend_Registry::get('db');

            $db->beginTransaction();

            if(empty($sn))
                throw new exception('invalid sn');



            // kiểm tra sn

            $whereMarketSN   = array();
            $whereMarketSN[] = $QMarketInvoice->getAdapter()->quoteInto('sn = ?' , $sn);

            $masketSn = $QMarketInvoice->fetchRow($whereMarketSN);

           if($masketSn and $printed_again)
              throw new exception('This invoice is printed');

            $whereMarket   = array();
            $whereMarket[] = $QMarket->getAdapter()->quoteInto('sn = ? ' , $sn);
            $whereMarket[] = $QMarket->getAdapter()->quoteInto('status = ? ' , 1);
            $markets       = $QMarket->fetchAll($whereMarket);

            if(empty($markets))
                throw new exception('invalid markets');

            if ($order_has_phone) {
                $imei_list = $params['imei'];
                $imei_list = trim($imei_list);
                $imei_list = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $imei_list);
                $imei_list = explode("\n", $imei_list);
                $imei_list = array_filter($imei_list);

                if (!($imei_list and is_array($imei_list)))
                    throw new exception('No IMEI Input.');
            }


            $total_imei_invoice = $total_row_imei = $total_row = $total_product_per_row = 0;
            $markets_invoice    =   $imeis    =    $digital_sns    =  $a_order_phones_group = array();



            foreach($markets as $k => $items)
            {
                $good_id            = $items['good_id'];
                $good_color         = $items['good_color'];

                $where                     = $QImei->getAdapter()->quoteInto('sales_id = ?', $items['id']);
                $imeis[$items['id']]       = $QImei->fetchAll($where);

                $where                     = $QDigitalSn->getAdapter()->quoteInto('sales_id = ?', $items['id']);
                $digital_sns[$items['id']] = $QDigitalSn->fetchAll($where);

                $whereMarketInvoice      = array();
                $whereMarketInvoice[]    = $QMarketInvoice->getAdapter()->quoteInto('sn = ?' , $sn);
                $whereMarketInvoice[]    = $QMarketInvoice->getAdapter()->quoteInto('good_id = ?', $good_id);
                $whereMarketInvoice[]    = $QMarketInvoice->getAdapter()->quoteInto('good_color = ?', $good_color);
                $whereMarketInvoice[]    = $QMarketInvoice->getAdapter()->quoteInto('pending != 0', null);

                $markets_invoice_pending = $QMarketInvoice->fetchRow($whereMarketInvoice);

                if($markets_invoice_pending)
                    $markets_invoice_pending = $markets_invoice_pending->toArray();

                $paramsData = array_filter(array(

                ));

                if(My_Finance_InvoiceNumber::$imei_per_row > self::total_imei_in_row || My_Finance_InvoiceNumber::total_product_per_row > self::total_product_per_row)
                {
                    // qua hd mới
                    My_Finance_InvoiceNumber::$no ++;
                    My_Finance_InvoiceNumber::$imei_per_row        = 0;
                    My_Finance_InvoiceNumber::$product_per_row = 0;
                }

                if($items['cat_id'] == PHONE_CAT_ID)
                {
                    if (isset($a_order_phones_group[$items['good_id']][$items['good_color']]) and $a_order_phones_group[$items['good_id']][$items['good_color']])
                        $a_order_phones_group[$items['good_id']][$items['good_color']] += $items['num'];
                    else
                        $a_order_phones_group[$items['good_id']][$items['good_color']] = $items['num'];

                    $order_has_phone = true;
                }

                if($a_order_phones_group[$items['good_id']][$items['good_color']] + $total_row_imei <= self::total_imei_in_row)
                {
                  
                    $invoice_number = $QInvoiceNumber->getLastIdInvoice(array(
                        'warehouse_id'   => $items['warehouse_id'],
                        'sn'             => $sn,
                        'invoice_prefix' => null
                    ));

                    $invoice_sign   = $QInvoicePrefix->getPrefix($items['warehouse_id']);
                    // lấy số hóa đơn mới

                    if(!$invoice_sign && !$invoice_number)
                        throw new exception('invalid invoice number');

                    if($imeis[$items['id']])
                        $imeis = self::rowArray($imeis[$items['id']]);

                    $paramsData = array_filter(array(
                        'sn'             => $sn,
                        'imeis'          => $imei[$items['id']],
                        'no'             => My_Finance_InvoiceNumber::$no,
                        'invoice_number' => $invoice_number,
                        'invoice_sign'   => $invoice_sign['id'],
                        'good_id'        => $items['good_id'],
                        'good_color'     => $items['good_color'],
                        'price'          => $items['price'] ? $items['price'] : 0,
                        'quantity'       => $items['num'] ? $items['num'] : 0,
                        'total'          => $items['total'] ? $items['total'] : 0,
                        'imeis'          => $imeis ? $imeis : array(),
                        'warehouse_id'   => $items['warehouse_id'] ? $items['warehouse_id'] : 0
                    ));


                    $result = self::pushRow($paramsData);

                    if($result['code'] != 0)
                        throw new exception($result['message']);

                    $total_row ++;
                    if(in_array($items['cat_id'] , array(PHONE_CAT_ID , DIGITAL_CAT_ID)))
                    {
                        My_Finance_InvoiceNumber::$imei_per_row += count($imeis);
                        My_Finance_InvoiceNumber::$product_per_row ++;
                    }
                    else
                    {
                        //phu kien thi chi dem so dong
                        My_Finance_InvoiceNumber::$product_per_row ++;
                    }

                }


                else
                {
                    // thêm bổ sung vô đơn hàng sau.

                   // $just_print_for_enough  = intval($a_order_phones_group[$items['good_id']][$items['good_color']] + $total_row_imei) - self::total_imei_in_row;
                   // $pending                = $items['num'] - $just_print_for_enough;

                    $paramsInvoice = array(
                        'warehouse_id'   => $items['warehouse_id'],
                        'sn'             => $sn,
                        'invoice_prefix' => null
                    );

                    $invoice_number = $QInvoiceNumber->getLastIdInvoice($paramsInvoice);

                    $invoice_sign   = $QInvoicePrefix->getPrefix($items['warehouse_id']);

                    if($imeis[$items['id']])
                        $imeis = self::rowArray($imeis[$items['id']]);

                    $paramsData = array_filter(array(
                        'sn'             => $sn,
                        'imeis'          => $imei[$items['id']],
                        'no'             => ++My_Finance_InvoiceNumber::$no,
                        'invoice_number' => $invoice_number,
                        'invoice_sign'   => $invoice_sign['id'],
                        'good_id'        => $items['good_id'],
                        'good_color'     => $items['good_color'],
                        'price'          => $items['price'] ? $items['price'] : 0,
                        'quantity'       => $a_order_phones_group[$items['good_id']][$items['good_color']] ? $a_order_phones_group[$items['good_id']][$items['good_color']] : 0,
                        'total'          => $items['total'] ? $items['total'] : 0,
                        'imeis'          => $imeis ? $imeis : array(),
                        'warehouse_id'   => $items['warehouse_id']
                    ));

                    // qua hd mới

                    My_Finance_InvoiceNumber::$product_per_row     = 0;

                    My_Finance_InvoiceNumber::$imei_per_row = 1;



                    if(!$invoice_sign && !$invoice_number)
                        throw new exception('invalid invoice number');


                    $result = self::pushRow($paramsData);

                    if($result['code'] != 0)
                        throw new exception($result['message']);

                    // bo peding cua record do

                    $wherePending = array();
                    $wherePending[]    = $QMarketInvoice->getAdapter()->quoteInto('sn = ?' , $sn);
                    $wherePending[]    = $QMarketInvoice->getAdapter()->quoteInto('good_id = ?', $good_id);
                    $wherePending[]    = $QMarketInvoice->getAdapter()->quoteInto('good_color = ?', $good_color);
                    $wherePending[]    = $QMarketInvoice->getAdapter()->quoteInto('pending <> 0', null);

                    $data = array(
                        'pending' => 0
                    );

                    $QMarketInvoice->update($data, $wherePending);

                }

                $markets_invoice['market_id'] = $items;
                $total_imei_invoice           += intval($items['num']);

            }

            // kiểm tra xem đã in chưa có pending hay không

            $db->commit();

            return array(
                'code'    => 0,
                'message' => 'Done'
            );
        }
        catch(exception $e)
        {
            $db->rollback();

            return array(
                'code' => -1,
                'message' => $e->getMessage()
            );
        }
    }

    public static function rowArray($imes) {
        $rowArray = array();
        foreach ($imes as $row) {
            $rowArray[] = $row['imei_sn'];
        }

        return $rowArray;
    }

}