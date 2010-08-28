/*!
 * jQuery JavaScript Library v1.4.2
 * http://jquery.com/
 *
 * Copyright 2010, John Resig
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 * Copyright 2010, The Dojo Foundation
 * Released under the MIT, BSD, and GPL Licenses.
 *
 * Date: Sat Feb 13 22:33:48 2010 -0500
 */
(function( window, undefined ) {

// Define a local copy of jQuery
var jQuery = function( selector, context ) {
		// The jQuery object is actually just the init constructor 'enhanced'
		return new jQuery.fn.init( selector, context );
	},

	// Map over jQuery in case of overwrite
	_jQuery = window.jQuery,

	// Map over the $ in case of overwrite
	_$ = window.$,

	// Use the correct document accordingly with window argument (sandbox)
	document = window.document,

	// A central reference to the root jQuery(document)
	rootjQuery,

	// A simple way to check for HTML strings or ID strings
	// (both of which we optimize for)
	quickExpr = /^[^<]*(<[\w\W]+>)[^>]*$|^#([\w-]+)$/,

	// Is it a simple selector
	isSimple = /^.[^:#\[\.,]*$/,

	// Check if a string has a non-whitespace character in it
	rnotwhite = /\S/,

	// Used for trimming whitespace
	rtrim = /^(\s|\u00A0)+|(\s|\u00A0)+$/g,

	// Match a standalone tag
	rsingleTag = /^<(\w+)\s*\/?>(?:<\/\1>)?$/,

	// Keep a UserAgent string for use with jQuery.browser
	userAgent = navigator.userAgent,

	// For matching the engine and version of the browser
	browserMatch,
	
	// Has the ready events already been bound?
	readyBound = false,
	
	// The functions to execute on DOM ready
	readyList = [],

	// The ready event handler
	DOMContentLoaded,

	// Save a reference to some core methods
	toString = Object.prototype.toString,
	hasOwnProperty = Object.prototype.hasOwnProperty,
	push = Array.prototype.push,
	slice = Array.prototype.slice,
	indexOf = Array.prototype.indexOf;

jQuery.fn = jQuery.prototype = {
	init: function( selector, context ) {
		var match, elem, ret, doc;

		// Handle $(""), $(null), or $(undefined)
		if ( !selector ) {
			return this;
		}

		// Handle $(DOMElement)
		if ( selector.nodeType ) {
			this.context = this[0] = selector;
			this.length = 1;
			return this;
		}
		
		// The body element only exists once, optimize finding it
		if ( selector === "body" && !context ) {
			this.context = document;
			this[0] = document.body;
			this.selector = "body";
			this.length = 1;
			return this;
		}

		// Handle HTML strings
		if ( typeof selector === "string" ) {
			// Are we dealing with HTML string or an ID?
			match = quickExpr.exec( selector );

			// Verify a match, and that no context was specified for #id
			if ( match && (match[1] || !context) ) {

				// HANDLE: $(html) -> $(array)
				if ( match[1] ) {
					doc = (context ? context.ownerDocument || context : document);

					// If a single string is passed in and it's a single tag
					// just do a createElement and skip the rest
					ret = rsingleTag.exec( selector );

					if ( ret ) {
						if ( jQuery.isPlainObject( context ) ) {
							selector = [ document.createElement( ret[1] ) ];
							jQuery.fn.attr.call( selector, context, true );

						} else {
							selector = [ doc.createElement( ret[1] ) ];
						}

					} else {
						ret = buildFragment( [ match[1] ], [ doc ] );
						selector = (ret.cacheable ? ret.fragment.cloneNode(true) : ret.fragment).childNodes;
					}
					
					return jQuery.merge( this, selector );
					
				// HANDLE: $("#id")
				} else {
					elem = document.getElementById( match[2] );

					if ( elem ) {
						// Handle the case where IE and Opera return items
						// by name instead of ID
						if ( elem.id !== match[2] ) {
							return rootjQuery.find( selector );
						}

						// Otherwise, we inject the element directly into the jQuery object
						this.length = 1;
						this[0] = elem;
					}

					this.context = document;
					this.selector = selector;
					return this;
				}

			// HANDLE: $("TAG")
			} else if ( !context && /^\w+$/.test( selector ) ) {
				this.selector = selector;
				this.context = document;
				selector = document.getElementsByTagName( selector );
				return jQuery.merge( this, selector );

			// HANDLE: $(expr, $(...))
			} else if ( !context || context.jquery ) {
				return (context || rootjQuery).find( selector );

			// HANDLE: $(expr, context)
			// (which is just equivalent to: $(context).find(expr)
			} else {
				return jQuery( context ).find( selector );
			}

		// HANDLE: $(function)
		// Shortcut for document ready
		} else if ( jQuery.isFunction( selector ) ) {
			return rootjQuery.ready( selector );
		}

		if (selector.selector !== undefined) {
			this.selector = selector.selector;
			this.context = selector.context;
		}

		return jQuery.makeArray( selector, this );
	},

	// Start with an empty selector
	selector: "",

	// The current version of jQuery being used
	jquery: "1.4.2",

	// The default length of a jQuery object is 0
	length: 0,

	// The number of elements contained in the matched element set
	size: function() {
		return this.length;
	},

	toArray: function() {
		return slice.call( this, 0 );
	},

	// Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get: function( num ) {
		return num == null ?

			// Return a 'clean' array
			this.toArray() :

			// Return just the object
			( num < 0 ? this.slice(num)[ 0 ] : this[ num ] );
	},

	// Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack: function( elems, name, selector ) {
		// Build a new jQuery matched element set
		var ret = jQuery();

		if ( jQuery.isArray( elems ) ) {
			push.apply( ret, elems );
		
		} else {
			jQuery.merge( ret, elems );
		}

		// Add the old object onto the stack (as a reference)
		ret.prevObject = this;

		ret.context = this.context;

		if ( name === "find" ) {
			ret.selector = this.selector + (this.selector ? " " : "") + selector;
		} else if ( name ) {
			ret.selector = this.selector + "." + name + "(" + selector + ")";
		}

		// Return the newly-formed element set
		return ret;
	},

	// Execute a callback for every element in the matched set.
	// (You can seed the arguments with an array of args, but this is
	// only used internally.)
	each: function( callback, args ) {
		return jQuery.each( this, callback, args );
	},
	
	ready: function( fn ) {
		// Attach the listeners
		jQuery.bindReady();

		// If the DOM is already ready
		if ( jQuery.isReady ) {
			// Execute the function immediately
			fn.call( document, jQuery );

		// Otherwise, remember the function for later
		} else if ( readyList ) {
			// Add the function to the wait list
			readyList.push( fn );
		}

		return this;
	},
	
	eq: function( i ) {
		return i === -1 ?
			this.slice( i ) :
			this.slice( i, +i + 1 );
	},

	first: function() {
		return this.eq( 0 );
	},

	last: function() {
		return this.eq( -1 );
	},

	slice: function() {
		return this.pushStack( slice.apply( this, arguments ),
			"slice", slice.call(arguments).join(",") );
	},

	map: function( callback ) {
		return this.pushStack( jQuery.map(this, function( elem, i ) {
			return callback.call( elem, i, elem );
		}));
	},
	
	end: function() {
		return this.prevObject || jQuery(null);
	},

	// For internal use only.
	// Behaves like an Array's method, not like a jQuery method.
	push: push,
	sort: [].sort,
	splice: [].splice
};

// Give the init function the jQuery prototype for later instantiation
jQuery.fn.init.prototype = jQuery.fn;

jQuery.extend = jQuery.fn.extend = function() {
	// copy reference to target object
	var target = arguments[0] || {}, i = 1, length = arguments.length, deep = false, options, name, src, copy;

	// Handle a deep copy situation
	if ( typeof target === "boolean" ) {
		deep = target;
		target = arguments[1] || {};
		// skip the boolean and the target
		i = 2;
	}

	// Handle case when target is a string or something (possible in deep copy)
	if ( typeof target !== "object" && !jQuery.isFunction(target) ) {
		target = {};
	}

	// extend jQuery itself if only one argument is passed
	if ( length === i ) {
		target = this;
		--i;
	}

	for ( ; i < length; i++ ) {
		// Only deal with non-null/undefined values
		if ( (options = arguments[ i ]) != null ) {
			// Extend the base object
			for ( name in options ) {
				src = target[ name ];
				copy = options[ name ];

				// Prevent never-ending loop
				if ( target === copy ) {
					continue;
				}

				// Recurse if we're merging object literal values or arrays
				if ( deep && copy && ( jQuery.isPlainObject(copy) || jQuery.isArray(copy) ) ) {
					var clone = src && ( jQuery.isPlainObject(src) || jQuery.isArray(src) ) ? src
						: jQuery.isArray(copy) ? [] : {};

					// Never move original objects, clone them
					target[ name ] = jQuery.extend( deep, clone, copy );

				// Don't bring in undefined values
				} else if ( copy !== undefined ) {
					target[ name ] = copy;
				}
			}
		}
	}

	// Return the modified object
	return target;
};

jQuery.extend({
	noConflict: function( deep ) {
		window.$ = _$;

		if ( deep ) {
			window.jQuery = _jQuery;
		}

		return jQuery;
	},
	
	// Is the DOM ready to be used? Set to true once it occurs.
	isReady: false,
	
	// Handle when the DOM is ready
	ready: function() {
		// Make sure that the DOM is not already loaded
		if ( !jQuery.isReady ) {
			// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
			if ( !document.body ) {
				return setTimeout( jQuery.ready, 13 );
			}

			// Remember that the DOM is ready
			jQuery.isReady = true;

			// If there are functions bound, to execute
			if ( readyList ) {
				// Execute all of them
				var fn, i = 0;
				while ( (fn = readyList[ i++ ]) ) {
					fn.call( document, jQuery );
				}

				// Reset the list of functions
				readyList = null;
			}

			// Trigger any bound ready events
			if ( jQuery.fn.triggerHandler ) {
				jQuery( document ).triggerHandler( "ready" );
			}
		}
	},
	
	bindReady: function() {
		if ( readyBound ) {
			return;
		}

		readyBound = true;

		// Catch cases where $(document).ready() is called after the
		// browser event has already occurred.
		if ( document.readyState === "complete" ) {
			return jQuery.ready();
		}

		// Mozilla, Opera and webkit nightlies currently support this event
		if ( document.addEventListener ) {
			// Use the handy event callback
			document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );
			
			// A fallback to window.onload, that will always work
			window.addEventListener( "load", jQuery.ready, false );

		// If IE event model is used
		} else if ( document.attachEvent ) {
			// ensure firing before onload,
			// maybe late but safe also for iframes
			document.attachEvent("onreadystatechange", DOMContentLoaded);
			
			// A fallback to window.onload, that will always work
			window.attachEvent( "onload", jQuery.ready );

			// If IE and not a frame
			// continually check to see if the document is ready
			var toplevel = false;

			try {
				toplevel = window.frameElement == null;
			} catch(e) {}

			if ( document.documentElement.doScroll && toplevel ) {
				doScrollCheck();
			}
		}
	},

	// See test/unit/core.js for details concerning isFunction.
	// Since version 1.3, DOM methods and functions like alert
	// aren't supported. They return false on IE (#2968).
	isFunction: function( obj ) {
		return toString.call(obj) === "[object Function]";
	},

	isArray: function( obj ) {
		return toString.call(obj) === "[object Array]";
	},

	isPlainObject: function( obj ) {
		// Must be an Object.
		// Because of IE, we also have to check the presence of the constructor property.
		// Make sure that DOM nodes and window objects don't pass through, as well
		if ( !obj || toString.call(obj) !== "[object Object]" || obj.nodeType || obj.setInterval ) {
			return false;
		}
		
		// Not own constructor property must be Object
		if ( obj.constructor
			&& !hasOwnProperty.call(obj, "constructor")
			&& !hasOwnProperty.call(obj.constructor.prototype, "isPrototypeOf") ) {
			return false;
		}
		
		// Own properties are enumerated firstly, so to speed up,
		// if last one is own, then all properties are own.
	
		var key;
		for ( key in obj ) {}
		
		return key === undefined || hasOwnProperty.call( obj, key );
	},

	isEmptyObject: function( obj ) {
		for ( var name in obj ) {
			return false;
		}
		return true;
	},
	
	error: function( msg ) {
		throw msg;
	},
	
	parseJSON: function( data ) {
		if ( typeof data !== "string" || !data ) {
			return null;
		}

		// Make sure leading/trailing whitespace is removed (IE can't handle it)
		data = jQuery.trim( data );
		
		// Make sure the incoming data is actual JSON
		// Logic borrowed from http://json.org/json2.js
		if ( /^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
			.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
			.replace(/(?:^|:|,)(?:\s*\[)+/g, "")) ) {

			// Try to use the native JSON parser first
			return window.JSON && window.JSON.parse ?
				window.JSON.parse( data ) :
				(new Function("return " + data))();

		} else {
			jQuery.error( "Invalid JSON: " + data );
		}
	},

	noop: function() {},

	// Evalulates a script in a global context
	globalEval: function( data ) {
		if ( data && rnotwhite.test(data) ) {
			// Inspired by code by Andrea Giammarchi
			// http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
			var head = document.getElementsByTagName("head")[0] || document.documentElement,
				script = document.createElement("script");

			script.type = "text/javascript";

			if ( jQuery.support.scriptEval ) {
				script.appendChild( document.createTextNode( data ) );
			} else {
				script.text = data;
			}

			// Use insertBefore instead of appendChild to circumvent an IE6 bug.
			// This arises when a base node is used (#2709).
			head.insertBefore( script, head.firstChild );
			head.removeChild( script );
		}
	},

	nodeName: function( elem, name ) {
		return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
	},

	// args is for internal usage only
	each: function( object, callback, args ) {
		var name, i = 0,
			length = object.length,
			isObj = length === undefined || jQuery.isFunction(object);

		if ( args ) {
			if ( isObj ) {
				for ( name in object ) {
					if ( callback.apply( object[ name ], args ) === false ) {
						break;
					}
				}
			} else {
				for ( ; i < length; ) {
					if ( callback.apply( object[ i++ ], args ) === false ) {
						break;
					}
				}
			}

		// A special, fast, case for the most common use of each
		} else {
			if ( isObj ) {
				for ( name in object ) {
					if ( callback.call( object[ name ], name, object[ name ] ) === false ) {
						break;
					}
				}
			} else {
				for ( var value = object[0];
					i < length && callback.call( value, i, value ) !== false; value = object[++i] ) {}
			}
		}

		return object;
	},

	trim: function( text ) {
		return (text || "").replace( rtrim, "" );
	},

	// results is for internal usage only
	makeArray: function( array, results ) {
		var ret = results || [];

		if ( array != null ) {
			// The window, strings (and functions) also have 'length'
			// The extra typeof function check is to prevent crashes
			// in Safari 2 (See: #3039)
			if ( array.length == null || typeof array === "string" || jQuery.isFunction(array) || (typeof array !== "function" && array.setInterval) ) {
				push.call( ret, array );
			} else {
				jQuery.merge( ret, array );
			}
		}

		return ret;
	},

	inArray: function( elem, array ) {
		if ( array.indexOf ) {
			return array.indexOf( elem );
		}

		for ( var i = 0, length = array.length; i < length; i++ ) {
			if ( array[ i ] === elem ) {
				return i;
			}
		}

		return -1;
	},

	merge: function( first, second ) {
		var i = first.length, j = 0;

		if ( typeof second.length === "number" ) {
			for ( var l = second.length; j < l; j++ ) {
				first[ i++ ] = second[ j ];
			}
		
		} else {
			while ( second[j] !== undefined ) {
				first[ i++ ] = second[ j++ ];
			}
		}

		first.length = i;

		return first;
	},

	grep: function( elems, callback, inv ) {
		var ret = [];

		// Go through the array, only saving the items
		// that pass the validator function
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			if ( !inv !== !callback( elems[ i ], i ) ) {
				ret.push( elems[ i ] );
			}
		}

		return ret;
	},

	// arg is for internal usage only
	map: function( elems, callback, arg ) {
		var ret = [], value;

		// Go through the array, translating each of the items to their
		// new value (or values).
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			value = callback( elems[ i ], i, arg );

			if ( value != null ) {
				ret[ ret.length ] = value;
			}
		}

		return ret.concat.apply( [], ret );
	},

	// A global GUID counter for objects
	guid: 1,

	proxy: function( fn, proxy, thisObject ) {
		if ( arguments.length === 2 ) {
			if ( typeof proxy === "string" ) {
				thisObject = fn;
				fn = thisObject[ proxy ];
				proxy = undefined;

			} else if ( proxy && !jQuery.isFunction( proxy ) ) {
				thisObject = proxy;
				proxy = undefined;
			}
		}

		if ( !proxy && fn ) {
			proxy = function() {
				return fn.apply( thisObject || this, arguments );
			};
		}

		// Set the guid of unique handler to the same of original handler, so it can be removed
		if ( fn ) {
			proxy.guid = fn.guid = fn.guid || proxy.guid || jQuery.guid++;
		}

		// So proxy can be declared as an argument
		return proxy;
	},

	// Use of jQuery.browser is frowned upon.
	// More details: http://docs.jquery.com/Utilities/jQuery.browser
	uaMatch: function( ua ) {
		ua = ua.toLowerCase();

		var match = /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
			/(opera)(?:.*version)?[ \/]([\w.]+)/.exec( ua ) ||
			/(msie) ([\w.]+)/.exec( ua ) ||
			!/compatible/.test( ua ) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec( ua ) ||
		  	[];

		return { browser: match[1] || "", version: match[2] || "0" };
	},

	browser: {}
});

