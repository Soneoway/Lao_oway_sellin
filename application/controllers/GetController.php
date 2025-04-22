<?php

/**
* Gom một số AJAX action mà không biết đặt vô đâu cho phù hợp
*/
class GetController extends My_Controller_Action
{

public function getOpenMarketCampaignByOperationAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $operation_id = $this->getRequest()->getParam('operation_id');

    $db = Zend_Registry::get('db');
    // echo $warehouse_type_id;die;   

    $sql="SELECT count(dis.oppo_distributor_id)as _status,dis.oppo_distributor_id,dis.partner_code,dis.partner_name FROM external_distributor_code dis where dis.oppo_distributor_id='".$distributor_id."' and dis.partner_name='".$operation_id."' and dis.status=1;";
    $result = $db->fetchAll($sql);

    if ($result)
    {
        echo json_encode(array('status'=>1,'result'=>$result));
        exit;
    } else {
        echo json_encode(array('status'=>0,'result'=>$result));
        exit;
    }
}

public function checkPhoneNumberAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $phone_number = $this->getRequest()->getParam('phone_number');
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $db = Zend_Registry::get('db');
    // echo $warehouse_type_id;die;   
    $QPhoneNumber     = new Application_Model_PhoneNumber();

    $sql="SELECT phone_number_sn,sales_order FROM  phone_number where sales_order <> '".$sales_sn."' and phone_number_sn='".$phone_number."'";
    $result = $db->fetchAll($sql);
    if($result==true){
        $status=2; //dup
    }else{
        $status=1;
    }
    echo json_encode(array('status'=>$status));
    exit;
}

public function checkPriceProtectionImeiAutoCheckAction()
{

    $imei_list = $this->getRequest()->getParam('imei_list');
    $cp_price = $this->getRequest()->getParam('price');
    //print_r($_POST);
    $db = Zend_Registry::get('db');

    $result_check_return=null;
    $result_return_by_distributor=null;$result_return_by_group=null;$result_return_by_imei=null;

    try{

        //------------------Return Product
        /* Check Imei Return Product (Group By Distributor) */
        $select_return_by_distributor ="SELECT t.distributor_id,
        t.store_code,
        t.title,t.rank_price,t.rank_price_name,
        COUNT(t.imei_sn) AS count_imei,
        sum(t.bvg_price) as sum_bvg_price,
        SUM(t.total_price)AS sum_price,
        ROUND(t.unit_price,2) as unit_price,
        ROUND((t.unit_price*COUNT(t.imei_sn)),2)AS cn_price
        FROM(
            SELECT   
            i.imei_sn,
            dis.id AS distributor_id,
            dis.store_code,
            dis.title,
            COUNT(bi.imei_sn) AS bvg_num,
            m.sn AS sales_sn,
            m.invoice_number,
            CASE m.price_clas
            WHEN 1 THEN (".$cp_price."*18)/100
            WHEN 2 THEN (".$cp_price."*15)/100
            WHEN 5 THEN (".$cp_price."*17)/100
            WHEN 6 THEN (".$cp_price."*10)/100
            WHEN 7 THEN 1500-".$cp_price."
            WHEN 8 THEN (".$cp_price."*15)/100
            WHEN 11 THEN (".$cp_price."*20)/100
            WHEN 12 THEN (".$cp_price."*15)/100
            WHEN 13 THEN 1500-".$cp_price."
            WHEN 14 THEN 1500-".$cp_price."
            ELSE 0
            END as unit_price,
            SUM(IFNULL( bi.price ,0)) AS bvg_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS invoice_price,
            ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS total_price,
            m.cat_id,       
            g.id AS good_id,
            c.id AS good_color,
            g.name AS product_name,
            g.desc AS product_detail_name,
            c.name AS product_color,
            m.price_clas as rank_price,
            CASE m.price_clas
            WHEN 1 THEN 'ORG-WDS(1)'
            WHEN 2 THEN 'ORG(2)'
            WHEN 3 THEN 'Online and Staff(3)'
            WHEN 5 THEN 'ORG-Dtac/Advice(5)'
            WHEN 6 THEN 'ORG-Lotus/Power by(6)'
            WHEN 7 THEN 'Dealer(7)'
            WHEN 8 THEN 'HUB(8)'
            WHEN 9 THEN 'Laos(9)'
            WHEN 10 THEN 'Brand Shop/Service(10)'
            WHEN 11 THEN 'King Power(11)'
            WHEN 12 THEN 'Jaymart(12)'
            WHEN 13 THEN 'Brand Shop By Dealer(13)'
            WHEN 14 THEN 'KR Dealer(14)'
            WHEN 15 THEN 'TRUE(15)'  
            ELSE '-'
            END as rank_price_name
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
            GROUP BY i.distributor_id,m.price_clas,i.sales_sn,i.good_id,i.good_color,i.imei_sn
            )t
        GROUP BY t.distributor_id,t.rank_price
        ORDER BY t.distributor_id,t.invoice_number";

        //echo $select_return_by_distributor;die;

        /* Check Imei Return Product (Group By Imei)*/
        $select_return_by_imei ="SELECT   
        i.imei_sn,
        dis.id AS distributor_id,
        dis.store_code,
        dis.title,
        COUNT(i.imei_sn) AS bvg_num,
        m.sn as sales_sn,
        m.invoice_number,
        CASE m.price_clas
        WHEN 1 THEN (1000*18)/100
        WHEN 2 THEN (1000*15)/100
        WHEN 5 THEN (1000*17)/100
        WHEN 6 THEN (1000*10)/100
        WHEN 7 THEN 1500-1000
        WHEN 8 THEN (1000*15)/100
        WHEN 11 THEN (1000*20)/100
        WHEN 12 THEN (1000*15)/100
        WHEN 13 THEN 1500-10
        WHEN 14 THEN 1500-10
        ELSE 0
        END as unit_price,
        SUM(IFNULL( bi.price ,0)) AS bvg_price,
        ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS invoice_price, 
        m.cat_id,       
        g.id AS good_id,
        c.id AS good_color,
        g.name AS product_name,
        g.desc AS product_detail_name,
        c.name AS product_color,
        m.price_clas as rank_price,
        CASE m.price_clas
        WHEN 1 THEN 'ORG-WDS(1)'
        WHEN 2 THEN 'ORG(2)'
        WHEN 3 THEN 'Online and Staff(3)'
        WHEN 5 THEN 'ORG-Dtac/Advice(5)'
        WHEN 6 THEN 'ORG-Lotus/Power by(6)'
        WHEN 7 THEN 'Dealer(7)'
        WHEN 8 THEN 'HUB(8)'
        WHEN 9 THEN 'Laos(9)'
        WHEN 10 THEN 'Brand Shop/Service(10)'
        WHEN 11 THEN 'King Power(11)'
        WHEN 12 THEN 'Jaymart(12)'
        WHEN 13 THEN 'Brand Shop By Dealer(13)'
        WHEN 14 THEN 'KR Dealer(14)'
        WHEN 15 THEN 'TRUE(15)' 
        ELSE '-'
        END as rank_price_name
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
        GROUP BY i.distributor_id,m.price_clas,i.sales_sn,i.good_id,i.good_color,i.imei_sn
        ORDER BY m.d_id,m.invoice_number";

       // echo $select_return_by_imei;die;


        $result_return_by_distributor = $db->fetchAll($select_return_by_distributor);
        if ($result_return_by_distributor)
        {
            $result_return_by_imei = $db->fetchAll($select_return_by_imei);
            //echo $select_return_by_imei;die;
        }

        if ($result_return_by_distributor)
        {
            echo json_encode(array('check'=>1,'result_return_by_distributor'=>$result_return_by_distributor,'result_return_by_imei'=>$result_return_by_imei));
            exit;
        }

    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        exit;
    }
}

public function loadJobNumberAjaxAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true); 
    $job_sn = $this->getRequest()->getParam('job_sn');
    //echo $job_sn.'aaaa';die;
    $QJobNumber = new Application_Model_JobNumber(); 
    $select = $QJobNumber->select()
    ->from(array('i' => 'job_number'), array('i.*'));
    $select->where('i.job_sn = ?',$job_sn);
    $row = $QJobNumber->fetchRow($select);

    $json[]=array(
       'job_sn'=> $row['job_sn'],
       'sales_order'=> $row['sales_order']
   );
    echo json_encode($json);
}

public function loadPrintPickingUserLogAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);    
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $QPrintPickingLog    = new Application_Model_PrintPickingLog();
    try{

        $select = $QPrintPickingLog->select()
        ->from(array('l'=> 'print_picking_log'),array('concat(s.username,"(",s.id,")") as user_staff'))
        ->joinLeft(array('s' => 'staff'), 's.id=l.user_id', array('l.user_id'))
        ->where('l.sales_order = ?', $sales_sn)
        ->order('l.print_date desc')
        ->limit(1,0);

        $result = $QPrintPickingLog->fetchRow($select);
        if ($result)
        {
            echo $result['user_staff'];
        }
    }catch(exception $e)
    {
        echo $e->getMessage();

        exit;
    }

}

public function loadSalesCattyAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $QStaff = new Application_Model_Staff();

    try{

        $result = $QStaff->getSalesCattyByStore($distributor_id,'');
        if ($result)
        {
            echo json_encode(array('status'=>1,'result'=>$result));
            exit;
        } else {
            echo json_encode(array());
            exit;
        }
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

public function loadCreditNotePayslipAction()
{

    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $creditnote_sn_list = $this->getRequest()->getParam('init_params');
    // die($distributor_id.'=='.$creditnote_sn_list);
    // Tanong Add Function CreditNote 2016/03/08 16:12
    $QCreditNote = new Application_Model_CreditNote();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        // $creditnote_sn_list='CN59020008,CN59030008';
        // $creditnote_sn_list='CN59020008';
        $result = $QCreditNote->getCredit_Note_PaySlip($distributor_id,$creditnote_sn_list);
        // print_r($result);die;
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have joint circular')));

        if($result['code'] != 1)
        {
            throw new exception('Distributor is don\'t have Credit Note');
        }

        $list_joint_exist = $result['data'];

        echo json_encode($list_joint_exist);
        exit;


    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }
}

public function loadInvoiceAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $good_id = $this->getRequest()->getParam('good_id');
    $good_color = $this->getRequest()->getParam('good_color');

    $QMarket = new Application_Model_Market();

    try{

        $select = $QMarket->select()
        ->distinct()
        ->where('d_id = ?', $distributor_id)
        ->where('good_id = ?', $good_id)
        ->where('good_color = ?', $good_color)
        ->where('invoice_number IS NOT NULL', null)
        ->order("invoice_time desc");
        $market = $QMarket->fetchAll($select);

        /*
        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('d_id = ?', $distributor_id);
        $where[] = $QMarket->getAdapter()->quoteInto('good_id = ?', $good_id);
        $where[] = $QMarket->getAdapter()->quoteInto('good_color = ?', $good_color);

        $market = $QMarket->fetchAll($where);
        */
        if ($market)
        {
            echo json_encode(array('invoice_sn' => $market->toArray()));
            exit;
        } else {
            echo json_encode(array());
            exit;
        }



    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

public function checkReturnImeiAutoCheckAction()
{
    //print_r($_GET);die;
    $imei_list = $this->getRequest()->getParam('imei_list');

    $imei_list_check_imei_locked = str_replace("'","",$imei_list);
    $imei_list_check_imei_locked = explode(',', $imei_list_check_imei_locked);

    // check lock imei
    $QImeiLock = new Application_Model_ImeiLock();
    $getImeiLock = $QImeiLock->checkImeiLock($imei_list_check_imei_locked);

    $listImeiLock = '';
    foreach ($getImeiLock as $key => $value) {
        if($key == 0){
            $listImeiLock = $value['imei_log'];
        }else{
            $listImeiLock .= '<br>'. $value['imei_log'];
        }

        $imei_list = str_replace($value['imei_log'],"",$imei_list);
    }
    // if($listImeiLock){
    //     exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
    //         '<br></div>');
    //     echo json_encode(array('error' => 'IMEI Locked<br>' . $listImeiLock .
    //         '<br>'));
    //     exit;
    // }

    $db = Zend_Registry::get('db');

    $result_check_return=null;
    $result_return_by_distributor=null;$result_return_by_group=null;$result_return_by_imei=null;$result_borrowing_by_group=null;$result_borrowing_by_imei=null;

    try{

        //------------------borrowing
        $select_borrowing_by_request ="SELECT bi.id,bi.sn,bl.rq,co.sn_ref AS co,
        g.cat_id, 
        bi.good_id,
        bi.good_color_id,
        bi.qty AS borrow_qty,
        (SELECT COUNT(bri.imei) 
            FROM borrowing_tran bri
            INNER JOIN imei AS i ON i.imei_sn = bri.imei 
            WHERE bri.bl_sn=bi.sn AND i.good_id=bi.good_id AND i.good_color=bi.good_color_id AND bri.status=1 
            AND bri.imei IN(".$imei_list.") 
            )AS return_qty,
        g.name AS product_name,
        g.desc AS product_detail_name,
        c.name AS product_color
        ,co.old_id
        ,who.name AS old_wh
        ,co.new_id
        ,whn.name AS new_wh
        ,bl.created_date AS borrowing_date,u.staff_code,CONCAT(u.firstname,' ',u.lastname) AS staff_borrowing_name,hrdp.position_name AS staff_position_name,
        bl.operation_director_approved_date,bl.operation_director_approved_by,CONCAT(uo.firstname,' ',uo.lastname) AS director_approve_name,hrdpo.position_name AS director_position_name,
        bl.rm_approved_date,bl.rm_approved_by,CONCAT(urm.firstname,' ',urm.lastname) AS rm_approve_name,hrdprm.position_name AS rm_position_name,
        bl.wms_status,bl.wms_datetime AS finance_confirm_date,s.email AS finance_email,CONCAT(s.firstname,' ',s.lastname) AS finance_name
        FROM borrowing_item bi
        LEFT JOIN borrowing_list bl ON bl.sn=bi.sn
        LEFT JOIN change_sales_order co ON co.borrowing_id=bl.id
        LEFT JOIN good AS g ON g.id = bi.good_id
        LEFT JOIN good_color AS c  ON c.id = bi.good_color_id
        LEFT JOIN warehouse whn ON whn.id = co.new_id
        LEFT JOIN warehouse who ON who.id = co.old_id
        LEFT JOIN oppohr.users u ON u.staff_code=bl.code
        LEFT JOIN oppohr.salary us ON us.staff_id=u.staff_id
        LEFT JOIN oppohr.department_position hrdp ON hrdp.code=us.department_position_code
        LEFT JOIN oppohr.users uo ON uo.staff_code=bl.operation_director_approved_by
        LEFT JOIN oppohr.salary uso ON uso.staff_id=uo.staff_id
        LEFT JOIN oppohr.department_position hrdpo ON hrdpo.code=uso.department_position_code
        LEFT JOIN oppohr.users urm ON urm.staff_code=bl.rm_approved_by
        LEFT JOIN oppohr.salary usrm ON usrm.staff_id=urm.staff_id
        LEFT JOIN oppohr.department_position hrdprm ON hrdprm.code=usrm.department_position_code
        LEFT JOIN staff s ON co.created_by=s.id
        LEFT JOIN borrowing_tran br ON br.bl_sn=bi.sn AND br.status=1
        WHERE 1=1 
        AND br.imei IN(".$imei_list.") 
        GROUP BY bi.sn,g.cat_id,bi.good_id,bi.good_color_id";


        //------------------Return Product
        /* Check Imei Return Product (Group By Distributor) */
        $select_return_by_distributor ="SELECT t.distributor_id,
        t.store_code,
        t.title,
        COUNT(t.imei_sn) AS count_imei,
        sum(t.bgv_price) as sum_bvg_price,
        SUM(t.total_price)AS sum_price
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
        GROUP BY t.distributor_id
        ORDER BY t.distributor_id,t.invoice_number";


        /* Check Imei Return Product (Group By Product) */
        $select_return_by_group ="SELECT t.distributor_id,
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

        /* Check Imei Return Product (Group By Imei)*/
        $select_return_by_imei ="SELECT   
        i.imei_sn,
        dis.id AS distributor_id,
        dis.store_code,
        dis.title,
        COUNT(i.imei_sn) AS bvg_num,
        m.sn as sales_sn,
        m.invoice_number,
        SUM(IFNULL( bi.price ,0)) AS bgv_price,
        ROUND(IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100)-SUM(IFNULL( bi.price ,0)),2)AS sum_unit_price, 
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
        ORDER BY m.d_id,m.invoice_number";

        //echo $select_borrowing_by_request;die;


        $result_borrowing_by_request = $db->fetchAll($select_borrowing_by_request);
        if ($result_borrowing_by_request)
        {

        }

        $result_return_by_distributor = $db->fetchAll($select_return_by_distributor);
        if ($result_return_by_distributor)
        {
            $result_return_by_group = $db->fetchAll($select_return_by_group);
            $result_return_by_imei = $db->fetchAll($select_return_by_imei);
            //echo $select_return_by_imei;die;
        }

        if ($result_return_by_group)
        {
            echo json_encode(array('check'=>1,'result_return_by_distributor'=>$result_return_by_distributor,'result_return_by_group'=>$result_return_by_group,'result_return_by_imei'=>$result_return_by_imei,'result_borrowing_by_request'=>$result_borrowing_by_request,'result_borrowing_by_group'=>$result_borrowing_by_group,'result_borrowing_by_imei'=>$result_borrowing_by_imei));
            exit;
        }

    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        exit;
    }
}

