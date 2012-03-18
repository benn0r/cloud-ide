/* Ajax Tree
 * Based on dftree.
 * License: BSD. 
 *    See details at http://www.opensource.org/licenses/bsd-license.php
 * 
 * Copyright (c) 2008 
 *    Beno�t VAN BOGAERT as Macq Electronique sub-contractor
 * {bvanbogaert} at users.sourceforge.net
 * All rights reserved.
 */

var ajaxTreeDebug = true;

function AjaxTreeHandler() {
	// Create Ajax handler
	if (window.XMLHttpRequest)     // Object of the current windows
	{ 
	    this.xhr = new XMLHttpRequest();     // Firefox, Safari, ...
	} 
	else if (window.ActiveXObject)   // ActiveX version
	{
		this.xhr = new ActiveXObject("Microsoft.XMLHTTP");  // Internet Explorer 
	}
	else
	{
		alert('Unsupported browser')
	}

	this._tree = [];
	this._inProgress = false;
	this._currentTree = 0;
	this._currentNode = 0;
	this._TransferTime = 0;
	this._XMLParsingTime = 0;
	this._getCookieTime = 0;
	this._nodeCreationTime = 0;
	this.start = getTime();
	this.startRequest = getTime();
	this.end = getTime();
}

AjaxTreeHandler.prototype._parseResponse = function() {
	this._inProgress = false;
	
	this.end = getTime();
	this._TransferTime += this.end - this.startRequest; 

	// Current node and tree
	var tree = this._tree[this._currentTree].dFTree;
	var node = tree._aNodes[this._currentNode];
	node._loaded = true;
	
	// Create new childrens
	this.start = getTime();
	// Assign the XML file to a var
	var doc = this.xhr.responseXML;
	if (doc != null)
	{
		var elements = doc.getElementsByTagName('node');   // Read the first element
		this.end = getTime();
		this._XMLParsingTime += this.end - this.start; 

		// No gain to start the next request here before construction
		// Transmission time is too smaller than construction time 
		// this._nextRequest();

		this.start = getTime();
		var i;
		for (i=0; i<elements.length; i++)
		{
			var id = elements.item(i).attributes.getNamedItem('id').value;
			var caption = elements.item(i).attributes.getNamedItem('caption').value;
			var isFolder = elements.item(i).attributes.getNamedItem('isFolder').value;
			var url = elements.item(i).attributes.getNamedItem('url').value;
			var newNode = _newNode(tree, node.id, id, caption, url, isFolder != 0);
		} 
		this.end = getTime();
		this._nodeCreationTime += this.end - this.start; 
		
		// Get coockies
		this.start = getTime();
		tree._getCookie();
		this.end = getTime();
		this._getCookieTime += this.end - this.start; 
	
		// In lazy mode, the direct childrens of a visible and opened node must also be loaded
		if (node._io || !tree.isLazy)
		{
			for(i in node._children)
			{
				node._children[i]._toBeLoaded = node._children[i].isFolder;
			}
		}
	
		if (this._tree[this._currentTree].onUpdate != null) {
			eval(this._tree[this._currentTree].onUpdate);
		}
	}
	
	if (!this._inProgress)
	{
		this._nextRequest();
	}
};

AjaxTreeHandler.prototype._nextRequest = function() {
	if (this._inProgress) return;
	
	var found = false;
	var quit = false;
	var tree = null
	var node = null;
	if (this._tree.length > 0)
	{
		if (this._currentTree >= this._tree.length) 
		{
			this._currentTree = this._tree.length - 1;
			this._currentNode = 0;
		}
		var baseTree = this._currentTree;
		if (this._currentNode >= this._tree[baseTree].dFTree._aNodes.length) this._currentNode = this._tree[baseTree].dFTree._aNodes.length - 1;
		if (this._currentNode < 0) this._currentNode = 0;
		var baseNode = this._currentNode;
		while (!found && !quit)
		{
			tree = this._tree[this._currentTree].dFTree;
			// Process current tree
			if (tree._aNodes.length > 0)
			{
				this._currentNode++;
				if (this._currentNode >= tree._aNodes.length)
				{
					this._currentTree++;
					if (this._currentTree >= this._tree.length) this._currentTree = 0;
					this._currentNode = 0;
					tree = this._tree[this._currentTree].dFTree;
				}
				if (this._currentNode < tree._aNodes.length)
				{
					node =  tree._aNodes[this._currentNode];
					found = node._toBeLoaded && !node._loaded;
				}
			}

			quit = (this._currentTree == baseTree) && (this._currentNode == baseNode);
		}
	}
	
	if (found)
	{
		if (ajaxTreeDebug) window.status = "Request tree '"+tree.name+"' node='"+node.id;
		this.startRequest = getTime();
		this.xhr.abort();
		this.xhr.onreadystatechange = _callOnReadyStateChange;
		var url = this._tree[this._currentTree].ajaxurl+'parent='+node.id;
		
		var ua = window.navigator.userAgent.toLowerCase();
		if ((ua.indexOf('msie')) != -1)
		{
			if (url.indexOf('?') == -1)
			{
				url += '?';
			}
			else
			{
				url += '&';
			}
			url += 'random='+Math.random();
		}
		this._inProgress = true;
		this.xhr.open('GET', url, true);
		this.xhr.send(null);
	} 
	else
	{
		if (ajaxTreeDebug) window.status = "Tree is complete";
	}
};

