<?php

add_filter('options', 'themes_AddToOptions');

function themes_AddToOptions($options) {
	$options[] = array(
		'name'     => 'Themes',
		'url'      => 'plugin=themes',
		'category' => 'templates'
	);  return $options;
}

add_action('plugins', 'themes_CheckAdminOptions');

function themes_CheckAdminOptions(){

	if ($_GET['plugin'] == 'themes'){
		themes_AdminOptions();
	}
}

function themes_AdminOptions(){
global $PHP_SELF;

    echoheader('options', 'Themes');

    $themes = array();
    $themes = run_filters('themes', $themes);
    echo_r($themes);
    
	
	echofooter();
}
?>