<?php

if (isset($_GET['parent'])) {
	header("Content-type: text/xml");
	//header("Content-Disposition: attachment; filename=\"content.xml\"");
	header("Content-Disposition: filename=\"content.xml\"");
	echo utf8_encode("<?xml version='1.0' encoding='UTF-8' ?>\r\n<nodes>\r\n");

	$path = $_GET['parent'];
	if (is_dir($path)) {
		if ($dh = opendir($path)) {
			while (($file = readdir($dh)) !== false) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				// echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
				$is_dir = is_dir($path.$file) && $file != "." && $file != "..";
				$end_char = $is_dir?"/":"";
				if ($is_dir) {
					echo utf8_encode("<node id='".$path.$file.$end_char."' caption='".$file."' url='' isFolder='1' />\r\n");
				} else {
					echo utf8_encode("<node id='".$path.$file."' url='javascript:open(\"" . $path.$file . "\")' caption='".$file."' isFolder='0' />\r\n");
				}
			}
			closedir($dh);
		}
	}

	echo utf8_encode("</nodes>\r\n");
	exit;
}

function get_icon($filename) {
	if (is_dir($filename)) {
		if (is_project($filename)) {
			return '<img src="img/tree/project.png">';
		}
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

function is_project($dir) {
	if (file_exists($filename = $dir . '/.project')) {
		$json = file_get_contents($filename);
		if (($config = json_decode($json)) != null) {
			// seems to be a project
			return true;
		}
	}
	
	// no project
	return false;
}

function print_files($path) {
	$files = array();
	$handle = opendir($path);
	while (($filename = readdir($handle)) != null) {
		if ($filename == '.' || $filename == '..' || $filename == '.project') {
			continue;
		}
		
		$file = new stdClass();
		$file->children = array();
		$file->path = $path;
		$file->name = $filename;
		$file->icon = get_icon($path . '/' . $filename);
		
		if (is_project($path . '/' . $filename)) {
			$file->type = 'project';
		} elseif (is_dir($path . '/' . $filename)) {
			$file->type = 'dir';
		} else {
			$file->type = 'file';
		}
		
		if (is_dir($path . '/' . $filename)) {
			$file->children = print_files($path . '/' . $filename);
		}
		
		$files[] = $file;
	}
	closedir($handle);
	
	return $files;
}

echo json_encode(print_files($_GET['path']));

/*function print_files($path) {
	$html = '';
	$files = '';
	$json = array();
	$json2 = array();
	
	$handle = opendir($path);
	while (($filename = readdir($handle)) != null) {
		if ($filename == '.' || $filename == '..') {
			continue; 
		}
		
		$obj = new stdClass();
		
// 		$left = (count(explode('/', $path)) - 1) * 21;
		
		$str = '<div class="" style="margin-left: ' . (count(explode('/', $path)) > 1 ? '21' : '0') . 'px">';
		$obj->name = $filename;
		$obj->path = $path;
		$obj->icon = get_icon($path . '/' . $filename);
		
		if (is_dir($path . '/' . $filename)) {
			$obj->type = 'dir';
			if ($filename == 'wwm' || $filename == 'wwm2') {
				$obj->type = 'project';
			}
			
			$str .= '<a onclick="return opentree($(this).parent(), \'' . $path . '/' . $filename . '\')" href="">' . 
				get_icon($path . '/' . $filename) . $filename . '</a>';
		} else {
			$obj->type = 'file';
			$str .= get_icon($path . '/' . $filename) . $filename;
		}
		
		$str .= '</div>';
		
		if (is_dir($path . '/' . $filename)) {
			$html .= $str;
			$json[] = $obj;
		} else {
			$files .= $str;
			$json2[] = $obj;
		}
	}
	closedir($handle);
	
	return json_encode(array_merge($json, $json2));
	return $html . $files;
}*/