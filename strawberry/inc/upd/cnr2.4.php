<?php
$urls = parse_ini_file(rufus_file, true);

if ($urls['config']['rufus'] and $urls['config']['rufus'] == 'yes'){
    $config['rufus'] = 'yes';
}

/*
if (!is_dir(templates_directory)){
	$handle = opendir(data_directory);
	while ($file = readdir($handle)){
	    if (substr($file, -3) == 'tpl'){
	        $contents = file_read(data_directory.'/'.$file);
	        $contents = str_replace('{link=plain/', '{link=home/', $contents);
	        file_write(data_directory.'/'.$file, $contents);
	    }
	}
}
*/

$contents = file_read(rufus_file);
$contents = str_replace('[plain]', '[home]', $contents);
file_write(rufus_file, $contents);
?>