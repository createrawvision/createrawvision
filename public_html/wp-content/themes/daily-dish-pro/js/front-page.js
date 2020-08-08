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
  // Equal to '2020-08-20 17:00:00 GMT+0200', but Date.parseString() is discouraged.
  const launchDate = new Date(Date.UTC(2020, 7, 20, 15));
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
   * Add 'sticky-wrapper--sticky' class to sticky element when is scrolls out of the viewport.
   */
  const countdown = $(".countdown:not(.countdown--inline)");
  const stickyWrapper = $(".sticky-wrapper");
  $(window).on("scroll", () => {
    const elBottom = countdown.offset().top + countdown.outerHeight();
    const windowTop = $(window).scrollTop();
    const gap = 500;
    const isScrolledPastGap = elBottom + gap < windowTop;
    if (isScrolledPastGap && !stickyWrapper.hasClass("visible")) {
      stickyWrapper.stop();
      stickyWrapper.fadeIn(800);
      stickyWrapper.addClass("visible");
    } else if (!isScrolledPastGap && stickyWrapper.hasClass("visible")) {
      stickyWrapper.stop();
      stickyWrapper.fadeOut(800);
      stickyWrapper.removeClass("visible");
    }
    // stickyWrapper.toggleClass('sticky-wrapper--sticky', isScrolledPastGap);
  });
})(jQuery);
