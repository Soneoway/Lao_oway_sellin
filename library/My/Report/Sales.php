<?php
/**
 * @author buu.pham
 * @create 2015-08-26 11:40
 * Xuất các report liên quan sales
 */
class My_Report_Sales
{
    public static function distributorList($data)
    {
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        
        while(@ob_end_clean());
        ob_start();
        $filename = 'Distributor list - ' . date('d-m-Y H-i-s');
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        header('Content-Type: application/csv; charset=utf-8');
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'Distributor Code',
            // 'Store Code',
            // 'Finance  Code',
            'Distributor Name',
            'Extarnal Code',
            'Upper-level Distributor',
            'Warehouse Name',
            'Distributor Type',
            'Number of Store',
            'Shops in cooperating',
            'Price System',
            'Office',
            'Affiliated Client',
            'Certificate Name',
            'Customer Type',

            // 'BRANCH OF NUMBER',
            'Leader Name',

            // 'Company Name',
            // 'Contact',
            // 'Address of Company ',
            // 'Default Address for Delivery',
            'Leader Tel.',

            // 'Email',
            // 'Grand Area',
            // 'Area',
            // 'Province',
            // 'District',
            // 'KA Type',
            // 'Channel Type',

            'Leader IdCardNo',
            'Status',
            'Remark',
            'Creation Time',
            'Last Modified Time',

            // 'Distributor Type Group',
            // 'Sales Man',
            // 'Rank',
            // 'Rank Name',
            // 'MST No.',
            // 'KA',
            // 'Internal',
            // 'Created At',
            // 'Create Period',
            // 'Creator user id',
            // 'Creator name',
            // 'Update Date',
            // 'Update By',
            // 'Notes',
            // 'Finance Group',
            // 'Partner Name',
            // 'Partner Code',
            // 'Sale IN Status',
            // 'Distributor Status',
            // 'OPPO Club Grade',
            // 'Verify'
            );

        fputcsv($output, $heads);

        $QStaff = new Application_Model_Staff();
        $QDistributor = new Application_Model_Distributor();

        $staffs = $QStaff->get_cache();

        $i = 1;

        $db = Zend_Registry::get('db');
        $data = $db->query($data);

        $grand_e1 = array(81,82,83,110,111,112);
        $grand_e2 = array(85,86,87,115,88,89,116,117);
        $grand_e3 = array(90,91,92,93,113);
        $grand_e4 = array(94,95,96);
        $grand_e5 = array(97,109);
        $grand_w1 = array(98,99,100,101,102,114);
        $grand_w2 = array(103,104,105);
        $grand_w3 = array(106,107,108);

