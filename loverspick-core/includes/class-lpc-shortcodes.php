<?php
/**
 * LPC_Shortcodes — All frontend shortcodes for LoversPick.
 *
 * [lpc_pricing]            — Pricing cards (pulls values from settings)
 * [lpc_lead_form]          — Lead capture form with UTM tracking
 * [lpc_partners]           — Byeolgram Road partner directory with filter tabs
 * [lpc_partner_apply_form] — Partner application form
 * [lpc_cta_telegram]       — Telegram CTA button
 * [lpc_cta_whatsapp]       — WhatsApp CTA button
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Shortcodes {

    public static function init() {
        add_shortcode( 'lpc_pricing', array( __CLASS__, 'pricing' ) );
        add_shortcode( 'lpc_lead_form', array( __CLASS__, 'lead_form' ) );
        add_shortcode( 'lpc_partners', array( __CLASS__, 'partners' ) );
        add_shortcode( 'lpc_partner_apply_form', array( __CLASS__, 'partner_apply_form' ) );
        add_shortcode( 'lpc_cta_telegram', array( __CLASS__, 'cta_telegram' ) );
        add_shortcode( 'lpc_cta_whatsapp', array( __CLASS__, 'cta_whatsapp' ) );
    }

    /* ── Pricing Cards ──────────────────────────────────────── */

    public static function pricing( $atts ) {
        $atts = shortcode_atts( array( 'show_premium' => 'yes' ), $atts, 'lpc_pricing' );

        $currency = esc_html( get_option( 'lpc_currency', '$' ) );
        $p3 = esc_html( get_option( 'lpc_price_3day', '29' ) );
        $p5 = esc_html( get_option( 'lpc_price_5day', '39' ) );
        $p7 = esc_html( get_option( 'lpc_price_7day', '49' ) );
        $pp = esc_html( get_option( 'lpc_price_premium', '15' ) );

        $c3 = esc_url( get_option( 'lpc_checkout_3day', '' ) );
        $c5 = esc_url( get_option( 'lpc_checkout_5day', '' ) );
        $c7 = esc_url( get_option( 'lpc_checkout_7day', '' ) );

        ob_start();
        ?>
        <div class="lpc-pricing">
            <div class="lpc-pricing__grid">

                <!-- 3-Day -->
                <div class="lpc-pricing__card">
                    <div class="lpc-pricing__header">
                        <h3 class="lpc-pricing__title">3-Day Pass</h3>
                        <div class="lpc-pricing__price"><span class="lpc-pricing__currency"><?php echo $currency; ?></span><?php echo $p3; ?></div>
                        <p class="lpc-pricing__subtitle">Perfect for a weekend trip</p>
                    </div>
                    <ul class="lpc-pricing__features">
                        <li>Unlimited questions</li>
                        <li>Real-time responses (9AM&ndash;11PM KST)</li>
                        <li>Restaurant &amp; price-check help</li>
                        <li>Direction assistance</li>
                    </ul>
                    <a href="<?php echo $c3 ?: '#lpc-lead-form'; ?>"
                       class="lpc-btn lpc-btn--outline lpc-pricing__btn"
                       data-tier="3day"
                       <?php echo $c3 ? '' : 'data-lead-fallback="1"'; ?>>
                        Get 3-Day Pass
                    </a>
                </div>

                <!-- 5-Day (Popular) -->
                <div class="lpc-pricing__card lpc-pricing__card--featured">
                    <div class="lpc-pricing__badge">Most Popular</div>
                    <div class="lpc-pricing__header">
                        <h3 class="lpc-pricing__title">5-Day Pass</h3>
                        <div class="lpc-pricing__price"><span class="lpc-pricing__currency"><?php echo $currency; ?></span><?php echo $p5; ?></div>
                        <p class="lpc-pricing__subtitle">Full trip coverage</p>
                    </div>
                    <ul class="lpc-pricing__features">
                        <li>Everything in 3-Day</li>
                        <li>Extended support hours</li>
                        <li>Reservation assistance</li>
                        <li>Cultural tips &amp; recommendations</li>
                    </ul>
                    <a href="<?php echo $c5 ?: '#lpc-lead-form'; ?>"
                       class="lpc-btn lpc-btn--primary lpc-pricing__btn"
                       data-tier="5day"
                       <?php echo $c5 ? '' : 'data-lead-fallback="1"'; ?>>
                        Get 5-Day Pass
                    </a>
                </div>

                <!-- 7-Day -->
                <div class="lpc-pricing__card">
                    <div class="lpc-pricing__header">
                        <h3 class="lpc-pricing__title">7-Day Pass</h3>
                        <div class="lpc-pricing__price"><span class="lpc-pricing__currency"><?php echo $currency; ?></span><?php echo $p7; ?></div>
                        <p class="lpc-pricing__subtitle">The complete Seoul experience</p>
                    </div>
                    <ul class="lpc-pricing__features">
                        <li>Everything in 5-Day</li>
                        <li>Priority response times</li>
                        <li>Emergency support</li>
                        <li>Byeolgram Road partner benefits</li>
                    </ul>
                    <a href="<?php echo $c7 ?: '#lpc-lead-form'; ?>"
                       class="lpc-btn lpc-btn--outline lpc-pricing__btn"
                       data-tier="7day"
                       <?php echo $c7 ? '' : 'data-lead-fallback="1"'; ?>>
                        Get 7-Day Pass
                    </a>
                </div>

            </div>

            <?php if ( 'yes' === $atts['show_premium'] ) : ?>
            <!-- Premium Add-on -->
            <div class="lpc-pricing__addon">
                <div class="lpc-pricing__addon-inner">
                    <div class="lpc-pricing__addon-info">
                        <h4>Premium Add-on</h4>
                        <p>Late-night support until 2AM KST &bull; Priority response under 5 min &bull; Available with any pass</p>
                    </div>
                    <div class="lpc-pricing__addon-price">
                        +<?php echo $currency . $pp; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ── Lead Form ──────────────────────────────────────────── */

    public static function lead_form( $atts ) {
        $atts = shortcode_atts( array(
            'style'       => 'full',     // full | compact
            'heading'     => '',
            'description' => '',
        ), $atts, 'lpc_lead_form' );

        $is_compact = 'compact' === $atts['style'];

        ob_start();
        ?>
        <div id="lpc-lead-form" class="lpc-lead-form <?php echo $is_compact ? 'lpc-lead-form--compact' : ''; ?>">
            <?php if ( $atts['heading'] ) : ?>
                <h3 class="lpc-lead-form__heading"><?php echo esc_html( $atts['heading'] ); ?></h3>
            <?php endif; ?>
            <?php if ( $atts['description'] ) : ?>
                <p class="lpc-lead-form__desc"><?php echo esc_html( $atts['description'] ); ?></p>
            <?php endif; ?>

            <form class="lpc-lead-form__form" data-lpc-lead-form>
                <?php if ( ! $is_compact ) : ?>
                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field">
                        <label for="lpc-lead-name">Name <span class="lpc-lead-form__optional">(optional)</span></label>
                        <input type="text" id="lpc-lead-name" name="name" placeholder="Your name" />
                    </div>
                    <div class="lpc-lead-form__field">
                        <label for="lpc-lead-country">Country <span class="lpc-lead-form__optional">(optional)</span></label>
                        <input type="text" id="lpc-lead-country" name="country" placeholder="e.g. Japan, USA" />
                    </div>
                </div>
                <?php endif; ?>

                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field lpc-lead-form__field--wide">
                        <label for="lpc-lead-email">Email <span class="lpc-lead-form__required">*</span></label>
                        <input type="email" id="lpc-lead-email" name="email" required placeholder="your@email.com" />
                    </div>
                </div>

                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field">
                        <label for="lpc-lead-messenger">Chat Handle <span class="lpc-lead-form__optional">(optional)</span></label>
                        <input type="text" id="lpc-lead-messenger" name="messenger_handle" placeholder="@username" />
                    </div>
                    <div class="lpc-lead-form__field">
                        <label for="lpc-lead-mtype">Preferred Messenger</label>
                        <select id="lpc-lead-mtype" name="messenger_type">
                            <option value="telegram">Telegram</option>
                            <option value="whatsapp">WhatsApp</option>
                            <option value="kakao">KakaoTalk</option>
                            <option value="line">LINE</option>
                        </select>
                    </div>
                </div>

                <?php if ( ! $is_compact ) : ?>
                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field lpc-lead-form__field--wide">
                        <label for="lpc-lead-dates">Travel Dates <span class="lpc-lead-form__optional">(optional)</span></label>
                        <input type="text" id="lpc-lead-dates" name="travel_dates" placeholder="e.g. March 15 - 20, 2026" />
                    </div>
                </div>
                <?php endif; ?>

                <div class="lpc-lead-form__consent">
                    <label>
                        <input type="checkbox" name="consent" value="1" required />
                        I agree to receive travel tips, updates, and promotional messages from LoversPick. I can unsubscribe at any time.
                    </label>
                </div>

                <!-- Hidden UTM fields (populated by JS) -->
                <input type="hidden" name="utm_source" />
                <input type="hidden" name="utm_medium" />
                <input type="hidden" name="utm_campaign" />
                <input type="hidden" name="utm_term" />
                <input type="hidden" name="utm_content" />
                <input type="hidden" name="source_page" />

                <div class="lpc-lead-form__actions">
                    <button type="submit" class="lpc-btn lpc-btn--primary lpc-btn--lg">
                        Get Started
                    </button>
                </div>

                <div class="lpc-lead-form__message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ── Partner Directory ──────────────────────────────────── */

    public static function partners( $atts ) {
        $atts = shortcode_atts( array(
            'category' => '',  // filter by slug: stay, restaurant
            'area'     => '',  // filter by area slug
            'limit'    => 50,
        ), $atts, 'lpc_partners' );

        // Build query args.
        $args = array(
            'post_type'      => 'lpc_partner',
            'posts_per_page' => absint( $atts['limit'] ),
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_query'     => array(),
            'tax_query'      => array(),
        );

        // Featured partners first.
        $args['meta_key'] = '_lpc_featured';
        $args['orderby']  = array( 'meta_value' => 'DESC', 'title' => 'ASC' );

        if ( $atts['category'] ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'lpc_partner_cat',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['category'] ),
            );
        }

        if ( $atts['area'] ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'lpc_partner_area',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['area'] ),
            );
        }

        $query = new WP_Query( $args );

        ob_start();
        ?>
        <div class="lpc-partners">
            <!-- Filter Tabs -->
            <div class="lpc-partners__filters">
                <button class="lpc-partners__tab lpc-partners__tab--active" data-filter="all">All</button>
                <button class="lpc-partners__tab" data-filter="stay">Stays</button>
                <button class="lpc-partners__tab" data-filter="restaurant">Restaurants</button>
            </div>

            <!-- Area Filters -->
            <div class="lpc-partners__areas">
                <button class="lpc-partners__area-btn lpc-partners__area-btn--active" data-area="all">All Areas</button>
                <?php
                $areas = get_terms( array( 'taxonomy' => 'lpc_partner_area', 'hide_empty' => true ) );
                if ( $areas && ! is_wp_error( $areas ) ) :
                    foreach ( $areas as $area_term ) :
                        ?>
                        <button class="lpc-partners__area-btn" data-area="<?php echo esc_attr( $area_term->slug ); ?>">
                            <?php echo esc_html( $area_term->name ); ?>
                        </button>
                        <?php
                    endforeach;
                endif;
                ?>
            </div>

            <!-- Partner Grid -->
            <div class="lpc-partners__grid">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <?php echo self::render_partner_card( get_the_ID() ); ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="lpc-partners__empty">Partner listings coming soon. Check back later!</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render a single partner card.
     */
    private static function render_partner_card( $post_id ) {
        $title       = get_the_title( $post_id );
        $desc        = get_post_meta( $post_id, '_lpc_short_desc', true );
        $benefits    = get_post_meta( $post_id, '_lpc_benefits', true );
        $languages   = get_post_meta( $post_id, '_lpc_languages', true );
        $map_link    = get_post_meta( $post_id, '_lpc_map_link', true );
        $contact     = get_post_meta( $post_id, '_lpc_contact_link', true );
        $featured    = get_post_meta( $post_id, '_lpc_featured', true ) === '1';

        // Get category and area.
        $cat_terms  = get_the_terms( $post_id, 'lpc_partner_cat' );
        $area_terms = get_the_terms( $post_id, 'lpc_partner_area' );
        $category   = $cat_terms && ! is_wp_error( $cat_terms ) ? $cat_terms[0]->slug : '';
        $cat_name   = $cat_terms && ! is_wp_error( $cat_terms ) ? $cat_terms[0]->name : '';
        $area_name  = $area_terms && ! is_wp_error( $area_terms ) ? $area_terms[0]->name : '';
        $area_slug  = $area_terms && ! is_wp_error( $area_terms ) ? $area_terms[0]->slug : '';

        $thumb = has_post_thumbnail( $post_id )
            ? get_the_post_thumbnail( $post_id, 'medium', array( 'class' => 'lpc-partner-card__img', 'loading' => 'lazy' ) )
            : '';

        ob_start();
        ?>
        <div class="lpc-partner-card <?php echo $featured ? 'lpc-partner-card--featured' : ''; ?>"
             data-category="<?php echo esc_attr( $category ); ?>"
             data-area="<?php echo esc_attr( $area_slug ); ?>">

            <?php if ( $thumb ) : ?>
                <div class="lpc-partner-card__image"><?php echo $thumb; ?></div>
            <?php endif; ?>

            <div class="lpc-partner-card__body">
                <div class="lpc-partner-card__meta">
                    <?php if ( $cat_name ) : ?>
                        <span class="lpc-partner-card__cat lpc-partner-card__cat--<?php echo esc_attr( $category ); ?>"><?php echo esc_html( $cat_name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $area_name ) : ?>
                        <span class="lpc-partner-card__area"><?php echo esc_html( $area_name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $featured ) : ?>
                        <span class="lpc-partner-card__featured-badge">Featured</span>
                    <?php endif; ?>
                </div>

                <h4 class="lpc-partner-card__title"><?php echo esc_html( $title ); ?></h4>

                <?php if ( $desc ) : ?>
                    <p class="lpc-partner-card__desc"><?php echo esc_html( $desc ); ?></p>
                <?php endif; ?>

                <?php if ( $benefits ) : ?>
                    <div class="lpc-partner-card__benefits">
                        <strong>Exclusive Benefits:</strong>
                        <p><?php echo esc_html( $benefits ); ?></p>
                    </div>
                <?php endif; ?>

                <div class="lpc-partner-card__footer">
                    <?php if ( is_array( $languages ) && $languages ) : ?>
                        <div class="lpc-partner-card__langs">
                            <?php foreach ( $languages as $lang ) : ?>
                                <span class="lpc-partner-card__lang"><?php echo esc_html( $lang ); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="lpc-partner-card__actions">
                        <?php if ( $map_link ) : ?>
                            <a href="<?php echo esc_url( $map_link ); ?>" target="_blank" rel="noopener" class="lpc-btn lpc-btn--sm lpc-btn--outline">Map</a>
                        <?php endif; ?>
                        <?php if ( $contact ) : ?>
                            <a href="<?php echo esc_url( $contact ); ?>" target="_blank" rel="noopener" class="lpc-btn lpc-btn--sm lpc-btn--primary">Reserve / Contact</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ── Partner Application Form ───────────────────────────── */

    public static function partner_apply_form( $atts ) {
        ob_start();
        ?>
        <div class="lpc-apply-form">
            <h3 class="lpc-apply-form__heading">Apply to Be a Partner</h3>
            <p class="lpc-apply-form__desc">Join the Byeolgram Road network. Reach international travelers and offer exclusive service benefits.</p>

            <form class="lpc-apply-form__form" data-lpc-partner-form>
                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-biz">Business Name <span class="lpc-lead-form__required">*</span></label>
                        <input type="text" id="lpc-app-biz" name="business_name" required placeholder="Your business name" />
                    </div>
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-contact-name">Contact Name</label>
                        <input type="text" id="lpc-app-contact-name" name="contact_name" placeholder="Your name" />
                    </div>
                </div>

                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-cat">Category</label>
                        <select id="lpc-app-cat" name="category">
                            <option value="stay">Stay / Hotel</option>
                            <option value="restaurant">Restaurant / Cafe</option>
                        </select>
                    </div>
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-area">Area</label>
                        <select id="lpc-app-area" name="area">
                            <option value="hongdae">Hongdae</option>
                            <option value="myeongdong">Myeongdong</option>
                            <option value="gangnam">Gangnam</option>
                            <option value="itaewon">Itaewon</option>
                            <option value="seongsu">Seongsu</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-url">Website / Instagram URL</label>
                        <input type="url" id="lpc-app-url" name="business_url" placeholder="https://" />
                    </div>
                    <div class="lpc-lead-form__field">
                        <label for="lpc-app-contact">Contact Info <span class="lpc-lead-form__required">*</span></label>
                        <input type="text" id="lpc-app-contact" name="contact_info" required placeholder="Email, phone, or KakaoTalk" />
                    </div>
                </div>

                <div class="lpc-lead-form__row">
                    <div class="lpc-lead-form__field lpc-lead-form__field--wide">
                        <label for="lpc-app-msg">Message <span class="lpc-lead-form__optional">(optional)</span></label>
                        <textarea id="lpc-app-msg" name="message" rows="3" placeholder="Tell us about your business and the exclusive benefits you'd like to offer..."></textarea>
                    </div>
                </div>

                <div class="lpc-lead-form__actions">
                    <button type="submit" class="lpc-btn lpc-btn--primary">Submit Application</button>
                </div>

                <div class="lpc-lead-form__message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /* ── CTA Buttons ────────────────────────────────────────── */

    public static function cta_telegram( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Start on Telegram', 'class' => '' ), $atts );
        $link = esc_url( get_option( 'lpc_telegram_link', '#' ) );
        return sprintf(
            '<a href="%s" target="_blank" rel="noopener" class="lpc-btn lpc-btn--primary %s">%s</a>',
            $link,
            esc_attr( $atts['class'] ),
            esc_html( $atts['text'] )
        );
    }

    public static function cta_whatsapp( $atts ) {
        $atts = shortcode_atts( array( 'text' => 'Chat on WhatsApp', 'class' => '' ), $atts );
        $link = esc_url( get_option( 'lpc_whatsapp_link', '#' ) );
        return sprintf(
            '<a href="%s" target="_blank" rel="noopener" class="lpc-btn lpc-btn--accent %s">%s</a>',
            $link,
            esc_attr( $atts['class'] ),
            esc_html( $atts['text'] )
        );
    }
}
