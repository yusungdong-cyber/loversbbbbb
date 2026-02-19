# CLAUDE.md

This file provides guidance for AI assistants working in this repository.

## Project Overview

- **Repository**: yusungdong-cyber/loversbbbbb
- **Project Name**: LP AI Match (lp-ai-match)
- **Type**: WordPress custom plugin
- **Target**: Japanese women — AI-powered romance matching web service
- **Positioning**: "韓国式AI運命マッチング" (Korean-style saju + AI compatibility analysis)
- **Purpose**: MVP for rapid revenue experimentation (avoid unnecessary complexity)
- **Language**: PHP (WordPress plugin), JavaScript (frontend interactions), CSS

## Architecture

This is a **WordPress plugin** (`lp-ai-match`). All logic lives in the plugin — never in the theme. The plugin must continue to work regardless of theme changes.

### Directory Structure

```
/
├── CLAUDE.md                          # AI assistant guidance (this file)
├── index.html                         # Standalone HTML demo page (no WP needed)
└── lp-ai-match/                       # WordPress plugin root
    ├── lp-ai-match.php                # Main plugin file (bootstrap)
    ├── uninstall.php                  # Cleanup on uninstall
    ├── includes/                      # Core PHP classes
    │   ├── class-lp-activator.php     # Activation hooks (DB tables, defaults)
    │   ├── class-lp-deactivator.php   # Deactivation hooks
    │   ├── class-lp-post-types.php    # CPT registration (4 types)
    │   ├── class-lp-matching.php      # Matching algorithm + personality questions
    │   ├── class-lp-saju.php          # Saju (四柱) calculation engine
    │   ├── class-lp-horoscope.php     # Zodiac sign calculation & horoscope
    │   ├── class-lp-tarot.php         # Tarot card logic (22 Major Arcana)
    │   ├── class-lp-report.php        # AI report generation (OpenAI integration)
    │   ├── class-lp-pdf.php           # HTML→PDF conversion (DomPDF with HTML fallback)
    │   ├── class-lp-chat.php          # Internal chat system with cron expiry
    │   ├── class-lp-stripe.php        # Stripe payment (direct API, no SDK)
    │   └── class-lp-rest-api.php      # REST API endpoints
    ├── admin/                         # WP admin pages
    │   ├── class-lp-admin.php         # Admin menu, settings registration
    │   ├── css/admin.css              # Admin styles
    │   ├── js/admin.js                # Admin scripts (block confirm dialog)
    │   └── views/                     # Admin page templates
    │       ├── dashboard.php          # Stats + recent matches
    │       ├── settings.php           # Pricing, weights, Stripe, OpenAI config
    │       ├── tarot.php              # Tarot card interpretation editor
    │       ├── horoscope.php          # Horoscope fortune text editor
    │       ├── match-logs.php         # Match history viewer
    │       ├── users.php              # User management + block/unblock
    │       └── chat-reports.php       # Chat report viewer
    └── public/                        # Frontend
        ├── class-lp-public.php        # Frontend controller + shortcode registration
        ├── css/public.css             # Frontend styles (~785 lines, pink/purple theme)
        ├── js/public.js               # Frontend interactions (~347 lines, jQuery)
        └── views/                     # Frontend templates (shortcodes)
            ├── horoscope.php          # DOB form + result display
            ├── tarot.php              # 3-card draw UI
            ├── profile-form.php       # 4-step profile creation form
            ├── matching.php           # User's match list
            ├── matching-result.php    # Report display + payment landing
            ├── chat.php               # Chat interface with AJAX polling
            └── pricing.php            # 3-tier pricing cards
```

### Missing Directories (Planned but Not Yet Created)

These are referenced in code but do not exist yet:

- `lp-ai-match/assets/images/` — Tarot card images (filenames like `fool.png` referenced in `LP_Tarot` but absent; JS uses `card.name.charAt(0)` as placeholder)
- `lp-ai-match/languages/` — i18n `.pot`/`.po`/`.mo` files (all `__()` calls fall back to hardcoded Japanese strings)
- `lp-ai-match/vendor/` — Composer autoload / DomPDF (PDF generation falls back to HTML file download)

