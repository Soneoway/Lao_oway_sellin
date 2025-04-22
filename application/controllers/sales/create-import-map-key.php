<?php

$flashMessenger = $this->_helper->flashMessenger;
$messages = $flashMessenger->setNamespace('error')->getMessages();
$messages_success = $flashMessenger->setNamespace('success')->getMessages();

set_time_limit(0);
ini_set('memory_limit', '200M');

$text_key      = $this->getRequest()->getParam('text_key');
$cat_id        = $this->getRequest()->getParam('cat_id');
$good_id       = $this->getRequest()->getParam('good_id');
$good_color_id = $this->getRequest()->getParam('good_color_id');

$params = array(
    'text_key'      => $text_key,
    'cat_id'        => $cat_id,
    'good_id'       => $good_id,
    'good_color_id' => $good_color_id,
);

$QGood          = new Application_Model_Good();
$goods_cached   = $QGood->get_cache();
// print_r($goods_cached); die;
$QGoodColor     = new Application_Model_GoodColor();
$goodColors     = $QGoodColor->get_cache();
// print_r($goodColors); die;
$QGoodCategory  = new Application_Model_GoodCategory();
$goodCategories = $QGoodCategory->get_cache();
// print_r($goodCategories); die;

// $this->view->params 		= $params;
$this->view->goods_cached   = $goods_cached;
$this->view->goodColors     = $goodColors;
$this->view->goodCategories = $goodCategories;

    $QCsvimport   = new Application_Model_CsvImport();

        if(!isset($params['text_key']) && $params['text_key'] = ""){
             array_push($messages,'Can not sync:: Please select warehouse');
              $this->view->messages  = $messages;
              return;
          }
          
          if(!isset($params['good_id']) && $params['good_id'] = ""){
             array_push($messages,'Can not sync:: Please select warehouse');
              $this->view->messages  = $messages;
              return;
          }
           
          if(!isset($params['good_color_id']) && $params['good_color_id'] = ""){
             array_push($messages,'Can not sync:: Please select warehouse');
              $this->view->messages  = $messages;
              return;
          }

          if(!isset($text_key)){
             return;
          }
          if(!isset($good_id)){
             return;
          }
           if(!isset($good_color_id)){
             return;
          }
        
        $userStorage    = Zend_Auth::getInstance()->getStorage()->read();

        $where = array();
        $where[] = $QCsvimport->getAdapter()->quoteInto('key_product =?',$text_key);
        $where[] = $QCsvimport->getAdapter()->quoteInto('good_id =?',$good_id);
        $where[] = $QCsvimport->getAdapter()->quoteInto('good_color =?',$good_color_id);
        $error_key = $QCsvimport->fetchRow($where);

        if($error_key > 0){
            array_push($messages,'ERROR!! Key Product ซ้ำ');
            $this->view->messages = $messages;
        }else{

            $QCsvimport->insert(array(
                'key_product' => $text_key,
                'cat_id'      => $cat_id,
                'good_id'     => $good_id,
                'good_color'  => $good_color_id,
                'create_date' => date('Y-m-d H:i:s'),
                'create_by'   => $userStorage->id,
                'status'      => 1

            ));
             array_push($messages_success,'Done');
             $this->view->messages_success = $messages_success;
        }

       
       
