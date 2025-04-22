<?php
class IndexController extends My_Application_Controller_Cli
{

    public function expiredAction(){
        ignore_user_abort();
        set_time_limit(0);

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }


        $today = date('Y-m-d');
        $specific_date = date('Y-m-d', strtotime($today. ' - 2 days'));

        $sql = 'UPDATE `'.$config['resources']['db']['params']['dbname'].'`.`market`
                    SET `status`=2
                    WHERE `status`=1
                        AND `isbacks` = 0
                        AND ( date_format(add_time,"%Y-%m-%d") = "'.$specific_date.'" )
                        AND `shipping_yes_time` IS NULL
                        AND `pay_time` IS NULL
                        AND `outmysql_time` IS NULL
                        AND invoice_number IS NULL
                        AND sales_confirm_date IS NULL
                        and warehouse_id NOT IN (62, 90)
                        AND d_id not in(21088,25760,32163)
               ;';

        mysqli_query($con,$sql);

        $sql1 = 'DELETE FROM `credit_note_tran` WHERE sales_order IN(select sn from market WHERE `status`=2
                        AND `isbacks` = 0
                        AND ( date_format(add_time,"%Y-%m-%d") = "'.$specific_date.'" )
                        AND `shipping_yes_time` IS NULL
                        AND `pay_time` IS NULL
                        AND `outmysql_time` IS NULL
                        AND sales_confirm_date IS NULL
                        and warehouse_id NOT IN (62, 90)
                        AND invoice_number IS NULL AND d_id not in(21088,25760,32163))';
        mysqli_query($con,$sql1);

        $sql2 = 'DELETE FROM `deposit_tran` WHERE sales_order IN(select sn from market WHERE `status`=2
                        AND `isbacks` = 0
                        AND ( date_format(add_time,"%Y-%m-%d") = "'.$specific_date.'" )
                        AND `shipping_yes_time` IS NULL
                        AND `pay_time` IS NULL
                        AND `outmysql_time` IS NULL
                        AND sales_confirm_date IS NULL
                        and warehouse_id NOT IN (62, 90)
                        AND invoice_number IS NULL AND d_id not in(21088,25760,32163))';
        mysqli_query($con,$sql2);

        mysqli_close($con);

        $content = date('Y-m-d H:i:s') . ' expired ';

        error_log($content . "\n", 3, APPLICATION_PATH.'/../bin/access.log');

        die();

        exit;
    }

    public function updateSalesIdAction(){
        ignore_user_abort();
        set_time_limit(0);

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $from_date = $this->getRequest()->getParam('from_date');
        $to_date = $this->getRequest()->getParam('to_date');

        if (!$from_date or !$to_date)
            die('Please choose date');

        $sql = '
                    SELECT *
                        FROM `'.$config['resources']['db']['params']['dbname'].'`.`imei`
                        WHERE `sales_sn` IS NOT NULL
                            AND `sales_sn` > 0
                            AND `sales_id` IS NULL

               ';

        $sql .= ' and out_date >= "'.$from_date.' 00:00:00"';
        $sql .= ' and out_date <= "'.$to_date.' 23:59:59"';

        $result = mysqli_query($con,$sql);

        while($item = mysqli_fetch_assoc($result))
        {
            //lấy danh sách đơn hàng
            $sql = '
                    SELECT *
                        FROM `'.$config['resources']['db']['params']['dbname'].'`.`market`
                        WHERE `sn` = "'.$item['sales_sn'].'"

               ';

            $result2 = mysqli_query($con,$sql);

            $sales_list_combined = array();
            // nhóm sales_list theo good_id và good_color
            while($value = mysqli_fetch_assoc($result2)){
                $item2 = array(
                    'good_id'    => $value['good_id'],
                    'good_color' => $value['good_color'],
                    'num'        => $value['num'],
                    'sales_id'   => $value['id'],
                );

                $sales_list_combined[$value['good_id']][$value['good_color']][] = $item2;


            }

            //check để lấy số sales id
            $check = isset($sales_list_combined[$item['good_id']][$item['good_color']]) ? $sales_list_combined[$item['good_id']][$item['good_color']] : null;

            $sales_id = 0;

            if ($check){

                //lấy số lượng scanned rồi trên sales_id này

                if (count($check) == 1){
                    $sales_id = $check[0]['sales_id'];

                } else {

                    //check trên từng row, cái nào đầy rồi thì đẩy kế tiếp
                    foreach ($check as $j=>$item3){

                        $sql = '
                            SELECT COUNT(imei_sn) as total
                                FROM `'.$config['resources']['db']['params']['dbname'].'`.`imei` i
                                WHERE `sn` = "'.$item['sales_sn'].'"
                                    AND i.out_date IS NOT NULL AND i.out_date <> 0 AND i.out_date <> \'\'
                                    AND i.into_date IS NOT NULL AND i.into_date <> 0 AND i.into_date <> \'\'
                                    AND i.good_id =  \''.$item3['good_id'].'\'
                                    AND i.good_color =  \''.$item3['good_color'].'\'
                                    AND i.sales_id =  \''.$item3['sales_id'].'\'

                       ';

                        $result3 = mysqli_query($con,$sql);

                        $count = mysql_fetch_assoc($result3);

                        $imei_scanned = $count['total'];

                        $needle_by_model = $item3['num'] - $imei_scanned;

                        if ($needle_by_model > 0){
                            $sales_id = $item3['sales_id'];
                            break;
                        }
                    }
                }

            }

            if ($sales_id) {
                $sql = '    UPDATE `'.$config['resources']['db']['params']['dbname'].'`.`imei`
                        SET `sales_id`= \''.$sales_id.'\'
                        WHERE imei_sn = \''.$item['imei_sn'].'\'

                   ;';

                mysqli_query($con,$sql);

                echo $item['imei_sn']." ==> ".$sales_id." \n";
            }
        }

        mysqli_query($con,$sql);

        mysqli_close($con);

        $content = date('Y-m-d H:i:s') . ' expired ';

        error_log($content . "\n", 3, APPLICATION_PATH.'/../bin/access.log');

        die();

    }

    public function migrateAction(){
        ignore_user_abort();
        set_time_limit(0);

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }



        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`log`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`log` (
                    id,
                    user_id,
                    info,
                    ip_address,
                    `time`
                )SELECT
                    wl.log_id,
                    wl.user_id,
                    wl.log_info,
                    wl.ip_address,
                    IF(wl.`log_time`>0, FROM_UNIXTIME(wl.log_time), null)
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_admin_log` wl;';


        mysqli_query($con,$sql);

        echo 'log: done!'."\n";


        /*$sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`staff`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`staff` (
                    id,
                    username,
                    firstname,
                    lastname,
                    email,
                    `password`,
                    `last_login`,
                    `last_ip`,
                    `phone_number`,
                    `note`,
                    `gender`,
                    `additional_info`,
                    `status`,
                    `group_id`,
                    `created_at`,
                    `created_by`,
                    `updated_at`,
                    `updated_by`
                )SELECT
                    wu.user_id,
                    wu.user_name,
                    wu.user_name,
                    wu.user_name,
                    wu.email,
                    md5(123456),
                    IF(wu.`last_login`>0, FROM_UNIXTIME(wu.last_login), null),
                    wu.last_ip,
                    wu.tel,
                    wu.admin_text,
                    1,
                    null,
                    1,
                    null,
                    IF(wu.`add_time`>0, FROM_UNIXTIME(wu.add_time), null) ,
                    1,
                    null,
                    null
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_admin_user` wu;';


        mysqli_query($con,$sql);

        echo 'staff: done!'."\n";*/


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`area`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`area` (
                    id,
                        `name`
                    )SELECT
                        wa.id,
                        wa.`name`
                    FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_area` wa;';


        mysqli_query($con,$sql);

        echo 'area: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`brand`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`brand` (
                    id,
                    `name`
                )SELECT
                    wb.brand_id,
                    wb.brand_name
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_brand` wb;';


        mysqli_query($con,$sql);

        echo 'brand: done!'."\n";



        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`distributor`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`distributor` (
                    id,
                    `title`,
                    `rank`,
                    `region`,
                    `unames`,
                    `name`,
                    `tel`,
                    `add`,
                    `email`,
                    `admin`,
                    `add_time`,
                    `mst_sn`,
                    `nots`
                )SELECT
                    wd.`d_id`,
                    wd.`d_title`,
                    wd.`d_rank`,
                    wd.`d_region`,
                    wd.`d_unames`,
                    wd.`d_name`,
                    wd.`d_tel`,
                    wd.`d_add`,
                    wd.`d_email`,
                    wd.`d_admin`,
                    IF(wd.`add_time`>0, FROM_UNIXTIME(wd.`add_time`), null),
                    wd.`mst_sn`,
                    wd.`nots`
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_distributor` wd;';


        mysqli_query($con,$sql);

        echo 'distributor: done!'."\n";



        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`good`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`good` (
                    id,
                    `cat_id`,
                    `name`,
                    `color`,
                    `brand_id`,
                    `price_4`,
                    `price_1`,
                    `price_2`,
                    `price_3`,
                    `desc`,
                    `add_time`
                )SELECT
                    wg.`goods_id`,
                    wg.`cat_id`,
                    wg.`goods_name`,
                    wg.`goods_color`,
                    wg.`brand_id`,
                    wg.`price_4`,
                    wg.`price_1`,
                    wg.`price_2`,
                    wg.`price_3`,
                    wg.`goods_desc`,
                    IF(wg.`add_time`>0, FROM_UNIXTIME(wg.`add_time`), null)
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_goods` wg;';


        mysqli_query($con,$sql);

        //insert good_color_combined
        $sql = 'TRUNCATE TABLE `good_color_combined`';
        mysqli_query($con,$sql);

        $sql = '    SELECT *
                    FROM `good`
                    ';

        $result = mysqli_query($con,$sql);

        while($row = @mysqli_fetch_array($result))
        {
            $colors = $row['color'];
            $colors = explode(',', $colors);

            foreach ($colors as $color){
                $sql = 'insert into `good_color_combined`( `good_id`, `good_color_id` ) values ("'.$row['id'].'", "'.$color.'"); ';
                mysqli_query($con,$sql);
            }
        }


        echo 'good: done!'."\n";

        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`good_color`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`good_color` (
                    id,
                    `name`
                )SELECT
                    wgc.color_id,
                    wgc.color_name
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_goods_color` wgc;';


        mysqli_query($con,$sql);

        echo 'good_color: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`good_category`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`good_category` (
                    id,
                    `name`
                )SELECT
                    wgc.cat_id,
                    wgc.cat_name
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_goods_cat` wgc;';


        mysqli_query($con,$sql);

        echo 'good_category: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`imei`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`imei` (
                    id,
                    `imei_sn`,
                    `good_id`,
                    `good_color`,
                    `po_sn`,
                    `distributor_id`,
                    `into_date`,
                    `out_date`,
                    `sales_sn`,
                    `activated_date`,
                    `out_price`,
                    `price_date`,
                    `out_user`,
                    `return_sn`,
                    `warehouse_id`
                )SELECT
                    wi.`main_id`,
                    wi.`imei_sn`,
                    wi.`goods_id`,
                    wi.`goods_color`,
                    wi.`procure_sn`,
                    wi.`goods_user`,
                    IF(wi.`into_date`>0, FROM_UNIXTIME(wi.`into_date`), null),
                    IF(wi.`out_date`>0, FROM_UNIXTIME(wi.`out_date`), null),
                    wi.`sales_sn`,
                    IF(wi.`activated_date`>0, FROM_UNIXTIME(wi.`activated_date`), null),
                    wi.`out_price`,
                    IF(wi.`price_date`>0, FROM_UNIXTIME(wi.`price_date`), null),
                    wi.`user_name`,
                    null,
                    1
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_imei` wi;';


        mysqli_query($con,$sql);

        echo 'imei: done!'."\n";




        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`imei_warehouse`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`imei_warehouse` (
                    `imei_sn`,
                    `dateline`,
                    `status`
                )SELECT
                    wiw.imei_sn,
                    FROM_UNIXTIME(wiw.dateline),
                    wiw.`status`
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_imei_warehouse` wiw;';


        mysqli_query($con,$sql);

        echo 'imei_warehouse: done!'."\n";




        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`region`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`region` (
                    `id`,
                    `name`,
                    `adminuser`,
                    `area_id`
                )SELECT
                    wr.region_id,
                    wr.region_name,
                    wr.adminuser,
                    wr.area_id
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_region` wr;';


        mysqli_query($con,$sql);

        echo 'region: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`purchase_order`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`purchase_order` (
                    `id`,
                    `cat_id`,
                    `good_id`,
                    `good_color`,
                    `sn`,
                    `price`,
                    `num`,
                    `warehouse_id`,
                    `flow`,
                    `flow_time`,
                    `mysql_time`,
                    `mysql_user`,
                    `text`,
                    `pay_user`,
                    `created_at`,
                    `created_by`,
                    `updated_at`,
                    `updated_by`
                )SELECT
                    wp.procure_id,
                    null,
                    wp.procure_goods_id,
                    wp.procure_goods_color,
                    wp.procure_sn,
                    wp.procure_price,
                    wp.procure_num,
                    1,
                    wp.procure_flow,
                    IF(wp.procure_flow_time>0, FROM_UNIXTIME(wp.procure_flow_time), null) ,
                    IF(wp.procure_mysql_time>0, FROM_UNIXTIME(wp.procure_mysql_time), null) ,
                    wp.procure_mysql_user,
                    wp.procure_text,
                    wp.procure_pay_user,
                    IF(wp.procure_time>0, FROM_UNIXTIME(wp.procure_time), null),
                    wp.procure_name,
                    null,
                    null
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_procure` wp;';


        mysqli_query($con,$sql);

        $sql = 'UPDATE purchase_order po,
                    good g
                SET po.cat_id = g.cat_id
                WHERE
                    po.good_id = g.id;';

        mysqli_query($con,$sql);

        echo 'purchase_order: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`market`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`market` (
                    `id`,
                    `sn`,
                    `user_id`,
                    `d_id`,
                    `num`,
                    `add_time`,
                    `price`,
                    `total`,
                    `shipping_yes_time`,
                    `pay_time`,
                    `shipping_yes_id`,
                    `pay_user`,
                    `outmysql_time`,
                    `outmysql_user`,
                    `good_color`,
                    `good_id`,
                    `cat_id`,
                    `text`,
                    `difference`,
                    `pay_text`,
                    `shipping_text`,
                    `procure_pay_user`,
                    `price_time`,
                    `price_clas`,
                    `isbatch`,
                    `isbacks`,
                    `backs_d_id`,
                    `warehouse_id`,
                    `print_no`,
                    `print_time`,
                    `salesman`
                )SELECT
                    wm.`mark_id`,
                    wm.`mark_sn`,
                    wm.`mark_user_id`,
                    wm.`mark_d_id`,
                    wm.`mark_num`,
                    if ( wm.`mark_add_time`> 0, FROM_UNIXTIME(wm.`mark_add_time`), null) ,
                    wm.`mark_price`,
                    wm.`mark_total`,
                    if ( wm.`mark_shipping_yes_time`> 0, FROM_UNIXTIME(wm.`mark_shipping_yes_time`), null) ,
                    if ( wm.`mark_pay_time`> 0, FROM_UNIXTIME(wm.`mark_pay_time`), null) ,
                    wm.`mark_shipping_yes_id`,
                    wm.`mark_pay_user`,
                    if ( wm.`mark_outmysql_time`> 0, FROM_UNIXTIME(wm.`mark_outmysql_time`), null) ,
                    wm.`mark_outmysql_user`,
                    wm.`mark_goods_color`,
                    wm.`mark_goods_id`,
                    null,
                    wm.`mark_text`,
                    wm.`mark_difference`,
                    wm.`mark_pay_text`,
                    wm.`mark_shipping_text`,
                    wm.`procure_pay_user`,
                    if ( wm.`mark_price_time`> 0, FROM_UNIXTIME(wm.`mark_price_time`), null) ,
                    wm.`price_clas`,
                    wm.`isbatch`,
                    wm.`isbacks`,
                    wm.`mark_backs_d_id`,
                    1,
                    null,
                    null,
                    wm.`mark_user_id`
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_market` wm;';


        mysqli_query($con,$sql);

        $sql = 'UPDATE market m,
                    good g
                SET m.cat_id = g.cat_id
                WHERE
                    m.good_id = g.id;';

        mysqli_query($con,$sql);

        echo 'market: done!'."\n";


        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_imei`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_imei` (
                    `id`,
                    `imei`,
                    `new_id`,
                    `old_id`,
                    `created_at`,
                    `created_by`,
                    `out_date`,
                    `out_user`,
                    `into_date`,
                    `into_user`,
                    `status`,
                    `changed_sn`
                )SELECT
                    ws.`id`,
                    ws.`imei`,
                    ws.`d_id`,
                    ws.`d_id_old`,
                    if ( ws.`dateline`> 0, FROM_UNIXTIME(ws.`dateline`), null),
                    ws.`admin_user`,
                    if ( ws.`dateline`> 0, FROM_UNIXTIME(ws.`dateline`), null),
                    ws.`admin_user`,
                    if ( ws.`dateline`> 0, FROM_UNIXTIME(ws.`dateline`), null),
                    ws.`admin_user`,
                    2,
                    ws.`dateline`
                FROM
                    `'.$config['resources']['db2']['params']['dbname'].'`.`web_salestwo` ws;';


        mysqli_query($con,$sql);

        $sql = '    SELECT *
                    FROM `change_sales_imei`
                    GROUP BY `changed_sn`
                    ';

        $result = mysqli_query($con,$sql);

        while($row = @mysqli_fetch_array($result))
        {
            $changed_sn = $row['changed_sn'];
            $changed_sn .= substr ( microtime (), 2, 4 );

            $sql = 'update `change_sales_imei` set `changed_sn` = \''.$changed_sn.'\' where `changed_sn` = \''.$row['changed_sn'].'\'; ';
            mysqli_query($con,$sql);
        }

        echo 'change_sales_imei: done!'."\n";

        $sql = ' truncate table `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_order`; ';

        mysqli_query($con,$sql);

        $sql = ' INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_order` (
                    `changed_sn`,
                    `is_changed_wh`,
                    `new_id`,
                    `old_id`,
                    `created_at`,
                    `created_by`,
                    `completed_date`,
                    `completed_user`
                )SELECT
										`csi`.`changed_sn`,
                    0,
                    `csi`.`new_id`,
                    `csi`.`old_id`,
                    `csi`.`created_at`,
                    `csi`.`created_by`,
                    `csi`.`created_at`,
                    `csi`.`created_by`
                FROM
                    `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_imei` csi
								group by csi.changed_sn;';

        mysqli_query($con,$sql);

        echo 'change_sales_order: done!'."\n";


        mysqli_close($con);

        $content = date('Y-m-d H:i:s') . ' imported ';

        error_log($content . "\n", 3, APPLICATION_PATH.'/../bin/access.log');

        die();

        exit;
    }

    public function updateChangeOrderAction(){
        ignore_user_abort();
        set_time_limit(0);

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $from = $this->getRequest()->getParam('from');
        $to = $this->getRequest()->getParam('to');

        if (!$to)
            die('Please choose range');

        $sql = '
                    SELECT *
                        FROM `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_imei`
                        WHERE `changed_sales_product_id` IS NULL
                            and imei >= \''.$from.'\'
                            and imei <= \''.$to.'\'

               ';

        $result = mysqli_query($con,$sql);

        while($item = mysqli_fetch_assoc($result))
        {
            //lay ra id cua don hang
            $sql = '
                    SELECT id
                        FROM `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_order`
                        WHERE
                            `changed_sn` = "'.$item['changed_sn'].'"

               ';

            $result3 = mysqli_query($con,$sql);

            $order_id = null;

            // nhóm sales_list theo good_id và good_color
            while($value = mysqli_fetch_assoc($result3)){

                $order_id = $value['id'];
            }

            $sql = '
                    SELECT id, num, receive, changed_sn, good_id, good_color
                        FROM `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_product`
                        WHERE
                            `changed_sn` = "'.$item['changed_sn'].'"
                            and `cat_id` = "'.$item['cat_id'].'"
                            and `good_id` = "'.$item['good_id'].'"
                            and `good_color` = "'.$item['good_color'].'"

               ';

            $result2 = mysqli_query($con,$sql);

            $changed_sales_product_id = null;

            $data = array();
            // nhóm sales_list theo good_id và good_color
            while($value = mysqli_fetch_assoc($result2)){
                $data = array(
                    'good_id'    => $value['good_id'],
                    'good_color' => $value['good_color'],
                    'num'        => $value['num'] + 1,
                    'changed_sn'   => $value['changed_sn'],
                    'receive'   => $value['receive'],
                );

                if ($item['status'] == 2){
                    $data['receive'] = $value['receive'] + 1;
                }

                $changed_sales_product_id = $value['id'];
            }

            if ($data){
                $sql = '    UPDATE `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_product`
                        SET
                            `num`= \''.$data['num'].'\',
                            `receive`= \''.$data['receive'].'\'
                        WHERE `changed_sn` = "'.$item['changed_sn'].'"
                            and `cat_id` = "'.$item['cat_id'].'"
                            and `good_id` = "'.$item['good_id'].'"
                            and `good_color` = "'.$item['good_color'].'"

                   ;';

                mysqli_query($con,$sql);
            } else {
                $receive = 0;
                if ($item['status'] == 2){
                    $receive = 1;
                }

                $sql = '    INSERT INTO `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_product`
                        (`changed_id`, `num`, `receive`, `changed_sn`, `cat_id`, `good_id`, `good_color` )
                        VALUES (


                            \''.$order_id.'\',
                            \'1\',
                            \''.$receive.'\',
                            \''.$item['changed_sn'].'\',
                            \''.$item['cat_id'].'\',
                            \''.$item['good_id'].'\',
                            \''.$item['good_color'].'\'
                        )

                   ;';

                mysqli_query($con,$sql);
                $changed_sales_product_id = mysqli_insert_id($con);
            }

            // update lai imei
            $sql = '    UPDATE `'.$config['resources']['db']['params']['dbname'].'`.`change_sales_imei`
                        SET
                            `changed_sales_product_id`= \''.$changed_sales_product_id.'\'
                        WHERE `changed_sn` = "'.$item['changed_sn'].'"
                            and `cat_id` = "'.$item['cat_id'].'"
                            and `good_id` = "'.$item['good_id'].'"
                            and `good_color` = "'.$item['good_color'].'"

                   ;';

            mysqli_query($con,$sql);

            echo $item['imei']." ==> ".$changed_sales_product_id." \n";


        }

        mysqli_close($con);

        die();

    }
}
