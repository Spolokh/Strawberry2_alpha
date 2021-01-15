<div class="pages">
<? if ($tpl['prev-next']['prev-link']){ ?>
<a href="<?=$tpl['prev-next']['prev-link']; ?>">&#9668;</a>
<? } ?>
 <?=$tpl['prev-next']['pages']; ?> 
<? if ($tpl['prev-next']['next-link']){ ?>
<a href="<?=$tpl['prev-next']['next-link']; ?>">&#9658;</a>
<? } ?>
</div>