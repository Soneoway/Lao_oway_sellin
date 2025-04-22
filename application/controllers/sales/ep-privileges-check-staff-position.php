<?php
//echo "<pre>";
//print_r($_POST);die;

	$staff_code     = $this->getRequest()->getParam('staff_code');
	$sort           = $this->getRequest()->getParam('sort', 'date_canceled');
	$desc           = $this->getRequest()->getParam('desc', 1);
	$page           = $this->getRequest()->getParam('page', 1);




	$limit = LIMITATION;
    $total = 0;


    $params['staff_code'] 		= $staff_code ;

    if ($this->getRequest()->getMethod() == 'POST') {
		$QEpPrivilegesTranOrder     = new Application_Model_EpPrivilegesTranOrder();
		$privileges =  $QEpPrivilegesTranOrder->CheckStaffPrivilegesPosition($page, $limit, $total, $params);
	}
	 //echo "<pre>";
	 //print_r($privileges);die;

	    $this->view->privileges   = $privileges;	
		$this->view->desc     = $desc;
		$this->view->sort     = $sort;
		$this->view->messages = $messages;
		$this->view->params   = $params;
		$this->view->limit    = $limit;
		$this->view->total    = $total;
		$this->view->url      = HOST.'sales/ep-privileges-check-staff-position'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages = $flashMessenger->setNamespace('error')->getMessages();