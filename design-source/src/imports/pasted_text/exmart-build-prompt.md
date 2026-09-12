# exMart — Compact Build Prompt (frontend, English/LTR)

Rules: (1) Build in the numbered STEPS, one per message; keep prior work; never refactor tokens. (2) Obey LAYOUT LAW — responsive rules, never fixed px on layout.

LAYOUT LAW: mobile-first from 360px; no horizontal scroll except carousels. `*{box-sizing:border-box}`; `img{max-width:100%}`. No fixed width/height on layout — use max-width/%/fr/minmax/clamp/min. Media=aspect-ratio, never fixed height. Space via gap. Grids: `grid-template-columns:repeat(auto-fill,minmax(min(100%,220px),1fr));gap:clamp(16px,2vw,24px)`. 2-col (PLP/checkout) collapses to 1-col <1024px; sidebar→mobile drawer. Rails: `overflow-x:auto;scroll-snap-type:x mandatory`, cards `scroll-snap-align:start;width:clamp(160px,45vw,240px)`. Container: `width:100%;max-width:1280px;margin-inline:auto;padding-inline:clamp(16px,4vw,32px)`. Touch ≥44px via padding+min-height. Focus ring all interactive: `outline:2px solid var(--accent-600);outline-offset:2px`.

PROJECT: exMart Egypt — health/hygiene/diagnostics/personal-care store, 60 products, English LTR. Minimal; achromatic chrome (product imagery/logos carry color); structure=1px hairlines+whitespace, no shadows except overlays (menu/mini-cart/modal).

BRANDS (2 types → 2 trust lockups; never label house as distributed):
- Distributed (official & sole distributor in Egypt): Diversey, Grace, Oview, SureCheck → "Official & sole distributor in Egypt".
- House (own): Qualita, Vodlia, Eliv, Verve → "exMart exclusive · our own brand".

TOKENS (define in Step 1 as CSS vars):
```css
:root{
--paper:#FFFFFF;--ink-50:#F8F9FA;--ink-100:#F1F2F4;--ink-200:#E4E6EA;--ink-300:#C9CBD1;--ink-400:#9598A0;--ink-500:#6B6E76;--ink-700:#2E2F33;--ink-800:#1A1B1E;--ink-900:#0B0B0C;
--accent-100:#E9EDFF;--accent-600:#2A44E8;--accent-700:#1E33B8;
--success:#157F4B;--success-bg:#E7F6EE;--error:#C62828;--error-bg:#FDEDED;--warning:#B7791F;--warning-bg:#FBF3E4;--sale:#E5341C;--sale-bg:#FDECE9;
--r-sm:8px;--r-md:12px;--r-lg:16px;--r-full:999px;
--shadow-menu:0 8px 24px rgba(11,11,12,.10);--shadow-modal:0 16px 48px rgba(11,11,12,.14);
--s1:.25rem;--s2:.5rem;--s3:.75rem;--s4:1rem;--s5:1.25rem;--s6:1.5rem;--s8:2rem;--s10:2.5rem;--s12:3rem;--s16:4rem;--s20:5rem;--s24:6rem;
--font:"Hanken Grotesk",system-ui,sans-serif;
}
```
COLOR RULES: achromatic chrome; 1 accent/view. Primary btn=ink-900/paper. accent-600 fill only for one action/view (Place order). Borders/hairlines=ink-200. Text ink-700/800/900; secondary ink-500; never ink-400 for essential text.

TYPE (relative units only — no px letter-spacing/line-height; headings 600):
h1 clamp(1.75rem,1.2rem+2.4vw,2.25rem)/1.1 ls-.01em · h2 clamp(1.5rem,1.2rem+1.4vw,1.75rem)/1.15 ls-.01em · h3 1.375rem/1.25 · h4 1.125rem/1.35 w500 · body 1rem/1.5 · body-s .875rem/1.45 · caption .8125rem/1.3 w500 · overline .75rem/1.3 ls.08em uppercase w600 · button .9375rem/1 ls.01em w600. Prices/counts: tabular-nums.

COMPONENTS (responsive; no fixed widths):
- Button: min-height 44 (lg 52 via padding), padding-inline s5, r-sm. Primary ink-900/paper (hover ink-800); Secondary paper+1px ink-300 (hover ink-50); Ghost ink-700 (hover ink-100); Accent accent-600; Disabled ink-100/ink-400.
- Input/select: min-height 48, padding-inline s4, 1px ink-200, r-sm; label caption above; focus border accent-600+ring; error border+helper.
- Product card: paper,1px ink-200,r-md,overflow hidden. Img aspect 1/1,width 100%,object-fit contain,bg ink-50. Body padding s4,flex-col gap s2: brand caption ink-500 · title body-s 2-line clamp · rating(filled ink-900/empty ink-200)+count · PRICE LOCKUP · actions: Add to cart(secondary,full-width)+wishlist icon. Sale badge top-start on img. OOS: img opacity .6, add disabled, "Notify me".
- Price lockup: inline-flex baseline gap 6px. "EGP" overline ink-500 before number · number tabular-nums w600 ink-900 · compare-at(sale) body-s ink-400 line-through after · sale badge sale/sale-bg.
- Trust lockup: 16px check icon+caption, text per brand type.
- Badge/chip: overline case,r-full,pad 2px 8px,tint bg. Rating stars filled ink-900/empty ink-200.
- Rail: header(h2+"View all →")+snap track.

