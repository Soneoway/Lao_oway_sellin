<?php

class MappingController extends My_Controller_Action
{
    /***************** MAPPING PRODUCT ***************/

    public function fptProductAction()
    {
        $page       = $this->getRequest()->getParam('page', 1);
        $desc       = $this->getRequest()->getParam('desc', 0);
        $sort       = $this->getRequest()->getParam('sort', 'good_id');
        $code       = $this->getRequest()->getParam('code');
        $good_id    = $this->getRequest()->getParam('good_id');
        $good_color = $this->getRequest()->getParam('good_color');

        $total = 0;
        $limit = LIMITATION;
        $params = array(
            'code'       => $code,
            'good_id'    => $good_id,
            'good_color' => $good_color,
            'sort'       => $sort,
            'desc'       => $desc,
        );

        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->good_colors = $QGoodColor->get_cache();

        $QGoodMapping = new Application_Model_GoodMapping();
        $mapping = $QGoodMapping->fetchPagination($page, $limit, $total, $params);
        $mapping_id_arr = array();

        foreach ($mapping as $key => $value)
            $mapping_id_arr[] = $value['id'];

        $mapping_id_arr = array_unique($mapping_id_arr);
        $total2 = 0;
        $params = array('id' => $mapping_id_arr);
        $mapping = $QGoodMapping->fetchPagination(1, null, $total2, $params);

        $mapping_arr = array();

        foreach ($mapping as $key => $value) {
            if (!isset($mapping_arr[ $value['id'] ]))
                $mapping_arr[ $value['id'] ] = array(
                    'good_id' => $value['good_id'],
                    'color_id' => $value['color_id'],
                    'code' => array(),
                );

            $mapping_arr[ $value['id'] ]['code'][] = $value['code'];
        }

        $this->view->mapping = $mapping_arr;

        $params = array(
            'code'       => $code,
            'good_id'    => $good_id,
            'good_color' => $good_color,
            'sort'       => $sort,
            'desc'       => $desc,
        );

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'mapping/fpt-product/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('fpt/product');
    }

    public function fptProductCreateAction()
    {
        $this->_helper->viewRenderer->setRender('fpt/product-edit');
        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->goodColors = $QGoodColor->get_cache();
    }

