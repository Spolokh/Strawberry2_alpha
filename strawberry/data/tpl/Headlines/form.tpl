<a name="comments"></a>
<div>Добавить комментарий к &laquo;<?=$tpl['post']['title']; ?>&raquo;</div>
<div class="comment_form">
<? if (!$tpl['if-logged']){ ?>
Имя<br /><input type="text" name="name" value="<?=$tpl['form']['saved']['name']; ?>"><br />
E-mail<br /><input type="text" name="mail" value="<?=$tpl['form']['saved']['mail']; ?>"><br />
Домстраница<br /><input type="text" name="homepage" value="<?=($tpl['form']['saved']['homepage'] ? $tpl['form']['saved']['homepage'] : 'http://'); ?>"><br />
<? } ?>
<?=$tpl['form']['smilies']; ?>
<br />
<textarea cols="40" rows="6" name="comments" style="overflow-x: hidden;overflow-y: visible;width: 350px;height: 100px;"></textarea>
<br />
<input type="submit" name="submit" value="   Добавить   " accesskey="s" style="cursor: hand;">
<br />
<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme" value="on" checked> Запомнить вас?</label>
<label for="sendcomments"><input type="checkbox" id="sendcomments" name="sendcomments" value="on"> Посылать комментарии на ваш e-mail?</label>
<br />
<noindex><?=$tpl['form']['bbcodes']; ?></noindex>
</div>