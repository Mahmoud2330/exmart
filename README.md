# exMart — WordPress/WooCommerce Theme

A WordPress + WooCommerce theme built from the exMart React storefront
design. Drag-and-drop install, WooCommerce-native cart/checkout/account
(real, working — not mocked), a custom Brand taxonomy, and a
lightweight localStorage wishlist. Designed to sit on top of your
existing WooCommerce product catalog — it doesn't ship or require any
demo/mock products.

## What this is (and isn't)

This is a **classic PHP theme**, not a page builder or a 1:1 pixel clone
of the React app. Where WooCommerce already does something well
(cart, checkout, login/register, order history, coupons, search), the
theme leans on WooCommerce's native pages and just restyles them —
that's a **feature**, not a shortcut: you get real accounts, real
orders, and real payment-gateway compatibility for free, instead of
the React app's mocked checkout/login that didn't actually do anything.

Two deliberate simplifications versus the original design, both easy
to extend later:

- **Checkout is WooCommerce's standard one-page checkout** (restyled),
  not the original 4-step wizard. Rebuilding a custom multi-step flow
  on top of WooCommerce's checkout is realistic but a separate, larger
  project — safer to ship the standard, battle-tested flow for
  anything touching real payments.
- **Product-loop cards use WooCommerce's normal "Add to cart" button**
  (AJAX, updates the cart count instantly) rather than an inline
  quantity stepper. You can still change quantity on the cart page.

## Requirements

- WordPress 6.0+
- WooCommerce 8.0+ (install & activate this **before** the theme, or
  activate WooCommerce and then re-activate this theme once — see
  Setup below)
- PHP 7.4+

## Install

1. **WordPress admin → Plugins → Add New → search "WooCommerce" → Install → Activate.**
   Click through WooCommerce's setup wizard (or skip it — the theme
   sets Egypt/EGP for you on first activation, see below). Make sure
   WooCommerce creates its default pages (Shop, Cart, Checkout, My
   Account) — it does this automatically on first activation; if it
   didn't, go to **WooCommerce → Status → Tools → "Create default
   WooCommerce pages"**.
2. **Appearance → Themes → Add New → Upload Theme** → choose
   `exmart.zip` → **Install Now** → **Activate**.
