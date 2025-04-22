<?php
class Application_Model_HubDistrict extends Zend_Db_Table_Abstract{
    protected $_name = 'hub_district';

    public function checkDistrictHub($district_id)
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('h' => $this->_name), array('h.hub_id'));

        $QRegion = new Application_Model_RegionalMarket();
        $province_id = $QRegion->nget_province_id_by_district_cache($district_id);

        $select
            ->where('h.type = 1 AND h.region_id = ?', $district_id)
            ->orWhere('h.type = 2 AND h.region_id = ?', $province_id);

        $hub = $db->fetchOne($select);

        if ($hub) {
            $QHub = new Application_Model_Hub();
            $hub_cache = $QHub->get_all_cache();
            return isset($hub_cache[ $hub ]) ? $hub_cache[ $hub ] : false;
        }

        return false;
    }
}