<?php

class StaffOrderController extends My_Controller_Action
{
    public function indexAction()
    {
    	set_time_limit( 0 );
        error_reporting( 0 );
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $from_date  = $this->getRequest()->getParam('from_date');
        $to_date    = $this->getRequest()->getParam('to_date');
        $sn         = $this->getRequest()->getParam('sn');
        $staff_name = $this->getRequest()->getParam('staff_name');
        $staff_code = $this->getRequest()->getParam('staff_code');
        $type       = $this->getRequest()->getParam('type');
        $id_number  = $this->getRequest()->getParam('id_number');
        $staff_id   = $this->getRequest()->getParam('staff_id');
        $export     = $this->getRequest()->getParam('export',0);
        $page       = $this->getRequest()->getParam('page',1);
        $company_id = $this->getRequest()->getParam('company_id');
        $for_partner = $this->getRequest()->getParam('for_partner');
        
		$total       = 0;
		$limit       = LIMITATION;
		$params      = array(
                'sn' => trim($sn),
				'staff_name' => trim($staff_name),
				'staff_code' => trim($staff_code),
				'type'       => intval($type),
				'id_number'  => trim($id_number),
				'staff_id'   => $staff_id,
                'from_date'  => $from_date,
                'to_date'    => $to_date,
                'company_id' => $company_id,
                'for_partner' => $for_partner
			);
		$QStaffOrder = new Application_Model_StaffOrder();
		if($export){
			$list = $QStaffOrder->fetchPagination($page,NULL,$total,$params);
			$this->_report($list);
			exit;
		}
        
		$list        = $QStaffOrder->fetchPagination($page,$limit,$total,$params);


        $this->view->list             = $list;
        $this->view->limit            = $limit;
        $this->view->total            = $total;
        $this->view->page             = $page;
        $this->view->offset           = $limit*($page-1);
        $this->view->params           = $params;
        $this->view->url              = HOST.'staff-order/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $flashMessenger = $this->_helper->flashMessenger;
        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages_success = $messages_success;
        $this->view->messages         = $messages;

		$staff_order_types = unserialize(STAFF_ORDER_TYPE);
		$this->view->staff_order_types = $staff_order_types;

        $arr_company = array(
            1 => 'OPPO',
            4 => 'INGAME',
        );
        $this->view->companies = $arr_company;

        $arr_for_partner = array(
            -1 => 'All',
            0 =>  'Staff',
            1 =>  'Partner'
        );

        $this->view->arr_for_partner = $arr_for_partner;
    }

    public function _report($data){
    	require_once 'PHPExcel.php';
        $PHPExcel = new PHPExcel();
        $heads = array(
            'Stt',
            'Công ty',
            'Code',
            'Họ tên',
            'CMND',
            'Khu vực',
            'Lô máy',
            'Model',
            'Màu',
            'Mức ưu đãi',
            'Giá sau ưu đãi',
            'Ngày lên đơn',
            'Mã đơn hàng',
            'Số imei',
            'Số hóa đơn',
            //'Hình thức thanh toán',
            'Ngày vào tiền',
            'Số phiếu thu',
            'Tiền nhận theo đơn hàng',
            'Số dư theo đơn hàng',
            'Tiền thanh toán',
            'Số dư',
            'Kho xuất hàng',

        );

        $PHPExcel->setActiveSheetIndex(0);
        $sheet    = $PHPExcel->getActiveSheet();
        $index    = 1;
       
        $alpha    = 'A';
        
        foreach($heads as $key)
        {
            $sheet->setCellValue($alpha.$index, $key);
            $alpha++;
        }
        $index    = 2;
        $staff_order_types = unserialize(STAFF_ORDER_TYPE);
        $i = 1;
        foreach($data as $item){
            $alpha = 'A';
           
            $market_created = '';
            if($item['market_created'] != NULL OR $item['market_created'] != ''){
                $market_created = date('d/m/Y',strtotime($item['market_created']));
            }

            if($item['payment_date'] != NULL OR $item['payment_date'] != ''){
                $payment_date = date('d/m/Y',strtotime($item['payment_date']));
            }

            $sheet->setCellValue($alpha++.$index, $i++);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['company_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['staff_code'], PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue($alpha++.$index, $item['staff_name']);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $item['id_number'], PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue($alpha++.$index, $item['area_name']);
			$sheet->setCellValue($alpha++.$index, $item['block']);
            $sheet->setCellValue($alpha++.$index, $item['good_name']);
            $sheet->setCellValue($alpha++.$index, $item['good_color_name']);
			$sheet->setCellValue($alpha++.$index, $staff_order_types[$item['type']]);
			$sheet->setCellValue($alpha++.$index, $item['price_after']);
            $sheet->getCell($alpha++.$index)->setValueExplicit( $market_created, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['sn'], PHPExcel_Cell_DataType::TYPE_STRING);

            $sheet->getCell($alpha++.$index)->setValueExplicit(str_replace(',',' ,',$item['imei']), PHPExcel_Cell_DataType::TYPE_STRING);
			$sheet->setCellValue($alpha++.$index, $item['invoice_number']);
			//$sheet->setCellValue($alpha++.$index, ''); //Hình thức thanh toán
			$sheet->setCellValue($alpha++.$index, $payment_date);
            $sheet->setCellValue($alpha++.$index, $item['number_sales_order']);//số phiếu thu
            $sheet->setCellValue($alpha++.$index, $item['receive_by_sn']);
            $sheet->setCellValue($alpha++.$index, $item['balance_by_sn']);
            $sheet->setCellValue($alpha++.$index, $item['actual_amount_paid']);
			$sheet->setCellValue($alpha++.$index, $item['balance_all']);
            $sheet->getCell($alpha++.$index)->setValueExplicit($item['warehouse_name'], PHPExcel_Cell_DataType::TYPE_STRING);
            $index++;

        }

        $filename = 'Staff_order_'.date('d/m/Y');
        $objWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        $objWriter->save('php://output');
        exit;
    }
} 