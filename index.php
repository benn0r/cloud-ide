<!DOCTYPE html>
<html>
<head>
	<title>cloud-ide</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="codemirror/codemirror.css">
	
	<script src="codemirror/codemirror.js"></script>
	<script src="codemirror/mode/php/php.js"></script>
	<script src="codemirror/mode/xml/xml.js"></script>
	<script src="codemirror/mode/javascript/javascript.js"></script>
	<script src="codemirror/mode/css/css.js"></script>
	<script src="codemirror/mode/clike/clike.js"></script>
</head>
<body>
	<fieldset style="float: left; width: 200px">
		<legend>Workspace</legend>
		<?php
		$dir = opendir('test');
		while ($file = readdir($dir)) {
			if (is_file('test/' . $file)) {
				echo '<a href="" onclick="return openfile(\'' . $file . '\')">' . $file . '</a><br />';
			}
		}
		closedir($dir);
		?>
	</fieldset>
	<fieldset style="float: left; width: 500px">
		<legend>Editor</legend>
		<textarea id="code" name="code"></textarea>
		<button onclick="save()">Save</button>
	</fieldset>
	<fieldset style="float: left; width: 200px">
		<legend>Staging <a href="" onclick="return staging()">[refresh]</a></legend>
		<div id="staging"></div>
	</fieldset>
	
	<script>
		var editor;
		var filename;
	
		function staging() {
			$.get('staging.php', function(data) {
				$('#staging').html(data);
			});

			return false;
		}

		function openfile(file) {
			filename = file;
			
			$.get('file.php?file=' + file, function(data) {
				editor.setValue(data);
			});

			return false;
		}

		function save() {
			$.post('save.php?file=' + filename, 'data=' + editor.getValue(), function(data) {
				
			});
			
			return false;
		}
		
		$(function() {
			 editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			   mode: "application/x-httpd-php",
			   lineNumbers: true,
			   lineWrapping: true,
			   onCursorActivity: function() {
			     editor.setLineClass(hlLine, null);
			     hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
			   }
			 });
			 var hlLine = editor.setLineClass(0, "activeline");
			
			staging();
		});
	</script>
</body>
</html>