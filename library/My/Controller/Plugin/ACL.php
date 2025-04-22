<?php
class My_Controller_Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    protected $_defaultRole = 'guest';

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$ctrl_name = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();

    	if ($ctrl_name == 'distributor') return false;

        $r = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

        if (My_Device_UserAgent::isCocCoc()) {
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
            session_destroy();

            if (My_Request::isXmlHttpRequest()) exit;

            if ($request->getControllerName() != "user" || $request->getActionName() != "login")
                $r->gotoUrl(HOST.'user/login')->redirectAndExit();
        }

        $auth = Zend_Auth::getInstance();
        $acl = new Zend_Acl();

        $default = array(
            'default::error::error',
            'default::user::login',
            'default::user::logout',
            'default::user::auth',
            'default::user::reset',
            'default::user::noauth',
            'default::wss::index',
            'default::warehouse::customer-invoice',
            'default::warehouse::customer-print',
            'default::warehouse::customer-save',
            'default::warehouse::internal-invoice',
            'default::warehouse::internal-print',
            'default::warehouse::mobilization-order',
            'default::warehouse::mobilization-list',
            'default::get::invoice-service',
            'default::kerryapi::shipmentupdate',
            'default::kerryapi::cron',
            'default::kerryapi::cronrollback',
            'default::kerryapi::getdetailbyso',
            'default::kerryapi::getdetailbyco',
            'default::kerryapi::logindelivery',
            'default::kerryapi::postdetail',
            'default::kerryapi::gethistory',
            'default::kerryapi::canceldelivery',
            'default::kerryapi::dashboarddelivery',
            'default::kerryapi::getsopendingtoday',
            'default::creditnote-api::cron-cn',
            'default::cron-staff-order::cron-staff-expired-order',
            'default::cron::gen-sn-ref',
            'default::finance::print-invoice-etax',
            'default::finance::print-creditnote-etax',
            'default::finance::export-pdf-etax',
            'default::user::check-pass',
            'default::sales::print-sale-expire',

            'default::api::check-imei',
            'default::api::get-province',
            'default::api::get-district',
            'default::api::get-sub-district',
            'default::api::get-zipcode',
            'default::api::create-order-free-reno',
            'default::creditnote-api::cron-cp-auto'
        );

        foreach ($default as $access)
            $acl->add(new Zend_Acl_Resource($access));

        if($auth->hasIdentity()) {
            $user = $auth->getIdentity();

            if ( ! ( @$user->id == SUPERADMIN_ID || @$user->group_id == ADMINISTRATOR_ID ) ) {
                $user_role = @$user->role;
                $user_accesses = @$user->accesses;
                if (!$user_role)
                    $user_role = $this->_defaultRole;

                if (!$user_accesses)
                    $user_accesses = $default;

                $acl->addRole(new Zend_Acl_Role($user_role));
                foreach ($default as $access)
                    $acl->allow($user_role, $access);

                if ($user_accesses) {
                    foreach ($user_accesses as $access){
                        if (!$acl->has($access))
                            $acl->add(new Zend_Acl_Resource($access));
                        $acl->allow($user_role, $access);
                    }


                    if (!$acl->has($request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()))
                        $acl->add(new Zend_Acl_Resource($request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()));

                    if( !$acl->isAllowed($user_role, $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()) ) {
                        $r->gotoUrl('/user/noauth')->redirectAndExit();
                    }
                } else {

                    $r->gotoUrl('/user/noauth')->redirectAndExit();
                }
            }

            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            if (null === $viewRenderer->view) {
                $viewRenderer->initView();
            }
            $view = $viewRenderer->view;
            $view->name = @$user->username;
        } else {

            $acl->addRole(new Zend_Acl_Role($this->_defaultRole));

            if (!$acl->has($request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()))
                $acl->add(new Zend_Acl_Resource($request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()));

            foreach ($default as $access){

                $acl->allow($this->_defaultRole, $access);
            }

            if ( !$acl->isAllowed($this->_defaultRole, $request->getModuleName() . '::' . $request->getControllerName() . '::' . $request->getActionName()) )
                $r->gotoUrl('/user/login')->redirectAndExit();

        }
    }

}