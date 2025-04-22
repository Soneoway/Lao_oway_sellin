<?php
define('HOST', 'https://sellin.oway-la.com/');
define('WSS_WH_URI', HOST.'wss');
define('CENTER_DB', 'hr');
define('HR_DB', 'hr');
define('TRADE_URI' , '');
define('HOST_ONEPLUS', '');
//DEV
// define('API_IOPPO_URL' , 'http://api-training.ioppo.oppo.in.th/');

//PRO
define('API_IOPPO_URL' , '');
define('API_IOPPO_STAFF_URL' , '');

//-- New Version --
define('KERRY_EDI_SHIPMENTINFO_URL' , '');
define('KERRY_EDI_PACKAGEDETAILS_URL' , '');
define('KERRY_API_UPDATESTATUS' , '');
//-- New Version --


// Pro J&T DEV
define('JNT_API_URL' , '');
define('JNT_ECCOMPANYID' , '');
define('JNT_CUSTOMERID' , '');
define('JNT_KEY' , '');
define('JNT_API_UPDATESTATUS' , '');

define('ENCRYPT_KET' , 'MosTest');
define('ENCRYPT_TOKEN_LIFETIME' , 30);

define('VERSION', "3.2");
define('LIMITATION', 10);
define('SUPERADMIN_ID', 107);
define('TAX_OPPO', '0745552000866');

define('ADMINISTRATOR_ID', 7);
define('HUMAN_RH_ID', 119);
define('MANAGER', 1);
define('WAREHOUSE', 2);
define('SALES_ADMIN_REGIONAL', 3);
define('FINANCE', 4);
define('WAREHOUSE_LEADER', 13);
define('SUPER_SALES_ADMIN', 14);
define('SALES_ADMIN', 3);
define('CHECK_MONEY', 10);
define('CANCEL_ORDER', 27);
define('SALES_ADMIN_ACCESSORIES', 23);
define('OPPO_BRAND_SHOP', 25);
define('OPPO_BRAND_SHOP_SERVICE', 28);
define('RETURN_NO_CN', 29);
define('KERRY_STAFF', 26);
define('KERRY_LEADER', 33);
define('FINANCE_UPDATE_PAYMENT', 32);
define('PO_SHOW_PRICE',34);
define('SPLIT_GIFBOX',51);
define('EDIT_PRICE_PRODUCT',53);
define('SALES_ADMIN_CONFIRM_PRICE_PROTECTION',56);
define('FINANCE_CONFIRM_PRICE_PROTECTION',57);
define('SEARCH_UNLIMITED_DATA', 86);
define('ROLLBACK_ORDER', 108);


define('OPPO_SERVICE_CLUB' , 8059);
define('OPPO_BORROW' , 8840);
define('OPPO_STAFF' , 8836);
define('OPPO_GIFT', 8838);
define('OPPO_EVENT', 2115);
define('OPPO_DEMO',9047);
define('OPPO_KVL' , 9196);
define('OPPO_SUPER_DAY' , 9540);
define('OPPO_PARTNER' , 12377);
define('OPPO_LAI_NGAN_HANG' , 10654);
define('OPPO_ONLINE_STORE',147);

define('OPPO_SERVICE_HCM',2123);
define('OPPO_SERVICE_HANOI',2124);
define('OPPO_SERVICE_CANTHO',2125);
define('OPPO_SERVICE_DANANG',2126);
define('OPPO_SERVICE_HAIPHONG',2127);
define('OPPO_SERVICE_DONGNAI',8805);
define('OPPO_SERVICE_BINHDUONG',8884);
define('OPPO_SERVICE_LIST',serialize(array(
    OPPO_SERVICE_CLUB,
    OPPO_SERVICE_HCM,
    OPPO_SERVICE_HANOI,
    OPPO_SERVICE_CANTHO,
    OPPO_SERVICE_DANANG,
    OPPO_SERVICE_HAIPHONG,
    OPPO_SERVICE_DONGNAI,
    OPPO_SERVICE_BINHDUONG
)));

define('PHONE_CAT_ID'   , 11);
define('ACCESS_CAT_ID'  , 12);
define('DIGITAL_CAT_ID' , 13);
define('ILIKE_CAT_ID'   , 14);
define('IOT_CAT_ID'  , 15);

