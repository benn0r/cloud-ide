<?php

function modified($content) {
	$lines = explode("\n", $content);
	$files = array();

	foreach ($lines as $line) {
		if (strstr($line, ' M ')) {
			$files[] = trim(str_replace(' M ', '', $line));
		}
	}

	return $files;
}

function untracked($content) {
	$lines = explode("\n", $content);
	$files = array();
	
	foreach ($lines as $line) {
		if (strstr($line, '?? ')) {
			$files[] = trim(str_replace('?? ', '', $line));
		}
	}
	
	return $files;
}

$content = shell_exec('"C:\\Program Files (x86)\\Git\\bin\\git" status --porcelain --untracked-files=all');

echo '<p><strong>Modified:</strong><br />';
foreach (modified($content) as $file) {
	echo $file . '<br />';
}
echo '</p><p><strong>Untracked:</strong><br />';
foreach (untracked($content) as $file) {
	echo $file . '<br />';
}
echo '</p><p><a href="staging.php">Reload</a></p>';