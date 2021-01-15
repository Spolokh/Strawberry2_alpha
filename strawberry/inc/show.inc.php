<?php

/**
 * @package Show
 * @access private
 */
 
defined('rootpath') or die('No direct access allowed.');

do	{
    if ($allow_active_news or $allow_full_story) {
    	include includes_directory.'/show.news.php';
    }
	
	if (empty($config['addcomments'])) {
		return;
	}

    $allow_comments     = run_filters('allow-comments', $allow_comments); 
    $allow_comment_form = run_filters('allow-comment-form', $allow_comment_form);

    if ($allow_comments and !$allow_comment_form) { 
        include includes_directory.'/show.comments.php';
    }

    if ($allow_comments and $allow_comment_form) {      
		echo '<div id="commentslist">' . PHP_EOL;
		include includes_directory.'/show.comments.php';
		echo '</div>';
		include includes_directory.'/show.comment-form.php';            
	}  
} while(0);
