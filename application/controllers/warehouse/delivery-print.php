<?php
$carrier_id = $this->getRequest()->getParam('carrier_id');
$sn = $this->getRequest()->getParam('sn');
$this->_helper->layout->disableLayout();

try {
    if (!$carrier_id || !My_Carrier::get($carrier_id)) throw new Exception("Invalid carrier");

    if (!$sn) throw new Exception("Invalid SN");

    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market_check = $QMarket->fetchRow($where);

    if (!$market_check) throw new Exception("Invalid SN");

    // lấy thông tin người nhận
    $QDistributor = new Application_Model_Distributor();
    $distributor = $QDistributor->find($market_check['d_id']);
    $distributor = $distributor->current();

    if (!$distributor) throw new Exception("Invalid distributor");

    // $this->view->market      = $market_check;
    $this->view->distributor = $distributor;

    switch ($carrier_id) {
        case My_Carrier::Kerry:
            $this->_helper->viewRenderer->setRender('delivery-bill/kerry');
            break;
        case My_Carrier::Saigon_Post:
            $this->_helper->viewRenderer->setRender('delivery-bill/saigon-post');
            break;
        
        default:
            throw new Exception("Invalid carrier");
            break;
    }

} catch (Exception $e) {
    $this->view->error = $e->getMessage();
}
