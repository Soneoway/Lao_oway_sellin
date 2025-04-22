<?php

class ProductController extends My_Controller_Action
{
	public function listAction()
	{
		$sort    = $this->getRequest()->getParam('sort', '');
		$desc    = $this->getRequest()->getParam('desc', 1);

		$name    = $this->getRequest()->getParam('name');
        $details = $this->getRequest()->getParam('details');
		$product_status = $this->getRequest()->getParam('product_status', 1);
        $cat_id           = $this->getRequest()->getParam('cat_id');
        $good_id          = $this->getRequest()->getParam('good_id');
        $good_color       = $this->getRequest()->getParam('good_color');
        $brand_id = $this->getRequest()->getParam('brand_id');
        

		$export  = $this->getRequest()->getParam('export', 0);

		$page  = $this->getRequest()->getParam('page', 1);
		$limit = LIMITATION;
		$total = 0;

		$params = array(
			'name'    => $name,
            'details' => $details,
			'product_status' => $product_status,
            'cat_id'  => $cat_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
			'sort'    => $sort,
			'desc'    => $desc,
            'export'  => $export,
            'brand_id' => $brand_id,
			);

		$QGood = new Application_Model_Good();

		$QGoodColor     = new Application_Model_GoodColor();
		$goodColors     = $QGoodColor->get_cache();

		$QGoodCategory  = new Application_Model_GoodCategory();
		$goodCategories = $QGoodCategory->get_cache();

		$QBrand         = new Application_Model_Brand();
		$brands         = $QBrand->get_cache();

		// Xuất excel
		if ($export) {

            switch ($export){
                case 1:
                    $goods = $QGood->fetchPagination($page, null, $total, $params);
                    $this->_exportExcel($goods);
                    break;
                case 2:
                    $getProductListBarCode = $QGood->getExportProductListBarCode();
                    $this->_exportExcelProductListBarCode($getProductListBarCode);
                    break;
            }

		} else {

			$goods = $QGood->fetchPagination($page, $limit, $total, $params);
		}

		$this->view->goods          = $goods;
		$this->view->colors         = $goodColors;
		$this->view->good_categories = $goodCategories;
		$this->view->brands         = $brands;

		$this->view->desc   = $desc;
		$this->view->sort   = $sort;
		$this->view->params = $params;
		$this->view->limit  = $limit;
		$this->view->total  = $total;
		$this->view->url    = HOST.'product/list/'.( $params ? '?'.http_build_query($params).'&' : '?' );

		$this->view->offset = $limit*($page-1);

		if($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setRender('product/partials/list');
		} else {
			$flashMessenger               = $this->_helper->flashMessenger;
			$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
			$this->view->messages_success = $messages_success;

			$messages                     = $flashMessenger->setNamespace('error')->getMessages();
			$this->view->messages         = $messages;

			$this->_helper->viewRenderer->setRender('product/list');
		}

	}

