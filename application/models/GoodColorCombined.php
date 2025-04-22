<?php
class Application_Model_GoodColorCombined extends Zend_Db_Table_Abstract
{
	protected $_name = 'good_color_combined';

	public function get_color_by_good($good_id)
	{
		$db = Zend_Registry::get('db');

		$select = $db->select()
			->from(array('p' => $this->_name),array('color_id' => 'gc.id','color_name' => 'gc.name'))
			->joinleft(array('gc' => 'good_color'),'gc.id = p.good_color_id',array())
			->where('p.good_id =?',$good_id);

		$result = $db->fetchAll($select);
        return $result;
	}

}
