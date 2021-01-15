<?php
$skin_prefix = ''; 

if ( !function_exists('options_submenu') ){
	function options_submenu(){
		global $member;
	    
		ob_start();
	    include modules_directory.'/options.mdu';
	    $options = ob_get_contents();
	    ob_get_clean();

	    $options = strip_tags($options, '<ul><li><a>');
	    $options = str_replace('&nbsp;', '', $options);
	    $options = explode("\r\n", $options);

	    foreach ($options as $option){
	        $result[] = !empty($option) ? $option : '';
	    }	
	    return $member ? @join(' ', $result) : false;
    }
}
  
ob_start(); 
?>

<!--form id="SearchForm" method="get" action="<?//=$PHP_SELF; ?>">
	<input type="hidden" name="mod" value="<? //echo $mod ? $mod : 'search'; ?>">
	<input type="hidden" name="baze" id="hiddenbox" value="news">
	<input type="search" name="search" id="search-box" value="<? //echo $search ? $search : 'Поиск по сайту'; ?>">
	<img title="Укажите где искать" src="skins/images/button-search-down.png" class="toggle-search" id="option-button">
	<ul class="search-more" id="search-more" style="display: none;">
		<li><a href="#" id="" title="<?//=t('Везде'); ?>">Везде</a></li>
		<li><a href="#" id="news" title="<?//=t('В новостях'); ?>">В новостях</a></li>
		<li><a href="#" id="blogs" title="<?//=t('В блогах'); ?>">В блогах</a></li>
		<li><a href="#" id="catalogs" title="<?//=t('В каталоге'); ?>">В каталоге</a></li>
	</ul>
</form-->

<ul>
	<li><a id="main" href="<?=$PHP_SELF; ?>?mod=main"><?=t('Статистика'); ?></a></li>
	<li><a id="addnews" href="<?=$PHP_SELF; ?>?mod=addnews"><?=t('Добавить'); ?></a></li>
	<li><a id="editnews" href="<?=$PHP_SELF; ?>?mod=editnews"><?=t('Редактировать'); ?></a></li>
	<li><a id="options" href="<?=$PHP_SELF; ?>?mod=options"><?=t('Настройки'); ?>  &nbsp; 
		<i style="font-size:12px;" class="icon-caret-down" onclick="$('options-submenu').toggle(); return false;"></i></a> 
		<?//=makePlusMinus('options-submenu'); ?>
	</li>
	<li><a id="help" href="<?=$PHP_SELF; ?>?mod=help"><?=t('Помощь'); ?></a></li>
	<?=($config['cache'] ? '<li><a href="'.$PHP_SELF.'?action=clearcache">'.t('Очистить кэш').'</a></li>' : ''); ?> 
	<li><a href="<?=$PHP_SELF; ?>?mod=logout"><?=t('Выход'); ?></a></li>
	<li><a href="<?=$config['http_home_url']; ?>" target="_blank"><?=t('На сайт'); ?></a></li>
	<!--li><a title="Modal header" href="#myModal" role="button" class="btn" data-toggle="modal">Demo modal</a></li-->
</ul>

<?php
	$skin_menu = ob_get_clean();
	ob_start();	
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?=$config['home_title']; ?></title>
		<meta http-equiv="content-type" content="text/html; charset=<?=$config['charset']; ?>">
		<meta http-equiv="Content-language" content="<?=$config['language']; ?>">

		<!--link href="<?///=($config['skin'] ? $config['http_script_dir'].'/strawberry/skins/'.$config['skin'] : 'skins/default'); ?>.css" rel="stylesheet" type="text/css" media="screen"-->
		<link rel="stylesheet" media="screen" href="skins/css/<?=($config['skin']?: 'default'); ?>.css.php"/>
        <link rel="stylesheet" media="screen" href="codemirror/lib/codemirror.css"/>
 
		<style>
			#navigation > ul > li > a#<?=(isset($mod) ? $mod : 'main'); ?>
				{background:#FFF}
		</style>

		<script type="text/javascript" src="/themes/js/prototype.1.7.2.min.js"></script>
		<script type="text/javascript" src="/themes/js/scriptaculous.js?load=effects,controls,dialog"></script>
		<script type="text/javascript" src="js/procolor.compressed.js"></script>
		
	</head>
	<body>
		<nav id="navigation">
			{menu}		
		</nav>
	
		<div id="tempContainer">
			<h1>{header-text}</h1>

<?php
	$skin_header = ob_get_clean();
    ob_start();
?>
		</div>
	
		<div id="copyrights">{copyrights}</div>
		<!-- Modal -->	
		<div id="myModal" class="modal fade" role="dialog" aria-hidden="true">
			<div class="modal-header">
				<a title="Закрыть" class="close" data-dismiss="modal" aria-hidden="true">×</a>
				<h3 id="myModalHeader">Modal header</h3>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer">
				<span id="resDataArea"></span>
				<button data-dismiss="modal" aria-hidden="true">
					<?=t('Закрыть')?>
				</button>
			</div>
		</div>

		<!--script type="text/javascript" src="<?//=$config['http_script_dir']; ?>/strawberry/skins/tooltip.js"></script-->
		<script type="text/javascript" src="/themes/js/functions.js"></script>
		<script type="text/javascript" src="js/cute.js"></script>
		<script type="text/javascript" src="js/tools.js"></script>
		<script type="text/javascript">
		<!--

			$(document).observe("dom:loaded", function() { 
				new Fabtabs('tabs'); //tooltip.init();  
			});

			function doAjaxItem(pars, box, url){
				
				if( typeof box === "undefined" ){
					box = this;
				}		
				if( typeof url === "undefined" ){
					url = '/ajax/ajax.admin.php';
				}
				
				new Ajax.Request(url, {
					parameters: pars,
					onCreate:   function(data){
					},
					onComplete: function(data){
						if (data.status == 200){
							box.toggleClassName('green icon-ok').toggleClassName('red icon-remove');
						}	
					},
					onFailure:  function(data){
						alert (data.responseText);
					}
				});
			}
			/*
			if ( window.sessionStorage ) {
				if (!sessionStorage['window']) {
					sessionStorage['window'] = true; //alert(sessionStorage['window']);
					new BootStrap.Modal($("myModal"));
				}
			}*/
		-->
		</script>  
	</body>
</html>

<?php
$skin_footer = ob_get_clean();
?>
