<?php
/**
 * @package Show
 * @access private
 */
 
include_once substr(dirname(__FILE__), 0, -4).'/head.php';

$where = [];
$where = run_filters('comments-where', $where);

if (!empty($post)) {
	$where[] = 'post_id = '.$post['id'];
	$where[] = 'and';
	$where[] = 'type = post';
}

if (!$config['cnumber']){
	$config['cnumber'] = $sql->tableCount('comments');
}

$query = $sql->select(['comments', 'where' => $where, 'orderby' => ['date', 'ASC'], 'limit' => [($cpage ?? 0), $config['cnumber']]]);

if (!$query = build_tree($query)) {
    return;
}

$count = $sql->count(['comments', 'where' => $where]);
$users = $sql->UsersByCommIDs($query); // added

foreach ($query as $k => $row) 
{
	$tpl['comment']      = $row;
	$tpl['comment']['_'] = $row;

    if ($tpl['post']['if-right-have'] or ((cute_get_rights('edit') or cute_get_rights('delete')) and $member['username'] == $row['author'])){
        $tpl['comment']['if-right-have'] = true;
    } else {
        $tpl['comment']['if-right-have'] = false;
    }

    if ($row['user_id'])
    {
        if ($users[$row['author']]['mail'] and !$users[$row['author']]['hide_mail']) {
            $tpl['comment']['mail'] = $users[$row['author']]['mail'];
		}

        //$tpl['comment']['homepage']    = $users[$row['author']]['homepage'];
        $tpl['comment']['if-user']     = true;
        $tpl['comment']['avatar']      = $config['path_userpic_upload'].'/'.($users[$row['author']]['avatar'] ? $row['author'].'.'.$users[$row['author']]['avatar'] : 'no_avatar.jpg');
        $tpl['comment']['location']    = $users[$row['author']]['location'];
        $tpl['comment']['about']       = run_filters('news-comment-content', $users[$row['author']]['about']);
        $tpl['comment']['author']      = $users[$row['author']]['name'];
        $tpl['comment']['username']    = $users[$row['author']]['username'];
        $tpl['comment']['user-id']     = $users[$row['author']]['id'];
        $tpl['comment']['lj-username'] = $users[$row['author']]['lj_username'];

    } else {
	    $tpl['comment']['avatar']     = $config['path_userpic_upload'].'/no_avatar.jpg';
        $tpl['comment']['if-user'] 	  = false;
    }

    if ($config['auto_wrap'])
    {
       $row['comment'] = preg_replace('/([^ .]{'.$config['auto_wrap'].'})/', '\\1', $row['comment']);
    }

    $tpl['comment']['date']   = langdate($config['timestamp_comment'], $row['date']);
    $tpl['comment']['story']  = run_filters('news-comment-content', $row['comment']);
    $tpl['comment']['reply']  = run_filters('news-comment-content', $row['reply']);
    $tpl['comment']['alternating'] = cute_that('cn_comment_odd', 'cn_comment_even');
    $tpl['comment']['number']  = ($k + 1);
    
    $tpl['comment']  = run_filters('comments-show-generic', $tpl['comment']);

    ob_start();
    include templates_directory.DS.$tpl['template'].DS.'comments.tpl';
    $output = ob_get_clean();
    $output = run_filters('news-comment', $output);
    $output = replace_comment('show', $output, true);
    echo $output;
}

// << Previous & Next >>
$cprev_next_msg = $template_cprev_next;

//----------------------------------
// Previous link
//----------------------------------
if ($cpage){
    $tpl['prev-next']['prev-link'] = cute_get_link(array_merge($post, ['cpage' => ($cpage - $config['cnumber'])]), 'cpage');
} else {
    $tpl['prev-next']['prev-link'] = '';
    $no_cprev = true;
}

