<?php

class StaffController extends My_Controller_Action
{
    public function indexAction()
    {
        //print_r($_POST);    
        
        $sort            = $this->getRequest()->getParam('sort', '');
        $desc            = $this->getRequest()->getParam('desc', 1);

        $page            = $this->getRequest()->getParam('page', 1);
        $username        = $this->getRequest()->getParam('username');
        $group_id        = $this->getRequest()->getParam('group_id',null);
        $status          = $this->getRequest()->getParam('status',null);
        $company        = $this->getRequest()->getParam('company');

        $staff_code      = $this->getRequest()->getParam('staff_code');

        $limit = LIMITATION;
        $total = 0;

        $params = array_filter( array(
            'username'            => $username,
            'group_id'            => $group_id,
            'staff_code'          => $staff_code,
            'status'              => $status,
            'company'            => $company
        ));

        $params['sort'] = $sort;
        $params['desc'] = $desc;

        $QStaff = new Application_Model_Staff();

        $staffs = $QStaff->fetchPagination($page, $limit, $total, $params);

        $this->view->desc = $desc;
        $this->view->sort = $sort;
        $this->view->staffs = $staffs;
        $this->view->params = $params;
        $this->view->limit = $limit;
        $this->view->total = $total;
        $this->view->url = HOST.'staff/'.( $params ? '?'.http_build_query($params).'&' : '?' );

        $this->view->offset = $limit*($page-1);

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages = $messages;

        if($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setRender('partials/list');
        }

        $QGroup = new Application_Model_Group();
        $this->view->groups = $QGroup->fetchAll();

        $QArea = new Application_Model_Area();
        $this->view->areas = $QArea->fetchAll();

    }

