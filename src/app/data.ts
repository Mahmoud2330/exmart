export type BrandType = "distributed" | "house";

export interface Brand {
  slug: string;
  name: string;
  type: BrandType;
  tagline: string;
  description: string;
  color: string; // brand accent for placeholder
}

export interface Product {
  id: string;
  slug: string;
  name: string;
  brand: string;
  category: string;
  subcategory?: string;
  collections: string[];
  price: number;
  compareAt?: number;
  rating: number;
  reviewCount: number;
  inStock: boolean;
  stockQty?: number;
  isNew?: boolean;
  isBestSeller?: boolean;
  description: string;
  specifications: Record<string, string>;
  howToUse: string;
  color: string; // category hue for placeholder SVG
  variants?: { label: string; options: string[] };
}

export const BRANDS: Brand[] = [
  {
    slug: "diversey",
    name: "Diversey",
    type: "distributed",
    tagline: "Professional hygiene & cleaning — trusted in 175 countries.",
    description:
      "Diversey is a world leader in professional cleaning and hygiene solutions. exMart is the official and sole distributor of Diversey products in Egypt, supplying the same formulations used in hospitals, hotels, and food-service facilities worldwide.",
    color: "#0066CC",
  },
  {
    slug: "grace",
    name: "Grace",
    type: "distributed",
    tagline: "Everyday surface care made to professional standards.",
    description:
      "Grace delivers high-performance household cleaning products developed for the Egyptian home. As the official and sole distributor in Egypt, exMart guarantees genuine Grace formulations with full manufacturer backing.",
    color: "#2D6A4F",
  },
  {
    slug: "oview",
    name: "Oview",
    type: "distributed",
    tagline: "Precision diagnostics for the home.",
    description:
      "Oview designs accurate, CE-marked home diagnostic devices including pulse oximeters, blood glucose monitors, and smart weighing scales. exMart is the official and sole distributor of Oview products in Egypt.",
    color: "#6A0572",
  },
  {
    slug: "surecheck",
    name: "SureCheck",
    type: "distributed",
    tagline: "Reliable health monitoring you can trust.",
    description:
      "SureCheck develops clinically validated health and diagnostics products — from blood pressure monitors to rapid antigen tests. exMart is the official and sole distributor of SureCheck products in Egypt.",
    color: "#C41230",
  },
  {
    slug: "qualita",
    name: "Qualita",
    type: "house",
    tagline: "Egyptian-made quality, globally sourced ingredients.",
    description:
      "Qualita is an exMart exclusive brand — formulated and quality-tested by our in-house team. From anti-bacterial wipes to scalp care, Qualita brings lab-grade standards to everyday personal and home care.",
    color: "#E07B39",
  },
  {
    slug: "vodlia",
    name: "Vodlia",
    type: "house",
    tagline: "Science-led personal care for Egyptian skin.",
    description:
      "Vodlia is an exMart exclusive brand focused on evidence-based skincare, feminine care, and intimate hygiene. Every Vodlia formula is dermatologist-reviewed and pH-appropriate for Egyptian climate conditions.",
    color: "#E76F51",
  },
  {
    slug: "eliv",
    name: "Eliv",
    type: "house",
    tagline: "Gentle care crafted for babies from day one.",
    description:
      "Eliv is an exMart exclusive baby care brand. Every product is hypoallergenic, paediatrician-reviewed, and free from harsh preservatives, fragrances, and sulphates — developed specifically for sensitive newborn skin.",
    color: "#74B3CE",
  },
  {
    slug: "verve",
    name: "Verve",
    type: "house",
    tagline: "Hair & skin science with visible results.",
    description:
      "Verve is an exMart exclusive brand combining active dermatology ingredients with appealing textures. The range covers hair growth, keratin repair, vitamin C brightening, and SPF protection — all developed for the Egyptian market.",
    color: "#9B5DE5",
  },
];

export const CATEGORIES = [
  { slug: "personal-care", name: "Personal Care", subcategories: [] },
  { slug: "hair-care", name: "Hair Care", subcategories: [] },
  { slug: "skin-care", name: "Skin Care", subcategories: [] },
  { slug: "baby-care", name: "Baby Care", subcategories: [] },
  { slug: "feminine-care", name: "Feminine Care", subcategories: [] },
  {
    slug: "home-care",
    name: "Home Care",
    subcategories: [
      "Surface & Floor",
      "Kitchen",
      "Laundry & Detergents",
      "Air Care",
      "Restroom",
      "Disinfectants & Sanitizers",
    ],
  },
  { slug: "home-diagnostics", name: "Home Diagnostics", subcategories: [] },
  { slug: "health-protection", name: "Health & Protection", subcategories: [] },
];

export const COLLECTIONS = [
  "Wipes",
  "Makeup Removal",
  "Disinfectants & Sanitizers",
  "Offers",
  "New",
  "Best Sellers",
];

export const GOVERNORATES = [
  "Cairo","Giza","Alexandria","Dakahlia","Sharqia","Qalyubia","Kafr El Sheikh",
  "Gharbia","Menoufia","Beheira","Ismailia","Suez","Port Said","Damietta",
  "Faiyum","Beni Suef","Minya","Asyut","Sohag","Qena","Luxor","Aswan",
  "Red Sea","New Valley","Matrouh","North Sinai","South Sinai",
];

// ---------- Products ----------

