/**
 * LP AI Match — Frontend JavaScript
 */
(function ($) {
	'use strict';

	var API = lpAiMatch.apiUrl;
	var NONCE = lpAiMatch.nonce;

	/**
	 * API helper.
	 */
	function apiRequest(endpoint, method, data) {
		var options = {
			url: API + endpoint,
			method: method || 'GET',
			beforeSend: function (xhr) {
				xhr.setRequestHeader('X-WP-Nonce', NONCE);
			},
			contentType: 'application/json',
			dataType: 'json',
		};
		if (data) {
			options.data = JSON.stringify(data);
		}
		return $.ajax(options);
	}

	/* ==========================================================================
	   Horoscope
	   ========================================================================== */
	$(document).on('click', '#lp-horoscope-submit', function () {
		var date = $('#lp-horoscope-date').val();
		if (!date) return;

		var $btn = $(this);
		$btn.prop('disabled', true).text(lpAiMatch.i18n.loading);

		apiRequest('/horoscope', 'POST', { birthdate: date })
			.done(function (res) {
				if (res.success) {
					var d = res.data;
					$('#lp-horoscope-symbol').text(d.sign.symbol);
					$('#lp-horoscope-sign').text(d.sign.name);
					$('#lp-horoscope-score').text(d.luck_score);
					$('#lp-horoscope-fortune').text(d.fortune);
					$('#lp-horoscope-color').text(d.lucky_color);
					$('#lp-horoscope-time').text(d.lucky_time);
					$('#lp-horoscope-result').fadeIn(400);
				}
			})
			.fail(function () {
				alert(lpAiMatch.i18n.error);
			})
			.always(function () {
				$btn.prop('disabled', false).text('占う');
			});
	});

	/* ==========================================================================
	   Tarot
	   ========================================================================== */
	$(document).on('click', '#lp-tarot-draw', function () {
		var $btn = $(this);
		$btn.prop('disabled', true).text(lpAiMatch.i18n.loading);

		apiRequest('/tarot/draw', 'POST')
			.done(function (res) {
				if (res.success) {
					var $container = $('#lp-tarot-result .lp-tarot__cards');
					$container.empty();

					res.data.forEach(function (card) {
						var reversedClass = card.is_reversed ? ' lp-tarot__card--reversed' : '';
						var reversedLabel = card.is_reversed ? '<div class="lp-tarot__card-reversed-label">逆位置</div>' : '';
						var html = '<div class="lp-tarot__card' + reversedClass + '">' +
							'<div class="lp-tarot__card-image">' + card.name.charAt(0) + '</div>' +
							'<div class="lp-tarot__card-body">' +
								'<div class="lp-tarot__card-position">' + escHtml(card.position) + '</div>' +
								'<div class="lp-tarot__card-name">' + escHtml(card.name) + '</div>' +
								reversedLabel +
								'<div class="lp-tarot__card-meaning">' + escHtml(card.meaning) + '</div>' +
							'</div>' +
						'</div>';
						$container.append(html);
					});

					$('#lp-tarot-draw-area').fadeOut(200, function () {
						$('#lp-tarot-result').fadeIn(400);
					});
				}
			})
			.fail(function () {
				alert(lpAiMatch.i18n.error);
			})
			.always(function () {
				$btn.prop('disabled', false).text('カードを引く');
			});
	});

	/* ==========================================================================
	   Profile Form — Multi-step
	   ========================================================================== */
	var currentStep = 1;
	var totalSteps = 4;

	function showStep(step) {
		$('.lp-form__step').hide();
		$('.lp-form__step[data-step="' + step + '"]').fadeIn(300);

		$('#lp-form-prev').toggle(step > 1);
		$('#lp-form-next').toggle(step < totalSteps);
		$('#lp-form-submit').toggle(step === totalSteps);
		$('#lp-form-progress').css('width', (step / totalSteps * 100) + '%');
	}

	$(document).on('click', '#lp-form-next', function () {
		if (currentStep < totalSteps) {
			currentStep++;
			showStep(currentStep);
		}
	});

	$(document).on('click', '#lp-form-prev', function () {
		if (currentStep > 1) {
			currentStep--;
			showStep(currentStep);
		}
	});

	$(document).on('submit', '#lp-profile-form-el', function (e) {
		e.preventDefault();

		// Gather data.
		var personalityAnswers = {};
		$(this).find('input[name^="q_"]:checked').each(function () {
			var name = $(this).attr('name').replace('q_', '');
			personalityAnswers[name] = $(this).val();
		});

		var selectedValues = [];
		$(this).find('input[name="values[]"]:checked').each(function () {
			selectedValues.push($(this).val());
		});

		var data = {
			nickname: $(this).find('#lp-nickname').val(),
			birthdate: $(this).find('#lp-birthdate').val(),
			gender: $(this).find('input[name="gender"]:checked').val(),
			personality_answers: personalityAnswers,
			values: selectedValues,
			age_verified: $(this).find('#lp-age-verify').is(':checked') ? 1 : 0,
		};

		var $btn = $('#lp-form-submit');
		$btn.prop('disabled', true).text(lpAiMatch.i18n.loading);

		apiRequest('/profile', 'POST', data)
			.done(function (res) {
				if (res.success) {
					window.location.href = '/matching/';
				} else {
					alert(res.message || lpAiMatch.i18n.error);
				}
			})
			.fail(function () {
				alert(lpAiMatch.i18n.error);
			})
			.always(function () {
				$btn.prop('disabled', false).text('プロフィールを保存');
			});
	});

	// Limit value tags to 3.
	$(document).on('change', 'input[name="values[]"]', function () {
		var checked = $('input[name="values[]"]:checked').length;
		if (checked > 3) {
			$(this).prop('checked', false);
		}
	});

	/* ==========================================================================
	   Pricing — Purchase
	   ========================================================================== */
	$(document).on('click', '.lp-pricing__btn', function () {
		var tier = $(this).data('tier');
		var $btn = $(this);
		$btn.prop('disabled', true).text(lpAiMatch.i18n.loading);

		apiRequest('/payment/create-session', 'POST', { tier: tier })
			.done(function (res) {
				if (res.success && res.data.checkout_url) {
					window.location.href = res.data.checkout_url;
				} else {
					alert(res.message || lpAiMatch.i18n.error);
				}
			})
			.fail(function (xhr) {
				var msg = lpAiMatch.i18n.error;
				if (xhr.responseJSON && xhr.responseJSON.message) {
					msg = xhr.responseJSON.message;
				}
				alert(msg);
			})
			.always(function () {
				$btn.prop('disabled', false).text('購入する');
			});
	});

	/* ==========================================================================
	   Chat
	   ========================================================================== */
	var chatPollInterval = null;
	var lastMessageId = 0;

	function initChat() {
		var $chat = $('#lp-chat');
		if (!$chat.length || !$chat.data('match-id')) return;

		loadMessages();
		chatPollInterval = setInterval(loadMessages, 5000);
	}

	function loadMessages() {
		var matchId = $('#lp-chat').data('match-id');
		var userId = $('#lp-chat').data('user-id');

		apiRequest('/chat/' + matchId + '?after=' + lastMessageId, 'GET')
			.done(function (res) {
				if (!res.success) return;

				if (res.data.length > 0) {
					$('#lp-chat-messages .lp-chat__loading').remove();

					res.data.forEach(function (msg) {
						var isMine = parseInt(msg.sender_id) === parseInt(userId);
						var cls = isMine ? 'lp-chat__message--mine' : 'lp-chat__message--theirs';
						var time = msg.created_at ? msg.created_at.substring(11, 16) : '';

						var html = '<div class="lp-chat__message ' + cls + '" data-id="' + msg.id + '">' +
							'<div class="lp-chat__bubble">' + escHtml(msg.message) + '</div>' +
							'<span class="lp-chat__message-time">' + escHtml(time) + '</span>' +
						'</div>';

						$('#lp-chat-messages').append(html);
						lastMessageId = Math.max(lastMessageId, parseInt(msg.id));
					});

					// Auto-scroll.
					var container = document.getElementById('lp-chat-messages');
					if (container) {
						container.scrollTop = container.scrollHeight;
					}
				}

				if (!res.chat_active) {
					$('#lp-chat-messages').append(
						'<div class="lp-chat__loading">' + lpAiMatch.i18n.chatExpired + '</div>'
					);
					$('.lp-chat__input-area').hide();
					clearInterval(chatPollInterval);
				}
			});
	}

	$(document).on('click', '#lp-chat-send', function () {
		var matchId = $('#lp-chat').data('match-id');
		var message = $('#lp-chat-input').val().trim();
		if (!message) return;

		$('#lp-chat-input').val('');

		apiRequest('/chat/send', 'POST', { match_id: matchId, message: message })
			.done(function () {
				loadMessages();
			})
			.fail(function () {
				alert(lpAiMatch.i18n.error);
			});
	});

	// Send on Enter (Shift+Enter for newline).
	$(document).on('keydown', '#lp-chat-input', function (e) {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			$('#lp-chat-send').click();
		}
	});

	// Report.
	$(document).on('click', '#lp-chat-report-btn', function () {
		$('#lp-report-modal').fadeIn(200);
	});
	$(document).on('click', '#lp-report-cancel', function () {
		$('#lp-report-modal').fadeOut(200);
	});
	$(document).on('click', '#lp-report-submit', function () {
		var reason = $('#lp-report-reason').val().trim();
		if (!reason) return;

		apiRequest('/chat/report', 'POST', {
			message_id: lastMessageId,
			reason: reason,
		}).done(function () {
			alert(lpAiMatch.i18n.reportSent);
			$('#lp-report-modal').fadeOut(200);
			$('#lp-report-reason').val('');
		});
	});

	// Contact consent.
	$(document).on('click', '#lp-chat-consent', function () {
		var matchId = $('#lp-chat').data('match-id');
		if (!confirm('連絡先を共有することに同意しますか？相手も同意した場合のみ、連絡先が公開されます。')) {
			return;
		}

		apiRequest('/chat/consent-contact', 'POST', { match_id: matchId })
			.done(function (res) {
				if (res.success) {
					if (res.data.both_consented) {
						alert('両者が同意しました！お互いのプロフィールから連絡先を確認できます。');
					} else {
						alert('同意が記録されました。相手の同意を待っています。');
					}
				}
			});
	});

	/* ==========================================================================
	   Utilities
	   ========================================================================== */
	function escHtml(str) {
		if (!str) return '';
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	}

	/* ==========================================================================
	   Init
	   ========================================================================== */
	$(document).ready(function () {
		initChat();
	});

})(jQuery);
