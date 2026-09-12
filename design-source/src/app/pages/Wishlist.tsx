import { useStore } from "../store";
import { PRODUCTS } from "../data";
import { ProductCard, Breadcrumb, EmptyState } from "../components/em";

export default function Wishlist() {
  const { wishlist } = useStore();
  const products = PRODUCTS.filter((p) => wishlist.includes(p.id));

  return (
    <div className="em-container" style={{ paddingBlock: "var(--s8)" }}>
      <Breadcrumb crumbs={[{ label: "Home", href: "/" }, { label: "Wishlist" }]} />
      <h1 className="em-h2" style={{ marginBottom: "var(--s8)" }}>
        Wishlist {products.length > 0 && <span style={{ fontSize: "1rem", color: "var(--ink-400)", fontWeight: 400 }}>({products.length})</span>}
      </h1>
      {products.length === 0 ? (
        <EmptyState title="Your wishlist is empty" body="Save products you love by tapping the heart icon on any product card." cta="Discover products" href="/shop" />
      ) : (
        <div className="em-grid">
          {products.map((p) => <ProductCard key={p.id} product={p} />)}
        </div>
      )}
    </div>
  );
}