### Standalone Demo Page

`index.html` at the repo root is a self-contained single-page HTML demo with all CSS and JS inline. It demonstrates all frontend features (horoscope, tarot, profile form, pricing, report, chat) without requiring WordPress. Useful for design previews.

## Plugin Bootstrap Flow

1. `lp-ai-match.php` defines constants (`LP_AI_MATCH_VERSION` = 1.0.0, `LP_AI_MATCH_PLUGIN_DIR`, `LP_AI_MATCH_PLUGIN_URL`, `LP_AI_MATCH_PLUGIN_BASENAME`)
2. Requires all 12 include files, admin class, and public class
3. Registers activation hook → `LP_Activator::activate()` (creates DB tables, sets default options)
4. Registers deactivation hook → `LP_Deactivator::deactivate()` (clears cron, flushes rewrite rules)
5. On `plugins_loaded`, initializes: `LP_Post_Types::init()`, `LP_Rest_API::init()`, `LP_Chat::init()`, `LP_Stripe::init()`, `LP_Admin::init()` (admin only), `LP_Public::init()`
6. Loads text domain `lp-ai-match` from `/languages`

## Custom Post Types

All 4 CPTs are registered as `public => false`, `show_ui => true`, `show_in_menu => false`, `rewrite => false`:

| CPT Slug | Japanese Label | `supports` | Status |
|-----------|---------------|------------|--------|
| `lp_profile` | プロフィール | title, author | Active — stores user dating profiles |
| `lp_match` | マッチング | title, author | Active — stores match records between profiles |
| `lp_report` | 相性レポート | title, author, editor | Active — stores AI compatibility reports |
| `lp_reading` | 占い結果 | title, author, editor | **Registered but unused** — no code writes to or reads from this CPT |

## Custom Database Tables

Created on plugin activation via `dbDelta()`:

### `{prefix}lp_chat_messages`

| Column | Type | Notes |
|--------|------|-------|
| `id` | BIGINT PK AUTO_INCREMENT | |
| `match_id` | BIGINT (indexed) | FK to `lp_match` post |
| `sender_id` | BIGINT (indexed) | WP user ID |
| `receiver_id` | BIGINT (indexed) | WP user ID |
| `message` | TEXT | Chat message content |
| `is_read` | TINYINT(1) DEFAULT 0 | Read status |
| `created_at` | DATETIME DEFAULT CURRENT_TIMESTAMP | |

### `{prefix}lp_matching_scores`

| Column | Type | Notes |
|--------|------|-------|
| `id` | BIGINT PK AUTO_INCREMENT | |
| `profile_a_id` | BIGINT (indexed) | FK to `lp_profile` post |
| `profile_b_id` | BIGINT (indexed) | FK to `lp_profile` post |
| `saju_score` | DECIMAL(5,2) | 0–100 |
| `personality_score` | DECIMAL(5,2) | 0–100 |
| `values_score` | DECIMAL(5,2) | 0–100 |
| `total_score` | DECIMAL(5,2) | Weighted composite |
| `calculated_at` | DATETIME DEFAULT CURRENT_TIMESTAMP | |
| | UNIQUE KEY | `(profile_a_id, profile_b_id)` |

## Post Meta Fields

### `lp_profile` meta

| Key | Type | Description |
|-----|------|-------------|
| `_lp_birthdate` | string (Y-m-d) | Date of birth |
| `_lp_gender` | string | Gender (male/female) |
| `_lp_nickname` | string | Display name |
| `_lp_personality_answers` | array | 8 personality question answers |
| `_lp_values` | array | Selected value keywords (max 3) |

### `lp_match` meta

