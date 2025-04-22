<?php

    $imei          = $this->getRequest()->getParam('imei');

    $QGood = new Application_Model_Good();
    $QGood_color = new Application_Model_GoodColor();
    $goods = $QGood->get_imei_good();
    $colors = $QGood_color->get_imei_color();

    $QImei = new Application_Model_Imei();
    $imeiupdates       = $QImei->getImeiUpdate($imei);

    $this->view->goods = $goods;
    $this->view->colors = $colors;
    $this->view->imeiupdates = $imeiupdates;
    $this->view->url    = HOST.'tool/imei-edit-create/';
