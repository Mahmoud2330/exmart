import { Link } from "react-router";
import { Heart } from "lucide-react";
import type { Product, Brand } from "../../data";
import { useStore } from "../../store";

// ── Category → product image map ─────────────────────────
const CATEGORY_IMAGES: Record<string, string> = {
  "personal-care":    "https://images.unsplash.com/photo-1599210822756-5c4f400b90ac?w=400&fit=crop&auto=format",
  "hair-care":        "https://images.unsplash.com/photo-1701992678972-d5a053ad0fb0?w=400&fit=crop&auto=format",
  "skin-care":        "https://images.unsplash.com/photo-1748543668646-e81cda0890f3?w=400&fit=crop&auto=format",
  "baby-care":        "https://images.unsplash.com/photo-1705155726507-8e1b9119349b?w=400&fit=crop&auto=format",
  "feminine-care":    "https://images.unsplash.com/photo-1748543668676-ea8241cb3886?w=400&fit=crop&auto=format",
  "home-care":        "https://images.unsplash.com/photo-1550963295-019d8a8a61c5?w=400&fit=crop&auto=format",
  "home-diagnostics": "https://images.unsplash.com/photo-1580281658460-2d1114999983?w=400&fit=crop&auto=format",
  "health-protection": "https://images.unsplash.com/photo-1584744982491-665216d95f8b?w=400&fit=crop&auto=format",
  "disinfectants":    "https://images.unsplash.com/photo-1716625300692-8d5f0341b1c5?w=400&fit=crop&auto=format",
};

// ── ProductImage ─────────────────────────────────────────
export function ProductImage({
  product,
  size = 80,
  className = "",
}: {
  product: Product;
  size?: number;
  className?: string;
}) {
  const src = CATEGORY_IMAGES[product.category] ?? CATEGORY_IMAGES["personal-care"];
  return (
    <img
      src={src}
      alt={product.name}
      className={className}
      width={size}
      height={size}
      loading="lazy"
      decoding="async"
      style={{ width: "100%", height: "100%", objectFit: "cover", display: "block" }}
    />
  );
}

// ── Stars ────────────────────────────────────────────────
export function Stars({ rating, size = 12 }: { rating: number; size?: number }) {
  const full = Math.round(rating);
  return (
    <span className="em-stars" aria-label={`${rating} out of 5 stars`}>
      {Array.from({ length: 5 }).map((_, i) => (
        <svg key={i} width={size} height={size} viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <path
            d="M8 1.5l1.545 3.13 3.455.502-2.5 2.436.59 3.432L8 9.25l-3.09 1.75.59-3.432L3 5.132l3.455-.502L8 1.5z"
            fill={i < full ? "var(--ink-900)" : "var(--ink-200)"}
          />
        </svg>
      ))}
    </span>
  );
}

// ── PriceLockup ──────────────────────────────────────────
export function PriceLockup({
  price,
  compareAt,
  large = false,
}: {
  price: number;
  compareAt?: number;
  large?: boolean;
}) {
  const isSale = compareAt !== undefined && compareAt > price;
  return (
    <span className="em-price-lockup">
      <span className="em-price-currency">EGP</span>
      <span className={`em-price-number${large ? " em-price-number-lg" : ""}${isSale ? " sale" : ""}`}>
        {price.toFixed(2)}
      </span>
      {isSale && <span className="em-price-compare">{compareAt!.toFixed(2)}</span>}
    </span>
  );
}

// ── TrustLockup ──────────────────────────────────────────
export function TrustLockup({ brand }: { brand: Brand }) {
  const text =
    brand.type === "distributed"
      ? "Official & sole distributor in Egypt"
      : "exMart exclusive · our own brand";
  return (
    <span className="em-trust">
      <svg className="em-trust-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
        <circle cx="8" cy="8" r="7" stroke="currentColor" strokeWidth="1.5" />
        <path d="M5 8l2 2 4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
      </svg>
      <span className="em-trust-text">{text}</span>
    </span>
  );
}

