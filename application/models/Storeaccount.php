<?php
class Application_Model_Storeaccount extends Zend_Db_Table_Abstract
{
	protected $_name='store_account';

    public function updateBalance($d_id){
        $db 	= Zend_Registry::get('db');

        try {
            $select = $db->select()
                ->from(array('d'=>$this->_name),
                    array('count(*)'))
                ->where($db->quoteInto('d.d_id = ?',$d_id));
            $check_existed = $db->fetchOne($select);

            // update
            if ($check_existed){
                $selectCheckmoneyRecord = $db->select()
                    ->from('checkmoney')
                    ->where('d_id = ?',$d_id);
                $check_cm_existed = $db->fetchAll($selectCheckmoneyRecord);
                if(!$check_cm_existed){
                    $where = $db->quoteInto('d_id = ?',$d_id);
                    $this->update(array('balance' => 0,'balance_smartmobile'=> 0),$where);
                }else{
                    $sql='UPDATE store_account
                    INNER JOIN (
                        SELECT checkmoney.d_id,
                        SUM(CASE WHEN company_id = 1 AND `type` IN (1,3,4) THEN (IFNULL(checkmoney.pay_money,0)+IFNULL(checkmoney.pay_banktransfer,0)+IFNULL(checkmoney.pay_servicecharge,0)+IFNULL(checkmoney.pay_service,0)) WHEN company_id = 1 AND `type` = 2 THEN (-1 * IFNULL(checkmoney.output,0)+IFNULL(checkmoney.pay_service,0)+IFNULL(checkmoney.lack_of_money,0)) ELSE 0 END) AS sumu,
                        SUM(CASE WHEN company_id = 2 AND `type` IN (1,3,4) THEN (IFNULL(checkmoney.pay_money,0)+IFNULL(checkmoney.pay_banktransfer,0)+IFNULL(checkmoney.pay_servicecharge,0)+IFNULL(checkmoney.pay_service,0)) WHEN company_id = 2 AND `type` = 2 THEN (-1 * IFNULL(checkmoney.output,0)+IFNULL(checkmoney.pay_service,0)+IFNULL(checkmoney.lack_of_money,0)) ELSE 0 END) AS sum_smartmobile
                        FROM checkmoney
                        WHERE checkmoney.d_id = ?
                        GROUP BY checkmoney.d_id
                    ) i ON store_account.d_id = i.d_id
                    SET store_account.balance = i.sumu,
                        store_account.balance_smartmobile = sum_smartmobile';

                     //echo $d_id;die;
                    $stmt = $db->query($sql,array($d_id)
                        );
                    $stmt->execute();
                }
            } else {
                $select = $db->select()
                    ->from(array('d'=>'checkmoney'),
                        array(
                            'balance'=>'SUM(CASE WHEN company_id = 1 THEN (IFNULL(pay_money,0)+IFNULL(pay_banktransfer,0)+IFNULL(d.pay_servicecharge,0)+IFNULL(d.pay_service,0)+IFNULL(d.lack_of_money,0)) ELSE 0 END)',
                            'balance_smartmobile' => 'SUM(CASE WHEN company_id = 2 THEN IFNULL(pay_money,0)+IFNULL(d.pay_service,0)+IFNULL(d.lack_of_money,0) ELSE 0 END)'
                            ))
                    ->where($db->quoteInto('d.d_id = ?',$d_id));
                $balances = $db->fetchRow($select);
                $this->insert(array(
                        'd_id'                => $d_id,
                        'balance'             => $balances['balance'],
                        'balance_smartmobile' => $balances['balance_smartmobile'])
                );
            }
            return true;
        } catch (Exception $e){
            PC::debug($e->getMessage());
            return false;
        }
    }

