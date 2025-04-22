<?php
class Application_Model_LogSwapImeiRework extends Zend_Db_Table_Abstract{
    protected $_name = 'log_swap_imei_rework';

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_model'=>'g.name','good_name' => 'g.desc'));

        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('color_name'=>'gc.name'));

        $select->joinLeft(array('s' => 'staff'), 's.id = p.created_by', array("TRIM(CONCAT(s.firstname,' ',s.lastname))AS fullname"));

        if (isset($params['id']) and $params['id'])
            $select->where('p.id = ?', $params['id']);

        if (isset($params['co_sn']) and $params['co_sn'])
            $select->where('p.co_sn = ?', $params['co_sn']);

        if (isset($params['co_ref']) and $params['co_ref'])
            $select->where('p.co_ref = ?', $params['co_ref']);

        if (isset($params['po_sn']) and $params['po_sn'])
            $select->where('p.po_sn = ?', $params['po_sn']);

        if (isset($params['po_ref']) and $params['po_ref'])
            $select->where('p.po_ref = ?', $params['po_ref']);

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('g.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['good_color_id']) and $params['good_color_id'])
            $select->where('p.good_color = ?', $params['good_color_id']);

        if (isset($params['good_type']) and $params['good_type'])
            $select->where('p.good_type = ?', $params['good_type']);

        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}