export const PRODUCTS: Product[] = [
  // Personal Care (7)
  {
    id: "pc-001", slug: "qualita-antibacterial-cleansing-bar",
    name: "Qualita Anti-Bacterial Cleansing Bar 120 g",
    brand: "Qualita", category: "personal-care", collections: [],
    price: 45, rating: 4.4, reviewCount: 87, inStock: true, isBestSeller: true,
    description: "A gentle yet effective anti-bacterial cleansing bar formulated with triclosan-free actives. pH-balanced for daily use on face and body. Rinses cleanly without leaving residue.",
    specifications: { "Net Weight": "120 g", "Format": "Solid bar", "Skin Type": "All", "pH": "5.5–6.0" },
    howToUse: "Lather with warm water and massage onto skin for 20 seconds. Rinse thoroughly. For best results, use twice daily.",
    color: "#E07B39",
    variants: { label: "Scent", options: ["Unscented", "Aloe Vera", "Charcoal"] },
  },
  {
    id: "pc-002", slug: "vodlia-intimate-wash-250ml",
    name: "Vodlia Intimate Wash pH-Balanced 250 ml",
    brand: "Vodlia", category: "personal-care", collections: [],
    price: 89, compareAt: 110, rating: 4.7, reviewCount: 213, inStock: true, isBestSeller: true,
    description: "A soap-free, lactic acid-enhanced intimate wash that preserves the natural vaginal flora. Gynaecologist-reviewed and suitable for daily use. Fragrance-free formula.",
    specifications: { "Volume": "250 ml", "pH": "3.8–4.5", "Fragrance": "Free", "Preservative": "Phenoxyethanol" },
    howToUse: "Apply a small amount to the external intimate area during your shower. Rinse thoroughly with water. Do not use internally.",
    color: "#E76F51",
  },
  {
    id: "pc-003", slug: "qualita-micellar-water-200ml",
    name: "Qualita Micellar Water 200 ml",
    brand: "Qualita", category: "personal-care", collections: ["Makeup Removal"],
    price: 65, rating: 4.3, reviewCount: 56, inStock: true, isNew: true,
    description: "Triple-action micellar water that cleanses, tones, and removes light makeup in one step. No rinsing required. Enriched with panthenol and allantoin.",
    specifications: { "Volume": "200 ml", "Skin Type": "All, incl. sensitive", "Alcohol": "Free" },
    howToUse: "Saturate a cotton pad and gently wipe face, eyes, and lips. No rinsing needed. Can be used morning and night.",
    color: "#E07B39",
  },
  {
    id: "pc-004", slug: "verve-brightening-face-wash-150ml",
    name: "Verve Brightening Face Wash 150 ml",
    brand: "Verve", category: "personal-care", collections: [],
    price: 79, rating: 4.5, reviewCount: 134, inStock: true,
    description: "A vitamin C-infused foaming face wash that gently removes impurities while brightening dull skin. Niacinamide helps minimise pores. Suitable for oily and combination skin.",
    specifications: { "Volume": "150 ml", "Key Actives": "Vitamin C, Niacinamide", "Skin Type": "Oily, Combination" },
    howToUse: "Wet face, apply a small amount and work into a lather. Rinse well. Use morning and evening.",
    color: "#9B5DE5",
  },
  {
    id: "pc-005", slug: "qualita-antibacterial-wipes-72ct",
    name: "Qualita Anti-Bacterial Wipes 72-count",
    brand: "Qualita", category: "personal-care", collections: ["Wipes", "Best Sellers"],
    price: 55, compareAt: 75, rating: 4.6, reviewCount: 398, inStock: true, isBestSeller: true,
    description: "Thick, alcohol-based anti-bacterial wipes that eliminate 99.9% of common bacteria. Individually stacked in a resealable tub. Safe for hands, surfaces, and non-porous objects.",
    specifications: { "Count": "72 wipes", "Active": "Isopropyl alcohol 70%", "Size per wipe": "18 × 20 cm" },
    howToUse: "Remove wipe and unfold. Wipe hands or surface thoroughly. Allow to air-dry completely.",
    color: "#E07B39",
  },
  {
    id: "pc-006", slug: "vodlia-whitening-deodorant-50ml",
    name: "Vodlia Whitening Deodorant Roll-On 50 ml",
    brand: "Vodlia", category: "personal-care", collections: [],
    price: 69, rating: 4.2, reviewCount: 91, inStock: true,
    description: "24-hour odour protection with a niacinamide-based whitening formula that gradually reduces underarm hyperpigmentation. Alcohol-free and gentle on recently shaved skin.",
    specifications: { "Volume": "50 ml", "Protection": "24 hr", "Alcohol": "Free", "Whitening Active": "Niacinamide 3%" },
    howToUse: "Apply to clean, dry underarms. Allow to dry before dressing. Do not apply to broken or irritated skin.",
    color: "#E76F51",
  },
  {
    id: "pc-007", slug: "verve-spf50-sunscreen-100ml",
    name: "Verve SPF 50 Sunscreen Lotion 100 ml",
    brand: "Verve", category: "personal-care", collections: ["New"],
    price: 129, rating: 4.6, reviewCount: 67, inStock: true, isNew: true,
    description: "Broad-spectrum UVA/UVB protection with SPF 50+. Lightweight, non-greasy texture absorbs quickly. Enriched with vitamin E and aloe vera. Water-resistant for 40 minutes.",
    specifications: { "Volume": "100 ml", "SPF": "50+", "PA Rating": "PA++++", "Water Resistance": "40 min" },
    howToUse: "Apply generously 15 minutes before sun exposure. Reapply every 2 hours or after swimming and sweating.",
    color: "#9B5DE5",
  },

  // Hair Care (7)
  {
    id: "hc-001", slug: "verve-keratin-repair-shampoo-400ml",
    name: "Verve Keratin Repair Shampoo 400 ml",
    brand: "Verve", category: "hair-care", collections: ["Best Sellers"],
    price: 115, rating: 4.7, reviewCount: 289, inStock: true, isBestSeller: true,
    description: "Sulphate-free shampoo with hydrolysed keratin and argan oil. Rebuilds hair structure from the inside out, reducing breakage by up to 80% after 4 weeks. Suitable for all hair types.",
    specifications: { "Volume": "400 ml", "Key Actives": "Hydrolysed keratin, Argan oil", "Sulphate": "Free" },
    howToUse: "Apply to wet hair, lather thoroughly from roots to tips. Leave for 2 minutes. Rinse and follow with Verve Keratin Repair Conditioner.",
    color: "#9B5DE5",
    variants: { label: "Hair Type", options: ["All types", "Colour-treated", "Curly"] },
  },
  {
    id: "hc-002", slug: "verve-keratin-repair-conditioner-400ml",
    name: "Verve Keratin Repair Conditioner 400 ml",
    brand: "Verve", category: "hair-care", collections: [],
    price: 115, rating: 4.6, reviewCount: 201, inStock: true,
    description: "Partner conditioner to the Keratin Repair Shampoo. Quaternary protein complex seals the cuticle for frizz-free, mirror-smooth finish. Detangles easily without weighing hair down.",
    specifications: { "Volume": "400 ml", "Key Actives": "Quaternised keratin, Cetrimonium chloride", "Silicone": "Light, water-soluble" },
    howToUse: "After shampooing, apply to mid-lengths and ends. Leave for 3 minutes. Rinse with cool water for extra shine.",
    color: "#9B5DE5",
  },
  {
    id: "hc-003", slug: "verve-hair-growth-serum-100ml",
    name: "Verve Hair Growth Serum 100 ml",
    brand: "Verve", category: "hair-care", collections: ["Best Sellers"],
    price: 175, compareAt: 220, rating: 4.5, reviewCount: 177, inStock: true, isBestSeller: true,
    description: "Leave-in scalp serum with 5% biotin complex and 2% caffeine to stimulate hair follicles and extend the growth phase. Clinically tested — 72% of users reported visible density improvement after 12 weeks.",
    specifications: { "Volume": "100 ml", "Key Actives": "Biotin 5%, Caffeine 2%, Niacinamide 3%", "Format": "Leave-in serum" },
    howToUse: "Apply 6–8 drops directly to scalp sections. Massage in circular motions for 2 minutes. Do not rinse. Use daily for best results.",
    color: "#9B5DE5",
  },
  {
    id: "hc-004", slug: "qualita-deep-cleansing-shampoo-300ml",
    name: "Qualita Deep Cleansing Anti-Dandruff Shampoo 300 ml",
    brand: "Qualita", category: "hair-care", collections: [],
    price: 85, rating: 4.3, reviewCount: 112, inStock: true,
    description: "Zinc pyrithione 1% formula that controls dandruff-causing Malassezia fungi while deep-cleansing product buildup. Menthol provides a refreshing scalp sensation.",
    specifications: { "Volume": "300 ml", "Active": "Zinc pyrithione 1%", "Menthol": "0.5%" },
    howToUse: "Wet hair thoroughly. Apply, lather, and leave on scalp for 3 minutes before rinsing. Use 3 times per week.",
    color: "#E07B39",
  },
  {
    id: "hc-005", slug: "verve-argan-oil-hair-mask-250ml",
    name: "Verve Argan Oil Intensive Hair Mask 250 ml",
    brand: "Verve", category: "hair-care", collections: ["Offers"],
    price: 99, compareAt: 135, rating: 4.8, reviewCount: 143, inStock: true,
    description: "Deep-conditioning weekly treatment with 100% pure Moroccan argan oil and shea butter. Restores moisture balance in severely damaged and heat-styled hair.",
    specifications: { "Volume": "250 ml", "Key Actives": "Argan oil, Shea butter, Panthenol", "Use Frequency": "1–2× per week" },
    howToUse: "After shampooing, apply generously to towel-dried hair. Cover with a shower cap and leave for 10–15 minutes. Rinse thoroughly.",
    color: "#9B5DE5",
  },
  {
    id: "hc-006", slug: "verve-biotin-caffeine-tonic-150ml",
    name: "Verve Biotin + Caffeine Hair Tonic 150 ml",
    brand: "Verve", category: "hair-care", collections: ["New"],
    price: 145, rating: 4.4, reviewCount: 58, inStock: true, isNew: true,
    description: "Daily leave-in tonic targeting thinning at the crown and temples. Caffeine blocks DHT at the receptor site; biotin supports keratin synthesis. Light mist formula with no stickiness.",
    specifications: { "Volume": "150 ml", "Key Actives": "Caffeine 3%, Biotin complex, Saw Palmetto extract", "Format": "Spray mist" },
    howToUse: "Spray 5–8 pumps onto dry or damp scalp. Do not rinse. Style as usual. Use daily for a minimum of 90 days.",
    color: "#9B5DE5",
  },
  {
    id: "hc-007", slug: "qualita-scalp-scrub-200g",
    name: "Qualita Scalp Scrub 200 g",
    brand: "Qualita", category: "hair-care", collections: [],
    price: 75, rating: 4.1, reviewCount: 44, inStock: false,
    description: "Salt-based scalp exfoliator with tea tree oil and salicylic acid 0.5%. Removes dead skin, product buildup, and unclogs follicles. Use before shampooing for a clean slate.",
    specifications: { "Net Weight": "200 g", "Exfoliant": "Sea salt crystals", "Active": "Salicylic acid 0.5%, Tea tree oil" },
    howToUse: "Apply to wet scalp sections. Massage gently with fingertips for 2 minutes. Do not use on broken or irritated scalp. Rinse well and shampoo as normal. Use once a week.",
    color: "#E07B39",
  },

  // Skin Care (7)
  {
    id: "sk-001", slug: "vodlia-hyaluronic-acid-serum-30ml",
    name: "Vodlia Hyaluronic Acid Serum 30 ml",
    brand: "Vodlia", category: "skin-care", collections: ["Best Sellers"],
    price: 189, compareAt: 240, rating: 4.8, reviewCount: 321, inStock: true, isBestSeller: true,
    description: "Multi-weight hyaluronic acid serum with three molecular weights for deep and surface hydration. Instantly plumps fine lines and fortifies the skin barrier. Fragrance-free and suitable for all skin types.",
    specifications: { "Volume": "30 ml", "HA Concentration": "2% (3 molecular weights)", "Fragrance": "Free", "Shelf Life": "18 months" },
    howToUse: "Apply 3–4 drops to clean, damp skin. Pat gently. Follow with moisturiser to lock in hydration. Use morning and evening.",
    color: "#E76F51",
  },
  {
    id: "sk-002", slug: "verve-vitamin-c-brightening-cream-50ml",
    name: "Verve Vitamin C Brightening Cream 50 ml",
    brand: "Verve", category: "skin-care", collections: [],
    price: 165, rating: 4.6, reviewCount: 189, inStock: true,
    description: "Stable ascorbyl glucoside 3% cream that fades hyperpigmentation and dark spots over 8 weeks. Lightweight gel-cream texture. Suitable for oily and combination skin.",
    specifications: { "Volume": "50 ml", "Active": "Ascorbyl glucoside 3%, Alpha-arbutin 1%", "Skin Type": "Oily, Combination" },
    howToUse: "Apply a pea-sized amount to clean face and neck. Use in the morning, followed by SPF. For enhanced results, use with Vodlia Hyaluronic Acid Serum underneath.",
    color: "#9B5DE5",
  },
  {
    id: "sk-003", slug: "vodlia-niacinamide-toner-150ml",
    name: "Vodlia Niacinamide Toner 150 ml",
    brand: "Vodlia", category: "skin-care", collections: [],
    price: 119, rating: 4.5, reviewCount: 156, inStock: true,
    description: "Alcohol-free toner with niacinamide 5% and zinc PCA. Controls sebum, minimises pores, and brightens skin tone. Absorbs in seconds — no sticky residue.",
    specifications: { "Volume": "150 ml", "Key Actives": "Niacinamide 5%, Zinc PCA", "Alcohol": "Free" },
    howToUse: "After cleansing, apply with a cotton pad or press into skin with palms. Follow with serum and moisturiser. Use morning and/or evening.",
    color: "#E76F51",
  },
  {
    id: "sk-004", slug: "verve-retinol-night-cream-50ml",
    name: "Verve Retinol Night Cream 50 ml",
    brand: "Verve", category: "skin-care", collections: ["New"],
    price: 199, rating: 4.7, reviewCount: 94, inStock: true, isNew: true,
    description: "0.3% encapsulated retinol in a rich ceramide base for overnight cell turnover. Minimal irritation formula with squalane and bakuchiol as buffer actives.",
    specifications: { "Volume": "50 ml", "Active": "Retinol 0.3% (encapsulated), Bakuchiol 0.5%", "Use": "Night only" },
    howToUse: "Apply a small amount to clean, dry face 20 minutes before bed. Start with 2–3 nights per week; increase to nightly as tolerated. Always use SPF the next morning.",
    color: "#9B5DE5",
  },
  {
    id: "sk-005", slug: "qualita-makeup-removal-wipes-25ct",
    name: "Qualita Makeup Removal Wipes 25-count",
    brand: "Qualita", category: "skin-care", collections: ["Makeup Removal", "Wipes"],
    price: 49, compareAt: 65, rating: 4.3, reviewCount: 207, inStock: true, isBestSeller: true,
    description: "Oil-infused micellar wipes that dissolve waterproof makeup including mascara, eyeliner, and long-wear foundation. Aloe vera soothes skin after removal.",
    specifications: { "Count": "25 wipes", "Size": "20 × 22 cm", "Fragrance": "Light floral", "Alcohol": "Free" },
    howToUse: "Unfold wipe and gently press onto closed eye or face. Hold for 5 seconds, then wipe away. No rinsing needed, but rinsing is recommended for a thorough cleanse.",
    color: "#E07B39",
  },
  {
    id: "sk-006", slug: "vodlia-rose-water-mist-100ml",
    name: "Vodlia Rose Water Facial Mist 100 ml",
    brand: "Vodlia", category: "skin-care", collections: [],
    price: 79, rating: 4.4, reviewCount: 118, inStock: true,
    description: "Pure steam-distilled Bulgarian rose water with no added fragrance or alcohol. Hydrates, tones, and soothes redness. Excellent as a mid-day refresh or before serum application.",
    specifications: { "Volume": "100 ml", "Rose Water": "99%", "Alcohol": "Free", "Preservative": "Free (sterile)" },
    howToUse: "Mist onto clean skin or over makeup. Can be used as a toner or throughout the day for refreshment. Store in a cool, dark place.",
    color: "#E76F51",
  },
  {
    id: "sk-007", slug: "verve-spf30-daily-moisturiser-75ml",
    name: "Verve SPF 30 Daily Moisturiser 75 ml",
    brand: "Verve", category: "skin-care", collections: ["Best Sellers"],
    price: 145, rating: 4.6, reviewCount: 162, inStock: true, isBestSeller: true,
    description: "Lightweight daily moisturiser with SPF 30 and ceramide NP for barrier support. Non-comedogenic and suitable for oily to normal skin. Doubles as a base under makeup.",
    specifications: { "Volume": "75 ml", "SPF": "30", "Key Actives": "Ceramide NP, Hyaluronic acid, Zinc oxide" },
    howToUse: "Apply as the last step of your morning routine. Dot onto face and blend evenly. Reapply every 2 hours if staying outdoors.",
    color: "#9B5DE5",
  },

  // Baby Care (8)
  {
    id: "bc-001", slug: "eliv-baby-wipes-80ct",
    name: "Eliv Baby Wipes Fragrance-Free 80-count",
    brand: "Eliv", category: "baby-care", collections: ["Wipes", "Best Sellers"],
    price: 55, rating: 4.9, reviewCount: 512, inStock: true, isBestSeller: true,
    description: "Ultra-soft, 99% water-based wipes with no fragrance, alcohol, or parabens. Thick 3-layer fabric is gentle on newborn skin. Pop-up dispenser keeps wipes moist between uses.",
    specifications: { "Count": "80 wipes", "Material": "100% biodegradable plant fibre", "Fragrance": "Free", "Preservative": "Free" },
    howToUse: "Open lid and pull one wipe from the centre. Gently clean baby's skin from front to back. Dispose of in waste bin — do not flush.",
    color: "#74B3CE",
    variants: { label: "Pack size", options: ["Single (80ct)", "Twin Pack (160ct)", "Triple Pack (240ct)"] },
  },
  {
    id: "bc-002", slug: "eliv-baby-shampoo-wash-250ml",
    name: "Eliv Baby Shampoo & Wash 2-in-1 250 ml",
    brand: "Eliv", category: "baby-care", collections: [],
    price: 79, rating: 4.8, reviewCount: 278, inStock: true,
    description: "Tear-free, sulphate-free 2-in-1 body wash and shampoo for babies from birth. Enriched with chamomile extract to calm and soothe. Paediatric-dermatologist tested.",
    specifications: { "Volume": "250 ml", "Key Actives": "Chamomile extract, Panthenol", "Sulphate": "Free", "Tear-free": "Yes" },
    howToUse: "Apply a small amount to wet skin or hair. Lather gently with hands. Rinse thoroughly with warm water. Avoid contact with eyes.",
    color: "#74B3CE",
  },
  {
    id: "bc-003", slug: "eliv-baby-moisturising-lotion-200ml",
    name: "Eliv Baby Moisturising Lotion 200 ml",
    brand: "Eliv", category: "baby-care", collections: [],
    price: 69, rating: 4.7, reviewCount: 193, inStock: true,
    description: "Light, fast-absorbing lotion with oat extract and shea butter that locks in moisture for up to 24 hours. Safe from newborn stage. No artificial colouring.",
    specifications: { "Volume": "200 ml", "Key Actives": "Colloidal oat, Shea butter, Glycerine", "Fragrance": "Mild natural" },
    howToUse: "After bath, apply generously to baby's skin. Massage gently until absorbed. Can be used daily.",
    color: "#74B3CE",
  },
  {
    id: "bc-004", slug: "eliv-baby-nappy-cream-100g",
    name: "Eliv Baby Nappy Cream 100 g",
    brand: "Eliv", category: "baby-care", collections: [],
    price: 59, rating: 4.8, reviewCount: 341, inStock: true, isBestSeller: true,
    description: "Thick barrier cream with 15% zinc oxide for immediate relief and prevention of nappy rash. Forms a protective layer against moisture without blocking skin's natural breathability.",
    specifications: { "Net Weight": "100 g", "Active": "Zinc oxide 15%", "Fragrance": "Free", "Paraben": "Free" },
    howToUse: "At each nappy change, clean and dry baby's bottom thoroughly. Apply a thick layer over the affected or at-risk area. No need to rub in completely.",
    color: "#74B3CE",
  },
  {
    id: "bc-005", slug: "eliv-sensitive-baby-wipes-twin-pack",
    name: "Eliv Sensitive Baby Wipes Twin Pack (2 × 80 ct)",
    brand: "Eliv", category: "baby-care", collections: ["Wipes", "Offers"],
    price: 99, compareAt: 120, rating: 4.9, reviewCount: 389, inStock: true,
    description: "Same award-winning Eliv formula in a twin pack. Extra-gentle on eczema-prone skin. Clinically tested under paediatric supervision.",
    specifications: { "Count": "2 × 80 wipes", "Material": "Plant fibre", "Fragrance": "Free", "pH": "Skin neutral" },
    howToUse: "Same as Eliv Baby Wipes 80-count.",
    color: "#74B3CE",
  },
  {
    id: "bc-006", slug: "eliv-baby-powder-150g",
    name: "Eliv Baby Powder Talc-Free 150 g",
    brand: "Eliv", category: "baby-care", collections: [],
    price: 49, rating: 4.5, reviewCount: 124, inStock: true,
    description: "Cornstarch-based talc-free powder that absorbs moisture and reduces friction. Lightly scented with natural chamomile. Safe from 3 months.",
    specifications: { "Net Weight": "150 g", "Base": "Cornstarch", "Talc": "Free", "Age": "3 months+" },
    howToUse: "Apply a small amount to palm away from baby's face, then pat gently onto skin. Keep away from baby's breathing zone.",
    color: "#74B3CE",
  },
  {
    id: "bc-007", slug: "eliv-baby-massage-oil-100ml",
    name: "Eliv Baby Massage Oil 100 ml",
    brand: "Eliv", category: "baby-care", collections: [],
    price: 65, rating: 4.6, reviewCount: 87, inStock: true,
    description: "Blend of cold-pressed sunflower, almond, and jojoba oils. Rapidly absorbed and leaves no greasy film. Promotes bonding and supports skin development from newborn stage.",
    specifications: { "Volume": "100 ml", "Oils": "Sunflower, Almond, Jojoba", "Mineral oil": "Free", "Fragrance": "Mild lavender" },
    howToUse: "Warm a small amount between palms and massage onto baby's skin with gentle circular motions. Best used after bath time.",
    color: "#74B3CE",
  },
  {
    id: "bc-008", slug: "eliv-baby-nasal-spray-50ml",
    name: "Eliv Baby Nasal Spray Isotonic Saline 50 ml",
    brand: "Eliv", category: "baby-care", collections: ["New"],
    price: 55, rating: 4.7, reviewCount: 62, inStock: true, isNew: true,
    description: "Preservative-free isotonic saline nasal spray for clearing nasal congestion in babies from 1 month old. Micro-fine mist reaches the entire nasal cavity without irritation.",
    specifications: { "Volume": "50 ml", "NaCl": "0.9% (isotonic)", "Preservative": "Free", "Age": "From 1 month" },
    howToUse: "Lay baby on its back. Insert nozzle gently into one nostril and spray 1–2 times. Repeat in other nostril. Use up to 6 times daily.",
    color: "#74B3CE",
  },

  // Feminine Care (6)
  {
    id: "fc-001", slug: "vodlia-daily-panty-liners-40ct",
    name: "Vodlia Daily Panty Liners 40-count",
    brand: "Vodlia", category: "feminine-care", collections: [],
    price: 39, rating: 4.5, reviewCount: 231, inStock: true,
    description: "Ultra-thin, breathable cotton-top panty liners with an odour-neutralising inner layer. Wings ensure they stay in place. Suitable for daily freshness and light discharge.",
    specifications: { "Count": "40 liners", "Top sheet": "100% cotton", "Wings": "Yes", "pH Neutral": "Yes" },
    howToUse: "Remove adhesive strip and press firmly into underwear. Change every 3–4 hours or when soiled. Do not flush.",
    color: "#E76F51",
  },
  {
    id: "fc-002", slug: "vodlia-ultra-thin-pads-10ct",
    name: "Vodlia Ultra-Thin Pads Regular 10-count",
    brand: "Vodlia", category: "feminine-care", collections: [],
    price: 35, rating: 4.6, reviewCount: 198, inStock: true,
    description: "Cottony-soft ultra-thin pads with rapid-dry channels that lock in fluid in seconds. Triple-layer leak guard sides protect against side leaks.",
    specifications: { "Count": "10 pads", "Absorbency": "Regular", "Top sheet": "Cotton", "Thickness": "3 mm" },
    howToUse: "Remove adhesive strip, centre pad in underwear and press firmly. Change every 4 hours or when saturated. Do not flush.",
    color: "#E76F51",
    variants: { label: "Absorbency", options: ["Regular", "Super", "Night"] },
  },
  {
    id: "fc-003", slug: "vodlia-night-pads-8ct",
    name: "Vodlia Night Pads 8-count",
    brand: "Vodlia", category: "feminine-care", collections: [],
    price: 38, rating: 4.7, reviewCount: 143, inStock: true,
    description: "Longer 35 cm night pad with extended back coverage. Extra-absorbent core handles heavy flow. Secure side wings prevent bunching during sleep.",
    specifications: { "Count": "8 pads", "Length": "35 cm", "Absorbency": "Heavy/Night", "Wings": "Yes" },
    howToUse: "Remove backing and secure wings around underwear. Position further back than daytime pads. Change in the morning or when needed.",
    color: "#E76F51",
  },
  {
    id: "fc-004", slug: "qualita-feminine-hygiene-wipes-20ct",
    name: "Qualita Feminine Hygiene Wipes 20-count",
    brand: "Qualita", category: "feminine-care", collections: ["Wipes"],
    price: 45, rating: 4.4, reviewCount: 112, inStock: true,
    description: "pH-balanced intimate wipes with lactic acid and aloe vera. Individually wrapped for on-the-go hygiene. Suitable for use during menstruation, after exercise, or travel.",
    specifications: { "Count": "20 individually wrapped wipes", "pH": "3.8–4.5", "Fragrance": "Free", "Alcohol": "Free" },
    howToUse: "Tear open individual sachet. Gently wipe the external intimate area from front to back. One wipe per use. Do not use internally.",
    color: "#E07B39",
  },
  {
    id: "fc-005", slug: "vodlia-feminine-intimate-gel-200ml",
    name: "Vodlia Feminine Intimate Gel 200 ml",
    brand: "Vodlia", category: "feminine-care", collections: ["New"],
    price: 95, rating: 4.5, reviewCount: 77, inStock: true, isNew: true,
    description: "Soap-free soothing gel with prebiotic inulin and lactic acid. Maintains healthy vaginal flora and reduces itching caused by dryness. Gynaecologist-tested.",
    specifications: { "Volume": "200 ml", "pH": "3.8–4.2", "Prebiotic": "Inulin", "Preservative": "Phenoxyethanol" },
    howToUse: "Apply a small amount to the external intimate area. Rinse thoroughly with water. Use daily or as needed. External use only.",
    color: "#E76F51",
  },
  {
    id: "fc-006", slug: "vodlia-menstrual-cup-size-m",
    name: "Vodlia Menstrual Cup Size M",
    brand: "Vodlia", category: "feminine-care", collections: [],
    price: 249, rating: 4.8, reviewCount: 56, inStock: true,
    description: "Medical-grade silicone menstrual cup. Holds up to 25 ml — 3× the capacity of a regular pad. BPA-free, reusable for up to 10 years. Includes storage pouch and sterilising instructions.",
    specifications: { "Material": "Medical-grade silicone, BPA-free", "Capacity": "25 ml", "Size": "M (after first birth / age 30+)", "Includes": "Storage pouch" },
    howToUse: "Sterilise before first use. Fold and insert in a C-fold or punch-down fold. Ensure a seal is formed (no leaks). Remove and empty every 4–12 hours.",
    color: "#E76F51",
    variants: { label: "Size", options: ["S", "M", "L"] },
  },

  // Home Care — Surface & Floor (3)
  {
    id: "hom-001", slug: "diversey-taski-jontec-300-1l",
    name: "Diversey Taski Jontec 300 Floor Maintainer 1 L concentrate",
    brand: "Diversey", category: "home-care", subcategory: "Surface & Floor", collections: [],
    price: 185, rating: 4.7, reviewCount: 63, inStock: true,
    description: "Professional-grade concentrated floor maintainer for all hard floor surfaces. Leaves a brilliant wet-look shine without streaks. Used in hospitals and hotels across 60 countries.",
    specifications: { "Volume": "1 L (concentrate)", "Dilution": "1:100–1:200 with water", "pH": "7.5–8.5", "Surfaces": "All sealed hard floors" },
    howToUse: "Dilute 10 ml per litre of water. Apply with mop. No rinsing required. Allow to dry naturally.",
    color: "#0066CC",
    variants: { label: "Size", options: ["1 L", "5 L", "10 L"] },
  },
  {
    id: "hom-002", slug: "grace-multipurpose-surface-spray-750ml",
    name: "Grace Multipurpose Surface Spray 750 ml",
    brand: "Grace", category: "home-care", subcategory: "Surface & Floor", collections: [],
    price: 65, compareAt: 79, rating: 4.5, reviewCount: 189, inStock: true, isBestSeller: true,
    description: "Ready-to-use antibacterial spray for kitchen counters, dining tables, bathroom fixtures, and non-porous surfaces. Eliminates 99.9% of bacteria in 30 seconds.",
    specifications: { "Volume": "750 ml", "Contact time": "30 seconds", "Surfaces": "Sealed non-porous surfaces", "Fragrance": "Fresh lemon" },
    howToUse: "Spray directly onto surface. Leave for 30 seconds. Wipe with a clean cloth or paper towel. No rinsing required on food-contact surfaces after wiping.",
    color: "#2D6A4F",
    variants: { label: "Scent", options: ["Fresh Lemon", "Lavender", "Unscented"] },
  },
  {
    id: "hom-003", slug: "qualita-antibacterial-floor-cleaner-1l",
    name: "Qualita Anti-Bacterial Floor Cleaner 1 L",
    brand: "Qualita", category: "home-care", subcategory: "Surface & Floor", collections: ["Disinfectants & Sanitizers"],
    price: 55, rating: 4.4, reviewCount: 134, inStock: true,
    description: "Concentrated floor cleaner that kills bacteria and leaves a fresh pine scent. Suitable for ceramic, marble, granite, and vinyl floors. One cap per 4 L of water.",
    specifications: { "Volume": "1 L (concentrate)", "Dilution": "1 cap per 4 L water", "Active": "Benzalkonium chloride 0.2%", "Fragrance": "Pine" },
    howToUse: "Mix 1 cap (~25 ml) with 4 litres of water. Mop floor normally. No rinsing needed. Keep out of reach of children.",
    color: "#E07B39",
  },
  // Home Care — Kitchen (2)
  {
    id: "hom-004", slug: "diversey-suma-star-d1-750ml",
    name: "Diversey Suma Star D1 Manual Dish Detergent 750 ml",
    brand: "Diversey", category: "home-care", subcategory: "Kitchen", collections: [],
    price: 89, rating: 4.6, reviewCount: 77, inStock: true,
    description: "Concentrated manual dishwashing liquid with superior grease-cutting power. Food-safe formula used in commercial kitchens worldwide. A few drops go a long way.",
    specifications: { "Volume": "750 ml", "Dilution": "3–5 ml per basin", "pH": "6.5–7.5", "Fragrance": "Mild citrus" },
    howToUse: "Add 3–5 ml to warm water. Wash dishes normally. Rinse thoroughly with clean water. Suitable for hand use — wear gloves with frequent use.",
    color: "#0066CC",
  },
  {
    id: "hom-005", slug: "grace-kitchen-degreaser-spray-500ml",
    name: "Grace Kitchen Degreaser Spray 500 ml",
    brand: "Grace", category: "home-care", subcategory: "Kitchen", collections: [],
    price: 59, rating: 4.5, reviewCount: 98, inStock: true,
    description: "Heavy-duty kitchen degreaser for hobs, extractors, oven fronts, and splashbacks. Breaks down baked-on grease in 60 seconds without scratching surfaces.",
    specifications: { "Volume": "500 ml", "Contact time": "60 seconds", "Surfaces": "Stainless steel, enamel, glass, ceramic hobs", "Fragrance": "Orange peel extract" },
    howToUse: "Spray onto greasy surface. Leave for 60 seconds. Wipe with a damp cloth. Rinse food-contact surfaces afterwards.",
    color: "#2D6A4F",
  },
  // Home Care — Laundry & Detergents (2)
  {
    id: "hom-006", slug: "diversey-clax-premium-laundry-2l",
    name: "Diversey Clax Premium Laundry Detergent 2 L",
    brand: "Diversey", category: "home-care", subcategory: "Laundry & Detergents", collections: [],
    price: 149, rating: 4.7, reviewCount: 55, inStock: true,
    description: "Professional-strength liquid laundry detergent with enzyme blend for stain removal at 30–60 °C. Low-foam formula suited for front-loading and top-loading machines.",
    specifications: { "Volume": "2 L", "Doses": "~66 doses", "Enzyme blend": "Protease, Lipase, Amylase", "Wash temp": "30–60 °C" },
    howToUse: "Add 30 ml to dispenser. Select appropriate wash cycle. For heavily soiled items, increase to 40 ml. Store upright with cap closed.",
    color: "#0066CC",
  },
  {
    id: "hom-007", slug: "grace-fabric-softener-1l",
    name: "Grace Fabric Softener Fresh Breeze 1 L",
    brand: "Grace", category: "home-care", subcategory: "Laundry & Detergents", collections: ["Offers"],
    price: 49, compareAt: 65, rating: 4.4, reviewCount: 112, inStock: true,
    description: "Long-lasting fabric softener that reduces static, softens fibres, and leaves a 48-hour fresh scent. Suitable for all fabrics including cotton, synthetics, and blends.",
    specifications: { "Volume": "1 L", "Doses": "~50 doses", "Scent longevity": "48 hours", "Fabric": "All fabrics" },
    howToUse: "Add 20 ml to the fabric softener compartment or during the final rinse cycle. Do not use with flame-retardant children's clothing.",
    color: "#2D6A4F",
  },
  // Home Care — Air Care (2)
  {
    id: "hom-008", slug: "grace-air-freshener-citrus-300ml",
    name: "Grace Air Freshener Spray Citrus Burst 300 ml",
    brand: "Grace", category: "home-care", subcategory: "Air Care", collections: [],
    price: 45, rating: 4.3, reviewCount: 88, inStock: true,
    description: "Instant odour-eliminating air freshener with encapsulated citrus fragrance. Neutralises cooking and pet odours rather than masking them. Safe for use around children and pets.",
    specifications: { "Volume": "300 ml", "Fragrance": "Citrus burst", "Propellant": "DME/Propane", "Duration": "~300 sprays" },
    howToUse: "Hold upright 30 cm from face. Spray 2–3 times into room air. Avoid spraying directly onto surfaces, fabrics, or near flames.",
    color: "#2D6A4F",
  },
  {
    id: "hom-009", slug: "grace-odour-eliminator-400ml",
    name: "Grace Odour Eliminator Fabric Spray 400 ml",
    brand: "Grace", category: "home-care", subcategory: "Air Care", collections: [],
    price: 55, rating: 4.5, reviewCount: 72, inStock: true,
    description: "Refreshes upholstery, curtains, carpets, and clothing by eliminating odour molecules. Leaves a clean linen scent. Dries in 2 minutes without staining.",
    specifications: { "Volume": "400 ml", "Fragrance": "Clean linen", "Drying time": "~2 minutes", "Surfaces": "Textiles, upholstery, carpets" },
    howToUse: "Spray lightly from 25 cm onto fabric surface. Allow to dry naturally. Do not use on silk or dry-clean-only fabrics.",
    color: "#2D6A4F",
  },
  // Home Care — Restroom (2)
  {
    id: "hom-010", slug: "diversey-taski-sani-4in1-750ml",
    name: "Diversey Taski Sani 4-in-1 Toilet Cleaner 750 ml",
    brand: "Diversey", category: "home-care", subcategory: "Restroom", collections: [],
    price: 79, rating: 4.8, reviewCount: 101, inStock: true, isBestSeller: true,
    description: "Professional toilet cleaner that descales, disinfects, deodorises, and prevents limescale re-formation in one application. Thick angled neck reaches under the rim.",
    specifications: { "Volume": "750 ml", "Active": "HCl 9.5%, Benzalkonium chloride", "Contact time": "5–10 minutes", "Surfaces": "Ceramic, porcelain" },
    howToUse: "Apply under the toilet rim. Leave for 5–10 minutes. Scrub with toilet brush and flush. Keep away from children. Avoid contact with other cleaners.",
    color: "#0066CC",
  },
  {
    id: "hom-011", slug: "grace-antibacterial-toilet-bowl-cleaner-500ml",
    name: "Grace Antibacterial Toilet Bowl Cleaner 500 ml",
    brand: "Grace", category: "home-care", subcategory: "Restroom", collections: [],
    price: 39, rating: 4.3, reviewCount: 67, inStock: true,
    description: "Daily-use toilet bowl cleaner with pine scent and benzalkonium chloride disinfectant. Thick gel clings to the bowl surface for longer contact. Safe for septic systems.",
    specifications: { "Volume": "500 ml", "Active": "Benzalkonium chloride 0.3%", "Fragrance": "Pine", "Septic safe": "Yes" },
    howToUse: "Apply 2–3 squirts under the rim. Brush bowl and flush after 3 minutes. Use daily for best results.",
    color: "#2D6A4F",
  },
  // Home Care — Disinfectants & Sanitizers (1)
  {
    id: "hom-012", slug: "diversey-oxivir-disinfectant-500ml",
    name: "Diversey Oxivir Disinfectant Spray 500 ml",
    brand: "Diversey", category: "home-care", subcategory: "Disinfectants & Sanitizers", collections: ["Disinfectants & Sanitizers", "Best Sellers"],
    price: 129, rating: 4.9, reviewCount: 244, inStock: true, isBestSeller: true,
    description: "Hospital-grade accelerated hydrogen peroxide (AHP) disinfectant. Kills SARS-CoV-2, influenza, norovirus, and 99.999% of bacteria in 60 seconds. Safe for use around children and food-preparation areas once dry.",
    specifications: { "Volume": "500 ml", "Active": "Accelerated H₂O₂ 0.5%", "Contact time": "60 seconds", "Spectrum": "Bactericidal, Virucidal, Fungicidal" },
    howToUse: "Spray onto hard non-porous surface. Leave for 60 seconds. Wipe with cloth or allow to air-dry. No rinsing needed. Do not mix with other chemicals.",
    color: "#0066CC",
  },

  // Home Diagnostics (6)
  {
    id: "diag-001", slug: "oview-blood-glucose-monitor",
    name: "Oview Digital Blood Glucose Monitor Kit",
    brand: "Oview", category: "home-diagnostics", collections: [],
    price: 395, rating: 4.7, reviewCount: 178, inStock: true,
    description: "CE-marked blood glucose monitor with 5-second results. Memory for 500 readings with 14/30/90-day averages. Includes lancet device, 10 lancets, and 10 test strips. No coding required.",
    specifications: { "Range": "20–600 mg/dL", "Memory": "500 readings", "Sample volume": "0.5 µL", "Battery": "2 × AAA", "Included": "10 strips, lancet device, 10 lancets" },
    howToUse: "Insert test strip; device auto-activates. Lance fingertip and apply blood to strip edge. Read result after 5 seconds. Log in accompanying app (iOS/Android) via Bluetooth.",
    color: "#6A0572",
    variants: { label: "Starter pack", options: ["Monitor only", "Monitor + 50 strips", "Monitor + 100 strips"] },
  },
  {
    id: "diag-002", slug: "surecheck-pregnancy-test-2ct",
    name: "SureCheck Pregnancy Test 2-count",
    brand: "SureCheck", category: "home-diagnostics", collections: [],
    price: 45, rating: 4.8, reviewCount: 612, inStock: true, isBestSeller: true,
    description: "Over 99% accurate from the day of missed period. Results in 3 minutes. Detects hCG levels from 10 mIU/ml. Easy-to-read window — one line negative, two lines positive.",
    specifications: { "Count": "2 tests", "Sensitivity": "10 mIU/mL hCG", "Accuracy": ">99%", "Result time": "3 minutes" },
    howToUse: "Collect urine in a clean container. Dip test for 5 seconds. Lay flat and read after 3 minutes. Do not read results after 10 minutes.",
    color: "#C41230",
  },
  {
    id: "diag-003", slug: "oview-pulse-oximeter",
    name: "Oview Fingertip Pulse Oximeter",
    brand: "Oview", category: "home-diagnostics", collections: ["Best Sellers"],
    price: 220, rating: 4.8, reviewCount: 391, inStock: true, isBestSeller: true,
    description: "CE-marked dual-display oximeter showing SpO₂ and pulse rate simultaneously. Accurate to ±2% SpO₂. Includes perfusion index. Large OLED display is easy to read in any orientation.",
    specifications: { "SpO₂ range": "70–99% (±2%)", "Pulse range": "30–250 bpm (±2 bpm)", "Display": "OLED dual-colour", "Battery": "2 × AA (included)", "Perfusion Index": "Yes" },
    howToUse: "Insert finger (index or middle) into sensor. Press power button. Hold still for 5–10 seconds until reading stabilises. Remove finger and device powers off automatically.",
    color: "#6A0572",
  },
  {
    id: "diag-004", slug: "surecheck-ovulation-test-7ct",
    name: "SureCheck Ovulation Test Kit 7-count",
    brand: "SureCheck", category: "home-diagnostics", collections: [],
    price: 55, rating: 4.6, reviewCount: 203, inStock: true,
    description: "Detects the LH surge 24–36 hours before ovulation. >99% accurate at detecting surge levels. Seven-test kit covers a full monitoring cycle. Easy midstream format.",
    specifications: { "Count": "7 tests", "Sensitivity": "20 mIU/mL LH", "Accuracy": ">99%", "Format": "Midstream" },
    howToUse: "Test between 10 am and 8 pm. Urinate onto absorbent tip for 5 seconds. Cap and lay flat. Read after 3 minutes. A line as dark or darker than the control = LH surge detected.",
    color: "#C41230",
  },
  {
    id: "diag-005", slug: "oview-digital-ear-thermometer",
    name: "Oview Digital Ear Thermometer",
    brand: "Oview", category: "home-diagnostics", collections: [],
    price: 265, rating: 4.6, reviewCount: 129, inStock: true,
    description: "Clinical-accuracy ear thermometer with 1-second reading. Fever indicator (three colour codes). Memory for last 30 readings. Includes 10 hygiene probe covers.",
    specifications: { "Temp range": "34–42.2 °C", "Accuracy": "±0.2 °C", "Reading time": "1 second", "Memory": "30 readings", "Includes": "10 probe covers" },
    howToUse: "Fit a clean probe cover. Gently insert tip into ear canal. Press button once. Remove and read temperature. Discard probe cover after each use.",
    color: "#6A0572",
  },
  {
    id: "diag-006", slug: "surecheck-rapid-antigen-test",
    name: "SureCheck COVID-19 Rapid Antigen Self-Test",
    brand: "SureCheck", category: "home-diagnostics", collections: [],
    price: 65, rating: 4.5, reviewCount: 88, inStock: true,
    description: "CE-marked and WHO-listed self-test for SARS-CoV-2 nucleocapsid antigen. Result in 15 minutes. Sensitivity 96.3%, specificity 99.5%. Suitable for adults and children (with adult assistance).",
    specifications: { "Sensitivity": "96.3%", "Specificity": "99.5%", "Sample": "Nasal swab", "Result time": "15 minutes" },
    howToUse: "Blow nose before testing. Insert swab 1.5 cm into each nostril and rotate 5 times. Swirl in extraction buffer for 15 seconds. Apply 3 drops to test window. Read after 15 minutes — not after 20 minutes.",
    color: "#C41230",
  },

  // Health & Protection (7)
  {
    id: "hp-001", slug: "surecheck-blood-pressure-monitor",
    name: "SureCheck Upper Arm Blood Pressure Monitor",
    brand: "SureCheck", category: "health-protection", collections: ["Best Sellers"],
    price: 450, rating: 4.8, reviewCount: 267, inStock: true, isBestSeller: true,
    description: "Clinically validated (ESH 2022) upper-arm blood pressure monitor. Dual-user memory (2 × 60 readings). Irregular heartbeat detection. Compatible with SureCheck Health app via Bluetooth for trend tracking.",
    specifications: { "Cuff size": "22–42 cm", "Accuracy": "±3 mmHg / ±5%", "Memory": "2 users × 60 readings", "Connectivity": "Bluetooth 5.0", "Power": "4 × AA or USB-C adapter" },
    howToUse: "Sit quietly for 5 minutes. Fit cuff 2–3 cm above elbow. Press START. Remain still during inflation. Record or sync result to app. Take readings at the same time each day.",
    color: "#C41230",
    variants: { label: "Cuff size", options: ["Standard (22–32 cm)", "Large (32–42 cm)"] },
  },
  {
    id: "hp-002", slug: "oview-smart-weighing-scale",
    name: "Oview Smart Weighing Scale with BMI",
    brand: "Oview", category: "health-protection", collections: [],
    price: 185, rating: 4.5, reviewCount: 143, inStock: true,
    description: "Bioelectrical impedance analysis (BIA) smart scale measuring weight, BMI, body fat %, muscle mass, bone mass, and hydration. Syncs to app for up to 8 users. Tempered glass platform.",
    specifications: { "Capacity": "180 kg", "Accuracy": "±0.1 kg", "Metrics": "13 body composition metrics", "Users": "Up to 8", "Connectivity": "Bluetooth" },
    howToUse: "Step on barefoot for BIA readings. Stand still for 10 seconds. Readings auto-sync to Oview Health app. For weight-only reading, shoes are acceptable.",
    color: "#6A0572",
  },
  {
    id: "hp-003", slug: "surecheck-n95-masks-10ct",
    name: "SureCheck N95 Respirator Masks 10-count",
    brand: "SureCheck", category: "health-protection", collections: [],
    price: 99, compareAt: 129, rating: 4.7, reviewCount: 312, inStock: true,
    description: "GB2626-2019 certified N95 respirators with 5-layer filtration (≥95% filtration of 0.3 µm particles). Adjustable nose bridge and double-seal design. Suitable for pollution, allergens, and pathogen protection.",
    specifications: { "Standard": "GB2626-2019 / N95", "Filtration": "≥95% at 0.3 µm", "Layers": "5", "Valve": "No (safe for source control)", "Count": "10 individually wrapped" },
    howToUse: "Cup mask in hand with nosepiece at fingertips. Position over nose and mouth. Pull straps over head. Press nosepiece to fit. Perform seal check before each use.",
    color: "#C41230",
  },
  {
    id: "hp-004", slug: "qualita-hand-sanitizer-500ml",
    name: "Qualita Hand Sanitizer 70% Ethanol 500 ml",
    brand: "Qualita", category: "health-protection", collections: ["Disinfectants & Sanitizers", "Best Sellers"],
    price: 75, rating: 4.6, reviewCount: 534, inStock: true, isBestSeller: true,
    description: "WHO-formulated hand sanitizer with 70% ethanol, glycerol, and hydrogen peroxide. Eliminates 99.9% of bacteria and viruses including SARS-CoV-2 in 30 seconds without water.",
    specifications: { "Volume": "500 ml", "Ethanol": "70% v/v", "Formula": "WHO hand-rub formula", "Contact time": "30 seconds" },
    howToUse: "Apply 3 ml to palm. Rub hands together covering all surfaces including between fingers and thumbs for 30 seconds. Allow to dry completely. Do not rinse.",
    color: "#E07B39",
    variants: { label: "Size", options: ["100 ml", "250 ml", "500 ml", "1 L"] },
  },
  {
    id: "hp-005", slug: "surecheck-nitrile-gloves-50ct",
    name: "SureCheck Disposable Nitrile Gloves 50-count",
    brand: "SureCheck", category: "health-protection", collections: [],
    price: 85, rating: 4.6, reviewCount: 198, inStock: true,
    description: "Powder-free, examination-grade nitrile gloves. Latex-free for allergy safety. Textured fingertips for improved grip. AQL 1.5 quality standard. Suitable for medical, food-handling, and cleaning applications.",
    specifications: { "Count": "50 gloves (25 pairs)", "Material": "Nitrile", "Powder": "Free", "Quality": "AQL 1.5", "Standard": "EN 374, EN 455" },
    howToUse: "Pull gloves from box from the cuff end. Ensure a snug fit. Inspect for holes or tears before use. Dispose of after use — do not wash and reuse examination gloves.",
    color: "#C41230",
    variants: { label: "Size", options: ["S", "M", "L", "XL"] },
  },
  {
    id: "hp-006", slug: "oview-digital-pedometer",
    name: "Oview Digital Pedometer Smart Bracelet",
    brand: "Oview", category: "health-protection", collections: [],
    price: 149, rating: 4.3, reviewCount: 89, inStock: true,
    description: "Lightweight smart bracelet tracking steps, distance, calories burned, and sleep quality. OLED display. Inactivity reminder. Water-resistant IP67. Battery lasts 10 days on a single charge.",
    specifications: { "Display": "0.96\" OLED", "Battery": "90 mAh (10-day life)", "Water resistance": "IP67", "Connectivity": "Bluetooth 4.0", "Strap": "Silicone, adjustable" },
    howToUse: "Charge for 2 hours. Download Oview Fit app and pair via Bluetooth. Wear on non-dominant wrist. Sync daily for trend data.",
    color: "#6A0572",
  },
  {
    id: "hp-007", slug: "qualita-alcohol-wipes-100ct",
    name: "Qualita Isopropyl Alcohol Wipes 100-count",
    brand: "Qualita", category: "health-protection", collections: ["Wipes", "Disinfectants & Sanitizers"],
    price: 65, rating: 4.7, reviewCount: 411, inStock: true, isBestSeller: true,
    description: "Individually foil-wrapped 70% isopropyl alcohol wipes for skin prep and surface disinfection. Meets EN1276 standard. Sterile, non-woven, single-use. Compatible with medical device surfaces.",
    specifications: { "Count": "100 individually wrapped", "IPA": "70% v/v", "Sterile": "Yes", "Standard": "EN1276" },
    howToUse: "Tear open foil sachet. Wipe skin or surface firmly for 15 seconds. Allow to air-dry completely. Discard after single use.",
    color: "#E07B39",
  },
];

