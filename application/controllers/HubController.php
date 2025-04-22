<?php
class HubController extends My_Controller_Action
{
    public function indexAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'index.php';
    }

    public function createAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'create.php';
    }

    public function saveAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'save.php';
    }

    public function deleteAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'delete.php';
    }

    public function editAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'edit.php';
    }

    public function orderControlAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'order-control.php';
    }

    public function orderControlDeliveredAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'order-control-delivered.php';
    }

    public function orderControlConfirmAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'order-control-confirm.php';
    }

    public function orderControlDetailAction()
    {
        require_once 'hub' . DIRECTORY_SEPARATOR . 'order-control-detail.php';
    }

    private function _exportCsvHubs($data)
    {
        set_time_limit(0);
        error_reporting(E_ALL);
        ini_set('display_error', 0);
        ini_set('memory_limit', -1);

        $tmp_name = md5(uniqid("", true) . microtime(true)) . '.csv';

        $uniqid = uniqid('', true);
        $userStorage = Zend_Auth::getInstance()->getStorage()->read();

        $save_dir = APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
            'public' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'hub' .
            DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . $userStorage->id .
            DIRECTORY_SEPARATOR . $uniqid;

        if (!is_dir($save_dir))
            @mkdir($save_dir, 0777, true);

        $fullpath = $save_dir . DIRECTORY_SEPARATOR . $tmp_name;

        $output = fopen($fullpath, 'w');

        $heads = array(
            'No.',
            'Hub ID',
            'Hub Name',
            'Address',
            'Phone Number',
            'Contact',
            'Mobile Phone',
            'Area',
            'Province',
            'District',
            'Note',
        );

        fputcsv($output, $heads);

        $i = 1;

        foreach ($data as $item)
        {
            $row = array();
            $alpha = 'A';
            $row[] = $i++;
            $row[] = $item['id'];
            $row[] = $item['name'];
            $row[] = $item['address'];
            $row[] = isset($item['phone_number']) ? ("=\"" . $item['phone_number'] . "\"") : '';
            $row[] = $item['contact'];
            $row[] = isset($item['mobile_phone']) ? ("=\"" . $item['mobile_phone'] . "\"") : '';
            $row[] = isset($item['district_id']) ? My_Region::getValue($item['district_id'], My_Region::Area) : '';
            $row[] = isset($item['district_id']) ? My_Region::getValue($item['district_id'], My_Region::Province) : '';
            $row[] = isset($item['district_id']) ? My_Region::getValue($item['district_id'], My_Region::District) : '';
            $row[] = $item['note'];

            fputcsv($output, $row);
        }

        $filename = 'Hub List ' . date('Y-m-d H-i-s');
        header('Content-Description: File Transfer');
        header('Content-Encoding: UTF-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        // echo "\xEF\xBB\xBF"; // UTF-8 BOM
        header('Content-Type: application/csv; charset=utf-8');
        echo chr(239) . chr(187) . chr(191); // UTF-8 BOM
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullpath));
        readfile($fullpath);

        exit;
    }
}
