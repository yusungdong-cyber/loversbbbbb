/**
 * LP AI Match — Admin JavaScript
 */
(function ($) {
	'use strict';

	// Confirmation for destructive actions.
	$(document).on('click', 'button[name="action_type"][value="block"]', function (e) {
		if (!confirm('このユーザーをブロックしますか？')) {
			e.preventDefault();
		}
	});

})(jQuery);
