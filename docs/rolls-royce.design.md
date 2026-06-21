---
version: alpha
name: "Rolls-Royce"
website: "https://www.rolls-roycemotorcars.com"
description: >-
  An ultra-luxury automotive marque whose marketing site runs a single proprietary typeface — Riviera Nights — across every typographic tier, set universally in small-caps with aggressive letter-spacing of 2–2.5px. The palette is strictly monochromatic: pure white text and borders on near-black cinematic photography, with no brand accent color anywhere in the chrome. Every button is a ghost pill with a 30px or 99px radius on a transparent field; no fill, no icon, no shadow. The system is closer to an atelier lookbook than to automotive marketing — typography restrained to weight 300–400, imagery doing all the emotional labor.

seo:
  title: "Rolls-Royce Design System for React — monochrome atelier, Riviera Nights, 14 components"
  metaDescription: "Rolls-Royce's marketing-site design system as a DESIGN.md file. Monochromatic white-on-black palette, Riviera Nights at uniform small-caps with 2px letter-spacing, ghost-pill CTAs, 14 components. For React, Next.js, and AI tools."
  highlights:
    - "Monochromatic atelier palette — the system carries zero brand accent colors; the only chromatic elements are the photography itself, making the cars the sole color statement"
    - "Riviera Nights at small-caps everywhere — every heading, button, nav link, and label runs uppercase with 2–2.5px letter-spacing; no mixed-case prose in the chrome"
    - "Ghost-pill CTAs — buttons are transparent with a white border at 30px or 99px radius; no fill, no brand color, no icon — the car image behind the button does the selling"
    - "Weight 300 display at 70px — the hero h2 is the lightest heavy headline in any automotive system; Ferrari and Lamborghini go to 700 and above, Rolls-Royce stays at 300"
    - "Negative-space composition — the design system's primary layout device is restraint; large-format photography bleeds edge-to-edge with text placed as sparse white type on dark imagery"
  tags:
    - "Automotive"
  lastUpdated: "2026-05-19"
  author:
    name: "Dov Azencot"
    url: "https://x.com/dovazencot"
  opening: |
    Rolls-Royce Motor Cars' marketing site operates by subtraction. Where Ferrari fills its hero with saturated racing red and Lamborghini wires the viewport with hexagonal geometry, Rolls-Royce places a single cinematic photograph edge-to-edge and layers six words of white uppercase type at weight 300. The headline for the Coachbuild Collection — "COACHBUILD COLLECTION" — runs at 70px in Riviera Nights with 2.5px letter-spacing. There is no background color behind it. There is no CTA button in the hero fold. The car is the design system.

    The DESIGN.md file packages the system into a machine-readable spec for React and AI tools. Inside: 6 color tokens, all monochromatic — pure white for text and borders, three steps of near-black for surfaces, a light grey for a single background instance, and a medium grey used once as a shadow ink. No brand accent color appears anywhere in the captured chrome. Nine typography tokens run a single family, Riviera Nights, in a uniform weight band of 300–500, with every nav, button, and heading surface forced to uppercase and tracked at 2–2.5px. The radius scale jumps directly from 0 to 30px to 99px — no small-step tier. Fourteen components cover the ghost-pill button, the all-caps nav link, the cinematic hero heading, and the sparse card grid below the fold.

    Feed this file to an AI coding tool and it reproduces Rolls-Royce's specific moves: zero brand accent color, Riviera Nights small-caps across every chrome surface, transparent ghost-pill CTAs, and ultra-light display weight. The one discipline worth borrowing only if your product can carry the visual weight of a hero image this strong: trust the photography entirely and give the type system almost nothing to do. Rolls-Royce gets away with this because the cars are among the most visually arresting objects a camera can capture. Most brands need a supporting chromatic layer.
  related:
    - href: "/design"
      title: "Browse all design systems"
      description: "The full directory of DESIGN.md files on shadcn.io, with live mockups for each."
    - href: "https://www.rolls-roycemotorcars.com"
      title: "Rolls-Royce — official site"
      description: "Rolls-Royce Motor Cars' public marketing site — the source of truth for the live tokens captured in this file."
    - href: "https://github.com/google-labs-code/design.md"
      title: "The DESIGN.md specification"
      description: "Google Labs' open spec for machine-readable design system files — the format this page is built on."
  questions:
    - id: "primary-color"
      title: "What is Rolls-Royce's primary brand color?"
      answer: "Rolls-Royce's marketing site carries no brand accent color at all. The entire chrome — navigation, buttons, typography, borders — runs in pure white on dark photographic backgrounds. The only colors visible on the page are those present in the car photography itself: the iridescent blue of the Coachbuild Droptail, the warm interior leathers, the mountain scenery. This is not an oversight — it is the deliberate positioning of an atelier that considers any logo-colored CTA button beneath its brand. The system's theme color is declared as a deep purple-black in the HTML meta tag, but that value does not appear in any rendered chrome element captured from the page."
    - id: "typography"
      title: "What typeface does Rolls-Royce use, and what should I use as a substitute?"
      answer: "Rolls-Royce runs Riviera Nights — a proprietary display typeface — across every typographic tier including navigation, headings, body paragraphs, and button labels. Every surface applies uppercase text-transform with 2–2.5px letter-spacing; there is no mixed-case prose in the chrome. Weight ranges from 300 (hero display, body) to 500 (labels, small caps). The closest open-source substitute for the light display sizes is Cormorant Garamond at equivalent weights, which shares Riviera Nights' high-contrast stroke modulation; for the body and nav tiers, Josefin Sans at weight 300 with tracking set to 0.15em is the nearest match."
    - id: "radius-philosophy"
      title: "What is Rolls-Royce's corner-radius philosophy?"
      answer: "The radius scale has three values and nothing in between: 0px on all content surfaces including cards, panels, and hero sections; 30px on the primary CTA buttons (the bespoke ghost-pill treatment); and 99px on the secondary pill buttons that appear in the discover/explore navigation rows. There is no 4px, 8px, or 16px middle tier. The jump from sharp-cornered cards to a 30px pill button is intentional — the radius contrast signals that the CTA is the only interactive element on an otherwise art-directed page. Aston Martin and Ferrari both use smaller radii (4–8px); Rolls-Royce's 30px pill is among the most generous in the automotive category."
    - id: "button-design"
      title: "How does Rolls-Royce design its CTA buttons?"
      answer: "Every button on the Rolls-Royce marketing site is a ghost pill: transparent background, 1px white border, white uppercase text in Riviera Nights at 12px / weight 400 / 2px letter-spacing. There is no filled button, no brand-colored CTA, and no hover-fill effect captured in the static surface. The ghost treatment places the car photography fully visible behind the button — the button is framing, not interruption. This is the opposite of the automotive convention where Ferrari uses a red-fill CTA and Porsche uses a black-fill pill; Rolls-Royce's button steps back so the vehicle can remain the visual subject."
    - id: "use-in-project"
      title: "Can I use this DESIGN.md to build a luxury brand marketing site?"
      answer: "Yes — the file is designed to be fed into Claude, Cursor, or any AI tool that reads structured design tokens. The agent will reproduce Rolls-Royce's specific moves: monochromatic white-on-dark chrome, Riviera Nights-equivalent small-caps at 2–2.5px tracking, ghost-pill CTAs at 30px radius, and ultra-light display weight at 300. The tokens resolve without invention. The key caveat for replication: the system depends almost entirely on full-bleed cinematic photography to carry all chromatic and emotional weight. Implemented without imagery of equivalent quality, the monochromatic scheme reads as unfinished rather than restrained. Plan the photography strategy before committing to the no-accent-color discipline."

