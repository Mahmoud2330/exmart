import { Link } from "react-router";
import { BRANDS } from "../data";
import { TrustLockup, Breadcrumb } from "../components/em";

export default function About() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s12)", maxWidth: 800 }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "About" }]} />
      <h1 className="em-h1" style={{ marginBottom: "var(--s8)" }}>About exMart</h1>

      <section style={{ marginBottom: "var(--s10)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s4)" }}>Our story</h2>
        <p className="em-body" style={{ color: "var(--ink-700)", marginBottom: "var(--s4)" }}>
          exMart was founded in Cairo with a clear mission: to give Egyptian households and healthcare professionals reliable access to authentic, professional-grade health and hygiene products — without the uncertainty of grey-market imports or counterfeits.
        </p>
        <p className="em-body" style={{ color: "var(--ink-700)", marginBottom: "var(--s4)" }}>
          We began as a distributor, earning sole-distributor agreements with Diversey, Grace, Oview, and SureCheck — four internationally recognised brands in professional cleaning, diagnostics, and health monitoring. Being the sole authorised distributor means every product on our shelves is sourced directly from the manufacturer, sealed and complete, with full warranty support.
        </p>
        <p className="em-body" style={{ color: "var(--ink-700)" }}>
          As we grew, we identified gaps the imported brands could not fill at accessible price points. That led us to develop our own exclusive brands — Qualita, Vodlia, Eliv, and Verve — formulated in collaboration with specialist laboratories and dermatologist advisors, and developed specifically for the Egyptian climate, water chemistry, and skin types.
        </p>
      </section>

      <section style={{ marginBottom: "var(--s10)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s6)" }}>Brands we distribute</h2>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%,320px),1fr))", gap: "var(--s4)" }}>
          {BRANDS.filter((b) => b.type === "distributed").map((brand) => (
            <Link key={brand.slug} to={`/brands/${brand.slug}`}
              style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)", textDecoration: "none", display: "flex", flexDirection: "column", gap: "var(--s2)" }}>
              <div style={{ display: "flex", alignItems: "center", gap: "var(--s3)" }}>
                <div style={{ width: 40, height: 40, borderRadius: "50%", background: brand.color + "22", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
                  <span style={{ fontWeight: 700, color: brand.color }}>{brand.name[0]}</span>
                </div>
                <span style={{ fontWeight: 700, color: "var(--ink-900)" }}>{brand.name}</span>
              </div>
              <TrustLockup brand={brand} />
              <p className="em-body-s" style={{ color: "var(--ink-500)" }}>{brand.tagline}</p>
            </Link>
          ))}
        </div>
      </section>

      <section style={{ marginBottom: "var(--s10)" }}>
        <h2 className="em-h3" style={{ marginBottom: "var(--s6)" }}>Our own brands</h2>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%,320px),1fr))", gap: "var(--s4)" }}>
          {BRANDS.filter((b) => b.type === "house").map((brand) => (
            <Link key={brand.slug} to={`/brands/${brand.slug}`}
              style={{ border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)", textDecoration: "none", display: "flex", flexDirection: "column", gap: "var(--s2)" }}>
              <div style={{ display: "flex", alignItems: "center", gap: "var(--s3)" }}>
                <div style={{ width: 40, height: 40, borderRadius: "50%", background: brand.color + "22", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
                  <span style={{ fontWeight: 700, color: brand.color }}>{brand.name[0]}</span>
                </div>
                <span style={{ fontWeight: 700, color: "var(--ink-900)" }}>{brand.name}</span>
              </div>
              <TrustLockup brand={brand} />
              <p className="em-body-s" style={{ color: "var(--ink-500)" }}>{brand.tagline}</p>
            </Link>
          ))}
        </div>
      </section>

      <section>
        <h2 className="em-h3" style={{ marginBottom: "var(--s4)" }}>Our commitment</h2>
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
          {[
            "Every product shipped by exMart is authentic — sourced directly from the manufacturer or produced under our own quality protocols.",
            "We never sell counterfeit, expired, or grey-market products. If you suspect a product's authenticity, contact us immediately.",
            "Our in-house brands are developed with dermatologist input and follow Egyptian regulatory standards for cosmetics and personal care.",
            "We offer hassle-free returns within 14 days for any product that does not meet your expectations.",
          ].map((t) => (
            <div key={t} style={{ display: "flex", gap: "var(--s3)", alignItems: "flex-start" }}>
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none" style={{ flexShrink: 0, marginTop: 2 }} aria-hidden="true">
                <circle cx="9" cy="9" r="8" stroke="var(--ink-700)" strokeWidth="1.5" />
                <path d="M6 9l2 2 4-4" stroke="var(--ink-700)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
              </svg>
              <p className="em-body-s" style={{ color: "var(--ink-700)" }}>{t}</p>
            </div>
          ))}
        </div>
      </section>
    </div>
  );
}
