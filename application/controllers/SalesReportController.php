<?php
/**
 * @author buu.pham
 * @create 2015-08-26 11:40
 * Tách riêng các action export để phân quyền
 */
class SalesReportController extends My_Controller_Action
{
    public function orderListAction()
    {
        PC::db('new action');
    }

    public function orderImeiAction()
    {
        
    }

    public function distributorListAction()
    {
        $params = $this->getRequest()->getParam('params');
        $total  = 0;
        $params['export'] = 1;
        $QDistributor = new Application_Model_Distributor();

        $distributors = $QDistributor->fetchPagination(1, null, $total, $params);
        My_Report_Sales::distributorList($distributors);
    }
}
