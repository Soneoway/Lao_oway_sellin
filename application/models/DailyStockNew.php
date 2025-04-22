<?php
class Application_Model_DailyStockNew extends Zend_Db_Table_Abstract
{
    protected $_name = 'daily_stock_new';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        switch ($params['document_type']) {
            case '1':
            //echo 'Storage Daily';
                $select = $db->select()
                ->from(array('p' => 'daily_stock_new'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Storage Daily' as document_type_name,stock_date as document_date,'1' as document_type,CONCAT('warehouse_storage_daily_',stock_date,'_new.xlsx')AS file_name,CONCAT('export_stock/warehouse_storage_daily_',p.stock_date,'_new.xlsx')AS file_link"), 'p.*'));
                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('p.stock_date');
                $select->order('p.stock_date desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);

            break;
            case '2':
            //echo 'Cancelled Daily';
                $select = $db->select()
                ->from(array('p' => 'imei_cancel_log'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Cancelled Daily' AS document_type_name,DATE(p.`date_canceled`) AS document_date,'1' AS document_type
,CONCAT('warehouse_cancelled_daily_',DATE(p.`date_canceled`),'.xlsx')AS file_name,CONCAT('export_cancel/warehouse_cancelled_daily_',DATE(p.`date_canceled`),'.xlsx')AS file_link"), 'p.*'));


                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('DATE(p.`date_canceled`) >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('DATE(p.`date_canceled`) <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('DATE(p.`date_canceled`)');
                $select->order('DATE(p.`date_canceled`) desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);
            break;
            case '3':
            //echo 'Storage Daily Digital';
                $select = $db->select()
                ->from(array('p' => 'daily_stock_digital_new'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Storage Daily Digital' AS document_type_name,stock_date AS document_date,'1' AS document_type,CONCAT('warehouse_storage_daily_digital_',stock_date,'_new.xlsx')AS file_name,CONCAT('export_digital/warehouse_storage_daily_digital_',p.stock_date,'_new.xlsx')AS file_link"), 'p.*'));
                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('p.stock_date');
                $select->order('p.stock_date desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);
            break;
            case '4':
            //echo 'Storage Daily Acc';
                $select = $db->select()
                ->from(array('p' => 'daily_stock_acc_new'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Storage Daily Acc' AS document_type_name,stock_date AS document_date,'4' AS document_type,CONCAT('warehouse_storage_daily_acc_',stock_date,'_new.xlsx')AS file_name,CONCAT('export_acc/warehouse_storage_daily_acc_',p.stock_date,'_new.xlsx')AS file_link"), 'p.*'));
                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('p.stock_date');
                $select->order('p.stock_date desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);
            break;
            case '5':
            //echo 'Daily Submitted';
                $select = $db->select()
                ->from(array('p' => 'daily_stock_new'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Daily Submitted' as document_type_name,stock_date as document_date,'5' as document_type,CONCAT('daily_submitted_',stock_date,'.xlsx')AS file_name,CONCAT('export_rb/daily_submitted_',p.stock_date,'.xlsx')AS file_link"), 'p.*'));
                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('p.stock_date');
                $select->order('p.stock_date desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);
            break;
            case '6':
            //echo 'Daily Distributor Surplus';
                $select = $db->select()
                ->from(array('p' => 'daily_stock_new'),
                    array(new Zend_Db_Expr("SQL_CALC_FOUND_ROWS 'Daily Distributor Surplus' as document_type_name,stock_date as document_date,'6' as document_type,CONCAT('daily_distributor_surplus_',stock_date,'.xlsx')AS file_name,CONCAT('export_surplus/daily_distributor_surplus_',p.stock_date,'.xlsx')AS file_link"), 'p.*'));
                if (isset($params['doc_created_at_from']) and $params['doc_created_at_from']){
                        list( $day, $month, $year ) = explode('/', $params['doc_created_at_from']);
                if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date >= ?', $year.'-'.$month.'-'.$day.' 00:00:00');
                }
                if (isset($params['doc_created_at_to']) and $params['doc_created_at_to']){
                    list( $day, $month, $year ) = explode('/', $params['doc_created_at_to']);

                    if (isset($day) and isset($month) and isset($year) )
                        $select->where('p.stock_date <= ?', $year.'-'.$month.'-'.$day.' 23:59:59');
                }
                $select->group('p.stock_date');
                $select->order('p.stock_date desc');
                if ($limit)
                $select->limitPage($page, $limit);
                $result = $db->fetchAll($select);

            break;
            default:
            //echo '-';
            break;
        }

        //echo $select;die;
        

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");

        return $result;
    }


}