SITEMAP (build all; 1 H1/page):
Home · Shop(all; /shop, not a category) · Category Index→PLP(Home Care nests) · Brand Index(2 groups)→Brand Landing · Collections: Wipes/Makeup Removal/Disinfectants & Sanitizers/Offers/New/Best Sellers · PDP · Search · Cart→Checkout→Confirmation→Tracking · Account(Login/Register·Orders·Addresses·Wishlist·Profile) · Wishlist · Info: About·Contact·Shipping & Returns·Payment·Privacy & Terms·FAQ.
Categories (8 flat; only Home Care nests): Personal Care·Hair Care·Skin Care·Baby Care·Feminine Care·Home Care·Home Diagnostics·Health & Protection.
Home Care subs: Surface & Floor·Kitchen·Laundry & Detergents·Air Care·Restroom·Disinfectants & Sanitizers.
No "Uncategorized"/"All" categories; each product in exactly 1 category; Wipes/Makeup Removal/Disinfectants=collections+filters.

PAGES (obey Layout Law):
- Chrome: announcement bar(dismissible,ink-900) → header(min-height 72 desktop/auto mobile; logo start · centered search+autocomplete · end: wishlist,account,cart→mini-cart) → nav w/ Categories+Brands mega-menus → footer(4 cols: category·brand·service·about+newsletter; strip: real Egyptian address,phone,email,trust lockups,payment icons).
- Home(stack gap s24/s16 mobile): 1 Hero(trust headline+CTA)·2 trust strip(authentic·official distributor·COD·nationwide)·3 Shop by Category tiles(aspect 4/3)·4 Shop by Brand on ink-50 band(cards aspect 3/2,grayscale→color,correct lockup)·5 Best Sellers rail·6 Offers rail·7 New Arrivals rail·8 real reviews or trust badges·9 newsletter/WhatsApp. H1=hero.
- PLP: breadcrumb·H1=category·subcategory chips·filters(sidebar→mobile drawer: price,brand,type,availability,rating)·sort dropdown·grid(auto-fill)·pagination·count+removable filter chips·empty state.
- Brand Index: H1·2 groups "Brands we distribute"/"exMart brands"·brand search·cards w/ correct lockup.
- Brand Landing: hero+authority band(correct lockup)·products w/ same filters/sort/pagination·claim-safe copy.
- PDP: breadcrumb·brand link·H1 title·gallery(main img aspect 1/1+thumbnails+zoom)·rating summary·price lockup large·stock line·variant pills·qty stepper·Add to cart(primary,full-width)+wishlist·trust block·tabs(Description,Specifications,How to use,Accuracy[factual,no diagnostic claims],Shipping & Returns)·reviews(score,distribution,list,write-a-review)·related rail.
- Cart+mini-cart: mini-cart width min(400px,100vw) slide from inline-end; cart=items+summary card(subtotal,shipping,total,coupon,COD note,checkout).
- Checkout(guest ok): grid→1col; form+sticky summary. contact→Egyptian address(governorate/city)→shipping(real cost)→payment(COD first,then Fawry/Meeza/card/Instapay)→Place order(accent-600). Confirmation: order#,summary,ETA,1-click account.
- Account: orders&tracking(Placed→Confirmed→Shipped→Delivered),addresses,wishlist,profile.
- Search: query echo·same filters/sort/pagination·zero-results(suggestions+popular categories/brands).
- About: real story(distributor+own brands),no placeholder.
- Contact: real Egyptian address+map,phone/email/WhatsApp/hours,form w/ distinct subjects(Order issue,Product inquiry,Authenticity/warranty,Partnership,Complaint,Other).
Content: no placeholder/Lorem; real company info; no fabricated reviews; health copy factual/claim-safe; 1 H1/page; alt text all images.

STEPS (one per message; keep prior; don't refactor tokens):
1 Foundation: reset, :root tokens, base type classes, .container, Layout Law utilities; output style-guide page(colors,type,spacing).
2 Core components: button,input,badge,rating,price lockup,trust lockup,product card → components page; verify no fixed widths/overflow at 360.
3 Chrome: header+search+nav+mega-menus+mini-cart+footer.
4 Home (9 modules).
5 Category Index+PLP (filters drawer,sort,pagination,grid).
6 PDP (gallery+zoom+tabs+reviews).
7 Brand Index+Landing.
8 Cart+mini-cart+Checkout+Confirmation.
9 Account+Search+Wishlist.
10 About+Contact+policy/FAQ.
11 QA: test 360/768/1280; 1 H1/page,focus rings,no h-scroll,no clipped text,tokens everywhere.

Start Step 1 only.