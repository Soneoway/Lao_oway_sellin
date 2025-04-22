<?php
class Application_Model_JobNumber extends Zend_Db_Table_Abstract{
    protected $_name = 'job_number';
    
    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.job_sn'), 'p.*'));

        if (isset($params['job_sn']) and $params['job_sn'])
            $select->where('p.job_sn LIKE ?', '%'.$params['job_sn'].'%');

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getJobNumber($job_sn,$sales_order){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*','if(i.job_type=1,"นอกประกัน","ในประกัน/อัพซอฟแวร์") as job_type_name'));

        if($job_sn !=''){       
            $select->where('i.job_sn = ?',$job_sn);
        }
        if($sales_order !=''){
            $select->where('i.sales_order = ?',$sales_order);
        }
        return $db->fetchRow($select);
    }

    


}