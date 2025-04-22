<?php
class Application_Model_Service extends Zend_Db_Table_Abstract
{
	protected $_name = 'service';

    public function init()
    {
        error_reporting(1);
        require_once 'My'.DIRECTORY_SEPARATOR.'nusoap'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'nusoap.php';
        $this->wssURI = 'http://cs.opposhop.vn/wss?wsdl';
        $this->namespace = 'OPPOVN';
    }

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));

        if (isset($params['name']) and $params['name'])
            $select->where('p.name LIKE ?', '%'.$params['name'].'%');

        $select->limitPage($page, $limit);

        $result = $db->fetchAll($select);
        $total = $db->fetchOne("select FOUND_ROWS()");
        return $result;
    }

    function get_cache(){
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_cache');

        if ($result === false) {

            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),
                    array('p.*'));

            $select->order(new Zend_Db_Expr('p.`name` COLLATE utf8_unicode_ci'));

            $data = $db->fetchAll($select, 'name');

            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['name'];
                }
            }
            $cache->save($result, $this->_name.'_cache', array(), null);
        }
        return $result;
    }

    function get_cache_service()
    {
        $cache      = Zend_Registry::get('cache');
        $result     = $cache->load($this->_name.'_service_cache');

        if ($result === false) {

            $client = new nusoap_client($this->wssURI);
            $client->soap_defencoding = 'UTF-8';
            $client->decode_utf8 = false;
            $wsParams = array(
                'latitude' => 0,
                'longitude' => 0,
                'distance' => 0,
                'statusBit' => -1
            );

            try {

                $result = $client->call("getAllShowroom", $wsParams);

                $data = array();

                if ($result) {
                    foreach ($result as $k => $item) {
                        $data[$item['id']] = array(
                            'address' => isset($item['address']) && $item['address'] ? $item['address'] : '',
                            'contact' => isset($item['agent_name']) && $item['agent_name'] ? $item['agent_name'] : '',
                            'phone_number' => isset($item['agent_phone']) && $item['agent_phone'] ? $item['agent_phone'] : '',
                            'name' => isset($item['name']) && $item['name'] ? $item['name'] : '',
                            'district' => isset($item['district_id']) && $item['district_id'] ? $item['district_id'] : ''
                        );
                    }
                }

                return $data;

            } catch (Exception $e) {

                var_dump($e);
                exit;

            }
            $cache->save($result, $this->_name.'_service_cache', array(), null);
        }
        return $result;
    }

    function get_all_cache(){
       return $this->get_cache_service();
    }
}
