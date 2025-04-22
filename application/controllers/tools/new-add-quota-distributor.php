<?php

	$flashMessenger = $this->_helper->flashMessenger;

	$QWarehouses     = new Application_Model_Warehouse();
	$QGoodCategory  = new Application_Model_GoodCategory();

	$warehouses 		= $QWarehouses->get_cache();
	$good_categories   	= $QGoodCategory->get_cache();

    if ($this->getRequest()->getMethod() == 'POST'){

    	// print_r($_POST);die;

    	$type = $this->getRequest()->getParam('type');
    	$warehouses = $this->getRequest()->getParam('warehouses');
    	$cat_id = $this->getRequest()->getParam('cat_id');
    	$good_id = $this->getRequest()->getParam('good_id');
    	$quota_date = $this->getRequest()->getParam('quota_date');
        $quota_date = DateTime::createFromFormat('d/m/Y', $quota_date);
        $quota_date = $quota_date->format('Y-m-d');
    	$rank = $this->getRequest()->getParam('rank');
    	// $d_id = $this->getRequest()->getParam('d_id');
    	$list_dis_quota = $this->getRequest()->getParam('list_dis_quota');
    	$list_color_quota = $this->getRequest()->getParam('list_color_quota');
    	$all_color = $this->getRequest()->getParam('all_color');

    	$currentTime    = date('Y-m-d h:i:s');
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
        $user_id        = $userStorage->id;

        $QNQD = new Application_Model_NewQuotaDistributor();
		$QNQDD = new Application_Model_NewQuotaDistributorDetails();

        $db = Zend_Registry::get('db');

        try{

            $db->beginTransaction();

            $count_list = -1;

            $array_distributor = array();

            foreach ($list_dis_quota as $key => $value) {

                $where = array();
                $where[] = $QNQD->getAdapter()->quoteInto('d_id = ?', $value);
                $where[] = $QNQD->getAdapter()->quoteInto('warehouse_id = ?', $warehouses);
                $where[] = $QNQD->getAdapter()->quoteInto('good_id = ?', $good_id);
                $where[] = $QNQD->getAdapter()->quoteInto('good_type = ?', $type);
                $where[] = $QNQD->getAdapter()->quoteInto('quota_date = ?', $quota_date);
                $data_quota = $QNQD->fetchRow($where);

                if($data_quota && !in_array($data_quota['id'], $array_distributor)){

                    throw new Exception('Can not upload! (Have Quota Product) => Distributor ID : ' . $value . ' | Warehouse ID : ' . $warehouses . ' | Type : ' . $type . ' | Category : ' . ' | Good ID : ' . $good_id . ' | Good Color ID : ' . $good_color_id . ' | Quota : ' . $quota_date);
                }

            	$data = array(
	            	'd_id' => $value,
	            	'warehouse_id' => $warehouses,
	            	'good_id' => $good_id,
	            	'good_type' => $type,
	            	'order_type' => $type,
	            	'quota_date' => $quota_date,
	            	'created_date' => $currentTime,
	            	'created_by' => $user_id,
	            	'status' => 1
	            );

	            $nqd_id = $QNQD->insert($data);

                array_push($array_distributor, $nqd_id);

            	foreach ($all_color as $key_sub => $value_sub) {

            		$count_list++;
            		$status = 1;
            		$color_num = $list_color_quota[$count_list];

            		if($color_num == '-'){
            			$status = 2;
            		}

                    if($color_num < 1){
                        $color_num = 0;
                    }

            		$data = array(
            			'nqd_id' => $nqd_id,
            			'good_color' => $value_sub,
            			'num' => $color_num,
            			'status' => $status,
            			'created_date' => $currentTime,
            			'created_by' => $user_id
            		);

            		$QNQDD->insert($data);
            	}
            }

	        $db->commit();  

	        $flashMessenger->setNamespace('success')->addMessage('Success');
	        $this->_redirect(HOST.'tool/new-quota-manage-distributor');

        }
        catch(exception $e)
        {
	        $db->rollback();
	        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
	        $this->_redirect(HOST.'tool/new-add-quota-distributor');
        }

    }

	$this->view->warehouses      		= $warehouses;
	$this->view->good_categories 		= $good_categories;

	$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
	$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();