    public function createAction(){
        
        
        if ($this->getRequest()->getMethod() == 'POST'){

            $staff = new Application_Model_Staff();
            $id              = $this->getRequest()->getParam('id');
            $staff_code       = $this->getRequest()->getParam('staff_code');
            $firstname       = $this->getRequest()->getParam('firstname');
            $lastname        = $this->getRequest()->getParam('lastname');
            $phone_number    = $this->getRequest()->getParam('phone_number');
            $gender          = $this->getRequest()->getParam('gender');
            $note            = $this->getRequest()->getParam('note');
            $email           = $this->getRequest()->getParam('email');
            $change_password = $this->getRequest()->getParam('change-pass');
            $password        = $this->getRequest()->getParam('password');
            $group_id        = $this->getRequest()->getParam('group_id');
            $username        = $this->getRequest()->getParam('username');
            $status          = $this->getRequest()->getParam('status');
            $hub             = $this->getRequest()->getParam('hub');
            $company         = $this->getRequest()->getParam('company');

            $position        = $this->getRequest()->getParam('position');
            $area_id         = $this->getRequest()->getParam('area_id');

            $warehouse_type        = $this->getRequest()->getParam('warehouse_type');
            $catty_staff        = $this->getRequest()->getParam('cmb_catty_staff_id');
            $distributor_group_id        = $this->getRequest()->getParam('distributor_group_id');
            $warehouse_group_id        = $this->getRequest()->getParam('warehouse_group_id');

            $group_id = array_values(array_unique($group_id));
            $area_id = array_values(array_unique($area_id)); // explo ,,area
            $warehouse_type = array_values(array_unique($warehouse_type));
            $distributor_group_id = array_values(array_unique($distributor_group_id));
            $warehouse_group_id = array_values(array_unique($warehouse_group_id));

            $QStaff = new Application_Model_Staff();

            $allow = true;

            if ($username){
                $where = array();
                $where[] = $QStaff->getAdapter()->quoteInto('username = ?', $username);
                $where[] = $QStaff->getAdapter()->quoteInto('id != ?', intval($id));

                $staffs_username = $QStaff->fetchRow($where);

                if($staffs_username){
                    $allow = false;
                }
            }

            if ($email){
                $where = array();
                $where[] = $QStaff->getAdapter()->quoteInto('email = ?', $email);
                $where[] = $QStaff->getAdapter()->quoteInto('id != ?', intval($id));

                $staffs_email = $QStaff->fetchRow($where);

                if($staffs_email){
                    $allow = false;
                }
            }

            if ($staff_code){
                $where = array();
                $where[] = $QStaff->getAdapter()->quoteInto('staff_code = ?', $staff_code);
                $where[] = $QStaff->getAdapter()->quoteInto('id != ?', intval($id));

                $staffs_staff_code = $QStaff->fetchRow($where);

                if($staffs_staff_code) {
                    $allow = false;
                }
            }

            if(!$allow){

                $flashMessenger = $this->_helper->flashMessenger;
                $flashMessenger->setNamespace('error')->addMessage('Cannot insert, please try again!, Duplicate Data!');
                if($id){
                    $this->_redirect( '/staff/create?id=' . $id );
                }else{
                    $this->_redirect( '/staff'  );
                }
            }

            $data = array(
                'staff_code'    => $staff_code,
                'firstname'    => $firstname,
                'lastname'     => $lastname,
                'phone_number' => $phone_number,
                'gender'       => $gender,
                'position'     => $position,
                'note'         => $note,
                'email'        => $email,
                'group_id'     => implode(',', $group_id),
                'area_id'      => implode(',', $area_id),
                'warehouse_type'     => implode(',', $warehouse_type),
                'username'     => $username,
                'status'       => $status,
                'company'       => $company
            );

            if($catty_staff[0] !='0'){
                $catty_staff_id =$catty_staff[0];
                $data['catty_staff_id'] = $catty_staff_id;
            }else{
                $data['catty_staff_id'] = null;
            }
            //print_r($data);die;s
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $QDGU = new Application_Model_DistributorGroupUser();
            $QWG = new Application_Model_WarehouseGroupUser();

            if ($id){
                $where = $staff->getAdapter()->quoteInto('id = ?', $id);

                if ($change_password)
                    $data['password'] = md5($password);

                $data['updated_at'] = date('Y-m-d H:i:s');
                $data['updated_by'] = $userStorage->id;

                $staff->update($data, $where);

                $where = $QDGU->getAdapter()->quoteInto('user_id = ?', $id);
                $QDGU->delete($where);

                foreach ($distributor_group_id as $key) {

                    if($key > 0){
                        $arrData = array(
                            'user_id' => $id,
                            'group_id' => $key,
                            'status' => 1,
                            'update_date' => date('Y-m-d H:i:s'),
                            'update_by' => $userStorage->id
                        );

                        $QDGU->insert($arrData);
                    }
                }

                $where = $QWG->getAdapter()->quoteInto('user_id = ?', $id);
                $QWG->delete($where);

                foreach ($warehouse_group_id as $key) {

                    if($key > 0){
                        $arrData = array(
                            'user_id' => $id,
                            'warehouse_id' => $key,
                            'status' => 1,
                            'update_date' => date('Y-m-d H:i:s'),
                            'update_by' => $userStorage->id
                        );

                        $QWG->insert($arrData);
                    }
                }

            } else {
                try{
                    $data['password'] = md5($password);

                    $data['created_at'] = date('Y-m-d H:i:s');
                    $data['created_by'] = $userStorage->id;

                    $id = $staff->insert($data);

                    foreach ($distributor_group_id as $key) {
                        $arrData = array(
                            'user_id' => $id,
                            'group_id' => $key,
                            'status' => 1,
                            'update_date' => date('Y-m-d H:i:s'),
                            'update_by' => $userStorage->id
                        );
                        $QDGU->insert($arrData);
                    }
                
                    foreach ($warehouse_group_id as $key) {

                        if($key > 0){
                            $arrData = array(
                                'user_id' => $id,
                                'warehouse_id' => $key,
                                'status' => 1,
                                'update_date' => date('Y-m-d H:i:s'),
                                'update_by' => $userStorage->id
                            );

                            $QWG->insert($arrData);
                        }
                    }

                }catch (Exception $e){
                    $flashMessenger = $this->_helper->flashMessenger;
                    $flashMessenger->setNamespace('error')->addMessage('Cannot insert, please try again!');
                }
            }

            $cache = Zend_Registry::get('cache');
            $cache->remove('staff_cache');

            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('success')->addMessage('Done!');


            $this->_redirect( '/staff'  );

        }

        //edit staff
        $id = $this->getRequest()->getParam('id');
        $QStaff = new Application_Model_Staff();

        if ($id) {
            $staffRowset = $QStaff->find($id);
            $staff = $staffRowset->current();
            $this->view->staff = $staff;

            $QDGU = new Application_Model_DistributorGroupUser();
            $getCurrentDistributorGroup = $QDGU->currentDistributorGroup($id);

            // print_r($getCurrentDistributorGroup);die;
            $this->view->currentDistributorGroup = $getCurrentDistributorGroup;

            $QWarehouseGroupUser = new Application_Model_WarehouseGroupUser();
            $getWarehouseGroup = $QWarehouseGroupUser->currentWarehouseGroupUserList($id);
            $this->view->currentWarehouseGroupUserList = $getWarehouseGroup;
        }


        $QWarehouseType  = new Application_Model_WarehouseType();
        $this->view->warehouse_type = $QWarehouseType->get_cache();
        
        $QDG = new Application_Model_DistributorGroup();
        $getDistributorGroup = $QDG->getDistributorGroup();
        $this->view->distributor_group = $getDistributorGroup;

        $QGroup = new Application_Model_Group();
        $getGroup = $QGroup->getGroup();
        $this->view->groups = $getGroup;

        $QArea = new Application_Model_Area();
        $getArea = $QArea->getArea();
        $this->view->areas = $getArea;

        $QWarehouse = new Application_Model_Warehouse();
        $this->view->warehouses = $QWarehouse->get_cache();

        

        //$data_staff = $QStaff->getCattyStaff();
        //print_r($getWarehouseGroup);

        $this->view->cattystaff = $QStaff->getCattyStaff();

        // $QArea = new Application_Model_Area();
        // $this->view->areas = $QArea->get_cache();

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;


    }

