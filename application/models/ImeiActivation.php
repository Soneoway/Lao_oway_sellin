<?php
class Application_Model_ImeiActivation extends Zend_Db_Table_Abstract
{
	protected $_name = 'imei_activation';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.imei_sn'), 'p.activated_at', 'p.phone'));

        if (isset($params['date']) && date_create_from_format('Y-m-d', $params['date'])) {
            $select->where('p.activated_at >= ?', $params['date']);
            $select->where('p.activated_at <= ?', $params['date'] . ' 23:59:59');
        }

        // if ($limit)
            // $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}
