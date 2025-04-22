<?php
class ToolController extends My_Application_Controller_Cli
{
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function fillStoreCodeAction()
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT
                    COUNT(id)
                FROM
                    distributor
                WHERE
                (
                    title LIKE 'TGDĐ-%'
                OR
                    title LIKE 'TGDĐ -%'
                )
                AND store_code NOT LIKE '%-%'";

        $stmt = $db->query($sql);
        $stmt->setFetchMode(Zend_Db::FETCH_NUM);
        $total = $stmt->fetchAll();
        $total = $total[0][0];

        $sql = "SELECT
                    id
                FROM
                    distributor
                WHERE
                (
                    title LIKE 'TGDĐ-%'
                OR
                    title LIKE 'TGDĐ -%'
                )
                AND store_code NOT LIKE '%-%'";

        $distributors = $db->query($sql);
        $i = 1;

        foreach ($distributors as $key => $distributor) {
            My_Cli_Util::show_status($i++, $total);

            $sql = "UPDATE distributor SET store_code = ? WHERE id = ?";
            $db->query($sql, array(My_Distributor::generateStoreCode('TGDD-'), $distributor['id']));
        }

        exit;
    }
}