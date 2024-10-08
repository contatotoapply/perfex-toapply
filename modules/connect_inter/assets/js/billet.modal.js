/* -------------------------------------------------------------------------- */
/*                                    MODAL                                   */
/* -------------------------------------------------------------------------- */
$.fn.modal = function (e) {

	if ($("body").hasClass("modal-active")) {

		$('body').removeClass('overflow-hidden');
		$('body').removeClass('modal-leave');
		$("body").removeClass(function (index, css) {
			return (css.match(/\bmodal-active\S+/g) || []).join(' '); /* removes anything that starts with "modal-active" */
		});
		$('.wrapper-scroll-modal').children().unwrap();
		$('.wrapper-modal').children().unwrap();

		$('.wrapper-scroll-modal').addClass('overflow-hidden');
		setTimeout(function () {
			$('.wrapper-scroll-modal').removeClass('overflow-hidden');
		}, 300);

	}
	/*else {*/
	$('body').addClass('modal-active');
	$('body').addClass('modal-active--' + e);


	$('body').addClass('overflow-hidden');
	$("#modal-" + e).wrap("<div class='wrapper-scroll-modal'><div class='wrapper-modal'></div></div>");


	$('.wrapper-scroll-modal').addClass('overflow-hidden');
	setTimeout(function () {
		$('.wrapper-scroll-modal').removeClass('overflow-hidden');
	}, 300);

  $("#modal-" + e).attr('tabindex', '-1')
  $("#modal-" + e).focus();
}

$.fn.modalClose = function (e) {

	if ($("body").hasClass("modal-active")) {

		$('body').addClass('modal-leave');


		setTimeout(function () {
			$('body').removeClass('overflow-hidden');
			$('body').removeClass('modal-leave');
			$("body").removeClass(function (index, css) {
				return (css.match(/\bmodal-active\S+/g) || []).join(' '); /* removes anything that starts with "modal-active" */
			});
			$('.wrapper-scroll-modal').children().unwrap();
			$('.wrapper-modal').children().unwrap();

		}, 300);

		$('body').removeClass('modal-active');

	}
}


$('[data-modal]').click(function () {
	$(this).modal($(this).attr('data-modal'));
});

$('[data-modal-close]').click(function () {
	$(this).modalClose();
  $('[data-modal]').focus();
});
/* END OF MODAL */
