<?php 
/**
* 
*/
class My_Delivery_Order_Status extends My_Type_Enum
{
    const Waiting                  = 1;
    const Warehouse_To_Distributor = 2;
    const Warehouse_To_Hub         = 3;
    const Hub_To_Distributor       = 4;
    const Delivered                = 5;
    const Deleted                  = 6;

    public static $name = array(
        self::Waiting                  => 'Waiting',
        self::Warehouse_To_Distributor => 'Warehouse To Distributor',
        self::Warehouse_To_Hub         => 'Warehouse To Hub',
        self::Hub_To_Distributor       => 'Hub To Distributor',
        self::Delivered                => 'Delivered',
        // self::Deleted                  => 'Deleted',
    );

    public static function setStatus($delivery_order_id, $status, $user_id)
    {
        $date = time();
        $QDeliveryOrderStatus = new Application_Model_DeliveryOrderStatus();

        $data = array(
            'delivery_order_id' => intval($delivery_order_id),
            'status' => intval($status),
            'updated_at' => date('Y-m-d H:i:s', $date),
            'updated_at_unix' => $date,
            'updated_by' => intval($user_id),
        );

        $QDeliveryOrderStatus->insert($data);
    }

    public static function getStatus($delivery_order_id, $date)
    {
        $QDeliveryOrderStatus = new Application_Model_DeliveryOrderStatus();
        return $QDeliveryOrderStatus->getStatus($delivery_order_id, $date);
    }
}

