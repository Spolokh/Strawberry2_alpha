<?php
include('../../head.php');

$news_id = $_GET['id'];
$rating = $_GET['rating'];
$stars = $_GET['stars'];
$stars_size = $_GET['stars_size'];
$ip = $_GET['ip'];

if(!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", "$ip")){
	echo "Error.";
	exit;
}

if(!$stars or !$ip or !is_numeric($news_id) or !is_numeric($rating) or $rating < 1 or $rating > 10){
	echo "Error.";
	exit;
}

$settings = new PluginSettings('rating');
$ip_lock = $settings->settings['ip_lock'] == 'yes' ? true : false;
$cookie_lock = $settings->settings['cookie_lock'] == 'yes' ? true : false;

		$rate_id = $sql->last_insert_id('rating', '', 'id') + 1;

		$sql->insert(array(
				 'table'  => 'rating',
				 'values' => array(
								 'id'		 => $rate_id,
								 'post_id'	 => $news_id,
								 'ip'		 => $ip
								 )
			  ));

		$query = reset($sql->select(array('table' => 'news', 'where' => array("id = ".$news_id))));

		$new_rating = $query['rating'] + $rating;
		$new_votes = $query['votes'] + 1;
		
		$current_rating = $new_rating / $new_votes;
		
			$sql->update(array(
					  'table'  => 'news',
					  'where'  => array("id = ".$news_id),
					  'values' => array(
									 'rating'	=> $new_rating,
									 'votes'	=> $new_votes
									 )
					  ));
		
			foreach ($sql->select(array(
							 'table'  => 'rating',
							 'where'  => array("post_id = ".$news_id))) as $row){
									 $all_ip[$row['ip']] = true;
			}
		

			 $form = '<div class="ratingblock'.$stars_size.'">'."\n".
					 '<ul class="unit-rating'.$stars_size.'" style="width:'.($stars * $stars_size).'px">'."\n".
					 '<li class="current-rating'.$stars_size.'" style="width:'.($current_rating * $stars_size).'px;">'.'Currently '.$current_rating.'/'.$stars.'</li>'."\n";
						
				for($i = 1; $i <= $stars; $i++){ //Check IP/Cookie
					if(($ip_lock and isset($all_ip[$ip])) or ($cookie_lock and $_COOKIE['rating'.$news_id] == $news_id)){
						$form .= '<li>'.$i.'</li>'."\n";
					}
					else{
						$onclick = 'onclick="rate_it(\''.$news_id.'\', \''.$i.'\', \''.$config['http_script_dir'].'/plugins/rating/\', \''.$stars.'\', \''.$stars_size.'\', \''.$ip.'\')" onmouseout="document.getElementById(\'message'.$news_id.'\').innerHTML = \'\'" ';
						
						$form .= '<li><a '.$onclick.'href="#nogo" title="'.$i.' out of '.$stars.'" class="star'.$i.'">'.$i.'</a></li>'."\n";
					}
						
				}
						
			$form .=	'</ul></div><div id="message'.$news_id.'"></div>'."\n";		
			echo $form;
?>