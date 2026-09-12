import { Link } from "react-router";
import { CATEGORIES, PRODUCTS, CATEGORY_COLORS } from "../data";
import { Breadcrumb } from "../components/em";

export default function CategoryIndex() {
  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Categories" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>All Categories</h1>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(min(100%, 280px), 1fr))", gap: "var(--s6)" }}>
        {CATEGORIES.map((cat) => {
          const count = PRODUCTS.filter((p) => p.category === cat.slug).length;
          return (
            <Link key={cat.slug} to={`/categories/${cat.slug}`}
              style={{ textDecoration: "none", border: "1px solid var(--ink-200)", borderRadius: "var(--r-md)", overflow: "hidden", display: "block" }}
              onMouseEnter={(e) => (e.currentTarget.style.borderColor = "var(--ink-400)")}
              onMouseLeave={(e) => (e.currentTarget.style.borderColor = "var(--ink-200)")}
            >
              <div style={{ aspectRatio: "4/3", background: CATEGORY_COLORS[cat.slug] ?? "var(--ink-50)", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <span style={{ fontSize: "3rem" }}>
                  {cat.slug === "personal-care" ? "🧴" : cat.slug === "hair-care" ? "💆" : cat.slug === "skin-care" ? "✨" : cat.slug === "baby-care" ? "👶" : cat.slug === "feminine-care" ? "🌸" : cat.slug === "home-care" ? "🏠" : cat.slug === "home-diagnostics" ? "🔬" : "💊"}
                </span>
              </div>
              <div style={{ padding: "var(--s5)" }}>
                <p style={{ fontWeight: 600, fontSize: "1.125rem", color: "var(--ink-900)", marginBottom: "var(--s1)" }}>{cat.name}</p>
                {cat.subcategories.length > 0 && (
                  <p className="em-caption" style={{ color: "var(--ink-500)", marginBottom: "var(--s2)" }}>
                    {cat.subcategories.join(" · ")}
                  </p>
                )}
                <p className="em-caption" style={{ color: "var(--ink-400)" }}>{count} products →</p>
              </div>
            </Link>
          );
        })}
      </div>
    </div>
  );
}
