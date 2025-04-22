<?php

class CronStaffOrderController extends My_Controller_Action
{
    public function init()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);

      date_default_timezone_set("Asia/Bangkok");
    }

    //http://local.wms-new/creditnote-api/cron-cn
    public function cronStaffExpiredOrderAction()
    {

      set_time_limit(0);
      ini_set('memory_limit', -1);

      //echo "OK";die;
      $QStaffSalesOrder = new Application_Model_EpPrivilegesTranOrder();

      try{

        $db = Zend_Registry::get('db');

        $db->beginTransaction();

        $date = date('Y-m-d H:i:s');

        $data['cancel_date']=$date;
        $data['cancel_by']='10'; 
        $data['status']='6';
        
        $data['hr_remark']='Expired! ระบบทำการยกเลิก "เนื่องจากไม่ยืนยันรายการตามเวลาที่กำหนด!!!"';
        

        $where = array();
        $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('payment_slip_image IS NULL', null);
        $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('staff_card_image IS NULL', null);
        $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('id_card_image IS NULL', null);
        $where[] = $QStaffSalesOrder->getAdapter()->quoteInto('status <>6', null);
        $create_box_number = $QStaffSalesOrder->update($data, $where);

        //die;
        $db->commit(); 
        echo "Done!";
        exit;
      }catch (Exception $e){

          $db->rollback();

          echo "Error";
          print_r($e.message);
          exit;
      }

    }


}