<?php
 $invoice_number          = $this->getRequest()->getParam('invoice_number');
        $invoice_sign            = $this->getRequest()->getParam('prefix', '');
        $sales_sn                = $this->getRequest()->getParam('sales_sn');
        $total_invoice_price     = $this->getRequest()->getParam('total_invoice_price');
        $total_invoice_vat       = $this->getRequest()->getParam('total_invoice_vat');
        $total_invoice_after_vat = $this->getRequest()->getParam('total_invoice_after_vat');
        $invoice_price           = $this->getRequest()->getParam('invoice_price');
        $service_id              = $this->getRequest()->getParam('service_id');

        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
        $QInvoiceNumber = new Application_Model_InvoiceNumber();
        $QCustomOrder   = new Application_Model_CustomOrder();
        $QLog           = new Application_Model_InvoiceLog();
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        if(!$invoice_number)
        {
            echo -1;
            exit;
        }

        if(!$invoice_sign)
        {
            echo -1;
            exit;
        }

        $where = $QCustomOrder->getAdapter()->quoteInto('sn = ?', $sales_sn);
        $market = $QCustomOrder->fetchRow($where);

        if (!$market)
        {
            echo '-2'; //sales sn error
            exit;
        }

        $data = array(
            'total'          => $total_invoice_price,
            'total_vat'      => $total_invoice_after_vat,
            'vat'            => $total_invoice_vat,
            'invoice_number' => $invoice_number,
        );

        $QCustomOrder->update($data, $where);

        $whereInvoiceNumber = array();
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign = ?' , $invoice_sign);
        $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

        if($exist_invoice_number and $sales_sn != $exist_invoice_number['sn'] and !$service_id)
        {
            echo '-6';
            exit;
        }

        $change_invoice = 0;
        $date = date('Y-m-d H:i:s');

        if (isset($total_invoice_after_vat) and $total_invoice_after_vat and isset($total_invoice_price) and
            $total_invoice_price and isset($total_invoice_vat) and $total_invoice_vat)
        {

            $QMarketInvoicePriceSn = new Application_Model_MarketInvoicePriceSn();
            $where = array();
            $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('sn = ?', $sales_sn);
            $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
            $m = $QMarketInvoicePriceSn->fetchRow($where);

            if ($m)
            {
                $QMarketInvoicePriceSn->update(array(
                    'total_invoice_price'     => $total_invoice_price,
                    'total_invoice_vat'       => $total_invoice_vat,
                    'total_invoice_after_vat' => $total_invoice_after_vat,
                    'invoice_number'          => trim($invoice_number)), $where);
            } else
            {
                $QMarketInvoicePriceSn->insert(array(
                    'sn'                      => $sales_sn,
                    'total_invoice_price'     => $total_invoice_price,
                    'total_invoice_vat'       => $total_invoice_vat,
                    'total_invoice_after_vat' => $total_invoice_after_vat,
                    'invoice_number'          => trim($invoice_number)));
            }
        }

        //update
        try
        {
            //tim so hoa don da co trong he thong chua
            $whereInvoiceNumber = array();

            $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
            if(isset($service_id) and $service_id)
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('service_id = ?' , $service_id);
            else
                $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('sn = ?' , $sales_sn);
            $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);



            if(isset($exist_invoice_number) and $exist_invoice_number) {
                $data = array(
                    'copy' => intval($exist_invoice_number['copy'] + 1),
                    'updated_at' => $date
                );

                $QInvoiceNumber->update($data , $whereInvoiceNumber);
            } else{



                $data = array('invoice_number' => $invoice_number,
                    'date'         => $date ,
                    'invoice_sign' => $invoice_sign,
                    'sn'           => $sales_sn,
                );
                //them so invoice vao he thong
                $id = $QInvoiceNumber->insert($data);

               


            }

            //them log
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info = 'Print Invoice : Sale order number: ' . $sales_sn . ' Invoice number : ' . $invoice_number . ' Copy : ' . $exist_invoice_number['copy'];
            $QLog->insert(array(
                'info'       => $info,
                'user_id'    => 1,
                'ip_address' => $ip,
                'time'       => $date,
            ));

            echo '0';
            $db->commit();
            exit;
        }
        catch (exception $e)
        {
            var_dump($e->getMessage());exit;
            $db->rollback();
            echo -4;
            exit;
        }
