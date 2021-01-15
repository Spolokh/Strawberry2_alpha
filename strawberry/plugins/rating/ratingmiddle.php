<?php
include('../../head.php');
$news_id = $_GET['id'];
$what = $_GET['what'];

		$query = reset($sql->select(array('table' => 'news', 'where' => array("id = ".$news_id))));

		$new_rating = $query['rating'];
		$new_votes = $query['votes'];
		
		$current_rating = @round($new_rating / $new_votes, 2);

		if($what == 'middle') echo $current_rating;
		if($what == 'total') echo $new_votes;
?>