// Helper functions
export function getBrand(slug: string): Brand | undefined {
  return BRANDS.find((b) => b.slug === slug);
}

export function getBrandByName(name: string): Brand | undefined {
  return BRANDS.find((b) => b.name === name);
}

export function getCategory(slug: string) {
  return CATEGORIES.find((c) => c.slug === slug);
}

export function getProductsByCategory(slug: string) {
  return PRODUCTS.filter((p) => p.category === slug);
}

export function getProductsByBrand(brandName: string) {
  return PRODUCTS.filter((p) => p.brand === brandName);
}

export function getProductsByCollection(collection: string) {
  return PRODUCTS.filter((p) => p.collections.includes(collection));
}

export function getBestSellers() {
  return PRODUCTS.filter((p) => p.isBestSeller);
}

export function getNewArrivals() {
  return PRODUCTS.filter((p) => p.isNew);
}

export function getOffers() {
  return PRODUCTS.filter((p) => p.compareAt !== undefined);
}

export function searchProducts(query: string) {
  const q = query.toLowerCase();
  return PRODUCTS.filter(
    (p) =>
      p.name.toLowerCase().includes(q) ||
      p.brand.toLowerCase().includes(q) ||
      p.category.toLowerCase().includes(q) ||
      p.description.toLowerCase().includes(q)
  );
}

