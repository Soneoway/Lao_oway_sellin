<?php
class Application_Model_PackedSim extends Zend_Db_Table_Abstract
{
	protected $_name = 'packed_sim';

	public function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('ps' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ps.distributor_id,e.partner_code,e.status,dis.store_code,dis.title as distributor_name,dis.finance_group,e.partner_name,ps.confirm_rebate_date,count(ps.imei_sn) as total_imei,count(ps.imei_sn)* '.$params['price_per_imei'].' as total_amount ')));
        $select->joinleft(array('e'=>'external_distributor_code'),'e.oppo_distributor_id=ps.distributor_id',array(null));    
        $select->joinleft(array('dis'=>'distributor'),'ps.distributor_id=dis.id',array(null));
        $select->joinleft(array('df'=>'distributor_info'),'ps.distributor_id=df.distributor_id',array(null));
        $select->joinleft(array('im'=>'imei'),'im.imei_sn=ps.imei_sn ',array(null));
	    $select->where('e.status = 1', null);

        if (isset($params['d_id']) && $params['d_id']) {
        	$select->where('ps.distributor_id = ?', $params['d_id']);
        }

        if (isset($params['partner_code']) && $params['partner_code']) {
        	$select->where('e.partner_code = ?', $params['partner_code']);
        }

        if (isset($params['phone_no']) && $params['phone_no']) {
            $select->where('ps.tel_no = ?', $params['phone_no']);
        }
                
        if (isset($params['imei_sn']) && $params['imei_sn']) {
            $imei_sn="";
            foreach($params['imei_sn'] as $res)
            {
                $imei_sn.=$res.",";
            }
        	$select->where("ps.imei_sn in(".rtrim($imei_sn, ',').")", null);
        }

        if (isset($params['good_id']) && $params['good_id'] !="all") {
            $select->where('im.good_id = ?', $params['good_id']);
        }

        /*if (isset($params['view_status']) && $params['view_status']==1) {
            $select->where('ps.activated_at is null', null);
        }else if (isset($params['view_status']) && $params['view_status']==2) {
            $select->where('ps.activated_at is not null', null);
        }*/

        if (isset($params['view_status']) && $params['view_status']==1) {
            $select->where('ps.sim_activated_at is null', null);
        }else if (isset($params['view_status']) && $params['view_status']==2) { // Wait Confirm Rebate 
            $select->where('ps.sim_activated_at is not null', null);
            $select->where('ps.sellout_at is not null', null);
            //$select->where('ps.activated_at is not null', null);
            $select->where('ps.confirm_rebate_date is null', null);
        }else if (isset($params['view_status']) && $params['view_status']==3) { // Confirm Rebate 
            $select->where('ps.sim_activated_at is not null', null);
            $select->where('ps.sellout_at is not null', null);
            //$select->where('ps.activated_at is not null', null);
            $select->where('ps.confirm_rebate_date is not null', null);
        }

        $select->group('ps.distributor_id','e.partner_name');
        //$select->order('ps.activated_at asc');
        $select->order('ps.distributor_id','ps.activated_at asc');

        if (isset($params['sort']) and $params['sort']) {
            $order_str = $collate = '';

            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';

            $order_str .= $params['sort'] . $collate . $desc;
            

            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

        //echo $select;die;

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    public function getDataExportOpenMarketCampaign($params)
    {
        try{
             	$db = Zend_Registry::get('db');

		        $select = $db->select()
		            ->from(array('ps' => $this->_name),
		                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ps.distributor_id,e.partner_code,e.status,dis.store_code,dis.title AS distributor_name
                            ,dis.rank
                            ,CASE dis.rank 
                            WHEN 1 THEN "ORG-WDS(1)" 
                            WHEN 2 THEN "ORG(2)"
                            WHEN 5 THEN "ORG-Dtac/Advice(5)"
                            WHEN 6 THEN "ORG-Lotus/Power by(6)" 
                            WHEN 7 THEN "Dealer(7)"
                            WHEN 8 THEN "HUB(8)"
                            WHEN 9 THEN "Laos(9)" 
                            WHEN 3 THEN "Online and Staff(3)" 
                            WHEN 10 THEN "Brand Shop/Service(10)" 
                            WHEN 11 THEN "King Power(11)"
                            WHEN 12 THEN "Jaymart(12)"
                            WHEN 13 THEN "Brand Shop By Dealer(13)" 
                            WHEN 14 THEN "KR Dealer(14)"
                            WHEN 15 THEN "TRUE(15)"
                            ELSE "" END AS distributor_type
                            ,g.id AS good_id,g.name AS good_name,im.good_color,gc.name AS goodcolor_name
                            ,dis.finance_group,e.partner_name,ps.imei_sn,ps.simcard,ps.tel_no,ps.operator,ps.confirm_rebate_date,ps.sellin_datetime,ps.sellout_at,ps.sim_activated_at,ps.activated_at,ps.created_at,'.$params['price_per_imei'].' as price_per_imei,e.no_pay_rebate')));
		        $select->joinleft(array('e'=>'external_distributor_code'),'e.oppo_distributor_id=ps.distributor_id',array(null));    
		        $select->joinleft(array('dis'=>'distributor'),'ps.distributor_id=dis.id',array(null));
		        $select->joinleft(array('df'=>'distributor_info'),'ps.distributor_id=df.distributor_id',array(null));
                $select->joinleft(array('im'=>'imei'),'im.imei_sn=ps.imei_sn ',array(null));
                $select->joinleft(array('g'=>'good'),'im.good_id=g.id',array(null));
                $select->joinleft(array('gc'=>'good_color'),'gc.id=im.good_color',array(null));

			    $select->where('e.status = 1', null);

                if (isset($params['good_id']) && $params['good_id'] !="all") {
                    $select->where('im.good_id = ?', $params['good_id']);
                }

		        if (isset($params['d_id']) && $params['d_id']) {
		        	$select->where('ps.distributor_id = ?', $params['d_id']);
		        }

                if (isset($params['phone_no']) && $params['phone_no']) {
                    $select->where('ps.tel_no = ?', $params['phone_no']);
                }

		        if (isset($params['partner_code']) && $params['partner_code']) {
		        	$select->where('e.partner_code = ?', $params['partner_code']);
		        }

		        /*if (isset($params['imei_sn']) && $params['imei_sn']) {
		        	$select->where('ps.imei_sn = ?', $params['imei_sn']);
		        }*/

                if (isset($params['imei_sn']) && $params['imei_sn']) {
                    $imei_sn="";
                    foreach($params['imei_sn'] as $res)
                    {
                        $imei_sn.=$res.",";
                    }
                    $select->where("ps.imei_sn in(".rtrim($imei_sn, ',').")", null);
                }

		        /*if (isset($params['view_status']) && $params['view_status']==1) {
		            $select->where('ps.activated_at is null', null);
		        }else if (isset($params['view_status']) && $params['view_status']==2) {
		            $select->where('ps.activated_at is not null', null);
		        }*/

                if (isset($params['view_status']) && $params['view_status']==1) {
                    $select->where('ps.sim_activated_at is null', null);
                }else if (isset($params['view_status']) && $params['view_status']==2) { // Activate Wait Confirm Rebate
                    $select->where('ps.sim_activated_at is not null', null);
                    $select->where('ps.sellout_at is not null', null);
                    //$select->where('ps.activated_at is not null', null);
                    $select->where('ps.confirm_rebate_date is null', null);
                }else if (isset($params['view_status']) && $params['view_status']==3) { // Activate And Confirm Rebate
                    $select->where('ps.sim_activated_at is not null', null);
                    $select->where('ps.sellout_at is not null', null);
                    //$select->where('ps.activated_at is not null', null);
                    $select->where('ps.confirm_rebate_date is not null', null);
                }

		        $select->order('ps.distributor_id','ps.activated_at asc');
            // echo $select;die;   
            $result = $db->fetchAll($select);
            $total = $db->fetchOne("select FOUND_ROWS()");
            
            
            return $result;
        } catch (Exception $e){

                    return array(
                        'code' => -1,
                        'message' => $e->getMessage(),
                    );
        }
    }

}                                                      