	public function productCreateAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id) { // load for editing
			$QGood      = new Application_Model_Good();
			$goodRowSet = $QGood->find($id);
			$good       = $goodRowSet->current();

			$this->view->good = $good;
		}

		$QGoodColor     = new Application_Model_GoodColor();
		$goodColors     = $QGoodColor->get_cache();

		$QGoodCategory  = new Application_Model_GoodCategory();
		$goodCategories = $QGoodCategory->get_cache();

		$QBrand         = new Application_Model_Brand();
		$brands         = $QBrand->get_cache();

		$this->view->goodColors     = $goodColors;
		$this->view->goodCategories = $goodCategories;
		$this->view->brands         = $brands;

		$flashMessenger = $this->_helper->flashMessenger;
		$messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

		$this->_helper->viewRenderer->setRender('product/product-create');
	}
    //Update By PungPond
    public function productUpdateAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {
            try {
                $id       = $this->getRequest()->getParam('id');
                $price_3    = $this->getRequest()->getParam('price_3', 0);
                $price_1 =($price_3*0.82);
                $price_2 =($price_3*0.85);
                $price_5 =($price_3*0.83);
                $price_6 =($price_3*0.9);
                $price_7 =($price_3*0.9);
                $price_8 =($price_3*0.85);
                $data = array(
                    'price_3'    => $price_3,
                    'price_1'    => $price_1,
                    'price_2'    => $price_2,
                    'price_5'    => $price_5,
                    'price_6'    => $price_6,
                    'price_7'    => $price_7,
                    'price_8'    => $price_8
                
                );
                if ($price_3 == '') {
                    $flashMessenger->setNamespace('error')->addMessage('Invalid Retail Price');
                    $this->_redirect(HOST.'product/list');
                }
                $QGood = new Application_Model_Good();

                if ($id) { // save
                    $where = $QGood->getAdapter()->quoteInto('id = ?', $id);
                    $QGood->update($data, $where);
                    $flashMessenger->setNamespace('success')->addMessage('Success');
                    $this->_redirect(HOST.'product/list');
                }

               
                
            } catch(Exception $e){
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'product/list');
            }

        } else {
            $flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
            $this->_redirect(HOST.'product/product-create');
        }
    }//end
	public function productSaveAction()
	{
		$flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {
        	try {
				$id       = $this->getRequest()->getParam('id');
				$name     = $this->getRequest()->getParam('name', '');

				if ($name == '') {
		            $flashMessenger->setNamespace('error')->addMessage('Invalid Name');
		            $this->_redirect(HOST.'product/product-create');
				}

                $cat_id     = $this->getRequest()->getParam('category');
                $brand_id   = $this->getRequest()->getParam('brand');
                $color      = $this->getRequest()->getParam('good_color');
                $price_1    = $this->getRequest()->getParam('price_1', 0);
                $price_2    = $this->getRequest()->getParam('price_2', 0);
                $price_3    = $this->getRequest()->getParam('price_3', 0);
                $price_4    = $this->getRequest()->getParam('price_4', 0);
                $price_5    = $this->getRequest()->getParam('price_5', 0);
                $price_6    = $this->getRequest()->getParam('price_6', 0);
                $price_7    = $this->getRequest()->getParam('price_7', 0);
                $price_8    = $this->getRequest()->getParam('price_8', 0);
                $price_9    = $this->getRequest()->getParam('price_9', 0);
                $price_10   = $this->getRequest()->getParam('price_10', 0);
                $price_11   = $this->getRequest()->getParam('price_11', 0);
                $price_12   = $this->getRequest()->getParam('price_12', 0);
                $price_13   = $this->getRequest()->getParam('price_13', 0);
                $price_1300 = $this->getRequest()->getParam('price_1300', 0);
                $price_14   = $this->getRequest()->getParam('price_14', 0);
                $price_15   = $this->getRequest()->getParam('price_15', 0);
                $price_16   = $this->getRequest()->getParam('price_16', 0);
                $price_17   = $this->getRequest()->getParam('price_17' , 0);
                $price_start   = $this->getRequest()->getParam('price_start' , 0);
                $desc       = $this->getRequest()->getParam('details');
                $desc_name  = $this->getRequest()->getParam('details_desc');
                $weight     = $this->getRequest()->getParam('weight');
                $pre_order_product = $this->getRequest()->getParam('pre_order_product',null);
                $hero_product = $this->getRequest()->getParam('hero_product',null);
                
                $product_status = $this->getRequest()->getParam('product_status');

                $remark = $this->getRequest()->getParam('remark');

				$data = array(
                    'name'       => $name,
                    'cat_id'     => intval($cat_id),
                    'brand_id'   => intval($brand_id),
                    'color'      => $color,
                    'price_1'    => $price_1,
                    'price_2'    => $price_2,
                    'price_3'    => $price_3,
                    'price_4'    => $price_4,
                    'price_5'    => $price_5,
                    'price_6'    => $price_6,
                    'price_7'    => $price_7,
                    'price_8'    => $price_8,
                    'price_9'    => $price_9,
                    'price_10'   => $price_10,
                    'price_11'   => $price_11,
                    'price_12'   => $price_12,
                    'price_13'   => $price_13,
                    'price_1300' => $price_1300,
                    'price_14'   => $price_14,
                    'price_15'   => $price_15,
                    'price_16'   => $price_16,
                    'price_17'   => $price_17,
                    'price_start'   => $price_start,
                    'desc'       => $desc,
                    'desc_name'  => $desc_name,
                    'weight'     => $weight,
                    'product_status' => $product_status,
                    'remark' => $remark,
                    'pre_order_product' => $pre_order_product,
                    'hero_product' => $hero_product
				);

				$QGood = new Application_Model_Good();
                $userStorage = Zend_Auth::getInstance()->getStorage()->read();

				if ($id) { // save
                    $add_time = date('Y-m-d H:i:s');
                    $data['update_time'] = $add_time;
                    $data['update_by'] = $userStorage->id;

					$where = $QGood->getAdapter()->quoteInto('id = ?', $id);
					$QGood->update($data, $where);
				} else { // create new
					$add_time = date('Y-m-d H:i:s');
                    $data['add_by'] = $userStorage->id;
					$data['add_time'] = $add_time;
                    $data['update_time'] = $add_time;
                    $data['update_by'] = $userStorage->id;

					$id = $QGood->insert($data);
				}

                // giờ éo log tự động nữa, chuyển qua quản lý trực tiếp cho tường minh

				$QGoodColorCombined = new Application_Model_GoodColorCombined();
				$where = $QGoodColorCombined->getAdapter()->quoteInto('good_id = ?', $id);
				$QGoodColorCombined->delete($where);

				if ($color) {
					$color_arr = explode(',', $color);
					foreach ($color_arr as $k => $c) {
						$data = array('good_id'=>$id, 'good_color_id'=>$c);
						$QGoodColorCombined->insert($data);
					}
				}

	            //remove cache
	            $cache = Zend_Registry::get('cache');
	            $cache->remove('good_cache');
	            $cache->remove('good_2_cache');
	            $cache->remove('goodsdescription_cache');

				$flashMessenger->setNamespace('success')->addMessage('Success');
				$this->_redirect(HOST.'product/list');
			} catch(Exception $e){
				$flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        		$this->_redirect(HOST.'product/product-create');
			}

        } else {
        	$flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
        	$this->_redirect(HOST.'product/product-create');
        }
	}

	public function productDeleteAction()
	{
		$flashMessenger   = $this->_helper->flashMessenger;
		$id = $this->getRequest()->getParam('id');
		$flag = FALSE;

		if ($id) {
			$QGood = new Application_Model_Good();
			$goodRowSet = $QGood->find($id);
			$good = $goodRowSet->current();

			if ($good) {

                //kiem tra neu co PO
                $QPO = new Application_Model_Po();
                $where = $QPO->getAdapter()->quoteInto('good_id = ?', $id);
                $has_PO = $QPO->fetchRow($where);
                if ($has_PO){
                    $flashMessenger->setNamespace('error')->addMessage('This Product had PO already, you cannot delete!');
                    $this->_redirect(HOST.'product/list');
                }

                //kiem tra neu co Order
                $QMarket = new Application_Model_Market();
                $where = $QMarket->getAdapter()->quoteInto('good_id = ?', $id);
                $has_Market = $QMarket->fetchRow($where);
                if ($has_Market){
                    $flashMessenger->setNamespace('error')->addMessage('This Product had Order already, you cannot delete!');
                    $this->_redirect(HOST.'product/list');
                }

				$where = $QGood->getAdapter()->quoteInto('id = ?', $id);
        		$QGood->delete($where);



				$messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
				$flag = TRUE;



			}
		}

        //remove cache
        $cache = Zend_Registry::get('cache');
        $cache->remove('good_cache');
        $cache->remove('good_2_cache');
        $cache->remove('goodsdescription_cache');

		if (!$flag)
			$messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

		$this->_redirect(HOST.'product/list');
	}

    // log price - quản lý giá bán lẻ và các lần thay đổi giá
    public function priceLogAction()
    {
        $QPrice = new Application_Model_GoodPriceLog();
        $QGood = new Application_Model_Good();
        $QGoodCategory = new Application_Model_GoodCategory();

        $page           = $this->getRequest()->getParam('page', 1);
        $from_date      = $this->getRequest()->getParam('from_date');
        $to_date        = $this->getRequest()->getParam('to_date');
        $good_id        = $this->getRequest()->getParam('good_id');
        $cat_id         = $this->getRequest()->getParam('cat_id');

        $limit   = LIMITATION * 2;
        $total   = 0;
        $params = array(
            'from_date'     => $from_date,
            'to_date'       => $to_date,
            'good_id'       => $good_id,
            'cat_id'        => $cat_id,
        );

        $this->view->logs   = $QPrice->fetchPagination($page, $limit, $total, $params);
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'product/price-log'.( $params ? '?'.http_build_query($params).'&' : '?' );
        $this->view->offset = $limit*($page-1);

        $goods              = $QGood->get_cache();
        $good_categories    = $QGoodCategory->get_cache();

        $this->view->goods  = $goods;
        $this->view->good_categories = $good_categories;

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('price-log/list');
    }

    public function priceLogSaveAction()
    {
        $id        = $this->getRequest()->getParam('id');
        $good_id   = $this->getRequest()->getParam('good_id');
        $color_id  = $this->getRequest()->getParam('color_id');
        $price     = $this->getRequest()->getParam('price');
        $from_date = $this->getRequest()->getParam('from_date');
        $to_date   = $this->getRequest()->getParam('to_date', null);
        $price_1    = $this->getRequest()->getParam('price_1', 0);
        $price_2    = $this->getRequest()->getParam('price_2', 0);
        $price_3    = $this->getRequest()->getParam('price_3', 0);
        $price_4    = $this->getRequest()->getParam('price_4', 0);
        $price_5    = $this->getRequest()->getParam('price_5', 0);
        $price_6    = $this->getRequest()->getParam('price_6', 0);
        $price_7    = $this->getRequest()->getParam('price_7', 0);
        $price_8    = $this->getRequest()->getParam('price_8', 0);
        $price_9    = $this->getRequest()->getParam('price_9', 0);
        $price_10   = $this->getRequest()->getParam('price_10', 0);
        $price_11   = $this->getRequest()->getParam('price_11', 0);
        $price_12   = $this->getRequest()->getParam('price_12', 0);
        $price_13   = $this->getRequest()->getParam('price_13', 0);
        $price_1300 = $this->getRequest()->getParam('price_1300', 0);
        $price_14   = $this->getRequest()->getParam('price_14', 0);
        $price_15   = $this->getRequest()->getParam('price_15', 0);
        $price_17   = $this->getRequest()->getParam('price_17' , 0);
        $price_start   = $this->getRequest()->getParam('price_start' , 0);
        $flashMessenger = $this->_helper->flashMessenger;

        try
        {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();
            $QPrice = new Application_Model_GoodPriceLog();
            $QGoodPriceDealer = new Application_Model_GoodPriceLogDetail();

            $datetime = date('Y-m-d H:i:s');

            $data = array(
                'good_id'    => intval($good_id),
                'color_id'   => intval($color_id),
                'price'      => intval($price),
                'from_date'  => DateTime::createFromFormat('d/m/Y', $from_date)->format('Y-m-d'),
                'to_date'    => empty($to_date) || !$to_date ? null : DateTime::createFromFormat('d/m/Y', $to_date)->format('Y-m-d'), // nếu bỏ trống ô "To" thì lưu là NULL
            );

            $price_array = array(
                LOG_PRICE_I_AGENT_PRICE     => intval($price_1),
                LOG_PRICE_II_AGENT_PRICE    => intval($price_2),
                LOG_PRICE_RETAILER_PRICE    => intval($price_3),
                LOG_PRICE_IMPORT_PRICE      => intval($price_4),
                LOG_PRICE_III_AGENT_PRICE_FOR_FPT    => intval($price_5),
                LOG_PRICE_IV_AGENT_PRICE_FOR_VIEN_HUNG    => intval($price_6),
                LOG_PRICE_V_AGENT_PRICE_FOR_TGDD_ACCESSORIES    => intval($price_7),
                LOG_PRICE_VI_AGENT_PRICE_FOR_NGUYEN_KIM    => intval($price_8),
                LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA   => intval($price_9),
                LOG_PRICE_VII_AGENT_PRICE_FOR_CAMPODIA   => intval($price_10),
                LOG_PRICE_IX_AGENT_PRICE_FOR_DIEN_MAY_CHO_LON   => intval($price_11),
                LOG_PRICE_LAOS   => intval($price_12),
                LOG_PRICE_TGDD_THUONG   => intval($price_13),
                LOG_PRICE_TGDD_NO => intval($price_1300),
                LOG_PRICE_VTA   => intval($price_14),
                LOG_PRICE_VINPRO   => intval($price_15),
                LOG_PRICE_DEMO     => intval($price_17),
                LOG_PRICE_START     => intval($price_start)
            );

            if ($id) {
                $where = $QPrice->getAdapter()->quoteInto('id = ?', $id);
                $data['updated_at'] = $datetime;
                $QPrice->update($data, $where);
                foreach($price_array as $k=>$v)
                {
                    $where_price_dealer = array();
                    $where_price_dealer[] = $QGoodPriceDealer->getAdapter()->quoteInto('price_log_id = ?' , $id);
                    $where_price_dealer[] = $QGoodPriceDealer->getAdapter()->quoteInto('rank_id = ?' , $k);
                    $data = array(
                        'price' => $v,
                    );

                    //edit nhung cai cu

                    $result = $QGoodPriceDealer->fetchRow($where_price_dealer);

                    if(empty($result))
                    {
                        $data = array(
                            'price_log_id' => $id,
                            'rank_id' => $k,
                            'price' => $v,
                        );
                        $QGoodPriceDealer->insert($data);
                    }
                    else{
                        $QGoodPriceDealer->update($data,$where_price_dealer);
                    }

                }

            } else {
                $userStorage = Zend_Auth::getInstance()->getStorage()->read();
                $data['created_at'] = $datetime;
                $data['created_by'] = $userStorage ? $userStorage->id : 0;
                $id_pricelog = $QPrice->insert($data);

                //cap nhat lai gia dealer log
                foreach($price_array as $k=>$v)
                {
                    $data = array(
                        'price_log_id' => $id_pricelog,
                        'rank_id' => $k,
                        'price' => $v,
                    );
                    $QGoodPriceDealer->insert($data);
                }
            }
            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $db->commit();
        }
        catch(exception $e)
        {
            var_dump($e);exit;
            $db->rollback();
            $flashMessenger->setNamespace('error')->addMessage('Cannot update, please try again!');
        }


        $this->_redirect(HOST.'product/price-log');
    }

    public function priceLogCreateAction()
    {
        $QGood = new Application_Model_Good();
        $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
        $goods = $QGood->fetchAll($where);

        $this->view->goods = $goods;

        $QGoodColor = new Application_Model_GoodColor();
        $this->view->good_colors = $QGoodColor->get_cache();
        
        $QGoodCat = new Application_Model_GoodCategory();
        $this->view->cat = $QGoodCat->get_cache();

        $this->_helper->viewRenderer->setRender('price-log/create');
    }
    
    //Lấy sản phẩm khi chọn danh mục product/price-log-create
    public function loadProductAction()
    {
        $cat_id = $this->getRequest()->getParam('cat_id');
        $QGood = new Application_Model_Good();

        if ($cat_id) {
            $where = array();
            $where[] = $QGood->getAdapter()->quoteInto('cat_id = ?',    $cat_id);
            $where[] = $QGood->getAdapter()->quoteInto('del = ?',       0);
            $goods = $QGood->fetchAll($where, 'name');

            echo json_encode(array('goods' => $goods->toArray()));
            exit;

        }
        else{
            echo json_encode(array());
            exit;
        }
    }

    public function priceLogEditAction()
    {
        $id = $this->getRequest()->getParam('id');

        try {
            $flashMessenger = $this->_helper->flashMessenger;

            if (!$id) throw new Exception("Invalid ID");

            $QPrice = new Application_Model_GoodPriceLog();
            $QGoodPriceDealer = new Application_Model_GoodPriceLogDetail();

            $whereDealer = array();
            $whereDealer[] = $QGoodPriceDealer->getAdapter()->quoteInto('price_log_id = ?' , $id );
            $price_dealer = $QGoodPriceDealer->fetchAll($whereDealer);

            $log = $QPrice->find($id);
            $log = $log->current();

            if (!$log) throw new Exception("Wrong ID");

            $this->view->log = $log;
            
            $QGood1 = new Application_Model_Good();
            $whereGood = $QGood1->getAdapter()->quoteInto('id = ?' , intval($log['good_id']) );
            $good = $QGood1->fetchRow($whereGood);
            
            $QGood = new Application_Model_Good();
            $where = $QGood->getAdapter()->quoteInto('cat_id = ?', intval($good['cat_id']));
            $goods = $QGood->fetchAll($where);

            $price_dealer_result = array();
            foreach($price_dealer as $k => $v)
            {
                $price_dealer_result[$v['rank_id']] = $v['price'];
            }

            $this->view->good  = $good;
            $this->view->goods = $goods;
            $this->view->price = $price_dealer_result;

            $QGoodColor = new Application_Model_GoodColor();
            $this->view->good_colors = $QGoodColor->get_cache();
            
            $QGoodCat = new Application_Model_GoodCategory();
            $this->view->cat = $QGoodCat->get_cache();

            $this->_helper->viewRenderer->setRender('price-log/create');
        } catch (Exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'product/price-log');
        }
    }

    public function priceLogDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;

        try {
            if (!$id) throw new Exception("Invalid ID");
            
            $QPrice = new Application_Model_GoodPriceLog();
            $log = $QPrice->find($id);
            $log = $log->current();

            if (!$log) throw new Exception("Wrong ID");
            
            $where = $QPrice->getAdapter()->quoteInto('id = ?', $id);
            $QPrice->delete($where);

            $QGoodPriceLogDetail = new Application_Model_GoodPriceLogDetail();
            $where = $QGoodPriceLogDetail->getAdapter()->quoteInto('price_log_id = ? ' , $id);
            $QGoodPriceLogDetail->delete($where);

            $flashMessenger->setNamespace('success')->addMessage('Success');
        } catch (Exception $e) {
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        }

        $this->_redirect(HOST.'product/price-log');
    }

    private function _exportExcel($data){

        set_time_limit(0);
        error_reporting(0);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $filename = 'Export_Product_List_'.date('d/m/Y');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        echo chr(239) . chr(187) . chr(191); 
        $output = fopen('php://output', 'w');

        $heads = array(
            'No.',
            'ID',
            'Model Name',
            'Color',
            'Category',
            'Brand',
            'Wholesale price', // ລາຄາສົ່ງ
            'Retail Price', // ລາຄາຂາຍໜ້າຮ້ານ
            'DEMO Price', // ລາຄາຂາຍໜ້າຮ້ານ
            'Staff Price', // ລາຄາພະນັກງານ
            'Import Price', // ລາຄານຳເຂົ້າ
            'Start Price', // ລາຄາເປິດໂຕ
            // 'Wholesale price For Alei', //ລາຄາສົ່ງຫົວພັນ
            // 'Product Details',
            // 'Invoice Details',
            'Added Time',
            'Update Time',
            'Product Status',
        );

        fputcsv($output, $heads);

          $QGoodColor     = new Application_Model_GoodColor();
        $goodColors     = $QGoodColor->get_cache();
        $goodColorsAll  = $QGoodColor->fetchAll()->toArray();

        $goodColorsAllArray = array();
        foreach($goodColorsAll as $k => $v)
            $goodColorsAllArray[$v['id']] = $v;

        $QGoodCategory  = new Application_Model_GoodCategory();
        $goodCategories = $QGoodCategory->get_cache();

        $QBrand         = new Application_Model_Brand();
        $brands         = $QBrand->get_cache();


        $no = 1;
        foreach ($data as $item) {

             if(isset($brands) && null !== $brands[$item['brand_id']])
                $brand = $brands[$item['brand_id']];
            else
                $brand = '';

            $row = array();
            $row[] = $no;
            $row[] = $item['id'];
            $row[] = $brand.' '.$item['name'];

            $colorStr = '';
            $colors = explode(',', $item['color']);

            foreach ($colors as $key => $color) {
                $colorStr .= $goodColors[$color] . ', ';
            }

            $colorStr = trim($colorStr, ', ');

            $row[] = $colorStr;

            if(isset($goodCategories) && isset($goodCategories[$item['cat_id']]))
                $cat = $goodCategories[$item['cat_id']];
            else
                $cat = '';
            $row[] = $cat;

           

            $row[] = $brand;
            $row[] = number_format($item['price_9']);
            $row[] = number_format($item['price_3']);
            $row[] = number_format($item['price_2']);
            $row[] = number_format($item['price_1']);
            $row[] = number_format($item['price_4']);
            $row[] = number_format($item['price_start']);

            // $row[] = number_format($item['price_10']);
            // $row[] = number_format($item['price_3']);

            if($item['discount']){
                $total_price = $item['price_9'] - (($item['price_9']*$item['discount']/100)*100)/100;
            }else{
                $total_price = 0;
            }

            // $row[] = number_format($total_price);

            // $row[] = $item['desc'];

            //  if(isset($item['cat_id']) and in_array($item['cat_id'] , array(ACCESS_CAT_ID , DIGITAL_CAT_ID)))
            //     $invoice_description = $item['desc_name'];

            // else if(isset($item['cat_id']) and in_array($item['cat_id'] , array(PHONE_CAT_ID)))
            // {
            //     $invoice_description = '';

            //     foreach ($colors as $key => $color) {
            //         $invoice_description .= '' . $item['desc'];
            //         $invoice_description .= ' ' . $goodColors[$color] . ' - ' . $item['name'] .  $goodColorsAllArray[$color]['short_name']   .', ';
            //     }
            // }

            // $row[] = $invoice_description ? $invoice_description : '';
            $row[] = $item['add_time'];
            $row[] = $item['update_time'];

             $product_status = '';
            switch ($item['product_status']) {
                case '1':
                    $product_status = 'Active';
                    break;
                case '2':
                    $product_status = 'Pre EOL';
                    break;
                case '3':
                    $product_status = 'EOL';
                    break;
            }

            $row[] = $product_status;


        fputcsv($output, $row);
        $no++;
        }

        exit;
    }

    private function _exportExcelProductListBarCode($data){
         set_time_limit(0);
        error_reporting(0);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $filename = 'Products_list_BarCode_'.date('d/m/Y');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        echo chr(239) . chr(187) . chr(191); 
        $output = fopen('php://output', 'w');

        $heads = array(
             'No.',
            'Category',
            'Product Name',
            'Product Color',
            'Short Code',
            'Match Code'
        );

        fputcsv($output, $heads);
                $no = 1;
        foreach ($data as $item) {
            $row[] = $no;
            $row[] = $item['category'];
            $row[] = $item['product_name'];
            $row[] = $item['product_color'];
            $row[] = $item['short_code'];
            $row[] = $item['match_code'];

        fputcsv($output, $row);
        $no++;
        }
         exit;
    }

	/***********************/

	public function categoryAction()
	{
		$QGoodCategory  = new Application_Model_GoodCategory();
		$goodCategories = $QGoodCategory->fetchAll();
		$this->view->goodCategories = $goodCategories;

		$flashMessenger = $this->_helper->flashMessenger;

		$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
		$this->view->messages_success = $messages_success;
		$messages                     = $flashMessenger->setNamespace('error')->getMessages();
		$this->view->messages         = $messages;

		$this->_helper->viewRenderer->setRender('category/list');
	}

	public function categoryCreateAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id) { // load for editing
			$QGoodCategory      = new Application_Model_GoodCategory();
			$goodCategoryRowSet = $QGoodCategory->find($id);
			$category       = $goodCategoryRowSet->current();

			$this->view->category = $category;
		}

		$flashMessenger = $this->_helper->flashMessenger;
		$messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

		$this->_helper->viewRenderer->setRender('category/category-create');
	}

	public function categorySaveAction()
	{
		$flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {
			$id       = $this->getRequest()->getParam('id');
			$name     = $this->getRequest()->getParam('name', '');

			if ($name == '') {
	            $flashMessenger->setNamespace('error')->addMessage('Invalid Name');
	            $this->_redirect(HOST.'product/category-create');
			}

			$data = array_filter( array(
				'name'     => $name,
			));

			$QGoodCategory = new Application_Model_GoodCategory();

			if ($id) { // save
				$where = $QGoodCategory->getAdapter()->quoteInto('id = ?', $id);
				$QGoodCategory->update($data, $where);
			} else { // create new
				$id = $QGoodCategory->insert($data);
			}

			//remove cache
            $cache = Zend_Registry::get('cache');
            $cache->remove('good_category_cache');

			$flashMessenger->setNamespace('success')->addMessage('Success');
			$this->_redirect(HOST.'product/category');

        } else {
        	$flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
        	$this->_redirect(HOST.'product/category-create');
        }
	}

	public function categoryDeleteAction()
	{
		$flashMessenger   = $this->_helper->flashMessenger;
		$id = $this->getRequest()->getParam('id');
		$flag = FALSE;

		if ($id) {
			$QGoodCategory = new Application_Model_GoodCategory();
			$goodCategoryRowSet = $QGoodCategory->find($id);
			$category = $goodCategoryRowSet->current();

			if ($category) {
				$where = $QGoodCategory->getAdapter()->quoteInto('id = ?', $id);
        		$QGoodCategory->delete($where);

        		//remove cache
	            $cache = Zend_Registry::get('cache');
	            $cache->remove('good_category_cache');

				$messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
				$flag = TRUE;
			}
		}

		if (!$flag)
			$messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

		$this->_redirect(HOST.'product/category');
	}

	/***********************/

	public function brandAction()
	{
		$QBrand  = new Application_Model_Brand();
		$brands = $QBrand->fetchAll();
		$this->view->brands = $brands;

		$flashMessenger = $this->_helper->flashMessenger;

		$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
		$this->view->messages_success = $messages_success;
		$messages                     = $flashMessenger->setNamespace('error')->getMessages();
		$this->view->messages         = $messages;

		$this->_helper->viewRenderer->setRender('brand/list');
	}

	public function brandCreateAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id) { // load for editing
			$QBrand      = new Application_Model_Brand();
			$brandRowSet = $QBrand->find($id);
			$brand       = $brandRowSet->current();

			$this->view->brand = $brand;
		}

		$flashMessenger = $this->_helper->flashMessenger;
		$messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

		$this->_helper->viewRenderer->setRender('brand/brand-create');
	}

	public function brandSaveAction()
	{
		$flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {
			$id       = $this->getRequest()->getParam('id');
			$name     = $this->getRequest()->getParam('name', '');

			if ($name == '') {
	            $flashMessenger->setNamespace('error')->addMessage('Invalid Name');
	            $this->_redirect(HOST.'product/brand-create');
			}

			$data = array_filter( array(
				'name'     => $name,
			));

			$QBrand = new Application_Model_Brand();

			if ($id) { // save
				$where = $QBrand->getAdapter()->quoteInto('id = ?', $id);
				$QBrand->update($data, $where);
			} else { // create new
				$id = $QBrand->insert($data);
			}

			//remove cache
            $cache = Zend_Registry::get('cache');
            $cache->remove('brand_cache');

			$flashMessenger->setNamespace('success')->addMessage('Success');
			$this->_redirect(HOST.'product/brand');

        } else {
        	$flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
        	$this->_redirect(HOST.'product/brand-create');
        }
	}

	public function brandDeleteAction()
	{
		$flashMessenger   = $this->_helper->flashMessenger;
		$id = $this->getRequest()->getParam('id');
		$flag = FALSE;

		if ($id) {
			$QBrand = new Application_Model_Brand();
			$brandRowSet = $QBrand->find($id);
			$brand = $brandRowSet->current();

			if ($brand) {
				$where = $QBrand->getAdapter()->quoteInto('id = ?', $id);
        		$QBrand->delete($where);

        		//remove cache
	            $cache = Zend_Registry::get('cache');
	            $cache->remove('brand_cache');

				$messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
				$flag = TRUE;
			}
		}

		if (!$flag)
			$messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

		$this->_redirect(HOST.'product/brand');
	}

	/***********************/

	public function colorAction()
	{
		$QColor  = new Application_Model_GoodColor();
		$colors = $QColor->fetchAll();
		$this->view->colors = $colors;

		$flashMessenger = $this->_helper->flashMessenger;

		$messages_success             = $flashMessenger->setNamespace('success')->getMessages();
		$this->view->messages_success = $messages_success;
		$messages                     = $flashMessenger->setNamespace('error')->getMessages();
		$this->view->messages         = $messages;

		$this->_helper->viewRenderer->setRender('color/list');
	}

	public function colorCreateAction()
	{
		$id = $this->getRequest()->getParam('id');

		if ($id) { // load for editing
			$QColor      = new Application_Model_GoodColor();
			$colorRowSet = $QColor->find($id);
			$color       = $colorRowSet->current();

			$this->view->color = $color;
		}

		$flashMessenger = $this->_helper->flashMessenger;
		$messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

		$this->_helper->viewRenderer->setRender('color/color-create');
	}

	public function colorSaveAction()
	{
		$flashMessenger = $this->_helper->flashMessenger;

        if ($this->getRequest()->getMethod() == 'POST') {
			$id       = $this->getRequest()->getParam('id');
			$name     = $this->getRequest()->getParam('name', '');

			if ($name == '') {
	            $flashMessenger->setNamespace('error')->addMessage('Invalid Name');
	            $this->_redirect(HOST.'product/color-create');
			}

			$data = array_filter( array(
				'name'     => $name,
			));

			$QGoodColor = new Application_Model_GoodColor();

			if ($id) { // save
				$where = $QGoodColor->getAdapter()->quoteInto('id = ?', $id);
				$QGoodColor->update($data, $where);
			} else { // create new
				$id = $QGoodColor->insert($data);
			}

			//remove cache
            $cache = Zend_Registry::get('cache');
            $cache->remove('good_color_cache');
            $cache->remove('good_cache');
            $cache->remove('good_2_cache');
            $cache->remove('goodsdescription_cache');

			$flashMessenger->setNamespace('success')->addMessage('Success');
			$this->_redirect(HOST.'product/color');

        } else {
        	$flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
        	$this->_redirect(HOST.'product/color-create');
        }
	}

	public function colorDeleteAction()
	{
		$flashMessenger   = $this->_helper->flashMessenger;
		$id = $this->getRequest()->getParam('id');
		$flag = FALSE;

		if ($id) {
			$QGoodColor = new Application_Model_GoodColor();
			$colorRowSet = $QGoodColor->find($id);
			$color = $colorRowSet->current();

			if ($color) {
				$where = $QGoodColor->getAdapter()->quoteInto('id = ?', $id);
        		$QGoodColor->delete($where);

        		//remove cache
	            $cache = Zend_Registry::get('cache');
	            $cache->remove('good_color_cache');
                $cache->remove('good_cache');
                $cache->remove('good_2_cache');
                $cache->remove('goodsdescription_cache');

				$messages_success = $flashMessenger->setNamespace('success')->addMessage('Success');
				$flag = TRUE;
			}
		}

		if (!$flag)
			$messages = $flashMessenger->setNamespace('error')->addMessage('Invalid ID');

		$this->_redirect(HOST.'product/color');
	}

    /**
     * list hàng tặng kèm
     * @return [type] [description]
     */
    public function bundleGiftAction()
    {
        $QGoodBonus = new Application_Model_BundleGift();
        $page = $this->getRequest()->getParam('page', 1);
        $desc = $this->getRequest()->getParam('desc', 0);
        $sort = $this->getRequest()->getParam('sort', '');
        $good_id = $this->getRequest()->getParam('good_id');

        $limit = LIMITATION;
        $total = 0;

        $params = array(
            'desc'    => $desc,
            'sort'    => $sort,
            'good_id' => $good_id,
            );

        $bonus = $QGoodBonus->fetchPagination($page, $limit, $total, $params);
        $bonus_array = array();

        foreach ($bonus as $key => $value) {
            $bonus_array[] = array(
                'id'      => $value['id'],
                'good_id' => $value['good_id'],
                'gift_id' => explode(',', $value['gift_id']),
                );
        }

        $QGood = new Application_Model_Good();

        $this->view->bonus  = $bonus_array;
        $this->view->goods  = $QGood->get_cache();
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'product/bundle-gift/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('bundle-gift/list');
    }

