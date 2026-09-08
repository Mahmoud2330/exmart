import { useState } from "react";
import { useNavigate, Link } from "react-router";
import { useStore } from "../store";
import { EmInput, EmSelect, Breadcrumb, EmptyState } from "../components/em";
import { GOVERNORATES } from "../data";

type Step = "contact" | "address" | "shipping" | "payment";

const STEPS: { id: Step; label: string }[] = [
  { id: "contact", label: "Contact" },
  { id: "address", label: "Address" },
  { id: "shipping", label: "Shipping" },
  { id: "payment", label: "Payment" },
];

const PAYMENT_METHODS = [
  { id: "cod", label: "Cash on Delivery", desc: "Pay when your order arrives. No additional fee." },
  { id: "fawry", label: "Fawry", desc: "Pay at any Fawry point or via the Fawry app." },
  { id: "meeza", label: "Meeza", desc: "Egyptian national payment card." },
  { id: "card", label: "Credit / Debit Card", desc: "Visa, Mastercard — secure payment via payment gateway." },
  { id: "instapay", label: "InstaPay", desc: "Instant bank transfer via InstaPay." },
];

export default function Checkout() {
  const { cart, cartTotal, clearCart } = useStore();
  const nav = useNavigate();
  const [step, setStep] = useState<Step>("contact");
  const [payment, setPayment] = useState("cod");
  const [shippingMethod, setShippingMethod] = useState("standard");
  const [form, setForm] = useState({
    email: "", phone: "", firstName: "", lastName: "",
    address: "", apartment: "", governorate: "Cairo", city: "", postalCode: "",
  });

  const standardShipping = cartTotal >= 300 ? 0 : 35;
  const shipping = shippingMethod === "express" ? 65 : standardShipping;
  const total = cartTotal + shipping;

  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));

  const next = () => {
    const order: Record<Step, Step> = { contact: "address", address: "shipping", shipping: "payment", payment: "payment" };
    setStep(order[step]);
  };
  const placeOrder = () => {
    clearCart();
    nav("/confirmation");
  };
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (step === "payment") placeOrder();
    else next();
  };

  const stepIndex = STEPS.findIndex((s) => s.id === step);

  if (cart.length === 0) {
    return (
      <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
        <Breadcrumb crumbs={[{ label: "Cart", href: "/cart" }, { label: "Checkout" }]} />
        <EmptyState title="Your cart is empty" body="Add products to your cart before checking out." cta="Continue shopping" href="/shop" />
      </div>
    );
  }

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Cart", href: "/cart" }, { label: "Checkout" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s6)" }}>Checkout</h1>

      {/* Progress pipeline */}
      <div className="em-pipeline" style={{ marginBottom: "var(--s8)", maxWidth: 480 }}>
        {STEPS.map((s, i) => (
          <div key={s.id} className={`em-pipeline-step${i < stepIndex ? " done" : ""}`}>
            <div className={`em-pipeline-dot${i < stepIndex ? " done" : i === stepIndex ? " current" : ""}`}>
              {i < stepIndex && (
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                  <path d="M2 5l2 2 4-4" stroke="var(--paper)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
              )}
            </div>
            <span className={`em-pipeline-label${i === stepIndex ? " current" : i < stepIndex ? " done" : ""}`}>{s.label}</span>
          </div>
        ))}
      </div>

      <div className="em-two-col" style={{ alignItems: "start" }}>
        {/* Form */}
        <form onSubmit={handleSubmit} style={{ display: "flex", flexDirection: "column", gap: "var(--s6)" }}>

          {step === "contact" && (
            <fieldset style={{ border: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
              <legend className="em-h4" style={{ marginBottom: "var(--s4)" }}>Contact Information</legend>
              <EmInput label="Email address" id="email" type="email" value={form.email} onChange={(e) => set("email", e.target.value)} placeholder="ahmed@example.com" required />
              <EmInput label="Phone number" id="phone" type="tel" value={form.phone} onChange={(e) => set("phone", e.target.value)} placeholder="+20 10 0000 0000" helper="For delivery updates via SMS." required />
              <p className="em-caption" style={{ color: "var(--ink-500)" }}>
                Already have an account? <Link to="/account/login" style={{ color: "var(--accent-600)" }}>Log in</Link>
              </p>
            </fieldset>
          )}

          {step === "address" && (
            <fieldset style={{ border: "none", padding: 0, margin: 0, display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
              <legend className="em-h4" style={{ marginBottom: "var(--s4)" }}>Delivery Address</legend>
              <div className="em-form-row">
                <EmInput label="First name" id="firstName" value={form.firstName} onChange={(e) => set("firstName", e.target.value)} required />
                <EmInput label="Last name" id="lastName" value={form.lastName} onChange={(e) => set("lastName", e.target.value)} required />
              </div>
              <EmInput label="Street address" id="address" value={form.address} onChange={(e) => set("address", e.target.value)} placeholder="12 El-Nozha Street" required />
              <EmInput label="Apartment / floor (optional)" id="apartment" value={form.apartment} onChange={(e) => set("apartment", e.target.value)} />
              <div className="em-form-row">
                <EmSelect label="Governorate" id="governorate" options={GOVERNORATES} value={form.governorate} onChange={(e) => set("governorate", e.target.value)} />
                <EmInput label="City / district" id="city" value={form.city} onChange={(e) => set("city", e.target.value)} required />
              </div>
              <EmInput label="Postal code (optional)" id="postalCode" value={form.postalCode} onChange={(e) => set("postalCode", e.target.value)} />
            </fieldset>
          )}

          {step === "shipping" && (
            <fieldset style={{ border: "none", padding: 0, margin: 0 }}>
              <legend className="em-h4" style={{ marginBottom: "var(--s4)" }}>Shipping Method</legend>
              {[
                { id: "standard", label: "Standard Delivery", eta: "2–5 working days", price: standardShipping },
                { id: "express", label: "Express Delivery", eta: "Next working day", price: 65 },
              ].map((opt) => (
                <label key={opt.id} style={{ display: "flex", alignItems: "center", gap: "var(--s3)", padding: "var(--s4)", border: `1px solid ${shippingMethod === opt.id ? "var(--ink-900)" : "var(--ink-200)"}`, borderRadius: "var(--r-sm)", marginBottom: "var(--s3)", cursor: "pointer" }}>
                  <input type="radio" name="shipping" checked={shippingMethod === opt.id} onChange={() => setShippingMethod(opt.id)} style={{ accentColor: "var(--ink-900)", width: 18, height: 18 }} />
                  <div style={{ flex: 1 }}>
                    <p style={{ fontWeight: 600, fontSize: ".9375rem" }}>{opt.label}</p>
                    <p className="em-caption" style={{ color: "var(--ink-500)" }}>{opt.eta}</p>
                  </div>
                  <span style={{ fontWeight: 600, fontVariantNumeric: "tabular-nums" }}>
                    {opt.price === 0 ? "Free" : `EGP ${opt.price.toFixed(2)}`}
                  </span>
                </label>
              ))}
            </fieldset>
          )}

          {step === "payment" && (
            <fieldset style={{ border: "none", padding: 0, margin: 0 }}>
              <legend className="em-h4" style={{ marginBottom: "var(--s4)" }}>Payment Method</legend>
              {PAYMENT_METHODS.map((m) => (
                <label key={m.id} style={{ display: "flex", alignItems: "flex-start", gap: "var(--s3)", padding: "var(--s4)", border: `1px solid ${payment === m.id ? "var(--ink-900)" : "var(--ink-200)"}`, borderRadius: "var(--r-sm)", marginBottom: "var(--s3)", cursor: "pointer", background: payment === m.id ? "var(--ink-50)" : "var(--paper)" }}>
                  <input type="radio" name="payment" checked={payment === m.id} onChange={() => setPayment(m.id)} style={{ accentColor: "var(--ink-900)", width: 18, height: 18, marginTop: 2 }} />
                  <div>
                    <p style={{ fontWeight: 600, fontSize: ".9375rem" }}>{m.label}</p>
                    <p className="em-caption" style={{ color: "var(--ink-500)" }}>{m.desc}</p>
                  </div>
                </label>
              ))}
            </fieldset>
          )}

          {step !== "payment" ? (
            <button type="submit" className="em-btn em-btn-primary em-btn-lg" style={{ alignSelf: "flex-start", minWidth: 200 }}>
              Continue
            </button>
          ) : (
            <button type="submit" className="em-btn em-btn-accent em-btn-lg" style={{ alignSelf: "flex-start", minWidth: 200 }}>
              Place Order — EGP {total.toFixed(2)}
            </button>
          )}
        </form>

        {/* Order summary */}
        <div style={{ position: "sticky", top: 140 }}>
          <div className="em-summary-card">
            <h2 className="em-h4" style={{ marginBottom: "var(--s3)" }}>Order Summary</h2>
            {cart.map((item) => (
              <div key={item.product.id} className="em-summary-row" style={{ fontSize: ".875rem" }}>
                <span style={{ color: "var(--ink-700)", maxWidth: "60%" }}>{item.product.name} × {item.qty}</span>
                <span style={{ fontWeight: 600, fontVariantNumeric: "tabular-nums" }}>EGP {(item.product.price * item.qty).toFixed(2)}</span>
              </div>
            ))}
            <hr className="em-rule" />
            <div className="em-summary-row">
              <span className="em-summary-label">Subtotal</span>
              <span className="em-summary-value">EGP {cartTotal.toFixed(2)}</span>
            </div>
            <div className="em-summary-row">
              <span className="em-summary-label">Shipping</span>
              <span className="em-summary-value">{shipping === 0 ? "Free" : `EGP ${shipping.toFixed(2)}`}</span>
            </div>
            <hr className="em-rule" />
            <div className="em-summary-row em-summary-total">
              <span className="em-summary-label">Total</span>
              <span className="em-summary-value">EGP {total.toFixed(2)}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
