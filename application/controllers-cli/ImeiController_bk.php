<?php
class ImeiController extends My_Application_Controller_Cli
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        set_time_limit(0);
//        error_reporting(~E_ALL);
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        define('DB_HOST',$config['resources']['db']['params']['host']);
        define('DB_USER', $config['resources']['db']['params']['username']);
        define('DB_PASS', $config['resources']['db']['params']['password']);
        define('DB_NAME', $config['resources']['db']['params']['dbname']);
    }

    function syncActivateAction(){
        $this->syncActivatedTime();
        echo "Done sync activated time\n";
    }

    public function indexAction()
    {
        $num = $this->getRequest()->getParam('num', 3);
        echo $num."\n";
        $this->fetch_imei($num);
        echo "Done fetching\n";
        echo "Sleep for 1 seconds\n";
        sleep(1);
        //$this->syncActivatedTime();
        //echo "Done sync activated time\n";

        exit;
    }

    public function oneAction()
    {
        echo date('Y-m-d H:i:s'). ": Start fetching\n";
        $num = $this->getRequest()->getParam('num', 1);
        echo $num."\n";
        $this->fetch_imei_one($num);
        echo "Done fetching\n";
        //$this->syncActivatedTime();
        //echo date('Y-m-d H:i:s'). ": Done sync activated time\n";

        exit;
    }

    /*
     * Crawl IMEI activation information from URL (provided by TEST Team)
     * After crawl, insert to a table
     * @param int $timestamp [optional]
     * @return string|bool a formatted date string. If a non-numeric value is used for
     * */
    function crawlAction(){

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $config = $config->toArray();

        $cf = $config['resources']['db']['params'];
        $cf2 = $config['resources']['db2']['params'];

        $con=mysqli_connect($cf['host'],$cf['username'],$cf['password'],$cf['dbname']);

        // Check connection
        if (mysqli_connect_errno())
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        $startTime = '2015-05-01 00:00:00';

        $sql = '
            SELECT ts.`imei`
            FROM `'.$cf2['dbname'].'`.`timing_sale` ts
                JOIN `'.$cf2['dbname'].'`.`timing` t ON ts.timing_id = t.id
                LEFT JOIN `'.$cf['dbname'].'`.`imei_activation` ia on ts.`imei` = ia.`imei_sn`
            WHERE
                ia.`imei_sn` IS NULL
                AND t.`approved_at` IS NOT NULL
                AND ts.imei NOT IN(
                    SELECT
                        imei_sn
                    FROM
                        `'.$cf['dbname'].'`.`imei_activation_temp`
                )
                AND t.`from` >= "'.$startTime.'"
        ';

        $result = mysqli_query($con,$sql);

        // get list imei
        $countFail = $countSuccess = 0;
        while($row = mysqli_fetch_array($result)){
            $data = $this->fetchIMEIFromURL($row['imei']);

            if (isset($data['code']) and $data['code']==0
                and isset($data['data']) and $data['data']
            ){
                $firstActivation = end($data['data']);

                $firstActivationConverted = date('Y-m-d H:i:s', strtotime($firstActivation));

                $sql = "    INSERT IGNORE INTO
                            `".$cf['dbname']."`.`imei_activation_temp`(
                                `imei_sn`,
                                `activated_at`
                            )
                            VALUES(
                                '".$row['imei']."'
                                ,'".$firstActivationConverted."'
                                )
                            ";

                mysqli_query($con,$sql);
                $countSuccess++;
                echo 'Success:'.$row['imei'].':'.$firstActivation.'-'.$firstActivationConverted."\n";
                usleep(10);


            } elseif (
                isset($data['code']) and $data['code']==0
                and !isset($data['data']) )
            {
                $countFail++;
                echo 'NoData:'.$row['imei']."\n";
            }

            else{
                $countFail++;
                echo 'Fail:'.$row['imei']."\n";
            }

        }

        echo 'Done-----Success:'.$countSuccess.'-------Fail:'.$countFail;
    }

    /*
     * Crawl IMEI activation information from URL (provided by TEST Team)
     * After crawl, insert to a table
     * @param string $IMEI
     * @return string a formatted date string
     * */
    private function fetchIMEIFromURL($IMEI) {
        date_default_timezone_set('Asia/Shanghai');

        //--------Setting-------------
        $server = 'http://warranty.oppo.com/searchby.aspx';
        $__VIEWSTATE = '%2FwEPDwULLTE4ODI3MjgyNjUPZBYCAgMPZBYEAgcPDxYCHgRUZXh0BRLmn6Xor6LkuI3liLDmlbDmja5kZAIJDzwrAA0BAA8WBB4LXyFEYXRhQm91bmRnHgtfIUl0ZW1Db3VudGZkZBgBBQlHcmlkVmlldzEPPCsACgEIZmTO0AhdEWRCfYNvQCan%2BxFUAb5mqw%3D%3D';
        $__EVENTVALIDATION = '%2FwEWBAKv0PykCgLs0bLrBgKy4p29AwLvjry%2FBR57q3K%2FUlP%2BpFls7H0%2FzxLsewod';
        $btnQuery = 'dcm';
        $tbImei = $IMEI;
        $TextBox1 = '';
        $post_string = '__VIEWSTATE='.$__VIEWSTATE.'&__EVENTVALIDATION='.$__EVENTVALIDATION.'&TextBox1='.$TextBox1.'&tbImei='.$tbImei.'&btnQuery='.$btnQuery;

        //==============DOWNLOADER===============
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_HEADER,  0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        // grab URL and pass it to the browser
        $response = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);

        if(($response == FALSE) OR (strstr($response, 'Runtime Error') <> FALSE) ){
            return array(
                'code'=>1,
                'message'=>'Cannot fetch data',
            );

        }

        $pattern = '/(\d{1,2})\/(\d{1,2})\/(\d{4}) (\d{1,2}):(\d{1,2}):(\d{1,2}) ((A|P)M)/';
        preg_match_all($pattern, $response, $m);

        if (isset($m[0]) and is_array($m[0]) and $m[0])
            return array(
                'code' => 0,
                'data' => $m[0]
            );

        return array(
            'code'=>0,
        );

    }


    private function fetch_imei($num = 3) {
        date_default_timezone_set('Asia/Shanghai');

        $datetime = date('Ymd', time()-$num*24*60*60);
        // $datetime = '20140115'; // assign start time

        for($n = 0; $n <= $num; $n++){ // Number of Days Fetchs

            //--------Setting-------------
            $server = 'http://dzbkjh2.myoppo.com/GetImeiByCountryCode.ashx';
            $ConfusionString = '44@ade$68%e*ad';
            $countryCode = 84;
            $datetime = date('Ymd', strtotime('+1 day', strtotime($datetime)));
            $authkey = MD5($datetime . MD5($ConfusionString));

            $post_string = 'countryCode='. $countryCode . '&datetime=' . $datetime . '&authkey=' . $authkey;

            echo "REQUEST: ".$post_string."\n";

            $i = 1; // Turn Index Counter
            runagain_from_here: // Run again from here in case cannot download data

            $time_start = microtime(true); // start time counter

            //==============DOWNLOADER===============
            // create a new cURL resource
            $ch = curl_init();

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, $server);
            curl_setopt($ch, CURLOPT_HEADER,  0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
            curl_setopt($ch, CURLOPT_POST, true);
            //curl_setopt($ch, CURLOPT_USERPWD, 'rocket:rock4me');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
            curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            // grab URL and pass it to the browser
            $response = curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);

            if(($response == FALSE) OR (strstr($response, 'Runtime Error') <> FALSE) ){ // if return FALSE echo error and LOOP to try fetch data again
                $time_end = microtime(true);
                $time = $time_end - $time_start;

                echo 'Turn: '. $i .' , Exe Time: '. $time . ' seconds, NO DATA RE-RUN AGAIN...'."\r\n";
                $i++;
                goto runagain_from_here;
            }

            echo "OK I GOT IT \n";

            //file_put_contents('jsonimei.txt',$response);
            //echo json_decode($response);

            //=======Parse JSON========
            $json_response = $response;

            $arr_response = json_decode($json_response,1);

            $json_imei = $arr_response['Message'];

            $arr_imei = json_decode($json_imei,1);
            $total = count($arr_imei);

            echo "Parse DONE \n";
            //file_put_contents('imei.txt', $arr_imei);
            //print_r($arr_imei);

            //======Import to DB=====
            echo "Start build query\n";

            $sql_insert = " INSERT INTO imei_activation(`imei_sn`,`activated_at`, `phone`) VALUES ";
            $sql_on = " ON DUPLICATE KEY UPDATE `activated_at` = VALUES(activated_at), `phone`=VALUES(phone); ";
            $sql_values = " ";

            for ($i=0; $i < $total; $i++) {
                if (!isset($arr_imei[$i]))
                    continue;

                $tmp = $arr_imei[$i];
                $imei = $tmp['Imei'];
                $registertime = $tmp['Registertime'];
                if (isset($tmp['Telephone']) || !empty($tmp['Telephone']))
                    $phone = trim($tmp['Telephone']);
                else
                    $phone = '';

                if (($i > 0 && $i % 500 == 0) || ($i == $total - 1)) {
                    $sql_values = trim($sql_values, ',');

                    $qry_insert_imei = $sql_insert . $sql_values . $sql_on;

                    $sql_values = trim($sql_values);
                    if (!empty($sql_values)) {
                        echo "Start executing query: ".time()."\n";
                        $this->sqlquery(DB_HOST,DB_USER,DB_PASS,DB_NAME,$qry_insert_imei);
                        echo "Execute query done: ".time()."\n";
                    }

                    $sql_values = " ";
                } else {
                    $sql_values .= "($imei, '$registertime', '$phone'),";
                }

                unset($imei);
                unset($registertime);
                unset($phone);
                unset($tmp);
                unset($qry_insert_imei);
            }

            echo "Import done\n";

            echo "Import DONE for $datetime \n";

        } // END BIG FOR
    }

    private function fetch_imei_one($num = 1) {
        date_default_timezone_set('Asia/Shanghai');

        $datetime = date('Ymd', time()-$num*24*60*60);
        // $datetime = '20140115'; // assign start time

        // for($n = 1; $n <= $num; $n++){ // Number of Days Fetchs

        //--------Setting-------------
        $server = 'http://dzbkjh2.myoppo.com/GetImeiByCountryCode.ashx';
        $ConfusionString = '44@ade$68%e*ad';
        $countryCode = 84;
        $datetime = date('Ymd', strtotime('+1 day', strtotime($datetime)));
        $authkey = MD5($datetime . MD5($ConfusionString));

        $post_string = 'countryCode='. $countryCode . '&datetime=' . $datetime . '&authkey=' . $authkey;

        echo "REQUEST: ".$post_string."\n";

        $i = 1; // Turn Index Counter
        runagain_from_here: // Run again from here in case cannot download data

        $time_start = microtime(true); // start time counter

        //==============DOWNLOADER===============
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_HEADER,  0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_USERPWD, 'rocket:rock4me');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        // grab URL and pass it to the browser
        $response = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);

        if(($response == FALSE) OR (strstr($response, 'Runtime Error') <> FALSE) ){ // if return FALSE echo error and LOOP to try fetch data again
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            echo 'Turn: '. $i .' , Exe Time: '. $time . ' seconds, NO DATA RE-RUN AGAIN...'."\r\n";
            $i++;
            goto runagain_from_here;
        }

        echo "OK I GOT IT \n";

        //file_put_contents('jsonimei.txt',$response);
        //echo json_decode($response);

        //=======Parse JSON========
        $json_response = $response;

        $arr_response = json_decode($json_response,1);

        $json_imei = $arr_response['Message'];

        $arr_imei = json_decode($json_imei,1);

        echo "Parse DONE \n";
        //file_put_contents('imei.txt', $arr_imei);
        //print_r($arr_imei);

        //======Import to DB=====
        $e = 0;
        WHILE(isset($arr_imei[$e]['Imei']) and $arr_imei[$e]['Imei']){
            $imei = $arr_imei[$e]['Imei'];
            $registertime = $arr_imei[$e]['Registertime'];

            $qry_insert_imei = "INSERT INTO imei_activation(`imei_sn`,`activated_at`) VALUES($imei,'$registertime') ON DUPLICATE KEY UPDATE `activated_at` = '$registertime';";
            $this->sqlquery(DB_HOST,DB_USER,DB_PASS,DB_NAME,$qry_insert_imei);

            $e++;
        }
        echo "Import DONE for $datetime \n";

        // } // END BIG FOR
    }

    private function syncActivatedTime()
    {
        date_default_timezone_set('Asia/Shanghai');
        $sql = "UPDATE imei, imei_activation
				SET imei.activated_date = imei_activation.activated_at
				WHERE imei_activation.imei_sn=imei.imei_sn";
        $this->sqlquery(DB_HOST,DB_USER,DB_PASS,DB_NAME,$sql);
    }

    private function sqlfetcharray($sqlquery){
        date_default_timezone_set('Asia/Shanghai');
        $response = $this->sqlquery(DB_HOST,DB_USER,DB_PASS,DB_NAME,$sqlquery);
        $rows = array();

        while(($row = mysql_fetch_array($response))) {
            $rows[] = $row;
        }

        return $rows;
    }

    private function sqlquery($host,$user,$pass,$database,$sqlquery){
        date_default_timezone_set('Asia/Shanghai');
        $con = mysqli_connect($host,$user,$pass,$database);

        // Check connection
        if (mysqli_connect_errno())
        {
            die( "Failed to connect to MySQL: " . mysqli_connect_error() );
        }

        mysqli_set_charset($con, "utf8");
        $response = mysqli_query($con, $sqlquery);
        mysqli_close($con);
        return $response;

        /*
        $con->select_db($database);

        $connection = mysql_connect($host,$user,$pass) or die("Mysql Error ".mysql_error());
        mysql_select_db($database)or die("Mysql Error ".mysql_error());
        mysql_set_charset('utf8',$connection);
        $response = mysql_query($sqlquery) or die("Mysql Error ".mysql_error());
        mysql_close($connection);

        return $response;
        */
    }

    public function requestAction()
    {
        $data = array(
            'countryCode' => 84,
            'datetime' => 20150116,
            'authkey' => '39ef9ee4fdbb176b89ce2ab5835a5b9b'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_URL, "http://dzbkjh2.myoppo.com/GetImeiByCountryCode.ashx");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($ch);

        $json_response = $res;

        $arr_response = json_decode($json_response,1);

        $json_imei = $arr_response['Message'];

        $arr_imei = json_decode($json_imei,1);

        echo count($arr_imei);
        exit;
    }

    // php cli.php imei read num=10
    public function readAction()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('display_errors', ~E_ALL);
        error_reporting(false);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        echo "\n-------------------------------------
        \nStart fetch IMEI from Singapore...\n";

        $start_time = microtime(1);

        try {
            $num = $this->getRequest()->getParam('num', 3);
            if ($num <= 0) throw new Exception("Required: Num > 0", 1);

            echo "Fetch $num day".($num > 1 ? "s":"").", ";
            $to = date('Y-m-d', time());
            if ($num > 1) {
                $from = date('Y-m-d', time()-($num-1)*3600*24);

                echo $from . " to " .$to.".\n";
            } else {
                echo $to."\n";
            }

            for ($i=0; $i < $num; $i++) {
                $d = time()-$i*3600*24;
                $date = date('Ymd', $d);
                echo "- Fetch: " . date('Y-m-d', $d)."\n";
                // echo ""
                $this->read_one_day($date);
                echo "- Done for: " . date('Y-m-d', $d)."\n";
            }

            echo "- Sync active time..."."\n";
            $this->syncActivatedTime();

            $end_time = microtime(1);
            $exec_time = $end_time - $start_time;
            echo "End fetch IMEI from Singapore.
            \nExec time: $exec_time (seconds).
            \nNo Error.\n-------------------------------------\n";

            exit;
        } catch (Exception $e) {
            $end_time = microtime(1);
            $exec_time = $end_time - $start_time;
            echo "End fetch IMEI from Singapore.
            \nExec time: $exec_time (seconds).
            \nWith Error:\n";
            exit($e->getMessage()."\n-------------------------------------\n");
        }

        exit;
    }

    private function read_one_day($date)
    {
        if (!$date || empty($date) || !date_create_from_format('Ymd', $date))
                throw new Exception("Invalid date", 1);

        $key = "eaee9169c87d742f3d7b457edf3f103006a884ba3adeaaaea6798278db549930";
        $hash_server = hash('sha512', substr($key, 3, 8).$date);
        $server = 'http://imei.buu.vn/ws-imei';
        $page = 1;
        // $limit = LIMITATION;
        $post_string = sprintf("date=%s&page=%s&hash=%s&key=%s", $date, $page, $hash_server, $key);
        // $a=file_get_contents( $server.'?'.$post_string);
        echo "Start CURL\n";
        //==============DOWNLOADER===============
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $server);
        curl_setopt($ch, CURLOPT_HEADER,  0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0');
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_USERPWD, 'rocket:rock4me');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        // grab URL and pass it to the browser
        $response = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);

        echo "Done CURL\n";

        $arr = json_decode($response);

        if (!isset($arr->imeis) || !is_array($arr->imeis))
            return;

        if (!isset($arr->total) || !is_numeric($arr->total))
            return;

        if (!isset($arr->limit) || !is_numeric($arr->limit))
            return;

        $total = $arr->total;
        $limit = $arr->limit;

        echo "Start build query\n";

        $sql_insert = " INSERT INTO imei_activation(`imei_sn`,`activated_at`, `phone`) VALUES ";
        $sql_on = " ON DUPLICATE KEY UPDATE `activated_at` = VALUES(activated_at), `phone`=VALUES(phone); ";
        $sql_values = " ";

        for ($i=0; $i < $total; $i++) {
            if (!isset($arr->imeis[$i]))
                continue;

            $tmp = $arr->imeis[$i];
            $imei = $tmp->imei_sn;
            $registertime = $tmp->activated_at;
            $phone = trim($tmp->phone);
            $phone = !empty($phone) ? $phone : '';
            $sql_values .= "($imei, '$registertime', '$phone'),";

            if (($i > 0 && $i % 500 == 0) || ($i == $total - 1) || $i == $limit - 1) {
                $sql_values = trim($sql_values, ',');

                $qry_insert_imei = $sql_insert . $sql_values . $sql_on;

                $sql_values = trim($sql_values);
                if (!empty($sql_values)) {
                    echo "Start executing query: ".time()."\n";
                    $this->sqlquery(DB_HOST,DB_USER,DB_PASS,DB_NAME,$qry_insert_imei);
                    echo "Execute query done: ".time()."\n";
                }

                $sql_values = " ";
            }

            unset($imei);
            unset($registertime);
            unset($phone);
            unset($tmp);
            unset($qry_insert_imei);
        }

        echo "Import done\n";
    }
}