// ── ProductCard ──────────────────────────────────────────
export function ProductCard({ product }: { product: Product }) {
  const { cart, addToCart, removeFromCart, updateQty, toggleWishlist, isWishlisted } = useStore();
  const wishlisted = isWishlisted(product.id);
  const isSale = product.compareAt !== undefined;
  const oos = !product.inStock;
  const cartItem = cart.find((i) => i.product.id === product.id);
  const qty = cartItem?.qty ?? 0;

  return (
    <article className="em-card">
      <Link to={`/products/${product.slug}`} className={`em-card-img-wrap${oos ? " oos" : ""}`} style={{ display: "block", textDecoration: "none" }}>
        <div className="em-card-placeholder">
          <ProductImage product={product} size={120} />
        </div>
        <div className="em-card-badge-pos">
          {isSale && <span className="em-badge em-badge-sale">Sale</span>}
          {product.isNew && <span className="em-badge em-badge-ink">New</span>}
          {product.isBestSeller && !isSale && !product.isNew && (
            <span className="em-badge em-badge-accent">Best Seller</span>
          )}
        </div>
      </Link>
      <div className="em-card-body">
        <span className="em-caption" style={{ color: "var(--ink-500)" }}>{product.brand}</span>
        <Link to={`/products/${product.slug}`} style={{ textDecoration: "none" }}>
          <p className="em-card-title">{product.name}</p>
        </Link>
        <div style={{ display: "flex", alignItems: "center" }}>
          <Stars rating={product.rating} />
          <span className="em-rating-count">({product.reviewCount})</span>
        </div>
        <PriceLockup price={product.price} compareAt={product.compareAt} />
        <div className="em-card-actions">
          <button
            className={`em-wishlist-btn${wishlisted ? " wishlisted" : ""}`}
            onClick={() => toggleWishlist(product.id)}
            aria-label={wishlisted ? "Remove from wishlist" : "Add to wishlist"}
          >
            <Heart size={16} fill={wishlisted ? "currentColor" : "none"} />
          </button>
          {qty > 0 ? (
            <div style={{ flex: 1, display: "flex", alignItems: "center", border: "1px solid var(--ink-900)", borderRadius: "var(--r-sm)", overflow: "hidden", height: 44 }}>
              <button
                onClick={() => qty === 1 ? removeFromCart(product.id) : updateQty(product.id, qty - 1)}
                aria-label="Decrease quantity"
                style={{ flex: 1, height: "100%", background: "none", border: "none", cursor: "pointer", fontSize: "1.25rem", fontWeight: 400, color: "var(--ink-700)", display: "flex", alignItems: "center", justifyContent: "center", transition: "background .12s" }}
                onMouseEnter={(e) => (e.currentTarget.style.background = "var(--ink-50)")}
                onMouseLeave={(e) => (e.currentTarget.style.background = "none")}
              >
                −
              </button>
              <span style={{ minWidth: 32, textAlign: "center", fontSize: ".9375rem", fontWeight: 700, color: "var(--ink-900)", fontVariantNumeric: "tabular-nums", borderInline: "1px solid var(--ink-200)" }}>
                {qty}
              </span>
              <button
                onClick={() => updateQty(product.id, qty + 1)}
                aria-label="Increase quantity"
                style={{ flex: 1, height: "100%", background: "none", border: "none", cursor: "pointer", fontSize: "1.25rem", fontWeight: 400, color: "var(--ink-700)", display: "flex", alignItems: "center", justifyContent: "center", transition: "background .12s" }}
                onMouseEnter={(e) => (e.currentTarget.style.background = "var(--ink-50)")}
                onMouseLeave={(e) => (e.currentTarget.style.background = "none")}
              >
                +
              </button>
            </div>
          ) : (
            <button
              className={`em-btn ${oos ? "" : "em-btn-secondary"}`}
              disabled={oos}
              style={{ flex: 1, fontSize: ".8125rem" }}
              onClick={() => !oos && addToCart(product)}
              aria-label={oos ? `Notify me when ${product.name} is back in stock` : `Add ${product.name} to cart`}
            >
              {oos ? "Notify me" : "Add to cart"}
            </button>
          )}
        </div>
      </div>
    </article>
  );
}

// ── Rail ─────────────────────────────────────────────────
export function Rail({
  title,
  products,
  viewAllHref,
}: {
  title: string;
  products: Product[];
  viewAllHref: string;
}) {
  return (
    <section>
      <div className="em-rail-header">
        <h2 className="em-h3">{title}</h2>
        <Link to={viewAllHref} className="em-view-all">View all →</Link>
      </div>
      <div className="em-rail-track">
        {products.map((p) => (
          <div key={p.id} className="em-rail-card">
            <ProductCard product={p} />
          </div>
        ))}
      </div>
    </section>
  );
}

