<?php
    	$action             = $this->getRequest()->getParam('action');
        //echo "<pre>";print_r($_POST);
        //die;
        $company_id                    = $this->getRequest()->getParam('company_id');
    	$discount_id              		= $this->getRequest()->getParam('discount_id');
	

    	$limit = LIMITATION;
        $total = 0;
        $params['company_id']              = $company_id;
        $params['discount_id'] 				= $discount_id;
        //print_r($params);
    	$QEpPrivilegesTranOrder     = new Application_Model_EpPrivilegesTranOrder();
        $QCompany    = new Application_Model_Company();
        

        $discount_setup = $QEpPrivilegesTranOrder->position_discount_setup($company_id,$discount_id);
        $discount = $QEpPrivilegesTranOrder->privileges_discount($company_id,$discount_id);
        /*$department = $QEpPrivilegesTranOrder->department($department_id);
        $position = array();
        foreach($department as $i => $v)
        {
            $position[$v['department_id']]=$v['department_id'];
            $result_item = $QEpPrivilegesTranOrder->position($v['department_id']);
            $position[$v['department_id']]=$result_item;

        }*/
        $company = $QCompany->get_company("");
        $position = $QEpPrivilegesTranOrder->position("");

        foreach($discount_setup as $i => $v)
        {
            $setup_department[]=$v['department_id'];
        }

        foreach($discount_setup as $i => $v)
        {
            $setup_position[]=$v['position_id'];
        }


        //echo "<pre>";print_r($setup_position);die;
        $this->view->company               = $company;
        $this->view->discount               = $discount;
        $this->view->department             = $department;
        $this->view->position               = $position;
        $this->view->discount_setup         = $discount_setup;
        $this->view->setup_department       = array_unique($setup_department);
        $this->view->setup_position         = array_unique($setup_position);

		$this->view->desc     				= $desc;
		$this->view->sort     				= $sort;
		$this->view->messages 				= $messages;
		$this->view->params   				= $params;
		$this->view->limit    				= $limit;
		$this->view->total    				= $total;
		$this->view->url      				= HOST.'tool/ep-privileges-position-list'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   				= $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();