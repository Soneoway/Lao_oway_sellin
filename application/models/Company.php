<?php
class Application_Model_Company extends Zend_Db_Table_Abstract
{
	protected $_name = 'company';

    
    function get_company($company_id)
    {
        //$this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

        $select = " SELECT c.company_id,c.company_name from company c where c.status=1 order by c.company_id";

        $result = $db->fetchAll($select);
        return $result;
        //echo "<pre>";print_r($result);
    }


}