define('FOR_RETAILER'   , 1);
define('FOR_DEMO'       , 5); // 2
define('FOR_STAFFS'     , 3);
define('FOR_LENDING'    , 4);
// define('FOR_APK'        , 5);

#time life of order IN second(s)
define('ORDER_TIMELIFE', 3*60*60);

define('TAG_ORDER', 1);


define('BALANCE_LIMIT', -300000);

define('TARGET_FROM', '2015-01-01');
define('TARGET_TO', '2015-01-31');
define('PRICE', '3200000');

define('MONEY_TYPE',serialize (array (
    '1' => 'IN',
    '2' => 'OUT',
    '3' => 'BANK TRANSACTION FEE',
    '4' => 'ASSESSMENT',
    '5' => 'CREDIT NOTE',
    '6' => 'DELIVERY FEE',
    '7' => 'BANKTRANSFER FEE',
    '8' => 'SERVICECHARGE FEE',
    '9' => 'SERVICE FEE',
    '10' => 'DISCOUNT',
    '11' => 'เงินขาด (เกิน)',
    '11' => 'SURPLUS (Used)',
    '12' => 'DEPOSIT',
    '13' => 'IN',
    '14' => 'SURPLUS (Unuse)',
    '15' => 'Deficit'
)));

//// 0: t?o v� edit b�nh thu?ng
//// 1: KH�NG cho t?o  - CHO edit
define('NO_CREATE_ORDER', 0);

//// 0: t?o v� edit b�nh thu?ng
//// 1: KH�NG cho t?o  - KH�NG cho edit
define('NO_CREATE_NO_EDIT_ORDER', 0);

// CHANGE ORDER STATUS //
define('CHANGE_ORDER_STATUS_PENDING',                   0);
define('CHANGE_ORDER_STATUS_SCANNED_OUT',               1);
define('CHANGE_ORDER_STATUS_ON_CHANGE',                 2);
define('CHANGE_ORDER_STATUS_SCANNED_IN',                3);
define('CHANGE_ORDER_STATUS_COMPLETED',                 4);
define('CHANGE_ORDER_STATUS_FULL_RECEIVED',             5);
define('CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED',        6);
define('CHANGE_ORDER_STATUS_LOST_RECEIVED',             7);
define('CHANGE_ORDER_STATUS_PENDING_NAME',              'Pending');
define('CHANGE_ORDER_STATUS_SCANNED_OUT_NAME',          'Scanned Out');
define('CHANGE_ORDER_STATUS_ON_CHANGE_NAME',            'On Change');
define('CHANGE_ORDER_STATUS_SCANNED_IN_NAME',           'Scanned In');
define('CHANGE_ORDER_STATUS_FULL_RECEIVED_NAME',        'Full Receive');
define('CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED_NAME',   'Partially Receive');
define('CHANGE_ORDER_STATUS_LOST_RECEIVED_NAME',        'Lost Receive');
define('CHANGE_ORDER_STATUS_COMPLETED_NAME',            'Completed');

define ("CHANGE_ORDER_STATUS", serialize (array (
    CHANGE_ORDER_STATUS_PENDING                          => CHANGE_ORDER_STATUS_PENDING_NAME,
    CHANGE_ORDER_STATUS_SCANNED_OUT                      => CHANGE_ORDER_STATUS_SCANNED_OUT_NAME,
    CHANGE_ORDER_STATUS_ON_CHANGE                        => CHANGE_ORDER_STATUS_ON_CHANGE_NAME,
    CHANGE_ORDER_STATUS_SCANNED_IN                       => CHANGE_ORDER_STATUS_SCANNED_IN_NAME,
    CHANGE_ORDER_STATUS_COMPLETED                        => CHANGE_ORDER_STATUS_COMPLETED_NAME,
    CHANGE_ORDER_STATUS_FULL_RECEIVED                    => CHANGE_ORDER_STATUS_FULL_RECEIVED_NAME,
    CHANGE_ORDER_STATUS_LOST_RECEIVED                    => CHANGE_ORDER_STATUS_LOST_RECEIVED_NAME,
    CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED               => CHANGE_ORDER_STATUS_PARTIALLY_RECEIVED_NAME,
)));
// End of CHANGE ORDER STATUS //

