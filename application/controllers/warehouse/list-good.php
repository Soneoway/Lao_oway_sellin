<?php
$this->_helper->layout->disableLayout();
ini_set('memory_limit', '-1');


$sn = $this->getRequest()->getParam('sn');
$type = $this->getRequest()->getParam('type');
$params = array(
    'sn' => $sn,

);

$page = 1;
$limit = 1;
$total = 0;


$QGood = new Application_Model_Good();
$QMarket = new Application_Model_Market();
$QMarketProduct = new Application_Model_MarketProduct();
$QImei  = new Application_Model_BvgImei();
$QJoint = new Application_Model_JointCircular();
$joint  = $QJoint->fetchAll();
$market = $QMarket->fetchPagination($page, $limit, $total, $params);

$QMarketDeduction = new Application_Model_MarketProduct();
$where = array();
$where[] = $QMarketDeduction->getAdapter()->quoteInto('sn = ?', $sn);
$market_deduction = $QMarketDeduction->fetchRow($where);
$invoice = $market_deduction['invoice_number'];

if (!(isset($market) && isset($market[0]) && $market[0])) {

} else {
    $market = $market[0];
}
unset($params['group_sn_bvg']);
$mk_goods = $QMarket->fetchPagination(1, null, $total2, $params);

$mk_bvg_joint = array();
$joint_cache = array();
foreach ($joint as $k => $v) {
    $params['joint'] = $v['id'];

    if( in_array($v['type'] , array(My_Discount::DISCOUNT_TYPE_BVG , My_Discount::DISCOUNT_TYPE_BVG_KA)))
        $mk_bvg_joint[$k] = $QMarketProduct->fetchPaginationlistGood(1, null, $total2, $params);
    elseif($v['type'] == My_Discount::DISCOUNT_TYPE_BVG_ACCESSORIES)
    {
        $mk_bvg_joint[$k] = $QMarketProduct->fetchPaginationListAccessories(1,null,$total2,$params);

    }

    $joint_cache[$v['id']] = $v['name'];
}




unset($params['group_sn']);
unset($params['sort']);

$params['sn'] = $market['sn'];
$total2 = 0;

$invoice_time = '';



$imeis = array();
foreach ($mk_bvg_joint as $k => $bvg) {

        if(isset($bvg['type']) and $bvg['type'] ==  My_Discount::BVGAccessories)
        {
            foreach ($bvg as $t => $v) {

                $where = array();
                $where[] = $QBvgAccessories->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                $where[] = $QBvgAccessories->getAdapter()->quoteInto('bvg_market_product_id = ?', $v['id']);

                $params = array(
                    'good_id'               => $v['good_id'],
                    'bvg_market_product_id' => $v['id']
                );

                $total = 0;
                $imeis[$v['id']] = $QBvgAccessories->fetchPagination(1, null , $total, $params);
                $invoice_time    = $v['invoice_time'];
                $distributor_id = $v['d_id'];
                $warehouse_id = $v['warehouse_id'];
                $this->_helper->viewRenderer('warehouse/partials/invoice/list-good-accessories.pthml', null, true);
            }
        }

        else
        {
             foreach ($bvg as $t => $v) {


                $where = array();
                $where[] = $QImei->getAdapter()->quoteInto('good_id = ?', $v['good_id']);
                $where[] = $QImei->getAdapter()->quoteInto('bvg_market_product_id = ?', $v['id']);
                $imeis[$v['id']] = $QImei->fetchAll($where);

                $invoice_time   = $v['invoice_time'];
                $distributor_id = $v['d_id'];
                $warehouse_id   = $v['warehouse_id'];
            }
        }

}

unset($params['sn']);

$this->view->market = $market;
$this->view->mk_goods = $mk_goods;
$this->view->imeis = $imeis;


$tmp = $QGood->fetchAll();
$all_goods = array();
foreach ($tmp as $k => $v) {
    $all_goods[$v['id']] = $v;
}
$this->view->all_goods = $all_goods;
$this->view->goods_cached = $QGood->get_cache();

$QGoodCategory = new Application_Model_GoodCategory();
$this->view->good_categories = $QGoodCategory->get_cache();

$QGoodColor = new Application_Model_GoodColor();
$this->view->good_colors = $QGoodColor->get_cache();

$QDistributor = new Application_Model_Distributor();

$distributor = $QDistributor->find($distributor_id);
$distributor = $distributor->current();

if ($distributor) {
    $this->view->distributor = $distributor;
}

$QStaff = new Application_Model_Staff();
$staff = $QStaff->find($market['user_id']);
$staff = $staff->current();

if ($staff) {
    $this->view->staff = $staff;
}

$QGoodColor = new Application_Model_GoodColor();
$this->view->colors_list = $QGoodColor->get_cache();
$add_time = $market['add_time'];
//set invoice time
if (!$market['invoice_time']) {
    $data = array('invoice_time' => date('Y-m-d H:i:s'));
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $QMarket->update($data, $where);
}



$this->view->invoice_time = $invoice_time;
$this->view->add_time = strtotime($add_time);
$this->view->warehouse = $warehouse_id;
$this->view->sn = $sn;
$this->view->invoice = $invoice;
$this->view->market_product = $mk_bvg_joint;
$this->view->joint = $joint_cache;
