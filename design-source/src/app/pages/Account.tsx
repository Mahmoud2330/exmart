import { useState } from "react";
import { Link, useParams, useNavigate } from "react-router";
import { useStore } from "../store";
import { PRODUCTS } from "../data";
import { ProductCard, Breadcrumb, EmInput } from "../components/em";

type AccountTab = "orders" | "addresses" | "wishlist" | "profile" | "login" | "register";

const MOCK_ORDERS = [
  { id: "EXM-112233", date: "2025-06-15", status: "Delivered", items: 3, total: 329.00 },
  { id: "EXM-223344", date: "2025-05-28", status: "Shipped", items: 1, total: 189.00 },
  { id: "EXM-334455", date: "2025-04-10", status: "Delivered", items: 5, total: 582.50 },
];

function OrderRow({ order }: { order: typeof MOCK_ORDERS[0] }) {
  const statusColor: Record<string, string> = {
    Delivered: "var(--success)", Shipped: "var(--accent-600)", Confirmed: "var(--warning)", Placed: "var(--ink-500)",
  };
  return (
    <div style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s4)", display: "flex", flexWrap: "wrap", gap: "var(--s4)", alignItems: "center", justifyContent: "space-between" }}>
      <div>
        <p style={{ fontWeight: 700, fontVariantNumeric: "tabular-nums" }}>{order.id}</p>
        <p className="em-caption" style={{ color: "var(--ink-500)" }}>{order.date} · {order.items} item{order.items !== 1 ? "s" : ""}</p>
      </div>
      <div style={{ display: "flex", alignItems: "center", gap: "var(--s4)" }}>
        <span style={{ fontWeight: 600, fontVariantNumeric: "tabular-nums" }}>EGP {order.total.toFixed(2)}</span>
        <span className="em-badge" style={{ background: statusColor[order.status] + "22", color: statusColor[order.status] }}>{order.status}</span>
        <Link to="/tracking" className="em-btn em-btn-secondary em-btn-sm">Track</Link>
      </div>
    </div>
  );
}

function Tracking() {
  const pipeline = ["Placed", "Confirmed", "Shipped", "Delivered"];
  const current = 2;
  return (
    <div>
      <h2 className="em-h4" style={{ marginBottom: "var(--s6)" }}>Order EXM-223344 — Tracking</h2>
      <div style={{ background: "var(--ink-50)", borderRadius: "var(--r-md)", border: "1px solid var(--ink-200)", padding: "var(--s5) var(--s6)", marginBottom: "var(--s6)" }}>
        <div className="em-pipeline">
          {pipeline.map((s, i) => (
            <div key={s} className={`em-pipeline-step${i < current ? " done" : ""}`}>
              <div className={`em-pipeline-dot${i < current ? " done" : i === current ? " current" : ""}`}>
                {i < current && (
                  <svg width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                    <path d="M2 5l2 2 4-4" stroke="var(--paper)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                  </svg>
                )}
              </div>
              <span className={`em-pipeline-label${i < current ? " done" : i === current ? " current" : ""}`}>{s}</span>
            </div>
          ))}
        </div>
      </div>
      <p className="em-body-s" style={{ color: "var(--ink-500)" }}>Your order was dispatched from our Cairo warehouse on 29 May 2025. Expected delivery: 1 June 2025.</p>
    </div>
  );
}

function WishlistView() {
  const { wishlist } = useStore();
  const products = PRODUCTS.filter((p) => wishlist.includes(p.id));
  if (products.length === 0) {
    return (
      <div style={{ paddingBlock: "var(--s12)", textAlign: "center" }}>
        <p className="em-body" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Your wishlist is empty.</p>
        <Link to="/shop" className="em-btn em-btn-primary">Discover products</Link>
      </div>
    );
  }
  return (
    <div className="em-grid">
      {products.map((p) => <ProductCard key={p.id} product={p} />)}
    </div>
  );
}

function LoginForm() {
  const [email, setEmail] = useState(""); const [pw, setPw] = useState("");
  const nav = useNavigate();
  return (
    <form
      onSubmit={(e) => { e.preventDefault(); nav("/account/orders"); }}
      style={{ maxWidth: 400, display: "flex", flexDirection: "column", gap: "var(--s4)" }}
    >
      <h2 className="em-h3">Log in</h2>
      <EmInput label="Email" id="login-email" type="email" value={email} onChange={(e) => setEmail(e.target.value)} required />
      <EmInput label="Password" id="login-pw" type="password" value={pw} onChange={(e) => setPw(e.target.value)} required minLength={8} />
      <button type="submit" className="em-btn em-btn-primary em-btn-lg" style={{ width: "100%" }}>Log in</button>
      <p className="em-body-s" style={{ color: "var(--ink-500)" }}>
        No account? <Link to="/account/register" style={{ color: "var(--accent-600)" }}>Register</Link>
      </p>
    </form>
  );
}

