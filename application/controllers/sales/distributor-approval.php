<?php

		$flashMessenger = $this->_helper->flashMessenger;

		$id    	= $this->getRequest()->getParam('id');
		$save    	= $this->getRequest()->getParam('save');

		if($save){

			$list_id = str_replace(' ', '', $id);
			$list_id = explode(',', $list_id);

            $QDistributor  = new Application_Model_Distributor();

			$data_approval = array(
                'activate' => 1
            ); 

            $where_approve = array();
            $where_approve = $QDistributor->getAdapter()->quoteInto('id in (?)', $list_id);
                
            $save_status = $QDistributor->update($data_approval,$where_approve);

            if($save_status){
            	$flashMessenger->setNamespace('success')->addMessage('Approved success!');
            }else{
            	$flashMessenger->setNamespace('error')->addMessage('Can not Approval!');
            }

				$this->_redirect('/sales/distributor-approval');
                
		}
		

		$dis_id 		= $this->getRequest()->getParam('dis_id');
		$dis_name 		= $this->getRequest()->getParam('dis_name');


		$page 		= $this->getRequest()->getParam('page',1);
		$limit 		= LIMITATION;
		$sort 		= $this->getRequest()->getParam('sort');
		$desc    	= $this->getRequest()->getParam('desc', 1);
		$total 		= 0;

		$params 	= array(
				'dis_id' => $dis_id,
				'dis_name' => $dis_name,
				'sort' => $sort,
				'desc' => $desc
		);

		$params['approval'] = true;
		$QDistributorFile 		= new Application_Model_DistributorFile();
		$QDistributor 			= new Application_Model_Distributor();
		$list 		= $QDistributorFile->fetchPagination($page, $limit, $total, $params);

		foreach ($list as $key => $value) {
			$getMST = $QDistributor->getDistributorMST($value['d_id'],$value['mst_sn']);
			$list[$key]['display_mst'] = $getMST;
		}

		// print_r($list);
		$this->view->list 				= $list;

		$this->view->limit 				= $limit;
		$this->view->total 				= $total;
		$this->view->page 				= $page;
		$this->view->offset 			= $limit*($page-1);
		$this->view->params 			= $params;
		$this->view->sort 				= $sort;
		$this->view->desc 				= $desc;
		$this->view->url 				= HOST.'sales/distributor-approval/'.( $params ? '?'.http_build_query($params).'&' : '?' );

		$messages 			= $flashMessenger->setNamespace('error')->getMessages();
		$messages_success 	= $flashMessenger->setNamespace('success')->getMessages();

		$this->view->messages = $messages;
		$this->view->messages_success = $messages_success;