//----------------------------------
// Pages
//----------------------------------
if ($config['cnumber']){
    $pages_count   = @ceil($count / $config['cnumber']);
    $pages_cpage   = 0;
    $pages         = [];
    $pages_section = (int)$config['cpages_section'];
    $pages_break   = (int)$config['cpages_break'];

    if ($pages_break and $pages_count > $pages_break){
        for ($j = 1; $j <= $pages_section; $j++){
            if ($pages_cpage != $cpage){
                //$pages[] = '<a href="'.cute_get_link(array_merge($post, array('cpage' => $pages_cpage)), 'cpage').'">'.$j.'</a>';
				$pages[] = '<a href="#" onclick="changeMain('.$pages_cpage.'); return false;">'.$j.'</a>';
            } else {
                $pages[] = '<b>'.$j.'</b>';
            }

            $pages_cpage += $config['cnumber'];
        }

        if (((($cpage / $config['cnumber']) + 1) > 1) and ((($cpage / $config['cnumber']) + 1) < $pages_count)){
            $pages[] = ((($cpage / $config['cnumber']) + 1) > ($pages_section + 2)) ? '...' : '';
            $page_min = ((($cpage / $config['cnumber']) + 1) > ($pages_section + 1)) ? ($cpage / $config['cnumber']) : ($pages_section + 1);
            $page_max = ((($cpage / $config['cnumber']) + 1) < ($pages_count - ($pages_section + 1))) ? (($cpage / $config['cnumber']) + 1) : $pages_count - ($pages_section + 1);

            $pages_cpage = ($page_min - 1) * $config['cnumber'];

            for ($j = $page_min; $j < $page_max + ($pages_section - 1); $j++){
                if ($pages_cpage != $cpage){
                	//$pages[] = '<a href="'.cute_get_link(array_merge($post, array('cpage' => $pages_cpage)), 'cpage').'">'.$j.'</a>';
					$pages[] = '<a href="#" onclick="changeMain('.$pages_cpage.'); return false;">'.$j.'</a>';
                } else {
                	$pages[] = '<b>'.$j.'</b>';
                }

                $pages_skip += $config['cnumber'];
            }

            $pages[] = ((($cpage / $config['cnumber']) + 1) < $pages_count - ($pages_section + 1)) ? '...' : '';
        } else {
        	$pages[] = '...';
        }

        $pages_cpage = ($pages_count - $pages_section) * $config['cnumber'];

        for ($j = ($pages_count - ($pages_section - 1)); $j <= $pages_count; $j++){
            if ($pages_cpage != $cpage){
            	//$pages[] = '<a href="'.cute_get_link(array_merge($post, array('cpage' => $pages_cpage)), 'cpage').'">'.$j.'</a>';
				$pages[] = '<a href="#" onclick="changeMain('.$pages_cpage.'); return false;">'.$j.'</a>';
            } else {
            	$pages[] = '<b>'.$j.'</b>';
            }

            $pages_cpage += $config['cnumber'];
        }
    } else {
         for ($j = 1; $j <= $pages_count; $j++){
            if ($pages_cpage != $cpage){
            	//$pages[] = '<a href="'.cute_get_link(array_merge($post, array('cpage' => $pages_cpage)), 'cpage').'">'.$j.'</a>';
				$pages[] = '<a href="#" onclick="changeMain('.$pages_cpage.'); return false;">'.$j.'</a>';
            } else {
            	$pages[] = ' <b>'.$j.'</b> ';
            }

            $pages_cpage += $config['cnumber'];
        }
    }

    $tpl['prev-next']['pages']        = join(' ', $pages);
    $tpl['prev-next']['current-page'] = (($cpage + $config['cnumber']) / $config['cnumber']);
    $tpl['prev-next']['total-pages']  = $pages_count;
}

//----------------------------------
// Next link
//----------------------------------
if ($cpage + $config['cnumber'] < $count)
{
	$tpl['prev-next']['next-link'] = cute_get_link(array_merge($post, ['cpage' => ($cpage + $config['cnumber'])]), 'cpage');
} else {
    $tpl['prev-next']['next-link'] = '';
    $no_cnext = true;
}

if (!$no_cprev or !$no_cnext){
	include templates_directory.'/'.$tpl['template'].'/cprev_next.tpl';
}
?>