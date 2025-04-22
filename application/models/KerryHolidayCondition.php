<?php
class Application_Model_KerryHolidayCondition extends Zend_Db_Table_Abstract{

	protected $_name = 'kerry_holiday_condition';

	function getKerryHoliday($DateNow){

		$db = Zend_Registry::get('db');

   		$select = $db->select()
                ->from(array('p' => $this->_name),array('p.*'));
        $select->where('p.status = ?', 0);
        $select->where('p.day_holiday = ?', $DateNow);
        $result = $db->fetchAll($select);
        return $result;

	}

    function getKerryHolidayByYear($year){

        $start_year = $year . '-01-01';
        $end_year = $year . '-12-31';

        $db = Zend_Registry::get('db');

        $select = $db->select()
                ->from(array('p' => $this->_name),array('p.*'));
        $select->where('p.status = ?', 0);
        $select->where('p.day_holiday >= ?', $start_year);
        $select->where('p.day_holiday <= ?', $end_year);
        $result = $db->fetchAll($select);
        return $result;

    }

}