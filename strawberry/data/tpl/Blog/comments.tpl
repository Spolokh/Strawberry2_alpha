<a name="<?=$tpl['comment']['number']; ?>"></a>
<div class="comment" style="margin-left: <?=($tpl['comment']['level'] * 20); ?>px;">
     <div id="comment<?=$tpl['comment']['id']; ?>" class="<?=$tpl['comment']['alternating']; ?>">

     <figure>
	    <img src="<?=$tpl['comment']['avatar']; ?>" width="40" />
     </figure>

<div class="title">
	<a href="javascript:insertext('[b]<?=$tpl['comment']['author']; ?>[/b],','','comments')"><?=$tpl['comment']['author']; ?></a> &nbsp;  &bull;
	<time><i><?=$tpl['comment']['date']; ?></i></time>
</div>

        <div class="story">
	        <?=$tpl['comment']['story']; ?>
        </div>
        <div class="story">
                 <?php if ($tpl['comment']['answer']) { ?>
                <blockquote>
                <i>Ответ Администратора</i><br/>
                <?=$tpl['comment']['answer']; ?>
                </blockquote>
                <?php } ?>
        </div>
        <div class="attr">
                <a title="Ответить" class="icon icon-reply" href="#" id="reply<?=$tpl['comment']['id']; ?>" onclick="quickreply('comment', <?=$tpl['comment']['id']; ?>); return false;">
                        <div class="tooltip tooltip-up">Ответить</div>
                </a>
                <? if ($tpl['comment']['if-right-have']){ ?>
                <a href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&newsid=<?=$tpl['post']['id']; ?>&comid=<?=$tpl['comment']['id']; ?>" target="_blank" title="Редактировать комментарий"></a>
                <!--a title="Удалить комментарий" class="icon icon-remove" href="<?=$config['http_script_dir']; ?>/index.php?mod=editcomments&action=dodeletecomment&newsid=<?=$tpl['post']['id']; ?>&delcomid[]=<?=$tpl['comment']['id']; ?>&deletecomment=yes" target="_blank"></a-->
                <? } ?>
        </div>
</div>
</div>