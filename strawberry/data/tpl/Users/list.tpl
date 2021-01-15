<tr class="<?=$tpl['user']['alternating']; ?>">
	<td width="25" align="center"><?=$tpl['user']['id'];?>
	<td width="50"><img src="<?=$tpl['user']['avatar'];?>" width="50" style="border-radius:100%"/>
	<td><a href="<?=$tpl['user']['link']['home/user']; ?>">
			&nbsp; <b><?=$tpl['user']['username']; ?></b>
		</a>
	<td><a href="<?=$tpl['user']['author'] ?>"><?=t('Всего публикаций') ?>
		<?=$tpl['user']['publications']?></a>
	<td width="170"><div class="togglelist" onClick="Effect.toggle(this.down('ul'), 'blind', {duration: 0.5});">
					   <!--a><?=t('Действие'); ?></a>
					   <b class="caret"></b>
					   <ul style="display:none;">
						   <li><a id="addfrend" onClick="$('qwerty').setValue(this.id); return false;" href="#"><?php echo t('Добавить в друзья'); ?></a></li>
						   <li><a id="sendmail" onClick="$('qwerty').setValue(this.id); return false;" href="#"><?php echo t('Отправить сообщение'); ?></a></li>
						   <li><a id="blocked" onClick="$('qwerty').setValue(this.id); return false;" href="#"><?php echo t('Заблокировать'); ?></a></li>
					   </ul-->
					</div>