export function getRelated(product: Product, limit = 4) {
  return PRODUCTS.filter(
    (p) => p.id !== product.id && (p.category === product.category || p.brand === product.brand)
  ).slice(0, limit);
}

export const CATEGORY_COLORS: Record<string, string> = {
  "personal-care": "#FFE5CC",
  "hair-care": "#E8D5F5",
  "skin-care": "#D5F0E8",
  "baby-care": "#D5E8F5",
  "feminine-care": "#F5D5E8",
  "home-care": "#D5F5F0",
  "home-diagnostics": "#D5E8FF",
  "health-protection": "#E0F5D5",
};

export const CATEGORY_NAMES: Record<string, string> = {
  "personal-care": "Personal Care",
  "hair-care": "Hair Care",
  "skin-care": "Skin Care",
  "baby-care": "Baby Care",
  "feminine-care": "Feminine Care",
  "home-care": "Home Care",
  "home-diagnostics": "Home Diagnostics",
  "health-protection": "Health & Protection",
};

export const REVIEWS = [
  { author: "Layla M.", rating: 5, date: "2025-05-12", text: "Exactly as described. Fast delivery and authentic packaging — you can tell it is the genuine product." },
  { author: "Karim A.", rating: 5, date: "2025-04-28", text: "Ordered three times already. Quality is consistent and the price is fair. Highly recommend exMart." },
  { author: "Nour F.", rating: 4, date: "2025-04-15", text: "Good product. Would have given 5 stars but delivery took an extra day. Product itself is excellent." },
  { author: "Ahmed S.", rating: 5, date: "2025-03-30", text: "Very satisfied. Sealed, authentic, and works perfectly. Will buy again." },
  { author: "Dina H.", rating: 4, date: "2025-03-18", text: "Works as expected. Packaging was in perfect condition. Customer service also responded quickly when I had a question." },
];
