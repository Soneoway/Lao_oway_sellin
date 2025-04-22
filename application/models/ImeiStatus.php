<?php
class Application_Model_ImeiStatus extends Zend_Db_Table_Abstract
{
    protected $_name = 'imei_status';

    public function getStatus($imei, $date, $status = null)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));

        $select->where('i.from_date_unix <= ?', strtotime($date));

        if ($status) {
            $select
                ->join(array('io' => 'imei_status_info'), 'i.id=io.imei_status_id', array('io.*'))
                ->where('i.status = ?', $status);
        }

        $where = $select->getPart(Zend_Db_Select::WHERE);

        $sub_select = $db->select()
            ->from(array('i' => $this->_name), 
                array('maxdate' =>  new Zend_Db_Expr('MAX(i.from_date_unix)')))
            ->where(implode(' ', $where));
        
        $select->join(array('md' => $sub_select), 'i.from_date_unix=md.maxdate', array());


        if (!$status) {
            $result = $db->fetchRow($select);
            return isset($result['status']) ? $result['status'] : false;
        }

        $result = $db->fetchAll($select);
        $status = array();

        foreach ($result as $key => $value)
            $status[ $value['type_id'] ] = $value['value'];
        
        return $status;
    }
}