mockups:
  - "marketing-hero"
  - "media-grid"

colors:
  ink: "#ffffff"
  ink-muted: "#222222"
  canvas: "#000000"
  surface-1: "#151515"
  hairline: "#ffffff"
  shadow: "#888888"

typography:
  display-xl:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 70px
    fontWeight: 300
    lineHeight: 80px
    letterSpacing: "2.5px"
  heading-lg:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 20px
    fontWeight: 400
    lineHeight: 30px
    letterSpacing: "2.5px"
  heading-sm:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 16px
    fontWeight: 400
    lineHeight: 26px
    letterSpacing: "2.5px"
  body-md:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 14px
    fontWeight: 300
    lineHeight: 22px
    letterSpacing: "0.8px"
  body-sm:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 14px
    fontWeight: 300
    lineHeight: 28px
    letterSpacing: "0.5px"
  body-lg:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 16px
    fontWeight: 400
    lineHeight: 24px
    letterSpacing: "0.5px"
  label-md:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 12px
    fontWeight: 500
    lineHeight: 24px
    letterSpacing: "2px"
  button-md:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 20px
    letterSpacing: "2px"
  nav-link:
    fontFamily: "\"Riviera Nights\", Helvetica, Arial, -apple-system, sans-serif"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 20px
    letterSpacing: "2px"

