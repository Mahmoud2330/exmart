import { useState, useMemo } from "react";
import { PRODUCTS, BRANDS, CATEGORIES } from "../data";
import { ProductCard, Pagination, Breadcrumb } from "../components/em";

const PER_PAGE = 12;

export default function Shop() {
  const [page, setPage] = useState(1);
  const [sort, setSort] = useState("default");
  const [brandFilter, setBrandFilter] = useState<string[]>([]);
  const [catFilter, setCatFilter] = useState<string[]>([]);

  const sorted = useMemo(() => {
    let p = [...PRODUCTS];
    if (brandFilter.length) p = p.filter((x) => brandFilter.includes(x.brand));
    if (catFilter.length) p = p.filter((x) => catFilter.includes(x.category));
    if (sort === "price-asc") p.sort((a, b) => a.price - b.price);
    if (sort === "price-desc") p.sort((a, b) => b.price - a.price);
    if (sort === "rating") p.sort((a, b) => b.rating - a.rating);
    if (sort === "new") p.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0));
    return p;
  }, [sort, brandFilter, catFilter]);

  const paginated = sorted.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  const toggleBrand = (b: string) => {
    setBrandFilter((prev) => prev.includes(b) ? prev.filter((x) => x !== b) : [...prev, b]);
    setPage(1);
  };
  const toggleCat = (c: string) => {
    setCatFilter((prev) => prev.includes(c) ? prev.filter((x) => x !== c) : [...prev, c]);
    setPage(1);
  };
  const clearAll = () => { setBrandFilter([]); setCatFilter([]); setPage(1); };

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Shop" }]} />
      <div style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s6)" }}>
        <h1 className="em-h2">All Products <span style={{ fontSize: "1rem", color: "var(--ink-400)", fontWeight: 400 }}>({sorted.length})</span></h1>
        <select className="em-input" value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} style={{ minHeight: 40, width: "auto", fontSize: ".875rem" }} aria-label="Sort products">
          <option value="default">Sort: Featured</option>
          <option value="price-asc">Price: Low → High</option>
          <option value="price-desc">Price: High → Low</option>
          <option value="rating">Top Rated</option>
          <option value="new">New Arrivals</option>
        </select>
      </div>

      {/* Active filters */}
      {(brandFilter.length > 0 || catFilter.length > 0) && (
        <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap", marginBottom: "var(--s4)" }}>
          {brandFilter.map((b) => (
            <button key={b} className="em-chip active" onClick={() => toggleBrand(b)}>
              {b} ×
            </button>
          ))}
          {catFilter.map((c) => (
            <button key={c} className="em-chip active" onClick={() => toggleCat(c)}>
              {CATEGORIES.find((x) => x.slug === c)?.name ?? c} ×
            </button>
          ))}
          <button className="em-chip" onClick={clearAll} style={{ color: "var(--ink-400)" }}>Clear all</button>
        </div>
      )}

      <div className="em-two-col" style={{ alignItems: "start" }}>
        {/* Sidebar */}
        <aside style={{ display: "flex", flexDirection: "column", gap: "var(--s6)" }}>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Brand</p>
            {BRANDS.map((b) => (
              <label key={b.slug} style={{ display: "flex", alignItems: "center", gap: "var(--s2)", paddingBlock: "var(--s1)", cursor: "pointer", minHeight: 36 }}>
                <input type="checkbox" checked={brandFilter.includes(b.name)} onChange={() => toggleBrand(b.name)} style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
                <span className="em-body-s">{b.name}</span>
              </label>
            ))}
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Category</p>
            {CATEGORIES.map((c) => (
              <label key={c.slug} style={{ display: "flex", alignItems: "center", gap: "var(--s2)", paddingBlock: "var(--s1)", cursor: "pointer", minHeight: 36 }}>
                <input type="checkbox" checked={catFilter.includes(c.slug)} onChange={() => toggleCat(c.slug)} style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
                <span className="em-body-s">{c.name}</span>
              </label>
            ))}
          </div>
          <div>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Availability</p>
            <label style={{ display: "flex", alignItems: "center", gap: "var(--s2)", cursor: "pointer", minHeight: 36 }}>
              <input type="checkbox" style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
              <span className="em-body-s">In stock only</span>
            </label>
          </div>
        </aside>

        <div>
          {paginated.length === 0 ? (
            <p className="em-body" style={{ color: "var(--ink-500)", paddingBlock: "var(--s12)" }}>No products match your filters.</p>
          ) : (
            <>
              <div className="em-grid">
                {paginated.map((p) => <ProductCard key={p.id} product={p} />)}
              </div>
              <div style={{ marginTop: "var(--s10)" }}>
                <Pagination page={page} total={sorted.length} perPage={PER_PAGE} onChange={(p) => { setPage(p); window.scrollTo(0, 0); }} />
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
