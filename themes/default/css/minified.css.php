<?php

//echo DIRECTORY_SEPARATOR;

/* css files for compression */
header('Content-type: text/css');

ob_start("compress");
include('navbar.css');
include('style.css');
include('forms.css');
include('carousel.css');
include('protoshow.css');
include('modalbox.css');
include('font-awesome.css');
ob_end_flush();

function compress($text) {
	
	$text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $text); /* remove comments */
	$text = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $text); /* remove tabs, spaces, newlines, etc. */
	return $text;
}