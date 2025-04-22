<?php
class BankController extends My_Controller_Action{
	public function init(){
		
	}
	
	public function indexAction(){
		
		$flashMessenger 	= $this->_helper->flashMessenger;
		$messages 			= $flashMessenger->setNamespace('error')->getMessages();
		$messages_success 	= $flashMessenger->setNamespace('success')->getMessages();
		$name  		= $this->getRequest()->getParam('name','');
		$id 		= $this->getRequest()->getParam('id',null);
		$page 		= $this->getRequest()->getParam('page',1);
		$limit 		= LIMITATION;
		$sort 		= $this->getRequest()->getParam('sort','b.id');
		$desc    	= $this->getRequest()->getParam('desc', 1);
		$total 		= 0;
		
		$params 	= array(
				'name'	=>	trim($name),
				'id'	=>	$id,
				'sort'	=>	$sort,
				'desc'	=>	$desc,
		
		);
		$QBank 		= new Application_Model_Bank();
		$list 		= $QBank->fetchPagination($page, $limit, $total, $params);
		
		$this->view->list 				= $list;
		$this->view->limit 				= $limit;
		$this->view->total 				= $total;
		$this->view->page 				= $page;
		$this->view->offset 			= $limit*($page-1);
		$this->view->params 			= $params;
		$this->view->sort 				= $sort;
		$this->view->desc 				= $desc;
		$this->view->url 				= HOST.'bank/index/'.( $params ? '?'.http_build_query($params).'&' : '?' );
		$this->view->messages_success 	= $messages_success;
		$this->view->messages 			= $messages;
	}
	
	public function addAction(){
		$flashMessenger 	= $this->_helper->flashMessenger;
		$messages 			= $flashMessenger->setNamespace('error')->getMessages();
		$messages_success 	= $flashMessenger->setNamespace('success')->getMessages();
		if($this->getRequest()->isPost()){
			$QBank  = new Application_Model_Bank();
			$name 		= trim($this->getRequest()->getParam('name'),'');
			$fullname 	= $this->getRequest()->getParam('fullname');
			$checkValue = true;
			if(	$name	==	''	){
				$checkValue = FALSE;
				$flashMessenger->setNamespace('error')->addMessage('Short name not empty!');
			}
			$where = $QBank->getAdapter()->quoteInto('name = ?', $name);
			$check_exist = $QBank->fetchRow($where);
			
			if($check_exist){
				$checkValue = false;	
				$flashMessenger->setNamespace('error')->addMessage('This name existed;');
			}
			
			if(	$checkValue	== TRUE	){
				$data 	= array('name'=>$name,'fullname'=>$fullname);
				try {
					$QBank->insert(	$data	);
					$flashMessenger->setNamespace('success')->addMessage('Success!');
				} catch (Exception $e) {
					$flashMessenger->setNamespace('error')->addMessage($e->getMessage());
					$flashMessenger->setNamespace('error')->addMessage('Insert error!');
				}
			}
			$this->_redirect('/bank/add');
		}
		$this->view->messages_success 	= $messages_success;
		$this->view->messages 			= $messages;
	}
	
	public function editAction(){
		$flashMessenger 	= $this->_helper->flashMessenger;
		$messages 			= $flashMessenger->setNamespace('error')->getMessages();
		$messages_success 	= $flashMessenger->setNamespace('success')->getMessages();
		$id 		= $this->getRequest()->getParam('id');
		$db 		= Zend_Registry::get('db');
		$QBank  	= new Application_Model_Bank();
		$where 		= $db->quoteInto('id = ?',$id);
		$bank 		= $QBank->fetchRow($where);
		
		if(!$bank){
			$messages[] = "A Bank is not exist!";
		}
		else{
			if($this->getRequest()->isPost()){
				$name 		= trim($this->getRequest()->getParam('name'),'');
				$fullname 	= $this->getRequest()->getParam('fullname');
				$checkValue = TRUE;
				if(	$name	==	''	){
					$checkValue = FALSE;
					$flashMessenger->setNamespace('error')->addMessage('Short name not empty!');
				}
				
				$where_exist = $QBank->getAdapter()->quoteInto('name = ?', $name);
				$check_exist = $QBank->fetchRow($where_exist);					
				if($check_exist){
					$checkValue = false;
					$flashMessenger->setNamespace('error')->addMessage('This name existed;');
				}
				
				if(	$checkValue	== TRUE){
					$data 	= array('name'=>$name,'fullname'=>$fullname);
					try {
						$QBank->update($data,$where);
						$flashMessenger->setNamespace('success')->addMessage('update success!');
					} catch (Exception $e) {
						$flashMessenger->setNamespace('error')->addMessage('Insert error!');
					}
				}
				$this->_redirect('/bank/edit?id='.$id);
			}
		}
		$this->view->messages_success 	= $messages_success;
		$this->view->messages 			= $messages;
		$this->view->bank 				= $bank;
	}
	
	public function delAction(){
		$flashMessenger 	= $this->_helper->flashMessenger;
		$id 				= $this->getRequest()->getParam('id');
		$db 				= Zend_Registry::get('db');
		$QBank  			= new Application_Model_Bank();
		$where 				= $db->quoteInto('id = ?',$id);
		$bank 				= $QBank->fetchRow($where);
		if($bank){
			try {
				$bank->delete();
				$flashMessenger->setNamespace('success')->addMessage('Removed');
			} catch (Exception $e) {
				$flashMessenger->setNamespace('error')->addMessage($e->getMessage());
			}
		}
		else{
			$flashMessenger->setNamespace('error')->addMessage('The bank is not exist');
		}
		
		$this->_redirect('/bank');
		
	}
}






