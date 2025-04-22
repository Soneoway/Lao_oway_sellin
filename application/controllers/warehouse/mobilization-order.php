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
        $user_id                 = $this->getRequest()->getParam('user_id');



        $product  = $this->getRequest()->getParam('product');
        $unit     = $this->getRequest()->getParam('unit');
        $in       = $this->getRequest()->getParam('in');
        $out      = $this->getRequest()->getParam('out');
        $price    = $this->getRequest()->getParam('price');
        $total    = $this->getRequest()->getParam('total');

        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $QWarehouse = new Application_Model_Warehouse();



        $user_id =  $user_id ? $user_id : 1;

        $sn = My_Sale_Order::generateSn();
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {
            // validate
            /*
            if (!$product || !is_array($product)) throw new Exception("Product is required", 3);
            if (!$unit || !is_array($unit)) throw new Exception("Unit is required", 3);
            if (!$in || !is_array($in)) throw new Exception("Quantity In is required", 4);
            if (!$out || !is_array($out)) throw new Exception("Quantity Out is required", 5);

            if (isset($invoice_number) and $invoice_number) {
               if(!preg_match('/^[0-9]{7}$/', $invoice_number))
                throw new Exception("Invalid invoice number");
            }
            */

            $order_name        = trim($order_name);
            $contract_name     = trim($contract_name);
            $contract_for_work = trim($contract_for_work);
            $contract_for_name = trim($contract_for_name);
            $transport_name    = trim($transport_name);
            $transport_type    = trim($transport_type);
            $from_warehouse    = trim($from_warehouse);
            $to_warehouse      = trim($to_warehouse);


            if(isset($product) and $product) {
                foreach ($product as $k => $p) {
                    $product[$k] = trim($product[$k]);
                    if (empty($p)) throw new Exception("Product is required in line " . ($k + 1), 9);
                    // if (!isset($unit[$k]) || empty($unit[$k])) throw new Exception("Unit is required in line " . ($k+1), 10);
                    // if (!isset($quantity[$k]) || empty($quantity[$k]) || !is_numeric($quantity[$k])) throw new Exception("Quantity is required in line " . ($k+1), 11);
                    // if (!isset($price[$k]) || empty($price[$k]) || !is_numeric($price[$k])) throw new Exception("Price is required in line " . ($k+1), 12);
                    //  if (!isset($total[$k]) || empty($total[$k]) || !is_numeric($total[$k])) throw new Exception("Total is required in line " . ($k+1), 13);
                }

                // save data
                $QInternalOrder = new Application_Model_InternalOrder();
                $QInternalOrderDetail = new Application_Model_InternalOrderDetail();

                $data = array(
                    'sn' => $sn,
                    'order_name' => $order_name,
                    'transport_date' => $transport_date,
                    'contract_name' => $contract_name,
                    'contract_for_name' => $contract_for_name,
                    'contract_for_work' => $contract_for_work,
                    'from_warehouse' => $from_warehouse,
                    'to_warehouse' => $to_warehouse,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $user_id,
                );

                $id = $QInternalOrder->insert($data);

                foreach ($product as $k => $p) {
                    $data = array(
                        'custom_order_id' => $id,
                        'product_name' => $p,
                        'mst' => !empty($unit[$k]) ? $unit[$k] : null,
                        'unit' => !empty($unit[$k]) ? $unit[$k] : null,
                        'in' => !empty($in[$k]) ? $in[$k] : null,
                        'price' => !empty($price[$k]) ? $price[$k] : null,
                        'total' => !empty($total[$k]) ? $total[$k] : null,
                    );

                    $QInternalOrderDetail->insert($data);
                }
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            $flashMessenger = $this->_helper->flashMessenger;
            echo sprintf("[%s] %s", $e->getCode(), $e->getMessage());
            exit;
        }





        $whereInvoicePrefix   = array();
        $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('id = ?' , $invoice_prefix_id);
        $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);

        $invoice_prefix       = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

        if(isset($invoice_prefix) and $invoice_prefix)
        {
            $this->view->invoice_prefix = $invoice_prefix->toArray();
            $warehouseRowset = $QWarehouse->find($invoice_prefix['warehouse_id']);
            $warehouse       = $warehouseRowset->current();
            $this->view->warehouse = $warehouse;
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
        $QInvoiceNumber = new Application_Model_InvoiceNumber();


        if(empty($invoice_number))
        {
            $invoice_number = $QInvoiceNumber->getLastId($invoice_prefix['warehouse_id']);
        }
        $this->view->invoice_number = $invoice_number;

