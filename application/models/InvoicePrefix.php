<?php
class Application_Model_InvoicePrefix extends Zend_Db_Table_Abstract
{


    protected $_name = 'invoice_prefix';

    function get_cache()
    {
        $cache = Zend_Registry::get('cache');
        $result = $cache->load($this->_name . '_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'name');

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name . '_cache', array(), null);
        }
        return $result;
    }

    function getPrefix($warehouse_id)
    {
        if(!$warehouse_id)
            return null;

        $QInvoicePrefix = new Application_Model_InvoicePrefix();

        $whereInvoicePrefix   = array();
        $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('warehouse_id = ?' , $warehouse_id);
        $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('status = ?' , 1);
        $whereInvoicePrefix[] = $QInvoicePrefix->getAdapter()->quoteInto('type is null' , null);
        $invoice_prefix       = $QInvoicePrefix->fetchRow($whereInvoicePrefix);

        return $invoice_prefix;

    }
}
