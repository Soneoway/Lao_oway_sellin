<?php

class IndexController extends My_Controller_Action
{
	public function indexAction()
	{
		$tabsets      = $this->getRequest()->getParam('tabset');

		$userStorage = Zend_Auth::getInstance()->getStorage()->read();
		$id = $userStorage->id;
		$QStaff = new Application_Model_Staff();
		$staffRowset = $QStaff->find($id);
        $staff = $staffRowset->current();
        $area_id = $staff['area_id'];

        $todate = date('d-m-Y');
		$toyear = date('Y');
		$numweek = date("W", strtotime($todate));
		$datefrom = date( "d/m/Y", strtotime($toyear."W".$numweek."1") );
		$dateto = date( "d/m/Y", strtotime($toyear."W".$numweek."7") );
        // print_r($area_id);
        // exit();
        $params2 = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('d/m/Y'),
            'group_by'        => 'm2.invoice_time',
        );
        $params = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('d/m/Y'),
        );
        if($tabsets == 'tabday'):
		$params = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('d/m/Y'),
        );
		$params2 = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('d/m/Y'),
            'group_by'        => 'm2.invoice_time',
        );
	    endif;
		if($tabsets == 'tabweek'):
		$params = array(
            'order_to'        => $dateto,
            'order_from'      => $datefrom,
        );
		$params2 = array(
            'order_to'        => $dateto,
            'order_from'      => $datefrom,
            'group_by'        => 'DATE_FORMAT(m2.invoice_time, "%d%")',
        );
        endif;
		if($tabsets == 'tabmonth'):
		$params = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('01/m/Y'),
        );
	    $params2 = array(
            'order_to'        => date('d/m/Y'),
            'order_from'      => date('01/m/Y'),
            'group_by'        => 'DATE_FORMAT(m2.invoice_time, "%d%")',
        );
        endif;
	    


		$QMarket         = new Application_Model_Market();
		$QDistributor    = new Application_Model_Distributor();
		$QWarehouse      = new Application_Model_Warehouse();
		$QArea           = new Application_Model_Area();

		$warehouses_cached = $QWarehouse->get_cache();
		$area              = $QArea->get_cache();
		$distributors      = $QDistributor->get_cache();


		$params['sort'] = $sort;
		$params['desc'] = $desc;

		if ($area_id != '----'):
			$params['area'] = $area_id;
		endif;
		if($area_id != ''):
			$params['area'] = $area_id;
		endif;
		if ($area_id == '----'):
			$params['area'] = '';
		endif;
		if($area_id == ''):
			$params['area'] = '';
		endif;
	
		$markets = array();

		    $markets_sn_dealer = $QMarket->chartCheckOrderDealer($params);
		    $markets_sn_wh = $QMarket->chartCheckOrderWH($params);
		    $markets_sn = $QMarket->chartCheckOrder($params2);

		$this->view->tabsets             = $tabsets;
		$this->view->area_id              = $area_id;
		$this->view->area                = $area;
		$this->view->warehouses_cached   = $warehouses_cached;
		$this->view->markets             = $markets;
		$this->view->markets_sn_dealer   = $markets_sn_dealer;
		$this->view->markets_sn_wh       = $markets_sn_wh;
		$this->view->markets_sn          = $markets_sn;
		$this->view->distributors        = $distributors;
	
	}
}