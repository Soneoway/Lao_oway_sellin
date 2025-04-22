<?php
class Application_Model_DeliverySales extends Zend_Db_Table_Abstract{
    protected $_name = 'delivery_sales';

    public function getDetail($id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->distinct()
            ->from(array('d' => $this->_name), array())
            ->join(array('m' => 'market'), 'd.sales_sn=m.sn', array('quantity' => 'SUM(m.num)'))
            ->join(array('c' => 'good_category'), 'c.id=m.cat_id', array('cat' => 'c.name'))
            // ->join(array('g' => 'good'), 'm.good_id=g.id', array('good' => 'g.name', 'gname' => 'g.desc'))
            ->where('d.delivery_order_id = ?', $id)
            ->group(array('m.cat_id'));

        return $db->fetchAll($select);
    }

    public function getDashboardDelivery($company){

        $dateNow = date("Y-m-d");

        // $start_date = '2017-05-22';
        $start_date = date("Y-m-d", strtotime('-1 day'));

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('count(*) as total, sum(case when kssl.shipment_status_id = "POD" and kssl.shipment_status_date >= "' . $dateNow . ' 00:00:00" and kssl.shipment_status_date <= "' . $dateNow . ' 23:59:59" then 1 else 0 end) as today, sum(case when (kssl.shipment_status_id <> "POD" OR kssl.shipment_status_id IS NULL) AND `do`.created_at >= "'.$start_date.'" then 1 else 0 end) AS pending'))
        ->joinLeft(array('do' => 'delivery_order'), 'do.id = p.delivery_order_id and do.status <> 6', array())
        ->joinLeft(array('kssl' => 'kerry_shipment_status_log'), 'kssl.tracking_no = do.tracking_no and LENGTH(kssl.shipment_status_id) < 10', array());
        $select->where('p.company = ?', $company);
        // $select->where('do.created_at >= ?', $start_date);
        // echo $select;die;
        return $db->fetchRow($select);
    }

    public function getTotalPODDeliveryByAccount($company){

        $dateNow = date("Y-m-d");

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('delivery_add_by, count(*) as pod_total, sd.id, sd.firstname, sd.lastname, TRIM(CONCAT(sd.firstname," ",sd.lastname))AS fullname, sd.company, case sd.company when 3 then "Genious" when 5 then "NKC" else "Kerry" end as company_logistics'))
        ->joinLeft(array('do' => 'delivery_order'), 'do.id = p.delivery_order_id and do.status <> 6', array())
        ->joinLeft(array('kssl' => 'kerry_shipment_status_log'), 'kssl.tracking_no = do.tracking_no and LENGTH(kssl.shipment_status_id) < 10', array())
        ->joinLeft(array('sd' => 'staff_delivery'), 'sd.id = kssl.delivery_add_by', array());
        $select->where('p.company = ?', $company);
        $select->where('kssl.shipment_status_id = ?', 'POD');
        $select->where('kssl.shipment_status_date >= ?', $dateNow . ' 00:00:00');
        $select->where('kssl.shipment_status_date <= ?', $dateNow . ' 23:59:59');
        $select->group('kssl.delivery_add_by');
        $select->order('pod_total desc');
        // echo $select;die;
        return $db->fetchAll($select);
    }

    public function getSoPendingDelivery($company){

        // $start_date = '2017-05-22';
        $start_date = date("Y-m-d", strtotime('-1 day'));

        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p'=> $this->_name), array('sn' => 'p.sales_sn', 'tracking_no' => 'do.tracking_no', 'sn_ref' => 'mar.sn_ref', 'invoice_number' => 'mar.invoice_number'))
        ->joinLeft(array('do' => 'delivery_order'), 'do.id = p.delivery_order_id and do.status <> 6', array())
        ->joinLeft(array('kssl' => 'kerry_shipment_status_log'), 'kssl.tracking_no = do.tracking_no and LENGTH(kssl.shipment_status_id) < 10', array())
        ->joinLeft(array('mar' => 'market'), 'mar.sn = p.sales_sn', array('mar.sn_ref'));
        $select->where('p.company = ?', $company);
        $select->where('do.created_at >= ?', $start_date);
        $select->where('kssl.shipment_status_id <> "POD" or kssl.shipment_status_id is null');
        $select->group('mar.sn');

        // echo $select;die;
        return $db->fetchAll($select);
    }
}