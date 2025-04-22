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
                'm.pay_text',
				'ch_id'     =>'id',
				'bank_name' =>'b.name',
                
			);
		$select = $db->select()
				->from(array('ch'=>$this->_name),$cols)
				->joinleft(array('d'=>'distributor'),'ch.d_id=d.id', array())
 				->joinleft(array('b'=>'bank'),'ch.bank = b.id',array())
                ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref'))
		;

        //$select->where("ch.d_id not in(SELECT id FROM distributor dis WHERE (dis.rank ='3' OR dis.rank ='10'))", null);

        if (isset($params['d_id']) && $params['d_id'])
            $select->where('d.id = ?', intval($params['d_id']));

        $select->where('ch.finance_confirm_date is not null', null);
        $select->where('ch.credit_tr_id is null', null);

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

    public function fetchPaginationCredit($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        //Dòng tiền vào
        $cols  = array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS DISTINCT ch.id'),
                'ch.*',
                'm.pay_text',
                'ch_id'     =>'id',
                'bank_name' =>'b.name',
                
            );
        $select = $db->select()
                ->from(array('ch'=>$this->_name),$cols)
                ->joinleft(array('d'=>'distributor'),'ch.d_id=d.id', array())
                ->joinleft(array('b'=>'bank'),'ch.bank = b.id',array())
                ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref', 'invoice_number'))
        ;

        //$select->where("ch.d_id not in(SELECT id FROM distributor dis WHERE (dis.rank ='3' OR dis.rank ='10'))", null);

        if (isset($params['d_id']) && $params['d_id'])
            $select->where('d.id = ?', intval($params['d_id']));

        $select->where('ch.finance_confirm_date is not null', null);
        $select->where('ch.credit_tr_id is not null or ch.type = 2', null);

        $select->where('ch.canceled is null or ch.canceled !=1', null);

        $select->group('m.sn');

        $select->order('ch.sn desc');
        $select->order('ch.create_at desc');
        $select->order('ch.type desc');

        if($limit){
            $select->limitPage($page, $limit);
        }
        // echo $select;die;
        $result = $db->fetchAll($select);
        $total  = $db->fetchOne("select FOUND_ROWS()");

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

        if(isset($params['is_credit']) AND $params['is_credit'] = 'CREDIT'){
            $select->where($db->quoteInto('ch.credit_tr_id is not null or ch.type = 2'));
        }else{
            $select->where($db->quoteInto('ch.credit_tr_id is null'));
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
            ->joinleft(array('m'=>'market'),'ch.sn=m.sn', array('m.sn_ref','m.bs_campaign'))
            ->group('ch.d_id')
            ->order('lasted DESC')
            ;

        //$select->where("ch.d_id not in(SELECT id FROM distributor dis WHERE (dis.rank ='3' OR dis.rank ='10'))", null);    

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

        if( isset($params['from_collection_time']) and $params['from_collection_time']    ){
            $select->where('DATE(ch.pay_time) >= ?',$params['from_collection_time']);
        }

        if( isset($params['to_collection_time']) and $params['to_collection_time']    ){
            $select->where('DATE(ch.pay_time) <= ?',$params['to_collection_time']);
        }

        if( isset($params['from_invoice_time']) and $params['from_invoice_time']    ){
            $select->where('DATE(m.invoice_time) >= ?',$params['from_invoice_time']);
        }

        if( isset($params['to_invoice_time']) and $params['to_invoice_time']    ){
            $select->where('DATE(m.invoice_time) <= ?',$params['to_invoice_time']);
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

        if( isset($params['invoice_number']) && $params['invoice_number'] != '' ){
            $select->where('m.invoice_number = ?',$params['invoice_number']);
        }

        if( isset($params['finance_group']) && $params['finance_group'] != '' ){
            $select->where('d.finance_group = ?',$params['finance_group']);
        }

        if( isset($params['cancel']) && $params['cancel'] != '' ){
            $select->where('m.canceled = ?',$params['cancel']);
        }
        
       // $select->where('m.old_data is null',null);
       // $select->where('m.canceled IS NULL', null);

        $select->where('ch.credit_tr_id IS NULL', null);

        if($limit){
            $select->limitPage($page, $limit);
        }
        //Tanong
        // echo $select;die;
        $result = $db->fetchAll($select);

        $total  = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function fetchPaginationByRetailerCredit($page, $limit, &$total, $params){
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

        //$select->where("ch.d_id not in(SELECT id FROM distributor dis WHERE (dis.rank ='3' OR dis.rank ='10'))", null);    

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

        if( isset($params['from_collection_time']) and $params['from_collection_time']    ){
            $select->where('DATE(ch.pay_time) >= ?',$params['from_collection_time']);
        }

        if( isset($params['to_collection_time']) and $params['to_collection_time']    ){
            $select->where('DATE(ch.pay_time) <= ?',$params['to_collection_time']);
        }

        if( isset($params['from_invoice_time']) and $params['from_invoice_time']    ){
            $select->where('DATE(m.invoice_time) >= ?',$params['from_invoice_time']);
        }

        if( isset($params['to_invoice_time']) and $params['to_invoice_time']    ){
            $select->where('DATE(m.invoice_time) <= ?',$params['to_invoice_time']);
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

        if( isset($params['invoice_number']) && $params['invoice_number'] != '' ){
            $select->where('m.invoice_number = ?',$params['invoice_number']);
        }

        if( isset($params['finance_group']) && $params['finance_group'] != '' ){
            $select->where('d.finance_group = ?',$params['finance_group']);
        }

        if(isset($params['no_show_brandshop']) AND $params['no_show_brandshop']){
            $select->where('m.warehouse_id not in(SELECT id FROM warehouse WHERE warehouse_type =3 OR warehouse_type =4) or m.warehouse_id in (71,92)',null);
            // allow warehouse WMDGC - คลังเคลมDigital, Brandshop Warehouse at Kerry
        }
        
        // $select->where('m.old_data is null',null);
       // $select->where('m.canceled IS NULL', null);

        $select->where('ch.credit_tr_id IS NOT NULL OR ch.type = 2', null);

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

        $collection_from = date('Y-m-01');
        $collection_to   = date('Y-m-t');

        if(isset($params['from_collection_time']) AND $params['from_collection_time']){
            $collection_from = $params['from_collection_time'];
        }

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $collection_to = $params['to_collection_time'] . ' 23:59:59';
        }

        $beforeMonthString_collection = strtotime($collection_from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth_collection = date('Y-m-d', $beforeMonthString_collection);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth_collection = date("Y-m-t 23:59:59", strtotime($beforeMonth_collection));

        $db = Zend_Registry::get('db');

        $currentCondition = '';
        $beforeCondition  = '';

        if(isset($collection_from) and $collection_from){
            $currentCondition .= ' AND DATE(ch.pay_time) >= \''.$collection_from.'\'';
        }

        if(	isset($collection_to) and $collection_to){
            $currentCondition .= ' AND ch.pay_time <= \''.$collection_to .'\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }

        $invoice_from = date('Y-m-01');
        $invoice_to   = date('Y-m-t');

        if(isset($params['from_invoice_time']) AND $params['from_invoice_time']){
            $invoice_from = $params['from_invoice_time'];
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $invoice_to = $params['to_invoice_time'] . ' 23:59:59';
        }

        $beforeMonthString_invoice = strtotime($invoice_from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth_invoice = date('Y-m-d', $beforeMonthString_invoice);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth_invoice = date("Y-m-t 23:59:59", strtotime($beforeMonth_invoice));

        if(isset($invoice_from) and $invoice_from){
            $currentCondition .= ' AND DATE(m.invoice_time) >= \''.$invoice_from.'\'';
        }

        if(isset($invoice_to) and $invoice_to){
            $currentCondition .= ' AND m.invoice_time <= \''.$invoice_to .'\'';
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
        }else{
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
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

        if(isset($params['from_invoice_time']) AND $params['from_invoice_time'] || isset($params['to_invoice_time']) AND $params['to_invoice_time']){

            $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->joinleft(array('m'=>'market'),'ch.sn = m.sn and AND m.invoice_time IS NOT NULL', array('m.sn_ref'))
            ->where('ch.d_id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->where('d.del = 0 OR d.del IS NULL')
            ->group('m.sn','ch.d_id');

        }else{

            $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->where('ch.d_id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->where('d.del = 0 OR d.del IS NULL')
            ->group('ch.d_id');

        }

        if(	isset($params['d_id']) && $params['d_id'] != ''	){
            $select->where('ch.d_id = ?',$params['d_id']);

        }

        $resultCurrentTerm = $db->fetchAll($select);
        return $resultCurrentTerm;

    }

    public function getReportFullByWarehouse($params,&$from = '',&$to =''){
        //mặc định là đầu và cuối của tháng hiện tại

        $collection_from = date('Y-m-01');
        $collection_to   = date('Y-m-t');

        if( isset($params['from_collection_time']) AND $params['from_collection_time'] ){
            $collection_from = $params['from_collection_time'];
        }

        if( isset($params['to_collection_time']) AND $params['to_collection_time'] ){
            $collection_to = $params['to_collection_time'];
        }

		// Ngày đầu tháng trước
        $beforeMonthString_collection = strtotime($collection_from.' -1 Month');
        $beforeMonth_collection = date('Y-m-d', $beforeMonthString_collection);// Y-m-d

        //ngày cuối cùng của tháng trước
        $expiredBeforeMonth_collection = date("Y-m-t", strtotime($beforeMonth_collection)) .' 23:59:59';

        $db                 = Zend_Registry::get('db');
        $currentCondition   = '';
        $beforeCondition    = '';

        if($collection_from){
            $currentCondition .= ' AND ch.pay_time >= \''.$collection_from.'\'';
        }

        if($collection_to){
            $currentCondition .= ' AND ch.pay_time <= \''.$collection_to .' 23:59:59\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }

        $invoice_from = date('Y-m-01');
        $invoice_to   = date('Y-m-t');

        if( isset($params['from_invoice_time']) AND $params['from_invoice_time'] ){
            $invoice_from = $params['from_invoice_time'];
        }

        if( isset($params['to_invoice_time']) AND $params['to_invoice_time'] ){
            $invoice_to = $params['to_invoice_time'];
        }

        // Ngày đầu tháng trước
        $beforeMonthString_invoice = strtotime($invoice_from.' -1 Month');
        $beforeMonth_invoice = date('Y-m-d', $beforeMonthString_invoice);// Y-m-d

        //ngày cuối cùng của tháng trước
        $expiredBeforeMonth_invoice = date("Y-m-t", strtotime($beforeMonth_invoice)) .' 23:59:59';

        if($invoice_from){
            $currentCondition .= ' AND m.invoice_time >= \''.$invoice_from.'\'';
        }

        if($invoice_to){
            $currentCondition .= ' AND m.invoice_time <= \''.$invoice_to .' 23:59:59\'';
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
        }else{
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
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

        if(isset($params['from_collection_time']) AND $params['from_collection_time'] || isset($params['to_collection_time']) AND $params['to_collection_time']){

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
            ->joinleft(array('m'=>'market'),'ch.sn = m.sn and AND m.invoice_time IS NOT NULL', array('m.sn_ref'))
            ->where('ch.pay_time <= ?',$params['to_collection_time'].' 23:59:59')
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('d.id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->group('m.sn','a.agent');

        }else{

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
            ->where('ch.pay_time <= ?',$params['to_collection_time'].' 23:59:59')
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('d.id NOT IN  (?)',unserialize(IGNORE_AUDIT))
            ->group('a.agent');

        }

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
        $collection_from = date('Y-m-01');
        $collection_to   = date('Y-m-t');
        if(isset($params['from_collection_time']) AND $params['from_collection_time']){
            $collection_from = $params['from_collection_time'];
        }

        if(isset($params['to_collection_time']) AND $params['to_collection_time']){
            $to = $params['to_collection_time'] . ' 23:59:59';
        }

        $beforeMonthString_collection = strtotime($collection_from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth_collection = date('Y-m-d', $beforeMonthString_collection);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth_collection = date("Y-m-t 23:59:59", strtotime($beforeMonth_collection));

        $db = Zend_Registry::get('db');

        $currentCondition = '';
        $beforeCondition  = '';

        if( isset($collection_from) and $collection_from  ){
            $currentCondition .= ' AND ch.pay_time >= \''.$collection_from.'\'';
        }

        if( isset($collection_to) and $collection_to){
            $currentCondition .= ' AND ch.pay_time <= \''.$collection_to .'\'';
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }else{
            $beforeCondition  .= ' AND ch.pay_time <= \''.$expiredBeforeMonth_collection.'\'' ;
        }

        $invoice_from = date('Y-m-01');
        $invoice_to   = date('Y-m-t');
        if(isset($params['from_invoice_time']) AND $params['from_invoice_time']){
            $invoice_from = $params['from_invoice_time'];
        }

        if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
            $to = $params['to_invoice_time'] . ' 23:59:59';
        }

        $beforeMonthString_invoice = strtotime($invoice_from.' -1 Month');

        // Ngày đầu tháng
        $beforeMonth_invoice = date('Y-m-d', $beforeMonthString_invoice);// Y-m-d

        //ngày cuối cùng của tháng
        $expiredBeforeMonth_invoice = date("Y-m-t 23:59:59", strtotime($beforeMonth_invoice));

        if( isset($invoice_from) and $invoice_from  ){
            $currentCondition .= ' AND m.invoice_time >= \''.$invoice_from.'\'';
        }

        if( isset($invoice_to) and $invoice_to){
            $currentCondition .= ' AND m.invoice_time <= \''.$invoice_to .'\'';
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
        }else{
            $beforeCondition  .= ' AND m.invoice_time <= \''.$expiredBeforeMonth_invoice.'\'' ;
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

        if(isset($params['from_invoice_time']) && $params['from_invoice_time'] ||  isset($params['to_invoice_time']) && $params['to_invoice_time']){

            $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->joinLeft(array('a'=> new Zend_Db_Expr("(
                        SELECT MAX(id) id,d_id FROM checkmoney
                        WHERE pay_time BETWEEN '".$collection_from."' AND '".$collection_to."'
                                AND type = 1
                        GROUP BY d_id
                    )")),'a.d_id = d.id',array())
            ->joinLeft(array('b'=>'checkmoney'),'b.id = a.id',array())
            ->joinLeft(array('c'=>'bank'),'c.id = b.bank',array())
            ->joinleft(array('m'=>'market'),'ch.sn = m.sn and AND m.invoice_time IS NOT NULL', array('m.sn_ref'))
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('ch.d_id NOT IN (?)',unserialize(IGNORE_AUDIT))
            ->group('m.sn','ch.d_id');

        }else{

            $select = $db->select()
            ->from(array('ch'=>$this->_name),$arrCols)
            ->joinleft(array('d'=>'distributor'),' ch.d_id = d.id ',array('d.title','d.store_code'))
            ->joinleft(array('s'=>'store_account'),' s.d_id = ch.d_id ',array('balance'=>'s.balance'))
            ->joinLeft(array('a'=> new Zend_Db_Expr("(
                        SELECT MAX(id) id,d_id FROM checkmoney
                        WHERE pay_time BETWEEN '".$collection_from."' AND '".$collection_to."'
                                AND type = 1
                        GROUP BY d_id
                    )")),'a.d_id = d.id',array())
            ->joinLeft(array('b'=>'checkmoney'),'b.id = a.id',array())
            ->joinLeft(array('c'=>'bank'),'c.id = b.bank',array())
            ->where('d.del = 0 OR d.del IS NULL')
            ->where('ch.d_id NOT IN (?)',unserialize(IGNORE_AUDIT))
            ->group('ch.d_id');

        }


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
                '(SUM(IFNULL(c.pay_money,0))*-1)-ifnull((SELECT SUM(ifnull(use_discount,0)) from credit_note_tran where sales_order = c.sn),0)-ifnull((SELECT
                SUM(ifnull(pgt.use_total , 0))
            FROM
                pay_group pg
            LEFT JOIN pay_group_tran pgt on pgt.payment_tran_id = pg.payment_id and pgt.status = 1
            WHERE
                pg.sale_order = c.sn AND pg.status = 1
            GROUP BY pgt.payment_id),0) AS pay_ment',
                '(SELECT invoice_number FROM market WHERE sn=c.sn GROUP BY sn) AS invoice_number',
                '(SUM(IFNULL(c.output,0))-SUM(IFNULL(c.pay_money*-1,0)))AS total'))
            
            ->where('1=1')
            ->where('c.d_id=?',$d_id)
            ->group(array('c.sn'))
            ->having('(SUM(IFNULL(c.pay_money,0))*-1)-ifnull((SELECT SUM(ifnull(use_discount,0)) from credit_note_tran where sales_order = c.sn),0)-ifnull((SELECT
                SUM(ifnull(pgt.use_total , 0))
            FROM
                pay_group pg
            LEFT JOIN pay_group_tran pgt on pgt.payment_tran_id = pg.payment_id and pgt.status = 1
            WHERE
                pg.sale_order = c.sn AND pg.status = 1
            GROUP BY pgt.payment_id),0) > 0');
        //     echo "<pre>";
        // echo $select; die;
        $result = $db->fetchAll($select);
        return $result;
    }
    //Pungpond
    public function getCheckDuplicate($sn,$photo_path,$payment_order_val)
     {

       //by pass function
       return array();

       $payment = explode('.', $payment_order_val);
      
       if (count($payment)==1) {
       $payment_order_val = $payment_order_val.'.00';
       }
        $db = Zend_Registry::get('db');
        $select = $db->select()
             ->from(array('c' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS c.id'), 'c.*'))
            ->where('c.sn = ?',$sn)
            ->where('c.file_pay_slip = ?',$photo_path)
            ->where('c.pay_money = ?',$payment_order_val);
            // ->group('m.sn');
        // die();    
        // echo "$select";
        $result = $db->fetchRow($select);
        return $result;
    }

    public function checkDuplicatePayment($sn,$bank_id,$pay_money,$from_time,$to_time)
    {
        $db = Zend_Registry::get('db');

        $select = "SELECT t2.* FROM (
SELECT t.sn,t.bank,t.pay_money,DATE_FORMAT(t.pay_time, '%Y-%m-%d %H:%i')AS payment_date
FROM checkmoney t
WHERE t.type=1
AND t.sn='".$sn."'
AND t.bank='".$bank_id."'
AND t.pay_money='".$pay_money."'
AND t.`pay_time` BETWEEN '".$from_time."' AND '".$to_time."'
)t1
INNER JOIN 
(SELECT d.title as d_name,sum(m.num) as total_qty,t.sn,m.sn_ref,s.username,t.bank,b.name AS bank_name,t.pay_money,DATE_FORMAT(t.pay_time, '%Y-%m-%d %H:%i')AS payment_date
FROM checkmoney t
LEFT JOIN market m ON t.sn=m.sn
LEFT JOIN bank b ON t.`bank`=b.id
LEFT JOIN staff s ON t.create_by=s.id
LEFT JOIN distributor d ON d.id = m.d_id
WHERE t.type=1 
and m.sn_ref is not null
AND t.bank='".$bank_id."'
AND t.pay_money='".$pay_money."'
AND t.`pay_time` BETWEEN '".$from_time."' AND '".$to_time."'
AND m.good_id <> 127
GROUP BY m.sn
)t2 
ON t1.payment_date=t2.payment_date
AND t1.bank=t2.bank
AND t1.pay_money=t2.pay_money
AND t1.sn!=t2.sn
GROUP BY t2.sn,t2.bank,t2.pay_money,t2.payment_date 
";

       // echo $select;die;

        $result = $db->fetchAll($select);
        return $result;
    }


    public function _getDataForExcelCashCollection($data,$params)
    {
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $arr_d_id=0;
        
        $from_time = '00:00:00';
        $from_to = '23:59:59';
        $where_paytime = '';
        $where_invoicetime = '';
        $where_d_id = '';
        $where_invoice = '';
        $where_finaceGroup = '';
        $where_group_id="";
        //print_r($params);die;

        if(isset($params['shop_type']) AND $params['shop_type']=='service' || $params['shop_type']=='brandshop'){
            $select_group = $db->select()
                ->from(array('u' => 'distributor_group_user'),array('u.group_id'))
                ->where('u.user_id=?',$userStorage->id);
            $result_group = $db->fetchAll($select_group);
            $group_id = "";
            if ($result_group){
                foreach ($result_group as $to) {
                    $group_id .= $to['group_id'].',';
                }

                $where_group_id = ' and d.group_id in('.rtrim($group_id, ',').')';
            }
        }

        if(isset($params['action_from']) AND $params['action_from']=='checkmoney'){

            if(isset($params['to_collection_time']) AND $params['to_collection_time']){
                $params['to_collection_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
            }

            if(isset($params['to_invoice_time']) AND $params['to_invoice_time']){
                $params['to_invoice_time'] .= ' 23:59:59';//tìm kiếm đến cuối ngày
            }
        }
            
        if($arr_d_id == 0){
            if(isset($params['action_from']) AND $params['action_from']=='checkmoney'){
                if(isset($params['from_collection_time']) AND $params['from_collection_time'])
                {
                    $pay_time_st = $params['from_collection_time'];
                    $where_paytime .= " AND (ck.pay_time >= '".$pay_time_st."') ";
                }

                if(isset($params['to_collection_time']) AND $params['to_collection_time'])
                {
                    $pay_time_en = $params['to_collection_time'];
                    $where_paytime .= " AND (ck.pay_time <= '".$pay_time_en."') ";
                }

                if(isset($params['from_invoice_time']) AND $params['from_invoice_time'])
                {
                    $invoice_time_st = $params['from_invoice_time'];
                    $where_invoicetime .= " AND (mm.invoice_time >= '".$invoice_time_st."') ";
                }

                if(isset($params['to_invoice_time']) AND $params['to_invoice_time'])
                {
                    $invoice_time_en = $params['to_invoice_time'];
                    $where_invoicetime .= " AND (mm.invoice_time <= '".$invoice_time_en."') ";
                }

                if(isset($params['d_id']) AND $params['d_id'])
                {
                    $d_id = $params['d_id'];
                    $where_d_id=" AND (ck.d_id = '".$d_id."') ";
                }
      
                if(isset($params['sn']) AND $params['sn'])
                {
                    $sn = $params['sn'];
                    $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
                }

                if( isset($params['invoice_number']) && $params['invoice_number']){
                    $where_invoice=" AND (mm.invoice_number = '".$params['invoice_number']."') ";
                }

                if( isset($params['finance_group']) && $params['finance_group']){
                    $where_finaceGroup=" AND (d.finance_group = '".$params['finance_group']."') ";
                }
            }else if(isset($params['action_from']) AND $params['action_from']=='sales'){
                if (isset($params['created_at_from']) and $params['created_at_from']){
                    list( $day, $month, $year ) = explode('/', $params['created_at_from']);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $where_date = $year.'-'.$month.'-'.$day.' '.$from_time;
                        $where_invoice .=" AND (mm.add_time >= '".$where_date."') ";
                    }
                }

                if (isset($params['created_at_to']) and $params['created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['created_at_to']);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $where_date = $year.'-'.$month.'-'.$day.' '.$from_to;
                        $where_invoice .=" AND (mm.add_time <= '".$where_date."') ";
                    }
                } 

                if (isset($params['invoice_time_from']) and $params['invoice_time_from']){
                    list( $day, $month, $year ) = explode('/', $params['invoice_time_from']);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $where_date = $year.'-'.$month.'-'.$day.' '.$from_time;
                        $where_invoice .=" AND (mm.invoice_time >= '".$where_date."') ";
                    }
                }

                if (isset($params['invoice_time_to']) and $params['invoice_time_to']){
                    list( $day, $month, $year ) = explode('/', $params['invoice_time_to']);
                    list( $year,$time ) = explode(' ', $year);

                    if (isset($day) and isset($month) and isset($year) ){
                        $where_date = $year.'-'.$month.'-'.$day.' '.$from_to;
                        $where_invoice .=" AND (mm.invoice_time <= '".$where_date."') ";
                    }
                } 

                if(isset($params['d_id']) AND $params['d_id'])
                {
                    $d_id = $params['d_id'];
                    $where_d_id=" AND (ck.d_id = '".$d_id."') ";
                }
      
                if(isset($params['sn']) AND $params['sn'])
                {
                    $sn = $params['sn'];
                    $where_sn=" AND (ck.sn = '".$sn."') or (ck.sn =(SELECT sn FROM market WHERE sn_ref='".$sn."' GROUP BY sn)) ";
                }

                if( isset($params['invoice_number']) && $params['invoice_number']){
                    $where_invoice.=" AND (mm.invoice_number = '".$params['invoice_number']."') ";
                }

                if( isset($params['finance_group']) && $params['finance_group']){
                    $where_finaceGroup=" AND (d.finance_group = '".$params['finance_group']."') ";
                }
            }
            
        
            //---------- New -------------
            $sql_total_pay_order="SELECT 
            `m`.`sn`,
            '2' as `TYPE`,m.payment_no,0 as lack_of_money,0 as payment_surplus,
            '1' AS `company_id`,
             if((select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)>0,(select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)*-1,(select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)) AS `pay_money`,
             if((select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)>0,(select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)*-1,(select IFNULL((SUM(cnt.use_discount)-ch.pay_money),ch.pay_money) from checkmoney ch left join credit_note_tran cnt on CAST(cnt.sales_order AS CHAR) = CAST(ch.sn AS CHAR)  where ch.sn = `m`.`sn` and ch.type = 2)) AS `output`,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `m`.`d_id`,
            `m`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            '' AS `bank_name`,
            '' AS `credit_card`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            '' AS file_pay_slip, 
            '1' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,(SELECT concat('''',ph.phone_number_sn) FROM phone_number ph where ph.sales_order=m.sn and ph.status=1)as phone_number_sn
          FROM
             `market` AS `m` 
          WHERE 1=1
            AND m.sn IN (SELECT  cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0' GROUP BY mm.sn) 
            GROUP BY `m`.`sn`";

            
            echo $sql_total_pay_order;die;

          /*--------------Tranfer payment_surplus_unuse เงินเกิน------------------*/
            $sql_checkmoney_payment_surplus_unuse=" SELECT 
            `m`.`sn`,
            '14' AS `TYPE`,
            ch.payment_no,
            0 AS lack_of_money,
            0 AS payment_surplus,
            1 AS company_id,
            ROUND((`pt`.`total_amount`)*-1, 2) AS pay_money,
            ROUND((`pt`.`use_total`)*-1, 2) AS output,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `ch`.`d_id`,
            `ch`.modified_date AS pay_time,
            '' AS `creditnote_sn`,
            '' AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            '' AS `credit_card`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            '' AS `file_pay_slip`,
            '9' AS seq,
            m.pay_text,
            m.`canceled`,
            m.`canceled_remark`,
            SUBSTRING(m.pay_text, - 14, 14) AS sales_order_ref,
            ch.payment_no AS payment_no_ref ,m.bs_campaign,'' as phone_number_sn
          FROM
            pay_group_balance pt 
            LEFT JOIN `pay_group` AS `ch` 
              ON ch.payment_id = pt.payment_id 
            LEFT JOIN `bank` AS `b` 
              ON b.id = 16 
            LEFT JOIN `market` AS `m` 
              ON m.sn = ch.sale_order 
          WHERE 1 = 1  
            AND ch.sale_order <> '0' 
            and pt.status=1
            and ch.status=1
            and `pt`.`total_amount` >0
            AND ch.sale_order IN 
            (SELECT 
              cast(ck.sn as char(20))
              FROM checkmoney ck 
              inner join market mm on mm.sn=ck.sn
                WHERE 1 = 1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <> '0') 
          GROUP BY pt.payment_id ,`pt`.use_total ";
            
           // echo $sql_checkmoney_payment_surplus_unuse;die;

          /*--------------Tranfer payment_surplus_before_surplus เงินเกิน------------------*/
            $sql_checkmoney_payment_surplus_before=" SELECT 
            `m`.`sn`,
            '11' AS `TYPE`,
            ch.payment_no,
            0 AS lack_of_money,
            0 AS payment_surplus,
            1 AS company_id,
            ROUND((`pt`.use_total), 2) AS pay_money,
            ROUND((`pt`.`use_total`), 2) AS output,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `ch`.`d_id`,
            `ch`.modified_date AS pay_time,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            '' AS `credit_card`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            '' AS `file_pay_slip`,
            '9' AS seq,
            m.pay_text,
            m.`canceled`,
            m.`canceled_remark`,
            SUBSTRING(m.pay_text, - 14, 14) AS sales_order_ref,ch.payment_no AS payment_no_ref ,m.bs_campaign,'' as phone_number_sn
          FROM
            pay_group_tran pt 
            LEFT JOIN `pay_group` AS `ch` ON ch.payment_id = pt.payment_tran_id
            LEFT JOIN `bank` AS `b` ON b.id = 16
            LEFT JOIN `market` AS `m` ON m.sn = ch.sale_order    
          WHERE 1 = 1 
            AND ch.sale_order <> '0' 
            and pt.status=1
            and ch.status=1
            AND ch.sale_order IN 
            (SELECT 
              cast(ck.sn as char(20)) 
              FROM checkmoney ck 
              inner join market mm on mm.sn=ck.sn
                WHERE 1 = 1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <> '0') 
          GROUP BY pt.payment_tran_id ,`pt`.use_total
                    ";

            //echo $sql_checkmoney_payment_surplus_before;die;  
            
            /*--------------Tranfer banktransfer Pay_group Amount------------------*/

            $sql_pay_group_banktransfer=" SELECT `ch`.`sn`,
            '13' AS `TYPE`,
            m.payment_no,
              IFNULL(ch.lack_of_money, 0) AS lack_of_money,
              IFNULL(ch.payment_surplus, 0) AS payment_surplus,
              `ch`.`company_id`,
              bg.`balance` AS `pay_money`,
              `ch`.`pay_money` AS output,
              `ch`.`bank_transaction_code`,             
              `ch`.`content`,
              '' as `note`,
              `bg`.`balance`,
              `ch`.`d_id`,
              `bg`.pay_date as `pay_time`,
              '' AS `creditnote_sn`,
              '' AS `deposit_sn`,
              b.`name` AS bank_name,
              `bg`.`credit_card` AS credit_card,
              `m`.`invoice_number`,
              `m`.`sn_ref`,
              `m`.`payment_type`,
              `ch`.`file_pay_slip`,
              '2' AS seq,
              m.pay_text,
              m.`canceled`,
              m.`canceled_remark`,
              '' AS sales_order_ref,
              '' AS payment_no_ref ,m.bs_campaign,'' as phone_number_sn
            FROM pay_group_bank bg
            INNER JOIN `checkmoney` `ch` ON CAST(ch.`payment_id` AS CHAR) = CAST(bg.`payment_id` AS CHAR) AND bg.`status` = 1 
            INNER JOIN `market` AS `m`     ON m.sn = ch.sn 
            LEFT JOIN `bank` AS `b`     ON b.id = bg.bank 
            WHERE 1 = 1 
              AND ch.sn <> '0' 
              AND ch.type = '2' AND ch.`finance_confirm_date` IS NOT NULL
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY m.payment_no,bg.`balance`,bg.file_pay_slip
            ";        

            //echo $sql_pay_group_banktransfer;die;

            /*--------------Total Use Credit Note Amount------------------*/
            $sql_credit_note_tran=" SELECT 
            ch.sales_order AS sn,
            '5' AS TYPE,m.payment_no,0 as lack_of_money,0 as payment_surplus,
            '1' AS company_id,
            ROUND(ch.use_discount,2) AS pay_money,
            ROUND(ch.use_discount,2) AS output,
            '' AS bank_transaction_code,
            '' content,
            'Use Credit Note' AS note,
            '0' AS balance,
            ch.distributor_id AS d_id,
            ch.update_date AS pay_time,
            CAST(ch.creditnote_sn AS CHAR) AS creditnote_sn,
            ''  AS `deposit_sn`,
            '' AS bank_name,
            '' AS credit_card,
            m.invoice_number,
            m.sn_ref ,
            m.payment_type,
            '' AS file_pay_slip,
            '3' as seq,'' as pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
          FROM
            credit_note_tran AS ch 
            LEFT JOIN market AS m 
              ON m.sn = ch.sales_order 
          WHERE 1=1
            AND ch.sales_order <> '0' 
            AND ch.sales_order IN 
            (SELECT 
              ck.sn
            FROM
              checkmoney ck 
              inner join market mm on mm.sn=ck.sn
            WHERE 1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <> '0')
          GROUP BY ch.id,
            m.sn,ch.creditnote_sn  
            ";

            //echo $sql_credit_note_tran;die;
            /*--------------Total Delivery Fee Amount------------------*/

            $sql_pay_delivery_fee="SELECT 
            `m`.`sn`,
            '6' as `TYPE`,m.payment_no,0 as lack_of_money,0 as payment_surplus,
            '1' AS `company_id`,
             (ROUND(SUM(distinct m.delivery_fee),2)*-1) AS `pay_money`,
             (ROUND(SUM(distinct m.delivery_fee),2)*-1) AS `output`,
            '' AS `bank_transaction_code`,
            '' AS `content`,
            '' AS `note`,
            0 AS `balance`,
            `m`.`d_id`,
            `m`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            '' AS `bank_name`,
            '' AS `credit_card`,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            '' AS file_pay_slip,
            '4' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
          FROM
             `market` AS `m` 
          WHERE 1=1
            AND m.delivery_fee >0
            AND m.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0') 
            GROUP BY `m`.`sn`";

            //echo $sql_pay_delivery_fee;die;
            /*--------------Total Use Deposit Amount------------------*/
            $sql_deposit_tran=" SELECT 
            ch.sales_order AS sn,
            '12' AS TYPE,m.payment_no,0 as lack_of_money,0 as payment_surplus,
            '1' AS company_id,
            ROUND(ch.use_discount,2) AS pay_money,
            ROUND(ch.use_discount,2) AS output,
            '' AS bank_transaction_code,
            '' content,
            'Use Deposit' AS note,
            '0' AS balance,
            ch.distributor_id AS d_id,
            ch.update_date AS pay_time,
            '' AS creditnote_sn,
            ch.deposit_sn COLLATE utf8_unicode_ci AS deposit_sn,
            '' AS bank_name,
            '' AS credit_card,
            m.invoice_number,
            m.sn_ref ,
            m.payment_type,
            '' AS file_pay_slip,
            '3' as seq,'' as pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
          FROM
            deposit_tran AS ch 
            LEFT JOIN market AS m 
              ON m.sn = ch.sales_order 
          WHERE 1=1
            AND ch.sales_order <> '0' 
            AND ch.sales_order IN 
            (SELECT 
              ck.sn
            FROM
              checkmoney ck 
              inner join market mm on mm.sn=ck.sn
            WHERE 1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <> '0')
          GROUP BY ch.id,
            m.sn,ch.deposit_sn  
            ";
            //echo $sql_deposit_tran;die;

            /*--------------Total pay_servicecharge Amount------------------*/

            $sql_checkmoney_pay_servicecharge=" SELECT 
            `ch`.`sn`,
            '8' as `TYPE`,m.payment_no,ifnull(ch.lack_of_money,0)as lack_of_money,ifnull(ch.payment_surplus,0)as payment_surplus,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_servicecharge`),2) as pay_money,
            ROUND((`ch`.`pay_servicecharge`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            `ch`.`credit_card` AS credit_card,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            ch.file_pay_slip, 
            '6' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and ch.pay_servicecharge >0
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`,ch.file_pay_slip 
            ";

            /*--------------Total pay service Amount------------------*/

            $sql_checkmoney_pay_service=" SELECT 
            `ch`.`sn`,
            '9' as `TYPE`,m.payment_no,ifnull(ch.lack_of_money,0)as lack_of_money,ifnull(ch.payment_surplus,0)as payment_surplus,
            `ch`.`company_id`,
            ROUND((`ch`.`pay_service`),2) as pay_money,
            ROUND((`ch`.`pay_service`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            `ch`.`credit_card` AS credit_card,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            ch.file_pay_slip, 
            '7' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and ch.pay_service >0
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              (1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`) 
              GROUP BY ch.file_pay_slip
            ";


            /*--------------Tranfer lack_of_money เงินขาด------------------*/
            $sql_checkmoney_lack_of_money=" SELECT 
            `ch`.`sn`,
            '10' as `TYPE`,m.payment_no,ifnull(ch.lack_of_money,0)as lack_of_money,ifnull(ch.payment_surplus,0)as payment_surplus,
            `ch`.`company_id`,
            ROUND((`ch`.`lack_of_money`),2) as pay_money,
            ROUND((`ch`.`lack_of_money`),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            `ch`.`credit_card` AS credit_card,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` ,
            `ch`.`file_pay_slip`,
            '8' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,SUBSTRING(m.pay_text,-14,14)AS sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            AND IFNULL(ch.lack_of_money, 0) >0
            AND IFNULL(ch.payment_surplus, 0) <=0
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`,ch.file_pay_slip 
            ";


            /*--------------Total pay bank fee transfer Amount------------------*/

            $sql_checkmoney_pay_banktransfer=" SELECT distinct
              '' as `sn`,
              '7' as `TYPE`,
              m.payment_no,
              ifnull(ch.lack_of_money, 0) as lack_of_money,
              ifnull(ch.payment_surplus, 0) as payment_surplus,
              `ch`.`company_id`,
              ROUND((`ch`.`pay_banktransfer`), 2) as pay_money,
              ROUND((`ch`.`pay_banktransfer`), 2) as output,
              `ch`.`bank_transaction_code`,
              `ch`.`content`,
              '' as `note`,
              `ch`.`balance`,
              `ch`.`d_id`,
              `ch`.`pay_time`,
              '' AS `creditnote_sn`,
              '' AS `deposit_sn`,
              '' AS `bank_name`,
              `ch`.`credit_card` AS credit_card,
              '' as `invoice_number`,
              '' as `sn_ref`,
              `m`.`payment_type`,
              '' as file_pay_slip,
              '5' as seq,
              '' as pay_text,
              m.`canceled`,
              m.`canceled_remark`,
              '' as sales_order_ref,
              '' as payment_no_ref ,m.bs_campaign,'' as phone_number_sn
            FROM
              `checkmoney` AS `ch` 
              LEFT JOIN `bank` AS `b` 
                ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` 
                ON m.sn = ch.sn 
            WHERE 1 = 1 
              AND ch.sn <> '0' 
              AND ch.type = '1' 
              and ch.pay_banktransfer > 0 
              AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0') group by payment_no
            ";


           // echo $sql_checkmoney_pay_banktransfer;die;


            /*--------------Tranfer Money Amount------------------*/
            $sql_checkmoney=" SELECT 
            `ch`.`sn`,
            `ch`.`TYPE`,m.payment_no,ifnull(ch.lack_of_money,0)as lack_of_money,ifnull(ch.payment_surplus,0)as payment_surplus,
            `ch`.`company_id`,
            `ch`.`pay_money` AS pay_money,
            `ch`.`pay_money` AS output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS bank_name,
            `ch`.`credit_card` AS credit_card,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type` ,
            `ch`.`file_pay_slip`,
            '2' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
            FROM
              `market` AS `m`
              LEFT JOIN `checkmoney` AS `ch`  ON m.sn = ch.sn 
              LEFT JOIN `bank` AS `b` ON b.id = ch.bank  
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1' and m.pay_group != 1
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`,ch.file_pay_slip 
            ";


            /*--------------Total pay service Amount------------------*/

            $sql_checkmoney_pay_surplus=" SELECT 
            `ch`.`sn`,
            '15' as `TYPE`,m.payment_no,ifnull(ch.lack_of_money,0)as lack_of_money,ifnull(pg.lacksurplus,0)as payment_surplus,
            `ch`.`company_id`,
            ROUND(if((pg.lacksurplus)<0,(pg.lacksurplus)*-1,(pg.lacksurplus)),2) as pay_money,
            ROUND((pg.lacksurplus),2) as output,
            `ch`.`bank_transaction_code`,
            `ch`.`content`,
            `ch`.`note`,
            `ch`.`balance`,
            `ch`.`d_id`,
            `ch`.`pay_time`,
            ''  AS `creditnote_sn`,
            ''  AS `deposit_sn`,
            `b`.`name` AS `bank_name`,
            `ch`.`credit_card` AS credit_card,
            `m`.`invoice_number`,
            `m`.`sn_ref`,
            `m`.`payment_type`,
            ch.file_pay_slip, 
            '7' as seq,m.pay_text,m.`canceled`,m.`canceled_remark`,'' as sales_order_ref,'' as payment_no_ref,m.bs_campaign,'' as phone_number_sn
            FROM `checkmoney` AS `ch` 
            LEFT JOIN `pay_group` AS `pg` ON pg.sale_order = ch.sn
              LEFT JOIN `bank` AS `b` ON b.id = ch.bank 
              LEFT JOIN `market` AS `m` ON m.sn = ch.sn 
            WHERE 1=1
            AND ch.sn <>'0' 
            AND ch.type ='1'
            and pg.lacksurplus < 0 
            AND ch.sn IN (SELECT cast(ck.sn as char(20))
            FROM checkmoney ck 
            inner join market mm on mm.sn=ck.sn
            WHERE  
              (1=1 ".$where_paytime.$where_invoicetime.$where_d_id.$where_sn.$where_invoice."
              AND ck.sn <>'0')
            GROUP BY `ch`.`id`,
              `m`.`sn`) 
              GROUP BY ch.file_pay_slip
            ";



           // echo $sql_checkmoney_pay_surplus;die;
            
            //sql_checkmoney_payment_group_surplus

            $sql_result = "select t1.*,d.finance_group 
            from (".$sql_total_pay_order
          //  .' UNION ALL '.$sql_checkmoney_payment_surplus_this
            .' UNION ALL '.$sql_checkmoney_payment_surplus_unuse
            .' UNION ALL '.$sql_checkmoney_payment_surplus_before
            .' UNION ALL '.$sql_pay_group_banktransfer
            .' UNION ALL '.$sql_credit_note_tran
            .' UNION ALL '.$sql_pay_delivery_fee
            .' UNION ALL '.$sql_deposit_tran
            .' UNION ALL '.$sql_checkmoney_pay_servicecharge
            .' UNION ALL '.$sql_checkmoney_pay_service
            .' UNION ALL '.$sql_checkmoney_lack_of_money
            .' UNION ALL '.$sql_checkmoney_pay_banktransfer
            .' UNION ALL '.$sql_checkmoney
            .' UNION ALL '.$sql_checkmoney_pay_surplus
            .")t1 
            LEFT JOIN `distributor` d ON d.id=t1.d_id 
            where ".$userStorage->id."=".$userStorage->id." 
            and type <> 6".$where_finaceGroup.$where_group_id." 
            order by t1.payment_no,t1.sn,t1.seq,t1.type desc,t1.pay_time";
   
            //print_r($sql_result);die;
         
        }
         //print_r($sql_result);die;
        try {
          //  $result_ch = $db->fetchAll($sql_result);    
            return $result_ch;
        } catch (Exception $e) {
            echo $e.messages;
            exit;
        }
        /*if (isset($params['export']) and $params['export'])
            {
                //Export
                switch ($params['export']) {
                    case 2: //CashCollectionExcel
                        $this->_exportExcelExportCashCollectionExcel($result_ch);
                    break;
                    case 3: //DailyCashInView
                        $this->_exportExcelExportDailyCashInViewExcel($result_ch);
                    break;
                    case 4: //DailyCashInForBankCheck
                        $this->_exportExcelExportDailyCashInForBankCheck($result_ch);
                    break;
                    case 5: //CashCollectionExcelService
                        $this->_exportExcelExportCashCollectionExcel($result_ch);
                    break;
                }
            }*/
        
    }


    

}



