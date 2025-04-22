<?php
class Application_Model_Asm extends Zend_Db_Table_Abstract
{
    protected $_name = 'asm';
    protected $_schema = HR_DB;

    /**
     * Lấy danh sách các area/province/district theo từng ASM
     * @param  int $staff_id    Staff ID (Default null)
     * @return array            Format array( asm ID => array(list region) )
     */
    public function get_cache($staff_id = null)
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false || ( !is_null($staff_id) && !isset($result[$staff_id]) )) {
            $db = Zend_Registry::get('db');

            $select_area = $db->select()
                ->from(array('p' => $this->_schema.'.'.$this->_name, array('p.staff_id')))
                ->joinLeft(array('r' => $this->_schema.'.'.'regional_market'), 'r.area_id=p.area_id', array('province' => 'r.id'))
                ->joinLeft(array('d' => $this->_schema.'.'.'regional_market'), 'd.parent=r.id', array('district' => 'd.id'));

            $select_province = $db->select()
                ->from(array('p' => $this->_schema.'.'.$this->_name, array('p.staff_id')))
                ->joinLeft(array('r' => $this->_schema.'.'.'regional_market'), 'r.id=p.area_id', array('province' => 'r.id'))
                ->joinLeft(array('d' => $this->_schema.'.'.'regional_market'), 'd.parent=r.id', array('district' => 'd.id'));

            $select = $db->select()
                ->union(array($select_area, $select_province));

            $data = $db->fetchAll($select);

            $result = array();

            if ($data){
                foreach ($data as $item){
                    if (!isset($result[$item['staff_id']]))
                        $result[ intval($item['staff_id']) ] = array('area' => array(), 'province' => array(), 'district' => array());

                    if ($item['type'] == My_Region::Area)
                        $result[ intval($item['staff_id']) ]['area'][] = intval($item['area_id']) ;

                    $result[ intval($item['staff_id']) ]['province'][] = intval($item['province']) ;
                    $result[ intval($item['staff_id']) ]['district'][] = intval($item['district']) ;
                }

                foreach ($result as $_staff_id => $value) {
                    $result[ $_staff_id ]['area'] = array_filter( array_unique( $value['area'] ) );
                    $result[ $_staff_id ]['province'] = array_filter( array_unique( $value['province'] ) );
                    $result[ $_staff_id ]['district'] = array_filter( array_unique( $value['district'] ) );
                }
            }

            $cache->save($result, $this->_name.'_cache', array(), 60*60*4); // cache 4 giờ
        }

        return is_null($staff_id) ? $result : ( isset($result[$staff_id]) ? $result[$staff_id] : false );
    }

     /**  Lấy danh sách các region theo từng ASM
     * @param  int $staff_id    Staff ID (Default null)
     * @return array            Format array( asm ID => array(list region) )
     */
    public function get_region_cache($staff_id = null)
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_region_new_cache');

        if ($result === false || ( !is_null($staff_id) && !isset($result[$staff_id]) )) {
            $data = $this->fetchAll();
            $result = array();

            if ($data){
                $QRegion = new Application_Model_RegionalMarket();

                foreach ($data as $item){
                    if (!isset($result[$item['staff_id']]))
                        $result[$item['staff_id']] = array();

                    $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $item['area_id']);
                    $regions = $QRegion->fetchAll($where);

                    if ($regions)
                        foreach ($regions as $reg)
                            $result[$item['staff_id']][] = $reg['id'];
                }
            }

            $cache->save($result, $this->_name.'_region_new_cache', array(), null);
        }

        return is_null($staff_id) ? $result : ( isset($result[$staff_id]) ? $result[$staff_id] : false );
    }



    public function is_asm($staff_id, $store_id)
    {
        $QStore = new Application_Model_Store();
        $store = $QStore->find($store_id);
        $store = $store->current();

        if (!$store) return false;

        $regions = $this->get_cache($staff_id);

        if ($regions && is_array($regions))
            return in_array($store['district'], $regions['district']);

        return false;
    }
}