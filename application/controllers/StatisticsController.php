<?php
class StatisticsController extends My_Controller_Action
{
    public function salesInAction(){
        $sort           = $this->getRequest()->getParam('sort', 'p.add_time');
        $desc           = $this->getRequest()->getParam('desc', 1);
        $page           = $this->getRequest()->getParam('page', 1);

        $sn              = $this->getRequest()->getParam('sn');
        $d_id            = $this->getRequest()->getParam('d_id');
        $good_id         = $this->getRequest()->getParam('good_id');
        $good_color      = $this->getRequest()->getParam('good_color');
        $pay_time        = $this->getRequest()->getParam('payment', 0);
        $outmysql_time   = $this->getRequest()->getParam('outmysql_time', 0);
        $created_at_to   = $this->getRequest()->getParam('created_at_to');
        $created_at_from = $this->getRequest()->getParam('created_at_from');
        $cat_id          = $this->getRequest()->getParam('cat_id');
        $export  = $this->getRequest()->getParam('export', 0);
        $limit = LIMITATION;
        $total = 0;

        $params = array_filter( array(
            'sn'              => $sn,
            'd_id'            => $d_id,
            'good_id'         => $good_id,
            'good_color'      => $good_color,
            'created_at_to'   => $created_at_to,
            'created_at_from' => $created_at_from,
            'cat_id'          => $cat_id,
        ));

        $params['isbacks'] = false;



        if ($pay_time)
            $params['payment'] = true;

        if ($outmysql_time)
            $params['outmysql_time'] = true;

        $QGood           = new Application_Model_Good();
        $QGoodColor      = new Application_Model_GoodColor();
        $QMarket         = new Application_Model_Market();
        $QDistributor    = new Application_Model_Distributor();
        $QGoodCategory   = new Application_Model_GoodCategory();

        $goods           = $QGood->get_cache();
        $goodColors      = $QGoodColor->get_cache();
        $distributors    = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        if ( isset($export) && $export ) {
            $params['export'] = $export;
            $sql = $QMarket->fetchPagination($page, null, $total, $params);
            $this->_exportSaleInXML( $sql );
        }

        $markets_sn = $QMarket->fetchPagination($page, $limit, $total, $params);

        //get total price
        $params['sales_in'] = true;
        $result = $QMarket->fetchPagination($page, null, $total2, $params);

        $this->view->total_qty_all   = isset($result[0]['total_qty_all']) ? $result[0]['total_qty_all'] : 0;
        $this->view->total_price_all = isset($result[0]['total_price_all']) ? $result[0]['total_price_all'] : 0;

        $this->view->goods           = $goods;
        $this->view->goodColors      = $goodColors;
        $this->view->markets_sn      = $markets_sn;
        $this->view->distributors    = $distributors;
        $this->view->good_categories = $good_categories;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'statistics/sales-in/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

    }

    public function salesOutAction(){
        $sort           = $this->getRequest()->getParam('sort', 'add_time');
        $desc           = $this->getRequest()->getParam('desc', 1);
        $page           = $this->getRequest()->getParam('page', 1);

        $good_id         = $this->getRequest()->getParam('good_id');
        $good_color      = $this->getRequest()->getParam('good_color');
        $activated_date_to   = $this->getRequest()->getParam('activated_date_to');
        $activated_date_from = $this->getRequest()->getParam('activated_date_from');
        $cat_id          = $this->getRequest()->getParam('cat_id');
        $export  = $this->getRequest()->getParam('export', 0);
        $limit = LIMITATION;
        $total = 0;

        $params = array_filter( array(
            'good_id'         => $good_id,
            'good_color'      => $good_color,
            'activated_date_to'   => $activated_date_to,
            'activated_date_from' => $activated_date_from,
            'cat_id'          => $cat_id,
        ));


        $QGood           = new Application_Model_Good();
        $QGoodColor      = new Application_Model_GoodColor();
        $QDistributor    = new Application_Model_Distributor();
        $QGoodCategory   = new Application_Model_GoodCategory();
        $QImei           = new Application_Model_Imei();

        $goods           = $QGood->get_cache();
        $goodColors      = $QGoodColor->get_cache();
        $distributors    = $QDistributor->get_cache();
        $good_categories = $QGoodCategory->get_cache();

        $params['sort'] = $sort;
        $params['desc'] = $desc;
    
        if ( isset($export) && $export ) {
            $params['export'] = $export;
            $sql = $QImei->fetchPaginationSalesOut($page, null , $total, $params);
            $this->_exportSaleOutXML($sql);
        }
            	
        $markets_sn = $QImei->fetchPaginationSalesOut($page, $limit, $total, $params);


        $this->view->goods           = $goods;
        $this->view->goodColors      = $goodColors;
        $this->view->markets_sn      = $markets_sn;
        $this->view->distributors    = $distributors;
        $this->view->good_categories = $good_categories;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'statistics/sales-out/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

    }

    private function _exportSaleInXML($sql)
    {
        require_once 'ExcelWriterXML.php';
        set_time_limit(0);
        ini_set('memory_limit', '200M');
        error_reporting(0);
        ini_set('display_error', 0);

        $filename = 'List_Sales_In_'.date('YmdHis');

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales IN');

        $heads = array(
            'No.',
            'SALES ORDER NO',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'RETAILER NAME',
            'REGION',
            'AREA',
            'SALES QUANTITY',
            'SALES PRICE',
            'TOTAL',
            'PAYMENT OR NOT',
            'SHIPPING',
            'OUT OF WAREHOUSE',
            'ORDER TIME'
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k=>$item){
            $sheet->stdOutSheetColumn('String', 1, $k+1, $item );
        }
        $sheet->stdOutSheetRowEnd();


        $QGood           = new Application_Model_Good();
        $QGoodColor      = new Application_Model_GoodColor();
        $QDistributor    = new Application_Model_Distributor();
        $QRegion         = new Application_Model_Region();
        $QArea           = new Application_Model_Area();

        $goods           = $QGood->get_cache();
        $goodColors      = $QGoodColor->get_cache();
        $distributors    = $QDistributor->get_cache2();
        $regions         = $QRegion->get_cache2();
        $areas           = $QArea->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con,"utf8");

