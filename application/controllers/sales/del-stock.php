<?php
$flashMessenger   = $this->_helper->flashMessenger;
$sn = $this->getRequest()->getParam('sn');

if ($sn) {
    $QMarket = new Application_Model_MarketStock();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $sales = $QMarket->fetchRow($where);

    $back_url = HOST.'sales/stock';

    if ($sales) {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        // không cho xóa đơn người khác
        if (!isset($sales['user_id'])) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid user');
            $this->_redirect($back_url);
        } elseif ($sales['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID)) {
            $flashMessenger->setNamespace('error')->addMessage('You cannot delete this Order');
            $this->_redirect($back_url);
        }

        if (!$sales['shipping_yes_time'] and !$sales['pay_time'] and !$sales['outmysql_time'] ){

            try{

                $db = Zend_Registry::get('db');
                $db->beginTransaction();
                $QMarket->delete($where);

                $QImeiStock = new Application_Model_ImeiStock();
                $where = $QImeiStock->getAdapter()->quoteInto('market_stock_sn = ?', $sn);
                $QImeiStock->delete($where);

                //todo log
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $info = 'Delete Stock Order SN : ' . $sn;
                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                $QLog = new Application_Model_Log();
                $QLog->insert( array (
                    'info' => $info,
                    'user_id' => $userStorage->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ) );

                $flashMessenger->setNamespace('success')->addMessage('Done!');

                //commit
                $db->commit();

            } catch (Exception $e){
                $db->rollback();
                $flashMessenger->setNamespace('error')->addMessage('Cannot delete this Order, please check order status!');
            }

        } else
            $flashMessenger->setNamespace('error')->addMessage('Cannot delete this Order, please check order status!');
    } else {
        $flashMessenger->setNamespace('error')->addMessage('Invalid Order');
    }

} else {
    $flashMessenger->setNamespace('error')->addMessage('Invalid SN');
}

$this->_redirect($back_url);
