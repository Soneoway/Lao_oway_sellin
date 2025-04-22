<?php
$userStorage = Zend_Auth::getInstance()->getStorage()->read();
if ($this->getRequest()->getMethod() == 'POST'){
  $QCreateDocument = new Application_Model_CreateDocument();
    $QDistributor = new Application_Model_Distributor();

   	echo "<pre>";
    // print_r($_POST);die;
  $select          = $this->getRequest()->getParam('select');
  $file          = $this->getRequest()->getParam('photo');
  $id          = $this->getRequest()->getParam('id');
  
  	for ($i=0; $i <count($select) ; $i++) { 
  		// echo  $select[$i]."<br >";

// 		for($f=0;$f<count($file[$select[$i]]);$f++){
// 			$data = array(
//   			    'd_id'				        => $id,
//             'd_category' 		      => $select[$i],
//             'd_document'          => $file[$select[$i]][$f],
//             'd_update'            => date('Y-m-d H:i:s')
//         );
// 			$file[$select[$i]][$f]."<br>";
// // print_r($data);die;
			
//   	$QCreateDocument->insert($data);

// 	    }
      $data = array(
            'd_id'                => $id,
            'd_category'          => $select[$i],
            'd_document'          => $file[$select[$i]][$f],
            'd_update'            => date('Y-m-d H:i:s')
        );
     
      foreach ($file[$select[$i]] as $k => $p) {
          $data_ex = array(
            'description'=> @$description[$k]
            );
          if($p){
          if (strstr($p, '_located_'))
            if (strstr($k, 'old_')) {
              $old_photo_id = str_replace('old_', '', $k);
              $photo_ids[] = $QCreateDocument->uploadFile($p, $sn, $userStorage,$old_photo_id ,$data );
            } else {
              
              $photo_ids[] = $QCreateDocument->uploadFile($p, $sn, $userStorage,null,$data);
            } elseif ($p) {
              if (strstr($k, 'old_')) {
                $old_photo_id = str_replace('old_', '', $k);
                $photo_ids[] = $old_photo_id;
              }
            }
                }}



  	}
  $db = Zend_Registry::get('db');

              $select = $db->select()
                  ->from(array('p' =>'distributor_file'),
                      array( 'p.d_category'));
                  $select->where('p.d_id = ?', $data['d_id']);
                

             

              $result = $db->fetchAll($select);
                print_r($result);

              $aa = array();
              for ($i=0; $i <count($result) ; $i++) { 
              $aa[]=  $result[$i]['d_category'];


              } 
                // print_r($aa);
                print_r(array_unique($aa));
      $check = array( "1","2","6","7");
          print_r($check);
      $activate = array_intersect($check, array_unique($aa));
        
      if(count($activate)==4){
        $where = $QDistributor->getAdapter()->quoteInto('id = ?', $data['d_id']);
        $data = array(
            'activate'  => 1
              
            );

              // $QDistributor->update($data,$where);
      }
  	 $this->_redirect(HOST . 'sales/distributor-document?id='.$id);
 // print_r($data);
 // print_r($select);
 // print_r($file);
 }

