---
name: Mukhamar Luxury Essence
colors:
  surface: '#fff8f8'
  surface-dim: '#dfd8d9'
  surface-bright: '#fff8f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f9f2f3'
  surface-container: '#f4eced'
  surface-container-high: '#eee6e7'
  surface-container-highest: '#e8e1e2'
  on-surface: '#1e1b1c'
  on-surface-variant: '#4d4449'
  inverse-surface: '#332f30'
  inverse-on-surface: '#f6eff0'
  outline: '#7f747a'
  outline-variant: '#d0c3c9'
  surface-tint: '#715668'
  primary: '#715668'
  on-primary: '#ffffff'
  primary-container: '#a7889c'
  on-primary-container: '#392333'
  inverse-primary: '#dfbdd2'
  secondary: '#685b62'
  on-secondary: '#ffffff'
  secondary-container: '#f1dde7'
  on-secondary-container: '#6f6068'
  tertiary: '#586245'
  on-tertiary: '#ffffff'
  tertiary-container: '#8a9574'
  on-tertiary-container: '#242d14'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#fcd9ee'
  primary-fixed-dim: '#dfbdd2'
  on-primary-fixed: '#291524'
  on-primary-fixed-variant: '#583f50'
  secondary-fixed: '#f1dde7'
  secondary-fixed-dim: '#d4c2cb'
  on-secondary-fixed: '#23191f'
  on-secondary-fixed-variant: '#50434a'
  tertiary-fixed: '#dce7c2'
  tertiary-fixed-dim: '#c0cba7'
  on-tertiary-fixed: '#161e07'
  on-tertiary-fixed-variant: '#404a2f'
  background: '#fff8f8'
  on-background: '#1e1b1c'
  surface-variant: '#e8e1e2'
typography:
  display-lg:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 42px
    fontWeight: '600'
    lineHeight: 52px
  display-lg-mobile:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
  headline-md:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 24px
    fontWeight: '500'
    lineHeight: 32px
  headline-sm:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 20px
    fontWeight: '500'
    lineHeight: 28px
  body-lg:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-md:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
    letterSpacing: 0.02em
  price-lg:
    fontFamily: IBM Plex Sans Arabic
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: '8'
  container-margin: 24px
  gutter: 16px
  stack-sm: 4px
  stack-md: 12px
  stack-lg: 24px
  section-gap: 48px
---

## Brand & Style

The design system is centered on the concept of "Modern Heritage"—fusing the ancestral traditions of Arabian perfumery with a high-end, contemporary e-commerce experience. The aesthetic is rooted in **Minimalism** with **Tactile/Skeuomorphic** accents, emphasizing the physical sensation of luxury through soft textures, expansive whitespace, and refined layering.

The target audience consists of discerning fragrance connoisseurs who value authenticity, craftsmanship, and a seamless digital journey. The UI should evoke a sense of calm, prestige, and olfactory richness. Visuals are treated with editorial care, prioritizing high-fidelity product photography over heavy decorative elements.

## Colors

The palette has shifted toward a "Dusty Botanical" aesthetic, inspired by sun-bleached petals, dried resins, and the muted, sophisticated tones of high-end apothecary packaging.

