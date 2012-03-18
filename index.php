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
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<title>cloud-ide</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	
	<link rel="stylesheet" type="text/css" href="codemirror/codemirror.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/editor.css">
	<link rel="stylesheet" type="text/css" href="css/dftree.css">
	
	<script src="js/editor.js"></script>
	<script src="js/tabs.js"></script>
	
	<script src="codemirror/codemirror.js"></script>
	<script src="codemirror/mode/php/php.js"></script>
	<script src="codemirror/mode/xml/xml.js"></script>
	<script src="codemirror/mode/javascript/javascript.js"></script>
	<script src="codemirror/mode/css/css.js"></script>
	<script src="codemirror/mode/clike/clike.js"></script>
	<script src="js/dftree.js"></script>
	<script src="js/dftreeajax.js"></script>
</head>
<body>
	<!-- <header class="pane">
	</header> -->
	
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="brand" href="#">Cloud IDE</a>
				<ul class="nav">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							File
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="$('.ide').ide('save'); return false">Save</a></li>
							<li><a href="$('.ide').ide('saveall'); return false">Save all</a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							Edit
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="" onclick="document.execCommand('undo'); return false;">Undo Text Change</a></li>
							<li><a href="" onclick="document.execCommand('redo'); return false;">Redo Text Change</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="ide">
		<div class="pane pane-left pane-workspace">
			<script>
				var tree = new AjaxTree({
							name: 'tree', 
							icondir: 'img', 
							useIcons: true, 
							isLazy: false, 
							id: 'projects/', 
							caption: 'Project',
							url: '', 
							ajaxurl: 'tree.php?',
						});

				function open(file) {
					$('.ide').ide('open', file);
				}

				tree.draw();
			</script>
			<!-- <form action="" class="form-search">
				<input type="text" placeholder="Search Workspace">
			</form> -->
		</div>
		
		<div class="pane pane-center">
			
		</div>
		<!-- <fieldset style="float: left; width: 200px">
			<legend>Staging <a href="" onclick="return staging()">[refresh]</a></legend>
			<div id="staging"></div>
		</fieldset> -->
	</div>
	
	<script src="js/bootstrap.min.js"></script>
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
			
			$('*[rel=tooltip]').tooltip();
		});
	</script>
</body>
</html>