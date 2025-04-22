<?php
//echo "1111";die;
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

$from    		  = $this->getRequest()->getParam('out_time_from', date('Y-m-01'));

if($from < '2017-06-06'){
    $from = '2017-06-06';
}

$to      		  = $this->getRequest()->getParam('out_time_to', date('Y-m-d'));
$export           = $this->getRequest()->getParam('export', 0);
$search           = $this->getRequest()->getParam('search');

$limit = LIMITATION;
$total = 0;

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

$params = array(
    'cat_id'        => array_unique($cat_id),
    'good_id'       => array_unique($good_id),
    'color_id'      => array_unique($color_id),
    'warehouse_id'  => array_unique($warehouse_id),
    'warehouse_type'=> $userStorage->warehouse_type,
    'from'          => $from,
    'to'            => $to,
    );

//print_r($params);die;
/*if(isset($params['warehouse_id'][0]) && $params['warehouse_id'][0] == 'All'){
    unset($params['warehouse_id']);
}*/

$db = Zend_Registry::get('db');

$QPO = new Application_Model_Po();
//$po = $QPO->po_stock_card($params);
//print_r($po); echo "<br/>";

$QMarket = new Application_Model_Market();
//$so = $QMarket->so_stock_card($params);

//print_r($so); echo "<br/>";

//$return = $QMarket->return_stock_card($params);

//print_r($return); echo "<br/>";
$result = '';

if (isset($search) and $search == 1) {
$QChangeSalesProduct = new Application_Model_ChangeSalesProduct();
//$transfer = $QChangeSalesProduct->transfer_stock_card($params);

//print_r($so); echo "<br/>";
/**/
// merge array and sort by date

//$result = array_merge($po, $so, $return,$transfer);
//$result = array_merge($so);
//usort($result, 'cmp'); 
}


//print_r($params);

if (isset($export) && $export && $export=="1") {
    $this->_exportStockCardKerry($params);
}else if (isset($export) && $export && $export=="2") {
    //print_r($params);die;
    $this->_exportStockCardFinance($params);
}else if (isset($export) && $export && $export=="3") {
    $this->_exportStockCardByStore($params);
}

$this->view->stocks = $result;
$this->view->params = $params;
$this->view->limit = $limit;
$this->view->total = $total;
$this->view->url = HOST . 'warehouse/stock-card/' . ($params ? '?' .http_build_query($params) . '&' : '?');

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
//$this->view->warehouse = $QWarehouse->fetchAll(null, 'name');

$where_wh = array();
//$warehouse_type = $userStorage->warehouse_type;
//$where_wh[] = $QWarehouse->getAdapter()->quoteInto('warehouse_type IN ('.$warehouse_type.')', null);
if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
    $where_wh[] = $QWarehouse->getAdapter()->quoteInto('show_kerry = ? ', 1);
}
$this->view->warehouse= $QWarehouse->fetchAll($where_wh, 'name');


$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if ($this->getRequest()->isXmlHttpRequest())
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setRender('partials/stock_card_list');
}
