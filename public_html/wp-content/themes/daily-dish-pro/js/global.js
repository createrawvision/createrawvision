/**
 * This script adds the jquery effects to the Daily Dish Pro Theme.
 *
 * @package Daily Dish\JS
 * @author StudioPress
 * @license GPL-2.0+
 */
(function( $ ) {

	// Make sure JS class is added.
	$( document ).ready( function() {
		$( 'body' ).addClass( 'js' );
	});

	// Set variables for header search.
	var $header    = $( '.nav-primary' ),
		$hsToggle  = $( '.toggle-header-search' ),
		$hsWrap    = $( '#header-search-wrap' ),
		$hsInput   = $hsWrap.find( 'input[type="search"]' );


	// Handler for click a show/hide button.
	$hsToggle.on( 'click', function( event ) {

		event.preventDefault();

		if ( $( this ).hasClass( 'close' ) ) {
			hideSearch();
		} else {
			showSearch();
		}

	});

	// Handler for pressing show/hide button.
	$hsToggle.on( 'keydown', function( event ) {

		// If tabbing from toggle button, and search is hidden, exit early.
		if ( event.keyCode === 9 && ! $header.hasClass( 'search-visible' ) ) {
			return;
		}

		event.preventDefault();
		handleKeyDown( event );

	});

	// Hide search when tabbing or escaping out of the search bar.
	$hsInput.on( 'keydown', function( event ) {

		// Tab: 9, Esc: 27.
		if ( event.keyCode === 9 || event.keyCode === 27 ) {
			hideSearch( event.target );
		}

	});

	// Hide search on blur, such as when clicking outside it.
	$hsInput.on( 'blur', hideSearch );

	// Helper function to show the search form.
	function showSearch() {

		$header.addClass( 'search-visible' );
		$hsWrap.fadeIn( 'fast' ).find( 'input[type="search"]' ).focus();
		$hsToggle.attr( 'aria-expanded', true );

	}

	// Helper function to hide the search form.
	function hideSearch() {

		$hsWrap.fadeOut( 'fast' ).parents( '.nav-primary' ).removeClass( 'search-visible' );
		$hsToggle.attr( 'aria-expanded', false );

	}

	// Keydown handler function for toggling search field visibility.
	function handleKeyDown( event ) {

		// Enter/Space, respectively.
		if ( event.keyCode === 13 || event.keyCode === 32 ) {

			event.preventDefault();

			if ( $( event.target ).hasClass( 'close' ) ) {
				hideSearch();
			} else {
				showSearch();
			}

		}

	}

	// Toggle help popup on button click.
	$( '.crv-help-popup__button' ).on( 'click', function () {
		$( this ).closest( '.crv-help-popup' ).toggleClass('crv-help-popup--visible')
	});
	
	// Close help popup on click outside.
	$( '.crv-help-popup' ).on( 'click', function ( event ) {
		if(event.target === event.currentTarget){
			$( this ).removeClass('crv-help-popup--visible')
		}
  });
  
  // Hide help button when scrolle to the bottom of the page.
  $(window).on( 'scroll', function() {
    const margin = 50;
    if( $(window).scrollTop() + $(window).height() > $(document).height() - margin ) {
      $( '.crv-help-popup__button' ).fadeOut();
    } else {
      $( '.crv-help-popup__button' ).fadeIn();
    }
  });

	// Make rcp form receive focus.
	$( document ).ready(function() {
		$('#rcp_login_form #rcp_user_login').focus();
  });
  
  // Make images in post content top level 90vw wide.
  const imageMargin = 'calc(-45vw + 50%)';
  $( '.single .entry-content > p > img:only-child' )
    .parent()
    .css( 'margin-left', imageMargin )
    .css( 'margin-right', imageMargin );

  // Toggle sub menu when clicking empty (unlinked) parent element.
  $( '.genesis-nav-menu .menu-item' )
    .has( '.sub-menu' )
    .find( '> a:not([href])' )
    .click( function() { 
      // If superfish is enabled for the current menu, don't do anything.
      if ( $(this).parents('.genesis-nav-menu.sf-js-enabled').length ) {
        return;
      }
      // Click on the toggle.
      $(this).siblings('.sub-menu-toggle').click() 
    }
  );

})( jQuery );
