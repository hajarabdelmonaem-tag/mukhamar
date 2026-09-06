---
name: Atheer Al-Sharq
colors:
  surface: '#fff8f8'
  surface-dim: '#dfd8da'
  surface-bright: '#fff7f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f9f2f3'
  surface-container: '#f4eced'
  surface-container-high: '#eee6e8'
  surface-container-highest: '#e8e1e2'
  on-surface: '#1e1b1c'
  on-surface-variant: '#4d4449'
  inverse-surface: '#332f31'
  inverse-on-surface: '#f6eff0'
  outline: '#7f747a'
  outline-variant: '#d0c3c9'
  surface-tint: '#715668'
  primary: '#583f50'
  on-primary: '#ffffff'
  primary-container: '#715668'
  on-primary-container: '#f1cee4'
  inverse-primary: '#dfbdd2'
  secondary: '#685b62'
  on-secondary: '#ffffff'
  secondary-container: '#eddbe3'
  on-secondary-container: '#6c5f66'
  tertiary: '#414a2f'
  on-tertiary: '#ffffff'
  tertiary-container: '#586245'
  on-tertiary-container: '#d2ddb9'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#fcd9ee'
  primary-fixed-dim: '#dfbdd2'
  on-primary-fixed: '#291524'
  on-primary-fixed-variant: '#583f50'
  secondary-fixed: '#f0dee6'
  secondary-fixed-dim: '#d3c2ca'
  on-secondary-fixed: '#22191f'
  on-secondary-fixed-variant: '#4f434a'
  tertiary-fixed: '#dce7c2'
  tertiary-fixed-dim: '#c0cba7'
  on-tertiary-fixed: '#161e07'
  on-tertiary-fixed-variant: '#414a2f'
  background: '#fff7f9'
  on-background: '#1e1b1c'
  surface-variant: '#e8e1e2'
  dusty-rosewood: '#8c6f82'
  ash-quartz: '#82737b'
  sage-resin: '#8a9574'
  steely-slate: '#7b7677'
typography:
  display-lg:
    fontFamily: Noto Sans Arabic
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 64px
  display-lg-mobile:
    fontFamily: Noto Sans Arabic
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 48px
  headline-lg:
    fontFamily: Noto Sans Arabic
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 44px
  headline-md:
    fontFamily: Noto Sans Arabic
    fontSize: 26px
    fontWeight: '600'
    lineHeight: 36px
  headline-sm:
    fontFamily: Noto Sans Arabic
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Noto Sans Arabic
    fontSize: 19px
    fontWeight: '400'
    lineHeight: 30px
  body-md:
    fontFamily: Noto Sans Arabic
    fontSize: 17px
    fontWeight: '400'
    lineHeight: 28px
  label-lg:
    fontFamily: Noto Sans Arabic
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
  label-md:
    fontFamily: Noto Sans Arabic
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 18px
  price-display:
    fontFamily: Noto Sans Arabic
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  gutter: 16px
  margin-mobile: 24px
  margin-desktop: 64px
  section-gap: 48px
  component-gap-sm: 12px
  component-gap-md: 24px
---

## Brand & Style

The design system embodies "Modern Heritage," a sophisticated fusion of traditional Arabian perfumery and contemporary luxury. The aesthetic is rooted in **Minimalism** with **Tactile** accents, prioritizing expansive whitespace, soft textures, and editorial-grade layout structures. It targets an audience of fragrance connoisseurs who value authenticity and craftsmanship. 

The UI must evoke a sense of prestige, olfactory richness, and calm. High-fidelity product photography is the primary visual driver, supported by a refined layering system and precise typography that respects the fluid nature of the Arabic script.

## Colors

The palette is inspired by "Dusty Botanicals"—sun-bleached petals, resins, and high-end apothecary aesthetics. 

