<?php

class DownloadController extends My_Controller_Action
{
    public function mouAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->getParam('id');
        if (!$id) $this->_redirect(HOST.'sales/mou-log');

        $type = $this->getRequest()->getParam('type');
        if (!$type) $this->_redirect(HOST.'sales/mou-log');

        $folder = $this->getRequest()->getParam('folder');
        if (!$folder) $this->_redirect(HOST.'sales/mou-log');

        $QLog = new Application_Model_FileUploadLog();
        $log = $QLog->find($id);
        $log = $log->current();

        if (!$log) $this->_redirect(HOST.'sales/mou-log');
        if ($log['folder'] != $folder) $this->_redirect(HOST.'sales/mou-log');

        $uploaded_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'files'
            . DIRECTORY_SEPARATOR . 'mou'
            . DIRECTORY_SEPARATOR . $log['staff_id']
            . DIRECTORY_SEPARATOR . $log['folder'];

        if ($type == 'success')
            $file = $uploaded_dir . DIRECTORY_SEPARATOR . $log['success_file_name'];
        elseif ($type == 'error')
            $file = $uploaded_dir . DIRECTORY_SEPARATOR . $log['error_file_name'];
        elseif ($type == 'origin')
            $file = $uploaded_dir . DIRECTORY_SEPARATOR . $log['filename'];
        else
            $this->_redirect(HOST.'sales/mou-log');

        if (file_exists($file)) {
            header('Content-Description: File Transfer');

            $info = new SplFileInfo($file);
            $ext = $info->getExtension();

            if ($ext == 'xls')
                header('Content-Type: application/vnd.ms-excel');
            elseif ($ext == 'xlsx')
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            else
                header('Content-Type: application/octet-stream');

            header('Content-Disposition: attachment; filename='.$type.' - '.date('d-m-Y H:i:s', $log['uploaded_at']).' - '.$log['real_file_name']);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('error')->addMessage('File not found.');
            $this->_redirect(HOST.'sales/mou-log');
        }

        exit;
    }

    /**
     * Không yêu cầu đăng nhập để xem. Chỉ xem được tên file. Muốn tải file phải đăng nhập.
     * @return [type] [description]
     */
    public function indexAction()
    {
        $this->_helper->layout->setLayout('download');
        $hash = $this->getRequest()->getParam('id');

        try {
            if (!$hash) throw new Exception("Invalid file", 1);

            $QLog = new Application_Model_FileUploadLog();
            $where = $QLog->getAdapter()->quoteInto('hash LIKE ?', $hash);
            $log = $QLog->fetchRow($where);

            if (!$log) throw new Exception("Invalid file", 2);
            if (!isset($log['content']) || empty($log['content'])) throw new Exception("File not exists", 3);

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $this->view->filename = $log['real_file_name'];
            $this->view->id = $hash;
            $this->view->filesize = strlen($log['content']);

            if ($userStorage && isset($userStorage->id))
                $this->view->check = md5($userStorage->id.$userStorage->id.$hash);
            else
                $this->view->check = 0;

            $this->view->userStorage = $userStorage;
        } catch (Exception $e) {
            $this->view->error = sprintf("Error: [%s] %s", $e->getCode(), $e->getMessage());
        }
    }

    /**
     */
    public function imeiPoAction()
    {
        $hash = $this->getRequest()->getParam('id');
        $check = $this->getRequest()->getParam('check', 0);

        try {
            if (!$hash) throw new Exception("Invalid file", 1);

            $QLog = new Application_Model_FileUploadLog();
            $where = $QLog->getAdapter()->quoteInto('hash LIKE ?', $hash);
            $log = $QLog->fetchRow($where);

            if (!$log) throw new Exception("Invalid file", 2);
            if (!isset($log['content']) || empty($log['content'])) throw new Exception("File not exists", 3);

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            if (!$userStorage || !isset($userStorage->id))
                $this->_redirect(HOST.'download?id='.$hash);

            $check_check = md5($userStorage->id.$userStorage->id.$hash);

            if ($check != $check_check) throw new Exception("Invalid file", 2);

            $data = array('download_time' => isset($log['download_time']) && intval($log['download_time']) ? ($log['download_time']+1) : 1);
            $where = $QLog->getAdapter()->quoteInto('id = ?', $log['id']);
            $QLog->update($data, $where);

            ////////////////////////////////////////////
            $info = new SplFileInfo($log['real_file_name']);
            $ext = $info->getExtension();

            header('Content-Description: File Transfer');
            if ($ext == 'xls')
                header('Content-Type: application/vnd.ms-excel');
            elseif ($ext == 'xlsx')
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            else
                header('Content-Type: application/octet-stream');

            header('Content-Disposition: attachment; filename='.$log['real_file_name']);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($log['content']));
            ob_clean();
            ob_end_clean();
            flush();
            echo $log['content'];
            exit;
        } catch (Exception $e) {
            $this->_redirect(HOST.'download');
        }
    }
}
