<article id="news<?=$tpl['post']['id']; ?>" class="post">

	<div class="fonts" style="float:right"> 
	  <?=$tpl['post']['rating']; ?> 
	  <small><a href="javascript:changeFontSize(-1)">A</a></small> 
	  <big><a href="javascript:changeFontSize(1)">A</a></big> 
	</div>

	<div class="date"><i class="icon-time"></i> <?=$tpl['post']['date']; ?> &nbsp; &nbsp;
	<a href="<?=$tpl['post']['user']; ?>"><i class="icon-user"></i> <?=$tpl['post']['author']; ?></a></div>

	<h1><a><?=$tpl['post']['title']; ?></a></h1>
	
	<? if($tpl['post']['image']){ ?>
	<a class="full-icon" href="<?=$tpl['post']['image']; ?>" title="<?=$tpl['post']['description']; ?>" >
	    <img src="/uploads/thumb.php?src=<?=$tpl['post']['image']; ?>&w=300&h=300"  data-src="<?=$tpl['post']['image']; ?>" alt="" />
	</a>
	<? } ?>


<div class="story full">
<?=($tpl['post']['full-story']?: $tpl['post']['short-story']); ?>
</div>

<?php if ( $tpl['post']['attach'] and strpos($tpl['post']['attach'], 'полностью') === FALSE ){ ?>
<div id="attach" style="width:640px;"></div>

<script type="text/javascript">
	new Uppod(<?=$tpl['post']['attach']?>); 
</script>
<? } ?>

<?php if ($tpl['post']['attachment']){
	echo $tpl['post']['attachment'];
} 
 
if ($tpl['post']['pages']){ ?>
<center>(<?=$tpl['post']['pages']; ?>)</center>
<? } ?>

<div class="attr">
<? if ($tpl['post']['category']['name']){ ?>
Категория:  <a title="<?=$tpl['post']['category']['name']; ?>" href="<?=$tpl['post']['category']['url']; ?>">
				<?=$tpl['post']['category']['name']; ?>
			</a> 
<? } ?>
<? if ($tpl['post']['tags']['id'] != ''){ ?>
Теги: <?=$tpl['post']['tags']['name']; ?> 
<? } ?>

 <a title="печать" href="<?=$tpl['post']['link']['print.php/post']; ?>"></a> 
 <a title="RSS комментариев" href="<?=$tpl['post']['link']['rss.php/post']; ?>"></a> 
</div>

</article>