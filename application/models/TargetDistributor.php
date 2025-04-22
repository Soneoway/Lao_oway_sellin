<?php
class Application_Model_TargetDistributor extends Zend_Db_Table_Abstract
{
    protected $_name = 'target_distributor';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select->join(array('d' => 'distributor'), 'd.id=p.d_id', array());

        if (isset($params['created_by']) && $params['created_by']) {
            $select->where('p.created_by = ?', $params['created_by']);
        }

        if (isset($params['from_date']) && $params['from_date']) {
            $select->where('p.from_date = ?', $params['from_date']);
        }

        if (isset($params['to_date']) && $params['to_date']) {
            $select->where('p.to_date = ?', $params['to_date']);
        }

        if (isset($params['name']) && $params['name']) {
            $select->where('d.title LIKE ?', '%'.$params['name'].'%');
        }

        if (isset($params['area_id']) && $params['area_id']) {
            $QRegion = new Application_Model_Region();
            $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $params['area_id']);
            $regions = $QRegion->fetchAll($where);

            $region_list = array();

            foreach ($regions as $key => $value)
                $region_list[] = $value['id'];

            $select->where('d.region IN (?)', $region_list);
        }

        if ( $limit && !(isset($params['export']) && $params['export']) )
            $select->limitPage($page, $limit);

        $select->order(new Zend_Db_Expr('p.`id` DESC'));

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}