    public function delAction(){
        $id = $this->getRequest()->getParam('id');

        $staff = new Application_Model_Staff();
        $where = $staff->getAdapter()->quoteInto('id = ?', $id);
        $staff->delete($where);

        $cache = Zend_Registry::get('cache');
        $cache->remove('staff_cache');

        $this->_redirect('/staff');
    }

    public function checkAction()
    {

        $id    = $this->getRequest()->getParam('id');
        $username  = $this->getRequest()->getParam('username');
        $email = $this->getRequest()->getParam('email');
        $staff_code = $this->getRequest()->getParam('staff_code');

        $where = array();
        $QStaff = new Application_Model_Staff();

        if ($username)
            $where[] = $QStaff->getAdapter()->quoteInto('username = ?', $username);

        if ($email)
            $where[] = $QStaff->getAdapter()->quoteInto('email = ?', $email);

        if ($staff_code)
            $where[] = $QStaff->getAdapter()->quoteInto('staff_code = ?', $staff_code);

        if (sizeof($where) == 0) {
            echo "-1";
            exit;
        }

        /*$where[] = $QStaff->getAdapter()->quoteInto('off_date IS NULL ', null);*/

        $where[] = $QStaff->getAdapter()->quoteInto('id != ?', intval($id));

        $staffs = $QStaff->fetchAll($where);

        if (isset($staffs[0])) echo '1';
        else echo '0';

        exit;
    }

    public function priviledgeAction(){
        $staff_id = $this->getRequest()->getParam('id');

        $QStaffPriveledge = new Application_Model_StaffPriviledge();

        $where = $QStaffPriveledge->getAdapter()->quoteInto('staff_id = ?', $staff_id);

        $result = $QStaffPriveledge->fetchRow($where);

        $personal_accesses = isset($result['access']) ? json_decode($result['access']) : null;

        $this->view->id = isset($result['id']) ? $result['id'] : null;

        //get group access
        if (!$personal_accesses){
            $db = Zend_Registry::get('db');
            $select = $db->select()
                ->from(array('p' => 'staff'),
                    array());

            $select->join(array('g' => 'group'),
                'p.group_id = g.id',
                array('g.access'));

            $select->where('p.id = ?', $staff_id);

            $result = $db->fetchRow($select);

            $group_accesses = isset($result['access']) ? json_decode($result['access']) : null;

            $this->view->default_page = isset($result['default_page']) ? $result['default_page'] : null;

            $this->view->accesses = $group_accesses;

            $personal_menus = (isset($result['menu']) and $result['menu']) ? explode(',', $result['menu']) : null;

        } else{

            $personal_menus = (isset($result['menu']) and $result['menu']) ? explode(',', $result['menu']) : null;

            $this->view->accesses = $personal_accesses;

            $this->view->default_page = isset($result['default_page']) ? $result['default_page'] : null;
        }

        $this->view->staff_id = $staff_id;

        //get all controller and action
        $front = $this->getFrontController();
        $acl = array();

        foreach ($front->getControllerDirectory() as $module => $path) {

            foreach (scandir($path) as $file) {

                if (strstr($file, "Controller.php") !== false) {

                    include_once $path . DIRECTORY_SEPARATOR . $file;

                    foreach (get_declared_classes() as $class) {

                        if (is_subclass_of($class, 'Zend_Controller_Action')) {

                            $class = lcfirst($class);
                            $controller = str_replace('Controller', '', $class);


                            $controller = strtolower(preg_replace_callback("/([A-Z])/",
                                function ($m){return '-'.$m[0];},
                                $controller));

                            $actions = array();

                            foreach (get_class_methods($class) as $action) {

                                if (strstr($action, "Action") !== false) {
                                    $action = lcfirst($action);
                                    $action = str_replace('Action', '', $action);


                                    $actions[] = strtolower(preg_replace_callback("/([A-Z])/",
                                        function ($m){return '-'.$m[0];},
                                        $action));
                                }
                            }
                        }
                    }

                    $acl[$module][$controller] = $actions;
                }
            }
        }
        //set current method
        $actions = array();
        foreach (get_class_methods($this) as $action){
            if (strstr($action, "Action") !== false) {
                $action = lcfirst($action);
                $action = str_replace('Action', '', $action);


                $actions[] = strtolower(preg_replace_callback("/([A-Z])/",
                    function ($m){return '-'.$m[0];},
                    $action));
            }
        }

        $acl[$this->getRequest()->getModuleName()][$this->getRequest()->getControllerName()] = $actions;

        $this->view->acl = $acl;


        //menu
        $QMenu = new Application_Model_Menu();
        $where = $QMenu->getAdapter()->quoteInto('group_id = ?', 1);
        $menus = $QMenu->fetchAll($where, array('parent_id', 'position'));
        foreach ($menus as $menu)
            $this->add_row($menu->id, $menu->parent_id, $menu->title);

        $this->view->menus = $this->generate_list($personal_menus);

        //back url
        $this->view->back_url = $this->getRequest()->getServer('HTTP_REFERER');
    }