| Key | Type | Description |
|-----|------|-------------|
| `_lp_profile_a` | int | Profile A post ID |
| `_lp_profile_b` | int | Profile B post ID |
| `_lp_tier` | string | basic/standard/premium |
| `_lp_report_id` | int | Associated report post ID |
| `_lp_chat_status` | string | active/expired/blocked |
| `_lp_chat_started` | string | Chat activation timestamp |
| `_lp_contact_consents` | array | User IDs who consented to share contact |

### `lp_report` meta

| Key | Type | Description |
|-----|------|-------------|
| `_lp_match_id` | int | Associated match post ID |
| `_lp_profile_a` | int | Profile A post ID |
| `_lp_profile_b` | int | Profile B post ID |
| `_lp_scores` | array | Score breakdown (saju, personality, values, total) |
| `_lp_tier` | string | basic/standard/premium |
| `_lp_generated_at` | string | Report generation timestamp |
| `_lp_pdf_path` | string | Path to generated PDF/HTML file |

### User meta

| Key | Type | Description |
|-----|------|-------------|
| `_lp_blocked` | bool | Whether user is blocked by admin |
| `_lp_last_payment` | array | Last Stripe payment details |
| `_lp_stripe_session_*` | string | Stripe checkout session references |

## WordPress Options

All prefixed with `lp_ai_match_`. Set on activation with defaults:

| Option | Default | Purpose |
|--------|---------|---------|
| `lp_ai_match_price_basic` | 1000 | Basic tier price (JPY) |
| `lp_ai_match_price_standard` | 3000 | Standard tier price (JPY) |
| `lp_ai_match_price_premium` | 6900 | Premium tier price (JPY) |
| `lp_ai_match_currency` | jpy | Currency code |
| `lp_ai_match_stripe_mode` | test | test or live |
| `lp_ai_match_stripe_test_publishable_key` | (empty) | Stripe test publishable key |
| `lp_ai_match_stripe_test_secret_key` | (empty) | Stripe test secret key |
| `lp_ai_match_stripe_live_publishable_key` | (empty) | Stripe live publishable key |
| `lp_ai_match_stripe_live_secret_key` | (empty) | Stripe live secret key |
| `lp_ai_match_stripe_webhook_secret` | (empty) | Stripe webhook signing secret |
| `lp_ai_match_openai_api_key` | (empty) | OpenAI API key for AI reports |
| `lp_ai_match_weight_saju` | 40 | Saju score weight (%) |
| `lp_ai_match_weight_personality` | 40 | Personality score weight (%) |
| `lp_ai_match_weight_values` | 20 | Values score weight (%) |
| `lp_ai_match_chat_duration_basic` | 24 | Basic chat duration (hours) |
| `lp_ai_match_chat_duration_standard` | 7 | Standard chat duration (days) |
| `lp_ai_match_chat_duration_premium` | 7 | Premium chat duration (days) |
| `lp_ai_match_matches_basic` | 1 | Basic match count |
| `lp_ai_match_matches_standard` | 3 | Standard match count |
| `lp_ai_match_matches_premium` | 5 | Premium match count |
| `lp_ai_match_horoscope_{sign}` | (per sign) | Admin-overridden fortune texts |
| `lp_ai_match_tarot_cards` | (per card) | Admin-overridden tarot interpretations |
| `lp_ai_match_chat_reports` | array | Chat report log |

## Core Features

### Free Features (Lead Generation)
1. **Horoscope** — DOB input → zodiac sign calculation (12 signs, handles Capricorn year-boundary) → deterministic daily fortune (based on day-of-year) → luck score (via crc32) → lucky color/time → CTA to paid matching
2. **Tarot 3-card draw** — 22 Major Arcana cards → random 3 drawn → 50% reversed chance → past/present/future positions → upright/reverse love interpretations → admin-editable

### Paid Products (Admin-configurable pricing)
| Tier | Default Price | Matches | Report Depth | Chat Duration |
|------|--------------|---------|--------------|---------------|
| Basic | ¥1,000 | 1 | Simple AI report | 24 hours |
| Standard | ¥3,000 | 3 | Detailed AI report + conflict/timing | 7 days |
| Premium | ¥6,900 | 5 | Full report + marriage luck graph | 7 days |

