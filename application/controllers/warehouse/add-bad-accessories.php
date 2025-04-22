<?php
$good_id        = $this->getRequest()->getParam('good_id');
$good_color     = $this->getRequest()->getParam('good_color');
$warehouse_id   = $this->getRequest()->getParam('warehouse_id');

$QGood          = new Application_Model_Good();
$QGoodColor     = new Application_Model_GoodColor();
$QWarehouse     = new Application_Model_Warehouse();

$where          = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
$good           = $QGood->fetchRow($where);
if (!$good){
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('This ID not exists');
    $this->_redirect(HOST."warehouse/bad-accessories-management");
}
$this->view->good_id = $good_id;
$this->view->good = $good;

$where          = $QGoodColor->getAdapter()->quoteInto('id = ?', $good_color);
$goodColor      = $QGoodColor->fetchRow($where);
if (!$goodColor){
    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->addMessage('This ID not exists');
    $this->_redirect(HOST."warehouse/bad-accessories-management");
}
$this->view->good_color = $good_color;
$this->view->goodColor = $goodColor;

$warehouse = null;

$QWarehouseProduct = new Application_Model_WarehouseProduct();

if ($warehouse_id){
    $where          = $QWarehouse->getAdapter()->quoteInto('id = ?', $warehouse_id);
    $warehouse      = $QWarehouse->fetchRow($where);
    if (!$warehouse){
        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->addMessage('This ID not exists');
        $this->_redirect(HOST."warehouse/bad-accessories-management");
    }

    $where = array();
    $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $good_id);
    $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $good_color);
    $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);

    $wh_product = $QWarehouseProduct->fetchRow($where);
    $this->view->wh_product = $wh_product;

}

if ($this->getRequest()->isXmlHttpRequest()) {
    $return = array(
        'quantity' => 0,
        'damage_quantity' => 0,
    );

    if (isset($wh_product) and $wh_product){
        $return['quantity'] = $wh_product['quantity'];
        $return['damage_quantity'] = $wh_product['damage_quantity'];
    }

    echo json_encode($return);
    exit;

}

$warehouses_cached = $QWarehouse->get_cache();
$this->view->warehouses_cached = $warehouses_cached;

$this->view->warehouse_id = $warehouse_id;



if ($this->getRequest()->getMethod() == 'POST')
{
    $unavailable        = $this->getRequest()->getParam('unavailable');
    $available          = $this->getRequest()->getParam('available');

    try {

        if ($wh_product){ //record existed

            $where = array();
            $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $good_id);
            $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $good_color);
            $where[]    = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);

            $data = array(
                'quantity' => $available,
                'damage_quantity' => $unavailable,
            );
            $QWarehouseProduct->update($data, $where);

        } else { //insert new

            $data = array(
                'quantity'      => $available,
                'damage_quantity'   => $unavailable,
                'good_id'       => $good_id,
                'good_color'    => $good_color,
                'warehouse_id'  => $warehouse_id,
                'cat_id'        => ACCESS_CAT_ID,
            );

            $QWarehouseProduct->insert($data);

        }



        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $data = array(
            'quantity'      => $available,
            'damage_quantity'   => $unavailable,
            'good_id'       => $good_id,
            'good_color'    => $good_color,
            'warehouse_id'  => $warehouse_id,
        );

        $info = 'Damage Accessories : ' . json_encode($data);


        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QLog = new Application_Model_Log();

        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('success')->addMessage('Update done!');
        $this->_redirect(HOST."warehouse/bad-accessories-management");

    } catch (Exception $e){

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->addMessage('Something went wrong! Please try again!');
        $this->_redirect(HOST."warehouse/bad-accessories-management");

    }
}