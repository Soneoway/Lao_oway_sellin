<?php
class Application_Model_DailyStockBalance extends Zend_Db_Table_Abstract
{
    protected $_name = 'DailyStockBalance';

    function compareDate($date1,$date2) {
        $arrDate1 = explode("-",$date1);
        $arrDate2 = explode("-",$date2);
        $timStmp1 = mktime(0,0,0,$arrDate1[1],$arrDate1[2],$arrDate1[0]);
        $timStmp2 = mktime(0,0,0,$arrDate2[1],$arrDate2[2],$arrDate2[0]);

        if ($timStmp1 <= $timStmp2) {
            return true;
        } else{
            return false;
        }
    }
    

    function fetchPagination($page, $limit, &$total, $params){

        //print_r($params);
        $db = Zend_Registry::get('db');
        $doc_created_at_from="";$doc_created_at_to="";

        if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
            if (isset($day) and isset($month) and isset($year) ){
                $doc_created_at_from = $year.'-'.$month.'-'.$day.' 00:00:00';
            }
        }

        if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

            if (isset($day) and isset($month) and isset($year) ){
                $doc_created_at_to = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
        }
        if($params['good_id'] !="all"){
            $select_good_name=" AND good_code in((SELECT `name` FROM good WHERE id='".$params['good_id']."')) ";
        }

        if($params['good_color'] !="all"){
            $select_good_color=" AND good_color in((SELECT `name` FROM good_color WHERE id='".$params['good_color']."')) ";
        }

        //Cal Befor
    