### Matching Algorithm
```
Final Score = (Saju score × weight_saju%) + (Personality score × weight_personality%) + (Values score × weight_values%)
```

- **Saju score** (`LP_Saju`): Computes Year/Month/Day pillars (3 pillars, no Hour pillar) from Heavenly Stems / Earthly Branches. Maps stems/branches to 5 elements (木火土金水). Uses 25-entry compatibility matrix to average across pillar pairs. Score 0–100.
- **Personality score** (`LP_Matching`): 8 questions with 4 options each. "Complement" questions score higher when different (85 vs 60); "match" questions score higher when same (90 vs 55). Final = average across questions.
- **Values score** (`LP_Matching`): Jaccard similarity of selected value keywords × 100. 12 available keywords (family, career, adventure, stability, creativity, health, education, kindness, humor, honesty, ambition, loyalty).

Matching finds opposite-gender profiles, scores all candidates, sorts descending, returns top N per tier.

### AI Report Structure
Content sections vary by tier:

| Section | Basic | Standard | Premium |
|---------|-------|----------|---------|
| Total compatibility score | Yes | Yes | Yes |
| Saju analysis | Yes | Yes | Yes |
| Personality complement | Yes | Yes | Yes |
| Conflict potential | — | Yes | Yes |
| Relationship timing | — | Yes | Yes |
| Marriage luck yearly graph | — | — | Yes |
| Values analysis | Yes | Yes | Yes |

Reports use score-based branching for text (5 tiers from "運命の相手！" at ≥90 down to lowest). When OpenAI API key is configured (standard/premium tiers), calls `gpt-4o-mini` for additional AI-generated narrative.

### PDF Generation
- `LP_PDF` tries DomPDF first (checks `vendor/autoload.php`)
- **Current state**: Falls back to saving as HTML file since `vendor/` directory doesn't exist
- Saves to `{wp_upload_dir}/lp-reports/` with `.htaccess` protection
- Embedded CSS with Japanese fonts, pink/purple color scheme

### Internal Chat
- 1:1 messaging via AJAX polling (5-second interval in `public.js`)
- Auto-expiry via WP-Cron (hourly check, `lp_chat_expiry_check`)
- Duration: 24h (basic) or 7 days (standard/premium), configurable
- Mutual contact sharing consent system
- Chat reporting with reason field
- Admin can block/unblock users (user meta `_lp_blocked`)
- Messages stored in custom `lp_chat_messages` table (limit 100 per fetch)

### Payment (Stripe)
- Direct Stripe API calls via `wp_remote_post()` (no PHP SDK)
- Creates Stripe Checkout sessions with `payment_method_types[]=card`
- Webhook handler with HMAC-SHA256 signature verification (5-minute timestamp tolerance)
- On `checkout.session.completed`: find matches → create `lp_match` posts → generate reports → activate chats
- Success URL: `/matching-result/`, Cancel URL: `/matching/`

## REST API

Namespace: `/wp-json/lp-ai-match/v1/`

| Method | Endpoint | Auth | Handler |
|--------|----------|------|---------|
| POST | `/horoscope` | Public | `LP_Horoscope::generate_daily_horoscope()` — requires `birthdate` param (Y-m-d) |
| POST | `/tarot/draw` | Public | `LP_Tarot::draw_three_cards()` |
| POST | `/profile` | Logged in | Create/update user profile post + meta |
| GET | `/profile` | Logged in | Return current user's profile data |
| POST | `/match/request` | Logged in | Validate profile, create Stripe checkout session |
| GET | `/match/(?P<id>\d+)` | Logged in | Match details (ownership verified) |
| GET | `/report/(?P<id>\d+)` | Logged in | Report content + scores (ownership verified) |
| GET | `/report/(?P<id>\d+)/pdf` | Logged in | PDF/HTML download (ownership verified) |
| POST | `/chat/send` | Logged in | Send chat message |
| GET | `/chat/(?P<match_id>\d+)` | Logged in | Get messages (polling, after_id param) + chat_active status |
| POST | `/chat/report` | Logged in | Report a chat message with reason |
| POST | `/chat/consent-contact` | Logged in | Record contact sharing consent |
| POST | `/payment/create-session` | Logged in | Create Stripe checkout session |
| POST | `/payment/webhook` | Public | Stripe webhook (signature-verified), registered by `LP_Stripe` |

