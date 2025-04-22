<?php
class Application_Model_Distributor extends Zend_Db_Table_Abstract
{
	protected $_name = 'distributor';

    function getDistributors(){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);

        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('p.region in(SELECT rm.id AS region_id
                    FROM hr.`asm` asm
                    LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                    WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        $select_group = $db->select()
        ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
        ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
        }


        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
        //echo $select;die;
        return $db->fetchAll($select);
    }

    function get_all(){
        return $this->getDistributors();
    }

    function getTranactionDistributorRecord($distributor_id)
    {
        try{
            $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('m' => 'market'),array('m.*'))
            ->where('m.d_id= ?', $distributor_id)
            ->where('m.canceled !=1', null);

            //echo $select;die; 
            $result = $db->fetchAll($select);
            return $result;
        } catch (Exception $e){
            return null;
        }


    }

    function getDistributorRecord($key){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.id = ?',$key);
        return $db->fetchRow($select);
    }

    function getStoreRecord($id){
        $db = Zend_Registry::get('db');
        // var_dump($select);
        $select = $db->select()
        ->from(array('ts' => HR_DB.".timing_sale",), array('ts.timing_id','ts.imei','t.store','t.staff_id','t.created_at as sold_on'));
        $select->joinLeft(array('t' => HR_DB.'.timing'), 't.id = ts.timing_id', array('t.store','t.staff_id'));
        $select->joinLeft(array('s' => HR_DB.'.store'), 's.id = t.store',array('store_name'=>'s.name'));
        $select->joinLeft(array('stf' => HR_DB.'.staff'), 'stf.id = t.staff_id',array('sold_by'=>'concat(stf.firstname," ",stf.lastname)'));
        $select->where("ts.imei = '{$id}'");
        return $db->fetchRow($select);
    }
    function fetchPagination($page, $limit, &$total, $params){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        $array_area = [];

        if (isset($params['export']) && $params['export'] && isset($params['dis_active']) && $params['dis_active']){
            $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id,(SELECT oc.oc_grade FROM oppo_club_distributor AS oc WHERE oc.oc_distributor_id = p.id ORDER BY oc.oc_year DESC,oc.oc_q DESC LIMIT 1) as oc_grade,(SELECT add_time FROM market tm where tm.d_id = p.id AND tm.isbacks = 0 AND tm.outmysql_time IS NOT NULL AND tm.add_time > ( NOW()- INTERVAL 3 MONTH ) LIMIT 1) as salein_inactive'), 'p.*'));
        }else{
            $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id,(SELECT oc.oc_grade FROM oppo_club_distributor AS oc WHERE oc.oc_distributor_id = p.id ORDER BY oc.oc_year DESC,oc.oc_q DESC LIMIT 1) as oc_grade'), 'p.*'));
        }

        $select->joinLeft(array('w' => 'warehouse'), 'p.warehouse_id = w.id', array('warehouse_name' => 'w.name'));
        $select->joinLeft(array('r' => HR_DB.'.regional_market'), 'p.region = r.id', array('r.area_id'));
        $select->joinLeft(array('a' => HR_DB.'.area'), 'a.id = r.area_id', array('area_name' => 'a.name'));
        $select->joinLeft(array('s' => 'store_account'), 's.d_id = p.id', array('balance' => 'ifnull(s.balance,0)'));

        $select->joinLeft(array('cl' => 'client'),'cl.customer_code = p.client_code',array('cl.client_name','cl.short_name'));

        $select->joinLeft(array('o' => HR_DB.'.org'), 'o.org_id = p.ka_type', array('o.org_id','o.org_name'));
        $select->joinleft(array('sto' => HR_DB.'.store'),'sto.d_id = p.id AND sto.status = 1',array('total_store'=> 'COUNT(sto.id)'));

        $select->joinLeft(array('dg' => 'distributor_group'), 'dg.group_id = p.group_id', array('group_type_id','group_name'));

        $select->joinLeft(array('staff' => 'staff'), 'staff.id = p.create_by', array('created_name' => 'concat(staff.firstname," ",staff.lastname)'));

        $select->joinLeft(array('staff_update' => 'staff'), 'staff_update.id = p.update_by', array('update_name' => 'concat(staff_update.firstname," ",staff_update.lastname)'));

        $select->joinLeft(array('edc' => 'external_distributor_code'), 'edc.oppo_distributor_id = p.id', array('partner_name','partner_code'));


        if (isset($params['id']) and $params['id'])
            $select->where('p.id = ?', $params['id']);

        if (isset($params['code']) and $params['code'])
            $select->where('p.distributor_code =?', $params['code']);

        if (isset($params['dis_type']) and $params['dis_type'])
            $select->where('p.distributor_type =?', $params['dis_type']);

        if (isset($params['status']) and $params['status'])
            $select->where('p.status =?',$params['status']);

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if (isset($params['store_code']) and $params['store_code'])
            $select->where('p.store_code LIKE ?', '%'.$params['store_code'].'%');

        if (isset($params['title']) and $params['title'])
            $select->where('p.title LIKE ?', '%'.$params['title'].'%');

        if (isset($params['region']) and intval($params['region']) > 0)
            $select->where('p.region = ?', $params['region']);

        if (isset($params['district']) and intval($params['district']) > 0)
            $select->where('p.district = ?', $params['district']);

        if (isset($params['alone']) && $params['alone'])
            $select->where('p.parent = 0 OR p.parent IS NULL', 1);
        elseif (isset($params['parent']) and intval($params['parent']) >= 0)
            $select->where('p.parent = ?', $params['parent']);

        if (isset($params['area']) and intval($params['area']) > 0)
            // $select->where('r.area_id = ?', $params['area']);
            array_push($array_area, $params['area']);

        if (isset($params['area_multi']) and $params['area_multi'])
            // $select->where('r.area_id in (?)', $params['area_multi']);
            $array_area = array_merge($array_area,$params['area_multi']);

        if (isset($params['grand_area_multi']) and $params['grand_area_multi']){

            foreach ($params['grand_area_multi'] as $key => $value) {

                switch ($value) {
                    case '1':
                        // $select->where('r.area_id in (?)', [81,82,83,110,111,112]);
                    $array_area = array_merge($array_area, [81,82,83,110,111,112]);
                    break;
                    case '2':
                        // $select->where('r.area_id in (?)', [85,86,87,115,88,89,117]);
                    $array_area = array_merge($array_area, [85,86,87,115,88,89,117]);
                    break;
                    case '3':
                        // $select->where('r.area_id in (?)', [90,91,92,93,113]);
                    $array_area = array_merge($array_area, [90,91,92,93,113]);
                    break;
                    case '4':
                        // $select->where('r.area_id in (?)', [94,95,96]);
                    break;
                    $array_area = array_merge($array_area, [94,95,96]);
                    case '5':
                        // $select->where('r.area_id in (?)', [97,109]);
                    break;
                    $array_area = array_merge($array_area, [97,109]);
                    case '6':
                        // $select->where('r.area_id in (?)', [98,99,100,101,102,114]);
                    $array_area = array_merge($array_area, [98,99,100,101,102,114]);
                    break;
                    case '7':
                        // $select->where('r.area_id in (?)', [103,104,105,116]);
                    $array_area = array_merge($array_area, [103,104,105,116]);
                    break;
                    case '8':
                        // $select->where('r.area_id in (?)', [106,107,108]);
                    $array_area = array_merge($array_area, [106,107,108]);
                    break;
                }

            }
        }

        if (isset($params['from']) and $params['from']){
            list( $day, $month, $year ) = explode('/', $params['from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.add_time >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['to']) and $params['to']){
            list( $day, $month, $year ) = explode('/', $params['to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.add_time <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['ka_type']) and $params['ka_type'])
            $select->where('p.ka_type = ?', $params['ka_type']);

        if (isset($params['quota_channel']) and $params['quota_channel'])
            $select->where('p.quota_channel = ?', $params['quota_channel']);

        if(isset($params['warehouse_id']) and $params['warehouse_id'])
            $select->where('p.warehouse_id =?',$params['warehouse_id']);

        if (isset($params['group_id']) and $params['group_id'])
            $select->where('dg.group_type_id = ?', $params['group_id']);

        if (isset($params['activate']) and $params['activate']){

            if($params['activate'] == 1){
                $select->where('p.activate = ?', $params['activate']);
            }else{
                $select->where('p.activate is null',1);
            }
        }

        if (isset($params['has_exported']) and intval($params['has_exported']) > 0){

            $select->joinLeft(array('m' => 'market'),
                '   m.d_id = p.id
                AND m.isbacks = 0
                AND m.outmysql_time IS NOT NULL
                AND m.add_time > ( NOW()- INTERVAL 3 MONTH )
                ',
                array());

            $select->group('p.id');

            if ($params['has_exported'] == 1)
                $select->having('COUNT(m.id) > 0');
            else
                $select->having('COUNT(m.id) = 0');
        }

        if (isset($params['admin']) and intval($params['admin']) > 0) {
            $select->where('p.admin = ?', $params['admin']);
        }

        if (isset($params['del']) && $params['del']) {
            $select->where('p.del = ?', $params['del']);
        } else {
            $select->where('p.del = 2 OR p.del IS NULL', 1);
        }

        if (isset($params['ka']) && $params['ka'])
            $select->where('p.is_ka = ?', $params['ka']);

        if (isset($params['internal']) && $params['internal'])
            $select->where('p.is_internal = ?', $params['internal']);

        if (isset($params['inactive']) && $params['inactive']) {
            $sub_select = $db->select()
            ->distinct()
            ->from(array('m' => 'market'), array('id' => 'm.d_id'))
            ->group('m.d_id')
            ->having('MAX(IFNULL(m.invoice_time, 0)) < DATE_SUB(NOW(),INTERVAL 90 day)', 1);

            $select->join(array('md' => $sub_select), 'p.id=md.id', array());
            $select->where('p.add_time < DATE_SUB(NOW(),INTERVAL 30 day)');
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('p.region in(SELECT rm.id AS region_id
                    FROM hr.`asm` asm
                    LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                    WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        $select_group = $db->select()
        ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
        ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
        }

        //$select->group('s.d_id');
        $select->group('p.id');

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('unames', 'email', 'name', 'region', 'admin', 'add', 'title', 'branch_number')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'region') {
                $order_str = 'r.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'branch_number'){
                $order_str = 'COUNT(sto.id)'. $desc;
            } elseif ($params['sort'] == 'area') {
                $order_str = 'a.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'admin') {
                $select->joinLeft( array('s' => 'staff'), 'p.admin = s.id', array() );
                $order_str = 's.`username` ' . $collate . $desc;
            } else {
                $order_str = '`'.$params['sort'] . '` ' . $collate . $desc;
            }

            $select->order(new Zend_Db_Expr($order_str));
        }

        if($array_area){
            $select->where('r.area_id in (?)', $array_area);
        }

        if (isset($params['export']) && $params['export']){
            //echo $select->__toString();die;
            return $select->__toString();
        }

        if ($limit)
           $select->limitPage($page, $limit);

       // echo $select;
       // echo '<br><br>';
       // print_r($array_area);
       // die;
       $result = $db->fetchAll($select);

       if ($limit)
        $total = $db->fetchOne("select FOUND_ROWS()");

    return $result;
}

function get_all_full(){
    $db = Zend_Registry::get('db');

    $select = $db->select()
    ->from(array('p' => $this->_name),
        array('p.*'));

    $select->where('p.del IS NULL OR p.del = ?', 0);
    $select->where('p.activate = ?', 1);

    $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

    return $db->fetchAll($select);
}

function get_cache4(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));

            $select->where('p.status =?',1);
            $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['title'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }

function get_cache3(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array("p.*,CONCAT(p.id,' - ',p.title) AS retailer_name"));

            // $select->where('p.del IS NULL OR p.del = ?', 0);
            // $select->where('p.activate = ?', 1);
            // $select->where('p.activate = ?', 1);
        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = $item['retailer_name'];
            }
        }
        $cache->save($result, $this->_name.'_cache', array(), null);
    }
    return $result;
}