browserMatch = jQuery.uaMatch( userAgent );
if ( browserMatch.browser ) {
	jQuery.browser[ browserMatch.browser ] = true;
	jQuery.browser.version = browserMatch.version;
}

// Deprecated, use jQuery.browser.webkit instead
if ( jQuery.browser.webkit ) {
	jQuery.browser.safari = true;
}

if ( indexOf ) {
	jQuery.inArray = function( elem, array ) {
		return indexOf.call( array, elem );
	};
}

// All jQuery objects should point back to these
rootjQuery = jQuery(document);

// Cleanup functions for the document ready method
if ( document.addEventListener ) {
	DOMContentLoaded = function() {
		document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
		jQuery.ready();
	};

} else if ( document.attachEvent ) {
	DOMContentLoaded = function() {
		// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
		if ( document.readyState === "complete" ) {
			document.detachEvent( "onreadystatechange", DOMContentLoaded );
			jQuery.ready();
		}
	};
}

// The DOM ready check for Internet Explorer
function doScrollCheck() {
	if ( jQuery.isReady ) {
		return;
	}

	try {
		// If IE is used, use the trick by Diego Perini
		// http://javascript.nwbox.com/IEContentLoaded/
		document.documentElement.doScroll("left");
	} catch( error ) {
		setTimeout( doScrollCheck, 1 );
		return;
	}

	// and execute any waiting functions
	jQuery.ready();
}

