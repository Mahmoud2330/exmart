/**
 * exMart theme — vanilla JS interactivity (no framework/build step).
 * Mega menus, mobile nav, mini-cart drawer, wishlist (localStorage),
 * FAQ accordion, header search overlay, newsletter forms.
 */
( function () {
	'use strict';

	var data = window.exmartData || {};
	var WISHLIST_KEY = 'exmart_wishlist';

	document.addEventListener( 'DOMContentLoaded', function () {
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
		initHeroSlider();
	} );

	// ── Homepage hero image slot (main-banner carousel, Figma layout) ──
	function initHeroSlider() {
		var wrap = document.querySelector( '[data-em-hero-slider]' );
		if ( ! wrap ) return;
		var slides = Array.prototype.slice.call( wrap.querySelectorAll( '.em-hero-slide' ) );
		if ( slides.length < 2 ) return;
		var index = 0;
		setInterval( function () {
			slides[ index ].classList.remove( 'is-active' );
			index = ( index + 1 ) % slides.length;
			slides[ index ].classList.add( 'is-active' );
			var bg = document.querySelector( '.em-hero-bg' );
			if ( bg && slides[ index ].src ) {
				bg.style.backgroundImage = 'url("' + slides[ index ].src + '")';
			}
		}, 5000 );
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

		function show() { drawer.hidden = false; backdrop.hidden = false; }
		function hide() { drawer.hidden = true; backdrop.hidden = true; }

		toggles.forEach( function ( btn ) { btn.addEventListener( 'click', show ); } );
		if ( close ) close.addEventListener( 'click', hide );
		backdrop.addEventListener( 'click', hide );
		document.addEventListener( 'keydown', function ( e ) { if ( e.key === 'Escape' ) hide(); } );

		// Open the drawer automatically after a WooCommerce AJAX add-to-cart,
		// mirroring the original app's "add to cart opens the drawer" UX.
		document.body.addEventListener( 'added_to_cart', function () {
			show();
		} );
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
			var id = btn.getAttribute( 'data-product-id' );
			btn.classList.toggle( 'wishlisted', ids.indexOf( id ) !== -1 );
		} );
	}

	function initWishlistButtons() {
		refreshWishlistDot();
		refreshWishlistButtonStates();

		document.body.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '.em-wishlist-toggle' );
			if ( ! btn ) return;
			e.preventDefault();
			var id = btn.getAttribute( 'data-product-id' );
			var ids = getWishlist().map( String );
			var idx = ids.indexOf( id );
			if ( idx === -1 ) ids.push( id ); else ids.splice( idx, 1 );
			saveWishlist( ids );
			refreshWishlistDot();
			refreshWishlistButtonStates();
		} );
	}

	// ── Wishlist page: fetch product cards for saved IDs ──
	function initWishlistPage() {
		var grid = document.getElementById( 'em-wishlist-grid' );
		var loading = document.getElementById( 'em-wishlist-loading' );
		var empty = document.getElementById( 'em-wishlist-empty' );
		var countEl = document.getElementById( 'em-wishlist-count' );
		if ( ! grid ) return;

		var ids = getWishlist();

		if ( ids.length === 0 ) {
			if ( loading ) loading.style.display = 'none';
			if ( empty ) empty.style.display = 'flex';
			return;
		}

		var body = new FormData();
		body.append( 'action', 'exmart_get_wishlist_products' );
		body.append( 'nonce', data.nonce );
		ids.forEach( function ( id ) { body.append( 'ids[]', id ); } );

		fetch( data.ajaxUrl, { method: 'POST', body: body } )
			.then( function ( r ) { return r.json(); } )
			.then( function ( res ) {
				if ( loading ) loading.style.display = 'none';
				if ( res.success && res.data.count > 0 ) {
					grid.innerHTML = res.data.html;
					grid.style.display = 'grid';
					if ( countEl ) countEl.textContent = '(' + res.data.count + ')';
					refreshWishlistButtonStates();
				} else if ( empty ) {
					empty.style.display = 'flex';
				}
			} )
			.catch( function () {
				if ( loading ) loading.style.display = 'none';
				if ( empty ) empty.style.display = 'flex';
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
} )();
