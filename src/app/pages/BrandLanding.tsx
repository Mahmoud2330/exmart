import { useState, useMemo } from "react";
import { useParams } from "react-router";
import { BRANDS, getProductsByBrand } from "../data";
import { ProductCard, Pagination, TrustLockup, Breadcrumb } from "../components/em";

const PER_PAGE = 12;

export default function BrandLanding() {
  const { slug } = useParams<{ slug: string }>();
  const brand = BRANDS.find((b) => b.slug === slug);
  const allProducts = brand ? getProductsByBrand(brand.name) : [];

  const [page, setPage] = useState(1);
  const [sort, setSort] = useState("default");
  const [inStockOnly, setInStockOnly] = useState(false);

  const filtered = useMemo(() => {
    let p = [...allProducts];
    if (inStockOnly) p = p.filter((x) => x.inStock);
    if (sort === "price-asc") p.sort((a, b) => a.price - b.price);
    if (sort === "price-desc") p.sort((a, b) => b.price - a.price);
    if (sort === "rating") p.sort((a, b) => b.rating - a.rating);
    return p;
  }, [allProducts, inStockOnly, sort]);

  const paginated = filtered.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  if (!brand) {
    return (
      <div className="em-container" style={{ paddingBlock: "var(--s16)", textAlign: "center" }}>
        <h1 className="em-h2">Brand not found</h1>
      </div>
    );
  }

  return (
    <>
      {/* Hero band */}
      <div style={{ borderBottom: "1px solid var(--ink-200)", background: "var(--ink-50)" }}>
        <div className="em-container" style={{ paddingBlock: "var(--s12)" }}>
          <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Brands", href: "/brands" }, { label: brand.name }]} />
          <div style={{ display: "flex", alignItems: "flex-start", gap: "var(--s6)", flexWrap: "wrap", marginTop: "var(--s4)" }}>
            <div style={{ width: 80, height: 80, borderRadius: "50%", background: brand.color + "22", display: "flex", alignItems: "center", justifyContent: "center", flexShrink: 0 }}>
              <span style={{ fontWeight: 700, fontSize: "2rem", color: brand.color }}>{brand.name[0]}</span>
            </div>
            <div style={{ flex: 1, minWidth: 240 }}>
              <h1 className="em-h2" style={{ marginBottom: "var(--s2)" }}>{brand.name}</h1>
              <TrustLockup brand={brand} />
              <p className="em-body" style={{ color: "var(--ink-500)", marginTop: "var(--s4)", maxWidth: "72ch" }}>{brand.description}</p>
            </div>
          </div>
        </div>
      </div>

      {/* Products */}
      <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s6)" }}>
          <p className="em-body-s" style={{ color: "var(--ink-500)" }}>{filtered.length} products</p>
          <div style={{ display: "flex", gap: "var(--s3)", alignItems: "center", flexWrap: "wrap" }}>
            <label style={{ display: "flex", alignItems: "center", gap: "var(--s2)", cursor: "pointer", minHeight: 36 }}>
              <input type="checkbox" checked={inStockOnly} onChange={(e) => { setInStockOnly(e.target.checked); setPage(1); }} style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
              <span className="em-body-s">In stock only</span>
            </label>
            <select className="em-input" value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} style={{ minHeight: 40, width: "auto", fontSize: ".875rem" }} aria-label="Sort">
              <option value="default">Sort: Featured</option>
              <option value="price-asc">Price ↑</option>
              <option value="price-desc">Price ↓</option>
              <option value="rating">Top Rated</option>
            </select>
          </div>
        </div>

        <div className="em-grid">
          {paginated.map((p) => <ProductCard key={p.id} product={p} />)}
        </div>

        <div style={{ marginTop: "var(--s10)" }}>
          <Pagination page={page} total={filtered.length} perPage={PER_PAGE} onChange={(p) => { setPage(p); window.scrollTo(0, 0); }} />
        </div>
      </div>
    </>
  );
}
