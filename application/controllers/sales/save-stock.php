<?php

$flashMessenger = $this->_helper->flashMessenger;
// $flashMessenger->setNamespace('error')->addMessage('Đơn Stock không thể edit nữa.');
// $this->_redirect(HOST.'sales/stock');

if ($this->getRequest()->getMethod() != 'POST')
    exit;

$flashMessenger = $this->_helper->flashMessenger;
$ids               = $this->getRequest()->getParam('ids');
$cat_ids           = $this->getRequest()->getParam('cat_id');
$good_ids          = $this->getRequest()->getParam('good_id');
$good_colors       = $this->getRequest()->getParam('good_color');
$nums              = $this->getRequest()->getParam('num');
$prices            = $this->getRequest()->getParam('price');
$totals            = $this->getRequest()->getParam('total');
$texts             = $this->getRequest()->getParam('text');
$distributor_id    = $this->getRequest()->getParam('distributor_id');
$warehouse_id      = $this->getRequest()->getParam('warehouse_id');
$salesman          = $this->getRequest()->getParam('salesman');
$type              = $this->getRequest()->getParam('type');
$sale_off_percent  = $this->getRequest()->getParam('sale_off_percent');
$sn                = $this->getRequest()->getParam('sn');
$life_time         = $this->getRequest()->getParam('life_time');
$rebate_price      = $this->getRequest()->getParam('rebate_price');
$campaign          = $this->getRequest()->getParam('campaign');
$imei_list         = $this->getRequest()->getParam('imei_list');

// chặn edit đơn của người khác
if (!empty($sn)) {
    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market_check = $QMarket->fetchRow($where);

    if ($market_check) {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if ($market_check['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID)) {
            $flashMessenger->setNamespace('error')->addMessage('You cannot edit this Order');
            $this->_redirect(HOST.'sales');
        }
    }
}

$params = array(
    'ids'               => $ids,
    'cat_id'            => $cat_ids,
    'good_id'           => $good_ids,
    'good_color'        => $good_colors,
    'num'               => $nums,
    'price'             => $prices,
    'total'             => $totals,
    'text'              => $texts,
    'distributor_id'    => $distributor_id,
    'warehouse_id'      => $warehouse_id,
    'salesman'          => $salesman,
    'type'              => $type,
    'sale_off_percent'  => $sale_off_percent,
    'sn'                => $sn,
    'life_time'         => $life_time,
    'isbatch'           => 1,
    'rebate_price'      => $rebate_price,
    'campaign'          => $campaign,
    'imei_list'         => $imei_list,
);

$result = $this->saveAPIStock($params);

if ($result['code'] == 1) { //success
    // save tags
    $tags        = $this->getRequest()->getParam('tags');
    $QTag       = new Application_Model_Tag();
    $QTagObject = new Application_Model_TagObject();

    // remove old record on tag_object
    $where = array();
    $where[] = $QTagObject->getAdapter()->quoteInto('object_id = ?', $result['sn']);
    $where[] = $QTagObject->getAdapter()->quoteInto('type = ?', TAG_ORDER);

    $QTagObject->delete($where);

    if ($tags and isset($result['sn']) and $result['sn']){
        foreach ($tags as $t){
            $where = $QTag->getAdapter()->quoteInto('name = ?', $t);
            $existed_tag = $QTag->fetchRow($where);

            if ($existed_tag)
                $tag_id = $existed_tag['id'];
            else
                $tag_id = $QTag->insert(array('name'=>$t));

            $QTagObject->insert(
                array(
                    'tag_id'    => $tag_id,
                    'object_id' => $result['sn'], //order sn
                    'type'      => TAG_ORDER,
                )
            );
        }
    }

    $flashMessenger->setNamespace('success')->addMessage($result['message']);
} else {
    $flashMessenger->setNamespace('error')->addMessage($result['message']);
}

$this->_redirect( HOST.'sales/stock' );
