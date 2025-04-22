<?php
class Application_Model_ImeiReturn extends Zend_Db_Table_Abstract
{
	protected $_name = 'imei_return';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['imei_sn']) and $params['imei_sn'])
            $select->where('p.imei_sn LIKE ?', '%'.$params['imei_sn'].'%');

        if (isset($params['return_sn']) and $params['return_sn'])
            $select->where('p.return_sn LIKE ?', '%'.$params['return_sn'].'%');

        $select->where('p.back_sale = ?', 0);

        $select->where('p.back_warehouse_at is not null', null);

        $order_str = $collate = '';

        if (isset($params['sort']) and $params['sort']) {
            $collate = '';


            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getReturnOrderNo_Ref($sn)
    {
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('RO',".$sn.")");
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Return Order No, please try again!');
        }
        return $sn_ref;
    }

    public function get_credit_note_sn($db,$distributor_id,$user_id,$sn,$status,$return_type)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $creditnote_sn="";

        try {
            if($return_type ==''){
                $return_type = 0;
            }
            $stmt = $db->query("CALL gen_credit_note_sn_new_return('".$distributor_id."','".$user_id."',".$sn.",'".$status."','".$return_type."')");
            $result = $stmt->fetch();
            $creditnote_sn = $result['creditnote_sn'];
        }catch (exception $e) 
        {
            print_r($e.message);
            //$flashMessenger->setNamespace('error')->addMessage('Cannot Create Credit Note No, please try again!');
        }
        return $creditnote_sn;
    }

    public function getImeiReturnOrder($sn){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
        $select->joinLeft(array('i'=>'imei'),'i.imei_sn=p.imei_sn',array('i.*','g.brand_id'));
        $select->joinLeft(array('g' => 'good'),'g.id = i.good_id',array());
        $select->joinLeft(array('m'=>'market'),'m.sn=p.sales_order_sn and m.good_id=i.good_id and m.good_color=i.good_color',array('m.*'));
         
        $select->where('p.sales_order_sn = ?',$sn);
        // echo $select;die;

        $data = $db->fetchAll($select);
        return $data;
    }

    public function checkreturnOn($list_imei){
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));

        $select->where('p.imei_sn in (?)',$list_imei);
        $select->where('p.status = ?',0);

        $data = $db->fetchAll($select);

        return $data;
    }

    public function CheckSelloutPrice($imei){
        $db = Zend_Registry::get('db');


        $select = $db->select()
        ->from(array('ir' => 'imei_return'),array('ir.imei_sn','m.price'))
        ->joinLeft(array('m' => 'market'),'m.sn = ir.sales_order_sn',array())
        ->join(array('i' => 'imei'),'i.imei_sn = ir.imei_sn',array())
        ->where('m.good_id = i.good_id')
        ->where('ir.imei_sn =?',$imei)
        ->Where('ir.return_type =?',1)
        ->group('ir.imei_sn');

        // echo $select; die;

        $data = $db->fetchAll($select);

        return $data;
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
