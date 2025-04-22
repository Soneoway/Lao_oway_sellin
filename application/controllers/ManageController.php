<?php

class ManageController extends My_Controller_Action
{

    public function autoGiftboxAction()
    {
        require_once 'manage' . DIRECTORY_SEPARATOR . 'auto-giftbox.php';
    }

    public function addAutoGiftboxAction()
    {
        require_once 'manage' . DIRECTORY_SEPARATOR . 'add-auto-giftbox.php';
    }

    // public function menuAction(){
    //     $this->_helper->viewRenderer->setRender('menu/menu');
    // }
    // Menu 
    public function menuAction()
    {
        require_once 'manage'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'list.php';
    }

    public function menuCreateAction()
    {
        require_once 'manage'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'create.php';
    }

    public function menuSaveAction()
    {
        require_once 'manage'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'save.php';
    }

    public function menuDeleteAction()
    {
        require_once 'manage'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'delete.php';
    }
    public function cacheAction(){
        $del = $this->getRequest()->getParam('del');

        if ($del) {

            $cache_folder = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;

            try {
                foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($cache_folder, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                    if ($path->getFilename() == 'zend_cache---server_notifis_cache'
                        || $path->getFilename() == 'zend_cache---internal-metadatas---server_notifis_cache')
                        continue;

                    $path->isFile() ? @unlink($path->getPathname()) : @rmdir($path->getPathname());
                }
            } catch (Exception $e){}

        }

        $back_url = $this->getRequest()->getServer('HTTP_REFERER');

        $this->_redirect( ( $back_url ? $back_url : HOST ) );
    }

    public function exceptionCaseAction()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $total = 0;

        $params = array();

        $QModel = new Application_Model_ExceptionCase();
        $exceptions = $QModel->fetchPagination($page, $limit, $total, $params);

        $this->view->exceptions = $exceptions;

        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST.'manage/exception-case/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $messages;

        $this->_helper->viewRenderer->setRender('exception-case/index');
    }

    public function exceptionCaseCreateAction(){

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $QModel = new Application_Model_ExceptionCase();
            $exceptionRowset = $QModel->find($id);
            $exception_case = $exceptionRowset->current();

            $this->view->exception_case = $exception_case;
        }

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        //back url
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');

        $this->_helper->viewRenderer->setRender('exception-case/create');
    }

    public function exceptionCaseSaveAction(){

        if ($this->getRequest()->getMethod() == 'POST'){
            $QModel = new Application_Model_ExceptionCase();

            $id = $this->getRequest()->getParam('id');
            $name = $this->getRequest()->getParam('name');
            $value = $this->getRequest()->getParam('value');
            $status = $this->getRequest()->getParam('status');

            $data = array(
                'name' => $name,
                'status' => $status,
                'value' => json_encode($value),
            );

            if ($id){
                $where = $QModel->getAdapter()->quoteInto('id = ?', $id);

                $QModel->update($data, $where);
            } else {
                $QModel->insert($data);
            }

            //remove cache
            $cache = Zend_Registry::get('cache');
            $cache->remove('exception_case_cache');

            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('success')->addMessage('Done!');
        }

        $back_url = $this->getRequest()->getParam('back_url');

        $this->_redirect( ( $back_url ? $back_url : HOST.'manage/exception-case' ) );
    }

    public function exceptionCaseDelAction(){
        $id = $this->getRequest()->getParam('id');

        $QModel = new Application_Model_ExceptionCase();
        $where = $QModel->getAdapter()->quoteInto('id = ?', $id);
        $QModel->delete($where);

        //remove cache
        $cache = Zend_Registry::get('cache');
        $cache->remove('exception_case_cache');

        $this->_redirect('/manage/exception-case');
    }

    public function delautogiftAction(){
        $id = $this->getRequest()->getParam('id');
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QAG = new Application_Model_AutoGiftbox();
        $where = $QAG->getAdapter()->quoteInto('id =?',$id);

            $data = array(
                'status' => 0,
                'cancel_by' => $userStorage->id,
                'cancel_date' => date('Y-m-d H:i:s')
            );
        $QAG->update($data,$where);
        $this->_redirect('/manage/auto-giftbox');
    }
}