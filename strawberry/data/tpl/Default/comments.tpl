<a name="<?=$tpl['comment']['number']; ?>"></a>
<div class="comment" style="margin-left: <?=($tpl['comment']['level'] * 20); ?>px;">
<div id="comment<?=$tpl['comment']['id']; ?>" class="<?=$tpl['comment']['alternating']; ?>">

<figure>
	<img src="<?=$tpl['comment']['avatar']; ?>" width="35" />
</figure>

<div class="title">
	<a href="javascript:insertext('[b]<?=$tpl['comment']['author']; ?>[/b],','','comments')"><?=$tpl['comment']['author']; ?></a> &nbsp;  
	<time><?=$tpl['comment']['date']; ?></time>
</div>

<div class="story">
	<?=$tpl['comment']['story']; ?>
</div>
<div class="story">
    <?=$tpl['comment']['answer']; ?>
</div>

<div class="attr">
действие: <a href="#" id="reply<?=$tpl['comment']['id']; ?>" onclick="quickreply('comment',<?=$tpl['comment']['id']; ?>); return false;">ответить</a>
<? if ($tpl['comment']['if-right-have']){ ?>
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&newsid=<?=$tpl['post']['id']; ?>&comid=<?=$tpl['comment']['id']; ?>" target="_blank" title="–едактировать комментарий">edit</a>
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&action=dodeletecomment&newsid=<?=$tpl['post']['id']; ?>&delcomid[]=<?=$tpl['comment']['id']; ?>&deletecomment=yes" target="_blank" title="”далить комментарий">del</a>
<? } ?>
</div>

</div>
</div>