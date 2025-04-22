<?php
class Application_Model_ServiceStockShopList extends Zend_Db_Table_Abstract{
	protected $_name = 'service_stock_shop_list';

        
    //Report 01 : Inventory / Purchase / Consumption
    function getInventoryPurchaseConsumption(&$total, $params,$limit)
    {
        $w_date="";$w_date_st="";$w_good_code="";$w_good_code_st="";$w_warehouse_name_st="";
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_name="";$good_code="";$start_date="";$end_date="";
        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $warehouse_name=$params['warehouse_name'];
            $w_warehouse_name_st=" AND (st.warehouse_name = '".$warehouse_name."') ";
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $good_code=$params['good_code'];
            $w_good_code=" AND (s.good_code = '".$good_code."') ";
            $w_good_code_st=" AND (st.good_code = '".$good_code."') ";
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                $w_date=" AND (s.data_date >= '".$start_date."') ";
                $w_date_st=" AND (st.data_date >= '".$start_date."') ";
            }
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                $w_date.=" AND (s.data_date <= '".$end_date."') ";
                $w_date_st.=" AND (st.data_date <= '".$end_date."') ";
            }
        }

        //print_r($params);die;
        $select="select st.stock_month,DATE_FORMAT(concat(st.stock_month,'-01'),'%Y-%M') AS stock_month_name
        ,ifnull(si.si_num_total,0)as si_num_total,ifnull(si.si_price_cost_total_usd,0)as si_price_cost_total_usd,ifnull(si.si_price_cost_total_bath,0)as si_price_cost_total_bath
        ,ifnull(so.so_num_total,0)as so_num_total,ifnull(so.so_price_cost_total_usd,0)as so_price_cost_total_usd,ifnull(so.so_price_cost_total_bath,0)as so_price_cost_total_bath
        ,ifnull(soa.soa_num_total,0)as soa_num_total,ifnull(soa.soa_price_cost_total_usd,0)as soa_price_cost_total_usd,ifnull(soa.soa_price_cost_total_bath,0)as soa_price_cost_total_bath
        ,(ifnull(so.so_num_total,0)+ifnull(soa.soa_num_total,0))as so_num_total_all
        ,(ifnull(so.so_price_cost_total_usd,0)+ifnull(soa.soa_price_cost_total_usd,0))as so_price_cost_total_usd_all
        ,(ifnull(so.so_price_cost_total_bath,0)+ifnull(soa.soa_price_cost_total_bath,0))as so_price_cost_total_bath_all
        ,(st.num_total) as inventory_num_total 
        ,(st.price_cost_total_usd) AS inventory_price_cost_usd_total 
        ,(st.price_cost_total_bath) AS inventory_price_cost_bath_total 
        from(
        SELECT DATE_FORMAT(st.data_date,'%Y-%m') AS stock_month
        ,SUM(st.`num`)AS num_total
        ,sum(st.`num`*st.`price_cost_usd`) as price_cost_total_usd
        ,SUM(st.`num`*st.`price_cost_bath`) AS price_cost_total_bath
        FROM service_stock_shop_list st
        INNER JOIN service_product_list p ON st.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date_st.$w_good_code_st.$w_warehouse_name_st."
        GROUP BY DATE_FORMAT(st.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(st.data_date,'%Y-%m')
        )st 
        left join(
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') as stock_month
        ,sum(s.`num`)as si_num_total,sum(s.`num`*s.`price_cost_usd`) AS si_price_cost_total_usd,sum(s.`price_cost_total_bath`)as si_price_cost_total_bath
        FROM service_good_stock_in s
        inner join service_product_list p on s.`good_code`=p.`good_code`
        where 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        order by DATE_FORMAT(s.data_date,'%Y-%m')
        ) si on si.stock_month=st.stock_month
        left join
        (
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') as stock_month
        ,SUM(s.`inside_num`+s.`outside_num`+s.`in_out_side_num`)as so_num_total
        ,SUM((s.inside_num* s.`price_cost_usd`)+(s.`outside_num`*s.price_cost_usd)+(s.`in_out_side_num`*s.price_cost_usd)) AS so_price_cost_total_usd
        ,SUM((s.inside_num* s.`price_cost_bath`)+(s.`outside_num`*s.price_cost_bath)+(s.`in_out_side_num`*s.price_cost_bath)) AS so_price_cost_total_bath
        FROM service_good_stock_out s
        INNER JOIN service_product_list p ON s.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(s.data_date,'%Y-%m')
        )so on si.stock_month=so.stock_month
        left join
        (
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') AS stock_month
        ,SUM(s.`num`)as soa_num_total
        ,SUM(s.`num`*s.`price_cost_usd`) as soa_price_cost_total_usd
        ,SUM(s.`num`*s.`price_cost_bath`) as soa_price_cost_total_bath
        FROM service_good_stock_out_acc s
        INNER JOIN service_product_list p ON s.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(s.data_date,'%Y-%m')
        )soa on si.stock_month=soa.stock_month
        where ifnull(si.si_num_total,0) > 0 or ((ifnull(so.so_num_total,0)+ifnull(soa.soa_num_total,0)) > 0 )
        order by st.stock_month";
        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    //Report 02 : Inventory by Branch
    function getInventoryByBranch(&$total, $params,$limit)
    {
        $w_date="";$w_good_code="";$w_warehouse_name="";
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_name="";$good_code="";$start_date="";$end_date="";
        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $warehouse_name=$params['warehouse_name'];
            $w_warehouse_name=" AND (p.warehouse_name = '".$warehouse_name."') ";
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $good_code=$params['good_code'];
            $w_good_code=" AND (p.good_code = '".$good_code."') ";
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                $w_date=" AND (p.data_date >= '".$start_date."') ";
            }
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                $w_date.=" AND (p.data_date <= '".$end_date."') ";
            }
        }

        //print_r($params);die;
        $select="select t.warehouse_name
        ,sum(t.total_num) as total_num
        ,round(sum(t.total_cost_price),4) as total_cost_price
        ,round(SUM(t.total_retail_price),4) AS total_retail_price
        from (
        SELECT p.good_code,p.warehouse_name
         ,(IFNULL(p.num,0)) AS total_num
         ,(ifnull(p.num,0)* IFNULL(sp.price,0)) as total_cost_price
         ,(IFNULL(p.num,0)* IFNULL(sp.retail_price,0)) AS total_retail_price
        FROM `service_stock_shop_list` AS `p` 
        inner JOIN `service_product_list` AS `sp` ON p.good_code = sp.good_code 
        WHERE (p.status = 1) AND (p.warehouse_name not in ('Thailand Warehouse')) 
        ".$w_date.$w_good_code.$w_warehouse_name."
        )t
        GROUP BY t.`warehouse_name` 
        ORDER BY t.`warehouse_name`";
        $select .=" limit ".$limit;
        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    //Report 03 : Turn Over Stock
    function getTurnOverStock(&$total, $params,$limit)
    {
        $w_date="";$w_date_st="";$w_good_code="";$w_good_code_st="";$w_warehouse_name_st="";
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_name="";$good_code="";$start_date="";$end_date="";
        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $warehouse_name=$params['warehouse_name'];
            $w_warehouse_name_st=" AND (st.warehouse_name = '".$warehouse_name."') ";
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $good_code=$params['good_code'];
            $w_good_code=" AND (s.good_code = '".$good_code."') ";
            $w_good_code_st=" AND (st.good_code = '".$good_code."') ";
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                $w_date=" AND (s.data_date >= '".$start_date."') ";
                $w_date_st=" AND (st.data_date >= '".$start_date."') ";
            }
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                $w_date.=" AND (s.data_date <= '".$end_date."') ";
                $w_date_st.=" AND (st.data_date <= '".$end_date."') ";
            }
        }

        //print_r($params);die;
        $select="select st.stock_month,DATE_FORMAT(concat(st.stock_month,'-01'),'%Y-%M') AS stock_month_name
        ,ifnull(si.si_num_total,0)as si_num_total,ifnull(si.si_price_cost_total_usd,0)as si_price_cost_total_usd,ifnull(si.si_price_cost_total_bath,0)as si_price_cost_total_bath
        ,ifnull(so.so_num_total,0)as so_num_total,ifnull(so.so_price_cost_total_usd,0)as so_price_cost_total_usd,ifnull(so.so_price_cost_total_bath,0)as so_price_cost_total_bath
        ,ifnull(soa.soa_num_total,0)as soa_num_total,ifnull(soa.soa_price_cost_total_usd,0)as soa_price_cost_total_usd,ifnull(soa.soa_price_cost_total_bath,0)as soa_price_cost_total_bath
        ,(ifnull(so.so_num_total,0)+ifnull(soa.soa_num_total,0))as so_num_total_all
        ,(ifnull(so.so_price_cost_total_usd,0)+ifnull(soa.soa_price_cost_total_usd,0))as so_price_cost_total_usd_all
        ,(ifnull(so.so_price_cost_total_bath,0)+ifnull(soa.soa_price_cost_total_bath,0))as so_price_cost_total_bath_all
        ,(st.num_total) as inventory_num_total 
        ,(st.price_cost_total_usd) AS inventory_price_cost_usd_total 
        ,(st.price_cost_total_bath) AS inventory_price_cost_bath_total 
        from(
        SELECT DATE_FORMAT(st.data_date,'%Y-%m') AS stock_month
        ,SUM(st.`num`)AS num_total
        ,sum(st.`num`*st.`price_cost_usd`) as price_cost_total_usd
        ,SUM(st.`num`*st.`price_cost_bath`) AS price_cost_total_bath
        FROM service_stock_shop_list st
        INNER JOIN service_product_list p ON st.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date_st.$w_good_code_st.$w_warehouse_name_st."
        GROUP BY DATE_FORMAT(st.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(st.data_date,'%Y-%m')
        )st 
        left join(
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') as stock_month
        ,sum(s.`num`)as si_num_total,sum(s.`num`*s.`price_cost_usd`) AS si_price_cost_total_usd,sum(s.`price_cost_total_bath`)as si_price_cost_total_bath
        FROM service_good_stock_in s
        inner join service_product_list p on s.`good_code`=p.`good_code`
        where 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        order by DATE_FORMAT(s.data_date,'%Y-%m')
        ) si on si.stock_month=st.stock_month
        left join
        (
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') as stock_month
        ,SUM(s.`inside_num`+s.`outside_num`+s.`in_out_side_num`)as so_num_total
        ,SUM((s.inside_num* s.`price_cost_usd`)+(s.`outside_num`*s.price_cost_usd)+(s.`in_out_side_num`*s.price_cost_usd)) AS so_price_cost_total_usd
        ,SUM((s.inside_num* s.`price_cost_bath`)+(s.`outside_num`*s.price_cost_bath)+(s.`in_out_side_num`*s.price_cost_bath)) AS so_price_cost_total_bath
        FROM service_good_stock_out s
        INNER JOIN service_product_list p ON s.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(s.data_date,'%Y-%m')
        )so on si.stock_month=so.stock_month
        left join
        (
        SELECT DATE_FORMAT(s.data_date,'%Y-%m') AS stock_month
        ,SUM(s.`num`)as soa_num_total
        ,SUM(s.`num`*s.`price_cost_usd`) as soa_price_cost_total_usd
        ,SUM(s.`num`*s.`price_cost_bath`) as soa_price_cost_total_bath
        FROM service_good_stock_out_acc s
        INNER JOIN service_product_list p ON s.`good_code`=p.`good_code`
        WHERE 1=1
        ".$w_date.$w_good_code."
        group by DATE_FORMAT(s.data_date,'%Y-%m')
        ORDER BY DATE_FORMAT(s.data_date,'%Y-%m')
        )soa on si.stock_month=soa.stock_month
        where ifnull(si.si_num_total,0) > 0 or ((ifnull(so.so_num_total,0)+ifnull(soa.soa_num_total,0)) > 0 )
        order by st.stock_month";
        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    //Report 04 : Stock Aging
    function getStockAging(&$total, $params,$limit)
    {
        $w_date="";$w_date_st="";$w_good_code="";$w_good_code_st="";$w_warehouse_name_st="";
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_name="";$good_code="";$start_date="";$end_date="";
        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $warehouse_name=$params['warehouse_name'];
            $w_warehouse_name_st=" AND (st.warehouse_name = '".$warehouse_name."') ";
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $good_code=$params['good_code'];
            $w_good_code=" AND (s.good_code = '".$good_code."') ";
            $w_good_code_st=" AND (st.good_code = '".$good_code."') ";
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                $w_date=" AND (s.data_date >= '".$start_date."') ";
                $w_date_st=" AND (st.data_date >= '".$start_date."') ";
            }
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                $w_date.=" AND (s.data_date <= '".$end_date."') ";
                $w_date_st.=" AND (st.data_date <= '".$end_date."') ";
            }
        }

        //print_r($params);die;
         $select="SELECT t.warehouse_name
                ,m2.total_num_m2,m2.total_cost_price_m2
                ,m6.total_num_m6,m6.total_cost_price_m6
                ,m12.total_num_m12,m12.total_cost_price_m12
                FROM (
                SELECT s.warehouse_name
                FROM service_stock_shop_list AS s
                WHERE (s.status = 1) AND (s.warehouse_name !='Thailand Warehouse') 
                GROUP BY s.warehouse_name
                )t
                LEFT JOIN (SELECT DATE_FORMAT(p.data_date,'%Y-%m') stock_month,p.good_code,p.warehouse_name
                ,SUM(IFNULL(p.num,0)) AS total_num_m2
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.price,0)) AS total_cost_price_m2
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.retail_price,0)) AS total_retail_price_m2
                FROM service_stock_shop_list AS p 
                INNER JOIN service_product_list AS sp ON p.good_code = sp.good_code 
                WHERE (p.status = 1) AND (p.warehouse_name NOT IN ('Thailand Warehouse')) 
                AND MONTH(p.data_date) = MONTH(NOW()- INTERVAL 1 MONTH) AND YEAR(p.data_date) = YEAR(NOW())
                GROUP BY p.warehouse_name 
                ) AS m2 ON t.warehouse_name=m2.warehouse_name
                LEFT JOIN (SELECT DATE_FORMAT(p.data_date,'%Y-%m') stock_month,p.good_code,p.warehouse_name
                ,SUM(IFNULL(p.num,0)) AS total_num_m6
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.price,0)) AS total_cost_price_m6
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.retail_price,0)) AS total_retail_price_m6
                FROM service_stock_shop_list AS p 
                INNER JOIN service_product_list AS sp ON p.good_code = sp.good_code 
                WHERE (p.status = 1) AND (p.warehouse_name NOT IN ('Thailand Warehouse')) 
                AND MONTH(p.data_date) = MONTH(NOW()- INTERVAL 2 MONTH) AND YEAR(p.data_date) = YEAR(NOW())
                GROUP BY p.warehouse_name 
                ) AS m6 ON t.warehouse_name=m6.warehouse_name
                LEFT JOIN (SELECT DATE_FORMAT(p.data_date,'%Y-%m') stock_month,p.good_code,p.warehouse_name
                ,SUM(IFNULL(p.num,0)) AS total_num_m12
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.price,0)) AS total_cost_price_m12
                ,SUM(IFNULL(p.num,0)* IFNULL(sp.retail_price,0)) AS total_retail_price_m12
                FROM service_stock_shop_list AS p 
                INNER JOIN service_product_list AS sp ON p.good_code = sp.good_code 
                WHERE (p.status = 1) AND (p.warehouse_name NOT IN ('Thailand Warehouse')) 
                AND MONTH(p.data_date) = MONTH(NOW()- INTERVAL 3 MONTH) AND YEAR(p.data_date) = YEAR(NOW())
                GROUP BY p.warehouse_name 
                ) AS m12 ON t.warehouse_name=m12.warehouse_name
                GROUP BY t.warehouse_name 
                ORDER BY t.warehouse_name
                ";
        $select .=" limit ".$limit;
        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    //Report 05 : Used spare part by ITEM
    function getUsedSparePartByItem(&$total, $params,$limit)
    {
        $w_date="";$w_date_st="";$w_good_code="";$w_good_code_st="";$w_warehouse_name_st="";
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $warehouse_name="";$good_code="";$start_date="";$end_date="";
        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $warehouse_name=$params['warehouse_name'];
            $w_warehouse_name_st=" AND (st.warehouse_name = '".$warehouse_name."') ";
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $good_code=$params['good_code'];
            $w_good_code=" AND (s.good_code = '".$good_code."') ";
            $w_good_code_st=" AND (st.good_code = '".$good_code."') ";
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
                $w_date=" AND (s.data_date >= '".$start_date."') ";
                $w_date_st=" AND (st.data_date >= '".$start_date."') ";
            }
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
                $w_date.=" AND (s.data_date <= '".$end_date."') ";
                $w_date_st.=" AND (st.data_date <= '".$end_date."') ";
            }
        }

        //print_r($params);die;
         $select="SELECT t.good_code,t.warehouse_name
                ,IFNULL(n.current_stock,0)AS current_stock
                ,IFNULL(m1.stock_m1,0)AS stock_m1
                ,IFNULL(m2.stock_m2,0)AS stock_m2
                ,ROUND((IFNULL(m1.stock_m1,0)+IFNULL(m2.stock_m2,0)/2),2)AS stock_avg
                ,CASE
                    WHEN IFNULL(n.current_stock,0) >= (ROUND((IFNULL(m1.stock_m1,0)+IFNULL(m2.stock_m2,0)/2),2)) THEN 0
                    WHEN IFNULL(n.current_stock,0) < (ROUND((IFNULL(m1.stock_m1,0)+IFNULL(m2.stock_m2,0)/2),2)) THEN CEILING(ROUND((IFNULL(m1.stock_m1,0)+IFNULL(m2.stock_m2,0)/2),2))-IFNULL(n.current_stock,0)
                    ELSE 2
                END AS on_hand
                FROM (
                SELECT s.warehouse_name,s.`good_code`
                FROM service_stock_shop_list AS s
                WHERE (s.status = 1) AND (s.warehouse_name !='Thailand Warehouse') 
                ".$w_good_code.$warehouse_name."
                GROUP BY s.warehouse_name,s.good_code
                )t
                LEFT JOIN (
                SELECT s.warehouse_name,s.`good_code`,SUM(IFNULL(s.`num`,0)) AS current_stock
                FROM service_stock_shop_list AS s
                WHERE (s.status = 1) AND (s.warehouse_name !='Thailand Warehouse') 
                ".$w_good_code.$warehouse_name."
                AND MONTH(s.data_date) = MONTH(NOW()) AND YEAR(s.data_date) = YEAR(NOW())
                GROUP BY s.warehouse_name,s.good_code
                ) AS n ON t.warehouse_name=n.warehouse_name AND t.good_code=n.good_code
                LEFT JOIN (
                SELECT s.warehouse_name,s.`good_code`,SUM(IFNULL(s.`num`,0)) AS stock_m1
                FROM service_stock_shop_list AS s
                WHERE (s.status = 1) AND (s.warehouse_name !='Thailand Warehouse') 
                ".$w_good_code.$warehouse_name."
                AND MONTH(s.data_date) = MONTH(NOW()- INTERVAL 1 MONTH) AND YEAR(s.data_date) = YEAR(NOW())
                GROUP BY s.warehouse_name,s.good_code
                ) AS m1 ON t.warehouse_name=m1.warehouse_name AND t.good_code=m1.good_code
                LEFT JOIN (
                SELECT s.warehouse_name,s.`good_code`,SUM(IFNULL(s.`num`,0)) AS stock_m2
                FROM service_stock_shop_list AS s
                WHERE (s.status = 1) AND (s.warehouse_name !='Thailand Warehouse') 
                ".$w_good_code.$warehouse_name."
                AND MONTH(s.data_date) = MONTH(NOW()- INTERVAL 2 MONTH) AND YEAR(s.data_date) = YEAR(NOW())
                GROUP BY s.warehouse_name,s.good_code
                ) AS m2 ON t.warehouse_name=m2.warehouse_name AND t.good_code=m2.good_code
                WHERE 1=1
                AND t.good_code IN('4900138','4900546')
                ORDER BY t.good_code,t.warehouse_name
                ";
        $select .=" limit ".$limit;
        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function getWarehouse(){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.warehouse_name'));
        $select->group('p.warehouse_name');
        $select->order(['p.warehouse_name']);    
        $result = $db->fetchAll($select);
        return $result;
    }

	function ServiceStockShopListfetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        //print_r($params);die;
        $select = $db->select();
        $select->from(array('p' => $this->_name),array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id,p.warehouse_name,sum(p.num) as total_num'),'p.good_code','p.num','p.create_by','p.create_date','p.status'));
        $select->joinleft(array('sp'=>'service_product_model_list'),'sp.good_code=p.good_code',array("good_model_name"=>"sp.good_model_name","sp.detail"));
        $select->joinleft(array('s'=>'staff'),'p.create_by=s.id',array("staff_name"=>"concat(s.firstname,' ',s.lastname)",'s.email'));

        if (isset($params['data_type']) and $params['data_type']){
            //$select->where('p.data_type = ?', $params['data_type']);
        }

        if (isset($params['warehouse_name']) and $params['warehouse_name']){
            $select->where('p.warehouse_name = ?', $params['warehouse_name']);
        }
        
        if (isset($params['good_code']) and $params['good_code']){
            $select->where('p.good_code = ?', $params['good_code']);
        }

        if (isset($params['start_date']) and $params['start_date']){
            list( $day, $month, $year ) = explode('/', $params['start_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $start_date = $year.'-'.$month.'-'.$day.' 00:00:00';
            }
            //$start_date = $params['start_date'];
            $select->where('p.data_date >= ?', $start_date);
        }

        if (isset($params['end_date']) and $params['end_date']){
            list( $day, $month, $year ) = explode('/', $params['end_date']);
            list( $year,$time ) = explode(' ', $year);

            if (isset($day) and isset($month) and isset($year) ){
                $end_date = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
            //$end_date = $params['end_date'];
            $select->where('p.data_date <= ?', $end_date);
        }

        $select->where('p.status = ?', 1);
        $select->group('p.good_code');

        $select->order(['p.data_date desc','p.good_code']);
        //print_r($params['export']);die;

        if (isset($params['export']) and $params['export']=='1'){
            
        }else{
            $select->limitPage($page, $limit);
        }

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }
}