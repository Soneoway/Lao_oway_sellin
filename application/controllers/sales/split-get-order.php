<?php 
/**
 * @author buu.pham
 * 
 */

$this->_helper->layout->disableLayout();

$sn = $this->getRequest()->getParam('sn');

try {
    if (!$sn) throw new Exception("SN requierd", 2);
    
    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $order = $QMarket->fetchAll($where);

    if (!count($order)) 
        $this->view->error = "Invalid Order SN";

    $order = $QMarket->fetchWithImei($sn);

    $QGood        = new Application_Model_Good();
    $QGoodColor   = new Application_Model_GoodColor();
    $QDistributor = new Application_Model_Distributor();
    $QWarehouse   = new Application_Model_Warehouse();
    $this->view->good_cache        = $QGood->get_cache();
    $this->view->good_color_cache  = $QGoodColor->get_cache();
    $this->view->distributor_cache = $QDistributor->get_cache();
    $this->view->warehouse_cache   = $QWarehouse->get_cache();
    $this->view->order             = $order;
} catch (Exception $e) {
    $this->view->error = $e->getMessage();
}