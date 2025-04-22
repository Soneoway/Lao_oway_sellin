<?php
class Application_Model_Po extends Zend_Db_Table_Abstract
{
	protected $_name = 'purchase_order';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select();


        if (isset($params['group_sn']) && $params['group_sn'])
        	$select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),'total_qty' => 'SUM(p.num)','total_price'=>'SUM(p.price)', 'p.*'))
        		->group('p.sn');
        else
        	$select->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),  'p.*'));

        $select->join(array('gca' => 'good_category'),
            'p.cat_id = gca.id',
            array('good_category_name'=>'gca.name'));

        $select->join(array('g' => 'good'),
            'p.good_id = g.id',
            array('good_name'=>'g.name'));

        $select->join(array('b' => 'brand'),'b.id = g.brand_id',array('brand_name' => 'b.name'));

        $select->join(array('gc' => 'good_color'),
            'p.good_color = gc.id',
            array('good_color_name'=>'gc.name'));

        $select->join(array('s' => 'staff'),
            'p.created_by = s.id',
            array('staff_username'=>'s.username'));

        $select->join(array('w' => 'warehouse'),
            'p.warehouse_id = w.id',
            array('warehouse_name'=>'w.name'));

        $select->joinLeft(array('poc' => 'purchase_order_cartoon_box'),
            'p.sn=poc.po_sn',
            array('uploaded_imei_file' => 'poc.cartoon_box_number'));

        if (isset($params['export']) && 'all_imei' == $params['export'])
            $select->join(array('i' => 'imei'), 'p.sn=i.po_sn', array('i.imei_sn'));

        //Tanong
        if (isset($params['sn']) and $params['sn'])
            $select->where('p.sn LIKE ? or p.sn_ref LIKE ?', '%'.$params['sn'].'%');

        if (isset($params['created_by']) and $params['created_by'])
            $select->where('p.created_by = ?', $params['created_by']);

        if (isset($params['type']) and $params['type'])
            $select->where('p.type = ?', $params['type']);

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('p.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('p.good_color = ?', $params['good_color']);

        if (isset($params['warehouse_id']) and $params['warehouse_id'])
            $select->where('p.warehouse_id = ?', $params['warehouse_id']);

        if (isset($params['payment']) and $params['payment']){
            $select->where('p.flow IS NOT NULL');
            $select->where('p.flow <> \'\'');
            $select->where('p.flow <> 0');
        }

        if (isset($params['no_payment']) and $params['no_payment'])
            $select->where('p.flow IS NULL OR p.flow = \'\' OR p.flow = 0');

        if (isset($params['ship_warehouse']) and $params['ship_warehouse']){
            $select->where('p.mysql_user IS NOT NULL');
            $select->where('p.mysql_user <> \'\'');
            $select->where('p.mysql_user <> 0');
        }

        if (isset($params['no_ship_warehouse']) and $params['no_ship_warehouse'])
            $select->where('p.mysql_user IS NULL OR p.mysql_user = \'\' OR p.mysql_user = 0');

        if (isset($params['created_at_from']) and $params['created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_at >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_at <= ?', $year.'-'.$month.'-'.$day . ' 23:59:59');
        }

        if (isset($params['receive_at_from']) and $params['receive_at_from']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.mysql_time >= ?', $year.'-'.$month.'-'.$day);
        }

        if (isset($params['receive_at_to']) and $params['receive_at_to']){
            list( $day, $month, $year ) = explode('/', $params['receive_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.mysql_time <= ?', $year.'-'.$month.'-'.$day . ' 23:59:59');
        }

        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';

            if (in_array($params['sort'], array('staff_username', 'warehouse_name')))
                $collate .= ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' DESC ';

            if($params['sort'] == 'total_qty') {
            	$params['sort'] = 'SUM(p.num)';
            } elseif ($params['sort'] == 'total_price') {
            	$params['sort'] = 'SUM(p.price)';
            }

            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if (!(isset($params['export']) && 'all_imei' == $params['export']))
            $select->group('p.sn');
            $select->order('p.sn_ref DESC');

        if ($limit)
            $select->limitPage($page, $limit);

        if (isset($params['export']) && $params['export'])
            return $select->__toString();

        //echo ($select);die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function count_imported_imei($po_sn) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'imei'),
                array('total' => 'COUNT(imei_sn)'))
            ->where('i.po_sn LIKE ?', $po_sn)
            ->where('i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'');

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_imported_iot($po_sn) {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => 'imei'),
                array('total' => 'COUNT(imei_sn)'))
            ->where('i.po_sn LIKE ?', $po_sn)
            ->where('i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'');

        $result = $db->fetchOne($select);

        return $result;
    }


    function count_imported_digitalsn($po_sn) {
    	$db = Zend_Registry::get('db');
    	$select = $db->select()
            ->from(array('i' => 'digital_sn'),
                array('total' => 'COUNT(DISTINCT sn)'))
            ->where('i.po_sn LIKE ?', $po_sn)
            ->where('i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'');

        $result = $db->fetchOne($select);

        return $result;
    }

    function count_imported_sn($po_sn, $type=1/* 1: iLIKE */) {
    	$db = Zend_Registry::get('db');
    	$select = $db->select()
            ->from(array('i' => 'good_sn'),
                array('total' => 'COUNT(DISTINCT sn)'))
            ->where('i.po_sn LIKE ?', $po_sn)
            ->where('i.type = ?', $type)
            ->where('i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'');

        $result = $db->fetchOne($select);

        return $result;
    }

    function is_po_sn_complete($po_sn)
    {
    	$db = Zend_Registry::get('db');
    	$select = $db->select()
            ->from(array('i' => $this->_name),
                array('COUNT(i.sn)'))
            ->where('i.sn LIKE ?', $po_sn)
            ->where('i.mysql_user IS NOT NULL AND i.mysql_user <> 0 AND i.mysql_user <> \'\'')
            ->where('i.mysql_time IS NOT NULL AND i.mysql_time <> 0 AND i.mysql_time <> \'\'');

        $result = $db->fetchOne($select);

        if ($result > 0) return 1;
        else return 0;
    }

    function po_stock_card($params) {
        // check filter Warehouse
        /*
        $add_filter = "";
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $add_filter = " AND p.warehouse_id IN (".implode(",",$params['warehouse_id']).")";
            else
                $add_filter = " AND p.warehouse_id = ".$params['warehouse_id'];
        }
        */
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(
                    'created_date'      => 'p.mysql_time',
                    'document_number'   => 'p.sn_ref',
                    'document_ref'      => 'p.receive_ref',
                    'document_type'     => new Zend_Db_Expr($db->quote('PO')),
                    'wh_from'           => new Zend_Db_Expr($db->quote('From Factory')),
                    'in_amount'         => 'p.num',
                    'in_total_cost'     => 'p.price'
                ))
            ->joinLeft(array('w'  => 'warehouse')       ,'p.warehouse_id = w.id',array('wh_to' => 'w.name'))
            ->joinLeft(array('go' => 'good')            ,'p.good_id = go.id'        ,array('in_cost' => 'go.price_4','product_name' => 'go.name'))
            ->joinLeft(array('g'  => 'good_color')      ,'p.good_color = g.id'      ,array('product_color' => 'g.name'))
            ->joinLeft(array('gc' => 'good_category')   ,'p.cat_id = gc.id'         ,array('category' => 'gc.name','p.good_id','p.good_color','p.cat_id'))
            ->where('p.mysql_time IS NOT NULL')

            ->where("p.mysql_time <> '' ")
            ->where('p.mysql_time <> 0')
            ->order('p.mysql_time ASC');

        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
               // $add_filter = " AND p.warehouse_id IN (".implode(",",$params['warehouse_id']).")";
                $select->where("p.warehouse_id IN (".implode(",",$params['warehouse_id']).")",null);
            else
                //$add_filter = " AND p.warehouse_id = ".$params['warehouse_id'];
                $select->where("p.warehouse_id = ?",$params['warehouse_id']);
        }

            

        $from = explode('/', $params['from']);
        $to = explode('/', $params['to']);
        // Filter Data From - To
        if (isset($params['from']) && $params['from'] && !isset($params['to'])) {
            $select->where( 'p.mysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
        } elseif (isset($params['to']) && $params['to'] && !isset($params['from'])) {
            $select->where( 'p.mysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        } elseif (isset($params['to']) && $params['to'] && isset($params['from']) && $params['from'] ) {
            $select->where( 'p.mysql_time >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
            $select->where( 'p.mysql_time <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        }

        // Filter 
        if ( isset($params['cat_id']) && $params['cat_id'] ) {

            if (is_array($params['cat_id']))
                $select->where( 'p.cat_id IN (?)', $params['cat_id']);
            else
                $select->where( 'p.cat_id = ?', $params['cat_id']);
        }
        if ( isset($params['good_id']) && $params['good_id'] ) {

            if (is_array($params['good_id']))
                $select->where( 'p.good_id IN (?)', $params['good_id']);
            else
                $select->where( 'p.good_id = ?', $params['good_id']);
        }
        if ( isset($params['color_id']) && $params['color_id'] ) {

            if (is_array($params['color_id']))
                $select->where( 'p.good_color IN (?)', $params['color_id']);
            else
                $select->where( 'p.good_color = ?', $params['color_id']);
        }
        // check warehouse type
        if ( isset($params['warehouse_type']) && $params['warehouse_type'] ) {
            $select->where( 'p.warehouse_id IN ( SELECT id FROM warehouse WHERE warehouse_type IN ('.$params['warehouse_type'].') )', null);
        }
/*
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id']))
                $select->where( 'p.warehouse_id IN (?)', $params['warehouse_id']);
            else
                $select->where( 'p.warehouse_id = ?', $params['warehouse_id']);
        }
*/

       // echo $select; 
        $result = $db->fetchAll($select);

        return $result;
    }

}