Auth check: `check_logged_in()` returns `WP_Error` with 401 status if not authenticated.

## Shortcodes

Registered by `LP_Public`:

| Shortcode | Template | Auth Required | Purpose |
|-----------|----------|---------------|---------|
| `[lp_horoscope]` | `public/views/horoscope.php` | No | Horoscope fortune form + results |
| `[lp_tarot]` | `public/views/tarot.php` | No | Tarot 3-card draw |
| `[lp_profile_form]` | `public/views/profile-form.php` | Yes | 4-step profile creation |
| `[lp_matching]` | `public/views/matching.php` | Yes | User's match list |
| `[lp_matching_result]` | `public/views/matching-result.php` | Yes | Report display (reads `report_id` from `$_GET`) |
| `[lp_chat]` | `public/views/chat.php` | Yes | Chat interface (reads `match_id` from `$_GET`) |
| `[lp_pricing]` | `public/views/pricing.php` | No | 3-tier pricing cards |

## WordPress Hooks

| Hook | Class | Method | Type |
|------|-------|--------|------|
| `plugins_loaded` | (main file) | `lp_ai_match_init()` | action |
| `init` | LP_Post_Types | `register_post_types()` | action |
| `rest_api_init` | LP_Rest_API | `register_routes()` | action |
| `rest_api_init` | LP_Stripe | `register_webhook_route()` | action |
| `lp_chat_expiry_check` | LP_Chat | `check_expired_chats()` | cron action (hourly) |
| `admin_menu` | LP_Admin | `add_menu_pages()` | action |
| `admin_init` | LP_Admin | `register_settings()` | action |
| `admin_enqueue_scripts` | LP_Admin | `enqueue_assets()` | action |
| `wp_enqueue_scripts` | LP_Public | `enqueue_assets()` | action |
| Activation hook | LP_Activator | `activate()` | register_activation_hook |
| Deactivation hook | LP_Deactivator | `deactivate()` | register_deactivation_hook |

## Admin Features

WordPress admin menu under "LP AI Match" (dashicons-heart, position 30, `manage_options` capability):

| Page | Slug | Purpose |
|------|------|---------|
| ダッシュボード | `lp-ai-match` | Profile/match/report counts + last 10 matches table |
| 設定 | `lp-ai-match-settings` | All plugin settings (pricing, weights, match counts, chat duration, Stripe keys, OpenAI key) |
| タロットカード | `lp-ai-match-tarot` | Edit upright/reverse interpretations for each of 22 cards |
| 星座運勢 | `lp-ai-match-horoscope` | Edit 3 fortune texts per zodiac sign |
| マッチングログ | `lp-ai-match-logs` | Last 50 matches with profiles, tier, chat status, report link |
| ユーザー管理 | `lp-ai-match-users` | All profiles with block/unblock actions |
| 通報管理 | `lp-ai-match-reports` | Chat report log (message ID, reporter, reason, date) |

## Frontend JavaScript Architecture

`public/js/public.js` is a jQuery IIFE that uses the `lpAiMatch` global (from `wp_localize_script`):

```javascript
window.lpAiMatch = {
    apiUrl: '/wp-json/lp-ai-match/v1',
    nonce: '...',        // wp_rest nonce
    stripeKey: '...',    // Stripe publishable key
    i18n: { loading, error, send, chatExpired, reportSent }
}
```