function get_cache(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array("p.*,CONCAT(p.distributor_code,' - ',p.title) AS retailer_name"));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);
        $select->where('p.activate = ?', 1);
        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = $item['retailer_name'];
            }
        }
        $cache->save($result, $this->_name.'_cache', array(), null);
    }
    return $result;
}


function get_cache5(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array("p.*,CONCAT(p.distributor_code,' - ',p.title) AS retailer_name"));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);
        $select->where('p.activate = ?', 1);
        $select->where('p.agent_status =?',0);
        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = $item['retailer_name'];
            }
        }
        $cache->save($result, $this->_name.'_cache5', array(), null);
    }
    return $result;
}

function storecode_get_cache(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_storecode_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);

        $select->order(new Zend_Db_Expr('p.`store_code`'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = $item['store_code'];
            }
        }
        $cache->save($result, $this->_name.'_storecode_cache', array(), null);
    }
    return $result;
}

function get_by_area_cache($area_id = null){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_by_area_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select_internal = $db->select()->from(array('p' => $this->_name), array('p.title', 'p.district', 'p.id'))
        ->where('p.is_internal = 1', 1);

        $select = $db->select()
        ->from(array('p' => $this->_name), array('p.title', 'p.district', 'p.id'))
        ->join(array('r' => HR_DB.'.regional_market'), 'r.id=p.district', array(''))
        ->join(array('a' => HR_DB.'.regional_market'), 'a.id=r.parent', array('a.area_id'))
        ->where('p.del IS NULL OR p.del = ?', 0)
        ->where('p.activate = ?', 1)
        ->where('p.is_internal = 0 OR p.is_internal IS NULL', 0)
        ->order(new Zend_Db_Expr('`title` COLLATE utf8_unicode_ci'));

        $data_internal = $db->fetchAll($select_internal);
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[ intval($item['area_id']) ][ intval($item['id']) ] = array('title' => $item['title'], 'id' => intval($item['id']));
            }

                // nối thêm các dealer nội bộ cho từng khu vực
            foreach ($result as $key => $value) {
                foreach ($data_internal as $item)
                    $result[ intval($key) ][ intval($item['id']) ] = array('title' => $item['title'], 'id' => intval($item['id']));
            }
        }
            $cache->save($result, $this->_name.'_by_area_cache', array(), 60*60*24*7); // cache 7 ngày
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

    function get_warehouse_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_warehouse_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.id', 'p.warehouse_id'));

            $select->where('p.del IS NULL OR p.del = ?', 0);
            $select->where('p.activate = ?', 1);

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[intval($item['id'])] = intval($item['warehouse_id']);
                }
            }
            $cache->save($result, $this->_name.'_warehouse_cache', array(), null);
        }
        return $result;
    }

    function get_with_store_code_cache(){ 
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_with_store_code_cache');
       // $result=false;
        if ($result == false) {
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));

            $select->where('p.del IS NULL OR p.del = ?', 0);
            $select->where('p.activate = ?', 1);
            if($userStorage->warehouse_type !=''){
                $warehouse_type = $userStorage->warehouse_type;
                $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
            }else{
                $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
            }
            
            if($userStorage->group_id !='7'){
                if($userStorage->catty_staff_id !=''){
                    $catty_staff_id = $userStorage->catty_staff_id;
                    $select->where('p.region in(SELECT rm.id AS region_id
                        FROM hr.`asm` asm
                        LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                        WHERE asm.`staff_id`=?)',$catty_staff_id);
                }
            }

            $select_group = $db->select()
            ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
            ->where('u.user_id=?',$userStorage->id);
            $result_group = $db->fetchAll($select_group);
            $group_id = "";
            if ($result_group){
                foreach ($result_group as $to) {
                    $group_id .= $to['group_id'].',';
                }

                $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
            }

            $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = array(
                        'title' => $item['title'],
                        'store_code' => $item['store_code'],
                    );
                }
            }
            $cache->save($result, $this->_name.'_with_store_code_cache', array(), null);
        }
        return $result;
    }
    // Pungpond 141159
    function get_with_retailer_code_cache(){ 

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);
        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('p.region in(SELECT rm.id AS region_id
                    FROM hr.`asm` asm
                    LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                    WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        $select_group = $db->select()
        ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
        ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
        }

        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = array(
                    'title' => $item['title'],
                    'store_code' => $item['store_code'],
                );
            }
        }

        return $result;
    }

    function get_with_retailer_for_search($rank_id){ 

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);
        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('p.region in(SELECT rm.id AS region_id
                    FROM hr.`asm` asm
                    LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                    WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }
        $select->where('p.rank = ?',$rank_id);
        $select_group = $db->select()
        ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
        ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
        }

        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
               $result[$item['id']] = $item['id'].' - '.$item['title'];                        

           }
       }

       return $result;
   }
   function get_cache2(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_2_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name), array('p.*'));

            $select->joinLeft(array('s' => HR_DB.'.store'), 's.d_id = p.id',array());
            $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = array(
                    'd_id'    => $item['id'],
                    'd_code' => $item['distributor_code'],
                    'title'    => $item['title'],
                    'branchoffnumber'   => $item['branch_number'],
                    'unames'   => $item['unames'],
                    'name'     => $item['name'],
                    'add'      => $item['add'],
                    'add_tax'  => $item['add_tax'],
                    'district' => $item['district'],
                    'parent'   => $item['parent'],
                    'code'     => $item['store_code'],
                    'mst_sn'   => $item['mst_sn'],
                    'branch_no'=> $item['branch_no'],
                    'finance_code'      => $item['finance_code'],
                    'tel'      => $item['tel'],
                    'discount_spc'      => $item['discount_spc'],
                    'finance_group' => $item['finance_group'],
                );
            }
        }
        $cache->save($result, $this->_name.'_2_cache', array(), null);
    }
    return $result;
}

