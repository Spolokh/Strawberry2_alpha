<div class="pagination pagination-centered">
	<ul>
<?=($tpl['prev-next']['prev-link'] ? '<li><a class="prev icon-chevron-left" href="'.$tpl['prev-next']['prev-link'].'"></a>' : ''); ?>
<?=$tpl['prev-next']['pages']; ?> 
<?=($tpl['prev-next']['next-link'] ? '<li><a class="next icon-chevron-right" href="'.$tpl['prev-next']['next-link'].'"></a>' : ''); ?>
</ul>

</div>

<script>	
var images = $$('a.icon img');
images.invoke('hide');
var interval = setInterval(function(){
	
	var completed = 0;
	images.each(function(i){ 	// Подсчитываем количество загруженных изображений
		completed++;	
	});

	if (completed == images.length){
		clearInterval(interval); // Таймаут добавлен для устранения проблем с Chrome
		setTimeout(function(){
			images.invoke('show');
		}, 2000);	
	}
}, 100);
</script>