<?php
class Application_Model_Imei extends Zend_Db_Table_Abstract
{
	protected $_name = 'imei';
    
    function getImeiBySalesOrder($sales_sn,$good_id,$good_color)
    {
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('im' => $this->_name), array('imei_sn' => 'im.imei_sn','im.sales_sn'));

        $select->where('im.sales_sn =?',$sales_sn);
        $select->where('im.good_id =?',$good_id);
        $select->where('im.good_color =?',$good_color);

        //echo $select;
        $result = $db->fetchAll($select);
        return $result;
    }

    function getImeiByProductWarranty($changed_sn,$warehouse_id,$type ,&$total)
    {
        $db = Zend_Registry::get('db');

          $select = $db->select()
            ->from(array('im' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS im.id'), 'im.id','im.imei_sn','im.good_id','im.good_color','im.po_sn','im.into_date','im.changed_sn','im.warehouse_id'));           

        $select->joinLeft(array('ir' => 'imei_renew'), 'im.imei_sn = ir.imei_new', array('ir.id','ir.imei_old', 'ir.co_sn'));
        $select->joinLeft(array('g' => 'good'), 'im.good_id = g.id', array('g.id','g.cat_id', 'good_name'=>'g.name','g.desc'));
        $select->joinLeft(array('gc' => 'good_color'), 'im.good_color = gc.id', array('gc.id', 'color_name'=>'gc.name'));
        if($type  == 'co'){
            if (isset($changed_sn) and $changed_sn)
                $select->where('im.changed_sn =?',$changed_sn);
        }else{
            if (isset($changed_sn) and $changed_sn)
                $select->where('im.po_sn =?',$changed_sn);
        }
        
        
        if (isset($warehouse_id) and $warehouse_id)
            $select->where('im.warehouse_id =?',$warehouse_id);
       
       
        $result = $db->fetchAll($select);
         $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getImeiWMPDForStamp($warehouse_id,$good_id,$good_color,&$totalImei)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('im' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS im.id'), 'im.*'));                           
        
        $select->joinLeft(array('cso' => 'change_sales_order'), 'cso.changed_sn = im.changed_sn', array('cso.id','cso.sn_ref'));

        if (isset($warehouse_id) and $warehouse_id)
            $select->where('im.warehouse_id =?',$warehouse_id);

        if (isset($good_id) and $good_id)
            $select->where('im.good_id =?',$good_id);

        if (isset($good_color) and $good_color)
            $select->where('im.good_color =?',$good_color);

        $select->where('im.good_id_old is null');
        // echo $select;exit();
        $result = $db->fetchRow($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getImeiRecord($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.imei_sn = ?',$imei);
        return $db->fetchRow($select);
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));


        if ($limit)
        	$select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
     //update by Pungpond export stock IMEI
    function fetchStorageImei($page, $limit, &$total, $params){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(~E_ALL);
        ini_set("display_error", '0');
        
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('i' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS i.id'), 'i.*'))
      	   ->joinLeft(array('w' => 'warehouse'),'w.id = i.warehouse_id',array())
            ->joinLeft(array('a' => hr.'.area'),'a.id = w.area_id',array('area_name' => 'a.name'))
            ->joinLeft(array('rm' => hr.'.regional_market'),'rm.id = w.province_id',array('provice_name' => 'rm.name'))
            ->joinLeft(array('il' => 'imei_lock'),'il.imei_log = i.imei_sn',array('lock_status' => 'il.imei_log'))
            ->joinLeft(array('ts' => hr.'.timing_sale'),'ts.imei = i.imei_sn',array('timing_status' => 'ts.imei'))
            ->joinLeft(array('g' => 'good'),'g.id = i.good_id',array())
            ->joinLeft(array('b' => 'brand'),'b.id = g.brand_id',array());
        
            $select->where('i.sales_sn is null'); 
            $select->where('i.old_data is null');

        if (isset($params['brand_id']) and $params['brand_id'])
            $select->where('g.brand_id =?',$params['brand_id']);

        if (isset($params['warehouses']) and $params['warehouses'])
            $select->where('i.warehouse_id IN (?)',$params['warehouses']);

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('i.good_id IN (?)', $params['good_id']);

        if (isset($params['good_color_id']) and $params['good_color_id'])
            $select->where('i.good_color IN (?)', $params['good_color_id']);

        $select->where('i.out_date is null', null);
        // $select->where('i.status <> ?', '4');

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select; die;
       
        $result = $db->fetchAll($select);
        
        
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }//end 
    function fetchBadPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['sn']) && $params['sn'])
            $select->where('p.sales_sn = ?', $params['sn']);

        if (isset($params['imei_sn']) && $params['imei_sn'])
            $select->where('p.imei_sn = ?', $params['imei_sn']);

        if (isset($params['good_id']) && $params['good_id'])
            $select->where('p.good_id = ?', $params['good_id']);

        if (isset($params['good_color']) && $params['good_color'])
            $select->where('p.good_color = ?', $params['good_color']);


        $select->where('p.out_date IS NULL', null);

        $select->where('p.shape <> ?', 1);

        $select->where('p.status = ?', 2); //proccessing

        if (isset($params['export']) and $params['export'])
            return $select->__toString();


        if ($limit)
            $select->limitPage($page, $limit);


        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchErrorPagination($page, $limit, &$total, $params){
  		// SELECT a.imei_sn, i.good_id, i.good_color, i.po_sn, a.activated_at  FROM imei i
		// INNER JOIN imei_activation a
		// ON i.imei_sn=a.imei_sn
		// WHERE i.sales_sn IS NULL
		// OR i.sales_sn = 0
		// OR i.sales_sn = ''
		// OR i.out_date IS NULL
		// OR i.out_date = 0
		// OR i.out_date = ''
		// ORDER BY i.po_sn, i.good_id, i.good_color, a.activated_at

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('i' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS i.id'), 'i.good_id', 'i.good_color', 'i.po_sn', 'i.into_date', 'i.out_date', 'i.warehouse_id'))
            ->joinRight(array('a'=>'imei_activation'), 'i.imei_sn=a.imei_sn', array('a.imei_sn','a.activated_at'));

        if (isset($params['po_sn']) && $params['po_sn'] == 1) {
        	$select->where('i.po_sn IS NULL');
        }

		if (isset($params['imei_sn']) && $params['imei_sn'] == 1) {
        	$select->where('i.imei_sn IS NULL');
        }

		if (isset($params['sales_sn']) && $params['sales_sn'] == 1) {
        	$select->where('i.sales_sn IS NULL OR i.sales_sn = 0 OR i.sales_sn = \'\' ');
        }

		if (isset($params['out_date']) && $params['out_date'] == 1) {
        	$select->where('i.out_date IS NULL OR i.out_date = 0 OR i.out_date = \'\'');
        }

        if ($limit)
        	$select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchNotExistsPagination($page, $limit, &$total, $params){
		// SELECT a.imei_sn, a.activated_at  FROM imei i
		// RIGHT JOIN imei_activation a
		// ON i.imei_sn=a.imei_sn
		// WHERE i.imei_sn IS NULL
		// ORDER BY a.activated_at

        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('a'=>'imei_activation'),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS a.imei_sn'), 'a.imei_sn', 'a.activated_at'))
            ->joinLeft(array('i' => $this->_name), 'i.imei_sn=a.imei_sn', array())
            ->where("i.imei_sn IS NULL")
            ->order('a.activated_at');

		if (isset($params['imei_sn']) && $params['imei_sn']) {
        	$select->where('a.imei_sn LIKE ?', $params['imei_sn']);
        }

        if ($limit)
        	$select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchPaginationSalesOut($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $where = ' WHERE
                        i.activated_date IS NOT NULL
                     AND i.activated_date <> \'\'
                     AND i.activated_date <> 0
                    ';

        if (isset($params['cat_id']) and $params['cat_id'])
            $where .= $db->quoteInto(' and g.cat_id = ?', $params['cat_id']);


        if (isset($params['good_id']) and $params['good_id'])
            $where .= $db->quoteInto(' and g.id = ?', $params['good_id']);


        if (isset($params['good_color_id']) and $params['good_color_id']){
            $where .= $db->quoteInto(' and gcc.good_color_id = ?', $params['good_color_id']);
        }

        if (isset($params['activated_date_from']) and $params['activated_date_from']){
            list( $day, $month, $year ) = explode('/', $params['activated_date_from']);

            if (isset($day) and isset($month) and isset($year) )
                $where .= $db->quoteInto(' and i.activated_date >= ?', $year.'-'.$month.'-'.$day . ' 00:00:00');


        }

        if (isset($params['activated_date_to']) and $params['activated_date_to']){
            list( $day, $month, $year ) = explode('/', $params['activated_date_to']);

            if (isset($day) and isset($month) and isset($year) )
                $where .= $db->quoteInto(' and i.activated_date <= ?', $year.'-'.$month.'-'.$day . ' 23:59:59');

        }

        $sLimit = '';

        if ($limit){
            $offset = ($page-1)*$limit;
            $sLimit = 'limit '.$offset.','.$limit;
        }

        $sql =
            '
                SELECT SQL_CALC_FOUND_ROWS
                    i.good_id,
                    i.good_color,
                    i.distributor_id,
                    count(1) AS goods_num,
                    r.`name`,
                    r.area_id,
                    (
                        SELECT
                            m2.add_time
                        FROM
                            `market` AS m2
                        WHERE
                            m2.sn = i.sales_sn
                        LIMIT 1
                    )AS add_time
                FROM
                    `imei` AS i
                LEFT JOIN `good` AS g ON g.id = i.good_id
                LEFT JOIN `good_color_combined` AS gcc ON g.id = gcc.good_id
                LEFT JOIN `distributor` AS d ON d.id = i.distributor_id
                LEFT JOIN `region` AS r ON r.id = d.region
                ' . $where . '
                GROUP BY
                    i.good_id,
                    i.good_color,
                    i.distributor_id,
                    i.out_user
                ORDER BY
                    i.id DESC
                ' . $sLimit . '
            '
        ;

        if (isset($params['export']) and $params['export'])
            return $sql;

        $result = $db->fetchAll($sql);


        $sql_count = 'select FOUND_ROWS()';

        $data = $db->fetchAll($sql_count);

        if ($data)
            foreach ($data[0] as $item)
                $total = $item;


        return $result;
    }

    function get_damage_return($warehouse_id=null, $good_id=null, $good_color=null){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('i' => $this->_name),
                array(new Zend_Db_Expr('COUNT( i.id) ')));

        $select->join(array('ir' => 'imei_return'), 'i.imei_sn = ir.imei_sn AND i.return_sn = ir.return_sn', array());

        if ($warehouse_id)
            $select->where('i.warehouse_id = ?', $warehouse_id);

        if ($good_id)
            $select->where('i.good_id = ?', $good_id);

        if ($good_color)
            $select->where('i.good_color = ?', $good_color);


        $total = $db->fetchOne($select);

        return $total;
    }

    function get_imei_storage($warehouse_id, $good_id, $good_color){

        $db = Zend_Registry::get('db');

        $params= array(
            'warehouse_id' => $warehouse_id,
            'good_id' => $good_id,
            'good_color_id' => $good_color
            );

        $arrParamsData = array();
        foreach ($params as $key=>$v){
            $arrParamsData[] = $key.'='.$v;
        }

        $strParamsData = implode('|', $arrParamsData);

        $stmt = $db->query("CALL proc_get_storage(?,?,?,@total)", array((string)$strParamsData, null, null));
        $result = $stmt->fetchAll();
        $stmt->closeCursor();

        return $result;
    }

    function getCoByImei($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('im' => $this->_name), array());

        $select->joinLeft(array('cso' => 'change_sales_order'), 'cso.changed_sn = im.changed_sn', array('cso.id','cso.sn_ref'));

        $select->where('im.imei_sn = ?',$imei);
        // echo $select;die;
        return $db->fetchRow($select);
    }

    function checkImeiForReturnBorrowing($arr_imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('im' => $this->_name), array('imei_sn'));

        $select->where('im.imei_sn in (?)', $arr_imei);
        $select->where('im.type = ?', 1);
        $select->where('im.sales_sn is not null');
        // echo $select;die;
        $featchAll = $db->fetchAll($select);

        $arrData = [];

        foreach ($featchAll as $key) {
            array_push($arrData, $key['imei_sn']);
        }

        return $arrData;
    }

    function checkImeiSold($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->joinLeft(array('hr_ts' => 'hr.timing_sale'), 'hr_ts.imei = p.imei_sn', array());

        $select->where('p.imei_sn = ?',$imei);
        $select->where('p.sales_sn is not null');
        $select->where('hr_ts.id is not null');

        return $db->fetchRow($select);
    }

    function checkImeiReady($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.imei_sn = ?',$imei);
        $select->where('p.sales_sn is null');

        return $db->fetchRow($select);
    }

    function checkImeiSoldAndNotiming($array_imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->joinLeft(array('d' => 'distributor'), 'd.id = p.distributor_id', array('d.title','d.ka_type'));

        $select->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_model' => 'g.name', 'good_name' => 'g.desc'));
        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('good_color' => 'gc.name'));

        $select->joinLeft(array('hr_ts' => 'hr.timing_sale'), 'hr_ts.imei = p.imei_sn', array());

        $select->where('p.imei_sn in (?)',$array_imei);
        // ka_type : 10 = Lotus
        // $select->where('d.ka_type = ?',10);
        $select->where('p.sales_sn is not null');
        $select->where('hr_ts.id is null');

        return $db->fetchAll($select);
    }

    function checkImeiReadyService($imei,$warehouse_id,$good_id,$good_color){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));
        // $select->joinLeft(array('hr_ts' => 'hr.timing_sale'), 'hr_ts.imei = p.imei_sn', array());

        $select->where('p.imei_sn = ?',$imei);
        $select->where('p.warehouse_id = ?',$warehouse_id);
        $select->where('p.good_id = ?',$good_id);
        $select->where('p.good_color = ?',$good_color);
        $select->where('p.sales_sn is null');
        // $select->where('hr_ts.id is null');

        // echo $select;die;

        return $db->fetchRow($select);
    }

    function checkImeiInWarehouse($array_imei,$warehouse_id,$flag=null){

        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->joinLeft(array('po' => 'purchase_order'), 'po.sn = p.po_sn', array('po_sn_ref' => 'po.sn_ref'));

        $select->joinLeft(array('cso' => 'change_sales_order'), 'cso.changed_sn = p.changed_sn', array('co_sn_ref' => 'cso.sn_ref'));

        $select->where('p.imei_sn in (?)',$array_imei);
        $select->where('p.warehouse_id = ?',$warehouse_id);
        $select->where('p.sales_sn is null');
        $select->where('p.good_id_old is null');

        if($flag == 'imei-factory-rework'){
            $select->where('p.flag_rework_status is null');
        }

        return $db->fetchAll($select);
    }

    function checkImeiInSystem($array_imei,$group_model = null){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));
        $select->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_model'=>'g.name','good_name' => 'g.desc'));
        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('color_name'=>'gc.name'));

        $select->where('p.imei_sn in (?)',$array_imei);

        if($group_model){
            // $select->group(['good_id','good_color','type']);
            $select->group(['good_id','type']);
        }

        return $db->fetchAll($select);
    }

    function fetchPaginationFactoryRework($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select->joinLeft(array('s' => 'staff'), 's.id = p.flag_rework_by', array("TRIM(CONCAT(s.firstname,' ',s.lastname))AS fullname"));

        $select->joinLeft(array('g' => 'good'), 'g.id = p.good_id', array('good_model'=>'g.name','good_name' => 'g.desc'));
        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = p.good_color', array('color_name'=>'gc.name'));

        if (isset($params['imei']) and $params['imei'])
            $select->where('p.imei_sn LIKE ?', '%'.$params['imei'].'%');

        if (isset($params['imei_munti']) and $params['imei_munti'])
            $select->where('p.imei_sn IN (?)', $params['imei_munti']);

        if (isset($params['fullname']) and $params['fullname'])
            $select->where('s.firstname LIKE ? or s.lastname LIKE ?', '%'.$params['fullname'].'%');

        if (isset($params['flag_rework_date_from']) and $params['flag_rework_date_from']){
                list( $day, $month, $year ) = explode('/', $params['flag_rework_date_from']);

        if (isset($day) and isset($month) and isset($year) )
                $select->where('p.flag_rework_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['flag_rework_date_to']) and $params['flag_rework_date_to']){
            list( $day, $month, $year ) = explode('/', $params['flag_rework_date_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.flag_rework_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            $order_str .= $params['sort'] . $collate . $desc;
            $select->order(new Zend_Db_Expr($order_str));
        }

        $select->where('p.flag_rework_status is not null');

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
    
    
     // //edit set olddata
    function getImeiOlddata($page, $limit, &$total, $params){
       $db = Zend_Registry::get('db');

    if($limit){

        $select = $db->select()
        ->from(array('p' => $this->_name),
         array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
    }else {

        $select = $db->select()
        ->from(array('p' => $this->_name), array('p.*'));
    }

    $select->joinLeft(array('d' => 'distributor'),'d.id = p.distributor_id',array('distributor_id' => 'd.id', 'distributor_name' => 'd.title'));
    $select->joinLeft(array('w' => 'warehouse'),'w.id = p.warehouse_id',array('warehouse_name' => 'w.name'));
    $select->joinLeft(array('s' => 'staff'),'s.id = p.set_olddata_by',array('staff_name' => 's.firstname'));
        
    if(isset($params['imei']) && $params['imei']){
        $imei = explode("\r\n", $params['imei']);
        $select->where('p.imei_sn IN (?)',$imei);
    }   
        $select->where('p.old_data =?',1);

        if($limit)
           $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if($limit)
           $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getImeiEdit($page, $limit, &$total, $params){
       $db = Zend_Registry::get('db');

    if($limit){

        $select = $db->select()
        ->from(array('p' => $this->_name),
         array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
    }else {

        $select = $db->select()
        ->from(array('p' => $this->_name), array('p.*'));
    }
        // $select->joinLeft(array('pp'=>'staff'), 'pp.id = p.user',
        //                   array('pp.username'));
               
        
    if(isset($params['imei']) && $params['imei']){
        $imei = explode("\r\n", $params['imei']);
        $select->where('p.imei_sn IN (?)',$imei);
    }   
    // $select->where('p.old_data IS NULL');
    $select->where('p.distributor_id IS NOT NULL');
    $select->order('p.good_id DESC');

        if($limit)
           $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);

        if($limit)
           $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getImeiUpdate($imei){
       $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));
        $select->where('p.imei_sn = ?',$imei);
        return $db->fetchRow($select);
    }

    function checkTiminginHr($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => hr.'.timing_sale'),array('p.timing_id'));
        $select->where('p.imei =? ',$imei);
        return $db->fetchRow($select);
    }

    function checkimeilock($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => 'imei_lock'),array('p.id'));
        $select->where('p.imei_log =? ',$imei);
        return $db->fetchRow($select);
    }

    function checkimei($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => 'imei'),array('p.imei_sn','p.good_id','p.good_color','count' => new Zend_Db_Expr('COUNT(imei_sn)')));
        $select->group('p.good_id');
        $select->group('p.good_color');
        $select->where('p.imei_sn IN (?)',$imei);

        // echo $select;die;
        return $db->fetchAll($select);
    }

   function checkimeiOne($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => 'imei'),array('p.imei_sn','p.good_id','p.good_color','count' => new Zend_Db_Expr('COUNT(imei_sn)')));
        $select->group('p.good_id');
        $select->group('p.good_color');
        $select->where('p.imei_sn =?',$imei);

        // echo $select;die;
        return $db->fetchRow($select);
    }


    function getImeiOrder($sn){
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('m' => 'market'),array('i.imei_sn','m.sn','i.good_color','i.good_id','g.brand_id'));
        $select->joinLeft(array('g' => 'good'),'g.id = m.good_id',array());
        $select->joinLeft(array('i' => 'imei'),'m.sn = i.sales_sn and m.good_id = i.good_id and m.good_color = i.good_color',array());
        $select->where('m.sn =?',$sn);


        // echo $select; die;

        $result = $db->fetchAll($select);
        return $result;
    }

}
