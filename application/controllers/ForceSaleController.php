<?php
class ForceSaleController extends My_Controller_Action
{
    public function indexAction()
    {
        require_once 'forcesale' . DIRECTORY_SEPARATOR . 'index.php';
    }
    public function createAction()
    {
        require_once 'forcesale' . DIRECTORY_SEPARATOR . 'create.php';
    }
    public function insertAction()
    {
        require_once 'forcesale' . DIRECTORY_SEPARATOR . 'insert.php';
    }

    public function deteleAction()
    {
        require_once 'forcesale' . DIRECTORY_SEPARATOR . 'delete.php';
    }

}
