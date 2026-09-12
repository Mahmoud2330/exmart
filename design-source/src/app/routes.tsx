import { createBrowserRouter } from "react-router";
import Root from "./Root";
import Home from "./pages/Home";
import Shop from "./pages/Shop";
import CategoryIndex from "./pages/CategoryIndex";
import PLP from "./pages/PLP";
import PDP from "./pages/PDP";
import BrandIndex from "./pages/BrandIndex";
import BrandLanding from "./pages/BrandLanding";
import Collection from "./pages/Collection";
import Cart from "./pages/Cart";
import Checkout from "./pages/Checkout";
import Confirmation from "./pages/Confirmation";
import Account from "./pages/Account";
import Wishlist from "./pages/Wishlist";
import Search from "./pages/Search";
import About from "./pages/About";
import Contact from "./pages/Contact";
import { ShippingReturns, Payment, PrivacyTerms, FAQ, NotFound, RouteError } from "./pages/InfoPages";

export const router = createBrowserRouter([
  {
    path: "/",
    Component: Root,
    ErrorBoundary: RouteError,
    children: [
      { index: true, Component: Home },
      { path: "shop", Component: Shop },
      { path: "search", Component: Search },
      { path: "categories", Component: CategoryIndex },
      { path: "categories/:slug", Component: PLP },
      { path: "brands", Component: BrandIndex },
      { path: "brands/:slug", Component: BrandLanding },
      { path: "collections/:slug", Component: Collection },
      { path: "products/:slug", Component: PDP },
      { path: "cart", Component: Cart },
      { path: "checkout", Component: Checkout },
      { path: "confirmation", Component: Confirmation },
      { path: "wishlist", Component: Wishlist },
      { path: "account", Component: Account },
      { path: "account/:section", Component: Account },
      { path: "tracking", Component: Account },
      { path: "about", Component: About },
      { path: "contact", Component: Contact },
      { path: "shipping", Component: ShippingReturns },
      { path: "payment", Component: Payment },
      { path: "privacy", Component: PrivacyTerms },
      { path: "faq", Component: FAQ },
      { path: "*", Component: NotFound },
    ],
  },
]);
