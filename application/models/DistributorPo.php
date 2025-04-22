<?php
class Application_Model_DistributorPo extends Zend_Db_Table_Abstract{

    protected $_name = 'distributor_po';

    public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => 'distributor_po'),array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'),
                'p.*',
                'fullname'      => 'CONCAT(s.firstname," ",s.lastname)',
                'status_check'  => 'MIN(CASE WHEN m.shipping_yes_time IS NULL THEN UNIX_TIMESTAMP(0) ELSE UNIX_TIMESTAMP(m.shipping_yes_time) END) '
            ))
            ->join(array('m'=>'market'),'m.po_id = p.id',array(NULL))
            ->join(array('s'=>'staff'),'p.created_by = s.id',array(NULL))
            ->where('m.status = 1')
            ->group('p.id')
        ;
        if(isset($params['po_name']) AND $params['po_name']){
            $select->where('p.po_name LIKE ?','%'.$params['po_name'].'%');
        }

        if( isset($params['d_id']) AND $params['d_id']){
            $select->where('distributor_id = ?',$params['d_id']);
        }

        if( isset($params['id']) AND $params['id']){
            $select->where('id = ?',$params['id']);
        }

        if( isset($params['status']) AND $params['status']){
            if($params['status'] == 1){//chưa check
                $select->where('m.shipping_yes_time IS NULL AND m.pay_time IS NULL');
            }
            if($params['status'] == 2){//đã check
                $select->where('m.shipping_yes_time IS NOT NULL AND m.pay_time  IS NOT NULL');
            }
        }

        if( isset($params['note']) AND $params['note']){
            $select->where('note LIKE ?','%'.$params['note'].'%');
        }

        if( isset($params['from']) AND $params['from']){
            $select->where('created_at <= ?',$params['from']);
        }

        if( isset($params['to']) AND $params['to']){
            $select->where('created_at <= ?',$params['to']);
        }

        if(isset($params['sort']) AND $params['sort']){
            $collate = '';

            if ( in_array($params['sort'], array('po_name','created_by')) ){
                //$collate .= ' COLLATE utf8_unicode_ci ';
            }

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if($params['sort'] == 'created_by'){
                $order_str = 's.`'.$params['sort'].'`' . $collate . $desc;        
            }else{
                $order_str = 'p.`'.$params['sort'].'`' . $collate . $desc;    
            }
            
            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function fetchOrderByPo($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $arrCols = array(
            'po_id'     => 'p.id',
            'po_name'   => 'p.po_name',
            'title'     => 'd.title',
            'sn'        => 'm.sn',
            'sn_total'  => 'SUM(m.total)',
            'pay_user'  => 'm.pay_user',
            'shipping_yes_id' => 'm.shipping_yes_id'
        );

        $select = $db->select()
            ->from(array('p'=>'distributor_po'),$arrCols)
            ->join(array('m'=>'market'),'p.id = m.po_id',array())
            ->join(array('d'=>'distributor'),'p.distributor_id = d.id',array())
            ->group('m.sn')
        ;

        if(isset($params['id']) AND $params['id']){
            $select->where('p.id = ?',$params['id']);
        }

        if(isset($params['po_name']) AND $params['po_name']){
            $select->where('p.po_name LIKE ?','%'.$params['po_name'].'%');
        }

        if(isset($params['sort']) AND $params['sort']){
            $collate = '';

            if ( in_array($params['sort'], array()) ){
                $collate .= ' COLLATE utf8_unicode_ci ';
            }

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str = 'p.`'.$params['sort'].'`' . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit){
            $select->limitPage($page, $limit);
        }

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function get_cache()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $data = $db->fetchAll($select, 'po_name');

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['po_name'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }
}