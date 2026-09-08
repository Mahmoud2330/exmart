import { useState } from "react";
import { Link } from "react-router";
import { CheckCircle } from "lucide-react";

function generateOrderId() {
  return `EXM-${Math.floor(100000 + Math.random() * 900000)}`;
}

export default function Confirmation() {
  const [ORDER_ID] = useState(generateOrderId);
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s16)", maxWidth: 600, margin: "0 auto", textAlign: "center" }}>
      <div style={{ display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s5)" }}>
        <div style={{ width: 72, height: 72, borderRadius: "50%", background: "var(--success-bg)", display: "flex", alignItems: "center", justifyContent: "center" }}>
          <CheckCircle size={36} color="var(--success)" />
        </div>
        <div>
          <h1 className="em-h2" style={{ marginBottom: "var(--s2)" }}>Order confirmed!</h1>
          <p className="em-body" style={{ color: "var(--ink-500)" }}>
            Thank you for your order. We will send you an SMS confirmation shortly.
          </p>
        </div>

        <div style={{ width: "100%", border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)", textAlign: "left", display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
          <div style={{ display: "flex", justifyContent: "space-between" }}>
            <span className="em-caption" style={{ color: "var(--ink-500)" }}>Order number</span>
            <span style={{ fontWeight: 700, fontVariantNumeric: "tabular-nums" }}>{ORDER_ID}</span>
          </div>
          <div style={{ display: "flex", justifyContent: "space-between" }}>
            <span className="em-caption" style={{ color: "var(--ink-500)" }}>Estimated delivery</span>
            <span style={{ fontWeight: 600 }}>2–5 working days</span>
          </div>
          <div style={{ display: "flex", justifyContent: "space-between" }}>
            <span className="em-caption" style={{ color: "var(--ink-500)" }}>Payment</span>
            <span style={{ fontWeight: 600 }}>Cash on Delivery</span>
          </div>
        </div>

        {/* Order status pipeline */}
        <div style={{ width: "100%" }}>
          <div className="em-pipeline">
            {["Placed", "Confirmed", "Shipped", "Delivered"].map((s, i) => (
              <div key={s} className={`em-pipeline-step${i === 0 ? " done" : ""}`}>
                <div className={`em-pipeline-dot${i === 0 ? " done" : i === 1 ? " current" : ""}`}>
                  {i === 0 && (
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                      <path d="M2 5l2 2 4-4" stroke="var(--paper)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                    </svg>
                  )}
                </div>
                <span className={`em-pipeline-label${i === 0 ? " done" : i === 1 ? " current" : ""}`}>{s}</span>
              </div>
            ))}
          </div>
        </div>

        {/* 1-click account creation */}
        <div style={{ width: "100%", border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)", background: "var(--ink-50)" }}>
          <p className="em-body-s" style={{ fontWeight: 600, marginBottom: "var(--s2)" }}>Save your order history</p>
          <p className="em-caption" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Create a free account to track this order and manage future purchases.</p>
          <Link to="/account/register" className="em-btn em-btn-primary" style={{ width: "100%", textAlign: "center" }}>Create account</Link>
        </div>

        <div style={{ display: "flex", gap: "var(--s3)", flexWrap: "wrap", justifyContent: "center" }}>
          <Link to="/account/orders" className="em-btn em-btn-secondary">Track order</Link>
          <Link to="/shop" className="em-btn em-btn-ghost">Continue shopping</Link>
        </div>
      </div>
    </div>
  );
}