// Get Discount
// Get Discount
// Get Discount
    public function discountAction()
    {
        $QDiscount = new Application_Model_Discount();
        $page = $this->getRequest()->getParam('page', 1);
        $desc = $this->getRequest()->getParam('desc', 0);
        $sort = $this->getRequest()->getParam('sort', '');
        $good_id = $this->getRequest()->getParam('good_id');

        $limit = LIMITATION;
        $total = 0;

        $params = array(
            'desc'    => $desc,
            'sort'    => $sort,
            'good_id' => $good_id,
            );

        $discount = $QDiscount->fetchPagination($page, $limit, $total, $params);
        $discount_array = array();

        foreach ($discount as $key => $value) {
            $discount_array[] = array(
                'id'      => $value['id'],
                'good_id' => $value['good_id'],
                'discount' => $value['discount'],
                'name'  => $value['name'],
                );
        }

        $QGood = new Application_Model_Good();

        $this->view->discount  = $discount_array;
        $this->view->goods  = $QGood->get_cache();
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'product/discount/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

        $this->_helper->viewRenderer->setRender('discount/list');
    }

    public function discountCreateAction()
    {
        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();
        $this->_helper->viewRenderer->setRender('discount/create');
    }

     public function discountSaveAction()
    {
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $id            = $this->getRequest()->getParam('id');
        $good_id       = $this->getRequest()->getParam('good_id');
        $name          = $this->getRequest()->getParam('name');
        $discount      = $this->getRequest()->getParam('discount');

        $flashMessenger = $this->_helper->flashMessenger;

        $QDiscount = new Application_Model_Discount();

         if ($id) {
            $where   = array();
            $where[] = $QDiscount->getAdapter()->quoteInto('id <> ?', $id);
            $where[] = $QDiscount->getAdapter()->quoteInto('good_id = ?', $good_id);

            $data = array(
                'good_id'      => $good_id,
                'name'      => $name,
                'discount' => $discount,

                'update_at'   => date('Y-m-d H:i:s'),
                'update_by'   => $userStorage->id,
                );

            $where = $QDiscount->getAdapter()->quoteInto('id = ?', $id);
            $QDiscount->update($data, $where);

        } else {

            $QDiscount = new Application_Model_Discount();
            $where = $QDiscount->getAdapter()->quoteInto('good_id = ?', $good_id);

            $data = array(
                'good_id'      => $good_id,
                'name'      => $name,
                'discount' => $discount,
                'create_at'   => date('Y-m-d H:i:s'),
                'create_by'   => $userStorage->id,
                );

            $QDiscount->insert($data);
        }

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'product/discount');
    }

    public function discountEditAction()
    {
        $id = $this->getRequest()->getParam('id');

        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/discount');
        }

        $QDiscount = new Application_Model_Discount();
        $gift = $QDiscount->find($id);
        $gift = $gift->current();


        $this->view->bundle_gift = array(
            'id' => $gift['id'],
            'good_id' => $gift['good_id'],
            'name' => $gift['name'],
            'discount' => $gift['discount'],
            );

        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();
        $this->view->id = $id;

        $this->_helper->viewRenderer->setRender('discount/create');
    }
     public function discountDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/discount');
        }

        $QDiscount = new Application_Model_Discount();
        $gift = $QDiscount->find($id);
        $gift = $gift->current();

        if (!$gift) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/discount');
        }

        $where = $QDiscount->getAdapter()->quoteInto('id = ?', $id);
        $QDiscount->delete($where);

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'product/discount');
    }

