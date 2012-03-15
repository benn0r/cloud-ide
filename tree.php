<?php

if (!isset($_GET['path'])) {
	die('Error.');
}

function get_icon($filename) {
	if (is_dir($filename)) {
		return '<img src="img/tree/folder.png">';
	}
	
	$parts = explode('.', $filename);
	switch (array_pop($parts)) {
		case 'php': 
			return '<img src="img/tree/php.png">';
		case 'html':
		case 'phtml':
			return '<img src="img/tree/html.png">';
		case 'sql':
			return '<img src="img/tree/sql.png">';
		case 'ini':
			return '<img src="img/tree/ini.png">';
		case 'css':
			return '<img src="img/tree/css.png">';
		case 'png':
		case 'jpg':
		case 'jpeg':
		case 'gif':
		case 'psd':
			return '<img src="img/tree/image.png">';
		case 'txt':
		case 'md': // github readme
			return '<img src="img/tree/text.png">';
		default:
			return '<img src="img/tree/undefined.png">';
	}
}

function print_files($path) {
	$html = '';
	$files = '';
	
	$handle = opendir($path);
	while (($filename = readdir($handle)) != null) {
		if ($filename == '.' || $filename == '..') {
			continue; 
		}
		
// 		$left = (count(explode('/', $path)) - 1) * 21;
		
		$str = '<div class="" style="margin-left: ' . (count(explode('/', $path)) > 1 ? '21' : '0') . 'px">';
		
		if (is_dir($path . '/' . $filename)) {
			$str .= '<a onclick="return opentree($(this).parent(), \'' . $path . '/' . $filename . '\')" href="">' . 
				get_icon($path . '/' . $filename) . $filename . '</a>';
		} else {
			$str .= get_icon($path . '/' . $filename) . $filename;
		}
		
		$str .= '</div>';
		
		if (is_dir($path . '/' . $filename)) {
			$html .= $str;
		} else {
			$files .= $str;
		}
	}
	closedir($handle);
	
	return $html . $files;
}

echo print_files($_GET['path']);