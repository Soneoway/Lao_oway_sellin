<?php
$userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //de admin in lun


        $this->_helper->layout->disableLayout();

        $name                = $this->getRequest()->getParam('name');
        $address             = $this->getRequest()->getParam('address');
        $delivery_address    = $this->getRequest()->getParam('delivery_address');
        $tax                 = $this->getRequest()->getParam('tax');
        $invoice_number      = $this->getRequest()->getParam('invoice_number');
        $invoice_prefix_id   = $this->getRequest()->getParam('invoice_prefix' , INVOICE_OPPO_SIGN);
        $warehouse_id        = $this->getRequest()->getParam('warehouse_id');
        $user_id             = $this->getRequest()->getParam('user_id');
        $invoice_date        = trim($this->getRequest()->getParam('invoice_date'));
        $po                  = $this->getRequest()->getParam('po');


        $product  = $this->getRequest()->getParam('product');
        $unit     = $this->getRequest()->getParam('unit');
        $quantity = $this->getRequest()->getParam('quantity');
        $price    = $this->getRequest()->getParam('price');
        $total    = $this->getRequest()->getParam('total');
        $ck = $this->getRequest()->getParam('ck');
        $service_id = $this->getRequest()->getParam('service_id' , 0);

        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $QWarehouse = new Application_Model_Warehouse();



        $user_id =  $user_id ? $user_id : 1;

        $sn = My_Sale_Order::generateSn();
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        try {
            // validate

            if (!$product || !is_array($product)) throw new Exception("Product is required", 3);
            if (!$unit || !is_array($unit)) throw new Exception("Unit is required", 3);
            if (!$quantity || !is_array($quantity)) throw new Exception("Quantity is required", 4);
            if (!$price || !is_array($price)) throw new Exception("Price is required", 5);
            

            if (isset($invoice_number) and $invoice_number) {
               if(!preg_match('/^[0-9]{7}$/', $invoice_number))
                throw new Exception("Invalid invoice number");
            }



            $name = trim($name);
            $address = trim($address);
            $delivery_address = trim($delivery_address);
            $tax = trim($tax);



            foreach ($product as $k => $p) {
                $product[$k] = trim($product[$k]);
                if (empty($p)) throw new Exception("Product is required in line " . ($k+1), 9);
                // if (!isset($unit[$k]) || empty($unit[$k])) throw new Exception("Unit is required in line " . ($k+1), 10);
                // if (!isset($quantity[$k]) || empty($quantity[$k]) || !is_numeric($quantity[$k])) throw new Exception("Quantity is required in line " . ($k+1), 11);
                // if (!isset($price[$k]) || empty($price[$k]) || !is_numeric($price[$k])) throw new Exception("Price is required in line " . ($k+1), 12);
                //  if (!isset($total[$k]) || empty($total[$k]) || !is_numeric($total[$k])) throw new Exception("Total is required in line " . ($k+1), 13);
            }

            // save data

            $QCustomOrder = new Application_Model_CustomOrder();
            $invoice_date_temp = explode('/' , $invoice_date);
            $invoice_date_temp = $invoice_date_temp[2] . '-' . $invoice_date_temp[1] . '-' . $invoice_date_temp[0];

            $data = array(
                'sn'               => $sn,
                'customer_name'    => $name,
                'address'          => $address,
                'delivery_address' => $delivery_address,                
                'tax'              => $tax,
                'created_at'       => $invoice_date_temp ? $invoice_date_temp : date('Y-m-d H:i:s'),
                'printed_at'       => date('Y-m-d H:i:s'),
                'created_by'       => $user_id,
                'service_id'       => intval($service_id)
             );

            $id = $QCustomOrder->insert($data);

            $QCustomOrderDetail = new Application_Model_CustomOrderDetail();

            foreach ($product as $k => $p) {
                $data = array(
                    'custom_order_id' => $id,
                    'product_name'    => $p,
                    'unit'            => !empty($unit[$k]) ? $unit[$k] : null,
                    'quantity'        => !empty($quantity[$k]) ? $quantity[$k] : null,
                    'price'           => !empty($price[$k]) ? $price[$k] : null,
                    'total'           => !empty($total[$k]) ? $total[$k] : null,
                );

                $QCustomOrderDetail->insert($data);
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

        $this->view->invoice_date     = $invoice_date_temp ? $invoice_date_temp : '';
        $this->view->name             = $name;
        $this->view->address          = $address;
        $this->view->delivery_address = $delivery_address;
        $this->view->tax              = $tax;
        $this->view->product          = $product;
        $this->view->unit             = $unit;
        $this->view->quantity         = $quantity;
        $this->view->price            = $price;
        $this->view->total            = $total;
        $this->view->ck               = $ck;
        $this->view->po               = $po;
        //  $this->view->invoice_prefix = $invoice_prefix ? $invoice_prefix : INVOICE_OPPO_SIGN;
        $this->view->service_id = $service_id ? $service_id : '';

        $this->view->sn = $sn;
        // My_Image_Barcode::renderNoCode($sn);
        $QInvoiceNumber = new Application_Model_InvoiceNumber();


        if(empty($invoice_number))
        {
            $invoice_number = $QInvoiceNumber->getLastId($invoice_prefix['warehouse_id']);
        }
        $this->view->invoice_number = $invoice_number;

