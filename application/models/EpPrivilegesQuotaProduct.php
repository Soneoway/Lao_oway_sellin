<?php
class Application_Model_EpPrivilegesQuotaProduct extends Zend_Db_Table_Abstract
{
	protected $_name = 'ep_privileges_quota_product';

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

    public function get_ep_pqp($company_id)
    {

        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('p' => $this->_name), array('*','(SELECT sum(qty) as sum_qty FROM ep_privileges_tran_order epto left join ep_privileges_tran_order_item eptoi on epto.privileges_sn = eptoi.privileges_sn 
                where epto.status = 1 and if(p.eol = 1,epto.payment_type = 3,epto.payment_type <> 3) 
                and epto.warehouse_id = p.warehouse_id and eptoi.good_id = p.good_id and eptoi.good_color = p.good_color and eptoi.cat_id = p.cat_id) as order_pending'));
            $select->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('CONCAT(g.name," [",g.desc,"]") as good_full_name'));
            $select->joinLeft(array('c' => 'company'), 'c.company_id=p.company_id', array('c.company_id','c.company_name'));

            $select->where('p.active = ?',1);

         //echo $select;die;

         //return $db->fetchAll($select);

         $res = null;
         $tran_order = $db->fetchAll($select);
         $row=0;
         foreach ($tran_order as $v)
         {
            if($v['company_id']=='2')
            {
                $res[$row] = $v;
                $product = $this->get_product_color($v['company_id'],$v['good_id'],$v['good_color']);
                $res[$row]['good_color_name'] = $product['product_color'];
            }else{
                $res[$row] = $v;
                $product = $this->get_product_color('1',$v['good_id'],$v['good_color']);
                $res[$row]['good_color_name'] = $product['product_color'];
            }
            
            $row +=1;
         }
         //print_r($res);//die;
         return $res;

    }

    public function get_ep_pqp_bk($company_id)
    {

        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name), array('*','(SELECT sum(qty) as sum_qty 
                FROM ep_privileges_tran_order epto 
                left join ep_privileges_tran_order_item eptoi on epto.privileges_sn = eptoi.privileges_sn where epto.status = 1 and if(p.eol = 1,epto.payment_type = 3,epto.payment_type <> 3) and epto.warehouse_id = p.warehouse_id and eptoi.good_id = p.good_id and eptoi.good_color = p.good_color and eptoi.cat_id = p.cat_id) as order_pending'))
            ->joinLeft(array('g' => 'good'), 'g.id=p.good_id', array('CONCAT(g.name," [",g.desc,"]") as good_full_name'));


        $select->where('p.active = ?',1);
        //$select->where('p.company_id = ?',2);
        $select->order('p.create_date desc');
        //$select->limit(0,3);

         //echo $select;die;
         $res = null;
         $tran_order = $db->fetchAll($select);
         $row=0;
         foreach ($tran_order as $v)
         {
            if($v['company_id']=='2')
            {
                $res[$row] = $v;
                $product = $this->get_product_color($v['company_id'],$v['good_id'],$v['good_color']);
                $res[$row]['good_full_name'] = $product['good_full_name'];
                $res[$row]['good_color_name'] = $product['product_color'];
                $res[$row]['company_name'] = "ONEPLUS";
            }else{
                $res[$row] = $v;
                $product = $this->get_product_color('1',$v['good_id'],$v['good_color']);
                $res[$row]['good_color_name'] = $product['product_color'];
                $res[$row]['company_name'] = "OPPO";
            }
            
            $row +=1;
         }
         //print_r($res);//die;
         return $res;
    }


    public function get_product($company_id,$good_id)
    {
        $this->_initConfig($company_id);
        $db = Zend_Registry::get('db');
        $select = $db->select()
            ->from(array('g' => 'good'), array('g.id as good_id','g.name','g.desc','CONCAT(g.name," [",g.desc,"]") as good_full_name'));
        $select->where('g.id = ?',$good_id);
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
}
