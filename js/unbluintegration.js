(function() {	

	var defaultSettings  = {
		bridgeURL : 'utils/preparebridge.php',
		webserviceURL: 'utils/service.php',
		unbluButtonId : 'activate_live_support',
		cartButtonId : 'updateCart',
		cartTextAddItem : 'Add Item',
		cartTextRemoveItem : 'Remove Item',
		cartImageId: 'cartImage',
		cartImageEmptySrc : 'img/empty.png',
		cartImageFullSrc : 'img/full.png',
		loggerEnabled : true	
	};
	
	var unbluAPI = false;
	var doc = document;
	var win = window;
	var unbluNotAvailable = false;
	var unbluApiConsumerAlreadyRegistered = false;	
	
	/* we will use here simple/native functions such as 
			addEventListener/attachEvent
			removeEventListener/detachEvent 
			apply
			console.log
			without an JS Library, you may want to use any JS libs such JQuery, Prototype, AngularJs, etc
	*/
	var u = {
		attachListener : function( node, type, listener ) {
			if ( node && node.addEventListener ) {
				node.addEventListener( type, listener, false );
			} else if ( node && node.attachEvent ) {
				node.attachEvent( "on" + type, listener, true );
			}
		},
		removeListener : function(node, type, listener ) {
			if ( node && node.removeEventListener ) {
				node.removeEventListener(type, listener, false);
			} else if ( node && node.detachEvent ) {
				node.detachEvent( "on" + type, listener );
			}
		},
		bind: function(fn, context) {
			var args = Array.prototype.slice.call( arguments, 2 );
			var bound = function() {
				var a = args.slice( 0 );
				if ( arguments.length > 0 ) {
					a.push( Array.prototype.slice.call( arguments, 0 ) );
				}
				return fn.apply( context, a );
			}
			
			return bound;
		},
		log : function( message ) {
			if ( defaultSettings.loggerEnabled && typeof win.console != 'undefined' && console.log) {
				console.log( '[Unblu] ' + message );
			}
		}
	};	
	// we will use here simple/native XMLHttpRequest without an JS Library, you may want to use any JS libs such JQuery, Prototype, AngularJs, etc
	var ajax = {
		method: 'post',
		asynchronous: true,
		requestEvents : ['Uninitialized', 'Open', 'Sent', 'Receiving', 'Loaded'],
		transport: ( function() {
			try {
				return new XMLHttpRequest();
			} catch(e){ }
			try {
				return new ActiveXObject( 'Msxml2.XMLHTTP' );
			} catch(e) { }
			try {
				return new ActiveXObject( 'Microsoft.XMLHTTP' );
			} catch(e) { }	
			
			return false;
		})(),	
		request: function( url, onSuccess, onFailure ) {
			this.url = url;
			this.onSuccess = onSuccess;	
			this.onFailure = onFailure;
			this.requestLoaded = false;
			try {
			  this.transport.open( this.method.toUpperCase(), this.url, this.asynchronous );
			  if ( !Function.prototype.bind ) {
				this.transport.onreadystatechange = u.bind( this.onStateChange, this );
			   } else {	
				  this.transport.onreadystatechange = this.onStateChange.bind( this );
			  }			  
			  this.transport.send();
			} catch( e ) { }
		},	
		success: function() {
			var status = this.getStatus();
			return !status || ( status >= 200 && status < 300 ) || status == 304;
		},
		getStatus: function(){
			try {
			  if (this.transport.status === 1223) return 204;
			  return this.transport.status || 0;
			} catch(e) { return 0;}
		},	  
		onStateChange: function() {
			if ( this.transport.readyState > 1 && !( ( this.transport.readyState == 4 ) && this.requestLoaded ) ) {
				this.respondToReadyState( this.transport.readyState );
			}
		},
		respondToReadyState : function() {
			if ( this.success() ) {
				if ( this.requestEvents[this.transport.readyState] === 'Loaded' ) {
					this.requestLoaded = true;
					try {
						this.onSuccess( this.transport.responseText );
					} catch(e) { }
				}
			} else {
				try {
					this.onFailure( this.transport.statusText );
				} catch( e ) { }
			}
		}
	};
	
	var p = {
		//	use simple API to display the PIN Entry UI	
		openUnbluPinEntryUI : function( ) {
			unblu.api.initialize().then( function( api ) { 
				api.ui.openPinEntryUi();
			});
		},
		/*
			This function is called when user clicks on "Live Support"
			an Ajax request is send to server, which will then sets a temporary cookie
			once the response is there, Unblu PIN Entry UI is displayed within the function openUnbluPinEntryUI
		
		*/
		initSessionMigrationBridge : function( ) {
			ajax.request( defaultSettings.bridgeURL, p.openUnbluPinEntryUI, function( resText ){ u.log( resText );} );			
		},
		
		addRemoveItem2Cart : function ( action ) {
			if ( typeof action === 'undefined' || action === '' ) {
				action = p.cartStatus === 'full' ? '?removeItem=OK' : '?addItem=OK';
			}
			
			ajax.request( defaultSettings.webserviceURL + action, function( resText ) { 
				p.updateCartStatus( resText );
				p.updateCartButtonStatus( resText );
			});
		},
		
		updateCartStatus : function ( resText ) {
			var el = document.getElementById(defaultSettings.cartImageId);
			var src = defaultSettings.cartImageEmptySrc;
			p.cartStatus = 'empty';
			
			if ( resText === "full" ) {
				src = defaultSettings.cartImageFullSrc;
				p.cartStatus = 'full';
			} 
			
			if ( el ) el.src =  src;
		},
		
		updateCartButtonStatus : function ( resText ) {
			var el = document.getElementById( defaultSettings.cartButtonId );
			var text = defaultSettings.cartTextAddItem;
			
			if ( resText === "full" ) {
				text = defaultSettings.cartTextRemoveItem;
			} 
			
			if ( el ) el.innerText = text;
		},
		
		cartStatus : 'empty'
	};	

	
	u.attachListener( window, 'load',  function() {	
		//update cart and button status;
		p.addRemoveItem2Cart('?status=get');
		
		//add listener to live support
		var unbluButton = doc.getElementById( defaultSettings.unbluButtonId );
		u.attachListener( unbluButton, 'click', p.initSessionMigrationBridge );	
		
		//add listener to cartButton
		var cartButton = doc.getElementById( defaultSettings.cartButtonId );
		u.attachListener( cartButton , 'click', function () {
			p.addRemoveItem2Cart();
		});
		
	});	
	
})();	
		
