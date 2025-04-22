<?php
class Application_Model_FinanceClient extends Zend_Db_Table_Abstract
{
	protected $_name = 'finance_client';


	function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');

		$select = $db->select()
		->from(array('p' => $this->_name),
			array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

		$select->joinleft(array('d1' => 'distributor'),'p.distributor_m_id = d1.id',array('distributor_my_side' => 'd1.title'));
		$select->joinleft(array('d2' => 'distributor'),'p.distributor_y_id = d2.id',array('distributor_your_side' => 'd2.title'));
		$select->joinleft(array('fw' => 'finance_warehouse'),'p.fw_id = fw.id',array('finance_warehouse_name' => 'fw.name','finance_warehouse_code' => 'fw.code'));
		$select->joinleft(array('aorg1' => 'accounting_organization'),'p.account_m = aorg1.id',array('account_org_my' => 'aorg1.name'));
		$select->joinleft(array('aorg2' => 'accounting_organization'),'p.account_y = aorg2.id',array('account_org_your' => 'aorg2.name'));

		$select->where('p.status IN (?)',array(1,2));

		if(isset($params['dis_id_m']) && $params['dis_id_m']) {
			$select->where('p.distributor_m_id =?',$params['dis_id_m']);
		}

		if(isset($params['dis_id_y']) && $params['dis_id_y']) {
			$select->where('p.distributor_y_id =?',$params['dis_id_y']);
		}

		if(isset($params['finance_client']) && $params['finance_client']) {
			$select->where('p.name LIKE ?', '%'.$params['finance_client'].'%');
		}

		if(isset($params['account_org_m']) && $params['account_org_m']) {
			$select->where('p.account_m LIKE ?', '%'.$params['account_org_m'].'%');
		}

		if(isset($params['account_org_y']) && $params['account_org_y']) {
			$select->where('p.account_y LIKE ?', '%'.$params['account_org_y'].'%');
		}

		if(isset($params['network']) && $params['network']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['mnemonic_code']) && $params['mnemonic_code']) {
			$select->where('p.mnemonic_code LIKE ?', '%'.$params['mnemonic_code'].'%');
		}

		if(isset($params['finance_warehouse']) && $params['finance_warehouse']) {
			$select->where('p.fw_id =?',$params['finance_warehouse']);
		}

		if(isset($params['cross_account']) && $params['cross_account']) {
			$select->where('p.cross_account =?',$params['cross_account']);
		}

		if(isset($params['status']) && $params['status']) {
			$select->where('p.status =?',$params['status']);
		}

		if(isset($params['fn']) && $params['fn']) {
			$select->where('p.distributor_m_id IN (?)',$params['fn']);
		}

		$select->where('p.del_by IS NULL');

		if($limit)
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
			$select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

			$data = $db->fetchAll($select);

			$result = array();
			if ($data){
				foreach ($data as $item){
					$result[$item['id']] = $item['name'];
				}
			}
			$cache->save($result, $this->_name.'_cache', array(), null);
		}
		return $result;
	}

	function getFinanceClientMoney($finance_client_id)
	{

		$db = Zend_Registry::get('db');

		$now = date('Y-m-d');

		$sub_select_01 = $db->select()
			->from(array('clm' => 'credit_limit'),array('SUM(clm.quota)'))
			->where('clm.end_date >= ?',$now)
			->where('clm.status =?',2)
			->where('clm.finance_client_id = p.id');

		$sub_select_02 = $db->select()
			->from(array('sr' => 'sale_receipt'),array('SUM(sr.amount)'))
			->where('sr.status =?',2)
			->where('sr.finance_client = p.id');

		$sub_select_03 = $db->select()
			->from(array('spm' => 'support_fund'),array('SUM(spm.amount)'))
			->where('spm.status =?',2)
			->where('spm.finance_client_id = p.id');

		$get = array(
			'finance_client_id'			=> 'p.id',
			'total_credit_limit'		=> new Zend_Db_Expr("(".$sub_select_01.")"),
			'total_sales_receipt'		=> new Zend_Db_Expr("(".$sub_select_02.")"),
			'total_support_payment'		=> new Zend_Db_Expr("(".$sub_select_03.")")
		);

		$select = $db->select()
			->from(array('p' => $this->_name),$get);

		$select->where('p.id =?',$finance_client_id);
		$select->group('p.id');

		// echo $select; die();

		$result = $db->fetchAll($select);
		return $result;

	}

	function DistributorReconciliation($page, $limit, &$total, $params)
	{
		$db = Zend_Registry::get('db');

		$now = date('Y-m-d');

		// Creadit Limit
		$sub_select_01 = $db->select()
			->from(array('clm' => 'credit_limit'),array('SUM(clm.quota)'))
			->where('clm.end_date >= ?',$now)
			->where('clm.status =?',2)
			->where('clm.finance_client_id = p.id');

		// Sale Receipt
		$sub_select_02 = $db->select()
			->from(array('sr' => 'sale_receipt'),array('SUM(sr.amount)'))
			->where('sr.status =?',2)
			->where('sr.finance_client = p.id');

		// Support Payment
		$sub_select_03 = $db->select()
			->from(array('spm' => 'support_fund'),array('SUM(spm.amount)'))
			->where('spm.status =?',2)
			->where('spm.finance_client_id = p.id');

		// Sale Refund
		$sub_select_04 = $db->select()
			->from(array('sf' => 'sale_refund'),array('SUM(sf.amount)'))
			->where('sf.status =?',2)
			->where('sf.finance_client_id = p.id');

		// Order Amount
		$sub_select_05 = $db->select()
			->from(array('cd' => 'contact_detail'),array('SUM(cd.amount)'))
			->where('cd.status = 2')
			->where('cd.type = 1')
			->where('cd.finance_client_id = p.id');

		// Client Contact Note
		$sub_select_06 = $db->select()
			->from(array('cn' => 'contact_note'),array('SUM(cn.amount)'))
			->where('cn.status = 2')
			->where('cn.finance_client_id = p.id');

		$select = $db->select()
			->from(array('p' => $this->_name),
				array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*',
					'finance_client_code' => 'p.code', 
					'finance_client_name' => 'p.name' , 
					'total_credit_limit' => new Zend_Db_Expr("(".$sub_select_01.")"), 
					'total_sales_receipt' => new Zend_Db_Expr("(".$sub_select_02.")"), 
					'total_support_payment' => new Zend_Db_Expr("(".$sub_select_03.")"),
					'total_sales_refund'	=> new Zend_Db_Expr("(".$sub_select_04.")"),
					'total_sales_order'		=> new Zend_Db_Expr("(".$sub_select_05.")"),
					'total_contact_note'	=> new Zend_Db_Expr("(".$sub_select_06.")")
				));

		if(isset($params['distributor_ids']) && $params['distributor_ids']){
			$select->where('p.distributor_m_id =?',$params['distributor_ids']);
		}

		if(isset($params['finance_client']) && $params['finance_client']){
			$select->where('p.id =?',$params['finance_client']);
		}

		if(isset($params['code']) && $params['code']){
			$select->where('p.code LIKE ?','%'.$params['code'].'%');
		}

		if($limit)
			$select->limitPage($page, $limit);

		// echo $select; die();

		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}

}

?>