- **Primary (Dusty Rosewood):** A sophisticated, desaturated mauve used for high-priority branding and primary interactions.
- **Secondary (Ash Quartz):** A warm grey with violet undertones for supporting UI elements.
- **Tertiary (Sage Resin):** A muted herbal green representing botanical top notes.
- **Neutral (Steely Slate):** Used for grounding the interface and defining borders.
- **Surface Strategy:** The design uses a soft, off-white background (#fff8f8) to provide a warmer, more premium feel than pure white, reducing eye strain and enhancing the luxury aesthetic.

## Typography

This design system utilizes **Noto Sans Arabic** as its primary typeface. It was selected for its exceptional legibility and premium character, specifically designed to harmonize with the traditional proportions of the Arabic script while maintaining a clean, modern aesthetic.

- **Legibility:** Base font sizes are increased to account for the visual density of Arabic characters. Line heights are set at a generous 1.5x–1.6x ratio to prevent diacritics (harakat) from clashing between lines.
- **Hierarchy:** Headings use Bold (700) and Semi-Bold (600) weights to create clear structural definition against the lighter body weights.
- **RTL Context:** All typography is optimized for Right-to-Left reading. Localized numerals should be used for prices, paired with the "ر.س" currency symbol.
- **Clarity:** For body text, the weight is kept at Regular (400) but uses a slightly larger 17px/19px base to ensure readability on all device types.

## Layout & Spacing

The layout follows a **Fluid Grid** model with an "RTL-First" philosophy.

- **Grid System:** A 12-column grid is used for desktop (max-width 1440px), transitioning to a 4-column grid for mobile.
- **Rhythm:** An 8px base unit drives all spatial relationships (padding, margins, and gaps).
- **Margins:** Generous side margins (24px on mobile, 64px on desktop) ensure the content feels airy and exclusive.
- **Directionality:** All layouts are mirrored for Arabic. Use logical properties (padding-inline-start/end) to ensure the design remains robust across potential localizations.

## Elevation & Depth

Visual hierarchy is conveyed through **Tonal Layers** and **Ambient Shadows**, creating a soft, tactile experience.

- **Surfaces:** The interface uses a tiered system where the background is the lowest layer, and interactive elements sit on "Surface Container" layers with slightly warmer tints.
- **Shadows:** Shadows are highly diffused and tinted with Ash Quartz to avoid a "dirty" look. They should appear like natural ambient light rather than harsh structural drops.
- **Depth Scale:**
    - **Surface Level:** Base background.
    - **Raised Level:** Product cards and interactive tiles (subtle 1px border or very low-opacity shadow).
    - **Overlay Level:** Modals and bottom sheets, which use a 40% opacity Dusty Rosewood scrim to dim the background.

## Shapes

The shape language is **Rounded**, echoing the organic silhouettes of luxury fragrance bottles and botanical ingredients.

- **Standard Radius (8px):** Applied to input fields, small buttons, and selection chips.
- **Large Radius (16px):** Applied to product cards, featured banners, and modal containers.
- **Pill Shape:** Reserved for category tags and search bars to introduce a softer, more inviting geometry.

## Components

### Buttons
- **Primary:** Contained with Dusty Rosewood background and White text. Semi-bold weight.
- **Secondary:** Outlined with a 1.5px Ash Quartz border and matching text.
- **State:** Hover states involve a subtle increase in color saturation rather than a dramatic shift in brightness.

### Product Cards
- **Structure:** Generous vertical padding. Features the product image (4:5 ratio), followed by a Bold headline and the Price component.
- **Detail:** A 1px border in Steely Slate ensures definition on light backgrounds without feeling heavy.

### Input Fields
- **Style:** Subtle background tint (#f4eced) with a bottom-only border or a light all-around stroke.
- **Labels:** Right-aligned, using Label-MD typography.

### Fragrance Pyramid
- **Visual:** A custom component representing Top, Heart, and Base notes using Sage Resin icons and thin connecting lines to symbolize the olfactory structure.

### Price Display
- **Format:** [Value] [Currency], e.g., ٢٥٠ ر.س. Always use the `price-display` token which is weighted heavily for immediate scanning.