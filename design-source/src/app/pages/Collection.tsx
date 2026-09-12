import { useState, useMemo } from "react";
import { useParams } from "react-router";
import { PRODUCTS, getProductsByCollection } from "../data";
import { ProductCard, Pagination, Breadcrumb } from "../components/em";

const COLLECTION_LABELS: Record<string, string> = {
  "wipes": "Wipes",
  "makeup-removal": "Makeup Removal",
  "disinfectants-sanitizers": "Disinfectants & Sanitizers",
  "offers": "Offers",
  "new": "New Arrivals",
  "best-sellers": "Best Sellers",
};

const COLLECTION_MAP: Record<string, string> = {
  "wipes": "Wipes",
  "makeup-removal": "Makeup Removal",
  "disinfectants-sanitizers": "Disinfectants & Sanitizers",
  "offers": "Offers",
  "new": "New",
  "best-sellers": "Best Sellers",
};

const PER_PAGE = 12;

export default function Collection() {
  const { slug } = useParams<{ slug: string }>();
  const collectionKey = COLLECTION_MAP[slug ?? ""];
  const label = COLLECTION_LABELS[slug ?? ""] ?? "Collection";

  const all = useMemo(() => {
    if (!collectionKey) return [];
    if (slug === "offers") return PRODUCTS.filter((p) => p.compareAt !== undefined);
    if (slug === "new") return PRODUCTS.filter((p) => p.isNew);
    if (slug === "best-sellers") return PRODUCTS.filter((p) => p.isBestSeller);
    return getProductsByCollection(collectionKey);
  }, [slug, collectionKey]);

  const [page, setPage] = useState(1);
  const [sort, setSort] = useState("default");

  const sorted = useMemo(() => {
    let p = [...all];
    if (sort === "price-asc") p.sort((a, b) => a.price - b.price);
    if (sort === "price-desc") p.sort((a, b) => b.price - a.price);
    if (sort === "rating") p.sort((a, b) => b.rating - a.rating);
    return p;
  }, [all, sort]);

  const paginated = sorted.slice((page - 1) * PER_PAGE, page * PER_PAGE);

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label }]} />
      <div style={{ display: "flex", alignItems: "baseline", justifyContent: "space-between", gap: "var(--s4)", flexWrap: "wrap", marginBottom: "var(--s8)" }}>
        <h1 className="em-h2">{label} <span style={{ fontSize: "1rem", color: "var(--ink-400)", fontWeight: 400 }}>({sorted.length})</span></h1>
        <select className="em-input" value={sort} onChange={(e) => { setSort(e.target.value); setPage(1); }} style={{ minHeight: 40, width: "auto", fontSize: ".875rem" }} aria-label="Sort">
          <option value="default">Sort: Featured</option>
          <option value="price-asc">Price ↑</option>
          <option value="price-desc">Price ↓</option>
          <option value="rating">Top Rated</option>
        </select>
      </div>
      {paginated.length === 0 ? (
        <p className="em-body" style={{ color: "var(--ink-400)", paddingBlock: "var(--s12)" }}>No products in this collection yet.</p>
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
  );
}
