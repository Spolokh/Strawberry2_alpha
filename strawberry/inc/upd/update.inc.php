<?php
/**
 * @package Private
 * @access private
 */

$versions = array(
		  // CuteNews.RU
		  'cnr2.2',
		  'cnr2.3',
		  'cnr2.4',
		  'cnr2.5',

		  // Strawberry
		  'str1.0',
		  'str1.0.3',
		  'str1.1',
		  'str1.1.1'
		  );

foreach ($versions as $version){
	include updates_directory.'/'.$version.'.php';
}

$config['version_name'] = $version_name;
$config['version_id']   = $version_id;

$save_config  = "<?\r\n";
$save_config .= '$config = ';
$save_config .= var_export($config, true);
$save_config .= ";\r\n";
$save_config .= '$allowed_extensions = array(\'gif\', \'jpg\', \'png\', \'bmp\', \'jpe\', \'jpeg\');';
$save_config .= "\r\n?>";

$cache->delete();

file_write(config_file, $save_config);
header('Location: '.$PHP_SELF);
exit;
?>