Key behaviors:
- **Horoscope**: POST to `/horoscope`, populate result DOM with fadeIn
- **Tarot**: POST to `/tarot/draw`, build card HTML with `escHtml()` utility
- **Profile Form**: Multi-step navigation (4 steps), POST to `/profile`, redirect to `/matching/`
- **Pricing**: POST to `/payment/create-session`, redirect to Stripe checkout URL
- **Chat**: 5-second polling interval, GET `/chat/{matchId}?after={lastId}`, mine/theirs bubble classes, auto-scroll, report modal

## UI/Design Guidelines

- **Target**: Japanese women
- **Style**: Minimal, cute but premium feel
- **Avoid**: Random chat vibes, occult/spiritual aesthetics
- **Tone**: Entertainment → seriousness (natural transition)
- **Colors**: Soft pastels (pink/purple palette), clean whites, subtle gradients — CSS custom properties defined in `public.css`
- **Typography**: Clean Japanese web fonts
- **CSS class pattern**: BEM-like `.lp-component__element--modifier`
- **Responsive**: Mobile-first with breakpoint at 600px

## Security Requirements

- WordPress nonce verification on all admin forms and REST API calls
- REST API auth via `check_logged_in()` returning `WP_Error(401)` for protected endpoints
- Input sanitization: `sanitize_text_field()`, `absint()`, `sanitize_email()`
- Output escaping: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- Prepared statements: `$wpdb->prepare()` for all custom SQL queries
- Stripe webhook HMAC-SHA256 signature verification with timestamp tolerance
- PDF directory protected with `.htaccess` deny all
- Age verification checkbox in profile form
- Chat reporting and admin user blocking
- JS uses `escHtml()` utility for DOM text escaping

## Code Conventions

### PHP
- WordPress coding standards (WPCS)
- Class-based architecture, one class per file, all static methods
- Class prefix: `LP_` (e.g., `LP_Saju`, `LP_Matching`)
- PHPDoc comments on public methods
- `$wpdb->prepare()` for all database queries
- Escape all output (`esc_html()`, `esc_attr()`, `esc_url()`)
- Sanitize all input (`sanitize_text_field()`, `absint()`, etc.)

### JavaScript
- jQuery (WordPress-bundled) — `jQuery(document).ready()` IIFE pattern
- Use `wp_localize_script()` for passing data to JS (global `lpAiMatch`)
- REST API calls via `jQuery.ajax()` with `X-WP-Nonce` header
- No additional JS frameworks

### CSS
- BEM-like naming: `.lp-component__element--modifier`
- CSS custom properties for theming (colors, fonts)
- Mobile-first responsive design (breakpoint: 600px)
- No CSS frameworks — custom styles only
- All styles scoped to `.lp-*` prefix

### Git Workflow
- Feature branches for new work
- Clear, descriptive commit messages
- Atomic commits

## Key Dependencies

| Dependency | Status | Notes |
|-----------|--------|-------|
| WordPress 6.0+ | Required | Core platform |
| PHP 7.4+ | Required | Server runtime |
| jQuery | Required | WordPress-bundled, used for frontend JS |
| DomPDF | **Not installed** | `vendor/` directory missing; PDF falls back to HTML download |
| OpenAI API | Optional | For AI-enhanced report narrative (`gpt-4o-mini`); score-based text works without it |
| Stripe API | Required for payments | Called directly via `wp_remote_post()`, no PHP SDK needed |

## Known Gaps and TODO Items

1. **No `vendor/` directory** — DomPDF library not installed. PDF generation falls back to HTML file download. Need to run Composer or bundle the library.
2. **No tarot card images** — `assets/images/` directory missing. Tarot card `image` filenames (e.g., `fool.png`) referenced in `LP_Tarot` but files don't exist. JS uses first character of card name as fallback.
3. **No i18n files** — `languages/` directory missing. All `__()` and `_e()` calls fall through to their literal Japanese strings. Functional but not translatable.
4. **No `.gitignore`** — Should be added to exclude `vendor/`, `node_modules/`, `.env`, etc.
5. **No `composer.json`** — No dependency management file for DomPDF or potential Stripe SDK.
6. **No tests** — No PHPUnit, Jest, or any testing framework.
7. **`lp_reading` CPT unused** — Registered in `LP_Post_Types` but no code creates or queries `lp_reading` posts. Could be used for storing horoscope/tarot reading history.
8. **Hardcoded page URLs** — `/profile/`, `/matching/`, `/matching-result/`, `/chat/` are hardcoded in JS redirects and view templates instead of using WordPress `get_permalink()` or options.
9. **Payment methods limited** — Only `card` payment method type. Apple Pay and PayPay mentioned as goals but not implemented.
10. **No face image upload** — Matching algorithm description mentions "optional face image" but no upload handling exists.

