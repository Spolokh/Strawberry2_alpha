<div class="pagination pagination-centered">
<ul>
<?=($tpl['prev-next']['prev'] ? '<li><a class="prev icon-chevron-left" href="'.$tpl['prev-next']['prev'].'"></a>' : ''); ?>
<?=$tpl['prev-next']['pages'] ?> 
<?=($tpl['prev-next']['next'] ? '<li><a class="next icon-chevron-right" href="'.$tpl['prev-next']['next'].'"></a>' : ''); ?>
</ul>
</div>