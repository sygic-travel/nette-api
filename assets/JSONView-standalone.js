/*
 * JSONView Standalone
 *
 * Forked from https://github.com/dovy/jsonview-standalone
 * which is a standalone port of http://jsonview.com.
 *
 * Ported by: Jamie Wilkinson (http://jamiedubs.com)
 * Adapted by: Jan Jakes (http://jan-jakes.com)
 *
 * License: MIT (http://opensource.org/licenses/MIT)
 */

function collapse(evt) {
	var collapser = evt.target;
	var target = collapser.parentNode.getElementsByClassName('collapsible');
	if (!target.length) {
		return;
	}
	target = target[0];
	if (target.style.display == 'none') {
		var ellipsis = target.parentNode.getElementsByClassName('ellipsis')[0];
		target.parentNode.removeChild(ellipsis);
		target.style.display = '';
	} else {
		target.style.display = 'none';
		var ellipsis = document.createElement('span');
		ellipsis.className = 'ellipsis';
		ellipsis.innerHTML = ' &hellip; ';
		target.parentNode.insertBefore(ellipsis, target);
	}
	collapser.innerHTML = (collapser.innerHTML == '-') ? '+' : '-';
}

function addCollapser(item) {
	// This mainly filters out the root object (which shouldn't be collapsible)
	if (item.nodeName != 'LI') {
		return;
	}
	var collapser = document.createElement('div');
	collapser.className = 'collapser';
	collapser.innerHTML = '-';
	collapser.addEventListener('click', collapse, false);
	item.insertBefore(collapser, item.firstChild);
}

function jsonView(id) {
	if (id.indexOf("#") != -1) {
		this.idType = "id";
		this.id = id.replace('#', '');
	} else if (id.indexOf(".") != -1) {
		this.idType = "class";
		this.id = id.replace('.', '');
	} else {
		if (this.debug)
			console.log("Can't find that element");
		return;
	}
	this.data = document.getElementById(this.id).innerText;
	//this.data = document.getElementById(this.id).innerHTML; // this was causing errors for { "key": "\"value\"" }

	// JSONFormatter json->HTML prototype straight from Firefox JSONView
	// For reference: http://code.google.com/p/jsonview

	function JSONFormatter() {
		// No magic required.
	}
	JSONFormatter.prototype = {
		htmlEncode: function(t) {
			return t != null ? t.toString().replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
		},
		decorateWithSpan: function(value, className) {
			return '<span class="' + className + '">' + this.htmlEncode(value) + '</span>';
		},
		// Convert a basic JSON datatype (number, string, boolean, null, object, array) into an HTML fragment.
		valueToHTML: function(value) {
			var valueType = typeof value;
			var output = "";
			if (value == null) {
				output += this.decorateWithSpan('null', 'null');
			} else if (value && value.constructor == Array) {
				output += this.arrayToHTML(value);
			} else if (valueType == 'object') {
				output += this.objectToHTML(value);
			} else if (valueType == 'number') {
				output += this.decorateWithSpan(value, 'num');
			} else if (valueType == 'string') {
				if (/^(http|https):\/\/[^\s]+$/.test(value)) {
					output += '<a href="' + value + '">' + this.htmlEncode(value) + '</a>';
				} else {
					output += this.decorateWithSpan('"' + value + '"', 'string');
				}
			} else if (valueType == 'boolean') {
				output += this.decorateWithSpan(value, 'bool');
			}
			return output;
		},
		// Convert an array into an HTML fragment
		arrayToHTML: function(json) {
			var output = '[<ul class="array collapsible">';
			var hasContents = false;
			for (var prop in json) {
				hasContents = true;
				output += '<li>';
				output += this.valueToHTML(json[prop]);
				output += '</li>';
			}
			output += '</ul>]';
			if (!hasContents) {
				output = "[ ]";
			}
			return output;
		},
		// Convert a JSON object to an HTML fragment
		objectToHTML: function(json) {
			var output = '{<ul class="obj collapsible">';
			var hasContents = false;
			for (var prop in json) {
				hasContents = true;
				output += '<li>';
				output += '<span class="prop">' + this.htmlEncode(prop) + '</span>: ';
				output += this.valueToHTML(json[prop]);
				output += '</li>';
			}
			output += '</ul>}';
			if (!hasContents) {
				output = "{ }";
			}
			return output;
		},
		// Convert a whole JSON object into a formatted HTML document.
		jsonToHTML: function(json, uri) {
			var output = '';
			output += '<div id="json">';
			output += this.valueToHTML(json);
			output += '</div>';
			return this.toHTML(output, uri);
		},
		// Produce an error document for when parsing fails.
		errorPage: function(error, data, uri) {
			// var output = '<div id="error">' + this.stringbundle.GetStringFromName('errorParsing') + '</div>';
			// output += '<h1>' + this.stringbundle.GetStringFromName('docContents') + ':</h1>';
			var output = '<div id="error">Error parsing JSON: ' + error.message + '</div>';
			output += '<h1>' + error.stack + ':</h1>';
			output += '<div id="json">' + this.htmlEncode(data) + '</div>';
			return this.toHTML(output, uri + ' - Error');
		},
		// Wrap the HTML fragment in a full document. Used by jsonToHTML and errorPage.
		toHTML: function(content, title) {
			return content;
		}
	};
	// Sanitize & output -- all magic from JSONView Firefox
	this.jsonFormatter = new JSONFormatter();

	var outputDoc = '';
	// Covert, and catch exceptions on failure
	try {
		// var jsonObj = this.nativeJSON.decode(cleanData);
		var jsonObj = JSON.parse(this.data);
		if (jsonObj) {
			outputDoc = this.jsonFormatter.jsonToHTML(jsonObj);
		} else {
			throw "There was no object!";
		}
	} catch (e) {
		if (this.debug)
			console.log(e);
		outputDoc = this.jsonFormatter.errorPage(e, this.data);
	}
	var links = '<style type="text/css">#json{font-family:monospace;}.prop{font-weight:bold;}.null{color:gray;}.string{color:green;}.collapser{position:absolute;left:-1em;cursor:pointer;}#json li{position:relative;line-height:15px;}#json li:after{content:\',\';}#json li:last-child:after{content:\'\';}#error{-moz-border-radius:8px;border:1px solid #970000;background-color:#F7E8E8;margin:.5em;padding:.5em;}.errormessage{font-family:monospace;}#json{font-family:monospace;font-size:1.0em;}ul{list-style:none;margin:0 0 0 2em;padding:0;}h1{font-size:1.2em;}.bool,.num{color:blue;}</style>';
	if (this.idType == "class") {
		document.getElementsByClassName(this.id).innerHTML = links + outputDoc;
	} else if (this.idType == "id") {
		document.getElementById(this.id).innerHTML = links + outputDoc;
	}
	var items = document.getElementsByClassName('collapsible');
	for (var i = 0; i < items.length; i++) {
		addCollapser(items[i].parentNode);
	}
}
