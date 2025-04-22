<?php
class Application_Model_ChangeSalesProduct extends Zend_Db_Table_Abstract
{
	protected $_name = 'change_sales_product';

	function transfer_stock_card($params) {

        /*
        // check filter Warehouse
        $add_filter1 = "";
        $add_filter2 = "";
        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id'])){
                $add_filter1 = " AND c.old_id IN (".implode(",",$params['warehouse_id']).")";
                $add_filter2 = " AND c.new_id IN (".implode(",",$params['warehouse_id']).")";
            }
            else {
                $add_filter1 = " AND c.old_id = ".$params['warehouse_id'];
                $add_filter2 = " AND c.new_id = ".$params['warehouse_id'];
            }   
        }*/

        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('c' => $this->_name),
                array(
                    'created_date'      => 'c.created_at',
                    'document_number'   => 'c.changed_sn',
                    'document_ref'      => 'c.changed_sn',
                    'document_type'     => new Zend_Db_Expr($db->quote('Transfer')),
                    'in_amount'         => 'c.num'
                ))
            ->joinLeft(array('w1' => 'warehouse')    	,'c.old_id = w1.id'.$add_filter1    ,array('wh_from' => 'w1.name'))
            ->joinLeft(array('w2' => 'warehouse')    	,'c.new_id = w2.id'.$add_filter2    ,array('wh_to' => 'w2.name','w2.id AS wh_to_id'))
            ->joinLeft(array('go' => 'good')            ,'c.good_id = go.id'   	,array('in_cost' => 'go.price_4','product_name' => 'go.name'))
            ->joinLeft(array('g'  => 'good_color')      ,'c.good_color = g.id' 	,array('product_color' => 'g.name'))
            ->joinLeft(array('gc' => 'good_category')   ,'c.cat_id = gc.id'   	,array('category' => 'gc.name'))  

            ->joinLeft(array('cso' => 'change_sales_order')   ,'cso.changed_sn = c.changed_sn'     ,array('changed_sn' => 'cso.changed_sn')) 

            ->where('c.created_at IS NOT NULL')
            ->where("c.created_at <> '' ")
            ->where('c.created_at <> 0')
            ->group('c.changed_sn')
            ->order('c.created_at ASC');

        $from = explode('/', $params['from']);
        $to = explode('/', $params['to']);
        // Filter Data From - To
        if (isset($params['from']) && $params['from'] && !isset($params['to'])) {
            $select->where( 'c.created_at >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
        } elseif (isset($params['to']) && $params['to'] && !isset($params['from'])) {
            $select->where( 'c.created_at <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        } elseif (isset($params['to']) && $params['to'] && isset($params['from']) && $params['from'] ) {
            $select->where( 'c.created_at >= ?', $from[2].'-'.$from[1].'-'.$from[0] . ' 00:00:00' );
            $select->where( 'c.created_at <= ?', $to[2].'-'.$to[1].'-'.$to[0] . ' 23:59:59' );
        }

        // Filter 
        if ( isset($params['cat_id']) && $params['cat_id'] ) {

            if (is_array($params['cat_id']))
                $select->where( 'c.cat_id IN (?)', $params['cat_id']);
            else
                $select->where( 'c.cat_id = ?', $params['cat_id']);
        }
        if ( isset($params['good_id']) && $params['good_id'] ) {

            if (is_array($params['good_id']))
                $select->where( 'c.good_id IN (?)', $params['good_id']);
            else
                $select->where( 'c.good_id = ?', $params['good_id']);
        }
        if ( isset($params['color_id']) && $params['color_id'] ) {

            if (is_array($params['color_id']))
                $select->where( 'c.good_color IN (?)', $params['color_id']);
            else
                $select->where( 'c.good_color = ?', $params['color_id']);
        }

        // check warehouse type
        if ( isset($params['warehouse_type']) && $params['warehouse_type'] ) {
            $select->where( 'c.old_id IN ( SELECT id FROM warehouse WHERE warehouse_type IN ('.$params['warehouse_type'].') )', null);
            $select->where( 'c.new_id IN ( SELECT id FROM warehouse WHERE warehouse_type IN ('.$params['warehouse_type'].') )', null);
        }

        if ( isset($params['warehouse_id']) && $params['warehouse_id'] ) {

            if (is_array($params['warehouse_id'])) {
                $select->where( '( c.old_id IN (?)', $params['warehouse_id']);
                $select->orWhere( 'c.new_id IN (?) )', $params['warehouse_id']);
            } else {
                $select->where( '( c.old_id = ?', $params['warehouse_id']);
                $select->orWhere( 'c.new_id = ? )', $params['warehouse_id']);
            }
        }

        //echo $select; 
        $result = $db->fetchAll($select);

        return $result;
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
		->joinLeft(array('g' => 'good'),'p.good_id = g.id'    ,array('price_9' => 'g.price_4'))
        ->joinLeft(array('b' => 'brand'),'b.id = g.brand_id',array('brand_name' => 'b.name'))
        ->joinLeft(array('csi' => 'change_sales_imei'),'csi.changed_sales_product_id = p.id',array('change_sales_pice' => 'csi.price'))
        ->joinLeft(array('gc' => 'good_color_combined'),'gc.good_id = g.id',array());
            
        if (isset($params['changed_id']) and $params['changed_id'])
            $select->where('p.changed_id = ?',$params['changed_id']);
            $select->group('p.id');
            $select->order('p.id desc');


        if ($limit)
            $select->limitPage($page, $limit);
        
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;

    }
}
