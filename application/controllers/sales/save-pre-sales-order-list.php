<?php
$flashMessenger = $this->_helper->flashMessenger;



if ($this->getRequest()->getMethod() == 'POST'){
    //print_r($_POST);die;
    $presales_sn      = $this->getRequest()->getParam('presales_sn');
    $admin_remark      = $this->getRequest()->getParam('admin_remark');
    $sales_order_no      = $this->getRequest()->getParam('sales_order_no');
    $admin_remark      = $this->getRequest()->getParam('admin_remark');
    $status      = $this->getRequest()->getParam('status');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QPreSalesOrder = new Application_Model_PreSalesOrder();

    $data=null;
    $cn_data=null;
    try{

        $db = Zend_Registry::get('db');

        $db->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $data['admin_confirm_date']=$date;
        $data['admin_id']=$userStorage->id; 
        $data['admin_remark']=$admin_remark;
        //$data['sales_order_sn']=$sales_order_no;
        $data['status']=$status;

        $where = array();
        $where[] = $QPreSalesOrder->getAdapter()->quoteInto('presales_sn = ?', $presales_sn);
        $create_box_number = $QPreSalesOrder->update($data, $where);

        //die;
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $db->commit(); 

       // die;
    }catch (Exception $e){

        $db->rollback();

        echo '<script>
                parent.palert("Cannot Create Pre-SalesOrder, Please try again!");
              </script>';
        exit;
    }
}

echo '<script>parent.location.href="/sales/pre-sales-order-list"</script>';
exit;


