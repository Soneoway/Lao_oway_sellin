<?php
/**
 * @author buu.pham
 * AJAX function
 * Tách đơn hàng thành 2 đơn nhỏ - (giữ một phần đơn gốc + tạo 1 đơn mới)
 */

$this->_helper->layout->disableLayout();
$this->_helper->viewRenderer->setNoRender(true);
$this->getResponse()->setHeader('Content-Type', 'application/json');

$market_ids       = $this->getRequest()->getParam('market_ids');
$imei_ids         = $this->getRequest()->getParam('imei_ids');
$distributor_code = $this->getRequest()->getParam('distributor_code');

try {
    // VALIDATAION
    if (!$market_ids || !is_array($market_ids) || !count($market_ids)) throw new Exception("Invalid Order IDs", 2);
    if (!$imei_ids || !is_array($imei_ids) || !count($imei_ids)) throw new Exception("Invalid IMEI IDs", 5);

    $QMarket    = new Application_Model_Market();
    $QImei      = new Application_Model_Imei();
    $QTagObject = new Application_Model_TagObject();
    $model      = array();
    $markets    = array();
    $imei_list = array();

    // kiểm tra các IMEI có thuộc đơn hàng tương ứng không
    foreach ($imei_ids as $_key => $_imei_id) {
        if (!isset( $market_ids[ $_key ] )) throw new Exception("Wrong data", 8);

        $where = $QImei->getAdapter()->quoteInto('id = ?', $_imei_id);
        $imei_check = $QImei->fetchRow($where);

        if (!$imei_check) throw new Exception("Invalid IMEI", 7);
        if ($imei_check['sales_id'] != $market_ids[ $_key ]) throw new Exception("Wrong data", 9);

        $where = $QMarket->getAdapter()->quoteInto('id = ?', $market_ids[ $_key ]);
        $market_check = $QMarket->fetchRow($where);

        if (!$market_check) throw new Exception("Invalid Order", 10);

        $markets[ $market_check['id'] ] = $market_check;
        $imei_list[ $market_check['id'] ][] = $_imei_id;

        // đếm số model của các IMEI
        if (!isset($model[ $imei_check['good_id'] ][ $imei_check['good_color'] ]))
            $model[ $imei_check['good_id'] ][ $imei_check['good_color'] ] = 0;

        $model[ $imei_check['good_id'] ][ $imei_check['good_color'] ]++;
    }

    // nếu có mã KH thì kiểm tra thông tin KH
    if (!empty($distributor_code)) {
        $QDistributor = new Application_Model_Distributor();
        $where = $QDistributor->getAdapter()->quoteInto('store_code = ?', $distributor_code);
        $distributor_check = $QDistributor->fetchRow($where);

        if (!$distributor_check) throw new Exception("Invalid store code", 11);
        $new_distributor_id = $distributor_check['id'];
    } else {
        $new_distributor_id = false;
    }

    // ACTION
    $new_sn = My_Sale_Order::generateSn();

    $db = Zend_Registry::get('db');
    $db->beginTransaction();

    // kiểm tra số IMEI với số num trong bảng market
    foreach ($markets as $_market_id => $_market) {
        if ($_market['num'] < $model[ $_market['good_id'] ][ $_market['good_color'] ])
            throw new Exception("The number of IMEIs less than in the Order. Check again", 12);

        // cập nhật số lượng và total cho market (order) cũ
        $where = $QMarket->getAdapter()->quoteInto('id = ?', $_market_id);
        $new_num = $_market['num'] - $model[ $_market['good_id'] ][ $_market['good_color'] ];

        if ($new_num > 0) { // trường hợp cắt đơn xong dòng đơn cũ còn lớn hơn 1 thì giữ
            $data = array(
                'num'   => $new_num,
                'total' => $new_num*$_market['price'],
            );

            $QMarket->update($data, $where);

            // chèn thêm dòng market mới
            $new_data = $_market->toArray();

            unset($new_data['id']); // bỏ cái id vì chèn mới tự tăng
            $new_data['sn'] = $new_sn;
            $new_data['num'] = $model[ $_market['good_id'] ][ $_market['good_color'] ];
            $new_data['total'] = $model[ $_market['good_id'] ][ $_market['good_color'] ] * $new_data['price'];

            if (isset($new_distributor_id) && $new_distributor_id)
                $new_data['d_id'] = $new_distributor_id;

            $market_id = $QMarket->insert($new_data);

        } else { // không thì update sn mới cmnl
            $data = array('sn' => $new_sn);

            if (isset($new_distributor_id) && $new_distributor_id)
                $data['d_id'] = $new_distributor_id;

            $QMarket->update($data, $where);
            $market_id = $_market['id'];
        }

        // cập nhật sales_sn, sales_id cho imei
        $where = $QImei->getAdapter()->quoteInto('id IN (?)', $imei_list[ $_market_id ]);
        $data = array('sales_sn' => $new_sn, 'sales_id' => $market_id);

        if (isset($new_distributor_id) && $new_distributor_id)
                $data['distributor_id'] = $new_distributor_id;

        $QImei->update($data, $where);
    }

    $db->commit();
    exit(json_encode(array('code' => 1, 'message' => 'Done.', 'new_sn' => $new_sn)));
} catch (Exception $e) {
    $db->rollback();
    exit(json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage())));
}