(function ($) {
  /**
   * Timer
   */
  // Equal to '2020-08-20 12:00:00 GMT+0200', but Date.parseString() is discouraged.
  const launchDate = new Date(Date.UTC(2020, 7, 20, 10));
  const now = new Date();
  const secondsToLaunch = (launchDate - now) / 1000;

  // No timer needed!
  if (secondsToLaunch < 1) {
    $(".countdown").addClass("countdown--done");
    return;
  }

  $.each($(".countdown__timer"), (i, timer) => {
    const clock = $(timer).FlipClock(secondsToLaunch, {
      countdown: true,
      language: "german",
      clockFace: "DailyCounter",
    });

  });

})(jQuery);
