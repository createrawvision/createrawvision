(function ($) {
  /**
   * Timer
   */
  // Equal to '2020-08-20 12:00:00 GMT+0200', but Date.parseString() is discouraged.
  const launchDate = new Date(Date.UTC(2020, 7, 20, 10));
  const now = new Date();
  const secondsToLaunch = (launchDate - now) / 1000;

  // No timer needed!
  if(secondsToLaunch < 1) {
    $(".countdown").addClass("countdown--done");
    return;
  }

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

  timer.addEventListener("targetAchieved", function (e) {
      $(".countdown").addClass("countdown--done");
  });
})(jQuery);
