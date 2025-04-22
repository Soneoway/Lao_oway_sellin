<?php
class Application_Model_MarketProduct extends Zend_Db_Table_Abstract
{
    protected $_name = 'market_product';

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn']) {
            $select_fields = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'total_qty' => 'SUM(p.num)',
                'total_price' => 'SUM(p.total)',
                'invoice_number',
                'invoice_time',
                'warehouse_id',
                'type'
            );

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
                else
                    array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name), $select_fields)->group('p.sn');
            $select->group('p.good_id');
            $select->where('sn =  ?', $params['sn']);
        } else
            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                    'p.*'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array(
            'd.name',
            'd.title',
            'd.mst_sn',
            'd.unames'));

        if (isset($params['joint']) and $params['joint'])
            $select->where('joint =  ?', $params['joint']);

        if (isset($params['export']) and $params['export'])
            return $select->__toString();
        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchPaginationlistGood($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn']) {
            $select_fields = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'total_qty' => 'SUM(p.num)',
                'total_price' => 'SUM(p.total)',
                'invoice_number',
                'invoice_time',
                'd_id',
                'warehouse_id',
                'type');

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
            else
                array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name), $select_fields)->group('p.sn');
            $select->group('p.good_id');
            $select->where('sn =  ?', $params['sn']);
        } else
            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'p.*'));

        $select->joinLeft(array('b' => 'bvg_imei'), 'b.bvg_market_product_id = p.id', array(
            'invoice' => 'b.invoice_number',
            'b.date' ,
            'quantity' => 'COUNT(b.id)',
            'imei_price' => 'b.price'
        ));

        if (isset($params['joint']) and $params['joint'])
            $select->where('joint =  ?', $params['joint']);

        if (isset($params['sn']) and $params['sn'])
            $select->where('sn =  ?', $params['sn']);

        if ($limit)
            $select->limitPage($page, $limit);

        $select->group('b.invoice_number');
        $select->group('b.good_id');
        $select->group('b.price');

        $select->order('b.good_id');

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function fetchPaginationListAccessories($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn']) {
            $select_fields = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'total_qty' => 'SUM(p.num)',
                'total_price' => 'SUM(p.total)',
                'invoice_number',
                'invoice_time',
                'd_id',
                'warehouse_id',
                'type');

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
            else
                array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name), $select_fields)->group('p.sn');
            $select->group('p.good_id');
            $select->where('sn =  ?', $params['sn']);
        } else
            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'p.*'));

        $select->joinLeft(array('b' => 'bvg_accessories'), 'b.bvg_market_product_id = p.id', array(
            'invoice'  => 'b.invoice_number',
            'date'     => 'b.invoice_date' ,
            'quantity' => 'b.number',
            'total_price' => 'b.total'
        ));

        $select->joinLeft(array('g' => 'good'), 'p.good_id = g.id', array(
            'good_desc'  => 'g.desc_name'
        ));

        if (isset($params['joint']) and $params['joint'])
            $select->where('joint =  ?', $params['joint']);

        if (isset($params['sn']) and $params['sn'])
            $select->where('sn =  ?', $params['sn']);

        if ($limit)
            $select->limitPage($page, $limit);

        $select->order('b.good_id');

        $result = $db->fetchAll($select);


        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    //load tong so tien cua don hang bvg voi ma sn
    public function getPrice($sn)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => 'market_product'), array('total_price' =>
                'SUM(p.total)'))->where('p.sn = ?', $sn);

        $result = $db->fetchOne($select);
        if (!$result) {


         /**   //KIEM TRA doi voi truong hop la DIAMOND CLUB
            $QMarketDeduction = new Application_Model_MarketDeduction();
            $where = array();
            $where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ? ' , $sn);
            $result = $QMarketDeduction->fetchRow($where);

            switch ($result['joint_circular_id']) {
                case DIAMOND_CLUB_DISCOUNT: {
                    $select = $db->select()->from(array('p' => 'market_deduction'), array('total_price' =>
                        'SUM(p.price)'))->where('p.sn = ?', $sn);
                }

                default: {
                $select = $db->select()->from(array('p' => 'market_deduction'), array('total_price' =>
                    'SUM(p.price)'))->where('p.sn = ?', $sn);
                }
                break;
            } **/

            $select = $db->select()->from(array('p' => 'market_deduction'), array('total_price' =>
                'SUM(p.price)'))->where('p.sn = ?', $sn);


            $result = $db->fetchOne($select);
        }
        return $result;
    }

    /*
     *
     * */

    public function fetchJointAccessories($d_id, $joint , $good_id)
    {
        try {
            $db = Zend_Registry::get('db');

            if(empty($joint))
            {
                throw new exception('Please input joint circular');
            }

            $QJointCircular  = new Application_Model_JointCircular();
            $QBvgAccessories = new Application_Model_BvgAccessories();
            $QBvgProduct     = new Application_Model_BvgProduct();

            $db = Zend_Registry::get('db');


            if(empty($good_id))
            {
                $select = $db->select()->from(array('p' => 'bvg_product'), array('p.*'))->where('p.joint_id = ?', $joint);
                $select->join(array('g'=>'good'),'g.id = p.good_id',array('name' => 'g.name'));
                $result  = $db->fetchAll($select);


            }
            else{
                $select_accessories = $db->select()
                    ->from(array('p' => 'bvg_accessories'), array('total_price' =>
                        'SUM(p.total)' , 'total_num' => 'SUM(p.number)'))
                    ->where('p.joint_id = ?', $joint)
                    ->where('p.d_id = ?', $d_id)
                    ->where('p.good_id = ? ' , $good_id);


                $result  = $db->fetchRow($select_accessories);

            }



            return array(
                'code' => 1,
                'data' => $result);

        } catch (Exception $e){

            return array(
                'code' => -1,
                'message' => $e->getMessage(),
            );
        }
    }

    public function fetchJointDicountexist($d_id)
    {
        try {
            $db = Zend_Registry::get('db');

            $QJointCircular = new Application_Model_JointCircular();
            $QMarketDeduction = new Application_Model_MarketDeduction();
            $where = $QJointCircular->getAdapter()->quoteInto('type in (?)' , array(JOINT_TYPE_DISCOUNT, JOINT_TYPE_INCENTIVE));

            $listJoint = $QJointCircular->fetchALl($where);

            $result = array();

            $maxPrice = 0;

            foreach($listJoint as $k => $v)
            {
                $params = array(
                    'd_id' => $d_id,
                    'joint_circular_id' => $v['id'],
                    'type' => $v['type']
                );

                $maxPrice = $QMarketDeduction->getMaxPrice($params);

                if($maxPrice > 0 )
                {
                    $result[] = array(
                        'id' => $v['id'],
                        'name' => $v['name']
                    );
                }
            }



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


    public function getInvoice($sn)
        {
            $db = Zend_Registry::get('db');
            $select = $db->select()->from(array('p' => 'market_product'), array('invoice_number' =>
                    'invoice_number'))->where('p.sn = ?', $sn);

            $result = $db->fetchOne($select);
            if (!$result) {
                $select = $db->select()->from(array('p' => 'market_deduction'), array('invoice_number' =>
                        'invoice_number'))->where('p.sn = ?', $sn);
                $result = $db->fetchOne($select);
            }
            return $result;
        }

    public function getDiscount($sn)
    {
            $db = Zend_Registry::get('db');
            $select = $db->select()->from(array('p' => 'market_product'), array('total_price' =>
                    'SUM(p.total)'))->where('p.sn = ?', $sn);

            $result = $db->fetchOne($select);
            $type = 0;

            if (isset($result) and $result) {
                $type = DISCOUNT_BVG;
            }

            if (!$result) {
                $select = $db->select()->from(array('p' => 'market_deduction'), array('total_price' =>
                        'SUM(p.price)'))->where('p.sn = ?', $sn);
                $result = $db->fetchOne($select);

                if (isset($result) and $result) {
                    $type = DISCOUNT_DEDUCTION;
                } else
                    $tyle = DISCOUNT_NONE;

            }
            return $type;
    }

    public function getDetailBVG($sn)
    {

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array('p' => 'market_product'), array('joint_name' =>'j.name', 'price' => 'p.total','good_name'=>'g.desc','good_code'=>'g.name','p.num'))
                ->join(array('j' => 'joint_circular'),'p.joint = j.id', array())->where('p.sn = ?', $sn)
                ->join(array('g'=>'good'),'g.id = p.good_id',array())
                ;


            
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getDetailDiscount($sn)
    {
        /**
         * lấy chi tiết chiết khấu
         */
        $db = Zend_Registry::get('db');
        $sl = $db->select()->from(array('md' => 'market_deduction'), array('joint_name' =>
                'jc.name', 'price' => 'md.price'))->join(array('jc' => 'joint_circular'),
            'md.joint_circular_id = jc.id', array())->where('md.sn = ?', $sn);
        $result = $db->fetchAll($sl);
        return $result;
    }

    public function getPriceDiscount($params)
    {
        $db = Zend_Registry::get('db');

        if (isset($params['d_id']) and $params['d_id']) {
            $select_distributor = $db->select()->from(array('p' => 'bvg_imei'), array('total' =>
                    'SUM(p.price)', 'd_id'))->where("p.status = ?", 1) // approved
                ->where("p.bvg_payment_confirmed_at is null", null) // approved
                ->where("p.d_id = ?", $params['d_id']);


            if (isset($params['joint']) and $params['joint']) {
                $select_distributor->where($db->quoteInto("joint_circular_id = ?", $params['joint']));
                $select_market->where($db->quoteInto("joint = ?", $params['joint']));
            }


            $result = $db->fetchRow($select_distributor);

            $first_price = $result['total'];
            return $first_price;
        } else {
            return null;
        }

    }


}
