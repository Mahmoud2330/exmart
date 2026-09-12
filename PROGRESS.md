# exMart theme progress

Living tracker for the Figma → WordPress/WooCommerce rebuild.  
**Branch:** `template` · **Deploy:** push → WP Pusher → hard refresh (bump `EXMART_VERSION` on CSS/JS).

**Source of truth:** [ExMart Figma](https://www.figma.com/design/nscFxXagTMTOIwpgvRFORo/ExMart) + `~/Downloads/figma exmart`

---

## Quick status

| Phase | Focus | Status |
|---|---|---|
| 0 | Inventory & decisions | Partially done (checkout/ATC still deferred) |
| 1 | Design system | Partial (cards/prices improved during Home) |
| 2 | Header / footer chrome | Mostly done (footer contacts + payment logos) |
| **3** | **Home** | **Done — polish left (images + mobile)** |
| 4 | Shop / PLP / Search / Collections | Not started as a focused pass |
| 5 | Product detail (PDP) | Not started as a focused pass |
| 6 | Brands index + brand landing | Partial (brand sync / logos work; Figma polish left) |
| 7 | Cart + mini-cart | Not started as a focused pass |
| 8 | Checkout · thank-you · account | **Account shell rebuilt to Figma (1.0.21):** Orders / Addresses / Wishlist / Profile stay in one shell; wishlist is `/my-account/wishlist/` (not a separate page jump); Track uses order pipeline. Checkout still deferred. |
| 9 | Content pages (About, FAQ, …) | Templates exist; need real content + Figma polish |
| 10 | Full responsive / a11y QA | Later (Home mobile still open) |
| 11 | Deploy / versioning habit | In use |

---

## How many surfaces are left?

Treat **Home as done** (aside from small polish). What’s left to “tackle down”:

### A. Storefront / shopping (highest priority) — **~6**

1. **Shop / category PLP** — grid, filters, sort, empty states  
2. **Search results** — same grid language as PLP  
3. **Collections** — Offers / New / Best Sellers / tag archives  
4. **Product detail (PDP)** — gallery, trust, tabs, related  
5. **Cart page** + **mini-cart drawer** visual parity  
6. **Checkout + thank-you + My Account** — restyle WC (decision: keep one-page)

### B. Brand surfaces — **~2**

7. **Brands index** (`template-brands.php`)  
8. **Brand landing** (`taxonomy-product_brand.php`) — products sync works; hero/filters polish left  

### C. Content / info pages — **~7**

9. About  
10. Contact (phone/email/WhatsApp already wired)  
11. FAQ  
12. Shipping & Returns  
13. Payment Methods  
14. Privacy & Terms  
15. Wishlist page (functional; visual polish)  

### D. Chrome / edge cases — **~3**

16. Header mega-menus / mobile drawer depth (partial)  
17. 404  
18. Cross-site mobile QA pass  

**Rough count: ~15–18 surfaces left** after Home, depending how you group Cart/Checkout/Account.

Suggested next sprint order: **Shop/PLP → PDP → Brands → Cart → content pages → Checkout restyle → full mobile QA**.

---

## Phase 3 — Home (done log)

Module stack (live):

1. Hero (Figma dark hero + WP banner images in image slot)  
2. Our Brands strip (logos + names, Vodlia hidden)  
3. Trust strip (centered)  
4. Shop by Category  
5. Best Sellers / Offers / New Arrivals rails  
6. Reviews band  
7. Stay in the loop (newsletter + WhatsApp)  
8. Footer (real phone/email, payment logos)

### Decisions / fixes recorded during Home

| Topic | What we did |
|---|---|
| Brand logos | Exact Media Library filenames per brand (not fuzzy match) |
| Vodlia | Hidden from Home brands strip + hero copy for now |
| Empty brand pages | Products lived on **product tags**; theme syncs tags/titles → `product_brand` |
| Brand logo map | Diversey / Grace / Oview / SureCheck / Qualita / Eliv / Verve (see `exmart_brand_logo_filenames()`) |
| Product cards | Unified height; 1-line title ellipsis; outline ATC; stars; sale price red via custom lockup |
| Trust strip | Centered |
| Contact | `+20 1064991378` · `info@exmartegypt.com` · WhatsApp `wa.me/201064991378` |
| Newsletter | Saves emails under **Tools → Newsletter** (`exmart_subscriber`) — does **not** send campaigns |
| Payment logos | COD text + Fawry / Meeza / Visa+MC / InstaPay from Media Library |
| Address in footer | Updated by you in `footer.php` (Al Haram / 6th of October) |

### Home leftovers (come back later)

- [ ] A few remaining **images** (hero/category photography polish)  
- [ ] **Mobile responsiveness** tweaks for Home sections  
- [ ] Optional: restore Vodlia when ready (logo + strip)  

### Rail data rules (for content ops)

| Rail | Rule |
|---|---|
| Best Sellers | Tag `best-sellers` / `best-seller`, else popularity |
| Offers | Products with a sale price |
| New Arrivals | Tag `new` / `new-arrivals`, else last 30 days, else newest |

---

## Open decisions (still deferred)

1. **Checkout:** keep WooCommerce one-page (recommended) vs Figma 4-step  
2. **Product cards:** keep WC Add to cart (current) vs qty stepper  
3. **Reviews band on Home:** keep static quotes vs real WC reviews only  
4. **Payments live:** COD only for now vs Paymob/Fawaterak/etc. later  

---

## Version note

Theme was at **1.0.17** when this doc was written. Always bump `EXMART_VERSION` in `functions.php` and `Version` in `style.css` together when shipping CSS/JS.

---

## How to use this file

- After finishing a page/phase, mark its row **Done** and add a short bullet under that phase.  
- Keep Home leftovers checked off when you return.  
- Don’t delete history — append dated notes if something changes.
- Every push to this branch gets a dated entry in the Changelog below, added in the same commit as the change (or the next one). Don't delete or rewrite prior entries — append only.

---

## Changelog

- **2026-09-12 — v1.0.27** — Grid/width audit: confirmed `.em-container` (max-width 1280px, same width as the hero section) is already the consistent outer wrapper on every template, header, footer, and WooCommerce page (via the `exmart_wc_wrapper_start/end` hooks in `inc/woocommerce-hooks.php`). Found and fixed one inconsistency: `template-about.php` had an inline `max-width: 800px` overriding its own `.em-page-narrow` class, which is `760px` everywhere else (FAQ, Shipping, Payment, Privacy, single product). Removed the stray inline override so About matches the other narrow-content pages. Still waiting on a screenshot/page list from the user to track down any remaining live-rendering width issues that don't show up in the markup/CSS itself.
- **2026-09-12 — v1.0.26** — Bumped `EXMART_VERSION` (`functions.php`) and `Version` (`style.css`) from 1.0.25 → 1.0.26 to bust the WP Pusher/browser cache after the CTA color revert below (nothing else changed).
- **2026-09-12 — v1.0.25 (reverted same day)** — Changed the CTA buttons (`.em-btn-primary`, `.em-btn-accent` in `style.css`; WooCommerce's native cart/checkout/place-order buttons in `assets/css/site.css`) from the blue `--accent-600`/`--accent-700` tokens to new red `--cta`/`--cta-hover` tokens (`#E5341C` / `#C62818`), then reverted on request — buttons are back on `--accent-600`/`--accent-700` blue, and the `--cta`/`--cta-hover` tokens were removed again.
