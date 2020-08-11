// import jQuery from 'jquery';

/**
 * This script adds functionality for the static front page
 */
(function ($) {
  $(".show-more-button").on("click", () =>
    $(".show-more-container").slideToggle()
  );

  /**
   * Timer
   */
  // Equal to '2020-08-20 12:00:00 GMT+0200', but Date.parseString() is discouraged.
  const launchDate = new Date(Date.UTC(2020, 7, 20, 10));
  const now = new Date();
  const secondsToLaunch = (launchDate - now) / 1000;

  const timer = new easytimer.Timer();
  timer.start({ countdown: true, startValues: { seconds: secondsToLaunch } });

  updateTimerElements(timer);
  timer.addEventListener("secondsUpdated", () => updateTimerElements(timer));

  function updateTimerElements(timer) {
    $(".countdown__timer__days").text(timer.getTimeValues().days);
    $(".countdown__timer__hours").text(timer.getTimeValues().hours);
    $(".countdown__timer__minutes").text(timer.getTimeValues().minutes);
    $(".countdown__timer__seconds").text(timer.getTimeValues().seconds);
  }

  /**
   * Add 'sticky-wrapper--visible' class to sticky element and animate it when is scrolls out of the viewport.
   */
  const countdown = $(".countdown:not(.countdown--inline)");
  const stickyWrapper = $(".sticky-wrapper");
  $(window).on("scroll", () => {
    const elBottom = countdown.offset().top + countdown.outerHeight();
    const windowTop = $(window).scrollTop();
    const gap = 500;
    const isScrolledPastGap = elBottom + gap < windowTop;
    if (isScrolledPastGap && !stickyWrapper.hasClass("sticky-wrapper--visible")) {
      stickyWrapper.stop().fadeIn(800).addClass("sticky-wrapper--visible");
    } else if (!isScrolledPastGap && stickyWrapper.hasClass("sticky-wrapper--visible")) {
      stickyWrapper.stop().fadeOut(800).removeClass("sticky-wrapper--visible");
    }
  });

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

  /** 
   * Popup testimonial entry on click.
   */
  $('.testimonials__entry').on("click", function(e) {
    $(this).parent().next('.testimonials__popup').empty().append($(this).clone()).addClass('testimonials__popup--visible');
  });

  $('.testimonials__popup').on("click", function(e) {
    if(e.target !== e.currentTarget) 
      return;
    
    $(this).removeClass('testimonials__popup--visible'); 
  });
})(jQuery);