AjaxTreeHandler.prototype._addTree = function(tree) {
	this._tree[this._tree.length] = tree;
	this._nextRequest();
};

AjaxTreeHandler.prototype._callOnFirstOpen = function(node) {
	var i;
	if (typeof(node) == "undefined") return;
	for (i in node._children)
	{
		node._children[i]._toBeLoaded = node._children[i].isFolder;
	}
	this._nextRequest();
}

var ajaxTreeHandler = new AjaxTreeHandler();

// AjaxTREE constructor 
//Usage: t = new AjaxTree({name:t, id:'root' caption:'tree root', url:'http://www.w3.org', ajaxurl:'http://www.myhost/myproject/getChildrens.php?'});
function AjaxTree(arrayProps) {
	// Initiate the AjaxTree properties
	this.addProperty(arrayProps, 'name', null, true);
 	this.addProperty(arrayProps, 'ajaxurl', null, true);
 	this.addProperty(arrayProps, 'id', 'root', false);
 	this.addProperty(arrayProps, 'caption', '', false);
 	this.addProperty(arrayProps, 'url', '', false);
	this.addProperty(arrayProps, 'onUpdate', null, false);
	
	// Other properties are for dftree
	arrayProps['name'] = this.name+".dFTree";
    this.dFTree = new dFTree(arrayProps);
    
	// Create root node
	this.rootNode = _newNode(this.dFTree, -1, this.id, this.caption, this.url, true);
	this.rootNode._toBeLoaded = true;
    
    // Load the root nodes and direct childs
    ajaxTreeHandler._addTree(this);
}

// Display the loaded tree
AjaxTree.prototype.draw = function() {
	this.dFTree.draw();
}

AjaxTree.prototype.addProperty = function(arrayProps, name, defaultValue, mandatory) {
	if (arrayProps[name] !== null)
 	{
		eval('this.'+name+' = arrayProps[\''+name+'\'];');
	    delete arrayProps[name];
	}
	else if (mandatory)
	{
		alert('AjaxTree: property \''+name+'\' must be defined');
	}
	else
	{
		eval('this.'+name+' = defaultValue;');
	}
}

function _newNode(tree, pid, id, caption, url, isFolder) {
	var newNode = new dNode({id: id,caption: caption, url: url, isFolder: isFolder});
	newNode._loaded = false;
	newNode._toBeLoaded = false;
	newNode._io = false;
	newNode.onFirstOpen = 'ajaxTreeHandler._callOnFirstOpen('+tree.name+'.getNodeById(\''+newNode.id+'\'));';
	tree.add(newNode, pid);
	return newNode;
}
	
function _callOnReadyStateChange() { 
	if (ajaxTreeDebug) window.status = "AJAX ReadyState="+ajaxTreeHandler.xhr.readyState;
	// instructions to process the response 
	if(ajaxTreeHandler.xhr.readyState  == 4)
	{
		if(ajaxTreeHandler.xhr.status  == 200) 
		{
			ajaxTreeHandler._parseResponse();
		}
		else
		{ 
			alert("Ajax: Error code " + ajaxTreeHandler.xhr.status);
			ajaxTreeHandler._inProgress = false;
			// ajaxTreeHandler._nextRequest();
		}
	}
}

function getTime() {
	if (Date.now)
		return Date.now();
	else
		var d = new Date();
		return d.getTime();
}
 
