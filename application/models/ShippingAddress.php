<?php
class Application_Model_ShippingAddress extends Zend_Db_Table_Abstract
{
    protected $_name = 'shipping_address';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()

            ->from(array('ss' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS ss.id'), 'ss.*'));
        $select->joinLeft(array('sp'=>'shipping_provinces'),'sp.provice_id=ss.province_id',array('provice_name'));  
        $select->joinLeft(array('sa'=>'shipping_amphures'),'sa.amphure_id=ss.`amphures_id`',array('amphure_name'));  
        $select->joinLeft(array('sd'=>'shipping_districts'),'sd.district_code=ss.districts_id',array('district_name'));  
        $select->joinLeft(array('sz'=>'shipping_zipcodes'),'sz.zip_id=ss.zipcodes',array('zipcode')); 

        $select->where('ss.d_id = ?', $params['id']);
        $select->where('ss.status is null',1);

        if (isset($params['contact_name']) and $params['contact_name'])
        $select->where('ss.contact_name LIKE ?', '%'.$params['contact_name'].'%');

        if (isset($params['address']) and $params['address'])
            $select->where('ss.address LIKE ?', '%'.$params['address'].'%');

        if (isset($params['phone']) and $params['phone'])
            $select->where('ss.phone LIKE ?', '%'.$params['phone'].'%');


        // if (isset($params['d_id']) and intval($params['d_id']) > 0)
        //     $select->where('p.d_id = ?', $params['d_id']);

        // if (isset($params['status']) and $params['status'] != '')
        //     $select->where('p.status = ?', $params['status']);

        // $select->where('p.canceled IS NULL OR p.canceled !=1', null);

        if (isset($params['sort']) and $params['sort']) {
            $desc = (isset($params['desc']) and $params['desc'] == 1) ? ' DESC ' : ' ASC ';
            $order_str = 'ss.`'.$params['sort'] . '` ' . $desc;
            $select->order(new Zend_Db_Expr($order_str));
        }

        if ($limit)
            $select->limitPage($page, $limit);

       // echo $select;die;
        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
       
        return $result;
    }


    function getProvince(){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => 'shipping_provinces'),
                array( 'p.*'));
        $data = $db->fetchAll($select);
        $result = array();
        if ($data){
                foreach ($data as $item){
                   $result[$item['provice_id']] = $item['provice_name'];                        
                    
                }
            }
        
