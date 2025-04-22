<?php
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //de admin in lun


        $this->_helper->layout->disableLayout();

        $order_name              = $this->getRequest()->getParam('order_name');
        $transport_date          = $this->getRequest()->getParam('transport_date');
        $contract_name           = $this->getRequest()->getParam('contract_name');
        $contract_for_name       = $this->getRequest()->getParam('contract_for_name');
        $contract_for_work       = $this->getRequest()->getParam('contract_for_work');
        $transport_name          = $this->getRequest()->getParam('transport_name');
        $transport_type          = $this->getRequest()->getParam('transport_type');
        $from_warehouse          = $this->getRequest()->getParam('from_warehouse');
        $to_warehouse            = $this->getRequest()->getParam('to_warehouse');
        $mst                     = $this->getRequest()->getParam('mst');
        $invoice_prefix_id       = $this->getRequest()->getParam('invoice_prefix' , INVOICE_OPPO_SIGN);
        $from_invoice_prefix     = $this->getRequest()->getParam('from_invoice_prefix');
        $to_invoice_prefix       = $this->getRequest()->getParam('to_invoice_prefix');
        $user_id                 = $this->getRequest()->getParam('user_id');
        $invoice_number          = $this->getRequest()->getParam('invoice_number');
        $invoice                 = $this->getRequest()->getParam('invoice');
        $sn                      = $this->getRequest()->getParam('change_order');
        $system                  = $this->getRequest()->getParam('system');
        $created_by              = $this->getRequest()->getParam('created_by');

        $product  = $this->getRequest()->getParam('product');
        $unit     = $this->getRequest()->getParam('unit');
        $in       = $this->getRequest()->getParam('in');
        $out      = $this->getRequest()->getParam('out');
        $price    = $this->getRequest()->getParam('price');
        $total    = $this->getRequest()->getParam('total');

        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $QWarehouse = new Application_Model_Warehouse();
        $QInvoiceNumber = new Application_Model_InternalNumber();
        $QMobilizationNumber = new Application_Model_MobilizationNumber();



        $user_id =  $user_id ? $user_id : 1;

        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {

            if (!$product || !is_array($product)) throw new Exception("Product is required", 3);
            if (!$unit || !is_array($unit)) throw new Exception("Unit is required", 3);
            if (!$in || !is_array($in)) throw new Exception("Quantity In is required", 4);
            if (!$out || !is_array($out)) throw new Exception("Quantity Out is required", 5);
            if (!$sn) throw new Exception("SN is required", 5);

            if (isset($invoice_number) and $invoice_number) {
               if(!preg_match('/^[0-9]{7}$/', $invoice_number))
                throw new Exception("Invalid invoice number");
            }

            $order_name        = trim($order_name);
            $contract_name     = trim($contract_name);
            $contract_for_work = trim($contract_for_work);
            $contract_for_name = trim($contract_for_name);
            $transport_name    = trim($transport_name);
            $transport_type    = trim($transport_type);

            if(isset($transport_date) and $transport_date)
            {
                $transport_date = explode('/' , $transport_date);
                $transport_date = $transport_date[2] . '-' . $transport_date[1] . '-' . $transport_date[0];
            }


            $date = date('Y-m-d h:i:s');


            $from_warehouse    = trim($from_warehouse);
            $from_warehouse    = unserialize($from_warehouse);
            $to_warehouse      = trim($to_warehouse);
            $to_warehouse      = unserialize($to_warehouse);
            
            $whereMobilizationNumber = array();
            $whereMobilizationNumber[] = $QMobilizationNumber->getAdapter()->quoteInto('order_name = ?' , $order_name);
            $whereMobilizationNumber[] = $QMobilizationNumber->getAdapter()->quoteInto('service_id = ?' , $from_warehouse['id']);
            $exist_invoice_number      = $QMobilizationNumber->fetchRow($whereMobilizationNumber);

            $temp = explode('/' , $order_name);


            if(isset($exist_invoice_number) and $exist_invoice_number)
            {
                $data = array(
                    'copy' => intval($exist_invoice_number['copy'] + 1),
                    'updated_at' => $date
                );
                $QMobilizationNumber->update($data , $whereMobilizationNumber);
            }
            else{


                $data = array(
                    'order_name'     => $order_name,
                    'date'           => $date ,
                    'service_id'     => $from_warehouse['id'],
                    'number'         => intval($temp[0])
                );
                //them so invoice vao he thong
                $id = $QMobilizationNumber->insert($data);

            }




            if(isset($product) and $product) {
                foreach ($product as $k => $p) {
                    $product[$k] = trim($product[$k]);
                   // if (empty($p)) throw new Exception("Product is required in line " . ($k + 1), 9);
                    // if (!isset($unit[$k]) || empty($unit[$k])) throw new Exception("Unit is required in line " . ($k+1), 10);
                    // if (!isset($quantity[$k]) || empty($quantity[$k]) || !is_numeric($quantity[$k])) throw new Exception("Quantity is required in line " . ($k+1), 11);
                    // if (!isset($price[$k]) || empty($price[$k]) || !is_numeric($price[$k])) throw new Exception("Price is required in line " . ($k+1), 12);
                    //  if (!isset($total[$k]) || empty($total[$k]) || !is_numeric($total[$k])) throw new Exception("Total is required in line " . ($k+1), 13);
                }

                // save data
                $QInternalOrder       = new Application_Model_InternalOrder();
                $QInternalOrderDetail = new Application_Model_InternalOrderDetail();


                //luu so hoa don va so lenh dieu dong vo he thong ke me tui no//




                //tim so hoa don da co trong he thong chua

                $whereorderExist = array();

                $whereorderExist[] = $QInternalOrder->getAdapter()->quoteInto('sn = ?' , $sn);
                $whereorderExist[] = $QInternalOrder->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
                $whereorderExist[] = $QInternalOrder->getAdapter()->quoteInto('invoice_prefix = ?' , $from_invoice_prefix);
                $orderExist        = $QInternalOrder->fetchRow($whereorderExist);

               /* if(isset($orderExist) and $orderExist and !$orderExist['is_back'])
                   throw new  Exception("Invoice is printed", 1);*/


                $data = array(
                    'sn'                => $sn,
                    'order_name'        => $order_name,
                    'transport_date'    => $transport_date,
                    'contract_name'     => $contract_name,
                    'contract_for_name' => $contract_for_name,
                    'contract_for_work' => $contract_for_work,
                    'from_warehouse'    => $from_warehouse['name'] ? $from_warehouse['name'] : '',
                    'to_warehouse'      => $to_warehouse['name'] ? $to_warehouse['name'] : '',
                    'created_at'        => date('Y-m-d H:i:s'),
                    'created_by_name'   => $created_by,
                    'invoice_number'    => $invoice_number ? $invoice_number : '',
                    'invoice_prefix'    => $from_invoice_prefix ? $from_invoice_prefix : '',
                 );
                if(!$orderExist)
                    $id = $QInternalOrder->insert($data);
                else if(isset($orderExist['is_back']) and $orderExist['is_back'])
                {
                    $dataBack = array('is_back' => 0 , 'is_back_time' => date('Y-m-d H:i:s'));
                    $QInternalOrder->update($dataBack, $whereorderExist);
                }
                else if(isset($orderExist))
                {
                    $QInternalOrder->update($data, $whereorderExist);
                }
                $total_price = 0;
                foreach ($product as $k => $p) {
                    $data = array(
                        'custom_order_id' => $id,
                        'product_name' => $p,
                        'mst' => !empty($mst[$k]) ? $mst[$k] : null,
                        'unit' => !empty($unit[$k]) ? $unit[$k] : null,
                        'in' => !empty($in[$k]) ? $in[$k] : null,
                        'price' => !empty($price[$k]) ? $price[$k] : null,
                        'total' => !empty($total[$k]) ? $total[$k] : null,
                    );

                    $total_price += intval($price[$k] * $in[$k]);
                    $QInternalOrderDetail->insert($data);
                }
            }



        $whereInvoicePrefixFrom    = array();
        $whereInvoicePrefixFrom[]  = $QInvoicePrefix->getAdapter()->quoteInto('id = ?' , $from_invoice_prefix);
        $whereInvoicePrefixFrom[]  = $QInvoicePrefix->getAdapter()->quoteInto('type = ?' , 2);
        $from_invoice_prefix       = $QInvoicePrefix->fetchRow($whereInvoicePrefixFrom);



        $whereInvoicePrefixTo   = array();
        $whereInvoicePrefixTo[] = $QInvoicePrefix->getAdapter()->quoteInto('id = ?' , $to_invoice_prefix);
        $whereInvoicePrefixTo[] = $QInvoicePrefix->getAdapter()->quoteInto('type = ?' , 2);
        $to_invoice_prefix       = $QInvoicePrefix->fetchRow($whereInvoicePrefixTo);


        if(isset($from_invoice_prefix) and $from_invoice_prefix and $to_invoice_prefix and isset($to_invoice_prefix))
        {
            $this->view->from_invoice_prefix = $from_invoice_prefix->toArray();
            $this->view->to_invoice_prefix   = $to_invoice_prefix->toArray();
            $warehouseInRowset     = $QWarehouse->find($from_invoice_prefix['warehouse_id']);
            $warehouse_in          = $warehouseInRowset->current();
            $warehouseOutRowset    = $QWarehouse->find($to_invoice_prefix['warehouse_id']);
            $warehouse_out         = $warehouseOutRowset->current();
            $this->view->from_warehouse_oppo = $warehouse_in;
            $this->view->to_warehouse_oppo   = $warehouse_out;
        }

      

        $this->view->order_name          = $order_name;
        $this->view->contract_name       = $contract_name;
        $this->view->contract_for_name   = $contract_for_name;
        $this->view->contract_for_work   = $contract_for_work;
        $this->view->transport_date      = $transport_date;
        $this->view->transport_name      = $transport_name;
        $this->view->transport_type      = $transport_type;
        $this->view->from_warehouse      = $from_warehouse;
        $this->view->to_warehouse        = $to_warehouse;
        $this->view->total_price         = $total_price ? $total_price : $total_price;
        $this->view->product             = $product;
        $this->view->mst                 = $mst;
        $this->view->unit                = $unit;
        $this->view->in                  = $in;
        $this->view->out                 = $out;
        $this->view->price               = $price;
        $this->view->total               = $total;
        //  $this->view->invoice_prefix = $invoice_prefix ? $invoice_prefix : INVOICE_OPPO_SIGN;

        $this->view->sn = $sn;
        // My_Image_Barcode::renderNoCode($sn);


        if(empty($invoice_number))
        {
            $invoice_number = $QInvoiceNumber->getLastId($from_invoice_prefix['warehouse_id']);
        }


        $this->view->invoice_number = $invoice_number;

        switch($invoice)
        {
            //in hoa don
            case 1:
            {
               
                $whereInvoiceNumber = array();
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign   = ?' , $from_invoice_prefix['id']);
                $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

                $whereorderExist = array();

                if(isset($exist_invoice_number) and $exist_invoice_number)
                {

                 /*   $data = array(
                        'copy' => intval($exist_invoice_number['copy'] + 1),
                        'updated_at' => $date
                    );
                    $QInvoiceNumber->update($data , $whereInvoiceNumber);
                    */
                    //throw new Exception("This invoice is printed", 1);
                    
                }
                else{

                    $data = array(
                        'invoice_number' => $invoice_number,
                        'date'           => $date ,
                        'invoice_sign'   => $from_invoice_prefix['id'],
                        'sn'             => $sn
                    );
                    //them so invoice vao he thong
                    $id = $QInvoiceNumber->insert($data);

                }
            }
                $this->_helper->viewRenderer('warehouse/internal-print', null, true);
                break;
            case 2:
            {
                $whereInvoiceNumber = array();
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign   = ?' , $from_invoice_prefix['id']);
                $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

                if(empty($exist_invoice_number))
                throw new Exception("Please print invoice as first", 1);


                $this->_helper->viewRenderer('warehouse/mobilization-list', null, true);
            }

                break;
            case 3:
            {
                $whereInvoiceNumber = array();
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign   = ?' , $from_invoice_prefix['id']);
                $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

                if(empty($exist_invoice_number))
                    throw new Exception("Please print invoice as first", 1);

                $this->_helper->viewRenderer('warehouse/mobilization-order', null, true);
            }

                break;
        }

            $db->commit();

        } catch (Exception $e) {
            $db->rollback();
            $flashMessenger = $this->_helper->flashMessenger;
            echo sprintf("[%s] %s", $e->getCode(), $e->getMessage());
            exit;
        }