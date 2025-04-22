<?php
class WssController extends My_Controller_Action
{
    private $wssURI;
    private $namespace;

    public function init()
    {
        error_reporting(0);

        require_once 'My'.DIRECTORY_SEPARATOR.'nusoap'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'nusoap.php';

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->wssURI = HOST.'wss';
        $this->namespace = 'OPPOVN';
    }

    public function indexAction()
    {
        //Create a new soap server
        $server = new soap_server(null, array('uri' => $this->wssURI));
        $server->soap_defencoding = 'utf-8';
        $server->encode_utf8 = true;

        //Configure our WSDL
        $server->configureWSDL($this->namespace, $this->namespace, $this->wssURI);

        $server->wsdl->addComplexType(
            'OrderSN',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'sn'   => array('name' => 'sn', 'type' => 'xsd:string'),
                'text' => array('name' => 'text', 'type' => 'xsd:string'),
                'outmysql_time' => array('name' => 'outmysql_time', 'type' => 'xsd:string'),

            )
        );

        $server->wsdl->addComplexType(
            'OrderSNs',
            'complexType',
            'array',
            '',
            'SOAP-ENC:Array',
            array(),
            array(
                array('ref' => 'SOAP-ENC:arrayType',
                    'wsdl:arrayType' => 'tns:OrderSN[]')
            ),
            'tns:OrderSN'
        );

        $server->register('getOrderForCS',                    // method name
            array(
                'ORDER_FOR_OPPO_SERVICE' => 'xsd:string',
                'wh_service_id' => 'xsd:string',
                'orderPOListSns' => 'tns:array',
                'GET_PO_FROM_TIME' => 'xsd:string',
            ),          // input parameters
            array('return' => 'tns:OrderSNs'),    // output parameters
            $this->namespace,                         // namespace
            $this->namespace . '#getOrderForCS',                   // soapaction
            'rpc',                                    // style
            'encoded',                                // use
            'Get Specific Orders SN'        // documentation
        );

