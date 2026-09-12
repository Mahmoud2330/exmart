import { useState } from "react";
import { Link } from "react-router";
import { Search } from "lucide-react";
import { BRANDS, PRODUCTS } from "../data";
import { TrustLockup, Breadcrumb } from "../components/em";
import type { Brand } from "../data";

function BrandCard({ brand }: { brand: Brand }) {
  const count = PRODUCTS.filter((p) => p.brand === brand.name).length;
  return (
    <Link to={`/brands/${brand.slug}`}
      style={{ textDecoration: "none", border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", padding: "var(--s5)", display: "flex", flexDirection: "column", gap: "var(--s3)", transition: "border-color .15s, box-shadow .15s" }}
      onMouseEnter={(e) => { e.currentTarget.style.borderColor = "var(--ink-400)"; e.currentTarget.style.boxShadow = "var(--shadow-menu)"; }}
      onMouseLeave={(e) => { e.currentTarget.style.borderColor = "var(--ink-200)"; e.currentTarget.style.boxShadow = "none"; }}
    >
      <div style={{ width: 56, height: 56, borderRadius: "50%", background: brand.color + "22", display: "flex", alignItems: "center", justifyContent: "center" }}>
        <span style={{ fontWeight: 700, fontSize: "1.5rem", color: brand.color }}>{brand.name[0]}</span>
      </div>
      <div>
        <p style={{ fontWeight: 700, fontSize: "1rem", color: "var(--ink-900)", marginBottom: "var(--s1)" }}>{brand.name}</p>
        <TrustLockup brand={brand} />
      </div>
      <p className="em-body-s" style={{ color: "var(--ink-500)", lineHeight: 1.45, flex: 1 }}>{brand.tagline}</p>
      <p className="em-caption" style={{ color: "var(--accent-600)", fontWeight: 600 }}>{count} products →</p>
    </Link>
  );
}

export default function BrandIndex() {
  const [q, setQ] = useState("");
  const filtered = BRANDS.filter((b) => b.name.toLowerCase().includes(q.toLowerCase()) || b.tagline.toLowerCase().includes(q.toLowerCase()));
  const distributed = filtered.filter((b) => b.type === "distributed");
  const house = filtered.filter((b) => b.type === "house");

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Brands" }]} />
      <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s8)" }}>
        <h1 className="em-h2">Our Brands</h1>
        <div className="em-search-wrap" style={{ maxWidth: 280 }}>
          <Search size={16} className="em-search-icon" aria-hidden="true" />
          <input className="em-input em-search-input" placeholder="Search brands…" value={q} onChange={(e) => setQ(e.target.value)} aria-label="Search brands" style={{ minHeight: 44 }} />
        </div>
      </div>

      {distributed.length > 0 && (
        <section style={{ marginBottom: "var(--s12)" }}>
          <h2 className="em-h3" style={{ marginBottom: "var(--s2)" }}>Brands we distribute</h2>
          <p className="em-body-s" style={{ color: "var(--ink-500)", marginBottom: "var(--s6)" }}>
            exMart is the official and sole distributor of these brands in Egypt — sourcing directly from the manufacturer.
          </p>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%, 260px), 1fr))", gap: "var(--s4)" }}>
            {distributed.map((b) => <BrandCard key={b.slug} brand={b} />)}
          </div>
        </section>
      )}

      {house.length > 0 && (
        <section>
          <h2 className="em-h3" style={{ marginBottom: "var(--s2)" }}>exMart brands</h2>
          <p className="em-body-s" style={{ color: "var(--ink-500)", marginBottom: "var(--s6)" }}>
            Developed and quality-tested by our in-house team — made exclusively for exMart customers.
          </p>
          <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%, 260px), 1fr))", gap: "var(--s4)" }}>
            {house.map((b) => <BrandCard key={b.slug} brand={b} />)}
          </div>
        </section>
      )}

      {filtered.length === 0 && (
        <p className="em-body" style={{ color: "var(--ink-400)", paddingBlock: "var(--s12)" }}>No brands match "{q}".</p>
      )}
    </div>
  );
}
