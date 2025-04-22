<?php
class Application_Model_FptAreaWarehouseMapping extends Zend_Db_Table_Abstract
{
    protected $_name = 'fpt_area_warehouse_mapping';

    function fetchPagination($page, $limit, &$total, $params) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        
        $select->join( array('w' => 'warehouse'), 'p.warehouse_id = w.id', array('warehouse_name' => 'w.name') );

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('warehouse_name', 'area_name')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'warehouse_name') {
                $order_str = 'w.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'area_name') {
                $order_str = 'p.`name` ' . $collate . $desc;
            } else {
                $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;
            }

            $select->order(new Zend_Db_Expr($order_str));
        } else {
            $select->order('p.id DESC');
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function get_warehouse($area = '', $province = '')
    {
        $province = trim(My_String::UnicodeToHop2DungSan($province));

        // check ngoại lệ - theo tỉnh
        $QFptException = new Application_Model_FptWarehouseException();
        $where = $QFptException->getAdapter()->quoteInto('LOWER(province) LIKE ?', strtolower($province));
        $exception = $QFptException->fetchRow($where);

        if ($exception) {
            return !empty( $exception['warehouse_id'] ) ? $exception['warehouse_id'] : false;
        } else {
            // không phải ngoại lệ thì check theo khu vực
            $area = trim(My_String::UnicodeToHop2DungSan($area));

            $where = $this->getAdapter()->quoteInto('LOWER(name) LIKE ?', strtolower($area));
            $mapping = $this->fetchRow($where);

            if ($mapping && !empty($mapping['warehouse_id'])) {
                return $mapping['warehouse_id'];
            }

            return false;
        }

    }
}