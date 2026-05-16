---
name: Wareflow
description: Multi-tenant warehouse inventory management — clear, capable, trusted.
colors:
  signal-blue: "#004ac6"
  signal-blue-bright: "#2563eb"
  signal-blue-faint: "#eeefff"
  signal-blue-light: "#eff4ff"
  indigo-accent: "#3e3fcc"
  indigo-container: "#585be6"
  error-red: "#dc2626"
  error-container: "#ffdad6"
  ink-deep: "#0f172a"
  slate-mid: "#475569"
  slate-dark: "#334155"
  work-surface: "#f8fafc"
  card-white: "#ffffff"
  surface-low: "#f1f5f9"
  surface-mid: "#e8edf5"
  surface-high: "#dde3ed"
  border-subtle: "#cbd5e1"
  border-standard: "#94a3b8"
  inverse-dark: "#1e293b"
  inverse-medium: "#162032"
typography:
  display:
    fontFamily: '"Plus Jakarta Sans", sans-serif'
    fontSize: "27px"
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: "-0.02em"
  headline:
    fontFamily: '"Plus Jakarta Sans", sans-serif'
    fontSize: "22px"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.01em"
  title:
    fontFamily: '"Plus Jakarta Sans", sans-serif'
    fontSize: "15px"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: '"Plus Jakarta Sans", sans-serif'
    fontSize: "13.5px"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: '"Plus Jakarta Sans", sans-serif'
    fontSize: "11px"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.05em"
rounded:
  xs: "6px"
  sm: "8px"
  md: "10px"
  lg: "14px"
  xl: "18px"
spacing:
  xs: "4px"
  sm: "12px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.signal-blue}"
    textColor: "{colors.card-white}"
    rounded: "{rounded.md}"
    padding: "8px 20px"
    typography: "{typography.body}"
  button-primary-hover:
    backgroundColor: "{colors.signal-blue-bright}"
  button-secondary:
    backgroundColor: "{colors.surface-low}"
    textColor: "{colors.slate-dark}"
    rounded: "{rounded.md}"
    padding: "8px 20px"
  button-secondary-hover:
    backgroundColor: "#e2e8f0"
  card:
    backgroundColor: "{colors.card-white}"
    rounded: "{rounded.lg}"
    padding: "24px"
  card-hover:
    backgroundColor: "{colors.card-white}"
  badge:
    rounded: "{rounded.xs}"
    padding: "2px 9px"
  nav-link-active:
    backgroundColor: "{colors.signal-blue-light}"
    textColor: "{colors.signal-blue}"
    rounded: "{rounded.md}"
  input:
    backgroundColor: "{colors.surface-low}"
    rounded: "{rounded.sm}"
    padding: "8px 12px"
---

# Design System: Wareflow

## 1. Overview

**Creative North Star: "The Clear Record"**

Wareflow is designed around a single metaphor: a well-kept logbook. Every number is accurate. Every action is traceable. The interface never calls attention to itself; it calls attention to the data. Like a logbook that a professional has maintained for years, Wareflow earns trust through consistency, density, and restraint.

