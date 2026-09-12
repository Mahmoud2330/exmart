import { useState, useRef, useEffect } from "react";
import { Link, NavLink, Outlet, useNavigate } from "react-router";
import { Search, ShoppingCart, Heart, User, X, ChevronDown, Menu, SlidersHorizontal } from "lucide-react";
import logoSrc from "../imports/ExMart.png";
import { useStore } from "./store";
import { BRANDS, CATEGORIES } from "./data";
import { TrustLockup } from "./components/em";
import type { Brand } from "./data";

// ── Logo ─────────────────────────────────────────────────
function Logo() {
  return (
    <Link to="/" aria-label="exMart home" style={{ textDecoration: "none", flexShrink: 0, display: "flex", alignItems: "center" }}>
      <img src={logoSrc} alt="exMart Online Shopping" style={{ height: 48, width: "auto", display: "block" }} />
    </Link>
  );
}

// ── Mini cart ────────────────────────────────────────────
function MiniCart() {
  const { cart, cartTotal, cartCount, removeFromCart, miniCartOpen, setMiniCartOpen } = useStore();
  const shippingThreshold = 300;
  const shipping = cartTotal >= shippingThreshold ? 0 : 35;

  if (!miniCartOpen) return null;
  return (
    <>
      <div className="em-backdrop" onClick={() => setMiniCartOpen(false)} aria-hidden="true" />
      <aside className="em-drawer" aria-label="Shopping cart">
        <div className="em-drawer-header">
          <h2 className="em-h4">Cart ({cartCount})</h2>
          <button className="em-btn-icon" onClick={() => setMiniCartOpen(false)} aria-label="Close cart">
            <X size={20} />
          </button>
        </div>
        <div className="em-drawer-body">
          {cart.length === 0 ? (
            <div style={{ textAlign: "center", paddingTop: "var(--s12)", color: "var(--ink-400)" }}>
              <ShoppingCart size={40} style={{ marginBottom: "var(--s4)" }} />
              <p className="em-body-s">Your cart is empty.</p>
            </div>
          ) : (
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
              {cartTotal < shippingThreshold && (
                <div style={{ background: "var(--accent-100)", borderRadius: "var(--r-sm)", padding: "var(--s3) var(--s4)", fontSize: ".8125rem", color: "var(--accent-600)", fontWeight: 500 }}>
                  Add EGP {(shippingThreshold - cartTotal).toFixed(2)} more for free shipping
                </div>
              )}
              {cart.map((item) => (
                <div key={item.product.id} style={{ display: "flex", gap: "var(--s3)", paddingBottom: "var(--s4)", borderBottom: "1px solid var(--ink-100)" }}>
                  <Link to={`/products/${item.product.slug}`} onClick={() => setMiniCartOpen(false)}
                    style={{ flexShrink: 0, width: 72, height: 72, background: "var(--ink-50)", borderRadius: "var(--r-sm)", display: "flex", alignItems: "center", justifyContent: "center", overflow: "hidden", border: "1px solid var(--ink-200)" }}>
                    <svg width="72" height="72" viewBox="0 0 72 72" fill="none" aria-hidden="true">
                      <rect width="72" height="72" fill={item.product.color + "22"} />
                      <text x="36" y="46" textAnchor="middle" fontFamily="system-ui" fontWeight="700" fontSize="24" fill={item.product.color} opacity=".85">{item.product.brand[0]}</text>
                    </svg>
                  </Link>
                  <div style={{ flex: 1, minWidth: 0 }}>
                    <Link to={`/products/${item.product.slug}`} onClick={() => setMiniCartOpen(false)} style={{ textDecoration: "none" }}>
                      <p style={{ fontSize: ".8125rem", color: "var(--ink-800)", lineHeight: 1.3, display: "-webkit-box", WebkitLineClamp: 2, WebkitBoxOrient: "vertical", overflow: "hidden" }}>{item.product.name}</p>
                    </Link>
                    {item.variant && <p style={{ fontSize: ".75rem", color: "var(--ink-500)", marginTop: 2 }}>{item.variant}</p>}
                    <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginTop: "var(--s2)" }}>
                      <span style={{ fontSize: ".875rem", fontWeight: 600, fontVariantNumeric: "tabular-nums" }}>EGP {(item.product.price * item.qty).toFixed(2)}</span>
                      <span style={{ fontSize: ".8125rem", color: "var(--ink-500)" }}>× {item.qty}</span>
                    </div>
                  </div>
                  <button className="em-btn-icon" onClick={() => removeFromCart(item.product.id)} aria-label="Remove" style={{ alignSelf: "flex-start", color: "var(--ink-400)", minWidth: 32, minHeight: 32 }}>
                    <X size={14} />
                  </button>
                </div>
              ))}
            </div>
          )}
        </div>
        {cart.length > 0 && (
          <div className="em-drawer-footer">
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: ".875rem" }}>
              <span style={{ color: "var(--ink-500)" }}>Subtotal</span>
              <span style={{ fontWeight: 600, fontVariantNumeric: "tabular-nums" }}>EGP {cartTotal.toFixed(2)}</span>
            </div>
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: ".875rem" }}>
              <span style={{ color: "var(--ink-500)" }}>Shipping</span>
              <span style={{ fontWeight: 600 }}>{shipping === 0 ? "Free" : `EGP ${shipping.toFixed(2)}`}</span>
            </div>
            <hr className="em-rule" />
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "1rem", fontWeight: 700 }}>
              <span>Total</span>
              <span style={{ fontVariantNumeric: "tabular-nums" }}>EGP {(cartTotal + shipping).toFixed(2)}</span>
            </div>
            <Link
              to="/checkout"
              className="em-btn em-btn-accent em-btn-lg"
              onClick={() => setMiniCartOpen(false)}
              style={{ width: "100%", textAlign: "center" }}
            >
              Checkout
            </Link>
            <Link
              to="/cart"
              className="em-btn em-btn-secondary"
              onClick={() => setMiniCartOpen(false)}
              style={{ width: "100%", textAlign: "center" }}
            >
              View cart
            </Link>
          </div>
        )}
      </aside>
    </>
  );
}

