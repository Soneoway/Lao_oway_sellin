<?php
class Application_Model_MobilizationNumber extends Zend_Db_Table_Abstract{
	protected $_name = 'mobilization_number';

    public function getLastId($from_warehouse)
    {
        try{


                if(!isset($from_warehouse))
                {
                    throw new exception("Invalid Showroom !Please try again.");
                }
                $db = Zend_Registry::get('db');

                $select = $db->select()
                    ->from("mobilization_number", array(new Zend_Db_Expr("MAX(number) AS maxID")))
                    ->where("service_id = ?" , intval($from_warehouse['id']))
                ;

                $result = $db->fetchOne($select);
                $pattern = '';

                if(empty($result))
                    $result = 0;

                $number = sprintf('%04d', intval($result+1));
                $date = date('Y');
                $pattern =  $number .'/'. $date  .'/'. 'OPPO' .'/'. $from_warehouse['short'];
                return $pattern;

            }
        catch(exception $e)
        {
            echo ($e->getMessage());exit;
            $result = 0;
        }

        return $result;



    }

    public function fixed()
    {
        $db = Zend_Registry::get('db');
        $select = $db->select()->from("invoice_number", array(new Zend_Db_Expr("MAX(invoice_number) AS maxID")));
        $data = $db->fetchOne($select);
        $query = 'ALTER TABLE invoice_number auto_increment = ' . intval($data + 1);
        $result = $db->query($query);
        return $result;
    }
}