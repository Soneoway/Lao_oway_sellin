<?php
class Application_Model_BvgImei extends Zend_Db_Table_Abstract{
	protected $_name = 'bvg_imei';
    const BvgImei_APPROVED = 1;

    function save_accessories($params){
        try {
            $id = isset($params['id']) ? $params['id'] : null;
            $imei_sn        = isset($params['imei_sn']) ? $params['imei_sn'] : null;
            $d_id = isset($params['d_id']) ? $params['d_id'] : null;
            $joint_circular_id = isset($params['joint_circular_id']) ? $params['joint_circular_id'] : null;
            $price = isset($params['price']) ? $params['price'] : null;


            $sales_sn = isset($params['sales_sn']) ? $params['sales_sn'] : null;
            $invoice_number = isset($params['invoice_number']) ? $params['invoice_number'] : null;

            $invoice_date   = isset($params['invoice_date']) ? $params['invoice_date'] : null;

            $invoice_sign = isset($params['invoice_sign']) ? $params['invoice_sign'] : null;

            // co kiem tra imei info
            $check_imei_info = isset($params['check_imei_info']) ? $params['check_imei_info'] : null;

            // co kiem tra duplicate
            $check_duplicate = isset($params['check_duplicate']) ? $params['check_duplicate'] : null;

            // co kiem tra trang thai
            $check_status = isset($params['check_status']) ? $params['check_status'] : null;

            // co kiem tra dung dealer
            $check_dealer = isset($params['check_dealer']) ? $params['check_dealer'] : null;

            // co update thong tin invoice
            $get_invoice = isset($params['get_invoice']) ? $params['get_invoice'] : null;

            // co update price
            $get_price = isset($params['get_price']) ? $params['get_price'] : null;

            // co update out_price
            $out_price = isset($params['out_price']) ? $params['out_price'] : null;

            $total_price = isset($params['total_price']) ? $params['total_price'] : null;

            $remark = isset($params['remark']) ? $params['remark'] : null;

            // co update price
            $sn = isset($params['sn']) ? $params['sn'] : null;

            $cat_id = isset($params['cat_id']) ? $params['cat_id'] : null;

            $good_id = isset($params['good_id']) ? $params['good_id'] : null;

            $good_color = isset($params['good_color']) ? $params['good_color'] : null;

            $qty = isset($params['qty']) ? $params['qty'] : 0;

            $create_by = isset($params['create_by']) ? $params['create_by'] : null;

            $QBvgImei             = new Application_Model_BvgImei();

           

            if ($id){
                $data = array(
                    'imei_sn' => $imei_sn,
                    'joint_circular_id' => $joint_circular_id,
                    'd_id' => $d_id,
                    'price' => $price,
                    'sales_sn' => $sales_sn,
                    'invoice_number' => $invoice_number,
                    'invoice_sign' => $invoice_sign,
                    'good_id' => $good_id,
                    'good_color' => $good_color,
                    'out_price' => $out_price,
                    'invoice_price' => $out_price,
                    'total_price' => $total_price,
                    'cat_id' => $cat_id,
                    'create_by' => $create_by,
                    'remark' => $remark,
                    'invoice_date' => $invoice_date,
                    'qty' => $qty
                );

                $whereBvgImei       = $QBvgImei->getAdapter()->quoteInto('id = ?', $id);

                $QBvgImei->update($data, $whereBvgImei);
            } else {
                $data = array(
                    'imei_sn' => $imei_sn,
                    'joint_circular_id' => $joint_circular_id,
                    'd_id' => $d_id,
                    'price' => $price,
                    'sales_sn' => $sales_sn,
                    'invoice_number' => $invoice_number,
                    'invoice_sign' => $invoice_sign,
                    'good_id' => $good_id,
                    'good_color' => $good_color,
                    'date' => $invoice_date,
                    'out_price' => $out_price,
                    'invoice_price' => $out_price,
                    'total_price' => $total_price,
                    'cat_id' => $cat_id,
                    'create_by' => $create_by,
                    'remark' => $remark,
                    'sn' => $sn,
                    'qty' => $qty
                );
                
                if ($bvgImeiInfo){

                    $whereBvgImei         = array();
                    $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                    $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?', $joint_circular_id);
                    $QBvgImei->update($data, $whereBvgImei);

                } else {
                    $id = $QBvgImei->insert($data);
                }
            }

            return array(
                'code' => 0,
                'message' => 'Success',
            ); //success
        } catch (Exception $e){
            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }

    function save($params){
        try {
            $id = isset($params['id']) ? $params['id'] : null;
            $imei_sn        = isset($params['imei_sn']) ? $params['imei_sn'] : null;
            $d_id = isset($params['d_id']) ? $params['d_id'] : null;
            $joint_circular_id = isset($params['joint_circular_id']) ? $params['joint_circular_id'] : null;
            $price = isset($params['price']) ? $params['price'] : null;
            $total_price = isset($params['total_price']) ? $params['total_price'] : null;

            $sales_sn = isset($params['sales_sn']) ? $params['sales_sn'] : null;
            $invoice_number = isset($params['invoice_number']) ? $params['invoice_number'] : null;

            $invoice_date   = isset($params['invoice_date']) ? $params['invoice_date'] : null;

            $invoice_sign = isset($params['invoice_sign']) ? $params['invoice_sign'] : null;

            // co kiem tra imei info
            $check_imei_info = isset($params['check_imei_info']) ? $params['check_imei_info'] : null;

            // co kiem tra duplicate
            $check_duplicate = isset($params['check_duplicate']) ? $params['check_duplicate'] : null;

            // co kiem tra trang thai
            $check_status = isset($params['check_status']) ? $params['check_status'] : null;

            // co kiem tra dung dealer
            $check_dealer = isset($params['check_dealer']) ? $params['check_dealer'] : null;

            // co update thong tin invoice
            $get_invoice = isset($params['get_invoice']) ? $params['get_invoice'] : null;

            // co update price
            $get_price = isset($params['get_price']) ? $params['get_price'] : null;

            // co update out_price
            $out_price = isset($params['out_price']) ? $params['out_price'] : null;

            $remark = isset($params['remark']) ? $params['remark'] : null;

            // co update price
            $sn = isset($params['sn']) ? $params['sn'] : null;

            $seq = isset($params['seq']) ? $params['seq'] : null;

            $status = isset($params['status']) ? $params['status'] : null;

            $lot_sn = isset($params['lot_sn']) ? $params['lot_sn'] : null;

            $cat_id = isset($params['cat_id']) ? $params['cat_id'] : null;

            $create_by = isset($params['create_by']) ? $params['create_by'] : null;

            $create_date = isset($params['create_date']) ? $params['create_date'] : null;

            $finance_confirm_date = isset($params['finance_confirm_date']) ? $params['finance_confirm_date'] : null;

            if (!preg_match('/^[0-9]{15}$/', $imei_sn))
                return array(
                    'code' => 1,
                    'message' => 'Invalid IMEI',
                );

            if (!$d_id
                or !$joint_circular_id
            )
                return array(
                    'code' => 2,
                    'message' => 'Invalid Params',
                );

            // check imei co ton tai chua
            $QBvgImei             = new Application_Model_BvgImei();
            $whereBvgImei         = array();
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('sn = ?', $sn);
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?', $joint_circular_id);
            $bvgImeiInfo          = $QBvgImei->fetchRow($whereBvgImei);

            if ($check_duplicate){
                if (
                    !$id or
                    ($id and $bvgImeiInfo['id'] != $id)
                ){
                    if ($bvgImeiInfo)
                        return array(
                            'code'    => 3,
                            'message' => 'IMEI is existed',
                        );
                }
            }

            $QImei           = new Application_Model_Imei();
            $whereImei       = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
            $imeiInfo        = $QImei->fetchRow($whereImei);

            if ($check_imei_info){
                if (!$imeiInfo)
                    return array(
                        'code' => 4,
                        'message' => 'IMEI is not existed in System',
                    );
            }

            $good_id = isset($imeiInfo['good_id']) ? $imeiInfo['good_id'] : '';
            $good_color = isset($imeiInfo['good_color']) ? $imeiInfo['good_color'] : '';
            $sales_sn = isset($imeiInfo['sales_sn']) ? $imeiInfo['sales_sn'] : $sales_sn;

            if ($check_dealer){

                if ($imeiInfo['distributor_id'] != $d_id)
                    return array(
                        'code' => 5,
                        'message' => 'The dealer is invalid',
                    );

            }

            if ($get_price and empty($price)){
                $listBvgByProduct = isset($params['listBvgByProduct']) ? $params['listBvgByProduct'] : null;
                if ($listBvgByProduct){
                    $price = isset($listBvgByProduct[$good_id]) ? $listBvgByProduct[$good_id] : 0;
                } else {
                    $QBvgProduct         = new Application_Model_BvgProduct();
                    $whereBvgProduct     = array();
                    $whereBvgProduct[]   = $QBvgProduct->getAdapter()->quoteInto('joint_id = ?', $joint_circular_id);
                    $whereBvgProduct[]   = $QBvgProduct->getAdapter()->quoteInto('good_id = ?', $good_id);
                    $BvgProduct = $QBvgProduct->fetchRow($whereBvgProduct);

                    $price = isset($BvgProduct['price']) ? $BvgProduct['price'] : 0;
                }

                if (!$price){
                    return array(
                        'code' => 9,
                        'message' => 'The Joint Circular is invalid for IMEI: '.$imei_sn.'',
                    );
                }
            }

            if ($id){
                $data = array(
                    'lot_sn' => $lot_sn,
                    'imei_sn' => $imei_sn,
                    'joint_circular_id' => $joint_circular_id,
                    'd_id' => $d_id,
                    'seq' => $seq,
                    'price' => $price,
                    'total_price' => $total_price,
                    'qty' => 1,
                    'status' => $status,
                    'sales_sn' => $sales_sn,
                    'invoice_number' => $invoice_number,
                    'invoice_sign' => $invoice_sign,
                    'good_id' => $good_id,
                    'good_color' => $good_color,
                    'out_price' => $out_price,
                    'invoice_price' => $out_price,
                    'cat_id' => $cat_id,
                    'create_by' => $create_by,
                    'create_date' => $create_date,
                    'remark' => $remark,
                    'finance_confirm_date' => $finance_confirm_date,
                    'date' => $invoice_date
                );

                $whereBvgImei       = $QBvgImei->getAdapter()->quoteInto('id = ?', $id);

                $QBvgImei->update($data, $whereBvgImei);
            } else {
                $data = array(
                    'lot_sn' => $lot_sn,
                    'imei_sn' => $imei_sn,
                    'seq' => $seq,
                    'joint_circular_id' => $joint_circular_id,
                    'd_id' => $d_id,
                    'price' => $price,
                    'status' => $status,
                    'sales_sn' => $sales_sn,
                    'invoice_number' => $invoice_number,
                    'invoice_sign' => $invoice_sign,
                    'good_id' => $good_id,
                    'good_color' => $good_color,
                    'date' => $invoice_date,
                    'out_price' => $out_price,
                    'invoice_price' => $out_price,
                    'cat_id' => $cat_id,
                    'create_by' => $create_by,
                    'create_date' => $create_date,
                    'finance_confirm_date' => $finance_confirm_date,
                    'remark' => $remark,
                    'sn' => $sn
                );

                if ($bvgImeiInfo){

                    $whereBvgImei         = array();
                    $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('imei_sn = ?', $imei_sn);
                    $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('sn = ?', $sn);
                    $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?', $joint_circular_id);
                    $QBvgImei->update($data, $whereBvgImei);

                } else {
                    $id = $QBvgImei->insert($data);
                }
            }

            return array(
                'code' => 0,
                'message' => 'Success',
            ); //success
        } catch (Exception $e){
            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        if (isset($params['not_get_total']) and $params['not_get_total'])
            $get = array('p.id');
        else
            $get = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                $get);

        if (isset($params['imei_sn']) and $params['imei_sn'])
            $select->where('p.imei_sn = ?', $params['imei_sn']);

        if (isset($params['status']) and $params['status'] != -1)
            $select->where('p.status = ?', $params['status']);

        if (isset($params['sales_sn']) and $params['sales_sn'])
            $select->where('p.sales_sn = ?', $params['sales_sn']);

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('p.d_id = ?', $params['d_id']);

        if (isset($params['joint_circular_id']) and $params['joint_circular_id'])
            $select->where('p.joint_circular_id = ?', $params['joint_circular_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if(isset($params['price']) and $params['price'])
            $select->where('p.price = ?' , $params['price']);

        if (isset($params['bvg_market_product_id']) and $params['bvg_market_product_id'])
            $select->where('p.bvg_market_product_id = ?', $params['bvg_market_product_id']);

        if (isset($params['market_product']) and $params['market_product'])
        {
            $select->joinLeft(array('m'=>'market_product'), 'p.bvg_market_product_id = m.id', array('sales_sn' => 'm.sn','invoice' => 'm.invoice_number' , 'm.invoice_time' ,'m.invoice_sign'));
        }


        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str = 'p.`'.$params['sort'] . '` ' . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }


        if($limit)
            $select->limitPage($page, $limit);

        if(isset($params['print']) and $params['print'])
        {
            $select->group('p.invoice_number');
        }

        $result = $db->fetchAll($select);

        if (isset($params['not_get_total']) and $params['not_get_total'])
            return $result;

        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    
    function getImei($params)
    {
         try {
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('p.*')));

            if (isset($params['d_id']) and $params['d_id'])
                $select->where('p.d_id = ?', $params['d_id']);

            if (isset($params['joint_circular_id']) and $params['joint_circular_id'])
                $select->where('p.joint_circular_id = ?', $params['joint_circular_id']);

            if (isset($params['good_id']) and $params['good_id'])
                $select->where('p.good_id = ?', $params['good_id']);

            if (isset($params['good_color']) and $params['good_color'])
                $select->where('p.good_color = ?', $params['good_color']);
            
            if(isset($params['num']) and $params['num'])
                 $select->limit($params['num']);
            
            $select->where('p.status = ?', self::BvgImei_APPROVED);
            
            $select->where('p.bvg_payment_confirmed_at IS NULL ');
          
            $result = $db->fetchAll($select);
            
            return ($result ? $result : 0);
        } catch (Exception $e){
            return -1;
        }
    }

    
    function getBalance($params){
        try {
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('sum(p.price)')));

            if (isset($params['d_id']) and $params['d_id'])
                $select->where('p.d_id = ?', $params['d_id']);

            if (isset($params['joint_circular_id']) and $params['joint_circular_id'])
                $select->where('p.joint_circular_id = ?', $params['joint_circular_id']);

            if (isset($params['good_id']) and $params['good_id'])
                $select->where('p.good_id = ?', $params['good_id']);

            if (isset($params['good_color']) and $params['good_color'])
                $select->where('p.good_color = ?', $params['good_color']);

            $select->where('p.status = ?', self::BvgImei_APPROVED);
            $select->where('p.bvg_payment_confirmed_at IS NULL AND p.bvg_payment_confirmed_at = \'\' AND p.bvg_payment_confirmed_at = 0');

            $result = $db->fetchOne($select);
            
            return ($result ? $result : 0);
        } catch (Exception $e){
            return -1;
        }
    }

    function fetchJointexist($d_id)
    {
        try {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('p.joint_circular_id')));

            $select->joinLeft(array('j'=>'joint_circular'), 'p.joint_circular_id = j.id', array('j.id', 'j.name'));

            $select->where('d_id = ?' , $d_id);

            $select->where('bvg_payment_confirmed_at IS NULL or bvg_payment_confirmed_at = \'\' or bvg_payment_confirmed_at = 0', null);

            $select->group('joint_circular_id');

            $result = $db->fetchAll($select);

            if(empty($result))
                throw new exception ('don\'t have imei');

            return array(
                'code' => 1,
                'data' => $result,
            );

        } catch (Exception $e){
            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }


    function approve($ids){
        try {
            $QBvgImei = new Application_Model_BvgImei();
            $whereBvgImei       = array();
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('id IN (?)', $ids);

            // check co status nao approved
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('status = ? OR bvg_payment_confirmed_at IS NOT NULL or bvg_payment_confirmed_at <> \'\' or bvg_payment_confirmed_at <> 0', self::BvgImei_APPROVED);
            $BvgImei = $QBvgImei->fetchRow($whereBvgImei);
            if ($BvgImei){
                return array(
                    'code' => 1,
                    'message' => 'Status was approved OR was paid',
                );
            }


            $whereBvgImei       = array();
            $whereBvgImei[]       = $QBvgImei->getAdapter()->quoteInto('id IN (?)', $ids);
            $QBvgImei->update(array('status' => self::BvgImei_APPROVED), $whereBvgImei);

            return array(
                'code' => 0,
            );
        } catch (Exception $e){
            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }


    public function getProtection_Price_List($joint_circular_id,$distributor_id)
    {
        try{
             $db = Zend_Registry::get('db');
$select = $db->select()
->from(array('jc' => 'joint_circular'),array('jc.id','jc.joint_circular_sn','jc.name','jc.note'))
->joinInner(array('im'=>'bvg_imei'), 'jc.id=im.joint_circular_id', array('im.imei_sn','im.date AS import_date','im.price','im.sales_sn','im.date AS import_date','im.invoice_number','im.bvg_payment_confirmed_at'))
->joinLeft(array('d'=>'distributor'), 'im.d_id=d.id', array('d.title','d.add_tax','d.mst_sn'))
->joinLeft(array('g'=>'good'), 'im.good_id=g.id', array('g.name AS product_code','g.desc AS product_detail'))
->joinLeft(array('gc'=>'good_color'), 'im.good_id=gc.id', array('gc.name AS color_name'))
->where('jc.id= ?', $joint_circular_id)
->where('im.d_id=?', $distributor_id);
//echo $select;die; 
$result = $db->fetchAll($select);
$total = $db->fetchOne("select FOUND_ROWS()");
            
          return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

    public function getBvgFromTools($d_id, $imei){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('*'));

        $select->joinLeft(array('m'=>'market'), 'm.sn = p.sales_sn and m.good_id = p.good_id and m.good_color = p.good_color', array('m.sn_ref'));

        $select->joinLeft(array('j'=>'joint_circular'), 'p.joint_circular_id = j.id', array('j.id', 'j.name'));

        $select->where('p.imei_sn in (?)', $imei);
        $select->where('p.d_id = ?' , $d_id);
        $select->where('p.status = ?' , 1);

        $select->order(['p.imei_sn ASC','p.create_date DESC']);

        $result = $db->fetchAll($select);

        return $result;
    }

}