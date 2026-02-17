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
└── lp-ai-match/                       # WordPress plugin root
    ├── lp-ai-match.php                # Main plugin file (bootstrap)
    ├── uninstall.php                  # Cleanup on uninstall
    ├── includes/                      # Core PHP classes
    │   ├── class-lp-activator.php     # Activation hooks (DB tables, defaults)
    │   ├── class-lp-deactivator.php   # Deactivation hooks
    │   ├── class-lp-post-types.php    # CPT registration
    │   ├── class-lp-matching.php      # Matching algorithm
    │   ├── class-lp-saju.php          # Saju (四柱) calculation engine
    │   ├── class-lp-horoscope.php     # Zodiac sign calculation & horoscope
    │   ├── class-lp-tarot.php         # Tarot card logic
    │   ├── class-lp-report.php        # AI report generation
    │   ├── class-lp-pdf.php           # HTML→PDF conversion
    │   ├── class-lp-chat.php          # Internal chat system
    │   ├── class-lp-stripe.php        # Stripe payment integration
    │   └── class-lp-rest-api.php      # REST API endpoints
    ├── admin/                         # WP admin pages
    │   ├── class-lp-admin.php         # Admin menu & pages
    │   ├── views/                     # Admin page templates
    │   ├── css/                       # Admin styles
    │   └── js/                        # Admin scripts
    ├── public/                        # Frontend
    │   ├── class-lp-public.php        # Frontend controller
    │   ├── views/                     # Frontend templates (shortcodes)
    │   ├── css/                       # Frontend styles
    │   └── js/                        # Frontend scripts
    ├── assets/                        # Static assets
    │   └── images/                    # Tarot card images, icons
    └── languages/                     # i18n (.pot/.po/.mo files, Japanese)
```

## Custom Post Types

| CPT Slug      | Purpose                                      |
|---------------|----------------------------------------------|
| `lp_profile`  | User dating profiles (DOB, preferences, etc.) |
| `lp_match`    | Match records between two profiles            |
| `lp_report`   | AI compatibility reports (linked to matches)  |
| `lp_reading`  | Horoscope/tarot reading results               |

Additional custom DB tables may be used for chat messages and matching scores.

## Core Features

### Free Features (Lead Generation)
1. **Horoscope** — DOB input → zodiac calculation → daily love fortune → CTA to paid matching
2. **Tarot 3-card draw** — random 3 cards → interpretation text → cute minimal UI → admin-editable

### Paid Products (Admin-configurable pricing)
| Tier | Default Price | Features |
|------|--------------|----------|
| Basic | ¥1,000 | 1 match, simple AI report, 24h chat |
| Standard | ¥3,000 | 3 matches, detailed AI report, 7-day chat |
| Premium | ¥6,900 | Saju marriage compatibility, yearly graph, 5 matches, 7-day chat |

### Matching Algorithm
```
Final Score = (Saju compatibility 40%) + (Personality match 40%) + (Values fit 20%)
```
Each component scored 0–100, then weighted. Input: DOB, 5–10 personality questions, romance style, value keywords, optional face image.

### AI Report Structure
1. Total compatibility score
2. Saju-based analysis
3. Personality complement explanation
4. Conflict potential
5. Relationship development timing

Reports are score-based explanations — never random text generation.

### PDF Generation
- HTML report → PDF conversion
- User download + admin access

### Internal Chat
- 1:1 messaging (AJAX polling, not real-time required)
- Auto-expiry (24h or 7 days based on plan)
- Optional mutual contact sharing
- Chat logs saved, reporting/blocking available

### Payment (Stripe)
- Stripe Checkout with webhooks
- On success: grant matching rights → generate report → activate chat
- Japan payment methods: credit card, Apple Pay, (PayPay if feasible)

## Security Requirements

- WordPress nonce verification on all forms and API calls
- Input sanitization (`sanitize_text_field`, `wp_kses`, etc.)
- XSS prevention (escape all output with `esc_html`, `esc_attr`, etc.)
- CSRF protection via nonces
- Prepared statements for all custom SQL (`$wpdb->prepare()`)
- Age verification checkbox
- Terms of service and privacy policy pages
- Chat reporting and admin blocking

## REST API

All interactive features use the WordPress REST API (`/wp-json/lp-ai-match/v1/`). Key endpoints:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/horoscope` | Generate horoscope reading |
| POST | `/tarot/draw` | Draw 3 tarot cards |
| POST | `/profile` | Create/update user profile |
| POST | `/match/request` | Request matching (paid) |
| GET | `/match/{id}` | Get match details |
| GET | `/report/{id}` | Get AI report |
| GET | `/report/{id}/pdf` | Download PDF report |
| POST | `/chat/send` | Send chat message |
| GET | `/chat/{match_id}` | Get chat messages (polling) |
| POST | `/payment/create-session` | Create Stripe checkout session |
| POST | `/payment/webhook` | Stripe webhook handler |

## Admin Features

WordPress admin dashboard provides:
- Tarot card image & interpretation management
- Horoscope text management
- Pricing configuration
- Matching weight adjustments
- User blocking
- Match log viewer
- Report regeneration

## UI/Design Guidelines

- **Target**: Japanese women
- **Style**: Minimal, cute but premium feel
- **Avoid**: Random chat vibes, occult/spiritual aesthetics
- **Tone**: Entertainment → seriousness (natural transition)
- **Colors**: Soft pastels, clean whites, subtle gradients
- **Typography**: Clean Japanese web fonts

## Code Conventions

### PHP
- WordPress coding standards (WPCS)
- Class-based architecture, one class per file
- Prefix all functions/classes with `lp_` or use `LP_` namespace
- Use PHPDoc comments on all public methods
- Use `$wpdb->prepare()` for all database queries
- Escape all output (`esc_html()`, `esc_attr()`, `esc_url()`)
- Sanitize all input (`sanitize_text_field()`, `absint()`, etc.)

### JavaScript
- Vanilla JS or jQuery (WordPress-bundled) — no additional JS frameworks
- Use `wp_localize_script()` for passing data to JS
- AJAX via `wp.apiRequest()` or `fetch()` to REST API

### CSS
- BEM-like naming: `.lp-component__element--modifier`
- Mobile-first responsive design
- No CSS frameworks — custom styles only

### Git Workflow
- Feature branches for new work
- Clear, descriptive commit messages
- Atomic commits

## Key Dependencies

- **WordPress** 6.0+
- **PHP** 7.4+
- **Stripe PHP SDK** (via Composer or bundled)
- **PDF library**: TCPDF or Dompdf (for HTML→PDF)
- **OpenAI API** or similar (for AI report text generation)

## Notes for AI Assistants

- This is an **MVP** — avoid unnecessary complexity. Build for extensibility but ship lean.
- All logic must be in the `lp-ai-match` plugin — never in themes.
- Always read existing files before modifying them.
- Keep this CLAUDE.md file up to date as the project evolves.
- All user-facing text must be in **Japanese** (日本語).
- Use WordPress i18n functions (`__()`, `_e()`) for all strings.
- Security is non-negotiable — every input sanitized, every output escaped, every DB query prepared.
- Prefer WordPress APIs over custom solutions (WP_Query, WP REST API, Settings API, etc.).