function evalScript( i, elem ) {
	if ( elem.src ) {
		jQuery.ajax({
			url: elem.src,
			async: false,
			dataType: "script"
		});
	} else {
		jQuery.globalEval( elem.text || elem.textContent || elem.innerHTML || "" );
	}

	if ( elem.parentNode ) {
		elem.parentNode.removeChild( elem );
	}
}

// Mutifunctional method to get and set values to a collection
// The value/s can be optionally by executed if its a function
function access( elems, key, value, exec, fn, pass ) {
	var length = elems.length;
	
	// Setting many attributes
	if ( typeof key === "object" ) {
		for ( var k in key ) {
			access( elems, k, key[k], exec, fn, value );
		}
		return elems;
	}
	
	// Setting one attribute
	if ( value !== undefined ) {
		// Optionally, function values get executed if exec is true
		exec = !pass && exec && jQuery.isFunction(value);
		
		for ( var i = 0; i < length; i++ ) {
			fn( elems[i], key, exec ? value.call( elems[i], i, fn( elems[i], key ) ) : value, pass );
		}
		
		return elems;
	}
	
	// Getting an attribute
	return length ? fn( elems[0], key ) : undefined;
}

function now() {
	return (new Date).getTime();
}
(function() {

	jQuery.support = {};

	var root = document.documentElement,
		script = document.createElement("script"),
		div = document.createElement("div"),
		id = "script" + now();

	div.style.display = "none";
	div.innerHTML = "   <link/><table></table><a href='/a' style='color:red;float:left;opacity:.55;'>a</a><input type='checkbox'/>";

	var all = div.getElementsByTagName("*"),
		a = div.getElementsByTagName("a")[0];

	// Can't get basic test support
	if ( !all || !all.length || !a ) {
		return;
	}

	jQuery.support = {
		// IE strips leading whitespace when .innerHTML is used
		leadingWhitespace: div.firstChild.nodeType === 3,

		// Make sure that tbody elements aren't automatically inserted
		// IE will insert them into empty tables
		tbody: !div.getElementsByTagName("tbody").length,

		// Make sure that link elements get serialized correctly by innerHTML
		// This requires a wrapper element in IE
		htmlSerialize: !!div.getElementsByTagName("link").length,

		// Get the style information from getAttribute
		// (IE uses .cssText insted)
		style: /red/.test( a.getAttribute("style") ),

		// Make sure that URLs aren't manipulated
		// (IE normalizes it by default)
		hrefNormalized: a.getAttribute("href") === "/a",

		// Make sure that element opacity exists
		// (IE uses filter instead)
		// Use a regex to work around a WebKit issue. See #5145
		opacity: /^0.55$/.test( a.style.opacity ),

		// Verify style float existence
		// (IE uses styleFloat instead of cssFloat)
		cssFloat: !!a.style.cssFloat,

		// Make sure that if no value is specified for a checkbox
		// that it defaults to "on".
		// (WebKit defaults to "" instead)
		checkOn: div.getElementsByTagName("input")[0].value === "on",

		// Make sure that a selected-by-default option has a working selected property.
		// (WebKit defaults to false instead of true, IE too, if it's in an optgroup)
		optSelected: document.createElement("select").appendChild( document.createElement("option") ).selected,

		parentNode: div.removeChild( div.appendChild( document.createElement("div") ) ).parentNode === null,

		// Will be defined later
		deleteExpando: true,
		checkClone: false,
		scriptEval: false,
		noCloneEvent: true,
		boxModel: null
	};

	script.type = "text/javascript";
	try {
		script.appendChild( document.createTextNode( "window." + id + "=1;" ) );
	} catch(e) {}

	root.insertBefore( script, root.firstChild );

	// Make sure that the execution of code works by injecting a script
	// tag with appendChild/createTextNode
	// (IE doesn't support this, fails, and uses .text instead)
	if ( window[ id ] ) {
		jQuery.support.scriptEval = true;
		delete window[ id ];
	}

	// Test to see if it's possible to delete an expando from an element
	// Fails in Internet Explorer
	try {
		delete script.test;
	
	} catch(e) {
		jQuery.support.deleteExpando = false;
	}

	root.removeChild( script );

	if ( div.attachEvent && div.fireEvent ) {
		div.attachEvent("onclick", function click() {
			// Cloning a node shouldn't copy over any
			// bound event handlers (IE does this)
			jQuery.support.noCloneEvent = false;
			div.detachEvent("onclick", click);
		});
		div.cloneNode(true).fireEvent("onclick");
	}

	div = document.createElement("div");
	div.innerHTML = "<input type='radio' name='radiotest' checked='checked'/>";

	var fragment = document.createDocumentFragment();
	fragment.appendChild( div.firstChild );

	// WebKit doesn't clone checked state correctly in fragments
	jQuery.support.checkClone = fragment.cloneNode(true).cloneNode(true).lastChild.checked;

	// Figure out if the W3C box model works as expected
	// document.body must exist before we can do this
	jQuery(function() {
		var div = document.createElement("div");
		div.style.width = div.style.paddingLeft = "1px";

		document.body.appendChild( div );
		jQuery.boxModel = jQuery.support.boxModel = div.offsetWidth === 2;
		document.body.removeChild( div ).style.display = 'none';

		div = null;
	});

	// Technique from Juriy Zaytsev
	// http://thinkweb2.com/projects/prototype/detecting-event-support-without-browser-sniffing/
	var eventSupported = function( eventName ) { 
		var el = document.createElement("div"); 
		eventName = "on" + eventName; 

		var isSupported = (eventName in el); 
		if ( !isSupported ) { 
			el.setAttribute(eventName, "return;"); 
			isSupported = typeof el[eventName] === "function"; 
		} 
		el = null; 

		return isSupported; 
	};
	
	jQuery.support.submitBubbles = eventSupported("submit");
	jQuery.support.changeBubbles = eventSupported("change");

	// release memory in IE
	root = script = div = all = a = null;
})();

