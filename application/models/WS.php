<?php
//fetch Imei
//get only from wspush
function fetch_imei($page=1, $limit=1000){
    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => 'imei'),
            array('p.*'));

    $select->join(array('wi' => 'wspush_imei'),
        'wi.imei_sn = p.imei_sn',
        array());

	$select->where('wi.created_at > ?', date('Y-m-d H:i:s', strtotime('-1 hour')));
		
    //$select->order('wi.created_at DESC');

    if ($limit)
        $select->limitPage($page, $limit);
	

    $result = $db->fetchAll($select);
	if ($result)
		return json_encode($result);
	else 
		return null;
}

//fetch Imei Activation
//get only from wspush
function fetch_imeiactivation($page=1, $limit=1000){
    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => 'imei_activation'),
            array('p.*'));

    $select->join(array('wi' => 'wspush_imei'),
        'wi.imei_sn = p.imei_sn',
        array());
		
	$select->where('wi.created_at > ?', date('Y-m-d H:i:s', strtotime('-1 hour')));

    //$select->order('wi.created_at DESC');

    if ($limit)
        $select->limitPage($page, $limit);

    $result = $db->fetchAll($select);
	if ($result)
		return json_encode($result);
	else 
		return null;
}

//fetch Market
//get only from wspush
function fetch_market($page=1, $limit=1000){
    $db = Zend_Registry::get('db');

    $select = $db->select()
        ->from(array('p' => 'market'),
            array('p.*'));

    $select->join(array('wm' => 'wspush_market'),
        'wm.sn = p.sn',
        array());
		
	$select->where('wm.created_at > ?', date('Y-m-d H:i:s', strtotime('-1 hour')));

    //$select->order('wm.created_at DESC');

    if ($limit)
        $select->limitPage($page, $limit);

    $result = $db->fetchAll($select);
    if ($result)
		return json_encode($result);
	else 
		return null;
}

function fetch_good(){
    $QGood = new Application_Model_Good();
    $result = $QGood->fetchAll();
    if ($result)
		return json_encode($result->toArray());
	else 
		return null;
}

function fetch_good_color(){
    $QGood = new Application_Model_GoodColor();
    $result = $QGood->fetchAll();
    if ($result)
		return json_encode($result->toArray());
	else 
		return null;
}

function fetch_good_category(){
    $QGood = new Application_Model_GoodCategory();
    $result = $QGood->fetchAll();
    if ($result)
		return json_encode($result->toArray());
	else 
		return null;
}

function fetch_good_color_combined(){
    $QGood = new Application_Model_GoodColorCombined();
    $result = $QGood->fetchAll();
    if ($result)
		return json_encode($result->toArray());
	else 
		return null;
}

function fetch_distributor(){
    $QDistributor = new Application_Model_Distributor();
    $result = $QDistributor->fetchAll();
    if ($result)
        return json_encode($result->toArray());
    else 
        return null;
}

function fetch_area(){
    $QArea = new Application_Model_Area();
    $result = $QArea->fetchAll();
    if ($result)
        return json_encode($result->toArray());
    else 
        return null;
}

function fetch_region(){
    $QRegion = new Application_Model_Region();
    $result = $QRegion->fetchAll();
    if ($result)
		return json_encode($result->toArray());
	else 
		return null;
}


function fetch_price_log(){
    $QPrice = new Application_Model_GoodPriceLog();
    $result = $QPrice->fetchAll();
    if ($result)
        return json_encode($result->toArray());
    else 
        return null;
}