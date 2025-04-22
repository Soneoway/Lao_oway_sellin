<?php
class FinanceExportController extends My_Controller_Action{
	
	 function oppoAllGreenAction() 
	{ 
		$key_sn = $_GET['sn'];
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$QOppoGreenAll   = new Application_Model_OppoAllGreenRewardCn();
		$sql = $QOppoGreenAll->Export($key_sn);
        // this function Copy from _exportExcel4 becuase cannot use PHPExcel here! //
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $db = Zend_Registry::get('db');
        set_time_limit(0);
        error_reporting(~E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);
        $filename = 'OPPO All Green Report -'.date('d-m-Y H-i-s').'.csv';
        // output headers so that the file is downloaded rather than displayed
        while (@ob_end_clean());
        ob_start();
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $file_path = APPLICATION_PATH.'/../public/files/sales/export/'.$userStorage->id.'/'.uniqid();
        if (!file_exists($file_path))
            mkdir($file_path, 0777, true);

        $path = $file_path.'/'.$filename;
        $output = fopen($path, 'w+');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM

        $heads = array(
            'ID',
            'ROUND NO',
            'ROUND YEAR',
            'AIR NUMBE',
            'TAX ID',
            'DISTRIBUTOR ID',
            'DISTRIBUTOR NAME',
            'STORE ID',
            'STORE CODE',
            'STORE NAME',
            'START DATE',
            'END DATE',
            'SHOP TYPE',
            'TOTAL REWARD_PRICE ',
            'TAX PRICE',
            'CREDITNOTE PRICE CONFIRM',
            'ASM CONFIRM BY',
            'ASM CONFIRM DATE',
            'CONFIRM BY' ,
	        'CONFIRM DATE' ,
	        'STATUS CN' ,
	        'CREDITNOTE_SN',
	        'CREATE_DATE',
	        'REASON REMARK',
        );

        fputcsv($output, $heads);

        $QDistributor   = new Application_Model_Distributor();
        $distributor_cache = $QDistributor->get_cache2();

        $i = 2;

        foreach($sql as $item) {
            $row = array();
            $row[] = $item['id']; 
			$row[] = $item['round_no']; 
			$row[] = $item['round_year']; 
			$row[] = $item['air_number']; 
			$row[] = '\''.$item['tax_id'].'\'';
			$row[] = $item['d_id']; 
			$row[] = $item['title']; 
			$row[] = $item['store_id'];
			$row[] = $item['store_code'];  
			$row[] = $item['store_name']; 
			$row[] = $item['start_date']; 
			$row[] = $item['end_date']; 
			$row[] = $item['shop_type']; 
			$row[] = $item['total_reward_price']; 
			$row[] = $item['tax_price']; 
			$row[] = $item['creditnote_price_confirm']; 
			$row[] = $item['asm_confirm_by']; 
			$row[] = $item['asm_confirm_date']; 
			$row[] = $item['confirm_by']; 
			$row[] = $item['confirm_date']; 
			$row[] = $item['status_cn']; 
			$row[] = $item['creditnote_sn']; 
			$row[] = $item['create_date']; 
			$row[] = $item['reason_remark']; 



            fputcsv($output, $row);
            unset($item);
            unset($row);
        
        }
        fclose($output);

        ob_flush();
        ob_start();
        while (@ob_end_flush());

        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;

        $file = fopen($path, 'r');
        $content = fread($file, filesize($path));
        var_dump(filesize($path));
        var_dump($content);

        exit;
    }
}