// IMEI STATUS //
define('IMEI_STATUS_OK',                                1);
define('IMEI_STATUS_PROCESSING',                        2);
define('IMEI_STATUS_LOST',                              3);
define('IMEI_STATUS_ON_CHANGE',                         4);

define('IMEI_STATUS_OK_NAME',                           'OK');
define('IMEI_STATUS_PROCESSING_NAME',                   'Processing');
define('IMEI_STATUS_LOST_NAME',                         'Lost');
define('IMEI_STATUS_ON_CHANGE_NAME',                    'On Change');


define ("IMEI_STATUS", serialize (array (
    IMEI_STATUS_OK                                      => IMEI_STATUS_OK_NAME,
    IMEI_STATUS_PROCESSING                              => IMEI_STATUS_PROCESSING_NAME,
    IMEI_STATUS_LOST_NAME                               => IMEI_STATUS_LOST_NAME,
    IMEI_STATUS_ON_CHANGE                               => IMEI_STATUS_ON_CHANGE_NAME,
)));


// End of IMEI STATUS //

// CHANGE_ORDER_IMEI_STATUS //
define('CHANGE_ORDER_IMEI_STATUS_PROCESSING',           1);
define('CHANGE_ORDER_IMEI_STATUS_RECEIVED',             2);
define('CHANGE_ORDER_IMEI_STATUS_LOST',                 3);

define('CHANGE_ORDER_IMEI_STATUS_PROCESSING_NAME',      'Processing');
define('CHANGE_ORDER_IMEI_STATUS_RECEIVED_NAME',        'Received');
define('CHANGE_ORDER_IMEI_STATUS_LOST_NAME',            'Lost');


define ("CHANGE_ORDER_IMEI_STATUS", serialize (array (
    CHANGE_ORDER_IMEI_STATUS_PROCESSING                  => CHANGE_ORDER_IMEI_STATUS_PROCESSING_NAME,
    CHANGE_ORDER_IMEI_STATUS_RECEIVED                    => CHANGE_ORDER_IMEI_STATUS_RECEIVED_NAME,
    CHANGE_ORDER_IMEI_STATUS_LOST                        => CHANGE_ORDER_IMEI_STATUS_LOST_NAME,
)));
// End of CHANGE_ORDER_IMEI_STATUS //

define('INVOICE_DESTROY', '0000000');
define('DISCOUNT_DEDUCTION', '2');
define('DISCOUNT_BVG', '1');
define('DISCOUNT_NONE', '0');

define('DISCOUNT_DEDUCTION_NAME', 'CK');
define('DISCOUNT_BVG_NAME', 'BVG');
define('DISCOUNT_NONE_NAME', '-');

define ("DISCOUNT_STATUS", serialize (array (
    DISCOUNT_DEDUCTION      => DISCOUNT_DEDUCTION_NAME,
    DISCOUNT_BVG            => DISCOUNT_BVG_NAME,
    DISCOUNT_NONE           => DISCOUNT_NONE_NAME,
)));

