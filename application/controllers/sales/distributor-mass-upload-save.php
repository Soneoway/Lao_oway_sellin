<?php
$this->_helper->layout->disableLayout();

$save_folder   = 'distributor';
$new_file_path = '';
$requirement   = array(
                'Size'      => array('min' => 5, 'max' => 5000000),
                'Count'     => array('min' => 1, 'max' => 1),
                'Extension' => array('xls', 'xlsx'),
            );

try {
    set_time_limit(0);
    ini_set('memory_limit', -1);

    $file = My_File::get($save_folder, $requirement);

    if (!$file || !count($file)) throw new Exception("Upload failed");

    $inputFileName = My_File::getDefaultDir() .DIRECTORY_SEPARATOR.$save_folder. DIRECTORY_SEPARATOR. $file['folder'] .DIRECTORY_SEPARATOR. $file['filename'];

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    // My_File_Progress::set('distributor', 0);
} catch (Exception $e) {
    $this->view->errors = $e->getMessage();
    return;
}

//read file
include 'PHPExcel/IOFactory.php';

//  Read your Excel workbook
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch (Exception $e) {
    $this->view->errors = $e->getMessage();
    return;
}

//  Get worksheet dimensions
$sheet = $objPHPExcel->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

try{
    $QDistributorMapping = new Application_Model_DistributorMapping();
    $QDistributor = new Application_Model_Distributor();
    $array_invalid = array();

    //  Loop through each row of the worksheet in turn
    for ($row = 6; $row <= $highestRow; $row++) {
        $percent = round($row * 100 / $highestRow, 1);
        // My_File_Progress::set('distributor', $percent);

        //  Read a row of data into an array
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
            NULL, TRUE, FALSE);

        $rowData = $rowData[0];

        $check = true;
        // for ($i=1; $i < 7; $i++) {
            if (empty($rowData[1]) || empty($rowData[2])) {
                $array_invalid[] = !empty($rowData[1]) ? $rowData[1] : ('A' . $row);
                $check = false;
            }
        // }

        if (!$check) {
            continue;
        } else { // INSERT
            $data = array(
                'title'         => 'TGDĐ - ' . $rowData[1],
                'name'          => $rowData[7],
                'tel'           => $rowData[8],
                'email'         => '',
                'warehouse_id'  => intval($rowData[0]),
                'region'        => 0,
                'add'           => $rowData[3],
                'add_tax'       => $rowData[5],
                'admin'         => 0,
                'rank'          => 13, // rank của TGDĐ
                'unames'        => $rowData[2],
                'mst_sn'        => $rowData[4],
                'nots'          => '',
                'store_code'    => My_Distributor::generateStoreCode('TGDĐ-'), // tạo code tự động
                'retailer_type' => 1,
                'parent'        => 2316, // ID của TGDĐ_KA
                'partner_id'    => $rowData[6],
                'is_ka'         => 1, // là KA
                'is_internal'   => 0, // Outside
            );

            try {
                PC::db($data);
                $id = $QDistributor->add_new($data);
                $code = intval(My_String::trim($rowData[6]));

                $where = $QDistributorMapping->getAdapter()->quoteInto('code = ?', $code);
                $code_check = $QDistributorMapping->fetchRow($where);

                if (!$code_check) {
                    $data = array(
                        'distributor_id' => $id,
                        'code' => $code,
                    );

                    $QDistributorMapping->insert($data);
                }
            } catch (Exception $e) {
                $array_invalid[] = $rowData[1] . ' - ' . $e->getMessage();
            }
        }
    } // END big for

    $cache = Zend_Registry::get('cache');
    $cache->remove('distributor_mapping_cache');
    $cache->remove('distributor_cache');
    $cache->remove('distributor_2_cache');
    $cache->remove('distributor_with_store_code_cache');
    $cache->remove('distributor_all_cache');
    $cache->remove('distributor_warehouse_cache');

    $this->view->array_invalid = $array_invalid;
    // My_File_Progress::set('distributor', 100);

} catch(Exception $e) {
    $cache = Zend_Registry::get('cache');
    $cache->remove('distributor_mapping_cache');

    $this->view->errors = $e->getMessage();
    return;
}