<?php
class FileLogController extends My_Controller_Action{

	
	public function oppoAllGreenAction(){

		$sort           = $this->getRequest()->getParam('sort', 'uploaded_at');
		$desc           = $this->getRequest()->getParam('desc', 1);
		$page           = $this->getRequest()->getParam('page', 1);
		$real_file_name = $this->getRequest()->getParam('real_file_name');
		$from           = $this->getRequest()->getParam('from');
		$to             = $this->getRequest()->getParam('to');
		$limit = LIMITATION;

		$params = array(
		    'desc'           => $desc,
		    'sort'           => $sort,
		    'real_file_name' => $real_file_name,
		    'from'           => $from,
		    'to'             => $to,
		    );

		$QLog = new Application_Model_FileUploadLog();
		$type ='OPPO Green All';
		$this->view->logs = $QLog->fetchPaginationType($page, $limit, $total, $params, $type);

		$userStorage = Zend_Auth::getInstance()->getStorage()->read();
		$this->view->uid = $userStorage->id;

		$flashMessenger = $this->_helper->flashMessenger;

		$messages = $flashMessenger->setNamespace('error')->getMessages();

		$this->view->desc     = $desc;
		$this->view->sort     = $sort;
		$this->view->messages = $messages;
		$this->view->params   = $params;
		$this->view->limit    = $limit;
		$this->view->total    = $total;
		$this->view->url      = HOST.'file-log/oppo-all-green/'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->offset   = $limit*($page-1);
	}
	
}
