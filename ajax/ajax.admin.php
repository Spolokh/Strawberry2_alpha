 <?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__).'/strawberry/head.php';

if ( !($member || $is_logged_in) ) { 
	header('HTTP/1.0 500 Internal Server Error');
	exit('No direct access allowed 2.');
}

if (!isset($member['usergroup']) or !cute_get_rights('edit_all')){
	header('HTTP/1.0 500 Internal Server Error');
	exit('No direct access allowed 1.');
}

$action = $_POST['action']) ?? null;

foreach ($_POST as $k => $v){
   $$k= htmlspecialchars($v); /////////////////////////
}

//header('Content-type: text/html; charset='.$config['charset']);

if ( isset($action) ) {

	if ( $action == 'hide_post' and !empty($id) ): /////// отключение поста в новостях /////////////////////
        
		if (!$row = reset($sql->select(['news', 'select' => ['id'], 'where' => $id])))
		{
			header('HTTP/1.0 500 Internal Server Error'); //	echo 'Запись с таким ID не найдена';
			exit('Запись с таким ID не найдена');
		}

		$hidden = empty($hidden)? true : false ;

		$sql->update(['news', 'where' => $row['id'], 'values' => ['hidden' => $hidden]]);
	endif; 
	
	/////// отключение поста в новостях ////////
	
	if ($action == 'deleted_img'):
        foreach ($sql->select(array('shop', 'where' => array("id = $selected_id"))) as $row):
            
			if ($img_arr = explode(',', $row['image'])):
                $imgs = array();
                unset($img_arr[$arr_img]);
                foreach($img_arr as $v => $k){
                    $imgs[] = $k;
                }
                           
            endif;
        endforeach;   //echo join(',', $imgs);
        
		$sql->update(array('shop', 'where' => array("id = $selected_id"),
            'values' => array('image' => join(',', $imgs)
        )));

		if (file_exists(upload_files. DS. 'shop' .DS. $selected_img)){
			@unlink(upload_files.DS.'shop'.DS.$selected_img); 
		}

		if (file_exists(upload_files.'/shop/thumbs/'.$selected_img)){
			@unlink(upload_files.'/shop/thumbs/'.$selected_img);
		}

           echo t('Изображение успешно удалено');
    endif;
 
    if( $action == 'edit_img_desc' ):
          /* $sql->update( array('attach', 
				 'where'  => array("id = $selected_id"), 
				 'values' => array('description' => replace_news('add', $desc))) );
				 */
    endif;
}