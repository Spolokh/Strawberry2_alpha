<?php
include_once substr(dirname(__FILE__), 0, -5).'/head.php';

if (isset($_POST["PHPSESSID"])) { 
   session_id($_POST["PHPSESSID"]);
}

ini_set("html_errors", "0");

// Check the upload
if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
    echo "ERROR:invalid upload";
    exit(0);
}


$album = ($_POST['album'] ? $_POST['album'] : $sql->last_insert_id('gallery', '', 'id'));
$id    = $sql->last_insert_id('images', '', 'id') + 1;
$img   = imagecreatefromjpeg($_FILES["Filedata"]["tmp_name"]); // Get the image and create a thumbnail

if (!$img) {
		echo "ERROR:could not create image handle ". $_FILES["Filedata"]["tmp_name"];
		exit(0);
}

	$width = imageSX($img);
	$height = imageSY($img);

	if (!$width || !$height) {
		echo "ERROR:Invalid width or height";
		exit(0);
	}

	// Build the thumbnail
	$target_width = 100;
	$target_height = 100;
	$target_ratio = $target_width / $target_height;

	$img_ratio = $width / $height;

	if ($target_ratio > $img_ratio) {
		$new_height = $target_height;
		$new_width = $img_ratio * $target_height;
	} else {
		$new_height = $target_width / $img_ratio;
		$new_width = $target_width;
	}

	if ($new_height > $target_height) {
		$new_height = $target_height;
	}
	if ($new_width > $target_width) {
		$new_height = $target_width;
	}

	$new_img = ImageCreateTrueColor(100, 100);
	if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, 0)) {	// Fill the image black
		echo "ERROR:Could not fill new image";
		exit(0);
	}

	if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
		echo "ERROR:Could not resize image";
		exit(0);
	}

	if (!isset($_SESSION["file_info"])) {
		$_SESSION["file_info"] = array();
	}

	// Use a output buffering to load the image into a variable
	ob_start();
	imagejpeg($new_img);
	$imagevariable = ob_get_contents();
	ob_end_clean();

	$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
	
	/*
        $ext   = end($ext = explode('.', $_FILES['Filedata']['name']));
        $type  = end($type = explode('/', $_FILES['Filedata']['type']));
	$image = preg_replace('/(.*?).'.$ext.'$/ie', "totranslit('\\1')", $_FILES['Filedata']['name']).'.'.$ext;
	
				
	@move_uploaded_file($_FILES['Filedata']['tmp_name'], $newsicon.'/'.$image);
	@rename($newsicon.'/'.$image, $newsicon.'/'.md5x($id).'.'.$ext);
        $image = md5x($id).'.'.$ext;
	$size = @getimagesize($newsicon.'/'.$image); // �������
	 
	if($size[0] > $config['newsicon']){
             @img_resize($newsicon.'/'.$image, $newsicon.'/thumbs/'.$image, $config['newsicon']);
        }
		  
        $sql->insert(array( ////////// ��������� � ��
	         'table'  => 'images',
	         'values' => array(            
                     'album'          => $album,
                     'width'          => $size[0],
                     'height'         => $size[1],
	                 'image'          => $image
	)));
        */
	 
	$_SESSION["file_info"][$file_id] = $imagevariable;
        echo 'FILEID:' . $file_id;	// Return the file id to the script
	
?>