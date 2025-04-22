<?php
class Application_Model_EpPrivilegesDiscount extends Zend_Db_Table_Abstract
{
	protected $_name = 'ep_privileges_discount';

    public function _initConfig($company_id)
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        if($company_id=='1')//OPPO
        {
            $db = Zend_Db::factory($config->resources->db);
            $db->query("SET NAMES utf8;");
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }else if($company_id=='2'){//ONEPLUS
            $db = Zend_Db::factory($config->resources->dboneplus);
            $db->query("SET NAMES utf8;");
            Zend_Db_Table::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);
        }
        $setdb = Zend_Registry::get('db');
        return $setdb;
    }

    public function get_ep_pd($company_id)
    {

        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

        $select='select c.company_id,c.company_name,p.*,IF(p.cat_id=11,IF(p.discount_type = 0,"",CONCAT((IF(p.good_id IS NOT NULL,(SELECT CONCAT("(",g.name," : ",g.desc,")") FROM warehouse.good g WHERE g.id=p.good_id),"")))),IF(p.discount_type = 0,"",""))AS discount_type_name  
        from ep_privileges_discount p 
        left join warehouse.good_category ct on ct.id=p.cat_id
        left join company c on c.company_id=p.company_id
        where p.active=1;';   

        return $db->fetchAll($select);
    }

    public function get_ep_pd_bk()
    {

        $db = Zend_Registry::get('db');

        $select='select c.company_id,c.company_name,p.*,IF(p.cat_id=11,IF(p.discount_type = 0,"",CONCAT((IF(p.good_id IS NOT NULL,(SELECT CONCAT("(",g.name," : ",g.desc,")") FROM good g WHERE g.id=p.good_id),"")))),IF(p.discount_type = 0,"",""))AS discount_type_name  
        from ep_privileges_discount p 
        left join good_category ct on ct.id=p.cat_id
        left join company c on c.company_id=p.company_id
        where p.active=1 order by p.create_date desc';   

         //echo $select;//die;
         $res = null;
         $discount = $db->fetchAll($select);
         $row=0;
         //print_r($discount);
         //die; distributor_id
         foreach ($discount as $v)
         {

            if($v['company_id']=='2')
            {
                $res[$row] = $v;
                if($v['good_id'] !="")
                {
                    $product = $this->get_product($v['company_id'],$v['good_id']);
                    $res[$row]['discount_type_name'] = $product['good_full_name'];
                }
                if($v['bank_id'] !="")
                {
                    $bank = $this->get_StaffBankAction($v['company_id'],$v['bank_id']);
                    $res[$row]['bank_name'] = $bank['name'];
                }

                if($v['distributor_id'] !="")
                {
                    $bank = $this->get_StaffDistributorAction($v['company_id'],$v['distributor_id']);
                    $res[$row]['distributor_name'] = $bank['distributor_name'];
                }
                
            }else{
                $res[$row] = $v;
                if($v['good_id'] !="")
                {
                    $product = $this->get_product($v['company_id'],$v['good_id']);
                    $res[$row]['discount_type_name'] = $product['good_full_name'];
                }
                if($v['bank_id'] !="")
                {
                    $bank = $this->get_StaffBankAction($v['company_id'],$v['bank_id']);
                    $res[$row]['bank_name'] = $bank['name'];
                }

                if($v['distributor_id'] !="")
                {
                    $bank = $this->get_StaffDistributorAction($v['company_id'],$v['distributor_id']);
                    $res[$row]['distributor_name'] = $bank['distributor_name'];
                }
            }
            
            $row +=1;
         }
         //print_r($res);//die;
         return $res;

        //return $db->fetchAll($select);
    }

    public function get_product($company_id,$good_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('g' => 'good'), array('g.id as good_id','g.name','g.desc','CONCAT(g.name," [",g.desc,"]") as good_full_name'));
        $select->where('g.id = ?',$good_id);
        //echo $select;die;
        $res = $db->fetchRow($select);
        return $res;
    }

    public function get_product_color($company_id,$good_id,$good_color_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $select = "SELECT gct.name AS category,g.name AS product_name,gc.name AS product_color,CONCAT(g.name,'_',gc.name) AS short_name
        ,CONCAT(g.name,'[',g.desc,']') AS good_full_name
        ,g.id AS good_id,gc.id AS good_color_id
        FROM good g
        LEFT JOIN good_category gct ON gct.id = g.cat_id
        LEFT JOIN good_color_combined gcc ON gcc.good_id = g.id
        LEFT JOIN good_color gc ON gc.id = gcc.good_color_id
        WHERE 1=1 
        AND g.del=0 
        and g.id=".$good_id." 
        and gc.id=".$good_color_id." 
        ORDER BY g.cat_id,g.id,gc.id";
        $res = $db->fetchRow($select);
        return $res;
    }


    public function get_StaffBankAction($company_id,$bank_id)
    {
        //echo $company_id;die;
        $this->_initConfig($company_id);
       
        $QBank = new Application_Model_Bank();
        $where = array();
        $where = $QBank->getAdapter()->quoteInto('id = ?', $bank_id);
        $res = $QBank->fetchRow($where);
        return $res;
    }


    public function get_StaffDistributorAction($company_id,$distributor_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        // echo $warehouse_type_id;die;   
        
        $sql="SELECT p.*,CONCAT(p.`store_code`,' ',p.`title`) AS distributor_name FROM  distributor p where p.id ='".$distributor_id."'";
        //echo $sql;die;
        $result = $db->fetchRow($sql);
        return $result;
    }



}