function get_all_cache(){
    $cache      = Zend_Registry::get('cache');
    $result     = $cache->load($this->_name.'_all_cache');

    if ($result === false) {

        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);

        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

        $data = $db->fetchAll($select);

        $result = array();
        if ($data){
            foreach ($data as $item){
                $result[$item['id']] = array(
                    'title'    => $item['title'],
                    'region'   => $item['region'],
                    'district' => $item['district'],
                    'add_tax'  => $item['add_tax'],
                );
            }
        }
        $cache->save($result, $this->_name.'_all_cache', array(), null);
    }
    return $result;
}

    /**
     * Do NOT set $id when using Mass Upload
     * MUST USE try-catch
     * @param [type] $data [description]
     * @param [type] $id   [description]
     */
    function add_new($data, $id = null)
    {
        if (empty($data['title'])) {
            throw new Exception("Empty Retailer Name");
        }

        $parent = 0;
        if (isset($data['parent']))
            $parent = $data['parent'];

        $is_ka = 0;
        if (isset($data['is_ka']))
            $is_ka = $data['is_ka'];

        $is_internal = 0;
        if (isset($data['is_internal']))
            $is_internal = $data['is_internal'];

        $data = array(
            'title'         => My_String::trim($data['title']),
            'name'          => My_String::trim($data['name']),
            'tel'           => str_replace(' ', '', $data['tel']),
            'email'         => str_replace(' ', '', $data['email']),
            'warehouse_id'  => intval($data['warehouse_id']),
            'region'        => intval($data['region']),
            'add'           => My_String::trim($data['add']),
            'add_tax'       => My_String::trim($data['add_tax']),
            'admin'         => intval($data['admin']),
            'rank'          => intval($data['rank']),
            'unames'        => My_String::trim($data['unames']),
            'mst_sn'        => My_String::trim($data['mst_sn']),
            'nots'          => My_String::trim($data['nots']),
            'store_code'    => My_String::trim($data['store_code']),
            'partner_id'    => isset($data['partner_id']) ? intval(My_String::trim($data['partner_id'])) : 0,
            'retailer_type' => intval($data['retailer_type']),
        );

        if (isset($parent))
            $data['parent'] = intval($parent);

        if (isset($is_ka))
            $data['is_ka'] = intval($is_ka);

        if (isset($is_internal))
            $data['is_internal'] = intval($is_internal);

        // ---------------check exists title or store_code-----------------------
        // check exists title or store_code
        $where = array();
        if ($id)
            $where[] = $this->getAdapter()->quoteInto('id <> ?', $id);

        $where[] = $this->getAdapter()->quoteInto('title = ?', $data['title']);
        $checkedTitle = $this->fetchRow($where);

        if ($checkedTitle) throw new Exception("Retailer Name is existed");

        // check store_code
        $where = array();
        if ($id)
            $where[] = $this->getAdapter()->quoteInto('id <> ?', $id);

        $where[] = $this->getAdapter()->quoteInto('store_code LIKE ?', $data['store_code']);
        $where[] = $this->getAdapter()->quoteInto('del IS NULL OR del = ?', 0);
        $checkedStoreCode = $this->fetchRow($where);

        if ($checkedStoreCode) throw new Exception("Store Code is existed: " . $data['store_code']);

        // check partner id
        if (isset($data['partner_id']) && $data['partner_id']) {
            $where = array();
            if ($id)
                $where[] = $this->getAdapter()->quoteInto('id <> ?', $id);

            $where[] = $this->getAdapter()->quoteInto('partner_id = ?', $data['partner_id']);
            $where[] = $this->getAdapter()->quoteInto('del IS NULL OR del = ?', 0);

            if (isset($data['parent']))
                $where[] = $this->getAdapter()->quoteInto('parent = ?', $data['parent']);

            $checkedPartnerId = $this->fetchRow($where);

            if ($checkedPartnerId) throw new Exception("Dealer is existed, partner ID: ".$data['partner_id']);
        }

        // ---------------End of: check exists title or store_code-----------------------

        if ($id) { // save
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $this->update($data, $where);
        } else { // create new
            $add_time = date('Y-m-d H:i:s');
            $data['add_time'] = $add_time;

            $id = $this->insert($data);
        }

        return $id;
    }

    function compare($full_store_name)
    {
        $full_store_name = My_String::trim($full_store_name);
        $check_list = array();

        $db = Zend_Registry::get('db');

        $store_name_split = explode('-', $full_store_name);

        if (is_array($store_name_split) && !empty($store_name_split[0])) {
            $store_name = $store_name_split[0];
        } else {
            $store_name = $full_store_name;
        }

        $where = array();
        $where[] = $this->getAdapter()->quoteInto('del = 0 OR del IS NULL', 1);
        $where[] = $this->getAdapter()->quoteInto('activate = ?', 1);
        $where[] = $this->getAdapter()->quoteInto('title LIKE ?', '%'.$store_name.'%');
        $check_store_name = $this->fetchAll($where, 'title', 5);

        foreach ($check_store_name as $key => $store) {
            $check_list[ $store['id'] ] = $store['title'];
        }

        //

        $full_store_name_like = str_replace(array('đường', 'số', 'ngõ', 'hẻm', 'huyện', 'quận', 'xã', 'phường', 'thôn', 'ấp', 'xóm', 'thành phố', 'tp', 'tỉnh', ',', '-', '(', ')'),
            '', mb_strtolower($full_store_name, 'UTF-8'));

        $full_store_name_like = str_replace(' ', '%', $full_store_name_like);
        $full_store_name_like = str_replace('%%', '%', $full_store_name_like);

        $where = array();
        $where[] = $this->getAdapter()->quoteInto('del = 0 OR del IS NULL', 1);
        $where[] = $this->getAdapter()->quoteInto('activate = ?', 1);
        $where[] = $this->getAdapter()->quoteInto('title LIKE ?', '%'.$full_store_name_like.'%');

        $check_store_name = $this->fetchAll($where);

        foreach ($check_store_name as $key => $store) {
            if (!isset($check_list[ $store['id'] ]))
                $check_list[ $store['id'] ] = $store['title'];
        }

        //

        $store_list = $this->get_cache();

        foreach ($store_list as $_store_id => $_store_name) {
            if ( similar_text($_store_name, $full_store_name) > 55 && !isset($check_list[ $_store_id ]))
                $check_list[ $_store_id ] = $_store_name;
        }


        return $check_list;
    }

    function getRootDistributor($id){
        $current = $this->find($id)->current();

        if(!$current){
            return false;
        }

        if($current->parent == 0){
            $root = $current;
        }else{
            $root = $this->find($current->parent)->current();
        }
        return $root->toArray();
    }

    function  get_cacheKA()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_ka_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));

            $select->where('p.del IS NULL OR p.del = ?', 0);
            $select->where('p.activate = ?', 1);
            $select->where('p.is_ka = ?', 1);

            $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = array(
                        'title'    => $item['title'],
                        'region'   => $item['region'],
                        'district' => $item['district'],
                        'add_tax'  => $item['add_tax'],
                    );
                }
            }
            $cache->save($result, $this->_name.'_ka_cache', array(), null);
        }
        return $result;
    }

    public function getDistributorCode($db,$pefix)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $running_no="";

        try {
            if($return_type ==''){
                $return_type = 0;
            }
            $stmt = $db->query("CALL gen_running_distributor('".$pefix."')");
            $result = $stmt->fetch();
            $running_no = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Create Distributor Code, Please try again!');
        }
        return $running_no;
    }

    function getDistributorCode1($pefix)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $running_no="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_distributor('".$pefix."')");

            //$stmt = $db->prepare("CALL gen_running_distributor('OP')");
            
            $stmt->execute();
            $result = $stmt->fetch();
            $running_no = $result['running_no'];
            //print_r( $running_no);
            //die;

        }catch (exception $e) {
           // $flashMessenger->setNamespace('error')->addMessage('Cannot Get Distributor Code, please try again!');
        }
        return $running_no;
    }


    function blackListDistributor($rank = null, $text = null, $field = null){

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $db = Zend_Registry::get('db');

        if($field){
            $select = $db->select()
            ->from(array('p' => $this->_name),
                $field);
        }else{
            $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        }
        $select->where('p.black_list_distributor is null',1);  
        $select->where('p.del != ?',1);

        if($rank){
            $select->where('p.rank = ?',$rank); 
        }

        if($text){
            $select->where('p.title LIKE ?', '%'.$text.'%');
            $select->orWhere('p.store_code LIKE ?', '%'.$text.'%');
            $select->orWhere('p.id = ?', $text);
        }

        $data = $db->fetchAll($select);
        
        return $data;  
    }
