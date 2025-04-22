<?php
class CronController extends My_Controller_Action{

	
	public function genSnRefAction(){

		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $QCS = new Application_Model_CronSo();
        $QCC = new Application_Model_ControlCron();
        $QM = new Application_Model_Market();

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        if(isset($userStorage->id) && $userStorage->id){
        	$user_id = $userStorage->id;
        }else{
        	$user_id = 'System';
        }

        $use_log = 0;

        if($use_log){
	        $log = new Logging();
			$log->lfile('file_log_cron_sn_ref.txt');
		}

		$timestamp = time();

        //id:1 = cron gen sn_ref
        $getCron = $QCC->getControlCronGenSnRef(1);

     	$control_cron_id = 0;

     	if(isset($getCron['running']) && $getCron['running'] == 0){
     		$control_cron_id = $getCron['id'];
     	}

     	if($control_cron_id == 0){

     		if($use_log){
				$log->lwrite('[CODE:'.$timestamp.'][ID:'.$user_id.']'.' out cron because cron processing.');
			}

     		exit();
     	}

     	if($use_log){
	     	$log->lwrite('[START][CODE:'.$timestamp.'][ID:'.$user_id.']'.' ---------------------------------');
	     	$log->lwrite('[STAP:1][CODE:'.$timestamp.'][ID:'.$user_id.']'.' usering cron.');
	     }

     	$data = array(
			'running' => 1,
			'last_update' => date('Y-m-d H:i:s')
		);

		$cron_where = $QCC->getAdapter()->quoteInto('id = ?', $control_cron_id);
		$QCC->update($data,$cron_where);

		if($use_log){
			$log->lwrite('[STAP:2][CODE:'.$timestamp.'][ID:'.$user_id.']'.' update cron status to processing.');
		}

		$db = Zend_Registry::get('db');
        $db->beginTransaction();

		try{

			$getCronSo = $QCS->getCronSo();

			$array_temp_so = array();

			foreach ($getCronSo as $key => $value) {

				if($value['sn_ref'] != ''){

					$data = array(
						'status' => 0,
						'update_date' => date('Y-m-d H:i:s')
					);

					$cron_where = $QCS->getAdapter()->quoteInto('id = ?', $value['id']);
					$QCS->update($data,$cron_where);

					if($use_log){
						$log->lwrite('[CODE:'.$timestamp.'][ID:'.$user_id.']'.' !This have sn_ref,update end process [ID:'.$value['id'].'|SO:'.$value['sn_ref'].']');
					}
					
					continue;
				}

	            $sn_ref = $this->getSalesOrderNo_Ref($value['sn']);

	            if($use_log){
	            	$log->lwrite('[STAP:3][CODE:'.$timestamp.'][ID:'.$user_id.']'.' Gen SO [ID:'.$value['id'].'|SO:'.$sn_ref.']');
	            }

	            if(in_array($sn_ref, $array_temp_so)){

	            	if($use_log){
	            		$log->lwrite('[CODE:'.$timestamp.'][ID:'.$user_id.']'.' !Duplicate so reject [ID:'.$value['id'].'|SO:'.$sn_ref.']');
	            	}

	            	continue;
	            }

	            array_push($array_temp_so, $sn_ref);

				$data = array(
					'sn_ref' => $sn_ref
				);

				$mar_where = $QM->getAdapter()->quoteInto('sn = ?', $value['sn']);
				$QM->update($data,$mar_where);

				if($use_log){
	            	$log->lwrite('[STAP:4][CODE:'.$timestamp.'][ID:'.$user_id.']'.' Update so to market [ID:'.$value['id'].'|SO:'.$sn_ref.']');
	            }

				$data = array(
					'sn_ref' => $sn_ref,
					'status' => 0,
					'update_date' => date('Y-m-d H:i:s')
				);

				$cron_where = $QCS->getAdapter()->quoteInto('id = ?', $value['id']);
				$QCS->update($data,$cron_where);

				if($use_log){
					$log->lwrite('[STAP:5][CODE:'.$timestamp.'][ID:'.$user_id.']'.' Update so to cron [ID:'.$value['id'].'|SO:'.$sn_ref.']');
				}
			}

            $db->commit();

        }catch (Exception $e){
        	$db->rollback();
        	$data = array(
				'running' => 0,
				'error_msg' => $e,
				'error_date' => date('Y-m-d H:i:s'),
				'last_update' => date('Y-m-d H:i:s')
			);

			$cron_where = $QCC->getAdapter()->quoteInto('id = ?', $control_cron_id);
			$QCC->update($data,$cron_where);

			if($use_log){
				$log->lwrite('[CODE:'.$timestamp.'][ID:'.$user_id.']'.' !Rollback. | '.$e->getCode()."/".$e->getMessage());
				$log->lwrite('[END][CODE:'.$timestamp.'][ID:'.$user_id.']'.' ---------------------------------');
				$log->lclose();
			}

			exit();
		}

		$data = array(
				'running' => 0,
				'last_update' => date('Y-m-d H:i:s')
			);

		$cron_where = $QCC->getAdapter()->quoteInto('id = ?', $control_cron_id);
		$QCC->update($data,$cron_where);

		if($use_log){
			$log->lwrite('[STAP:6][CODE:'.$timestamp.'][ID:'.$user_id.']'.' Cron End Process.');
			$log->lwrite('[END][CODE:'.$timestamp.'][ID:'.$user_id.']'.' ---------------------------------');
			$log->lclose();
		}

		// get recheck loss sn_ref
		$getRecheckLossSO = $QCS->getRecheckLossSO();
		if($getRecheckLossSO){
			// recheck loss sn_ref
			$data = array(
				'status' => 1,
				'update_date' => date('Y-m-d H:i:s')
			);

			$cron_where = array();
			$cron_where[] = $QCS->getAdapter()->quoteInto('status = ?', 0);
			$cron_where[] = $QCS->getAdapter()->quoteInto('sn_ref = ?', '');
			$status_repair_so = $QCS->update($data,$cron_where);

			if($status_repair_so){
				$log->lwrite('[CODE:'.$timestamp.'][ID:'.$user_id.']'.' !Repair so.');
			}
		}

	}

	public function getSalesOrderNo_Ref($sn)
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $sn_ref="";
        try {
            $db = Zend_Registry::get('db');
            $stmt = $db->prepare("CALL gen_running_no_ref('SO',".$sn.")");
            
            $stmt->execute();
            $result = $stmt->fetch();
            $sn_ref = $result['running_no'];

        }catch (exception $e) {
            $flashMessenger->setNamespace('error')->addMessage('Cannot Get Sales Order No, please try again!');
        }
        return $sn_ref;
    }
	
}

/** 
 * Logging class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class Logging {
    // declare log file and file pointer as private properties
    private $log_file, $fp;
    // set log file (path and name)
    public function lfile($path) {
        $this->log_file = $path;
    }
    // write message to the log file
    public function lwrite($message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'c:/php/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}