//log price dealer
define('LOG_PRICE_I_AGENT_PRICE',                                      1);
define('LOG_PRICE_II_AGENT_PRICE',                                     2);
define('LOG_PRICE_III_AGENT_PRICE_FOR_FPT',                            3);
define('LOG_PRICE_IV_AGENT_PRICE_FOR_VIEN_HUNG',                       4);
define('LOG_PRICE_V_AGENT_PRICE_FOR_TGDD_ACCESSORIES',                 5);
define('LOG_PRICE_VI_AGENT_PRICE_FOR_NGUYEN_KIM',                      6);
define('LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA',                       7);
define('LOG_PRICE_VIII_AGENT_PRICE_FOR_CAMPODIA_ACCESSORIES',          8);
define('LOG_PRICE_IX_AGENT_PRICE_FOR_DIEN_MAY_CHO_LON',                9);
define('LOG_PRICE_TGDD_THUONG',                                       10);
define('LOG_PRICE_TGDD_NO',                                           12);
define('LOG_PRICE_VTA',                                               13);
define('LOG_PRICE_VINPRO',                                            14);
define('LOG_PRICE_LAOS',                                              15);
define('LOG_PRICE_RETAILER_PRICE',                                    16);
define('LOG_PRICE_IMPORT_PRICE',                                      17);
define('LOG_PRICE_DEMO',                                              18);
define('LOG_PRICE_I_AGENT_PRICE_NAME',                               'I Agent Price');
define('LOG_PRICE_II_AGENT_PRICE_NAME',                              'II Agent Price');
define('LOG_PRICE_III_AGENT_PRICE_FOR_FPT_NAME',                     'III Agent Price');
define('LOG_PRICE_IV_AGENT_PRICE_FOR_VIEN_HUNG_NAME',                'Deal Smartphone - Viễn Hung');
define('LOG_PRICE_V_AGENT_PRICE_FOR_TGDD_ACCESSORIES_NAME',          'TGDD - Accessories');
define('LOG_PRICE_VI_AGENT_PRICE_FOR_NGUYEN_KIM_NAME',               'Nguyen Kim');
define('LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA_NAME',                'Campodia');
define('LOG_PRICE_VIII_AGENT_PRICE_FOR_CAMPODIA_ACCESSORIES_NAME',   'Campodia Accessories');
define('LOG_PRICE_IX_AGENT_PRICE_FOR_DIEN_MAY_CHO_LON_NAME',         'Dien may cho lon');
define('LOG_PRICE_TGDD_THUONG_NAME',                                 'TGDÐ Thường');
define('LOG_PRICE_TGDD_NO_NAME',                                     'TGDÐ Nợ');
define('LOG_PRICE_VTA_NAME',                                         'Viễn Thông A');
define('LOG_PRICE_VINPRO_NAME',                                      'Vinpro');
define('LOG_PRICE_LAOS_NAME',                                        'Lào');
define('LOG_PRICE_RETAILER_PRICE_NAME',                              'Retailer');
define('LOG_PRICE_IMPORT_PRICE_NAME',                                'Import price');
define('LOG_PRICE_DEMO_NAME',                                        'DEMO');

define ("LOG_PRICE", serialize (array (
    LOG_PRICE_I_AGENT_PRICE                              => LOG_PRICE_I_AGENT_PRICE_NAME,
    LOG_PRICE_II_AGENT_PRICE                             => LOG_PRICE_II_AGENT_PRICE_NAME,
    LOG_PRICE_III_AGENT_PRICE_FOR_FPT                    => LOG_PRICE_III_AGENT_PRICE_FOR_FPT_NAME,
    LOG_PRICE_IV_AGENT_PRICE_FOR_VIEN_HUNG               => LOG_PRICE_IV_AGENT_PRICE_FOR_VIEN_HUNG_NAME,
    LOG_PRICE_V_AGENT_PRICE_FOR_TGDD_ACCESSORIES         => LOG_PRICE_V_AGENT_PRICE_FOR_TGDD_ACCESSORIES_NAME,
    LOG_PRICE_VI_AGENT_PRICE_FOR_NGUYEN_KIM              => LOG_PRICE_VI_AGENT_PRICE_FOR_NGUYEN_KIM_NAME,
    LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA               => LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA_NAME,
    LOG_PRICE_VIII_AGENT_PRICE_FOR_CAMPODIA_ACCESSORIES  => LOG_PRICE_VIII_AGENT_PRICE_FOR_CAMPODIA_ACCESSORIES_NAME,
    LOG_PRICE_IX_AGENT_PRICE_FOR_DIEN_MAY_CHO_LON        => LOG_PRICE_IX_AGENT_PRICE_FOR_DIEN_MAY_CHO_LON_NAME,
    LOG_PRICE_TGDD_THUONG                                => LOG_PRICE_TGDD_THUONG_NAME,
    LOG_PRICE_TGDD_NO                                    => LOG_PRICE_TGDD_NO_NAME,
    LOG_PRICE_VTA                                        => LOG_PRICE_VTA_NAME,
    LOG_PRICE_LAOS                                       => LOG_PRICE_LAOS_NAME,
    LOG_PRICE_RETAILER_PRICE                             => LOG_PRICE_RETAILER_PRICE_NAME,
    LOG_PRICE_IMPORT_PRICE                               => LOG_PRICE_IMPORT_PRICE_NAME,
    LOG_PRICE_DEMO                                       => LOG_PRICE_DEMO_NAME
)));
define('DISCOUNT_DIAMOND_CLUB',                                14);
define('DISCOUNT_DIAMOND_CLUB_5',                                26);
define('DISCOUNT_DIAMOND_CLUB_6', 45);
define('DISCOUNT_DIAMOND_CLUB_7', 83);
define('DISCOUNT_DIAMOND_CLUB_8', 92);
define('DISCOUNT_CK',                                8);
define('DISCOUNT_CK_II' , 46);
define('DISCOUNT_CK_III' , 125);
define('INVOICE_OPPO_SIGN' , 10);
define('DISCOUNT_TYPE_CK' , 2);
define('DISCOUNT_TYPE_DIAMOND', 4);
define('COMPANY_OPPO',1);
//define('COMPANY_SMARTMOILE',2);
define('COMPANIES',serialize(array(
        COMPANY_OPPO => 'OPPO'
       // COMPANY_SMARTMOILE => 'SMARTMOBILE'
    )));