        $server->wsdl->addComplexType(
            'Order',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'id' => array('name' => 'id', 'type' => 'xsd:string'),
                'sn' => array('name' => 'sn', 'type' => 'xsd:string'),
                'price' => array('name' => 'price', 'type' => 'xsd:string'),
                'total' => array('name' => 'total', 'type' => 'xsd:string'),
                'good_color' => array('name' => 'good_color', 'type' => 'xsd:string'),
                'good_id' => array('name' => 'good_id', 'type' => 'xsd:string'),
                'cat_id' => array('name' => 'cat_id', 'type' => 'xsd:string'),
                'type' => array('name' => 'type', 'type' => 'xsd:string'),
                'num' => array('name' => 'num', 'type' => 'xsd:string'),
                'warehouse_id' => array('name' => 'warehouse_id', 'type' => 'xsd:string'),

            )
        );

        $server->wsdl->addComplexType(
            'Orders',
            'complexType',
            'array',
            '',
            'SOAP-ENC:Array',
            array(),
            array(
                array('ref' => 'SOAP-ENC:arrayType',
                    'wsdl:arrayType' => 'tns:Order[]')
            ),
            'tns:Order'
        );

        $server->register('getDetailOrderForCS',                    // method name
            array(
                'ORDER_FOR_OPPO_SERVICE' => 'xsd:string',
                'wh_order_sn' => 'xsd:string'
            ),          // input parameters
            array('return' => 'tns:Orders'),    // output parameters
            $this->namespace,                         // namespace
            $this->namespace . '#getDetailOrderForCS',                   // soapaction
            'rpc',                                    // style
            'encoded',                                // use
            'Get Specific Orders'        // documentation
        );

        $server->wsdl->addComplexType(
            'IMEI',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'imei_sn' => array('name' => 'imei_sn',
                    'type' => 'xsd:string'),

            )
        );

        $server->wsdl->addComplexType(
            'IMEIs',
            'complexType',
            'array',
            '',
            'SOAP-ENC:Array',
            array(),
            array(
                array('ref' => 'SOAP-ENC:arrayType',
                    'wsdl:arrayType' => 'tns:IMEI[]')
            ),
            'tns:IMEI'
        );

        $server->register('getIMEIDetailOrderForCS',                    // method name
            array('sales_id' => 'xsd:string'),          // input parameters
            array('return' => 'tns:IMEIs'),    // output parameters
            $this->namespace,                         // namespace
            $this->namespace . '#getIMEIDetailOrderForCS',                   // soapaction
            'rpc',                                    // style
            'encoded',                                // use
            'Get Specific IMEI'        // documentation
        );

        $server->register(
            'getImeiInfo',
            array('imei'=>'xsd:string'),
            array('return'=>'xsd:Array'),
            $this->namespace,
            false,
            'rpc',
            'encoded',
            'description'
        );

        $server->register(
            'getImeiInfoCheck',
            array('imei'=>'xsd:string'),
            array('return'=>'xsd:Array'),
            $this->namespace,
            false,
            'rpc',
            'encoded',
            'description'
        );

        $server->register(
            'getCustomerInfo',
            array('imei'=>'xsd:string'),
            array('return'=>'xsd:Array'),
            $this->namespace,
            false,
            'rpc',
            'encoded',
            'description'
        );

        /* WSDL nay dung de get danh sach cac cua dealer */
        $server->wsdl->addComplexType(
            'Dealer',
            'complexType',
            'struct',
            'all',
            '',
            array(
                'id'   => array('name' => 'id', 'type' => 'xsd:string'),
                'title' => array('name' => 'title', 'type' => 'xsd:string')
            )
        );

        $server->wsdl->addComplexType(
            'Dealers',
            'complexType',
            'array',
            '',
            'SOAP-ENC:Array',
            array(),
            array(
                array('ref' => 'SOAP-ENC:arrayType',
                    'wsdl:arrayType' => 'tns:Dealer[]')
            ),
            'tns:Dealer'
        );

        $server->register('getDealersBanking',      // method name
            array(
                'dealer_code'=>'xsd:string',
            ),                                    // input parameters
            array('return' => 'xsd:string'),     // output parameters
            $this->namespace,                     // namespace
            $this->namespace . '#getDealersBanking',// soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Get Dealer For Banking By Code'               // documentation
        );

        $server->register('getDealers',      // method name
            array(
                'dealer_name'=>'xsd:string',
                'dealer_id'  =>'xsd:string',
                'dealer_code'=>'xsd:string',
            ),                                    // input parameters
            array('return' => 'xsd:Array'),     // output parameters
            $this->namespace,                     // namespace
            $this->namespace . '#getDealers',// soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Get All Dealer'               // documentation
        );

        $server->register('getProducts',      // method name
            array(
            ),                                    // input parameters
            array('return' => 'xsd:Array'),     // output parameters
            $this->namespace,                     // namespace
            $this->namespace . '#getProducts',// soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Get All Product'               // documentation
        );

        $server->register('getWarehouses',      // method name
            array(
            ),                                    // input parameters
            array('return' => 'xsd:Array'),     // output parameters
            $this->namespace,                     // namespace
            $this->namespace . '#getWarehouses',// soapaction
            'rpc',                                // style
            'encoded',                            // use
            'Get All Warehouse'               // documentation
        );

        // Complex Array Keys and Types ++++++++++++++++++++++++++++++++++++++++++
        $server->wsdl->addComplexType('params', 'complexType', 'struct', 'all', '', array(
            'name'  => array('name' => 'name','type' => 'xsd:string'),
            'value' => array('name' => 'value','type' => 'xsd:string'),
            'code'  => array('name' => 'code','type' => 'xsd:string'),
        ));

        // Complex Array ++++++++++++++++++++++++++++++++++++++++++
        $server->wsdl->addComplexType(
            'paramsArray',
            'complexType',
            'array',
            '',
            'SOAP-ENC:Array',
            array(),
            array(array(
                'ref' => 'SOAP-ENC:arrayType',
                'wsdl:arrayType' => 'tns:params[]'
            )),
            'tns:params'
        );

        // ---------------- wsUpdateImeiInfo
        $server->register(
            'wsUpdateImeiInfo',
            array('imei' => 'xsd:string', 'data' => 'tns:paramsArray'),
            array('return' => 'xsd:int'), // output parameters
            $this->namespace,
            $this->namespace.'#wsUpdateImeiInfo',
            'rpc',
            'encoded',
            'Update IMEI information'
        );

        /****************************************************/

        $POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : '';

        $server->service($POST_DATA);
    }

}

function wsUpdateImeiInfo($imei, $data)
{
    try {
        $data_array = array();

        foreach ($data as $_key => $_value)
            $data_array[$_value['name']] = intval($_value['value']);
        $QImei = new Application_Model_Imei();
        $where = $QImei->getAdapter()->quoteInto('imei_sn = ?', $imei);
        $QImei->update($data_array, $where);
        return 0;
    } catch (Exception $e){
        return 1;
    }
}