3. That's it for the storefront shell. On activation the theme
   automatically:
   - registers the **Brands** taxonomy and seeds it with exMart's 8
     brand terms (Diversey, Grace, Oview, SureCheck, Qualita, Vodlia,
     Eliv, Verve) and their accent colors/taglines, ready for you to
     assign to your existing products — see "Using your existing
     products" below;
   - creates the **About / Contact / FAQ / Shipping & Returns /
     Payment Methods / Privacy & Terms / Brands / Wishlist** pages
     with their templates already assigned;
   - sets **Home** as your static front page;
   - sets the WooCommerce store country to Egypt and currency to EGP
     (only if you haven't already configured something else).

## Using your existing products

This theme is built to sit on top of a WooCommerce catalog you already
have (e.g. products already entered on your staging site) — there's
nothing to import. A few things to check so the design's specific
features light up correctly against your real data:

- **Brands.** The mega menu, brand pills, brand landing pages, and the
  brand line above each product title all read from the **Brands**
  taxonomy this theme registers (`product_brand`) — go to
  **Products → Brands** in wp-admin, create a term per brand you carry
  (set its accent color, type, and tagline on the term's edit screen),
  then assign it to each product, same as you would a category.
  Products without a Brand term still work fine; they just won't show
  a brand line or appear on a brand page.
- **Categories & Tags.** These map directly to WooCommerce's built-in
  Product Categories and Tags — if your existing products already have
  categories set, the category mega menu, footer links, and Shop by
  Category strip populate automatically. Tag a product `Best Sellers`
  to get the "Best Seller" ribbon; the "New" ribbon is automatic for
  anything published in the last 30 days; "Sale" follows WooCommerce's
  own sale-price logic — none of these need manual setup beyond that
  one tag.
- **"How to Use" / "Accuracy" tabs.** Optional. Fill in the "How to
  Use" field on a product's edit screen (General tab) to have it show
  as its own product tab on the front end; products in the Home
  Diagnostics or Health & Protection categories automatically get an
  "Accuracy" disclaimer tab too.
- **Product images.** The theme prefers each product's own featured
  image; a product with no featured image falls back to a
  category-representative placeholder photo purely so the grid isn't
  full of gaps while photography catches up — set a featured image
  and the placeholder is bypassed automatically.

## Content model

| Original app concept | WordPress/WooCommerce equivalent |
|---|---|
| Product | WooCommerce Product (`product` post type) |
| Category | WooCommerce Product Category (`product_cat`) |
| Brand | Custom taxonomy `product_brand` (this theme) |
| Collection (Wipes, Offers, New, Best Sellers…) | WooCommerce Product Tag (`product_tag`) |
| Cart / Checkout / Login / Register / Orders / Addresses | Native WooCommerce pages & endpoints |
| Wishlist | localStorage on the visitor's browser + a small AJAX endpoint that renders matching product cards — no account required, same as the original |
| Newsletter signup | Stored as a lightweight `exmart_subscriber` post type (**Tools → Newsletter** in wp-admin) — swap `inc/contact-form.php`'s AJAX handler for Mailchimp/Klaviyo/etc. whenever you're ready |
| Contact form | Real submission via `wp_mail()` to the site admin email (no plugin) |
| "How to Use" / "Accuracy" product tabs | Custom field on the product edit screen (`_exmart_how_to_use`) + an automatic "Accuracy" tab on Home Diagnostics / Health & Protection products |

## Payments

Cash on Delivery works out of the box (**WooCommerce → Settings →
Payments → Cash on delivery**). Fawry / Meeza / InstaPay / cards need
an Egypt-compatible payment gateway plugin (e.g. Paymob, Fawaterak,
Kashier) — the Payment Methods page already lists them for customers,
but the theme intentionally doesn't fake a gateway integration; wire up
the real one before taking real payments.

## Menus

The header/footer navigation (Categories, Brands, quick-links, footer
columns) is generated directly from your Product Categories, Brands,
and Tags — you don't need to build a nav menu for the storefront to
work. A `primary` menu location is registered if you want to add extra
links (**Appearance → Menus**), but it's optional.

## File map

```
exmart/
├── style.css                    Theme header + design tokens + component CSS
├── functions.php                Requires everything in inc/
├── inc/
│   ├── theme-setup.php          Theme supports, menus, asset enqueueing
│   ├── custom-taxonomies.php    Brand taxonomy + color/type/tagline term meta
│   ├── woocommerce-hooks.php    Wrapper, product tabs, trust block, grid settings
│   ├── wishlist.php             AJAX: product IDs → rendered cards
│   ├── contact-form.php         Contact form handler + newsletter AJAX
│   ├── theme-activation.php     One-time seed: brand terms, pages, WC locale
│   └── template-tags.php        Small shared helpers (breadcrumbs, stars, rails…)
├── header.php / footer.php      Announcement bar, mega menus, mini-cart drawer, footer
├── front-page.php               Homepage
├── page.php                     Generic page fallback
├── template-*.php               About / Contact / FAQ / Shipping / Payment / Privacy / Brands / Wishlist
├── taxonomy-product_brand.php   Brand landing page
├── search.php / searchform.php  Product-aware search
├── 404.php / index.php / single.php
├── woocommerce/
│   ├── content-product.php          Product card (grid/rail item)
│   └── content-single-product.php   Gallery + summary 2-col wrapper
└── assets/
    ├── css/site.css              Layout & WooCommerce class-name styling
    ├── js/site.js                Vanilla JS: menus, drawers, wishlist, FAQ…
    └── images/logo.png
```

## Notes for whoever maintains this next

- No build step, no npm/Tailwind — `style.css` is the actual shipped
  CSS. Edit it directly.
- `assets/js/site.js` is one vanilla-JS file, no bundler. Keep it that
  way unless you have a good reason not to (a from-scratch WP theme is
  exactly the place framework-free JS pays off).
- Product cards, single-product layout, and the mini-cart all hook
  into WooCommerce's standard template/hook system rather than
  forking every WooCommerce template — that means WooCommerce core
  updates stay safe to install. If you do need to customize something
  not covered here, copy the relevant file from
  `wp-content/plugins/woocommerce/templates/` into this theme's
  `woocommerce/` folder (WooCommerce prefers the theme's copy
  automatically) rather than editing the plugin directly.