public function checkChangeImeiDistributorAction()
{
     //print_r($_GET);die;
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    //$distributor_id = $this->getRequest()->getParam('distributor_id');
    $imei_list = $this->getRequest()->getParam('imei_list');

    $db = Zend_Registry::get('db');
    $result_phone_data=null;

    try{

        $imei_list_check_imei_locked = str_replace("'","",$imei_list);
        $imei_list_check_imei_locked = explode(',', $imei_list_check_imei_locked);

        // check lock imei
        $QImeiLock = new Application_Model_ImeiLock();
        $getImeiLock = $QImeiLock->checkImeiLock($imei_list_check_imei_locked);

        $QImeiReturn = new Application_Model_ImeiReturn();
        $check_return_on = $QImeiReturn->checkreturnOn($imei_list_check_imei_locked);

        if($check_return_on){
            echo json_encode(array('error' => 'IMEI Return On, Please Check Return To System : ' . $listImeiLock,'check'=>3));
            exit;
        }

        $listImeiLock = '';
        foreach ($getImeiLock as $key => $value) {
            if($key == 0){
                $listImeiLock = $value['imei_log'];
            }else{
                $listImeiLock .= ','. $value['imei_log'];
            }

            $imei_list = str_replace($value['imei_log'],"",$imei_list);
        }
        if($listImeiLock){
            // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
            //     '<br></div>');
            echo json_encode(array('error' => 'IMEI Locked : ' . $listImeiLock,'check'=>3));
            exit;
        }

        /*  Get Phone Data For Save Market */
        $select = $db->select()
        ->from(array('i'=> 'imei'),array('i.warehouse_id','i.distributor_id','g1.cat_id,i.good_id,i.good_color,i.imei_sn,COUNT(DISTINCT i.imei_sn)AS qty,IFNULL(i.out_price,0)AS imei_out_price1
            ','i.sales_sn'))
        ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_sn AND m.good_id=i.good_id AND m.good_color=i.good_color', array('m.sn_ref','m.invoice_number,round(IFNULL(m.total/m.num,0),2) AS imei_out_price'))
        ->joinLeft(array('g1' => 'good'),'g1.id = i.good_id',array())
        ->joinLeft(array('g' => 'good'), 'g.id = m.good_id', 
            array('ROUND(IF(
                (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                )
                -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)- IFNULL(
                (SELECT 
                SUM(IFNULL(price, 0)) 
                FROM
                bvg_imei 
                WHERE imei_sn IN (i.imei_sn) 
                AND `d_id` = m.`d_id` 
                AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
                AND good_id = m.`good_id` 
                AND good_color = m.`good_color`),
                0
                )

                ,2) AS out_price'
                ,'ROUND((
                IF(
                (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                ) 
                -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)
                -IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0)

                )* COUNT(i.imei_sn)

                ,2) AS total_price
                ,m.`spc_discount`,m.`user_id`  
                ,ROUND((((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100),2) AS spc_discount
                ,IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0) AS bvg_price'))
        ->joinLeft(array('bm' => 'bvg_imei'), 'bm.imei_sn = i.imei_sn AND bm.d_id=i.distributor_id AND bm.invoice_number= m.invoice_number COLLATE utf8_unicode_ci', array('bm.imei_sn as bvg_imei_sn'))
        ->joinLeft(array('imp' => 'credit_note_cp_import'), 'imp.imei_sn = i.imei_sn  AND imp.d_id=i.distributor_id  ', array('imp.imei_sn as imp_imei_sn'));

       // ->where('i.warehouse_id = ?', $warehouse_id)
       // $select->where('i.distributor_id in('.$distributor_id.')', null);
        $select->where('i.imei_sn in('.$imei_list.')', null);
        $select->group('m.sn_ref');
        $select->group('i.good_id');
        $select->group('i.good_color');
        $select->order('i.good_id');

       // echo $select;die;
        $result_phone_data = $db->fetchAll($select);

        $select->group('i.imei_sn');
        //echo $select;die;
        $result_phone_imei_data = $db->fetchAll($select);

        if ($result_phone_data)
        {
            echo json_encode(array('check'=>1,'result_market'=>$result_phone_data,'result_imei'=>$result_phone_imei_data));
            exit;
        }

    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        exit;
    }
}

public function checkReturnImeiAutoScanAction()
{
    //print_r($_GET);die;
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    //$distributor_id = $this->getRequest()->getParam('distributor_id');
    $imei_list = $this->getRequest()->getParam('imei_list');

    $db = Zend_Registry::get('db');
    $result_phone_data=null;

    try{

        $imei_list_check_imei_locked = str_replace("'","",$imei_list);
        $imei_list_check_imei_locked = explode(',', $imei_list_check_imei_locked);

        // check lock imei
        $QImeiLock = new Application_Model_ImeiLock();
        $getImeiLock = $QImeiLock->checkImeiLock($imei_list_check_imei_locked);

        $QImeiReturn = new Application_Model_ImeiReturn();
        $check_return_on = $QImeiReturn->checkreturnOn($imei_list_check_imei_locked);

        if($check_return_on){
            echo json_encode(array('error' => 'IMEI Return On, Please Check Return To System : ' . $listImeiLock,'check'=>3));
            exit;
        }

        $listImeiLock = '';
        foreach ($getImeiLock as $key => $value) {
            if($key == 0){
                $listImeiLock = $value['imei_log'];
            }else{
                $listImeiLock .= ','. $value['imei_log'];
            }

            $imei_list = str_replace($value['imei_log'],"",$imei_list);
        }
        if($listImeiLock){
            // exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
            //     '<br></div>');
            echo json_encode(array('error' => 'IMEI Locked : ' . $listImeiLock,'check'=>3));
            exit;
        }

        /*  Get Phone Data For Save Market */
        $select = $db->select()
        ->from(array('i'=> 'imei'),array('i.warehouse_id','i.distributor_id','g.cat_id,i.good_id,i.good_color,i.imei_sn,COUNT(DISTINCT i.imei_sn)AS qty,IFNULL(i.out_price,0)AS imei_out_price1
            ','i.sales_sn'))
        ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_sn AND m.good_id=i.good_id AND m.good_color=i.good_color', array('m.sn_ref','m.invoice_number,round(IFNULL(m.total/m.num,0),2) AS imei_out_price'))
        ->joinLeft(array('g' => 'good'), 'g.id = m.good_id', 
            array('ROUND(IF(
                (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                )
                -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)- IFNULL(
                (SELECT 
                SUM(IFNULL(price, 0)) 
                FROM
                bvg_imei 
                WHERE imei_sn IN (i.imei_sn) 
                AND `d_id` = m.`d_id` 
                AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
                AND good_id = m.`good_id` 
                AND good_color = m.`good_color`),
                0
                )

                ,2) AS out_price'
                ,'ROUND((
                IF(
                (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                ) 
                -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)
                -IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0)

                )* COUNT(i.imei_sn)

                ,2) AS total_price
                ,m.`spc_discount`,m.`user_id`  
                ,ROUND((((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100),2) AS spc_discount
                ,IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0) AS bvg_price'))
        ->joinLeft(array('bm' => 'bvg_imei'), 'bm.imei_sn = i.imei_sn AND bm.d_id=i.distributor_id AND bm.invoice_number= m.invoice_number COLLATE utf8_unicode_ci', array('bm.imei_sn as bvg_imei_sn'))
        ->joinLeft(array('imp' => 'credit_note_cp_import'), 'imp.imei_sn = i.imei_sn  AND imp.d_id=i.distributor_id  ', array('imp.imei_sn as imp_imei_sn'));

       // ->where('i.warehouse_id = ?', $warehouse_id)
       // $select->where('i.distributor_id in('.$distributor_id.')', null);
        $select->where('i.imei_sn in('.$imei_list.')', null);
        $select->group('m.sn_ref');
        $select->group('i.good_id');
        $select->group('i.good_color');
        $select->order('i.good_id');

       // echo $select;die;
        $result_phone_data = $db->fetchAll($select);

        $select->group('i.imei_sn');
        //echo $select;die;
        $result_phone_imei_data = $db->fetchAll($select);

        if ($result_phone_data)
        {
            echo json_encode(array('check'=>1,'result_market'=>$result_phone_data,'result_imei'=>$result_phone_imei_data));
            exit;
        }

    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
        exit;
    }
}

public function checkReturnImeiAction()
{
    //print_r($_GET);die;
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $imei_list = $this->getRequest()->getParam('imei_list');

    $db = Zend_Registry::get('db');

    try{

        $imei_list_check_imei_locked = str_replace("'","",$imei_list);
        $imei_list_check_imei_locked = explode(',', $imei_list_check_imei_locked);

        // check lock imei
        $QImeiLock = new Application_Model_ImeiLock();
        $getImeiLock = $QImeiLock->checkImeiLock($imei_list_check_imei_locked);

        $listImeiLock = '';
        foreach ($getImeiLock as $key => $value) {
            if($key == 0){
                $listImeiLock = $value['imei_log'];
            }else{
                $listImeiLock .= '<br>'. $value['imei_log'];
            }

            $imei_list = str_replace($value['imei_log'],"",$imei_list);
        }
        // if($listImeiLock){
        //     exit('<div class="alert alert-danger">IMEI Locked<br>' . $listImeiLock .
        //         '<br></div>');
        //     echo json_encode(array('error' => 'IMEI Locked<br>' . $listImeiLock .
        //         '<br>'));
        //     exit;
        // }

        $select_phone = $db->select()
        ->from(array('i'=> 'imei_return'),array('i.imei_sn'))
        ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_order_sn', array('m.sn_ref','m.invoice_number'))
        ->where('m.d_id = ?', $distributor_id)
        ->where('i.imei_sn in('.$imei_list.')', null)
        ->where('i.`status`= 0 ', null)
        ->group('i.imei_sn');

        //echo $select_phone;die;exit;

        $result_phone = $db->fetchAll($select_phone);
        if ($result_phone)
        {
            //echo $select;exit;    
            echo json_encode(array('check'=>2,'result'=>$result_phone));
            exit;
        }
    }catch(exception $e)
    {
        echo json_encode(array('check'=>2,'result'=>$result_phone));
        exit;
    }

    try{
        $select_digital = $db->select()
        ->from(array('i'=> 'digital_sn_return'),array('i.sn'))
        ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_order_sn', array('m.sn_ref','m.invoice_number'))
        ->where('m.d_id = ?', $distributor_id)
        ->where("i.sn in(".$imei_list.")", null)
        ->where('i.`status`= 0 ', null)
        ->group('i.sn');

        //echo $select_digital;die;
       // exit;
        $result_digital = $db->fetchAll($select_digital);

        if ($result_digital)
        {
            //echo $select;exit;    
            echo json_encode(array('check'=>2,'result'=>$result_digital));
            exit;
        }

    }catch(exception $e)
    {
        echo json_encode(array('check'=>2,'result'=>$result_digital));
        exit;
    }

    try{

        $QDistributor = new Application_Model_Distributor();
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $distributor_id);
        $distributor = $QDistributor->fetchRow($where);

        if ($rank_type && $rank_type == My_Finance_Type::CREDIT && $distributor['rank'] != 14) // trừ VTA
            $rank = $distributor['rank'] * 100; // căn cứ theo ID rank thường tương ứng, nhân lên 100 ra ID rank công nợ
            else
                $rank = $distributor['rank'];

        if (in_array($type, array(FOR_DEMO, FOR_STAFFS))) //for demo; for staffs
            $rank = 3; //retail price

            $price_rank = 'g.price_'.$rank;

            /*  Get Phone Data  */
            $select_phone_data = $db->select()
            ->from(array('i'=> 'imei'),array('i.warehouse_id','i.distributor_id','m.cat_id,i.good_id,i.good_color,i.imei_sn,COUNT(DISTINCT i.imei_sn)AS qty,IFNULL(i.out_price,0)AS imei_out_price1
                ','i.sales_sn'))
            ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_sn AND m.good_id=i.good_id AND m.good_color=i.good_color', array('m.sn_ref','m.invoice_number,round(IFNULL(m.total/m.num,0),2) AS imei_out_price'))
            ->joinLeft(array('g' => 'good'), 'g.id=m.good_id', 
                array('ROUND(IF(
                    (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                    IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                    )
                    -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)- IFNULL(
                    (SELECT 
                    SUM(IFNULL(price, 0)) 
                    FROM
                    bvg_imei 
                    WHERE imei_sn IN (i.imei_sn) 
                    AND `d_id` = m.`d_id` 
                    AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
                    AND good_id = m.`good_id` 
                    AND good_color = m.`good_color`),
                    0
                    )

                    ,2) AS out_price'
                    ,'ROUND((
                    IF(
                    (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
                    IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
                    ) 
                    -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)
                    -IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0)

                    )* COUNT(i.imei_sn)

                    ,2) AS total_price'))
            ->joinLeft(array('bm' => 'bvg_imei'), 'bm.imei_sn = i.imei_sn AND bm.d_id=i.distributor_id AND bm.invoice_number= m.invoice_number COLLATE utf8_unicode_ci', array('bm.imei_sn'))
            ->joinLeft(array('imp' => 'credit_note_cp_import'), 'imp.imei_sn = i.imei_sn  AND imp.d_id=i.distributor_id  ', array('imp.imei_sn'))

       // ->where('i.warehouse_id = ?', $warehouse_id)
            ->where('i.distributor_id = ?', $distributor_id)
            ->where('i.imei_sn in('.$imei_list.')', null)
            ->group('m.sn_ref')
            ->group('i.good_id')
            ->group('i.good_color')
            ->order('i.good_id');

        //echo $select_phone_data;die;
            $result_phone_data = $db->fetchAll($select_phone_data);

            if ($result_phone_data)
            {
                echo json_encode(array('check'=>1,'result'=>$result_phone_data));
                exit;
            }

            /*  Get Digital Data  */
            $select_digital_data = $db->select()
            ->from(array('i'=> 'digital_sn'),array('m.warehouse_id','i.distributor_id',"m.cat_id",'m.good_id','m.good_color','i.sn as imei_sn','COUNT(DISTINCT i.sn)AS qty,IFNULL(i.out_price,0)AS imei_out_price1
                ','i.sales_sn'))
            ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_sn AND m.good_id=i.good_id AND m.good_color=i.good_color and m.cat_id=13', array('m.sn_ref','m.invoice_number,round(IFNULL(m.total/m.num,0),2) AS imei_out_price'))
            ->joinLeft(array('g' => 'good'), 'g.id=m.good_id', 
                array('ROUND(
                    IFNULL(m.total / m.num, 0)  - (
                    ((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100) ,
                    2
                ) AS out_price'
                ,'  ROUND(
                (
                IFNULL(m.total / m.num, 0) - (((m.total / m.num) * IFNULL(m.spc_discount, 0)) / 100) 
                ) * COUNT(i.sn),
                2
            ) AS total_price'))



       // ->where('i.warehouse_id = ?', $warehouse_id)
            ->where('i.distributor_id = ?', $distributor_id)
            ->where('i.sn in('.$imei_list.')', null)
            ->group('m.sn_ref')
            ->group('i.good_id')
            ->group('i.good_color')
            ->order('i.good_id');

        //echo $select_digital_data;exit;
            $result_digital_data = $db->fetchAll($select_digital_data);

            if ($result_digital_data)
            {
                echo json_encode(array('check'=>1,'result'=>$result_digital_data));
                exit;
            } else {
                echo json_encode(array());
                exit;
            }

        }catch(exception $e)
        {
            echo json_encode(array('error' => $e->getMessage()));
            exit;
        }
    }


    public function distributorCachedAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

        $QDistributor     = new Application_Model_Distributor();
        $distributorCached = $QDistributor->get_with_retailer_code_cache();

        if (!$distributorCached) {
            $response
            ->appendBody(json_encode(array('no_data' => 1)))
            ->sendResponse();
            exit;
        }

        $response
        ->appendBody(json_encode($distributorCached))->sendResponse();
        exit;
    }

    public function distributorCachedForSearchAction()
    {
        $this->_helper->layout->disableLayout();
    // $this->_helper->viewRenderer->setNoRender(true);
        $rank_id = $this->getRequest()->getParam('rank_id');

        $QDistributor     = new Application_Model_Distributor();
        $distributorCached = $QDistributor->get_with_retailer_for_search($rank_id);

        $this->view->response = $distributorCached;

    }

    public function wareHouseNameListAction()
    {
        $this->_helper->layout->disableLayout();
        $warehouse_type_id = $this->getRequest()->getParam('warehouse_type_id');
    // echo $warehouse_type_id;die;   
        $QWarehouse     = new Application_Model_Warehouse();
        $where = $QWarehouse->getAdapter()->quoteInto('id = ?', $warehouse_type_id);
        $data = $QWarehouse->fetchAll();
    //print_r($data);
    //exit;

        $result = array();
        if ($data){
            foreach ($data as $item){
               $result[$item['id']] = $item['name'];                        

           }
       }
       if (!$result) {
        print_r(json_encode(array('no_data' => 1)));    
        exit;
    }
    print_r(json_encode($result));
    exit;
}

/**
 * Lấy các region theo area_id, dùng cho combobox area/region
 */
public function regionAction()
{
    if (!$this->getRequest()->isXmlHttpRequest())
        exit;

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $area_id = $this->getRequest()->getParam('area_id');

    $QRegion = new Application_Model_RegionalMarket();
    $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area_id);
    $regions = $QRegion->fetchAll($where, 'name');
    $regions_arr = array();

    foreach ($regions as $k => $region)
    {
        $regions_arr[] = array(
            'id' => $region['id'],
            'name' => $region['name'],
        );
    }

    echo json_encode($regions_arr);
    exit;
}

/**
 * Lấy các province theo area_id, dùng cho combobox area/region
 */
public function provinceV2Action()
{
    if (!$this->getRequest()->isXmlHttpRequest())
        exit;

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

    $area_id = $this->getRequest()->getParam('area_id');

    if (!$area_id) {
        $response
        ->appendBody(json_encode(array()))
        ->sendResponse();
        exit;
    }

    $QRegion = new Application_Model_RegionalMarket();

    $provinces = array();
    if (is_array($area_id) && count($area_id)) {
        foreach ($area_id as $_key => $_id)
            $provinces += $QRegion->nget_province_by_area_cache($_id);
    } elseif (is_numeric($area_id) && $area_id) {
        $provinces = $QRegion->nget_province_by_area_cache($area_id);
    }

    $response
    ->appendBody(json_encode($provinces))
    ->sendResponse();
    exit;
}

/**
 * Lấy các district theo province id, dùng cho combobox area/region
 */
public function districtV2Action()
{
    if (!$this->getRequest()->isXmlHttpRequest())
        exit;

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

    $province_id = $this->getRequest()->getParam('province_id');

    if (!$province_id) {
        $response
        ->appendBody(json_encode(array()))
        ->sendResponse();
        exit;
    }

    $QRegion = new Application_Model_RegionalMarket();
    $districts = array();
    if (is_array($province_id) && count($province_id)) {
        foreach ($province_id as $_key => $_id)
            $districts += $QRegion->nget_district_by_province_cache($_id);
    } elseif (is_numeric($province_id) && $province_id) {
        $districts = $QRegion->nget_district_by_province_cache($province_id);
    }

    $response
    ->appendBody(json_encode($districts))
    ->sendResponse();
    exit;
}