/*function get all Products*/
function getProducts()
{
    $rs = array(
        'ResponseCode'  =>'00',
        'Description'   =>'Successful',
    );

    try {
        $QGood = new Application_Model_Good();

        $data = $QGood->get_model_color_cache();
        $re = array();
        foreach ($data as $k => $item){
            if ($item['cat_id'] == PHONE_CAT_ID)
                $re[$k] = $item;
        }
        $rs['data'] = $re;
    } catch (Exception $e){
        $Description = $e->getMessage();
        if (!isset($ResponseCode)){
            $ResponseCode   = '01';
            $Description    = 'Unknown Error';
        }
        $rs['ResponseCode'] = $ResponseCode;
        $rs['Description'] = $Description;
    }
    return $rs;
}
/* end Products */



/*function get all Warehouse*/
function getWarehouses()
{
    $rs = array(
        'ResponseCode'  =>'00',
        'Description'   =>'Successful',
    );

    try {
        $QWarehouse = new Application_Model_Warehouse();
        $data = $QWarehouse->get_cache();

        $re = array();
        foreach ($data as $k => $item){
                $re[$k] = array(
                    'id' => $k,
                    'name' => $item
                );
        }

        $rs['data'] = $re;
    } catch (Exception $e){
        $Description = $e->getMessage();
        if (!isset($ResponseCode)){
            $ResponseCode   = '01';
            $Description    = 'Unknown Error';
        }
        $rs['ResponseCode'] = $ResponseCode;
        $rs['Description'] = $Description;
    }
    return $rs;
}
/* end Warehouse */

function getDealersBanking($dealer_code = null){
    $rs = array(
        'ResponseCode'  =>'00',
        'Description'   =>'Successful',
    );
    try {
        $re = getDealers(null,null,$dealer_code);
        if ($re['ResponseCode']!='00'){
            $ResponseCode   = $re['ResponseCode'];
            throw new Exception($re['Description']);
        }
        $rs['data'] = $re['data'][0]['title'];
    } catch (Exception $e){
        $Description = $e->getMessage();
        if (!isset($ResponseCode)){
            $ResponseCode   = '01';
            $Description    = 'Unknown Error';
        }
        $rs['ResponseCode'] = $ResponseCode;
        $rs['Description'] = $Description;
    }
    return $rs['ResponseCode'].'|'.$rs['Description'].'|'.$rs['data'];
}

/*function get all Dealer for cs*/
function getDealers($dealer_name = null, $dealer_id = null, $dealer_code = null)
{
    $rs = array(
        'ResponseCode'  =>'00',
        'Description'   =>'Successful',
    );

    try {
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('d' => 'distributor'), array('d.id', 'd.title', 'd.store_code'));
        if (isset($dealer_name) and $dealer_name)
            $select->where('d.title LIKE ?', '%' . $dealer_name . '%');
        if (isset($dealer_id) and $dealer_id)
            $select->where('d.id = ?', $dealer_id);
        if (isset($dealer_code) and $dealer_code)
            $select->where('d.store_code = ?', $dealer_code);

        $rs['data'] = $db->fetchAll($select);
    } catch (Exception $e){
        $Description = $e->getMessage();
        if (!isset($ResponseCode)){
            $ResponseCode   = '01';
            $Description    = 'Unknown Error';
        }
        $rs['ResponseCode'] = $ResponseCode;
        $rs['Description'] = $Description;
    }
    return $rs;
}
/* end store */

function getOrderForCS($ORDER_FOR_OPPO_SERVICE, $wh_service_id, $orderPOListSns=null, $GET_PO_FROM_TIME=null)
{

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('m' => 'market'), array('m.sn', 'm.outmysql_time', 'm.text' ))
        ->where('d_id = ?', $ORDER_FOR_OPPO_SERVICE)
        ->where('service = ?', $wh_service_id)
        ->where('outmysql_time IS NOT NULL')
    ;

    if ($orderPOListSns)
        $select->where('sn NOT IN (?)', $orderPOListSns);

    if ($GET_PO_FROM_TIME)
        $select->where('outmysql_time >= ?', $GET_PO_FROM_TIME);

    $select->group('sn');

    $result = $db->fetchAll($select);

    return $result;
}

function getDetailOrderForCS($ORDER_FOR_OPPO_SERVICE, $wh_order_sn)
{
    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('m' => 'market'), array('m.id', 'm.sn', 'm.price', 'm.total', 'm.good_color', 'm.good_id', 'm.cat_id', 'm.type', 'm.num', 'm.warehouse_id', ))
        ->where('d_id = ?', $ORDER_FOR_OPPO_SERVICE)
        ->where('sn = ?', $wh_order_sn)
        ->where('outmysql_time IS NOT NULL')
    ;

    $result = $db->fetchAll($select);

    return $result;
}

