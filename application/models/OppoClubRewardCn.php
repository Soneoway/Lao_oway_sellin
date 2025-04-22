<?php
class Application_Model_OppoClubRewardCn extends Zend_Db_Table_Abstract {
	protected $_name = 'oppoclub_reward_cn';

	public function check_file_import_reward_cn_confirm($quater_year, $quater_no) {
		$db     = Zend_Registry::get('db');
		$select = $db->select()
		             ->from(array('oc' => 'oppoclub_reward_cn'), array('COUNT(oc.distributor_id)'))
			->where('oc.quater_year = ?', $quater_year)
			->where('oc.quater_no = ?', $quater_no);
		//echo $select;
		// die;

		$total = $db->fetchOne($select);
		return $total;
	}

	public function check_reward_cn_confirm($quater_year, $quater_no, $d_id, $level_name, $key_sn, $creditnote_price_confirm) {
		$db     = Zend_Registry::get('db');
		$select = $db->select()
		             ->from(array('oc' => 'oppoclub_reward_cn'), array('COUNT(oc.distributor_id)'))
			->where('oc.key_sn = ?', $key_sn)
			->where('oc.quater_year = ?', $quater_year)
			->where('oc.quater_no = ?', $quater_no)
			->where('oc.distributor_id = ?', $d_id)
			->where('oc.level_name = ?', $level_name)
		// ->where('oc.decorate_status = ?', 1)
		//	->where('oc.creditnote_price >= ?', $creditnote_price_confirm)
			->where('oc.creditnote_sn is null', null);
		//echo $select;
		// die;

		$total = $db->fetchOne($select);
		return $total;
	}