    public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from(array('s'=>$this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS s.id'),'*'))
                ->join(array('d'=>'distributor'),'s.d_id = d.id',array('title'))
                ->order('d.title ASC')
        ;

        if( isset($params['d_id']) AND $params['d_id'] ){
            $select->where('d_id = ?',$params['d_id']);
        }

        if( isset($params['priority']) AND $params['priority']){
            $select->where('priority = ?',$params['priority']);
        }

        if($limit){
            $select->limitPage($page, $limit);
        }


        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;

    }

    public function getBalance($d_id){
        /**
         * Trả về balance and balance full
         */
        $db = Zend_Registry::get('db');

        $select_id="SELECT `main_distributor_id` FROM distributor WHERE id='".$d_id."'";
        $main_distributor = $db->fetchRow($select_id);

        $main_distributor_id =$main_distributor['main_distributor_id'];
        if($main_distributor_id !='')
        {
            $current = $this->getMainDistributorBalance($main_distributor_id);
        }else{
            $select = $db->select()
            ->from(array('p'=>$this->_name),array('p.balance','p.balance_smartmobile'))
            ->where('p.d_id = ?',$d_id);
            $current = $db->fetchRow($select);
        }

        if($current){
            return $current;
        }else{
            return false;
        }
    }

    public function getMainDistributorBalance($d_id)
    {
        if($d_id==''){
            return 0;
        }
        $db = Zend_Registry::get('db');

        $select_id="SELECT `main_distributor_id` FROM distributor WHERE id='".$d_id."'";
        $main_distributor = $db->fetchRow($select_id);

        $main_distributor_id =$main_distributor['main_distributor_id'];
        if($main_distributor_id !='')
        {
            $select="SELECT IFNULL(t1.credit_amount,0)as credit_amount
            ,IFNULL((t2.balance*-1),0) AS use_credit
            ,IFNULL(sum(IFNULL(IF(t1.credit_amount >0,(t1.credit_amount-(t2.balance*-1)-(ifnull(t3.pay_money,0)*-1)),0),0)),0) AS balance
            FROM (
            SELECT IFNULL(SUM(d.`credit_amount`),0) AS credit_amount,'1' AS key_id
            FROM distributor d 
            WHERE d.del = 0 AND  d.`id`='".$main_distributor_id."'
            GROUP BY d.`main_distributor_id`
            )t1
            LEFT JOIN (SELECT IFNULL(SUM(s.`pay_money`),0) AS balance,'1' AS key_id
            FROM distributor d 
            LEFT JOIN checkmoney s ON s.`d_id`=d.`id`
            WHERE d.del = 0 AND  d.main_distributor_id='".$main_distributor_id."' AND s.`canceled`<>1 AND s.sales_confirm_date IS NOT NULL AND s.TYPE=2
            GROUP BY d.`main_distributor_id`
            )t2 ON t1.key_id=t2.key_id
            LEFT JOIN (SELECT IFNULL(SUM(ck.`pay_money`),0)+IFNULL(SUM(ck.`pay_banktransfer`),0)+IFNULL(SUM(ck.`pay_servicecharge`),0)+IFNULL(SUM(ck.`pay_service`),0)+IFNULL(SUM(ck.`lack_of_money`),0) AS pay_money,'1' AS key_id
            FROM checkmoney ck 
            INNER JOIN market mk ON ck.`d_id`=mk.`d_id` AND ck.`sn`=mk.`sn`
            INNER JOIN distributor d ON d.`id`=ck.`d_id` AND d.`id`=mk.`d_id`
            WHERE mk.`canceled`<>1 
            AND  d.del = 0 AND  d.main_distributor_id='".$main_distributor_id."'
            AND ck.`canceled`<>1 AND ck.`finance_confirm_date` IS NULL
            GROUP BY mk.sn)t3 ON t1.key_id=t3.key_id
            ";
        }else{
            $select="SELECT IFNULL(t1.credit_amount,0)as credit_amount
            ,IFNULL((t2.balance*-1),0) AS use_credit
            ,IFNULL(IF(t1.credit_amount >0,(t1.credit_amount-(t2.balance*-1)-(ifnull(t3.pay_money,0)*-1)),0),0) AS balance
            FROM (
            SELECT IFNULL(SUM(d.`credit_amount`),0) AS credit_amount,'1' AS key_id
            FROM distributor d 
            WHERE d.del = 0 AND  d.`id`='".$d_id."'
            GROUP BY d.`id`
            )t1
            LEFT JOIN (SELECT IFNULL(SUM(s.`pay_money`),0) AS balance,'1' AS key_id
            FROM distributor d 
            LEFT JOIN checkmoney s ON s.`d_id`=d.`id`
            WHERE d.del = 0 AND d.`id`='".$d_id."' AND s.`canceled`<>1 AND s.sales_confirm_date IS NOT NULL AND s.TYPE=2
            GROUP BY d.`id`
            )t2 ON t1.key_id=t2.key_id
            LEFT JOIN (SELECT IFNULL(SUM(ck.`pay_money`),0)+IFNULL(SUM(ck.`pay_banktransfer`),0)+IFNULL(SUM(ck.`pay_servicecharge`),0)+IFNULL(SUM(ck.`pay_service`),0)+IFNULL(SUM(ck.`lack_of_money`),0) AS pay_money,'1' AS key_id
            FROM checkmoney ck 
            INNER JOIN market mk ON ck.`d_id`=mk.`d_id` AND ck.`sn`=mk.`sn`
            INNER JOIN distributor d ON d.`id`=ck.`d_id` AND d.`id`=mk.`d_id`
            WHERE mk.`canceled`<>1 
            AND  d.del = 0 AND d.`id`='".$d_id."'
            AND ck.`canceled`<>1 AND ck.`finance_confirm_date` IS NULL
            GROUP BY mk.sn)t3 ON t1.key_id=t3.key_id

            ";
        }

        //$result = $db->fetchAll($select);
        $result = $db->fetchRow($select);
        $total = $result;
        
        if($total > 0){
            return $total;
        }else{
            return 0;
        }
    }

    public function getMainDistributorBalanceByID($d_id)
    {
        if($d_id==''){
            return 0;
        }
        $db = Zend_Registry::get('db');

        $select_id="SELECT `main_distributor_id` FROM distributor WHERE id='".$d_id."'";
        $main_distributor = $db->fetchRow($select_id);

        $main_distributor_id =$main_distributor['main_distributor_id'];
        if($main_distributor_id !='')
        {
            $select="SELECT t1.credit_amount
            ,IFNULL((t2.balance*-1),0) AS use_credit
            ,IFNULL((t1.credit_amount-(t2.balance*-1)),0) AS balance
            FROM (
            SELECT IFNULL(SUM(d.`credit_amount`),0) AS credit_amount,'1' AS key_id
            FROM distributor d 
            WHERE d.del = 0 AND  d.id='".$main_distributor_id."'
            GROUP BY d.`main_distributor_id`
            )t1
            LEFT JOIN (SELECT IFNULL(SUM(s.`balance`),0) AS balance,'1' AS key_id
            FROM distributor d 
            LEFT JOIN store_account s ON s.`d_id`=d.`id`
            WHERE d.del = 0 AND  d.main_distributor_id='".$main_distributor_id."'
            GROUP BY d.`main_distributor_id`
            )t2 ON t1.key_id=t2.key_id";

        }else{
            $select="SELECT t1.credit_amount
            ,(t2.balance*-1) AS use_credit
            ,(t1.credit_amount-(t2.balance*-1)) AS balance
            FROM (
            SELECT IFNULL(SUM(d.`credit_amount`),0) AS credit_amount,'1' AS key_id
            FROM distributor d 
            WHERE d.del = 0 AND  d.`id`='".$d_id."'
            GROUP BY d.`id`
            )t1
            LEFT JOIN (SELECT IFNULL(SUM(s.`balance`),0) AS balance,'1' AS key_id
            FROM distributor d 
            LEFT JOIN store_account s ON s.`d_id`=d.`id`
            WHERE d.del = 0 AND d.`id`='".$d_id."'
            GROUP BY d.`id`
            )t2 ON t1.key_id=t2.key_id";
        }

        //$result = $db->fetchAll($select);
        $result = $db->fetchRow($select);
        $total = $result;
        
        if($total > 0){
            return $total;
        }else{
            return 0;
        }
    }


    public function getMainDistributorCreditBalance($d_id)
    {

        $db = Zend_Registry::get('db');

        if($d_id==''){
            return 0;
        }

        $select_id="SELECT `main_distributor_id` FROM distributor WHERE id='".$d_id."'";
        $main_distributor = $db->fetchRow($select_id);

        $main_distributor_id =$main_distributor['main_distributor_id'];

        $select = $db->select()
            ->from(array('d'=>'distributor'),array('balance' => 'SUM(d.credit_amount)'))
            ->where('d.main_distributor_id = ?',$main_distributor_id)
            ->where('d.del = ?',0);
        $result = $db->fetchAll($select);
        $total = $result[0]['balance'];
        
        if($total > 0){
            return $total;
        }else{
            return 0;
        }
        
    }

    public function getBalanceByGroup($d_id){
        $db           = Zend_Registry::get('db');
        $QDistributor = new Application_Model_Distributor();
        $current      = $QDistributor->find($d_id)->current();
        if(!$current){
            return false;
        }

        if($current->parent == 0){
            $root = $current;
        }else{
            $root = $QDistributor->find($current->parent)->current();
        }

        $d_id     = $root->id;
        $arr_id   = array();
        $arr_id[] = $d_id;
        $select = $db->select()
            ->from(array('p'=>'distributor'),array('p.id'))
            ->where('p.parent = ?',$d_id)
            //->where('p.del = 0')
            ;

        $ids = $db->fetchAll($select);

        if($ids){
            foreach($ids as $id){
                $arr_id[] = $id;
            }    
        }
        
        $select_all = $db->select()
            ->from(array('p'=>$this->_name),array(
               'balance' => 'SUM(p.balance)',
               'balance_smartmobile' => 'SUM(p.balance_smartmobile)',
            ))
            ->where('p.d_id IN (?)',$arr_id);
        $total = $db->fetchRow($select_all);
        //echo $select_all;die;
        return $total;
    }
}