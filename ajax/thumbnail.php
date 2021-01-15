<?php

include_once substr(dirname(__FILE__), 0, -5).'/strawberry/head.php';

if (isset($_SERVER['HTTP_X_FILE_NAME']) && isset($_SERVER['CONTENT_LENGTH'])){
	
	if (!$_SERVER['CONTENT_LENGTH']) {
		exit("No size");
	} 

	include classes_directory.'/class.upload.php';
	
	$handle = new Upload('php:'.$_SERVER['HTTP_X_FILE_NAME']); 
		
	if ($handle->uploaded) {

		$handle->file_new_name_body = $member['username'];  
		$handle->allowed 			= array('image/*');  
		$handle->forbidden          = array('application/*');
		//$handle->image_convert    = 'jpg';
		$handle->file_overwrite     = true;		
		$handle->Process(data_directory.'/userpics');

		// we check if everything went OK
		if ($handle->processed) {
			echo $config['path_userpic_upload'].'/'.$handle->file_dst_name;
		}
		
		$handle->Clean(); // we delete the temporary files
	} 
		
		//$name = $_SERVER['HTTP_X_FILE_NAME'];
		//$size = $_SERVER['CONTENT_LENGTH'];
		//file_put_contents("import/".$name, file_get_contents('php://input'));
}	//echo $name;
?>

	
