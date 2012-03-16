<?php

function open_dir($dirname) {
	$dir = opendir($dirname);
	while ($file = readdir($dir)) {
		if ($file == '.' || $file == '..') {
			continue;
		}
		
		if (is_dir($dirname . '/' . $file)) {
			open_dir($dirname . '/' . $file);
		} else if (is_file($dirname . '/' . $file)) {
			echo '<a href="" onclick="return openfile(\'' . $dirname . '/' . $file . '\')">' . $dirname . '/' . $file . '</a><br />';
		}
	}
	closedir($dir);
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>cloud-ide</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="codemirror/codemirror.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/editor.css">
	
	<script src="js/editor.js"></script>
	
	<script src="codemirror/codemirror.js"></script>
	<script src="codemirror/mode/php/php.js"></script>
	<script src="codemirror/mode/xml/xml.js"></script>
	<script src="codemirror/mode/javascript/javascript.js"></script>
	<script src="codemirror/mode/css/css.js"></script>
	<script src="codemirror/mode/clike/clike.js"></script>
</head>
<body>
	<!-- <header class="pane">
	</header> -->
	
	<div class="ide">
		<div class="pane pane-left pane-workspace">
			<!-- <form action="" class="form-search">
				<input type="text" placeholder="Search Workspace">
			</form> -->
		</div>
		
		<div class="pane pane-center">
			<!-- <ul class="nav nav-tabs nav-files">
				<li class="dropdown active"><a href="#">bar.php <b class="close">&times;</b></a></li>
				<li class="dropdown"><a href="#">blub.php</a></li>
				<li class="dropdown"><a href="#">foo.php</a></li>
			</ul>
		
			<textarea id="code" name="code"></textarea>
			
			<button onclick="save()">Save</button>
			<button onclick="run()">Run</button> -->
		</div>
		<!-- <fieldset style="float: left; width: 200px">
			<legend>Staging <a href="" onclick="return staging()">[refresh]</a></legend>
			<div id="staging"></div>
		</fieldset> -->
	</div>
	
	<script>
		/*var editor;
		var filename;

		function opentree(parent, path) {
			var parent = $(parent);

			if (parent.data('open')) {
				parent.data('open', false);
				parent.data('loaded', true);
				parent.find('div').hide();
				
				return false;
			}

			if (parent.data('loaded')) {
				parent.find('div').show();
				parent.data('open', true);
				
				return false;
			}

			parent.append('<div class="waiting"><img src="img/tree/loader.gif"></div>');
			$.get('tree.php?path=' + path, function(data) {
				parent.data('open', true);
				parent.append(data);
				parent.find('.waiting').remove();

				parent.find('div').find('a').mousedown(function(event) {
					if (event.which == 3) {
						alert('clicked');
					}
				});
			});

			return false;
		}
	
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
				staging();
			});
			
			return false;
		}

		function run() {
			window.open(
				filename,
				'_blank'
			);
			
			return false;
		}*/
		
		$(function() {
			 /*editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			   mode: "application/x-httpd-php",
			   lineNumbers: true,
			   lineWrapping: true,
			   onCursorActivity: function() {
			     editor.setLineClass(hlLine, null);
			     hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
			   }
			 });
			 var hlLine = editor.setLineClass(0, "activeline");

			 $.get('tree.php?path=projects/wwm', function(data) {
				 $('.pane-workspace').append(data);
			 });
			
			staging();*/

			$('.ide').ide();
		});
	</script>
</body>
</html>