function RegisterForm() {
  const [form, setForm] = useState({ name: "", email: "", password: "", phone: "" });
  const nav = useNavigate();
  const set = (k: string, v: string) => setForm((f) => ({ ...f, [k]: v }));
  return (
    <form
      onSubmit={(e) => { e.preventDefault(); nav("/account/orders"); }}
      style={{ maxWidth: 400, display: "flex", flexDirection: "column", gap: "var(--s4)" }}
    >
      <h2 className="em-h3">Create account</h2>
      <EmInput label="Full name" id="reg-name" placeholder="Ahmed Hassan" value={form.name} onChange={(e) => set("name", e.target.value)} required />
      <EmInput label="Email" id="reg-email" type="email" value={form.email} onChange={(e) => set("email", e.target.value)} required />
      <EmInput label="Password" id="reg-pw" type="password" helper="Minimum 8 characters." value={form.password} onChange={(e) => set("password", e.target.value)} required minLength={8} />
      <EmInput label="Phone" id="reg-phone" type="tel" placeholder="+20 10 0000 0000" value={form.phone} onChange={(e) => set("phone", e.target.value)} required />
      <button type="submit" className="em-btn em-btn-primary em-btn-lg" style={{ width: "100%" }}>Create account</button>
      <p className="em-body-s" style={{ color: "var(--ink-500)" }}>
        Already have an account? <Link to="/account/login" style={{ color: "var(--accent-600)" }}>Log in</Link>
      </p>
    </form>
  );
}

const TABS = [
  { id: "orders", label: "Orders" },
  { id: "addresses", label: "Addresses" },
  { id: "wishlist", label: "Wishlist" },
  { id: "profile", label: "Profile" },
];

export default function Account() {
  const { section } = useParams<{ section?: string }>();

  if (section === "login") return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Login" }]} />
      <LoginForm />
    </div>
  );
  if (section === "register") return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Register" }]} />
      <RegisterForm />
    </div>
  );
  if (section === "tracking") return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Account", href: "/account" }, { label: "Tracking" }]} />
      <Tracking />
    </div>
  );

  const activeTab = (section as AccountTab) ?? "orders";

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Account" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s6)" }}>My Account</h1>
      <div style={{ display: "grid", gridTemplateColumns: "1fr", gap: "var(--s6)" }} className="acct-grid">
        <style>{`@media(min-width:768px){.acct-grid{grid-template-columns:200px 1fr !important;}}`}</style>

        {/* Side nav */}
        <nav style={{ display: "flex", flexDirection: "column", gap: "var(--s1)" }} aria-label="Account navigation">
          {TABS.map((t) => (
            <Link key={t.id} to={`/account/${t.id}`}
              style={{ padding: "var(--s3) var(--s4)", borderRadius: "var(--r-sm)", textDecoration: "none", fontSize: ".9375rem", fontWeight: 500, color: activeTab === t.id ? "var(--ink-900)" : "var(--ink-500)", background: activeTab === t.id ? "var(--ink-100)" : "transparent", transition: "background .15s, color .15s" }}>
              {t.label}
            </Link>
          ))}
        </nav>

        {/* Content */}
        <div>
          {activeTab === "orders" && (
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
              <h2 className="em-h4" style={{ marginBottom: "var(--s2)" }}>Order History</h2>
              {MOCK_ORDERS.map((o) => <OrderRow key={o.id} order={o} />)}
            </div>
          )}
          {activeTab === "addresses" && (
            <div>
              <h2 className="em-h4" style={{ marginBottom: "var(--s5)" }}>Saved Addresses</h2>
              <div style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s4)", marginBottom: "var(--s4)" }}>
                <p style={{ fontWeight: 600 }}>Ahmed Hassan</p>
                <p className="em-body-s" style={{ color: "var(--ink-500)" }}>12 El-Nozha Street, Heliopolis</p>
                <p className="em-body-s" style={{ color: "var(--ink-500)" }}>Cairo, 11361</p>
                <p className="em-body-s" style={{ color: "var(--ink-500)" }}>+20 100 123 4567</p>
                <div style={{ display: "flex", gap: "var(--s2)", marginTop: "var(--s3)" }}>
                  <button className="em-btn em-btn-secondary em-btn-sm">Edit</button>
                  <button className="em-btn em-btn-ghost em-btn-sm" style={{ color: "var(--error)" }}>Remove</button>
                </div>
              </div>
              <button className="em-btn em-btn-secondary">+ Add new address</button>
            </div>
          )}
          {activeTab === "wishlist" && (
            <div>
              <h2 className="em-h4" style={{ marginBottom: "var(--s5)" }}>Wishlist</h2>
              <WishlistView />
            </div>
          )}
          {activeTab === "profile" && (
            <div style={{ maxWidth: 480, display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
              <h2 className="em-h4" style={{ marginBottom: "var(--s2)" }}>Profile</h2>
              <div className="em-form-row">
                <EmInput label="First name" id="pf-first" defaultValue="Ahmed" />
                <EmInput label="Last name" id="pf-last" defaultValue="Hassan" />
              </div>
              <EmInput label="Email" id="pf-email" type="email" defaultValue="ahmed@example.com" />
              <EmInput label="Phone" id="pf-phone" type="tel" defaultValue="+20 100 123 4567" />
              <button className="em-btn em-btn-primary" style={{ alignSelf: "flex-start" }}>Save changes</button>
              <hr className="em-rule" />
              <h3 className="em-h4">Change password</h3>
              <EmInput label="Current password" id="pf-cpw" type="password" />
              <EmInput label="New password" id="pf-npw" type="password" />
              <button className="em-btn em-btn-secondary" style={{ alignSelf: "flex-start" }}>Update password</button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
