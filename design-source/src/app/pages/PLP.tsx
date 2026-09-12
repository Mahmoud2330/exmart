import { useState, useMemo } from "react";
import { useParams, useSearchParams } from "react-router";
import { X } from "lucide-react";
import { CATEGORIES, CATEGORY_NAMES, getProductsByCategory } from "../data";
import { ProductCard, Pagination, Breadcrumb } from "../components/em";

const PER_PAGE = 12;

export default function PLP() {
  const { slug } = useParams<{ slug: string }>();
  const [searchParams, setSearchParams] = useSearchParams();
  const activeSub = searchParams.get("sub") ?? "";

  const cat = CATEGORIES.find((c) => c.slug === slug);
  const catName = CATEGORY_NAMES[slug ?? ""] ?? "Products";

  const [page, setPage] = useState(1);
  const [sort, setSort] = useState("default");
  const [brandFilter, setBrandFilter] = useState<string[]>([]);
  const [inStockOnly, setInStockOnly] = useState(false);
  const [drawerOpen, setDrawerOpen] = useState(false);

  const allForCat = getProductsByCategory(slug ?? "");
  const brandsInCat = [...new Set(allForCat.map((p) => p.brand))];

  const filtered = useMemo(() => {
    let p = allForCat;
    if (activeSub) p = p.filter((x) => x.subcategory === activeSub);
    if (brandFilter.length) p = p.filter((x) => brandFilter.includes(x.brand));
    if (inStockOnly) p = p.filter((x) => x.inStock);
    if (sort === "price-asc") p = [...p].sort((a, b) => a.price - b.price);
    if (sort === "price-desc") p = [...p].sort((a, b) => b.price - a.price);
    if (sort === "rating") p = [...p].sort((a, b) => b.rating - a.rating);
    if (sort === "new") p = [...p].sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0));
    return p;
  }, [allForCat, activeSub, brandFilter, inStockOnly, sort]);

  const paginated = filtered.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  const setSub = (sub: string) => {
    if (sub) setSearchParams({ sub }); else setSearchParams({});
    setPage(1);
  };
  const toggleBrand = (b: string) => {
    setBrandFilter((prev) => prev.includes(b) ? prev.filter((x) => x !== b) : [...prev, b]);
    setPage(1);
  };
  const clearFilters = () => { setBrandFilter([]); setInStockOnly(false); setSub(""); };

  const hasFilters = brandFilter.length > 0 || inStockOnly || activeSub;

  const FilterPanel = () => (
    <div style={{ display: "flex", flexDirection: "column", gap: "var(--s6)" }}>
      <div>
        <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Brand</p>
        {brandsInCat.map((b) => (
          <label key={b} style={{ display: "flex", alignItems: "center", gap: "var(--s2)", paddingBlock: "var(--s1)", cursor: "pointer", minHeight: 36 }}>
            <input type="checkbox" checked={brandFilter.includes(b)} onChange={() => toggleBrand(b)} style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
            <span className="em-body-s">{b}</span>
          </label>
        ))}
      </div>
      <div>
        <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s3)" }}>Availability</p>
        <label style={{ display: "flex", alignItems: "center", gap: "var(--s2)", cursor: "pointer", minHeight: 36 }}>
          <input type="checkbox" checked={inStockOnly} onChange={(e) => { setInStockOnly(e.target.checked); setPage(1); }} style={{ accentColor: "var(--ink-900)", width: 16, height: 16 }} />
          <span className="em-body-s">In stock only</span>
        </label>
      </div>
      {hasFilters && (
        <button className="em-btn em-btn-ghost em-btn-sm" onClick={clearFilters} style={{ alignSelf: "flex-start" }}>Clear all filters</button>
      )}
    </div>
  );

  return (
    <>
      {/* Mobile filter drawer */}
      {drawerOpen && (
        <>
          <div className="em-backdrop" onClick={() => setDrawerOpen(false)} aria-hidden="true" />
          <aside className="em-drawer em-drawer-l" aria-label="Filters">
            <div className="em-drawer-header">
              <h2 className="em-h4">Filters</h2>
              <button className="em-btn-icon" onClick={() => setDrawerOpen(false)} aria-label="Close filters"><X size={20} /></button>
            </div>
            <div className="em-drawer-body"><FilterPanel /></div>
            <div className="em-drawer-footer">
              <button className="em-btn em-btn-primary" onClick={() => setDrawerOpen(false)} style={{ width: "100%" }}>Show {filtered.length} results</button>
            </div>
          </aside>
        </>
      )}

      <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
        <Breadcrumb crumbs={[
          { label: "Home", href: "/" },
          { label: "Categories", href: "/categories" },
          { label: catName },
        ]} />

        <h1 className="em-h2" style={{ marginBottom: "var(--s5)" }}>{catName}</h1>

        {/* Subcategory chips (Home Care) */}
        {cat && cat.subcategories.length > 0 && (
          <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap", marginBottom: "var(--s5)" }}>
            <button className={`em-chip${!activeSub ? " active" : ""}`} onClick={() => setSub("")}>All</button>
            {cat.subcategories.map((sub) => (
              <button key={sub} className={`em-chip${activeSub === sub ? " active" : ""}`} onClick={() => setSub(sub)}>
                {sub}
              </button>
            ))}
          </div>
        )}

        {/* Sort + filter controls */}
        <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s6)", paddingTop: "var(--s2)", borderTop: "1px solid var(--ink-100)" }}>
          <p className="em-body-s" style={{ color: "var(--ink-500)" }}>{filtered.length} products</p>
          <div style={{ display: "flex", gap: "var(--s3)", alignItems: "center" }}>
            <button className="em-btn em-btn-secondary em-btn-sm" onClick={() => setDrawerOpen(true)} id="filter-btn">
              Filters {hasFilters ? `(${brandFilter.length + (inStockOnly ? 1 : 0) + (activeSub ? 1 : 0)})` : ""}
            </button>
            <style>{`@media(min-width:1024px){#filter-btn{display:none!important;}}`}</style>
            <select className="em-input" value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} style={{ minHeight: 40, width: "auto", fontSize: ".875rem" }} aria-label="Sort">
              <option value="default">Sort: Featured</option>
              <option value="price-asc">Price ↑</option>
              <option value="price-desc">Price ↓</option>
              <option value="rating">Top Rated</option>
              <option value="new">New</option>
            </select>
          </div>
        </div>

        {/* Active filter chips */}
        {hasFilters && (
          <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap", marginBottom: "var(--s4)" }}>
            {brandFilter.map((b) => (
              <button key={b} className="em-chip active" onClick={() => toggleBrand(b)}>{b} ×</button>
            ))}
            {inStockOnly && <button className="em-chip active" onClick={() => setInStockOnly(false)}>In stock ×</button>}
            <button className="em-chip" onClick={clearFilters} style={{ color: "var(--ink-400)" }}>Clear all</button>
          </div>
        )}

        <div className="em-two-col" style={{ alignItems: "start" }}>
          {/* Desktop sidebar */}
          <aside id="plp-sidebar" style={{ position: "sticky", top: 140 }}>
            <style>{`@media(max-width:1023px){#plp-sidebar{display:none!important;}}`}</style>
            <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s5)" }}>Filter</p>
            <FilterPanel />
          </aside>

          <div>
            {paginated.length === 0 ? (
              <div style={{ paddingBlock: "var(--s16)", textAlign: "center" }}>
                <p className="em-body" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>No products match your filters.</p>
                <button className="em-btn em-btn-secondary" onClick={clearFilters}>Clear filters</button>
              </div>
            ) : (
              <>
                <div className="em-grid">
                  {paginated.map((p) => <ProductCard key={p.id} product={p} />)}
                </div>
                <div style={{ marginTop: "var(--s10)" }}>
                  <Pagination page={page} total={filtered.length} perPage={PER_PAGE} onChange={(p) => { setPage(p); window.scrollTo(0, 0); }} />
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </>
  );
}
