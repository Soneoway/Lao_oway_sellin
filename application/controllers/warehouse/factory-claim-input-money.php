<?php

    $userStorage = Zend_Auth::getInstance()->getStorage()->read();
    $flashMessenger = $this->_helper->flashMessenger;

    $id = $this->getRequest()->getParam('id');

    if(!$id){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/factory-claim-input-money-list");
    }

    if ($this->getRequest()->getMethod() == 'POST') {

        $money = $this->getRequest()->getParam('money');
        $part_id = $this->getRequest()->getParam('part_id');

        if(!$id){
            $flashMessenger->setNamespace('error')->addMessage('Error! Invalid Data');
            $this->_redirect(HOST."warehouse/factory-claim-input-money?id=" . $part_id);
        }

        if(!$money){
            $flashMessenger->setNamespace('error')->addMessage('Please Input Money');
            $this->_redirect(HOST."warehouse/factory-claim-input-money?id=" . $part_id);
        }

        if(count($id) != count($money)){
            $flashMessenger->setNamespace('error')->addMessage('Error! Invalid Data');
            $this->_redirect(HOST."warehouse/factory-claim-input-money?id=" . $part_id);
        }

        try{

            $db = Zend_Registry::get('db');
            $db->beginTransaction();

            $QFC = new Application_Model_FactoryClaim();

            $i = 0;
            foreach ($id as $key) {
                $data = array(
                'price' => $money[$i],
                'status' => 4,
                'input_money_date' => date('Y-m-d H:i:s'),
                'input_money_by' => $userStorage->id
                );

                $where_update = [];
                $where_update[] = $QFC->getAdapter()->quoteInto('factory_claim_id = ?', $key);
                $where_update[] = $QFC->getAdapter()->quoteInto('status = ?', 2);
                $QFC->update($data, $where_update);

                $i++;
            }

            // $get_data_detail = $QFC->getDetailImeiByFactoryClaim($id,4);

            $db->commit();

            $flashMessenger->setNamespace('success')->addMessage('Done!');
            $this->_redirect(HOST."warehouse/factory-claim-input-money-list");

        } catch (Exception $e){
            $db->rollback();

            $flashMessenger->setNamespace('error')->addMessage('Failed - '.$e->getMessage());
            $this->_redirect(HOST."warehouse/factory-claim-input-money?id=" . $part_id);
        }

    }

    $array_id = explode(',', $id);

    $QFC = new Application_Model_FactoryClaim();
    $get_factory_claim_array = $QFC->getFactoryClaimArray($array_id,2);

    if(count($array_id) != count($get_factory_claim_array)){
        $flashMessenger->setNamespace('error')->addMessage('Not Find!');
        $this->_redirect(HOST."warehouse/factory-claim-input-money-list");
    }

    $this->view->get_factory_claim_array = $get_factory_claim_array;

    $this->view->id = $id;

    $messages = $flashMessenger->setNamespace('error')->getMessages();
    $this->view->messages = $messages;