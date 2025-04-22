<?php
class Application_Model_FactoryClaim extends Zend_Db_Table_Abstract{
    protected $_name = 'factory_claim';
    
    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.factory_claim_id'), 'p.*'));

        $select->joinLeft(array('s' => 'staff'), 's.id = p.created_by', array("TRIM(CONCAT(s.firstname,' ',s.lastname))AS fullname"));

        $select->joinLeft(array('sc' => 'staff'), 'sc.id = p.del_by', array("TRIM(CONCAT(sc.firstname,' ',sc.lastname))AS fullname_cancel"));


        $select->joinLeft(array('oi' => 'imei'), 'oi.imei_sn = p.old_imei', array());
        $select->joinLeft(array('oid' => 'good'), 'oid.id = oi.good_id', array('old_good_model' => 'oid.name', 'old_good' => 'oid.desc'));
        $select->joinLeft(array('oidc' => 'good_color'), 'oidc.id = oi.good_color', array('old_good_color' => 'oidc.name'));


        $select->joinLeft(array('ni' => 'imei'), 'ni.imei_sn = p.new_imei', array());
        $select->joinLeft(array('nid' => 'good'), 'nid.id = ni.good_id', array('new_good_model' => 'nid.name', 'new_good' => 'nid.desc'));
        $select->joinLeft(array('nidc' => 'good_color'), 'nidc.id = ni.good_color', array('new_good_color' => 'nidc.name'));


        if (isset($params['job_number']) and $params['job_number'])
            $select->where('p.job_number LIKE ?', '%'.$params['job_number'].'%');

        if (isset($params['old_imei']) and $params['old_imei'])
            $select->where('p.old_imei LIKE ?', '%'.$params['old_imei'].'%');

        if (isset($params['new_imei']) and $params['new_imei'])
            $select->where('p.new_imei LIKE ?', '%'.$params['new_imei'].'%');

        if (isset($params['warehouse']) and $params['warehouse'])
            $select->where('p.warehouse = ?', $params['warehouse']);

        if (isset($params['status']) and $params['status'])
            $select->where('p.status = ?', $params['status']);

        if (isset($params['request_at_from']) and $params['request_at_from']){
                list( $day, $month, $year ) = explode('/', $params['request_at_from']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
        }

        if (isset($params['request_at_to']) and $params['request_at_to']){
            list( $day, $month, $year ) = explode('/', $params['request_at_to']);

            if (isset($day) and isset($month) and isset($year) )
                $select->where('p.created_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
        }



        if(isset($params['action']) and $params['action']){

            // 0=delete,1=active,2=approve,3=cancel,4=input_money,5=collected_money
            switch ($params['action']) {
                case 'approve':
                    $select->where('p.status = ?',1);
                    break;
                case 'input_money':
                    $select->where('p.status = ?',2);
                    break;
                case 'get_money':
                    $select->where('p.status = ?',4);
                    break;
                
                default:
                    $select->where('p.status <> ?',0);
                    break;
            }
        }else{
            $select->where('p.status <> ?',0);
        }

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getJobNumber($job_number){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.job_number = ?',$job_number);
        $select->where('p.status not in (?)',[0,3]);

        return $db->fetchRow($select);
    }

    function checklistOldImei($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.old_imei = ?',$imei);
        $select->where('p.status not in (?)',[0,3]);

        return $db->fetchRow($select);
    }

    function checklistNewImei($imei){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->where('p.new_imei = ?',$imei);
        $select->where('p.status not in (?)',[0,3]);

        return $db->fetchRow($select);
    }

    function getFactoryClaim($id,$status){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->joinLeft(array('s' => 'staff'), 's.id = p.created_by', array("TRIM(CONCAT(s.firstname,' ',s.lastname))AS fullname"));

        $select->joinLeft(array('oi' => 'imei'), 'oi.imei_sn = p.old_imei', array());
        $select->joinLeft(array('oid' => 'good'), 'oid.id = oi.good_id', array('old_good_model' => 'oid.name', 'old_good' => 'oid.desc'));
        $select->joinLeft(array('oidc' => 'good_color'), 'oidc.id = oi.good_color', array('old_good_color' => 'oidc.name'));

        $select->joinLeft(array('ni' => 'imei'), 'ni.imei_sn = p.new_imei', array());
        $select->joinLeft(array('nid' => 'good'), 'nid.id = ni.good_id', array('new_good_model' => 'nid.name', 'new_good' => 'nid.desc'));
        $select->joinLeft(array('nidc' => 'good_color'), 'nidc.id = ni.good_color', array('new_good_color' => 'nidc.name'));

        $select->where('p.status = ?',$status);

        return $db->fetchRow($select);
    }

    function getFactoryClaimArray($array_id,$status){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('p.*'));

        $select->joinLeft(array('s' => 'staff'), 's.id = p.created_by', array("TRIM(CONCAT(s.firstname,' ',s.lastname))AS fullname"));

        $select->joinLeft(array('sa' => 'staff'), 'sa.id = p.approved_by', array("TRIM(CONCAT(sa.firstname,' ',sa.lastname))AS fullname_approved"));

        if(isset($status) and $status == 4){
            $select->joinLeft(array('sim' => 'staff'), 'sim.id = p.approved_by', array("TRIM(CONCAT(sim.firstname,' ',sim.lastname))AS fullname_input_money"));
        }

        $select->joinLeft(array('oi' => 'imei'), 'oi.imei_sn = p.old_imei', array());
        $select->joinLeft(array('oid' => 'good'), 'oid.id = oi.good_id', array('old_good_model' => 'oid.name', 'old_good' => 'oid.desc', 'old_good_price_import' => 'oid.price_4'));
        $select->joinLeft(array('oidc' => 'good_color'), 'oidc.id = oi.good_color', array('old_good_color' => 'oidc.name'));

        $select->joinLeft(array('ni' => 'imei'), 'ni.imei_sn = p.new_imei', array());
        $select->joinLeft(array('nid' => 'good'), 'nid.id = ni.good_id', array('new_good_model' => 'nid.name', 'new_good' => 'nid.desc'));
        $select->joinLeft(array('nidc' => 'good_color'), 'nidc.id = ni.good_color', array('new_good_color' => 'nidc.name'));

        $select->where('p.factory_claim_id in (?)',$array_id);
        $select->where('p.status = ?',$status);

        return $db->fetchAll($select);
    }

    function getDetailImeiByFactoryClaim($array_id,$status){
        $db = Zend_Registry::get('db');
        $select = $db->select()->from(array('p' => $this->_name), array('count_good' => 'count(p.factory_claim_id)', 'sum_price' => 'sum(p.price)'));
        
        $select->joinLeft(array('oi' => 'imei'), 'oi.imei_sn = p.old_imei', array('oi.good_id','oi.good_color'));

        $select->where('p.factory_claim_id in (?)',$array_id);
        $select->where('p.status = ?',$status);

        $select->group(['oi.good_id', 'oi.good_color']);

        return $db->fetchAll($select);
    }

}