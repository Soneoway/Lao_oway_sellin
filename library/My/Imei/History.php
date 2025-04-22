<?php
class My_Imei_History extends My_Type_Enum
{
    /*===========================*/
    /* IMEI History
    /*===========================*/
    const IMPORTED          = 1;
    const CHANGED_OUT       = 1;
    const CHANGED_IN        = 1;
    const EXPORTED          = 1;
    const REPORTED_SELL_OUT = 1;
    const RETURNED          = 1;
    const WARRANTY          = 1;
    const ACTIVE            = 1;
    const REBATE            = 1;
    const RETURN_FROM_WARRANTY = 1;
    const MARKED_SELL_IN    = 1;
    const MARKED_SELL_OUT   = 1;

    public static function setHistory($imei, $action, $object_type, $object_id, array $info)
    {
        $rs = array('code' => 0);
        try {
            $QImeiHistory       = new Application_Model_ImeiHistory();
            $QImeiHistoryDetail = new Application_Model_ImeiHistoryDetail();
            $currentTime = time();

            $data = array(
                'imei'          => $imei,
                'action'        => $action,
                'object_type'   => $object_type,
                'object_id'     => $object_id,
                'created_at'    => $currentTime,
            );

            $id = $QImeiHistory->insert($data);

            foreach ($info as $key => $value) {

                $data = array(
                    'imei_history_id' => $id,
                    'type_id'        => $key,
                    'value'          => $value,
                );

                $QImeiHistoryDetail->insert($data);
            }
        } catch (Exception $e){
            $rs = array('code' => 1, 'message' => $e->getMessage());

        }

        return $rs;
    }

}
