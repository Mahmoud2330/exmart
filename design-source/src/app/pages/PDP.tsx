import { useState } from "react";
import { useParams, Link, useNavigate } from "react-router";
import { Heart } from "lucide-react";
import { PRODUCTS, getBrandByName, getRelated, REVIEWS, CATEGORY_NAMES } from "../data";
import { Stars, PriceLockup, TrustLockup, Rail, Breadcrumb, QtyInput, ProductImage, SectionRule } from "../components/em";
import { useStore } from "../store";

export default function PDP() {
  const { slug } = useParams<{ slug: string }>();
  const product = PRODUCTS.find((p) => p.slug === slug);
  const { cart, addToCart, removeFromCart, updateQty, toggleWishlist, isWishlisted } = useStore();
  const nav = useNavigate();

  const [qty, setQty] = useState(1);
  const [activeTab, setActiveTab] = useState("description");
  const [selectedVariant, setSelectedVariant] = useState<string | undefined>(
    product?.variants?.options[0]
  );
  const [activeThumb, setActiveThumb] = useState(0);

  if (!product) {
    return (
      <div className="em-container" style={{ paddingBlock: "var(--s16)", textAlign: "center" }}>
        <h1 className="em-h2" style={{ marginBottom: "var(--s4)" }}>Product not found</h1>
        <Link to="/shop" className="em-btn em-btn-primary">Back to shop</Link>
      </div>
    );
  }

  const brand = getBrandByName(product.brand);
  const related = getRelated(product, 6);
  const wishlisted = isWishlisted(product.id);
  const catName = CATEGORY_NAMES[product.category] ?? product.category;
  const isSale = product.compareAt !== undefined;
  const isOOS = !product.inStock;
  const discount = isSale ? Math.round((1 - product.price / product.compareAt!) * 100) : 0;

  const thumbPlaceholders = [product, ...related.slice(0, 3)];
  const tabs = [
    { id: "description", label: "Description" },
    { id: "specifications", label: "Specifications" },
    { id: "how-to-use", label: "How to Use" },
    ...(product.category === "home-diagnostics" || product.category === "health-protection"
      ? [{ id: "accuracy", label: "Accuracy" }] : []),
    { id: "shipping", label: "Shipping & Returns" },
  ];

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[
        { label: "Home", href: "/" },
        { label: catName, href: `/categories/${product.category}` },
        { label: product.name },
      ]} />

      {/* Main layout */}
      <div style={{ display: "grid", gridTemplateColumns: "1fr", gap: "var(--s8)" }} className="pdp-grid">
        <style>{`@media(min-width:768px){.pdp-grid{grid-template-columns:1fr 1fr !important;}}`}</style>

        {/* Gallery */}
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
          <div className="em-gallery-main">
            <ProductImage product={product} size={320} />
            {isSale && (
              <div style={{ position: "absolute", top: "var(--s3)", left: "var(--s3)" }}>
                <span className="em-badge em-badge-sale">−{discount}%</span>
              </div>
            )}
          </div>
          <div style={{ display: "flex", gap: "var(--s2)", overflowX: "auto" }}>
            {thumbPlaceholders.slice(0, 4).map((p, i) => (
              <button
                key={i}
                className={`em-gallery-thumb${activeThumb === i ? " active" : ""}`}
                onClick={() => setActiveThumb(i)}
                style={{ flexShrink: 0, width: 72, height: 72 }}
                aria-label={`View image ${i + 1}`}
              >
                <ProductImage product={p} size={72} />
              </button>
            ))}
          </div>
        </div>

        {/* Info */}
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s5)" }}>
          {brand && (
            <div style={{ display: "flex", alignItems: "center", gap: "var(--s3)", flexWrap: "wrap" }}>
              <Link to={`/brands/${brand.slug}`} style={{ fontWeight: 700, fontSize: ".9375rem", color: "var(--ink-700)", textDecoration: "none" }}>
                {brand.name}
              </Link>
              {brand && <TrustLockup brand={brand} />}
            </div>
          )}

          <h1 className="em-h3" style={{ color: "var(--ink-900)" }}>{product.name}</h1>

          <div style={{ display: "flex", alignItems: "center", gap: "var(--s3)" }}>
            <Stars rating={product.rating} size={16} />
            <a href="#reviews" style={{ color: "var(--ink-500)", fontSize: ".875rem", textDecoration: "none" }}>
              {product.rating.toFixed(1)} ({product.reviewCount} reviews)
            </a>
          </div>

          <div>
            <PriceLockup price={product.price} compareAt={product.compareAt} large />
          </div>

          {/* Stock */}
          <p className={isOOS ? "em-stock-out" : product.stockQty && product.stockQty < 10 ? "em-stock-low" : "em-stock-in"}>
            {isOOS ? "Out of stock" : product.stockQty && product.stockQty < 10 ? `Only ${product.stockQty} left in stock` : "In stock — ready to ship"}
          </p>

          {/* Variants */}
          {product.variants && (
            <div>
              <p className="em-caption" style={{ color: "var(--ink-500)", marginBottom: "var(--s2)" }}>
                {product.variants.label}: <strong style={{ color: "var(--ink-800)" }}>{selectedVariant}</strong>
              </p>
              <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap" }}>
                {product.variants.options.map((opt) => (
                  <button
                    key={opt}
                    className={`em-variant-pill${selectedVariant === opt ? " active" : ""}`}
                    onClick={() => setSelectedVariant(opt)}
                  >
                    {opt}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Qty + CTA */}
          <div style={{ display: "flex", gap: "var(--s3)", flexWrap: "wrap", alignItems: "center" }}>
            <QtyInput value={qty} onChange={setQty} />
            {(() => {
              const cartItem = cart.find((i) => i.product.id === product.id && i.variant === selectedVariant);
              const cartQty = cartItem?.qty ?? 0;
              if (cartQty > 0) {
                return (
                  <div style={{ flex: 1, minWidth: 180, display: "flex", flexDirection: "column", gap: "var(--s2)" }}>
                    <div style={{ display: "flex", alignItems: "center", border: "2px solid var(--accent-600)", borderRadius: "var(--r-sm)", overflow: "hidden", height: 52 }}>
                      <button
                        onClick={() => cartQty === 1 ? removeFromCart(product.id) : updateQty(product.id, cartQty - 1)}
                        aria-label="Decrease quantity"
                        style={{ flex: 1, height: "100%", background: "none", border: "none", cursor: "pointer", fontSize: "1.5rem", fontWeight: 400, color: "var(--accent-600)", display: "flex", alignItems: "center", justifyContent: "center", transition: "background .12s" }}
                        onMouseEnter={(e) => (e.currentTarget.style.background = "var(--accent-100)")}
                        onMouseLeave={(e) => (e.currentTarget.style.background = "none")}
                      >−</button>
                      <span style={{ minWidth: 48, textAlign: "center", fontSize: "1.125rem", fontWeight: 700, color: "var(--accent-600)", fontVariantNumeric: "tabular-nums", borderInline: "1px solid var(--accent-100)" }}>
                        {cartQty}
                      </span>
                      <button
                        onClick={() => updateQty(product.id, cartQty + 1)}
                        aria-label="Increase quantity"
                        style={{ flex: 1, height: "100%", background: "none", border: "none", cursor: "pointer", fontSize: "1.5rem", fontWeight: 400, color: "var(--accent-600)", display: "flex", alignItems: "center", justifyContent: "center", transition: "background .12s" }}
                        onMouseEnter={(e) => (e.currentTarget.style.background = "var(--accent-100)")}
                        onMouseLeave={(e) => (e.currentTarget.style.background = "none")}
                      >+</button>
                    </div>
                    <button
                      className="em-btn em-btn-lg em-btn-primary"
                      style={{ width: "100%" }}
                      onClick={() => nav("/checkout")}
                    >
                      Buy now
                    </button>
                  </div>
                );
              }
              return (
                <button
                  className={`em-btn em-btn-lg${isOOS ? "" : " em-btn-primary"}`}
                  disabled={isOOS}
                  onClick={() => !isOOS && addToCart(product, qty, selectedVariant)}
                  style={{ flex: 1, minWidth: 180 }}
                >
                  {isOOS ? "Out of stock" : "Add to cart"}
                </button>
              );
            })()}
            <button
              className={`em-wishlist-btn${wishlisted ? " wishlisted" : ""}`}
              onClick={() => toggleWishlist(product.id)}
              aria-label={wishlisted ? "Remove from wishlist" : "Add to wishlist"}
              style={{ minHeight: 52, minWidth: 52 }}
            >
              <Heart size={20} fill={wishlisted ? "currentColor" : "none"} />
            </button>
          </div>

          {/* Trust block */}
          <div style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s4)", display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
            {[
              "Authenticated by exMart — direct from manufacturer",
              "Cash on delivery available",
              "Free returns within 14 days",
              "Delivery 2–5 working days across Egypt",
            ].map((t) => (
              <div key={t} style={{ display: "flex", gap: "var(--s2)", alignItems: "flex-start" }}>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style={{ flexShrink: 0, marginTop: 1 }} aria-hidden="true">
                  <circle cx="8" cy="8" r="7" stroke="var(--ink-700)" strokeWidth="1.5" />
                  <path d="M5 8l2 2 4-4" stroke="var(--ink-700)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
                <span className="em-body-s" style={{ color: "var(--ink-700)" }}>{t}</span>
              </div>
            ))}
          </div>
        </div>
      </div>

      <SectionRule />

      {/* Tabs */}
      <div style={{ marginBlock: "var(--s8)" }}>
        <div className="em-tabs-list" role="tablist">
          {tabs.map((t) => (
            <button
              key={t.id}
              className={`em-tab${activeTab === t.id ? " active" : ""}`}
              role="tab"
              aria-selected={activeTab === t.id}
              onClick={() => setActiveTab(t.id)}
            >
              {t.label}
            </button>
          ))}
        </div>

        <div role="tabpanel" style={{ paddingTop: "var(--s6)", maxWidth: "72ch" }}>
          {activeTab === "description" && (
            <p className="em-body" style={{ color: "var(--ink-700)" }}>{product.description}</p>
          )}
          {activeTab === "specifications" && (
            <table style={{ borderCollapse: "collapse", width: "100%" }}>
              <tbody>
                {Object.entries(product.specifications).map(([k, v]) => (
                  <tr key={k} style={{ borderBottom: "1px solid var(--ink-100)" }}>
                    <td style={{ padding: "var(--s3) var(--s4) var(--s3) 0", fontWeight: 500, color: "var(--ink-700)", width: "40%", fontSize: ".875rem" }}>{k}</td>
                    <td style={{ padding: "var(--s3) 0", color: "var(--ink-800)", fontSize: ".875rem" }}>{v}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
          {activeTab === "how-to-use" && (
            <p className="em-body" style={{ color: "var(--ink-700)" }}>{product.howToUse}</p>
          )}
          {activeTab === "accuracy" && (
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
              <p className="em-body" style={{ color: "var(--ink-700)" }}>
                This device provides readings for informational purposes only. It is not intended to diagnose, treat, cure, or prevent any disease or health condition.
              </p>
              <p className="em-body" style={{ color: "var(--ink-700)" }}>
                Always consult a qualified healthcare professional for clinical decisions. Readings may vary based on usage technique. Refer to the included manual for accuracy specifications and limitations.
              </p>
            </div>
          )}
          {activeTab === "shipping" && (
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
              <p className="em-body" style={{ color: "var(--ink-700)" }}>Standard delivery: 2–5 working days across Egypt. Free shipping on orders over EGP 300.</p>
              <p className="em-body" style={{ color: "var(--ink-700)" }}>Returns accepted within 14 days of delivery. Item must be unused and in original packaging. Contact us at hello@exmart.eg to initiate a return.</p>
            </div>
          )}
        </div>
      </div>

      <SectionRule />

      {/* Reviews */}
      <div id="reviews" style={{ marginBlock: "var(--s8)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s6)" }}>Customer Reviews</h2>
        <div style={{ display: "grid", gridTemplateColumns: "1fr", gap: "var(--s8)" }} className="reviews-grid">
          <style>{`@media(min-width:768px){.reviews-grid{grid-template-columns:200px 1fr !important;}}`}</style>

          {/* Score summary */}
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)", alignItems: "flex-start" }}>
            <div style={{ display: "flex", alignItems: "baseline", gap: "var(--s2)" }}>
              <span style={{ fontSize: "3rem", fontWeight: 700, lineHeight: 1 }}>{product.rating.toFixed(1)}</span>
              <span style={{ color: "var(--ink-500)" }}>/ 5</span>
            </div>
            <Stars rating={product.rating} size={18} />
            <p className="em-caption" style={{ color: "var(--ink-400)" }}>Based on {product.reviewCount} reviews</p>
            {[5, 4, 3, 2, 1].map((n) => {
              const pct = n === 5 ? 68 : n === 4 ? 20 : n === 3 ? 8 : n === 2 ? 2 : 2;
              return (
                <div key={n} className="em-review-bar" style={{ width: "100%" }}>
                  <span className="em-caption" style={{ minWidth: 12 }}>{n}</span>
                  <div className="em-review-bar-track">
                    <div className="em-review-bar-fill" style={{ width: `${pct}%` }} />
                  </div>
                  <span className="em-caption" style={{ color: "var(--ink-400)", minWidth: 28 }}>{pct}%</span>
                </div>
              );
            })}
          </div>

          {/* Review list */}
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--s6)" }}>
            {REVIEWS.map((r) => (
              <div key={r.author} style={{ paddingBottom: "var(--s6)", borderBottom: "1px solid var(--ink-100)" }}>
                <div style={{ display: "flex", alignItems: "center", gap: "var(--s3)", marginBottom: "var(--s2)" }}>
                  <Stars rating={r.rating} size={14} />
                  <span className="em-caption" style={{ color: "var(--ink-700)", fontWeight: 600 }}>{r.author}</span>
                  <span className="em-caption" style={{ color: "var(--ink-400)" }}>{r.date}</span>
                </div>
                <p className="em-body-s" style={{ color: "var(--ink-700)" }}>{r.text}</p>
              </div>
            ))}
          </div>
        </div>
      </div>

      <SectionRule />

      {/* Related */}
      {related.length > 0 && (
        <div style={{ marginTop: "var(--s8)" }}>
          <Rail title="You may also like" products={related} viewAllHref={`/categories/${product.category}`} />
        </div>
      )}
    </div>
  );
}