    public function fptProductEditAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'mapping/fpt-product');
        }

        $id = intval($id);

        $this->_helper->viewRenderer->setRender('fpt/product-edit');

        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->goodColors = $QGoodColor->get_cache();

        $QGoodMapping = new Application_Model_GoodMapping();
        $mapping = $QGoodMapping->find($id);
        $mapping = $mapping->current();

        if (!$mapping) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'mapping/fpt-product');
        }

        $QGoodMappingCode = new Application_Model_GoodMappingCode();
        $where = $QGoodMappingCode->getAdapter()->quoteInto('good_mapping_id = ?', $id);
        $code_list = $QGoodMappingCode->fetchAll($where);
        $code_arr = array();

        foreach ($code_list as $key => $value)
            $code_arr[] = $value['code'];

        $this->view->mapping = $mapping;
        $this->view->code_list = $code_arr;
    }

    public function fptProductSaveAction()
    {
        if (!$this->getRequest()->getMethod() == 'POST')
            $this->_redirect(HOST.'mapping/fpt-product');

        $code_list = $this->getRequest()->getParam('code_list');
        $good_id   = $this->getRequest()->getParam('good_id');
        $color_id  = $this->getRequest()->getParam('color_id');
        $id        = $this->getRequest()->getParam('id');

        $QGoodMapping = new Application_Model_GoodMapping();
        $QGoodMappingCode = new Application_Model_GoodMappingCode();
        $QGood = new Application_Model_Good();
        $flashMessenger = $this->_helper->flashMessenger;
        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            if (trim($code_list) == "") throw new Exception("Code is required.");
            $code_list = explode(',', $code_list);
            if (!is_array($code_list) || !count($code_list)) $code_list = array();

            $code_list = array_unique($code_list);
            if (!is_array($code_list) || !count($code_list)) $code_list = array();

            foreach ($code_list as $key => $value) {
                $where = array();

                if ($id)
                    $where[] = $QGoodMappingCode->getAdapter()->quoteInto('good_mapping_id <> ?', intval($id));

                $where[] = $QGoodMappingCode->getAdapter()->quoteInto('code LIKE ?', strval($value));
                $code_check = $QGoodMappingCode->fetchRow($where);
                if ($code_check) throw new Exception("Code exists: " .$value, 1);
            }

            $where = $QGood->getAdapter()->quoteInto('id = ?', intval($good_id));
            $good = $QGood->fetchRow($where);
            if (!$good) throw new Exception("Invalid product", 1);

            if ($id) {
                $id = intval($id);
                $mapping = $QGoodMapping->find($id);
                $mapping = $mapping->current();

                if (!$mapping) throw new Exception("Invalid ID.");

                $data = array(
                    'good_id'  => intval($good_id),
                    'color_id' => intval($color_id),
                );

                // update
                $where = $QGoodMapping->getAdapter()->quoteInto('id = ?', $id);
                $QGoodMapping->update($data, $where);
            } else {
                $data = array(
                    'good_id'  => intval($good_id),
                    'color_id' => intval($color_id),
                );

                $id = $QGoodMapping->insert($data);
            }

            // xóa code cũ
            $where = $QGoodMappingCode->getAdapter()->quoteInto('good_mapping_id = ?', $id);
            $QGoodMappingCode->delete($where);

            // chèn code mới
            foreach ($code_list as $key => $value) {
                $data = array(
                    'good_mapping_id' => $id,
                    'code' => strval($value),
                );

                $QGoodMappingCode->insert($data);
            }

            try {
                // chèn code riêng của oppo
                $data = array(
                    'good_mapping_id' => $id,
                    'code' => "P".sprintf("%05d", $good_id).sprintf("%03d", $color_id),
                );

                $QGoodMappingCode->insert($data);
            } catch (Exception $e) {
            }
            

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Success');
        } catch (Exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }

        $cache = Zend_Registry::get('cache');
        $cache->remove('good_mapping_cache');

        $this->_redirect(HOST.'mapping/fpt-product');
    }

    public function fptProductDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;

        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            if (!$id) throw new Exception("Invalid ID", 1);
            $id = intval($id);

            $QGoodMapping = new Application_Model_GoodMapping();
            $mapping = $QGoodMapping->find($id);
            $mapping = $mapping->current();

            if (!$mapping) throw new Exception("Invalid ID", 1);

            $where = $QGoodMapping->getAdapter()->quoteInto('id = ?', $id);
            $QGoodMapping->delete($where);

            // xóa code cũ
            $QGoodMappingCode = new Application_Model_GoodMappingCode();
            $where = $QGoodMappingCode->getAdapter()->quoteInto('good_mapping_id = ?', $id);
            $QGoodMappingCode->delete($where);

            $flashMessenger->setNamespace('success')->addMessage('Success');

            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }

        $cache = Zend_Registry::get('cache');
        $cache->remove('good_mapping_cache');

        $this->_redirect(HOST.'mapping/fpt-product');
    }

    /***************** MAPPING STORE ***************/
    public function fptStoreAction()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $desc = $this->getRequest()->getParam('desc', 0);
        $sort = $this->getRequest()->getParam('sort', 'code');
        $code = $this->getRequest()->getParam('code');
        $distributor_id = $this->getRequest()->getParam('distributor_id');

        $total = 0;
        $limit = LIMITATION;
        $params = array(
            'sort' => $sort,
            'desc' => $desc,
            'code' => $code,
            'distributor_id' => $distributor_id,
        );

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->get_cache();

        $QDistributorMapping = new Application_Model_DistributorMapping();
        $this->view->mapping = $QDistributorMapping->fetchPagination($page, $limit, $total, $params);

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'mapping/fpt-store/'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('fpt/store');
    }

    public function fptStoreCreateAction()
    {
        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->get_cache();

        $this->_helper->viewRenderer->setRender('fpt/store-edit');
    }

    public function fptStoreEditAction()
    {
        $distributor_id = $this->getRequest()->getParam('distributor_id');
        $flashMessenger = $this->_helper->flashMessenger;

        try {
            if (!$distributor_id) throw new Exception("Invalid ID", 1);
            $distributor_id = intval($distributor_id);

            $QDistributorMapping = new Application_Model_DistributorMapping();
            $mapping = $QDistributorMapping->find($distributor_id);
            $mapping = $mapping->current();

            if (!$mapping) throw new Exception("Invalid ID", 1);

            $this->view->mapping = $mapping;

            $QDistributor = new Application_Model_Distributor();
            $this->view->distributors = $QDistributor->get_cache();

            $this->_helper->viewRenderer->setRender('fpt/store-edit');
        } catch (Exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'mapping/fpt-store');
        }
    }

    public function fptStoreSaveAction()
    {
        if (!$this->getRequest()->getMethod() == 'POST')
            $this->_redirect(HOST.'mapping/fpt-store');

        $code = $this->getRequest()->getParam('code');
        $distributor_id = $this->getRequest()->getParam('distributor_id');
        $flashMessenger = $this->_helper->flashMessenger;

        try {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            if (!$distributor_id) throw new Exception("Invalid distributor id", 5);
            $distributor_id = intval($distributor_id);

            $QDistributor = new Application_Model_Distributor();
            $QDistributorMapping = new Application_Model_DistributorMapping();

            $where = $QDistributor->getAdapter()->quoteInto('id = ?', intval($distributor_id));
            $distributor_check = $QDistributor->fetchRow($where);
            if (!$distributor_check) throw new Exception("Invalid distributor", 4);

            if (!$code) throw new Exception("Invalid code", 2);

            $code = trim($code);
            if (empty($code)) throw new Exception("Invalid code", 3);
            $code = strval($code);

            $where = array();
            $where[] = $QDistributorMapping->getAdapter()->quoteInto('distributor_id <> ?', $distributor_id);
            $where[] = $QDistributorMapping->getAdapter()->quoteInto('code LIKE ?', $code);

            $mapping = $QDistributorMapping->fetchRow($where);

            if ($mapping) throw new Exception("Code exists", 6);

            $data = array(
                'distributor_id' => $distributor_id,
                'code'           => $code,
            );

            try {
                $QDistributorMapping->insert($data);
            } catch (Exception $e) {
                $where = $QDistributorMapping->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
                $QDistributorMapping->update($data, $where);
            }

            $flashMessenger->setNamespace('success')->addMessage('Success');
            $db->commit();
        } catch (Exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $db->rollback();
        }

        $cache = Zend_Registry::get('cache');
        $cache->remove('distributor_mapping_cache');

        $this->_redirect(HOST.'mapping/fpt-store');
    }

    public function fptStoreDeleteAction()
    {
        $distributor_id = $this->getRequest()->getParam('distributor_id');

        try {
            $flashMessenger = $this->_helper->flashMessenger;
            if (!$distributor_id) throw new Exception("Invalid ID", 1);

            $QDistributorMapping = new Application_Model_DistributorMapping();
            $where = $QDistributorMapping->getAdapter()->quoteInto('distributor_id = ?', $distributor_id);
            $mapping = $QDistributorMapping->fetchRow($where);

            if (!$mapping) throw new Exception("Invalid ID", 2);

            $QDistributorMapping->delete($where);

            $flashMessenger->setNamespace('success')->addMessage('Success');
        } catch (Exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }

        $cache = Zend_Registry::get('cache');
        $cache->remove('distributor_mapping_cache');

        $this->_redirect(HOST.'mapping/fpt-store');
    }

    /***************** MAPPING WAREHOUSE ***************/
    public function fptWarehouseAction()
    {
        $page = $this->getRequest()->getParam('page', 1);
        $desc = $this->getRequest()->getParam('desc', 0);
        $sort = $this->getRequest()->getParam('sort', 'area_name');
        $total = 0;
        $limit = LIMITATION;
        $params = array(
            'sort' => $sort,
            'desc' => $desc,
            );

        $QFptWarehouseMapping = new Application_Model_FptAreaWarehouseMapping();
        $this->view->mapping = $QFptWarehouseMapping->fetchPagination($page, $limit, $total, $params);

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'mapping/fpt-warehouse/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('fpt/warehouse');
    }

    public function fptWarehouseEditAction()
    {
        $id = $this->getRequest()->getParam('id');

        $flashMessenger = $this->_helper->flashMessenger;
        $this->_helper->viewRenderer->setRender('fpt/warehouse-edit');

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->get_cache();

        if (!$id) {
            return;
        }

        $QFptWarehouseMapping = new Application_Model_FptAreaWarehouseMapping();
        $mapping = $QFptWarehouseMapping->find($id);
        $mapping = $mapping->current();

        if (!$mapping) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'mapping/fpt-warehouse');
        }

        $this->view->mapping = $mapping;
    }

    public function fptWarehouseSaveAction()
    {
        if (!$this->getRequest()->getMethod() == 'POST') {
            $this->_redirect(HOST.'mapping/fpt-warehouse');
        }

        $area_name    = $this->getRequest()->getParam('area_name');
        $warehouse_id = $this->getRequest()->getParam('warehouse_id');
        $id           = $this->getRequest()->getParam('id');

        $QFptWarehouseMapping = new Application_Model_FptAreaWarehouseMapping();
        $flashMessenger = $this->_helper->flashMessenger;

        if ($id) {
            try {
                $mapping = $QFptWarehouseMapping->find($id);
                $mapping = $mapping->current();

                if (!$mapping) {
                    $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
                    $this->_redirect(HOST.'mapping/fpt-warehouse');
                }

                if (trim($area_name) == "") {
                    $flashMessenger->setNamespace('error')->addMessage('Area Name is required. Not saved.');
                    $this->_redirect(HOST.'mapping/fpt-warehouse');
                }

                $data = array(
                    'warehouse_id' => $warehouse_id,
                    'name'         => trim($area_name),
                    );

                $where = $QFptWarehouseMapping->getAdapter()->quoteInto('id = ?', $id);
                $QFptWarehouseMapping->update($data, $where);
            } catch(Exception $ex) {
                // PC::db($ex->getMessage());
            }

        } else {
            try {
                if (trim($area_name) == "") {
                    $flashMessenger->setNamespace('error')->addMessage('Area Name is required. Not saved.');
                    $this->_redirect(HOST.'mapping/fpt-warehouse');
                }

                $data = array(
                    'warehouse_id' => $warehouse_id,
                    'name'         => trim($area_name),
                    );

                $QFptWarehouseMapping->insert($data);
            } catch(Exception $ex) {

            }
        }

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'mapping/fpt-warehouse');
    }

    public function fptWarehouseDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'mapping/fpt-warehouse');
        }

        $QFptWarehouseMapping = new Application_Model_FptAreaWarehouseMapping();
        $mapping = $QFptWarehouseMapping->find($id);
        $mapping = $mapping->current();

        if (!$mapping) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'mapping/fpt-warehouse');
        }

        $where = $QFptWarehouseMapping->getAdapter()->quoteInto('id = ?', $id);
        $QFptWarehouseMapping->delete($where);

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'mapping/fpt-warehouse');
    }

    /***************** MAPPING DISTRIBUTOR ***************/
    public function distributorAction()
    {
        $page   = $this->getRequest()->getParam('page', 1);
        $sort   = $this->getRequest()->getParam('sort', 'title');
        $desc   = $this->getRequest()->getParam('desc', 0);
        $region = $this->getRequest()->getParam('region');
        $area   = $this->getRequest()->getParam('area');
        $name   = $this->getRequest()->getParam('name');
        $ka     = $this->getRequest()->getParam('ka');
        $parent = $this->getRequest()->getParam('parent',0);

        $limit = LIMITATION;
        $total = 0;
        $params = array(
            'parent'    => ($parent == 0) ? $parent : NULL,
            'area'      => $area,
            'region'    => $region,
            'title'     => $name,
            'ka'        => $ka,
            );

        $QDistributor = new Application_Model_Distributor();
        $this->view->distributors = $QDistributor->fetchPagination($page, $limit, $total, $params);

        $params['parent'] = $parent;

        $QRegion = new Application_Model_RegionalMarket();
        $this->view->regions = $QRegion->get_cache_all();

        if ($area) {
            $where = $QRegion->getAdapter()->quoteInto('area_id = ?', $area);
            $regions = $QRegion->fetchAll($where);
            $regions_search = array();

            foreach ($regions as $key => $value)
                $regions_search[$value['id']] = $value['name'];

            $this->view->regions_search = $regions_search;
        }

        $QArea = new Application_Model_Area();
        $this->view->areas = $QArea->get_cache();

        $this->_helper->viewRenderer->setRender('distributor/index');

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'mapping/distributor/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);
    }

    public function distributorEditAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $QDistributor = new Application_Model_Distributor();
            $distributor = $QDistributor->find($id);
            $distributor = $distributor->current();

            if ($distributor) {
                // lấy các distributor thuộc distributor này
                $where = $QDistributor->getAdapter()->quoteInto('parent = ?', $id);
                $this->view->subs = $QDistributor->fetchAll($where);

                $QArea = new Application_Model_Area();
                $this->view->areas = $QArea->get_cache();
                $QRegion = new Application_Model_RegionalMarket();
                $this->view->regions = $QRegion->get_cache_all();

                $this->view->distributor = $distributor;
            } else {
                $this->_redirect(HOST.'mapping/distributor');
            }

            $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');

            $this->_helper->viewRenderer->setRender('distributor/edit');
        } else {
            $this->_redirect(HOST.'mapping/distributor');
        }
    }

    public function distributorSaveAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getRequest()->getParam('id');

        $flashMessenger = $this->_helper->flashMessenger;
        $back_url = $this->getRequest()->getParam('back_url');
        $back_url = $back_url ? $back_url : (HOST.'mapping/distributor');

        if ($id) {
            //check id của distributor
            $QDistributor = new Application_Model_Distributor();
            $distributor = $QDistributor->find($id);
            $distributor = $distributor->current();

            if ( ! $distributor ) {
                // báo lỗi
                $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
                echo '<script>parent.location.href="'.( $back_url ).'"</script>';
                exit;
            }

            $pic = $this->getRequest()->getParam('store_ids');
            $store_ids = explode(',', $pic);
            $store_ids = is_array($store_ids) ? array_filter($store_ids) : array();

            // lấy dealer cũ thuộc dealer này
            $where = $QDistributor->getAdapter()->quoteInto('parent = ?', $id);
            $stores = $QDistributor->fetchAll($where);

            $old_ids = array();

            foreach ($stores as $key => $store) {
                $old_ids[] = $store['id'];
            }

            // định dạng dealer id hiện tại
            foreach ($store_ids as $key => $s_id) {
                $store_ids[$key] = intval( trim( $s_id ) );
            }

            $del_ids = array_diff($old_ids, $store_ids);
            $del_ids = is_array($del_ids) ? array_filter($del_ids) : array();

            $new_ids = array_diff($store_ids, $old_ids);
            $new_ids = is_array($new_ids) ? array_filter($new_ids) : array();

            // cập nhật d_id cho các dealer mới thêm
            foreach ($new_ids as $s_id) {
                // không cho bind chính nó
                if ($s_id == $id) {
                    $parent = $QDistributor->find($s_id);
                    $parent = $parent->current();

                    if ($parent) {
                        echo '<script>
                                parent.palert("Dealer ['.$parent['title'] .'] không thể có dealer con (sub) là chính nó.");

                                parent.alert("Dealer ['.$parent['title'] .'] không thể có dealer con (sub) là chính nó.");
                            </script>';
                        exit;
                    }
                }

                // kiểm tra dealer này có thuộc dealer nào chưa
                $where = $QDistributor->getAdapter()->quoteInto('parent IS NOT NULL AND parent <> 0 AND id = ?', $s_id);
                $check_store = $QDistributor->fetchRow($where);

                if ($check_store) {
                    $check_dealer = $QDistributor->find($check_store['parent']);
                    $check_dealer = $check_dealer->current();

                    if ($check_dealer) {
                        echo '<script>
                                parent.palert("Dealer ['.$check_store['title'] .'] đã thuộc Dealer [<a href=\"'.HOST.'mapping/distributor-edit?id='.$check_dealer['id'].'\" target=\"_blank\">'.$check_dealer['title'] .'</a>] địa chỉ ['
                                    .$check_dealer['add_tax'].'] Vui lòng vào Dealer đó để remove Dealer này ra. Sau đó add lại tại đây.");

                                parent.alert("Dealer ['.$check_store['title'] .'] đã thuộc Dealer ['.$check_dealer['title'] .'] địa chỉ ['
                                    .$check_dealer['add_tax'].'] Vui lòng vào Dealer đó để remove Dealer này ra. Sau đó add lại tại đây.");
                            </script>';
                        exit;
                    } else {
                        echo '<script>
                                parent.palert("Dealer ['.$check_store['title'] .'] đã thuộc Dealer khác. Vui lòng kiểm tra lại.");

                                parent.alert("Dealer ['.$check_store['title'] .'] đã thuộc Dealer khác. Vui lòng kiểm tra lại.");
                            </script>';
                        exit;
                    }
                }

                // kiểm tra dealer này có con chưa
                $where = $QDistributor->getAdapter()->quoteInto('parent = ?', $s_id);
                $child = $QDistributor->fetchRow($where);

                if ($child) {
                    $parent = $QDistributor->find($s_id);
                    $parent = $parent->current();

                    if ($parent) {
                        echo '<script>
                                parent.palert("Dealer [<a href=\"'.HOST.'manage/dealer-edit?id='.$parent['id'].'\" target=\"_blank\">'.$parent['title'] .'</a>] đã có Dealer con là ['.$child['title'] .'] địa chỉ ['
                                    .$child['add_tax'].'] Dealer đã có dealer con (sub) không thể làm sub của dealer khác. Vui lòng kiểm tra lại.");

                                parent.alert("Dealer ['.$parent['title'] .'] đã có Dealer con là ['.$child['title'] .'] địa chỉ ['
                                    .$child['add_tax'].'] Dealer đã có dealer con (sub) không thể làm sub của dealer khác. Vui lòng kiểm tra lại.");
                            </script>';
                        exit;
                    }
                }

                $data = array('parent' => $id);
                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $s_id);
                $QDistributor->update($data, $where);
            }

            // cập nhật d_id là NULL cho các dealer bị remove
            foreach ($del_ids as $s_id) {
                $data = array('parent' => 0);
                $where = $QDistributor->getAdapter()->quoteInto('id = ?', $s_id);
                $QDistributor->update($data, $where);
            }

            $QLog = new Application_Model_Log();
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();
            $ip = $this->getRequest()->getServer('REMOTE_ADDR');
            $info = 'DISTRIBUTOR - Bind('.$id.') - Children('.$pic.')';

            //todo log
            $QLog->insert( array (
                'info' => $info,
                'user_id' => $userStorage->id,
                'ip_address' => $ip,
                'time' => date('Y-m-d H:i:s'),
            ) );

            $flashMessenger->setNamespace('success')->addMessage('Done');
        } else {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
        }

        echo '<script>parent.location.href="'.( $back_url ).'"</script>';
        exit;
    }
}