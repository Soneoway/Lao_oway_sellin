<?php 
/**
* 
*/
class My_Distributor
{
    public static function generateStoreCode($distributor_type, $length = 5)
    {
        $store_code = null;

        $new_number = self::_get_max_store_code($distributor_type) + 1;

        if (isset($distributor_type) && !empty($distributor_type))
            $store_code = $distributor_type .sprintf('%0'.$length.'d', $new_number);

        return $store_code;
    }

    /**
     * Lấy store code lớn nhất hiện tại trong danh sách distributor
     * @param  [type] $distributor_type [description]
     * @return [type]                   [description]
     */
    private static function _get_max_store_code($distributor_type)
    {
        $db = Zend_Registry::get('db');

        $sql = sprintf("SELECT MAX(REPLACE( store_code, '%s', '')) AS maxnum
                FROM distributor
                WHERE store_code LIKE '%s%%'", $distributor_type, $distributor_type);

        $stmt = $db->query($sql);
        $stmt->setFetchMode(Zend_Db::FETCH_NUM);
        $result = $stmt->fetchAll();
        
        $max = $result && isset($result[0][0]) ? intval($result[0][0]) : 0;

        return $max;
    }
}