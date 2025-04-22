<?php
$flashMessenger = $this->_helper->flashMessenger;


if ($this->getRequest()->getMethod() == 'POST'){
    //print_r($_POST);die;
    $privileges_sn      = $this->getRequest()->getParam('privileges_sn');
    $company_id      = $this->getRequest()->getParam('company_id');
    $hr_remark      = $this->getRequest()->getParam('hr_remark');
    $sales_order_no      = $this->getRequest()->getParam('sales_order_no');
    //$hr_remark      = $this->getRequest()->getParam('hr_remark');
    $status      = $this->getRequest()->getParam('status');

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $QQuotaProduct = new Application_Model_EpPrivilegesQuotaProduct();
    $QQuotaProduct->_initConfig($company_id);
    $db = Zend_Registry::get('db');
    $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();
    $data=null;
    $cn_data=null;
    try{
        $db->beginTransaction();

        $date = date('Y-m-d H:i:s');

        if($status=='6'){
            $data['cancel_date']=$date;
            $data['cancel_by']=$userStorage->id; 
            $data['status']=$status;
        }else{
            $data['hr_confirm_date']=$date;
            $data['hr_comfirm_by']=$userStorage->id; 
            $data['status']=$status;
        }
        
        $data['hr_remark']=$hr_remark;
        
        //print_r($db);
        //print_r($data);die;
        $where = array();
        $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('privileges_sn = ?', $privileges_sn);
        $create_box_number = $QStaffSalesOrder->update($data, $where);

        //die;
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $rs = $db->commit(); 
        //print_r($rs);die;
       // die;
    }catch (Exception $e){
        //print_r($e);die;
        $db->rollback();

        echo '<script>
                parent.palert("Cannot Update Staff-SalesOrder, Please try again!");
              </script>';
        exit;
    }
}

echo '<script>parent.location.href="/sales/staff-sales-order-list"</script>';
exit;