    public function priviledgeSaveAction(){

        if ($this->getRequest()->getMethod() == 'POST'){


            $id = $this->getRequest()->getParam('id');
            $staff_id = $this->getRequest()->getParam('staff_id');
            $default_page = $this->getRequest()->getParam('default_page');
            $menus = $this->getRequest()->getParam('menus');

            $raw_access = $this->getRequest()->getParam('access');
            $access = array();
            if (is_array($raw_access)){
                foreach ($raw_access as $module=>$item)
                    foreach ($item as $controller=>$item_2)
                        foreach ($item_2 as $action=>$item_3){
                            if ($item_3)
                                $access[] = $module.'::'.$controller.'::'.$action;
                        }
            }


            $QStaffPriveledge = new Application_Model_StaffPriviledge();

            $data = array(
                'staff_id' => $staff_id,
                'default_page' => ( $default_page ? $default_page : null ),
                'menu' => ( is_array($menus) ? implode(',', $menus) : null ),
                'access' => json_encode($access),
            );

            if ($id){
                $where = $QStaffPriveledge->getAdapter()->quoteInto('id = ?', $id);

                $QStaffPriveledge->update($data, $where);
            } else {
                $QStaffPriveledge->insert($data);
            }

            //remove cache
            $cache = Zend_Registry::get('cache');
            $cache->remove('staff_priviledge_cache');

            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('success')->addMessage('Done!');
        }


        $this->_redirect( '/staff' );
    }

    private function generate_list($group_menus) {
        return $this->ul(0, '', $group_menus);
    }

    function ul($parent = 0, $attr = '', $group_menus = null) {
        static $i = 1;
        $indent = str_repeat("\t\t", $i);
        if (isset($this->data[$parent])) {
            if ($attr) {
                $attr = ' ' . $attr;
            }
            $html = "\n$indent";
            $html .= "<ul$attr>";
            $i++;
            foreach ($this->data[$parent] as $row) {
                $child = $this->ul($row['id'], '', $group_menus);
                $html .= "\n\t$indent";
                $html .= '<li>';
                $html .= '<input value="'.$row['id'].'" '.( ($group_menus and in_array($row['id'] , $group_menus)) ? 'checked' : '' ).' type="checkbox" id="menus_'.$row['id'].'" name="menus[]"><label>'.$row['label'].'</label>';
                if ($child) {
                    $i--;
                    $html .= $child;
                    $html .= "\n\t$indent";
                }
                $html .= '</li>';
            }
            $html .= "\n$indent</ul>";
            return $html;
        } else {
            return false;
        }
    }

    private function add_row($id, $parent, $label) {
        $this->data[$parent][] = array('id' => $id, 'label' => $label);
    }
}