// ── Categories mega menu ─────────────────────────────────
function CategoriesMega({ onClose }: { onClose: () => void }) {
  return (
    <div className="em-mega" role="dialog" aria-label="Categories menu">
      <div className="em-mega-inner em-container">
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(180px,1fr))", gap: "var(--s4)" }}>
          {CATEGORIES.map((cat) => (
            <div key={cat.slug}>
              <Link
                to={`/categories/${cat.slug}`}
                onClick={onClose}
                style={{ fontWeight: 600, fontSize: ".9375rem", color: "var(--ink-800)", textDecoration: "none", display: "block", paddingBottom: "var(--s2)" }}
              >
                {cat.name}
              </Link>
              {cat.subcategories.map((sub) => (
                <Link
                  key={sub}
                  to={`/categories/${cat.slug}?sub=${encodeURIComponent(sub)}`}
                  onClick={onClose}
                  style={{ display: "block", fontSize: ".8125rem", color: "var(--ink-500)", textDecoration: "none", paddingBlock: "3px" }}
                >
                  {sub}
                </Link>
              ))}
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── Brands mega menu ─────────────────────────────────────
function BrandsMega({ onClose }: { onClose: () => void }) {
  const distributed = BRANDS.filter((b) => b.type === "distributed");
  const house = BRANDS.filter((b) => b.type === "house");

  const BrandItem = ({ brand }: { brand: Brand }) => (
    <Link
      to={`/brands/${brand.slug}`}
      onClick={onClose}
      style={{ display: "flex", flexDirection: "column", gap: "var(--s1)", padding: "var(--s3)", border: "1px solid var(--ink-200)", borderRadius: "var(--r-sm)", textDecoration: "none", transition: "background .15s" }}
      onMouseEnter={(e) => (e.currentTarget.style.background = "var(--ink-50)")}
      onMouseLeave={(e) => (e.currentTarget.style.background = "transparent")}
    >
      <span style={{ fontWeight: 600, fontSize: ".9375rem", color: "var(--ink-800)" }}>{brand.name}</span>
      <span style={{ fontSize: ".75rem", color: "var(--ink-500)", lineHeight: 1.3 }}>
        {brand.type === "distributed" ? "Sole distributor in Egypt" : "exMart exclusive brand"}
      </span>
    </Link>
  );

  return (
    <div className="em-mega" role="dialog" aria-label="Brands menu">
      <div className="em-mega-inner em-container">
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--s8)" }}>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Brands we distribute</p>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--s3)" }}>
              {distributed.map((b) => <BrandItem key={b.slug} brand={b} />)}
            </div>
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>exMart brands</p>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "var(--s3)" }}>
              {house.map((b) => <BrandItem key={b.slug} brand={b} />)}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// ── Mobile drawer nav ────────────────────────────────────
function MobileNav({ open, onClose }: { open: boolean; onClose: () => void }) {
  if (!open) return null;
  return (
    <>
      <div className="em-backdrop" onClick={onClose} aria-hidden="true" />
      <nav className="em-drawer em-drawer-l" aria-label="Mobile navigation">
        <div className="em-drawer-header">
          <Logo />
          <button className="em-btn-icon" onClick={onClose} aria-label="Close menu"><X size={20} /></button>
        </div>
        <div className="em-drawer-body" style={{ padding: 0 }}>
          <div style={{ borderBottom: "1px solid var(--ink-200)", padding: "var(--s4)" }}>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Shop</p>
            <Link to="/shop" onClick={onClose} style={{ display: "block", padding: "var(--s2) 0", fontWeight: 600, textDecoration: "none", color: "var(--ink-800)" }}>All Products</Link>
            {CATEGORIES.map((c) => (
              <Link key={c.slug} to={`/categories/${c.slug}`} onClick={onClose}
                style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)", fontSize: ".9375rem" }}>
                {c.name}
              </Link>
            ))}
          </div>
          <div style={{ padding: "var(--s4)" }}>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Brands</p>
            {BRANDS.map((b) => (
              <Link key={b.slug} to={`/brands/${b.slug}`} onClick={onClose}
                style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)", fontSize: ".9375rem" }}>
                {b.name}
              </Link>
            ))}
          </div>
          <div style={{ padding: "var(--s4)", borderTop: "1px solid var(--ink-200)" }}>
            <Link to="/account" onClick={onClose} style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)" }}>My Account</Link>
            <Link to="/wishlist" onClick={onClose} style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)" }}>Wishlist</Link>
            <Link to="/about" onClick={onClose} style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)" }}>About</Link>
            <Link to="/contact" onClick={onClose} style={{ display: "block", padding: "var(--s2) 0", textDecoration: "none", color: "var(--ink-700)" }}>Contact</Link>
          </div>
        </div>
      </nav>
    </>
  );
}

