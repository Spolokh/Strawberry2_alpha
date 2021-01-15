<h3><?=t('Добавить комментарий') ?></h3>

<a name="comments"></a>
<div id="comment_form">
	<?php if (!$tpl['if-logged']){ ?>
	<input type="text" name="name" value="<?=$tpl['form']['saved']['name']; ?>" placeholder="Имя" required /><br />
	<input type="email" name="mail" value="<?=$tpl['form']['saved']['mail']; ?>" placeholder="E-mail"><br />
	<input type="text" name="page" value="<?=($tpl['form']['saved']['page'] ? $tpl['form']['saved']['page'] : ''); ?>" placeholder="http://"><br />
	<?php } ?>
	
	<div id="blokbbcodes">
		<noindex><?=tpl('bbcodes', 1) ?></noindex>
	</div>
	
	<?=$tpl['form']['smilies']; ?><br/>
	<textarea name="comments" id="comments" onkeydown="message_onkeydown(this, <?=$config['comments_length']; ?>);" placeholder="Ваш комментарий" required></textarea><br/>
	
	<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme">Запомнить вас?</label>
	<label for="sendcomments"><input type="checkbox" id="sendcomments" name="sendcomments"> Посылать комментарии на ваш e-mail?</label><br/> 
	
	<input type="hidden" name="id" value="<?=( isset($post['id'])? intval($post['id']) : 0 )?>">
	<!--input type="hidden" name="type" value="post"-->
	<input type="hidden" name="parent" id="parent" value="0">
	<input type="hidden" name="template" value="<?=$tpl['template']; ?>">
	<input type="submit" id="submit" name="submit" value="  Добавить  " accesskey="s" />
</div>

<noscript>
<div class="error">Комментарий вы не добавите. Нужно разрешить использовать JavaScript.</div>
</noscript>