	public function getOppoclup_Reward_List_CN_page($page, $limit, &$total, $params) {
		//print_r($params);
		//die;

		set_time_limit(0);
		ini_set('memory_limit', -1);
		ini_set('display_error', 0);
		error_reporting('~E_ALL');
		try {
			$db     = Zend_Registry::get('db');
			$select = $db->select()

			->from(array('oc'     => 'oppoclub_reward_cn'), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT oc.distributor_id', 'creditnote_sn'), 'oc.*'))
				->joinLeft(array('d' => 'distributor'), 'oc.distributor_id=d.id', array('d.id as distributor_id', 'd.title', 'd.store_code'))
				->joinLeft(array('s' => 'staff'), 'oc.confirm_by=s.id', array("CONCAT(s.firstname,' ',s.lastname)AS confirm_by"));

			if (isset($params['creditnote_sn']) and $params['creditnote_sn'] != '') {
				$select->where('oc.creditnote_sn LIKE ?', '%'.$params['creditnote_sn'].'%');
			}

			//$select->where('oc.decorate_status =?',1);

			if (isset($params['d_id']) and $params['d_id'] != '') {
				$select->where('oc.distributor_id =?', $params['d_id']);
			}

			/*if (isset($params['decorate_status']) and $params['decorate_status'] == '1') {
				$select->where('oc.decorate_status =?', $params['decorate_status']);
			} else {
				$select->where('oc.decorate_status in(0,2)', null);
			}*/

			if (isset($params['level_name']) and $params['level_name'] != 'All') {
				$select->where('oc.level_name =?', $params['level_name']);
			}


			if (isset($params['status_cn']) and $params['status_cn'] != '') {
				$select->where('oc.status_cn =?', $params['status_cn']);
			} else {
				$select->where('oc.status_cn =?', 0);
			}

			if (isset($params['quater_no']) and $params['quater_no'] != '') {
				$select->where('oc.quater_no =?', $params['quater_no']);
			}

			if (isset($params['quater_year']) and $params['quater_year'] != '') {
				$select->where('oc.quater_year =?', $params['quater_year']);
			}

			if (isset($params['sort']) and $params['sort']) {
				$order_str = $collate = '';

				$desc = (isset($params['desc']) and $params['desc'] == 1)?' DESC ':' ASC ';

				if ($params['sort'] == 'confirm_date') {
					$params['sort'] = 'oc.confirm_date';
				} elseif ($params['sort'] == 'distributor_name') {
					$params['sort'] = 'oc.distributor_name';
				}

				$order_str .= $params['sort'].$collate.$desc;

				$select->order(new Zend_Db_Expr($order_str));
			}

			if (isset($params['export']) and $params['export'] == '0') {
				if ($limit) {$select->limitPage($page, $limit);}
			}

			//echo $select;
			//die;

			$result = $db->fetchAll($select);
			$total  = $db->fetchOne("select FOUND_ROWS()");
			return $result;
		} catch (Exception $e) {
			return array(
				'code'    => -1,
				'message' => $e->getMessage(),
			);
		}
	}

	public function generat_RewardCnFromCatty($quater_year, $quater_no) {
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		error_reporting(~E_ALL);
		ini_set("display_error", '0');
		try {
			$flashMessenger = $this->_helper->flashMessenger;
			$db             = Zend_Registry::get('db');
			$db->beginTransaction();

			$db->query("CALL gen_reward_credit_note_sn('".$quater_year."','".$quater_no."');");

			$this->check_distributor_decorate_RewardCnFromCatty($db, $quater_year, $quater_no);
			//$db->query("CALL gen_reward_credit_note_sn_imei('".$quater_year."','".$quater_no."');");

			$db->commit();
		} catch (exception $e) {
			$flashMessenger->setNamespace('error')->addMessage('Cannot  Get Data Reward CN From Catty, please try again!');
		}
	}

	public function check_distributor_decorate_RewardCnFromCatty($db, $quater_year, $quater_no) {
		try {
			$flashMessenger    = $this->_helper->flashMessenger;
			$QOppoClubRewardHR = new Application_Model_OppoClubReward();
			$QOppoClubRewardCn = new Application_Model_OppoClubRewardCn();
			//$db = Zend_Registry::get('db');
			$select = $db->select()
			             ->from(array('oc' => 'oppoclub_reward_cn'), array('oc.distributor_id', 'oc.level_name'
					, new Zend_Db_Expr("CASE oc.level_name WHEN 'Silver' THEN 'S' WHEN 'Gold' THEN 'G' WHEN 'Platinum' THEN 'P'  END AS level_name_key")))
				->where('oc.quater_year = ?', $quater_year)
				->where('oc.quater_no = ?', $quater_no)
				->where('oc.creditnote_sn is null', null)
				->where('oc.decorate_status is null', null);
			// echo $select;
			//die;

			$result = $db->fetchAll($select);
			foreach ($result as $item) {
				$distributor_id = $item['distributor_id'];
				$level_name     = $item['level_name'];
				$level_name_key = $item['level_name_key'];

				//echo "<d_id>".$distributor_id."<level>".$level_name.">".$res=$this->call_ws_check_dealer_decorate_Action($distributor_id,$level_name);

				$res = $this->call_ws_check_distributor_decorate_Action($distributor_id, $level_name_key);
				//$res='false';
				if ($res == 'true') {//OK
					$data = array(
						'decorate_status' => 1,
					);

					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);

					$QOppoClubRewardCn->update($data, $whereReward);
				} else if ($res == 'false') {//No OK
					$data = array(
						'decorate_status' => 2,
					);

					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('creditnote_sn IS NULL', null);

					$QOppoClubRewardCn->update($data, $whereReward);
				} else {// Null
					$data = array(
						'decorate_status' => 0,
					);
					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('creditnote_sn IS NULL', null);

					$QOppoClubRewardCn->update($data, $whereReward);

					//$QOppoClubRewardCn->delete($whereReward);

					/*
				$data = array(
				'generat_date' => null,
				);
				$whereRewardHR = array();
				$whereRewardHR[] = $QOppoClubRewardHR->getAdapter()->quoteInto('quater_year = ?', $quater_year);
				$whereRewardHR[] = $QOppoClubRewardHR->getAdapter()->quoteInto('quater_no = ?', $quater_no);
				$whereRewardHR[] = $QOppoClubRewardHR->getAdapter()->quoteInto('d_id = ?', $distributor_id);

				$QOppoClubRewardHR->update($data, $whereRewardHR);
				 */

				}
			}
		} catch (exception $e) {
			$flashMessenger->setNamespace('error')->addMessage('Cannot Check Data Distributor Decorate From Trade, please try again!');
		}
	}

