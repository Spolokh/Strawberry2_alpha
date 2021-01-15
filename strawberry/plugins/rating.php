<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name:	Rating (AJAX)
Plugin URI: 	http://english.cutenews.ru/forum
Description:	Allows you to rate news...
Version: 	    1.0
Application: 	Strawberry
Author: 	    FI-DD/SwiZZeR
Author URI:     http://english.cutenews.ru/forum/profile.php?mode=viewprofile&u=2
*/

add_filter('news-show-generic', 'Rating');

add_filter('template-active', 'template_rating_active');
add_filter('template-full', 'template_rating_full');

add_filter('options', 'rating_AddToOptions');
add_action('plugins', 'rating_CheckAdminOptions');

function rating_AddToOptions($options){
global $PHP_SELF;
		$echo = cute_lang("plugins/rating");
		$options[] = array(
				'name'		=> t('�������� ��������'),
				'url'	    => 'plugin=rating',
				'category'	=> 'news'
		);      return $options;
}

function rating_CheckAdminOptions(){
	if ($_GET['plugin'] == 'rating'){
        rating_AdminOptions();
    }
}

function Rating($tpl) {
	global $post;
	$tpl['rating'] = '<script>new Starry(\'default\', {startAt: \''.$post['rating'].'\'}); </script>';
	return $tpl;
}


/*
function Rating($tpl) {
	global $sql, $row, $allow_full_story, $config, $allow_active_news;

         $settings    = new PluginSettings('rating');

         $ip_lock     = $settings->settings['ip_lock'] == 'yes' ? true : false;
         $cookie_lock = $settings->settings['cookie_lock'] == 'yes' ? true : false;

		 $echo = cute_lang("plugins/rating");

		 $rating = $row['votes'] > 0 ? $row['rating'] / $row['votes'] : 0;
		 $middle = @round(($row['rating'] / $row['votes']), 1);

         	 $allr = array();
		 $tpl = str_replace('{rating-total}', $rating, $tpl);
		 $tpl = str_replace('{rating-middle}', $middle, $tpl);
		 
		 //$find_stars = preg_match("#{rating:(.*):(.*)}#", $tpl, $match); //
		 
		 $id = $row['id'];

		 if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
		 else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
		 else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
		 else $ip = "not detected";

		 $all_ip = array();

		 foreach ($sql->select(array('table'  => 'rating', 'where'  => array("post_id = $id"))) as $iprow){
			 $all_ip[$iprow['ip']] = true;
		 }
				 
		 if ($allow_full_story or $allow_active_news) {
		
			 $stars = trim($match[1]);
			 $stars_size = trim($match[2]);
			 $current_rating = $rating;
			 $form .=	
			 '<div class="ratingblock'.$stars_size.'">'."\n".
			 '<ul class="unit-rating'.$stars_size.'" style="width:'.($stars * $stars_size).'px">'."\n".
			 '<li class="current-rating'.$stars_size.'" style="width:'.($current_rating * $stars_size).'px;">'.'Currently '.$current_rating.'/'.$stars.'</li>'."\n";
												
			 for($i = 1; $i <= $stars; $i++){
								
				if(($ip_lock and isset($all_ip[$ip])) or ($cookie_lock and $_COOKIE['rating'.$id] == $id)){ //Check IP/Cookie
					$form .= '<li>'.$i.'</li>'."\n";
				} else {
					$onclick = 'onclick="rate_it(\''.$id.'\', \''.$i.'\', \''.$config['http_script_dir'].'/plugins/rating/\', \''.$stars.'\', \''.$stars_size.'\', \''.$ip.'\')" onmouseout="document.getElementById(\'message'.$id.'\').innerHTML = \'\'" ';
					$form .= '<li><a '.$onclick.'href="#nogo" title="'.$i.' out of '.$stars.'" class="star'.$i.'">'.$i.'</a></li>'."\n";
				}
			 }
						
			 $form .=	'</ul></div><div id="message'.$id.'"></div>'."\n";
			 $tpl['rating'] = str_replace('{rating:'.$stars.':'.$stars_size.'}', $form, $tpl);
		 }   

                 //$tpl = str_replace('{rating:'.$stars.':'.$stars_size.'}', $form, $tpl);
                 //$tpl['rating'] = $ip;
                 return $tpl;
}*/

