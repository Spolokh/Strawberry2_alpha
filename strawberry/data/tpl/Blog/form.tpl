<h3><?=t('Добавить комментарий') ?></h3>

<a name="comments"></a>
<div id="comment_form">
	<? if (!$tpl['if-logged']){ ?>
	<input type="text" name="name" maxlength="50" value="<?=$tpl['form']['saved']['name'] ?>" placeholder="Ваше имя" required /><br />
	<input type="email" name="mail" maxlength="50" value="<?=$tpl['form']['saved']['mail'] ?>" placeholder="E-mail"><br />
	<input type="text" name="page" maxlength="50" value="<?=$tpl['form']['saved']['homepage'] ?>" placeholder="http://"><br />
	<? } ?>
	
	<div id="blokbbcodes">
		<noindex>
			<?=tpl('bbcodes', 1); ?>
		</noindex>
	</div>
	
<?=$tpl['form']['smilies']; ?><br/>
<textarea name="comments" id="comments" rows="1" placeholder="Ваш комментарий" required></textarea><br/>
<label class="option" for="rememberme">
	<input type="checkbox" id="rememberme" name="rememberme" value="on" checked>
	<span class="checkbox"></span>
	Запомнить вас?
</label>
<label class="option" for="sendcomments">
	<input type="checkbox" id="sendcomments" name="sendcomments" value="on">
	<span class="checkbox"></span>
	Посылать комментарии на ваш e-mail?
</label>
	<br/> 
	
<input type="hidden" name="parent" id="parent" value="0">
<input type="hidden" name="id" value="<?=$post['id']?>">
<input type="hidden" name="type" value="<?=($post['id'] ? 'post': '')?>">
<input type="hidden" name="template" value="<?=$tpl['template']; ?>">
<input type="submit" id="submit" name="submit" value="  Добавить  " accesskey="s" />
</div>
