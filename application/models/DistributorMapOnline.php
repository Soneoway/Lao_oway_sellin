<?php
class Application_Model_DistributorMapOnline extends Zend_Db_Table_Abstract
{
protected $_name = 'distributor_mapping_online';

	public function check_distributor_online(){
		// echo "string"; die;
		$db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),array('p.*'));
            // ->where('p.distributor_id_online =?',$result);

        $db->fetchRow($select);

	}



}