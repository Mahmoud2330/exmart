import { useState } from "react";
import { useRouteError, isRouteErrorResponse } from "react-router";
import { Breadcrumb } from "../components/em";

// ── Shipping & Returns ───────────────────────────────────
export function ShippingReturns() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)", maxWidth: 760 }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Shipping & Returns" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>Shipping & Returns</h1>

      <section style={{ marginBottom: "var(--s8)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s4)" }}>Delivery</h2>
        <table style={{ borderCollapse: "collapse", width: "100%" }}>
          <thead>
            <tr>
              {["Zone", "Estimated delivery", "Cost"].map((h) => (
                <th key={h} style={{ padding: "var(--s3) var(--s4)", textAlign: "left", borderBottom: "2px solid var(--ink-200)", fontSize: ".8125rem", fontWeight: 600, color: "var(--ink-500)", textTransform: "uppercase", letterSpacing: ".08em" }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {[
              ["Greater Cairo & Giza", "1–2 working days", "EGP 25 (free over EGP 300)"],
              ["Alexandria & Delta", "2–3 working days", "EGP 35 (free over EGP 300)"],
              ["Upper Egypt & Sa'id", "3–5 working days", "EGP 45 (free over EGP 500)"],
              ["Sinai, Red Sea & Frontier", "4–6 working days", "EGP 55"],
            ].map(([zone, eta, cost]) => (
              <tr key={zone} style={{ borderBottom: "1px solid var(--ink-100)" }}>
                <td style={{ padding: "var(--s3) var(--s4)", fontSize: ".875rem", color: "var(--ink-700)" }}>{zone}</td>
                <td style={{ padding: "var(--s3) var(--s4)", fontSize: ".875rem", color: "var(--ink-700)" }}>{eta}</td>
                <td style={{ padding: "var(--s3) var(--s4)", fontSize: ".875rem", color: "var(--ink-700)" }}>{cost}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </section>

      <section style={{ marginBottom: "var(--s8)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s4)" }}>Returns & Refunds</h2>
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
          {[
            "We accept returns within 14 days of delivery for unused items in original, sealed packaging.",
            "To initiate a return, email hello@exmart.eg with your order number and reason. We will send a prepaid return label for orders within Greater Cairo.",
            "Refunds are processed within 3–5 working days after we receive and inspect the item.",
            "Personalised, perishable, or intimate hygiene products (opened) are excluded from the return policy for health and safety reasons.",
            "If you receive a damaged or incorrect product, contact us within 48 hours with a photo and we will arrange a replacement at no cost.",
          ].map((t) => (
            <div key={t} style={{ display: "flex", gap: "var(--s3)", alignItems: "flex-start" }}>
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style={{ flexShrink: 0, marginTop: 2 }} aria-hidden="true">
                <circle cx="8" cy="8" r="7" stroke="var(--ink-700)" strokeWidth="1.5" />
                <path d="M5 8l2 2 4-4" stroke="var(--ink-700)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              <p className="em-body-s" style={{ color: "var(--ink-700)" }}>{t}</p>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}

// ── Payment ──────────────────────────────────────────────
export function Payment() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)", maxWidth: 760 }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Payment Methods" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>Payment Methods</h1>
      <div style={{ display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
        {[
          { name: "Cash on Delivery (COD)", desc: "Pay in cash when your order arrives. Available across all governorates. No surcharge." },
          { name: "Fawry", desc: "Pay at any Fawry point across Egypt using your order reference number. Payment valid for 24 hours." },
          { name: "Meeza", desc: "Egyptian national debit card — accepted directly at checkout via our secure payment gateway." },
          { name: "Credit / Debit Card", desc: "Visa and Mastercard accepted. Payments are processed via a PCI-DSS-compliant gateway. We do not store card numbers." },
          { name: "InstaPay", desc: "Send a bank transfer via InstaPay to our registered number. Your order will be confirmed within 30 minutes during business hours." },
        ].map((m) => (
          <div key={m.name} style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)" }}>
            <p style={{ fontWeight: 700, marginBottom: "var(--s2)" }}>{m.name}</p>
            <p className="em-body-s" style={{ color: "var(--ink-500)" }}>{m.desc}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

// ── Privacy & Terms ──────────────────────────────────────
export function PrivacyTerms() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)", maxWidth: 760 }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Privacy & Terms" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>Privacy Policy & Terms of Use</h1>

      {[
        {
          title: "Data we collect",
          body: "We collect your name, email, phone number, and delivery address solely to process and deliver your orders. We do not sell or share your data with third parties for marketing purposes."
        },
        {
          title: "How we use your data",
          body: "Your data is used to process orders, send order status updates via SMS or email, respond to customer service requests, and (with your consent) send newsletters. You can unsubscribe at any time."
        },
        {
          title: "Data security",
          body: "All data is stored on encrypted servers. Card payments are processed by our payment gateway and we do not store or have access to card numbers."
        },
        {
          title: "Cookies",
          body: "We use essential cookies to maintain your cart and session. We use analytics cookies (anonymised) to improve the site. You can disable non-essential cookies in your browser settings."
        },
        {
          title: "Terms of use",
          body: "Products sold on exMart.eg are intended for personal, household, or professional use by competent adults. Diagnostic devices are for informational purposes only and do not substitute for clinical medical advice. Health claims on our own-brand products comply with Egyptian regulatory standards and are not intended to diagnose, treat, or cure any condition."
        },
        {
          title: "Governing law",
          body: "These terms are governed by Egyptian law. Any disputes shall be subject to the jurisdiction of Egyptian courts."
        },
      ].map((s) => (
        <section key={s.title} style={{ marginBottom: "var(--s6)" }}>
          <h2 className="em-h4" style={{ marginBottom: "var(--s3)" }}>{s.title}</h2>
          <p className="em-body-s" style={{ color: "var(--ink-700)", lineHeight: 1.6 }}>{s.body}</p>
        </section>
      ))}
    </div>
  );
}

// ── FAQ ──────────────────────────────────────────────────
const FAQS = [
  { q: "Are the products authentic?", a: "Yes — all products are sourced directly from manufacturers or produced under our own quality protocols. We are the official sole distributor in Egypt for Diversey, Grace, Oview, and SureCheck." },
  { q: "Do you offer cash on delivery?", a: "Yes. Cash on delivery is available across all Egyptian governorates at no extra charge." },
  { q: "How long does delivery take?", a: "Greater Cairo and Giza: 1–2 working days. Delta and Alexandria: 2–3 days. Upper Egypt: 3–5 days. Remote governorates: 4–6 days." },
  { q: "Can I return a product?", a: "Yes, within 14 days of delivery for unused items in original packaging. Intimate hygiene and opened products are excluded for health and safety reasons. Contact hello@exmart.eg to start a return." },
  { q: "What is your warranty policy?", a: "Electronic devices (Oview, SureCheck) carry a 12-month warranty from purchase date. Contact us with your order number and a description of the fault." },
  { q: "Are your house brands safe for sensitive skin?", a: "All Qualita, Vodlia, Eliv, and Verve products are developed with dermatologist review and are formulated to minimise irritation. However, as with any personal care product, we recommend performing a patch test before first use if you have known skin sensitivities." },
  { q: "Do you ship outside Egypt?", a: "Not currently. We ship within Egypt only." },
  { q: "How do I track my order?", a: "After dispatch you will receive an SMS with your tracking number. You can also track your order from the Account section on our website." },
];

export function FAQ() {
  const [open, setOpen] = useState<number | null>(null);
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)", maxWidth: 760 }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "FAQ" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>Frequently Asked Questions</h1>
      <div style={{ display: "flex", flexDirection: "column" }}>
        {FAQS.map((f, i) => (
          <div key={i} style={{ borderBottom: "1px solid var(--ink-200)" }}>
            <button
              onClick={() => setOpen(open === i ? null : i)}
              aria-expanded={open === i}
              style={{
                width: "100%", display: "flex", justifyContent: "space-between", alignItems: "center",
                padding: "var(--s4) 0", background: "none", border: "none", cursor: "pointer",
                fontFamily: "var(--font)", fontSize: ".9375rem", fontWeight: 600, color: "var(--ink-800)", textAlign: "left", gap: "var(--s4)",
              }}
            >
              <span>{f.q}</span>
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"
                style={{ flexShrink: 0, transform: open === i ? "rotate(180deg)" : "none", transition: "transform .2s" }}>
                <path d="M5 7.5l5 5 5-5" stroke="var(--ink-500)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
            </button>
            {open === i && (
              <p className="em-body-s" style={{ color: "var(--ink-700)", paddingBottom: "var(--s4)", lineHeight: 1.6 }}>{f.a}</p>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}

// ── Style Guide (Step 1) ─────────────────────────────────
export function StyleGuide() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>exMart Style Guide</h1>
      <p className="em-body" style={{ color: "var(--ink-500)" }}>Design tokens, typography, and components — see Step 1 output.</p>
    </div>
  );
}

// ── 404 ──────────────────────────────────────────────────
export function NotFound() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s20)", textAlign: "center", display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s5)" }}>
      <p style={{ fontSize: "5rem", fontWeight: 700, color: "var(--ink-200)", lineHeight: 1 }}>404</p>
      <h1 className="em-h2">Page not found</h1>
      <p className="em-body" style={{ color: "var(--ink-500)" }}>The page you are looking for does not exist or has been moved.</p>
      <a href="/" className="em-btn em-btn-primary">Go home</a>
    </div>
  );
}

// ── Route error boundary ──────────────────────────────────
export function RouteError() {
  const error = useRouteError();

  if (isRouteErrorResponse(error) && error.status === 404) {
    return <NotFound />;
  }

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s20)", textAlign: "center", display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s5)" }}>
      <p style={{ fontSize: "5rem", fontWeight: 700, color: "var(--ink-200)", lineHeight: 1 }}>Oops</p>
      <h1 className="em-h2">Something went wrong</h1>
      <p className="em-body" style={{ color: "var(--ink-500)" }}>
        We hit an unexpected error loading this page. Please try again, or head back home.
      </p>
      <div style={{ display: "flex", gap: "var(--s3)" }}>
        <button className="em-btn em-btn-primary" onClick={() => window.location.reload()}>Try again</button>
        <a href="/" className="em-btn em-btn-secondary">Go home</a>
      </div>
    </div>
  );
}
