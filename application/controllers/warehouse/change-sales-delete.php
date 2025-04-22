<?php

$back_url_refer = $this->getRequest()->getServer('HTTP_REFERER') ? $this->getRequest()->
    getServer('HTTP_REFERER') : '/warehouse/'; 

set_time_limit(0);
ini_set('memory_limit', -1);

$flashMessenger = $this->_helper->flashMessenger;

$id          = $_POST['id'];
$reason      = $_POST['reason'];

$userStorage = Zend_Auth::getInstance()->getStorage()->read();

try{
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

	$data['delete_status'] = 1 ;
	$data['delete_by'] = $userStorage->id;
	$data['delete_reason'] = $reason ;
	$data['delete_date'] = date('Y-m-d H:i:s') ;
	$QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
	$where = $QChangeSalesOrder->getAdapter()->quoteInto('id = ?', $id);
	$QChangeSalesOrder->update($data, $where);

	$db = Zend_Registry::get('db');
    $QBL = new Application_Model_BorrowingList();

	$select = $db->select()->from(array('p' => 'change_sales_order'), array('p.borrowing_id'));
	$select->where('p.id = ?',$id);
	$getDetail = $db->fetchRow($select);

	if($getDetail){

		// update borrowing status for app
		if(isset($getDetail['borrowing_id']) and $getDetail['borrowing_id']){

			$getBorrowingList = $QBL->getDetailsBorrowingByID($getDetail['borrowing_id']);

			if($getBorrowingList){
                
	            //status 12 is cancel by wms
				$update = array(
							'read_data' => 1,
	                        'update_datetime' => date('Y-m-d H:i:s'),
	                        'status' => 12,
		                    'wms_status' => 2,
		                    'wms_datetime' => date('Y-m-d H:i:s'),
		                    'wms_by' => $userStorage->id,
		                    'remark' => $reason
		                );

		        $where_update = $QBL->getAdapter()->quoteInto('id = ?', $getDetail['borrowing_id']);
		        $status_update_12 = $QBL->update($update,$where_update);
			}
		}
	}

	$db->commit();

	if(isset($status_update_12) && $status_update_12){
        $data_curl_12 = array('code' => $getBorrowingList['code'], 'status' => 12, 'rq' => $getBorrowingList['rq']);

        $handle = curl_init(API_IOPPO_URL . 'warehouse-notification');
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $data_curl_12);
        curl_exec($handle);
        curl_close($handle);
    }

} catch (Exception $e){
    $db->rollback();

    $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
    $this->_redirect(HOST."warehouse/change-sales-list");
}

$flashMessenger->setNamespace('success')->addMessage('Done!');

$this->_redirect($back_url_refer);