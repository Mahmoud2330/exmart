import { useState, useEffect, useMemo } from "react";
import { useSearchParams, Link } from "react-router";
import { searchProducts, CATEGORIES, BRANDS } from "../data";
import { ProductCard, Pagination } from "../components/em";

const PER_PAGE = 12;

export default function Search() {
  const [searchParams] = useSearchParams();
  const q = searchParams.get("q") ?? "";
  const [page, setPage] = useState(1);
  const [sort, setSort] = useState("default");

  useEffect(() => { setPage(1); }, [q]);

  const results = useMemo(() => {
    let r = searchProducts(q);
    if (sort === "price-asc") r = [...r].sort((a, b) => a.price - b.price);
    if (sort === "price-desc") r = [...r].sort((a, b) => b.price - a.price);
    if (sort === "rating") r = [...r].sort((a, b) => b.rating - a.rating);
    return r;
  }, [q, sort]);

  const paginated = results.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      {q ? (
        <>
          <div style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s6)" }}>
            <h1 className="em-h3">
              {results.length > 0
                ? <>{results.length} result{results.length !== 1 ? "s" : ""} for <em style={{ fontStyle: "normal", color: "var(--ink-500)" }}>"{q}"</em></>
                : <>No results for <em style={{ fontStyle: "normal", color: "var(--ink-500)" }}>"{q}"</em></>
              }
            </h1>
            {results.length > 0 && (
              <select className="em-input" value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} style={{ minHeight: 40, width: "auto", fontSize: ".875rem" }} aria-label="Sort">
                <option value="default">Sort: Relevance</option>
                <option value="price-asc">Price ↑</option>
                <option value="price-desc">Price ↓</option>
                <option value="rating">Top Rated</option>
              </select>
            )}
          </div>

          {results.length === 0 ? (
            <div style={{ paddingBlock: "var(--s8)" }}>
              <p className="em-body" style={{ color: "var(--ink-500)", marginBottom: "var(--s8)" }}>
                We could not find anything matching "{q}". Try a different search term, or browse by category or brand below.
              </p>
              <div style={{ display: "flex", flexDirection: "column", gap: "var(--s8)" }}>
                <div>
                  <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Popular Categories</p>
                  <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap" }}>
                    {CATEGORIES.map((c) => (
                      <Link key={c.slug} to={`/categories/${c.slug}`} className="em-chip">{c.name}</Link>
                    ))}
                  </div>
                </div>
                <div>
                  <p className="em-overline" style={{ color: "var(--ink-500)", marginBottom: "var(--s4)" }}>Brands</p>
                  <div style={{ display: "flex", gap: "var(--s2)", flexWrap: "wrap" }}>
                    {BRANDS.map((b) => (
                      <Link key={b.slug} to={`/brands/${b.slug}`} className="em-chip">{b.name}</Link>
                    ))}
                  </div>
                </div>
              </div>
            </div>
          ) : (
            <>
              <div className="em-grid">
                {paginated.map((p) => <ProductCard key={p.id} product={p} />)}
              </div>
              <div style={{ marginTop: "var(--s10)" }}>
                <Pagination page={page} total={results.length} perPage={PER_PAGE} onChange={(p) => { setPage(p); window.scrollTo(0, 0); }} />
              </div>
            </>
          )}
        </>
      ) : (
        <div style={{ paddingBlock: "var(--s12)", textAlign: "center" }}>
          <h1 className="em-h2" style={{ marginBottom: "var(--s4)" }}>Search</h1>
          <p className="em-body" style={{ color: "var(--ink-500)" }}>Use the search bar above to find products, brands, and categories.</p>
        </div>
      )}
    </div>
  );
}