## Uninstall Behavior

`uninstall.php` performs complete cleanup:
- Deletes all posts of types: `lp_profile`, `lp_match`, `lp_report`, `lp_reading`
- Drops tables: `{prefix}lp_chat_messages`, `{prefix}lp_matching_scores`
- Deletes all options matching `lp_ai_match_%`

## Notes for AI Assistants

- This is an **MVP** — avoid unnecessary complexity. Build for extensibility but ship lean.
- All logic must be in the `lp-ai-match` plugin — never in themes.
- Always read existing files before modifying them.
- Keep this CLAUDE.md file up to date as the project evolves.
- All user-facing text must be in **Japanese** (日本語).
- Use WordPress i18n functions (`__()`, `_e()`) for all strings.
- Security is non-negotiable — every input sanitized, every output escaped, every DB query prepared.
- Prefer WordPress APIs over custom solutions (WP_Query, WP REST API, Settings API, etc.).
- All classes use static methods — no instantiation. Call via `LP_ClassName::method()`.
- The plugin has no build step — edit PHP/JS/CSS files directly.
- The standalone `index.html` demo is independent of the WordPress plugin and should be kept in sync with UI changes when practical.

---

# LoversPick Core (`loverspick-core`)

## Overview

- **Plugin Name**: LoversPick Core
- **Purpose**: Seoul anti-ripoff travel concierge — landing pages, pricing, lead capture, and Byeolgram Road partner directory
- **Target**: International travelers visiting Seoul (English-primary)
- **Model**: Real human chat support via Telegram/WhatsApp (NOT AI)
- **Brand Rule**: **NO discount language** (no "10% off / discount / sale"). Use "Exclusive Benefits / Free Extras / Upgrades / Bonus items" instead.

## Directory Structure

```
loverspick-core/
├── loverspick-core.php            # Main plugin file (bootstrap + analytics + assets)
├── uninstall.php                  # Cleanup on uninstall
├── includes/
│   ├── class-lpc-activator.php    # Activation (DB tables, defaults, page creation)
│   ├── class-lpc-partners.php     # Partners CPT + taxonomies + meta boxes
│   ├── class-lpc-settings.php     # Admin settings page (tabbed)
│   ├── class-lpc-shortcodes.php   # All frontend shortcodes
│   ├── class-lpc-leads.php        # Lead + partner application REST endpoints
│   └── class-lpc-pages.php        # Creates all pages with content on activation
├── admin/
│   ├── css/admin.css              # Admin styles
│   └── views/
│       ├── settings.php           # Settings page template (3 tabs)
│       ├── leads.php              # Leads viewer
│       └── partner-apps.php       # Partner applications viewer
└── public/
    ├── css/loverspick.css         # Frontend styles (~900 lines, mobile-first)
    └── js/loverspick.js           # Frontend JS (forms, filters, UTM capture)
```

## Pages Created on Activation

| Slug | Title | Key Content |
|------|-------|-------------|
| `loverspick-home` | LoversPick Seoul | Hero + How It Works + Services + Why Not AI + Benefits + Testimonials + FAQ preview + Lead form |
| `pricing` | Pricing & Purchase | `[lpc_pricing]` cards + lead form fallback |
| `how-it-works` | How It Works | Detailed 3-step process + example questions + support hours |
| `faq` | FAQ | Comprehensive FAQ using HTML `<details>` elements |
| `contact` | Contact & Support | Contact cards + lead form |
| `terms` | Terms of Service | Placeholder legal text |
| `privacy-policy-loverspick` | Privacy Policy | Placeholder legal text |
| `byeolgram-road` | Byeolgram Road | Partner directory `[lpc_partners]` + apply form `[lpc_partner_apply_form]` |