/**
 * Lấy các region theo area_id, dùng cho combobox area/region
 */
public function districtAction()
{
    if (!$this->getRequest()->isXmlHttpRequest())
        exit;

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $region = $this->getRequest()->getParam('region');

    $QRegion = new Application_Model_RegionalMarket();
    $where = $QRegion->getAdapter()->quoteInto('parent = ?', $region);
    $districts = $QRegion->fetchAll($where, 'name');
    $regions_arr = array();

    foreach ($districts as $k => $region)
    {
        $regions_arr[] = array(
            'id' => $region['id'],
            'name' => $region['name'],
        );
    }

    echo json_encode($regions_arr);
    exit;
}

public function loadWarehouseAction()
{
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $QDistributor = new Application_Model_Distributor();


    if ($warehouse_id)
    {

        $where = array();
        $where[] = $QDistributor->getAdapter()->quoteInto('warehouse = ?', $warehouse_id);
        $where[] = $QDistributor->getAdapter()->quoteInto('del = ?', 0);
        $goods = $QDistributor->fetchAll($where, 'title');

        echo json_encode(array('goods' => $goods->toArray()));
        exit;
    }

    echo json_encode(array());
    exit;
}
function array_delete($del_val, $array) {
    if(is_array($del_val)) {
     foreach ($del_val as $del_key => $del_value) {
        foreach ($array as $key => $value){
            if ($value == $del_value) {
                unset($array[$key]);
            }
        }
    }
} else {
    foreach ($array as $key => $value){
        if ($value == $del_val) {
            unset($array[$key]);
        }
    }
}
return array_values($array);
} 
public function load2Action()
{
$cat_id         = $this->getRequest()->getParam('cat_id');
$good_id        = $this->getRequest()->getParam('good_id');
$d_id           = $this->getRequest()->getParam('d_id');
$warehouse_id   = $this->getRequest()->getParam('warehouse_id');
$rank           = $this->getRequest()->getParam('rank');
$type           = $this->getRequest()->getParam('type');

$QGood          = new Application_Model_Good();
$QGoodHoldAll   = new Application_Model_GoodHoldAll();
$QGoodHoldDis   = new Application_Model_GoodHoldDis(); 
if ($good_id) {
    $where      = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
    $good       = $QGood->fetchRow($where);
    $resultAll  = $QGoodHoldAll->CheckHoldAll($good_id,$warehouse_id);
    $resultHold = $QGoodHoldDis->CheckHoldDis($good_id,$d_id,$warehouse_id);
    
    if ($good)
    {
        $aColor = array_filter(explode(',', $good->color));
        $countt =$good->id;

        $QGoodColor = new Application_Model_GoodColor();
        $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

        $QDiscount = new Application_Model_Discount();


        $where2 = array();
        $where2[] = $QDiscount->getAdapter()->quoteInto('good_id IN (?)',array(0,$countt));
        if($type == 5){
            $where2[] = $QDiscount->getAdapter()->quoteInto('name NOT IN (?)',array('Staff','Normal'));
        }elseif($type == 3){
            $where2[] = $QDiscount->getAdapter()->quoteInto('name NOT IN (?)',array('DEMO','Normal','APK For LTH'));
        }else{
            $where2[] = $QDiscount->getAdapter()->quoteInto('name NOT IN (?)',array('DEMO','Staff','APK For LTH'));
        }



        $colors = $QGoodColor->fetchAll($where);
        $discount = $QDiscount->fetchAll($where2);
                // echo $discount; die;
        echo json_encode(array('colors' => $colors->toArray(),'discount' => $discount->toArray()));
        exit;
    }

} elseif ($cat_id) {

    $QGood = new Application_Model_Good();
    $goods = $QGood->getProduct($cat_id,$warehouse_id);

    echo json_encode(array('goods' => $goods));
    exit;

} else {

    echo json_encode(array());
    exit;
}
}


public function loadAction()
{
$cat_id = $this->getRequest()->getParam('cat_id');
$good_id = $this->getRequest()->getParam('good_id');
$brand_id = $this->getRequest()->getParam('brand_id');

$QGood = new Application_Model_Good();

if ($good_id) {
        $where = $QGood->getAdapter()->quoteInto('id IN (?)', $good_id);
        $good = $QGood->fetchAll($where);

        if ($good)
        {
            $aColor = array();
            foreach($good as $key => $value) {
                $aColor[] = array_filter(explode(',', $value->color));
            }

            if ($aColor)
            {
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                $colors = $QGoodColor->fetchAll($where);
                echo json_encode(array('colors' => $colors->toArray()));
                exit;
            }
        }
    }

    $goods = $QGood->getProductBrand($brand_id,$cat_id);

    echo json_encode(array('goods' => $goods),true); exit;

// Old
// } elseif ($cat_id) {

//     if($cat_id ==11){

//         $goods = $QGood->getGoodRecordByCategory($cat_id);

        // $goods = $QGood->fetchAll(
        //     $QGood->select()
        //     ->where('cat_id = ?', $cat_id)
        //     ->where('del = ?', 0)
        //     ->order('add_time DESC')
        //     , 'name');

        // echo json_encode(array('goods' => $goods),true); exit;

    // }else{
        // $where = array();
        // $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
        // $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
        // $goods = $QGood->fetchAll($where, 'name');

        // echo json_encode(array('goods' => $goods->toArray())); exit;
    // }


// } else {

//     echo json_encode(array());
//     exit;
// }
}

public function loadProductStockAction()
{
$cat_id = $this->getRequest()->getParam('cat_id');

if($this->getRequest()->getParam('good_id')=="all"){
    $good_id="";
}else{
    $good_id = $this->getRequest()->getParam('good_id'); 
}

$QGood = new Application_Model_Good();

if ($good_id) {
    $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
    $good = $QGood->fetchRow($where);

    if ($good)
    {
        $aColor = array_filter(explode(',', $good->color));
        if ($aColor)
        {
            $QGoodColor = new Application_Model_GoodColor();
            $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

            $colors = $QGoodColor->fetchAll($where);
            echo json_encode(array('colors' => $colors->toArray()));
            exit;
        }
    }

} elseif ($cat_id) {
    if($cat_id ==11){
/*                $goods = $QGood->fetchAll(
            $QGood->select()
            ->where('cat_id = ?', $cat_id)
            ->where('del = ?', 0)
            ->order('add_time DESC')
            , 'name');*/

            /*$where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);

            $goods = $QGood->fetchAll($where, 'name');*/


            //$select =" select name from good where cat_id='11' and del=0 and name in((SELECT good_code FROM stock_product_in_purchase GROUP BY good_code))";
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);

            $where[] = $QGood->getAdapter()->quoteInto('name in((SELECT good_code FROM stock_product_in_purchase GROUP BY good_code))',null);

            $goods = $QGood->fetchAll($where,"name");


        }else{
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
            $goods = $QGood->fetchAll($where, 'name');
        }    
        
        echo json_encode(array('goods' => $goods->toArray()));
        exit;
    } else {
        echo json_encode(array());
        exit;
    }

    exit;
}

public function loadCustomerBrandShopAction()
{
    $customer_id = $this->getRequest()->getParam('customer_id');

    $QMarket = new Application_Model_Market();

    try{

        $result = $QMarket->getCustomerBrandShop($customer_id);

        if ($result)
        {

            echo json_encode(array('status'=>1,'result'=>$result));
            exit;
        } else {
            echo json_encode(array());
            exit;
        }
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }
}

/**
 *
 */
public function loadJointAction()
{
    $joint = $this->getRequest()->getParam('joint');

    $good_id = $this->getRequest()->getParam('good_id');

    $distributor_id = $this->getRequest()->getParam('distributor_id');

    $init_params = $this->getRequest()->getParam('init_params');

    $QGood = new Application_Model_Good();

    $QBVG_Product = new Application_Model_BvgProduct();

    $QMarketProduct = new Application_Model_MarketProduct();

    $QBvgImei = new Application_Model_BvgImei();



    if(!empty($init_params) and $init_params)
    {

        try{
            if(empty($distributor_id))
                exit(json_encode(array('error' => 'Distributor is not existed.')));

            switch($init_params)
            {
                case 1:
                $result = $QBvgImei->fetchJointexist($distributor_id);
                break;
                case 2:
                $result = $QMarketProduct->fetchJointDicountexist($distributor_id);
                break;
                case 3:
                $result = $QMarketProduct->fetchJointAccessories($distributor_id , $joint , $good_id);
                break;
            }



            if(empty($result))
                exit(json_encode(array('error' => 'Distributor is don\'t have joint circular')));

            if($result['code'] != 1)
            {
                throw new exception('Distributor is don\'t have joint circular');
            }

            $list_joint_exist = $result['data'];





            echo json_encode($list_joint_exist);
            exit;

        }
        catch(exception $e)
        {
            echo json_encode(array('error' => $e->getMessage()));

            exit;
        }


    }

    if ($joint)
    {

        if ($joint and $good_id)
        {

            $where = array();

            $where[] = $QBVG_Product->getAdapter()->quoteInto('joint_id = ?', $joint);

            $where[] = $QBVG_Product->getAdapter()->quoteInto('good_id = ?', $good_id);

            $goods = $QBVG_Product->fetchRow($where);

            $where_bvg_imei = array();
            $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('good_id = ?' ,$good_id);
            $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?' , $joint );
            $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('d_id = ?' , $distributor_id );
            $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('bvg_payment_confirmed_at is null' , null);

            $result = $QBvgImei->fetchAll($where_bvg_imei);


            $total  = count($result);


            $data = array(
                'price' => $goods['price'],
                'total_price' => intval($goods['price'] * $total),
                'total' => $total
            );

            echo json_encode($data);

            exit;

        } else
        {
            $list_good_exist = array();

            $where   = array();
            $where[] = $QBVG_Product->getAdapter()->quoteInto('joint_id = ?', $joint);
            $goods   =  $QBVG_Product->fetchAll($where, 'good_id');



            foreach($goods as $k => $item)
            {
                $where_bvg_imei = array();

                $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('good_id = ?' , $item['good_id']);
                $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('joint_circular_id = ?' , $joint);
                $where_bvg_imei[] = $QBvgImei->getAdapter()->quoteInto('d_id = ? ' , $distributor_id);


                $result = $QBvgImei->fetchRow($where_bvg_imei);

                if(!empty($result))
                    $list_good_exist[] = $item['good_id'];
            }

            $goods = $goods->toArray();

            $where = $QGood->getAdapter()->quoteInto('id IN (?)', $list_good_exist);

            $goods = $QGood->fetchAll($where);

            echo json_encode(array('goods' => $goods->toArray()));

            exit;
        }


    }
    echo json_encode(array());
    exit;
}

public function loadJointtypeAction()
{
    $joint_type = $this->getRequest()->getParam('joint_type');
    $QJointCircular = new Application_Model_JointCircular();
    if(isset($joint_type) and $joint_type)
    {
        $where = array();
        $where[] = $QJointCircular->getAdapter()->quoteInto('type = ?' , $joint_type);
        $where[] = $QJointCircular->getAdapter()->quoteInto('status = 1' , null);
        $joint = $QJointCircular->fetchAll($where);

        echo json_encode(array('joint' => $joint->toArray()));

        exit;
    }
    else
    {
        echo json_encode(array());
        exit;
    }
}

public function loadJointpriceAction()
{
    $joint_id = $this->getRequest()->getParam('joint');
    $d_id  = $this->getRequest()->getParam('d_id');
    $QJointCircular = new Application_Model_JointCircular();
    $QMarketDeduction = new Application_Model_MarketDeduction();
    $QMarketProduct =  new Application_Model_MarketProduct();

    if (!$this->getRequest()->isXmlHttpRequest())
    {
        exit(json_encode(array('code' => '-100', 'error' => 'Require AJAX request.')));
    }

    $joint_rowset  = $QJointCircular->find($joint_id);
    $joint = $joint_rowset->current();

    if (!$joint)
    {
        exit(json_encode(array('code' => '-2', 'error' => 'Joint is not existed.')));
    }

    if (!$d_id)
    {
        exit(json_encode(array('code' => '-2', 'error' => 'Distributor is not existed.')));
    }

    $params = array(
        'joint_circular_id' => $joint_id,
        'd_id' => $d_id,
        'type' => $joint['type']
    );



    $max_price = $QMarketDeduction->getMaxPrice($params);


    exit(json_encode(array('code' => '1', 'price' => $max_price)));
}

public function loadPriceAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $num = $this->getRequest()->getParam('num');
    $good_id = $this->getRequest()->getParam('good_id');
    $good_color = $this->getRequest()->getParam('good_color');
    $cat_id = $this->getRequest()->getParam('cat_id');
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $is_sales_price = $this->getRequest()->getParam('is_sales_price');
    
    $is_return = $this->getRequest()->getParam('is_return');
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $type = $this->getRequest()->getParam('type');
    $id = $this->getRequest()->getParam('id');
    $campaign_id = $this->getRequest()->getParam('campaign_id' , 0);

    $QGood = new Application_Model_Good();

    //Kiểm tra có chọn lô hàng ko nếu chọn lô hàng thì lấy giá trong lô hàng, nếu ko chọn lô hàng(shipment) thì tính bình thường
    $id_shipment = $this->getRequest()->getParam('id_shipment');
    $type_shipment = $this->getRequest()->getParam('type_shipment');

    //return echo $id_shipment;
    // For Demo
    if($id_shipment){
        $result = $QGood->get_price_shipment($num, $good_id, $id_shipment, $type_shipment,$cat_id);
    }
    else{
        $result = $QGood->get_price($num, $good_id, $good_color, $cat_id, $distributor_id,
            $warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
    }

    // print_r($warehouse_id); exit;

    
    switch ($result['code'])
    {
        case 1:
        echo $result['price'];
        break;
        case 0:
        echo '0';
        break;
        case - 1:
        echo '-1';
        break;
        case - 2:
        echo '-2|' . $result['quantity'];
        break;
        case - 3:
        echo '-3|' . $result['quantity'];
        break;
	case - 5:
        echo '-5|';
        break;
        case -6:
        echo '-6|';
        break;

        default:
        echo '0';
        break;
    }

    exit;
}

public function loadPriceReturnAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $num = $this->getRequest()->getParam('num');
    $good_id = $this->getRequest()->getParam('good_id');
    $good_color = $this->getRequest()->getParam('good_color');
    $cat_id = $this->getRequest()->getParam('cat_id');
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $is_sales_price = $this->getRequest()->getParam('is_sales_price');
    
    $is_return = $this->getRequest()->getParam('is_return');
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $type = $this->getRequest()->getParam('type');
    $id = $this->getRequest()->getParam('id');
    $campaign_id = $this->getRequest()->getParam('campaign_id' , 0);

    $QGood = new Application_Model_Good();

    //Kiểm tra có chọn lô hàng ko nếu chọn lô hàng thì lấy giá trong lô hàng, nếu ko chọn lô hàng(shipment) thì tính bình thường
    $id_shipment = $this->getRequest()->getParam('id_shipment');
    $type_shipment = $this->getRequest()->getParam('type_shipment');

    //return echo $id_shipment;
    // For Demo
    if($id_shipment){
        $result = $QGood->get_price_shipment($num, $good_id, $id_shipment, $type_shipment,$cat_id);
    }
    else{

        $result = $QGood->get_return_price($num, $good_id, $good_color, $cat_id, $distributor_id,
            $warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
        
        /*if($result['price']<=0){
            $result = $QGood->get_price($num, $good_id, $good_color, $cat_id, $distributor_id,
            $warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
        }*/
    }


    switch ($result['code'])
    {
        case 1:
        echo $result['price'];
        break;
        case 0:
        echo '0';
        break;
        case - 1:
        echo '-1';
        break;
        case - 2:
        echo '-2|' . $result['quantity'];
        break;
        case - 3:
        echo '-3|' . $result['quantity'];
        break;

        default:
        echo '0';
        break;
    }

    exit;
}



public function loadPriceStockAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $num = $this->getRequest()->getParam('num');
    $good_id = $this->getRequest()->getParam('good_id');
    $good_color = $this->getRequest()->getParam('good_color');
    $cat_id = $this->getRequest()->getParam('cat_id');
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $is_sales_price = $this->getRequest()->getParam('is_sales_price');
    $is_return = $this->getRequest()->getParam('is_return');
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $type = $this->getRequest()->getParam('type');
    $id = $this->getRequest()->getParam('id');
    $campaign_id = $this->getRequest()->getParam('campaign_id' , 0);

    $QGood = new Application_Model_Good();

    //Kiểm tra có chọn lô hàng ko nếu chọn lô hàng thì lấy giá trong lô hàng, nếu ko chọn lô hàng(shipment) thì tính bình thường
    $id_shipment = $this->getRequest()->getParam('id_shipment');
    $type_shipment = $this->getRequest()->getParam('type_shipment');
    if($id_shipment){
        $result = $QGood->get_price_shipment($num, $good_id, $id_shipment, $type_shipment,$cat_id);
    }
    else{

        $result = $QGood->get_price_stock($num, $good_id, $good_color, $cat_id, $distributor_id,
            $warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);
    }

    echo $result['price'];

    exit;
}

public function checkAction()
{

    $id = $this->getRequest()->getParam('id');
    $username = $this->getRequest()->getParam('username');
    $email = $this->getRequest()->getParam('email');

    $where = array();
    $QStaff = new Application_Model_Staff();

    if ($username)
        $where[] = $QStaff->getAdapter()->quoteInto('username = ?', $username);

    if ($email)
        $where[] = $QStaff->getAdapter()->quoteInto('email = ?', $email);

    if (sizeof($where) == 0)
    {
        echo "-1";
        exit;
    }

    /*$where[] = $QStaff->getAdapter()->quoteInto('off_date IS NULL ', null);*/

    $where[] = $QStaff->getAdapter()->quoteInto('id != ?', intval($id));

    $staffs = $QStaff->fetchAll($where);

    if (isset($staffs[0]))
        echo '1';
    else
        echo '0';

    exit;
}

public function priceAction()
{
    $price = $_POST['price'];
    if (isset($price))
    {
        $symbol = ' VND';
        $symbol_thousand = '.';
        $decimal_place = 0;
        //$priceFloat=round($priceFloat, 0, PHP_ROUND_HALF_UP);
        $price = number_format($price, $decimal_place, ',', $symbol_thousand);
        echo $price;
        exit;
    }
}