	public function call_ws_check_distributor_decorate_Action($d_id, $type) {
		try {
			require_once 'My'.DIRECTORY_SEPARATOR.'nusoap'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'nusoap.php';
			$this->wssCenterURI = TRADE_URI.'wss';
			$this->namespace    = 'OPPO';
			//type= P,G,S
			$params = array(
				'd_id' => $d_id,
				'type' => $type,
			);

			$client = new nusoap_client($this->wssCenterURI);

			// Call Web Service
			$result = $client->call("wsTypeForReward", array('params' => $params));
		} catch (exception $e) {
			$flashMessenger->setNamespace('error')->addMessage('Cannot Call WS Check Data Distributor Decorate From Trade, please try again!');
			$result = null;
		}
		return $result;
	}

	/*------------Call Cli----------------*/

	public function generat_RewardCnFromCatty_Cli($db, $quater_year, $quater_no) {
		set_time_limit(0);
		ini_set('memory_limit', '-1');
		error_reporting(~E_ALL);
		ini_set("display_error", '0');
		try {
			//$db = Zend_Registry::get('db');
			//$db->beginTransaction();
			echo "Call Store gen_reward_credit_note_sn..."."\r\n";
			$db->query("CALL gen_reward_credit_note_sn('".$quater_year."','".$quater_no."');");

			//echo "Call WS check_distributor_decorate_Reward..."."\r\n";
			//$this->check_distributor_decorate_RewardCnFromCatty_Cli($db,$quater_year,$quater_no);

			echo "Call Store gen_reward_credit_note_sn_imei..."."\r\n";
			$db->query("CALL gen_reward_credit_note_sn_imei('".$quater_year."','".$quater_no."');");

			//$db->commit();
			$status  = 1;
			$message = "Complete!";
		} catch (exception $e) {
			$db->rollBack();
			$status  = 0;
			$message = "Cannot  Get Data Reward CN From Catty, please try again!";
		}

		$arr = array(
			'status'  => $status,
			'message' => $message,
		);
		return $arr;
	}

