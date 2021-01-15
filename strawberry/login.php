<?php

include_once 'head.php';

switch($REQUEST_URI)
{
	case "/do/post" :

	break;
		
    default :
    
        ob_start();
        include forms_directory.'/LoginForm.tpl';
        $result = ob_get_clean();	
	break;
}

echo $result;
