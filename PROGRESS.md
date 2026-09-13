# exMart theme progress

Living tracker for the Figma → WordPress/WooCommerce rebuild.  
**Branch:** `template` · **Deploy:** push → WP Pusher → hard refresh (bump `EXMART_VERSION` on CSS/JS).

**Source of truth:** [ExMart Figma](https://www.figma.com/design/nscFxXagTMTOIwpgvRFORo/ExMart) + `~/Downloads/figma exmart`

---

## Quick status

| Phase | Focus | Status |
|---|---|---|
| 0 | Inventory & decisions | Mostly settled (ATC steppers shipped; checkout is Figma 4-step) |
| 1 | Design system | Partial (cards/prices improved during Home) |
| 2 | Header / footer chrome | Mostly done (footer contacts + payment logos) |
| **3** | **Home** | **Done — polish left (images + mobile)** |
| 4 | Shop / PLP / Search / Collections | **Shop PLP shell (1.0.42):** filters left + multi-col grid; mobile filters drawer (1.0.55); Search/Collections still open |
| 5 | Product detail (PDP) | Partial (1.0.49–1.0.53 reconstruction; polish left) |
| 6 | Brands index + brand landing | Partial (brand sync / logos work; Figma polish left) |
| 7 | Cart + mini-cart | **Done (1.0.60–1.0.66):** Figma cart + mini-cart drawer; mobile cart fixed |
| 8 | Checkout · thank-you · account | **Checkout 4-step (1.0.67–1.0.70)** + Account shell (1.0.21). Thank-you still open. |
| 9 | Content pages (About, FAQ, …) | Templates exist; need real content + Figma polish |
| 10 | Full responsive / a11y QA | Later (Home mobile still open) |
| 11 | Deploy / versioning habit | In use |

---

## How many surfaces are left?

Treat **Home as done** (aside from small polish). What’s left to “tackle down”:

### A. Storefront / shopping (highest priority) — **~6**

1. **Shop / category PLP** — layout + Brand/Category/Availability filters shipped (1.0.42); polish/edge cases left  
2. **Search results** — same grid language as PLP  
3. **Collections** — Offers / New / Best Sellers / tag archives  
4. **Product detail (PDP)** — gallery/ATC pass done (1.0.49–53); polish left  
5. ~~**Cart page** + **mini-cart drawer**~~ — **done (1.0.60–1.0.66)**  
6. **Checkout + thank-you + My Account** — checkout 4-step done (1.0.67–70); thank-you still open; account shell already done  

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

**Rough count: ~12–14 surfaces left** after Home + Cart + Checkout.

Suggested next sprint order: **Search/Collections → PDP polish → Brands → thank-you → content pages → full mobile QA**.

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

1. ~~**Checkout:** keep WooCommerce one-page vs Figma 4-step~~ — **shipped Figma 4-step (1.0.67+)**  
2. ~~**Product cards:** keep WC Add to cart vs qty stepper~~ — **stepper shipped**  
3. **Reviews band on Home:** keep static quotes vs real WC reviews only  
4. **Payments live:** COD + FawryPay (`fawry_pay`) enabled; theme presents Fawry as one Visa|MC|Meeza|Fawry option  

---

## Version note

Theme was at **1.0.17** when this doc was written; current head is **1.0.70**. Always bump `EXMART_VERSION` in `functions.php` and `Version` in `style.css` together when shipping CSS/JS.

---

## How to use this file

- After finishing a page/phase, mark its row **Done** and add a short bullet under that phase.  
- Keep Home leftovers checked off when you return.  
- Don’t delete history — append dated notes if something changes.
- Every push to this branch gets a dated entry in the Changelog below, added in the same commit as the change (or the next one). Don't delete or rewrite prior entries — append only.

---

## Changelog

- **2026-09-13 — v1.0.70** — Removed blue focus outline on checkout payment radios (global `input:focus` accent ring).
- **2026-09-13 — v1.0.69** — Payment cards rebuilt; Fawry option uses Media Library Visa/MC + Meeza + Fawry logos instead of plugin sprite icons.
- **2026-09-13 — v1.0.68** — Checkout polish: pipeline current-dot blue ring; hide coupon strip; remove phone SMS helper; COD + grouped `fawry_pay` as “Visa | Mastercard | Meeza | Fawry”.
- **2026-09-13 — v1.0.67** — Figma 4-step checkout (Contact → Address → Shipping → Payment) with sticky Order Summary; mobile stacks form then summary.
- **2026-09-13 — v1.0.66** — Cart row dividers continuous again (border on `tr`, not per-cell).
- **2026-09-13 — v1.0.65** — Cart column align (Product/Qty/Price) + tighter Continue shopping spacing.
- **2026-09-13 — v1.0.62–1.0.64** — Cart: drop items outer border; normal checkout-button height; mobile cart card layout (kill WC responsive labels).
- **2026-09-13 — v1.0.61** — Cart page rebuilt to Figma (items + Order Summary / coupon / COD note / checkout CTA).
- **2026-09-13 — v1.0.60** — Mini-cart drawer: stable View cart + Checkout buttons; header/pill cart count badges.
- **2026-09-13 — v1.0.59** — Batch: centered login/register; mobile drawer logo; guest checkout layout; sort arrow; brand landing logos; wishlist mobile + guest `/wishlist/`.
- **2026-09-13 — v1.0.58** — Login/Register: stop collapsing page width (auth form max-width no longer on `.em-container`).
- **2026-09-12 — v1.0.57** — Fixed "Shop by Category" circles on mobile: `.em-cat-circle` was a fixed `64px` regardless of screen size — tiny next to everything else on the page, which all scales with viewport width. Replaced the fixed value and the separate desktop-only `clamp()` with one continuous `clamp(80px, 22vw, 112px)` spanning mobile through desktop, so there's no size jump at the 640px breakpoint (circles already reach their 112px max by the time the layout switches from horizontal-scroll to evenly-spaced). Bumped the label font-size to a matching continuous clamp and `.em-cat-item`'s `min-width` from 72px to 88px so labels don't feel cramped next to the now-larger circles.
- **2026-09-12 — v1.0.56** — Removed the drop shadow (`box-shadow: 0 1px 24px rgba(11,11,12,.06)`) from the sticky navbar's glassmorphism treatment, on request. The translucent blurred background and soft bottom border stay; just the shadow is gone.
- **2026-09-12 — v1.0.55** — Fixed the Shop/PLP filters on mobile: they were rendering as a plain block above the product grid (checkbox lists for Brand + Category + Availability), pushing products off-screen and forcing a scroll just to see any results — flagged in the earlier UI/UX audit as the single biggest mobile issue found. Turned it into a real slide-in drawer below 1024px, reusing the exact same drawer/backdrop pattern already used for the cart and mobile nav (same CSS shape, same open/close/backdrop-click/Escape-key JS behavior) — added a "Filters" button in the shop toolbar (`#em-shop-filters-open`) and a close button in a new drawer header, wired via `initShopFiltersDrawer()` in `site.js`. At 1024px+ the same `<aside>` reverts to exactly the static sidebar it already was (no markup duplication — one filter form, shown as a sidebar or a drawer depending on viewport). Filter-on-checkbox-change auto-submit (already in place) is untouched, so picking a filter still reloads with results applied, drawer closed on the fresh page load.
- **2026-09-12 — v1.0.54** — Locked every product grid on the site to the same fixed column counts (2 mobile / 3 tablet / 4 desktop) instead of `grid-template-columns: repeat(auto-fill, minmax(220px, 1fr))`. Auto-fill sizes columns purely by available width, so pages with more room landed on 5 while narrower ones landed on 4 — Shop/category archives have a 220px filter sidebar eating width (naturally ~4), while Related Products/Upsells on the PDP get the full 1280px with no sidebar (fits ~5). Fixed `ul.products` (site.css) and `.em-grid` (style.css, used only by the two wishlist product grids) to `repeat(2,1fr)` → `repeat(3,1fr)` at 640px → `repeat(4,1fr)` at 1024px, so 4 is the universal desktop count everywhere: Shop, category/tag archives, Related products, Upsells, search results, the brand landing page, and both wishlist grids. Also **descoped these selectors from `.woocommerce`** — WooCommerce only adds that body class on its own core pages (shop/product/cart/checkout/account), not reliably on a generic search-results page or every custom taxonomy archive, and `search.php`/`taxonomy-product_brand.php` both render `<ul class="products">` too; the old `.woocommerce`-prefixed selectors likely weren't even applying there before, meaning those two pages may have been silently falling back to WooCommerce's own classic float-based grid this whole time.
- **2026-09-12 — v1.0.53** — User reported neither the 1.0.52 ATC change nor the 1.0.50 gallery-width fix are visible live. Did a full re-audit of both looking specifically for a code-level conflict ("dead code blockage"): grepped every rule touching `.woocommerce-product-gallery*`, `.em-card-actions`, `.em-card-atc*` in both `style.css` and `assets/css/site.css`, and re-read `exmart_pdp_add_to_cart()`/`content-single-product.php` end to end. Found no conflicting or leftover rule, no duplicate hook, nothing hiding either change — the code as written should work. Two changes made as a precaution rather than because a bug was found: widened the gallery `!important` fix to also force `width:100%` on `.woocommerce-product-gallery`, `__wrapper`, `__image`, and the `<a>` around the image (previously only the innermost `<img>`), in case WooCommerce's gallery/slider JS is constraining a wrapping element rather than the image itself. Given the trust-block/product-meta styling from 1.0.49 *did* show up correctly in an earlier screenshot — proving the deploy pipeline and this exact template work — but nothing since 1.0.50 is showing, the leading theory is that this isn't a code bug: either WP Pusher hasn't pulled past 1.0.49 yet, or a page/object cache (host-level, a caching plugin, or Cloudflare) is serving a stale render of this specific page. Worth checking directly: view-source on the product page and confirm the enqueued CSS `<link>` reads `?ver=1.0.53`; if it still says an older version, the deploy hasn't landed and no further code change here will help until it does.
- **2026-09-12 — v1.0.52** — Reverted the bespoke PDP quantity stepper from 1.0.51 (it overlapped/misaligned with the row around it) and replaced it with the exact same working control already used on product cards, per direct instruction: don't reinvent, reuse what's proven. `exmart_pdp_add_to_cart()` now unhooks WooCommerce's default `woocommerce_template_single_add_to_cart` and calls `exmart_card_atc_control( $product )` (same function, same `.em-card-atc`/`.em-card-qty` markup, same `initCardAtc()` JS already wired generically via event delegation — confirmed `exmartData`/ajaxUrl/nonce are localized on every page, not just shop/archive, so it's fully functional here too) — wrapped in `.em-card-actions` so the existing card-scoped CSS actually applies. Only for simple, non-variable products: `exmart_card_atc_control()` falls back to a "Select options" link to the product's own permalink for anything else, which would just loop back to the same page, so variable/grouped/external products keep WooCommerce's real form. Removed the now-dead custom stepper CSS/JS from 1.0.51. Also strengthened the gallery image width fix from 1.0.50 with `!important`, since it was reported as still not filling the column — the earlier fix was theoretically sound (nothing else in the CSS constrains that img), so if it's still small after this deploys it's most likely a caching/deploy-lag question rather than a wrong selector.
- **2026-09-12 — v1.0.51** — Matched the PDP's Add to Cart row to the original reference layout (user shared a screenshot of the intended design at ex-mart-storefront.vercel.app). Found two more fully-built-but-orphaned components in the design system, same pattern as the earlier `.em-stock-*` find: **quantity stepper** — `.em-qty`/`.em-qty-btn`/`.em-qty-val` were completely styled in `style.css` but never referenced by any template; the PDP was rendering WooCommerce's bare `<input type="number">` with default browser spinner arrows instead of a real −/+ stepper. Wired it up: `exmart_pdp_qty_minus_button()`/`exmart_pdp_qty_plus_button()` (new, `inc/woocommerce-hooks.php`) inject the buttons via WooCommerce's `woocommerce_before/after_add_to_cart_quantity` hooks (guarded to `is_product()` only, so cart/checkout quantity inputs are untouched), styled the `.quantity` wrapper as the pill control, and added `initPdpQtyStepper()` in `site.js` — a plain increment/decrement respecting the input's min/max/step, no AJAX (unlike the product-card stepper, which auto-adds/removes from the cart on every click — deliberately not reusing that component here since the PDP flow is pick-a-quantity-then-submit, not sync-on-every-click). Also added a **wishlist heart button** next to Add to Cart — the PDP had no wishlist entry point at all before; reused the exact `.em-wishlist-btn`/`.em-wishlist-toggle` markup and existing (already generic) JS from product cards, added via `woocommerce_after_add_to_cart_button`. Star rating color and the "EGP" currency label were already correct on inspection — no change needed there. Gallery/thumbnail behavior itself (WooCommerce's native slider, already fixed to fill width in 1.0.50) appears to already match the reference's thumbnail-row pattern once a product actually has multiple images assigned.
- **2026-09-12 — v1.0.50** — Fixed the two real bugs visible in a live screenshot of the product page: (1) huge, uneven gaps between title/price/Add-to-cart — `.em-pdp-info` also carries WooCommerce's own `.summary`/`.entry-summary` classes, and WooCommerce's bundled stylesheet (still loaded) puts `margin-bottom: 1.618em` on every direct child of `.summary`; since that's an em value it scales with each child's own font-size (huge after the large H1) and stacks on top of the flex `gap` the theme already uses, producing wildly uneven spacing. Reset with `.em-pdp-info > * { margin: 0; }` so the flex gap is the only spacing source. (2) Tiny product photo floating in a mostly-empty gallery box — the gallery's main `<img>` had no `width: 100%` rule anywhere, so it rendered at its uploaded file's native pixel size instead of filling the column; added `width:100%; height:auto` to `.woocommerce-product-gallery__wrapper img`. Multiple-images/thumbnails not shown is still unconfirmed as code vs. content (this product may only have one image assigned in wp-admin) — waiting on a reference screenshot before touching gallery layout further.
- **2026-09-12 — v1.0.49** — PDP reconstruction pass. Traced "weird and bad" to several genuinely unstyled/broken pieces, not a subjective redesign: (1) the sale badge (`span.onsale`) had no positioned ancestor on the product page (`div.images` had no `position`, unlike the card grid's `.em-card-img-wrap`), so it was floating relative to the page's initial containing block instead of the product image — added `position: relative` to `.woocommerce div.product div.images`; (2) `.product_meta` (SKU/Categories/Tags) and the stock status line (`.stock`) had zero theme CSS and were falling back to bare WooCommerce/browser defaults — styled both to match the design system, `.stock` colored by `--success`/`--error`; (3) "Related products"/"Upsells" rendered with a plain default `<h2>` (browser UA font-size/margin) with no spacing from the tabs above — gave it real heading typography and a top border + margin to read as its own section; (4) removed `.em-stock-in`/`.em-stock-low`/`.em-stock-out` from `style.css` — three rules that were never applied anywhere in any template (verified via grep), dead code left over from an unfinished stock-status feature, now superseded by the real `.stock` classes; (5) `.em-pdp-grid`'s 2-column breakpoint moved from 768px → 900px (768 was cramping gallery+info into ~350px columns on tablet portrait, same reasoning as the hero/shop breakpoints elsewhere); (6) the Add to Cart button was 44px tall next to a 48px quantity stepper — matched it to 48px so the row has one clean baseline.
- **2026-09-12 — v1.0.48** — Replaced the mobile vertical stack with a real mobile layout: below 900px the 4 hero tiles are now a horizontal scroll-snap carousel of uniform blocks (`flex: 0 0 clamp(260px, 78vw, 340px)`, `scroll-snap-align: start`, next card peeking in at the edge) instead of one full-width card per row. Added `initHeroCarousel()` in `assets/js/site.js`: autoplay advances one tile every 4.5s with wraparound, pauses for 6s after a manual swipe (so it doesn't fight the visitor's gesture), and is disabled entirely at the 900px+ grid breakpoint and for `prefers-reduced-motion: reduce` — the row stays fully swipeable by hand either way, only the automatic advance is what's gated.
- **2026-09-12 — v1.0.47** — Mobile pass on the bento hero (the desktop layout from the last few entries was left untouched). Three fixes, all scoped behind `@media (min-width: 900px)` so mobile gets its own values instead of inheriting desktop's: (1) `.em-hero-tile-content`'s `max-width: 60%` (480px on the main tile) was left over from when images sat in a side column — with images now full-bleed there's no side column to avoid, so mobile now gets full-width text instead of cramming headlines into 60% of a phone screen; (2) the text-legibility scrim (`.em-hero-tile::before`) fades out by 75% width on desktop assuming text stays in the left column — since mobile text can now span the full tile, mobile gets a more even dark tint across the whole tile instead; (3) tile heights (200px / 320px main) are smaller on mobile than desktop's (260px / 420px) so the 4 stacked tiles don't add up to ~1200px of scrolling before the rest of the homepage.
- **2026-09-12 — v1.0.46** — Fixed tile images to actually fill the card edge-to-edge like the reference, instead of sitting as a small contained cutout in the corner. `.em-hero-tile-img` is now `position:absolute; inset:0; width/height:100%; object-fit:cover` (was `object-fit:contain` boxed to ~44% width/78% height). Since a full-bleed photo can land under the text in any tile, added a `.em-hero-tile::before` dark left-to-right scrim (matches where the text sits) so the copy stays legible over whatever the photo looks like — this also applies over the plain gradient on tiles without an image yet, which is a harmless minor darkening.
- **2026-09-12 — v1.0.45** — Wired real images into all 4 bento hero tiles. This session has no access to the live site's media library or database (network egress to exmartegypt.com is blocked from here), so instead of hardcoding attachment IDs, added `exmart_find_media_by_filename()` in `inc/template-tags.php` — a `_wp_attached_file LIKE '%needle%'` lookup (same pattern `exmart_get_hero_images()` already used for `main-banner`) that runs on the live WordPress server at render time. `exmart_get_promo_image()` now checks its Customizer setting first, then falls back to this filename search. Wired per the naming convention given: main tile → `big-block` (object-position: left center), Offers tile → `top-right` (object-position: center top), New in → `bottom-left`, Best sellers → `bottom-right` (both default centered). Re-added an `<img class="em-hero-tile-img--main">` to the main tile now that a real image source exists again. If a tile still renders as gradient-only after deploy, the filename in the Media Library doesn't contain the expected substring — check the exact filename or set the image directly via Appearance → Customize → Homepage Hero instead.
- **2026-09-12 — v1.0.44** — Removed the old two-column layout's leftovers from the new bento hero's main tile: the multi-slide product image (`.em-hero-img-wrap`/`.em-hero-slide`, its `data-em-hero-slider` JS rotation in `assets/js/site.js`, and the associated CSS) and the "100% Authentic / Direct from manufacturer" badge overlay. The main tile is now just the gradient background with text + CTA, matching the other three tiles until new imagery is ready. Also dropped the now-unused `$hero_images`/`$hero_multi` variables in `front-page.php`. Left `exmart_get_hero_images()` and its Customizer settings (`exmart_hero_image`, `exmart_hero_image_ids`) in place, unused for now, in case a main-tile image comes back later.
- **2026-09-12 — v1.0.43** — Rebuilt the Home hero as a 4-tile "bento" grid (desktop pass; mobile is a plain single-column stack for now, to be refined as its own follow-up pass) matching a reference layout: a large main tile (left, spans both rows — kept the existing multi-slide hero image/badge, restyled) plus three secondary promo tiles on the right (top: Offers, spanning both right columns; bottom: New in / Best sellers, one each). Each promo tile links to the existing `exmart_rail_view_all_url()` destination (Offers/New Arrivals/Best Sellers) and gets its own gradient background drawn from existing design tokens (`--sale` red for Offers, `--success` green for New in, `--warning` amber for Best sellers) so nothing looks blank before real photography is in. Added 3 new Customizer image settings (Appearance → Customize → Homepage Hero: "Promo tile 1/2/3 image") plus `exmart_get_promo_image()` in `inc/template-tags.php` — tiles render gradient-only until an image is picked there, same pattern as the existing hero image setting. Dropped the old full-bleed hero background photo and the "Our story" secondary button to match the reference's one-CTA-per-card look; `/about/` is still reachable from nav/footer.
- **2026-09-12 — v1.0.31** — Glassmorphism on the sticky navbar: `.em-site-header` now uses a translucent white background (`rgba(255,255,255,.72)`) with `backdrop-filter: blur(16px) saturate(180%)` (plus `-webkit-` prefix for Safari), a softer semi-transparent border, and a subtle drop shadow instead of a flat white bar — page content blurs through it while scrolling underneath. Added an `@supports` fallback to a solid `var(--paper)` background for browsers without `backdrop-filter` support. Scope is just the header bar itself — the mega-menu dropdown panels (`.em-mega`) already have their own solid white background and higher z-index, so they stay fully opaque and readable.
- **2026-09-12 — v1.0.30** — Fixed the sticky header not actually sticking while scrolling. `.em-site-header` already had `position: sticky; top: 0`, but both `html` and `body` had `overflow-x: hidden` — setting `overflow-x` without `overflow-y` forces the browser to compute `overflow-y: auto` on that element, so having it on *both* ancestors turned them into two competing scroll containers, and the sticky header ended up anchored to `body`'s own scroll box instead of the real page viewport (a well-known way for `position: sticky` to silently fail). Removed `overflow-x: hidden` from `body`, keeping it only on `html` — still suppresses horizontal overflow (e.g. from the hero's `left: -24px` badge) without the redundant nested scroll container.
- **2026-09-12 — v1.0.29** — Sitewide grid pass: removed the `.em-page-narrow` (760px) treatment from every content page — About, FAQ, Shipping & Returns, Payment Methods, Privacy & Terms, the generic page fallback (`page.php`), and the blog-post fallback (`single.php`) — so they all sit on the same 1280px `.em-container` grid as the home page/hero instead of being visibly narrower with extra side whitespace. Deleted the now-unused `.em-page-narrow` CSS rule. Left untouched: form-specific widths that are deliberately narrow by design, not page-grid bugs (`.em-auth` login/register form at 420px, `.em-profile-form` at 480px, the search box, and prose `max-width: 72ch` text measures) — those aren't the site's outer content grid.
- **2026-09-12 — v1.0.28** — Fixed the real bug from the screenshot: on Home, the "Our Brands" strip (`.em-brands-strip-row`) and the "Shop by Category" circle track (`.em-cat-track`) were NOT wrapped in `.em-container`, so on wide screens they stretched edge-to-edge past the 1280px grid every other section (including the hero) uses — only their own small `padding-inline` was constraining them, with no max-width. Wrapped both in `.em-container` (matching how the product rails already do it) and removed their now-redundant `padding-inline` from `assets/css/site.css` so the container supplies the edge spacing instead. Both rows now cap at the same 1280px width as the hero, centered the same way.
- **2026-09-12 — v1.0.27** — Grid/width audit: confirmed `.em-container` (max-width 1280px, same width as the hero section) is already the consistent outer wrapper on every template, header, footer, and WooCommerce page (via the `exmart_wc_wrapper_start/end` hooks in `inc/woocommerce-hooks.php`). Found and fixed one inconsistency: `template-about.php` had an inline `max-width: 800px` overriding its own `.em-page-narrow` class, which is `760px` everywhere else (FAQ, Shipping, Payment, Privacy, single product). Removed the stray inline override so About matches the other narrow-content pages. Still waiting on a screenshot/page list from the user to track down any remaining live-rendering width issues that don't show up in the markup/CSS itself.
- **2026-09-12 — v1.0.26** — Bumped `EXMART_VERSION` (`functions.php`) and `Version` (`style.css`) from 1.0.25 → 1.0.26 to bust the WP Pusher/browser cache after the CTA color revert below (nothing else changed).
- **2026-09-12 — v1.0.25 (reverted same day)** — Changed the CTA buttons (`.em-btn-primary`, `.em-btn-accent` in `style.css`; WooCommerce's native cart/checkout/place-order buttons in `assets/css/site.css`) from the blue `--accent-600`/`--accent-700` tokens to new red `--cta`/`--cta-hover` tokens (`#E5341C` / `#C62818`), then reverted on request — buttons are back on `--accent-600`/`--accent-700` blue, and the `--cta`/`--cta-hover` tokens were removed again.
