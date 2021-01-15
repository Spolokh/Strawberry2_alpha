<?php

header('Content-type: text/css');
ob_start("compress"); /* css files for compression */

include('style.css');
include('modalbox.css');
include('tools.css3.css'); 
include('font-awesome.css');


ob_end_flush();

function compress($buffer) {
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); /* remove comments */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer); /* remove tabs, spaces, newlines, etc. */
	return $buffer;
}