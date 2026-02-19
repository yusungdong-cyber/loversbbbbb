<?php
/**
 * LPC_Pages — Creates all LoversPick pages on plugin activation.
 *
 * Each page is pre-populated with production copy and embedded shortcodes.
 * Content is fully editable in Gutenberg or Elementor after creation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LPC_Pages {

    /**
     * Create all pages (skips if they already exist by slug).
     */
    public static function create_all() {
        $pages = array(
            'loverspick-home'            => array( 'title' => 'LoversPick Seoul', 'content' => self::home_content() ),
            'pricing'                    => array( 'title' => 'Pricing & Purchase', 'content' => self::pricing_content() ),
            'how-it-works'               => array( 'title' => 'How It Works', 'content' => self::how_it_works_content() ),
            'faq'                        => array( 'title' => 'FAQ', 'content' => self::faq_content() ),
            'contact'                    => array( 'title' => 'Contact & Support', 'content' => self::contact_content() ),
            'terms'                      => array( 'title' => 'Terms of Service', 'content' => self::terms_content() ),
            'privacy-policy-loverspick'  => array( 'title' => 'Privacy Policy', 'content' => self::privacy_content() ),
            'byeolgram-road'             => array( 'title' => 'Byeolgram Road', 'content' => self::byeolgram_content() ),
        );

        foreach ( $pages as $slug => $data ) {
            if ( get_page_by_path( $slug ) ) {
                continue; // Page already exists.
            }
            wp_insert_post( array(
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_content' => $data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id() ? get_current_user_id() : 1,
            ) );
        }
    }

    /* ════════════════════════════════════════════════════════════
       HOME PAGE
       ════════════════════════════════════════════════════════ */

    private static function home_content() {
        return '<!-- HERO SECTION -->
<div class="lpc-hero">
<div class="lpc-hero__inner">
<h1 class="lpc-hero__title">Travel Seoul without rip-offs.<br>A real local friend on chat.</h1>
<p class="lpc-hero__subtitle">No AI chatbots. No guidebooks. Just a real Korean local answering your questions in real time &mdash; so you never overpay, never get lost, and always find the best spots.</p>
<div class="lpc-hero__ctas">
[lpc_cta_telegram text="Start on Telegram" class="lpc-btn--lg"]
<a href="/pricing/" class="lpc-btn lpc-btn--outline lpc-btn--lg">Buy a Pass</a>
</div>
</div>
</div>

<!-- HOW IT WORKS -->
<div class="lpc-section">
<h2 class="lpc-section__title">How It Works</h2>
<p class="lpc-section__subtitle">Three simple steps to stress-free travel</p>
<div class="lpc-steps">
<div class="lpc-steps__item">
<div class="lpc-steps__number">1</div>
<h3>Choose Your Pass</h3>
<p>Pick a 3, 5, or 7-day pass. One price, unlimited questions for your entire trip.</p>
</div>
<div class="lpc-steps__item">
<div class="lpc-steps__number">2</div>
<h3>Chat with a Local</h3>
<p>Message us anytime on Telegram or WhatsApp. A real person responds &mdash; not a bot.</p>
</div>
<div class="lpc-steps__item">
<div class="lpc-steps__number">3</div>
<h3>Travel with Confidence</h3>
<p>Get price checks, restaurant recs, directions, and help in real time.</p>
</div>
</div>
</div>

<!-- WHAT WE HELP WITH -->
<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">What We Help With</h2>
<div class="lpc-services">
<div class="lpc-services__item">
<div class="lpc-services__icon">&#128176;</div>
<h4>Price Checks &amp; Tourist-Trap Prevention</h4>
<p>&ldquo;Is this price fair?&rdquo; &mdash; We&rsquo;ll tell you instantly before you pay.</p>
</div>
<div class="lpc-services__item">
<div class="lpc-services__icon">&#127860;</div>
<h4>Restaurant Ordering &amp; Recommendations</h4>
<p>We&rsquo;ll help you order, translate menus, and find hidden local gems.</p>
</div>
<div class="lpc-services__item">
<div class="lpc-services__icon">&#128652;</div>
<h4>Directions &amp; Transportation</h4>
<p>Subway, taxi, bus &mdash; we&rsquo;ll guide you step by step, in real time.</p>
</div>
<div class="lpc-services__item">
<div class="lpc-services__icon">&#128197;</div>
<h4>Reservation Help</h4>
<p>Restaurants, cafes, experiences &mdash; we&rsquo;ll call ahead and book for you.</p>
</div>
<div class="lpc-services__item">
<div class="lpc-services__icon">&#127973;</div>
<h4>Emergency Support</h4>
<p>Lost? Confused? Medical issue? We&rsquo;ve got your back with real-time guidance.</p>
</div>
<div class="lpc-services__item">
<div class="lpc-services__icon">&#127759;</div>
<h4>Cultural Tips</h4>
<p>Etiquette, tipping, customs &mdash; navigate Korean culture with confidence.</p>
</div>
</div>
</div>

<!-- WHY NOT AI -->
<div class="lpc-section">
<h2 class="lpc-section__title">Why a Real Person Instead of AI?</h2>
<div class="lpc-why-human">
<div class="lpc-why-human__comparison">
<div class="lpc-why-human__col lpc-why-human__col--ai">
<h4>AI gives you information</h4>
<ul>
<li>Generic answers from a database</li>
<li>Can&rsquo;t verify prices in real time</li>
<li>Doesn&rsquo;t know which places are actually good</li>
<li>Can&rsquo;t make phone calls for you</li>
</ul>
</div>
<div class="lpc-why-human__col lpc-why-human__col--human">
<h4>We solve real situations</h4>
<ul>
<li>&ldquo;Is this taxi driver overcharging me right now?&rdquo;</li>
<li>&ldquo;Can you explain my allergy to this chef?&rdquo;</li>
<li>&ldquo;Call this restaurant and check if they have space tonight&rdquo;</li>
<li>&ldquo;I think I left my bag on the subway &mdash; help!&rdquo;</li>
</ul>
</div>
</div>
</div>
</div>

<!-- EXCLUSIVE BENEFITS -->
<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">Exclusive Partner Benefits</h2>
<p class="lpc-section__subtitle">When you book through our Byeolgram Road partners, you unlock service extras not available to regular customers.</p>
<div class="lpc-benefits">
<div class="lpc-benefits__item">
<strong>Free welcome drink</strong> at partner restaurants
</div>
<div class="lpc-benefits__item">
<strong>Late checkout</strong> at partner stays (when available)
</div>
<div class="lpc-benefits__item">
<strong>Room upgrades</strong> at partner hotels (when available)
</div>
<div class="lpc-benefits__item">
<strong>Bonus appetizer or side dish</strong> at select restaurants
</div>
<div class="lpc-benefits__item">
<strong>Priority seating</strong> during busy hours
</div>
</div>
<p style="text-align:center;margin-top:24px;"><a href="/byeolgram-road/" class="lpc-btn lpc-btn--outline">Explore Byeolgram Road</a></p>
</div>

<!-- TESTIMONIALS -->
<div class="lpc-section">
<h2 class="lpc-section__title">What Travelers Say</h2>
<div class="lpc-testimonials">
<div class="lpc-testimonials__card">
<p class="lpc-testimonials__text">&ldquo;I almost paid 3x for a taxi from the airport. My LoversPick guide told me the real price and how to use the metro instead. Saved me $40 in the first hour.&rdquo;</p>
<div class="lpc-testimonials__author">&mdash; Sarah K., USA</div>
</div>
<div class="lpc-testimonials__card">
<p class="lpc-testimonials__text">&ldquo;They called ahead to a restaurant for me, got us a great table, and even told the chef about my shellfish allergy in Korean. I felt so safe.&rdquo;</p>
<div class="lpc-testimonials__author">&mdash; Yuki T., Japan</div>
</div>
<div class="lpc-testimonials__card">
<p class="lpc-testimonials__text">&ldquo;Best money I spent on my trip. It&rsquo;s like having a Korean friend who&rsquo;s always available. Way better than any travel app.&rdquo;</p>
<div class="lpc-testimonials__author">&mdash; Tom M., UK</div>
</div>
</div>
</div>

<!-- FAQ PREVIEW -->
<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">Frequently Asked Questions</h2>
<div class="lpc-faq-preview">
<details class="lpc-faq__item">
<summary class="lpc-faq__q">Is this a real person or AI?</summary>
<p class="lpc-faq__a">100% real. Every response comes from a local Korean who lives in Seoul. We never use AI chatbots for your conversations.</p>
</details>
<details class="lpc-faq__item">
<summary class="lpc-faq__q">How fast do you respond?</summary>
<p class="lpc-faq__a">Typically within 5&ndash;15 minutes during support hours (9AM&ndash;11PM KST). Premium members get priority response under 5 minutes.</p>
</details>
<details class="lpc-faq__item">
<summary class="lpc-faq__q">Can I use one pass for my whole travel group?</summary>
<p class="lpc-faq__a">Yes! One pass covers your entire group. No need to buy separate passes.</p>
</details>
</div>
<p style="text-align:center;margin-top:24px;"><a href="/faq/" class="lpc-btn lpc-btn--outline lpc-btn--sm">See All FAQs</a></p>
</div>

<!-- LEAD CAPTURE -->
<div class="lpc-section">
<h2 class="lpc-section__title">Get Started</h2>
<p class="lpc-section__subtitle">Leave your details and we&rsquo;ll send you everything you need to chat with a local in Seoul.</p>
[lpc_lead_form heading="" description=""]
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       PRICING PAGE
       ════════════════════════════════════════════════════════ */

    private static function pricing_content() {
        return '<div class="lpc-section">
<h1 class="lpc-section__title">Choose Your Seoul Pass</h1>
<p class="lpc-section__subtitle">One price. Unlimited questions. Real human support for your entire trip.</p>

[lpc_pricing]

<div style="text-align:center;margin-top:32px;">
<p><strong>Not sure yet?</strong> Start a free chat on Telegram to ask us anything before you buy.</p>
[lpc_cta_telegram text="Chat with us first" class="lpc-btn--outline"]
</div>
</div>

<!-- LEAD FORM FALLBACK -->
<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">Or Sign Up to Get Started</h2>
<p class="lpc-section__subtitle">We&rsquo;ll send you the next steps by email.</p>
[lpc_lead_form style="compact"]
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       HOW IT WORKS PAGE
       ════════════════════════════════════════════════════════ */

    private static function how_it_works_content() {
        return '<div class="lpc-section">
<h1 class="lpc-section__title">How LoversPick Works</h1>
<p class="lpc-section__subtitle">Your personal Seoul travel concierge &mdash; available on Telegram or WhatsApp.</p>
</div>

<div class="lpc-section">
<div class="lpc-steps lpc-steps--detailed">

<div class="lpc-steps__item">
<div class="lpc-steps__number">1</div>
<h3>Get Your Pass</h3>
<p>Choose a 3, 5, or 7-day pass on our <a href="/pricing/">pricing page</a>. Add the Premium add-on if you want late-night support and priority responses. One pass covers your entire travel group.</p>
</div>

<div class="lpc-steps__item">
<div class="lpc-steps__number">2</div>
<h3>Connect on Telegram or WhatsApp</h3>
<p>After purchase, you&rsquo;ll receive a link to start chatting with your local guide. No app downloads needed beyond Telegram or WhatsApp &mdash; no special accounts, no setup.</p>
</div>

<div class="lpc-steps__item">
<div class="lpc-steps__number">3</div>
<h3>Ask Anything, Anytime</h3>
<p>Your local guide is available during support hours to help with anything. Just send a text, photo, or voice message.</p>
</div>

</div>
</div>

<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">Things You Can Ask</h2>
<div class="lpc-examples">
<div class="lpc-examples__item">&ldquo;Is &#8361;45,000 fair for this jacket in Myeongdong?&rdquo;</div>
<div class="lpc-examples__item">&ldquo;Can you recommend a good BBQ place near Hongdae?&rdquo;</div>
<div class="lpc-examples__item">&ldquo;How do I get from Gangnam to Bukchon Hanok Village?&rdquo;</div>
<div class="lpc-examples__item">&ldquo;Can you call this restaurant and make a reservation for 4?&rdquo;</div>
<div class="lpc-examples__item">&ldquo;I think the taxi driver is going the long way &mdash; is this route right?&rdquo;</div>
<div class="lpc-examples__item">&ldquo;Where&rsquo;s the nearest pharmacy? I need cold medicine.&rdquo;</div>
</div>
</div>

<div class="lpc-section">
<h2 class="lpc-section__title">Support Hours</h2>
<div class="lpc-hours">
<div class="lpc-hours__item">
<h4>Standard</h4>
<p>9:00 AM &ndash; 11:00 PM KST<br><small>Included with all passes</small></p>
</div>
<div class="lpc-hours__item">
<h4>Premium</h4>
<p>9:00 AM &ndash; 2:00 AM KST<br><small>+Priority response under 5 min</small></p>
</div>
</div>
<p style="text-align:center;margin-top:32px;"><a href="/pricing/" class="lpc-btn lpc-btn--primary lpc-btn--lg">View Pricing</a></p>
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       FAQ PAGE
       ════════════════════════════════════════════════════════ */

    private static function faq_content() {
        return '<div class="lpc-section">
<h1 class="lpc-section__title">Frequently Asked Questions</h1>

<div class="lpc-faq">

<h3 class="lpc-faq__category">About the Service</h3>

<details class="lpc-faq__item" open>
<summary class="lpc-faq__q">Is this a real person or AI?</summary>
<p class="lpc-faq__a">100% real. Every response comes from a local Korean who lives in Seoul. We never use AI chatbots for your conversations.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What languages do you support?</summary>
<p class="lpc-faq__a">English and Japanese. Our locals are fluent in at least one, and most speak both.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What hours are you available?</summary>
<p class="lpc-faq__a">Standard support: 9AM&ndash;11PM Korea Standard Time (KST). Premium add-on extends this to 2AM KST.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">How fast do you respond?</summary>
<p class="lpc-faq__a">Typically within 5&ndash;15 minutes during support hours. Premium members get priority response under 5 minutes.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What messenger apps do you use?</summary>
<p class="lpc-faq__a">Telegram and WhatsApp. Both are free to download and use. We recommend Telegram for the best experience.</p>
</details>

<h3 class="lpc-faq__category">Pricing &amp; Passes</h3>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">Can I use one pass for my whole travel group?</summary>
<p class="lpc-faq__a">Yes! One pass covers your entire group. No need to buy separate passes for each person.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What payment methods do you accept?</summary>
<p class="lpc-faq__a">Credit/debit cards (Visa, Mastercard, Amex) via our secure checkout. More payment options coming soon.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">Can I get a refund?</summary>
<p class="lpc-faq__a">If you haven&rsquo;t used the service yet, we offer a full refund within 24 hours of purchase. See our <a href="/terms/">Terms of Service</a> for details.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What&rsquo;s the Premium add-on?</summary>
<p class="lpc-faq__a">Premium extends your support hours to 2AM KST and guarantees priority responses under 5 minutes. Great for nightlife and late-night situations.</p>
</details>

<h3 class="lpc-faq__category">Byeolgram Road</h3>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What is Byeolgram Road?</summary>
<p class="lpc-faq__a">Our curated directory of Seoul stays and restaurants that offer exclusive service benefits to LoversPick members &mdash; like free welcome drinks, late checkout, and room upgrades. These are genuine extras, not price discounts.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">How do I access Byeolgram Road benefits?</summary>
<p class="lpc-faq__a">Simply mention &ldquo;LoversPick&rdquo; when booking or arriving at a partner location. Your chat guide can also help you arrange reservations with partner benefits included.</p>
</details>

<h3 class="lpc-faq__category">Safety &amp; Emergencies</h3>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">What if I need help with an emergency?</summary>
<p class="lpc-faq__a">We&rsquo;ll help you navigate medical situations, lost items, police stations, and embassy contacts. For life-threatening emergencies, always call <strong>119</strong> (fire/ambulance) or <strong>112</strong> (police) first.</p>
</details>

<details class="lpc-faq__item">
<summary class="lpc-faq__q">Is my personal information safe?</summary>
<p class="lpc-faq__a">Absolutely. We only collect what&rsquo;s needed to provide the service. See our <a href="/privacy-policy-loverspick/">Privacy Policy</a> for full details. We never sell your data.</p>
</details>

</div>

<div style="text-align:center;margin-top:40px;">
<p>Still have questions?</p>
[lpc_cta_telegram text="Ask us on Telegram" class="lpc-btn--lg"]
</div>
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       CONTACT PAGE
       ════════════════════════════════════════════════════════ */

    private static function contact_content() {
        return '<div class="lpc-section">
<h1 class="lpc-section__title">Contact &amp; Support</h1>
<p class="lpc-section__subtitle">We&rsquo;re here to help. Reach out through any of these channels.</p>

<div class="lpc-contact-grid">

<div class="lpc-contact-card">
<h3>Chat with Us</h3>
<p>The fastest way to reach us. Available during support hours.</p>
<div class="lpc-contact-card__actions">
[lpc_cta_telegram text="Open Telegram"]
[lpc_cta_whatsapp text="Open WhatsApp"]
</div>
</div>

<div class="lpc-contact-card">
<h3>Email</h3>
<p>For account issues, partnership inquiries, or anything that&rsquo;s not urgent.</p>
<p><strong>hello@loverspick.com</strong></p>
<p><small>We reply within 24 hours</small></p>
</div>

<div class="lpc-contact-card">
<h3>Support Hours</h3>
<p><strong>Standard:</strong> 9AM&ndash;11PM KST daily</p>
<p><strong>Premium:</strong> 9AM&ndash;2AM KST daily</p>
<p><small>Korea Standard Time (UTC+9)</small></p>
</div>

</div>
</div>

<div class="lpc-section lpc-section--alt">
<h2 class="lpc-section__title">Send Us a Message</h2>
[lpc_lead_form heading="" description=""]
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       TERMS PAGE
       ════════════════════════════════════════════════════════ */

    private static function terms_content() {
        return '<div class="lpc-section lpc-legal">
<h1>Terms of Service</h1>
<p><em>Last updated: ' . gmdate( 'F j, Y' ) . '</em></p>

<h2>1. Service Description</h2>
<p>LoversPick provides real-time travel assistance for visitors to Seoul, South Korea, through messaging platforms (Telegram, WhatsApp). Our service connects you with local Korean guides who provide information, recommendations, and assistance during your trip.</p>

<h2>2. Passes &amp; Payment</h2>
<p>Passes are valid for the stated duration (3, 5, or 7 days) starting from the first use. Passes cover unlimited messaging during support hours for the pass holder and their immediate travel group.</p>

<h2>3. Refund Policy</h2>
<p>Full refunds are available within 24 hours of purchase if the service has not been used. Once a chat session has begun, the pass is considered used and is non-refundable.</p>

<h2>4. Service Limitations</h2>
<ul>
<li>Support is available during stated hours only (Standard: 9AM-11PM KST; Premium: 9AM-2AM KST)</li>
<li>We provide guidance and information, not professional services (medical, legal, financial)</li>
<li>Response times are estimates, not guarantees</li>
<li>For life-threatening emergencies, contact Korean emergency services directly (119 or 112)</li>
</ul>

<h2>5. Byeolgram Road Partners</h2>
<p>Partner benefits are provided at the discretion of each partner business. Availability of specific benefits (room upgrades, late checkout, etc.) depends on the partner&rsquo;s capacity at the time of visit. LoversPick does not guarantee the availability of specific benefits.</p>

<h2>6. User Conduct</h2>
<p>Users agree to communicate respectfully with our guides. We reserve the right to terminate service without refund for abusive, threatening, or inappropriate behavior.</p>

<h2>7. Liability</h2>
<p>LoversPick provides information and guidance to the best of our ability. We are not liable for decisions made based on our recommendations, including but not limited to financial losses, injuries, or missed reservations. Use of our service is at your own risk.</p>

<h2>8. Changes to Terms</h2>
<p>We may update these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>

<h2>9. Contact</h2>
<p>For questions about these terms, contact us at <strong>hello@loverspick.com</strong>.</p>
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       PRIVACY PAGE
       ════════════════════════════════════════════════════════ */

    private static function privacy_content() {
        return '<div class="lpc-section lpc-legal">
<h1>Privacy Policy</h1>
<p><em>Last updated: ' . gmdate( 'F j, Y' ) . '</em></p>

<h2>1. Information We Collect</h2>
<p>We collect information you provide directly:</p>
<ul>
<li><strong>Contact information:</strong> Email address, messenger handle, name (optional)</li>
<li><strong>Trip details:</strong> Travel dates, country of origin (optional)</li>
<li><strong>Chat messages:</strong> Conversations with our local guides during your service period</li>
<li><strong>Payment information:</strong> Processed securely by our payment provider; we do not store card details</li>
</ul>

<h2>2. How We Use Your Information</h2>
<ul>
<li>To provide the travel assistance service you purchased</li>
<li>To send service-related communications (pass activation, support follow-ups)</li>
<li>To send marketing communications (only with your explicit consent; you can unsubscribe anytime)</li>
<li>To improve our service quality</li>
</ul>

<h2>3. Data Sharing</h2>
<p>We do not sell your personal data. We may share limited information with:</p>
<ul>
<li><strong>Payment processors:</strong> To process your purchase</li>
<li><strong>Byeolgram Road partners:</strong> Only your name, when making reservations on your behalf and with your permission</li>
</ul>

<h2>4. Data Retention</h2>
<p>We retain your information for as long as needed to provide the service and for legitimate business purposes (up to 2 years). Chat messages are retained for 90 days after your pass expires.</p>

<h2>5. Your Rights</h2>
<p>You have the right to:</p>
<ul>
<li>Access the personal data we hold about you</li>
<li>Request correction or deletion of your data</li>
<li>Withdraw consent for marketing communications at any time</li>
<li>Request a copy of your data in a portable format</li>
</ul>

<h2>6. Cookies &amp; Analytics</h2>
<p>Our website uses analytics tools (Google Analytics, Meta Pixel) to understand site usage. These tools may use cookies. You can control cookies through your browser settings.</p>

<h2>7. Security</h2>
<p>We use industry-standard security measures to protect your information, including encrypted connections (HTTPS) and secure data storage.</p>

<h2>8. Contact</h2>
<p>For privacy-related inquiries, contact us at <strong>hello@loverspick.com</strong>.</p>
</div>';
    }

    /* ════════════════════════════════════════════════════════════
       BYEOLGRAM ROAD PAGE
       ════════════════════════════════════════════════════════ */

    private static function byeolgram_content() {
        return '<div class="lpc-section">
<div class="lpc-byeolgram-hero">
<h1 class="lpc-section__title">Byeolgram Road</h1>
<p class="lpc-byeolgram-hero__kr">&#48324;&#44536;&#47016;&#47196;&#46300;</p>
<p class="lpc-section__subtitle">Curated Seoul stays &amp; restaurants with exclusive service benefits for LoversPick members. No price discounts &mdash; just better experiences.</p>
</div>
</div>

<div class="lpc-section lpc-section--alt">
<div class="lpc-byeolgram-benefits">
<h3>What &ldquo;Exclusive Benefits&rdquo; Means</h3>
<p>Our partners offer genuine service extras &mdash; not price markdowns. When you visit a Byeolgram Road partner, you may receive:</p>
<ul>
<li><strong>Free welcome drink</strong> at restaurants &amp; cafes</li>
<li><strong>Bonus appetizer or side dish</strong> at select locations</li>
<li><strong>Late checkout</strong> at partner stays (when available)</li>
<li><strong>Room upgrade</strong> at partner hotels (when available)</li>
<li><strong>Priority seating</strong> during busy hours</li>
<li><strong>Bonus add-on treatment</strong> at partner spas</li>
</ul>
<p><small>Benefits vary by partner and availability. Mention &ldquo;LoversPick&rdquo; when booking or arriving.</small></p>
</div>
</div>

<div class="lpc-section">
<h2 class="lpc-section__title">Our Partners</h2>
[lpc_partners]
</div>

<div class="lpc-section lpc-section--alt">
[lpc_partner_apply_form]
</div>';
    }
}
