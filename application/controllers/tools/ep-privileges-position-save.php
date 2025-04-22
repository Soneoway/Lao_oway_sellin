<?php

	$this->_helper->layout->disableLayout();
	$this->_helper->viewRenderer->setNoRender(TRUE);
	$key_sn = date('YmdHis') . substr(microtime(), 2, 4);


	$flashMessenger = $this->_helper->flashMessenger;

 
 	$action          				= $this->getRequest()->getParam('action');
 	//echo "<pre>";print_r($_POST);die;

 	$company_id           			= $this->getRequest()->getParam('company_id');
	$discount_id           			= $this->getRequest()->getParam('discount_id');
	$department          			= $this->getRequest()->getParam('department');
	$position          				= $this->getRequest()->getParam('position');
    $date      						= date('Y-m-d H:i:s');

    	$company_id = $this->getRequest()->getParam('company_id');
    	//echo $company_id;die;
        $db = _initConfig($company_id);

    	$db = Zend_Registry::get('db');
		$userStorage = Zend_Auth::getInstance()->getStorage()->read();
 	
		$QEpPrivilegesPositionDiscount  			= new Application_Model_EpPrivilegesPositionDiscount();
		$QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();

		$arr = explode(";", $discount_id);
		$discount_id = $arr[0];
		$warehouse_id = $arr[1];
		$bank_id = $arr[2];
		$distributor_id = $arr[3];
		//echo "<pre>";print_r($_POST);die;
		//$db->beginTransaction();
		try{


				//echo "<pre>";print_r($_POST);die;
				$where = array();
				$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('company_id = ?', $company_id);
				$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('discount_id = ?', $discount_id);
				$res = $QEpPrivilegesPositionDiscount->delete($where);

				foreach ($position as $key => $v){

					$department_id = $key;
					foreach ($v as $position_key){
						$arr1 = explode(";", $position_key);
						$position_id = $arr1[0];
						$position_code = $arr1[1];
						//echo "<pre>";print_r($position_code);die;
						
						//$check_position			= $QEpPrivilegesPositionDiscount->fetchAll($where);
						//$check_exist = $QEpPrivilegesPositionDiscount->fetchRow($where);

						/*$check_exist =	$QStaffSalesOrder->check_position_discount($department_id,$position_code,$discount_id,$warehouse_id,$bank_id,$distributor_id);
						if($check_exist){
							$where = array();
			            	$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('department_id = ?', $department_id);
			            	$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('discount_id = ?', $discount_id);
			            	$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
			            	$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('bank_id = ?', $bank_id);
			            	$where[] = $QEpPrivilegesPositionDiscount->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
							$res = $QEpPrivilegesPositionDiscount->delete($where);
						}*/
						//die;

						$params = array();
						$params['company_id'] 		= $company_id;
						$params['department_id'] 	= $department_id;
						$params['position_code'] 	= $position_code;
						$params['position_id'] 		= $position_id;
						$params['discount_id'] 		= $discount_id;
						$params['warehouse_id'] 	= $warehouse_id;
						$params['distributor_id'] 	= $distributor_id;
						$params['bank_id'] 			= $bank_id;
						$params['active'] 		= 1;
						$params['create_date'] 	= $date;
						$params['create_by'] 	= $userStorage->id;
						//print_r($params);die;
						$res1 = $QEpPrivilegesPositionDiscount->insert($params);

					}
					//$chk = $db->commit();
				}

			
	
		}
        catch (exception $e)
        {
            //$db->rollback();
            return array(
                    'code' => 2,
                    'message' => 'Cannot save, please try again!' . $e->getCode()."/".$e->getMessage(),
                    );
                
        }

		$flashMessenger->setNamespace('success')->addMessage("Success");
		$this->_redirect('tool/ep-privileges-position-create?action=view&company_id='.$company_id.'&discount_id='.$discount_id);


		function _initConfig($company_id)
		{
		    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
		    if($company_id=='1')//OPPO
		    {
		        $db = Zend_Db::factory($config->resources->db);
		        $db->query("SET NAMES utf8;");
		        Zend_Db_Table::setDefaultAdapter($db);
		        Zend_Registry::set('db', $db);
		    }else if($company_id=='2'){//ONEPLUS
		        $db = Zend_Db::factory($config->resources->dboneplus);
		        $db->query("SET NAMES utf8;");
		        Zend_Db_Table::setDefaultAdapter($db);
		        Zend_Registry::set('db', $db);
		    }
		    $setdb = Zend_Registry::get('db');
		    return $setdb;
		}