define('KA_NGUYEN_KIM' , 7483);
define('KA_VINPRO' , 9187);
define('KA_VIETTEL' , 10007);
define('KA_TGDD' , 2316);
define('KA_VTA' , 2317);
define('KA_FPT' , 2363);

define('BVG_SUPPORT_SELL_OUT' , 6);
define('TIME_LIMIT_ORDER' , '16:30:00');
define('FILENAME_SALT', 'Th0ng!@#' );
define('JOINT_TYPE_BVG' , 1);
define('JOINT_TYPE_BVG_ACCESSORIES' , 5);
define('JOINT_TYPE_DISCOUNT' ,2);
define('JOINT_TYPE_INCENTIVE' ,4);

define('INTERNAL_NUMBER_FOR_WAREHOUSE' , 1);
define('INTERNAL_NUMBER_FOR_SERVICE' , 2);
define('INTERNAL_NUMBER_FOR_SERVICE_INVOICE' , 3);
define('INTERNAL_TYPE' , serialize(array(INTERNAL_NUMBER_FOR_WAREHOUSE => 'Warehouseme' , INTERNAL_NUMBER_FOR_SERVICE => 'Service' , INTERNAL_NUMBER_FOR_SERVICE_INVOICE => 'Service Invoice')));

define('INVOICE_PREFIX_TYPE_INVOICE' , 1);
define('INVOICE_PREFIX_TYPE_TRANSPORT' , 2);

define('STAFF_BUY_40',730);

define('ORDER_FOR_RETAILER',1);
define('ORDER_FOR_DEMO',5); // 2
define('ORDER_FOR_STAFF',3);
define('ORDER_FOR_LENDING',4);
// define('ORDER_FOR_APK',5);

define('ORDER_FOR_RETAILER_NAME','FOR RETAILER');
define('ORDER_FOR_DEMO_NAME','FOR DEMO');
define('ORDER_FOR_STAFF_NAME','FOR STAFF');
define('ORDER_FOR_LENDING_NAME','FOR LENDING');
define('ORDER_FOR_APK_NAME','FOR APK');
define('ORDER_TYPE',serialize(array(
            ORDER_FOR_RETAILER => ORDER_FOR_RETAILER_NAME,
            //ORDER_FOR_DEMO     => ORDER_FOR_DEMO_NAME,
            ORDER_FOR_STAFF    => ORDER_FOR_STAFF_NAME,
            ORDER_FOR_LENDING  => ORDER_FOR_LENDING_NAME,
            ORDER_FOR_DEMO      => ORDER_FOR_DEMO_NAME

    )));


/* NHÂN VIÊN MUA MÁY */
define('OPPO_ORDER_TYPE_STAFF' , 3);
define('OPPO_INGAME' , 8236);
define('OPPO_SALE_NVMM1' , 1);

define('OPPO_SALE_NVMM2' , 2128);

define('STAFF_ORDER_TYPE',serialize(
        array(
            1 => '40%',
            2 => '15%',
            3 => 'Thanh lý',
            4 => 'Bình thường',
            5 => '30%',
        )
    ));

//danh sách dealer không cần kiểm tra công nợ
define('IGNORE_AUDIT',serialize(
    array_merge(array(
        OPPO_BORROW,
        OPPO_LAI_NGAN_HANG,
        OPPO_ONLINE_STORE
    ),unserialize(OPPO_SERVICE_LIST))
));