// End Discount
// End Discount
// End Discount

    public function bundleGiftCreateAction()
    {
        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();
        $this->_helper->viewRenderer->setRender('bundle-gift/create');
    }

    public function bundleGiftEditAction()
    {
        $id = $this->getRequest()->getParam('id');

        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/bundle-gift');
        }

        $QBundleGift = new Application_Model_BundleGift();
        $gift = $QBundleGift->find($id);
        $gift = $gift->current();

        if (!$gift) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/bundle-gift');
        }

        $this->view->bundle_gift = array(
            'id' => $gift['id'],
            'good_id' => $gift['good_id'],
            'gift_id' => explode(',', $gift['gift_id']),
            );

        $QGood = new Application_Model_Good();
        $this->view->goods = $QGood->get_cache();
        $this->view->id = $id;

        $this->_helper->viewRenderer->setRender('bundle-gift/create');
    }

    public function bundleGiftSaveAction()
    {
        $id            = $this->getRequest()->getParam('id');
        $good_id       = $this->getRequest()->getParam('good_id');
        $bonus_good_id = $this->getRequest()->getParam('bonus_good_id');
        $bundle_check  = $this->getRequest()->getParam('bundle_check', 0);

        $flashMessenger = $this->_helper->flashMessenger;

        if (!$good_id || !$bonus_good_id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid Product.');

            $this->_redirect(HOST.'product/bundle-gift-create' . ( $id ? ( '?id='.$id ) : '' ) );
        }

        $bonus_good_id = is_array($bonus_good_id) ? array_unique($bonus_good_id) : array();

        if ($id) {
            $QBundleGift = new Application_Model_BundleGift();

            $gift = $QBundleGift->find($id);
            $gift = $gift->current();

            if (!$gift) {
                $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
                $this->_redirect(HOST.'product/bundle-gift');
            }

            $where   = array();
            $where[] = $QBundleGift->getAdapter()->quoteInto('id <> ?', $id);
            $where[] = $QBundleGift->getAdapter()->quoteInto('good_id = ?', $good_id);
            $gift    = $QBundleGift->fetchRow($where);

            if ($gift) {
                $flashMessenger->setNamespace('error')->addMessage('Product existed.');
                $this->_redirect(HOST.'product/bundle-gift');
            }

            $data = array(
                'good_id'      => $good_id,
                'gift_id'      => implode(',', $bonus_good_id),
                'bundle_check' => $bundle_check,
                );

            $where = $QBundleGift->getAdapter()->quoteInto('id = ?', $id);
            $QBundleGift->update($data, $where);

        } else {
            $QBundleGift = new Application_Model_BundleGift();
            $where = $QBundleGift->getAdapter()->quoteInto('good_id = ?', $good_id);
            $gift = $QBundleGift->fetchRow($where);

            if ($gift) {
                $flashMessenger->setNamespace('error')->addMessage('Product existed.');
                $this->_redirect(HOST.'product/bundle-gift');
            }

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $data = array(
                'good_id'      => $good_id,
                'gift_id'      => implode(',', $bonus_good_id),
                'bundle_check' => $bundle_check,
                'created_at'   => time(),
                'created_by'   => $userStorage->id,
                );

            $QBundleGift->insert($data);
        }

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'product/bundle-gift');
    }

    public function bundleGiftDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $flashMessenger = $this->_helper->flashMessenger;

        if (!$id) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/bundle-gift');
        }

        $QBundleGift = new Application_Model_BundleGift();
        $gift = $QBundleGift->find($id);
        $gift = $gift->current();

        if (!$gift) {
            $flashMessenger->setNamespace('error')->addMessage('Invalid ID');
            $this->_redirect(HOST.'product/bundle-gift');
        }

        $where = $QBundleGift->getAdapter()->quoteInto('id = ?', $id);
        $QBundleGift->delete($where);

        $flashMessenger->setNamespace('success')->addMessage('Success');
        $this->_redirect(HOST.'product/bundle-gift');
    }

    public function holdProductAction()
    {
        $sort               = $this->getRequest()->getParam('sort', '');
        $desc               = $this->getRequest()->getParam('desc', 1);
        $name               = $this->getRequest()->getParam('name');
        $details            = $this->getRequest()->getParam('details');
        $cat_id             = $this->getRequest()->getParam('cat_id');
        $good_id            = $this->getRequest()->getParam('good_id');
        $good_color         = $this->getRequest()->getParam('good_color');
        $hold               = $this->getRequest()->getParam('hold');
        

        $export  = $this->getRequest()->getParam('export', 0);

        $page  = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $total = 0;

        $params = array(
            'name'    => $name,
            'details' => $details,
            'cat_id'  => $cat_id,
            'good_id' => $good_id,
            'good_color' => $good_color,
            'sort'    => $sort,
            'desc'    => $desc,
            );
        
        $QGood          = new Application_Model_Good();
        $QGoodColor     = new Application_Model_GoodColor();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QBrand         = new Application_Model_Brand();
        $QGoodHoldAll   = new Application_Model_GoodHoldAll();
        $QGoodHoldDis   = new Application_Model_GoodHoldDis();
        
        $goodColors     = $QGoodColor->get_cache();
        $goodCategories = $QGoodCategory->get_cache();
        $brands         = $QBrand->get_cache();
        if ($hold == 1) {
            $all =$QGoodHoldDis->GetGoodId();
            $dis =$QGoodHoldAll->GetGoodId();

           
            $params['hold'] = array_merge($all,$dis);
           
        }
        // Xuất excel
        if ( isset($export) && $export ) {
            $goods = $QGood->fetchPagination($page, null, $total, $params);
            $this->_exportExcel($goods);
        } else {

            $goods = $QGood->fetchPagination($page, $limit, $total, $params);
        }

        $this->view->goods          = $goods;
        $this->view->colors         = $goodColors;
        $this->view->good_categories = $goodCategories;
        $this->view->brands         = $brands;

        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'product/hold-product/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        if($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setRender('hold-product/partials/list');
        } else {
            $flashMessenger               = $this->_helper->flashMessenger;
            $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
            $this->view->messages_success = $messages_success;

            $messages                     = $flashMessenger->setNamespace('error')->getMessages();
            $this->view->messages         = $messages;

            $this->_helper->viewRenderer->setRender('hold-product/list');
        }


        
    }

    public function addHoldProductAction()
    {
        $good_id = $this->getRequest()->getParam('good_id');
        $sort    = $this->getRequest()->getParam('sort', '');
        $desc    = $this->getRequest()->getParam('desc', 1);

        $page  = $this->getRequest()->getParam('page', 1);
        $limit = LIMITATION;
        $total = 0;

        if ($good_id) { // load for editing
            $QGood      = new Application_Model_Good();
            $goodRowSet = $QGood->find($good_id);
            $good       = $goodRowSet->current();

            $this->view->good = $good;
        }
        $params['good_id'] = $good_id;
        $QGoodColor     = new Application_Model_GoodColor();
        $QBrand         = new Application_Model_Brand();
        $QGoodCategory  = new Application_Model_GoodCategory();
        $QBrand         = new Application_Model_Brand();
        $QGoodHoldAll   = new Application_Model_GoodHoldAll();
        $QGoodHoldDis   = new Application_Model_GoodHoldDis();
        $QWarehouse     = new Application_Model_Warehouse();
        $goodColors     = $QGoodColor->get_cache();
        $goodCategories = $QGoodCategory->get_cache();
        $brands         = $QBrand->get_cache();
        $hold_all       = $QGoodHoldAll->fetchGoodHoldAll($page, $limit, $total, $params);
        
        if ($hold_all) {
            $this->view->hold_all     = $hold_all;
        }else{

            $hold_dis       = $QGoodHoldDis->fetchGoodHoldDis($page, $limit, $total, $params);
            
            $this->view->hold_dis     = $hold_dis;

        }
        
        $warehouses_cached = $QWarehouse->get_cache();
        $this->view->warehouses = $warehouses_cached;

        
        $this->view->goodColors     = $goodColors;
        $this->view->goodCategories = $goodCategories;
        $this->view->brands         = $brands;
        $this->view->desc   = $desc;
        $this->view->sort   = $sort;
        $this->view->params = $params;
        $this->view->limit  = $limit;
        $this->view->total  = $total;
        $this->view->url    = HOST.'product/add-hold-product/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);
        $flashMessenger               = $this->_helper->flashMessenger;
        $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        $messages                     = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages         = $messages;

        $this->_helper->viewRenderer->setRender('hold-product/add-hold-product');
    }

    public function listDistributorAction(){
        $this->_helper->layout->disableLayout();
        // $this->_helper->viewRenderer->setNoRender(true);
        $QDistributor               = new Application_Model_Distributor();
        $QBlackListReason           = new Application_Model_BlackListReason();
        $QDistributorBlackList      = new Application_Model_DistributorBlackList();  

        $rank                       = $this->getRequest()->getParam('rank');

        $blacklist                  = $QDistributor->blackListDistributor($rank );


        $reason                     = $QBlackListReason->get_cache();
        $this->view->blacklist      = $blacklist;
        $this->view->reason         = $reason;
        $this->_helper->viewRenderer->setRender('hold-product/list-distributor');

    }
    public function unblockProductAction(){
        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $QGoodHoldAll = new Application_Model_GoodHoldAll();
        $QGoodHoldDis = new Application_Model_GoodHoldDis(); 

        $hold_type                  = $this->getRequest()->getParam('type');
        $id                         = $this->getRequest()->getParam('id');
        $good_id                    = $this->getRequest()->getParam('good_id');
        // die(print_r($_POST));
        $data = array(
            'unblock_by'       => $userStorage->id ,
            'unblock_at'       => date('Y-m-d H:i:s') ,
            'status'           => 1
            );
        try {
        if ($hold_type == 1) {
            for ($i=0; $i < count($id) ; $i++) { 
                $where = $QGoodHoldAll->getAdapter()->quoteInto('id = ?', $id[$i]);
                $QGoodHoldAll->update($data, $where);
            }
        }
        if ($hold_type == 2) {
            for ($i=0; $i < count($id) ; $i++) { 
                $where = $QGoodHoldDis->getAdapter()->quoteInto('id = ?', $id[$i]);
                $QGoodHoldDis->update($data, $where);
            }
        }

                $flashMessenger->setNamespace('success')->addMessage('Success');
                $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
                 

        } catch(Exception $e){
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
                

        }                  
    

    }
    public function holdProductSaveAction()
    {
        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        if ($this->getRequest()->getMethod() == 'POST') {
            try {
                $good_id       = $this->getRequest()->getParam('good_id');
                $d_id     = $this->getRequest()->getParam('d_id');
                $color     = $this->getRequest()->getParam('color');
                $hold     = $this->getRequest()->getParam('hold');
                $warehouse_id     = $this->getRequest()->getParam('warehouse_id');

                $QGoodHoldAll = new Application_Model_GoodHoldAll();
                $QGoodHoldDis = new Application_Model_GoodHoldDis();
                if (!$color) {
                        $flashMessenger->setNamespace('error')->addMessage('กรุณาเลือกสี');
                        $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
                       }       
                if($hold==1){
                    if ($color[0]==0) {
                        $data = array(
                            'good_id'       => $good_id,
                            'type_all'      => 1 ,
                            'hold_by'       => $userStorage->id ,
                            'hold_at'       => date('Y-m-d H:i:s'),
                            'warehouse_id'  => $warehouse_id
                            );
                           $QGoodHoldAll->insert($data);
                    }else{
                       for ($i=0; $i < count($color); $i++) { 

                           $data = array(
                            'good_id'       => $good_id,
                            'good_color'    => $color[$i],
                            'hold_by'       => $userStorage->id ,
                            'hold_at'       => date('Y-m-d H:i:s'),
                            'warehouse_id'  => $warehouse_id

                            );
                           $QGoodHoldAll->insert($data);
                       }
                    }
                          
                }elseif ($hold == 2) {
                    if ($color[0]==0) {
                        for ($i=0; $i < count($d_id) ; $i++) { 
                           $data = array(
                            'd_id'          => $d_id[$i],
                            'good_id'       => $good_id,
                            'type_all'      => 1,
                            'hold_by'       => $userStorage->id ,
                            'hold_at'       => date('Y-m-d H:i:s'),
                            'warehouse_id'  => $warehouse_id

                            );
                           $QGoodHoldDis->insert($data);
                        }
                        
                    }else{
                        for ($i=0; $i < count($color); $i++) { 
                           
                           for ($j=0; $j < count($d_id); $j++) { 
                            $data = array(
                            'good_id'       => $good_id,
                            'good_color'    => $color[$i] ,
                            'd_id'    => $d_id[$j],
                            'hold_by'       => $userStorage->id ,
                            'warehouse_id'  => $warehouse_id,
                            'hold_at'       => date('Y-m-d H:i:s'), 
                            );
                              $QGoodHoldDis->insert($data);
                           }
                           
                       }
                    }
                }

                $flashMessenger->setNamespace('success')->addMessage('Success');
                $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
            } catch(Exception $e){
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
                $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
            }

        } else {
            $flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
            $this->_redirect(HOST.'product/add-hold-product?good_id='.$good_id);
        }
    }