rounded:
  none: "0px"
  pill: "30px"
  full: "99px"
  circle: "50%"

spacing:
  xs: "8px"
  sm: "15px"
  base: "16px"
  lg: "24px"
  xl: "32px"
  2xl: "48px"
  section: "50px"

components:
  button-primary:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.button-md}"
    rounded: "{rounded.pill}"
    padding: "15px 16px"
    borderColor: "{colors.hairline}"
    height: "44px"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.button-md}"
    rounded: "{rounded.full}"
    padding: "15px 16px"
    borderColor: "{colors.hairline}"
    height: "44px"
  top-nav:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.nav-link}"
    padding: "16px"
    height: "65px"
  nav-link:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.nav-link}"
    padding: "0px 16px"
  hero-heading:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.display-xl}"
    padding: "0px"
  section-heading:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.heading-lg}"
    padding: "0px"
  body-paragraph:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.body-md}"
    padding: "12px 24px 0px"
  card:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.none}"
    padding: "24px"
  card-label:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    padding: "0px"
  hero-section:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.display-xl}"
    rounded: "{rounded.none}"
  footer:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink-muted}"
    typography: "{typography.body-sm}"
    padding: "48px 16px"
  discover-label:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.heading-sm}"
    padding: "0px"
  category-strip:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.label-md}"
    padding: "8px 16px"
  image-caption:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    typography: "{typography.body-sm}"
    padding: "12px 0px"
---

## Overview

Rolls-Royce Motor Cars' marketing site operates by the principle of absolute restraint. **Atelier by subtraction.** Where Ferrari fills its hero canvas with saturated red and Lamborghini deploys geometric accents, Rolls-Royce places a single cinematic photograph at full bleed and overlays six words of white uppercase type at weight 300. There is no brand accent color. There is no filled CTA button. The photography carries all chromatic and emotional labor; the design system exists only to frame it.

The comparison to peer ultra-luxury automotive brands makes the move legible. Aston Martin uses a dark teal accent and fills its primary button. McLaren wires orange voltage into its chrome. Bugatti uses a red-and-blue racing identity on every surface. Rolls-Royce holds the chrome neutral through the full page — not as an absence of decision but as the most deliberate one available to a brand whose product costs as much as a house.

Typography is Riviera Nights across every tier — a single proprietary sans-serif family running from the 70px display headline to the 12px navigation label, with every surface set in uppercase and tracked at 2–2.5px. Weight never exceeds 500. The display sits at weight 300, lighter than any comparable automotive hero headline in the directory.

**Key Characteristics:**
- Monochromatic chrome — pure white text and borders on near-black surfaces throughout, zero chromatic brand accent anywhere in the captured page.
- Riviera Nights small-caps across every tier — navigation, hero heading, body copy, buttons, and labels all use uppercase text-transform with 2–2.5px letter-spacing.
- Weight 300 display at 70px — the lightest heavy-headline treatment in the automotive category; Ferrari, Lamborghini, and Aston Martin all go to 600+ on their display tiers.
- Ghost-pill CTAs — transparent background, 1px white border, 30px or 99px radius, no fill, no brand color, no hover state captured.
- Zero radius on content surfaces — every card, section panel, and content container runs at 0px; the pill radius appears only on buttons.
- Full-bleed cinematic photography as the only chromatic voice — the cars' paint, interiors, and settings supply all color.
- 16px base spacing with 50px section gaps — sparse vertical rhythm that keeps the page from feeling dense.

## Colors

### Text

