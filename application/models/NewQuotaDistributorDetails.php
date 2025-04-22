<?php
class Application_Model_NewQuotaDistributorDetails extends Zend_Db_Table_Abstract{
  protected $_name = 'new_quota_distributor_details';
   
  function fetchPagination($page, $limit, &$total, $params){
    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => $this->_name),
            array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

    // if (isset($params['name']) and $params['name'])
        // $select->where('p.name LIKE ?', '%'.$params['name'].'%');

    $select->limitPage($page, $limit);

    $result = $db->fetchAll($select);
    $total = $db->fetchOne("select FOUND_ROWS()");
    return $result;
  }

  function getQuotaDetails($array_id){

    $db = Zend_Registry::get('db');
    $select = $db->select()
        ->from(array('p' => $this->_name), array('p.*'));

    $select->where('p.nqd_id in (?)',$array_id);
    $select->where('p.status <> ?',0);
    // echo $select;die;

    return $db->fetchAll($select);
  }

}