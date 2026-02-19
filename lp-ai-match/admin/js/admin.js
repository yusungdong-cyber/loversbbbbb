/**
 * LP AI Match — Admin JavaScript
 */
(function ($) {
	'use strict';

	// Confirmation for block action.
	$(document).on('submit', 'form', function (e) {
		var $form = $(this);
		var action = $form.find('input[name="action_type"]').val();
		if ('block' === action) {
			if (!confirm('このユーザーをブロックしますか？')) {
				e.preventDefault();
			}
		}
	});

})(jQuery);
