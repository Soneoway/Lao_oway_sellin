<?php
class Application_Model_Checkmoney extends Zend_Db_Table_Abstract
{
	protected $_name = 'checkmoney';
	protected $_primary = 'id';

	public function fetchPagination($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');
		//Dòng tiền vào
		$cols  = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT ch.id'),
				'ch.*',
				'ch_id'     =>'id',
				'bank_name' =>'b.name',
                
			);
		$select = $db->select()
				->from(array('ch'=>$this->_name),$cols)
				->joinleft(array('d'=>'distributor'),'ch.d_id=d.id', array())
 				->joinleft(array('b'=>'bank'),'ch.bank = b.id',array())
                ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref'))
		;

        if (isset($params['d_id']) && $params['d_id'])
            $select->where('d.id = ?', intval($params['d_id']));

        $select->where('ch.finance_confirm_date is not null', null);

        $select->where('ch.canceled is null or ch.canceled !=1', null);
        $select->order('ch.sn desc');
        $select->order('ch.create_at desc');
        $select->order('ch.type desc');

		if($limit){
			$select->limitPage($page, $limit);
		}
        //echo $select;die;
		$result = $db->fetchAll($select);
		$total 	= $db->fetchOne("select FOUND_ROWS()");

		return $result;

	}

    /**
     * @param $page
     * @param $limit
     * @param $total
     * @param $params
     * @return mixed
     * @throws Zend_Exception
     * Hien thi transaction theo tung dealer
     */
	public function fetchPaginationHistory($page, $limit, &$total, $params){
		$db = Zend_Registry::get('db');
        $sub_select = $db->select()
            ->from(array('m' => 'market'), array('sn','sn_ref', 'total_sn' => 'SUM(total)'))
            ->group('sn');
        ;

        if (isset($params['d_id']) && $params['d_id'])
            $sub_select->where('d_id = ?', intval($params['d_id']));

        $cols = array(
            new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT ch.id'),'ch.*',
            'ch_id' => 'ch.id',
            'm.total_sn'
        );
		$select = $db->select()
			->from(array('ch' => 'checkmoney'),$cols)
			->joinLeft(array('d'  => 'distributor'),	'd.id = ch.d_id',array('d.unames','d.title'))
			->joinLeft(array('b'  => 'bank'),'ch.bank = b.id',array("bank_name" => 'b.name'))
			->joinLeft(array('m'=> $sub_select),'m.sn = ch.sn',array('m.sn_ref'))
			->where($db->quoteInto('ch.d_id = ?',$params['d_id']))
            ->where($db->quoteInto('ch.canceled is null or ch.canceled !=1'));

		if(	isset($params['type']) AND $params['type']	){
			$select->where($db->quoteInto('ch.type = ?',$params['type']));
		}

		if(isset($params['sort']) && $params['sort']){
			$order_str = $collate = '';
			if (!in_array($params['sort'], array('pay_money','pay_time','type'))){
				$collate .= ' COLLATE utf8_unicode_ci ';
			}
			$desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
			$order_str .= $params['sort'] . $collate . $desc;
			$select->order(new Zend_Db_Expr($order_str));
		}// End sort

		if($limit){
			$select->limitPage($page, $limit);
        }
       // echo $select;die;
		$result = $db->fetchAll($select);
		$total = $db->fetchOne("select FOUND_ROWS()");
		return $result;
	}


	public function fetchPaginationByRetailer($page, $limit, &$total, $params){
        $db     = Zend_Registry::get('db');

        $cols   = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ch.d_id'),
                    'ch_id'=>'ch.id','bank_id'=>'ch.bank',
                    'lasted'=> 'MAX(ch.pay_time)',
                    'ch.company_id'
                );
        $select = $db->select()
            ->from(array('ch'=>$this->_name),$cols)
            ->joinleft(array('d'=>'distributor'),'ch.d_id=d.id',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),'s.d_id=ch.d_id',array('balance'=>'s.balance','balance_smartmobile'))
            ->joinleft(array('b'=>'bank'),'b.id = ch.bank',array('name'))
            ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref'))
            ->group('ch.d_id')
            ->order('lasted DESC')
            ;

        if( isset($params['d_id']) && $params['d_id'] != '' ){
            $select->where('ch.d_id = ?',$params['d_id']);
        }

        if( isset($params['bank']) && $params['bank']   ){
            if( is_array($params['bank']) ){
                $select->where('ch.bank IN (?)',$params['bank']);
            }else{
                $select->where('ch.bank = ?',$params['bank']);
            }
        }

        if( isset($params['bank_serial']) && $params['bank_serial'] ){
            $select->where('ch.bank_serial like ?','%'.$params['bank_serial'].'%');
        }

        if( isset($params['bank_transaction_code']) && $params['bank_transaction_code'] ){
            $select->where('ch.bank_transaction_code like ?','%'.$params['bank_transaction_code'].'%');
        }

        if( isset($params['from_time']) and $params['from_time']    ){
            $select->where('DATE(ch.pay_time) >= ?',$params['from_time']);
        }

        if( isset($params['to_time']) and $params['to_time']    ){
            $select->where('DATE(ch.pay_time) <= ?',$params['to_time']);
        }

        if(isset($params['sn']) && $params['sn']){
            //$select->where('ch.sn LIKE ?','%'.$params['sn'].'%');
            $select->where('ch.sn LIKE ? or m.sn_ref LIKE ?','%'.$params['sn'].'%');
        }

        if(isset($params['type_money']) && $params['type_money']){
            $select->where('ch.type = ?',$params['type_money']);
        }

        if( isset($params['from_money']) ){
            $select->where('ch.pay_money >= ?',$params['from_money']);
        }

        if(isset($params['to_money'])){
            $select->where('ch.pay_money <= ?',$params['to_money']);
        }

        if(isset($params['note']) && $params['note']){
            $select->where('ch.note like ?','%'.$params['note'].'%');
        }

        if(isset($params['content']) && $params['content']){
            $select->where('ch.content like ?','%'.$params['content'].'%');
        }
        
      //  $select->where('m.old_data is null',null);
       // $select->where('m.canceled IS NULL', null);

        if($limit){
            $select->limitPage($page, $limit);
        }
        //Tanong
        //echo $select;die;
        $result = $db->fetchAll($select);

        $total  = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }


    public function fetchCashCollection($page, $limit, &$total, $params){
        $db     = Zend_Registry::get('db');

        $cols   = array(
                new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ch.d_id'),
                    'ch_id'=>'ch.id','bank_id'=>'ch.bank',
                    'lasted'=> 'MAX(ch.pay_time)',
                    'ch.company_id'
                );
        $select = $db->select()
            ->from(array('ch'=>$this->_name),$cols)
            ->joinleft(array('d'=>'distributor'),'ch.d_id=d.id',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),'s.d_id=ch.d_id',array('balance'=>'s.balance','balance_smartmobile'))
            ->joinleft(array('b'=>'bank'),'b.id = ch.bank',array('name'))
            ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref'))
            ->group('ch.d_id')
            ->order('lasted DESC')
            ;

        if( isset($params['d_id']) && $params['d_id'] != '' ){
            $select->where('ch.d_id = ?',$params['d_id']);
        }

        if( isset($params['bank']) && $params['bank']   ){
            if( is_array($params['bank']) ){
                $select->where('ch.bank IN (?)',$params['bank']);
            }else{
                $select->where('ch.bank = ?',$params['bank']);
            }
        }

        if( isset($params['bank_serial']) && $params['bank_serial'] ){
            $select->where('ch.bank_serial like ?','%'.$params['bank_serial'].'%');
        }

        if( isset($params['bank_transaction_code']) && $params['bank_transaction_code'] ){
            $select->where('ch.bank_transaction_code like ?','%'.$params['bank_transaction_code'].'%');
        }

        if( isset($params['from_time']) and $params['from_time']    ){
            $select->where('DATE(ch.pay_time) >= ?',$params['from_time']);
        }

        if( isset($params['to_time']) and $params['to_time']    ){
            $select->where('DATE(ch.pay_time) <= ?',$params['to_time']);
        }

        if(isset($params['sn']) && $params['sn']){
            //$select->where('ch.sn LIKE ?','%'.$params['sn'].'%');
            $select->where('ch.sn LIKE ? or m.sn_ref LIKE ?','%'.$params['sn'].'%');
        }

        if(isset($params['type_money']) && $params['type_money']){
            $select->where('ch.type = ?',$params['type_money']);
        }

        if( isset($params['from_money']) ){
            $select->where('ch.pay_money >= ?',$params['from_money']);
        }

        if(isset($params['to_money'])){
            $select->where('ch.pay_money <= ?',$params['to_money']);
        }

        if(isset($params['note']) && $params['note']){
            $select->where('ch.note like ?','%'.$params['note'].'%');
        }

        if(isset($params['content']) && $params['content']){
            $select->where('ch.content like ?','%'.$params['content'].'%');
        }
        
      //  $select->where('m.old_data is null',null);
       // $select->where('m.canceled IS NULL', null);

        if($limit){
            $select->limitPage($page, $limit);
        }
        //Tanong
        //echo $select;die;
        $result = $db->fetchAll($select);

        $total  = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }


    /**
     * Nếu from_time không chọn thì lấy ngày 1 của tháng hiện tại
     * Nếu to_time không chọn thì lấy ngày cuối cùng của tháng hiện tại
     * tiền dư đầu kì được tính bằng tiền dư cuối tháng ( tiền dư đầu tháng tính tổng số dư các khoản thời gian trước đó)
     * Tiền phí cũng được tính vào tiền khách hàng chuyển
     * cộng tiên đi đơn, và tiền khách gửi vào trong 1 tháng
     *
     */
    public function _getMoneyIn($params,&$from = '',&$to =''){

        $from = date('Y-m-01');
        $to   = date('Y-m-t');

        if( isset($params['from_time']) AND $params['from_time'] ){
            $from = $params['from_time'];
        }

        if( isset($params['to_time']) AND $params['to_time'] ){
            $to = $params['to_time'] . ' 23:59:59';
        }

        $beforeMonthString = strtotime($from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth = date('Y-m-d', $beforeMonthString);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth = date("Y-m-t 23:59:59", strtotime($beforeMonth));

        $db = Zend_Registry::get('db');

        $currentCondition = '';
        $beforeCondition  = '';

        if(	isset($from) and $from	){
            $currentCondition .= ' AND DATE(ch.pay_time) >= \''.$from.'\'';
        }

        if(	isset($to) and $to){
            $currentCondition .= ' AND ch.pay_time <= \''.$to .'\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }

        $arrCols    =  array(
            new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ch.d_id'),
            'd_id',
            'money_in'          =>  'SUM( CASE WHEN ( ch.type = 1 OR ch.type = 3 )'.$currentCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out'         =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$currentCondition.' THEN ch.output ELSE 0 END )',
            'money_in_before'   =>  'SUM( CASE WHEN ( ch.type = 1 OR ch.type = 3 ) '.$beforeCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out_before'  =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$beforeCondition.' THEN ch.output ELSE 0 END )',
        );

        //lấy giá trị trong thời gian hiện tại hoặc đang chọn
        $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->where('ch.d_id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->where('d.del = 0 OR d.del IS NULL')
            ->group('ch.d_id')
        ;

        if(	isset($params['d_id']) && $params['d_id'] != ''	){
            $select->where('ch.d_id = ?',$params['d_id']);

        }

        $resultCurrentTerm = $db->fetchAll($select);
        return $resultCurrentTerm;

    }

    public function getReportFullByWarehouse($params,&$from = '',&$to =''){
        //mặc định là đầu và cuối của tháng hiện tại

        $from = date('Y-m-01');
        $to   = date('Y-m-t');

        if( isset($params['from_time']) AND $params['from_time'] ){
            $from = $params['from_time'];
        }

        if( isset($params['to_time']) AND $params['to_time'] ){
            $to = $params['to_time'];
        }

		// Ngày đầu tháng trước
        $beforeMonthString = strtotime($from.' -1 Month');
        $beforeMonth = date('Y-m-d', $beforeMonthString);// Y-m-d

        //ngày cuối cùng của tháng trước
        $expiredBeforeMonth = date("Y-m-t", strtotime($beforeMonth)) .' 23:59:59';

        $db                 = Zend_Registry::get('db');
        $currentCondition   = '';
        $beforeCondition    = '';

        if($from){
            $currentCondition .= ' AND ch.pay_time >= \''.$from.'\'';
        }

        if($to){
            $currentCondition .= ' AND ch.pay_time <= \''.$to .' 23:59:59\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }

    	$arrCols    =  array(
            new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ch.d_id'),
            'ch.d_id',
            'd.parent',
            'money_in'          =>  'SUM(CASE WHEN ( ch.type = 1 OR ch.type = 3 OR ch.type = 4)'.$currentCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out'         =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$currentCondition.' THEN ch.output ELSE 0 END )',
            'money_in_before'   =>  'SUM(CASE WHEN ( ch.type = 1 OR ch.type = 3 OR ch.type = 4) '.$beforeCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out_before'  =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$beforeCondition.' THEN ch.output ELSE 0 END )',
        );

        //lấy giá trị trong thời gian hiện tại hoặc đang chọn
        $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->join(array('a'=> new Zend_Db_Expr('(
                        SELECT
                            id,
                            CASE WHEN parent IS NULL OR parent = 0 THEN id ELSE parent END as "agent"
                        FROM distributor
                )')),'a.id = ch.d_id',array())
            ->join(array('d'=>'distributor'),' a.agent = d.id ',array('d.title','d.store_code'))
            ->joinLeft(array('b'=>'bank'),'ch.bank = b.id',array())
            ->where('ch.pay_time <= ?',$params['to_time'].' 23:59:59')
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('d.id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->group('a.agent')
        ;

        if(isset($params['export_smartmobile']) AND $params['export_smartmobile']){
        	$select->where('ch.company_id = ?',2);
        }elseif(isset($params['export_oppo']) AND $params['export_oppo']){
        	$select->where('ch.company_id = ?',1);
        }else{

        }

        if(	isset($params['d_id']) && $params['d_id'] != ''	){
            $select->where('ch.d_id = ?',$params['d_id']);
        }

        $result = $db->fetchAll($select);
        $balances = $this->listBalance();
        foreach($result as $key => &$item){
            if(isset($params['export_smartmobile']) AND $params['export_smartmobile']){
                $balance = $balances[$item['d_id']]['balance_smartmobile'];
            }elseif(isset($params['export_oppo']) AND $params['export_oppo']){
                $balance = $balances[$item['d_id']]['balance'];
            }else{
                $balance = $balances[$item['d_id']]['balance'] + $balances[$item['d_id']]['balance_smartmobile'];
            }
            $result[$key]['balance'] = $balance;
        }

        return $result;
    }

    /**
     * @return array
     * @throws Zend_Exception
     * Dành cho function getReportFullByWarehouse()
     */
    public function listBalance(){
        $db = Zend_Registry::get('db');
        $cols = array(
            'id'                  => 'b.agent',
            'balance'             => new Zend_Db_Expr('SUM(a.balance)'),
            'balance_smartmobile' => new Zend_Db_Expr('SUM(a.balance_smartmobile)')
        );
        $select = $db->select()
            ->from(array('a'=>'store_account'),$cols)
            ->join(array('b'=>new Zend_Db_Expr('(
                SELECT
                    id,
                    CASE WHEN parent IS NULL OR parent = 0 THEN id ELSE parent END as "agent"
                FROM distributor
            )')),'a.d_id = b.id',array())
            ->join(array('c'=>'distributor'),'c.id = b.agent',array())
            ->where('c.id NOT IN (?)',unserialize(IGNORE_AUDIT))
            ->group('b.agent')
        ;
        $result = $db->fetchAll($select);
        $tmp  = array();
        foreach($result as $item){
            $tmp[$item['id']] = $item;
        }
        return $tmp;
    }

    public function fetchPaginationByDay($params,&$from = '',&$to =''){
        $from = date('Y-m-01');
        $to   = date('Y-m-t');
        if( isset($params['from_time']) AND $params['from_time'] ){
            $from = $params['from_time'];
        }

        if( isset($params['to_time']) AND $params['to_time'] ){
            $to = $params['to_time'] . ' 23:59:59';
        }

        $beforeMonthString = strtotime($from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth = date('Y-m-d', $beforeMonthString);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth = date("Y-m-t 23:59:59", strtotime($beforeMonth));

        $db = Zend_Registry::get('db');

        $currentCondition = '';
        $beforeCondition  = '';

        if( isset($from) and $from  ){
            $currentCondition .= ' AND ch.pay_time >= \''.$from.'\'';
        }

        if( isset($to) and $to){
            $currentCondition .= ' AND ch.pay_time <= \''.$to .'\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth.'\'' ;
        }

        $arrCols    =  array(
            new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ch.d_id'),
            'd_id',
            'money_in'         =>  'SUM( CASE WHEN ( ch.type = 1 OR ch.type = 3 )'.$currentCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out'        =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$currentCondition.' THEN ch.output ELSE 0 END )',
            'money_in_before'  =>  'SUM( CASE WHEN ( ch.type = 1 OR ch.type = 3 ) '.$beforeCondition.' THEN ch.pay_money ELSE 0 END )',
            'money_out_before' =>  'SUM(CASE WHEN ( ch.type = 2 ) '.$beforeCondition.' THEN ch.output ELSE 0 END )',
            'day_money'        => 'b.pay_money',
            'day_bank'         => 'c.name',
        );

        //lấy giá trị trong thời gian hiện tại hoặc đang chọn
        $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->joinLeft(array('a'=> new Zend_Db_Expr("(
                        SELECT MAX(id) id,d_id FROM checkmoney
                        WHERE pay_time BETWEEN '".$from."' AND '".$to."'
                                AND type = 1
                        GROUP BY d_id
                    )")),'a.d_id = d.id',array())
            ->joinLeft(array('b'=>'checkmoney'),'b.id = a.id',array())
            ->joinLeft(array('c'=>'bank'),'c.id = b.bank',array())
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('ch.d_id NOT IN (?)',unserialize(IGNORE_AUDIT))
            ->group('ch.d_id')
        ;


        if( isset($params['d_id']) && $params['d_id'] != '' ){
            $select->where('ch.d_id = ?',$params['d_id']);

        }
        $resultCurrentTerm = $db->fetchAll($select);

        return $resultCurrentTerm;
    }

     public function getAllPaymentOrder($sn)
     {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('c'=>'checkmoney'),array('SUM(c.pay_banktransfer)AS pay_banktransfer','SUM(c.pay_servicecharge)AS pay_servicecharge','SUM(c.pay_service)AS pay_service'))
            ->where('c.type=1',null)
            ->where('c.sn=?',$sn)
        ;
        $result = $db->fetchAll($select);
        return $result;
    }
     public function getSoPaymentslip($d_id)
     {
        $db = Zend_Registry::get('db');
        $sub_select = $db->select()
            ->from(array('m'=>'market'),array('m.sn_ref','m.invoice_number'))
            ->where('m.sn=c.sn')
            ->group('m.sn');


        $select = $db->select()
            ->from(array('c'=>'checkmoney'),array('c.sn', '(SELECT sn_ref FROM market WHERE sn=c.sn GROUP BY sn) AS sn_ref',
                '(SELECT SUM(ifnull(use_discount,0)) from   credit_note_tran where sales_order = c.sn) AS cn_sum',
                'SUM(IFNULL(c.output,0)) AS out_put',
                '(SUM(IFNULL(c.pay_money,0))*-1)-ifnull((SELECT SUM(ifnull(use_discount,0)) from credit_note_tran where sales_order = c.sn),0) AS pay_ment',
                '(SELECT invoice_number FROM market WHERE sn=c.sn GROUP BY sn) AS invoice_number',
                '(SUM(IFNULL(c.output,0))-SUM(IFNULL(c.pay_money*-1,0)))AS total'))
            
            ->where('1=1')
            ->where('c.d_id=?',$d_id)
            ->group(array('c.sn'))
            ->having('(SUM(IFNULL(c.pay_money,0))*-1)-ifnull((SELECT SUM(ifnull(use_discount,0)) from credit_note_tran where sales_order = c.sn),0) > 0');
        
        $result = $db->fetchAll($select);
        return $result;
    }

    public function getCheckDuplicate($sn,$photo_path)
     {
        $db = Zend_Registry::get('db');
        $select = $db->select()
             ->from(array('c' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS c.id'), 'c.*'))
            ->where('c.sn = ?',$sn)
            ->where('c.file_pay_slip = ?',$photo_path);
            // ->group('m.sn');

        // echo "$select";
        $result = $db->fetchRow($select);
        return $result;
    }
}


/
