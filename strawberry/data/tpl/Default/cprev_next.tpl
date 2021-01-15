<div class="pages" style="clear: both">
<? if ($tpl['prev-next']['prev-link']){ ?>
<a href="<?=$tpl['prev-next']['prev-link']; ?>">&laquo;</a>
<? } ?>
 <?=$tpl['prev-next']['pages']; ?> 
<? if ($tpl['prev-next']['next-link']){ ?>
<a href="<?=$tpl['prev-next']['next-link']; ?>">&raquo;</a>
<? } ?>
</div>
<script type="text/javascript">
function changeMain(v){
  quickreply('comment', 0);
  var pars = '<?=($post ? 'id='.$post['id'] : 'go='.$go); ?>&cpage=' + v;
  new Ajax.Updater('commentslist', '<?=$config['http_script_dir']; ?>/inc/show.comments.php', 
    { method: 'get', 
	  parameters: pars, 
	  onComplete: function() { 
	  } 
	});
}
</script>