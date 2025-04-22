<?php
class Application_Model_DistrictFee extends Zend_Db_Table_Abstract
{
    protected $_name = 'district_fee';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select
            ->join(array('d' => HR_DB.'.regional_market'), 'p.district_id=d.id', array('district_name' => 'd.name'))
            ->join(array('pr' => HR_DB.'.regional_market'), 'd.parent=pr.id', array('province_name' => 'pr.name'));

        if (isset($params['name']) and $params['name'])
            $select->where('pr.name LIKE ? OR d.name LIKE ?', '%'.$params['name'].'%');

        if (isset($params['no_delivery']) and $params['no_delivery'])
            $select->where('p.no_delivery = 1', 1);

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }
}