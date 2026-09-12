import { createContext, useContext, useState, useCallback, useEffect, ReactNode } from "react";
import { PRODUCTS } from "./data";
import type { Product } from "./data";

const CART_KEY = "exmart:cart";
const WISHLIST_KEY = "exmart:wishlist";

function loadCart(): CartItem[] {
  try {
    const raw = localStorage.getItem(CART_KEY);
    if (!raw) return [];
    const parsed: { productId: string; qty: number; variant?: string }[] = JSON.parse(raw);
    const items: CartItem[] = [];
    for (const entry of parsed) {
      const product = PRODUCTS.find((p) => p.id === entry.productId);
      if (product) items.push({ product, qty: entry.qty, variant: entry.variant });
    }
    return items;
  } catch {
    return [];
  }
}

function loadWishlist(): string[] {
  try {
    const raw = localStorage.getItem(WISHLIST_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

export interface CartItem {
  product: Product;
  qty: number;
  variant?: string;
}

interface StoreCtx {
  cart: CartItem[];
  wishlist: string[]; // product ids
  addToCart: (product: Product, qty?: number, variant?: string) => void;
  removeFromCart: (productId: string) => void;
  updateQty: (productId: string, qty: number) => void;
  clearCart: () => void;
  toggleWishlist: (productId: string) => void;
  isWishlisted: (productId: string) => boolean;
  cartCount: number;
  cartTotal: number;
  miniCartOpen: boolean;
  setMiniCartOpen: (v: boolean) => void;
}

const StoreContext = createContext<StoreCtx | null>(null);

export function StoreProvider({ children }: { children: ReactNode }) {
  const [cart, setCart] = useState<CartItem[]>(loadCart);
  const [wishlist, setWishlist] = useState<string[]>(loadWishlist);
  const [miniCartOpen, setMiniCartOpen] = useState(false);

  useEffect(() => {
    try {
      const serializable = cart.map((i) => ({ productId: i.product.id, qty: i.qty, variant: i.variant }));
      localStorage.setItem(CART_KEY, JSON.stringify(serializable));
    } catch {
      // localStorage unavailable (private mode, quota, etc.) — cart still works for this session
    }
  }, [cart]);

  useEffect(() => {
    try {
      localStorage.setItem(WISHLIST_KEY, JSON.stringify(wishlist));
    } catch {
      // localStorage unavailable — wishlist still works for this session
    }
  }, [wishlist]);

  const addToCart = useCallback((product: Product, qty = 1, variant?: string) => {
    setCart((prev) => {
      const existing = prev.find((i) => i.product.id === product.id && i.variant === variant);
      if (existing) {
        return prev.map((i) =>
          i.product.id === product.id && i.variant === variant
            ? { ...i, qty: i.qty + qty }
            : i
        );
      }
      return [...prev, { product, qty, variant }];
    });
    setMiniCartOpen(true);
  }, []);

  const removeFromCart = useCallback((productId: string) => {
    setCart((prev) => prev.filter((i) => i.product.id !== productId));
  }, []);

  const updateQty = useCallback((productId: string, qty: number) => {
    if (qty < 1) return;
    setCart((prev) => prev.map((i) => (i.product.id === productId ? { ...i, qty } : i)));
  }, []);

  const clearCart = useCallback(() => setCart([]), []);

  const toggleWishlist = useCallback((productId: string) => {
    setWishlist((prev) =>
      prev.includes(productId) ? prev.filter((id) => id !== productId) : [...prev, productId]
    );
  }, []);

  const isWishlisted = useCallback(
    (productId: string) => wishlist.includes(productId),
    [wishlist]
  );

  const cartCount = cart.reduce((s, i) => s + i.qty, 0);
  const cartTotal = cart.reduce((s, i) => s + i.product.price * i.qty, 0);

  return (
    <StoreContext.Provider
      value={{
        cart,
        wishlist,
        addToCart,
        removeFromCart,
        updateQty,
        clearCart,
        toggleWishlist,
        isWishlisted,
        cartCount,
        cartTotal,
        miniCartOpen,
        setMiniCartOpen,
      }}
    >
      {children}
    </StoreContext.Provider>
  );
}

export function useStore() {
  const ctx = useContext(StoreContext);
  if (!ctx) throw new Error("useStore must be used within StoreProvider");
  return ctx;
}
