<?php
class Application_Model_BundleGift extends Zend_Db_Table_Abstract
{
    protected $_name = 'bundle_gift';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.gift_id', 'p.good_id'));

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['sort']) && $params['sort']) {
            if ($params['sort'] == 'name') {
                $select->join(array('g' => 'good'), 'g.id=p.good_id', array());

                $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
                $order_str = 'g.`name` ' . $desc;

                $select->order(new Zend_Db_Expr($order_str));
            }
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function check_gift($good_ids = null)
    {
        if (!$good_ids) {
            return false;
        }

        foreach ($good_ids as $id) {
            if ($gifts = $this->get_gift($id)) {
                foreach ($gifts as $g) {
                   if (!in_array($g, $good_ids)) {
                       return array('good_id' => $id, 'gift_id' => $gifts);
                   }
                }
            }
        }

        return false;
    }

    private function get_gift($good_id = null)
    {
        if (!$good_id) return false;

        $where = $this->getAdapter()->quoteInto('good_id = ?', $good_id);
        $result = $this->fetchRow($where);

        if ($result && isset($result['gift_id'])) {
            return explode(',', $result['gift_id']);
        } else {
            return false;
        }
    }
}