// ── Footer ───────────────────────────────────────────────
function Footer() {
  const [email, setEmail] = useState("");
  const [subscribed, setSubscribed] = useState(false);

  return (
    <footer style={{ borderTop: "1px solid var(--ink-200)", background: "var(--ink-50)", marginTop: "auto" }}>
      {/* 4-col grid */}
      <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%,180px),1fr))", gap: "var(--s8)" }}>
          {/* Categories */}
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Categories</p>
            {CATEGORIES.map((c) => (
              <Link key={c.slug} to={`/categories/${c.slug}`}
                style={{ display: "block", fontSize: ".875rem", color: "var(--ink-500)", textDecoration: "none", paddingBlock: "3px", lineHeight: 1.5 }}>
                {c.name}
              </Link>
            ))}
          </div>
          {/* Brands */}
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Brands</p>
            {BRANDS.map((b) => (
              <Link key={b.slug} to={`/brands/${b.slug}`}
                style={{ display: "block", fontSize: ".875rem", color: "var(--ink-500)", textDecoration: "none", paddingBlock: "3px" }}>
                {b.name}
              </Link>
            ))}
          </div>
          {/* Service */}
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Service</p>
            {[
              { label: "Shipping & Returns", href: "/shipping" },
              { label: "Payment Methods", href: "/payment" },
              { label: "FAQ", href: "/faq" },
              { label: "Track Order", href: "/account/orders" },
              { label: "Contact Us", href: "/contact" },
            ].map((l) => (
              <Link key={l.href} to={l.href}
                style={{ display: "block", fontSize: ".875rem", color: "var(--ink-500)", textDecoration: "none", paddingBlock: "3px" }}>
                {l.label}
              </Link>
            ))}
          </div>
          {/* About + newsletter */}
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--s4)" }}>
            <div>
              <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>About</p>
              {[
                { label: "Our Story", href: "/about" },
                { label: "Privacy & Terms", href: "/privacy" },
              ].map((l) => (
                <Link key={l.href} to={l.href}
                  style={{ display: "block", fontSize: ".875rem", color: "var(--ink-500)", textDecoration: "none", paddingBlock: "3px" }}>
                  {l.label}
                </Link>
              ))}
            </div>
            <div>
              <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Newsletter</p>
              {subscribed ? (
                <p className="em-body-s" style={{ color: "var(--success)" }}>You're subscribed — thank you!</p>
              ) : (
                <form onSubmit={(e) => { e.preventDefault(); if (email) setSubscribed(true); }} style={{ display: "flex", gap: "var(--s2)" }}>
                  <input
                    type="email"
                    className="em-input"
                    placeholder="your@email.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                    style={{ minHeight: 40, fontSize: ".875rem", flex: 1 }}
                    aria-label="Email address for newsletter"
                  />
                  <button type="submit" className="em-btn em-btn-primary em-btn-sm">Join</button>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Strip */}
      <div style={{ borderTop: "1px solid var(--ink-200)", background: "var(--paper)" }}>
        <div className="em-container" style={{ paddingBlock: "var(--s6)", display: "flex", flexWrap: "wrap", gap: "var(--s6)", alignItems: "flex-start", justifyContent: "space-between" }}>
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--s1)" }}>
            <Logo />
            <p className="em-caption" style={{ color: "var(--ink-500)", maxWidth: 260, lineHeight: 1.5 }}>
              12 El-Nozha St, Heliopolis, Cairo, Egypt<br />
              +20 100 123 4567 · hello@exmart.eg
            </p>
          </div>
          <div style={{ display: "flex", flexDirection: "column", gap: "var(--s3)" }}>
            <div style={{ display: "flex", flexWrap: "wrap", gap: "var(--s3)" }}>
              {BRANDS.filter((b) => b.type === "distributed").slice(0, 2).map((b) => (
                <TrustLockup key={b.slug} brand={b} />
              ))}
            </div>
            <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap" }}>
              {["COD", "Fawry", "Meeza", "Visa", "MC", "Instapay"].map((p) => (
                <span key={p} className="em-payment-icon">{p}</span>
              ))}
            </div>
          </div>
        </div>
        <div style={{ borderTop: "1px solid var(--ink-100)", padding: "var(--s3) 0", textAlign: "center" }}>
          <p className="em-caption" style={{ color: "var(--ink-400)" }}>© {new Date().getFullYear()} exMart Egypt. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}