public function pricenameAction()
{
    $amount = $_POST['id'];

    if ($amount <= 0)
    {
        return $textnumber = "";
    }
    $Text = array(
        "không",
        "một",
        "hai",
        "ba",
        "bốn",
        "năm",
        "sáu",
        "bảy",
        "tám",
        "chín");
    $TextLuythua = array(
        "",
        "nghìn",
        "triệu",
        "tỷ",
        "ngàn tỷ",
        "triệu tỷ",
        "tỷ tỷ");
    $textnumber = "";
    $length = strlen($amount);

    for ($i = 0; $i < $length; $i++)
        $unread[$i] = 0;

    for ($i = 0; $i < $length; $i++)
    {
        $so = substr($amount, $length - $i - 1, 1);

        if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0))
        {
            for ($j = $i + 1; $j < $length; $j++)
            {
                $so1 = substr($amount, $length - $j - 1, 1);
                if ($so1 != 0)
                    break;
            }

            if (intval(($j - $i) / 3) > 0)
            {
                for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                    $unread[$k] = 1;
            }
        }
    }

    for ($i = 0; $i < $length; $i++)
    {
        $so = substr($amount, $length - $i - 1, 1);
        if ($unread[$i] == 1)
            continue;

        if (($i % 3 == 0) && ($i > 0))
            $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;

        if ($i % 3 == 2)
            $textnumber = 'trăm ' . $textnumber;

        if ($i % 3 == 1)
            $textnumber = 'mươi ' . $textnumber;


        $textnumber = $Text[$so] . " " . $textnumber;
    }

    //Phai de cac ham replace theo dung thu tu nhu the nay
    $textnumber = str_replace("không mươi", "lẻ", $textnumber);
    $textnumber = str_replace("lẻ không", "", $textnumber);
    $textnumber = str_replace("mươi không", "mươi", $textnumber);
    $textnumber = str_replace("một mươi", "mười", $textnumber);
    $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
    $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
    $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

    echo ucfirst($textnumber);
    exit;
}

public function bvgAction()
{
    $sales_sn = $this->getRequest()->getParam('sales_sn');

    if (!$sales_sn)
    {
        echo '-1'; //sales sn error
        exit;
    }

    $total_debate = $this->getRequest()->getParam('total_debate');
    $num = $this->getRequest()->getParam('num');
    $price = $this->getRequest()->getParam('price');
    $total = $this->getRequest()->getParam('total');

    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn);

    $market = $QMarket->fetchRow($where);

    if (!$market)
    {
        echo '-1'; //sales sn error
        exit;
    }

    $QMarketBVG = new Application_Model_MarketBvg();
    $invoice_price = $this->getRequest()->getParam('invoice_price');

    $log = 'Số lượng thay đổi : ' . $num . ' - ';
    $log = $log . 'Giá tiền : ' . $price . ' - ';
    $log = $log . 'tổng số tiền : ' . $total;


    try
    {
        $where = $QMarketBVG->getAdapter()->quoteInto('market_id = ?', $sales_sn);
        $m = $QMarketBVG->fetchRow($where);

        if ($m)
        {
            $QMarketBVG->update(array(
                'market_id' => $sales_sn,
                'deduction' => $total,
                'log' => $log,
            ), $where);
        } else
        {
            $QMarketBVG->insert(array(
                'market_id' => $sales_sn,
                'deduction' => $total,
                'log' => $log,
            ), $where);
        }


        echo '0';
        exit;
    }

    catch (exception $e)
    {
        echo '-4';
        exit;
    }
}

/*Dung de luu so hoa don khi in hoa don laser*/
public function laserSaveAction()
{
    $invoice_number = $this->getRequest()->getParam('invoice_number');
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $invoice_sign = $this->getRequest()->getParam('prefix', '');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $db = Zend_Registry::get('db');
    //update
    $db->beginTransaction();


    if(!$invoice_number)
    {
        echo -1;
        exit;
    }

    if(!$invoice_sign)
    {
        echo -1;
        exit;
    }

    if(!$sales_sn)
    {
        echo -2;
        exit;
    }



    $QMarket = new Application_Model_Market();
    $QInvoiceNumber = new Application_Model_InvoiceNumber();
    $QLog    = new Application_Model_InvoiceLog();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn);
    $market = $QMarket->fetchRow($where);
    $warehouse_id = $market['warehouse_id'];

    $date = $this->getRequest()->getParam('date');
    $change_invoice = 0;


    $whereInvoiceNumber   = array();
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign = ?' , $invoice_sign);
    $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

    if($exist_invoice_number and $sales_sn != $exist_invoice_number['sn'] and $market['campaign'] != 99)
    {
        echo '-6';
        exit;
    }

    if (!$market)
    {
        echo '-2'; //sales sn error
        exit;
    }

    if(!$warehouse_id)
    {
        echo '-2'; //sales sn error
        exit;
    }

    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);

    if (isset($invoice_number))
    {

        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_sign = ?', $invoice_sign);
        $where[] = $QMarket->getAdapter()->quoteInto('sn <> ?', $sales_sn);

        $duplicate = $QMarket->fetchRow($where);


        if ($duplicate)
        {
            echo '-3';
            exit;
        }
    }




    $total_invoice_price = $this->getRequest()->getParam('total_invoice_price');
    $total_invoice_vat = $this->getRequest()->getParam('total_invoice_vat');
    $total_invoice_after_vat = $this->getRequest()->getParam('total_invoice_after_vat');
    $invoice_price = $this->getRequest()->getParam('invoice_price');


    $QMarketInvoicePrice = new Application_Model_MarketInvoicePrice();

    if (isset($total_invoice_after_vat) and $total_invoice_after_vat and isset($total_invoice_price) and
        $total_invoice_price and isset($total_invoice_vat) and $total_invoice_vat)
    {

        $QMarketInvoicePriceSn = new Application_Model_MarketInvoicePriceSn();
        $where = array();
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('sn = ?', $sales_sn);
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $m = $QMarketInvoicePriceSn->fetchRow($where);

        if ($m)
        {
            $QMarketInvoicePriceSn->update(array(
                'total_invoice_price' => $total_invoice_price,
                'total_invoice_vat' => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number' => trim($invoice_number)), $where);
        } else
        {
            $QMarketInvoicePriceSn->insert(array(
                'sn' => $sales_sn,
                'total_invoice_price' => $total_invoice_price,
                'total_invoice_vat' => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number' => trim($invoice_number)));
        }
    }

    try
    {

        $date = date('Y-m-d h:i:s');

        //tim so hoa don da co trong he thong chua

        $whereInvoiceNumber = array();
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('sn = ?' , $sales_sn);
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
        $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);



        if(isset($exist_invoice_number) and $exist_invoice_number)
        {

            $data = array(
                'copy' => intval($exist_invoice_number['copy'] + 1),
                'updated_at' => $date
            );
            $QInvoiceNumber->update($data , $whereInvoiceNumber);
        }
        else{
            $last_invoice_number = $QInvoiceNumber->getLastId($warehouse_id);

            if($invoice_number != $last_invoice_number)
                throw new exception('invoice number is invalid');

            $data = array('invoice_number' => $invoice_number,
                'date'         => $date ,
                'invoice_sign' => $invoice_sign,
                'sn'           => $sales_sn,
            );
            //them so invoice vao he thong
            $id = $QInvoiceNumber->insert($data);

        }

        // cap nhat so hoa don vao don hang
        $data = array('invoice_number' => $invoice_number, 'invoice_sign' => $invoice_sign , 'invoice_time' => $date);
        $QMarket->update($data, $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn));

        //them log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $info = 'Print Invoice : Sale order number: ' . $sales_sn . ' Invoice number : ' . $invoice_number . ' Copy : ' . $exist_invoice_number['copy'];

        $QLog->insert(array(
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => $date,
        ));

        $db->commit();

        echo '0';
        exit;
    }
    catch (exception $e)
    {

        $db->rollback();
        echo -4;
        exit;
    }



}

public function invoiceServiceAction()
{

    try{
        $service           = $this->getRequest()->getParam('service');
        $invoice_prefix_id = $this->getRequest()->getParam('invoice_prefix');

        // $service_object = $this->getRequest()->getParam('service_object');
        // $warehouse = 1;

        $QInvoicePrefix = new Application_Model_InvoicePrefix();
        $invoicePrefix  = $QInvoicePrefix->find($invoice_prefix_id);
        $invoice_prefix = $invoicePrefix->current();

        $warehouse      = $invoice_prefix['warehouse_id'];


        if(!$service)
        {
            exit(json_encode((array(
                'code' => -2,
                'message' => 'Invalid service'
            ))));
        }

        $QInvoice       = new Application_Model_InvoiceNumber();
        $invoice_number = $QInvoice->getLastId($warehouse , $service);


        exit(json_encode(array(
            'code'    =>  0,
            'data' => 'Done',
            'invoice_number' => $invoice_number
        )));
    }
    catch(exception $e){
        exit(json_encode((array(
            'code' => -1,
            'message' => 'Error'
        ))));
    }



}

/*Dung de luu so hoa don khi in hoa don laser*/
public function customLaserSaveAction()
{
    $invoice_number          = $this->getRequest()->getParam('invoice_number');
    $invoice_sign            = $this->getRequest()->getParam('prefix', '');
    $sales_sn                = $this->getRequest()->getParam('sales_sn');
    $total_invoice_price     = $this->getRequest()->getParam('total_invoice_price');
    $total_invoice_vat       = $this->getRequest()->getParam('total_invoice_vat');
    $total_invoice_after_vat = $this->getRequest()->getParam('total_invoice_after_vat');
    $invoice_price           = $this->getRequest()->getParam('invoice_price');
    $service_id              = $this->getRequest()->getParam('service_id');

    $userStorage    = Zend_Auth::getInstance()->getStorage()->read();
    $QInvoiceNumber = new Application_Model_InvoiceNumber();
    $QCustomOrder   = new Application_Model_CustomOrder();
    $QLog           = new Application_Model_InvoiceLog();
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    if(!$invoice_number)
    {
        echo -1;
        exit;
    }

    if(!$invoice_sign)
    {
        echo -1;
        exit;
    }

    $where  = $QCustomOrder->getAdapter()->quoteInto('sn = ?', $sales_sn);
    $market = $QCustomOrder->fetchRow($where);

    if (!$market)
    {
        echo '-2'; //sales sn error
        exit;
    }

    $data = array(
        'total'          => $total_invoice_price,
        'total_vat'      => $total_invoice_after_vat,
        'vat'            => $total_invoice_vat,
        'invoice_number' => $invoice_number,
    );

    $QCustomOrder->update($data, $where);

    $whereInvoiceNumber = array();
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign = ?' , $invoice_sign);
    $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

    if($exist_invoice_number and $sales_sn != $exist_invoice_number['sn'] and !isset($service_id))
    {
        echo '-6';
        exit;
    }

    $change_invoice = 0;
    $date = date('Y-m-d H:i:s');

    if (isset($total_invoice_after_vat) and $total_invoice_after_vat and isset($total_invoice_price) and
        $total_invoice_price and isset($total_invoice_vat) and $total_invoice_vat)
    {

        $QMarketInvoicePriceSn = new Application_Model_MarketInvoicePriceSn();
        $where = array();
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('sn = ?', $sales_sn);
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $m = $QMarketInvoicePriceSn->fetchRow($where);

        if ($m)
        {
            $QMarketInvoicePriceSn->update(array(
                'total_invoice_price'     => $total_invoice_price,
                'total_invoice_vat'       => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number'          => trim($invoice_number)), $where);
        } else
        {
            $QMarketInvoicePriceSn->insert(array(
                'sn'                      => $sales_sn,
                'total_invoice_price'     => $total_invoice_price,
                'total_invoice_vat'       => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number'          => trim($invoice_number)));
        }
    }

    //update
    try
    {
        //tim so hoa don da co trong he thong chua
        $whereInvoiceNumber = array();
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('sn = ?' , $sales_sn);
        $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
        $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

        if(isset($exist_invoice_number) and $exist_invoice_number) {
            $data = array(
                'copy' => intval($exist_invoice_number['copy'] + 1),
                'updated_at' => $date
            );

            $QInvoiceNumber->update($data , $whereInvoiceNumber);
        } else{

            $QInvoiceNumber->fixed();

            $data = array('invoice_number' => $invoice_number,
                'date'         => $date ,
                'invoice_sign' => $invoice_sign,
                'sn'           => $sales_sn,
                'service_id'   => intval($service_id)
            );
            //them so invoice vao he thong
            $id = $QInvoiceNumber->insert($data);

            if($id != intval($invoice_number))
            {
                throw new exception('404');
            }


        }

        //them log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');
        $info = 'Print Invoice : Sale order number: ' . $sales_sn . ' Invoice number : ' . $invoice_number . ' Copy : ' . $exist_invoice_number['copy'];
        $QLog->insert(array(
            'info'       => $info,
            'user_id'    => $userStorage->id,
            'ip_address' => $ip,
            'time'       => $date,
        ));

        echo '0';
        $db->commit();
        exit;
    }
    catch (exception $e)
    {
        $db->rollback();
        $QInvoiceNumber->fixed($id);
        echo -4;
        exit;
    }
}


/*Dung de luu so hoa don khi in hoa don laser*/
public function laserdeductionSaveAction()
{
    $invoice_number = $this->getRequest()->getParam('invoice_number');
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $invoice_sign = $this->getRequest()->getParam('prefix', '');
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $type = $this->getRequest()->getParam('type');

    $QMarketProduct = new Application_Model_MarketProduct();
    $type = $QMarketProduct->getDiscount($sales_sn);

    $db = Zend_Registry::get('db');
    //update
    $db->beginTransaction();


    if(!$invoice_number)
    {
        echo -1;
        exit;
    }

    if(!$invoice_sign)
    {
        echo -1;
        exit;
    }

    if(!$sales_sn)
    {
        echo -2;
        exit;
    }



    $QMarket = new Application_Model_Market();

    if (isset($type) and $type == 2)
    {
        $QMarketProduct = new Application_Model_MarketDeduction();

    } else
    {
     $QMarketProduct = new Application_Model_MarketProduct();
 }

 $QInvoiceNumber = new Application_Model_InvoiceNumber();
 $QInvoiceSign   = new Application_Model_InvoicePrefix();
 $QLog    = new Application_Model_InvoiceLog();
 $where = $QInvoiceSign->getAdapter()->quoteInto('id = ?', $invoice_sign);
 $invoice_sign_row = $QInvoiceSign->fetchRow($where);

 $date = $this->getRequest()->getParam('date');
 $change_invoice = 0;
 $warehouse_id = $invoice_sign_row['warehouse_id'];

 if(!$warehouse_id)
 {
    echo -2;
    exit;
}

$whereInvoiceNumber = array();
$whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
$whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_sign = ?' , $invoice_sign);
$exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);

if($exist_invoice_number and $sales_sn != $exist_invoice_number['sn'])
{
    echo '-6';
    exit;
}






$where = array();
$where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);

if (isset($invoice_number))
{

    $where = array();
    $where[] = $QMarketProduct->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
    $where[] = $QMarketProduct->getAdapter()->quoteInto('invoice_sign = ?', $invoice_sign);
    $where[] = $QMarketProduct->getAdapter()->quoteInto('sn <> ?', $sales_sn);
    $duplicate = $QMarketProduct->fetchRow($where);
    if ($duplicate)
    {
        echo '-3';
        exit;
    }
}




$total_invoice_price = $this->getRequest()->getParam('total_invoice_price');
$total_invoice_vat = $this->getRequest()->getParam('total_invoice_vat');
$total_invoice_after_vat = $this->getRequest()->getParam('total_invoice_after_vat');
$invoice_price = $this->getRequest()->getParam('invoice_price');


$QMarketInvoicePrice = new Application_Model_MarketInvoicePrice();

if (isset($total_invoice_after_vat) and $total_invoice_after_vat and isset($total_invoice_price) and
    $total_invoice_price and isset($total_invoice_vat) and $total_invoice_vat)
{

    $QMarketInvoicePriceSn = new Application_Model_MarketInvoicePriceSn();
    $where = array();
    $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('sn = ?', $sales_sn);
    $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
    $m = $QMarketInvoicePriceSn->fetchRow($where);

    if ($m)
    {
        $QMarketInvoicePriceSn->update(array(
            'total_invoice_price' => $total_invoice_price,
            'total_invoice_vat' => $total_invoice_vat,
            'total_invoice_after_vat' => $total_invoice_after_vat,
            'invoice_number' => trim($invoice_number)), $where);
    } else
    {
        $QMarketInvoicePriceSn->insert(array(
            'sn' => $sales_sn,
            'total_invoice_price' => $total_invoice_price,
            'total_invoice_vat' => $total_invoice_vat,
            'total_invoice_after_vat' => $total_invoice_after_vat,
            'invoice_number' => trim($invoice_number)));
    }
}

    //update
try
{
    $date = date('Y-m-d h:i:s');
        //tim so hoa don da co trong he thong chua

    $whereInvoiceNumber = array();
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('sn = ?' , $sales_sn);
    $whereInvoiceNumber[] = $QInvoiceNumber->getAdapter()->quoteInto('invoice_number = ?' , $invoice_number);
    $exist_invoice_number = $QInvoiceNumber->fetchRow($whereInvoiceNumber);



    if(isset($exist_invoice_number) and $exist_invoice_number)
    {
        $data = array(
            'copy' => intval($exist_invoice_number['copy'] + 1),
            'updated_at' => $date
        );
        $QInvoiceNumber->update($data , $whereInvoiceNumber);
    }
    else{

        $last_invoice_number = $QInvoiceNumber->getLastId($warehouse_id);

        if($invoice_number != $last_invoice_number)
            throw new exception('invoice number is invalid');

        $data = array('invoice_number' => $invoice_number,
            'date'         => $date ,
            'invoice_sign' => $invoice_sign,
            'sn'           => $sales_sn,
        );
            //them so invoice vao he thong
        $id = $QInvoiceNumber->insert($data);

    }

        // cap nhat so hoa don vao don hang
    $data = array('invoice_number' => $invoice_number, 'invoice_sign' => $invoice_sign , 'invoice_time' => $date);
    $QMarketProduct->update($data, $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn));

        //them log
    $ip = $this->getRequest()->getServer('REMOTE_ADDR');

    $info = 'Print Invoice : Sale order number: ' . $sales_sn . ' Invoice number : ' . $invoice_number . ' Copy : ' . $exist_invoice_number['copy'];

    $QLog->insert(array(
        'info' => $info,
        'user_id' => $userStorage->id,
        'ip_address' => $ip,
        'time' => $date,
    ));

    $db->commit();
    echo '0';
    exit;
}
catch (exception $e)
{
    $db->rollback();
    echo -4;
    exit;
}

}

public function invoiceSaveAction()
{
$invoice_number = $this->getRequest()->getParam('invoice_number');
$sales_sn = $this->getRequest()->getParam('sales_sn');
$invoice_sign = $this->getRequest()->getParam('prefix', '');

if (!$invoice_number)
{
        echo '-1'; //invoice error
        exit;
    }

    if (!$sales_sn)
    {
        echo '-2'; //sales sn error
        exit;
    }

    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn);

    $market = $QMarket->fetchRow($where);

    if (!$market)
    {
        echo '-2'; //sales sn error
        exit;
    }

    //check duplicate
    $where = array();
    $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);

    if (isset($invoice_number) and $invoice_number != INVOICE_DESTROY)
    {

        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_sign = ?', $invoice_sign);
        $where[] = $QMarket->getAdapter()->quoteInto('sn <> ?', $market['sn']);
        $duplicate = $QMarket->fetchRow($where);
        if ($duplicate)
        {
            echo '-3';
            exit;
        }
    }


    $total_invoice_price = $this->getRequest()->getParam('total_invoice_price');
    $total_invoice_vat = $this->getRequest()->getParam('total_invoice_vat');
    $total_invoice_after_vat = $this->getRequest()->getParam('total_invoice_after_vat');
    $invoice_price = $this->getRequest()->getParam('invoice_price');
    $date = $this->getRequest()->getParam('date');

    $QMarketInvoicePrice = new Application_Model_MarketInvoicePrice();

    if (isset($total_invoice_after_vat) and $total_invoice_after_vat and isset($total_invoice_price) and
        $total_invoice_price and isset($total_invoice_vat) and $total_invoice_vat)
    {

        $QMarketInvoicePriceSn = new Application_Model_MarketInvoicePriceSn();
        $where = array();
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('sn = ?', $sales_sn);
        $where[] = $QMarketInvoicePriceSn->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $m = $QMarketInvoicePriceSn->fetchRow($where);

        if ($m)
        {
            $QMarketInvoicePriceSn->update(array(
                'total_invoice_price' => $total_invoice_price,
                'total_invoice_vat' => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number' => intval($invoice_number)), $where);
        } else
        {
            $QMarketInvoicePriceSn->insert(array(
                'sn' => $sales_sn,
                'total_invoice_price' => $total_invoice_price,
                'total_invoice_vat' => $total_invoice_vat,
                'total_invoice_after_vat' => $total_invoice_after_vat,
                'invoice_number' => intval($invoice_number)));
        }
    } else
    {

    }

    if (isset($invoice_price) and is_array($invoice_price))
    {

        foreach ($invoice_price as $item)
        {
            $tm = explode('_', $item);
            if (isset($tm[0]) and isset($tm[1]))
            {
                $where = $QMarket->getAdapter()->quoteInto('market_id = ?', $tm[0]);
                $m = $QMarketInvoicePrice->fetchRow($where);

                if ($m)
                {
                    $QMarketInvoicePrice->update(array(
                        'invoice_price' => $tm[1],
                        'total_invoice_price' => $total_invoice_price,
                        'total_invoice_vat' => $total_invoice_vat,
                        'total_invoice_after_vat' => $total_invoice_after_vat,
                    ), $where);
                } else
                {
                    $QMarketInvoicePrice->insert(array(
                        'market_id' => $tm[0],
                        'invoice_price' => $tm[1],
                        'total_invoice_price' => $total_invoice_price,
                        'total_invoice_vat' => $total_invoice_vat,
                        'total_invoice_after_vat' => $total_invoice_after_vat,
                    ), $where);
                }

            } else
            {
                echo '-4';
                exit;
            }
        }

    } else
    {
      //cho luu lun eo care
    }

    //update
    try
    {

        $data = array('invoice_number' => $invoice_number);

        if (isset($date) and $date)
        {
            $temp = explode('/', $date);
            if (!(isset($temp[2]) and intval($temp[2]) >= 2013 and intval($temp[2]) <= 2020 and
                isset($temp[0]) and intval($temp[0]) <= 12 and intval($temp[0]) >= 1 and isset($temp[0]) and
                intval($temp[1]) <= 31 and intval($temp[1]) >= 1))
            {
                echo '-4';
                exit;
            }
            $date = explode('/', $date);
            $date = $date[2] . '-' . $date[0] . '-' . $date[1];
            $time = date("h:i:s");
            $data['invoice_time'] = $date . ' ' . $time;
        }

        $QMarket->update($data, $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn));


        echo '0';
        exit;
    }
    catch (exception $e)
    {
        echo $e->getMessage();
        exit;
    }
}

public function invoiceDeductionSaveAction()
{
    $invoice_number = $this->getRequest()->getParam('invoice_number');
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $invoice_prefix = $this->getRequest()->getParam('prefix');
    $type = $this->getRequest()->getParam('type');
    $date = $this->getRequest()->getParam('date');

    if (!$invoice_number)
    {
        echo '-1'; //invoice error
        exit;
    }

    if (!$invoice_prefix)
    {
        echo '-1'; //invoice error
        exit;
    }

    if (!$sales_sn)
    {
        echo '-2';
        exit;
    }
    if (isset($type) and $type)
    {
        $QMarket = new Application_Model_MarketProduct();
    } else
    {
        $QMarket = new Application_Model_MarketDeduction();
    }

    $QInvoice_prefix = new Application_Model_InvoicePrefix();

    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn);
    $market = $QMarket->fetchRow($where);

    if (!$market)
    {
        echo '-2';
        exit;
    }

    if (isset($invoice_number) and $invoice_number != INVOICE_DESTROY)
    {
        //check duplicate
        $where = array();
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_sign = ?', $invoice_prefix);
        $where[] = $QMarket->getAdapter()->quoteInto('sn <> ?', $market['sn']);
        $duplicate = $QMarket->fetchRow($where);
        if ($duplicate)
        {
            echo '-3';
            exit;
        }
    }


    //update
    try
    {
        $data = array(
            'invoice_number' => $invoice_number,
            'invoice_time' => date('Y-m-d h:i:s'),
            'invoice_sign' => $invoice_prefix
        );

        if (isset($date) and $date)
        {
            $temp = explode('/', $date);

            if (!(isset($temp[2]) and intval($temp[2]) >= 2013 and intval($temp[2]) <= 2020 and
                isset($temp[1]) and intval($temp[1]) <= 12 and intval($temp[1]) >= 1 and isset($temp[0]) and
                intval($temp[0]) <= 31 and intval($temp[0]) >= 1))
            {
                echo '-4';
                exit;
            }

            $date = explode('/', $date);
            $date = $date[2] . '-' . $date[1] . '-' . $date[0];
            $time = date("h:i:s");
            $data['invoice_time'] = $date . ' ' . $time;

        }

        $QMarket->update($data, $QMarket->getAdapter()->quoteInto('sn = ?', $sales_sn));

        echo '0';
        exit;
    }
    catch (exception $e)
    {
        echo $e->getMessage();
        exit;
    }
}


