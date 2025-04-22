<?php

try {
    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    $flashMessenger = $this->_helper->flashMessenger;

    $id              = $this->getRequest()->getParam('id');
    $name            = $this->getRequest()->getParam('name');
    $address         = $this->getRequest()->getParam('address');
    $phone_number    = $this->getRequest()->getParam('phone_number');
    $contact         = $this->getRequest()->getParam('contact');
    $mobile_phone    = $this->getRequest()->getParam('mobile_phone');
    $region          = $this->getRequest()->getParam('region', array());
    $region_district = $this->getRequest()->getParam('region_district', array());
    $district_id     = $this->getRequest()->getParam('district_id', array());

    $refer           = $this->getRequest()->getParam('refer', HOST.'hub');

    if (!$name) throw new Exception("Name cannot blank", 2);

    $QHub = new Application_Model_Hub();

    /////////////////////
    /// UPDATE HUB INFO
    /////////////////////

    if ($id) $id  = intval($id);
    $name         = My_String::trim($name);
    $address      = My_String::trim($address);
    $phone_number = My_String::trim($phone_number);
    $contact      = My_String::trim($contact);
    $mobile_phone = My_String::trim($mobile_phone);

    $data = array(
        'name'         => $name,
        'address'      => $address,
        'phone_number' => $phone_number,
        'contact'      => $contact,
        'mobile_phone' => $mobile_phone,
    );

    $where = array();

    $where[] = $QHub->getAdapter()->quoteInto('name LIKE ?', $name);

    if ($id)
        $where[] = $QHub->getAdapter()->quoteInto('id <> ?', $id);

    $hub_check = $QHub->fetchRow($where);

    if ($hub_check) throw new Exception("Name Exists", 5);

    if ($id) {
        $where = $QHub->getAdapter()->quoteInto('id = ?', $id);
        $hub_check = $QHub->fetchRow($where);

        if (!$hub_check) throw new Exception("Wrong ID", 4);

        $where = $QHub->getAdapter()->quoteInto('id = ?', $id);
        $QHub->update($data, $where);

    } else {
        $id = $QHub->insert($data);
        if (!$id) throw new Exception("Insert failed", 6);
    }

    /////////////////////////////////////
    /// UPDATE HUB PROVINCE AND DISTRICT
    /////////////////////////////////////

    $QHubDistrict = new Application_Model_HubDistrict();

    // fetch old districts
    $where = $QHubDistrict->getAdapter()->quoteInto('hub_id = ?', $id);
    $regions = $QHubDistrict->fetchAll($where); // province + district
    $old_provinces = array(); // phân loại
    $old_districts = array();

    foreach ($regions as $key => $value) {
        if ($value['type'] == 1) $old_districts[] = $value['region_id'];
        if ($value['type'] == 2) $old_provinces[] = $value['region_id'];
    }
    
    // xóa province, district bị remove
    $delete_province = array_diff($old_provinces, $region);
    $delete_province = is_array($delete_province) ? $delete_province : array();

    if (count($delete_province)) {
        $where = array();
        $where[] = $QHubDistrict->getAdapter()->quoteInto('region_id IN (?)', $delete_province);
        $where[] = $QHubDistrict->getAdapter()->quoteInto('type = ?', 2);
        $QHubDistrict->delete($where);
    }

    $delete_district = array_diff($old_districts, $district_id);
    $delete_district = is_array($delete_district) ? $delete_district : array();

    if (count($delete_district)) {
        $where = array();
        $where[] = $QHubDistrict->getAdapter()->quoteInto('region_id IN (?)', $delete_district);
        $where[] = $QHubDistrict->getAdapter()->quoteInto('type = ?', 1);
        $QHubDistrict->delete($where);
    }

    // insert các province, district mới
    $add_province = array_diff($region, $old_provinces);
    $add_province = is_array($add_province) ? $add_province : array();

    if (count($add_province)) {
        foreach ($add_province as $key => $value) {
            $data = array('hub_id' => $id, 'region_id' => $value, 'type' => 2);
            $QHubDistrict->insert($data);
        }
    }

    $add_district = array_diff($district_id, $old_districts);

    if (count($add_district)) {
        foreach ($add_district as $key => $value) {
            $data = array('hub_id' => $id, 'region_id' => $value, 'type' => 1);
            $QHubDistrict->insert($data);
        }
    }

    $cache = Zend_Registry::get('cache');
    $cache->remove('hub_cache');
    $cache->remove('hub_all_cache');

    $db->commit();

    $flashMessenger->setNamespace('success')->addMessage('Success');
    $this->_redirect($refer);
} catch (Exception $e) {
    $db->rollback();

    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%d] %s", $e->getCode(), $e->getMessage()));

    if ($id)
        $this->_redirect(HOST.'hub/edit?id='.$id);
    else
        $this->_redirect(HOST.'hub/create');
}