- **Ink** (`{colors.ink}` — ): frequency 151. Used as text (72), border (73), bg (6). The sole text and border color across the entire chrome — white on dark photography throughout every section.
- **Ink Muted** (`{colors.ink-muted}` — ): frequency 100. Used as text (50), border (50). A very dark charcoal used in lower-contrast footer and sub-panel contexts — nearly black but distinct from the true canvas.

### Surface

- **Canvas** (`{colors.canvas}` — ): frequency 23. Used as bg (1), text (11), border (10), gradient (1). The deepest black, used on the page floor and inside gradient overlays behind photography.
- **Surface-1** (`{colors.surface-1}` — ): frequency 14. Used as bg (2), text (6), border (6). An elevated dark surface for card containers and the discover-content grid below the fold.
- **Shadow** (`{colors.shadow}` — ): frequency 1, as shadow ink. Used once for a subtle elevation halo — the system has nearly no shadow presence.

### Hairline

- **Hairline** (`{colors.hairline}` — ): carried by the ink token at border contexts (73 occurrences). White borders on dark surfaces are the only divider and button-edge treatment in the system.

## Typography

### Font Family

The system runs **Riviera Nights** exclusively — a proprietary display-and-text sans-serif shipped by Rolls-Royce as part of its global brand identity. Fallbacks walk `Helvetica, Arial, -apple-system, sans-serif`. There is no second voice, no serif, no monospace tier. The entire typographic range — 70px hero down to 12px label — uses one family at one transform setting: uppercase.

### Hierarchy

| Token | Size | Weight | Line Height | Letter Spacing | Use |
|---|---|---|---|---|---|
| `{typography.display-xl}` | 70px | 300 | 80px | 2.5px | Hero h2 — "Coachbuild Collection" |
| `{typography.heading-lg}` | 20px | 400 | 30px | 2.5px | Section h3 headings |
| `{typography.heading-sm}` | 16px | 400 | 26px | 2.5px | Sub-section h4 titles |
| `{typography.body-lg}` | 16px | 400 | 24px | 0.5px | Lead body text |
| `{typography.body-md}` | 14px | 300 | 22px | 0.8px | Standard body paragraph |
| `{typography.body-sm}` | 14px | 300 | 28px | 0.5px | Secondary body / captions |
| `{typography.label-md}` | 12px | 500 | 24px | 2px | Category labels, section tags |
| `{typography.button-md}` | 12px | 400 | 20px | 2px | CTA button labels |
| `{typography.nav-link}` | 12px | 400 | 20px | 2px | Navigation links |

### Principles

Display sits at weight 300 — the lightest treatment for a headline this large anywhere in the automotive directory. Ferrari and Lamborghini use 600+ for their display tiers; Rolls-Royce's 70px / 300 reads as a whisper rather than a proclamation, which is the intended effect for a brand where the car itself is meant to speak. Weight 500 is reserved for the smallest label tier — 12px small-caps tags — where it prevents thin letterforms from disappearing at small sizes. The 2–2.5px letter-spacing is the system's defining typographic move: applied universally, it transforms Riviera Nights from a text sans into an atelier-display face at every size.

### Note on Font Substitutes

Riviera Nights is proprietary. For the display tier (70px / 300), **Cormorant Garamond Light** in uppercase achieves a similar high-contrast, calibrated quality. For the navigation and button tiers (12px / 400 / uppercase / 2px tracking), **Josefin Sans Light** is the closest open-source match — it was designed specifically for the small-caps tracking treatment. For body paragraphs in the 14px range, **Montserrat Light** at 0.08em tracking transfers cleanly.

## Layout

### Spacing System

- **Base unit:** 16px — the dominant padding token, appearing 19 times in the captured page.
- **Tokens:** `{spacing.xs}` 8px · `{spacing.sm}` 15px · `{spacing.base}` 16px · `{spacing.lg}` 24px · `{spacing.xl}` 32px · `{spacing.2xl}` 48px · `{spacing.section}` 50px.
- **Section spacing:** 50px bottom on hero content; 64px between discover content and the editorial grid.
- **Card internal padding:** `{spacing.lg}` (24px) on the discover-category cards.

### Grid & Container

