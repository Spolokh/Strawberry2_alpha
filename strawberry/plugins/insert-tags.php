<?php
/**
 * @package skins/images
 * @access private
 */

/*
Plugin Name:	Insert Tags
Plugin URI:      http://cutenews.ru
Description:	Быстрые теги для добавления и редактирования новости.
Version:	1.0
Application: 	Strawberry
Author:		SwiZZeR
*/

add_filter('new-advanced-options', 'insert_tags', 1);
add_filter('edit-advanced-options', 'insert_tags', 1);
//add_filter('insert-tags-options', 'insert_tags', 1);

function insert_tags($location){
    global $config;
    ob_start();
?>
<div class="tags">
    <a href="javascript:insertext('<br />','','<?=$location ?>')" title='Перенос строки (Line break)'><img style="width:19px" width="19" height="18" src="/cp/skins/images/tags/br.gif"></a>
    <a href="javascript:insertext('<p>','</p>','<?=$location ?>')" title='Параграф (Paragraph)'><img style="width:19px" src="/cp/skins/images/tags/p.gif"></a>
    <a href="javascript:insertext('<b>','</b>','<?=$location ?>')" title='Жирный (Bold)'><img style="width:19px" src="/cp/skins/images/tags/b.gif"></a>
    <a href="javascript:insertext('<i>','</i>','<?=$location ?>')" title='Курсив (Italic)'><img style="width:19px" src="/cp/skins/images/tags/i.gif"></a>
    <a href="javascript:insertext('<u>','</u>','<?=$location ?>')" title='Подчеркнутый (Underline)'><img style="width:19px" src="/cp/skins/images/tags/u.gif"></a>
    <a href="javascript:insertext('<s>','</s>','<?=$location ?>')" title='Зачеркнутый (Strikethrough)'><img style="width:19px" src="/cp/skins/images/tags/s.gif"></a>
    <a href="javascript:insertext('<sub>','</sub>','<?=$location ?>')" title='Подстрочный (Subscript)'><img style="width:19px" src="/cp/skins/images/tags/sub.gif"></a>
    <a href="javascript:insertext('<sup>','</sup>','<?=$location ?>')" title='Надстрочный (Superscript)'><img style="width:19px" src="/cp/skins/images/tags/sup.gif"></a>
    <a href="javascript:insertext('<font color=&quot;&quot;>','</font>','<?=$location; ?>')" title='Цвет текста (Text color)'><img style="width:19px" src=/cp/skins/images/tags/color.gif></a>
    <a href="javascript:insertext('<font size=&quot;&quot;>','</font>','<?=$location; ?>')" title='Размер шрифта (Font size)'><img style="width:19px" src=/cp/skins/images/tags/size.gif></a>
    <a href="javascript:insertext('<ul>','</ul>','<?=$location; ?>')" title='Cписок ()'><img style="width:19px" src="/cp/skins/images/tags/ul.gif"></a>
    <a href="javascript:insertext('<li>','</li>','<?=$location; ?>')" title='Элемент списка ()'><img style="width:19px" src="/cp/skins/images/tags/li.gif"></a>
    <a href="javascript:insertext('<a href=&quot;&quot;>','</a>','<?=$location; ?>')" title='Ссылка (Link)'><img style="width:19px" src="/cp/skins/images/tags/url.gif"></a>
    <a href="javascript:insertext('<a href=&quot;mailto:&quot;>','</a>','<?=$location; ?>')" title='Email'><img style="width:19px" src="/cp/skins/images/tags/mailto.gif"></a>
    <a href="#" onclick="window.open('<?=$PHP_SELF; ?>?mod=images&area=<?=$location; ?>', '_Addimage', 'height=500,resizable=yes,scrollbars=yes,width=550');return false;" target="_Addimage"><img style="width:19px" src="/cp/skins/images/tags/img.gif"></a>
    <a href="javascript:insertext('<div align=&quot;&quot;>','</div>','<?=$location; ?>')" title='Выравнивание (Align)'><img style="width:19px" src="/cp/skins/images/tags/align.gif"></a>
</div>

<?php return ob_get_clean(); 
} ?>