        return $result;
    }

    function getAreaName(){
        $db = Zend_Registry::get('db');

        $select="SELECT a.id AS area_id,a.name AS area_name FROM hr.area a WHERE a.name LIKE '%BKK%';"; 
        $result = $db->fetchAll($select);
        return $result;
    }

    

    function data_export_excel($d_id){
        // echo "TEST"; die;
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));
                  $select->where('p.d_id=?',$d_id);
            
        $result = $db->fetchAll($select);
        // echo $select; die;
        return $result;
    }

    function get_cache(){
  
            $db = Zend_Registry::get('db');

            $select = $db->select()
                ->from(array('p' => $this->_name),array('p.*'));

            $data = $db->fetchAll($select);
        
            $result = array();
            if ($data){
                foreach ($data as $item){
                    $result[$item['id']] = $item['address'];
                }
            }
           
        return $result;

    }

    function get_shipping_address_staff($staff_code)
    {  
        $db = Zend_Registry::get('db');

            // $sql ="SELECT s.code, CONCAT(s.firstname, ' ', s.lastname) AS staff_name, g.name, a.name,
            //        tp.name_th, tp.name_en,ifnull(am.th_province_id,1)as province_id
            //       FROM hr.staff AS s 
            //       JOIN hr.group AS g ON s.group_id = g.id 
            //       JOIN hr.regional_market AS rm ON s.regional_market = rm.id 
            //       JOIN hr.area AS a ON rm.area_id = a.id 
            //       LEFT JOIN hr.area_map AS am ON s.regional_market = am.regional_market 
            //       LEFT JOIN hr.th_province AS tp ON am.th_province_id = tp.id 
            //       WHERE s.code ='".$staff_code."' ";
            //       // print_r($staff_code); die;

            //     $provice = $db->fetchAll($sql);
            //     // print_r($provice); die;
                
            //     $list_provice = [];
            //     foreach ($provice as $value) {
            //         array_push($list_provice, $value['province_id']);
            //     } 

            //     if(!$list_provice){
            //         return [];
            //     }


            // // $select = $db->select()
            // //     ->from(array('p' => $this->_name),array('p.*'));
            // // $select->where('p.province_id IN (?)',$list_provice);
            // // $select->where('p.d_id = 34807');

        $sql ="SELECT 
            s.code,s.group_id,IFNULL(am.th_province_id,1)AS province_id, TRIM(tp.name_th) AS province_name,IFNULL(tpp.id,am.th_province_id) AS shipping_province_id,TRIM(IFNULL(tpp.name_th,tp.name_th)) AS shipping_province_name
        FROM hr.staff AS s 
        JOIN hr.group AS g ON s.group_id = g.id 
        JOIN hr.asm AS asm ON s.id = asm.staff_id 
        JOIN hr.area AS a ON asm.area_id = a.id 
        JOIN hr.regional_market AS rm ON a.id = rm.area_id 
        LEFT JOIN hr.area_map AS am ON rm.id = am.regional_market 
        LEFT JOIN hr.th_province AS tp ON tp.id =IFNULL(am.th_province_id, 1)
        LEFT JOIN warehouse.province_shipping_common sm ON sm.province_id= am.th_province_id
        LEFT JOIN hr.th_province AS tpp ON tpp.id = IFNULL(sm.shipping_province_id, sm.province_id)
        WHERE s.status=1 AND s.code='".$staff_code."' ";

        $provice = $db->fetchAll($sql);
        
        $list_provice = [];
        foreach ($provice as $value) {
            array_push($list_provice, $value['shipping_province_id']);
        } 

        if(!$list_provice){
            return [];
        }

        $select = "SELECT p.id, trim(p.contact_name) as contact_name, if(sp.provice_id=1,(concat(trim(p.address), ' แขวง',trim(sd.district_name), trim(sa.amphure_name), ' จ.',trim(sp.provice_name))),(concat(trim(p.address),' ต.',trim(sd.district_name),' อ.',trim(sa.amphure_name),' จ.',trim(sp.provice_name)))) as address_full, trim(p.address) as address, trim(sd.district_name) as district_name, trim(sa.amphure_name) as amphure_name, trim(sp.provice_name) as provice_name
        FROM warehouse.shipping_address As p 
        LEFT JOIN warehouse.shipping_provinces As sp ON sp.provice_id = p.province_id
        LEFT JOIN warehouse.shipping_amphures As sa ON sa.amphure_id = p.amphures_id
        LEFT JOIN warehouse.shipping_districts As sd ON sd.district_code=p.districts_id
        LEFT JOIN warehouse.shipping_zipcodes As sz ON sz.zip_id = p.zipcodes
        WHERE p.d_id = 34807 and p.status is null and p.province_id IN (".implode(",",$list_provice).")";

        // echo($select); die;
        $provice_address = $db->fetchAll($select);
    
        return $provice_address;
       
    }

    function get_staff_id_name($staff_code)
    {
        $db = Zend_Registry::get('db');

            $sql ="SELECT s.code, CONCAT(s.firstname, ' ', s.lastname) AS staff_name, g.name, a.name,
                   tp.name_th, tp.name_en,ifnull(am.th_province_id,1)as province_id
                  FROM hr.staff AS s 
                  JOIN hr.group AS g ON s.group_id = g.id 
                  JOIN hr.regional_market AS rm ON s.regional_market = rm.id 
                  JOIN hr.area AS a ON rm.area_id = a.id 
                  LEFT JOIN hr.area_map AS am ON s.regional_market = am.regional_market 
                  LEFT JOIN hr.th_province AS tp ON am.th_province_id = tp.id 
                  WHERE s.code ='".$staff_code."' ";
                  // print_r($staff_code); die;

                $staff = $db->fetchAll($sql);
                // print_r($staff); die;
               $list_staff = [];
                foreach ($staff as $value) {
                    array_push($list_staff, $value['staff_name']);
                }
            return $list_staff;
    }

    function checkAddressOnOrder($d_id,$shipping_address){
        $db = Zend_Registry::get('db');

        $select = $db->select()->from(array('p' => $this->_name),array('p.*'));
                  $select->where('p.id=?',$shipping_address);
                  $select->where('p.d_id=?',$d_id);
            
        $result = $db->fetchRow($select);
        // echo $select; die;
        return $result;
    }


}                                                      
