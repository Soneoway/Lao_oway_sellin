<?php
$sort           = $this->getRequest()->getParam('sort', '');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$imei_sn        = $this->getRequest()->getParam('imei_sn');

$do    			= $this->getRequest()->getParam('do_number');
$form_date 		= $this->getRequest()->getParam('form_date', date('d/m/Y'));
$to_date 		= $this->getRequest()->getParam('to_date', date('d/m/Y', strtotime('-0 day')));
$new_distributor = $this->getRequest()->getParam('new_distributor');

$this->view->rank = $rank;
$this->view->d_id = $d_id;


$export  = $this->getRequest()->getParam('export', 0);

$limit = LIMITATION;
$total = 0;

$params = array(
    'imei_sn'           => $imei_sn,
    'do_number'    		=> $do,
    'form_date'			=> $form_date,
    'to_date'			=> $to_date,
    'new_distributor'	=> $new_distributor,
    'export'            => $export
);

$QChange = new Application_Model_ChangeImeiDistibutor();
$QDistibutor = new Application_Model_Distributor();
$QStaff = new Application_Model_Staff();
$QWarehouse = new Application_Model_Warehouse();
$QStore = new Application_Model_Store();

$distibutor = $QDistibutor->get_cache3();
$staff = $QStaff->get_cache();
$warehouse  = $QWarehouse->get_cache();

$imei_sn = explode("\r\n", $imei_sn);

$params['sort'] = $sort;
$params['desc'] = $desc;

if ( isset($export) && $export == 1 ) {
	$cahngelist = $QChange->_exportchangeimeilist($page,$limit, $total, $params);
	$this->_exportChangeList($cahngelist);
}

$change = $QChange->fetchPagination($page, $limit, $total, $params);

$where = $QDistibutor->getAdapter()->quoteInto('agent_warehouse_id is null',null);
$this->view->distibutor_list = $QDistibutor->fetchAll($where);

$this->view->change = $change;
$this->view->distibutor = $distibutor;
$this->view->staff  = $staff;
$this->view->warehouse = $warehouse;
$this->view->store = $QStore->get_cache();


$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'sales/change-list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;