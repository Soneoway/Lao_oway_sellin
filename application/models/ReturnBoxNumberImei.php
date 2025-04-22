<?php
class Application_Model_ReturnBoxNumberImei extends Zend_Db_Table_Abstract{
    protected $_name = 'return_box_imei';
    
    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        //print_r($params);die;
        $result_check_return=null;
        try{
            $select_return_by_imei ="SELECT br.box_sn,br.box_number,br.sender_name
            ,br.total_imei,br.box_post_number,br.distributor_name
            ,DATE_FORMAT(br.receive_date, '%Y-%m-%d')as receive_date,bi.create_date
            ,br.remark as box_remark,bi.action_imei
            ,bi.imei_sn as imei_scan,i.imei_sn as imei_return,i.type as imei_type
            ,CASE i.type 
            WHEN 1 THEN 'Normal'
            WHEN 2 THEN 'Demo'
            WHEN 5 THEN 'APK'
            END as imei_type_name
            ,dis.id AS distributor_id,
            dis.store_code,
            dis.title, 
            11 as cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color,w.name AS warehouse_name,bi.finance_confirm_by
            ,bi.damage_detail,bi.rtn_number,bi.remark,bi.create_cn,bi.active_cn,bi.warehouse_id,bi.shape_status,dis.finance_group,bi.creditnote_sn,bi.creditnote_date
            ,bi.finance_confirm_date,bi.finance_confirm_by,bi.staff_confirm_date,bi.staff_confirm_by
            ,CASE bi.create_cn
            WHEN 1 THEN 'Y'
            else '-'
            END as create_cn_name
            ,CASE bi.active_cn
            WHEN 1 THEN 'Y'
            else '-'
            END as active_name
            ,concat(ss.firstname,' ',ss.lastname) as staff_confirm_by_name
            ,concat(sf.firstname,' ',sf.lastname) as finance_confirm_by_name
            ,m.invoice_number,bi.total_amount
            ,bi.return_type,CASE bi.return_type
            WHEN 1 THEN 'เครื่องเสีย DOA/DAP' 
            WHEN 3 THEN 'Demo' 
            WHEN 4 THEN 'กรณีพิเศษ/อื่นๆ' 
            WHEN 5 THEN 'EOL' 
            END as return_type_name,bi.cn_to_d_id
            FROM return_box_number br
            left join return_box_imei bi on br.box_sn=bi.box_sn
            left join imei i on i.imei_sn=bi.imei_sn
            LEFT JOIN market AS m ON m.sn = bi.sales_sn AND m.good_id = bi.good_id AND m.good_color = bi.good_color 
            LEFT JOIN good AS g ON g.id = bi.good_id
            LEFT JOIN good_color AS c  ON c.id = bi.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = bi.distributor_id 
            LEFT JOIN warehouse w on w.id = bi.warehouse_id
            LEFT JOIN staff AS ss  ON ss.id = bi.staff_confirm_by 
            LEFT JOIN staff AS sf  ON sf.id = bi.finance_confirm_by 
            where 1=1 ";

            if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='list')
            {
                if (isset($params['box_sn']) and $params['box_sn'] and $params['box_sn'] !=''){
                    $select_return_by_imei .=" and br.box_sn='".$params['box_sn']."'";
                }
                if (isset($params['box_number']) and $params['box_number'] and $params['box_number'] !=''){
                    $select_return_by_imei .=" and br.box_number='".$params['box_number']."'";
                }
                $select_return_by_imei .=" and br.active=1 and bi.staff_confirm_date is null";
            }else if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='export')
            {
                if (isset($params['view_status']) and $params['view_status'] and $params['view_status'] ='1')
                {
                    $select_return_by_imei .=" and bi.staff_confirm_date is not null";
                }
                else if (isset($params['view_status']) and $params['view_status'] and $params['view_status'] ='2')
                {
                    $select_return_by_imei .=" and bi.finance_confirm_date is not null";
                }

                if (isset($params['box_number']) and $params['box_number'] and $params['box_number'] !='')
                {
                    $select_return_by_imei .=" and br.box_number='".$params['box_number']."'";
                }

                if (isset($params['box_sn']) and $params['box_sn'] and $params['box_sn'] !='')
                {
                    $select_return_by_imei .=" and br.box_sn='".$params['box_sn']."'";
                }

                if (isset($params['start_date']) and $params['start_date'] and $params['start_date'] !='')
                {
                    $select_return_by_imei .=" and bi.create_date >='".$params['start_date']."'";
                }

                if (isset($params['end_date']) and $params['end_date'] and $params['end_date'] !='')
                {
                    $select_return_by_imei .=" and bi.create_date <='".$params['end_date']."'";
                }

            }
            
            $select_return_by_imei .=" GROUP BY bi.distributor_id,bi.good_id,bi.good_color,bi.imei_sn
            ORDER BY bi.create_date";

            //print_r($select_return_by_imei);die;
            $result_check_return = $db->fetchAll($select_return_by_imei);
            if ($result_check_return)
            {
                return $result_check_return;
            }

        }catch(exception $e){
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }

    public function getReturnBoxNumberImeiCheckAction($params)
    {

        $db = Zend_Registry::get('db');
        //print_r($params);die;
        $result_check_return=null;
        try{
            $select_return_by_imei ="SELECT br.box_sn,br.box_number,br.sender_name,br.total_imei,br.box_post_number,br.sender_name,br.distributor_name,DATE_FORMAT(br.receive_date, '%Y-%m-%d')as receive_date,br.create_date,br.remark as box_remark,bi.action_imei
            ,bi.imei_sn as imei_scan,im.imei_sn as imei_oppo,i.imei_sn as imei_return,i.type as imei_type
            ,CASE i.type 
            WHEN 1 THEN 'Normal'
            WHEN 2 THEN 'Demo'
            WHEN 5 THEN 'APK'
            END as imei_type_name
            ,dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(i.imei_sn) AS bvg_num,
            m.sn AS sales_sn,
            m.invoice_number,
            SUM(IFNULL( bvg.price ,0)) AS bgv_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bvg.price ,0)),2)AS sum_unit_price, 
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color,w.name AS warehouse_name,bi.finance_confirm_by
            ,bi.return_type,bi.damage_detail,bi.rtn_number,bi.remark,bi.create_cn,bi.active_cn,bi.warehouse_id,bi.shape_status,dis.finance_group,bi.cn_to_d_id
            FROM return_box_number br
            left join return_box_imei bi on br.box_sn=bi.box_sn
            left join imei im on im.imei_sn=bi.imei_sn
            left join imei i on i.imei_sn=bi.imei_sn and i.`sales_sn` is not null
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN bvg_imei bvg ON bvg.imei_sn=i.imei_sn AND bvg.d_id = m.d_id 
            AND bvg.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bvg.good_id = m.good_id 
            AND bvg.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id 
            LEFT JOIN warehouse w on w.id = i.warehouse_id
            where 1=1 ";

            if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='staff_confirm')
            {
                if (isset($params['box_sn']) and $params['box_sn'] and $params['box_sn'] !=''){
                    $select_return_by_imei .=" and br.box_sn='".$params['box_sn']."'";
                }
                $select_return_by_imei .=" and br.active=1 and bi.staff_confirm_date is null";
            }else{
                //and i.distributor_id is not null
                if (isset($params['box_sn']) and $params['box_sn'] and $params['box_sn'] !=''){
                    $select_return_by_imei .=" and bi.status=0 and br.box_sn='".$params['box_sn']."'";
                }

                if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='list')
                {
                    $select_return_by_imei .=" and br.active=1 and br.box_status=1 and bi.active=1 ";
                    $select_return_by_imei .=" and bi.status=0 and bi.staff_confirm_date is null";
                }

                if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='confirm')
                {
                    $select_return_by_imei .=" and br.active=1 and br.box_status=1 and bi.active=1 ";
                    $select_return_by_imei .=" and bi.status=0 and bi.staff_confirm_date is not null";
                    $select_return_by_imei .=" and bi.finance_confirm_date is null";
                }

                if (isset($params['action_frm']) and $params['action_frm'] and $params['action_frm'] =='confirm_cn'){
                    $select_return_by_imei .=" and br.active=1 and br.box_status=1 and bi.active=1 ";
                    $select_return_by_imei .=" and bi.status=0 and bi.staff_confirm_date is not null";
                    $select_return_by_imei .=" and bi.finance_confirm_date is not null";
                    $select_return_by_imei .=" and bi.creditnote_date is null";
                    $select_return_by_imei .=" AND i.distributor_id = bi.distributor_id ";
                    $select_return_by_imei .=" and bi.action_imei =1";
                }
            }

            if (isset($params['start_date']) and $params['start_date'] and $params['start_date'] !='')
            {
                $select_return_by_imei .=" and bi.create_date >='".$params['start_date']."'";
            }

            if (isset($params['end_date']) and $params['end_date'] and $params['end_date'] !='')
            {
                $select_return_by_imei .=" and bi.create_date <='".$params['end_date']."'";
            }
            
            $select_return_by_imei .=" and i.distributor_id is not null";
            $select_return_by_imei .=" GROUP BY i.distributor_id,i.sales_sn,i.good_id,i.good_color,bi.imei_sn
            ORDER BY bi.create_date";

            //print_r($select_return_by_imei);die;
            $result_check_return = $db->fetchAll($select_return_by_imei);
            if ($result_check_return)
            {
                return $result_check_return;
            }

        }catch(exception $e){
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }


    }


    public function getReturnBoxNumberByProductCheckAction($imei_list)
    {
        $db = Zend_Registry::get('db');

        $result_check_return=null;
        try{
            $select_return_by_product ="SELECT t.distributor_id,
            t.store_code,
            t.title,
            COUNT(t.imei_sn) AS num,
            t.bgv_price,
            t.unit_price,
            SUM(t.total_price)AS total_price,
            t.sales_sn AS sn,
            t.invoice_number,
            t.cat_id,       
            t.good_id,
            t.good_color,
            t.product_name,
            t.product_detail_name,
            t.product_color
            FROM(
            SELECT   
            i.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(bi.imei_sn) AS bvg_num,
            m.sn AS sales_sn,
            m.invoice_number,
            SUM(IFNULL( bi.price ,0)) AS bgv_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS unit_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS total_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color
            FROM
            imei AS i 
            LEFT JOIN market AS m ON m.sn = i.sales_sn AND m.good_id = i.good_id AND m.good_color = i.good_color 
            LEFT JOIN bvg_imei bi ON bi.imei_sn=i.imei_sn AND bi.d_id = m.d_id 
            AND bi.invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND bi.good_id = m.good_id 
            AND bi.good_color = m.good_color
            LEFT JOIN good AS g ON g.id = i.good_id
            LEFT JOIN good_color AS c  ON c.id = i.good_color  
            LEFT JOIN distributor AS dis  ON dis.id = i.distributor_id  
            WHERE 1=1 
            AND i.imei_sn IN (".$imei_list.")
            GROUP BY i.distributor_id,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            )t
            GROUP BY t.distributor_id,t.sales_sn,t.good_id,t.good_color,t.bvg_num
            ORDER BY t.distributor_id,t.invoice_number";

            //print_r($select_return_by_product);die;
            $result_check_return = $db->fetchAll($select_return_by_product);
            if ($result_check_return)
            {
                return $result_check_return;
            }

        }catch(exception $e){
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }


    }

}