function rating_AdminOptions() {
		global $sql, $PHP_SELF, $config, $config;

		$echo = cute_lang("plugins/rating");
	        $settings = new PluginSettings('rating');
	
	    if(!is_array($settings -> settings)){
			$settings -> settings = array(
				  'ip_lock'		=>'yes',
				  'cookie_lock'	=>'yes'
		   	 );   $settings -> save();
	   }

		$action = $_GET['action'];

		if (!$action) {
				echoheader('votes', $echo['NewsRating'], $echo['NewsRating']);
				if (!$sql->table_exists('rating', $config['dbname'])){
		?>
				 <form method="POST" action="<?=$PHP_SELF;?>?plugin=rating&amp;action=addtable">
				   <input type="submit" value="<?=$echo['AddTable'];?>">
				 </form>
                                 <br />
		<? } ?>
				
				<a href="<?=$PHP_SELF;?>?plugin=rating&amp;action=config">Configuration</a><br /><br />				
				<form method="POST" name="rating" action="<?=$PHP_SELF;?>?plugin=rating&amp;action=delete">
				<table border="0" width="100%" class="panel">
				  <tr align="center" class="enabled">
                                  <td width="1%">#
				  <td width="80%"><b><?=$echo['Title'];?></b>
				  <td width="10%"><b><?=$echo['Total'];?></b>
				  <td width="5%"><b><?=$echo['Rating'];?></b>
				  <td width="2%"><input type="checkbox" name="master_box" onclick="javascript:ckeck_uncheck_all('rating')">

				 <?
				  $news_per_page = 21;
				  $start_from = isset($_GET['start_from']) ? $_GET['start_from'] : '';
				  $i = $start_from;
				  $j = 0;
				  $total_news = $sql->table_count('news');

				  foreach ($sql->select(array('table' => 'news')) as $row) {
						 $rating = @round(($row['rating'] / $row['votes']), 2);
						 if ($j < $start_from){
						    $j++;
						    continue;
			          }
						    $i++;
				 ?>
				  <tr <?=cute_that(); ?>>
                                  <td width="1%"><?=$row['id'];?>
				  <td width="80%"><a href="<?=$PHP_SELF;?>?mod=editnews&id=<?=$row['id'];?>"><?=replace_news('show', $row['title']);?></a>
				  <td align="center" width="10%"><?=$row['votes'];?>
				  <td align="center" width="5%"><?=$rating;?>
				  <td align="center" width="2%"><input name="selected_ratings[]" value="<?=$row['id'];?>" type="checkbox">

				 <?
				  if ($i >= $news_per_page + $start_from){
						 break;
				      }
				  }

				  if ($start_from > 0){
						$previous = $start_from - $news_per_page;
						$npp_nav .= '<a href="'.$PHP_SELF.'?plugin=rating&amp;start_from='.$previous.'">&lt;&lt;</a>';
				  }

				  if ($total_news > $news_per_page){
						$npp_nav .= ' [ ';
						$enpages_count = @ceil($total_news / $news_per_page);
						$enpages_start_from = 0;
						$enpages = '';

						for ($j = 1; $j <= $enpages_count; $j++){
							if ($enpages_start_from != $start_from){
								$enpages .= '<a href="'.$PHP_SELF.'?plugin=rating&amp;start_from='.$enpages_start_from.'">'.$j.'</a> ';
						    } else {
								$enpages .= ' <b> <u>'.$j.'</u> </b> ';
						    }
							    $enpages_start_from += $news_per_page;
						   }

						 $npp_nav .= $enpages;
				         $npp_nav .= ' ] ';
				  }

				 if ($total_news > $i){
					 $npp_nav .= '<a href="'.$PHP_SELF.'?plugin=rating&amp;start_from='.$i.'">&gt;&gt;</a>';
				 }
				?>
				</tr>
			   </table>
				<p align="right"><input type="submit" value="<?=$echo['Del'];?>" accesskey="d"></p>           
				 <p align="center"><?=$npp_nav; ?></p>
			  </form>
		   <?
		   } elseif ($action == "delete") {
				   if (!$_POST['selected_ratings']){
				           msg('error',$echo['Error'],$echo['ErrorNoRow'].sprintf($echo['GoBack'], $PHP_SELF.'?plugin=rating'));
				   } else {
					  $total = count($selected_ratings);
				          echoheader('options', $echo['NewsRating'], $echo['NewsRating']);
				  ?>
				  <form method="POST" action="<?=$PHP_SELF;?>?plugin=rating&amp;action=dodelete">
				   <table border="0" cellpading="0" cellspacing="0" width="100%" height="100%">
					<tr>
					 <td><?=sprintf($echo['SureToDel'],sizeof($_POST['selected_ratings'])); ?><br /><br />
					  <input type="submit" value="<?=$echo['YesSure'];?>">&nbsp;<input type="button" value="<?=$echo['NoSure'];?>" onclick="javascript:document.location='<?=$PHP_SELF; ?>?plugin=rating'">
					<?
					foreach($_POST['selected_ratings'] as $ratingid){
							echo '<input type="hidden" name="selected_ratings[]" value="'.$ratingid.'">';
					}
					?>
					</table>
				   </form>
			 <?
		       }
		    } elseif ($action == "dodelete") {

				$deleted_ratings = 0;
				foreach ($_POST['selected_ratings'] as $id){
					$sql->update(array(
							'table' => 'news',
							'where' => array("id = $id"),
							'values' => array(
								'rating' => '0',
								'votes'  => '0'
						         ) 
					));

					$sql->delete(array(
							'table' => 'rating',
							'where' => array("post_id = $id"),
					));
						$deleted_ratings++;
				}
					echoheader('options', $echo['NewsRating'], $echo['NewsRating']);
					echo $echo['Deleted'].sprintf($echo['GoBack'], $PHP_SELF.'?plugin=rating');
                        
			   } elseif ($action == "addtable") {

		       $sql->createtable(array(
			        'table'	  => 'rating',
			        'columns' => array(
					'id' => array(
						'type'	         =>  'int',
						'auto_increment' =>  1,
						'primary'        =>  1,
					 ),
					'post_id' => array('type' =>  'int'),
					'ip'      => array('type' =>  'string'),
			        )
			));


		  echoheader('options', $echo['NewsRating'], $echo['NewsRating']);
		  echo $echo['TableAdded'].sprintf($echo['GoBack'], $PHP_SELF.'?plugin=rating');

	  } elseif($action == "config"){
		echoheader('options', 'Configuration', 'Configuration');
		echo 	'<b>Configuration</b><br />'.
			'<form method="post" action="'.$PHP_SELF.'?plugin=rating&action=save_config">'.
			'<table>'.
			'<tr><td title="Prevent multiple votes by IP check">Check IP</td><td>'.makeDropDown(array('yes' => 'yes', 'no' => 'no'), 'ip_lock', $settings -> settings['ip_lock']).'</td></tr>'.	
			'<tr><td title="Prevent multiple votes by cookie check">Check cookie</td><td>'.makeDropDown(array('yes' => 'yes', 'no' => 'no'), 'cookie_lock', $settings -> settings['cookie_lock']).'</td></tr>'.
			'</table>'.
			'<input type="submit" value="Save">'.
			'</form>';
                
	          } elseif($action == 'save_config'){
			    $settings -> settings = array(
			    	'ip_lock'     => $_POST['ip_lock'],
				    'cookie_lock' => $_POST['cookie_lock']
			   );
			
		    $settings -> save();
	            msg('info', 'Configuration', 'Configuration saved.', $PHP_SELF.'?plugin=rating');
	          } echofooter();
}

function template_rating_active($template){
		$template['{rating:x:y}']    = 'Printing news rating<br />&nbsp;&nbsp;x is the number of stars (2 - 10)<br />&nbsp;&nbsp;y is the width of the stars (10, 25 or 30)';
		$template['{rating-middle}'] = 'Printing average value';
		$template['{rating-total}']	 = 'Printing total votes';
        return $template;
}

function template_rating_full($template){
		$template['{rating:x:y}']	 = 'Printing news rating<br />&nbsp;&nbsp;x is the number of stars (2 - 10)<br />&nbsp;&nbsp;y is the width of the stars (10, 25 or 30)';
		$template['{rating-middle}'] = 'Printing average value';
		$template['{rating-total}']	 = 'Printing total votes';
        return $template;
}
?>