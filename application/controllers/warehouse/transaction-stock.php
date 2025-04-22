<?php

function cmp($a, $b){
    $ad = strtotime($a['created_date']);
    $bd = strtotime($b['created_date']);
    return ($ad-$bd);
}

$sort             = $this->getRequest()->getParam('sort', 'outmysql_time');
$desc             = $this->getRequest()->getParam('desc', 1);
$page             = $this->getRequest()->getParam('page', 1);

$cat_id           = $this->getRequest()->getParam('cat_id');
$good_id          = $this->getRequest()->getParam('good_id');
$color_id         = $this->getRequest()->getParam('color_id');
$warehouse_id     = $this->getRequest()->getParam('warehouse_id');

$from    		  = $this->getRequest()->getParam('out_time_from', date('01/m/Y'));
$to      		  = $this->getRequest()->getParam('out_time_to', date('d/m/Y'));
$export           = $this->getRequest()->getParam('export', 0);

$limit = LIMITATION;
$total = 0;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

//print_r($_GET);
$params = array(
    'cat_id'        => array_unique($cat_id),
    'good_id'       => array_unique($good_id),
    'color_id'      => array_unique($color_id),
    'warehouse_id'  => array_unique($warehouse_id),
    'warehouse_type'=> $userStorage->warehouse_type,
    'from'          => $from,
    'to'            => $to,
    );

$db = Zend_Registry::get('db');

// Prepare Data
/*
if ($from == '') { $tmp_from = ""; } 
else {
	$tmp = explode("/",$from);
	$tmp_from = $tmp[2] ."-". $tmp[1] ."-". $tmp[0] ." 00:00:00";
}

if ($to == '') { $tmp_to = ""; } 
else {
	$tmp = explode("/",$to);
	$tmp_to = $tmp[2] ."-". $tmp[1] ."-". $tmp[0] ." 23:59:59";
}*/

$QWarehouse = new Application_Model_Warehouse();

/*
$QPO = new Application_Model_Po();
$po = $QPO->po_stock_card($params);

$QMarket = new Application_Model_Market();
$so = $QMarket->so_stock_card($params);

$return = $QMarket->return_stock_card($params);

$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
$transfer = $QChangeSalesProduct->transfer_stock_card($params);

$result = array_merge($po, $so, $return,$transfer);

usort($result, 'cmp');
*/

//$stmt = $db->query($sql);
 
//$result = $stmt->fetchAll();

$result = $QWarehouse->getTransactionStockGroup($params);

$this->view->stocks = $result;
$this->view->params = $params;
$this->view->limit = $limit;
$this->view->total = $total;
$this->view->url = HOST . 'warehouse/transaction-stock/' . ($params ? '?' .http_build_query($params) . '&' : '?');

$this->view->offset = $limit * ($page - 1);


$QGoodCategory  = new Application_Model_GoodCategory();
$this->view->catagory   = $QGoodCategory->fetchAll(null, 'name');

if ($cat_id) {
	$QGood = new Application_Model_Good();

	if (is_array($cat_id) && $cat_id)
		$where = $QGood->getAdapter()->quoteInto('cat_id IN (?)', $cat_id);
	else
		$where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);

	$this->view->goods = $QGood->fetchAll($where, 'name');

	if ($good_id) {
        $QGoodColorCombined = new Application_Model_GoodColorCombined();
        $QGoodColor = new Application_Model_GoodColor();
        
        if (is_array($good_id) && $good_id)
            $where = $QGoodColorCombined->getAdapter()->quoteInto('good_id IN (?)', $good_id);
        else
            $where = $QGoodColorCombined->getAdapter()->quoteInto('good_id = ?', $good_id);

        $temp = $QGoodColorCombined->fetchAll($where, 'good_color_id');

        for ($i=0;$i<count($temp);$i++) {
            $tmp[$i] = $temp[$i]['good_color_id'];
        }

        $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $tmp);
        $result = $QGoodColor->fetchAll($where, 'name');

        $this->view->colors = $result;
	}
}

$QWarehouse = new Application_Model_Warehouse();
$this->view->warehouse = $QWarehouse->fetchAll(null, 'name');


$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if ($this->getRequest()->isXmlHttpRequest())
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/transaction-stock-group-list');
}