jQuery.props = {
	"for": "htmlFor",
	"class": "className",
	readonly: "readOnly",
	maxlength: "maxLength",
	cellspacing: "cellSpacing",
	rowspan: "rowSpan",
	colspan: "colSpan",
	tabindex: "tabIndex",
	usemap: "useMap",
	frameborder: "frameBorder"
};
var expando = "jQuery" + now(), uuid = 0, windowData = {};

jQuery.extend({
	cache: {},
	
	expando:expando,

	// The following elements throw uncatchable exceptions if you
	// attempt to add expando properties to them.
	noData: {
		"embed": true,
		"object": true,
		"applet": true
	},

	data: function( elem, name, data ) {
		if ( elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()] ) {
			return;
		}

		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ], cache = jQuery.cache, thisCache;

		if ( !id && typeof name === "string" && data === undefined ) {
			return null;
		}

		// Compute a unique ID for the element
		if ( !id ) { 
			id = ++uuid;
		}

		// Avoid generating a new cache unless none exists and we
		// want to manipulate it.
		if ( typeof name === "object" ) {
			elem[ expando ] = id;
			thisCache = cache[ id ] = jQuery.extend(true, {}, name);

		} else if ( !cache[ id ] ) {
			elem[ expando ] = id;
			cache[ id ] = {};
		}

		thisCache = cache[ id ];

		// Prevent overriding the named cache with undefined values
		if ( data !== undefined ) {
			thisCache[ name ] = data;
		}

		return typeof name === "string" ? thisCache[ name ] : thisCache;
	},

	removeData: function( elem, name ) {
		if ( elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()] ) {
			return;
		}

		elem = elem == window ?
			windowData :
			elem;

		var id = elem[ expando ], cache = jQuery.cache, thisCache = cache[ id ];

		// If we want to remove a specific section of the element's data
		if ( name ) {
			if ( thisCache ) {
				// Remove the section of cache data
				delete thisCache[ name ];

				// If we've removed all the data, remove the element's cache
				if ( jQuery.isEmptyObject(thisCache) ) {
					jQuery.removeData( elem );
				}
			}

		// Otherwise, we want to remove all of the element's data
		} else {
			if ( jQuery.support.deleteExpando ) {
				delete elem[ jQuery.expando ];

			} else if ( elem.removeAttribute ) {
				elem.removeAttribute( jQuery.expando );
			}

			// Completely remove the data cache
			delete cache[ id ];
		}
	}
});

jQuery.fn.extend({
	data: function( key, value ) {
		if ( typeof key === "undefined" && this.length ) {
			return jQuery.data( this[0] );

		} else if ( typeof key === "object" ) {
			return this.each(function() {
				jQuery.data( this, key );
			});
		}

		var parts = key.split(".");
		parts[1] = parts[1] ? "." + parts[1] : "";

		if ( value === undefined ) {
			var data = this.triggerHandler("getData" + parts[1] + "!", [parts[0]]);

			if ( data === undefined && this.length ) {
				data = jQuery.data( this[0], key );
			}
			return data === undefined && parts[1] ?
				this.data( parts[0] ) :
				data;
		} else {
			return this.trigger("setData" + parts[1] + "!", [parts[0], value]).each(function() {
				jQuery.data( this, key, value );
			});
		}
	},

	removeData: function( key ) {
		