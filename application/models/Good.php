<?php
class Application_Model_Good extends Zend_Db_Table_Abstract
{
    protected $_name = 'good';
    
    function getGoodRecord($key){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.id = ?',$key);
        return $db->fetchRow($select);
    }

    function getGoodRecordByCategory($cat_id) {
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
        ->joinLeft(array('b' => 'brand'),'b.id = p.brand_id',array('brand_name' => 'b.name'))
        ->where('p.product_status IN (?)',array('1','2'))
        ->where('p.cat_id =?',$cat_id);

       return $db->fetchAll($select);
    }

    function getGoodNameSP($d_id,$good_id,$good_color_id,$good_type)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('i' => 'good_name_sp'), array('i.*'));

        $select->where('i.good_id = ?',$good_id);
        $select->where('i.good_color_id = ?',$good_color_id);
        $select->where('i.good_type = ?',$good_type);
        $select->where('i.d_id = ?',$d_id);
        
        return $db->fetchAllt($select);
    }

 	function get_load_cache(){
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_2_cache');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('p' => $this->_name), array('p.*'));

                $select->joinLeft(array('b' => 'brand'), 'b.id = p.brand_id',array('brand_name' => 'b.name'));
                $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']] = array(
                            'good_id'       => $item['id'],
                            'good_name'     => $item['name'],
                            'brand_name'    => $item['brand_name'],
                        );
                    }
                }
                $cache->save($result, $this->_name.'_load_cache', array(), null);
            }
            return $result;

        }


    function getGoodProtectionPrice($key){
        $db = Zend_Registry::get('db');
        $select = $db->select()
        ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.id = ?',$key);
        return $db->fetchRow($select);
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
        ->joinLeft(array('gd' => 'good_discount'),'gd.good_id = p.id and gd.name = "Staff"',array('discount'=>'gd.discount'))
        ->joinLeft(array('b' => 'brand'),'b.id = p.brand_id',array('brand_name' => 'b.name'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        if (isset($params['details']) and $params['details'])
            $select->where('p.desc LIKE ?', '%'.$params['details'].'%');

        if (isset($params['brand_id']) and $params['brand_id'])
            $select->where('p.brand_id = ?', $params['brand_id']);

        if (isset($params['cat_id']) and $params['cat_id'])
            $select->where('p.cat_id = ?', $params['cat_id']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('p.id = ?', $params['good_id']);

        if (isset($params['good_color']) and $params['good_color'])
            $select->where('p.color = ?', $params['good_color']);

        if (isset($params['product_status']) and $params['product_status'])
            $select->where('p.product_status = ?', $params['product_status']);
        
        if (isset($params['hold']) and $params['hold'])
            $select->where('p.id in (?)', $params['hold']);

        if (isset($export) && $export) {

        }

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            if (in_array($params['sort'], array('name', 'category', 'brand', 'details')))
                $collate = ' COLLATE utf8_unicode_ci ';

            if ($params['sort'] == 'category') {
                $select->joinLeft( array('c' => 'good_category'), 'p.cat_id = c.id', array() );
                $order_str = 'c.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'brand') {
                $select->joinLeft( array('b' => 'brand'), 'p.brand_id = b.id', array() );
                $order_str = 'b.`name` ' . $collate . $desc;
            } elseif ($params['sort'] == 'details') {
                $order_str = 'p.`desc` ' . $collate . $desc;
            } else {
                $order_str = 'p.`'.$params['sort'] . '` ' . $collate . $desc;
            }

            $select->order(new Zend_Db_Expr($order_str));
        } else {
            $select->order('p.cat_id ASC');
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    
    function fetchPaginationStorage($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $offset = 0;
        if ($limit){
            $offset = ($page-1)*$limit;
        }


        $arrParamsData = array();
        foreach ($params as $key=>$v){
            $arrParamsData[] = $key.'='.$v;
        }


        $strParamsData = implode('|', $arrParamsData);
       // print_r($strParamsData);die; 
        $stmt = $db->query("CALL proc_get_storage(?,?,?,@total)", array((string)$strParamsData, (int)$offset, (int)$limit));
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        if ( isset($params['not_get_total']) and $params['not_get_total'] )
            return $result;

        $stmt = $db->query("SELECT @total");

        $data = $stmt->fetchAll();

        if ($data)
            foreach ($data[0] as $item)
                $total = $item;

            return $result;
        }

    function getProduct2($params) {
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
                    ->joinLeft(array('b' =>'brand'),'b.id = p.brand_id',array('brand_name' => 'b.name'))
                    ->where('p.product_status IN (?)',array('1','2'));

        if (isset($params['brand_id']) && $params['brand_id'])
            $select->where('p.brand_id =?',$params['brand_id']);

        if (isset($params['cat_id']) && $params['cat_id'])
            $select->where('p.cat_id =?',$params['cat_id']);

        $result = $db->fetchAll($select);
        return $result;
    }

    function getProductBrand($brand_id,$cat_id) {
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'))
                ->joinLeft(array('b' =>'brand'),'b.id = p.brand_id',array('brand_name' => 'b.name'))
                ->where('p.product_status IN (?)',array('1','2'));

        if(isset($brand_id) && $brand_id) 
            $select->where('p.brand_id =?',$brand_id);


        if(isset($cat_id) && $cat_id)
            $select->where('p.cat_id =?',$cat_id);

        $result = $db->fetchAll($select);
        return $result;

    }

        function get_cache(){
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_cache');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

                $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']] = $item['name'];
                    }
                }
                $cache->save($result, $this->_name.'_cache', array(), null);
            }
            return $result;
        }
        
    //pungpond
        function get_desc_name_cache(){
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_cache_desc_name');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

                $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']] = $item['desc_name'];
                    }
                }
                $cache->save($result, $this->_name.'_cache_desc_name', array(), null);
            }
            return $result;
        }
        function get_phone_cache(){
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_phone_cache');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.id', 'p.name'));
                $select->where('p.cat_id = ?', PHONE_CAT_ID);
                $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

                $data = $db->query($select->__toString());

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']] = $item['name'];
                    }
                }
                $cache->save($result, $this->_name.'_phone_cache', array(), null);
            }
            return $result;
        }

        function get_name()
        {
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load('goodsdescription_cache');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

                $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']] = $item['desc'];
                    }
                }
                $cache->save($result, 'goodsdescription_cache', array(), null);
            }
            return $result;
        }

    //get color by good_id
        function get_cache2(){
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_2_cache');

            if ($result === false) {

                $db = Zend_Registry::get('db');

                $select = $db->select()
                ->from(array('g' => $this->_name),
                    array('g.*'));

                $select->join(array('gcc' => 'good_color_combined'),
                    'g.id = gcc.good_id',
                    array());

                $select->join(array('gc' => 'good_color'),
                    'gcc.good_color_id = gc.id',
                    array('good_color_name' => 'gc.name', 'good_color_id' => 'gc.id'));

                $select->order(new Zend_Db_Expr('g.`name` COLLATE utf8_unicode_ci'));

                $data = $db->fetchAll($select);

                $result = array();
                if ($data){
                    foreach ($data as $item){
                        $result[$item['id']][$item['good_color_id']] = $item['good_color_name'];
                    }
                }
                $cache->save($result, $this->_name.'_2_cache', array(), null);
            }
            return $result;
        }

        public function get_price($num, $good_id, $good_color, $cat_id, $distributor_id, $warehouse_id, $is_sales_price, $is_return, $type, $id =  null, $rank_type = 0 , $campaign_id)
        {


      //  echo $campaign_id;exit;
            if ($num and $good_id) {
                $where = $this->getAdapter()->quoteInto('id = ?', $good_id);
                $good = $this->fetchRow($where);

                try {

                    if ($is_sales_price) {
                        $QDistributor = new Application_Model_Distributor();
                        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                        $distributor = $QDistributor->fetchRow($where);

                    if ($rank_type && $rank_type == My_Finance_Type::CREDIT && $distributor['rank'] != 14) // trừ VTA
                        $rank = $distributor['rank'] * 100; // căn cứ theo ID rank thường tương ứng, nhân lên 100 ra ID rank công nợ
                        else
                            $rank = $distributor['rank'];

                    /*if (in_array($type, array(FOR_DEMO,FOR_APK, FOR_STAFFS))) //for demo; for staffs
                    $rank = 3; //retail price*/

                    if (in_array($type, array(FOR_STAFFS))){ //for staffs
                        $rank = 9; //retail price
                        $price_rank = 'price_'.$rank;
                    }else if (in_array($type, array(FOR_DEMO,FOR_APK))){ //for demo; for apk
                        $rank = 9; //retail price
                        $price_rank = 'price_'.$rank;
                    }else{
                        $price_rank = 'price_'.$rank;
                    }    

                    //quota_channel    
                    $quota_channel = $distributor['quota_channel'];    
                    /*if (in_array($type, array(2))) //for staffs
                    $rank = 17; //retail price*/

                    if(!$good->$price_rank)
                        return array(
                            'code'     => 0,
                            'quantity' => 0,
                            'message'  => 'Please input price for this rank',
                        );

                    //get sales price
                    $price = $good->$price_rank * ($num);

                    if (!$is_return){

                        //check quantity
                        $QGood = new Application_Model_Good();

                        $storageParams = array(
                            'warehouse_id'  => $warehouse_id,
                            'cat_id'        => $cat_id,
                            'good_id'       => $good_id,
                            'good_color_id' => $good_color,
                        );

                        // truong hop edit lai
                        if ($id)
                            $storageParams['current_order_id'] = $id;

                        $storageParams['not_get_ilike_bad_count']   = $storageParams['not_get_digital_bad_count'] =
                        $storageParams['not_get_imei_bad_count']    = $storageParams['not_get_damage_product_count'] =
                        $storageParams['not_get_total']             = $storageParams['not_order'] = true;

                        if ($cat_id == PHONE_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                            $storageParams['not_get_product_count'] = true;
                        } elseif ($cat_id == ACCESS_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_id == DIGITAL_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_product_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_id == ILIKE_CAT_ID) {
                            $storageParams['not_get_digital_count'] = $storageParams['not_get_product_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_id == IOT_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                            $storageParams['not_get_product_count'] = true;
                        }
                        $total2 = 0;
                        $storage = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                        $current_order = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                        // change order
                        $current_change_order = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;

                        if ($cat_id==PHONE_CAT_ID and $type==FOR_DEMO){
                            $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                            $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                        }

                        if ($cat_id==PHONE_CAT_ID and $type==FOR_APK){
                            $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                            $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                        }
                        if($cat_id == PHONE_CAT_ID and $type==FOR_STAFFS){
                            $current_order          = isset($storage[0]['current_order_staff']) ? $stores[0]['current_order_staff'] : 0;
                            $current_change_order   = isset($stores[0]['current_change_order_staff']) ? $stores[0]['current_change_order_staff'] : 0;
                        }

                        $current_storage = 0;

                        if (isset($storage[0]) and $storage[0]) {
                            switch ($cat_id){
                                case DIGITAL_CAT_ID:
                                $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                                break;
                                case PHONE_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                if ($type==FOR_DEMO){
                                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                }

                                if ($type==FOR_APK){
                                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                                }

                                if($type == FOR_STAFFS){
                                    $current_storage = (isset($storage[0]['imei_staff_count']) and $storage[0]['imei_staff_count']) ? $storage[0]['imei_staff_count'] : 0;
                                }
                                break;
                                case ILIKE_CAT_ID:
                                $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                                break;
                                case ACCESS_CAT_ID:
                                $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                                break;
                                case IOT_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                if($type == FOR_STAFFS){
                                    $current_storage = (isset($storage[0]['imei_staff_count']) and $storage[0]['imei_staff_count']) ? $storage[0]['imei_staff_count'] : 0;
                                }
                                if ($type==FOR_APK){
                                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                                }
				if ($type==FOR_DEMO){
                                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                }
                                break;

                            }
                        }

                        $count = $current_storage - $current_order - $current_change_order;

			 // Add New
                        $QLockStock = new Application_Model_LockStock();

                        $first_check = $QLockStock->getLockStock($good_id);


                        if($first_check[0]['lock_type'] == 1) {

                            return array(
                                'code' => -5,
                                'quantity' => 0,
                                'message' => 'The product is locked by the warehouse. Cannot order.',
                            );

                        }

                        $lock_stock = $QLockStock->getLockStockByProduct($storageParams);

                        // Loop For Lock Stock Table
                        foreach($lock_stock as $item) {
                            $lock_type = $item['lock_type'];
                            $lock_good_id = $item['good_id'];
                            $lock_good_color_id = $item['color_id'];
                            $lock_good_qulity   = $item['qulity'];
                        }

                        // Loop Fr Warehouse Storage
                        foreach($storage as $item) {
                            $storage_good_id = $item['id'];
                            $storage_color_id = $item['good_color_id'];
                            $storage_qulity  = $count;
                        }

                        $second_check = ($storage_qulity - $lock_good_qulity) - $num;


                        if($second_check < 0){

                           return array(
                            'code' => -6,
                            'quantity' => 0,
                            'message' => 'The product is locked by the warehouse. Cannot order.',
                        ); 

                       }

                        if ($count < $num){
                            return array(
                                'code' => -3,
                                'quantity' => $count>0 ? $count : 0,
                                'message' => 'Good not enough in this warehouse',
                            );
                        }

                    } else {

                        //check return: quantity of this product is available

                        $db = Zend_Registry::get('db');

                        $select = $db->select()
                        ->from(array('p' => 'market'),
                            array('total_qty' => 'SUM(num)'))
                        ->join(array('d' => 'distributor'), 'd.id=p.d_id', array());

                        $select->where('p.outmysql_time is not null',1);
                        $select->where('p.outmysql_time <> \'\'');
                        $select->where('p.outmysql_time <> 0');
                        $select->where('p.isbacks = 0');
                        $select->where('d.id = ? OR d.parent = ?', $distributor_id);
                        $select->where('p.good_color = ?', $good_color);
                        $select->where('p.good_id = ?', $good_id);
                        $total_qty = $db->fetchOne($select);

                        if (!$total_qty){
                            return array(
                                'code' => -1,
                                'quantity' => 0,
                                'message' => 'Good not existed in this retailer',
                            );

                        } elseif ($num>$total_qty){
                            return array(
                                'code' => -2,
                                'quantity' => $total_qty,
                                'message' => 'Good not enough in this retailer',
                            );
                        }
                    }

                } else {
                    //get import price
                    $price = $good->price_4 * ($num);


                }

                //05SB0005
                if($cat_id==ACCESS_CAT_ID && $good_id==154 && $quota_channel==10){
                    $price=0;
                }

                return array(
                    'code' => 1,
                    'price' => $price,
                    'message' => 'OK',
                );
            } catch (Exception $e){
                return array(
                    'code' => 0,
                    'message' => $e->getMessage(),
                );
            }
        }

        return array(
            'code' => 0,
            'message' => 'Invalid params',
        );
    }

//
// I think this is check stock
//
    public function get_stock($good_id, $good_color, $cat_id, $warehouse_id, $type){

        //check quantity
        $QGood = new Application_Model_Good();

        $storageParams = array(
            'warehouse_id'  => $warehouse_id,
            'cat_id'        => $cat_id,
            'good_id'       => $good_id,
            'good_color_id' => $good_color,
        );

        $storageParams['not_get_ilike_bad_count']   = $storageParams['not_get_digital_bad_count'] =
        $storageParams['not_get_imei_bad_count']    = $storageParams['not_get_damage_product_count'] =
        $storageParams['not_get_total']             = $storageParams['not_order'] = true;

        if ($cat_id == PHONE_CAT_ID) {
            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
            $storageParams['not_get_product_count'] = true;
        } elseif ($cat_id == ACCESS_CAT_ID) {
            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
            $storageParams['not_get_imei_count'] = true;
        } elseif ($cat_id == DIGITAL_CAT_ID) {
            $storageParams['not_get_ilike_count'] = $storageParams['not_get_product_count'] =
            $storageParams['not_get_imei_count'] = true;
        } elseif ($cat_id == ILIKE_CAT_ID) {
            $storageParams['not_get_digital_count'] = $storageParams['not_get_product_count'] =
            $storageParams['not_get_imei_count'] = true;
        }
        $total2 = 0;
        $storage = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

        $current_order = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
        // change order
        $current_change_order = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;

        if ($cat_id==PHONE_CAT_ID and $type==FOR_DEMO){
            $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
            $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
        }

        if ($cat_id==PHONE_CAT_ID and $type==FOR_APK){
            $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
            $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
        }

        $current_storage = 0;

        if (isset($storage[0]) and $storage[0]) {
            switch ($cat_id){
                case DIGITAL_CAT_ID:
                $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                break;
                case PHONE_CAT_ID:
                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                if ($type==FOR_DEMO){
                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                }

                if ($type==FOR_APK){
                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                }
                break;
                case ILIKE_CAT_ID:
                $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                break;
                case ACCESS_CAT_ID:
                $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                break;
                case IOT_CAT_ID:
                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                break;

            }
        }

        $count = $current_storage - $current_order - $current_change_order;

        return $count;

    }

    public function get_return_price($num, $good_id, $good_color, $cat_id, $distributor_id, $warehouse_id, $is_sales_price, $is_return, $type, $id =  null, $rank_type = 0 , $campaign_id)
    {

      //  echo $campaign_id;exit;
        if ($num and $good_id) {
            $where = $this->getAdapter()->quoteInto('id = ?', $good_id);
            $good = $this->fetchRow($where);

            try {

                if ($is_sales_price) {
                    $QDistributor = new Application_Model_Distributor();
                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                    $distributor = $QDistributor->fetchRow($where);

                    if ($rank_type && $rank_type == My_Finance_Type::CREDIT && $distributor['rank'] != 14) // trừ VTA
                        $rank = $distributor['rank'] * 100; // căn cứ theo ID rank thường tương ứng, nhân lên 100 ra ID rank công nợ
                        else
                            $rank = $distributor['rank'];

                    if (in_array($type, array(FOR_DEMO,FOR_APK, FOR_STAFFS))) //for demo; for staffs
                        $rank = 3; //retail price

                    /*if (in_array($type, array(2))) //for staffs
                    $rank = 17; //retail price*/

                    $price_rank = 'price_'.$rank;

                    if(!$good->$price_rank)
                        return array(
                            'code'     => 0,
                            'quantity' => 0,
                            'message'  => 'Please input price for this rank',
                        );

                    
                    $QMarket = new Application_Model_Market();
                    $where = array();
                    $where[] = $QMarket->getAdapter()->quoteInto('d_id = ?', $distributor_id);
                    $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $good_id);
                    $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $good_color);
                    $return_good  = $QMarket->fetchAll($where);

                    foreach ($return_good as $sale)
                    {
                        $price = $sale->price * ($num);
                    }

                    if(floatval($good->$price_rank) <= floatval($sale->price)){
                        $price = $good->$price_rank * ($num);
                    }else{
                        $price = $sale->price * ($num);
                    }
                    //$price = $good->$price_rank * ($num);
                    if (!$is_return){
                        
                        //check quantity
                        $QGood = new Application_Model_Good();

                        $storageParams = array(
                            'warehouse_id'  => $warehouse_id,
                            'cat_id'        => $cat_id,
                            'good_id'       => $good_id,
                            'good_color_id' => $good_color,
                        );

                        // truong hop edit lai
                        if ($id)
                            $storageParams['current_order_id'] = $id;

                        $storageParams['not_get_ilike_bad_count']   = $storageParams['not_get_digital_bad_count'] =
                        $storageParams['not_get_imei_bad_count']    = $storageParams['not_get_damage_product_count'] =
                        $storageParams['not_get_total']             = $storageParams['not_order'] = true;

                        if ($cat_id == PHONE_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                            $storageParams['not_get_product_count'] = true;
                        } elseif ($cat_id == ACCESS_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_digital_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_id == DIGITAL_CAT_ID) {
                            $storageParams['not_get_ilike_count'] = $storageParams['not_get_product_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        } elseif ($cat_id == ILIKE_CAT_ID) {
                            $storageParams['not_get_digital_count'] = $storageParams['not_get_product_count'] =
                            $storageParams['not_get_imei_count'] = true;
                        }
                        $total2 = 0;
                        $storage = $QGood->fetchPaginationStorage(1, null, $total2, $storageParams);

                        $current_order = isset($storage[0]['current_order']) ? $storage[0]['current_order'] : 0;
                        // change order
                        $current_change_order = isset($storage[0]['current_change_order']) ? $storage[0]['current_change_order'] : 0;

                        if ($cat_id==PHONE_CAT_ID and $type==FOR_DEMO){
                            $current_order          = isset($storage[0]['current_order_demo']) ? $storage[0]['current_order_demo'] : 0;
                            $current_change_order   = isset($storage[0]['current_change_order_demo']) ? $storage[0]['current_change_order_demo'] : 0;
                        }

                        if ($cat_id==PHONE_CAT_ID and $type==FOR_APK){
                            $current_order          = isset($storage[0]['current_order_apk']) ? $storage[0]['current_order_apk'] : 0;
                            $current_change_order   = isset($storage[0]['current_change_order_apk']) ? $storage[0]['current_change_order_apk'] : 0;
                        }

                        $current_storage = 0;

                        if (isset($storage[0]) and $storage[0]) {
                            switch ($cat_id){
                                case DIGITAL_CAT_ID:
                                $current_storage = (isset($storage[0]['digital_count']) and $storage[0]['digital_count']) ? $storage[0]['digital_count'] : 0;
                                break;
                                case PHONE_CAT_ID:
                                $current_storage = (isset($storage[0]['imei_count']) and $storage[0]['imei_count']) ? $storage[0]['imei_count'] : 0;
                                if ($type==FOR_DEMO){
                                    $current_storage = (isset($storage[0]['imei_demo_count']) and $storage[0]['imei_demo_count']) ? $storage[0]['imei_demo_count'] : 0;
                                }

                                if ($type==FOR_APK){
                                    $current_storage = (isset($storage[0]['imei_apk_count']) and $storage[0]['imei_apk_count']) ? $storage[0]['imei_apk_count'] : 0;
                                }

                                break;
                                case ILIKE_CAT_ID:
                                $current_storage = (isset($storage[0]['ilike_count']) and $storage[0]['ilike_count']) ? $storage[0]['ilike_count'] : 0;
                                break;
                                case ACCESS_CAT_ID:
                                $current_storage = (isset($storage[0]['product_count']) and $storage[0]['product_count']) ? $storage[0]['product_count'] : 0;
                                break;

                            }
                        }

                        $count = $current_storage - $current_order - $current_change_order;

                        if ($count < $num){
                            return array(
                                'code' => -3,
                                'quantity' => $count>0 ? $count : 0,
                                'message' => 'Good not enough in this warehouse',
                            );
                        }

                    } else {

                        //check return: quantity of this product is available

                        $db = Zend_Registry::get('db');

                        $select = $db->select()
                        ->from(array('p' => 'market'),
                            array('total_qty' => 'SUM(num)'))
                        ->join(array('d' => 'distributor'), 'd.id=p.d_id', array());

                        $select->where('p.outmysql_time is not null',1);
                        $select->where('p.outmysql_time <> \'\'');
                        $select->where('p.outmysql_time <> 0');
                        $select->where('p.isbacks = 0');
                        $select->where('d.id = ? OR d.parent = ?', $distributor_id);
                        $select->where('p.good_color = ?', $good_color);
                        $select->where('p.good_id = ?', $good_id);
                        $total_qty = $db->fetchOne($select);

                        if (!$total_qty){
                            return array(
                                'code' => -1,
                                'quantity' => 0,
                                'message' => 'Good not existed in this retailer',
                            );

                        } elseif ($num>$total_qty){
                            return array(
                                'code' => -2,
                                'quantity' => $total_qty,
                                'message' => 'Good not enough in this retailer',
                            );
                        }
                    }

                } else {
                    //get import price
                    $price = $good->price_4 * ($num);
                }


                return array(
                    'code' => 1,
                    'price' => $price,
                    'message' => 'OK',
                );
            } catch (Exception $e){
                return array(
                    'code' => 0,
                    'message' => $e->getMessage(),
                );
            }
        }

        return array(
            'code' => 0,
            'message' => 'Invalid params',
        );
    }

    public function get_price_stock($num, $good_id, $good_color, $cat_id, $distributor_id, $warehouse_id, $is_sales_price, $is_return, $type, $id =  null, $rank_type = 0 , $campaign_id)
    {
        if ($num and $good_id) {
            $where = $this->getAdapter()->quoteInto('id = ?', $good_id);
            $good = $this->fetchRow($where);

            try {

                if ($is_sales_price) {
                    $QDistributor = new Application_Model_Distributor();
                    $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
                    $distributor = $QDistributor->fetchRow($where);

                    if ($rank_type && $rank_type == My_Finance_Type::CREDIT && $distributor['rank'] != 14) // trừ VTA
                        $rank = $distributor['rank'] * 100; // căn cứ theo ID rank thường tương ứng, nhân lên 100 ra ID rank công nợ
                        else
                            $rank = $distributor['rank'];

                    if (in_array($type, array(FOR_DEMO,FOR_APK, FOR_STAFFS))) //for demo; for staffs
                        $rank = 3; //retail price

                        $price_rank = 'price_'.$rank;

                        if(!$good->$price_rank)
                            return array(
                                'code'     => 0,
                                'quantity' => 0,
                                'message'  => 'Please input price for this rank',
                            );

                    //get sales price
                        $price = $good->$price_rank * intval($num);
                    } else {
                    //get import price
                        $price = $good->price_4 * intval($num);
                    }

                    return array(
                        'code' => 1,
                        'price' => $price,
                        'message' => 'OK',
                    );
                } catch (Exception $e){
                    return array(
                        'code' => 0,
                        'message' => $e->getMessage(),
                    );
                }
            }

            return array(
                'code' => 0,
                'message' => 'Invalid params',
            );
        }

        public function get_price_shipment($num,$good_id, $shipment_id, $shipment_type, $cat_id)
        {
            if ($good_id and $shipment_id and $num) {
                if($cat_id == ACCESS_CAT_ID){
                    $shipment_type = 1;
                }

                $QGoodShipmentPhone = new Application_Model_GoodShipmentPhone();
                $where[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_id = ?', $good_id);
                $where[] = $QGoodShipmentPhone->getAdapter()->quoteInto('good_shipment_id = ?', $shipment_id);
                $where[] = $QGoodShipmentPhone->getAdapter()->quoteInto('type = ?', $shipment_type);
                $data = $QGoodShipmentPhone->fetchRow($where);
                if($data){
                    $price = ($data->price) * intval($num);
                    if($price){
                        return array(
                            'code' => 1,
                            'price' => $price,
                            'message' => 'OK',
                        );
                    }
                }
            }
            return array(
                'code' => 1,
                'price' => 0,
                'message' => 'OK',
            );
        }

        function get_model_color_cache()
        {
            $cache      = Zend_Registry::get('cache');
            $result     = $cache->load($this->_name.'_model_color_cache');

            if (!$result) {

                $db = Zend_Registry::get('db');
                $select = $db->select()
                ->distinct()
                ->from(array('p' => $this->_name), array('product_id' => 'p.id', 'product_name' => 'p.name', 'product_desc' => 'p.desc', 'price' => 'p.price_3', 'p.cat_id'))
                ->join(array('cc' => 'good_color_combined'), 'p.id=cc.good_id', array())
                ->join(array('gc' => 'good_color'), 'cc.good_color_id=gc.id', array('color_id' => 'gc.id','color_name' => 'gc.name'))
                ->order('p.name');

                $data = $db->fetchAll($select);

                $result = array();

                if ($data) {
                    foreach ($data as $key => $value) {

                        if ( ! isset( $result[ $value['product_id'] ] ) )
                            $result[ $value['product_id'] ] = array(
                                'id'        => $value['product_id'],
                                'cat_id'    => $value['cat_id'],
                                'name'      => $value['product_name'],
                                'desc'      => $value['product_desc'],
                                'price'     => $value['price'],
                                'children'  => array(),
                            );

                        if ( ! isset( $result[ $value['product_id'] ]
                            ['children']
                            [ $value['color_id'] ] ) )

                            $result[ $value['product_id'] ]
                        ['children']
                        [ $value['color_id'] ] = array(
                            'id'    => $value['color_id'],
                            'name'  => $value['color_name'],
                        );
                    }
                }

                $cache->save($result, $this->_name.'_model_color_cache', array(), null);
            }
            return $result;
        }

        function getColorByGood($key){
            $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('p' => $this->_name), array('p.*'));

            $select->joinLeft(array('dcc'=>'good_color_combined'),'dcc.good_id=p.id',array());
            $select->joinLeft(array('gc'=>'good_color'),'gc.id=dcc.good_color_id',array('color_id' => 'gc.id','color_name' => 'name'));

            $select->where('p.id = ?',$key);
            return $db->fetchAll($select);
        }

        function getProduct($cat_id,$warehouse_id){

            $db = Zend_Registry::get('db');
            $select = $db->select()->from(array('p' => $this->_name), array('p.*'))
                ->joinLeft(array('b' => 'brand'),'b.id = p.brand_id',array('brand_name' => 'b.name'));

            if($warehouse_id){

            //get hold product
                $select_sub = $db->select()->from(array('gha' => 'good_hold_all'), array('gha.good_id'));
                $select_sub->where('gha.type_all = ?', 1);
                $select_sub->where('gha.status is null', null);
                $select_sub->where('gha.warehouse_id = ?', $warehouse_id);
                $select_sub->group('gha.good_id');
                $not_product = $db->fetchAll($select_sub);

                if($not_product){
                    $select->where('p.id NOT IN (?)', $not_product);
                }
                
            }

            $select->where('p.product_status IN (?)',array('1','2'));
            $select->where('p.cat_id = ?', $cat_id);
            $select->where('p.del = ?', 0);

            switch ($cat_id) {
                case '11':
                $select->order('p.add_time DESC');
                break;
                case '12':
                $select->order('p.name');
                break;
            }

        // echo $select;die;
            return $db->fetchAll($select);

        }

        function getExportProductListBarCode(){
            $db = Zend_Registry::get('db');
            $select = $db->select()
            ->from(array('p' => $this->_name), array('category' => 'gct.name','product_name' => 'p.name','product_color' => 'gc.name','short_code' => 'CONCAT(p.name,"_",gc.name)','match_code' => 'CONCAT(mask(p.id,"########"),mask(gc.id,"#####"))'));

            $select->joinLeft(array('gct'=>'good_category'),'gct.id = p.cat_id',array());
            $select->joinLeft(array('dcc'=>'good_color_combined'),'dcc.good_id=p.id',array());
            $select->joinLeft(array('gc'=>'good_color'),'gc.id=dcc.good_color_id',array());

            $select->where('p.del = ?',0);
            $select->order('p.cat_id');
            $select->order('p.id');
            $select->order('gc.id');
        // echo $select;die;
            return $db->fetchAll($select);
        }

        function get_imei_good()
        {
            $db = Zend_Registry::get('db');
            $select = $db->select()->from(array('p' => $this->_name), array('good_id' => 'p.id', 'good_name' => 'p.name'))
            ->where('p.cat_id =?', 11);
            
            $result = $db->fetchAll($select);
            return $result;
        }

	function getColorWithProductSelected($good_id)
        {
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),array('gc.id','gc.name'))
                ->joinLeft(array('gcc' => 'good_color_combined'),'p.id = gcc.good_id',array())
                ->joinLeft(array('gc' => 'good_color'),'gcc.good_color_id = gc.id',array())
                ->where('p.id IN (?)',$good_id);

            $result = $db->fetchAll($select);
            return $result;
        }

    }                                                      
