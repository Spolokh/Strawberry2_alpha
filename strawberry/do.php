<?php

include_once 'head.php';

use classes\ModalForm;

switch($REQUEST_URI)
{
	case '/do/form' :
		$result = (new ModalForm($config))->run();
	break;
		
	case '/do/login': 

		ob_start();
		include forms_directory.'/LoginForm.tpl';
		$result = ob_get_clean();
	break;
		
	default : 
		header('HTTP/1.1 404 Not Found');
        	exit;
	break;
}

exit($result);
