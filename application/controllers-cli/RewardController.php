<?php

class RewardController extends My_Application_Controller_Cli
{
	// Excecute OPPO Club Result 
	public function getoppoclubresultAction() 
	{
		set_time_limit(0);
        ini_set('memory_limit', '-1');
        error_reporting(~E_ALL);
        ini_set("display_error", '0');

        $quater = $this->getRequest()->getParam('quater');
        $year = $this->getRequest()->getParam('year');

		$db = Zend_Registry::get('db');
        $db->beginTransaction();

        echo "Getting Data Oppo Club Reward From Catty ..."."\r\n";

        $QOppoClubRewardCn     = new Application_Model_OppoClubRewardCn();
       
        try {       
			echo "Start Transaction..."."\r\n";
			// insert data
			$result = $QOppoClubRewardCn->generat_RewardCnFromCatty_Cli($db,$year,$quater);

			$db->commit();
            echo "End Transaction..."."\r\n";
            echo "Result : Load Data ".$result['message']."\r\n";

        } catch (Exception $e) {
        	$db->rollBack();
            echo "Fail! : ".$e;
        }
        exit;
    }

}