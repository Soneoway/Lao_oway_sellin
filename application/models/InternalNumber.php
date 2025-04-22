<?php
class Application_Model_InternalNumber extends Zend_Db_Table_Abstract{
	protected $_name = 'internal_number';

    public function getLastId($params)
    {
        try{
                $QWarehouse = new Application_Model_Warehouse();
                $QRange     = new Application_Model_Range();

                $from_warehouse = $params['from_warehouse'];


               /* if(!isset($from_warehouse))
                {
                    throw new exception("Invalid Showroom!Please try again.");
                }

                if(!isset($from_warehouse['warehouse_id']))
                {
                    throw new exception("Invalid Warehouse!Please try again.");
                }

                $warehouse_rowset = $QWarehouse->find($from_warehouse['warehouse_id']);
                $warehouse        = $warehouse_rowset->current();

                if(!isset($warehouse))
                {
                    throw new exception("Invalid Warehouse!Please try again.");
                }
                */
                $QInvoicePrefix = new Application_Model_InvoicePrefix();

                //load invoice prefix cua don hang
                $whereInvoicePrefix = array();
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('warehouse_id = ?' , $from_warehouse['warehouse_id']);
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);
                $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type = ?' , 2);
                $invoice_prefixs      = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

                
                /*
                if(!isset($invoice_prefixs))
                {
                    throw new exception("Invalid invoice prefix!Please try again.");
                }
                */

                // tim khoang range cua so hoa don

                $whereRange = array();
                $whereRange[] = $QRange->getAdapter()->quoteInto('object_id = ? ' , $from_warehouse['id'] ? $from_warehouse['id'] : '');
                $whereRange[] = $QRange->getAdapter()->quoteInto('object_type = ?' , INTERNAL_NUMBER_FOR_SERVICE );
                $range = $QRange->fetchRow($whereRange);


                if(empty($range) || !$range['first_input'] || !$range['last_input'])
                {
                    throw new exception('Invalid range for object');
                }

                if(empty($invoice_prefixs['id']))
                    $invoice_prefix = INVOICE_OPPO_SIGN;
                else
                    $invoice_prefix = $invoice_prefixs['id'];



                $db = Zend_Registry::get('db');

                $select = $db->select()
                    ->from("internal_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")))
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