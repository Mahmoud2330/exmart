# exMart — image slots at a glance

Upload sizes are about 2× the on-screen size so images stay sharp on phones and Retina screens. Measured from the theme at v1.1.37.

| Image | Upload size | Shape | Where to change it |
| --- | --- | --- | --- |
| Homepage hero banners (4 tiles) | 1080 × 1350 px | Portrait 4:5 | Appearance → Customize → Homepage Hero → “Main banner (first) / Offers banner / New in banner / Best sellers banner — image” |
| Category images | 600 × 600 px | Square 1:1 (shown as a circle) | Products → Categories → edit a category → Thumbnail |
| Brand logos | Bundled with the theme (already cropped, transparent PNG, shown in a 104 × 40 px box) | Horizontal logo, trimmed tight | Theme files in `assets/img/brands/{brand-slug}.png`. A logo set on Products → Brands → edit a brand → “Logo attachment ID” overrides the bundled one. |
| Product images | 1200 × 1200 px | Square 1:1 | Products → edit product → Product image (main) and Product gallery (extra photos). Each variation can also have its own image. |
| Shop Page Banner — phone banner (wide) | 2100 × 600 px | Wide strip 21:6 (3.5 : 1) | Appearance → Customize → Shop Page Banner → “Slide 1/2/3 — phone banner (wide)” |
| Shop Page Banner — desktop banner (tall) | 600 × 1500 px | Tall 1 : 2.5 | Appearance → Customize → Shop Page Banner → “Slide 1/2/3 — desktop banner (tall)” |
| Product Page Banner — phone banner (wide) | 2100 × 600 px | Wide strip 21:6 (3.5 : 1) | Appearance → Customize → Product Page Banner → “Slide 1/2/3 — phone banner (wide)” |
| Product Page Banner — desktop banner (tall) | 600 × 1500 px | Tall 1 : 2.5 | Appearance → Customize → Product Page Banner → “Slide 1/2/3 — desktop banner (tall)” |
| exMart site logo | Current file is 1540 × 844 px PNG, transparent | Horizontal ~1.8 : 1 | Not editable in WordPress — it is a theme file (`assets/images/logo.png`). Replacing it is a code change. |
| Site icon (browser tab) | 512 × 512 px | Square 1:1 | Appearance → Customize → Site Identity → Site Icon |

**Making product photos consistent:** Tools → Normalize Images in wp-admin squares every product photo to one canvas size, centres the product with equal margins and removes plain backgrounds. Preview first; originals are kept and can be restored.
