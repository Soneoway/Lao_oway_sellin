<?php
class Application_Model_StaffDelivery extends Zend_Db_Table_Abstract
{
	protected $_name = 'staff_delivery';

	function loginStaffDelivery($username, $password){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),array('p.id', 'p.firstname','p.lastname', "TRIM(CONCAT(p.firstname,' ',p.lastname))AS fullname", 'p.company', 'p.is_admin'));

            $select->where('p.username = ?', $username);
            $select->where('p.password = ?', $password);
            $select->where('p.status = ?', 1);

        $result = $db->fetchRow($select);
        return $result;
    }

    function checkAdminDelivery($id, $company){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),array('p.id'));

            $select->where('p.id = ?', $id);
            $select->where('p.company = ?', $company);
            $select->where('p.is_admin = ?', 1);
            $select->where('p.status = ?', 1);

        $result = $db->fetchRow($select);
        return $result;
    }

}