public function markCancelOrderAction() {

    $sn_new = $this->getRequest()->getParam('sn');
    $remark = $this->getRequest()->getParam('remark');
    $sn = substr($sn_new,1,-1);
    
    
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    if (!$sn)
    {
        echo '-1'; //invoice error
        exit;
    }

    if ($market['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, ADMINISTRATOR_ID))
    {

        if ($market['user_id'] != $userStorage->id && !My_Staff_Group::inGroup($userStorage->group_id, CANCEL_ORDER))
        {
            echo '-5'; //status error
            exit;
        }
    }

    $QImei = new Application_Model_Imei();
    $QCheckmoney = new Application_Model_Checkmoney();
    $ImeiCancelLog = new Application_Model_ImeiCancelLog();
    $QCheckmoneyPaymentorder = new Application_Model_CheckmoneyPaymentorder();
    $QWarehouseProduct = new Application_Model_WarehouseProduct();
    $QJobNumber = new Application_Model_JobNumber(); 
    $QPayGroup = new Application_Model_PayGroup(); 
    
    // Check Sales Order
    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market = $QMarket->fetchRow($where);
    //print_r($market['invoice_number']);die;
    if(!$market)
    {
        echo '-2'; //sales sn error
        exit;
    }

    $chk_invoice_number = $market['invoice_number'];
    // Check Phone Return
    $QImeiReturn = new Application_Model_ImeiReturn();
    $where = $QImeiReturn->getAdapter()->quoteInto('sales_order_sn = ?', $sn);
    $returnPhone = $QImeiReturn->fetchRow($where);
    if($returnPhone)
    {
        echo '-6'; //sales sn error
        exit;
    }

    
    // Check Accessories Return
    $invoice_number='';
    if(isset($temp[2])){
        $invoice_number = $market['invoice_number'];
    }

    if($invoice_number!=''){   
        $where[] = $QMarket->getAdapter()->quoteInto('invoice_number = ?', $invoice_number);
        $where[] = $QMarket->getAdapter()->quoteInto('isbacks = 1',null);
        $where[] = $QMarket->getAdapter()->quoteInto('cat_id !=11', null);
        $returnAccessories = $QMarket->fetchRow($where);
        if($returnAccessories)
        {
            echo '-6'; //status error
            exit;
        }
    }

    if ($market['status'] != 1)
    {
        echo '-3'; //status error
        exit;
    }

    //update
    try
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();

        $sql = "update checkmoney AS cm
        JOIN market AS mk
        ON mk.sn = cm.sn
        JOIN store_account AS sa
        ON sa.d_id = cm.d_id
        SET sa.balance = (sa.balance - cm.pay_money)
        WHERE mk.sn = ".$sn."
        ";
        $db->query($sql);


        $sql_imei = "select m.sn ,i.imei_sn 
        from market m 
        join imei i on m.sn=i.sales_sn
        where m.sn = ".$sn."
        group by i.imei_sn";
        $cancel_imei = $db->fetchAll($sql_imei);
        
        for ($i=0; $i < count($cancel_imei); $i++) { 
         $data_update = array(
            'imei_sn'           => $cancel_imei[$i]['imei_sn'],
            'sales_sn'          => $cancel_imei[$i]['sn'],
            'date_canceled'     => date('Y-m-d h:i:s'),
            'canceled_by'       => $userStorage->id,
        );
         $ImeiCancelLog->insert($data_update);
     }

     $canceled_data = array(
        'canceled'           => 1,
        'confirm_so'         => 1,
        'date_canceled'      => date('Y-m-d h:i:s'),
        'canceled_remark'    => $remark,
        'canceled_by'        => $userStorage->id,
    );


     $QMarket->update($canceled_data, $QMarket->getAdapter()->quoteInto('sn = ?',$sn));
     $QCheckmoney->update(array('canceled' => 1), $QCheckmoney->getAdapter()->quoteInto('sn = ?',$sn));
     $QCheckmoneyPaymentorder->update(array('canceled' => 1), $QCheckmoneyPaymentorder->getAdapter()->quoteInto('sn = ?',$sn));

     $where = $QImei->getAdapter()->quoteInto('sales_sn =?', $sn);
     $data = array(
        'out_date' => null,
        'distributor_id' => null,
        'sales_sn' => null,
        'out_price' => null,
        'price_date' => null,
        'sales_id' => null,
        'stock_shop_id' => null,
        'stock_shop_status' => null,
        'stock_shop_date' => null,
        'stock_shop_scan' => null
    );

     $QImei->update($data, $where);

     $where_del_job = array();
     $where_del_job = $QJobNumber->getAdapter()->quoteInto('sales_order = ?', $sn);
     $QJobNumber->delete($where_del_job);

     $sql_acc = "SELECT warehouse_id,cat_id,good_id,good_color,num FROM market WHERE sn='".$sn."' and cat_id=12;";
     $cancel_acc = $db->fetchAll($sql_acc);
     for ($i=0; $i < count($cancel_acc); $i++) { 

         $warehouse_id = $cancel_acc[$i]['warehouse_id'];
         $cat_id = $cancel_acc[$i]['cat_id'];
         $good_id = $cancel_acc[$i]['good_id'];
         $good_color_id = $cancel_acc[$i]['good_color'];
         $num = $cancel_acc[$i]['num'];
             //print_r($num);die;
         if ($cat_id == ACCESS_CAT_ID)
         {

            $where = array();
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('cat_id = ?', $cat_id);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_id = ?', $good_id);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('good_color = ?', $good_color_id);
            $where[] = $QWarehouseProduct->getAdapter()->quoteInto('warehouse_id = ?', $warehouse_id);

            $result = $QWarehouseProduct->fetchRow($where);
            if ($result) {
                if($chk_invoice_number !=''){  
                    $quantity = $result['quantity'] + $num;
                    $data = array('quantity' => ($quantity > 0 ? $quantity : 0));
                        //print_r($num);
                        //print_r($result['quantity']);
                        //print_r($quantity);
                        //die;
                    $QWarehouseProduct->update($data, $where);
                }
            }
                //print_r($chk_invoice_number);die;
        }


    }

    $db->query("CALL update_credit_note_sn('0','no_discount',0,'".$sn."','".$userStorage->id."',0,0)");

    $db->query("CALL update_deposit_tran('0','no_discount',0,'".$sn."','".$userStorage->id."')");

    $QPayGroup->update(array('status' => 0), $QPayGroup->getAdapter()->quoteInto('sale_order = ?',$sn));

    $ip = $this->getRequest()->getServer('REMOTE_ADDR');
    $info = 'Market:Cancel Order -> Sale order number: '.$sn;
    $QLog = new Application_Model_Log();
    $QLog->insert( array (
        'info'              => $info,
        'user_id'           => $userStorage->id,
        'ip_address'        => $ip,
        'time'              => date('Y-m-d h:i:s'),
    ) );

        // commit
    $db->commit();

    echo '0';
    exit;
}
catch (exception $e)
{
    $db->rollback();
    echo '-4';
    exit;
}
}

public function getMarkCancelAction()
{
    // disable layouts and renderers
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender();

$sn_new = $this->getRequest()->getParam('sn');
$sn = substr($sn_new,1,-1);

$QMarket = new Application_Model_Market();
$where = $QMarket->getAdapter()->quoteInto('sn = ?',  $sn);
$rem = $QMarket->fetchRow($where);
$QStaff = new Application_Model_Staff();
$name =  $QStaff->get_cache();   

$result = '';
$result .= '<p><storage>Canceled by : </storage>'.$name[$rem['canceled_by']].'</p>';
$result .= '<p><storage>Canceled date : </storage>'.$rem['date_canceled'].'</p>';
$result .= '<p><storage>Remark : </storage> </p><p style="margin-top: -30px;margin-left: 55px;width: 400px;word-wrap: break-word;">'.$rem['canceled_remark'].'</p>';
$result .= '<a href="'.HOST.'sales?export=90&can_sn='.$rem['sn'].'">Show Imei</a>';
echo $result;
exit;

}
public function getTagsAction()
{
    // disable layouts and renderers
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender();

$tag_name = $this->getRequest()->getParam('term');

$QTag = new Application_Model_Tag();
$where = $QTag->getAdapter()->quoteInto('name LIKE ?', $tag_name . '%');
$tags = $QTag->fetchAll($where, 'name');

$aTags = array();
if ($tags)
    foreach ($tags as $t)
    {
        $aTags[] = array(
            'id' => $t['id'],
            'value' => $t['name'],
        );
    }

    echo json_encode($aTags, true);
}
public function distributorAction()
{
    $this->_helper->layout->disableLayout();

    if (!$this->getRequest()->isXmlHttpRequest())
    {
        exit;
    }

    $store_name = $this->getRequest()->getParam('store_name');

    if (!$store_name)
    {
        exit;
    }

    $QStore = new Application_Model_Distributor();
    $where = $QStore->getAdapter()->quoteInto('title LIKE ?', '%' . $store_name .
        '%');
    $this->view->stores = $QStore->fetchAll($where);

    $QArea = new Application_Model_Area();
    $this->view->areas = $QArea->get_cache();
    $QRegion = new Application_Model_RegionalMarket();
    $this->view->regions = $QRegion->get_cache_all();
}

//return json
public function getdistributorAction()
{

    $title = $this->getRequest()->getParam('title');

    $db = Zend_Registry::get('db');
    $select = $db->select()->from('distributor', array('id', 'title'))->where('title like ?',
        "%" . $title . "%")->limit(30, 0);
    $result = $db->fetchAll($select);
    $this->_helper->json->sendJson($result);
    $this->_helper->layout()->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);

}

public function countDiscountAction()
{
    $this->_helper->layout()->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    $d_id = $this->getRequest()->getParam('id', null);
    $joint_id = $this->getRequest()->getParam('joint', null);
    $params = array('d_id' => $d_id, 'joint_id' => $joint_id);

    $QMarketDeduction = new Application_Model_MarketDeduction();
    $final_price = $QMarketDeduction->getPrice($params);

    $result_price = $final_price[DISCOUNT_CK] + $final_price[DISCOUNT_CK_II];

    $this->_helper->json->sendJson($result_price);
}

