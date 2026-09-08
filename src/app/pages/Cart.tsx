import { Link } from "react-router";
import { X } from "lucide-react";
import { useStore } from "../store";
import { Breadcrumb, EmptyState, QtyInput } from "../components/em";
import { useState } from "react";

const SHIPPING_THRESHOLD = 300;
const SHIPPING_COST = 35;

export default function Cart() {
  const { cart, removeFromCart, updateQty, cartTotal } = useStore();
  const [coupon, setCoupon] = useState("");
  const [couponApplied, setCouponApplied] = useState(false);

  const shipping = cartTotal >= SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
  const discount = couponApplied ? Math.round(cartTotal * 0.1) : 0;
  const total = cartTotal - discount + shipping;

  if (cart.length === 0) {
    return (
      <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
        <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Cart" }]} />
        <EmptyState title="Your cart is empty" body="Add products from the shop and they will appear here." cta="Continue shopping" href="/shop" />
      </div>
    );
  }

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Cart" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>Your Cart</h1>

      <div className="em-two-col" style={{ alignItems: "start" }}>
        {/* Items */}
        <div style={{ display: "flex", flexDirection: "column", gap: 0 }}>
          {/* Header row */}
          <div style={{ display: "grid", gridTemplateColumns: "1fr 120px 80px 40px", gap: "var(--s4)", paddingBottom: "var(--s3)", borderBottom: "1px solid var(--ink-200)" }}>
            {["Product", "Qty", "Price", ""].map((h) => (
              <span key={h} className="em-overline" style={{ color: "var(--ink-500)" }}>{h}</span>
            ))}
          </div>

          {cart.map((item) => (
            <div key={item.product.id} style={{ display: "grid", gridTemplateColumns: "1fr 120px 80px 40px", gap: "var(--s4)", paddingBlock: "var(--s5)", borderBottom: "1px solid var(--ink-100)", alignItems: "center" }}>
              {/* Product */}
              <div style={{ display: "flex", gap: "var(--s3)" }}>
                <Link to={`/products/${item.product.slug}`}
                  style={{ flexShrink: 0, width: 64, height: 64, background: "var(--ink-50)", borderRadius: "var(--r-sm)", display: "flex", alignItems: "center", justifyContent: "center", border: "1px solid var(--ink-200)", overflow: "hidden" }}>
                  <svg width="64" height="64" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <rect width="64" height="64" fill={item.product.color + "22"} />
                    <text x="32" y="42" textAnchor="middle" fontFamily="system-ui" fontWeight="700" fontSize="20" fill={item.product.color}>{item.product.brand[0]}</text>
                  </svg>
                </Link>
                <div>
                  <Link to={`/products/${item.product.slug}`} style={{ fontWeight: 500, fontSize: ".875rem", color: "var(--ink-800)", textDecoration: "none", display: "-webkit-box", WebkitLineClamp: 2, WebkitBoxOrient: "vertical", overflow: "hidden" }}>
                    {item.product.name}
                  </Link>
                  <p className="em-caption" style={{ color: "var(--ink-500)", marginTop: 4 }}>{item.product.brand}</p>
                  {item.variant && <p className="em-caption" style={{ color: "var(--ink-400)" }}>{item.variant}</p>}
                </div>
              </div>

              {/* Qty */}
              <QtyInput value={item.qty} onChange={(v) => updateQty(item.product.id, v)} />

              {/* Price */}
              <span style={{ fontWeight: 600, fontSize: ".9375rem", fontVariantNumeric: "tabular-nums", color: "var(--ink-900)" }}>
                EGP {(item.product.price * item.qty).toFixed(2)}
              </span>

              {/* Remove */}
              <button className="em-btn-icon" onClick={() => removeFromCart(item.product.id)} aria-label={`Remove ${item.product.name}`} style={{ color: "var(--ink-400)", minWidth: 36, minHeight: 36 }}>
                <X size={16} />
              </button>
            </div>
          ))}
        </div>

        {/* Summary */}
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s4)", position: "sticky", top: 140 }}>
          <div className="em-summary-card">
            <h2 className="em-h4" style={{ marginBottom: "var(--s2)" }}>Order Summary</h2>
            <div className="em-summary-row">
              <span className="em-summary-label">Subtotal ({cart.reduce((s, i) => s + i.qty, 0)} items)</span>
              <span className="em-summary-value">EGP {cartTotal.toFixed(2)}</span>
            </div>
            {couponApplied && (
              <div className="em-summary-row">
                <span className="em-summary-label" style={{ color: "var(--success)" }}>Discount (10%)</span>
                <span className="em-summary-value" style={{ color: "var(--success)" }}>−EGP {discount.toFixed(2)}</span>
              </div>
            )}
            <div className="em-summary-row">
              <span className="em-summary-label">Shipping</span>
              <span className="em-summary-value">{shipping === 0 ? "Free" : `EGP ${shipping.toFixed(2)}`}</span>
            </div>
            {cartTotal < SHIPPING_THRESHOLD && (
              <p className="em-caption" style={{ color: "var(--accent-600)" }}>
                Add EGP {(SHIPPING_THRESHOLD - cartTotal).toFixed(2)} more for free shipping
              </p>
            )}
            <hr className="em-rule" />
            <div className="em-summary-row em-summary-total">
              <span className="em-summary-label">Total</span>
              <span className="em-summary-value">EGP {total.toFixed(2)}</span>
            </div>
          </div>

          {/* Coupon */}
          <div style={{ display: "flex", gap: "var(--s2)" }}>
            <input className="em-input" placeholder="Coupon code" value={coupon} onChange={(e) => setCoupon(e.target.value)} style={{ flex: 1, minHeight: 44 }} aria-label="Coupon code" />
            <button className="em-btn em-btn-secondary" onClick={() => { if (coupon.trim()) setCouponApplied(true); }} disabled={couponApplied}>
              Apply
            </button>
          </div>
          {couponApplied && <p className="em-caption" style={{ color: "var(--success)" }}>Coupon applied — 10% off!</p>}

          {/* COD note */}
          <div style={{ display: "flex", gap: "var(--s2)", alignItems: "center", padding: "var(--s3)", background: "var(--ink-50)", borderRadius: "var(--r-sm)", border: "1px solid var(--ink-200)" }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect x="2" y="5" width="20" height="14" rx="2" stroke="var(--ink-600)" strokeWidth="1.5" />
              <path d="M2 10h20" stroke="var(--ink-600)" strokeWidth="1.5" />
            </svg>
            <span className="em-caption" style={{ color: "var(--ink-600)" }}>Cash on delivery available at checkout</span>
          </div>

          <Link to="/checkout" className="em-btn em-btn-accent em-btn-lg" style={{ textAlign: "center" }}>
            Proceed to checkout
          </Link>
          <Link to="/shop" className="em-btn em-btn-ghost" style={{ textAlign: "center" }}>
            Continue shopping
          </Link>
        </div>
      </div>
    </div>
  );
}
