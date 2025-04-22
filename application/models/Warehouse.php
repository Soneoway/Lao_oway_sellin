<?php
class Application_Model_Warehouse extends Zend_Db_Table_Abstract
{
	protected $_name = 'warehouse';

  function getWarehouses($warehouse_type)
  {

    $db = Zend_Registry::get('db');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    //$warehouse_type = $userStorage->warehouse_type;

    $select = $db->select()
            ->from(array('p' => $this->_name),array('p.*'));

    if($warehouse_type !=""){      
      $select->where('p.warehouse_type in('.rtrim($warehouse_type, ',').')',null);
    }

    if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
      $select->where('p.show_kerry = ?', 1);
    }

    $select_group = $db->select()
        ->from(array('u' => 'warehouse_group_user'),array('u.warehouse_id'))
        ->where('u.user_id=?',$userStorage->id); 
    $result_group = $db->fetchAll($select_group); 
    $warehouse_id = "";  
    if ($result_group){ 
        foreach ($result_group as $to) {
            $warehouse_id .= $to['warehouse_id'].',';
        } 

        $select->where('p.id in('.rtrim($warehouse_id, ',').')',null);
    }
    $select->where('p.status =?',1);
    $select->order('p.position_no DESC');
    $result = $db->fetchALL($select);
    return $result;
    
  }

