<a name="<?=$tpl['comment']['number']; ?>"></a>
<div class="comment" style="margin-left: <?=($tpl['comment']['level'] * 20); ?>px;">
<div id="comment<?=$tpl['comment']['id']; ?>" class="<?=$tpl['comment']['alternating']; ?>">
<div class="date"><?=$tpl['comment']['date']; ?></div>
<div class="title"><?=$tpl['comment']['author']; ?>
<? if ($tpl['comment']['homepage']){ ?>
<small>(<a href="<?=$tpl['comment']['homepage']; ?>"><?=$tpl['comment']['homepage']; ?></a>)</small>
<? } ?>
</div>
<hr align="left">
<div class="story"><?=$tpl['comment']['story']; ?></div>
<hr align="right">
<div class="attr">
действие:
<? if ($tpl['comment']['if-right-have']){ ?>
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&newsid=<?=$tpl['post']['id']; ?>&comid=<?=$tpl['comment']['id']; ?>" target="_blank" title="Редактировать комментарий">edit</a>
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&action=dodeletecomment&newsid=<?=$tpl['post']['id']; ?>&delcomid[]=<?=$tpl['comment']['id']; ?>&deletecomment=yes" target="_blank" title="Удалить комментарий">del</a>
<? } ?>
<a href="#" id="reply<?=$tpl['comment']['id']; ?>" onclick="quickreply(<?=$tpl['comment']['id']; ?>); return false;">ответить</a>
</div>
</div>
</div>