// ── Announcement bar ─────────────────────────────────────
function AnnouncementBar() {
  const [visible, setVisible] = useState(true);
  if (!visible) return null;
  return (
    <div className="em-announce" role="banner">
      <span>Free shipping on orders over EGP 300 · Cash on delivery available nationwide</span>
      <button className="em-announce-close" onClick={() => setVisible(false)} aria-label="Dismiss announcement">
        <X size={14} />
      </button>
    </div>
  );
}

// ── Root layout ──────────────────────────────────────────
export default function Root() {
  const { cartCount, wishlist, setMiniCartOpen } = useStore();
  const [catOpen, setCatOpen] = useState(false);
  const [brandOpen, setBrandOpen] = useState(false);
  const [mobileNavOpen, setMobileNavOpen] = useState(false);
  const [headerSearchOpen, setHeaderSearchOpen] = useState(false);
  const [headerQ, setHeaderQ] = useState("");
  const headerSearchRef = useRef<HTMLInputElement>(null);
  const navRef = useRef<HTMLDivElement>(null);
  const headerNav = useNavigate();

  useEffect(() => {
    if (headerSearchOpen) setTimeout(() => headerSearchRef.current?.focus(), 60);
  }, [headerSearchOpen]);

  // Close megas on outside click
  useEffect(() => {
    function handler(e: MouseEvent) {
      if (navRef.current && !navRef.current.contains(e.target as Node)) {
        setCatOpen(false);
        setBrandOpen(false);
      }
    }
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  return (
    <div style={{ minHeight: "100vh", display: "flex", flexDirection: "column", fontFamily: "var(--font)", background: "var(--paper)", color: "var(--ink-900)" }}>
      <AnnouncementBar />

      {/* Header */}
      <header style={{ borderBottom: "1px solid var(--ink-200)", background: "var(--paper)", position: "sticky", top: 0, zIndex: 20 }}>
        <style>{`
          @media(min-width:768px){
            .hdr-desktop{display:flex!important;}
          }
          @media(max-width:767px){
            #desktop-nav{display:none!important;}
            #hamburger{display:flex!important;}
          }
        `}</style>

        {/* Header search overlay (desktop) */}
        {headerSearchOpen && (
          <>
            <div onClick={() => { setHeaderSearchOpen(false); setHeaderQ(""); }} style={{ position: "fixed", inset: 0, zIndex: 25 }} aria-hidden="true" />
            <div style={{ position: "absolute", top: "100%", left: 0, right: 0, background: "var(--paper)", borderBottom: "1px solid var(--ink-200)", padding: "var(--s4)", zIndex: 26, boxShadow: "0 8px 24px rgba(11,11,12,.10)", display: "flex", gap: "var(--s3)", alignItems: "center" }}>
              <form onSubmit={(e) => { e.preventDefault(); if (headerQ.trim()) { headerNav(`/search?q=${encodeURIComponent(headerQ.trim())}`); setHeaderSearchOpen(false); setHeaderQ(""); } }} role="search" style={{ flex: 1, position: "relative" }}>
                <Search size={18} style={{ position: "absolute", left: 12, top: "50%", transform: "translateY(-50%)", color: "var(--ink-400)", pointerEvents: "none" }} aria-hidden="true" />
                <input ref={headerSearchRef} className="em-input" placeholder="Search products, brands…" value={headerQ} onChange={(e) => setHeaderQ(e.target.value)} aria-label="Search" style={{ paddingInlineStart: 40, minHeight: 48 }} />
              </form>
              <button className="em-btn-icon" onClick={() => { setHeaderSearchOpen(false); setHeaderQ(""); }} aria-label="Close search"><X size={20} /></button>
            </div>
          </>
        )}

        <div className="em-container" style={{ display: "flex", alignItems: "center", minHeight: 72, position: "relative" }}>

          {/* Left slot */}
          <div style={{ display: "flex", alignItems: "center", gap: "var(--s1)", zIndex: 1 }}>
            <button className="em-btn-icon" onClick={() => setMobileNavOpen(true)} aria-label="Open menu" style={{ display: "none" }} id="hamburger">
              <Menu size={22} />
            </button>
            <button className="em-btn-icon hdr-desktop" onClick={() => setHeaderSearchOpen((v) => !v)} aria-label="Search" style={{ display: "none" }}>
              <Search size={20} />
            </button>
            <Link to="/wishlist" className="em-btn-icon hdr-desktop" aria-label={`Wishlist (${wishlist.length} items)`} style={{ position: "relative", textDecoration: "none", display: "none" }}>
              <Heart size={20} />
              {wishlist.length > 0 && <span style={{ position: "absolute", top: 6, right: 6, width: 8, height: 8, borderRadius: "50%", background: "var(--sale)", border: "2px solid var(--paper)" }} />}
            </Link>
          </div>

          {/* Logo — always perfectly centered */}
          <div style={{ position: "absolute", left: "50%", transform: "translateX(-50%)" }}>
            <Logo />
          </div>

          {/* Right slot */}
          <div style={{ marginLeft: "auto", display: "flex", alignItems: "center", gap: "var(--s1)", zIndex: 1 }}>
            <Link to="/wishlist" className="em-btn-icon" id="mobile-wishlist" aria-label={`Wishlist (${wishlist.length} items)`} style={{ position: "relative", textDecoration: "none" }}>
              <Heart size={20} />
              {wishlist.length > 0 && <span style={{ position: "absolute", top: 6, right: 6, width: 8, height: 8, borderRadius: "50%", background: "var(--sale)", border: "2px solid var(--paper)" }} />}
            </Link>
            <style>{`@media(min-width:768px){#mobile-wishlist{display:none!important;}}`}</style>
            <Link to="/account" className="em-btn-icon hdr-desktop" aria-label="My account" style={{ textDecoration: "none", display: "none" }}>
              <User size={20} />
            </Link>
            <button className="em-btn-icon hdr-desktop" onClick={() => setMiniCartOpen(true)} aria-label={`Cart, ${cartCount} items`} style={{ position: "relative", display: "none" }}>
              <ShoppingCart size={20} />
              {cartCount > 0 && (
                <span style={{ position: "absolute", top: 4, right: 4, minWidth: 18, height: 18, borderRadius: "var(--r-full)", background: "var(--accent-600)", color: "#fff", fontSize: ".6875rem", fontWeight: 700, display: "flex", alignItems: "center", justifyContent: "center", border: "2px solid var(--paper)", paddingInline: 3 }}>
                  {cartCount}
                </span>
              )}
            </button>
          </div>
        </div>

        {/* Desktop nav */}
        <nav ref={navRef} id="desktop-nav" style={{ borderTop: "1px solid var(--ink-100)", position: "relative" }} aria-label="Main navigation">
          <div className="em-container" style={{ display: "flex", gap: "var(--s1)", justifyContent: "center" }}>
            <NavButton
              label="Categories"
              open={catOpen}
              onClick={() => { setCatOpen((v) => !v); setBrandOpen(false); }}
            />
            <NavButton
              label="Brands"
              open={brandOpen}
              onClick={() => { setBrandOpen((v) => !v); setCatOpen(false); }}
            />
            <hr style={{ border: "none", borderLeft: "1px solid var(--ink-200)", height: 24, alignSelf: "center", margin: "0 var(--s1)" }} aria-hidden="true" />
            {[
              { label: "Wipes", href: "/collections/wipes" },
              { label: "Disinfectants", href: "/collections/disinfectants-sanitizers" },
              { label: "Offers", href: "/collections/offers" },
              { label: "New", href: "/collections/new" },
            ].map((l) => (
              <NavLink key={l.href} to={l.href}
                style={({ isActive }) => ({
                  padding: "var(--s3) var(--s4)", fontSize: ".875rem", fontWeight: 600,
                  color: isActive ? "var(--ink-900)" : "var(--ink-500)", textDecoration: "none",
                  borderBottom: isActive ? "2px solid var(--ink-900)" : "2px solid transparent",
                  display: "inline-flex", alignItems: "center", minHeight: 44, transition: "color .15s",
                })}
              >
                {l.label}
              </NavLink>
            ))}
          </div>
          {catOpen && <CategoriesMega onClose={() => setCatOpen(false)} />}
          {brandOpen && <BrandsMega onClose={() => setBrandOpen(false)} />}
        </nav>
      </header>

      {/* Mobile search row */}

      <MobileNav open={mobileNavOpen} onClose={() => setMobileNavOpen(false)} />
      <MiniCart />

      <style>{`@media(max-width:767px){#main-content{padding-bottom:88px;}}`}</style>
      <main id="main-content" style={{ flex: 1 }}>
        <Outlet />
      </main>

      <Footer />
      <FloatingPill />
    </div>
  );
}

// ── Floating pill ─────────────────────────────────────────
function FloatingPill() {
  const { cartCount, setMiniCartOpen } = useStore();
  const [searchOpen, setSearchOpen] = useState(false);
  const [filtersOpen, setFiltersOpen] = useState(false);
  const [q, setQ] = useState("");
  const [activeFilter, setActiveFilter] = useState<string | null>(null);
  const nav = useNavigate();
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (searchOpen) setTimeout(() => inputRef.current?.focus(), 80);
  }, [searchOpen]);

  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key === "Escape") { setSearchOpen(false); setFiltersOpen(false); }
    }
    document.addEventListener("keydown", onKey);
    return () => document.removeEventListener("keydown", onKey);
  }, []);

  const submitSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (q.trim()) { nav(`/search?q=${encodeURIComponent(q.trim())}`); setSearchOpen(false); setQ(""); }
  };

  const applyFilter = (slug: string) => {
    nav(`/categories/${slug}`);
    setFiltersOpen(false);
    setActiveFilter(null);
  };

  return (
    <>
      {/* Search overlay */}
      {searchOpen && (
        <>
          <div onClick={() => setSearchOpen(false)} style={{ position: "fixed", inset: 0, background: "rgba(11,11,12,.5)", zIndex: 90, backdropFilter: "blur(4px)" }} aria-hidden="true" />
          <div style={{ position: "fixed", top: 0, left: 0, right: 0, zIndex: 91, background: "var(--paper)", padding: "var(--s4)", boxShadow: "0 4px 24px rgba(11,11,12,.12)", display: "flex", gap: "var(--s3)", alignItems: "center" }}>
            <form onSubmit={submitSearch} role="search" style={{ flex: 1, position: "relative" }}>
              <Search size={18} style={{ position: "absolute", left: 12, top: "50%", transform: "translateY(-50%)", color: "var(--ink-400)", pointerEvents: "none" }} aria-hidden="true" />
              <input
                ref={inputRef}
                className="em-input"
                placeholder="Search products, brands…"
                value={q}
                onChange={(e) => setQ(e.target.value)}
                aria-label="Search"
                style={{ paddingInlineStart: 40, minHeight: 48 }}
              />
            </form>
            <button className="em-btn-icon" onClick={() => setSearchOpen(false)} aria-label="Close search"><X size={20} /></button>
          </div>
        </>
      )}

      {/* Filters bottom sheet */}
      {filtersOpen && (
        <>
          <div onClick={() => setFiltersOpen(false)} style={{ position: "fixed", inset: 0, background: "rgba(11,11,12,.45)", zIndex: 90 }} aria-hidden="true" />
          <div style={{ position: "fixed", bottom: 0, left: 0, right: 0, zIndex: 91, background: "var(--paper)", borderRadius: "var(--r-lg) var(--r-lg) 0 0", padding: "var(--s5)", boxShadow: "0 -8px 32px rgba(11,11,12,.12)", maxHeight: "75vh", overflowY: "auto" }}>
            <div style={{ width: 40, height: 4, borderRadius: 2, background: "var(--ink-200)", margin: "0 auto var(--s5)" }} />
            <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", marginBottom: "var(--s5)" }}>
              <h3 className="em-h4">Browse by Category</h3>
              <button className="em-btn-icon" onClick={() => setFiltersOpen(false)} aria-label="Close"><X size={18} /></button>
            </div>
            <div style={{ display: "flex", flexWrap: "wrap", gap: "var(--s2)", marginBottom: "var(--s6)" }}>
              {CATEGORIES.map((cat) => (
                <button
                  key={cat.slug}
                  className={`em-chip${activeFilter === cat.slug ? " active" : ""}`}
                  onClick={() => setActiveFilter(cat.slug === activeFilter ? null : cat.slug)}
                >
                  {cat.name}
                </button>
              ))}
            </div>
            <div style={{ display: "flex", gap: "var(--s3)" }}>
              <button
                className="em-btn em-btn-primary"
                style={{ flex: 1 }}
                disabled={!activeFilter}
                onClick={() => activeFilter && applyFilter(activeFilter)}
              >
                Show results
              </button>
              <button className="em-btn em-btn-secondary" onClick={() => { nav("/shop"); setFiltersOpen(false); }}>
                All products
              </button>
            </div>
          </div>
        </>
      )}

      {/* The pill — mobile only */}
      <style>{`@media(min-width:768px){#floating-pill{display:none!important;}}`}</style>
      <div id="floating-pill" style={{
        position: "fixed", bottom: 24, left: "50%", transform: "translateX(-50%)",
        zIndex: 80, display: "flex", alignItems: "center",
        background: "var(--accent-600)", borderRadius: "var(--r-full)",
        boxShadow: "0 8px 32px rgba(42,68,232,.35), 0 2px 8px rgba(42,68,232,.2)",
        padding: "6px 8px", gap: 2,
      }}>
        {/* Search */}
        <button
          onClick={() => { setSearchOpen(true); setFiltersOpen(false); }}
          aria-label="Search"
          style={{ display: "flex", alignItems: "center", justifyContent: "center", width: 44, height: 44, borderRadius: "var(--r-full)", border: "none", background: searchOpen ? "rgba(255,255,255,.15)" : "transparent", color: "#fff", cursor: "pointer", transition: "background .15s" }}
        >
          <Search size={18} />
        </button>

        {/* Divider */}
        <span style={{ width: 1, height: 20, background: "rgba(255,255,255,.15)", flexShrink: 0 }} />

        {/* Filters */}
        <button
          onClick={() => { setFiltersOpen(true); setSearchOpen(false); }}
          aria-label="Browse categories"
          style={{ display: "flex", alignItems: "center", justifyContent: "center", width: 44, height: 44, borderRadius: "var(--r-full)", border: "none", background: filtersOpen ? "rgba(255,255,255,.15)" : "transparent", color: "#fff", cursor: "pointer", transition: "background .15s" }}
        >
          <SlidersHorizontal size={18} />
        </button>

        {/* Divider */}
        <span style={{ width: 1, height: 20, background: "rgba(255,255,255,.15)", flexShrink: 0 }} />

        {/* Cart */}
        <button
          onClick={() => { setMiniCartOpen(true); setSearchOpen(false); setFiltersOpen(false); }}
          aria-label={`Cart, ${cartCount} items`}
          style={{ display: "flex", alignItems: "center", justifyContent: "center", width: 44, height: 44, borderRadius: "var(--r-full)", border: "none", background: "transparent", color: "#fff", cursor: "pointer", transition: "background .15s", position: "relative" }}
        >
          <ShoppingCart size={18} />
          {cartCount > 0 && (
            <span style={{ position: "absolute", top: 6, right: 6, minWidth: 16, height: 16, borderRadius: "var(--r-full)", background: "var(--sale)", fontSize: ".625rem", fontWeight: 700, display: "flex", alignItems: "center", justifyContent: "center", border: "2px solid var(--ink-900)", paddingInline: 2 }}>
              {cartCount}
            </span>
          )}
        </button>
      </div>
    </>
  );
}

function NavButton({ label, open, onClick }: { label: string; open: boolean; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      aria-expanded={open}
      aria-haspopup="true"
      style={{
        display: "inline-flex", alignItems: "center", gap: "var(--s1)",
        padding: "var(--s3) var(--s4)", fontSize: ".875rem", fontWeight: 600,
        color: open ? "var(--ink-900)" : "var(--ink-500)", background: "none", border: "none",
        borderBottom: open ? "2px solid var(--ink-900)" : "2px solid transparent",
        cursor: "pointer", fontFamily: "var(--font)", minHeight: 44, transition: "color .15s",
      }}
    >
      {label}
      <ChevronDown size={14} style={{ transform: open ? "rotate(180deg)" : "none", transition: "transform .2s" }} />
    </button>
  );
}