        foreach ($data as $item)
        {

            $distributor_data = $QDistributor->getSuperiorDistributor($item['warehouse_id']);


            $excel_area_name = My_Region::getValue($item['district'], My_Region::Area);

            $excel_area_id = My_Region::getValue($item['district'], My_Region::Area, My_Region::ID);

            if ( in_array($excel_area_id, $grand_e1) ) { $grand_area = 'BKK East-1'; } 
            else if ( in_array($excel_area_id, $grand_e2) ) { $grand_area = 'BKK East-2'; }
            else if ( in_array($excel_area_id, $grand_e3) ) { $grand_area = 'BKK East-3'; }
            else if ( in_array($excel_area_id, $grand_e4) ) { $grand_area = 'BKK East-4'; }
            else if ( in_array($excel_area_id, $grand_e5) ) { $grand_area = 'BKK East-5'; }
            else if ( in_array($excel_area_id, $grand_w1) ) { $grand_area = 'BKK West-1'; }
            else if ( in_array($excel_area_id, $grand_w2) ) { $grand_area = 'BKK West-2'; }
            else if ( in_array($excel_area_id, $grand_w3) ) { $grand_area = 'BKK West-3'; }
            else { $grand_area = $excel_area_name; }

            $row = array();
            $row[]  = $i++;
            $row[]  = $item['distributor_code'];

            // $row[] = $item['store_code'];
            // $row[] = $item['finance_code'];

            $row[]  = $item['title'];
            $row[]  = $item['external_serial'];
            $row[]  = $distributor_data[0]['title'];
            $row[]  = $distributor_data[0]['name'];
            $row[]  = '';
            $row[]  = '';
            $row[]  = '';
            $row[]  = '';
            $row[]  = My_Region::getValue($item['district'], My_Region::Province);
            $row[]  = $item['client_name'];
            $row[]  = $item['short_name'];
            $row[]  = '';


            // $row[] = $item['branch_amout'];

            $row[] = $item['owner_name'];

            // $row[] = isset($item['unames']) ? $item['unames'] : '';
            // $row[] = isset($item['name']) ? $item['name'] : '';
            // $row[] = isset($item['add']) ? $item['add'] : '';
            // $row[] = isset($item['add_tax']) ? $item['add_tax'] : '';

            $row[] = isset($item['tel']) ? ("=\"" . $item['tel'] . "\"") : '';

            // $row[] = isset($item['email']) ? $item['email'] : '';

            // $row[] = $grand_area;
            // $row[] = $excel_area_name;
            // $row[] = My_Region::getValue($item['district'], My_Region::District);
            // $row[] = My_Region::getValue($item['district'], My_Region::District);

            // if(isset($item['org_id']) && $item['org_id'] == 1){
            //     $row[] = 'อื่นๆ';
            // }else{
            //     $row[] = isset($item['org_name']) ? $item['org_name'] : '-';
            // }

            // switch ($item['group_type_id']) {
            //     case '10':
            //         $row[] = 'Brand Shop';
            //         break;
            //     case '11':
            //         $row[] = 'Brand Shop By Dealer';
            //         break;
            //     case '12':
            //         $row[] = 'Brand Shop-ORG';
            //         break;
            //     case '13':
            //         $row[] = 'Brand Shop by KR Dealer';
            //         break;
            //     case '1':
            //         $row[] = 'Dealer and Hub';
            //         break;
            //     case '8':
            //         $row[] = 'Digital';
            //         break;
            //     case '7':
            //         $row[] = 'Export';
            //         break;
            //     case '3':
            //         $row[] = 'KA(ORG)';
            //         break;
            //     case '2':
            //         $row[] = 'KR-Dealer';
            //         break;
            //     case '5':
            //         $row[] = 'Online';
            //         break;
            //     case '4':
            //         $row[] = 'Operator';
            //         break;
            //     case '9':
            //         $row[] = 'Service Shop';
            //         break;
            //     case '6':
            //         $row[] = 'Staff';
            //         break;
            //     default:
            //         $row[] = '-';
            //         break;
            // }

            if($item['status'] == 1) {
                $status_name = "In Cooperation";
            }elseif($item['status'] == 2) {
                $status_name = "Suspend Cooperation";
            }elseif($item['status'] == 3){
                $status_name = "Close";
            }

            $row[]  = '';
            $row[]  = $status_name;
            $row[]  = $item['remark'];
            $row[]  = isset($item['add_time']) ? $item['add_time'] : '';
            $row[]  = isset($item['update_date']) ? $item['update_date'] : '';

            // switch ($item['rank']) {
            //     case '1':
            //         $rank_name = 'ORG-WDS(1)';
            //         break;
            //     case '2':
            //         $rank_name = 'ORG(2)';
            //         break;
            //     case '3':
            //         $rank_name = 'Online and Staff(3)';   
            //         break; 
            //     case '5':
            //         $rank_name = 'ORG-Dtac/Advice(5)';
            //         break;  
            //     case '6':
            //         $rank_name = 'ORG-Lotus/Power by(6)';
            //         break;  
            //     case '7':
            //         $rank_name = 'Dealer(7)';
            //         break;  
            //     case '8':
            //         $rank_name = 'HUB(8)';
            //         break;  
            //     case '9':
            //         $rank_name = 'Laos(9)';
            //         break;  
            //     case '10':
            //         $rank_name = 'Brand Shop/Service(10)';
            //         break;  
            //     case '11':
            //         $rank_name = 'King Power(11)';
            //         break;  
            //     case '12':
            //         $rank_name = 'Jaymart(12)';
            //         break;  
            //     case '13':
            //         $rank_name = 'Brand Shop By Dealer(13)';
            //         break;  
            //     case '14':
            //         $rank_name = 'KR Dealer(14)';
            //         break;    
            //     case '15':
            //         $rank_name = 'TRUE(15)';
            //         break;                                     
            // }

            // $row[] = isset($item['admin']) ? $item['admin'] : '';
            // $row[] = isset($item['rank']) ? $item['rank'] : '';
            // $row[] = $rank_name;
            // $row[] = isset($item['mst_sn']) ? ("=\"" . $item['mst_sn'] . "\"") : '';
            // $row[] = isset($item['is_ka']) && $item['is_ka'] ? 'X' : '';
            // $row[] = isset($item['is_internal']) && $item['is_internal'] ? 'X' : '';
            // $row[] = isset($item['add_time']) ? $item['add_time'] : '';
            // $row[] = isset($item['add_time']) ? date("Y_m", strtotime($item['add_time'])) : '';
            // $row[] = isset($item['create_by']) ? $item['create_by'] : '';
            // $row[] = isset($item['created_name']) ? $item['created_name'] : '';

            // $row[] = isset($item['update_date']) ? $item['update_date'] : '';
            // $row[] = isset($item['update_name']) ? $item['update_name'] : '';

            // $row[] = isset($item['nots']) ? $item['nots'] : '';
            // $row[] = isset($item['finance_group']) ? $item['finance_group'] : '';

            // $row[] = isset($item['partner_name']) ? $item['partner_name'] : '';
            // $row[] = isset($item['partner_code']) ? $item['partner_code'] : '';

            // $salein_status = 'Inactive';
            // if(isset($item['salein_inactive']) && $item['salein_inactive']){
            //     $salein_status = 'Active';
            // }
            // $row[] = $salein_status;

            // $distribitor_status = 'Active';
            // if(isset($item['del']) && $item['del']){
            //     $distribitor_status = 'Deleted';
            // }
            // $row[] = $distribitor_status;

            // $row[] = $item['oc_grade'];

            // $verify = 'Non Verify';
            // if(isset($item['activate']) && $item['activate'] == 1){
            //     $verify = 'Verified';
            // }
            // $row[] = $verify;

            fputcsv($output, $row);

            unset($item);
            unset($row);
        }

        unset($data);
        exit;
    }
}
