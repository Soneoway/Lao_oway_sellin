<?php
/**
* @author buu.pham
*/
class My_Imei_Status extends My_Type_Enum
{
    /*===========================*/
    /* IMEI Status
    /*===========================*/
    const Warehouse      = 1;
    const Scanned_Out    = 2;
    const On_The_Way     = 3;
    const Distributor    = 4;
    const Sold_Out       = 5;
    const Warranty       = 6;
    const On_Change_Sale = 7;
    const Changed_Sale   = 8;
    const Returned       = 9;

    /**
     * Lưu thông tin trạng thái IMEI
     * @param integer $imei   IMEI SN
     * @param integer $status Trạng thái IMEI (danh sách const ở trên)
     * @param array  $info   Thông tin bổ sung
     */
    public static function setStatus($imei, $status, array $info)
    {
        $QImeiStatus = new Application_Model_ImeiStatus();
        $QImeiStatusInfo = new Application_Model_ImeiStatusInfo();
        $date = time();

        $data = array(
            'imei'           => $imei,
            'status'         => $status,
            'from_date'      => date('Y-m-d H:i:s', $date),
            'from_date_unix' => $date,
        );

        $id = $QImeiStatus->insert($data);

        foreach ($info as $key => $value) {

            $data = array(
                'imei_status_id' => $id,
                'type_id'        => $key,
                'value'          => $value,
            );

            $QImeiStatusInfo->insert($data);
        }

    }

    public static function getStatus($imei, $date)
    {
        $QImeiStatus = new Application_Model_ImeiStatus();
        return $QImeiStatus->getStatus($imei, $date);
    }

    public static function getStatusInfo($imei, $date, $status)
    {
        $QImeiStatus = new Application_Model_ImeiStatus();
        return $QImeiStatus->getStatus($imei, $date, $status);
    }
}
