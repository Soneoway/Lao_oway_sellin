<?php
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$area = $this->getRequest()->getParam('area');
$distributor_id = $this->getRequest()->getParam('distributor_id');
$target = $this->getRequest()->getParam('target');

$QArea = new Application_Model_Area();
$QDistributor = new Application_Model_Distributor();
$QTarget = new Application_Model_TargetDistributor();

try {
    throw new Exception("Đã hết hạn đăng ký target");
    
    $where = $QArea->getAdapter()->quoteInto('id = ?', $area);
    $area_check = $QArea->fetchRow($where);

    if (!$area_check) throw new Exception("Khu vực không hợp lệ");
    
    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
    $distributor = $QDistributor->fetchRow($where);

    if (!$distributor) throw new Exception("Retailer không hợp lệ");
    
    $where = array();
    $where[] = $QTarget->getAdapter()->quoteInto('d_id = ?', $distributor_id);
    $where[] = $QTarget->getAdapter()->quoteInto('from_date = ?', TARGET_FROM);
    $where[] = $QTarget->getAdapter()->quoteInto('to_date = ?', TARGET_TO);
    $target_check = $QTarget->fetchRow($where);

    if ($target_check) throw new Exception("Đã đăng ký target");

    if (!intval($target)) throw new Exception("Target phải là số dương");

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    
    $data = array(
        'd_id'      => $distributor_id,
        'target'    => $target,
        'from_date' => TARGET_FROM,
        'to_date'   => TARGET_TO,
        'area_id'   => $area,
        'total'     => PRICE*$target,
        'created_by' => $userStorage->id,
        'created_at' => date('Y-m-d H:i:s'),
        );

    $id = $QTarget->insert($data);

    if ($id) 
        exit(json_encode(array(
            'result' => 1,
            )));
    else
        exit(json_encode(array(
            'error' => 'Cannot insert',
            )));
} catch (Exception $e) {
    exit(json_encode(array(
        'error' => $e->getMessage(),
        )));
}
