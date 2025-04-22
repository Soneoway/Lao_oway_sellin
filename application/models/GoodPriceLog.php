<?php
class Application_Model_GoodPriceLog extends Zend_Db_Table_Abstract
{
    protected $_name = 'good_price_log';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
            ->join(array('g' => 'good'), 'g.id=p.good_id', array('good_name' => 'g.name'))
            ->joinLeft(array('c' => 'good_color'), 'c.id=p.color_id', array('color_name' => 'c.name'));

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('g.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('g.id = ?', $params['good_id']);

        if (isset($params['from_date']) and $params['from_date']){
            list($day, $month, $year) = explode('/', $params['from_date']);
            $from_date = $year.'-'.$month.'-'.$day;
            $select->where('p.from_date >= ?', $from_date);
        }

        if (isset($params['to_date']) and $params['to_date']){
            list($day, $month, $year) = explode('/', $params['to_date']);
            $to_date = $year.'-'.$month.'-'.$day;
            $select->where('p.to_date <= ? OR p.to_date IS NULL', $to_date);
        }

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    /**
     * Lấy giá bán lẻ của sản phẩm, tại thời điểm cụ thể
     * @param  int $good_id - Goods ID
     * @param  int $date    - Date in UNIX TIMESTAMP
     * @return int          goods price
     */
    public function get_price($good_id, $color_id, $date)
    {
        $check_by_color = " AND " .$this->getAdapter()->quoteInto('color_id = ?', $color_id);
        $check_all_model = " AND " .$this->getAdapter()->quoteInto('color_id IS NULL', 1);
        $where = $this->getAdapter()->quoteInto('good_id = ?', $good_id)
            . " AND " .$this->getAdapter()->quoteInto('from_date <= ?', $date)
            . " AND 
                ( "
                .$this->getAdapter()->quoteInto('to_date IS NULL', 1)
                . " OR "
                . $this->getAdapter()->quoteInto('to_date > ?', $date)
            . " ) ";
    
        // Kiểm tra các ngoại lệ trước
        // Ngoại lệ là các model có giá tùy thuộc vào màu sắc
        $result = $this->fetchRow($where . $check_by_color);

        if ( $result && isset($result['price']) )
            return $result['price'];

        // các trường hợp còn lại
        // giá như nhau cho tất cả các màu
        $result = $this->fetchRow($where . $check_all_model);

        if ( $result && isset($result['price']) )
            return $result['price'];

        return null;
    }
}