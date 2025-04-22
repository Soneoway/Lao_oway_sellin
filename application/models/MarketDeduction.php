<?php
class Application_Model_MarketDeduction extends Zend_Db_Table_Abstract
{
    protected $_name = 'market_deduction';

    public function getPrice($params)
    {
        $db = Zend_Registry::get('db');

        if (!isset($params['d_id']) || !$params['d_id']) return null;
        ////////////////////////////////////////////////////////////

        $select_distributor = $db->select()->from(array('p' => 'deduction_sales_sn'),
            array('total' => 'SUM(p.total_discount)','total_vat' => 'SUM(p.vat)', 'deduction_distributor_id', 'joint_circular_id'))
            ->where("p.status = ?", 1) // approved
            ->where("p.d_id = ?", $params['d_id'])
            ->group('p.joint_circular_id');

        $select_market = $db->select()->from(array('p' => 'market_deduction'), array('total' => 'SUM(p.price)', 'joint_circular_id'))
            ->where($db->quoteInto('d_id = ?', $params['d_id']))
            ->where($db->quoteInto("add_time IS NOT NULL", null))
            ->where('payment_confirmed_at IS NOT NULL', null)
            ->group('p.joint_circular_id');

        $select_diamond = $db->select()
            ->from(array('p' => 'incentive_distributor'), array('total' => 'SUM(p.amount)', 'joint_id'))
            ->where("p.distributor_id = ?", $params['d_id'])
            ->group('p.joint_id');

        $price_result           = array();
        $result_distributor_arr = array();
        $result_market_arr      = array();
        $result_diamond_arr     = array();

        $result_distributor = $db->fetchAll($select_distributor);
        $result_market      = $db->fetchAll($select_market);
        $result_diamond     = $db->fetchAll($select_diamond);

        // gộp số tiền tổng/đã xài theo joint_id
        foreach ($result_distributor as $key => $value)
            $result_distributor_arr[ $value['joint_circular_id'] ] = $value['total'];

        foreach ($result_market as $key => $value)
            $result_market_arr[ $value['joint_circular_id'] ] = $value['total'];

        foreach ($result_diamond as $key => $value)
            $result_diamond_arr[ $value['joint_id'] ] = $value['total'];

        // tính khoản thưởng/chiết khấu còn lại
        // bằng cách lấy tổng trừ đi khoản đã xài
        foreach ($result_diamond_arr as $key => $value)
            $price_result[ $key ] = $value - (isset($result_market_arr[ $key ]) ? $result_market_arr[ $key ] : 0);

        foreach ($result_distributor_arr as $key => $value)
            $price_result[ $key ] = $value - (isset($result_market_arr[ $key ]) ? $result_market_arr[ $key ] : 0);



        // kết cmn quả
        return $price_result;
    }

    public function getMaxPrice($params)
    {
        try{


        $db = Zend_Registry::get('db');

        if (!isset($params['d_id']) || !$params['d_id']) return null;
        ////////////////////////////////////////////////////////////

        $select_distributor = $db->select()->from(array('p' => 'deduction_sales_sn'),
            array('total' => 'SUM(p.total_discount)','total_vat' => 'SUM(p.vat)', 'deduction_distributor_id', 'joint_circular_id'))
            ->where("p.status = ?", 1) // approved
            ->where("p.d_id = ?", $params['d_id'])
            ->where("p.joint_circular_id = ?", $params['joint_circular_id'])
            ->group('p.joint_circular_id');

        $select_market = $db->select()->from(array('p' => 'market_deduction'), array('total' => 'SUM(p.price)', 'joint_circular_id'))
            ->where($db->quoteInto('d_id = ?', $params['d_id']))
            ->where($db->quoteInto("add_time IS NOT NULL", null))
            ->where('payment_confirmed_at IS NOT NULL', null)
            ->where("p.joint_circular_id = ?", $params['joint_circular_id'])
            ->group('p.joint_circular_id');

        $select_diamond = $db->select()
            ->from(array('p' => 'incentive_distributor'), array('total' => 'SUM(p.amount)', 'joint_id'))
            ->where("p.distributor_id = ?", $params['d_id'])
            ->where("p.joint_id = ?", $params['joint_circular_id'])
            ->group('p.joint_id');


        $price_result           = 0;

        $discount_distributor = $db->fetchRow($select_distributor);
        $discount_market      = $db->fetchRow($select_market);
        $discount_diamond     = $db->fetchRow($select_diamond);
        $discount = $discount_market['total'] ? $discount_market['total'] : 0;

        if(isset($params['type']) and $params['type'])
        {
            switch($params['type']) {

                case DISCOUNT_TYPE_CK:
                {
                    $price_result = $discount_distributor['total'] - $discount;
                }
                    break;
                case DISCOUNT_TYPE_DIAMOND:
                {
                    $price_result = $discount_diamond['total'] - $discount_market['total'];
                }
                    break;
            }
        }

        return $price_result >= 0 ? $price_result : 0;
    }
        catch(exception $e)
        {
            return false;
        }
    }

}
