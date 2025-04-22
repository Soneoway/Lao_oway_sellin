<?php
set_time_limit(0);
ini_set('memory_limit', '200M');

    $params        = $this->getRequest()->getParam('params');
    $sort          = $this->getRequest()->getParam('sort', '');
    $page          = $this->getRequest()->getParam('page', 1);
    $desc          = $this->getRequest()->getParam('desc', 1);

    $imei          = $this->getRequest()->getParam('imei');

    $limit = LIMITATION;
    $total = 0;

    $params = array_filter(array(
        'imei'          => $imei

    ));

    $imei = explode("\r\n", $imei);

    $QImei = new Application_Model_Imei();
    $editimei        = $QImei->getImeiEdit($page, $limit, $total, $params);


    $this->view->editimei = $editimei;
    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->url    = HOST.'tool/imei-edit/'.( $params ? '?'.http_build_query($params).'&' : '?' );
    $this->view->offset = $limit*($page-1);



