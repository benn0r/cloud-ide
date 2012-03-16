(function($) {
	// reference to the ide object
	var ide = null;
	
	var m = {
		/**
		 * Opens a file as a tab
		 */
		openFile: function(elem) {
			var settings = ide.data('settings'); // load settings
			var nav = settings.tabs.find('.nav-tabs').first(); // load navigation
			var editor = settings.tabs.find('textarea').first(); // load navigation
			
			$.get(settings.file.replace('%s', elem.path + '/' + elem.name), function(source) {
				elem.source = source;
				
				editor.val(elem.source);
				
				var link = $('<li class="dropdown"><a href="#">' + elem.name + 
						' <b class="close">&times;</b></a></li>');
				link.data('elem', elem);
				link.find('a').click(function() {
					m.displayFile(elem);
					return false;
				});
				
				nav.append(link);
			});
		},
		
		displayFile: function(elem) {
			
		},
		
		/**
		 * Loads a folder in the workspace
		 */
		loadWorkspace: function(path, parent) {
			// load settings
			var settings = ide.data('settings');
			
			if (!path) {
				path = ide.data('settings').workspace;
			}
			
			$.getJSON(settings.tree.replace('%s', path), function(json) {
				console.log(json);
				
				for (var i = 0; i < json.length; i++) {
					var elem = json[i];
					var div = $('<div><a href="">' + elem.icon + elem.name + '</a></div>');
					div.find('a').first().data('elem', elem);
					
					if (elem.type == 'dir') {
						// its a folder
						div.find('a').first().click(function() {
							// load folder
							m.loadWorkspace(path + '/' + $(this).data('elem').name, $(this).parent());
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
						div.css('margin-left', '21px');
						parent.append(div);
					} else {
						settings.explorer.append(div);
					}
				}
			});
		},
		
		/**
		 * Inits the IDE and stuff
		 */
		init: function(options) {
			var settings = $.extend({
				'explorer': $('.pane-left'),
				'tabs': $('.pane-center'),
				
				'workspace': 'projects/wwm',
				'tree': 'tree.php?path=%s',
				'file': 'file.php?file=%s'
		    }, options);
			
			ide.data('settings', settings);
			m.loadWorkspace(settings.workspace);
			
			settings.tabs.append('<ul class="nav nav-tabs nav-files"></ul><textarea id="code" name="code"></textarea>');
		}
	};
	
	/**
	 * plugin starts here
	 */
	$.fn.ide = function(options) {
		ide = this;
		m.init(options);
	};
})(jQuery);