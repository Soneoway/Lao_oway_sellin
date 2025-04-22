<?php
class Application_Model_BankAccountYourSide extends Zend_Db_Table_Abstract
{
	protected $_name = 'bank_account_your'; 

	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinLeft(array('bam' => 'bank_account_my'),'p.b_account_my = bam.id',array('bank_account_my' => 'bam.bank_account','my_card_no' => 'bam.card_no','my_bank_code' => 'p.card_no'));
		$select->joinLeft(array('ac' => 'accounting_organization'),'bam.account_org_id = ac.id',array('account_org_name' => 'ac.name'));
		$select->joinLeft(array('s' => HR_DB.'.store'),'p.store_id = s.id',array('store_name' => 's.name'));
		$select->joinLeft(array('d' => 'distributor'),'s.d_id = d.id',array('distributor_your_name' => 'd.title'));
		$select->joinLeft(array('f' => 'finance_client'),'p.finance_client_id = f.id',array('finance_client_name' => 'f.name','finance_client_mnemonic_code' => 'f.mnemonic_code'));
		$select->joinLeft(array('b' => 'bank'),'b.id = p.bank_id',array('bank_name' => 'b.name'));

		$select->where('p.del_by IS NULL');

		if(isset($params['dis_id']) && $params['dis_id']) {
			$select->where('p.d_id =?',$params['dis_id']);
		}

		if(isset($params['account_pp']) && $params['account_pp']) {
			$select->where('p.account_pp =?',$params['account_pp']);
		}

		if(isset($params['bank_id']) && $params['bank_id']) {
			$select->where('p.bank_id =?',$params['bank_id']);
		}

		if(isset($params['account']) && $params['account']) {
			$select->where('p.account_name LIKE ?','%'.$params['account'].'%');
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.finance_client_id =?',$params['finance_client']);
		}

		if(isset($params['my_bank']) && $params['my_bank']) {
			$select->where('p.b_account_my =?',$params['my_bank']);
		}

		if(isset($params['card_no']) && $params['card_no']) {
			$select->where('p.card_no LIKE ?','%'.$params['card_no'].'%');
		}

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