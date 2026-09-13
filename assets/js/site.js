/**
 * exMart theme — vanilla JS interactivity (no framework/build step).
 * Mega menus, mobile nav, mini-cart drawer, wishlist (localStorage),
 * FAQ accordion, header search overlay, newsletter forms.
 */
( function () {
	'use strict';

	var data = window.exmartData || {};
	var WISHLIST_KEY = 'exmart_wishlist';
	var latestMiniCartHtml = null;

	function updateCartBadges( count ) {
		count = Math.max( 0, parseInt( count, 10 ) || 0 );
		document.querySelectorAll( '.em-cart-count-badge' ).forEach( function ( badge ) {
			badge.textContent = String( count );
			if ( count > 0 ) {
				badge.hidden = false;
				badge.removeAttribute( 'hidden' );
				badge.style.display = '';
			} else {
				badge.hidden = true;
				badge.style.display = 'none';
			}
		} );
	}

	function fillMiniCart( html ) {
		if ( typeof html !== 'string' || ! html.trim() ) return;
		// Don't wipe a full cart with an empty-state response mid-flight.
		var looksEmpty = html.indexOf( 'woocommerce-mini-cart__empty-message' ) !== -1
			|| html.indexOf( 'em-mini-cart-empty' ) !== -1;
		var hasButtons = html.indexOf( 'em-mini-cart-btn' ) !== -1
			|| html.indexOf( 'woocommerce-mini-cart__buttons' ) !== -1;
		if ( looksEmpty && ! hasButtons && latestMiniCartHtml && latestMiniCartHtml.indexOf( 'em-mini-cart-btn' ) !== -1 ) {
			var badge = document.querySelector( '.em-cart-count-badge' );
			var shown = badge ? ( parseInt( badge.textContent, 10 ) || 0 ) : 0;
			if ( shown > 0 ) return;
		}
		latestMiniCartHtml = html;
		var target = document.querySelector( '#em-cart-drawer .widget_shopping_cart_content' );
		if ( target ) {
			target.innerHTML = html;
		}
	}

	function refreshMiniCartFromServer() {
		var ajaxUrl = data.ajaxUrl || '';
		var nonce = data.nonce || '';
		if ( ! ajaxUrl ) return Promise.resolve();
		var body = new FormData();
		body.append( 'action', 'exmart_get_cart_qtys' );
		body.append( 'nonce', nonce );
		return fetch( ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
			.then( function ( res ) { return res.json(); } )
			.then( function ( json ) {
				if ( ! json || ! json.success || ! json.data ) return;
				if ( typeof json.data.mini_cart_html === 'string' ) {
					fillMiniCart( json.data.mini_cart_html );
				}
				if ( typeof json.data.count !== 'undefined' ) {
					updateCartBadges( json.data.count );
				}
				if ( typeof window.exmartApplyCartSnapshot === 'function' ) {
					window.exmartApplyCartSnapshot( json.data );
				}
			} )
			.catch( function () { /* ignore */ } );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initHeroCarousel();
		initAnnouncement();
		initMegaMenus();
		initMobileNav();
		initCartDrawer();
		initHeaderSearch();
		initFloatingPill();
		initFaq();
		initNewsletterForms();
		initWishlistButtons();
		initWishlistPage();
		initBrandFilter();
		initCardAtc();
		initPdpVariationPills();
		initShopFilters();
		initShopFiltersDrawer();
		initCartPageQty();
		initCheckoutSteps();
		initCartEmptyReload();
	} );

	// ── Product card ATC → quantity stepper ───────────────
	// Contract:
	// 1) UI updates instantly on the card (optimistic).
	// 2) Server sync is quiet in the background.
	// 3) Never open the mini-cart drawer from card taps.
	// 4) Never rewrite mini-cart HTML from card taps.
	// 5) Reconcile cards only after external cart changes / return to page.
	function initCardAtc() {
		var ajaxUrl = data.ajaxUrl || '';
		var nonce = data.nonce || '';
		if ( ! ajaxUrl ) return;

		var desired = {};
		var timers = {};
		var inflight = {};

		function setBadgeCount( count ) {
			updateCartBadges( count );
		}

		function bumpCartBadge( delta ) {
			if ( ! delta ) return;
			var badge = document.querySelector( '.em-cart-count-badge' );
			var current = badge ? ( parseInt( badge.textContent, 10 ) || 0 ) : 0;
			setBadgeCount( current + delta );
		}

		function writeQtyMap( map ) {
			var el = document.getElementById( 'exmart-cart-qty-map' );
			if ( ! el ) return;
			el.textContent = JSON.stringify( map && typeof map === 'object' ? map : {} );
		}

		function readQtyMap() {
			var el = document.getElementById( 'exmart-cart-qty-map' );
			if ( ! el ) return {};
			try {
				var parsed = JSON.parse( el.textContent || '{}' );
				return parsed && typeof parsed === 'object' ? parsed : {};
			} catch ( err ) {
				return {};
			}
		}

		function syncUi( productId, qty ) {
			document.querySelectorAll( '[data-em-card-atc][data-product-id="' + productId + '"]' ).forEach( function ( wrap ) {
				wrap.setAttribute( 'data-qty', String( qty ) );
				var addBtn = wrap.querySelector( '[data-em-atc-add]' );
				var stepper = wrap.querySelector( '[data-em-atc-stepper]' );
				var val = wrap.querySelector( '[data-em-qty-val]' );
				var minus = wrap.querySelector( '[data-em-qty-minus]' );
				if ( qty > 0 ) {
					if ( addBtn ) addBtn.hidden = true;
					if ( stepper ) stepper.hidden = false;
					if ( val ) val.textContent = String( qty );
					if ( minus ) {
						minus.classList.toggle( 'is-remove', qty === 1 );
						minus.setAttribute( 'aria-label', qty === 1 ? 'Remove from cart' : 'Decrease quantity' );
					}
				} else {
					if ( addBtn ) addBtn.hidden = false;
					if ( stepper ) stepper.hidden = true;
					if ( val ) val.textContent = '1';
					if ( minus ) {
						minus.classList.add( 'is-remove' );
						minus.setAttribute( 'aria-label', 'Remove from cart' );
					}
				}
			} );
			syncPdpUi( productId, qty );
		}

		function syncPdpUi( productId, qty ) {
			document.querySelectorAll( '[data-em-pdp-atc][data-product-id="' + productId + '"]' ).forEach( function ( wrap ) {
				wrap.setAttribute( 'data-cart-qty', String( qty ) );
				var idle = wrap.querySelector( '[data-em-pdp-idle]' );
				var incart = wrap.querySelector( '[data-em-pdp-incart]' );
				var val = wrap.querySelector( '[data-em-pdp-cart-val]' );
				if ( qty > 0 ) {
					if ( idle ) idle.hidden = true;
					if ( incart ) incart.hidden = false;
					if ( val ) val.textContent = String( qty );
				} else {
					if ( idle ) idle.hidden = false;
					if ( incart ) incart.hidden = true;
				}
			} );
		}

		function setProductQty( productId, quantity, max ) {
			if ( ! productId ) return;
			var prev = typeof desired[ productId ] !== 'undefined'
				? desired[ productId ]
				: ( parseInt( ( readQtyMap()[ productId ] || 0 ), 10 ) || 0 );
			var next = quantity;
			if ( typeof max === 'number' && max > 0 ) next = Math.min( max, next );
			next = Math.max( 0, next );
			if ( next === prev ) {
				syncUi( productId, next );
				return;
			}
			desired[ productId ] = next;
			syncUi( productId, next );
			bumpCartBadge( next - prev );
			scheduleSync( productId );
		}

		function isPending( productId ) {
			return !!( timers[ productId ] || inflight[ productId ] );
		}

		function applyQtyMap( map, force ) {
			if ( ! map || typeof map !== 'object' ) map = {};
			writeQtyMap( map );
			document.querySelectorAll( '[data-em-card-atc]' ).forEach( function ( wrap ) {
				var id = wrap.getAttribute( 'data-product-id' );
				if ( ! id ) return;
				if ( ! force && isPending( id ) ) return;
				var qty = parseInt( map[ id ] != null ? map[ id ] : 0, 10 ) || 0;
				desired[ id ] = qty;
				syncUi( id, qty );
			} );
			document.querySelectorAll( '[data-em-pdp-atc]' ).forEach( function ( wrap ) {
				var id = wrap.getAttribute( 'data-product-id' );
				if ( ! id ) return;
				if ( ! force && isPending( id ) ) return;
				var qty = parseInt( map[ id ] != null ? map[ id ] : 0, 10 ) || 0;
				desired[ id ] = qty;
				syncPdpUi( id, qty );
			} );
		}

		function fetchCartQtys() {
			var body = new FormData();
			body.append( 'action', 'exmart_get_cart_qtys' );
			body.append( 'nonce', nonce );
			return fetch( ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
				.then( function ( res ) { return res.json(); } )
				.then( function ( json ) {
					if ( ! json || ! json.success || ! json.data ) return;
					applyQtyMap( json.data.quantities || {}, true );
					if ( typeof json.data.count !== 'undefined' ) {
						setBadgeCount( json.data.count );
					}
					if ( typeof json.data.mini_cart_html === 'string' ) {
						fillMiniCart( json.data.mini_cart_html );
					}
				} )
				.catch( function () { /* ignore network blips */ } );
		}

		window.exmartApplyCartSnapshot = function ( snapshot ) {
			if ( ! snapshot ) return;
			if ( snapshot.quantities ) applyQtyMap( snapshot.quantities, false );
			if ( typeof snapshot.count !== 'undefined' ) setBadgeCount( snapshot.count );
			if ( typeof snapshot.mini_cart_html === 'string' ) fillMiniCart( snapshot.mini_cart_html );
		};

		function sendSync( productId ) {
			var quantity = desired[ productId ];
			if ( typeof quantity === 'undefined' ) return;
			if ( inflight[ productId ] ) {
				inflight[ productId ].dirty = true;
				return;
			}

			inflight[ productId ] = { qty: quantity, dirty: false };

			var body = new FormData();
			body.append( 'action', 'exmart_set_cart_qty' );
			body.append( 'nonce', nonce );
			body.append( 'product_id', productId );
			body.append( 'quantity', String( quantity ) );

			fetch( ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' } )
				.then( function ( res ) { return res.json(); } )
				.then( function ( json ) {
					var meta = inflight[ productId ] || {};
					var sent = meta.qty;
					delete inflight[ productId ];

					if ( json && json.success && json.data ) {
						// Persist server truth into the qty map / badge, but do not
						// touch card UI if the shopper already tapped ahead.
						if ( json.data.quantities ) {
							writeQtyMap( json.data.quantities );
						}
						if ( typeof json.data.count !== 'undefined' ) {
							setBadgeCount( json.data.count );
						}
						if ( typeof json.data.mini_cart_html === 'string' ) {
							fillMiniCart( json.data.mini_cart_html );
						}
						if ( desired[ productId ] === sent ) {
							var serverQty = parseInt( json.data.quantity, 10 ) || 0;
							desired[ productId ] = serverQty;
							syncUi( productId, serverQty );
						}
					}

					if ( desired[ productId ] !== sent || meta.dirty ) {
						sendSync( productId );
					}
				} )
				.catch( function () {
					delete inflight[ productId ];
					window.setTimeout( function () {
						if ( typeof desired[ productId ] !== 'undefined' ) sendSync( productId );
					}, 500 );
				} );
		}

		function scheduleSync( productId ) {
			if ( timers[ productId ] ) window.clearTimeout( timers[ productId ] );
			timers[ productId ] = window.setTimeout( function () {
				delete timers[ productId ];
				sendSync( productId );
			}, 200 );
		}

		function setQty( wrap, quantity ) {
			var productId = wrap.getAttribute( 'data-product-id' );
			if ( ! productId ) return;
			var max = parseInt( wrap.getAttribute( 'data-max' ) || '9999', 10 ) || 9999;
			setProductQty( productId, quantity, max );
		}

		document.addEventListener( 'click', function ( e ) {
			/* PDP: local pick qty (does not touch cart until Add). */
			var pickMinus = e.target.closest( '[data-em-pdp-pick-minus]' );
			if ( pickMinus ) {
				e.preventDefault();
				var pickWrapM = pickMinus.closest( '[data-em-pdp-atc]' );
				if ( ! pickWrapM ) return;
				var pickValM = pickWrapM.querySelector( '[data-em-pdp-pick-val]' );
				var pickCurM = parseInt( ( pickValM && pickValM.textContent ) || '1', 10 ) || 1;
				var nextM = Math.max( 1, pickCurM - 1 );
				if ( pickValM ) pickValM.textContent = String( nextM );
				pickMinus.disabled = nextM <= 1;
				return;
			}
			var pickPlus = e.target.closest( '[data-em-pdp-pick-plus]' );
			if ( pickPlus ) {
				e.preventDefault();
				var pickWrapP = pickPlus.closest( '[data-em-pdp-atc]' );
				if ( ! pickWrapP ) return;
				var pickValP = pickWrapP.querySelector( '[data-em-pdp-pick-val]' );
				var pickMax = parseInt( pickWrapP.getAttribute( 'data-max' ) || '99', 10 ) || 99;
				var pickCurP = parseInt( ( pickValP && pickValP.textContent ) || '1', 10 ) || 1;
				var nextP = Math.min( pickMax, pickCurP + 1 );
				if ( pickValP ) pickValP.textContent = String( nextP );
				var minusBtn = pickWrapP.querySelector( '[data-em-pdp-pick-minus]' );
				if ( minusBtn ) minusBtn.disabled = nextP <= 1;
				return;
			}
			var pdpAdd = e.target.closest( '[data-em-pdp-add]' );
			if ( pdpAdd ) {
				e.preventDefault();
				if ( pdpAdd.disabled ) return;
				var pdpWrap = pdpAdd.closest( '[data-em-pdp-atc]' );
				if ( ! pdpWrap ) return;
				var pid = pdpWrap.getAttribute( 'data-product-id' );
				var pickEl = pdpWrap.querySelector( '[data-em-pdp-pick-val]' );
				var pickQty = parseInt( ( pickEl && pickEl.textContent ) || '1', 10 ) || 1;
				var cartQty = parseInt( pdpWrap.getAttribute( 'data-cart-qty' ) || '0', 10 ) || 0;
				var pMax = parseInt( pdpWrap.getAttribute( 'data-max' ) || '99', 10 ) || 99;
				setProductQty( pid, cartQty + pickQty, pMax );
				return;
			}
			var cartMinus = e.target.closest( '[data-em-pdp-cart-minus]' );
			if ( cartMinus ) {
				e.preventDefault();
				var cWrapM = cartMinus.closest( '[data-em-pdp-atc]' );
				if ( ! cWrapM ) return;
				var cQtyM = parseInt( cWrapM.getAttribute( 'data-cart-qty' ) || '0', 10 ) || 0;
				setProductQty( cWrapM.getAttribute( 'data-product-id' ), Math.max( 0, cQtyM - 1 ), parseInt( cWrapM.getAttribute( 'data-max' ) || '99', 10 ) || 99 );
				return;
			}
			var cartPlus = e.target.closest( '[data-em-pdp-cart-plus]' );
			if ( cartPlus ) {
				e.preventDefault();
				var cWrapP = cartPlus.closest( '[data-em-pdp-atc]' );
				if ( ! cWrapP ) return;
				var cQtyP = parseInt( cWrapP.getAttribute( 'data-cart-qty' ) || '0', 10 ) || 0;
				var cMax = parseInt( cWrapP.getAttribute( 'data-max' ) || '99', 10 ) || 99;
				setProductQty( cWrapP.getAttribute( 'data-product-id' ), Math.min( cMax, cQtyP + 1 ), cMax );
				return;
			}

			var addBtn = e.target.closest( '[data-em-atc-add]' );
			if ( addBtn ) {
				e.preventDefault();
				var wrapAdd = addBtn.closest( '[data-em-card-atc]' );
				if ( wrapAdd ) setQty( wrapAdd, 1 );
				return;
			}

			var minus = e.target.closest( '[data-em-qty-minus]' );
			if ( minus ) {
				e.preventDefault();
				var wrapMinus = minus.closest( '[data-em-card-atc]' );
				if ( ! wrapMinus ) return;
				var qMinus = parseInt( wrapMinus.getAttribute( 'data-qty' ) || '0', 10 ) || 0;
				setQty( wrapMinus, Math.max( 0, qMinus - 1 ) );
				return;
			}

			var plus = e.target.closest( '[data-em-qty-plus]' );
			if ( plus ) {
				e.preventDefault();
				var wrapPlus = plus.closest( '[data-em-card-atc]' );
				if ( ! wrapPlus ) return;
				var qPlus = parseInt( wrapPlus.getAttribute( 'data-qty' ) || '0', 10 ) || 0;
				var max = parseInt( wrapPlus.getAttribute( 'data-max' ) || '9999', 10 ) || 9999;
				setQty( wrapPlus, Math.min( max, qPlus + 1 ) );
			}
		} );

		// Mini-cart / cart-page AJAX removals while cards are on screen.
		if ( typeof jQuery !== 'undefined' ) {
			jQuery( document.body ).on( 'removed_from_cart updated_cart_totals', function () {
				window.setTimeout( fetchCartQtys, 50 );
			} );
		}

		// Browser back after editing cart on another page.
		window.addEventListener( 'pageshow', function ( e ) {
			if ( e.persisted ) fetchCartQtys();
		} );

		// Returning to this tab after editing cart elsewhere.
		var visibilityTimer = null;
		document.addEventListener( 'visibilitychange', function () {
			if ( document.visibilityState !== 'visible' ) return;
			if ( visibilityTimer ) window.clearTimeout( visibilityTimer );
			visibilityTimer = window.setTimeout( function () {
				var anyPending = Object.keys( timers ).length > 0 || Object.keys( inflight ).length > 0;
				if ( ! anyPending ) fetchCartQtys();
			}, 300 );
		} );

		window.exmartRefreshCardAtc = fetchCartQtys;
	}

	/**
	 * Turn WooCommerce variation <select>s into Figma pill buttons on the PDP.
	 */
	function initPdpVariationPills() {
		var form = document.querySelector( '.em-pdp-info form.variations_form' );
		if ( ! form ) return;

		form.querySelectorAll( 'table.variations tr' ).forEach( function ( row ) {
			var select = row.querySelector( 'select' );
			if ( ! select || select.dataset.emPills === '1' ) return;
			select.dataset.emPills = '1';
			select.classList.add( 'em-pdp-select-hidden' );

			var labelCell = row.querySelector( '.label label' );
			var attrLabel = labelCell ? labelCell.textContent.replace( /[:\s]+$/, '' ) : '';
			var valueCell = row.querySelector( '.value' );
			if ( ! valueCell ) return;

			var labelEl = document.createElement( 'p' );
			labelEl.className = 'em-pdp-variant-label em-caption';
			function refreshLabel() {
				var opt = select.options[ select.selectedIndex ];
				var name = opt && opt.value ? opt.textContent.trim() : '';
				labelEl.textContent = '';
				if ( attrLabel ) {
					labelEl.appendChild( document.createTextNode( attrLabel + ': ' ) );
				}
				var strong = document.createElement( 'strong' );
				strong.textContent = name || '—';
				labelEl.appendChild( strong );
			}

			var pills = document.createElement( 'div' );
			pills.className = 'em-pdp-variant-pills';
			Array.prototype.forEach.call( select.options, function ( opt ) {
				if ( ! opt.value ) return;
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'em-variant-pill' + ( select.value === opt.value ? ' active' : '' );
				btn.textContent = opt.textContent.trim();
				btn.setAttribute( 'data-value', opt.value );
				btn.addEventListener( 'click', function () {
					select.value = opt.value;
					pills.querySelectorAll( '.em-variant-pill' ).forEach( function ( b ) {
						b.classList.toggle( 'active', b.getAttribute( 'data-value' ) === opt.value );
					} );
					refreshLabel();
					if ( typeof jQuery !== 'undefined' ) {
						jQuery( select ).trigger( 'change' );
					} else {
						select.dispatchEvent( new Event( 'change', { bubbles: true } ) );
					}
				} );
				pills.appendChild( btn );
			} );

			valueCell.insertBefore( labelEl, select );
			valueCell.insertBefore( pills, select );
			if ( labelCell && labelCell.parentNode ) {
				labelCell.parentNode.style.display = 'none';
			}
			refreshLabel();
			select.addEventListener( 'change', function () {
				pills.querySelectorAll( '.em-variant-pill' ).forEach( function ( b ) {
					b.classList.toggle( 'active', b.getAttribute( 'data-value' ) === select.value );
				} );
				refreshLabel();
			} );
		} );
	}

	// ── Homepage hero bento: autoplaying carousel on mobile ──
	// Below 900px the tiles are a horizontal scroll-snap row (CSS); this
	// just auto-advances it, pausing while the visitor is swiping.
	function initHeroCarousel() {
		var track = document.querySelector( '.em-hero-bento-grid' );
		if ( ! track ) return;
		var tiles = Array.prototype.slice.call( track.children );
		if ( tiles.length < 2 ) return;

		if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) return;

		var mq = window.matchMedia( '(min-width: 900px)' );
		var timer = null;
		var resumeTimer = null;

		function step() {
			var gap = parseFloat( window.getComputedStyle( track ).columnGap || window.getComputedStyle( track ).gap || '0' ) || 0;
			var tileWidth = tiles[ 0 ].getBoundingClientRect().width + gap;
			var atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
			track.scrollTo( { left: atEnd ? 0 : track.scrollLeft + tileWidth, behavior: 'smooth' } );
		}

		function stop() {
			if ( timer ) {
				window.clearInterval( timer );
				timer = null;
			}
		}

		function start() {
			stop();
			if ( mq.matches ) return;
			timer = window.setInterval( step, 4500 );
		}

		// A user-initiated swipe pauses autoplay for a bit rather than
		// fighting it mid-gesture.
		track.addEventListener( 'scroll', function () {
			if ( mq.matches ) return;
			stop();
			window.clearTimeout( resumeTimer );
			resumeTimer = window.setTimeout( start, 6000 );
		}, { passive: true } );

		var onBreakpointChange = function () {
			if ( mq.matches ) {
				stop();
			} else {
				start();
			}
		};
		if ( mq.addEventListener ) {
			mq.addEventListener( 'change', onBreakpointChange );
		} else if ( mq.addListener ) {
			mq.addListener( onBreakpointChange );
		}
		onBreakpointChange();
	}

	// ── Announcement bar dismiss ──────────────────────────
	function initAnnouncement() {
		var bar = document.getElementById( 'em-announce' );
		var close = document.getElementById( 'em-announce-close' );
		if ( ! bar || ! close ) return;
		close.addEventListener( 'click', function () {
			bar.hidden = true;
		} );
	}

	// ── Desktop mega menus ────────────────────────────────
	function initMegaMenus() {
		var catsToggle = document.getElementById( 'em-cats-toggle' );
		var brandsToggle = document.getElementById( 'em-brands-toggle' );
		var catsMega = document.getElementById( 'em-cats-mega' );
		var brandsMega = document.getElementById( 'em-brands-mega' );
		var nav = document.getElementById( 'em-desktop-nav' );
		if ( ! nav ) return;

		function closeAll() {
			[ catsToggle, brandsToggle ].forEach( function ( btn ) {
				if ( btn ) { btn.classList.remove( 'open' ); btn.setAttribute( 'aria-expanded', 'false' ); }
			} );
			[ catsMega, brandsMega ].forEach( function ( m ) { if ( m ) m.classList.remove( 'open' ); } );
		}

		function toggle( btn, mega, other, otherMega ) {
			if ( ! btn || ! mega ) return;
			btn.addEventListener( 'click', function () {
				var willOpen = ! mega.classList.contains( 'open' );
				closeAll();
				if ( willOpen ) {
					mega.classList.add( 'open' );
					btn.classList.add( 'open' );
					btn.setAttribute( 'aria-expanded', 'true' );
				}
			} );
		}

		toggle( catsToggle, catsMega );
		toggle( brandsToggle, brandsMega );

		document.addEventListener( 'click', function ( e ) {
			if ( ! nav.contains( e.target ) ) closeAll();
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) closeAll();
		} );
	}

	// ── Mobile nav drawer ─────────────────────────────────
	function initMobileNav() {
		var open = document.getElementById( 'em-hamburger' );
		var close = document.getElementById( 'em-mobile-nav-close' );
		var drawer = document.getElementById( 'em-mobile-nav' );
		var backdrop = document.getElementById( 'em-mobile-nav-backdrop' );
		if ( ! open || ! drawer || ! backdrop ) return;

		function show() { drawer.hidden = false; backdrop.hidden = false; }
		function hide() { drawer.hidden = true; backdrop.hidden = true; }

		open.addEventListener( 'click', show );
		if ( close ) close.addEventListener( 'click', hide );
		backdrop.addEventListener( 'click', hide );
		document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' ) hide(); } );
	}

	// ── Mini-cart drawer ───────────────────────────────────
	function initCartDrawer() {
		var toggles = [ document.getElementById( 'em-cart-toggle' ), document.getElementById( 'em-pill-cart' ) ].filter( Boolean );
		var close = document.getElementById( 'em-cart-close' );
		var drawer = document.getElementById( 'em-cart-drawer' );
		var backdrop = document.getElementById( 'em-cart-backdrop' );
		if ( ! drawer || ! backdrop ) return;

		function show() {
			drawer.hidden = false;
			backdrop.hidden = false;
			// Prefer last synced mini-cart HTML, then confirm with server.
			if ( latestMiniCartHtml ) {
				fillMiniCart( latestMiniCartHtml );
			}
			refreshMiniCartFromServer();
		}
		function hide() {
			drawer.hidden = true;
			backdrop.hidden = true;
		}

		toggles.forEach( function ( btn ) { btn.addEventListener( 'click', show ); } );
		if ( close ) close.addEventListener( 'click', hide );
		backdrop.addEventListener( 'click', hide );
		document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' ) hide(); } );

		// Card ATC uses an inline stepper — do not auto-open the drawer on add.
	}

	// ── Header search overlay (desktop) ───────────────────
	function initHeaderSearch() {
		var toggle = document.getElementById( 'em-header-search-toggle' );
		var close = document.getElementById( 'em-header-search-close' );
		var overlay = document.getElementById( 'em-header-search-overlay' );
		if ( ! toggle || ! overlay ) return;

		toggle.addEventListener( 'click', function () {
			overlay.hidden = ! overlay.hidden;
			if ( ! overlay.hidden ) {
				var input = overlay.querySelector( 'input[type="search"]' );
				if ( input ) setTimeout( function () { input.focus(); }, 60 );
			}
		} );
		if ( close ) close.addEventListener( 'click', function () { overlay.hidden = true; } );
	}

	// ── Floating mobile pill (search shortcut) ────────────
	function initFloatingPill() {
		var searchBtn = document.getElementById( 'em-pill-search' );
		var overlay = document.getElementById( 'em-header-search-overlay' );
		if ( ! searchBtn ) return;
		searchBtn.addEventListener( 'click', function () {
			if ( overlay ) {
				overlay.hidden = false;
				var input = overlay.querySelector( 'input[type="search"]' );
				if ( input ) setTimeout( function () { input.focus(); }, 60 );
				return;
			}
			window.location.href = ( data.searchUrl || '/' );
		} );
	}

	// ── FAQ accordion ──────────────────────────────────────
	function initFaq() {
		document.querySelectorAll( '.em-faq-item' ).forEach( function ( item ) {
			var btn = item.querySelector( '.em-faq-q' );
			if ( ! btn ) return;
			btn.addEventListener( 'click', function () {
				var isOpen = item.classList.toggle( 'open' );
				btn.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			} );
		} );
	}

	// ── Newsletter forms (footer + homepage section) ──────
	function initNewsletterForms() {
		[
			{ form: 'em-newsletter-form', done: 'em-newsletter-done' },
			{ form: 'em-newsletter-form-2', done: 'em-newsletter-done-2' },
		].forEach( function ( pair ) {
			var form = document.getElementById( pair.form );
			var done = document.getElementById( pair.done );
			if ( ! form ) return;
			form.addEventListener( 'submit', function ( e ) {
				e.preventDefault();
				var email = form.querySelector( 'input[name="email"]' ).value;
				var body = new FormData();
				body.append( 'action', 'exmart_newsletter_subscribe' );
				body.append( 'nonce', data.nonce );
				body.append( 'email', email );
				fetch( data.ajaxUrl, { method: 'POST', body: body } )
					.then( function ( r ) { return r.json(); } )
					.then( function ( res ) {
						if ( res.success && done ) {
							form.style.display = 'none';
							done.textContent = res.data.message;
							done.style.display = 'block';
						}
					} );
			} );
		} );
	}

	// ── Wishlist (localStorage, no account required) ──────
	function getWishlist() {
		try {
			var raw = window.localStorage.getItem( WISHLIST_KEY );
			return raw ? JSON.parse( raw ) : [];
		} catch ( err ) {
			return [];
		}
	}

	function saveWishlist( ids ) {
		try {
			window.localStorage.setItem( WISHLIST_KEY, JSON.stringify( ids ) );
		} catch ( err ) { /* localStorage unavailable — wishlist just won't persist */ }
	}

	function refreshWishlistDot() {
		var dot = document.getElementById( 'em-wishlist-dot' );
		if ( ! dot ) return;
		dot.hidden = getWishlist().length === 0;
	}

	function refreshWishlistButtonStates() {
		var ids = getWishlist().map( String );
		document.querySelectorAll( '.em-wishlist-toggle' ).forEach( function ( btn ) {
			var id = String( btn.getAttribute( 'data-product-id' ) || '' );
			var on = id && ids.indexOf( id ) !== -1;
			btn.classList.toggle( 'wishlisted', on );
			btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
			btn.setAttribute( 'aria-label', on ? 'Remove from wishlist' : 'Add to wishlist' );
		} );
	}

	function initWishlistButtons() {
		refreshWishlistDot();
		refreshWishlistButtonStates();

		document.body.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.em-wishlist-toggle' );
			if ( ! btn ) return;
			e.preventDefault();
			e.stopPropagation();
			var id = String( btn.getAttribute( 'data-product-id' ) || '' );
			if ( ! id ) return;
			var ids = getWishlist().map( String );
			var idx = ids.indexOf( id );
			var nowOn = idx === -1;
			if ( nowOn ) ids.push( id ); else ids.splice( idx, 1 );
			saveWishlist( ids );
			btn.classList.toggle( 'wishlisted', nowOn );
			btn.setAttribute( 'aria-pressed', nowOn ? 'true' : 'false' );
			btn.setAttribute( 'aria-label', nowOn ? 'Remove from wishlist' : 'Add to wishlist' );
			refreshWishlistDot();
			refreshWishlistButtonStates();
		}, true );
	}

	// ── Wishlist page / account wishlist panel ───────────
	function showWishlistEl( el, display ) {
		if ( ! el ) return;
		el.hidden = false;
		el.removeAttribute( 'hidden' );
		el.style.display = display || 'block';
	}

	function hideWishlistEl( el ) {
		if ( ! el ) return;
		el.style.display = 'none';
		el.hidden = true;
	}

	function initWishlistPage() {
		var grid = document.getElementById( 'em-wishlist-grid' );
		var loading = document.getElementById( 'em-wishlist-loading' );
		var empty = document.getElementById( 'em-wishlist-empty' );
		var countEl = document.getElementById( 'em-wishlist-count' );
		if ( ! grid ) return;

		var ids = getWishlist();

		if ( ids.length === 0 ) {
			hideWishlistEl( loading );
			hideWishlistEl( grid );
			showWishlistEl( empty, 'flex' );
			if ( countEl ) countEl.textContent = '';
			return;
		}

		var body = new FormData();
		body.append( 'action', 'exmart_get_wishlist_products' );
		body.append( 'nonce', data.nonce );
		ids.forEach( function ( id ) { body.append( 'ids[]', id ); } );

		fetch( data.ajaxUrl, { method: 'POST', body: body } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( res ) {
				hideWishlistEl( loading );
				if ( res.success && res.data.count > 0 ) {
					grid.innerHTML = res.data.html;
					showWishlistEl( grid, 'grid' );
					hideWishlistEl( empty );
					if ( countEl ) countEl.textContent = '(' + res.data.count + ')';
					refreshWishlistButtonStates();
				} else {
					hideWishlistEl( grid );
					showWishlistEl( empty, 'flex' );
					if ( countEl ) countEl.textContent = '';
				}
			} )
			.catch( function () {
				hideWishlistEl( loading );
				hideWishlistEl( grid );
				showWishlistEl( empty, 'flex' );
			} );
	}

	// ── Brand index live filter ────────────────────────────
	function initBrandFilter() {
		var input = document.getElementById( 'em-brand-filter' );
		if ( ! input ) return;
		var cards = Array.prototype.slice.call( document.querySelectorAll( '.em-brand-card' ) );
		var groups = Array.prototype.slice.call( document.querySelectorAll( '[data-brand-group]' ) );
		var emptyMsg = document.getElementById( 'em-brand-empty' );

		input.addEventListener( 'input', function () {
			var q = input.value.trim().toLowerCase();
			var anyVisible = false;
			cards.forEach( function ( card ) {
				var match = ! q || ( card.getAttribute( 'data-brand-name' ) || '' ).indexOf( q ) !== -1;
				card.style.display = match ? '' : 'none';
				if ( match ) anyVisible = true;
			} );
			groups.forEach( function ( group ) {
				var hasVisible = Array.prototype.slice.call( group.querySelectorAll( '.em-brand-card' ) )
					.some( function ( c ) { return c.style.display !== 'none'; } );
				group.style.display = hasVisible ? '' : 'none';
			} );
			if ( emptyMsg ) emptyMsg.style.display = anyVisible ? 'none' : 'block';
		} );
	}

	// ── Shop PLP filters — submit on checkbox change ───────
	function initShopFilters() {
		var form = document.querySelector( '.em-shop-filter-form' );
		if ( ! form ) return;
		form.addEventListener( 'change', function ( e ) {
			if ( e.target && e.target.matches( 'input[type="checkbox"]' ) ) {
				form.submit();
			}
		} );
	}

	// ── Shop PLP filters — mobile drawer (same pattern as cart/mobile nav) ──
	function initShopFiltersDrawer() {
		var open = document.getElementById( 'em-shop-filters-open' );
		var close = document.getElementById( 'em-shop-filters-close' );
		var drawer = document.getElementById( 'em-shop-filters' );
		var backdrop = document.getElementById( 'em-shop-filters-backdrop' );
		if ( ! open || ! drawer || ! backdrop ) return;

		function show() {
			drawer.classList.add( 'is-open' );
			backdrop.hidden = false;
			open.setAttribute( 'aria-expanded', 'true' );
		}
		function hide() {
			drawer.classList.remove( 'is-open' );
			backdrop.hidden = true;
			open.setAttribute( 'aria-expanded', 'false' );
		}

		open.addEventListener( 'click', show );
		if ( close ) close.addEventListener( 'click', hide );
		backdrop.addEventListener( 'click', hide );
		document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' ) hide(); } );
	}

	// ── Cart page qty steppers → update cart form ─────────
	function initCartPageQty() {
		var form = document.querySelector( 'form.woocommerce-cart-form' );
		if ( ! form ) return;

		var updateBtn = form.querySelector( 'button[name="update_cart"]' );
		var timer = null;

		function queueUpdate() {
			if ( ! updateBtn ) return;
			updateBtn.disabled = false;
			updateBtn.removeAttribute( 'disabled' );
			clearTimeout( timer );
			timer = setTimeout( function () {
				// WC enables the button on qty change; click to refresh totals.
				updateBtn.click();
			}, 450 );
		}

		form.addEventListener( 'click', function ( e ) {
			var minus = e.target.closest( '[data-em-cart-qty-minus]' );
			var plus = e.target.closest( '[data-em-cart-qty-plus]' );
			if ( ! minus && ! plus ) return;
			e.preventDefault();
			var wrap = e.target.closest( '[data-em-cart-qty]' );
			if ( ! wrap ) return;
			var input = wrap.querySelector( 'input.qty' );
			if ( ! input ) return;
			var step = parseFloat( input.getAttribute( 'step' ) ) || 1;
			var min = input.getAttribute( 'min' ) !== '' && input.getAttribute( 'min' ) != null
				? parseFloat( input.getAttribute( 'min' ) ) : 0;
			var maxAttr = input.getAttribute( 'max' );
			var max = maxAttr !== '' && maxAttr != null ? parseFloat( maxAttr ) : NaN;
			var val = parseFloat( input.value ) || 0;
			if ( plus ) val += step;
			if ( minus ) val -= step;
			if ( ! isNaN( max ) ) val = Math.min( val, max );
			val = Math.max( val, min );
			input.value = String( val );
			input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			queueUpdate();
		} );

		form.addEventListener( 'change', function ( e ) {
			if ( e.target && e.target.matches( 'input.qty' ) ) {
				queueUpdate();
			}
		} );
	}

	// After the last cart item is removed via AJAX, force a full reload
	// so cart-empty.php (Figma empty state) renders instead of a blank table.
	function initCartEmptyReload() {
		if ( ! document.body.classList.contains( 'woocommerce-cart' ) ) return;
		if ( ! window.jQuery ) return;
		window.jQuery( document.body ).on( 'updated_wc_div updated_cart_totals', function () {
			var form = document.querySelector( 'form.woocommerce-cart-form' );
			if ( ! form ) return;
			if ( form.querySelector( '.cart_item, tr.cart_item, .em-cart-product' ) ) return;
			if ( document.querySelector( '.em-cart-empty' ) ) return;
			window.location.reload();
		} );
	}

	// ── Checkout multi-step (Figma: Contact → Address → Shipping → Payment)
	function initCheckoutSteps() {
		var form = document.querySelector( 'form.checkout[data-em-checkout-steps]' );
		if ( ! form ) return;

		var STEPS = [ 'contact', 'address', 'shipping', 'payment' ];
		var stepIndex = 0;
		var continueBtn = form.querySelector( '.em-checkout-continue' );
		var nav = form.querySelector( '.em-checkout-nav' );
		var pipeline = form.querySelector( '.em-checkout-pipeline' );

		function panelsFor( step ) {
			return form.querySelectorAll( '.em-checkout-step[data-step="' + step + '"]' );
		}

		function withPanels( step ) {
			return form.querySelectorAll( '[data-em-checkout-with="' + step + '"]' );
		}

		function setPipeline( index ) {
			if ( ! pipeline ) return;
			var steps = pipeline.querySelectorAll( '.em-pipeline-step' );
			steps.forEach( function ( el, i ) {
				var dot = el.querySelector( '.em-pipeline-dot' );
				var lab = el.querySelector( '.em-pipeline-label' );
				el.classList.toggle( 'done', i < index );
				if ( dot ) {
					dot.classList.toggle( 'done', i < index );
					dot.classList.toggle( 'current', i === index );
					dot.disabled = i > index;
					dot.innerHTML = '';
					if ( i < index ) {
						dot.innerHTML = '<svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true"><path d="M2 5l2 2 4-4" stroke="var(--paper)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
					}
				}
				if ( lab ) {
					lab.classList.toggle( 'done', i < index );
					lab.classList.toggle( 'current', i === index );
				}
			} );
		}

		function showStep( index ) {
			stepIndex = Math.max( 0, Math.min( index, STEPS.length - 1 ) );
			var id = STEPS[ stepIndex ];

			STEPS.forEach( function ( sid ) {
				panelsFor( sid ).forEach( function ( panel ) {
					var on = sid === id;
					panel.hidden = ! on;
					panel.classList.toggle( 'is-active', on );
				} );
				withPanels( sid ).forEach( function ( el ) {
					el.hidden = sid !== id;
				} );
			} );

			setPipeline( stepIndex );

			if ( nav ) {
				nav.hidden = id === 'payment';
			}
			if ( continueBtn ) {
				continueBtn.hidden = id === 'payment';
			}

			var first = form.querySelector( '.em-checkout-step[data-step="' + id + '"] input, .em-checkout-step[data-step="' + id + '"] select, .em-checkout-step[data-step="' + id + '"] textarea' );
			if ( first && window.matchMedia( '(min-width: 768px)' ).matches ) {
				try { first.focus( { preventScroll: true } ); } catch ( err ) { /* ignore */ }
			}

			form.setAttribute( 'data-em-step', id );
		}

		function visibleRequired( step ) {
			var fields = [];
			panelsFor( step ).forEach( function ( panel ) {
				panel.querySelectorAll( 'input, select, textarea' ).forEach( function ( el ) {
					if ( el.disabled || el.type === 'hidden' || el.type === 'checkbox' || el.type === 'radio' ) return;
					if ( el.getAttribute( 'aria-required' ) === 'true' || el.required || ( el.closest( '.validate-required' ) && el.type !== 'checkbox' ) ) {
						fields.push( el );
					}
				} );
			} );
			return fields;
		}

		function validateStep( step ) {
			var ok = true;
			var firstBad = null;
			visibleRequired( step ).forEach( function ( el ) {
				var empty = ! String( el.value || '' ).trim();
				var invalid = empty;
				if ( ! empty && typeof el.checkValidity === 'function' ) {
					invalid = ! el.checkValidity();
				}
				el.classList.toggle( 'em-checkout-invalid', invalid );
				if ( invalid ) {
					ok = false;
					if ( ! firstBad ) firstBad = el;
				}
			} );
			if ( firstBad ) {
				firstBad.focus();
				if ( typeof firstBad.reportValidity === 'function' ) {
					firstBad.reportValidity();
				}
			}
			return ok;
		}

		if ( continueBtn ) {
			continueBtn.addEventListener( 'click', function () {
				var current = STEPS[ stepIndex ];
				if ( ! validateStep( current ) ) return;
				if ( current === 'address' && window.jQuery ) {
					window.jQuery( document.body ).trigger( 'update_checkout' );
				}
				showStep( stepIndex + 1 );
				if ( STEPS[ stepIndex ] === 'shipping' && window.jQuery ) {
					window.jQuery( document.body ).trigger( 'update_checkout' );
				}
			} );
		}

		if ( pipeline ) {
			pipeline.addEventListener( 'click', function ( e ) {
				var btn = e.target.closest( '[data-em-checkout-goto]' );
				if ( ! btn || btn.disabled ) return;
				var target = btn.getAttribute( 'data-em-checkout-goto' );
				var idx = STEPS.indexOf( target );
				if ( idx < 0 || idx > stepIndex ) return;
				showStep( idx );
			} );
		}

		// Payment fragment refresh can remount #payment — keep step visibility.
		if ( window.jQuery ) {
			window.jQuery( document.body ).on( 'updated_checkout', function () {
				showStep( stepIndex );
			} );
		}

		showStep( 0 );
	}

} )();