	public function check_distributor_decorate_RewardCnFromCatty_Cli($db, $quater_year, $quater_no) {
		try {
			$QOppoClubRewardHR = new Application_Model_OppoClubReward();
			$QOppoClubRewardCn = new Application_Model_OppoClubRewardCn();
			//$db = Zend_Registry::get('db');
			$select = $db->select()
			             ->from(array('oc' => 'oppoclub_reward_cn'), array('oc.distributor_id', 'oc.level_name'
					, new Zend_Db_Expr("CASE oc.level_name WHEN 'Silver' THEN 'S' WHEN 'Gold' THEN 'G' WHEN 'Platinum' THEN 'P'  END AS level_name_key")))
				->where('oc.quater_year = ?', $quater_year)
				->where('oc.quater_no = ?', $quater_no)
				->where('oc.creditnote_sn is null', null)
				->where('oc.decorate_status is null', null);
			// echo $select;
			die;

			$result = $db->fetchAll($select);
			foreach ($result as $item) {
				$distributor_id = $item['distributor_id'];
				$level_name     = $item['level_name'];
				$level_name_key = $item['level_name_key'];

				$res = $this->call_ws_check_distributor_decorate_Cli_Action($distributor_id, $level_name_key);
				//$res='false';
				if ($res == 'true') {//OK
					$data = array(
						'decorate_status' => 1,
					);

					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);

					$QOppoClubRewardCn->update($data, $whereReward);
				} else if ($res == 'false') {//No OK
					$data = array(
						'decorate_status' => 2,
					);

					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('creditnote_sn IS NULL', null);

					$QOppoClubRewardCn->update($data, $whereReward);
				} else {// Null
					$data = array(
						'decorate_status' => 0,
					);
					$whereReward   = array();
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_year = ?', $quater_year);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('quater_no = ?', $quater_no);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('level_name = ?', $level_name);
					$whereReward[] = $QOppoClubRewardCn->getAdapter()->quoteInto('creditnote_sn IS NULL', null);

					$QOppoClubRewardCn->update($data, $whereReward);

				}
			}
			return 1;
		} catch (exception $e) {
			return 0;
		}
	}

	public function call_ws_check_distributor_decorate_Cli_Action($d_id, $type) {
		try {
			require_once 'My'.DIRECTORY_SEPARATOR.'nusoap'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'nusoap.php';
			$this->wssCenterURI = TRADE_URI.'wss';
			$this->namespace    = 'OPPO';
			//type= P,G,S
			$params = array(
				'd_id' => $d_id,
				'type' => $type,
			);

			$client = new nusoap_client($this->wssCenterURI);

			// Call Web Service
			$result = $client->call("wsTypeForReward", array('params' => $params));
		} catch (exception $e) {
			$result = null;
		}
		return $result;
	}

	/*------------End Call Cli----------------*/
	public function cn_reward_view_print($params, &$imei) {
		$db          = Zend_Registry::get('db');
		$type        = array('Platinum', 'Gold', 'Silver');
		$select_imei = $db->select()
		                  ->from(array('imei' => 'oppoclub_reward_cn_imei'), array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS imei.quater_year'), 'count(imei.good_id)'))
			->group('imei.good_id')
			->where('imei.creditnote_sn = ?', $params['sn'])
		;
		$total = $db->fetchAll($select_imei);
		$imei  = $db->fetchOne("select FOUND_ROWS()");

		try {

			$select = $db->select()
			             ->from(array('op' => 'oppoclub_reward_cn'), array('op.quater_year', 'op.quater_no'
					, 'a.name AS area_name'
					, 'rm2.name AS province'
					, 'rm.name AS district', 'dis.id AS d_id,dis.store_code', 'dis.title', 'dis.mst_sn'
					, 'dis.tel'
					, 'op.level_name', 'op.total_imei'
					, 'op.total_price', 'op.creditnote_price'
					, 'op.creditnote_price_confirm'
					, 'op.confirm_date'
					, 'cn.create_date'
					, 'op.start_date'
					, 'op.end_date'
					, 'op.creditnote_sn'
					, 'op.decorate_status'
					, 'dis.add AS add_tax'))
				->joinLeft(array('dis' => 'distributor'), 'dis.id = op.distributor_id', array())
				->joinLeft(array('rm'  => 'hr.regional_market'), 'dis.district = rm.id', array())
				->joinLeft(array('rm2' => 'hr.regional_market'), 'dis.region = rm2.id', array())
				->joinLeft(array('a'   => 'hr.area'), 'rm2.area_id = a.id ', array())
				->joinLeft(array('cn'   => 'credit_note'), 'cn.creditnote_sn = op.creditnote_sn ', array())
				->where('level_name in (?)', $type)
				->where("op.quater_year in(2016,2017,2018) ", null)
				->where("op.quater_no in('Quater_01','Quater_02','Quater_03','Quater_04')", null)

				->where('op.creditnote_sn = ?', $params['sn'])
				->where('op.distributor_id = ?', $params['d_id']);

			$result = $db->fetchRow($select);

			return $result;
		} catch (exception $e) {
			$result = null;
		}
		return $result;
	}

}