<?php
class Application_Model_DeductionSalesSn extends Zend_Db_Table_Abstract
{
    protected $_name = 'deduction_sales_sn';

    function fetchPagination($page, $limit, &$total, $params)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select();

        if (isset($params['group_sn']) and $params['group_sn']) {
            $select_fields = array();

            if (isset($params['get_fields']) and is_array($params['get_fields']))
                foreach ($params['get_fields'] as $get_field)
                    array_push($select_fields, $get_field);
                else
                    array_push($select_fields, 'p.*');

            $select->from(array('p' => $this->_name), $select_fields);

            $select->where('d_id =  ?', $params['d_id']);
        } else
            $select->from(array('p' => $this->_name), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT p.id'),
                    'p.*'));

        //  $select->joinLeft(array('ds' => 'deduction_distributor'), 'ds.id = p.deduction_distributor_id', array('d.name', 'd.title', 'd.mst_sn', 'd.unames'));

        //  $select->joinLeft(array('d' => 'distributor'), 'd.id = ds.distributor_id', array('d.name', 'd.title', 'd.mst_sn', 'd.unames'));

        //  if (isset($params['joint']) and $params['joint'])
        //            $select->where('joint =  ?', $params['joint']);
        //
        if(isset($params['joint']) and $params['joint'])
        {
            $select->where('joint_circular_id = ? ' , $params['joint']);
        }
        if (isset($params['export']) and $params['export'])
            return $select->__toString();
        if ($limit)
            $select->limitPage($page, $limit);


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
            $select = $db->select()->from(array('p' => 'market_deduction'), array('total_price' =>
                    'SUM(p.price)'))->where('p.sn = ?', $sn);
            $result = $db->fetchOne($select);
        }
        return $result;
    }

}