// $userStorage = Zend_Auth::getInstance()->getStorage()->read();
//         $db = Zend_Registry::get('db');

//         $select = $db->select()
//             ->from(array('p' => $this->_name),
//                 array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
//         $select->where('p.del != ?',1); 
//         $select->where('p.rank = ?',$rank); 

//         $data = $db->fetchAll($select);

//         return $data;  


    function listStoreForCreate($distributor_id){
        // // $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        // $db = Zend_Registry::get('db');

        // $select = $db->select()
        // ->from(array('p' => HR_DB.'.store'),
        //     array('p.*'));

        // $select->where('p.status =?', 1);
        // $select->where('p.d_id =?',$distributor_id);

        // $result = $db->fetchAll($select);

        // // print_r($result);

        // return $result;

        // $db = Zend_Registry::get('db');

        // $distributor_id = $this->getRequest()->getParam('dis_id');

        //  $select = $db->select()
        // ->from(array('p' => HR_DB.'.store'),
        //     array('p.*'));

        // $select->where('p.status =?', 1);
        // $select->where('p.d_id =?',$distributor_id);

        // $result = $db->fetchAll($select);


        // echo json_encode($result->toArray());
        // exit;
    }


    function testDistributor($distributor_id){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => WAREHOUSE_DB.'.distributor'),array('p.*'));

        $select->where('p.id = ?',$distributor_id);

        $result = $db->fetchAll($select);

        return $result;
    }

    function listDistributorForCreate($rank){
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        if (My_Staff_Group::inGroup($userStorage->group_id, SALES_ADMIN)) {


            $QAsm = new Application_Model_Asm();
            $asm_cache = $QAsm->get_cache();

            if (isset($asm_cache[ $userStorage->hr_id ])
                && isset($asm_cache[ $userStorage->hr_id ]['area'])
                && count($asm_cache[ $userStorage->hr_id ]['area'])) {
             $area_id = $QDistributor->get_by_area_cache( $asm_cache[ $userStorage->hr_id ]['area'] );

         $db = Zend_Registry::get('db');

         $select_internal = $db->select()->from(array('p' => $this->_name), array('p.title', 'p.district', 'p.id'))
         ->where('p.is_internal = 1', 1);

         $select = $db->select()
         ->from(array('p' => $this->_name), array('p.title', 'p.district', 'p.id'))
         ->join(array('r' => HR_DB.'.regional_market'), 'r.id=p.district', array(''))
         ->join(array('a' => HR_DB.'.regional_market'), 'a.id=r.parent', array('a.area_id'))
         ->where('p.del IS NULL OR p.del = ?', 0)
         ->where('p.activate = ?', 1)
         ->where('p.is_internal = 0 OR p.is_internal IS NULL', 0)
         ->order(new Zend_Db_Expr('`title` COLLATE utf8_unicode_ci'));

         $data_internal = $db->fetchAll($select_internal);
         $data = $db->fetchAll($select);

         $result = array();
         if ($data){
            foreach ($data as $item){
                $result[ intval($item['area_id']) ][ intval($item['id']) ] = array('title' => $item['title'], 'id' => intval($item['id']));
            }

                // nối thêm các dealer nội bộ cho từng khu vực
            foreach ($result as $key => $value) {
                foreach ($data_internal as $item)
                    $result[ intval($key) ][ intval($item['id']) ] = array('title' => $item['title'], 'id' => intval($item['id']));
            }
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

    } else {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('p' => $this->_name),
            array('p.*'));

        $select->where('p.del IS NULL OR p.del = ?', 0);
        $select->where('p.activate = ?', 1);

        if($userStorage->warehouse_type !=''){
            $warehouse_type = $userStorage->warehouse_type;
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
        }else{
            $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
        }

        if($userStorage->group_id !='7'){
            if($userStorage->catty_staff_id !=''){
                $catty_staff_id = $userStorage->catty_staff_id;
                $select->where('p.region in(SELECT rm.id AS region_id
                    FROM hr.`asm` asm
                    LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                    WHERE asm.`staff_id`=?)',$catty_staff_id);
            }
        }

        $select->where('p.rank = ?',$rank);

        $select_group = $db->select()
        ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
        ->where('u.user_id=?',$userStorage->id);
        $result_group = $db->fetchAll($select_group);
        $group_id = "";
        if ($result_group){
            foreach ($result_group as $to) {
                $group_id .= $to['group_id'].',';
            }

            $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
        }

        $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
        // echo $select;die;
        return $db->fetchAll($select);
    }
} else {
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('p' => $this->_name),
        array('p.*'));

    $select->where('p.del IS NULL OR p.del = ?', 0);
    $select->where('p.activate = ?', 1);

    if(in_array($userStorage->group_id,array(106,107))){
        $select->joinLeft(array('wgs' => 'warehouse_group_user'),'wgs.warehouse_id = p.warehouse_id',array());
        $select->where('wgs.user_id =?',$userStorage->id);
    }

    if($userStorage->warehouse_type !=''){
        $warehouse_type = $userStorage->warehouse_type;
        $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type IN ('.$warehouse_type.'))',null);
    }else{
        $select->where('p.warehouse_id in(SELECT id FROM warehouse WHERE warehouse_type =1)',null);
    }

    if($userStorage->group_id !='7'){
        if($userStorage->catty_staff_id !=''){
            $catty_staff_id = $userStorage->catty_staff_id;
            $select->where('p.region in(SELECT rm.id AS region_id
                FROM hr.`asm` asm
                LEFT JOIN hr.`regional_market` rm ON asm.area_id=rm.area_id
                WHERE asm.`staff_id`=?)',$catty_staff_id);
        }
    }

    $select->where('p.rank = ?',$rank);
    $select->where('p.id <> ?','22819');
    $select->where('p.id <> ?','26327');

    $select_group = $db->select()
    ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
    ->where('u.user_id=?',$userStorage->id);
    $result_group = $db->fetchAll($select_group);
    $group_id = "";
    if ($result_group){
        foreach ($result_group as $to) {
            $group_id .= $to['group_id'].',';
        }

        $select->where('p.group_id in('.rtrim($group_id, ',').')',null);
    }

    $select->order(new Zend_Db_Expr('p.`title` COLLATE utf8_unicode_ci'));
        // echo $select;die;
    return $db->fetchAll($select);
}
}

