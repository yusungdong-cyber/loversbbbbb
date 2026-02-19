/**
 * LoversPick — Frontend JavaScript
 *
 * Handles: lead form submission, partner application, partner directory
 * filtering, UTM parameter capture, smooth scroll, and floating CTA.
 */
(function ($) {
    'use strict';

    var api = ((window.lpcData && window.lpcData.apiUrl) || '/wp-json/loverspick/v1').replace(/\/+$/, '');
    var nonce = (window.lpcData && window.lpcData.nonce) || '';
    var telegramLink = (window.lpcData && window.lpcData.telegramLink) || '';

    /* ── UTM Capture (persists across pages via sessionStorage) */

    function getUtmParams() {
        var params = {};
        var search = window.location.search;
        if (search) {
            var pairs = search.substring(1).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var kv = pairs[i].split('=');
                var key = decodeURIComponent(kv[0]);
                if (key.indexOf('utm_') === 0) {
                    params[key] = decodeURIComponent(kv[1] || '');
                }
            }
        }

        // Store UTMs in sessionStorage on first landing.
        if (Object.keys(params).length > 0) {
            try { sessionStorage.setItem('lpc_utm', JSON.stringify(params)); } catch (e) { /* noop */ }
        }

        // Fall back to stored UTMs if none in current URL.
        if (Object.keys(params).length === 0) {
            try {
                var stored = sessionStorage.getItem('lpc_utm');
                if (stored) { params = JSON.parse(stored); }
            } catch (e) { /* noop */ }
        }

        return params;
    }

    function populateUtmFields($form) {
        var utm = getUtmParams();
        $form.find('input[name="utm_source"]').val(utm.utm_source || '');
        $form.find('input[name="utm_medium"]').val(utm.utm_medium || '');
        $form.find('input[name="utm_campaign"]').val(utm.utm_campaign || '');
        $form.find('input[name="utm_term"]').val(utm.utm_term || '');
        $form.find('input[name="utm_content"]').val(utm.utm_content || '');
        $form.find('input[name="source_page"]').val(window.location.href);
    }

    /* ── Lead Form ──────────────────────────────────────────── */

    $(document).on('submit', '[data-lpc-lead-form]', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $msg = $form.find('.lpc-lead-form__message');

        populateUtmFields($form);

        var data = {};
        $form.serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });
        data.consent = $form.find('input[name="consent"]').is(':checked') ? 1 : 0;

        $btn.prop('disabled', true).text('Sending...');
        $msg.hide().removeClass('lpc-lead-form__message--success lpc-lead-form__message--error');

        $.ajax({
            url: api + '/lead',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            headers: { 'X-WP-Nonce': nonce },
            success: function (res) {
                $msg.text(res.message || 'Thanks! We\'ll be in touch.')
                    .addClass('lpc-lead-form__message--success')
                    .slideDown();
                $form[0].reset();
                // Redirect to Telegram after short delay if available.
                if (telegramLink) {
                    setTimeout(function () {
                        window.open(telegramLink, '_blank');
                    }, 1500);
                }
            },
            error: function (xhr) {
                var errMsg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                $msg.text(errMsg)
                    .addClass('lpc-lead-form__message--error')
                    .slideDown();
            },
            complete: function () {
                $btn.prop('disabled', false).text('Get Started');
            }
        });
    });

    /* ── Partner Application Form ───────────────────────────── */

    $(document).on('submit', '[data-lpc-partner-form]', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var $msg = $form.find('.lpc-lead-form__message');

        var data = {};
        $form.serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        $btn.prop('disabled', true).text('Submitting...');
        $msg.hide().removeClass('lpc-lead-form__message--success lpc-lead-form__message--error');

        $.ajax({
            url: api + '/partner-apply',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            headers: { 'X-WP-Nonce': nonce },
            success: function (res) {
                $msg.text(res.message || 'Application received!')
                    .addClass('lpc-lead-form__message--success')
                    .slideDown();
                $form[0].reset();
            },
            error: function (xhr) {
                var errMsg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                $msg.text(errMsg)
                    .addClass('lpc-lead-form__message--error')
                    .slideDown();
            },
            complete: function () {
                $btn.prop('disabled', false).text('Submit Application');
            }
        });
    });

    /* ── Partner Directory Filters ──────────────────────────── */

    var currentCat = 'all';
    var currentArea = 'all';

    function filterPartners() {
        $('.lpc-partner-card').each(function () {
            var $card = $(this);
            var cardCat = $card.data('category') || '';
            var cardArea = $card.data('area') || '';
            var catMatch = currentCat === 'all' || cardCat === currentCat;
            var areaMatch = currentArea === 'all' || cardArea === currentArea;
            $card.attr('data-hidden', !(catMatch && areaMatch));
        });
    }

    $(document).on('click', '.lpc-partners__tab', function () {
        var $btn = $(this);
        $('.lpc-partners__tab').removeClass('lpc-partners__tab--active');
        $btn.addClass('lpc-partners__tab--active');
        currentCat = $btn.data('filter');
        filterPartners();
    });

    $(document).on('click', '.lpc-partners__area-btn', function () {
        var $btn = $(this);
        $('.lpc-partners__area-btn').removeClass('lpc-partners__area-btn--active');
        $btn.addClass('lpc-partners__area-btn--active');
        currentArea = $btn.data('area');
        filterPartners();
    });

    /* ── Pricing Button Lead Fallback ───────────────────────── */

    $(document).on('click', '.lpc-pricing__btn[data-lead-fallback]', function (e) {
        e.preventDefault();
        var $form = $('#lpc-lead-form');
        if ($form.length) {
            $('html, body').animate({ scrollTop: $form.offset().top - 80 }, 500);
            $form.find('input[name="email"]').focus();
        }
    });

    /* ── Smooth Scroll for Anchor Links ─────────────────────── */

    $(document).on('click', 'a[href^="#lpc-"]', function (e) {
        var target = $($(this).attr('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: target.offset().top - 80 }, 500);
        }
    });

    /* ── Floating CTA ───────────────────────────────────────── */

    $(function () {
        if (telegramLink && window.innerWidth <= 768) {
            var $cta = $('<a>')
                .attr('href', telegramLink)
                .attr('target', '_blank')
                .attr('rel', 'noopener')
                .addClass('lpc-floating-cta')
                .text('Chat on Telegram')
                .css('display', 'block');
            $('body').append($cta);
        }
    });

})(jQuery);
