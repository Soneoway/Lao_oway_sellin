<?php

  //auto refresh
        // $this->view->meta_refresh = 30;

      
        $po = $this->getRequest()->getParam('po');
        $created_at_from_po = $this->getRequest()->getParam('created_at_from_po');
        $created_at_to_po = $this->getRequest()->getParam('created_at_to_po');
                
        $co = $this->getRequest()->getParam('co');
        $created_at_from_co = $this->getRequest()->getParam('created_at_from_co');
        $created_at_to_co = $this->getRequest()->getParam('created_at_to_co');
                
        $limit = LIMITATION;
        $total = 0;

        $params = array_filter(array(
            'co' => $co,
            'created_at_from_co' => $created_at_from_co,
            'created_at_to_co' => $created_at_to_co,
            'po' => $po,
            'created_at_from_po' => $created_at_from_po, 
            'created_at_to_po' => $created_at_to_po,           
        ));

        // --------------------start CO----------------------------------//
        $QChangeSalesOrder = new Application_Model_ChangeSalesOrder();
        $where = "";//$QChangeSalesOrder->getAdapter()->quoteInto('cat_id = ?', $cat_id);
        $totalCO = 0;

        $warrantyCO = $QChangeSalesOrder->fetchPaginationWarrantyCO($params,$totalCO);
        $warrantyCOTotal = $QChangeSalesOrder->fetchPaginationWarrantyCO("",$totalCOTotal);

        $co_receive_total = 0;
        foreach ($warrantyCOTotal as $key => $value) {
            if($key = "total_qty_product_receive"){                
                $co_receive_total += (int)$value[$key];
            }
        }
         $this->view->co_receive_total = $co_receive_total;
         $this->view->totalCO = $totalCO;
         $this->view->warrantyCO = $warrantyCO;
        // --------------------end CO----------------------------------//

        // --------------------start PO----------------------------------//
        $QPurchaseOrder = new Application_Model_PurchaseOrder();
        $where = "";//$QPurchaseOrder->getAdapter()->quoteInto('cat_id = ?', $cat_id);
        $totalPO = 0;

        $warrantyPO = $QPurchaseOrder->fetchPaginationWarrantyPO($params,$totalPO);
        $warrantyPOTotal = $QPurchaseOrder->fetchPaginationWarrantyPO("",$totalPOTotal);

        $po_receive_total = 0;
        foreach ($warrantyPOTotal as $key => $value) {
            if($key = "num"){                
                $po_receive_total += (int)$value[$key];
            }
        }

        $this->view->po_receive_total = $po_receive_total;
        $this->view->totalPO = $totalPO;
        $this->view->warrantyPO = $warrantyPO;

        // --------------------end PO----------------------------------//
        $this->view->params =$params; 
       

       

        // $flashMessenger = $this->_helper->flashMessenger;
        // $messages = $flashMessenger->setNamespace('error')->getMessages();
        // $this->view->messages = $messages;

        // $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        // $this->view->messages_success = $messages_success;

        // if ($this->getRequest()->isXmlHttpRequest()) {
        //     $this->_helper->layout->disableLayout();

        //     $this->_helper->viewRenderer->setRender('partials/list');
        // }
?>