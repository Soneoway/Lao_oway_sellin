<?php
class Application_Model_Staff extends Zend_Db_Table_Abstract
{
	protected $_name = 'staff';

    function getCattyStaff()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('s' => HR_DB.'.staff'), 
                array('s.id','s.code', "TRIM(CONCAT(s.firstname,' ',s.lastname, ' [',s.code,']'))AS fullname"));

        $select->where('s.off_date IS NULL',null);
        return $db->fetchAll($select);
    }

    function getCattyStaffByID($staff_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('s' => HR_DB.'.staff'), 
                array('s.id','s.code', "TRIM(CONCAT(s.firstname,' ',s.lastname, ' [',s.code,']'))AS fullname", 'firstname', 'lastname'));

        $select->where('s.status=1 AND s.off_date IS NULL',null);
        $select->where('s.id=?',$staff_id);
        //echo $select;die;
        return $db->fetchAll($select);
    }

    function getSalesCattyByStore($d_id,$staff_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('ss' => HR_DB.'.store_staff'), array('ss.store_id'))
            ->join(array('s' => HR_DB.'.staff'), 's.id=ss.staff_id AND ss.is_leader=1', array('s.id','s.code', "TRIM(CONCAT(s.firstname,' ',s.lastname, ' [',s.code,']'))AS fullname"),'s.regional_market','s.area_id')
            ->join(array('st' => HR_DB.'.store'), 'st.id=ss.store_id', array('st.d_id','s.phone_number'))
            ->where('st.d_id=?',$d_id)
            ->where('s.id IS NOT NULL')
            ->group('s.id');

            if($staff_id !=''){
                $select->where('ss.staff_id=?',$staff_id);
            }
        //echo $select;
        return $db->fetchAll($select);
    }



    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        //print_r($params);

        if ($limit) {
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        } else {
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));
        }

        if(isset($params['status']) and $params['status'] == 1){
            $select->where('p.status =?',1);
        }
        elseif(isset($params['status']) and $params['status'] == 2){
            $select->where('p.status =?',0);
        }
        else{

        }

        if(isset($params['company']) and $params['company'] == 1){
            $select->where('p.company =?',1);
        }

        if(isset($params['company']) and $params['company'] == 2){
            $select->where('p.company =?',2);
        }


        if (isset($params['username']) and $params['username'])
            $select->where('p.username LIKE ?', '%'.$params['username'].'%');

        if (isset($params['group_id']) and $params['group_id'])
            $select->where('p.group_id LIKE ?', '%'.$params['group_id'].'%');

        if (isset($params['staff_code']) and $params['staff_code'])
            $select->where('p.staff_code LIKE ?', '%'.$params['staff_code'].'%');
        
        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';

            if (in_array($params['sort'], array('username', 'email')))
                $collate = ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        //echo $select;die;
        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchDeliveryPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        if ($limit) {
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.username', 'p.email'));
        } else {
            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.id', 'p.username', 'p.email'));
        }

        $select
            ->joinLeft(array('sh' => 'staff_hub'), 'sh.staff_id=p.id', array('hub_list' => new Zend_Db_Expr('GROUP_CONCAT(sh.hub_id)')))
            ->joinLeft(array('sc' => 'staff_carrier'), 'sc.staff_id=p.id', array('sc.carrier_id'));

        if (isset($params['username']) and $params['username'])
            $select->where('p.username LIKE ?', '%'.$params['username'].'%');

        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';

            if (in_array($params['sort'], array('username', 'email')))
                $collate = ' COLLATE utf8_unicode_ci ';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        $select->group('p.id');

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }


    function get_cache() {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $data = $this->fetchAll('username is not null and username not like \'\'', 'username');

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['username'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), 60*29); // 29 phút
        }
        return $result;
    }

    function get_by_area_cache($area_id = null) {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_by_area_cache');

        if ($result === false) {
            $db = Zend_Registry::get('db');
            $select = $db->select()->from(array('s' => 'staff'), array('s.id', 's.username'))
                ->join(array('hrs' => HR_DB.'.staff'), 'hrs.id=s.hr_id', array())
                ->join(array('p' => HR_DB.'.regional_market'), 'p.id=hrs.regional_market', array('p.area_id'))
                ->where('s.hr_id IS NOT NULL')
                ->where('s.username IS NOT NULL and s.username not like \'\' ')
                ->where('hrs.id IS NOT NULL')
                ->order('s.username')
            ;

            $data = $db->fetchAll($select);

            $result = array();
            if ($data) {
                foreach ($data as $item) {
                    $result[ $item['area_id'] ][ $item['id'] ] = $item['username'];
                }
            }

            $cache->save($result, $this->_name.'_by_area_cache', array(), 60*29); // 29 phút
        }

        if (isset($area_id) && !is_null($area_id)) {
            if (is_numeric($area_id)) {
                if (isset($result[ $area_id ]) && $result[ $area_id ])
                    return $result[ $area_id ];

            } elseif (is_array($area_id) && count($area_id)) {
                $_result = array();

                foreach ($area_id as $_id) {
                    $_result += $result[ $_id ];
                }

                return $_result;
            } else {
                return false;
            }
        } else {
            return $result;
        }

    }

    function getSalesByRdArea($area_id){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('s' => 'staff'),array('s.id'));

        $select->where('s.area_id =?',$area_id);
        $select->where('s.position =?',1);

        $result = $db->fetchAll($select);
        return $result;
    }
}