// count demo good ( lấy số thông tin các máy demo đã đặt )
public function countGoodDemoAction()
{
    $d_id = $this->getRequest()->getParam('d_id', null);
    $db = Zend_Registry::get('db');
    $QDistributor = new Application_Model_Distributor();
    $select = $db->select()->from(array('m' => 'market'), array('total' =>
        'SUM(m.num)', 'good_id'))->where($db->quoteInto('d_id = ?', $d_id))->where($db->
        quoteInto("cat_id = ?", PHONE_CAT_ID))->where($db->quoteInto('type = ?',
            FOR_DEMO))->where($db->quoteInto('status = ?', 1))->where('outmysql_time IS NOT NULL', null)->
        group('m.good_id');

        $distributorRowset = $QDistributor->find($d_id);
        $distributor       = $distributorRowset->current();





    //Lấy số lượng máy demo trên từng sản phẩm => good_id, quantity
        $result_market = $db->fetchAll($select);

    //Chuyển sang array value để tìm kiêm
        $arr = array();
        foreach ($result_market as $item):
            $arr[] = $item['good_id'];
        endforeach;

    //Lấy tên sản phẩm
        $QGood = new Application_Model_Good();
        $result = null;
        if ($arr)
        {
            $select = $QGood->select()->from('good', array('name', 'id'))->where('id IN (?)',
                $arr);
            $result = $QGood->fetchAll($select)->toArray();


        //tạo mảng gồm các phần tử id,total,name
            foreach ($result as $key => $value):
                foreach ($result_market as $k => $v):
                    if ($value['id'] == $v['good_id']):
                        $result[$key]['total'] = $v['total'];
                    endif;
                endforeach;
            endforeach;
        }

    //Lấy số lượng cửa hàng
        $select_dealer = $db->select()->from(array('s' => CENTER_DB . '.store'), array('COUNT(s.id)'))->
        where('d_id = ?', $d_id);
        $qty_dealer = $db->fetchOne($select_dealer);

        $arr = array(
            'status'     => 1,
            'qty_dealer' => $qty_dealer,
            'result'     => $result,
        );

    // ốc mượn hồn
    // lấy incentive tháng 4
        $QMarketDeduction = new Application_Model_MarketDeduction();
        $params = array('d_id' => $d_id);
        $final_price = $QMarketDeduction->getPrice($params);


        if ($final_price && isset($final_price))
            $arr['incentive'] = My_Number::f($final_price[DISCOUNT_DIAMOND_CLUB]  + $final_price[DISCOUNT_DIAMOND_CLUB_6]) ;
        if(in_array($distributor['parent'] , array(KA_TGDD , KA_VTA , KA_FPT , KA_NGUYEN_KIM)))
         $arr['d_parent'] = $distributor['parent'] ? $distributor['parent'] : '';
    // END // lấy incentive tháng 4

     $this->_helper->json->sendJson($arr);
     $this->_helper->layout()->disableLayout(true);
     $this->_helper->viewRenderer->setNoRender(true);
 }


// return json
 public function checkGoodDemoAction()
 {

    $good_ids = $this->getRequest()->getParam('good_id', null);
    $nums = $this->getRequest()->getParam('num', null);
    $d_id = $this->getRequest()->getParam('d_id', null);
    $checkInput = true;
    $checkInputMessage = array();
    $check = array(); //lưu kết quả kiểm tra máy

    if (!is_array($good_ids))
    {
        $checkInput = false;
        $checkInputMessage[] = "good_id's type must be array";
    }

    if (!is_array($nums))
    {
        $checkInput = false;
        $checkInputMessage[] = "nums's type must be array";
    }

    if (!is_numeric($d_id))
    {
        $checkInput = false;
        $checkInputMessage[] = "'d_id' must be a number";
    }

    foreach ($good_ids as $good):
        if (!is_numeric($good))
        {
            $checkInput = false;
            $checkInputMessage[] = "good_id value must be a number";
            break;
        }
    endforeach;

    foreach ($nums as $num):
        if (!is_numeric($num))
        {
            $checkInput = false;
            $checkInputMessage[] = "num value must be a number";
            break;
        }
    endforeach;

    if ($checkInput == true)
    {
        $product = array();

        //Cộng dồn số lượng nếu mã sản phẩm trùng
        foreach ($good_ids as $key => $value):
            if (array_key_exists($value, $product))
            {
                $product[$value] += $nums[$key];
            } else
            {
                $product[$value] = $nums[$key];
            }
        endforeach;
        ;

        $db = Zend_Registry::get('db');

        //Lấy số lượng cửa hàng
        $select = $db->select()->from(array('s' => CENTER_DB . '.store'), array('COUNT(s.id)'))->
        where('d_id = ?', $d_id);
        $qtyStore = $db->fetchOne($select);

        //Lấy số lượng máy demo đã đặt trên từng sản phẩm => good_id, quantity
        $select_good = $db->select()->from(array('m' => 'market'), array('total' =>
            'SUM(m.num)', 'good_id'))->where('d_id  = ?', $d_id)->where('cat_id = ?',
        PHONE_CAT_ID)->where('type  = ?', FOR_DEMO)->where('status = ?', 1)->where('outmysql_time IS NOT NULL', null)->
            group('m.good_id');
            $result_market_good = $db->fetchAll($select_good);

        //kiểm tra số lượng từng dòng máy có thể đặt
            $i = 0;
            $arr_good_id = array();

            foreach ($product as $good_id => $num):
                foreach ($result_market_good as $k => $v):
                $avaiable_good = 0; //số máy có thể đặt
                $over = 0; //số máy đặt vượt
                if ($good_id == $v['good_id']):
                    $avaiable_good = $qtyStore - $v['total'];
                    $over = $num - $avaiable_good;

                    if ($avaiable_good < 0):
                        $avaiable_good = 0;
                    endif;
                    //No Limit Sell Produc Demo
                    /*
                        if ($avaiable_good < $num):
                            $arr_good_id[] = $good_id;
                            $check[$i]['good_id'] = $good_id;
                            $check[$i]['avaiable'] = $avaiable_good;
                            $check[$i]['over'] = $over;
                            $i += 1;
                        endif;
                    */
                    endif;
                endforeach;
            endforeach;

            if ($arr_good_id)
            {
            //Lấy tên sản phẩm
                $QGood = new Application_Model_Good();
                $select = $QGood->select()->from('good', array('name', 'id'))->where('id IN (?)',
                    $arr_good_id);
                $result_name = $QGood->fetchAll($select);

            //tạo mảng gồm các phần tử id,total,name
                foreach ($result_name as $name):
                    foreach ($check as $k => $v):
                        if ($name['id'] == $v['good_id']):
                            $check[$k]['name'] = $name['name'];
                        endif;
                    endforeach;
                endforeach;
            } else
            {
                $check = array();
            }

            $arr_result = array('status' => true, 'result' => $check);
            $this->_helper->json->sendJson($arr_result);
        } else
        {
            $arr_result = array('status' => false, 'messages' => $checkInputMessage);
            $this->_helper->json->sendJson($arr_result);
        }

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout(true);
    }


    public function checkGoodAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header('Content-Type: application/json');

        if (!$this->getRequest()->isXmlHttpRequest())
        {
            exit(json_encode(array('code' => '-100', 'error' => 'Require AJAX request.')));
        }

        $good_id = $this->getRequest()->getParam('good_id');

        if (!$good_id)
        {
            exit(json_encode(array('code' => '-1', 'error' => 'Invalid params.')));
        }

        $id = $this->getRequest()->getParam('id');

        $QBundleGift = new Application_Model_BundleGift();
        $where = array();

        if ($id)
            $where[] = $QBundleGift->getAdapter()->quoteInto('id <> ?', $id);

        $where[] = $QBundleGift->getAdapter()->quoteInto('good_id = ?', $good_id);
        $gift = $QBundleGift->fetchRow($where);

        if ($gift)
        {
            exit(json_encode(array('code' => '-2', 'error' => 'Product existed.')));
        }

        exit(json_encode(array('code' => '1', 'message' => 'Success.')));
    }

    public function checkGiftAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        header('Content-Type: application/json');

        if (!$this->getRequest()->isXmlHttpRequest())
        {
            exit(json_encode(array('code' => '-100', 'error' => 'Require AJAX request.')));
        }

        $good_list = $this->getRequest()->getParam('good_list');
        $quantity  = $this->getRequest()->getParam('quantity');

        if (!$good_list)
        {
            exit(json_encode(array('code' => '-1', 'error' => 'Invalid params.')));
        }

        $QBundleGift = new Application_Model_BundleGift();
        $QGood = new Application_Model_Good();
        $goods = $QGood->get_cache();

   // var_dump($QBundleGift->check_gift($good_list , $quantity));exit;

        if ($gifts = $QBundleGift->check_gift($good_list , $quantity))
        {


            $msg = 'Please choose gifts for ' . $goods[$gifts['good_id']] . ': ';
            $msg_name = $html = array();

            foreach ($gifts['gift_id'] as $g)
            {
                $msg_name[] = $goods[$g['gift_id']];
                $html[]     = $this->giftHTML($g['gift_id'] , $g['quantity']);
            }

            $msg .= implode(', ', $msg_name);
            $msg .= ". Do you want to continue?";

            exit(json_encode(array(
                'code'  => '-2',
                'error' => $msg,
                'data'  => $gifts,
                'html'  => $html
            )));

        } else
        {
            exit(json_encode(array('code' => '1', 'message' => 'Success.')));
        }

        exit;
    }

    private function giftHTML($gift_id , $quantity = 0)
    {
        $QGood = new Application_Model_Good();
        $QGoodCategory = new Application_Model_GoodCategory();
        $good_categories = $QGoodCategory->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $goodcolors = $QGoodColor->get_cache();

        $goodRowset = $QGood->find($gift_id);
        $good       = $goodRowset->current();

        $good_color = explode(',', $good['color']);

        if(empty($good))
            return null;

        $price = $good['price_3'] ? $good['price_3'] : 0;
        $total = intval($price * $quantity);

        $div = '
        <div class="row div-add">
        <div class="span12">
        <label class="span1">Category <span style="color: red">*</span>
        <select class="span1 cat_id" name="cat_id[]" required="required" disabled>
        <option selected value="'.$good['cat_id'].'">'.$good_categories[$good['cat_id']].'</option>
        </select>
        </label>

        <label class="span1">Product <span style="color: red">*</span>
        <select readonly class="span1 good_id" name="good_id[]" required="required">
        <option selected  value="'.$good['id'].'">'.$good['name'].'</option>
        </select>
        </label>

        <label class="span1">Color <span style="color: red">*</span>
        <select class="span1 good_color" name="good_color[]" required="required" >
        <option>Please select good color</option>
        ';
        $good_color_first = 0;
        if(isset($good_color) and is_array($good_color)):
            foreach($good_color as $k => $color):
                if($good_color_first == 0)
                {
                    $div .= '<option selected value="'. $color .'">'.$goodcolors[$color].'</option>';
                    $good_color_first ++;
                }
                else
                {
                    $div .= '<option value="'. $color .'">'.$goodcolors[$color].'</option>';
                }
                //$div .= '<option value="'.$color.'">'. isset($goodcolors[$color]) ? $goodcolors[$color] : ''.'</option> ';
            endforeach;endif;

            $div .= '</select>
            </label>

            <label class="span1">Quantity <span style="color: red">*</span>
            <input readonly type="number" min="1" class="span1 num" name="num[]" value="'.$quantity.'" required="required" maxlength="5" style="width: 50px"  />
            </label>

            <label class="span1">Price <span style="color: red">*</span>
            <input type="text" min="1" required="required" readonly class="span1 price" value="'.$price.'" name="price[]" disabled />
            </label>

            <label class="span1">Sale off<span style="color: red">*</span>
            <select readonly class="span1 sale_off_percent" name="sale_off_percent[]" required="required" >
            <option value="0">0%</option>
            </select>
            </label>


            <label class="span2">Total<span style="color: red">*</span>
            <input type="text" readonly min="0" required="required" class="span2 total total_new" value="0" name="total[]"  />
            </label>

            <label class="span2">Remark
            <textarea name="text[]" class="span2 text" value="Gift for campaign" readonly ></textarea>
            </label>

            <label class="span1 campaign">Campaign <span style="color: red">*</span>
            <input type="checkbox" class="span1 campaign_ck" name="campaign_ck[]"   disabled />
            <input type="hidden" class="campaign_val" name="campaign[]" value="" />
            </label>

            <label class="span1">&nbsp;<button type="button" class="btn-danger remove-sales"><i class="icon-minus"></i></button></label>

            <input type="hidden" name="ids[]" class="ids" disabled />
            </div>

            </div>
            </div>';
            return $div;
        }

        public function distributorCbbAction()
        {
            $this->_helper->layout->disableLayout();

            $area = $this->getRequest()->getParam('area');

            $QRegion = new Application_Model_Region();
            $regions = $QRegion->get_by_area_cache($area);

            if (isset($regions) && is_array($regions) && count($regions))
            {
                $region_ids = array();

                foreach ($regions as $key => $value)
                    $region_ids[] = $key;

                $QDistributor = new Application_Model_Distributor();
                $where = array();
                $where[] = $QDistributor->getAdapter()->quoteInto('del IS NULL OR del = ?', 0);
                $where[] = $QDistributor->getAdapter()->quoteInto('region IN (?)', $region_ids);
                $this->view->list = $QDistributor->fetchAll($where, 'title');
            }
        }

        public function marketDetailAction()
        {
            $this->_helper->layout->disableLayout();
            $sn = $this->getRequest()->getParam('sn');
            $list = $this->getRequest()->getParam('list');
            $params = $this->getRequest()->getParam('params');
            $params = @unserialize($params);

            $QGood = new Application_Model_Good();
            $QGoodCategory = new Application_Model_GoodCategory();
            $QGoodColor = new Application_Model_GoodColor();
            $QMarket = new Application_Model_Market();
            $QBrand = new Application_Model_Brand();

            $goods = $QGood->get_cache();
            $goodColors = $QGoodColor->get_cache();
            $goodCategory = $QGoodCategory->get_cache();
            $brands = $QBrand->get_cache();

            $params['created_at_from'] = substr($params['created_at_from'], 0, 10);
            $params['created_at_to'] = substr($params['created_at_to'], 0, 10);

            $params['get_fields'] = array(
                'cat_id',
                'good_id',
                'good_color',
                'num',
                'price',
                'total',

                'sale_off_percent',
            );
            $params['not_get_total'] = true;

            $params['sn'] = $sn;

            if (isset($params['sort']) and $params['sort'])
                unset($params['sort']);

            // print_r($params);die;
            $total = 0;
            $markets =  $QMarket->fetchPagination(1, null, $total, $params);

            // print_r($markets[0]['good_id']);die;

            $this->view->brands = $brands;
            $this->view->markets = $QMarket->fetchPagination(1, null, $total, $params);


            $this->view->list = $list;
            $this->view->sn = $sn;
            $this->view->goods = $goods;
            $this->view->goodColors = $goodColors;
            $this->view->goodCategory = $goodCategory;
        }

        public function marketStockDetailAction()
        {
            $this->_helper->viewRenderer->setRender('market-detail');
            $this->_helper->layout->disableLayout();
            $sn = $this->getRequest()->getParam('sn');
            $list = $this->getRequest()->getParam('list');
            $params = $this->getRequest()->getParam('params');
            $params = @unserialize($params);

            $QGood = new Application_Model_Good();
            $QGoodCategory = new Application_Model_GoodCategory();
            $QGoodColor = new Application_Model_GoodColor();
            $QMarket = new Application_Model_MarketStock();

            $goods = $QGood->get_cache();
            $goodColors = $QGoodColor->get_cache();
            $goodCategory = $QGoodCategory->get_cache();

            $params['get_fields'] = array(
                'cat_id',
                'good_id',
                'good_color',
                'num',
                'price',
                'total',
            );
            $params['not_get_total'] = true;

            $params['sn'] = $sn;

            if (isset($params['sort']) and $params['sort'])
                unset($params['sort']);

            $total = 0;
            $this->view->markets = $QMarket->fetchPagination(1, null, $total, $params);

            $this->view->list = $list;
            $this->view->sn = $sn;
            $this->view->goods = $goods;
            $this->view->goodColors = $goodColors;
            $this->view->goodCategory = $goodCategory;
        }

        public function checkDistributorNameAction()
        {
            $this->_helper->layout->disableLayout();

            $this->view->id = $this->getRequest()->getParam('id');
            $distributor_name = $this->getRequest()->getParam('distributor_name');
            $QDistributor = new Application_Model_Distributor();
            $this->view->result = $QDistributor->compare($distributor_name);
        }

        public function poCreateAction()
        {
            $this->_helper->layout->disableLayout();
            $this->view->distributor_id = $this->getRequest()->getParam('distributor_id');

            $QDistributor = new Application_Model_Distributor();
            $this->view->distributors = $QDistributor->get_all();
        }

        public function jointSaveAction()
        {
            $joint_name = $this->getRequest()->getParam('joint_name');
            $bvg        = $this->getRequest()->getParam('bvg');
            $price      = $this->getRequest()->getParam('price');
            $good_id    = $this->getRequest()->getParam('good_id');

            $note = $this->getRequest()->getParam('note');
            try
            {
                $joint_name = strval(trim($joint_name));
                $QJointCircular = new Application_Model_JointCircular();
                if(empty($joint_name))
                {
                    throw new Exception("Please input jointcircular");
                }
                $where = array();
                $where[] = $QJointCircular->getAdapter()->quoteInto('name = ? ' , $joint_name);
                $joint  = $QJointCircular->fetchRow($where);

                if(isset($joint) and $joint)
                {
                    throw new Exception(sprintf("Joint Circular %s  already exists. Click Cancel and select from the list announced.",
                        $joint_name), 4);
                }

                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                $user_id     = $userStorage->id;

                $data = array(
                    'name' => $joint_name,
            //'type' => BVG_SUPPORT_SELL_OUT,
                    'type' => 1,
                    'note' => $note,
                    'status' => 1,
                    'time' => date('Y-m-d H:i:s'),
                );

                $id_joint = $QJointCircular->insert($data);

                if(isset($bvg) and $bvg)
                {
                    $QBvgProduct  = new Application_Model_BvgProduct();

                    if(!(isset($good_id) and $good_id))
                    {
                        throw new Exception(sprintf("Please input good id"
                    ), 5);
                    }

                    if(!(isset($price) and $price))
                    {
                        throw new Exception(sprintf("Please input price"
                    ), 6);
                    }

                    $dataProduct  = array_filter(array(
                        'joint_id' => $id_joint,
                        'price'    => $price,
                        'good_id'  => $good_id
                    ));

                    $QBvgProduct->insert($dataProduct);
                }

                exit(json_encode(array('code' => 1)));

            }
            catch(exception $e)
            {
                exit(json_encode(array('code' => $e->getCode(), 'error' => $e->getMessage())));
            }

        }

        public function poSaveAction()
        {
    /**
     * Tạm thời bỏ distributor
     */
    //$distributor_id = $this->getRequest()->getParam('distributor_id');
    $po_name = $this->getRequest()->getParam('po_name');
    $note = $this->getRequest()->getParam('note');

    try
    {
        /*
        if (!$distributor_id) throw new Exception("Invalid Distributor", 2);
        $distributor_id = intval($distributor_id);

        $QDistributor = new Application_Model_Distributor();
        $distributor  = $QDistributor->find($distributor_id);
        $distributor  = $distributor->current();

        if (!$distributor) throw new Exception("Wrong Distributor ID", 3);
        */
        $po_name = strval(trim($po_name));

        if (empty($po_name))
            throw new Exception("PO Number cannot empty");

        $QDistributorPo = new Application_Model_DistributorPo();
        $where = array();
        //$where[]        = $QDistributorPo->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
        $where[] = $QDistributorPo->getAdapter()->quoteInto('po_name = ?', $po_name);
        $po = $QDistributorPo->fetchRow($where);

        if ($po)
            throw new Exception(sprintf("PO %s  đã tồn tại. Bấm Cancel và chọn PO từ danh sách.",
                $po_name), 4);

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $data = array(
            //'distributor_id' => $distributor_id,
            'po_name' => $po_name,
            'note' => $note,
            'created_by' => $userStorage->id,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $QDistributorPo->insert($data);

        exit(json_encode(array('code' => 1)));
    }
    catch (exception $e)
    {
        exit(json_encode(array('code' => $e->getCode(), 'error' => $e->getMessage())));
    }
}

public function poListAction()
{
    $this->_helper->layout->disableLayout();
    /*
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    if (!$distributor_id) {
    $this->view->po_list = array();
    return;
    }
    */
    $QDistributorPo = new Application_Model_DistributorPo();
    $where = $QDistributorPo->select()->order('created_at DESC');
    $this->view->po_list = $QDistributorPo->fetchAll($where);
}

public function deliveryFeeAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    try {
        if (!$this->getRequest()->isXmlHttpRequest())
            throw new Exception("Only accept AJAX request", 100);

        $distributor_id = $this->getRequest()->getParam('distributor_id');
        $value = $this->getRequest()->getParam('value');
        $type = $this->getRequest()->getParam('type');

        switch ($type) {
            case 'shipping':
            $type = My_Sale_Order_Fee::Shipping;
            break;

            default:
            throw new Exception("Invalid fee type", 1);
            break;
        }

        if (!is_numeric($value)) throw new Exception("Value must be numberic", 2);

        $QDistributor = new Application_Model_Distributor();
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', intval($distributor_id));
        $distributor_check = $QDistributor->fetchRow($where);

        if (!$distributor_check) throw new Exception("Invalid distributor", 3);

        if (!isset($distributor_check['district']) || !intval($distributor_check['district'])) {
            file_put_contents(APPLICATION_PATH."/../logs/delivery_fee.txt", "Please setup district for this distributor: ".$distributor_check['title']."\r\n", FILE_APPEND);
            throw new Exception("Please setup district for this distributor", 4);
        }

        $fee = My_Sale_Order_Fee::getDistrictFee($value, $distributor_check['district'], $type);

        exit(json_encode(array('code' => 0, 'fee' => $fee)));
    } catch (Exception $e) {
        exit(json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage())));
    }


}

