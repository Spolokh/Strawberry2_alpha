<article class="blog <?=($tpl['post']['hidden'] ? 'hidden' : 'active'); ?>">
        <div class="date">
                <i class="icon-time"></i> <?=$tpl['post']['date']; ?>  &nbsp; 
                <a href="<?=$tpl['post']['user']['profile']; ?>"><i class="icon-user"></i> <?=$tpl['post']['author']; ?></a>
        </div>
	
        <div class="title">
                <a href="<?=$tpl['post']['link']['type']; ?>"><?=$tpl['post']['title']; ?></a>
        </div>
	<?php if( $tpl['post']['image'] ) { ?>  
        <figure class="attach">
                <a href="<?=$tpl['post']['link']['type']; ?>" title="<?=$tpl['post']['title']; ?>">
                        <img loading="lazy" data-src="/uploads/thumb.php?src=<?=$tpl['post']['image'];?>&w=640&h=360&s=1" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="<?=$tpl['post']['title']; ?>" />
                </a>
        </figure>
	<?php } ?>

        <div class="short story">
                <?=$tpl['post']['short-story']; ?>
        </div>
   
        <div class="attr clearfix">
            <div class="more" title="Подробнее">
                 <a href="<?=$tpl['post']['link']['type']; ?>">Подробнее <i class="icon-double-angle-right"></i></a>
            </div>
            <div class="icons">                           
                 <a class="showtooltip  icon-eye-open" data-tooltip="Просмотров: <?=$tpl['post']['views'] ?>"> 
                         <?=$tpl['post']['views'] ?>
                         <div class="tooltip tooltip-up"></div>
                 </a>
                 <a class="showtooltip icon-comment" data-tooltip="Комментариев: <?=$tpl['post']['comments'] ?>"> 
                         <?=$tpl['post']['comments'] ?>
                         <div class="tooltip tooltip-up"></div>
                 </a>
                 <a class="showtooltip icon-heart" data-tooltip="Голосов: <?=$tpl['post']['votes'] ?>" href="#" data-id="<?=$tpl['post']['id'] ?>">
                         <?=$tpl['post']['votes'] ?>
                         <div class="tooltip tooltip-up"></div>
                 </a>      
         </div>
     </div>      
</article>
