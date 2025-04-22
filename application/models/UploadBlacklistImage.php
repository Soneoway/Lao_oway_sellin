<?php
class Application_Model_UploadBlacklistImage extends Zend_Db_Table_Abstract
{
    protected $_name = 'upload_blacklist';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('p' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS p.id'), 'p.*'));
        $select->joinLeft(array('d'=>'distributor'),'p.d_id=d.id',array('d.title','d.del','d.activate','remark'));

        if (isset($params['d_id']) && $params['d_id']) {
            $select->where('p.d_id = ?', $params['d_id']);
        }      

        $select->where('p.status = 1');

        if ($limit)
            $select->limitPage($page, $limit);

        // echo $select;die;
        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
       
        return $result;
    }

    function uploadFile($photo, $userStorage, $photo_id = null,$data) {

    	$timestamp = time();
		
		$located_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
		DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'upload'.
		DIRECTORY_SEPARATOR.'blacklist_image'.DIRECTORY_SEPARATOR.$data['d_id'].DIRECTORY_SEPARATOR;

		try
		{

			if (!is_dir($located_dir)) {
				@mkdir($located_dir, 0777, true);
			}

			$tem = explode('_located_', $photo);

			if (isset($tem[1]) and $tem[1]) {
				$uniqid    = $tem[0];
				$file_name = $tem[1];

				$uploaded_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
				DIRECTORY_SEPARATOR.'public'.
				DIRECTORY_SEPARATOR.'upload'.
				DIRECTORY_SEPARATOR.'blacklist_image'.
				DIRECTORY_SEPARATOR.'temp'.
				DIRECTORY_SEPARATOR.$userStorage->id.
				DIRECTORY_SEPARATOR.$uniqid.
				DIRECTORY_SEPARATOR;

				$new_file_name = null;
				if (is_file($uploaded_dir.$file_name)) {

					$info = pathinfo($uploaded_dir.$file_name);

					// $new_file_name = md5($photo).'.'.$info['extension'];
					$new_file_name = $timestamp . '_' . $photo;

					if ($info['extension'] == 'jpeg' || $info['extension'] == 'jpg'|| $info['extension'] == 'JPG') 
					{ 
						$image = imagecreatefromjpeg($uploaded_dir.$file_name);
						imagejpeg($image, $located_dir.$new_file_name, 50);
					}

					copy($uploaded_dir.$file_name, $located_dir.$new_file_name);

					$files = glob($uploaded_dir.'*');// get all file names
					foreach ($files as $file) {// iterate files
						if (is_file($file)) {
							$fn = pathinfo($file, PATHINFO_FILENAME);
							$ex = pathinfo($file, PATHINFO_EXTENSION);

							if (!$l) {
								unlink($file);
							}
							// delete file
							 elseif (!in_array($fn.'.'.$ex, $l)) {
								unlink($file);
							}
							// delete file
						}
					}
					unlink($uploaded_dir);
				}

				$QUploadBlacklistImage = new Application_Model_UploadBlacklistImage();

				$data = array(
		  			'd_id'			=> $data['d_id'],
		            'type' 		    => $data['type'],
		            'file_name'    	=> $new_file_name,
		            'status'      	=> 1,
		            'created_date'  => date('Y-m-d H:i:s'),
		            'created_by'    => $userStorage->id,
		            'updated_date'  => date('Y-m-d H:i:s'),
		            'updated_by'   	=> $userStorage->id
		        );

		  	$QUploadBlacklistImage->insert($data);
		  	
			}
			return $photo_id;//success
		}
		 catch (exception $e) {
		 	echo 'Error! Please contact IT Team';
		 	die;
			return -1;//error
		}

	}
}
