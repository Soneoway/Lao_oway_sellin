<?php
class Application_Model_InvoiceNumber extends Zend_Db_Table_Abstract{
	protected $_name = 'invoice_number';

    public function getLastId($warehouse_id , $service_id = 0)
    {
        try {
            $QInvoicePrefix = new Application_Model_InvoicePrefix();

            //load invoice prefix cua don hang
            $whereInvoicePrefix   = array();
            $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
            $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?', 1);
            $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type is null', null);
            $invoice_prefixs      = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

            if (!isset($invoice_prefixs)) {
                throw new exception("Invalid invoice prefix!Please try again.");
            }

            if (empty($invoice_prefixs['id']))
                $invoice_prefix = INVOICE_OPPO_SIGN;
            else
                $invoice_prefix = $invoice_prefixs['id'];


            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")))
                ->where("invoice_sign = ?", $invoice_prefix);

            if(!($service_id))
            {
                $select->where("service_id = 0")
                       ->where("service is null")
                ;
            }
            if (isset($service_id) and $service_id) {
                $select->where('service_id  = ?', intval($service_id));
                $select->where('updated_at is not null', null);

            }

            $result = $db->fetchOne($select);

            if(!($result) and $service_id)
            {
                $select = $db->select()
                    ->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")))
                    ->where("invoice_sign = ?", $invoice_prefix)
                ;
                $select->where('service_id  = ?', intval($service_id));
                $result = $db->fetchOne($select);
            }

            if (empty($result))
                $result = 0;

            $result = sprintf('%07d', intval($result + 1));

        } catch (exception $e) {
            echo($e->getMessage());
            exit;
            $result = 0;
        }

        return $result;

    }

    public function getLastServiceId($params)
    {
        try{
            $QWarehouse = new Application_Model_Warehouse();
            $QRange     = new Application_Model_Range();

            $service_id     = $params['service'];
            $invoice_prefix = $params['invoice_prefix'];

            $QInvoicePrefix = new Application_Model_InvoicePrefix();

            // tim khoang range cua so hoa don

            $whereRange   = array();
            $whereRange[] = $QRange->getAdapter()->quoteInto('object_id = ? ' , $service_id ? $service_id : '');
            $whereRange[] = $QRange->getAdapter()->quoteInto('object_type = ?' , INTERNAL_NUMBER_FOR_SERVICE_INVOICE );
            $range = $QRange->fetchRow($whereRange);


            if(empty($range) || !$range['first_input'] || !$range['last_input'])
            {
                throw new exception('Invalid range for object');
            }

            if(empty($invoice_prefix))
                throw new exception('Invalid invoice prefix');



            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")))
                ->where("invoice_sign = ?" , $invoice_prefix)
                ->where("invoice_number >= ?" , $range['first_input'])
                ->where("invoice_number <= ?" , $range['last_input'])
            ;

            $result = $db->fetchOne($select);


            if(empty($result))
                $result = intval($range['first_input']) - 1;

            if(intval($result + 1) > $range['last_input'] and intval($result + 1) < $range['last_input'])
            {
                throw new exception('Invalid range for object');
            }

            $result = sprintf('%07d', intval($result+1));

        }
        catch(exception $e)
        {
            echo ($e->getMessage());exit;
            $result = 0;
        }

        return $result;
    }

    public function getLastIdInvoice($params)
    {
        try {
            $QInvoicePrefix = new Application_Model_InvoicePrefix();
            $warehouse_id   = $params['warehouse_id'] ? $params['warehouse_id'] : '';
            $sales_sn       = $params['sn'] ? $params['sn'] : '';
            $invoice_prefix = $params['invoice_prefix'] ? $params['invoice_prefix'] : '';
            $currentTime    = date('Y-m-d H:i:s');

            if(empty($invoice_prefix))
            {
                //load invoice prefix cua don hang
                $whereInvoicePrefix = array();
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?', 1);
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type is null', null);
                $invoice_prefixs = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

                if (!isset($invoice_prefixs)) {
                    throw new exception("Invalid invoice prefix!Please try again.");
                }

                if (empty($invoice_prefixs['id']))
                    $invoice_prefix = INVOICE_OPPO_SIGN;
                else
                    $invoice_prefix = $invoice_prefixs['id'];
            }



            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")))
                ->where("invoice_sign = ?", $invoice_prefix);
            if (isset($service_id) and $service_id) {
                $select->where('service_id  = ?', intval($service_id));
                $select->where('updated_at is not null', null);
            }


            $result = $db->fetchOne($select);


            if (empty($result))
                $result = 0;

            $result = sprintf('%07d', intval($result + 1));

        } catch (exception $e) {
            echo($e->getMessage());
            exit;
            $result = 0;
        }

        // thêm số hóa đơn vào hệ thống
        $QInvoiceNumber = new Application_Model_InvoiceNumber();
        $data = array('invoice_number' => $result,
            'date'         => $currentTime ,
            'invoice_sign' => $invoice_prefix,
            'sn'           => $sales_sn,
        );

        $QInvoiceNumber->insert($data);
        return $result;
    }
    public function fixed()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")));
        $data = $db->fetchOne($select);
        $query = 'ALTER TABLE invoice_number auto_increment = ' . intval($data + 1);
        $result = $db->query($query);
        return $result;
    }
}