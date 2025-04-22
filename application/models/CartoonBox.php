<?php
class Application_Model_CartoonBox extends Zend_Db_Table_Abstract
{
    protected $_name = 'cartoon_box';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.cartoon_box_number', 'p.shipping_time'));

        $select->join(array('po' => 'purchase_order_cartoon_box'), 'p.cartoon_box_number=po.cartoon_box_number', array('po.po_sn'))
            ->join(array('car' => 'cartoon_box'), 'car.cartoon_box_number=po.cartoon_box_number', array())
            ->join(array('flog' => 'file_upload_log'), 'flog.id=car.file_upload_log_id', array('flog.hash'));

        $join_po = false;

        if (isset($params['po_sn']) and $params['po_sn']) {
            if (is_array($params['po_sn']) && count($params['po_sn']))
                $select->where('po.po_sn IN (?)', $params['po_sn']);

            elseif (is_numeric($params['po_sn']))
                $select->where('p.po_sn = ?', $params['po_sn']);
        }

        if (isset($params['cartoon_box_number']) and $params['cartoon_box_number']) {
            if (is_array($params['cartoon_box_number']) && count($params['cartoon_box_number']))
                $select->where('p.cartoon_box_number IN (?)', $params['cartoon_box_number']);

            elseif (is_numeric($params['cartoon_box_number']))
                $select->where('p.cartoon_box_number = ?', $params['cartoon_box_number']);
        }

        if (isset($params['imei']) and $params['imei']) {
            $select->joinLeft(array('i' => 'cartoon_box_imei'), 'p.cartoon_box_number=i.cartoon_box_number', array('i.imei_sn'));

            if (is_array($params['imei']) && count($params['imei']))
                $select->where('i.imei_sn IN (?)', $params['imei']);

            elseif (is_numeric($params['imei']))
                $select->where('i.imei_sn = ?', $params['imei']);

            else
                $select->where('1=0', 1);
        }

        if (isset($params['shipping_from']) && date_create_from_format("d/m/Y", $params['shipping_from'])) {
            $select->where('p.shipping_time >= ?', date_create_from_format("d/m/Y", $params['shipping_from'])->format('Y-m-d'));
        }

        if (isset($params['shipping_to']) && date_create_from_format("d/m/Y", $params['shipping_to'])) {
            $select->where('p.shipping_time <= ?', date_create_from_format("d/m/Y", $params['shipping_to'])->format('Y-m-d 23:59:59'));
        }

        if (isset($params['po_from']) && date_create_from_format("d/m/Y", $params['po_from'])) {
            if (!$join_po) {
                $select->join(array('por' => 'purchase_order'), 'por.sn=po.po_sn', array());
                $join_po = true;
            }

            $select->where('por.created_at >= ?', date_create_from_format("d/m/Y", $params['po_from'])->format('Y-m-d'));
        }

        if (isset($params['po_to']) && date_create_from_format("d/m/Y", $params['po_to'])) {
            if (!$join_po) {
                $select->join(array('por' => 'purchase_order'), 'por.sn=po.po_sn', array());
                $join_po = true;
            }

            $select->where('por.created_at <= ?', date_create_from_format("d/m/Y", $params['po_to'])->format('Y-m-d 23:59:59'));
        }

        $select->order('p.shipping_time DESC');

        if (isset($params['export']) && $params['export'])
            return $select->__toString();

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }
}