The visual language is a single sans family (Plus Jakarta Sans) at tightly-controlled weights, a cool near-white field, and a single accent color so precise in its placement that it feels authoritative the moment it appears. Signal Blue (#004ac6) is not a decoration; it is a directive. When you see it, something is interactive, active, or important. Everything else is Ink or Slate.

This system explicitly rejects two traps. First, the bloated enterprise: heavy sidebars, 20-column tables, modals stacked inside modals, settings that require a manual. Wareflow is a tool, not an ERP. Second, the over-animated startup: skeleton loaders on every panel, entrance choreography on routine navigation, transitions that make the app feel like a demo. In Wareflow, motion is only used to confirm a state change; it never announces itself.

**Key Characteristics:**
- Single sans typeface throughout; hierarchy through weight (400 to 700) and size, not family switching.
- Restrained color strategy: Signal Blue occupies less than 10% of any screen. Its scarcity is its authority.
- Flat surfaces by default; depth through tonal surface layers, not shadow stacking.
- Tables for operations data. Cards for summaries and KPIs only.
- Motion limited to state transitions (150-200ms, ease-out). No entrance choreography on routine interactions.
- Dark mode via CSS class toggle; full dark palette defined with blue-shifted neutrals.

## 2. Colors: The Signal Palette

A cool, near-white field anchored by a single deep blue. Everything else is Slate.

### Primary
- **Signal Blue** (#004ac6): The brand's sole directive color. Used on primary buttons, active navigation links, interactive text links, checkbox accent, and focus rings. Occupies less than 10% of any screen at any time.
- **Signal Blue Bright** (#2563eb): Hover and pressed state for Signal Blue elements. Never used as a resting fill.
- **Signal Blue Light** (#eff4ff): Active navigation background. The lightest surface tint that names Signal Blue's presence without using it directly.
- **Signal Blue Faint** (#eeefff): On-container text tint; the lightest possible echo of the primary hue.

### Tertiary
- **Indigo Accent** (#3e3fcc): Reserved exclusively for the Owner role badge and tertiary-tier accent distinctions. Visually adjacent to Signal Blue but distinct in hue, so role hierarchy reads immediately.

### Error
- **Error Red** (#dc2626): Danger states, delete confirmations, low-stock quantity alerts, error flash messages. Paired with text or icon, never color alone.
- **Error Container** (#ffdad6): Background tint for inline error callouts.

### Neutral
- **Ink Deep** (#0f172a): All primary text. Near-black with a blue cast that ties it to Signal Blue's hue family. Never pure black.
- **Slate Mid** (#475569): Secondary text, hint labels, icon defaults, placeholder text.
- **Slate Dark** (#334155): Secondary button label; slightly heavier than Slate Mid for interactive weight.
- **Work Surface** (#f8fafc): App shell background. A cool near-white with the faintest blue undertone.
- **Card White** (#ffffff): Card and modal backgrounds. One step brighter than Work Surface, creating a card-on-field distinction without a visible shadow at rest.
- **Surface Low** (#f1f5f9): Table header backgrounds, input fields, inactive tab fills. The primary tonal depth step.
- **Surface Mid** (#e8edf5): Hover states for secondary surfaces; progress bar tracks; the second tonal depth step.
- **Surface High** (#dde3ed): Deepest tonal layer for inactive filters or nested containers.
- **Border Subtle** (#cbd5e1): All hairline dividers: table row separators, card borders, input strokes at rest.
- **Border Standard** (#94a3b8): Icon defaults, stronger dividers, inactive input borders.
- **Inverse Dark** (#1e293b): Dark mode card and sidebar background.
- **Inverse Medium** (#162032): Dark mode card surface, slightly elevated above Inverse Dark.

### Named Rules
**The Signal Rule.** Signal Blue (#004ac6) is used as a fill on less than 10% of any screen. Primary buttons, active nav links, and focus rings are its territory. Using it as a section background, a banner fill, or a decorative accent violates this rule. If you need warmth or variety, use the Tertiary or semantic color tokens; do not dilute Signal Blue's authority by spreading it.

**The Semantic Color Rule.** Error Red is reserved for genuine error states and destructive confirmations. Do not use it for warnings, information, or decoration. Amber and Violet appear in the codebase as chart-palette colors only; they are not status signals.

## 3. Typography

**Primary Font:** Plus Jakarta Sans (Google Fonts, weights 300–800)
**Icon Font:** Material Symbols Outlined (variable font; FILL 0, wght 400, opsz 24)
**Monospace:** System mono (`font-mono` utility) for SKU codes, timestamps, and reference numbers only.

**Character:** Plus Jakarta Sans at a tight tracking reads as both friendly and precise — closer to Notion or Linear than to the heavy corporate sans of enterprise tools. The geometric letterforms hold well at 11px label size and stay readable at 27px display without needing a separate display face.

### Hierarchy
- **Display** (700, 27px, line-height 1.1, tracking -0.02em): KPI metric values on the dashboard. The largest in-app element. Not used in running text.
- **Headline** (700, 22–24px, line-height 1.2, tracking -0.01em): Page titles (`<h1>`), dashboard welcome header. One per page.
- **Title** (600, 15px, line-height 1.4): Card section headers, modal titles, sidebar section headings. The workhorse headline level.
- **Body** (400–500, 13–13.5px, line-height 1.6): All standard UI text, table cell content, form labels. Max line length 65–75ch in reading contexts.
- **Label** (600, 10.5–11.5px, line-height 1.4, tracking 0.05em, uppercase): Table column headers, navigation section dividers, metadata chips, badge text. Always uppercase with generous tracking; never used for body copy.

### Named Rules
**The Weight Hierarchy Rule.** Hierarchy is achieved through weight contrast (400 body to 700 headline) and size steps with at least a 1.25 ratio. Never achieve emphasis by size alone at the body level; pair it with a weight shift. A 14px/600 label is more readable than a 16px/400 one in data-dense layouts.

**The Mono Exception Rule.** Monospace (`font-mono`) is reserved for machine-readable strings: SKUs, reference codes, timestamps, invite tokens. Its visual distinctness signals "this is a code, not a phrase." Using mono for decorative copy or subheadings violates this convention.

## 4. Elevation

Wareflow uses tonal layering as its primary depth mechanism. Shadows are structural responses to state, not decorative atmosphere.

At rest, every surface is flat. A card resting on the Work Surface background is distinguished by its Card White fill and a hairline border (#e2e8f0), not by a shadow. The eye reads depth through the tone step, not the blur. This keeps the interface from feeling "floaty" and aligns with the Clear Record metaphor: a logbook page is flat against the desk.

Shadows appear in three contexts only: hover lift on interactive cards (a subtle ambient), modal elevation (a significant lifted state communicating interruption), and the colored accent glow on a primary button hover.

### Shadow Vocabulary
- **Ambient hover** (`0 4px 6px -1px rgba(0,0,0,.10), 0 2px 4px -2px rgba(0,0,0,.10)`): Applied on `.card:hover`. Signals interactivity without drama.
- **Modal lift** (`0 24px 64px rgba(0,0,0,.15)`): Applied to `.modal`. The only large shadow in the system. Its scale communicates that the modal is interrupting the normal flow; it is not reused elsewhere.
- **Button glow** (`0 4px 14px rgba(0,74,198,.28)`): Applied on `.btn-primary:hover` only. A colored shadow that reinforces Signal Blue's identity on the primary action. Not used on secondary or ghost buttons.

### Named Rules
**The Flat-By-Default Rule.** Surfaces are flat at rest. Shadows appear only as a response to state (hover, elevation, modal). If you find yourself adding a shadow to a resting card for "depth," use a tonal background tint instead. Shadows are responses, not decorations.

## 5. Components

### Buttons
The button language is intentionally minimal: two variants at rest, no tertiary ghost button in the primary flow.

- **Shape:** Gently rounded (10px radius). Not pill-shaped; not square-cornered. Radiused enough to feel friendly, structured enough to feel capable.
- **Primary** (Signal Blue fill, white label, 8px / 20px padding, 13.5px / 600): The single most important interactive element on any screen. Used once per page section for the primary action.
- **Hover / Focus:** Background shifts to Signal Blue Bright (#2563eb) + colored glow shadow. Focus ring: 3px / rgba(0,74,198,.30).
- **Secondary** (Surface Low fill, Slate Dark label, Border Subtle stroke): For cancel, reset, and non-destructive secondary actions. Never in a prominent visual position competing with a primary button.
- **Destructive** (Error Red fill, white label): Used only in delete confirmations, never in the main UI as a shortcut. Always inside a modal with a cancel escape.

### Badges
Small, inline role and status labels. The most color-expressive component in the system.

- **Shape:** 6px radius, 2px / 9px padding, 11.5px / 600.
- **Active / In:** Emerald tint (green-50 background, green-700 label). Positive completion state.
- **Inactive / Suspended:** Surface Low background, Slate Mid label. Neutral, receded.
- **Warning / Out:** Error-tinted background, red label. Urgency without full error weight.
- **Admin:** Signal Blue Light background, Signal Blue label.
- **Owner:** Indigo-tinted background, Indigo Accent label. Visually distinct from Admin at a glance.
- **Transfer / Adjustment:** Blue-tinted / violet-tinted backgrounds for stock movement type codes.

### Cards
Cards are for summaries, KPIs, and chart containers. Not for inventory rows.

- **Corner Style:** Gently rounded (14px). Larger than buttons to feel like a container, not a control.
- **Background:** Card White (#ffffff) on Work Surface (#f8fafc). The 3-step tonal gap is the entire depth story.
- **Shadow Strategy:** None at rest. Ambient hover shadow on interactive cards only (see Elevation).
- **Border:** Hairline (#e2e8f0, 1px). Present at rest; never colored.
- **Internal Padding:** 24px (spacing.lg) standard. Sections within cards use 16px (spacing.md) sub-padding.

### Inputs / Fields
- **Style:** Surface Low fill, Border Subtle stroke (1px), 8px radius. Reads as recessed relative to the Card White background they usually appear on.
- **Focus:** Border shifts to Signal Blue; 3px ring rgba(0,74,198,.18). The only place Signal Blue appears on a form element.
- **Label:** Label-tier typography (11px / 600 / uppercase / tracking 0.05em). Label always above, never inline.
- **Error:** Border becomes Error Red; error message in red below the field.
- **Disabled:** 60% opacity; cursor not-allowed.

### Navigation
- **Sidebar structure:** Fixed 260px left panel. Logo + tenant name at top, nav groups with uppercase label dividers, user identity footer.
- **Nav link default:** 13.5px / 500 / Slate Mid. 9px / 14px padding. 10px radius.
- **Nav link hover:** Surface Mid background (#e8edf5), Ink Deep text. No animation.
- **Nav link active:** Signal Blue Light background (#eff4ff), Signal Blue text, 600 weight. The only non-neutral fill in the navigation.
- **Section dividers:** 10.5px / 600 / uppercase / tracking-widest / Slate Mid. Visually receded; structural only.
- **Mobile:** Sidebar translates off-screen (transform translateX). Overlay + blur on open. No animation on individual nav links, only the panel slide.

### Dashboard Header (Signature Component)
A full-width banner that opens the dashboard, distinct from the standard card.

- **Background:** Deep navy gradient (linear-gradient(135deg, #001a5c, #003399, #004ac6)). The only place in the app UI where a large dark fill is used. Creates a clear "you are at the top level" signal.
- **SVG grid overlay:** Subtle dashed grid pattern at 6% opacity. Structural decoration referencing the warehouse floor-plan metaphor.
- **Typography on dark:** White headline (700 / 22px), pale blue date label (11px / mono / uppercase), muted blue supporting text.
- **Inline alert pill:** Error Red / 20% on dark when low stock alerts exist. Communicates urgency inside the banner without a separate alert component.

### Tables
Operations data lives in tables, not cards.

- **Header row:** Surface Low background, Label-tier typography (uppercase / 600 / tracking-wide / Slate Mid).
- **Body rows:** Card White background, Body-tier typography. Hover: Work Surface (#f8fafc) tint, no border.
- **Inline actions:** Icon buttons (p-1.5 / 10px radius). Default: Slate Mid. Hover: contextual (Signal Blue for edit, Error Red for delete).
- **Pagination:** Slim bar below the table (Surface Low background, border-top) with current/total count and page links.

## 6. Do's and Don'ts

### Do:
- **Do** use Signal Blue only for the primary action, active navigation state, focus rings, and interactive link text. Its scarcity is its authority.
- **Do** use tonal surface layers (Card White on Work Surface, Surface Low in table headers) to create depth. The tonal gap does the work of shadows at rest.
- **Do** use tables for inventory, stock movements, user lists, and warehouse data. Tables communicate operations; cards communicate summaries.
- **Do** use Label-tier typography (11px / 600 / uppercase) for all table headers, navigation section dividers, and metadata. Never use sentence-case label typography at this scale.
- **Do** keep motion to state transitions (150–200ms ease-out). A modal sliding in, a card lifting on hover, a progress bar filling — these are confirmations of state. Limit entrance animations to page-load contexts only.
- **Do** use Badge components for role and status signals. Every badge variant has a defined color meaning; don't invent new badge colors outside the defined set.
- **Do** pair every color-only state signal with a text or icon secondary indicator. Error Red quantity is also accompanied by a warning icon.

### Don't:
- **Don't** build nested navigation, collapsible sidebar sub-menus, or breadcrumb trails inside breadcrumbs. Wareflow should never feel like SAP or a legacy ERP — depth is a failure mode, not a feature.
- **Don't** add skeleton loaders, animated spinners, or loading placeholders on routine page transitions or form submissions. These transitions make the app feel like a product demo rather than a working tool. If a response is fast, show the result; if it is slow, show a simple inline indicator.
- **Don't** use gradient text (`background-clip: text` with a gradient). Signal Blue is a solid color; emphasis comes from weight, not color theatrics.
- **Don't** use `border-left` or `border-right` greater than 1px as a colored accent stripe on cards, alerts, or list rows. Use a full background tint or a full border instead.
- **Don't** stack shadows. If a card already has a hover shadow, nesting a card inside it with its own shadow creates visual noise. One shadow level per z-index layer.
- **Don't** show a disabled button where hiding the button would better communicate the constraint. Role-based UI should hide inaccessible controls (see PRODUCT.md: Role-aware clarity).
- **Don't** use identical grid cards (icon + heading + 2-line description, repeated 6 times) for feature sections or list data. This is the first-order SaaS reflex. Use tables, varied-width layouts, or structured lists instead.
- **Don't** open a modal as the first response to a user action if an inline approach exists. Modals are interruptions; reserve them for confirmations (delete, invite), not for forms that belong on their own page.