//---------------------Staff Order----------------------------------//

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

    public function setupEmployeePrivilegesAction(){

        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QGood = new Application_Model_Good();
        $QGoodColor = new Application_Model_GoodColor();
        $QWarehouse = new Application_Model_Warehouse();

        $QEPQP = new Application_Model_EpPrivilegesQuotaProduct();

        $goods_cache = $QGood->get_cache();
        $good_colors_cache = $QGoodColor->get_cache();
        $warehouses_cache = $QWarehouse->get_cache();

        $res=null;
        $company_id=1;
        $get_ep_pqp = $QEPQP->get_ep_pqp($company_id);
        //print_r($get_ep_pqp);
        $company_id=2;
        $get_ep_pqp_oneplus = $QEPQP->get_ep_pqp($company_id);
        //print_r($get_ep_pqp_oneplus);

        if($get_ep_pqp && $get_ep_pqp_oneplus)
        {
           $res = array_merge($get_ep_pqp,$get_ep_pqp_oneplus); 
        }else
        {
            if($get_ep_pqp)
            {
               $res =  $get_ep_pqp;
            }else if($get_ep_pqp_oneplus)
            {
               $res =  $get_ep_pqp_oneplus;
            }
        }

        //print_r($res);
        $this->view->get_ep_pqp = $res;

        $this->view->goods_cache = $goods_cache;
        $this->view->good_colors_cache = $good_colors_cache;
        $this->view->warehouses_cache = $warehouses_cache;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;
    }

    public function saveEmployeePrivilegesAction(){

        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QEPQP = new Application_Model_EpPrivilegesQuotaProduct();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                $id = $this->getRequest()->getParam('id');
                $eol = $this->getRequest()->getParam('eol');
                $eol_price = $this->getRequest()->getParam('eol_price');
                $warehouse = $this->getRequest()->getParam('warehouse');
                $start_date = $this->getRequest()->getParam('start_date');
                $remark = $this->getRequest()->getParam('remark');

                if(!$eol){
                    $eol_price = 0;
                }

                if($eol_price < 1){
                    $eol_price = 0;
                }

                $data_update = array(
                    // 'EOL'              => $eol,
                    'price'            => $eol_price,
                    'warehouse_id'     => $warehouse,
                    'start_date'       => $start_date,
                    'remark'           => $remark,
                    'update_by'        => $userStorage->id,
                    'update_date'      => date('Y-m-d H:i:s') 
                );

                $where_update = $QEPQP->getAdapter()->quoteInto('ids = ?', $id);
                $status_update = $QEPQP->update($data_update, $where_update);

                $db->commit();

                if($status_update){
                    echo json_encode(['status' => 200,'message' => 'Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not save.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }

    public function createEmployeePrivilegesAction(){

        $flashMessenger = $this->_helper->flashMessenger;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $company_id = $this->getRequest()->getParam('company_id');
        $db = $this->_initConfig($company_id);

        $QEPQP = new Application_Model_EpPrivilegesQuotaProduct();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');

                $db->beginTransaction();
                
                $cat_id = $this->getRequest()->getParam('cat_id');
                $good_id = $this->getRequest()->getParam('good_id');
                $good_color = $this->getRequest()->getParam('good_color');
                $special_product = $this->getRequest()->getParam('special_product');

                $eol = $this->getRequest()->getParam('eol');
                $eol_price = $this->getRequest()->getParam('eol_price');
                $warehouse = $this->getRequest()->getParam('warehouse');
                $start_date = $this->getRequest()->getParam('start_date');
                $remark = $this->getRequest()->getParam('remark');

                if(!$special_product){
                    $special_product = null;
                }

                if(!$eol){
                    $eol_price = 0;
                }

                if($eol_price < 1){
                    $eol_price = 0;
                }

                $where = [];
                $where[] = $QEPQP->getAdapter()->quoteInto('company_id = ?', $company_id);
                $where[] = $QEPQP->getAdapter()->quoteInto('cat_id = ?', $cat_id);
                $where[] = $QEPQP->getAdapter()->quoteInto('good_id = ?', $good_id);
                $where[] = $QEPQP->getAdapter()->quoteInto('special_product = ?', $special_product);
                $where[] = $QEPQP->getAdapter()->quoteInto('good_color = ?', $good_color);
                $where[] = $QEPQP->getAdapter()->quoteInto('warehouse_id = ?', $warehouse);
                $where[] = $QEPQP->getAdapter()->quoteInto('EOL = ?', $eol);
                $get_data = $QEPQP->fetchAll($where);

                if(count($get_data) > 0){

                    if(count($get_data) > 1){
                        $db->rollback();
                        echo json_encode(['status' => 400,'message' => 'Can not create : Have order more 1 row, Please contact Team IT.']);
                        exit();
                    }

                    if($get_data[0]['active']){
                        $db->rollback();
                        echo json_encode(['status' => 400,'message' => 'Can not create : Have order on list!']);
                        exit();
                    }else{

                        $id = $get_data[0]['ids'];

                        $data_update = array(
                            'price'            => $eol_price,
                            'warehouse_id'     => $warehouse,
                            'start_date'       => $start_date,
                            'remark'           => $remark,
                            'special_product'  => $special_product,
                            'update_by'        => $userStorage->id,
                            'update_date'      => date('Y-m-d H:i:s'),
                            'active'           => 1
                        );

                        $where_update = $QEPQP->getAdapter()->quoteInto('ids = ?', $id);
                        $status = $QEPQP->update($data_update, $where_update);

                    }

                }else{

                    $data = array(
                        'company_id'       => $company_id,
                        'cat_id'           => $cat_id,
                        'good_id'          => $good_id,
                        'good_color'       => $good_color,
                        'special_product'  => $special_product,
                        'EOL'              => $eol,
                        'price'            => $eol_price,
                        'warehouse_id'     => $warehouse,
                        'start_date'       => $start_date,
                        'remark'           => $remark,
                        'create_by'        => $userStorage->id,
                        'create_date'      => date('Y-m-d H:i:s'),
                        'active'           => 1
                    );
                    //print_r($data);die;
                    $status = $QEPQP->insert($data);
                }

                $db->commit();

                if($status){
                    $flashMessenger->setNamespace('success')->addMessage('Created Done.');
                    echo json_encode(['status' => 200,'message' => 'Created Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not create.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }

    public function delEmployeePrivilegesAction(){
        //print_r($_POST);die;
        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $company_id = $this->getRequest()->getParam('company_id');
        $this->_initConfig($company_id);
        $QEPQP = new Application_Model_EpPrivilegesQuotaProduct();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                $id = $this->getRequest()->getParam('id');

                $data_update = array(
                    'active'           => 0,
                    'update_by'        => $userStorage->id,
                    'update_date'      => date('Y-m-d H:i:s')
                );

                $where_update = $QEPQP->getAdapter()->quoteInto('ids = ?', $id);
                $status_update = $QEPQP->update($data_update, $where_update);

                $db->commit();

                if($status_update){
                    $flashMessenger->setNamespace('success')->addMessage('Deleted Done.');
                    echo json_encode(['status' => 200,'message' => 'Deleted Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not delete.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }

    public function setupDiscountEmployeePrivilegesAction(){


        $flashMessenger = $this->_helper->flashMessenger;

        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QWarehouse = new Application_Model_Warehouse();
        $QDistributor = new Application_Model_Distributor();

        $QEPD = new Application_Model_EpPrivilegesDiscount();

        $warehouses_cache = $QWarehouse->get_cache();
        $distributors_cache = $QDistributor->get_cache();

        $QBank = new Application_Model_Bank();
        $banks = $QBank->fetchAll(null,'name asc');

        //$get_ep_pd = $QEPD->get_ep_pd();

        $res=null;
        $company_id=1;
        $rs_ep_pd = $QEPD->get_ep_pd($company_id);
        $company_id=2;
        $rs_ep_pd_oneplus = $QEPD->get_ep_pd($company_id);
        $res = array_merge($rs_ep_pd,$rs_ep_pd_oneplus);
        
        $this->view->get_ep_pd = $res;

        $this->view->warehouses_cache = $warehouses_cache;
        $this->view->distributors_cache = $distributors_cache;

        $this->view->banks = $banks;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;
    }

    public function saveDiscountEmployeePrivilegesAction(){

        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $company_id = $this->getRequest()->getParam('company_id');
        $db = $this->_initConfig($company_id);
        $QEPD = new Application_Model_EpPrivilegesDiscount();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                $id = $this->getRequest()->getParam('id');

                $qty_per_year = $this->getRequest()->getParam('qty_per_year');
                $start_use_day = $this->getRequest()->getParam('start_use_day');
                $period_limit_day = $this->getRequest()->getParam('period_limit_day');
                $reset_date = $this->getRequest()->getParam('reset_date');


                $data_update = array(
                    'qty_per_year'     => $qty_per_year,
                    'start_use_day'    => $start_use_day,
                    'period_limit_day' => $period_limit_day,
                    'reset_date'       => $reset_date,
                    'update_by'        => $userStorage->id,
                    'update_date'      => date('Y-m-d H:i:s') 
                );

                $where_update = $QEPD->getAdapter()->quoteInto('discount_id = ?', $id);
                $status_update = $QEPD->update($data_update, $where_update);

                $db->commit();

                if($status_update){
                    echo json_encode(['status' => 200,'message' => 'Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not save.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }

    public function createDiscountEmployeePrivilegesAction(){

        $flashMessenger = $this->_helper->flashMessenger;
        $company_id = $this->getRequest()->getParam('company_id');
        $db = $this->_initConfig($company_id);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $QEPD = new Application_Model_EpPrivilegesDiscount();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');
                $db->beginTransaction();

                
                $payment_type = $this->getRequest()->getParam('payment_type');
                $discount = $this->getRequest()->getParam('discount');

                $company_id = $this->getRequest()->getParam('company_id');
                $cat_id = $this->getRequest()->getParam('cat_id',0);
                $good_id = $this->getRequest()->getParam('good_id');
                $special_product = $this->getRequest()->getParam('special_product');

                $warehouse = $this->getRequest()->getParam('warehouse');
                $bank = $this->getRequest()->getParam('bank');
                $distributor = $this->getRequest()->getParam('distributor');

                $qty_per_year = $this->getRequest()->getParam('qty_per_year');
                $start_use_day = $this->getRequest()->getParam('start_use_day');
                $period_limit_day = $this->getRequest()->getParam('period_limit_day');
                $reset_date = $this->getRequest()->getParam('reset_date');

                $where = [];
                $where[] = $QEPD->getAdapter()->quoteInto('company_id = ?', $company_id);
                $where[] = $QEPD->getAdapter()->quoteInto('discount_type = ?', $discount);
                $where[] = $QEPD->getAdapter()->quoteInto('payment_type = ?', $payment_type);
                $where[] = $QEPD->getAdapter()->quoteInto('warehouse_id = ?', $warehouse);
                $where[] = $QEPD->getAdapter()->quoteInto('bank_id = ?', $bank);
                $where[] = $QEPD->getAdapter()->quoteInto('distributor_id = ?', $distributor);
                $where[] = $QEPD->getAdapter()->quoteInto('cat_id = ?', $cat_id);
                $where[] = $QEPD->getAdapter()->quoteInto('good_id = ?', $good_id);
                $where[] = $QEPD->getAdapter()->quoteInto('special_product = ?', $special_product);
                $get_data = $QEPD->fetchAll($where);

                if($good_id==''){
                    $good_id=null;
                }

                if($special_product=='' || $special_product=='null'){
                    $special_product=null;
                    $good_id=null;
                }

                if(count($get_data) > 0){

                    if(count($get_data) > 1){
                        $db->rollback();
                        echo json_encode(['status' => 400,'message' => 'Can not create : Have order more 1 row, Please contact Team IT.']);
                        exit();
                    }

                    if($get_data[0]['active']){
                        $db->rollback();
                        echo json_encode(['status' => 400,'message' => 'Can not create : Have order on list!']);
                        exit();
                    }else{

                        $id = $get_data[0]['discount_id'];

                        $data_update = array(
                            'qty_per_year'      => $qty_per_year,
                            'start_use_day'     => $start_use_day,

                            'cat_id'            => $cat_id,
                            'good_id'           => $good_id,
                            'special_product'   => $special_product,

                            'period_limit_day'  => $period_limit_day,
                            'reset_date'        => $reset_date,
                            'update_by'         => $userStorage->id,
                            'update_date'       => date('Y-m-d H:i:s'),
                            'active'            => 1
                        );

                        $where_update = $QEPD->getAdapter()->quoteInto('discount_id = ?', $id);
                        $status = $QEPD->update($data_update, $where_update);

                    }

                }else{

                    $data = array(
                        'company_id'        => $company_id,
                        'cat_id'            => $cat_id,
                        'good_id'           => $good_id,
                        'special_product'   => $special_product,
                        'discount_type'     => $discount,
                        'payment_type'      => $payment_type,
                        'qty_per_year'      => $qty_per_year,
                        'start_use_day'     => $start_use_day,
                        'period_limit_day'  => $period_limit_day,
                        'reset_date'        => $reset_date,
                        'warehouse_id'      => $warehouse,
                        'bank_id'           => $bank,
                        'distributor_id'    => $distributor,
                        'create_by'         => $userStorage->id,
                        'create_date'       => date('Y-m-d H:i:s'),
                        'active'            => 1
                    );

                    $status = $QEPD->insert($data);
                }

                $db->commit();

                if($status){
                    $flashMessenger->setNamespace('success')->addMessage('Created Done.');
                    echo json_encode(['status' => 200,'message' => 'Created Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not create.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }

    public function delDiscountEmployeePrivilegesAction(){
        //print_r($_POST);die;
        $flashMessenger = $this->_helper->flashMessenger;
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();
        $company_id = $this->getRequest()->getParam('company_id');

        $this->_initConfig($company_id);
        $QEPD = new Application_Model_EpPrivilegesDiscount();

        if ($this->getRequest()->getMethod() == 'POST') {
            try {

                $db = Zend_Registry::get('db');
                $db->beginTransaction();


                $id = $this->getRequest()->getParam('id');

                $data_update = array(
                    'active'           => 0,
                    'update_by'        => $userStorage->id,
                    'update_date'      => date('Y-m-d H:i:s')
                );

                $where_update = $QEPD->getAdapter()->quoteInto('discount_id = ?', $id);
                $status_update = $QEPD->update($data_update, $where_update);

                $db->commit();

                if($status_update){
                    $flashMessenger->setNamespace('success')->addMessage('Deleted Done.');
                    echo json_encode(['status' => 200,'message' => 'Deleted Done.']);
                    exit();
                }else{
                    $db->rollback();
                    echo json_encode(['status' => 400,'message' => 'Can not delete.']);
                    exit();
                }
            } catch(Exception $e){
                $db->rollback();
                echo json_encode(['status' => 400,'message' => 'Invalid Data : ' . $e->getMessage()]);
                exit();
            }

        } else {
            $db->rollback();
            echo json_encode(['status' => 400,'message' => 'This function is support type post only.']);
            exit();
        }
    }


      public function  goodBundleOnlineAction(){
       $QGoodBundleOnline = new Application_Model_GoodBundleOnline();
       $QGood = new Application_Model_Good();

       $page           = $this->getRequest()->getParam('page', 1);
       $from_date      = $this->getRequest()->getParam('from_date');
       $to_date        = $this->getRequest()->getParam('to_date');
       $good_id        = $this->getRequest()->getParam('good_id');

       $limit   = LIMITATION * 2;
       $total   = 0;
       $params = array(
        'from_date'     => $from_date,
        'to_date'       => $to_date,
        'good_id'       => $good_id
      );

       $this->view->logs   = $QGoodBundleOnline->fetchPagination($page, $limit, $total, $params);
       $this->view->params = $params;
       $this->view->limit  = $limit;
       $this->view->total  = $total;
       $this->view->url    = HOST.'product/good-bundle-online'.( $params ? '?'.http_build_query($params).'&' : '?' );
       $this->view->offset = $limit*($page-1);


       $goods              = $QGood->getPhone();
        // echo "<pre>";
        // print_r($goods);
        // die;
       $this->view->goods  = $goods;
       $this->view->good_categories = $good_categories;

       $flashMessenger = $this->_helper->flashMessenger;
       $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
       $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

       $this->_helper->viewRenderer->setRender('good-bundle-online/list');

     }
     public function goodBundleOnlineCreateAction()
     {
      $QGood = new Application_Model_Good();
      $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
      $goods = $QGood->fetchAll($where);

      $this->view->goods1 = $goods;

      $QGoodCategory = new Application_Model_GoodCategory();
      $this->view->good_categories = $QGoodCategory->get_cache();

      $QGoodColor = new Application_Model_GoodColor();
      $this->view->good_colors = $QGoodColor->get_cache();


      $this->_helper->viewRenderer->setRender('good-bundle-online/create');
    }
    public function goodBundleOnlineSaveAction()
    {
      $sn = date('YmdHis') . substr(microtime(), 2, 4);
      $from_date = $this->getRequest()->getParam('from_date');
      $to_date   = $this->getRequest()->getParam('to_date', null);
      $main_good_id    = $this->getRequest()->getParam('good_id', 0);
      $main_good_color    = $this->getRequest()->getParam('good_color', 0);
      $bundle_category    = $this->getRequest()->getParam('cat_id', 0);
      $good_bundle    = $this->getRequest()->getParam('good_bundle_id', 0);
      $good_bundle_color    = $this->getRequest()->getParam('good_bundle_color', 0);
      $good_bundle_qty    = $this->getRequest()->getParam('good_bundle_qty', 0);
      $pre_order    = $this->getRequest()->getParam('pre');
      $sn_old    = $this->getRequest()->getParam('sn');
      $userStorage = Zend_Auth::getInstance()->getStorage()->read();
      $check = count($good_bundle);
      $checkcolor = count($main_good_color);   

      try
      {
        $flashMessenger   = $this->_helper->flashMessenger;
        $db = Zend_Registry::get('db');
        $db->beginTransaction();
        $QGoodBundleOnline = new Application_Model_GoodBundleOnline();
        $QGoodBundleGiftOnline = new Application_Model_GoodBundleGiftOnline();
        $datetime = date('Y-m-d H:i:s'); 


        if ($sn_old){

          $where     = array();
          $where[]   = $QGoodBundleOnline->getAdapter()->quoteInto('sn = ?', $sn_old);
          $where[]   = $QGoodBundleOnline->getAdapter()->quoteInto('main_good_id = ?', $main_good_id);
          $where[]   = $QGoodBundleOnline->getAdapter()->quoteInto('main_good_color_id = ?', $main_good_color);
          $data2['updated_at'] = $datetime;
          $data2['status'] = 0;
          $data2['updated_by'] = $userStorage ? $userStorage->id : 0;
          $QGoodBundleOnline->update($data2,$where);
          $array = [];
          $array1 = [];

          
          for ($j=0; $j < $checkcolor ; $j++) {  

            $data = array(
              'main_good_id'    => $main_good_id,
              'sn'      => $sn ,
              'status'      => 1 ,
              'main_good_color_id' => intval($main_good_color[$j]),
              'start_date'  =>$from_date,
              'end_date'    => $to_date, 
            );


            
            $data['created_at'] = $datetime;
            $data['created_by'] = $userStorage ? $userStorage->id : 0;
            $data['updated_at'] = $datetime;
            $data['updated_by'] = $userStorage ? $userStorage->id : 0;

            if($pre_order==1){
              $data['pre_order'] = 1;
            }else{
              $data['pre_order'] = 0;
            }
            array_push($array, $data); 
          }

          for ($i=0; $i < $check ; $i++) { 
            $data1 = array(
              'sn'      => intval($sn) ,
              'bundle_good_id'    => intval($good_bundle[$i]),
              'bundle_good_cat_id'    => intval($bundle_category[$i]),
              'bundle_color_id'    => intval($good_bundle_color[$i]),
              'bundle_qty'      => intval($good_bundle_qty[$i]),
              'status'      => 1 ,
            );
            array_push($array1, $data1);   
          }

          
          $arrid = array();
          foreach ($array as $key => $value) {
            $id = $QGoodBundleOnline->insert($value);
          }
          foreach ($array1 as $key => $value1) {
            $id1 = $QGoodBundleGiftOnline->insert($value1);
          }
        }else{

          $array = [];
          $array1 = [];

          for ($j=0; $j < $checkcolor ; $j++) {  
            $data = array(
              'main_good_id'    => intval($main_good_id),
              'sn'      => intval($sn) ,
              'status'      => 1 ,
              'main_good_color_id' => intval($main_good_color[$j]),
              'start_date'  => DateTime::createFromFormat('d/m/Y', $from_date)->format('Y-m-d'),
              'end_date'    => empty($to_date) || !$to_date ? null : DateTime::createFromFormat('d/m/Y', $to_date)->format('Y-m-d'), 
            );
            $data['created_at'] = $datetime;
            $data['created_by'] = $userStorage ? $userStorage->id : 0;
            $data['updated_at'] = $datetime;
            $data['updated_by'] = $userStorage ? $userStorage->id : 0;
            if($pre_order==1){
              $data['pre_order'] = 1;
            }else{
              $data['pre_order'] = 0;
            }
            array_push($array, $data); 
          }
          for ($i=0; $i < $check ; $i++) { 
            $data1 = array(
              'sn'      => intval($sn) ,
              'bundle_good_id'    => intval($good_bundle[$i]),
              'bundle_good_cat_id'    => intval($bundle_category[$i]),
              'bundle_color_id'    => intval($good_bundle_color[$i]),
              'bundle_qty'      => intval($good_bundle_qty[$i]),
              'status'      => 1 ,
            );
            array_push($array1, $data1);   
          }
          
          $arrid = array();
          foreach ($array as $key => $value) {
            $id = $QGoodBundleOnline->insert($value);
          }
          foreach ($array1 as $key => $value1) {
            $id1 = $QGoodBundleGiftOnline->insert($value1);
          }


        }

        
        $flashMessenger->setNamespace('success')->addMessage('Done!');
        $db->commit();
      }
      catch(exception $e)
      {
        var_dump($e);exit;
        $db->rollback();
        $flashMessenger->setNamespace('error')->addMessage('Not success, please try again!');
      }


      $this->_redirect(HOST.'/product/good-bundle-online/list');
    }

    public function goodBundleOnlineEditAction()
    {
      $id = $this->getRequest()->getParam('id');
      $color_id   = $this->getRequest()->getParam('color_id');
      $sn   = $this->getRequest()->getParam('sn');
      $QGoodBundleOnline = new Application_Model_GoodBundleOnline();
      $QGoodBundleGiftOnline = new Application_Model_GoodBundleGiftOnline();
      $QGood = new Application_Model_Good();
      $QGoodColorCombined = new Application_Model_GoodColorCombined();
      $where = $QGood->getAdapter()->quoteInto('cat_id = ?', PHONE_CAT_ID);
      $good = $QGood->fetchAll($where);
      $this->view->edit = 1;

      $this->view->goods1 = $good;
      $getdetail = $QGoodBundleOnline->getdetail($sn,$id,$color_id);

      foreach ($getdetail as $key => $value) {
        $good1 = $QGood->getGood($value['bundle_good_cat_id']);
        $color1 = $QGoodColorCombined->getGoodColor($value['bundle_good_id']);
        $getdetail[$key]['bundle_good'] = $good1;
        $getdetail[$key]['bundle_color'] = $color1;
      }
      //  echo "<pre>";
      // print_r($getdetail);
      // die;
      $this->view->goods = $getdetail;



      $QGoodCategory = new Application_Model_GoodCategory();
      $this->view->good_categories = $QGoodCategory->get_cache();

      $QGoodColor = new Application_Model_GoodColor();
      $this->view->good_colors = $QGoodColor->get_cache();


      $this->_helper->viewRenderer->setRender('good-bundle-online/create');
    }



    public function checkTimeGoodBundleOnlineAction()
    {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      $from_date = $this->getRequest()->getParam('from_date');
      $to_date = $this->getRequest()->getParam('to_date');
      $good_color = $this->getRequest()->getParam('good_color');
      $good_id = $this->getRequest()->getParam('good_id');

      $QGoodBundleOnline = new Application_Model_GoodBundleOnline();

      if ($from_date!=""&&$to_date!=""&&$good_id!=""&&$good_color!=null) {
       $f = explode('/', $from_date);
       $t = explode('/', $to_date);
       $f_date = $f['2'].'-'.$f['1'].'-'.$f['0'];
       $t_date = $t['2'].'-'.$t['1'].'-'.$t['0'];
       $checktime = $QGoodBundleOnline->checktime($f_date,$t_date,$good_id,$good_color);
       $result['count'] = count($checktime);
       echo json_encode($result);




     }

   }


   public function  colorOnlineAction(){
     $GoodColorOnline = new Application_Model_GoodColorOnline();
     $QMappingColorOnline = new Application_Model_MappingColorOnline();
     $QGoodColor = new Application_Model_GoodColor();
     $where = $QGoodColor->get_cache3();
     $this->view->color = $where;
     $page           = $this->getRequest()->getParam('page', 1);
     $color        = $this->getRequest()->getParam('color');

     $limit   = LIMITATION * 2;
     $total   = 0;
     $params = array(
      'color'     => $color,
    );

     $this->view->logs   = $QMappingColorOnline->fetchPagination($page, $limit, $total, $params);
     $this->view->params = $params;
     $this->view->limit  = $limit;
     $this->view->total  = $total;
     $this->view->url    = HOST.'product/color-online'.( $params ? '?'.http_build_query($params).'&' : '?' );
     $this->view->offset = $limit*($page-1);
        // echo "<pre>";
        // print_r($QMappingColorOnline->fetchPagination($page, $limit, $total, $params));
        // die;

     $flashMessenger = $this->_helper->flashMessenger;
     $this->view->messages_success = $flashMessenger->setNamespace('success')->getMessages();
     $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();

     $this->_helper->viewRenderer->setRender('color-online/list');

   }
   public function colorOnlineCreateAction()
   {
    $QGoodColor = new Application_Model_GoodColor();
    $where = $QGoodColor->get_cache3();
    $this->view->color = $where;

    $this->_helper->viewRenderer->setRender('color-online/create');
  }

  public function colorOnlineSaveAction()
  {

    $color_name = $this->getRequest()->getParam('color_name');
    $wms_id   = $this->getRequest()->getParam('wms_id');
    $id   = $this->getRequest()->getParam('id');
    $color_online_id   = $this->getRequest()->getParam('color_online_id');
    $edit   = $this->getRequest()->getParam('edit');
    try{
     $flashMessenger   = $this->_helper->flashMessenger;
     $db = Zend_Registry::get('db');
     $db->beginTransaction();
     $userStorage = Zend_Auth::getInstance()->getStorage()->read();
     $QGoodColorOnline = new Application_Model_GoodColorOnline();
     $QMappingColorOnline = new Application_Model_MappingColorOnline();
     $datetime = date('Y-m-d H:i:s'); 

     if(isset($id)&&isset($color_online_id)){

      $data1['color_name'] = $color_name;
      $data1['updated_at'] = $datetime;
      $data1['updated_by'] = $userStorage ? $userStorage->id : 0;
      $where_update = $QGoodColorOnline->getAdapter()->quoteInto('id = ?', $color_online_id);
      $status = $QGoodColorOnline->update($data1, $where_update);
      $data['good_color_id'] = $wms_id;
      $data['color_online_id'] = $color_online_id;
      $data['updated_at'] = $datetime;
      $data['updated_by'] = $userStorage ? $userStorage->id : 0;
      $where_update1 = $QMappingColorOnline->getAdapter()->quoteInto('id = ?', $id);
      $status = $QMappingColorOnline->update($data, $where_update1);

    }else{



      $data1['color_name'] = $color_name;
      $data1['status'] = 1;
      $data1['updated_at'] = $datetime;
      $data1['updated_by'] = $userStorage ? $userStorage->id : 0;
      $data1['created_at'] = $datetime;
      $data1['created_by'] = $userStorage ? $userStorage->id : 0;



      $id=$QGoodColorOnline->insert($data1);

      $data['good_color_id'] = $wms_id;
      $data['status'] = 1;
      $data['color_online_id'] = $id;
      $data['created_at'] = $datetime;
      $data['created_by'] = $userStorage ? $userStorage->id : 0;
      $data['updated_at'] = $datetime;
      $data['updated_by'] = $userStorage ? $userStorage->id : 0;

      $QMappingColorOnline->insert($data);
    }
    $flashMessenger->setNamespace('success')->addMessage('Done!');
    $db->commit();
  }catch(exception $e){
    var_dump($e);exit;
    $db->rollback();
    $flashMessenger->setNamespace('error')->addMessage('Not success, please try again!');
  }
  $this->_redirect(HOST.'/product/color-online/list');
}
public function colorOnlineEditAction()
{
  $id = $this->getRequest()->getParam('id');
  $QMappingColorOnline = new Application_Model_MappingColorOnline();
  $QGoodColor = new Application_Model_GoodColor();
  $where = $QGoodColor->get_cache3();
  $this->view->color = $where;
  $this->view->logs  =  $QMappingColorOnline->get_color_online($id);
      // print_r($QMappingColorOnline->get_color_online($id));
      // die;


  $this->_helper->viewRenderer->setRender('color-online/create');
}

public function productLockStockAction()
{
    $flashMessenger               = $this->_helper->flashMessenger;

    $sort           = $this->getRequest()->getParam('sort', '');
    $desc           = $this->getRequest()->getParam('desc', 1);
    $page           = $this->getRequest()->getParam('page', 1);

    $warehouse_id   = $this->getRequest()->getParam('warehouse_id');
    $product_model   = $this->getRequest()->getParam('product_model');
    $lock_type   = $this->getRequest()->getParam('lock_type');


    $limit = LIMITATION;
    $total = 0;

    $QLockStock = new Application_Model_LockStock();
    $QStaff = new Application_Model_Staff();
    $QWarehouse = new Application_Model_Warehouse();
    $QGood = new Application_Model_Good();

    $params = array(
        'warehouse_id'      => $warehouse_id,
        'product_model'     => $product_model,
        'lock_type'         => $lock_type
    );

    $where = $QWarehouse->getAdapter()->quoteInto('status =?',1);
    $warehouse = $QWarehouse->fetchAll($where);

    $data = $QLockStock->fetchPagination($page, $limit, $total, $params);

    $this->view->data   = $data;
    $this->view->staff  = $QStaff->get_cache();
    $this->view->warehouse = $warehouse;
    $this->view->goods = $QGood->get_load_cache();

    $this->view->desc   = $desc;
    $this->view->sort   = $sort;
    $this->view->params = $params;
    $this->view->limit  = $limit;
    $this->view->total  = $total;
    $this->view->url    = HOST.'product/product-lock-stock/'.( $params ? '?'.http_build_query($params).'&' : '?' );


    $messages_success             = $flashMessenger->setNamespace('success')->getMessages();
    $this->view->messages_success = $messages_success;

    $messages                     = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages         = $messages;


    $this->_helper->viewRenderer->setRender('product-lock-stock/product-lock-stock');
}

public function addProductLockStockAction()
{

    $id   = $this->getRequest()->getParam('id');

    $QLockStock = new Application_Model_LockStock();
    $QWarehouse = new Application_Model_Warehouse();
    $QGood = new Application_Model_Good();
    $QGoodColorCombined = new Application_Model_GoodColorCombined();

    if($id){

        $checkErro = $QLockStock->find($id);
        $info = $checkErro->current();

        if(empty($checkErro)){
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            $this->_redirect(HOST.'product/product-lock-stock');
        }

        $color = $QGoodColorCombined->get_color_by_good($info->good_id);

        $this->view->lock = $info;
        $this->view->color = $color;

    }



    $where = $QWarehouse->getAdapter()->quoteInto('status =?',1);
    $warehouse = $QWarehouse->fetchAll($where);

    $this->view->warehouse = $warehouse;
    $this->view->goods = $QGood->get_load_cache();

    $flashMessenger = $this->_helper->flashMessenger;
    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;

    $this->_helper->viewRenderer->setRender('product-lock-stock/create');
}

public function saveProductLockStockAction()
{
    $flashMessenger = $this->_helper->flashMessenger;

    $QLockStock = new Application_Model_LockStock();
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $datetime = date('Y-m-d H:i:s');
    $userID = $userStorage->id;

    if ($this->getRequest()->getMethod() == 'POST') {

        try {

            $id                 = $this->getRequest()->getParam('id');
            $warehouse_id       = $this->getRequest()->getParam('warehouse_id');
            $lock_type          = $this->getRequest()->getParam('lock_type');
            $product_model      = $this->getRequest()->getParam('product_model');
            $product_color      = $this->getRequest()->getParam('product_color');
            $qulity             = $this->getRequest()->getParam('qulity');
            $remark             = $this->getRequest()->getParam('remark');

            if($id) {

                $where_update = $QLockStock->getAdapter()->quoteInto('id =?',$id);
                $lock_stock = $QLockStock->fetchRow($where_update);

                if($lock_stock->warehouse_id !== $warehouse_id || $lock_stock->good_id !== $product_model || $lock_stock->color_id !== $product_color) {

                   if($lock_type == 1) {

                    $where_check = array();
                    $where_check[] = $QLockStock->getAdapter()->quoteInto('good_id =?',$product_model);
                    $where_check[] = $QLockStock->getAdapter()->quoteInto('warehouse_id =?',$warehouse_id);
                    $check_resualt = $QLockStock->fetchRow($where_check);

                    if($check_resualt) {
                        $flashMessenger->setNamespace('error')->addMessage('The product is locked by other conditions and additional conditions cannot be created.. ( All Model Colro )');
                        $this->_redirect(HOST.'product/product-lock-stock');
                    }

                }else {

                    $where_check = array();
                    $where_check[] = $QLockStock->getAdapter()->quoteInto('good_id =?',$product_model);
                    $where_check[] = $QLockStock->getAdapter()->quoteInto('warehouse_id =?',$warehouse_id);
                    $where_check[] = $QLockStock->getAdapter()->quoteInto('color_id =?',$product_color);
                    $check_resualt = $QLockStock->fetchRow($where_check);

                    if($check_resualt) {
                        $flashMessenger->setNamespace('error')->addMessage('The product is locked by other conditions and additional conditions cannot be created..( Model Color )');
                        $this->_redirect(HOST.'product/product-lock-stock');
                    }
                }

            }


            $data = array(
                'lock_type'     => $lock_type,
                'warehouse_id'  => $warehouse_id,
                'good_id'       => $product_model,
                'color_id'      => $product_color,
                'qulity'        => $qulity,
                'remark'        => $remark,
                'updated_by'    => $userID,
                'updated_at'    => $datetime,
            );


            $where = $QLockStock->getAdapter()->quoteInto('id =?',$id);
            $QLockStock->update($data,$where);

            $flashMessenger->setNamespace('success')->addMessage('Updated Lock Stock Success !');
            $this->_redirect(HOST.'product/product-lock-stock');

        }else{


            if($lock_type == 1) {

                $where_check = array();
                $where_check[] = $QLockStock->getAdapter()->quoteInto('good_id =?',$product_model);
                $where_check[] = $QLockStock->getAdapter()->quoteInto('warehouse_id =?',$warehouse_id);
                $check_resualt = $QLockStock->fetchRow($where_check);

                if($check_resualt) {
                    $flashMessenger->setNamespace('error')->addMessage('The product is locked by other conditions and additional conditions cannot be created.. ( All Model Colro )');
                    $this->_redirect(HOST.'product/add-product-lock-stock');
                }

            }else {

                $where_check = array();
                $where_check[] = $QLockStock->getAdapter()->quoteInto('good_id =?',$product_model);
                $where_check[] = $QLockStock->getAdapter()->quoteInto('warehouse_id =?',$warehouse_id);
                $where_check[] = $QLockStock->getAdapter()->quoteInto('color_id =?',$product_color);
                $check_resualt = $QLockStock->fetchRow($where_check);

                if($check_resualt) {
                    $flashMessenger->setNamespace('error')->addMessage('The product is locked by other conditions and additional conditions cannot be created..( Model Color )');
                    $this->_redirect(HOST.'product/add-product-lock-stock');
                }
            }

            $data = array(
                'lock_type'     => $lock_type,
                'warehouse_id'  => $warehouse_id,
                'good_id'       => $product_model,
                'color_id'      => $product_color,
                'qulity'        => $qulity,
                'remark'        => $remark,
                'created_by'    => $userID,
                'created_at'    => $datetime,
            );


            $QLockStock->insert($data);

            $flashMessenger->setNamespace('success')->addMessage('Add Lock Stock Success !');
            $this->_redirect(HOST.'product/product-lock-stock');
        }

    } catch(Exception $e){
        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST.'product/add-product-lock-stock');
    }

} else {
    $flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
    $this->_redirect(HOST.'product/add-product-lock-stock');
}
}

public function deleteProductLockStockAction()
{
    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    $QLockStock = new Application_Model_LockStock();
    $userStorage = Zend_Auth::getInstance()->getStorage()->read();

    $checkErro = $QLockStock->find($id);
    $info = $checkErro->current();

    if(empty($checkErro)){
        $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
        $this->_redirect(HOST.'product/product-lock-stock');
    }


    $where = $QLockStock->getAdapter()->quoteInto('id =?',$id);
    $delete_lock = $QLockStock->delete($where);

    if($delete_lock){
        $flashMessenger->setNamespace('success')->addMessage('Deleted Lock Stock Success !');
        $this->_redirect(HOST.'product/product-lock-stock');
    }else{
        $flashMessenger->setNamespace('error')->addMessage('Wrong Action.');
        $this->_redirect(HOST.'product/add-product-lock-stock');
    }

    $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
    $this->_redirect(HOST.'product/add-product-lock-stock');


}

}