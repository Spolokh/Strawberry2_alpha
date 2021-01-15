<?php

header('Content-type: text/css');

ob_start("compress"); /* css files for compression */
include('default.css'); 
//include('forms.css');
include('dialog.css');
include('font-awesome.css');
include('redactor.css');
ob_end_flush();

function compress($buffer) {	
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);	
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $buffer);
	return $buffer;
}