/**
 * Lấy các hub theo district
 */
public function checkDistrictHubAction()
{
    if (!$this->getRequest()->isXmlHttpRequest())
        exit;

    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

    $district_id = $this->getRequest()->getParam('district_id');

    if (!$district_id) {
        $response
        ->appendBody(json_encode(array('have_hub' => 0)))
        ->sendResponse();
        exit;
    }

    $QHubDistrict = new Application_Model_HubDistrict();
    $hub = $QHubDistrict->checkDistrictHub($district_id);

    if (!$hub) {
        $response
        ->appendBody(json_encode(array('have_hub' => 0)))
        ->sendResponse();
        exit;
    }

    $response
    ->appendBody(json_encode(array(
        'have_hub'    => 1,
        'hub_id'      => $hub['id'],
        'hub_name'    => $hub['name'],
        'hub_address' => $hub['address'],
        'hub_contact' => $hub['contact'],
        'hub_tel'     => $hub['mobile_phone'],
    )))->sendResponse();
    exit;
}

public function distributorInfoByCodeAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

    $code = $this->getRequest()->getParam('code');

    if (!$code) {
        $response
        ->appendBody(json_encode(array('no_data' => 1)))
        ->sendResponse();
        exit;
    }

    $QDistributor = new Application_Model_Distributor();
    $where = $QDistributor->getAdapter()->quoteInto('CAST(store_code AS CHAR) = ?', $code);
    $distributor = $QDistributor->fetchRow($where);

    if (!$distributor) {
        $response
        ->appendBody(json_encode(array('no_data' => 1)))
        ->sendResponse();
        exit;
    }

    $response
    ->appendBody(json_encode(array(
        'title'      => $distributor['title'],
        'store_code' => $distributor['store_code'],
        'address'    => $distributor['add'],
    )))->sendResponse();
    exit;
}

public function deliveryOrderDetailAction()
{
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    $response = My_Cache::header($this->getResponse(), time(), 3600, 'text/json');

    $id = $this->getRequest()->getParam('id');

    if (!$id) {
        $response
        ->appendBody(json_encode(array('no_data' => 1)))
        ->sendResponse();
        exit;
    }

    $QDeliveryOrder = new Application_Model_DeliveryOrder();
    $where = $QDeliveryOrder->getAdapter()->quoteInto('id = ?', intval($id));
    $order = $QDeliveryOrder->fetchRow($where);

    if (!$order) {
        $response
        ->appendBody(json_encode(array('no_data' => 1)))
        ->sendResponse();
        exit;
    }

    $QDeliverySales = new Application_Model_DeliverySales();
    $detail = $QDeliverySales->getDetail(intval($id));

    if (!$detail) {
        $response
        ->appendBody(json_encode(array('no_data' => 1)))
        ->sendResponse();
        exit;
    }

    $response
    ->appendBody(json_encode(array(
        'detail'      => $detail,
        'weight'      => !is_null($order['weight']) ? $order['weight'] : 'n/a',
        'num_package' => !is_null($order['number_of_package']) ? $order['number_of_package'] : 'n/a',
    )))
    ->sendResponse();
    exit;
}

public function getBankAction(){
    $company_id = $this->getRequest()->getParam('company_id');
    $db = Zend_Registry::get('db');
    $select = $db->select()
    ->from(array('p'=>'bank'),array('id','name'));
    if($company_id){
        $select->where('company_id = ?',$company_id);
    }
    $result = $db->fetchPairs($select);
    $this->_helper->json->sendJson($result);
    $this->_helper->layout()->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
}


public function goodByCatagoryAction() {

    $cat_id = $this->getRequest()->getParam('cat_id');
    $QGood = new Application_Model_Good();

    if (is_array($cat_id) && $cat_id)
        $where = $QGood->getAdapter()->quoteInto('cat_id IN (?)', $cat_id);
    else
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', $cat_id);

    echo json_encode($QGood->fetchAll($where, 'name')->toArray());
    exit;
}

public function colorByGoodAction() {

    $good_id = $this->getRequest()->getParam('good_id');
    $QGoodColorCombined = new Application_Model_GoodColorCombined();
    $QGoodColor = new Application_Model_GoodColor();
    
    if (is_array($good_id) && $good_id)
        $where = $QGoodColorCombined->getAdapter()->quoteInto('good_id IN (?)', $good_id);
    else
        $where = $QGoodColorCombined->getAdapter()->quoteInto('good_id = ?', $good_id);

    $temp = $QGoodColorCombined->fetchAll($where, 'good_color_id');

    for ($i=0;$i<count($temp);$i++) {
        $tmp[$i] = $temp[$i]['good_color_id'];
    }

    $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $tmp);
    $result = $QGoodColor->fetchAll($where, 'name');

    echo json_encode($result->toArray());
    exit;
}


public function loadCreditNoteAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $creditnote_sn_list = $this->getRequest()->getParam('init_params');
    
    // Tanong Add Function CreditNote 2016/03/08 16:12
    $QCreditNote = new Application_Model_CreditNote();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        //$creditnote_sn_list='CN59020008,CN59030008';
        //$creditnote_sn_list='CN59020008';
        $result = $QCreditNote->getCredit_Note($distributor_id,$creditnote_sn_list);
        //print_r($result);
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have joint circular')));

        if($result['code'] != 1)
        {
            throw new exception('Distributor is don\'t have Credit Note');
        }

        $list_joint_exist = $result['data'];

        echo json_encode($list_joint_exist);
        exit;


    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

public function loadDepositSnAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $deposit_sn_list = $this->getRequest()->getParam('init_params');
    
    // Tanong Add Function CreditNote 2016/03/08 16:12
    $QDeposit = new Application_Model_Deposit();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        //$creditnote_sn_list='CN59020008,CN59030008';
        //$creditnote_sn_list='CN59020008';
        $result = $QDeposit->getDeposit_sn($distributor_id,$deposit_sn_list);
        //print_r($result);
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have joint circular')));

        if($result['code'] != 1)
        {
            throw new exception('Distributor is don\'t have Deposit');
        }

        $list_joint_exist = $result['data'];

        echo json_encode($list_joint_exist);
        exit;


    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

//Tanong
public function loadSalesOrderAmountAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    // Tanong Add Function SalesOrderAmount 2016/03/23 14:55
    $QMarket = new Application_Model_Market();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        $result = $QMarket->LoadSalesOrderAmount($distributor_id);
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have Sales Order')));

        echo json_encode($result[0]['total_acmount']);
        exit;
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

public function loadDeliveryFeeAllAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    // Tanong Add Function SalesOrderAmount 2016/03/23 14:55
    $QMarket = new Application_Model_Market();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        $result = $QMarket->LoadSalesOrderAmount($distributor_id);
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have Sales Order')));

        echo json_encode($result[0]['delivery_fee']);
        exit;
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));

        exit;
    }

}

public function loadProductOrderAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $good_id = $this->getRequest()->getParam('good_id');
    $sales_sn = $this->getRequest()->getParam('sales_sn');
    $order_date = $this->getRequest()->getParam('order_date');
    // Tanong Add Function SalesOrderAmount 2016/03/23 14:55
    $QMarket = new Application_Model_Market();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        $result = $QMarket->LoadProductSalesOrder($distributor_id,$sales_sn,$good_id,$order_date);
        if(empty($result))
            exit(json_encode(array('error' => 'Distributor is don\'t have Sales Order')));

        echo json_encode($result[0]['qty']);
         //   echo $result[0]['qty'];
        exit;
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
       // echo $e->getMessage();

        exit;
    }

}


public function loadDistributorNoCheckLimitAction()
{
    $distributor_id = $this->getRequest()->getParam('distributor_id');
    $good_id = $this->getRequest()->getParam('good_id');

    // Tanong Add Function SalesOrderAmount 2016/03/23 14:55
    $QMarket = new Application_Model_Market();

    try{

        if(empty($distributor_id))
            exit(json_encode(array('error' => 'Distributor is not existed.')));


        $result = $QMarket->LoadDistributorNoCheckLimit($distributor_id,$good_id);
        if(empty($result)){
            echo json_encode($result[0]['chk']);
        }else{
            echo json_encode($result[0]['chk']);
        }
        exit;
    }catch(exception $e)
    {
        echo json_encode(array('error' => $e->getMessage()));
       // echo $e->getMessage();

        exit;
    }

}

public function markCancelOrderDeliveryAction() {
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $sn_new = $this->getRequest()->getParam('sn');

    // $sn = $sn_new;
    $sn = substr($sn_new,1,-1);

    $QMarket = new Application_Model_Market();
    $where = $QMarket->getAdapter()->quoteInto('sn = ?', $sn);
    $market = $QMarket->fetchRow($where);

    if(!$market)
    {
        echo '-2'; //sales sn error
        exit;
    }

    try
    {
        $db = Zend_Registry::get('db');
        $db->beginTransaction();


        $canceled_data = array(
            'cancel_delivery_date'      => date('Y-m-d H:i:s'),
            'cancel_delivery_status'    => 1,
            'cancel_delivery_by'        => $userStorage->id,
        );

        
        $QMarket->update($canceled_data, $QMarket->getAdapter()->quoteInto('sn = ?',$sn));
       //  $QLog = new Application_Model_Log();
       //  $QLog->insert( array (
       //      'info'              => $info,
       //      'user_id'           => $userStorage->id,
       //      'ip_address'        => $ip,
       //      'time'              => date('Y-m-d h:i:s'),
       //  ) );

        // commit
        $db->commit();

        echo '0';
        exit;
    }
    catch (exception $e)
    {
        $db->rollback();
        echo '-4';
        exit;
    }
}

public function forceSaleAction()
{

    $good_ids = $this->getRequest()->getParam('good_id', null);
    $nums = $this->getRequest()->getParam('num', null);
    $warehouse_id = $this->getRequest()->getParam('warehouse_id', null);
    $distributor_id = $this->getRequest()->getParam('distributor_id', null);
    $type = $this->getRequest()->getParam('type', null);
    
    $checkInput = true;
    $checkInputMessage = array();
    $check = array(); //lưu kết quả kiểm tra máy

    if (!is_array($good_ids))
    {
        $checkInput = false;
        $checkInputMessage[] = "good_id's type must be array";
    }

    if (!is_array($nums))
    {
        $checkInput = false;
        $checkInputMessage[] = "nums's type must be array";
    }

    if (!is_numeric($distributor_id))
    {
        $checkInput = false;
        $checkInputMessage[] = "'distributor_id' must be a number";
    }
    $QGoodCategory = new Application_Model_GoodCategory();
    $this->view->good_categories = $QGoodCategory->fetchAll();
    $QGood = new Application_Model_Good();

    $this->view->goods = $QGood->get_cache();
    $QGoodColor = new Application_Model_GoodColor();
    $this->view->colors = $QGoodColor->get_cache($where);
    $campaign_id = '2147483647';
    $QForcesale = new Application_Model_ForceSaleDetail();
    $QGood = new Application_Model_Good();

    $forcesale = $QForcesale->forceSale($campaign_id);
    foreach ($forcesale as $key => $value) {
       $result = $QGood->get_price($value['g_id_num'], $value['g_id'], $value['color'], $value['cat_id'], $distributor_id,
        $warehouse_id, $is_sales_price, $is_return, $type, $id , 0 , $campaign_id);

       if ($result['code']==1) {
           $this->view->forcesale = $forcesale;
       }
   }





    // foreach ($good_ids as $good):
    //     if (!is_numeric($good))
    //     {
    //         $checkInput = false;
    //         $checkInputMessage[] = "good_id value must be a number";
    //         break;
    //     }
    // endforeach;

    // foreach ($nums as $num):
    //     if (!is_numeric($num))
    //     {
    //         $checkInput = false;
    //         $checkInputMessage[] = "num value must be a number";
    //         break;
    //     }
    // endforeach;
    // echo "<pre>";
    // print_r($good_ids);
    // print_r($nums);
   if ($checkInput == true)
   {
    $product = array();

        //Cộng dồn số lượng nếu mã sản phẩm trùng
    foreach ($good_ids as $key => $value):
        if (array_key_exists($value, $product))
        {
            $product[$value] += $nums[$key];
        } else
        {
            $product[$value] = $nums[$key];
        }
    endforeach;
    ;
        // print_r($product);

    $db = Zend_Registry::get('db');

        //Lấy số lượng cửa hàng
        // SELECT 
        //         (SELECT COUNT(fss.campaign_id) FROM force_sale fss WHERE fss.campaign_id=fs.campaign_id) AS total_item
        //         ,fs.*
        //         FROM force_sale fs
        //         WHERE 1=1
        //         AND fs.status=1 
        //         AND fs.order_type='1'
        //         AND 1 = (SELECT 1 FROM force_sale_warehouse WHERE w_id ='36')
        //         AND fs.start_date <= DATE(NOW()) AND fs.end_date >= DATE(NOW())
        //         AND (CASE WHEN fs.distributor_all IS NOT NULL THEN '1' ELSE (SELECT COUNT(*) FROM force_sale_distributor WHERE d_id='3009') END)
        //         AND fs.good_id IN(142,140)
        // $select = $db->select()->from(array('fss' => 'force_sale'), array('fs.*'))
        // ->where('d_id = ?', $distributor_id);
        // ->where('d_id = ?', $distributor_id);
        //     echo $select;
        // $qtyStore = $db->fetchOne($select);

        //Lấy số lượng máy demo đã đặt trên từng sản phẩm => good_id, quantity
        // $select_good = $db->select()->from(array('m' => 'market'), array('total' =>
        //         'SUM(m.num)', 'good_id'))->where('d_id  = ?', $distributor_id)->where('cat_id = ?',
        //     PHONE_CAT_ID)->where('type  = ?', FOR_DEMO)->where('status = ?', 1)->where('outmysql_time IS NOT NULL', null)->
        //     group('m.good_id');


        // $result_market_good = $db->fetchAll($select_good);

        //kiểm tra số lượng từng dòng máy có thể đặt
    //     $i = 0;
    //     $arr_good_id = array();

    //     foreach ($product as $good_id => $num):
    //         foreach ($result_market_good as $k => $v):
    //             $avaiable_good = 0; //số máy có thể đặt
    //             $over = 0; //số máy đặt vượt
    //             if ($good_id == $v['good_id']):
    //                 $avaiable_good = $qtyStore - $v['total'];
    //                 $over = $num - $avaiable_good;

    //                 if ($avaiable_good < 0):
    //                     $avaiable_good = 0;
    //                 endif;
    //                 //No Limit Sell Produc Demo
    //                 /*
    //                     if ($avaiable_good < $num):
    //                         $arr_good_id[] = $good_id;
    //                         $check[$i]['good_id'] = $good_id;
    //                         $check[$i]['avaiable'] = $avaiable_good;
    //                         $check[$i]['over'] = $over;
    //                         $i += 1;
    //                     endif;
    //                 */
    //             endif;
    //         endforeach;
    //     endforeach;

    //     if ($arr_good_id)
    //     {
    //         //Lấy tên sản phẩm
    //         $QGood = new Application_Model_Good();
    //         $select = $QGood->select()->from('good', array('name', 'id'))->where('id IN (?)',
    //             $arr_good_id);
    //         $result_name = $QGood->fetchAll($select);

    //         //tạo mảng gồm các phần tử id,total,name
    //         foreach ($result_name as $name):
    //             foreach ($check as $k => $v):
    //                 if ($name['id'] == $v['good_id']):
    //                     $check[$k]['name'] = $name['name'];
    //                 endif;
    //             endforeach;
    //         endforeach;
    //     } else
    //     {
    //         $check = array();
    //     }

    //     $arr_result = array('status' => true, 'result' => $check);
    //     $this->_helper->json->sendJson($arr_result);
    // } else
    // {
    //     $arr_result = array('status' => false, 'messages' => $checkInputMessage);
    //     $this->_helper->json->sendJson($arr_result);
    // }
}  
$this->_helper->layout->disableLayout();
    // $this->_helper->viewRenderer->setNoRender(true);
}