- **Primary (Dusty Rosewood):** A desaturated, sophisticated mauve-pink (#8c6f82) used for branding and primary actions. It represents the delicate yet enduring scent of dried florals.
- **Secondary (Ash Quartz):** A neutral, warm grey with a slight violet undertone (#82737b) used for secondary UI elements and supporting accents.
- **Tertiary (Sage Resin):** A muted, herbal green (#8a9574) that evokes the freshness of botanical top notes and the earthy heart of crushed leaves.
- **Neutral (Steely Slate):** A balanced, cool-toned grey (#7b7677) ensures the interface feels grounded and modern.
- **Functional Colors:** Success and Error states are muted to align with the sophisticated aesthetic, avoiding neon or overly vibrant hues.

## Typography

This design system utilizes **IBM Plex Sans Arabic** for its exceptional clarity and modern technical structure, which balances the traditional curves of Arabic script with a professional, geometric skeleton.

- **Hierarchy:** Display styles are used for product titles and hero sections. Body copy maintains generous line heights (1.5x minimum) to ensure readability in RTL contexts.
- **Alignment:** All text is Right-to-Left (RTL) by default. Numerical values (prices) should use the localized digits accompanied by the currency symbol "ر.س".
- **Weight:** Medium (500) and Semi-Bold (600) weights are used to denote luxury and importance, while Regular (400) handles all long-form descriptions.

## Layout & Spacing

The layout follows a **Fluid Grid** model with a "Mobile-First, RTL-First" philosophy. 

- **Grid:** A 12-column grid is used for desktop (max-width 1280px), collapsing to a 4-column grid for mobile devices.
- **Rhythm:** An 8px base unit drives all spatial relationships. 
- **Margins:** Generous 24px side margins on mobile prevent the content from feeling cramped, reinforcing the premium brand positioning.
- **RTL Logic:** All horizontal layouts must be mirrored. Logic for "Start" and "End" padding should be used in place of "Left" and "Right" to ensure seamless transition between Arabic and potential English localization.

## Elevation & Depth

Hierarchy is established through **Tonal Layering** and **Ambient Shadows**.

- **Surfaces:** The base background uses a neutral grey tint with a hint of purple. Interactive cards and modals sit on elevated surfaces defined by high-container roles.
- **Shadows:** Use extremely soft, diffused shadows with a hint of the secondary Ash Quartz tint. 
  - *Example:* `box-shadow: 0 10px 30px rgba(130, 115, 123, 0.08);`
- **Depth Levels:**
  - **Level 0:** Background.
  - **Level 1:** Product Cards (Flat with 1px border or subtle shadow).
  - **Level 2:** Floating Action Buttons and Bottom Navigation.
  - **Level 3:** Modals and Bottom Sheets (High diffusion shadow, dimming the background with a 40% opacity Dusty Rosewood overlay).

## Shapes

The shape language is **Rounded**, reflecting the organic nature of fragrance ingredients and glass bottle silhouettes.

- **Base Radius:** 8px for small components like inputs and chips.
- **Large Radius:** 16px for product cards and bottom sheets.
- **Pill Shape:** Used exclusively for tags, category chips, and search bars to create a softer, more inviting interface.

## Components

### Primary Buttons
- **Style:** Contained.
- **Color:** Dusty Rosewood background with White text.
- **Padding:** 16px vertical, 32px horizontal.
- **Interaction:** Shifts to a slightly darker, more saturated tone on hover/active.

### Secondary Buttons
- **Style:** Outlined.
- **Color:** Transparent background, Ash Quartz (#82737b) border (1.5px), Ash Quartz text.
- **State:** High-class, reserved for "Add to Wishlist" or "Learn More."

### Product Cards
- **Structure:** Image first (ratio 4:5), followed by Title, Scent Profile (chips), and Price.
- **Detail:** Use a 1px border of the Steely Slate (#7b7677) to define the edge without adding heavy visual weight.

### Category Chips
- **Style:** Pill-shaped, light neutral background with text in Ash Quartz. 
- **Active State:** Dusty Rosewood background with White text.

### Search Bar
- **Design:** Pill-shaped with a soft shadow.
- **Placeholder:** Right-aligned text: "ابحث عن عطرك المفضل..." (Search for your favorite perfume...).

### Fragrance Pyramid Visual
- **Visual:** A stylized vertical or triangular stack representing Top, Heart, and Base notes. Use fine Sage Resin (#8a9574) lines and small circular icons for ingredients.

### Checkout Progress
- **Style:** Horizontal stepper with Thin Ash Quartz lines. Completed steps show a Dusty Rosewood checkmark; active steps show a Dusty Rosewood solid circle.

### Loading Skeletons
- **Animation:** A soft "shimmer" effect moving from right to left, using a gradient of Neutral to a slightly warmer, tinted Grey.

### Price Component
- **Formatting:** The value precedes the currency for Arabic readability (e.g., ٢٥٠ ر.س). The price font should be slightly heavier (Semi-Bold) than the surrounding text.