function fetchBlackList($page, $limit, &$total, $params){
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $db = Zend_Registry::get('db');

    $select = $db->select()
    ->from(array('p' => $this->_name),
        array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'd_id'=>'p.id','p.title'));
    $select->joinLeft(array('bl'=>'warehouse.distributor_black_list'),'bl.d_id=p.id',array('bl.*'));

    $select->joinLeft(array('hrrm'=>'hr.regional_market'),'hrrm.id=p.region',array('hrrm_name' => 'hrrm.name','hrrm.area_id'));

    $select->joinLeft(array('hrarea'=>'hr.area'),'hrarea.id=hrrm.area_id',array('hrarea_name' => 'hrarea.name'));

    $select->where('p.black_list_distributor = ?',1);

    if (isset($params['reason_id']) and $params['reason_id'])
        $select->where('bl.remark = ?',$params['reason_id']);

    if (isset($params['distributor']) and $params['distributor'])
        $select->where('p.title like ?','%'.$params['distributor'].'%');

    if (isset($params['dis_id']) and $params['dis_id'])
        $select->where('p.id = ?',$params['dis_id']);

    if (isset($params['type']) and $params['type'])
        $select->where('bl.type = ?',$params['type']);

    if ($limit)
        $select->limitPage($page, $limit);


    $result = $db->fetchAll($select);

    if ($limit)
        $total = $db->fetchOne("select FOUND_ROWS()");

    return $result;
}