        if($this->compareDate("2016-12-31",$doc_created_at_from)==true)
        {
            $Beginning =" UNION ALL (SELECT 'Beginning' AS doc_type ,'Beginning' AS doc_no_day,(DATE_ADD(SUBSTRING('".$doc_created_at_from."', 1, 10),INTERVAL -1 DAY)) AS doc_date,t.good_code,t.good_color,SUM(IFNULL(t.qty_in_purchase,0))AS qty_in_purchase,SUM(IFNULL(t.qty_in_cn,0))AS qty_in_cn,SUM(IFNULL(t.qty_in_do,0))AS qty_in_do,SUM(IFNULL(t.qty_in_ro,0))AS qty_in_ro,SUM(IFNULL(t.qty_in_re,0))AS qty_in_re,SUM(IFNULL(t.out_product,0))AS out_product
            FROM (
                (SELECT 'in_purchase' AS doc_type,sp.`doc_no`,sp.`doc_date`,sp.`good_code`,sp.`good_color`,SUM(IFNULL(sp.`qty`,0))AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
                FROM `stock_product_in_purchase` sp
                WHERE sp.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY sp.`doc_date`,sp.`good_code`,sp.`good_color`) 
            UNION ALL (SELECT 'in_cn' AS doc_type,cn.`doc_no`,cn.`doc_date`,cn.`good_code`,cn.`good_color`, 0 AS qty_in_purchase,SUM(IFNULL(cn.`qty`,0))AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
                FROM `stock_product_in_cn` cn
                WHERE cn.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY cn.`doc_date`,cn.`good_code`,cn.`good_color`)

            UNION ALL (SELECT 'in_do' AS doc_type,do.`doc_no`,do.`doc_date`,do.`good_code`,do.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,SUM(IFNULL(do.`qty`,0)) AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
                FROM `stock_product_in_do` do
                WHERE do.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY do.`doc_date`,do.`good_code`,do.`good_color`)
            
            UNION ALL (SELECT 'in_ro' AS doc_type,ro.`doc_no`,ro.`doc_date`,ro.`good_code`,ro.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,SUM(IFNULL(ro.`qty`,0)) AS qty_in_ro,0 AS qty_in_re,0 AS out_product
                FROM `stock_product_in_ro` ro
                WHERE ro.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY ro.`doc_date`,ro.`good_code`,ro.`good_color`)

            UNION ALL (SELECT 'in_re' AS doc_type,re.`doc_no`,re.`doc_date`,re.`good_code`,re.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,SUM(IFNULL(re.`qty`,0)) AS qty_in_re,0 AS out_product
                FROM `stock_product_in_re` re
                WHERE re.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY re.`doc_date`,re.`good_code`,re.`good_color`)

            UNION ALL (SELECT 'out_product' AS doc_type,so.`doc_no`,so.`doc_date`,so.`good_code`,so.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,SUM(IFNULL(so.`qty`,0))AS out_product
                FROM `stock_product_out` so
                WHERE so.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY so.`doc_date`,so.`good_code`,so.`good_color`) 
            )t GROUP BY DATE_FORMAT(t.doc_date, '%Y-%m'),t.`good_code`,t.`good_color`
            ORDER BY t.doc_date
            ) ";
        }


        // Select By Screen
        $select="

        SELECT SUBSTRING('".$doc_created_at_from."', 1, 10) as stock_date
        ,t1.good_code,t1.good_color,CONCAT(t1.good_code,' สี ',t1.good_color) AS product_detail
        ,SUM(t1.qty_in_purchase) AS qty_in_purchase,SUM(t1.qty_in_cn)AS qty_in_cn,SUM(t1.qty_in_do)AS qty_in_do,SUM(t1.qty_in_ro)AS qty_in_ro,SUM(t1.qty_in_re)AS qty_in_re,SUM(t1.out_product)AS out_product
        ,IFNULL(pc.`cost_price`,0)as cost_price,((SUM(t1.qty_in_purchase)+SUM(t1.qty_in_cn)+SUM(t1.qty_in_do)+SUM(t1.qty_in_ro)-SUM(t1.out_product))+(IF(SUM(t1.qty_in_re)>0,SUM(t1.qty_in_re),(SUM(t1.qty_in_ro)*-1))))AS total_qty,((SUM(t1.qty_in_purchase)+SUM(t1.qty_in_cn)+SUM(t1.qty_in_do)+SUM(t1.qty_in_ro)-SUM(t1.out_product)+(IF(SUM(t1.qty_in_re)>0,SUM(t1.qty_in_re),(SUM(t1.qty_in_ro)*-1))))*IFNULL(pc.`cost_price`,0))AS total_cost
        FROM (
            (SELECT 'in_purchase' AS doc_type,sp.`doc_no`,sp.`doc_date`,sp.`good_code`,sp.`good_color`,SUM(IFNULL(sp.`qty`,0)) AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
            FROM `stock_product_in_purchase` sp
            WHERE sp.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY sp.`doc_date`,sp.`good_code`,sp.`good_color`) 
        UNION ALL (SELECT 'in_cn' AS doc_type,cn.`doc_no`,cn.`doc_date`,cn.`good_code`,cn.`good_color`, 0 AS qty_in_purchase,SUM(IFNULL(cn.`qty`,0)) AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
            FROM `stock_product_in_cn` cn 
            WHERE cn.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY cn.`doc_date`,cn.`good_code`,cn.`good_color`)

        UNION ALL (SELECT 'in_do' AS doc_type,do.`doc_no`,do.`doc_date`,do.`good_code`,do.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,SUM(IFNULL(do.`qty`,0)) AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,0 AS out_product
            FROM `stock_product_in_do` do
            WHERE do.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY do.`doc_date`,do.`good_code`,do.`good_color`)
        
        UNION ALL (SELECT 'in_ro' AS doc_type,ro.`doc_no`,ro.`doc_date`,ro.`good_code`,ro.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,SUM(IFNULL(ro.`qty`,0)) AS qty_in_ro,0 AS qty_in_re,0 AS out_product
            FROM `stock_product_in_ro` ro
            WHERE ro.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY ro.`doc_date`,ro.`good_code`,ro.`good_color`)

        UNION ALL (SELECT 'in_re' AS doc_type,re.`doc_no`,re.`doc_date`,re.`good_code`,re.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,SUM(IFNULL(re.`qty`,0)) AS qty_in_re,0 AS out_product
            FROM `stock_product_in_re` re
            WHERE re.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY re.`doc_date`,re.`good_code`,re.`good_color`)

        UNION ALL (SELECT 'out_product' AS doc_type,so.`doc_no`,so.`doc_date`,so.`good_code`,so.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,0 AS qty_in_do,0 AS qty_in_ro,0 AS qty_in_re,SUM(IFNULL(so.`qty`,0))AS out_product
            FROM `stock_product_out` so
            WHERE so.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY so.`doc_date`,so.`good_code`,so.`good_color`) ".$Beginning." 
        )t1 
        LEFT JOIN stock_product_cost pc ON pc.`good_code`=t1.`good_code` AND pc.`good_color`=t1.`good_color` AND pc.`status`=1 AND pc.`cost_date`=CONCAT(DATE_FORMAT(t1.doc_date, '%Y-%m'),'-01') 
        GROUP BY DATE_FORMAT(t1.doc_date, '%Y-%m'),t1.good_code,t1.good_color
        ORDER BY t1.good_code,t1.good_color";

        //------------------------------------------
        //echo $select;die;

        $result = $db->fetchAll($select);
        //print_r($result);
        return $result;
    }

    /*function fetchPagination($page, $limit, &$total, $params){

        //print_r($params);
        $db = Zend_Registry::get('db');
        $doc_created_at_from="";$doc_created_at_to="";

        if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
            list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
            if (isset($day) and isset($month) and isset($year) ){
                $doc_created_at_from = $year.'-'.$month.'-'.$day.' 00:00:00';
            }
        }

        if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
            list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

            if (isset($day) and isset($month) and isset($year) ){
                $doc_created_at_to = $year.'-'.$month.'-'.$day.' 23:59:59';
            }
        }
        if($params['good_id'] !="all"){
            $select_good_name=" AND good_code in((SELECT `name` FROM good WHERE id='".$params['good_id']."')) ";
        }

        if($params['good_color'] !="all"){
            $select_good_color=" AND good_color in((SELECT `name` FROM good_color WHERE id='".$params['good_color']."')) ";
        }

        //Cal Befor
    
        if($this->compareDate("2016-12-31",$doc_created_at_from)==true)
        {
            $Beginning =" UNION ALL (SELECT 'Beginning' AS doc_type ,'Beginning' AS doc_no_day,(DATE_ADD(SUBSTRING('".$doc_created_at_from."', 1, 10),INTERVAL -1 DAY)) AS doc_date,t.good_code,t.good_color,SUM(t.qty_in_purchase)AS qty_in_purchase,SUM(t.qty_in_cn)AS qty_in_cn,SUM(t.out_product)AS out_product
            FROM (
                (SELECT 'in_purchase' AS doc_type,sp.`doc_no`,sp.`doc_date`,sp.`good_code`,sp.`good_color`,SUM(sp.`qty`)AS qty_in_purchase,0 AS qty_in_cn,0 AS out_product
                FROM `stock_product_in_purchase` sp
                WHERE sp.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY sp.`doc_date`,sp.`good_code`,sp.`good_color`) 
            UNION ALL (SELECT 'in_cn' AS doc_type,cn.`doc_no`,cn.`doc_date`,cn.`good_code`,cn.`good_color`, 0 AS qty_in_purchase,SUM(cn.`qty`)AS qty_in_cn,0 AS out_product
                FROM `stock_product_in_cn` cn
                WHERE cn.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY cn.`doc_date`,cn.`good_code`,cn.`good_color`)
            UNION ALL (SELECT 'out_product' AS doc_type,so.`doc_no`,so.`doc_date`,so.`good_code`,so.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,SUM(so.`qty`)AS out_product
                FROM `stock_product_out` so
                WHERE so.`doc_date` BETWEEN '2016-01-10 00:00:00' AND (DATE_ADD(CONCAT(SUBSTRING('".$doc_created_at_from."', 1, 10),' 23:59:59'),INTERVAL -1 DAY)) ".$select_good_name." ".$select_good_color."  and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
                GROUP BY so.`doc_date`,so.`good_code`,so.`good_color`) 
            )t
            ORDER BY t.doc_date
            ) ";
        }


        // Select By Screen
        $select="

        SELECT DATE_FORMAT(t1.doc_date, '%Y-%m')AS yearmonth
        ,t1.good_code,t1.good_color,CONCAT(t1.good_code,' สี ',t1.good_color) AS product_detail
        ,SUM(t1.qty_in_purchase) AS qty_in_purchase,SUM(t1.qty_in_cn)AS qty_in_cn,SUM(t1.out_product)AS out_product
        ,IFNULL(pc.`cost_price`,0)as cost_price,((SUM(t1.qty_in_purchase)+SUM(t1.qty_in_cn)-SUM(t1.out_product)))AS total_qty,((SUM(t1.qty_in_purchase)+SUM(t1.qty_in_cn)-SUM(t1.out_product))*IFNULL(pc.`cost_price`,0))AS total_cost
        FROM (
            (SELECT 'in_purchase' AS doc_type,sp.`doc_no`,sp.`doc_date`,sp.`good_code`,sp.`good_color`,IFNULL(SUM(sp.`qty`),0) AS qty_in_purchase,0 AS qty_in_cn,0 AS out_product
            FROM `stock_product_in_purchase` sp
            WHERE sp.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY sp.`doc_date`,sp.`good_code`,sp.`good_color`) 
        UNION ALL (SELECT 'in_cn' AS doc_type,cn.`doc_no`,cn.`doc_date`,cn.`good_code`,cn.`good_color`, 0 AS qty_in_purchase,IFNULL(SUM(cn.`qty`),0) AS qty_in_cn,0 AS out_product
            FROM `stock_product_in_cn` cn
            WHERE cn.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY cn.`doc_date`,cn.`good_code`,cn.`good_color`)
        UNION ALL (SELECT 'out_product' AS doc_type,so.`doc_no`,so.`doc_date`,so.`good_code`,so.`good_color`, 0 AS qty_in_purchase,0 AS qty_in_cn,IFNULL(SUM(so.`qty`),0)AS out_product
            FROM `stock_product_out` so
            WHERE so.`doc_date` BETWEEN '".$doc_created_at_from."' AND '".$doc_created_at_to."' ".$select_good_name." ".$select_good_color." and good_code in((SELECT ip.good_code FROM stock_product_in_purchase ip GROUP BY ip.good_code))
            GROUP BY so.`doc_date`,so.`good_code`,so.`good_color`) ".$Beginning." 
        )t1 
        LEFT JOIN stock_product_cost pc ON pc.`good_code`=t1.`good_code` AND pc.`good_color`=t1.`good_color` AND pc.`status`=1 AND pc.`cost_date`=CONCAT(DATE_FORMAT(t1.doc_date, '%Y-%m'),'-01') 
        GROUP BY DATE_FORMAT(t1.doc_date, '%Y-%m'),t1.good_code,t1.good_color
        ORDER BY DATE_FORMAT(t1.doc_date, '%Y-%m'),t1.good_code,t1.good_color";

        //------------------------------------------
        //echo $select;die;

        $result = $db->fetchAll($select);
        //print_r($result);
        return $result;
    }*/


}
