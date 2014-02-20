<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

function pr($value=''){
	$btr=debug_backtrace();
	$line=$btr[0]['line'];
	$file=basename($btr[0]['file']);
	print"<pre>$file:$line</pre>\n";
	if(is_array($value)){
		print"<pre>";
		print_r($value);
		print"</pre>\n";
	}elseif(is_object($value)){
		print"<pre>";
		print_r($value);
		print"</pre>\n";
	}else{
		print("<p>&gt;${value}&lt;</p>");
	}
}

