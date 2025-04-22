<?php

class DistributorController  extends My_Controller_Action
{
	private $_key = 'jAun729*hA6T6sE2nP3!m3@a4hw';

    public function indexAction()
    {
    	$this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

		$key   = $this->getRequest()->getParam('key', '');

		if ($key != $this->_key) {
			echo json_encode(array('error' => 'wrong key'));
			exit;
		}

		$sort   = $this->getRequest()->getParam('sort', '');
		$desc   = $this->getRequest()->getParam('desc', 1);
		
		$id     = $this->getRequest()->getParam('id');
		$region = $this->getRequest()->getParam('region', 0);
		
		$page   = $this->getRequest()->getParam('page', 1);
		$limit  = LIMITATION;
		$total  = 0;

        $params = array(
			'sort'   => $sort,
			'desc'   => $desc,
			'id'     => $id,
			'region' => $region,
        );

        $QDistributor = new Application_Model_Distributor();
        $distributors = $QDistributor->fetchPagination($page, $limit, $total, $params);

        $QRegion = new Application_Model_Region();
        $regions = $QRegion->get_cache();

        $distributors_arr = array();

        foreach ($distributors as $k => $v) {
			$tmp = array();

			$tmp['id']     = $v['id'];
			$tmp['title']  = $v['title'];
			$tmp['region'] = isset($regions[$v['region']]) ? $regions[$v['region']] : "";
			$tmp['add']    = $v['add'];
			$tmp['tel']    = $v['tel'];

			$distributors_arr[] = $tmp;
        }

        $result = array(
        	'distributors' => $distributors_arr,
        	'page' => $page,
        	'total' => $total,
        	'limit' => $limit,
    	);

        echo json_encode($result);
        exit;
    }
}