- **Hero:** full-bleed photography, no max-width constraint, text overlaid centered with left-aligned positioning at a 16px horizontal margin.
- **Discover grid:** 3-column card layout below the fold — the "Discover Further" section shows vehicle category tiles with 24px internal padding and no border.
- **Navigation:** horizontal top-nav with no background fill — transparent over the hero photography until a scroll threshold.

### Rhythm

The page paces slowly. The hero photograph occupies nearly the full viewport; the first text moment is sparse white type at the bottom third. Below the fold, content arrives in generous single-panel sections with 50px of breathing room between them. The discover grid is the densest section — three cards at equal width — and even there, the typography-to-image ratio favors the image. The rhythm is the brand promise made spatial.

## Elevation

The system has essentially **no shadow tier**. The captured page records one shadow occurrence, using a mid-grey (`{colors.shadow}` — grey) ink at low opacity. No cards use drop shadows. Depth comes from placing content against full-bleed photography rather than from surface layering — the car's three-dimensional form in the photograph provides all perceived depth.

- **Flat (no shadow):** 99% of all surfaces — navigation, cards, hero, footer.
- **Single shadow:** one subtle halo on an interactive element; the grey ink reads softer than pure black and avoids drawing attention away from the photography.

## Shapes

The radius scale has **three values and two contexts**:

- `{rounded.none}` 0px — all content surfaces including every card, panel, and page section. Sharp corners on every container throughout the page.
- `{rounded.pill}` 30px — the primary CTA buttons ("VIEW THE COLLECTION", "CONFIGURE YOUR CAR"). Applied to the ghost-pill buttons with 15px vertical and 16px horizontal padding.
- `{rounded.full}` 99px — the secondary pill variant used in the discover/explore navigation rows. Renders as a fully-rounded capsule on small label-height elements.
- `{rounded.circle}` 50% — avatar and icon treatments in profile and media contexts.

There is no 4 / 8 / 12 / 16px middle tier. Content surfaces are always sharp; interactive pill surfaces jump to 30px or 99px. The contrast between sharp cards and pill buttons is the system's only shape-language device.

## Components

**`button-primary`** — The signature ghost pill. Transparent background, white border 1px, white text in `{typography.button-md}` (12px / 400 / uppercase / 2px tracking), `{rounded.pill}` 30px radius, 15×16px padding, 44px height. "VIEW THE COLLECTION" is the canonical instance, placed at the center of the hero after the heading. No fill, no background color behind it — the car photograph shows through.

**`button-secondary`** — Same ghost treatment with a `{rounded.full}` 99px radius. Used in the discover-category navigation rows for shorter action labels. Same border, text, and sizing discipline as the primary.

**`top-nav`** — Transparent background on page load, white text, 65px height, 16px horizontal padding. The Rolls-Royce wordmark and Spirit of Ecstasy mark sit flush left; navigation links (Models, Bespoke, World of Rolls-Royce, Dealers, My Rolls-Royce) run center; a single account link sits right. No nav background fill is visible on the captured static surface.

**`nav-link`** — Transparent background, white text, `{typography.nav-link}` (12px / 400 / uppercase / 2px tracking), 16px horizontal padding. The smallest interactive text element in the system — yet the same tracked uppercase as the hero heading.

**`hero-heading`** — White text on transparent overlay, `{typography.display-xl}` (70px / 300 / uppercase / 2.5px tracking). 0 horizontal padding — the text sits directly in the compositional space of the photograph. The lightest display weight in the automotive category.

**`section-heading`** — White text, `{typography.heading-lg}` (20px / 400 / uppercase / 2.5px tracking). Section h3 markers — "DISCOVER FURTHER", "COACHBUILD COLLECTION".

**`body-paragraph`** — White text, `{typography.body-md}` (14px / 300), 12px top padding on a 24px left indent. The descriptive copy beneath vehicle names — sparse, never more than 2–3 lines.

**`card`** — Dark `{colors.surface-1}` fill, white text, 0px radius, 24px internal padding. Holds the three-tile discover grid images and their below-image text labels.

**`card-label`** — Transparent, white text, `{typography.label-md}` (12px / 500 / uppercase / 2px tracking). The category tags above discover grid items — "THE SPIRIT OF INNOVATION", "ROLLS-ROYCE MOTOR CARS BESPOKE".