## Shortcodes

| Shortcode | Purpose |
|-----------|---------|
| `[lpc_pricing]` | Pricing cards (3/5/7-day + premium add-on) |
| `[lpc_lead_form]` | Lead capture form with UTM tracking (`style="compact"` variant) |
| `[lpc_partners]` | Partner directory with category/area filter tabs |
| `[lpc_partner_apply_form]` | Partner application form |
| `[lpc_cta_telegram]` | Telegram CTA button (text customizable via `text` attribute) |
| `[lpc_cta_whatsapp]` | WhatsApp CTA button |

## Custom Post Type: `lpc_partner`

Used for Byeolgram Road partner listings.

**Taxonomies:** `lpc_partner_cat` (Stay/Restaurant), `lpc_partner_area` (Hongdae/Myeongdong/Gangnam/Itaewon/Seongsu)

**Meta fields:**
| Key | Type | Description |
|-----|------|-------------|
| `_lpc_short_desc` | textarea | Why it's good for foreigners |
| `_lpc_benefits` | textarea | Exclusive benefits (NO discount language) |
| `_lpc_languages` | array | Supported languages (EN/JP/KR/CN) |
| `_lpc_map_link` | url | Google Maps link |
| `_lpc_contact_link` | url | Reservation/contact URL |
| `_lpc_featured` | checkbox | Featured partner toggle |

## Custom Database Tables

### `{prefix}lpc_leads`
Stores lead form submissions with UTM tracking.

### `{prefix}lpc_partner_apps`
Stores partner application form submissions.

## WordPress Options

| Option | Default | Purpose |
|--------|---------|---------|
| `lpc_telegram_link` | (empty) | Telegram chat link for CTAs |
| `lpc_whatsapp_link` | (empty) | WhatsApp link for CTAs |
| `lpc_admin_email` | admin email | Notification email for leads |
| `lpc_currency` | $ | Currency symbol |
| `lpc_price_3day` | 29 | 3-day pass price |
| `lpc_price_5day` | 39 | 5-day pass price |
| `lpc_price_7day` | 49 | 7-day pass price |
| `lpc_price_premium` | 15 | Premium add-on price |
| `lpc_checkout_3day` | (empty) | Checkout URL (empty = lead form fallback) |
| `lpc_checkout_5day` | (empty) | Checkout URL |
| `lpc_checkout_7day` | (empty) | Checkout URL |
| `lpc_ga4_id` | (empty) | Google Analytics 4 Measurement ID |
| `lpc_meta_pixel_id` | (empty) | Meta Pixel ID |

## REST API

Namespace: `/wp-json/loverspick/v1/`

| Method | Endpoint | Auth | Purpose |
|--------|----------|------|---------|
| POST | `/lead` | Public | Lead form submission |
| POST | `/partner-apply` | Public | Partner application submission |

## Admin Pages

| Page | Slug | Purpose |
|------|------|---------|
| Settings | `loverspick` | Tabbed settings: General / Pricing / Analytics |
| Leads | `loverspick-leads` | Lead submissions viewer with stats |
| Partner Apps | `loverspick-partner-apps` | Partner application viewer |

Partners are managed via the standard WP post editor: **Partners** menu in admin sidebar.

## Design System

CSS custom properties in `public/css/loverspick.css`:
- Primary: `#E85D75` (romantic coral)
- Dark: `#1A1A2E`
- Background: `#FAFAF9`
- Gold accent: `#FFB347`
- All classes scoped to `lpc-*` prefix
- Mobile-first with breakpoint at 768px
- Floating Telegram CTA on mobile
