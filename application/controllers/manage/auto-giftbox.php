<?php 
$id = $this->getRequest()->getParam('id');
$flashMessenger = $this->_helper->flashMessenger;

$QShippingAddress = new Application_Model_ShippingAddress();
$QDistributor     = new Application_Model_Distributor();
$QAG              = new Application_Model_AutoGiftbox();

$sort          = $this->getRequest()->getParam('sort', '');
$page          = $this->getRequest()->getParam('page', 1);
$desc          = $this->getRequest()->getParam('desc', 1);

$limit = LIMITATION;
$total = 0;

$good_id         = $this->getRequest()->getParam('good_id');
$checkbox_allday = $this->getRequest()->getParam('checkbox_allday');
$auto_giftbox_start_date = $this->getRequest()->getParam('auto_giftbox_start_date');
$auto_giftbox_end_date   = $this->getRequest()->getParam('auto_giftbox_end_date');
$good_id_give 	         = $this->getRequest()->getParam('good_id_give');
$cat_id 		= $this->getRequest()->getParam('cat_id');
$cat_id_give 	= $this->getRequest()->getParam('cat_id_give');

$cat_id      = PHONE_CAT_ID;
// $cat_id_give = ACCESS_CAT_ID;

$params = array(
		'cat_id'		  => $cat_id,
		'cat_id_give'     => $cat_id_give,
		'checkbox_allday' => $checkbox_allday,
		'good_id_give'    => $good_id_give,
		'good_id'         => $good_id,
		'auto_giftbox_start_date'  => $auto_giftbox_start_date,
		'auto_giftbox_end_date'    => $auto_giftbox_end_date,
);
$this->view->params = $params;

$QGood = new Application_Model_Good();
$this->view->goods_cached = $QGood->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

if($cat_id){
	$where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);
    $goods_cat = $QGood->fetchAll($where, 'name');
    $this->view->goods_cat = $goods_cat;
}

// if($cat_id_give){
// 	$where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id_give);
//     $goods_cat_give = $QGood->fetchAll($where, 'name');
//     $this->view->goods_cat_give = $goods_cat_give;
// }

$list_autogift = $QAG->fetchPagination($page, $limit, $total, $params);
$this->view->list_autogift = $list_autogift;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->page   = $page;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->offset = $limit*($page-1);
$this->view->url    = ( $params ? '?'.http_build_query($params).'&' : '?' );

$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;