function getIMEIDetailOrderForCS($sales_id)
{
    if (!$sales_id){
        return array();
    }

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('i' => 'imei'), array('i.imei_sn'))
        ->where('sales_id = ?', $sales_id)
    ;

    $result = $db->fetchAll($select);

    return $result;

}

function getCustomerInfo($imei){

    if (!$imei){
        return array('code' => 1);
    }

    $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    $config = $config->toArray();

    // addition variable for short name
    $cf_tunnel_center   = $config['resources']['dbtunnel']['params'];

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('ts' => $cf_tunnel_center['dbname'].'.timing_sale'), array('ts.product_id','ts.model_id','ts.imei'))
        ->join(array('t' => $cf_tunnel_center['dbname'].'.timing'), 't.id=ts.timing_id', array('t.created_at'))
        ->join(array('c' => $cf_tunnel_center['dbname'].'.customer'), 'ts.customer_id=c.id', array('c.name', 'c.phone_number', 'c.email', 'c.address'));

    $select->where('ts.imei = ?', $imei);
    $select->where('t.approved_at IS NOT NULL');
    $select->where('t.approved_at <> 0');
    $select->where('t.approved_at <> \'\'');

    $customerInfo = $db->fetchRow($select);

    if ($customerInfo){
        $result['code'] = 0;
        $result['imei_sn'] = $customerInfo['imei'];
        $result['product_id'] = $customerInfo['product_id'];
        $result['model_id'] = $customerInfo['model_id'];
        $result['created_at'] = $customerInfo['created_at'];
        $result['name'] = $customerInfo['name'];
        $result['phone_number'] = $customerInfo['phone_number'];
        $result['email'] = $customerInfo['email'];
        $result['address'] = $customerInfo['address'];
    } else {
        $result['code'] = 2;
    }

    return $result;
}

function getImeiInfoCheck($imei)
{
    $result = $info = array();
    if (!$imei){
        return array('code' => 1);
    }

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('i' => 'imei'), array('i.*'))
        ->join(array('g' => 'good'), 'i.good_id = g.id', array('good_name' => 'g.name'))
        ->join(array('gc' => 'good_color'), 'i.good_color = gc.id', array('good_color_name' => 'gc.name'))
        ->where('imei_sn = ?', $imei);

    $imeiInfo = $db->fetchRow($select);

    if ($imeiInfo){
        $info['imei_sn']      = $imeiInfo['imei_sn'];
        $info['good_name']    = $imeiInfo['good_name'];
        $info['good_color']   = $imeiInfo['good_color_name'];
        $info['status']       = $imeiInfo['status'];
    }

    if ($info){
        $result = $info;

        $result['code'] = 0;
    } else {
        $result['code'] = 2;
    }

    return $result;

}

function getImeiInfo($imei){
    $result = $info = array();

    if (!$imei){
        return array('code' => 1);
    }

    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('i' => 'imei'), array('i.*'))
        ->where('imei_sn = ?', $imei);

    $imeiInfo = $db->fetchRow($select);
    if ($imeiInfo){
        $info['imei_sn'] = $imeiInfo['imei_sn'];
        $info['good_id'] = $imeiInfo['good_id'];
        $info['good_color'] = $imeiInfo['good_color'];
        $info['distributor_id'] = $imeiInfo['distributor_id'];
        $info['into_date'] = $imeiInfo['into_date'];
        $info['out_date'] = $imeiInfo['out_date'];
        $info['activated_date'] = $imeiInfo['activated_date'];
        $info['status'] = $imeiInfo['status'];
    }

    $select = $db->select()
        ->from(array('iwp' => 'imei_warranty_period'), array('iwp.*'))
        ->where('iwp.imei_sn = ?', $imei);
    $imeiWarrantyPeriodInfo = $db->fetchRow($select);
    if ($imeiWarrantyPeriodInfo){
        $info['iwp_imei_sn'] = $imeiWarrantyPeriodInfo['imei_sn'];
        $info['iwp_good_id'] = $imeiWarrantyPeriodInfo['good_id'];
        $info['iwp_good_color'] = $imeiWarrantyPeriodInfo['good_color'];
        $info['iwp_out_date'] = $imeiWarrantyPeriodInfo['out_date'];
        $info['iwp_warranty_period'] = $imeiWarrantyPeriodInfo['warranty_period'];
        $info['iwp_note'] = $imeiWarrantyPeriodInfo['note'];
    }
    if ($info){
        $result = $info;

        $result['code'] = 0;
    } else {
        $result['code'] = 2;
    }

    return $result;
}