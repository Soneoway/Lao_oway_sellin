<?php
set_time_limit(0);
/*$sn = $this->getRequest()->getParam('sn'); */
$shape = $this->getRequest()->getParam('cat_id');
if ($this->getRequest()->getMethod() == 'POST')
{
    echo '<link href="/css/bootstrap.min.css" rel="stylesheet">';
    $imei_list = trim($this->getRequest()->getParam('imei', ''));
    $imei_list = explode("\n", $imei_list);

    if (count($imei_list) == 1 && $imei_list[0] == '') {
        exit('<div class="alert alert-warning">No input imei.</div>');
    }

    $count_all = count($imei_list);
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    /*$QMarket = new Application_Model_Market();
    $QPO = new Application_Model_Po();*/
    $QImei = new Application_Model_Imei();
    $imei_failed = array();
    $imei_reason = array();
    $imei_out_wh = NULL;
    foreach ($imei_list as $imei) {
        // tính số đã xuất trên từng sản phẩm

        $flag = FALSE;
        $check_imei_good = FALSE;
        $imei = trim($imei);
        $count = 0;
        // check định dạng imei
        if ( !preg_match('/^[0-9]{15}$/', $imei) ) {
            $imei_failed[] = $imei;
            $imei_reason[$imei] = 'Wrong format';
            continue;
        }
        /// check imei co trong kho hay không

        $where = array();
        $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $where[] = 'out_date IS NULL OR out_date = \'\' OR out_date = 0';
        $where[] = 'sales_sn IS NULL OR sales_sn = \'\' OR sales_sn = 0';
        $imei_out_wh = $QImei->fetchRow($where); // IMEI có trong hệ thống và chưa bị xuất kho

        if ( count($imei_out_wh) == 0 )
        {
            $imei_reason[$imei] = 'IMEI is not in warehouse or exported.';
            $flag = TRUE;
        }


        /// check imei co dung trang thai hay khong
        $where = array();
        $where[] = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $where[] = 'status = 1 OR status = 2'; //trong kho hoặc bị damage (trường hợp mất, hay đang trên đường chuyển kho không được update)
        $imei_out_wh = $QImei->fetchRow($where);

        if ( count($imei_out_wh) == 0 )
        {
            $imei_reason[$imei] = 'IMEI was lost or on the way changing sales.';
            $flag = TRUE;
        }

        else
        {
            // Kiểm tra hàng return
            /*if ($imei_out_wh['return_sn'] && $imei_out_wh['return_sn'] != '') { // đã bị return
                $QImeiReturn = new Application_Model_ImeiReturn();
                $where = array();
                $where[] = $QImeiReturn->getAdapter()->quoteInto('return_sn = ?', $imei_out_wh['return_sn']);
                $where[] = $QImeiReturn->getAdapter()->quoteInto('imei_sn = ?', $imei);
                $return = $QImeiReturn->fetchRow($where);

                if ( !$return || $return['back_sale'] == 1 ) { // return and can back to sales
                    $check_imei_good = TRUE;
                    $flag = FALSE;
                } else { // cannot back to sales
                    $imei_reason[$imei] = 'IMEI is returned and cannot back to sales.';

                    $flag = TRUE;
                }
            }
            else
            {*/ // chưa bị return
            $check_imei_good = TRUE;
            $flag = FALSE;

            /*}*/
        }

        if (!$check_imei_good) $imei_failed[] = $imei;

        if ($flag) continue;

        $data = array(
            'status'    => 2,//processing
            'shape'     => $shape,
        );

        /*if ($shape==1)
            $data['status'] = 1;*/

        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $n = $QImei->update($data, $where);
    }
    //luu log
    $imei_failed = array_unique($imei_failed, SORT_REGULAR);
    try {

        //todo log
        $ip = $this->getRequest()->getServer('REMOTE_ADDR');
        $info = 'Check shield IMEI - failed: ('.count($imei_failed).') IMEIs';
        $QLog = new Application_Model_Log();
        $QLog->insert( array (
            'info' => $info,
            'user_id' => $userStorage->id,
            'ip_address' => $ip,
            'time' => date('Y-m-d H:i:s'),
        ) );

    }catch (Exception $e){
        //eo catch
    }

    if ( count($imei_failed) == 0)
        echo '<div class="alert alert-success"> Success </div>';
    else
    {
        echo '<div class="alert alert-success">Success ('.($count_all - count($imei_failed)).')</div>';
        echo '<div class="alert alert-error">Failed ('.count($imei_failed).')</div>';
        echo '<p><strong>List of failed IMEIs</strong></p>';
        echo '<textarea rows="12">';
        foreach ($imei_failed as $k => $v) {
            echo $v.' - '.$imei_reason[$v]."\n";
        }
        echo '</textarea>';
    }

    $this->_helper->layout->disableLayout();

    exit;
}

exit;