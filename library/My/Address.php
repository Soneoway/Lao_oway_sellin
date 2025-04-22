<?php 
/**
* 
*/
class My_Address extends Zend_Controller_Plugin_Abstract
{


    public static function genAddess($address,$districts,$amphures,$provinces,$zipcodes)
    {   
      
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes         = new Application_Model_ShippingZipcodes();
      
        try {
        
        $d_amphures     = $QShippingAmphures->get_data($amphures);
        $d_districts    = $QShippingDistricts->get_data($districts);
        $d_provinces    = $QShippingProvinces->get_data($provinces);      
        $d_zipcodes     = $QShippingZipcodes->get_data($zipcodes);

        $new_address = $address.' ตำบล'.$d_districts.' อำเภอ'.$d_amphures.' จังหวัด'.$d_provinces.' '.$d_zipcodes;
       
        $result = $new_address;
        
        } catch (Exception $e){
            $result = '-';
           
        }
        return $result;
    }

    public static function genAddessOrder($delivery_id)
    {   
      
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes          = new Application_Model_ShippingZipcodes();
        $QShippingAddress           = new Application_Model_ShippingAddress();
      
        try {
        $where = $QShippingAddress->getAdapter()->quoteInto('id = ?', $delivery_id);
        $ship  = $QShippingAddress->fetchAll($where);
        
        $d_amphures     = $QShippingAmphures->get_data($ship[0]['amphures_id']);
        $d_districts    = $QShippingDistricts->get_data($ship[0]['districts_id']);
        $d_provinces    = $QShippingProvinces->get_data($ship[0]['province_id']);      
        $d_zipcodes     = $QShippingZipcodes->get_data($ship[0]['zipcodes']);

        $new_address = $ship[0]['contact_name'].' '.$ship[0]['address'].' ต.'.$d_districts.' อ.'.$d_amphures.' จ.'.$d_provinces.' '.$d_zipcodes.' เบอร์โทร '.$ship[0]['phone'];
       
        $result = $new_address;
        } catch (Exception $e){
            $result = '-';
           
        }
        return $result;
    } 
    public static function genDataArray($delivery_id)
    {   
      
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes          = new Application_Model_ShippingZipcodes();
        $QShippingAddress           = new Application_Model_ShippingAddress();
      
        try {
        $where = $QShippingAddress->getAdapter()->quoteInto('id = ?', $delivery_id);
        $ship  = $QShippingAddress->fetchAll($where);
        
        $d_amphures     = $QShippingAmphures->get_data($ship[0]['amphures_id']);
        $d_districts    = $QShippingDistricts->get_data($ship[0]['districts_id']);
        $d_provinces    = $QShippingProvinces->get_data($ship[0]['province_id']);      
        $d_zipcodes     = $QShippingZipcodes->get_data($ship[0]['zipcodes']);
        $data = array(
                'contact_name'  => $ship[0]['contact_name'],
                'address'       => $ship[0]['address'],
                'districts'     => $d_districts,
                'amphures'      => $d_amphures,
                'provinces'     => $d_provinces,
                'zipcodes'      => $d_zipcodes,
                'phone'         => $ship[0]['phone']
            );
        $new_address = $data;
       
        $result = $new_address;
        } catch (Exception $e){
            $result = array(
                'contact_name'  => '',
                'address'       => '',
                'districts'     => '',
                'amphures'      => '',
                'provinces'     => '',
                'zipcodes'      => '',
                'phone'         => ''
            );
           
        }
        return $result;
    } 
    public static function genAddessDelivery($delivery_id)
    {   
      
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes          = new Application_Model_ShippingZipcodes();
        $QShippingAddress           = new Application_Model_ShippingAddress();
      
        try {
        $where = $QShippingAddress->getAdapter()->quoteInto('id = ?', $delivery_id);
        $ship  = $QShippingAddress->fetchAll($where);
        
        $d_amphures     = $QShippingAmphures->get_data($ship[0]['amphures_id']);
        $d_districts    = $QShippingDistricts->get_data($ship[0]['districts_id']);
        $d_provinces    = $QShippingProvinces->get_data($ship[0]['province_id']);      
        $d_zipcodes     = $QShippingZipcodes->get_data($ship[0]['zipcodes']);

        $new_address = $ship[0]['contact_name'].' '.$ship[0]['address'].' ต.'.$d_districts.' อ.'.$d_amphures.' จ.'.$d_provinces.' '.$d_zipcodes;
       
        $result = $new_address;
        } catch (Exception $e){
            $result = '-';
           
        }
        return $result;
    } 
    public static function genAddessDeliveryNote($delivery_id)
    {   
      
        $QShippingAmphures          = new Application_Model_ShippingAmphures();
        $QShippingDistricts         = new Application_Model_ShippingDistricts();
        $QShippingProvinces         = new Application_Model_ShippingProvinces();
        $QShippingZipcodes          = new Application_Model_ShippingZipcodes();
        $QShippingAddress           = new Application_Model_ShippingAddress();
      
        try {
        $where = $QShippingAddress->getAdapter()->quoteInto('id = ?', $delivery_id);
        $ship  = $QShippingAddress->fetchAll($where);
        
        $d_amphures     = $QShippingAmphures->get_data($ship[0]['amphures_id']);
        $d_districts    = $QShippingDistricts->get_data($ship[0]['districts_id']);
        $d_provinces    = $QShippingProvinces->get_data($ship[0]['province_id']);      
        $d_zipcodes     = $QShippingZipcodes->get_data($ship[0]['zipcodes']);

        $shipping = array();
        $shippinew_addressng = array();
        $shipping['contact_name'] = $ship[0]['contact_name'];
        $shipping['address'] = $ship[0]['address'];
        $shipping['districts'] = $d_districts;
        $shipping['amphures'] = $d_amphures;
        $shipping['provinces'] = $d_provinces;
        $shipping['zipcodes'] = $d_zipcodes;
        $shipping['phone'] = $ship[0]['phone'];
        $shipping['remark'] = $ship[0]['remark'];
        $new_address['address'] = $ship[0]['contact_name'].' '.$ship[0]['address'].' '.$d_districts.' '.$d_amphures.' '.$d_provinces.' '.$d_zipcodes;
        $new_address['phone'] =$ship[0]['phone'];
        $new_address['address_id'] =$ship[0]['id'];
       
        $result = $new_address;
        } catch (Exception $e){
            $result = '-';
           
        }
        return $result;
    }
    public static function genStaffName($staff_id)
    {   
      
       
        try {
        
        $db = Zend_Registry::get('db');
        $select = $db->select()
                         ->from(array('s' => 'staff'),
                array('s.firstname','s.lastname'));
        $staff_name = $db->fetchAll($select);                 
        
        $result = $staff_name[0]['firstname'].' '.$staff_name[0]['lastname'];
        
        } catch (Exception $e){
            $result = '-';
           
        }
        return $result;
    }
    
}
