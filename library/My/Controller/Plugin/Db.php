<?php
class My_Controller_Plugin_Db extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopShutdown()
    {
        $db = Zend_Registry::get('db');
        $db->closeConnection();
    }
}
