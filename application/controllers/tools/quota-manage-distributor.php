<?php
    
 
    $QQuota = new Application_Model_QuotaOppoByDistributor();
    $quota_s = $QQuota->fetchPagination();
    $this->view->quota = $quota_s;

  


 $flashMessenger               = $this->_helper->flashMessenger;
    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;

    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

