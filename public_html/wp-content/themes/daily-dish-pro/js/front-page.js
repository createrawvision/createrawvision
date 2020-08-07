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
   * Add 'countdown--is_sticky' class to countdown when is reaches sticky position.
   */
  const stickyWrapper = document.querySelector(".sticky-wrapper");
  const countdownElement = document.querySelector(".countdown");

  const observer = new IntersectionObserver(
    ([e]) => {
        e.target.classList.toggle("sticky-wrapper--is_sticky", e.intersectionRatio < 1);
        countdownElement.classList.toggle("full-width", e.intersectionRatio < 1);
    },
    { threshold: 1.0 }
  );

  observer.observe(stickyWrapper);
})(jQuery);
