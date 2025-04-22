<?php 
$district         = $this->getRequest()->getParam('district');
$value            = $this->getRequest()->getParam('value');
$additional_value = $this->getRequest()->getParam('additional_value');
$no_delivery      = $this->getRequest()->getParam('no_delivery', 0);
$id               = $this->getRequest()->getParam('id');

$flashMessenger = $this->_helper->flashMessenger;

try {
    if (!intval($district)) throw new Exception("Please choose Province, District", 1);
    if (!$value) throw new Exception("Please fill in Fee", 2);
    if (!is_numeric($value)) throw new Exception("Fee must be a number", 3);
    if (!is_numeric($additional_value)) throw new Exception("Additional Fee must be a number", 3);
    
    $QDistrictFee = new Application_Model_DistrictFee();
    $data = array(
        'district_id'      => intval($district),
        'fee_id'           => My_Sale_Order_Fee::Shipping,
        'value'            => floatval($value),
        'additional_value' => floatval($additional_value),
        'no_delivery'      => intval($no_delivery),
    );

    if ($id) {
        $where = $QDistrictFee->getAdapter()->quoteInto('id = ?', intval($id));
        $fee = $QDistrictFee->fetchRow($id);

        if (!$fee) throw new Exception("Invalid Fee", 4);
        
        $QDistrictFee->update($data, $where);
    } else {

        $id = $QDistrictFee->insert($data);
    }

    if ($id) {
        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'warehouse/delivery-fee');
    }
} catch (Exception $e) {
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));
    $refer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (HOST.'warehouse/delivery-order-control');
    $this->_redirect($refer);
}
