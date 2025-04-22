<?php
class Application_Model_BvgDistributor extends Zend_Db_Table_Abstract{
	protected $_name = 'bvg_distributor';

    function getBalance($params){
        try {
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.price'));

            if (isset($params['d_id']) and $params['d_id'])
                $select->where('p.distributor_id = ?', $params['d_id']);

            $result = $db->fetchOne($select);
            return ($result ? $result : 0);
        } catch (Exception $e){
            return -1;
        }
    }
}