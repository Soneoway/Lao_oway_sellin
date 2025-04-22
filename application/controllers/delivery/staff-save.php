<?php
$hub_id         = $this->getRequest()->getParam('hub_id');
$carrier_id     = $this->getRequest()->getParam('carrier_id');
$staff_id       = $this->getRequest()->getParam('staff_id');
$refer          = $this->getRequest()->getParam('refer', HOST.'delivery/staff');
$flashMessenger = $this->_helper->flashMessenger;

$db = Zend_Registry::get('db');
$db->beginTransaction();

try {
    $QStaff = new Application_Model_Staff();
    $where = $QStaff->getAdapter()->quoteInto('id = ?', $staff_id);
    $staff_check = $QStaff->fetchRow($where);

    if (!$staff_check) throw new Exception("Invalid staff", 1);

    $QHub = new Application_Model_Hub();
    $hub_cache = $QHub->get_cache();

    if ($hub_id) {
        if (is_numeric($hub_id)) {
            if (!isset($hub_cache[ $hub_id ]))
                throw new Exception("Invalid hub", 2);

        } elseif (is_array($hub_id)) {
            foreach ($hub_id as $key => $value) {
                if (!isset($hub_cache[ $value ]))
                    throw new Exception("Invalid hub", 2);
            }
        }
    }

    if ($carrier_id && !isset(My_Carrier::$name[ $carrier_id ])) throw new Exception("Invalid carrier", 3);

    $QStaffHub = new Application_Model_StaffHub();
    $where = $QStaffHub->getAdapter()->quoteInto('staff_id = ?', $staff_id);
    $QStaffHub->delete($where);

    if ($hub_id) {
        if (is_numeric($hub_id)) {
            $data = array('staff_id' => $staff_id, 'hub_id' => $hub_id);
            $QStaffHub->insert($data);
        } elseif (is_array($hub_id)) {
            foreach ($hub_id as $key => $value) {
                $data = array('staff_id' => $staff_id, 'hub_id' => $value);
                $QStaffHub->insert($data);
            }
        }
    }

    $QStaffCarrier = new Application_Model_StaffCarrier();
    $where = $QStaffCarrier->getAdapter()->quoteInto('staff_id = ?', $staff_id);
    $QStaffCarrier->delete($where);

    if ($carrier_id) {
        $data = array('staff_id' => $staff_id, 'carrier_id' => $carrier_id);
        $QStaffCarrier->insert($data);
    }

    $cache = Zend_Registry::get('cache');
    $cache->remove('staff_hub_cache');
    $cache->remove('staff_carrier_cache');

    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    $flashMessenger->setNamespace('error')->addMessage(sprintf("[%s] %s", $e->getCode(), $e->getMessage()));
}

$this->_redirect($refer);

