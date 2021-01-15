<article id="post<?=$tpl['post']['id']; ?>" class="post <?=($tpl['post']['hidden'] ? 'hidden' : 'active'); ?>">
<div class="<?=$tpl['post']['alternating']; ?>">
<div class="title"><a href="<?=$tpl['post']['link']['post']; ?>"><?=$tpl['post']['title']; ?></a></div>
<div class="date">
	<i class="icon-time"></i> <?=$tpl['post']['date']; ?> &nbsp; &nbsp;
	<a title="<?=t('Автор: ') . $tpl['post']['author']; ?>" href="<?=$tpl['post']['user']; ?>"><i class="icon-user"></i> <?=$tpl['post']['author']; ?></a>
</div>
<?php if($tpl['post']['image']){ ?>
		<figure>
        <a class="icon" href="<?=$tpl['post']['link']['post']; ?>" title="<?=$tpl['post']['description']; ?>">
            <img src="/uploads/thumb.php?src=<?=$tpl['post']['image']; ?>&w=<?=$config['newsicon'];?>&h=<?=$config['newsicon'];?>" alt=""/>
		    <!--img src="<?//=$tpl['post']['icon']; ?>" width="<?//=$config['newsicon'];?>"-->              
        </a>
		</figure>
	<?php } ?>

	<div class="short story" style="margin-left:<?=($tpl['post']['image'] ? $config['newsicon'] : '0'); ?>px;">
	      <?=$tpl['post']['short-story']; ?>
	      <?=($tpl['post']['full-story'] ? ' <a href="'.$tpl['post']['link']['post'].'">'.$tpl['post']['more'].'</a>' : ''); ?> 
	</div>


<div class="attr">
   <?//=$tpl['post']['category'] ? 'Категория: '.$tpl['post']['category']['name'] : ''; ?> 
   <?=$tpl['post']['tags']['name'] ? '<i class="icon-tags"></i>&nbsp;' . $tpl['post']['tags']['name']  : ''; ?> 
       &nbsp; &nbsp; <i class="icon-eye-open"></i>&nbsp; (<?=$tpl['post']['views']; ?>) |
    <a href="<?=$tpl['post']['link']['post']?>#comments">Комментариев: (<?=$tpl['post']['comments']?>)</a>
</div>
</div>

<? if ($tpl['post']['if-right-have']){ ?>
<div class="actions" style="position: absolute; top:7px; right:0px;">
<a style="margin: 0 4px" class="icon-pencil" href="index.php?mod=editnews&id=<?=$tpl['post']['id']; ?>" title="Редактировать"></a>
<a class="icon-remove" href="index.php?mod=editnews&action=delete&selected_news[]=<?=$tpl['post']['id']; ?>" title="Удалить"></a>
<div>
<? } ?>
</article>