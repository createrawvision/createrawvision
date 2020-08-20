// import jQuery from 'jquery';

/**
 * This script adds functionality for the static front page
 */
(function ($) {
  /**
   * Make collapsible FAQ Container
   */
  const faqsContainer = $('.homepage-faqs__container');
  const faqsButton = $('.homepage-faqs__button');
  faqsButton.click(function() {
    // Determine 'height: auto' in pixels
    const curHeight = faqsContainer.height();
    const autoHeight = faqsContainer.css('height', 'auto').height();
    const initialHeight = faqsContainer.css('height', '').height();
    faqsContainer.height(curHeight);

    const duration = 400;
    const scrollMargin = 200;

    // Open container, set class, button text and scroll
    if(faqsContainer.hasClass('homepage-faqs__container--open')) {
      faqsContainer.animate({height: initialHeight}, duration, 'swing', () => faqsContainer.css('height', ''))
        .removeClass('homepage-faqs__container--open');
      $(':root').animate({scrollTop: faqsContainer.offset().top + faqsContainer.outerHeight() - scrollMargin}, duration);
      faqsButton.text('Mehr anzeigen').removeClass('homepage-faqs__button--open');
    } else {
      faqsContainer.animate({height: autoHeight}, duration, 'swing', () => faqsContainer.css('height', 'auto'))
        .addClass('homepage-faqs__container--open');
      $(':root').animate({scrollTop: faqsContainer.offset().top - scrollMargin}, duration);
      faqsButton.text('Weniger anzeigen').addClass('homepage-faqs__button--open');
    }
  });
})(jQuery);
