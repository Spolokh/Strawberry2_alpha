<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	Сортировка по дате
Plugin URI:     https://vk.com/club51280180
Description: 	Выставляет даты и сортирует новости по датам (еженедельные посты, ежемесячные, ежегодные).
Version: 		1.0 
Application: 	Strawberry
Author: 		Yury Spolokh
Author URI:     mailto:yury.d.spolokh@gmail.com
*/

define('HOUR', 60);
define('CUTEDATE', true);

function cuteDate($row)
{
    global $time, $config;

    $date = $row['date'];
    $last = round((time - $date) / HOUR);

    $h = date('H:i', $date);
    $d = date('d', $date);
    $w = date('w', $date);
    $m = date('m', $date);
    $y = date('Y', $date);

    if ($last < HOUR) :
        return sprintf('%s минут назад', $last); //$last .' минут назад';
    elseif ($d.$m.$y == date('dmY', time)) :
        return sprintf('Сегодня в %s', $h); //'Сегодня в '. $tm;
    elseif ($d.$m.$y == date('dmY', strtotime('-1 day'))) :
        return sprintf('Вчера в %s', $h); //'Вчера в '. $tm;
    else :
        return langdate($config['timestamp_active'], $date);
    endif;
}

function timePeriod()
{
    global $sql, $sort, $skip, $number;

    $where[] = 'hidden = 0'; 
    //$where[] = 'and' ;
    //$where[] = 'period = 0';

    $query = $sql->select([
        'news', 'select' => ['date'], 'where' => $where, 'orderby' => $sort, 'limit' => [($skip ?? 0), $number]
    ]);

    $count = count($query);

    if ($count < 1) {
        return 'date < '.time;
    }
    
    var_dump($query);
    return 'date < '.time;
}
