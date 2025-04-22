<?php
class Application_Model_MarketBvgKa extends Zend_Db_Table_Abstract{
	protected $_name = 'market_bvg_ka';

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn']) {


            $select_fields = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'total_qty' => 'SUM(p.num)',
                'total_price' => 'SUM(p.total)',
                'p.warehouse_id');


                array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name), $select_fields)->group('p.sn');

        } else
            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                'p.*'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.d_id', array(
            'd.name',
            'd.title',
            'd.mst_sn',
            'd.unames'));



        if (isset($params['joint']) and $params['joint'])
            $select->where('p.joint =  ?', $params['joint']);

        if (isset($params['sales_sn']) and $params['sales_sn'])
            $select->where('p.sn =  ?', $params['sales_sn']);

        if (isset($params['d_id']) and $params['d_id'])
            $select->where('p.d_id =  ?', $params['d_id']);




        if (isset($params['export']) and $params['export'])
            return $select->__toString();


        if ($limit)
            $select->limitPage($page, $limit);

        $select->order('created_at DESC');




        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

}