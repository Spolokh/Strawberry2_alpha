<div class="pages">
<? if ($tpl['prev-next']['prev-link']){ ?>
<a href="<?=$tpl['prev-next']['prev-link']; ?>">&laquo;</a>
<? } ?>
 <?=$tpl['prev-next']['pages']; ?> 
<? if ($tpl['prev-next']['next-link']){ ?>
<a href="<?=$tpl['prev-next']['next-link']; ?>">&raquo;</a>
<? } ?>
</div>