<?php

include_once dirname(__DIR__).'/strawberry/head.php';

if ( !($member || $is_logged_in) ) { 
	return $cute->msg( 'Achtung!',  t('Необходима авторизация на сайте') );	
    exit;
}

if (isset($_FILES['file']['name']) and empty($_FILES['file']['error']))
{ 
	if (empty($_FILES['file']['size']))
	{
		exit ();
	}

	//if ( strpos($_FILES['file']['type'], 'image') !== FALSE ) {
	//	$values ['type'] = $_FILES['file']['type'];
	//}

	if ( !is_uploaded_file($_FILES['file']['tmp_name']) ) {
		# code...
	}
	
	$handle = new Upload($_FILES['file']);
	
	if ($handle ->uploaded) 
	{	
		//$handle ->allowed = ['image/*'];
		$handle ->file_new_name_body = uniqid('Image_');  
		//$handle ->image_convert  = 'jpg';
		$handle ->file_overwrite = true ;
		$handle ->process(UPLOADS.'/files');
		  
		if ($handle ->processed) {

			//$values['size']   = $handle->file_src_size;
			//$values['file']   = $handle->file_dst_name; 
			//$values['thumb']  = $handle->file_dst_name; 
			//$values['width']  = $handle->image_dst_x;
			//$values['height'] = $handle->image_dst_y;
			//$values['folder'] = 'Image';
			//$values['ext']    = $handle->file_dst_name_ext;
			//$sql->insert(['attach', 'values' => $values]);

			$json = [
				'filelink' => '/uploads/files/'.$handle->file_dst_name, 
				'filename' => $handle->file_dst_name
			];

			$result = stripslashes(json_encode($json));
			$values = [];
		} 
	}	

	$handle->clean();
}	
exit ($result);
