const copyActionTrigger = $('.copy-action-container .copy');

function giveFeedback(trigger) {
  const container = trigger.parent();
  const feedback = container.find('.copy-feedback');
  const durationInSeconds = 2;

  feedback.addClass('active');

  setTimeout(() => {
    feedback.removeClass('active');
  }, durationInSeconds * 1000);
}

$(document).ready(function() {
  copyActionTrigger.click(function() {
    const thisTrigger = $(this);

    giveFeedback(thisTrigger);
  });
});
