<?php
class Application_Model_BorrowingList extends Zend_Db_Table_Abstract{
	protected $_name = 'borrowing_list';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select->joinLeft(array('hrs' => 'oppohr.users'), 'hrs.staff_code = p.code', array('hrs_id' => 'hrs.id', "TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->joinLeft(array('hrsdepartment' => 'oppohr.department'), 'hrsdepartment.id = hrs.department', array('hrs_department_id' => 'hrs.department', 'hrs_department_name' => 'hrsdepartment.name'));

        $select->joinLeft(array('hrsalary' => 'oppohr.salary'), 'hrsalary.staff_id=hrs.staff_id', array('hrsalary.position','hrsalary.department_position_code'));

        $select->joinLeft(array('hrdp' => 'oppohr.department_position'), 'hrdp.code=hrsalary.department_position_code', array('hrdp.position_name'));

        $select->joinLeft(array('bi'=>'borrowing_item'), 'bi.sn = p.sn', array('bi.product_grade'));

        $select->joinLeft(array('buf'=>'borrowing_upload_file'), 'buf.sn = p.sn and buf.enabled = 1', array('buf.image_name'));

        if (isset($params['rq']) and $params['rq'])
            $select->where('p.rq LIKE ?', '%'.$params['rq'].'%');

        if (isset($params['code']) and $params['code'])
            $select->where('p.code LIKE ?', '%'.$params['code'].'%');

        if (isset($params['fullname']) and $params['fullname'])
            $select->where('hrs.firstname LIKE ? or hrs.lastname LIKE ?', '%'.$params['fullname'].'%');

        if (isset($params['position']) and $params['position'])
            $select->where('hrdp.position_name LIKE ?', '%'.$params['position'].'%');

        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

        if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['return_at_from']) and $params['return_at_from']){
            list( $day, $month, $year ) = explode('/', $params['return_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.return_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['return_at_to']) and $params['return_at_to']){
            list( $day, $month, $year ) = explode('/', $params['return_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.return_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['grade']) and $params['grade'])
            $select->where('bi.product_grade = ?', $params['grade']);

        // if (isset($params['type']) and $params['type']){
        //     $select->where('p.borrowing_type = ?', $params['type']);
        // }

        if (isset($params['hrs_department_id']) and $params['hrs_department_id']){
            $select->where('hrs.department = ?', $params['hrs_department_id']);
        }

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('bi.good_id = ?', $params['good_id']);

        if (isset($params['good_color_id']) and $params['good_color_id'])
            $select->where('bi.good_color_id = ?', $params['good_color_id']);

        if (isset($params['missing']) and $params['missing'])
            $select->where('p.missing > 0');

        if (isset($params['status']) and $params['status']){
            if($params['status'] == '1.1'){
                $select->where('p.status = ?', 1);
                $select->where('p.wms_status = ?', 0);
            }else{
                $select->where('p.status = ?', $params['status']);
            }
        }

        if(isset($params['report']) && $params['report']){

            $select->joinLeft(array('cso'=>'change_sales_order'), 'cso.borrowing_id = p.id', array('completed_date','sn_ref' => 'max(cso.sn_ref)','first_sn_ref' => 'min(cso.sn_ref)'));

            $select->joinLeft(array('hrss' => 'hr.staff'), 'hrss.code = p.code', array('hrss.regional_market'));

            $select->joinLeft(array('hrsrm' => 'hr.regional_market'), 'hrsrm.id = hrss.regional_market', array('hrsrm.area_id'));

            $select->joinLeft(array('hrsarea' => 'hr.area'), 'hrsarea.id = hrsrm.area_id', array('area_name' => 'hrsarea.name'));

            if (isset($params['co']) and $params['co'])
            $select->where('cso.sn_ref LIKE ?', '%'.$params['co'].'%');

            if (isset($params['area']) and $params['area'])
            $select->where('hrsarea.name LIKE ?', '%'.$params['area'].'%');

            // $select->where('cso.borrowing_id is not null');
        }else{
            if (isset($params['return']) and $params['return']){

                $select->joinLeft(array('hrs_rm' => 'oppohr.users'), 'p.rm_approved_by=hrs_rm.staff_code', array('hrs_rm_id' => 'hrs_rm.id',"TRIM(CONCAT(hrs_rm.firstname,' ',hrs_rm.lastname))AS rm_fullname"));

                $select->joinLeft(array('hrs_mg' => 'oppohr.users'), 'p.manager_approved_by=hrs_mg.staff_code', array('hrs_mg_id' => 'hrs_mg.id',"TRIM(CONCAT(hrs_mg.firstname,' ',hrs_mg.lastname))AS mg_fullname"));

                if (isset($params['rm_fullname']) and $params['rm_fullname'])
                    $select->where('hrs_rm.firstname LIKE ? or hrs_rm.lastname LIKE ?', '%'.$params['rm_fullname'].'%');

                if (isset($params['mg_fullname']) and $params['mg_fullname'])
                    $select->where('hrs_mg.firstname LIKE ? or hrs_mg.lastname LIKE ?', '%'.$params['mg_fullname'].'%');

                $select->where('p.borrowing_type <> ? or p.borrowing_type is null',2);
                $select->where('p.wms_return_date is null');
                $select->where('p.return_date is not null');
                $select->where('p.status = ?',13);
                $select->where('p.wms_status = ?',1);
            }else{
                $select->where('p.status = ?',1);
                $select->where('p.wms_status = ?',0);
            }
        }

        if(isset($params['report']) && $params['report']){
            $select->joinLeft(array('hrs_admin' => 'oppohr.users'), 'p.admin_approved_by=hrs_admin.staff_code', array('hrs_admin_id' => 'hrs_admin.id',"TRIM(CONCAT(hrs_admin.firstname,' ',hrs_admin.lastname))AS admin_fullname"));

            $select->joinLeft(array('hrs_asm' => 'oppohr.users'), 'p.asm_approved_by=hrs_asm.staff_code', array('hrs_asm_id' => 'hrs_asm.id',"TRIM(CONCAT(hrs_asm.firstname,' ',hrs_asm.lastname))AS asm_fullname"));

            $select->joinLeft(array('hrs_rm' => 'oppohr.users'), 'p.rm_approved_by=hrs_rm.staff_code', array('hrs_rm_id' => 'hrs_rm.id',"TRIM(CONCAT(hrs_rm.firstname,' ',hrs_rm.lastname))AS rm_fullname"));

            $select->joinLeft(array('hrs_area' => 'oppohr.users'), 'p.area_director_approved_by=hrs_area.staff_code', array('hrs_area_id' => 'hrs_area.id',"TRIM(CONCAT(hrs_area.firstname,' ',hrs_area.lastname))AS area_fullname"));

            $select->joinLeft(array('hrs_op' => 'oppohr.users'), 'p.operation_director_approved_by=hrs_op.staff_code', array('hrs_op_id' => 'hrs_op.id',"TRIM(CONCAT(hrs_op.firstname,' ',hrs_op.lastname))AS op_fullname"));

            $select->joinLeft(array('hrs_mg' => 'oppohr.users'), 'p.manager_approved_by=hrs_mg.staff_code', array('hrs_mg_id' => 'hrs_mg.id',"TRIM(CONCAT(hrs_mg.firstname,' ',hrs_mg.lastname))AS mg_fullname"));
        }

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            $order_str .= $params['sort'] . $collate . $desc;
            $select->order(new Zend_Db_Expr($order_str));
        }

        $select->group('bi.sn');

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function fetchPagination_return_imei($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        $select->joinLeft(array('hrs' => 'oppohr.users'), 'hrs.staff_code = p.code', array('hrs_id' => 'hrs.id', "TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->joinLeft(array('hrsdepartment' => 'oppohr.department'), 'hrsdepartment.id = hrs.department', array('hrs_department_id' => 'hrs.department', 'hrs_department_name' => 'hrsdepartment.name'));

        $select->joinLeft(array('hrsalary' => 'oppohr.salary'), 'hrsalary.staff_id=hrs.staff_id', array('hrsalary.position','hrsalary.department_position_code'));

        $select->joinLeft(array('hrdp' => 'oppohr.department_position'), 'hrdp.code=hrsalary.department_position_code', array('hrdp.position_name'));

        $select->joinLeft(array('bt'=>'borrowing_tran'), 'bt.bl_id = p.id', array('bt.imei','bt.co_return','imei_return_date' => 'bt.return_date','return_status' => 'bt.status'));

        $select->joinLeft(array('i' => 'imei'), 'i.imei_sn = bt.imei', array());
        $select->joinLeft(array('g' => 'good'), 'g.id = i.good_id', array('good_model' => 'g.name', 'good_name' => 'g.desc'));

        $select->joinLeft(array('gc' => 'good_color'), 'gc.id = i.good_color', array('good_color' => 'gc.name'));

        $select->joinLeft(array('bi'=>'borrowing_item'), 'bi.sn = p.sn and bi.good_id = i.good_id and bi.good_color_id = i.good_color', array('bi.product_grade'));

        $select->joinLeft(array('buf'=>'borrowing_upload_file'), 'buf.sn = p.sn and buf.enabled = 1', array('buf.image_name'));

        // $select->joinLeft(array('cso'=>'change_sales_order'), 'cso.borrowing_id = p.id', array('completed_date','sn_ref' => 'max(cso.sn_ref)','first_sn_ref' => 'min(cso.sn_ref)'));

        $select->joinLeft(array('cso'=>'change_sales_order'), 'cso.borrowing_id = p.id', array('completed_date','sn_ref' => 'max(cso.sn_ref)','first_sn_ref' => 'min(cso.sn_ref)'));

        $select->group(['cso.borrowing_id','bt.imei']);

        $select->joinLeft(array('hrss' => 'hr.staff'), 'hrss.code = p.code', array('hrss.regional_market'));

        $select->joinLeft(array('hrsrm' => 'hr.regional_market'), 'hrsrm.id = hrss.regional_market', array('hrsrm.area_id'));

        $select->joinLeft(array('hrsarea' => 'hr.area'), 'hrsarea.id = hrsrm.area_id', array('area_name' => 'hrsarea.name'));

        if (isset($params['co']) and $params['co'])
        $select->where('cso.sn_ref LIKE ?', '%'.$params['co'].'%');

        if( isset($params['co_munti']) && $params['co_munti'] != '' ){
                $arrayCo = $params['co_munti'];
                if(count($arrayCo) == 1){
                    $select->where('cso.sn_ref = ?',$params['co_munti']);
                }else{
                    $select->where('cso.sn_ref IN (?)',$params['co_munti']);
                }
            }

        if (isset($params['area']) and $params['area'])
        $select->where('hrsarea.name LIKE ?', '%'.$params['area'].'%');

        if (isset($params['rq']) and $params['rq'])
            $select->where('p.rq LIKE ?', '%'.$params['rq'].'%');

        if (isset($params['code']) and $params['code'])
            $select->where('p.code LIKE ?', '%'.$params['code'].'%');

        if (isset($params['fullname']) and $params['fullname'])
            $select->where('hrs.firstname LIKE ? or hrs.lastname LIKE ?', '%'.$params['fullname'].'%');

        if (isset($params['position']) and $params['position'])
            $select->where('hrdp.position_name LIKE ?', '%'.$params['position'].'%');

        if (isset($params['created_at_from']) and $params['created_at_from']){
                list( $day, $month, $year ) = explode('/', $params['created_at_from']);

        if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['created_at_to']) and $params['created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['created_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['return_at_from']) and $params['return_at_from']){
            list( $day, $month, $year ) = explode('/', $params['return_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.return_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['return_at_to']) and $params['return_at_to']){
            list( $day, $month, $year ) = explode('/', $params['return_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.return_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }

        if (isset($params['grade']) and $params['grade'])
            $select->where('bi.product_grade = ?', $params['grade']);

        // if (isset($params['type']) and $params['type']){
        //     $select->where('p.borrowing_type = ?', $params['type']);
        // }

        if (isset($params['status_return']) and $params['status_return']){

            switch ($params['status_return']) {
                case '1':
                    $select->where('bt.status = ?', $params['status_return']);

                    $select->where('p.wms_return_date is null');
                    // $select->where('p.return_date is not null');
                    $select->where('p.status = ?',13);
                    $select->order('p.return_date desc');
                    break;
                case '2':
                    // $select->where('bt.status = ? or p.return_date is null', $params['status_return']);
                    $select->where('bt.status = ?', $params['status_return']);

                    $select->order('bt.return_date desc');
                    break;
            }
        }

        if (isset($params['imei']) and $params['imei'])
            $select->where('bt.imei = ?', $params['imei']);

        if( isset($params['imei_munti']) && $params['imei_munti'] != '' ){
            $arrayImei = $params['imei_munti'];
            if(count($arrayImei) == 1){
                $select->where('bt.imei = ?',$params['imei_munti']);
            }else{
                $select->where('bt.imei IN (?)',$params['imei_munti']);
            }
        }

        if (isset($params['hrs_department_id']) and $params['hrs_department_id']){
            $select->where('hrs.department = ?', $params['hrs_department_id']);
        }

        if (isset($params['good_id']) and $params['good_id'])
            $select->where('bi.good_id = ?', $params['good_id']);

        if (isset($params['good_color_id']) and $params['good_color_id'])
            $select->where('bi.good_color_id = ?', $params['good_color_id']);

        if (isset($params['missing']) and $params['missing'])
            $select->where('p.missing > 0');

        if (isset($params['status']) and $params['status']){
            if($params['status'] == '1.1'){
                $select->where('p.status = ?', 1);
                $select->where('p.wms_status = ?', 0);
            }else{
                $select->where('p.status = ?', $params['status']);
            }
        }

        $select->joinLeft(array('hrs_rm' => 'oppohr.users'), 'p.rm_approved_by=hrs_rm.staff_code', array('hrs_rm_id' => 'hrs_rm.id',"TRIM(CONCAT(hrs_rm.firstname,' ',hrs_rm.lastname))AS rm_fullname"));

        $select->joinLeft(array('hrs_mg' => 'oppohr.users'), 'p.manager_approved_by=hrs_mg.staff_code', array('hrs_mg_id' => 'hrs_mg.id',"TRIM(CONCAT(hrs_mg.firstname,' ',hrs_mg.lastname))AS mg_fullname"));

        if (isset($params['rm_fullname']) and $params['rm_fullname'])
            $select->where('hrs_rm.firstname LIKE ? or hrs_rm.lastname LIKE ?', '%'.$params['rm_fullname'].'%');

        if (isset($params['mg_fullname']) and $params['mg_fullname'])
            $select->where('hrs_mg.firstname LIKE ? or hrs_mg.lastname LIKE ?', '%'.$params['mg_fullname'].'%');

        // $select->where('p.borrowing_type <> ? or p.borrowing_type is null',2);
        
        $select->where('p.wms_status = ?',1);

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            $order_str .= $params['sort'] . $collate . $desc;
            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getItemByRequert($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('bri' => 'borrowing_item'), 'p.sn=bri.sn', array('bri.*','total_qty' => 'sum(bri.qty)'));

        $select->joinLeft(array('g' => 'good'), 'g.id=bri.good_id', array('good_name' => 'g.name', 'good_main_name' => 'desc'));

        $select->joinLeft(array('gc' => 'good_color'), 'gc.id=bri.good_color_id', array('color_name' => 'gc.name'));

        $select->joinLeft(array('hrs' => 'oppohr.users'), 'p.code=hrs.staff_code', array('hrs_id' => 'hrs.id', "TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->joinLeft(array('hrsdepartment' => 'oppohr.department'), 'hrsdepartment.id = hrs.department', array('hrs_department_id' => 'hrs.department', 'hrs_department_name' => 'hrsdepartment.name'));

        $select->joinLeft(array('hrs_admin' => 'oppohr.users'), 'p.admin_approved_by=hrs_admin.staff_code', array('hrs_admin_id' => 'hrs_admin.id',"TRIM(CONCAT(hrs_admin.firstname,' ',hrs_admin.lastname))AS admin_fullname"));

        $select->joinLeft(array('hrs_asm' => 'oppohr.users'), 'p.asm_approved_by=hrs_asm.staff_code', array('hrs_asm_id' => 'hrs_asm.id',"TRIM(CONCAT(hrs_asm.firstname,' ',hrs_asm.lastname))AS asm_fullname"));

        $select->joinLeft(array('hrs_rm' => 'oppohr.users'), 'p.rm_approved_by=hrs_rm.staff_code', array('hrs_rm_id' => 'hrs_rm.id',"TRIM(CONCAT(hrs_rm.firstname,' ',hrs_rm.lastname))AS rm_fullname"));

        $select->joinLeft(array('hrs_area' => 'oppohr.users'), 'p.area_director_approved_by=hrs_area.staff_code', array('hrs_area_id' => 'hrs_area.id',"TRIM(CONCAT(hrs_area.firstname,' ',hrs_area.lastname))AS area_fullname"));

        $select->joinLeft(array('hrs_op' => 'oppohr.users'), 'p.operation_director_approved_by=hrs_op.staff_code', array('hrs_op_id' => 'hrs_op.id',"TRIM(CONCAT(hrs_op.firstname,' ',hrs_op.lastname))AS op_fullname"));

        $select->joinLeft(array('hrs_mg' => 'oppohr.users'), 'p.manager_approved_by=hrs_mg.staff_code', array('hrs_mg_id' => 'hrs_mg.id',"TRIM(CONCAT(hrs_mg.firstname,' ',hrs_mg.lastname))AS mg_fullname"));

        $select->where('p.id = ?',$id);
        $select->where('p.wms_status = ?',0);
        $select->where('p.status = ?',1);
        $select->group(['good_id','good_color_id']);


        // echo $select;die;

        $data = $db->fetchAll($select);

        return $data;

    }

    function getItemByReturn($id, $report = null, $return = null){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('bri' => 'borrowing_item'), 'p.sn=bri.sn', array('bri.*','total_qty' => 'sum(bri.qty)'));

        $select->joinLeft(array('g' => 'good'), 'g.id=bri.good_id', array('good_name' => 'g.name', 'good_main_name' => 'g.desc','good_cat_id' => 'g.cat_id'));

        $select->joinLeft(array('gc' => 'good_color'), 'gc.id=bri.good_color_id', array('color_name' => 'gc.name'));

        $select->joinLeft(array('hrs' => 'oppohr.users'), 'p.code=hrs.staff_code', array('hrs_id' => 'hrs.id', "TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->joinLeft(array('hrsdepartment' => 'oppohr.department'), 'hrsdepartment.id = hrs.department', array('hrs_department_id' => 'hrs.department', 'hrs_department_name' => 'hrsdepartment.name'));

        $select->joinLeft(array('hrsalary' => 'oppohr.salary'), 'hrsalary.staff_id=hrs.staff_id', array('hrsalary.position','hrsalary.department_position_code'));

        $select->joinLeft(array('hrdp' => 'oppohr.department_position'), 'hrdp.code=hrsalary.department_position_code', array('hrdp.position_name'));

        $select->joinLeft(array('hrs_admin' => 'oppohr.users'), 'p.admin_approved_by=hrs_admin.staff_code', array('hrs_admin_id' => 'hrs_admin.id',"TRIM(CONCAT(hrs_admin.firstname,' ',hrs_admin.lastname))AS admin_fullname"));

        $select->joinLeft(array('hrs_asm' => 'oppohr.users'), 'p.asm_approved_by=hrs_asm.staff_code', array('hrs_asm_id' => 'hrs_asm.id',"TRIM(CONCAT(hrs_asm.firstname,' ',hrs_asm.lastname))AS asm_fullname"));

        $select->joinLeft(array('hrs_rm' => 'oppohr.users'), 'p.rm_approved_by=hrs_rm.staff_code', array('hrs_rm_id' => 'hrs_rm.id',"TRIM(CONCAT(hrs_rm.firstname,' ',hrs_rm.lastname))AS rm_fullname"));

        $select->joinLeft(array('hrs_area' => 'oppohr.users'), 'p.area_director_approved_by=hrs_area.staff_code', array('hrs_area_id' => 'hrs_area.id',"TRIM(CONCAT(hrs_area.firstname,' ',hrs_area.lastname))AS area_fullname"));

        $select->joinLeft(array('hrs_op' => 'oppohr.users'), 'p.operation_director_approved_by=hrs_op.staff_code', array('hrs_op_id' => 'hrs_op.id',"TRIM(CONCAT(hrs_op.firstname,' ',hrs_op.lastname))AS op_fullname"));

        $select->joinLeft(array('hrs_mg' => 'oppohr.users'), 'p.manager_approved_by=hrs_mg.staff_code', array('hrs_mg_id' => 'hrs_mg.id',"TRIM(CONCAT(hrs_mg.firstname,' ',hrs_mg.lastname))AS mg_fullname"));

        $select->joinLeft(array('hrss' => 'hr.staff'), 'hrss.code = p.code', array('hrss.regional_market'));

        $select->joinLeft(array('hrsrm' => 'hr.regional_market'), 'hrsrm.id = hrss.regional_market', array('hrsrm.area_id'));

        $select->joinLeft(array('hrsarea' => 'hr.area'), 'hrsarea.id = hrsrm.area_id', array('area_name' => 'hrsarea.name'));

        if($return){
            $select->joinLeft(array('cso'=>'change_sales_order'), 'cso.id = (SELECT MAX(csot.id) FROM change_sales_order csot WHERE csot.borrowing_id = p.id)', array('cso.sn_ref','cso.type','cso.new_id','cso.old_id','cso.completed_date'));
        }else{
            $select->joinLeft(array('cso'=>'change_sales_order'), 'cso.id = (SELECT MIN(csot.id) FROM change_sales_order csot WHERE csot.borrowing_id = p.id)', array('cso.sn_ref','cso.type','cso.new_id','cso.old_id','cso.completed_date'));
        }

        $select->where('p.id = ?',$id);

        if(!$report){
            $select->where('p.wms_status = ?',1);
            $select->where('p.status = ?',13);
        }else{

            $select->joinLeft(array('buf'=>'borrowing_upload_file'), 'buf.sn = p.sn and buf.enabled = 1', array('buf.image_name'));
        }
        
        $select->group(['good_id','good_color_id']);

        // echo $select;die;

        $data = $db->fetchAll($select);

        return $data;

    }

    function getItem($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('bri' => 'borrowing_item'), 'p.sn=bri.sn', array('bri.*','total_qty' => 'sum(bri.qty)'));

        $select->joinLeft(array('g' => 'good'), 'g.id=bri.good_id', array('cat_id' ,'good_name' => 'g.name', 'good_main_name' => 'desc'));

        $select->joinLeft(array('gc' => 'good_color'), 'gc.id=bri.good_color_id', array('color_name' => 'gc.name'));

        $select->where('p.id = ?',$id);
        $select->group(['good_id','good_color_id']);

        // echo $select;die;

        $data = $db->fetchAll($select);

        return $data;

    }

    function getImeiByID($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('cso' => 'change_sales_order'), 'cso.borrowing_id=p.id', array('cso.changed_sn'));

        $select->joinLeft(array('i' => 'imei'), 'i.changed_sn=cso.changed_sn', array('i.imei_sn','i.good_id','i.good_color'));

        $select->where('p.id = ?',$id);

        // echo $select;die;

        $data = $db->fetchAll($select);

        return $data;

    }

    function getNameBorrowingByID($staff_code){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('hrs' => 'oppohr.users'),array("TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->where('hrs.staff_code = ?',$staff_code);

        $data = $db->fetchRow($select);

        return $data;
    }

    function getDetailsBorrowingByID($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.id = ?',$id);

        $data = $db->fetchRow($select);

        return $data;
    }

    function getImgBorrowing($rq){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array());

        $select->joinLeft(array('buf' => 'borrowing_upload_file'), 'buf.sn=p.sn', array('buf.image_name'));

        $select->where('buf.enabled = ?',1);
        $select->where('p.rq = ?',$rq);

        $data = $db->fetchAll($select);

        return $data;

    }

    function getDetailsUser($staff_code){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('hrs' => 'oppohr.users'),array("TRIM(CONCAT(hrs.firstname,' ',hrs.lastname))AS fullname"));

        $select->joinLeft(array('hrsdepartment' => 'oppohr.department'), 'hrsdepartment.id = hrs.department', array('hrs_department_id' => 'hrs.department', 'hrs_department_name' => 'hrsdepartment.name'));

        $select->where('hrs.staff_code = ?',$staff_code);

        $data = $db->fetchRow($select);

        return $data;
    }

    function getDetailsBorrwing($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('*'));

        $select->where('p.id = ?',$id);

        $data = $db->fetchRow($select);

        return $data;
    }

    function checkEndProcessRQ($id){

        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->joinLeft(array('bt' => 'borrowing_tran'), 'bt.bl_id = p.id', array('bt.*'));

        $select->where('bt.status = ?',1);
        $select->where('p.id = ?',$id);

        $data = $db->fetchRow($select);

        return $data;

    }
   
}