(function($) {
	// reference to the ide object
	var ide = null;
	
	var m = {
		updateTabs: function() {
			var settings = ide.data('settings');
			var nav = settings.tabs.find('.nav-tabs').first();
			var width = 0;
			
			nav.find('li').each(function() {
				width += $(this).width() + 2;
			});
			
			if (width >= nav.width()) {
				// too much tabs
			}
		},
			
		/**
		 * Opens a file as a tab
		 */
		openFile: function(file) {
			var settings = ide.data('settings'); // load settings
			var content = settings.tabs;
			var nav = settings.tabs.find('.nav-tabs').first(); // load navigation
			var editors = settings.tabs.find('.pane-editor'); // load navigation
			
			var exists = false;
			nav.find('li').each(function() {
				if ($(this).data('file') == file) {
					exists = true;
					m.displayFile($(this));
					return true;
				}
			});
			
			if (exists) {
				return true;
			}
			
			$.get(settings.file.replace('%s', file), function(source) {
				
				var id = 'text_' + file;
				var parts = file.split('/');
				var link = $('<li class="dropdown"><a href="#">' + parts[parts.length - 1] + 
						' <b class="close">&times;</b></a></li>');
								
				// add elem to link
				link.find('a').click(function() {
					m.displayFile($(this).parent());
					return false;
				});
				
				var container = $('<div class="editor" id="editor_' + id + '"><textarea id="' + id + '" style="display: none">' + source + '</textarea></div>');
				editors.append(container);

				// init editor
				var editor = CodeMirror.fromTextArea(document.getElementById(id), {
					mode: 'application/x-httpd-php',
					lineNumbers: true,
					lineWrapping: true,
					onCursorActivity: function() {
						editor.setLineClass(hlLine, null);
						hlLine = editor.setLineClass(editor.getCursor().line, 'activeline');
					},
					onChange: function(editor, data) {
						if (!editor.link.data('edited')) {
							editor.link.data('edited', true);
							editor.link.find('a').append('*');
						}
					}
				});
				editor.link = link;
				
				var hlLine = editor.setLineClass(0, 'activeline');
				
				link.data('container', container);
				link.data('editor', editor);
				link.data('file', file);
				
				//nav.prepend(link);
				nav.append(link);
				m.updateTabs();
				
				m.displayFile(link);
			});
		},
		
		displayFile: function(link) {
			var settings = ide.data('settings'); // load settings
			var nav = settings.tabs.find('.nav-tabs').first(); // load navigation
			var editors = settings.tabs.find('.pane-editor'); // load contents
			
			nav.find('li').removeClass('active'); // deselect navigation
			editors.find('.editor').hide(); // hide all editors
			
			link.addClass('active'); // select current navigation point
			link.data('container').show(); // show selected editor
		},
		
		printWorkspace: function(elems) {
			// load settings
			var settings = ide.data('settings');
			
			for (var i = 0; i < elems.length; i++) {
				var elem = elems[i];
				
				switch (elem.type) {
					case 'project':
						var html = $('<div class="pane-project well">' + elem.icon + ' <a href="">' + elem.name + '</a></div>');
						break;
					case 'dir':
						var html = $('<div class=""><img src="img/tree/dotted.png">' + elem.icon + ' <a href="">' + elem.name + '</a></div>');
						break;
					case 'file':
						var html = $('<div class=""><img src="img/tree/dotted.png">' + elem.icon + ' <a href="">' + elem.name + '</a></div>');
						break;
				}
				
				html.css('padding-left', (elem.path.split('/').length - 1) * 21);
				settings.explorer.append(html);
				
				if (elem.children.length > 0) {
					m.printWorkspace(elem.children);
				}
			}
		},
		
		/**
		 * Loads a folder in the workspace
		 */
		loadWorkspace: function(path, parent) {
			return false;
			// load settings
			var settings = ide.data('settings');
			
			if (!path) {
				path = ide.data('settings').workspace;
			}
			
			$.getJSON(settings.tree.replace('%s', path), function(json) {
				
				m.printWorkspace(json);
				
				//var ul = $('<ul class="nav"></ul>');
				
				return;
				
				for (var i = 0; i < json.length; i++) {
					var elem = json[i];
					
					if (elem.type == 'project') {
						var div = $('<div class="pane-project well"><h6><a href="">' + elem.icon + elem.name + '</a></h6></div>');
						div.find('a').first().data('elem', elem);
					} else {
						var div = $('<li><a href="">' + elem.icon + elem.name + '</a></li>');
						div.find('a').first().data('elem', elem);
					}
					
					if (elem.type == 'dir') {
						// its a folder
						div.find('a').first().click(function() {
							// load folder
							m.loadWorkspace(path + '/' + $(this).data('elem').name, $(this).parent());
							return false;
						});
					} else if (elem.type == 'project') {
						// its a project
						div.find('a').first().click(function() {
							// load folder
							m.loadWorkspace(path + '/' + $(this).data('elem').name, $(this).parent().parent().parent());
							return false;
						});
					} else if (elem.type == 'file') {
						// its a file
						div.find('a').first().click(function() {
							// open file in editor
							m.openFile($(this).data('elem'));
							return false;
						});
					}
					
					if (parent) {
						ul.append(div);
					} else {
						settings.explorer.append(div);
					}
				}
				
				if (parent) {
					ul.css('margin-left', '21px');
					parent.append(ul);
				}
			});
		},
		
		/**
		 * Inits the IDE and stuff
		 */
		init: function(options) {
			var settings = $.extend({
				'explorer': $('.pane-workspace'),
				'tabs': $('.pane-center'),
				
				'workspace': 'projects',
				'tree': 'tree.php?path=%s',
				'file': 'file.php?file=%s'
		    }, options);
			
			ide.data('settings', settings);
			m.loadWorkspace(settings.workspace);
			
			settings.tabs.append('<ul class="nav nav-tabs nav-files">' +
					'<li class="moretabs active"><a href="#"><b class="caret"></b></a></li></ul>' +
					'<div class="pane-editor"></div>');
		}
	};
	
	/**
	 * plugin starts here
	 */
	$.fn.ide = function(options, param1) {
		if (options == 'open') {
			m.openFile(param1);
			return;
		}
		
		if (options == 'save') {
			m.saveFile();
			return;
		}
		
		ide = this;
		m.init(options);
	};
})(jQuery);