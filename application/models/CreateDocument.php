<?php
class Application_Model_CreateDocument extends Zend_Db_Table_Abstract
{
    protected $_name = 'distributor_file';



    function uploadFile($photo, $sn, $userStorage, $photo_id = null,$data) {
		
		$located_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
		DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'upload'.
		DIRECTORY_SEPARATOR.'distributor_doc'.DIRECTORY_SEPARATOR.$data['d_id'].DIRECTORY_SEPARATOR;


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
				DIRECTORY_SEPARATOR.'distributor_doc'.
				DIRECTORY_SEPARATOR.'temp'.
				DIRECTORY_SEPARATOR.$userStorage->id.
				DIRECTORY_SEPARATOR.$uniqid.
				DIRECTORY_SEPARATOR;

				$new_file_name = null;
				if (is_file($uploaded_dir.$file_name)) {

					$info = pathinfo($uploaded_dir.$file_name);

					$new_file_name = md5($photo).'.'.$info['extension'];

					if ($info['extension'] == 'jpeg' || $info['extension'] == 'jpg'|| $info['extension'] == 'JPG') 
					{ 
						$image = imagecreatefromjpeg($uploaded_dir.$file_name);
						imagejpeg($image, $located_dir.$new_file_name, 50);
					}  
					

					// $im = imagecreatefromjpeg($uploaded_dir . $file_name);
					

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

				$QCreateDocument = new Application_Model_CreateDocument();

				$data = array(
		  			'd_id'				        => $data['d_id'],
		            'd_category' 		      	=> $data['d_category'],
		            'd_document'          		=> $new_file_name,
		            'd_update'           		=> date('Y-m-d H:i:s')
		        );
					
	
		  	$QCreateDocument->insert($data );

		  	
			}
			
			

			
			

			

			return $photo_id;//success

		}
		 catch (exception $e) {

			return -1;//error
		}

	}
}