function getFinanceGroup()
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('p' => $this->_name),array('p.finance_group'))
    ->where('p.finance_group is not null',1)
    ->group('p.finance_group');

            //echo $select;die; 
    $result = $db->fetchAll($select);
    return $result;
}

function getDistributorGroup($d_id)
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('p' => $this->_name),array('p.warehouse_id'));

    $select->joinLeft(array('dg'=>'distributor_group'),'dg.group_id=p.group_id',array('dg.group_type_id','dg.group_name'));

    $select->where('p.id = ?', $d_id);

            //echo $select;die; 
    $result = $db->fetchRow($select);
    return $result;
}

function getDistributorMST($d_id,$mst_sn)
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('p' => $this->_name),array('p.id','p.title'))
    ->where('p.del = ?',0)
    ->where('p.id <> ?',$d_id)
    ->where('p.mst_sn = ?',$mst_sn);

            //echo $select;die; 
    $result = $db->fetchAll($select);
    return $result;
}

function getSuperiorDistributor($warehouse_id)
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
        ->from(array('p' => $this->_name),array('p.title'));
    $select->joinLeft(array('w' => 'warehouse'),'w.id = p.agent_warehouse_id',array('w.name'));

    $select->where('w.id =?',$warehouse_id);

    $result = $db->fetchAll($select);
    return $result;

}


function getDistributorByWarehouse($warehouse_id)
{
    $db = Zend_Registry::get('db');
    $select = $db->select()
        ->from(array('p' => $this->_name),array('id'));

    $select->where('p.status =?',1);
    $select->where('p.agent_warehouse_id IN (?)',$warehouse_id);

    $result = $db->fetchAll($select);
    return $result;
}

}
