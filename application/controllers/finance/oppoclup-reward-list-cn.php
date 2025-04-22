<?php
$sort           = $this->getRequest()->getParam('sort', 'confirm_date');
$desc           = $this->getRequest()->getParam('desc', 1);
$page           = $this->getRequest()->getParam('page', 1);

$creditnote_sn   = $this->getRequest()->getParam('creditnote_sn');
$d_id            = $this->getRequest()->getParam('d_id');
$quater_no       = $this->getRequest()->getParam('quater_no');
$quater_year     = $this->getRequest()->getParam('quater_year');
$status_cn       = $this->getRequest()->getParam('status_cn');
$decorate_status       = $this->getRequest()->getParam('decorate_status');
$level_name       = $this->getRequest()->getParam('level_name');

$gen_reward      = $this->getRequest()->getParam('gen_reward',0);

$export  = $this->getRequest()->getParam('export', 0);

$this->_helper->viewRenderer->setRender('oppoclup-reward-list-cn');

$limit = LIMITATION;
//$limit = 2;
$total = 0;

$params = array(
    'creditnote_sn'   => $creditnote_sn,
    'd_id'            => $d_id,
    'quater_no'       => $quater_no,
    'quater_year'     => $quater_year,
    'status_cn'       => $status_cn,
    'decorate_status' => $decorate_status,
    'level_name' => $level_name,
);

$params['isbacks'] = true;
$params['group_sn'] = true;

$QDistributor    = new Application_Model_Distributor();
$QOppoClubRewardCn     = new Application_Model_OppoClubRewardCn();
$QCreditNote     = new Application_Model_CreditNote();

$distributors    = $QDistributor->get_cache();

$params['export'] = $export;

$params['sort'] = $sort;
$params['desc'] = $desc;

$markets = array();

if ( isset($export) && $export == 1 ) {
    // wait Confirm
    $reward_list_cn = $QOppoClubRewardCn->getOppoclup_Reward_List_CN_page($page, $limit, $total, $params);
    $this->_exportExcelRewardCreditNote_Wait_Confirm($reward_list_cn);
}else if ( isset($export) && $export == 2 ) {
    // Confirmed
    $reward_list_cn = $QOppoClubRewardCn->getOppoclup_Reward_List_CN_page($page, $limit, $total, $params);
    $this->_exportExcelRewardCreditNote_Confirm($reward_list_cn);
}else if($gen_reward==1){   
    // get data from catty
    if($quater_year !='' and $quater_no !='')
    {
        $QOppoClubRewardCn->generat_RewardCnFromCatty($quater_year,$quater_no);
        $reward_list_cn = $QOppoClubRewardCn->getOppoclup_Reward_List_CN_page($page, $limit, $total, $params);
    }
}else {  //show list
    $reward_list_cn = $QOppoClubRewardCn->getOppoclup_Reward_List_CN_page($page, $limit, $total, $params);
}

//print_r($reward_list_cn);

$this->view->reward_list_cn  = $reward_list_cn;
$this->view->distributors    = $distributors;

$this->view->desc   = $desc;
$this->view->sort   = $sort;
$this->view->params = $params;
$this->view->limit  = $limit;
$this->view->total  = $total;
$this->view->url    = HOST.'finance/oppoclup-reward-list-cn/'.( $params ? '?'.http_build_query($params).'&' : '?' );

$this->view->offset = $limit*($page-1);

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$this->view->messages = $messages;

$messages_success = $flashMessenger->setNamespace('success')->getMessages();
$this->view->messages_success = $messages_success;

if($this->getRequest()->isXmlHttpRequest()) {
    $this->_helper->layout->disableLayout();

    $this->_helper->viewRenderer->setRender('partials/list-oppoclup-reward-list-cn');
}