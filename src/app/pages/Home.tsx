import { Link } from "react-router";
import { Shield, Truck, CreditCard, MapPin } from "lucide-react";
import { BRANDS, CATEGORIES, getBestSellers, getNewArrivals, getOffers } from "../data";
import { Rail, SectionRule } from "../components/em";
import { useState } from "react";

const bestSellers = getBestSellers().slice(0, 8);
const offers = getOffers().slice(0, 8);
const newArrivals = getNewArrivals().slice(0, 8);

function Hero() {
  return (
    <section style={{ background: "var(--ink-900)", color: "var(--paper)", position: "relative", overflow: "hidden" }}>
      {/* Background image */}
      <div style={{
        position: "absolute", inset: 0, zIndex: 0,
        backgroundImage: "url(https://images.unsplash.com/photo-1600709206786-f96656b15a3c?w=1600&fit=crop&auto=format)",
        backgroundSize: "cover", backgroundPosition: "center right",
        opacity: 0.18,
      }} aria-hidden="true" />
      {/* Gradient overlay so text stays readable */}
      <div style={{ position: "absolute", inset: 0, zIndex: 1, background: "linear-gradient(90deg, var(--ink-900) 45%, transparent 100%)" }} aria-hidden="true" />

      <div className="em-container" style={{ position: "relative", zIndex: 2, display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--s8)", alignItems: "center", minHeight: "clamp(380px, 55vw, 560px)" }}>
        {/* Copy */}
        <div style={{ paddingBlock: "clamp(48px, 8vw, 96px)" }}>
          <p className="em-overline" style={{ color: "var(--accent-100)", marginBottom: "var(--s4)" }}>Authentic health & hygiene — Egypt</p>
          <h1 className="em-h1" style={{ color: "var(--paper)", marginBottom: "var(--s5)" }}>
            Professional-grade<br />health products,<br />delivered to your door.
          </h1>
          <p className="em-body" style={{ color: "var(--ink-300)", marginBottom: "var(--s8)", maxWidth: "48ch" }}>
            Official sole distributor of Diversey, Grace, Oview & SureCheck in Egypt. Plus our own exclusive brands — Qualita, Vodlia, Eliv, and Verve.
          </p>
          <div style={{ display: "flex", gap: "var(--s3)", flexWrap: "wrap" }}>
            <Link to="/shop" className="em-btn em-btn-lg em-btn-primary">
              Shop all products
            </Link>
            <Link to="/about" className="em-btn em-btn-lg em-btn-ghost" style={{ color: "var(--ink-300)", border: "1px solid var(--ink-700)" }}>
              Our story
            </Link>
          </div>
        </div>

        {/* Feature image */}
        <div style={{ display: "flex", alignItems: "center", justifyContent: "center", paddingBlock: "var(--s8)" }} id="hero-img-col">
          <div style={{ position: "relative", width: "100%", maxWidth: 420 }}>
            <img
              src="https://images.unsplash.com/photo-1627495395570-d2c94e3319f5?w=800&fit=crop&auto=format"
              alt="Professional hygiene products"
              style={{ width: "100%", aspectRatio: "4/5", objectFit: "cover", borderRadius: "var(--r-lg)", boxShadow: "0 32px 64px rgba(0,0,0,.4)", display: "block" }}
            />
            {/* Floating badge */}
            <div style={{ position: "absolute", bottom: 24, left: -24, background: "var(--paper)", borderRadius: "var(--r-md)", padding: "var(--s3) var(--s4)", boxShadow: "0 8px 24px rgba(0,0,0,.2)", display: "flex", alignItems: "center", gap: "var(--s3)" }}>
              <div style={{ width: 40, height: 40, borderRadius: "50%", background: "var(--success-bg)", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M9 12l2 2 4-4" stroke="var(--success)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/><circle cx="12" cy="12" r="9" stroke="var(--success)" strokeWidth="2"/></svg>
              </div>
              <div>
                <p style={{ fontSize: ".75rem", fontWeight: 700, color: "var(--ink-900)", margin: 0 }}>100% Authentic</p>
                <p style={{ fontSize: ".6875rem", color: "var(--ink-500)", margin: 0 }}>Direct from manufacturer</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <style>{`@media(max-width:767px){#hero-img-col{display:none!important;} .em-container{grid-template-columns:1fr!important;}}`}</style>
    </section>
  );
}

function TrustStrip() {
  const items = [
    { icon: Shield, text: "100% authentic — direct from manufacturer" },
    { icon: MapPin, text: "Official sole distributor in Egypt" },
    { icon: CreditCard, text: "Cash on delivery available" },
    { icon: Truck, text: "Nationwide delivery across Egypt" },
  ];
  return (
    <div style={{ borderBottom: "1px solid var(--ink-200)" }}>
      <div className="em-container" style={{ paddingBlock: "var(--s5)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%, 220px), 1fr))", gap: "var(--s4)" }}>
          {items.map(({ icon: Icon, text }) => (
            <div key={text} style={{ display: "flex", alignItems: "center", gap: "var(--s3)" }}>
              <div style={{ width: 36, height: 36, borderRadius: "50%", background: "var(--ink-100)", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
                <Icon size={18} color="var(--ink-700)" />
              </div>
              <span className="em-body-s" style={{ color: "var(--ink-700)", fontWeight: 500 }}>{text}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

function ShopByCategory() {
  return (
    <section className="em-section-sm">
      <div>
        <div className="em-container" style={{ marginBottom: "var(--s5)" }}>
          <h2 className="em-h2">Shop by Category</h2>
        </div>
        <style>{`
          .cat-track { display:flex; overflow-x:auto; gap:clamp(12px,2vw,24px); padding-inline:clamp(16px,4vw,32px); padding-bottom:var(--s3); scroll-snap-type:x mandatory; scrollbar-width:none; }
          .cat-track::-webkit-scrollbar { display:none; }
          .cat-item { text-decoration:none; display:flex; flex-direction:column; align-items:center; gap:var(--s2); flex-shrink:0; scroll-snap-align:start; min-width:72px; }
          .cat-circle { border-radius:50%; overflow:hidden; flex-shrink:0; border:2.5px solid var(--ink-100); transition:transform .15s, border-color .15s; width:64px; height:64px; }
          .cat-circle:hover { transform:scale(1.08); border-color:var(--accent-600); }
          .cat-circle img { width:100%; height:100%; object-fit:cover; display:block; }
          .cat-label { font-weight:600; color:var(--ink-700); text-align:center; line-height:1.25; width:100%; font-size:.6875rem; }
          @media(min-width:640px) {
            .cat-track { overflow-x:visible; justify-content:space-evenly; flex-wrap:nowrap; }
            .cat-item { flex:1; min-width:0; flex-shrink:1; scroll-snap-align:unset; }
            .cat-circle { width:clamp(72px,8vw,112px); height:clamp(72px,8vw,112px); }
            .cat-label { font-size:clamp(.75rem,.9vw,.9375rem); }
          }
        `}</style>
        <div className="cat-track">
          {CATEGORIES.map((cat) => {
            const catImages: Record<string, string> = {
              "personal-care":    "https://images.unsplash.com/photo-1599210822756-5c4f400b90ac?w=200&h=200&fit=crop&auto=format",
              "hair-care":        "https://images.unsplash.com/photo-1701992678972-d5a053ad0fb0?w=200&h=200&fit=crop&auto=format",
              "skin-care":        "https://images.unsplash.com/photo-1748543668646-e81cda0890f3?w=200&h=200&fit=crop&auto=format",
              "baby-care":        "https://images.unsplash.com/photo-1705155726507-8e1b9119349b?w=200&h=200&fit=crop&auto=format",
              "feminine-care":    "https://images.unsplash.com/photo-1748543668676-ea8241cb3886?w=200&h=200&fit=crop&auto=format",
              "home-care":        "https://images.unsplash.com/photo-1550963295-019d8a8a61c5?w=200&h=200&fit=crop&auto=format",
              "home-diagnostics": "https://images.unsplash.com/photo-1580281658460-2d1114999983?w=200&h=200&fit=crop&auto=format",
            };
            const src = catImages[cat.slug] ?? catImages["personal-care"];
            return (
              <Link key={cat.slug} to={`/categories/${cat.slug}`} className="cat-item">
                <div className="cat-circle">
                  <img src={src} alt={cat.name} loading="lazy" decoding="async" />
                </div>
                <span className="cat-label">{cat.name}</span>
              </Link>
            );
          })}
        </div>
      </div>
    </section>
  );
}

function BrandsStrip() {
  return (
    <div style={{ borderBottom: "1px solid var(--ink-200)", paddingBlock: "var(--s4)" }}>
      <div style={{ display: "flex", alignItems: "center", gap: "var(--s4)", overflowX: "auto", paddingInline: "clamp(16px,4vw,32px)", scrollbarWidth: "none" }}>
        <style>{`.brands-strip::-webkit-scrollbar{display:none}`}</style>
        <span className="em-overline" style={{ color: "var(--ink-400)", flexShrink: 0 }}>Our Brands</span>
        <div style={{ width: 1, height: 20, background: "var(--ink-200)", flexShrink: 0 }} />
        {BRANDS.map((brand) => (
          <Link
            key={brand.slug}
            to={`/brands/${brand.slug}`}
            style={{ textDecoration: "none", display: "flex", alignItems: "center", gap: "var(--s2)", flexShrink: 0, padding: "var(--s2) var(--s3)", borderRadius: "var(--r-full)", border: "1px solid var(--ink-200)", transition: "border-color .15s, background .15s" }}
            onMouseEnter={(e) => { (e.currentTarget as HTMLElement).style.borderColor = brand.color; (e.currentTarget as HTMLElement).style.background = brand.color + "12"; }}
            onMouseLeave={(e) => { (e.currentTarget as HTMLElement).style.borderColor = "var(--ink-200)"; (e.currentTarget as HTMLElement).style.background = "transparent"; }}
          >
            <span style={{ width: 20, height: 20, borderRadius: "50%", background: brand.color, display: "flex", alignItems: "center", justifyContent: "center", fontSize: ".625rem", fontWeight: 700, color: "#fff", flexShrink: 0 }}>
              {brand.name[0]}
            </span>
            <span style={{ fontSize: ".8125rem", fontWeight: 600, color: "var(--ink-800)", whiteSpace: "nowrap" }}>{brand.name}</span>
          </Link>
        ))}
      </div>
    </div>
  );
}

function ReviewBand() {
  const reviews = [
    { author: "Layla M.", text: "Authentic products and fast delivery. Finally a trustworthy Egyptian source for Diversey.", rating: 5 },
    { author: "Karim A.", text: "Ordered Eliv baby wipes three times already. Quality is consistent and pricing is fair.", rating: 5 },
    { author: "Nour F.", text: "The SureCheck blood pressure monitor I received is exactly the genuine product — box sealed, complete accessories.", rating: 4 },
  ];
  return (
    <section className="em-section-sm" style={{ background: "var(--ink-900)", color: "var(--paper)" }}>
      <div className="em-container">
        <h2 className="em-h2" style={{ color: "var(--paper)", marginBottom: "var(--s8)" }}>What customers say</h2>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%, 280px), 1fr))", gap: "var(--s4)" }}>
          {reviews.map((r) => (
            <div key={r.author} style={{ border: "1px solid var(--ink-700)", borderRadius: "var(--r-md)", padding: "var(--s5)" }}>
              <div style={{ display: "flex", gap: "var(--s1)", marginBottom: "var(--s3)" }}>
                {Array.from({ length: 5 }).map((_, i) => (
                  <svg key={i} width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <path d="M8 1.5l1.545 3.13 3.455.502-2.5 2.436.59 3.432L8 9.25l-3.09 1.75.59-3.432L3 5.132l3.455-.502L8 1.5z"
                      fill={i < r.rating ? "var(--paper)" : "var(--ink-700)"} />
                  </svg>
                ))}
              </div>
              <p className="em-body-s" style={{ color: "var(--ink-200)", marginBottom: "var(--s3)", fontStyle: "italic" }}>"{r.text}"</p>
              <p className="em-caption" style={{ color: "var(--ink-400)" }}>— {r.author}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}

function Newsletter() {
  const [email, setEmail] = useState("");
  const [done, setDone] = useState(false);
  return (
    <section className="em-section-sm">
      <div className="em-container">
        <div style={{ maxWidth: 520, margin: "0 auto", textAlign: "center", display: "flex", flexDirection: "column", alignItems: "center", gap: "var(--s4)" }}>
          <h2 className="em-h2">Stay in the loop</h2>
          <p className="em-body" style={{ color: "var(--ink-500)" }}>
            New products, exclusive offers, and health tips — delivered to your inbox.
          </p>
          {done ? (
            <p className="em-body" style={{ color: "var(--success)", fontWeight: 600 }}>You're subscribed — thank you!</p>
          ) : (
            <form onSubmit={(e) => { e.preventDefault(); if (email) setDone(true); }} style={{ display: "flex", gap: "var(--s2)", width: "100%" }}>
              <input type="email" className="em-input" placeholder="your@email.com" value={email} onChange={(e) => setEmail(e.target.value)} required aria-label="Email for newsletter" style={{ flex: 1 }} />
              <button type="submit" className="em-btn em-btn-primary">Subscribe</button>
            </form>
          )}
          <a href="https://wa.me/201001234567" style={{ display: "inline-flex", alignItems: "center", gap: "var(--s2)", color: "var(--ink-700)", fontSize: ".875rem", fontWeight: 600, textDecoration: "none" }}>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path fillRule="evenodd" clipRule="evenodd" d="M12 2C6.477 2 2 6.477 2 12c0 1.89.52 3.66 1.43 5.17L2 22l4.95-1.41A9.97 9.97 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm-1.5 13.5c-2.5-1.5-4-4-4-4s.5-1 1-1.5c.5-.5.5-1 0-1.5s-1.5-2-2-2c-.5 0-1 .5-1.5 1.5S3 9.5 4 11s3.5 4.5 6 5.5 4 .5 4.5 0 1-1.5.5-2-1.5-1.5-2-2-.5-.5-1-.5c-.5.5-.5.5-1.5 1.5z" fill="var(--success)" />
            </svg>
            Chat with us on WhatsApp
          </a>
        </div>
      </div>
    </section>
  );
}

export default function Home() {
  return (
    <>
      <Hero />
      <BrandsStrip />
      <TrustStrip />
      <ShopByCategory />
      <section className="em-section-sm">
        <div className="em-container">
          <Rail title="Best Sellers" products={bestSellers} viewAllHref="/collections/best-sellers" />
        </div>
      </section>
      <SectionRule />
      <section className="em-section-sm">
        <div className="em-container">
          <Rail title="Offers" products={offers} viewAllHref="/collections/offers" />
        </div>
      </section>
      <SectionRule />
      <section className="em-section-sm">
        <div className="em-container">
          <Rail title="New Arrivals" products={newArrivals} viewAllHref="/collections/new" />
        </div>
      </section>
      <ReviewBand />
      <Newsletter />
    </>
  );
}