// ── Breadcrumb ───────────────────────────────────────────
export function Breadcrumb({ crumbs }: { crumbs: { label: string; href?: string }[] }) {
  return (
    <nav className="em-breadcrumb" aria-label="Breadcrumb">
      {crumbs.map((c, i) => (
        <span key={i} style={{ display: "flex", alignItems: "center", gap: "var(--s2)" }}>
          {i > 0 && <span className="em-breadcrumb-sep" aria-hidden="true">/</span>}
          {c.href ? (
            <Link to={c.href} className="em-breadcrumb-item">{c.label}</Link>
          ) : (
            <span className="em-breadcrumb-current">{c.label}</span>
          )}
        </span>
      ))}
    </nav>
  );
}

// ── Pagination ───────────────────────────────────────────
export function Pagination({
  page,
  total,
  perPage,
  onChange,
}: {
  page: number;
  total: number;
  perPage: number;
  onChange: (p: number) => void;
}) {
  const pages = Math.ceil(total / perPage);
  if (pages <= 1) return null;
  return (
    <nav className="em-pagination" aria-label="Pagination">
      <button
        className="em-page-btn"
        onClick={() => onChange(page - 1)}
        disabled={page === 1}
        aria-label="Previous page"
      >
        ←
      </button>
      {Array.from({ length: pages }).map((_, i) => (
        <button
          key={i}
          className={`em-page-btn${page === i + 1 ? " active" : ""}`}
          onClick={() => onChange(i + 1)}
          aria-current={page === i + 1 ? "page" : undefined}
        >
          {i + 1}
        </button>
      ))}
      <button
        className="em-page-btn"
        onClick={() => onChange(page + 1)}
        disabled={page === pages}
        aria-label="Next page"
      >
        →
      </button>
    </nav>
  );
}

// ── QtyInput ─────────────────────────────────────────────
export function QtyInput({
  value,
  min = 1,
  max = 99,
  onChange,
}: {
  value: number;
  min?: number;
  max?: number;
  onChange: (v: number) => void;
}) {
  return (
    <div className="em-qty" role="group" aria-label="Quantity">
      <button className="em-qty-btn" onClick={() => onChange(value - 1)} disabled={value <= min} aria-label="Decrease">−</button>
      <span className="em-qty-val" aria-live="polite">{value}</span>
      <button className="em-qty-btn" onClick={() => onChange(value + 1)} disabled={value >= max} aria-label="Increase">+</button>
    </div>
  );
}

// ── EmInput ──────────────────────────────────────────────
export function EmInput({
  label,
  id,
  error,
  helper,
  ...props
}: React.InputHTMLAttributes<HTMLInputElement> & {
  label: string;
  id: string;
  error?: string;
  helper?: string;
}) {
  return (
    <div className="em-input-wrap">
      <label className="em-input-label" htmlFor={id}>{label}</label>
      <input id={id} className={`em-input${error ? " error" : ""}`} {...props} />
      {error && <span className="em-helper em-helper-error">{error}</span>}
      {helper && !error && <span className="em-helper">{helper}</span>}
    </div>
  );
}

// ── EmSelect ─────────────────────────────────────────────
export function EmSelect({
  label,
  id,
  options,
  ...props
}: React.SelectHTMLAttributes<HTMLSelectElement> & {
  label: string;
  id: string;
  options: string[];
}) {
  return (
    <div className="em-input-wrap">
      <label className="em-input-label" htmlFor={id}>{label}</label>
      <select id={id} className="em-input" style={{ appearance: "none" }} {...props}>
        {options.map((o) => (
          <option key={o} value={o}>{o}</option>
        ))}
      </select>
    </div>
  );
}

// ── SectionRule ──────────────────────────────────────────
export function SectionRule() {
  return <hr className="em-rule" aria-hidden="true" />;
}

// ── EmptyState ───────────────────────────────────────────
export function EmptyState({
  title,
  body,
  cta,
  href,
}: {
  title: string;
  body: string;
  cta: string;
  href: string;
}) {
  return (
    <div style={{ textAlign: "center", paddingBlock: "var(--s20)", display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s4)" }}>
      <div style={{ width: 64, height: 64, borderRadius: "50%", background: "var(--ink-100)", display: "flex", alignItems: "center", justifyContent: "center" }}>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1z" stroke="var(--ink-400)" strokeWidth="1.5" />
          <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="var(--ink-400)" strokeWidth="1.5" />
        </svg>
      </div>
      <h2 className="em-h3" style={{ color: "var(--ink-800)" }}>{title}</h2>
      <p className="em-body-s" style={{ color: "var(--ink-500)", maxWidth: "40ch" }}>{body}</p>
      <Link to={href} className="em-btn em-btn-primary">{cta}</Link>
    </div>
  );
}
