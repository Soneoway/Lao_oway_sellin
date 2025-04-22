<?php
$flashMessenger   = $this->_helper->flashMessenger;
$list_id = $this->getRequest()->getParam('id');

foreach ($list_id as $sn) {
    if ($sn) {
    //print_r($_GET);die;
    $QMarket = new Application_Model_Market();
    $QJobNumber = new Application_Model_JobNumber();
    $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();
    $QQLogQuotaTranDistributor = new Application_Model_LogQuotaTranDistributor();
    $QQLogQuotaTran = new Application_Model_LogQuotaTran();
    $where1 = $QQLogQuotaTranDistributor->getAdapter()->quoteInto('sn = ?', $sn);
    $where2 = $QQLogQuotaTran->getAdapter()->quoteInto('sn = ?', $sn);
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket->fetchRow($where);

    $back_url = '/sales';

    if ($sales) {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        // không cho xóa đơn người khác
        /*
        if (!isset($sales['user_id'])) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid user');
            $this->_redirect($back_url);
        } elseif ($sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID)) {
            $flashMessenger->setNamespace('error')->addMessage('You cannot delete this Order');
            $this->_redirect($back_url);
        }
        */

        if (!isset($sales['user_id'])) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid user');
            $this->_redirect($back_url);
        } else if ($sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, array(ADMINISTRATOR_ID,CANCEL_ORDER)))
        {

            if ($sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN_ACCESSORIES))
            {
                if ($sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN))
                {
                //echo '-5'; //status error SALES_ADMIN
                $flashMessenger->setNamespace('error')->addMessage('Cannot delete this Order, please check order status!');
                $this->_redirect($back_url);
                exit;
                }
            }
        }

        
        if (!$sales['shipping_yes_time'] and !$sales['pay_time'] and !$sales['outmysql_time'] ){

            try{

                $db = Zend_Registry::get('db');

                $db->beginTransaction();
                
                $QQLogQuotaTranDistributor->delete($where1);
                $QQLogQuotaTran->delete($where2);
                $QMarket->delete($where);

                $QPreSalesOrder = new Application_Model_PreSalesOrder();
                $array_data = array('sales_order_sn' => null,'status' => 0,
                                    'sales_order_date' => null);

                $where_pre = $QPreSalesOrder->getAdapter()->quoteInto('sales_order_sn = ?', $sn);
                $QPreSalesOrder->update($array_data, $where_pre);

                $array_staff = array('sales_order_sn' => null,'status' => 2,
                                    'hr_confirm_date' => null,'hr_comfirm_by' => null);

                $where_staff = $QStaffSalesOrder->getAdapter()->quoteInto('sales_order_sn = ?', $sn);
                $QStaffSalesOrder->update($array_staff, $where_staff);

                $where_job = $QJobNumber->getAdapter()->quoteInto('sales_order = ?', $sn);
                $QJobNumber->delete($where_job);
                // if order is return
                if ($sales['isbacks']==1){

                    $back_url = '/sales/return-list';

                    //delete from imei return
                    $QImeiReturn = new Application_Model_ImeiReturn();
                    $where = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                    $QImeiReturn->delete($where);

                    //delete from product return
                    $QProductReturn = new Application_Model_ProductReturn();
                    $where = $QProductReturn->getAdapter()->quoteInto('return_sn = ?', $sn);

                    $QProductReturn->delete($where);

                }

                //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info = 'Delete Sales number : ' . $sn;

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

                $QLog = new Application_Model_Log();

                $QLog->insert( array (
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ) );

                $flashMessenger->setNamespace('success')->addMessage('Done!');

                //Tanong Delete Credit Note
                $distributor_id=0;
                $result_status = $this->saveAPICreditNoteAction($db,$distributor_id,$sn,'no_discount');

                $result_status = $this->saveAPIDepositAction($db,$distributor_id,$sn,'no_discount');
                
                //commit
                $db->commit();

            } catch (Exception $e){

                $db->rollback();

                $flashMessenger->setNamespace('error')->addMessage('Cannot delete this Order, please check order status!'.$e.message);

            }

        } else

            $flashMessenger->setNamespace('error')->addMessage('Cannot delete this Order, please check order status!');
    } else {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Order');
    }

} else {
    $flashMessenger->setNamespace('error')->addMessage('Invalid SN');
}
}

$this->_redirect($back_url);