// Pungpond
public function amphuresAction()
{
if (!$this->getRequest()->isXmlHttpRequest())
    exit;

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$province = $this->getRequest()->getParam('province');

$QAmphures = new Application_Model_ShippingAmphures();
$amphures = $QAmphures->getAmphures($province);



echo json_encode($amphures);
exit;
}
public function districtsAction()
{
if (!$this->getRequest()->isXmlHttpRequest())
    exit;

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$amphures = $this->getRequest()->getParam('amphures');

$QDistricts = new Application_Model_ShippingDistricts();
$districts = $QDistricts->getDistricts($amphures);



echo json_encode($districts);
exit;
}
public function zipcodeAction()
{
if (!$this->getRequest()->isXmlHttpRequest())
    exit;

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$districts = $this->getRequest()->getParam('districts');

$QZipcodes = new Application_Model_ShippingZipcodes();
$zipcode = $QZipcodes->getZipCode($districts);



echo json_encode($zipcode);
exit;
}

public function checkCampainAutoAddAction()
{
if (!$this->getRequest()->isXmlHttpRequest())
    exit;
$edit = $this->getRequest()->getParam('edit', null);
$cat_id = $this->getRequest()->getParam('cat_id', null);
$good_ids = $this->getRequest()->getParam('good_id', null);
$warehouse_id = $this->getRequest()->getParam('warehouse_id', null);
$type = $this->getRequest()->getParam('type', null);
$distributor_id = $this->getRequest()->getParam('distributor_id', null);
$sn = $this->getRequest()->getParam('sn', null);
$num = $this->getRequest()->getParam('num', null);

if ($edit==1) {
    $cam_1 = array();
    $csum = array(); 
    foreach ($cat_id as $key => $cat) {


       if ($cat == 11) {

        $findFoceSale = new Application_Model_Warehouse();
        $QForcesale = new Application_Model_ForceSaleDetail();
        $paramsFoce = array(
            'good_id'          => $good_ids[$key],
            'warehouse_id'     => intval( $warehouse_id ),
            'type'             => intval( $type ),
            'd_id'             => intval( $distributor_id ),
        );
                        //Pungpond
        $foce_sale = $findFoceSale->findForceSales($paramsFoce) ; 
        if ($foce_sale) {

            $forcesale = $QForcesale->forceSale($foce_sale['campaign_id']);

            for($i=0;$i<count($forcesale);$i++) {
                $forcesale[$i]['g_id_num'] = $forcesale[$i]['g_id_num'] * $num[$key];
                                // $forcesale[$i]['g_id']] += 2;
                if(!isset($csum[$forcesale[$i]['g_id']])) $csum[$forcesale[$i]['g_id']] = 0;
                $csum[$forcesale[$i]['g_id']] += $forcesale[$i]['g_id_num'];
            }
        }
    }


}
if ($foce_sale) {


    $acc = array();
    foreach ($cat_id as $key => $cat2) {
        if ($cat2 == 12) {
            $acc[$good_ids[$key]] = $num[$key];
        }
    }
    unset($acc['127']);
                // print_r($acc);
                // print_r($csum);
    $result=array_diff_assoc($acc,$csum); 

    if (isset($result) and $result) {
        $check_acc = 0;
    }else{
        $check_acc = 1;
    }

}else{

    $check_acc = 1;
}

print_r($check_acc);
exit;
}else{
$check_acc = 1;
print_r($check_acc);
}
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);
}


// return json
public function checkDuplicatePaymentAction()
{
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$sn = $this->getRequest()->getParam('sn', null);
$bank_id = $this->getRequest()->getParam('bank_id', null);
$pay_money = $this->getRequest()->getParam('pay_money', null);
$from_time = $this->getRequest()->getParam('from_time', null);
$to_time = $this->getRequest()->getParam('to_time', null);

$QCheckmoney = new Application_Model_Checkmoney();
$result = $QCheckmoney->checkDuplicatePayment($sn,$bank_id,$pay_money,$from_time,$to_time);

    //print_r($result);die;
echo json_encode(['status' => 200,'data' => $result]);

}

public function borrowingDetailAction()
{
$this->_helper->layout->disableLayout();
$sn = $this->getRequest()->getParam('sn');

$QGood = new Application_Model_Good();
$QGoodColor = new Application_Model_GoodColor();
$QGoodCategory = new Application_Model_GoodCategory();
$QBI = new Application_Model_BorrowingItem();

$goods = $QGood->get_cache();
$goodColors = $QGoodColor->get_cache();
$goodCategory = $QGoodCategory->get_cache();

$this->view->details = $QBI->getBorrowingDetailsBySN($sn);

$this->view->sn = $sn;
$this->view->goods = $goods;
$this->view->goodColors = $goodColors;
$this->view->goodCategory = $goodCategory;
}

public function checkTrueCodeAction()
{
$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);

$true_code = $this->getRequest()->getParam('true_code', null);
$QEDC = new Application_Model_ExternalDistributorCode();

$where = $QEDC->getAdapter()->quoteInto('partner_code = ?', $true_code);
$get_true_code = $QEDC->fetchAll($where);

if(count($get_true_code)==0){
    echo json_encode(['status' => 1]);
}else{
    echo json_encode(['status' => 2]);
}
}

public function _initConfig($company_id)
{
$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    if($company_id=='1')//OPPO
    {
        $db = Zend_Db::factory($config->resources->db);
        $db->query("SET NAMES utf8;");
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }else if($company_id=='2'){//ONEPLUS
        $db = Zend_Db::factory($config->resources->dboneplus);
        $db->query("SET NAMES utf8;");
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Registry::set('db', $db);
    }
    $setdb = Zend_Registry::get('db');
    return $setdb;
}

public function loadStaffProductAction()
{
    $company_id = $this->getRequest()->getParam('company_id');
    $cat_id = $this->getRequest()->getParam('cat_id');
    $good_id = $this->getRequest()->getParam('good_id');
    //echo $company_id;die;
    $this->_initConfig($company_id);

    $QGood = new Application_Model_Good();

    if ($good_id) {
        $where = $QGood->getAdapter()->quoteInto('id = ?', $good_id);
        $good = $QGood->fetchRow($where);

        if ($good)
        {
            $aColor = array_filter(explode(',', $good->color));
            if ($aColor)
            {
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                $colors = $QGoodColor->fetchAll($where);
                echo json_encode(array('colors' => $colors->toArray()));
                exit;
            }
        }

    } elseif ($cat_id) {
        if($cat_id ==11){
            $goods = $QGood->fetchAll(
                $QGood->select()
                ->where('cat_id = ?', $cat_id)
                ->where('del = ?', 0)
                ->order('add_time DESC')
                , 'name');
        }else{
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
            $goods = $QGood->fetchAll($where, 'name');
        }    
        

        echo json_encode(array('goods' => $goods->toArray()));
        exit;
    } else {
        echo json_encode(array());
        exit;
    }
}


public function loadStaffWarehouseAction()
{
    $company_id = $this->getRequest()->getParam('company_id');
    //echo $company_id;die;
    $this->_initConfig($company_id);

    $QWarehouse = new Application_Model_Warehouse();
    if ($company_id){
        $where = array();
        $Warehouse = $QWarehouse->fetchAll($where, 'name');
        echo json_encode(array('warehouse' => $Warehouse->toArray()));
        exit;
    }else{
        echo json_encode(array());
        exit;
    }
    
}

public function loadStaffBankAction()
{
    $company_id = $this->getRequest()->getParam('company_id');
    //echo $company_id;die;
    $this->_initConfig($company_id);

    $QBank = new Application_Model_Bank();
    if ($company_id){
        $where = array();
        $Bank = $QBank->fetchAll($where, 'name');
        echo json_encode(array('bank' => $Bank->toArray()));
        exit;
    }else{
        echo json_encode(array());
        exit;
    }
    
}


public function loadStaffDistributorAction()
{
    $company_id = $this->getRequest()->getParam('company_id');
    $this->_initConfig($company_id);
    $db = Zend_Registry::get('db');
    // echo $warehouse_type_id;die;   
    
    /*if ($company_id=="1"){
        $distributor_id="24026"; //OPPO
    }else{
        $distributor_id="47401"; //ONEPLUS
    }*/
    $sql="SELECT p.*,CONCAT(p.`store_code`,' ',p.`title`) AS distributor_name FROM  distributor p where p.for_staff=1";
    //echo $sql;die;
    $result = $db->fetchAll($sql);
    echo json_encode(array('distributor'=>$result));
    exit;
}

public function loadStaffDiscountAction()
{
    $company_id = $this->getRequest()->getParam('company_id');
    //echo $company_id;die;
    $this->_initConfig($company_id);

    $QEpPrivilegesTranOrder     = new Application_Model_EpPrivilegesTranOrder();

    if ($company_id){
        $discount = $QEpPrivilegesTranOrder->privileges_discount($company_id,$discount_id);
        echo json_encode(array('discount' => $discount));
        //print_r($discount);die;
        //echo json_encode(array('discount' => $discount->toArray()));
        exit;
    }else{
        echo json_encode(array());
        exit;
    }
    
}

public function checkImeiReturnAction()
{

    // $this->_helper->layout->disableLayout();
    // $this->_helper->viewRenderer->setNoRender(true);

    $store_id = $this->getRequest()->getParam('store_id');
    $warehouse_id = $this->getRequest()->getParam('warehouse_id');
    $imei_list = $this->getRequest()->getParam('imei_list');
    $return_type = $this->getRequest()->getParam('return_type');

    $QDistributor = new Application_Model_Distributor();
    $QImeiReturn = new Application_Model_ImeiReturn();
    $QImeiLock = new Application_Model_ImeiLock();
    $QImei = new Application_Model_Imei();

    $db = Zend_Registry::get('db');
    $result_phone_data=null;

    try {

        $imei_list_check_imei_locked = str_replace("'","",$imei_list);
        $imei_list_check_imei_locked = explode(',', $imei_list_check_imei_locked);

        $check_return_on = $QImeiReturn->checkreturnOn($imei_list_check_imei_locked);
        $getImeiLock = $QImeiLock->checkImeiLock($imei_list_check_imei_locked);

        $ImeiExit = '';

        for ($i=0; $i < count($imei_list_check_imei_locked); $i++) { 

            $check_imei_exit = $QImei->checkimeiOne($imei_list_check_imei_locked[$i]);

            if($check_imei_exit['imei_sn'] != $imei_list_check_imei_locked[$i]) {
                $ImeiExit .= '[ '. $imei_list_check_imei_locked[$i] .' ]';
            }

        }

        if($ImeiExit) {
           echo json_encode(array('error' => 'Please Check Imei Not Exit In System : ' . $ImeiExit, 'check'=>3));
           exit;
       }


        // Check Imei In Store Stock
       if(isset($return_type) && $return_type == 1) {

        $where_imei = array();
        $where_imei[] = $QImei->getAdapter()->quoteInto('imei_sn IN (?)',$imei_list_check_imei_locked);
        $where_imei[] = $QImei->getAdapter()->quoteInto('store_id =?',$store_id);
        $imei_arr = $QImei->fetchAll($where_imei);


        if(!$imei_arr){
            echo json_encode(array('error' => 'IMEI Not In Store Stock. Check and try agian !','check'=>3));
            exit;
        }

    }

    // print_r($listImeiError); die();


        // Check Imei In Warehouse Agent Stock
    if(isset($return_type) && $return_type == 2) {

        $where_imei = array();
        $where_imei[] = $QImei->getAdapter()->quoteInto('imei_sn IN (?)',$imei_list_check_imei_locked);
        $where_imei[] = $QImei->getAdapter()->quoteInto('warehouse_id =?',$warehouse_id);
        $where_imei[] = $QImei->getAdapter()->quoteInto('store_id is not null', null);
        $where_imei[] = $QImei->getAdapter()->quoteInto('distributor_id is not null', null);
        $imei_arr = $QImei->fetchAll($where_imei);


        if(!$imei_arr){
            echo json_encode(array('error' => 'IMEI Not In Warehouse Stock. Check and try agian !','check'=>3));
            exit;
        }
    }

    if($check_return_on){
        echo json_encode(array('error' => 'IMEI Return On, Please Check Return To System : ' . $listImeiLock,'check'=>3));
        exit;
    }

    $listImeiLock = '';
    foreach ($getImeiLock as $key => $value) {
        if($key == 0){
            $listImeiLock = $value['imei_log'];
        }else{
            $listImeiLock .= ','. $value['imei_log'];
        }

        $imei_list = str_replace($value['imei_log'],"",$imei_list);
    }

    if($listImeiLock){
        echo json_encode(array('error' => 'IMEI Locked : ' . $listImeiLock,'check'=>3));
        exit;
    }


    $select = $db->select()
    ->from(array('i'=> 'imei'),array('i.warehouse_id','g2.cat_id','i.distributor_id','i.store_id,i.good_id,i.good_color,i.imei_sn,COUNT(DISTINCT i.imei_sn)AS qty,IFNULL(i.out_price,0)AS imei_out_price1
        ','i.sales_sn'))
    ->joinLeft(array('m' => 'market'), 'm.sn=i.sales_sn AND m.good_id=i.good_id AND m.good_color=i.good_color', array('m.sn_ref','m.invoice_number,round(IFNULL(m.total/m.num,0),2) AS imei_out_price'))
    ->joinLeft(array('g' => 'good'), 'g.id = m.good_id', 
        array('ROUND(IF(
            (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
            IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
            )
            -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)- IFNULL(
            (SELECT 
            SUM(IFNULL(price, 0)) 
            FROM
            bvg_imei 
            WHERE imei_sn IN (i.imei_sn) 
            AND `d_id` = m.`d_id` 
            AND invoice_number = m.invoice_number COLLATE utf8_unicode_ci 
            AND good_id = m.`good_id` 
            AND good_color = m.`good_color`),
            0
            )

            ,2) AS out_price'
            ,'ROUND((
            IF(
            (IFNULL(bm.imei_sn, IFNULL(imp.imei_sn, 0))) <= 0,
            IFNULL(m.total / m.num, 0),IFNULL(m.total / m.num, 0)-(IFNULL(imp.total_amount, 0))
            ) 
            -(((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100)
            -IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0)

            )* COUNT(i.imei_sn)

            ,2) AS total_price
            ,m.`spc_discount`,m.`user_id`  
            ,ROUND((((m.`total`/m.`num`)*IFNULL(m.`spc_discount`,0))/100),2) AS spc_discount
            ,IFNULL((SELECT SUM(IFNULL(price,0)) FROM bvg_imei WHERE imei_sn IN(i.imei_sn) AND `d_id`=m.`d_id` AND invoice_number= m.invoice_number COLLATE utf8_unicode_ci AND good_id=m.`good_id` AND good_color=m.`good_color`),0) AS bvg_price'))
    ->joinLeft(array('bm' => 'bvg_imei'), 'bm.imei_sn = i.imei_sn AND bm.d_id=i.distributor_id AND bm.invoice_number= m.invoice_number COLLATE utf8_unicode_ci', array('bm.imei_sn as bvg_imei_sn'))
    ->joinLeft(array('imp' => 'credit_note_cp_import'), 'imp.imei_sn = i.imei_sn  AND imp.d_id=i.distributor_id  ', array('imp.imei_sn as imp_imei_sn'))

    ->joinLeft(array('ir' => 'imei_return'),'ir.imei_sn = i.imei_sn AND ir.return_type = 1',array())
    ->joinLeft(array('m2' => 'market'),'m2.sn = ir.sales_order_sn and m2.good_id = i.good_id',array('agent_to_dealer_price' => 'm2.price','agent_sale_invoice' => 'm2.invoice_number'))
    ->joinLeft(array('g2' => 'good'),'g2.id = i.good_id',array('good_name' => 'g2.name'))
    ->joinLeft(array('gc' => 'good_color'),'gc.id = i.good_color',array('good_color_name' => 'gc.name'))
    ->joinLeft(array('bn' => 'brand'),'bn.id = g2.brand_id',array('brand_name' => 'bn.name'));


    $select->where('i.imei_sn in('.$imei_list.')', null);
    $select->group('m.sn_ref');
    $select->group('i.good_id');
    $select->group('i.good_color');
    $select->order('i.good_id');

    // echo $select; die();

    if(isset($return_type) && $return_type == 1) {
       $select->where('i.store_id =?',$store_id);
   }

   if(isset($return_type) && $return_type == 2){

    $select->where('i.warehouse_id =?',$warehouse_id);
    $select->where('i.store_id is null', null);
    $select->where('i.distributor_id is null', null);

}

$result_phone_data = $db->fetchAll($select);

$select->group('i.imei_sn');

$result_imei_data = $db->fetchAll($select);

if($result_phone_data){

    echo json_encode(array('check'=>1,'result_phone'=>$result_phone_data,'result_imei'=>$result_imei_data));
    exit;

}else{

    echo json_encode(array('error' => 'Please Check Imei, Store or Warehouse Return and Try agian !','check'=>3));
    exit;

}

} catch (Exception $e) {

    echo json_encode(array('error' => $e->getMessage()));
    exit;

}

return false;

}

public function loadGoodColorAction()
{
    $good_id = $this->getRequest()->getParam('good_id');

    $QGood = new Application_Model_Good();

    if ($good_id) {
        $where = $QGood->getAdapter()->quoteInto('id IN (?)', $good_id);
        $good = $QGood->fetchAll($where);


        if ($good)
        {
            $aColor = array();
            foreach($good as $key => $value) {
                $aColor[] = array_filter(explode(',', $value->color));
            }

            if ($aColor)
            {
                $QGoodColor = new Application_Model_GoodColor();
                $where = $QGoodColor->getAdapter()->quoteInto('id IN (?)', $aColor);

                $colors = $QGoodColor->fetchAll($where);
                echo json_encode(array('colors' => $colors->toArray()));
                exit;
            }
        }
    }
}

}
