<?php
class Application_Model_BankAccountMySide extends Zend_Db_Table_Abstract
{
	protected $_name = 'bank_account_my'; 

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('b' => 'bank'),'p.bank = b.id',array('bank_name' => 'b.name'));
		$select->joinleft(array('aorg' => 'accounting_organization'),'p.account_org_id = aorg.id',array('account_org_name' => 'aorg.name'));
		$select->joinleft(array('at' => 'account_type'),'p.account_type = at.id',array('account_type_name' => 'at.name'));

		$select->where('p.del_by IS NULL');

		if(isset($params['dis_id']) && $params['dis_id']) {
			$select->where('p.d_id =?',$params['dis_id']);
		}

		if(isset($params['bank_account']) && $params['bank_account']) {
			$select->where('p.bank_account LIKE ?','%'.$params['bank_account'].'%');
		}

		if(isset($params['bank']) && $params['bank']) {
			$select->where('p.bank =?',$params['bank']);
		}

		if(isset($params['account_type']) && $params['account_type']) {
			$select->where('p.account_type =?',$params['account_type']);
		}

		if(isset($params['mnemonic_code']) && $params['mnemonic_code']) {
			$select->where('p.mnemonic_code LIKE ?','%'.$params['mnemonic_code'].'%');
		}

		if(isset($params['host']) && $params['host']) {
			$select->where('p.host =?',$params['host']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['account_pp']) && $params['account_pp']) {
			$select->where('p.account_pp =?',$params['account_pp']);
		}

		if(isset($params['user_id']) && $params['user_id']) {
			$select->where('p.user_id =?',$params['user_id']);
		}

		if(isset($params['card_type']) && $params['card_type']) {
			$select->where('p.card_type =?',$params['card_type']);
		}

		if(isset($params['fn']) && $params['fn']) {
			$select->where('p.d_id IN (?)',$params['fn']);
		}

		if ($limit)
			$select->limitPage($page, $limit);

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

	function get_cache(){
		$cache      = Zend_Registry::get('cache');
		$result     = $cache->load($this->_name.'_cache');

		if ($result === false) {

			$db = Zend_Registry::get('db');

			$select = $db->select()
			->from(array('p' => $this->_name),
				array('p.*'));

			$select->where('p.status =?',1);
			$select->order(new Zend_Db_Expr('p.`bank_account` COLLATE utf8_unicode_ci'));

			$data = $db->fetchAll($select);

			$result = array();
			if ($data){
				foreach ($data as $item){
					$result[$item['id']] = $item['bank_account'];
				}
			}
			$cache->save($result, $this->_name.'_cache', array(), null);
		}
		return $result;
	}

}

?>