        $result = mysqli_query($con,$sql);


        $i = 2;

        while($item = mysqli_fetch_assoc($result))
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            $row = array();

            if(isset($distributors) && isset($distributors[$item['d_id']]['title']))
                $distributor = $distributors[$item['d_id']]['title'];
            else
                $distributor = '';

            $region = (
                isset($distributors[$item['d_id']]['region'])
                and isset($regions[$distributors[$item['d_id']]['region']])
            ) ? $regions[$distributors[$item['d_id']]['region']]['name'] : '';

            $area = (
                isset($regions[$distributors[$item['d_id']]['region']])
                and isset($areas[$regions[$distributors[$item['d_id']]['region']]['area_id']])
            ) ? $areas[$regions[$distributors[$item['d_id']]['region']]['area_id']] : '';

            //check payment
            isset($item['pay_time'])?$pay='v':$pay='X';
            //check shipping
            if($item['shipping_yes_time'])                $shipping='v';              else              $shipping='X';
            //check out_warehouse
            isset($item['outmysql_time'])?$out='v':$out='X';
            if(isset($goods) && isset($goods[$item['good_id']]))
                $good_name=$goods[$item['good_id']];
            if(isset($goodColors) && isset($goodColors[$item['good_color']]))
                $good_color=$goodColors[$item['good_color']];


            $sheet->stdOutSheetColumn('String', $i,$j++,($i-1));
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['sn']);
            $sheet->stdOutSheetColumn('String', $i,$j++,$good_name);
            $sheet->stdOutSheetColumn('String', $i,$j++,$good_color);
            $sheet->stdOutSheetColumn('String', $i,$j++,$distributor);
            $sheet->stdOutSheetColumn('String', $i,$j++,$region);
            $sheet->stdOutSheetColumn('String', $i,$j++,$area);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['num']);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['price']);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['total']);
            $sheet->stdOutSheetColumn('String', $i,$j++,$pay);
            $sheet->stdOutSheetColumn('String', $i,$j++,$shipping);
            $sheet->stdOutSheetColumn('String', $i,$j++,$out);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['add_time']);

            $sheet->stdOutSheetRowEnd();

            $i++;

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }

    private function _exportSaleOutXML($sql)
    {
        require_once 'ExcelWriterXML.php';

        set_time_limit(0);
        ini_set('memory_limit', '200M');
        error_reporting(0);
        ini_set('display_error', 0);


        $filename = 'List_Sales_Out_'.date('YmdHis');

        $xml = new ExcelWriterXML($filename);
        $xml->docAuthor('OPPO Vietnam');

        $xml->sendHeaders();

        $xml->stdOutStart();

        $sheet = $xml->addSheet('Sales OUT');

        $heads = array(
            'No.',
            'PRODUCT NAME',
            'PRODUCT COLOR',
            'RETAILER NAME',
            'SALES QUANTITY',
            'ORDER TIME'
        );

        $sheet->stdOutSheetStart();


        $sheet->stdOutSheetRowStart(1);
        foreach ($heads as $k=>$item){
            $sheet->stdOutSheetColumn('String', 1, $k+1, $item );
        }
        $sheet->stdOutSheetRowEnd();

        $QGood           = new Application_Model_Good();
        $QGoodColor      = new Application_Model_GoodColor();
        $QDistributor    = new Application_Model_Distributor();

        $goods           = $QGood->get_cache();
        $goodColors      = $QGoodColor->get_cache();
        $distributors    = $QDistributor->get_cache();

        //load config
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        $config = $config->toArray();
        $con=mysqli_connect($config['resources']['db']['params']['host'],$config['resources']['db']['params']['username'],$config['resources']['db']['params']['password'],$config['resources']['db']['params']['dbname']);
        mysqli_set_charset($con,"utf8");

        $result = mysqli_query($con,$sql);

        $i = 2;

        while($item = mysqli_fetch_assoc($result))
        {
            $sheet->stdOutSheetRowStart($i);

            $j = 1;

            if(isset($distributors) && isset($distributors[$item['distributor_id']]))
                $distributor = $distributors[$item['distributor_id']];
            else
                $distributor = '';


            if(isset($goods) && isset($goods[$item['good_id']]))
                $good_name=$goods[$item['good_id']];
            if(isset($goodColors) && isset($goodColors[$item['good_color']]))
                $good_color=$goodColors[$item['good_color']];


            $sheet->stdOutSheetColumn('String', $i,$j++,$i-1);
            $sheet->stdOutSheetColumn('String', $i,$j++,$good_name);
            $sheet->stdOutSheetColumn('String', $i,$j++,$good_color);
            $sheet->stdOutSheetColumn('String', $i,$j++,$distributor);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['goods_num']);
            $sheet->stdOutSheetColumn('String', $i,$j++,$item['add_time']);

            $sheet->stdOutSheetRowEnd();

            $i++;

        }

        $sheet->stdOutSheetEnd();

        $xml->stdOutEnd();

        exit;
    }

}