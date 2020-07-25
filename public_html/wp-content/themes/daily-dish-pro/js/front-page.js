// import jQuery from 'jquery';

/**
 * This script adds functionality for the static front page
 */
(function ($) {
    $('.show-more-button').on( 
        'click', 
        () => $('.show-more-container').slideToggle() 
    );
})(jQuery);
