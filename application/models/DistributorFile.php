<?php
class Application_Model_DistributorFile extends Zend_Db_Table_Abstract
{
    protected $_name = 'distributor_file';

    function fetchPagination($page, $limit, &$total, $params){
        $db = Zend_Registry::get('db');

        $select = $db->select()
            ->from(array('df' => $this->_name),
                array(new Zend_Db_Expr('SQL_CALC_FOUND_ROWS df.id'), 'df.*'));
        $select->joinLeft(array('dt'=>'document_type'),'df.d_category=dt.id_type',array('dt.name_type')); 
        $select->joinLeft(array('d'=>'distributor'),'df.d_id=d.id',array('d.title','d.del','d.activate','d.remark','d.mst_sn'));       

        if (isset($params['id']) and $params['id']){
	        $select->where('df.d_id = ?', $params['id']);
	    }

	    if (isset($params['dis_id']) and $params['dis_id']){
	        $select->where('d.id = ?', $params['dis_id']);
	    }

	    if (isset($params['dis_name']) and $params['dis_name']){
	        $select->where('d.title like ?', '%'.$params['dis_name'].'%');
	    }

	    if (isset($params['id']) and $params['id']){
	        $select->where('df.d_id = ?', $params['id']);
	    }

        if (isset($params['approval']) and $params['approval']){
        	$select->where('d.activate is null',1);
            $select->where('df.d_category in (?)',[1,2,6,7]);
            $select->group('d.id');
            $select->having('sum(distinct df.d_category)>15');
        }

        $select->where('d.del <> 1');

        if ($limit)
            $select->limitPage($page, $limit);

       // echo $select;die;
        $result = $db->fetchAll($select);

        if ($limit)
            $total = $db->fetchOne("select FOUND_ROWS()");
       
        return $result;
    }
}

function uploadPhotoStore($photo, $sn, $userStorage, $photo_id = null,$data_ex) {

		$located_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
										DIRECTORY_SEPARATOR.'public'.
										DIRECTORY_SEPARATOR.'photo'.
										DIRECTORY_SEPARATOR.'oppo-store-display'.
										DIRECTORY_SEPARATOR. $sn.
										DIRECTORY_SEPARATOR.'Before'.
										DIRECTORY_SEPARATOR;

		try {

			if (!is_dir($located_dir)) {
				@mkdir($located_dir, 0777, true);
			}

			$tem = explode('_located_', $photo);

			if (isset($tem[1]) and $tem[1]) {
				$uniqid    = $tem[0];
				$file_name = $tem[1];

				$uploaded_dir = APPLICATION_PATH.DIRECTORY_SEPARATOR.'..'.
												DIRECTORY_SEPARATOR.'public'.
												DIRECTORY_SEPARATOR.'photo'.
												DIRECTORY_SEPARATOR.'oppo-store-display'.
												DIRECTORY_SEPARATOR.'temp'.
												DIRECTORY_SEPARATOR.$userStorage->id.
												DIRECTORY_SEPARATOR.$uniqid.
												DIRECTORY_SEPARATOR;

				$new_file_name = null;

				if (is_file($uploaded_dir.$file_name)) {

					$info = pathinfo($uploaded_dir.$file_name);

					$new_file_name = 'brfore-'.md5($photo).'.'.$info['extension'];

					if ($info['extension'] == 'jpeg' || $info['extension'] == 'jpg'|| $info['extension'] == 'JPG') {$image = imagecreatefromjpeg($uploaded_dir.$file_name);
					} elseif ($info['extension'] == 'gif' || $info['extension'] == 'GIF') {$image = imagecreatefromgif($uploaded_dir.$file_name);
					} elseif ($info['extension'] == 'png' || $info['extension'] == 'PNG') {$image = imagecreatefrompng($uploaded_dir.$file_name);
					}

					imagejpeg($image, $located_dir.$new_file_name, 50);  

					$files = glob($uploaded_dir.'*');// get all file names
					
					

				}

				$data = array('photo' => $new_file_name);
				$OppoStoreDisplayImage = new Application_Model_OppoStoreDisplayImage();
				if ($photo_id) {					
				
					$data['description']  	= $data_ex['description'];
					$where                	= $OppoStoreDisplayImage->getAdapter()->quoteInto('id = ?', $photo_id);
					$OppoStoreDisplayImage->update($data, $where);

				} else {
					// insert
					$data['sn'] 		  	= $sn;
					$data['type']   		= 1;
					$data['description']  	= $data_ex['description'];
					print_r($data);
					$photo_id 				= $OppoStoreDisplayImage->insert($data);

				}
				
				// echo "<pre>";
				// print_r($data);
				// die;
				
			}
			return $photo_id;//success

		}

		 catch (exception $e) {

			return -1;//error
		}

	}