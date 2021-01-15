<article class="blog">
	<div class="date">
		<i class="icon-time"></i> <?=$tpl['post']['date']; ?>  &nbsp; 
		<a href="<?=$tpl['post']['user']['profile']; ?>"><i class="icon-user"></i> <?=$tpl['post']['author']; ?></a>
	</div>
	
	<h1 class="title"><?=$tpl['post']['title']; ?></h1>
    <div>
 
     [if-logged]
      <div style="height: 30px;" class="ya-share2" data-limit="4" data-services="vkontakte,facebook,gplus,twitter,lj,skype,telegram"></div>
     [/if-logged]
   
	<?php if ( $tpl['post']['attach'] and strpos($tpl['post']['attach'], 'полностью') === FALSE ) { ?>
                <a class="changevideo " href="#" title="Смотреть видео" onclick="$('attach').toggle(); return false;"><i class="icon-film"></i> Смотреть видео</a>
                <script type="text/javascript" src="/themes/js/uppod.js"></script>
		<script type="text/javascript">
			 new Uppod(<?=$tpl['post']['attach']?>); 
		</script>
        <? } ?>
    </div>	 
  
<?php if($tpl['post']['image']){ // icon-zoom-in ?>
	<figure id="news<?=$tpl['post']['id']; ?>" class="attach">
		<a class="full-icon" href="<?=$tpl['post']['image']; ?>" title="<?=$tpl['post']['description']; ?>">
			<img src="/uploads/thumb.php?src=<?=$tpl['post']['image'];?>&w=640&h=360&s=1" alt="<?=$tpl['post']['description']; ?>"/>
		</a>
		<div id="attach" style="display:none; top:0; left:0; position:absolute; width:640px; height:360px;"></div>
	</figure>
<?php } ?>
	
	<div id="story" class="full story">
	   <?=($tpl['post']['full-story'] ?: $tpl['post']['short-story'])?>
        </div>

	<div class="attr clearfix">
		<?php if ($tpl['post']['category']['name']) { ?>
		Категория: <?=$tpl['post']['category']['name']; ?> 
		<?php } ?>

		<?php if ( !empty($tpl['post']['tags']['id']) ) { ?>
		Теги: <?=$tpl['post']['tags']['name']; ?> 
		<?php }  ?>
	</div>

        <?=$tpl['post']['attach'] ?>
</article>