**`hero-section`** — Full-bleed canvas black floor beneath the photography, text overlaid, no radius, no border.

**`discover-label`** — White text at `{typography.heading-sm}` (16px / 400 / uppercase / 2.5px tracking). The per-card title beneath discover-grid images.

**`category-strip`** — Transparent, white text, `{typography.label-md}`, 8×16px padding. The horizontal navigation categories in the discover row — "MOTOR CARS", "BESPOKE", "EXPERIENCES".

**`image-caption`** — White text, `{typography.body-sm}` (14px / 300 / 0.5px tracking), 12px top padding. Descriptive captions beneath vehicle images in the editorial sections.

**`footer`** — Dark `{colors.surface-1}` surface, charcoal `{colors.ink-muted}` text, 48×16px padding. No elevated border from the page floor — the footer continues the dark surface directly.

## Do's and Don'ts

**Do** let the photography carry all chromatic weight. The design system is built on the assumption that a Rolls-Royce photograph — whether the Droptail's iridescent paint on a mountain road or the Phantom's interior detail — contains more visual authority than any brand color applied to chrome.

**Do** apply uppercase and 2px letter-spacing to every text element regardless of size. The tracked small-caps treatment is the single typographic identity token; departing from it at any tier — even the smallest label — breaks the system's internal consistency.

**Do** use `{rounded.pill}` 30px or `{rounded.full}` 99px on buttons and pill labels only. The sharp/pill radius contrast is the system's only shape-language move — introducing a 4 / 8 / 16px middle tier softens the contrast and makes buttons read as ordinary interface elements rather than deliberate framing devices.

**Do** keep display weight at 300. The ultra-light hero headline at 70px / 300 is the system's most counter-discoverable typographic decision and its most precise brand signal — bumping to 500 or 600 moves it toward Ferrari or Aston Martin territory.

**Don't** introduce any accent color into the chrome. Not a hover state in brand gold, not a CTA button in heritage green, not an icon in teal. The monochromatic discipline is the design system's single hardest constraint and its clearest brand statement. Any chromatic addition reads as lacking conviction.

**Don't** use a filled button background. The ghost-pill treatment — transparent with a 1px white border — is the only CTA treatment in the system. Filling it with even a near-white or off-white tone (, as Cloudflare uses for cream) would change the figure-ground relationship between the button and the photography behind it.

**Don't** use shadows on any card or content surface. There is one shadow occurrence in the entire captured page. The system achieves separation between surfaces through dark tonal contrast (`{colors.surface-1}` on `{colors.canvas}`) rather than shadow. Adding shadows makes the surfaces feel like UI components rather than gallery frames.

**Don't** set body copy at weight 400 or above. The body tier runs at 300 — the same weight as the 70px display headline. Weight 400 appears only on navigation and sub-heading tiers; weight 500 only on the smallest labels. Increasing body weight makes the text compete with the imagery rather than recede gracefully beneath it.

## Known Gaps

- **Hover and focus states:** no hover or active states were captured from the static marketing surface — it is likely that ghost-pill buttons have a subtle white-fill or opacity transition, but this is not documented here.
- **Dark/light mode:** the site is dark-only on the captured surface; a light-mode variant may exist for editorial content pages (model specification sheets, dealer locator) but is not represented.
- **Animation:** the hero photography transitions between multiple vehicle images on a slow dissolve cycle; the duration and easing curves are not captured.
- **Configurator surface:** the Rolls-Royce bespoke configurator (`www.rolls-roycemotorcars.com/configure`) likely carries a much richer token set with color swatch selectors, material-picker components, and multi-step wizard layouts that are not exposed on the marketing homepage.
- **Bespoke variant palette:** the Black Badge sub-brand uses a near-identical chrome system but with a distinct set of darkness levels — the system may have a `surface-2` token for the deeper-black Black Badge contexts.
- **Responsive breakpoints:** the mobile navigation drawer, hamburger state, and image-crop behavior at mobile widths are not represented in the captured desktop tokens.
- **Typography at mobile sizes:** the 70px display headline almost certainly scales down significantly on mobile; the scale factor and breakpoint are not captured.
