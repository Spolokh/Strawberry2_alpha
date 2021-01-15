<?
// inc/show.add-comment.php
?>

Subject: Новый комментарий от <?=$name; ?>

URL: <?=cute_get_link($row); ?>

Заголовок: <?=$row['title']; ?>

Имя: <?=$name; ?>

IP: <?=$_SERVER['REMOTE_ADDR']; ?>

E-mail: <?=$mail; ?>

Homepage: <?=$homepage; ?>


Комментарий:
------------
<?=str_replace('<br />', "\n", $comments); ?>

Редактировать:
<?=$config['http_script_dir']; ?>/?mod=editcomments&action=editcomment&newsid=<?=$id; ?>&comid=<?=$comid; ?>

Удалить:
<?=$config['http_script_dir']; ?>/?mod=editcomments&action=doeditcomment&newsid=<?=$id; ?>&delcomid[]=<?=$comid; ?>&deletecomment=yes