function getStockCardAccFinance($params){
        $db = Zend_Registry::get('db');
        //print_r($params);
        //die;  
      if (isset($params['from'])){
        $st_date = $params['from'];
      }

      if (isset($params['to'])){
        $en_date = $params['to'];
      }


      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all=="1"){
            $warehouse_id_t1 =" AND w.id in(".$temp_warehouse_id.") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t5 =" AND cso.old_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t6 =" AND csi.new_id in(".$temp_warehouse_id.") ";
            
      }else{
            $temp_warehouse_id = $params['warehouse_id'];
            unset($temp_warehouse_id[$key]);
            $temp_warehouse_id = array_values($temp_warehouse_id);

            $warehouse_id_t1 =" AND w.id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t5 =" AND cso.old_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t6 =" AND csi.new_id in(".implode(",",$temp_warehouse_id).") ";
      }




      if (isset($params['good_id'])){
        if (is_array($params['good_id'])){
          $good_id_t1 =" AND g.id in(".implode(",",$params['good_id']).") ";
          $good_id_t2 =" AND p.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t3 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t4 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t5 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t6 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
        }else{
          $good_id_t1 =" AND g.id in(".$params['good_id'].") ";
          $good_id_t2 =" AND p.good_id in(".$params['good_id'].") ";
          $good_id_t3 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t4 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t5 =" AND csp.good_id in(".$params['good_id'].") ";
          $good_id_t6 =" AND csp.good_id in(".$params['good_id'].") ";
        }
      }

      if (isset($params['color_id'])){
        if (is_array($params['color_id'])){
          $good_color_t1 =" AND gc.id in(".implode(",",$params['color_id']).") ";
          $good_color_t2 =" AND p.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t3 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t4 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t5 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t6 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
        }else{
          $good_color_t1 =" AND gc.id in(".$params['color_id'].") ";
          $good_color_t2 =" AND p.good_color in(".$params['color_id'].") ";
          $good_color_t3 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t4 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t5 =" AND csp.good_color in(".$params['color_id'].") ";
          $good_color_t6 =" AND csp.good_color in(".$params['color_id'].") ";
        }
      }

      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all =="1"){

      $select="
      SELECT 
        t.stock_date,
        t1.good_id,
        t1.good_name,
        t1.good_color,
        t1.color_name,
        t1.std_date,
        IFNULL(t1.std_qty_chk, 0) AS std_qty_chk,
        IFNULL(t1.std_qty, 0) AS std_qty,
        t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM
        (
          SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
        ) t 
        LEFT JOIN 
          (SELECT 
            ds.stock_date AS std_date,
            IFNULL(SUM(ds.imei_storage), 0) AS std_qty_chk,
            IFNULL(
            (SELECT 
              SUM(IFNULL(dss.imei_storage, 0)) AS std_qty 
            FROM
              daily_stock_acc_new dss 
            WHERE dss.good_id = ds.good_id 
              AND dss.good_color_id = ds.good_color_id 
              AND dss.stock_date = DATE_ADD(ds.stock_date, INTERVAL - 1 DAY)),
            0
          ) AS std_qty,
            g.id AS good_id,
            g.name AS good_name,
            gc.id AS good_color,
            gc.name AS color_name
          FROM
            good g 
            LEFT JOIN daily_stock_acc_new ds 
              ON g.id = ds.good_id 
            LEFT JOIN good_color gc 
              ON gc.id = ds.good_color_id 
          WHERE 1 = 1 
      ".$good_id_t1."
      ".$good_color_t1."
            AND (
              ds.stock_date >= '".$st_date." 00:00:00'
            ) 
            AND (
              ds.stock_date <= '".$en_date." 23:59:59'
            ) 
          GROUP BY g.id,
            gc.id,
            ds.stock_date) t1 
          ON t1.std_date = t.stock_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,
            IFNULL(SUM(p.num), 0) AS po_qty,
            p.good_id,
            p.good_color
          FROM
            purchase_order p 
          WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
            AND (
              p.mysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              p.mysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),
            p.good_id,
            p.good_color) t2 
          ON t1.good_id = t2.good_id 
          AND t1.good_color = t2.good_color 
          AND t.stock_date = t2.po_date    
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,
            IFNULL(SUM(m.num), 0) AS ro_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks = 1 
      ".$good_id_t3."
      ".$good_color_t3."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.warehouse_id,
            m.good_id,
            m.good_color) t3 
          ON t1.good_id = t3.good_id 
          AND t1.good_color = t3.good_color 
          AND t.stock_date = t3.ro_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,
            IFNULL(SUM(m.num), 0) AS inv_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks != 1 
            AND m.canceled = 0 
            AND m.status = 1 
      ".$good_id_t4."
      ".$good_color_t4."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.good_id,
            m.good_color) t4 
          ON t1.good_id = t4.good_id 
          AND t1.good_color = t4.good_color 
          AND t.stock_date = t4.inv_date     
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,
            IFNULL(SUM(csp.num), 0) AS cso_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` cso 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = cso.id 
          WHERE 1 = 1 
            AND cso.scanned_in_at IS NOT NULL 
      ".$good_id_t5."
      ".$good_color_t5."
            AND (
              cso.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              cso.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t5 
          ON t1.good_id = t5.good_id 
          AND t1.good_color = t5.good_color 
          AND t.stock_date = t5.cso_date   
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d') AS csi_date,
            IFNULL(SUM(csp.num), 0) AS csi_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` csi 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = csi.id 
          WHERE 1 = 1 
      ".$good_id_t6."
      ".$good_color_t6."
            AND (
              csi.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              csi.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t6 
          ON t1.good_id = t6.good_id 
          AND t1.good_color = t6.good_color 
          AND t.stock_date = t6.csi_date      
      ORDER BY t.stock_date,
        t1.good_id,
        t1.good_color
      ";

        }else{

          $select=" SELECT t.stock_date,
      t1.good_id,
      t1.good_name,
      t1.good_color,
      t1.color_name,
      t1.warehouse_id,
      t1.warehouse_name,
      t1.std_date,
      IFNULL(t1.std_qty, 0) AS std_qty,
      t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM ( 
       SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
      )t
      LEFT JOIN ( 
      SELECT ds.stock_date AS std_date
      ,IFNULL(SUM(ds.imei_storage),0) AS std_qty
      ,g.id AS good_id,g.name AS good_name,gc.id AS good_color,gc.name AS color_name,w.id AS warehouse_id,w.name AS warehouse_name
      FROM  good g
      LEFT JOIN  daily_stock_acc_new ds ON g.id=ds.good_id
      LEFT JOIN good_color gc ON gc.id = ds.good_color_id
      LEFT JOIN warehouse w ON w.id = ds.warehouse_id 
      WHERE 1 = 1
      ".$good_id_t1."
      ".$good_color_t1."
      ".$warehouse_id_t1."
      AND (ds.stock_date >= DATE_ADD('".$st_date." 00:00:00',INTERVAL - 1 DAY)) 
      AND (ds.stock_date <= '".$en_date." 23:59:59')   

      GROUP BY g.id,ds.warehouse_id ,gc.id,ds.stock_date
      )t1 ON t1.std_date = DATE_ADD(t.stock_date, INTERVAL - 1 DAY) 
      LEFT JOIN (SELECT STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,IFNULL(SUM(p.num),0)AS po_qty,p.good_id,p.good_color,p.warehouse_id 
      FROM purchase_order p
      WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
      ".$warehouse_id_t2."
      AND (p.mysql_time >= '".$st_date." 00:00:00') 
      AND (p.mysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),p.warehouse_id,p.good_id,p.good_color
      )t2 
      ON t1.good_id=t2.good_id AND t1.good_color=t2.good_color AND t1.warehouse_id=t2.warehouse_id AND t.stock_date=t2.po_date
      LEFT JOIN (SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,IFNULL(SUM(m.num),0)AS ro_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks =1
      ".$good_id_t3."
      ".$good_color_t3."
      ".$warehouse_id_t3."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t3 
      ON t1.good_id=t3.good_id AND t1.good_color=t3.good_color AND t1.warehouse_id=t3.warehouse_id AND t.stock_date=t3.ro_date
      LEFT JOIN (
      SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,IFNULL(SUM(m.num),0)AS inv_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks !=1
      AND m.canceled =0
      AND m.status=1
      ".$good_id_t4."
      ".$good_color_t4."
      ".$warehouse_id_t4."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t4 
      ON t1.good_id=t4.good_id AND t1.good_color=t4.good_color AND t1.warehouse_id=t4.warehouse_id AND t.stock_date=t4.inv_date
      LEFT JOIN (
      SELECT STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,IFNULL(SUM(csp.num),0)AS cso_qty,csp.good_id,csp.good_color,cso.old_id AS warehouse_id
      FROM `change_sales_order` cso
      LEFT JOIN change_sales_product csp ON csp.changed_id=cso.id
      WHERE 1 = 1
      AND cso.scanned_in_at IS NOT NULL
      ".$good_id_t5."
      ".$good_color_t5."
      ".$warehouse_id_t5."
      AND (cso.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (cso.scanned_in_at <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,cso.old_id
      )t5 
      ON t1.good_id=t5.good_id AND t1.good_color=t5.good_color AND t1.warehouse_id=t5.warehouse_id AND t.stock_date=t5.cso_date
      LEFT JOIN (
      SELECT STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d')AS csi_date,IFNULL(SUM(csp.num),0)AS csi_qty,csp.good_id,csp.good_color,csi.new_id AS warehouse_id
      FROM `change_sales_order` csi
      LEFT JOIN change_sales_product csp ON csp.changed_id=csi.id
      WHERE 1 = 1
      ".$good_id_t6."
      ".$good_color_t6."
      ".$warehouse_id_t6."
      AND (csi.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (csi.scanned_in_at <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,csi.new_id
      )t6 
      ON t1.good_id=t6.good_id AND t1.good_color=t6.good_color AND t1.warehouse_id=t6.warehouse_id AND t.stock_date=t6.csi_date
      ORDER BY t.stock_date,t1.warehouse_id,t1.good_id,t1.good_color
      ";

        }

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
}

function getStockCardDigitalFinance($params){
        $db = Zend_Registry::get('db');
        //print_r($params);
        //die;  
      if (isset($params['from'])){
        $st_date = $params['from'];
      }

      if (isset($params['to'])){
        $en_date = $params['to'];
      }


      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all=="1"){
            $warehouse_id_t1 =" AND w.id in(".$temp_warehouse_id.") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t5 =" AND cso.old_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t6 =" AND csi.new_id in(".$temp_warehouse_id.") ";
            
      }else{
            $temp_warehouse_id = $params['warehouse_id'];
            unset($temp_warehouse_id[$key]);
            $temp_warehouse_id = array_values($temp_warehouse_id);

            $warehouse_id_t1 =" AND w.id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t5 =" AND cso.old_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t6 =" AND csi.new_id in(".implode(",",$temp_warehouse_id).") ";
      }




      if (isset($params['good_id'])){
        if (is_array($params['good_id'])){
          $good_id_t1 =" AND g.id in(".implode(",",$params['good_id']).") ";
          $good_id_t2 =" AND p.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t3 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t4 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t5 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t6 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
        }else{
          $good_id_t1 =" AND g.id in(".$params['good_id'].") ";
          $good_id_t2 =" AND p.good_id in(".$params['good_id'].") ";
          $good_id_t3 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t4 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t5 =" AND csp.good_id in(".$params['good_id'].") ";
          $good_id_t6 =" AND csp.good_id in(".$params['good_id'].") ";
        }
      }

      if (isset($params['color_id'])){
        if (is_array($params['color_id'])){
          $good_color_t1 =" AND gc.id in(".implode(",",$params['color_id']).") ";
          $good_color_t2 =" AND p.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t3 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t4 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t5 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t6 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
        }else{
          $good_color_t1 =" AND gc.id in(".$params['color_id'].") ";
          $good_color_t2 =" AND p.good_color in(".$params['color_id'].") ";
          $good_color_t3 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t4 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t5 =" AND csp.good_color in(".$params['color_id'].") ";
          $good_color_t6 =" AND csp.good_color in(".$params['color_id'].") ";
        }
      }

      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all =="1"){

      $select="
      SELECT 
        t.stock_date,
        t1.good_id,
        t1.good_name,
        t1.good_color,
        t1.color_name,
        t1.std_date,
        IFNULL(t1.std_qty_chk, 0) AS std_qty_chk,
        IFNULL(t1.std_qty, 0) AS std_qty,
        t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM
        (
          SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
        ) t 
        LEFT JOIN 
          (SELECT 
            ds.stock_date AS std_date,
            IFNULL(SUM(ds.imei_storage), 0) AS std_qty_chk,
            IFNULL(
            (SELECT 
              SUM(IFNULL(dss.imei_storage, 0)) AS std_qty 
            FROM
              daily_stock_digital_new dss 
            WHERE dss.good_id = ds.good_id 
              AND dss.good_color_id = ds.good_color_id 
              AND dss.stock_date = DATE_ADD(ds.stock_date, INTERVAL - 1 DAY)),
            0
          ) AS std_qty,
            g.id AS good_id,
            g.name AS good_name,
            gc.id AS good_color,
            gc.name AS color_name
          FROM
            good g 
            LEFT JOIN daily_stock_digital_new ds 
              ON g.id = ds.good_id 
            LEFT JOIN good_color gc 
              ON gc.id = ds.good_color_id 
          WHERE 1 = 1 
      ".$good_id_t1."
      ".$good_color_t1."
            AND (
              ds.stock_date >= '".$st_date." 00:00:00'
            ) 
            AND (
              ds.stock_date <= '".$en_date." 23:59:59'
            ) 
          GROUP BY g.id,
            gc.id,
            ds.stock_date) t1 
          ON t1.std_date = t.stock_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,
            IFNULL(SUM(p.num), 0) AS po_qty,
            p.good_id,
            p.good_color
          FROM
            purchase_order p 
          WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
            AND (
              p.mysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              p.mysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),
            p.good_id,
            p.good_color) t2 
          ON t1.good_id = t2.good_id 
          AND t1.good_color = t2.good_color 
          AND t.stock_date = t2.po_date    
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,
            IFNULL(SUM(m.num), 0) AS ro_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks = 1 
      ".$good_id_t3."
      ".$good_color_t3."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.warehouse_id,
            m.good_id,
            m.good_color) t3 
          ON t1.good_id = t3.good_id 
          AND t1.good_color = t3.good_color 
          AND t.stock_date = t3.ro_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,
            IFNULL(SUM(m.num), 0) AS inv_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks != 1 
            AND m.canceled = 0 
            AND m.status = 1 
      ".$good_id_t4."
      ".$good_color_t4."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.good_id,
            m.good_color) t4 
          ON t1.good_id = t4.good_id 
          AND t1.good_color = t4.good_color 
          AND t.stock_date = t4.inv_date     
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,
            IFNULL(SUM(csp.num), 0) AS cso_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` cso 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = cso.id 
          WHERE 1 = 1 
            AND cso.scanned_in_at IS NOT NULL 
      ".$good_id_t5."
      ".$good_color_t5."
            AND (
              cso.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              cso.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t5 
          ON t1.good_id = t5.good_id 
          AND t1.good_color = t5.good_color 
          AND t.stock_date = t5.cso_date   
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d') AS csi_date,
            IFNULL(SUM(csp.num), 0) AS csi_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` csi 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = csi.id 
          WHERE 1 = 1 
      ".$good_id_t6."
      ".$good_color_t6."
            AND (
              csi.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              csi.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t6 
          ON t1.good_id = t6.good_id 
          AND t1.good_color = t6.good_color 
          AND t.stock_date = t6.csi_date      
      ORDER BY t.stock_date,
        t1.good_id,
        t1.good_color
      ";

        }else{

          $select=" SELECT t.stock_date,
      t1.good_id,
      t1.good_name,
      t1.good_color,
      t1.color_name,
      t1.warehouse_id,
      t1.warehouse_name,
      t1.std_date,
      IFNULL(t1.std_qty, 0) AS std_qty,
      t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM ( 
       SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
      )t
      LEFT JOIN ( 
      SELECT ds.stock_date AS std_date
      ,IFNULL(SUM(ds.imei_storage),0) AS std_qty
      ,g.id AS good_id,g.name AS good_name,gc.id AS good_color,gc.name AS color_name,w.id AS warehouse_id,w.name AS warehouse_name
      FROM  good g
      LEFT JOIN  daily_stock_digital_new ds ON g.id=ds.good_id
      LEFT JOIN good_color gc ON gc.id = ds.good_color_id
      LEFT JOIN warehouse w ON w.id = ds.warehouse_id 
      WHERE 1 = 1
      ".$good_id_t1."
      ".$good_color_t1."
      ".$warehouse_id_t1."
      AND (ds.stock_date >= DATE_ADD('".$st_date." 00:00:00',INTERVAL - 1 DAY)) 
      AND (ds.stock_date <= '".$en_date." 23:59:59')   

      GROUP BY g.id,ds.warehouse_id ,gc.id,ds.stock_date
      )t1 ON t1.std_date = DATE_ADD(t.stock_date, INTERVAL - 1 DAY) 
      LEFT JOIN (SELECT STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,IFNULL(SUM(p.num),0)AS po_qty,p.good_id,p.good_color,p.warehouse_id 
      FROM purchase_order p
      WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
      ".$warehouse_id_t2."
      AND (p.mysql_time >= '".$st_date." 00:00:00') 
      AND (p.mysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),p.warehouse_id,p.good_id,p.good_color
      )t2 
      ON t1.good_id=t2.good_id AND t1.good_color=t2.good_color AND t1.warehouse_id=t2.warehouse_id AND t.stock_date=t2.po_date
      LEFT JOIN (SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,IFNULL(SUM(m.num),0)AS ro_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks =1
      ".$good_id_t3."
      ".$good_color_t3."
      ".$warehouse_id_t3."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t3 
      ON t1.good_id=t3.good_id AND t1.good_color=t3.good_color AND t1.warehouse_id=t3.warehouse_id AND t.stock_date=t3.ro_date
      LEFT JOIN (
      SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,IFNULL(SUM(m.num),0)AS inv_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks !=1
      AND m.canceled =0
      AND m.status=1
      ".$good_id_t4."
      ".$good_color_t4."
      ".$warehouse_id_t4."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t4 
      ON t1.good_id=t4.good_id AND t1.good_color=t4.good_color AND t1.warehouse_id=t4.warehouse_id AND t.stock_date=t4.inv_date
      LEFT JOIN (
      SELECT STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,IFNULL(SUM(csp.num),0)AS cso_qty,csp.good_id,csp.good_color,cso.old_id AS warehouse_id
      FROM `change_sales_order` cso
      LEFT JOIN change_sales_product csp ON csp.changed_id=cso.id
      WHERE 1 = 1
      AND cso.scanned_in_at IS NOT NULL
      ".$good_id_t5."
      ".$good_color_t5."
      ".$warehouse_id_t5."
      AND (cso.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (cso.scanned_in_at <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,cso.old_id
      )t5 
      ON t1.good_id=t5.good_id AND t1.good_color=t5.good_color AND t1.warehouse_id=t5.warehouse_id AND t.stock_date=t5.cso_date
      LEFT JOIN (
      SELECT STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d')AS csi_date,IFNULL(SUM(csp.num),0)AS csi_qty,csp.good_id,csp.good_color,csi.new_id AS warehouse_id
      FROM `change_sales_order` csi
      LEFT JOIN change_sales_product csp ON csp.changed_id=csi.id
      WHERE 1 = 1
      ".$good_id_t6."
      ".$good_color_t6."
      ".$warehouse_id_t6."
      AND (csi.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (csi.scanned_in_at <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,csi.new_id
      )t6 
      ON t1.good_id=t6.good_id AND t1.good_color=t6.good_color AND t1.warehouse_id=t6.warehouse_id AND t.stock_date=t6.csi_date
      ORDER BY t.stock_date,t1.warehouse_id,t1.good_id,t1.good_color
      ";

        }

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
}


function getStockCardFinance($params){
        $db = Zend_Registry::get('db');
        //print_r($params);
        //die;  
      if (isset($params['from'])){
        $st_date = $params['from'];
      }

      if (isset($params['to'])){
        $en_date = $params['to'];
      }


      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all=="1"){
            $warehouse_id_t1 =" AND w.id in(".$temp_warehouse_id.") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t5 =" AND cso.old_id in(".$temp_warehouse_id.") ";
            $warehouse_id_t6 =" AND csi.new_id in(".$temp_warehouse_id.") ";
            
      }else{
            $temp_warehouse_id = $params['warehouse_id'];
            unset($temp_warehouse_id[$key]);
            $temp_warehouse_id = array_values($temp_warehouse_id);

            $warehouse_id_t1 =" AND w.id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t2 =" AND p.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t3 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t4 =" AND m.warehouse_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t5 =" AND cso.old_id in(".implode(",",$temp_warehouse_id).") ";
            $warehouse_id_t6 =" AND csi.new_id in(".implode(",",$temp_warehouse_id).") ";
      }




      if (isset($params['good_id'])){
        if (is_array($params['good_id'])){
          $good_id_t1 =" AND g.id in(".implode(",",$params['good_id']).") ";
          $good_id_t2 =" AND p.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t3 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t4 =" AND m.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t5 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
          $good_id_t6 =" AND csp.good_id in(".implode(",",$params['good_id']).") ";
        }else{
          $good_id_t1 =" AND g.id in(".$params['good_id'].") ";
          $good_id_t2 =" AND p.good_id in(".$params['good_id'].") ";
          $good_id_t3 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t4 =" AND m.good_id in(".$params['good_id'].") ";
          $good_id_t5 =" AND csp.good_id in(".$params['good_id'].") ";
          $good_id_t6 =" AND csp.good_id in(".$params['good_id'].") ";
        }
      }

      if (isset($params['color_id'])){
        if (is_array($params['color_id'])){
          $good_color_t1 =" AND gc.id in(".implode(",",$params['color_id']).") ";
          $good_color_t2 =" AND p.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t3 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t4 =" AND m.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t5 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
          $good_color_t6 =" AND csp.good_color in(".implode(",",$params['color_id']).") ";
        }else{
          $good_color_t1 =" AND gc.id in(".$params['color_id'].") ";
          $good_color_t2 =" AND p.good_color in(".$params['color_id'].") ";
          $good_color_t3 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t4 =" AND m.good_color in(".$params['color_id'].") ";
          $good_color_t5 =" AND csp.good_color in(".$params['color_id'].") ";
          $good_color_t6 =" AND csp.good_color in(".$params['color_id'].") ";
        }
      }

      if (!isset($params['warehouse_id'])){
        $warehouse_all ="1";
      }else {
        $warehouse_all ="0";
      }

      if($warehouse_all =="1"){

      $select="
      SELECT 
        t.stock_date,
        t1.good_id,
        t1.good_name,
        t1.good_color,
        t1.color_name,
        t1.std_date,
        IFNULL(t1.std_qty_chk, 0) AS std_qty_chk,
        IFNULL(t1.std_qty, 0) AS std_qty,
        t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM
        (
          SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
        ) t 
        LEFT JOIN 
          (SELECT 
            ds.stock_date AS std_date,
            IFNULL(SUM(ds.imei_storage), 0) AS std_qty_chk,
            IFNULL(
            (SELECT 
              SUM(IFNULL(dss.imei_storage, 0)) AS std_qty 
            FROM
              daily_stock_new dss 
            WHERE dss.good_id = ds.good_id 
              AND dss.good_color_id = ds.good_color_id 
              AND dss.stock_date = DATE_ADD(ds.stock_date, INTERVAL - 1 DAY)),
            0
          ) AS std_qty,
            g.id AS good_id,
            g.name AS good_name,
            gc.id AS good_color,
            gc.name AS color_name
          FROM
            good g 
            LEFT JOIN daily_stock_new ds 
              ON g.id = ds.good_id 
            LEFT JOIN good_color gc 
              ON gc.id = ds.good_color_id 
          WHERE 1 = 1 
      ".$good_id_t1."
      ".$good_color_t1."
            AND (
              ds.stock_date >= '".$st_date." 00:00:00'
            ) 
            AND (
              ds.stock_date <= '".$en_date." 23:59:59'
            ) 
          GROUP BY g.id,
            gc.id,
            ds.stock_date) t1 
          ON t1.std_date = t.stock_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,
            IFNULL(SUM(p.num), 0) AS po_qty,
            p.good_id,
            p.good_color
          FROM
            purchase_order p 
          WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
            AND (
              p.mysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              p.mysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),
            p.good_id,
            p.good_color) t2 
          ON t1.good_id = t2.good_id 
          AND t1.good_color = t2.good_color 
          AND t.stock_date = t2.po_date    
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,
            IFNULL(SUM(m.num), 0) AS ro_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks = 1 
      ".$good_id_t3."
      ".$good_color_t3."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.warehouse_id,
            m.good_id,
            m.good_color) t3 
          ON t1.good_id = t3.good_id 
          AND t1.good_color = t3.good_color 
          AND t.stock_date = t3.ro_date 
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,
            IFNULL(SUM(m.num), 0) AS inv_qty,
            m.good_id,
            m.good_color,
            m.warehouse_id 
          FROM
            market m 
          WHERE 1 = 1 
            AND m.isbacks != 1 
            AND m.canceled = 0 
            AND m.status = 1 
      ".$good_id_t4."
      ".$good_color_t4."
            AND (
              m.outmysql_time >= '".$st_date." 00:00:00'
            ) 
            AND (
              m.outmysql_time <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),
            m.good_id,
            m.good_color) t4 
          ON t1.good_id = t4.good_id 
          AND t1.good_color = t4.good_color 
          AND t.stock_date = t4.inv_date     
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,
            IFNULL(SUM(csp.num), 0) AS cso_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` cso 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = cso.id 
          WHERE 1 = 1 
            AND cso.scanned_in_at IS NOT NULL 
      ".$good_id_t5."
      ".$good_color_t5."
            AND (
              cso.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              cso.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t5 
          ON t1.good_id = t5.good_id 
          AND t1.good_color = t5.good_color 
          AND t.stock_date = t5.cso_date   
      LEFT JOIN 
          (SELECT 
            STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d') AS csi_date,
            IFNULL(SUM(csp.num), 0) AS csi_qty,
            csp.good_id,
            csp.good_color
          FROM
            `change_sales_order` csi 
            LEFT JOIN change_sales_product csp 
              ON csp.changed_id = csi.id 
          WHERE 1 = 1 
      ".$good_id_t6."
      ".$good_color_t6."
            AND (
              csi.scanned_in_at >= '".$st_date." 00:00:00'
            ) 
            AND (
              csi.scanned_in_at <= '".$en_date." 23:59:59'
            ) 
          GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),
            csp.good_id,
            csp.good_color) t6 
          ON t1.good_id = t6.good_id 
          AND t1.good_color = t6.good_color 
          AND t.stock_date = t6.csi_date      
      ORDER BY t.stock_date,
        t1.good_id,
        t1.good_color
      ";

        }else{

          $select=" SELECT t.stock_date,
      t1.good_id,
      t1.good_name,
      t1.good_color,
      t1.color_name,
      t1.warehouse_id,
      t1.warehouse_name,
      t1.std_date,
      IFNULL(t1.std_qty, 0) AS std_qty,
      t2.po_date,
        IFNULL(t2.po_qty, 0) AS po_qty,
        t3.ro_date,
        IFNULL(t3.ro_qty, 0) AS ro_qty,
        t4.inv_date,
        IFNULL(t4.inv_qty, 0) AS inv_qty,
        t5.cso_date,
        IFNULL(t5.cso_qty, 0) AS cso_qty,
        t6.csi_date,
        IFNULL(t6.csi_qty, 0) AS csi_qty,
        (
          IFNULL(t1.std_qty, 0) + IFNULL(t2.po_qty, 0) + IFNULL(t3.ro_qty, 0) - IFNULL(t4.inv_qty, 0) - IFNULL(t5.cso_qty, 0) + IFNULL(t6.csi_qty, 0)
        ) AS total_qty 
      FROM ( 
       SELECT stock_date 
          FROM 
          (SELECT ADDDATE('1970-01-01',t4*10000 + t3*1000 + t2*100 + t1*10 + t0) stock_date 
          FROM
           (SELECT 0 t0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
           (SELECT 0 t1 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
           (SELECT 0 t2 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
           (SELECT 0 t3 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
           (SELECT 0 t4 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
          WHERE stock_date BETWEEN '".$st_date."' AND '".$en_date."'
      )t
      LEFT JOIN ( 
      SELECT ds.stock_date AS std_date
      ,IFNULL(SUM(ds.imei_storage),0) AS std_qty
      ,g.id AS good_id,g.name AS good_name,gc.id AS good_color,gc.name AS color_name,w.id AS warehouse_id,w.name AS warehouse_name
      FROM  good g
      LEFT JOIN  daily_stock_new ds ON g.id=ds.good_id
      LEFT JOIN good_color gc ON gc.id = ds.good_color_id
      LEFT JOIN warehouse w ON w.id = ds.warehouse_id 
      WHERE 1 = 1
      ".$good_id_t1."
      ".$good_color_t1."
      ".$warehouse_id_t1."
      AND (ds.stock_date >= DATE_ADD('".$st_date." 00:00:00',INTERVAL - 1 DAY)) 
      AND (ds.stock_date <= '".$en_date." 23:59:59')   

      GROUP BY g.id,ds.warehouse_id ,gc.id,ds.stock_date
      )t1 ON t1.std_date = DATE_ADD(t.stock_date, INTERVAL - 1 DAY) 
      LEFT JOIN (SELECT STR_TO_DATE(p.mysql_time, '%Y-%m-%d') AS po_date,IFNULL(SUM(p.num),0)AS po_qty,p.good_id,p.good_color,p.warehouse_id 
      FROM purchase_order p
      WHERE 1 = 1 
      ".$good_id_t2."
      ".$good_color_t2."
      ".$warehouse_id_t2."
      AND (p.mysql_time >= '".$st_date." 00:00:00') 
      AND (p.mysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(p.mysql_time, '%Y-%m-%d'),p.warehouse_id,p.good_id,p.good_color
      )t2 
      ON t1.good_id=t2.good_id AND t1.good_color=t2.good_color AND t1.warehouse_id=t2.warehouse_id AND t.stock_date=t2.po_date
      LEFT JOIN (SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS ro_date,IFNULL(SUM(m.num),0)AS ro_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks =1
      ".$good_id_t3."
      ".$good_color_t3."
      ".$warehouse_id_t3."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t3 
      ON t1.good_id=t3.good_id AND t1.good_color=t3.good_color AND t1.warehouse_id=t3.warehouse_id AND t.stock_date=t3.ro_date
      LEFT JOIN (
      SELECT STR_TO_DATE(m.outmysql_time, '%Y-%m-%d') AS inv_date,IFNULL(SUM(m.num),0)AS inv_qty,m.good_id,m.good_color,m.warehouse_id 
      FROM market m
      WHERE 1 = 1
      AND m.isbacks !=1
      AND m.canceled =0
      AND m.status=1
      ".$good_id_t4."
      ".$good_color_t4."
      ".$warehouse_id_t4."
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(m.outmysql_time, '%Y-%m-%d'),m.warehouse_id,m.good_id,m.good_color
      )t4 
      ON t1.good_id=t4.good_id AND t1.good_color=t4.good_color AND t1.warehouse_id=t4.warehouse_id AND t.stock_date=t4.inv_date
      LEFT JOIN (
      SELECT STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d') AS cso_date,IFNULL(SUM(csp.num),0)AS cso_qty,csp.good_id,csp.good_color,cso.old_id AS warehouse_id
      FROM `change_sales_order` cso
      LEFT JOIN change_sales_product csp ON csp.changed_id=cso.id
      WHERE 1 = 1
      AND cso.scanned_in_at IS NOT NULL
      ".$good_id_t5."
      ".$good_color_t5."
      ".$warehouse_id_t5."
      AND (cso.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (cso.scanned_in_at <= '".$en_date." 23:59:59')


      GROUP BY STR_TO_DATE(cso.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,cso.old_id
      )t5 
      ON t1.good_id=t5.good_id AND t1.good_color=t5.good_color AND t1.warehouse_id=t5.warehouse_id AND t.stock_date=t5.cso_date
      LEFT JOIN (
      SELECT STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d')AS csi_date,IFNULL(SUM(csp.num),0)AS csi_qty,csp.good_id,csp.good_color,csi.new_id AS warehouse_id
      FROM `change_sales_order` csi
      LEFT JOIN change_sales_product csp ON csp.changed_id=csi.id
      WHERE 1 = 1
      ".$good_id_t6."
      ".$good_color_t6."
      ".$warehouse_id_t6."
      AND (csi.scanned_in_at >= '".$st_date." 00:00:00') 
      AND (csi.scanned_in_at <= '".$en_date." 23:59:59')

      GROUP BY STR_TO_DATE(csi.scanned_in_at, '%Y-%m-%d'),csp.good_id,csp.good_color,csi.new_id
      )t6 
      ON t1.good_id=t6.good_id AND t1.good_color=t6.good_color AND t1.warehouse_id=t6.warehouse_id AND t.stock_date=t6.csi_date
      ORDER BY t.stock_date,t1.warehouse_id,t1.good_id,t1.good_color
      ";

        }

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
}
  
  function getStockCardKerry($params){
        $db = Zend_Registry::get('db');
       // print_r($params);
        //die;  
if (isset($params['from'])){
  $st_date = $params['from'];
}

if (isset($params['to'])){
  $en_date = $params['to'];
}

$item_type="IN (1,3)";
$item_type_demo="IN (2)";

        $select="

SELECT w.name AS warehouse_name,c.name AS cat_name,g.name AS product_name,gc.name AS color_name,t1.*
      
FROM (
  SELECT  
'IN' AS action_type
,'normal,demo' as transaction_type
,'STD' AS doc_type,ds.`stock_date` AS action_time,ds.id AS sn,'-' AS sn_ref,
  ds.warehouse_id,
  ds.cat_id,
  ds.good_id,
  ds.`good_color_id` AS good_color,(ds.`available_stock`) AS num_qty,(ds.`available_stock_demo`) AS num_qty_demo
FROM daily_stock ds
WHERE 
      (ds.stock_date >= (SELECT DATE_SUB('".$st_date." 00:00:00', INTERVAL 1 DAY)))
      AND 
      (ds.stock_date <= (SELECT DATE_SUB('".$en_date." 23:59:59', INTERVAL 1 DAY)))
GROUP BY ds.warehouse_id,ds.good_id,ds.good_color_id      
UNION
  SELECT
  'IN' AS action_type
  ,CASE
          WHEN ph.`type` ".$item_type."
           THEN 'normal'
          ELSE 'demo' 
        END as transaction_type
  ,'PO' AS doc_type,ph.`mysql_time` AS action_time,ph.sn,CONCAT(ph.sn_ref,'/',ph.receive_ref) as sn_ref,
  ph.warehouse_id,
  ph.cat_id,
  ph.good_id,
  ph.good_color,
    
      SUM(
        CASE
          WHEN ph.`type` ".$item_type."
          THEN ph.`num` 
          ELSE 0 
        END
      ) AS num_qty,
      SUM(
        CASE
          WHEN ph.`type` ".$item_type_demo."
          THEN ph.`num` 
          ELSE 0 
        END
      ) AS num_qty_demo
FROM `purchase_order` ph 
WHERE 1=1 
AND ph.`mysql_time` IS NOT NULL
AND (ph.mysql_time >= '".$st_date." 00:00:00') 
AND (ph.mysql_time <= '".$en_date." 23:59:59')
GROUP BY ph.warehouse_id,ph.good_id,ph.good_color
UNION
SELECT 
'OUT' AS action_type
 ,CASE
          WHEN m.`type` ".$item_type."
           THEN 'normal'
          ELSE 'demo' 
        END as transaction_type
,'SO' AS doc_type,m.`outmysql_time` AS action_time,m.sn,CONCAT(m.sn_ref,'/',m.invoice_number) AS sn_ref,
      m.warehouse_id,
      m.cat_id,
      m.good_id,
      m.good_color,
      SUM(
        CASE
          WHEN m.`type` ".$item_type."
          THEN m.`num` 
          ELSE 0 
        END
      )*-1 AS num_qty,
      SUM(
        CASE
          WHEN m.`type` ".$item_type_demo."
          THEN m.`num` 
          ELSE 0 
        END
      )*-1 AS num_qty_demo
    FROM
      market m 
    WHERE m.isbacks = 0 
      AND m.`status` = 1 
      AND (
        m.canceled IS NULL 
        OR m.canceled = 0 
        OR m.canceled = ''
      ) 
      AND (
        m.outmysql_time IS NOT NULL 
        OR m.outmysql_time != 0 
        OR m.outmysql_time != ''
      ) 
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')
    GROUP BY m.sn,m.sn_ref,m.good_id,m.warehouse_id,m.good_color
    UNION
    SELECT 
      'OUT' AS action_type
        ,CASE
          WHEN cso.`type` ".$item_type."
           THEN 'normal'
          ELSE 'demo' 
        END as transaction_type

      ,'CO' AS doc_type,cso.`completed_date` AS action_time,cso.changed_sn,cso.sn_ref,
      csp.old_id AS warehouse_id,
      csp.cat_id,
      csp.good_id,
      csp.good_color,
      SUM(
        CASE
          WHEN cso.`type` ".$item_type."
          THEN csp.num 
          ELSE 0 
        END
      )*-1 AS num_qty,
      SUM(
        CASE
          WHEN cso.`type` ".$item_type_demo."
          THEN csp.num 
          ELSE 0 
        END
      )*-1 AS num_qty_demo   
    FROM
      change_sales_order cso 
      JOIN change_sales_product csp 
        ON cso.id = csp.changed_id 
    WHERE 1=1 
      AND cso.STATUS =4
      AND cso.`is_changed_wh` = 1 
      AND (cso.completed_date >= '".$st_date." 00:00:00') 
      AND (cso.completed_date <= '".$en_date." 23:59:59')
    GROUP BY csp.good_id,
      csp.good_color,
      cso.old_id
UNION
SELECT 
'IN' AS action_type
 ,CASE
          WHEN m.`type` ".$item_type."
           THEN 'normal'
          ELSE 'demo' 
        END as transaction_type
,'RO' AS doc_type,m.`outmysql_time` AS action_time,m.sn,m.sn_ref,
      m.warehouse_id,
      m.cat_id,
      m.good_id,
      m.good_color,
      SUM(
        CASE
          WHEN m.`type` ".$item_type."
          THEN m.`num` 
          ELSE 0 
        END
      ) AS num_qty,
      SUM(
        CASE
          WHEN m.`type` ".$item_type_demo."
          THEN m.`num` 
          ELSE 0 
        END
      ) AS num_qty_demo 
    FROM
      market m 
    WHERE m.isbacks = 1 
      AND m.`status` = 1 
      AND (
        m.canceled IS NULL 
        OR m.canceled = 0 
        OR m.canceled = ''
      ) 
      AND (
        m.outmysql_time IS NOT NULL 
        OR m.outmysql_time != 0 
        OR m.outmysql_time != ''
      ) 
      AND (m.outmysql_time >= '".$st_date." 00:00:00') 
      AND (m.outmysql_time <= '".$en_date." 23:59:59')
    GROUP BY m.sn,m.sn_ref,m.cat_id,m.good_id,m.warehouse_id,m.good_color
   )AS t1 
   LEFT JOIN warehouse w ON w.id=t1.warehouse_id
   LEFT JOIN `good_category` c ON c.id = t1.cat_id 
   LEFT JOIN good g ON g.id = t1.good_id 
   LEFT JOIN good_color gc ON gc.id = t1.good_color 
   WHERE 1=1

  ";
  
if (isset($params['warehouse_id'])){
    if (is_array($params['warehouse_id'])){
      $select .=" AND (t1.warehouse_id IN (".implode(",",$params['warehouse_id']).")) ";
    }else{
      $select .=" AND (t1.warehouse_id IN (".$params['warehouse_id'].")) ";
    }
  }

  if (isset($params['cat_id'])){
    if (is_array($params['cat_id'])){
      $select .=" AND (t1.cat_id IN (".implode(",",$params['cat_id']).")) ";
    }else{
      $select .=" AND (t1.cat_id IN (".$params['cat_id'].")) ";
    }
  }

  if (isset($params['good_id'])){
    if (is_array($params['good_id'])){
      $select .=" AND (t1.good_id IN (".implode(",",$params['good_id']).")) ";
    }else{
      $select .=" AND (t1.good_id IN (".$params['good_id'].")) ";
    }
  }

  if (isset($params['color_id'])){
    if (is_array($params['color_id'])){
      $select .=" AND (t1.good_color IN (".implode(",",$params['color_id']).")) ";
    }else{
      $select .=" AND (t1.good_color IN (".$params['color_id'].")) ";
    }
  }

  $select .="GROUP BY t1.sn,t1.sn_ref,t1.warehouse_id,t1.cat_id,t1.good_id,t1.good_color,
  t1.action_type ORDER BY t1.action_time,t1.action_type";

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
}
  
    function getTransactionStockGroup($params){
        $db = Zend_Registry::get('db');
       // print_r($params);
        //die;  

        $sql="

SELECT SUM(t1.in_amount) AS balance_stock,t1.product_name,t1.product_color,t1.category,t1.good_id,t1.good_color,t1.cat_id,t1.warehouse_id,t1.warehouse_name 
FROM 
(SELECT 
  SUM(`p`.`num`) AS `in_amount`,
  `go`.`name` AS `product_name`,
  `g`.`name` AS `product_color`,
  `gc`.`name` AS `category`,
  `p`.`good_id`,
  `p`.`good_color`,
  `p`.`cat_id` ,
  p.warehouse_id,w.name as warehouse_name
  
FROM
  `purchase_order` AS `p` 
  LEFT JOIN `warehouse` AS `w` 
    ON p.warehouse_id = w.id 
  LEFT JOIN `good` AS `go` 
    ON p.good_id = go.id 
  LEFT JOIN `good_color` AS `g` 
    ON p.good_color = g.id 
  LEFT JOIN `good_category` AS `gc` 
    ON p.cat_id = gc.id 
WHERE (p.mysql_time IS NOT NULL) 
  AND (p.mysql_time <> '') 
  AND (p.mysql_time <> 0) 
  ";
  
  if (isset($params['warehouse_id'])){
    if (is_array($params['warehouse_id'])){
      $sql .=" AND (p.warehouse_id IN (".implode(",",$params['warehouse_id']).")) ";
    }else{
      $sql .=" AND (p.warehouse_id IN (".$params['warehouse_id'].")) ";
    }
  }

  if (isset($params['cat_id'])){
    if (is_array($params['cat_id'])){
      $sql .=" AND (p.cat_id IN (".implode(",",$params['cat_id']).")) ";
    }else{
      $sql .=" AND (p.cat_id IN (".$params['cat_id'].")) ";
    }
  }

  if (isset($params['good_id'])){
    if (is_array($params['good_id'])){
      $sql .=" AND (p.good_id IN (".implode(",",$params['good_id']).")) ";
    }else{
      $sql .=" AND (p.good_id IN (".$params['good_id'].")) ";
    }
  }

  if (isset($params['color_id'])){
    if (is_array($params['color_id'])){
      $sql .=" AND (p.good_color IN (".implode(",",$params['color_id']).")) ";
    }else{
      $sql .=" AND (p.good_color IN (".$params['color_id'].")) ";
    }
  }

  $sql .=" AND (
    p.warehouse_id IN 
    (SELECT 
      id 
    FROM
      warehouse 
    WHERE warehouse_type IN (2, 1, 3, 4))
  ) 
UNION ALL

SELECT 
  SUM(`c`.`num`) AS `in_amount`,
  `go`.`name` AS `product_name`,
  `g`.`name` AS `product_color`,
  `gc`.`name` AS `category`, 
  `c`.`good_id`,
  c.good_color,
  c.cat_id ,
  w2.id AS warehouse_id ,w2.name AS warehouse_name
FROM `change_sales_product` AS `c` 
  LEFT JOIN `warehouse` AS `w1` ON c.old_id = w1.id 
  LEFT JOIN `warehouse` AS `w2` ON c.new_id = w2.id 
  LEFT JOIN `good` AS `go` ON c.good_id = go.id 
  LEFT JOIN `good_color` AS `g` ON c.good_color = g.id 
  LEFT JOIN `good_category` AS `gc` ON c.cat_id = gc.id 
  INNER JOIN warehouse.change_sales_order AS cso ON cso.changed_sn = c.changed_sn
WHERE (c.created_at IS NOT NULL) 
  AND (c.created_at <> '') 
  AND (c.created_at <> 0) 
";

  if (isset($params['warehouse_id'])){
    if (is_array($params['warehouse_id'])){
      $sql .=" AND (c.new_id IN (".implode(",",$params['warehouse_id']).")) ";
    }else{
      $sql .=" AND (c.new_id IN (".$params['warehouse_id'].")) ";
    }
  }

  if (isset($params['cat_id'])){
    if (is_array($params['cat_id'])){
      $sql .=" AND (c.cat_id IN (".implode(",",$params['cat_id']).")) ";
    }else{
      $sql .=" AND (c.cat_id IN (".$params['cat_id'].")) ";
    }
  }

  if (isset($params['good_id'])){
    if (is_array($params['good_id'])){
      $sql .=" AND (c.good_id IN (".implode(",",$params['good_id']).")) ";
    }else{
      $sql .=" AND (c.good_id IN (".$params['good_id'].")) ";
    }
  }

  if (isset($params['color_id'])){
    if (is_array($params['color_id'])){
      $sql .=" AND (c.good_color IN (".implode(",",$params['color_id']).")) ";
    }else{
      $sql .=" AND (c.good_color IN (".$params['color_id'].")) ";
    }
  }

  $sql .=" AND (
    c.old_id IN 
    (SELECT 
      id 
    FROM
      warehouse 
    WHERE warehouse_type IN (2, 1, 3, 4))
  ) 
  AND (
    c.new_id IN 
    (SELECT 
      id 
    FROM
      warehouse 
    WHERE warehouse_type IN (2, 1, 3, 4))
  ) ";

  if (isset($params['warehouse_id'])){
    if (is_array($params['warehouse_id'])){
      $sql .=" AND ((c.old_id IN (".implode(",",$params['warehouse_id']).")) OR (c.new_id IN (".implode(",",$params['warehouse_id'])."))) ";
    }else{
      $sql .=" AND ((c.old_id IN (".$params['warehouse_id'].")) OR (c.new_id IN (".$warehouse_id."))) ";
    }
  }


$sql .="  
UNION ALL

SELECT 

  SUM((`m`.`num`)*-1) AS `out_amount`,

  `go`.`name` AS `product_name`,
  `g`.`name` AS `product_color`,
  `gc`.`name` AS `category`,
  `m`.`good_id`,
  `m`.`good_color`,
  `m`.`cat_id`,
  m.warehouse_id ,w.name AS warehouse_name
   
FROM
  `market` AS `m` 
  LEFT JOIN `warehouse` AS `w` 
    ON m.warehouse_id = w.id 
  LEFT JOIN `good` AS `go` 
    ON m.good_id = go.id 
  LEFT JOIN `good_color` AS `g` 
    ON m.good_color = g.id 
  LEFT JOIN `good_category` AS `gc` 
    ON m.cat_id = gc.id 
  LEFT JOIN `distributor` AS `d` 
    ON m.d_id = d.id 
WHERE (m.isbacks = 0) 
  AND (m.old_data IS NULL) 
  AND (m.outmysql_time IS NOT NULL) 
  AND (m.outmysql_time <> '') 
  AND (m.outmysql_time <> 0) 

  ";

  if (isset($params['warehouse_id'])){
    if (is_array($params['warehouse_id'])){
      $sql .=" AND (m.warehouse_id IN (".implode(",",$params['warehouse_id']).")) ";
    }else{
      $sql .=" AND (m.warehouse_id IN (".$params['warehouse_id'].")) ";
    }
  }

  if (isset($params['cat_id'])){
    if (is_array($params['cat_id'])){
      $sql .=" AND (m.cat_id IN (".implode(",",$params['cat_id']).")) ";
    }else{
      $sql .=" AND (m.cat_id IN (".$params['cat_id'].")) ";
    }
  }

  if (isset($params['good_id'])){
    if (is_array($params['good_id'])){
      $sql .=" AND (m.good_id IN (".implode(",",$params['good_id']).")) ";
    }else{
      $sql .=" AND (m.good_id IN (".$params['good_id'].")) ";
    }
  }

  if (isset($params['color_id'])){
    if (is_array($params['color_id'])){
      $sql .=" AND (m.good_color IN (".implode(",",$params['color_id']).")) ";
    }else{
      $sql .=" AND (m.good_color IN (".$params['color_id'].")) ";
    }
  }

  $sql .=" 
  AND m.`canceled` <>1
  AND (
    w.id IN
    (SELECT 
      id 
    FROM
      warehouse 
    WHERE warehouse_type IN (2, 1, 3, 4))
  ) 
GROUP BY 
  `m`.`good_id`,
  `m`.`good_color` 
  )t1
  WHERE t1.product_name IS NOT NULL
  GROUP BY t1.sn,t1.sn_ref,t1.warehouse_id,t1.cat_id,t1.good_id,t1.good_color,
  t1.action_type
  
";
 // echo $sql;
  //die;
        $stmt = $db->query($sql);

        return $result = $stmt->fetchAll();
    }

    function getWarehouseRecord($key){
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('i' => $this->_name), array('i.*'));
        $select->where('i.id = ?',$key);
        return $db->fetchRow($select);
    }
    //Create Function Check Imei By Invoice
    public function fetchSummaryBVGByInv($sales_no) {
        $db = Zend_Registry::get('db');

            $select = $db->select()  

        ->from(array('bv'=> 'bvg_imei'),array(new Zend_Db_Expr('bv.sales_sn,bv.d_id AS distributor_id,bv.joint_circular_id,bv.good_id,gl.name AS color_name')))
        ->joinLeft(array('jc' => 'joint_circular'), 'bv.joint_circular_id=jc.id', array(new Zend_Db_Expr('jc.name AS joint_circular_name')))
        ->joinLeft(array('bp' => 'bvg_product'), 'bp.joint_id=bv.joint_circular_id AND bp.good_id=bv.good_id', array(new Zend_Db_Expr('COUNT(bp.`good_id`) AS qty,bp.`price` AS discount_price,SUM(bp.`price`) AS sum_discount_price')))
        ->joinLeft(array('g' => 'good'), 'bv.good_id=g.id', array(new Zend_Db_Expr('g.cat_id,g.name AS good_name,g.DESC AS description')))
        ->joinLeft(array('gc' => 'good_category'), 'g.cat_id=gc.id')
        ->joinLeft(array('gl' => 'good_color'), 'bv.good_color=gl.id')
        ->joinLeft(array('d' => 'distributor'), 'bv.d_id=d.id', array(new Zend_Db_Expr('d.title AS distributor_name')))
        ->group('bp.good_id');


        //$select->where('gkl.from_date <= ?', $from);
        //$select->where('bv.bvg_payment_confirmed_at is not null',1);
        $select->where('bv.sales_sn= ?', $sales_no);
        $select->order('jc.name');
        

        //echo $select;die;
        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
}

    

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name']) {
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');
          }

        if(isset($params['status']) and $params['status']){
          $select->where('p.status =?',$params['status']);
        }

        if(isset($params['distributor']) and $params['distributor']) {
            $select->joinLeft(array('d' => 'distributor'),'d.agent_warehouse_id = p.id',array());
            $select->where('d.id =?',$params['distributor']);
          }

        if(isset($params['type']) and $params['type']) {
            $select->where('p.warehouse_type =?',$params['type']);
        }

        if(isset($params['office']) and $params['office']) {
          $select->where('p.area_id =?',$params['office']);
        }

        if(isset($params['external_serial']) and $params['external_serial']) {
          $select->where('p.external_serial =?',$params['external_serial']);
        }

        if($limit)
        	$select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function get_cache(){

        // $cache      = Zend_Registry::get('cache');
        // $result     = $cache->load($this->_name.'_cache');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id=$userStorage->id;
        $warehouse_type = $userStorage->warehouse_type;

        // if ($result === false) {
            $db = Zend_Registry::get('db');

            $warehouse_list = '';

            if(isset($user_id) && $user_id){
              $warehouse_list = $this->checkWarehouse_list($user_id);
            }

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
            //$select->where('p.warehouse_type in('.$warehouse_type.')', null);

            if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
              $select->where('p.show_kerry = ?', 1);
            }

            if($warehouse_list !='')
            {
            //  $select->where('p.id in('.$warehouse_list.')', null);
            }

            // $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));
            $select->where('p.status =?',1);
            $select->order('p.position_no DESC');

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name'];
                }
            }
            // $cache->save($result, $this->_name.'_cache', array(), null);
        // }
        return $result;
    }


    function get_cache2(){

        // $cache      = Zend_Registry::get('cache');
        // $result     = $cache->load($this->_name.'_cache');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $user_id=$userStorage->id;
        $warehouse_type = $userStorage->warehouse_type;

        // if ($result === false) {
            $db = Zend_Registry::get('db');

            $warehouse_list = '';

            if(isset($user_id) && $user_id){
              $warehouse_list = $this->checkWarehouse_list($user_id);
            }

            $select = $db->select()
            ->from(array('p' => $this->_name),
                array('p.*'));
            //$select->where('p.warehouse_type in('.$warehouse_type.')', null);

            if (My_Staff_Group::inGroup($userStorage->group_id, array(KERRY_STAFF,KERRY_LEADER))){
              $select->where('p.show_kerry = ?', 1);
            }

            if($warehouse_list !='')
            {
            //  $select->where('p.id in('.$warehouse_list.')', null);
            }

            // $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));
            $select->where('p.status IS NULL');
            $select->order('p.position_no DESC');

            $data = $db->fetchAll($select);

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['warehouse_name'];
                }
            }
            // $cache->save($result, $this->_name.'_cache', array(), null);
        // }
        return $result;
    }


    //Tanong
    //Tanong Get InvoiceNoRef 20160313 1155
    public function getInvoiceNo($sn)
    {
       $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('INV',".$sn.")");
            
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Invoice No, please try again!');
        }
        return $sn_ref;
    }

    public function getInvoiceNo_Ref($sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('INV',".$sn.")");
            
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Invoice No, please try again!');
        }
        return $sn_ref;
    }

    public function getInvoiceNo1($sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        try {
            //print_r($sn);die;
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref_per_month('INV','".$sn."')");
            $stmt->execute();
            $data = $stmt->fetchAll();
            return $running_no= $data[0]['running_no'];
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
    }

    //Tanong Get GRNo Ref 20160328 1115
    public function getGROrderNo_Ref($sn)
    {
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('GR',".$sn.")");
            $stmt->execute();
        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get GR Order No, please try again!');
        }
    }
    //PungPond
    public function findForceSales($params){
      $db = Zend_Registry::get('db');
      $good_id = $params['good_id'];
      // $good_id = implode(',',$params['good_id']);
      $warehouse_id = $params['warehouse_id'];
      $type = $params['type'];
      $d_id = $params['d_id'];
      $select = "
      SELECT t1.* 
          FROM (
          SELECT 
          (SELECT COUNT(fss.campaign_id) FROM force_sale fss WHERE fss.campaign_id=fs.campaign_id) AS total_item
          ,(SELECT COUNT(fsh.id)
          FROM force_sale fsh
          WHERE 1=1
          AND fsh.status=1 
          AND fsh.order_type=$type
          AND 1 IN (SELECT 1 FROM force_sale_warehouse WHERE w_id =$warehouse_id)
          AND fsh.start_date <= DATE(NOW()) AND fsh.end_date >= DATE(NOW())
          AND fsh.campaign_id=(CASE WHEN fsh.distributor_all IS NOT NULL THEN fsh.campaign_id ELSE (SELECT DISTINCT d.force_sale_id FROM force_sale_distributor d WHERE d.d_id=$d_id AND d.force_sale_id=fsh.campaign_id) END)
          AND fsh.good_id IN($good_id)AND fsh.campaign_id=fs.campaign_id ) total_row 
          ,fs.campaign_id,fs.good_id,fs.num,fs.distributor_all
          FROM force_sale fs
          WHERE 1=1
          AND fs.status=1
          AND fs.order_type=$type
          AND 1 IN (SELECT 1 FROM force_sale_warehouse WHERE w_id =$warehouse_id)
          AND fs.start_date <= DATE(NOW()) AND fs.end_date >= DATE(NOW())
          AND fs.campaign_id=(CASE WHEN fs.distributor_all IS NOT NULL THEN fs.campaign_id ELSE (SELECT DISTINCT d.force_sale_id FROM force_sale_distributor d WHERE d.d_id=$d_id AND d.force_sale_id=fs.campaign_id) END)
          
          AND fs.good_id IN($good_id)
          )t1
          GROUP BY t1.good_id 
          HAVING total_item=total_row ";
          // echo $select;die;

          $result = $db->fetchAll($select);
          $data = array();
          
          foreach ($result as $key => $value) {
                $data['good_id']      = $value['good_id'];
                $data['campaign_id'] = $value['campaign_id'];
                
          }
        
        // print_r($data);die;
        return $data;
    }

    //PungPond
    public function checkAccessoriesInMarket($good_id,$sn){
         $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('m' => 'market'),
                array('m.num','m.id'));
            $select->where('m.sn = ?', $sn);
            $select->where('m.good_id = ?', $good_id);

       
            // echo "$select";die;
        $result = $db->fetchRow($select);
        return $result;
    }
    //PungPond
    public function checkPhoneInMarket($sn){
         $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('m' => 'market'),
                array('sum(m.num)'));
            $select->where('m.sn = ?', $sn);
            $select->where('m.cat_id = ?', 11);

       
            // echo "$select";die;
        $result = $db->fetchOne($select);
        return $result;
    }

    //PungPond
    public function checkPhoneInMarketForesale($sn,$good,$good_color=false,$not_color=false){
         $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('m' => 'market'),
                array('sum(m.num)'));
            $select->where('m.sn = ?', $sn);
            $select->where('m.good_id in (?)', $good);

            if($good_color && !$not_color){
              $select->where('m.good_color in (?)', $good_color);
            }
            if($good_color && $not_color){
              $select->where('m.good_color not in (?)', $good_color);
            }

        $result = $db->fetchOne($select);
        return $result;
    }

    public function checkStockForesale($good,$warehouse_id){
         $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('m' => 'warehouse_product'),
                array('quantity'));
            $select->where('m.good_id = ?', $good);
            $select->where('m.warehouse_id = ?', $warehouse_id);

       
            // echo "$select";die;
        $result = $db->fetchOne($select);
        return $result;
    }
    public function checkWarehouse_list($user_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('w' => 'warehouse_list'),
                array('w.warehouse_id'));
            $select->where('w.active = 1', null);
            $select->where('w.user_id = ?', $user_id);
        $result = $db->fetchALL($select);
        $list='';
        foreach ($result as $key => $value) {
            $list      .= $value['warehouse_id'].',';
        }

        return trim($list,",");
    }

    public function in_phone($params)
    { 
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => 'purchase_order'),
                array('date' => 'date_format(p.mysql_time,"%Y-%m-%d")','qty'=>'sum(p.num)'));
            $select->where('cat_id = ?', 11);

             if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(p.mysql_time) >= ?', $year.'-'.$month.'-'.$day );

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(p.mysql_time) <= ?', $year.'-'.$month.'-'.$day);
            }


            $select->where('p.warehouse_id in (?)', $params['warehouse_id']);
            $select->group('date_format(p.mysql_time,"%Y-%m-%d")');
        // echo $select;die;
        $result = $db->fetchALL($select);
        $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }


    public function in_accessories($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => 'purchase_order'),
                array('date' => 'date_format(p.mysql_time,"%Y-%m-%d")','qty'=>'sum(p.num)'));
            $select->where('p.cat_id = ?', 12);

            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(p.mysql_time) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(p.mysql_time) <=  ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('p.good_id not in (?)', $giftbox);
            $select->where('p.warehouse_id in (?)', $params['warehouse_id']);
            
            $select->group('date_format(p.mysql_time,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
      // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
    public function out_phone($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('m' => 'market'),
                array('date' => 'date_format(m.outmysql_time,"%Y-%m-%d")','qty'=>'sum(m.num)'));
            $select->where('cat_id = ?', 11);
            $select->where('canceled != ?', 1);
            $select->where('status = ?', 1);
            $select->where('isbacks != ?', 1);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(m.outmysql_time) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(m.outmysql_time) <=  ?', $year.'-'.$month.'-'.$day);
            }

           
            $select->where('m.warehouse_id in (?)', $params['warehouse_id']);
            $select->group('date_format(m.outmysql_time,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
     // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
    public function out_accessories($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('m' => 'market'),
                array('date' => 'date_format(m.outmysql_time,"%Y-%m-%d")','qty'=>'sum(m.num)'));
            $select->where('cat_id = ?', 12);
            $select->where('canceled != ?', 1);
            $select->where('status = ?', 1);
            $select->where('isbacks != ?', 1);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(m.outmysql_time) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(m.outmysql_time) <= ?', $year.'-'.$month.'-'.$day);
            }
            
            $giftbox = array('152','183','209','215','216','257');
            $select->where('m.good_id not in (?)', $giftbox);
            $select->where('m.warehouse_id in (?)', $params['warehouse_id']);
            $select->group('date_format(m.outmysql_time,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
          // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }

    public function co_in_phone($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 11);
            $select->where('co.new_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            
            
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
     // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
    public function co_out_phone($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 11);
            $select->where('co.old_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            
            
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
      // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
    public function co_in_accessories($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 12);
            $select->where('co.new_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('cp.good_id not in (?)', $giftbox);
            
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
      // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
    public function co_out_accessories($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 12);
            $select->where('co.old_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                 $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('cp.good_id not in (?)', $giftbox);    
           
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
     // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }


    public function in_accessories_gift_box($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => 'purchase_order'),
                array('date' => 'date_format(p.mysql_time,"%Y-%m-%d")','qty'=>'sum(p.num)'));
            $select->where('p.cat_id = ?', 12);

            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(p.mysql_time) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(p.mysql_time) <=  ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('p.good_id in (?)', $giftbox);
            $select->where('p.warehouse_id in (?)', $params['warehouse_id']);
            
            $select->group('date_format(p.mysql_time,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
      // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }

    public function out_accessories_gift_box($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('m' => 'market'),
                array('date' => 'date_format(m.outmysql_time,"%Y-%m-%d")','qty'=>'sum(m.num)'));
            $select->where('cat_id = ?', 12);
            $select->where('canceled != ?', 1);
            $select->where('status = ?', 1);
            $select->where('isbacks != ?', 1);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(m.outmysql_time) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(m.outmysql_time) <= ?', $year.'-'.$month.'-'.$day);
            }
            
            $giftbox = array('152','183','209','215','216','257');
            $select->where('m.good_id in (?)', $giftbox);
            $select->where('m.warehouse_id in (?)', $params['warehouse_id']);
            $select->group('date_format(m.outmysql_time,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
          // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }

    public function co_in_accessories_gift_box($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 12);
            $select->where('co.new_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('cp.good_id in (?)', $giftbox);
            
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
      // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }

    public function co_out_accessories_gift_box($params)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('co' => 'change_sales_order'),
                array('date' => 'date_format(co.completed_date,"%Y-%m-%d")'));
            $select->joinLeft(array('cp'=>'change_sales_product'),'co.id=cp.changed_id',array('qty'=>'sum(cp.num)'));
            $select->where('cp.cat_id = ?', 12);
            $select->where('co.old_id in (?)', $params['warehouse_id']);
            $select->where('co.status = ?', 4);
            if (isset($params['date_from']) and $params['date_from']){
              list( $day, $month, $year ) = explode('/', $params['date_from']);

              if (isset($day) and isset($month) and isset($year) )
               $select->where('DATE(co.completed_date) >= ?', $year.'-'.$month.'-'.$day);

            }
            if (isset($params['date_to']) and $params['date_to']){
              list( $day, $month, $year ) = explode('/', $params['date_to']);

              if (isset($day) and isset($month) and isset($year) )
                 $select->where('DATE(co.completed_date) <= ?', $year.'-'.$month.'-'.$day);
            }
            $giftbox = array('152','183','209','215','216','257');
            $select->where('cp.good_id in (?)', $giftbox);    
           
            
            $select->group('date_format(co.completed_date,"%Y-%m-%d")');
        $result = $db->fetchALL($select);
     // echo $select;die;
       $list= array();
        foreach ($result as $key => $value) {
            $list[$value['date']]  = $value['qty'];
        }

        return $list;
    }
	function getWh()
  {
    $db = Zend_Registry::get('db');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $select = $db->select()
            ->from(array('p' => $this->_name),array('p.*'));
    $result = $db->fetchALL($select);
    return $result;
    
  }

}                  

 