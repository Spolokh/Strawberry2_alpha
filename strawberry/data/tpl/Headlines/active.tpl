<li>
<?=($tpl['post']['type'] == 'page' ? str_repeat('&nbsp;', ($tpl['post']['_']['level'] - 1)) : ''); ?>
<a href="<?=$tpl['post']['link']['post']; ?>"><?=$tpl['post']['title']; ?></a>
<? if ($tpl['post']['if-right-have']){ ?>
<small>(<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&id=<?=$tpl['post']['id']; ?>" title="Редактировать новость">edit</a>
<a href="<?=$config['http_script_dir']; ?>/index.php?mod=editnews&action=delete&selected_news[]=<?=$tpl['post']['id']; ?>" title="Удалить новость">del</a>)</small>
<? } ?>
</li>