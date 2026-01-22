# Toriz Inventory Dashboard — Design System

## Overview
A clean, professional, enterprise SaaS inventory management dashboard built for daily store operations. No AI visuals, no futuristic aesthetics, no neon colors. Production-ready UI following modern standards.

---

## Color Palette

### Primary Colors
- **Background**: `#ffffff` (white), `#f9fafb` (light gray)
- **Primary Accent**: `#0f766e` (teal — single focus color)
- **Text**: `#1f2937` (dark gray), `#6b7280` (medium gray), `#9ca3af` (light gray)
- **Borders**: `#e5e7eb` (light gray)

### Semantic Colors
- **Success**: `#059669` (green — stock in)
- **Error**: `#dc2626` (red — stock out, alerts)
- **Warning**: `#f59e0b` (amber — low stock)
- **Info**: `#0ea5e9` (blue — informational)

### Neutral Scale
| Use | Color | Code |
|-----|-------|------|
| White | White | `#ffffff` |
| Light BG | Light Gray | `#f9fafb` |
| Subtle BG | Light Gray | `#f3f4f6` |
| Hover BG | Light Gray | `#efefef` |
| Borders | Light Gray | `#e5e7eb` |
| Disabled Text | Medium Gray | `#9ca3af` |
| Secondary Text | Medium Gray | `#6b7280` |
| Primary Text | Dark Gray | `#1f2937` |

---

## Typography

### Font Stack
```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
```
Uses system fonts for optimal performance.

### Scale
| Role | Size | Weight | Line Height | Usage |
|------|------|--------|-------------|-------|
| Page Title | 20px | 600 | 1.5 | H1 — dashboard header |
| Section Title | 18px | 600 | 1.5 | H2 — card/panel headers |
| Subsection Title | 16px | 600 | 1.5 | H3 — table headers |
| Body | 14px | 400 | 1.6 | Paragraphs, body text |
| Label | 12px | 600 | 1.5 | Form labels, captions |
| Caption | 12px | 400 | 1.5 | Secondary text, dates |
| Badge | 11px | 600 | 1.4 | Status badges, tags |

---

## Layout & Spacing

### Page Structure
```
┌────────────────────────────────────────────────────────────────┐
│  Logo   Toriz       Search Bar        🔔 👤                   │  64px (Top Bar)
├──────────┬──────────────────────────────────────────────────────┤
│          │                                                      │
│  Nav     │  Main Content Area                                  │
│  240px   │  Padding: 24px                                      │
│          │  Gap: 24px between sections                         │
│  (Dark)  │                                                      │
│          │  • KPI Row (4 cards, 16px gap)                     │
│          │  • Transaction Table (2/3 width) + Low Stock Alert │
│          │  • Stock Movement Chart (full width)                │
│          │                                                      │
└──────────┴──────────────────────────────────────────────────────┘
```

### Spacing Rules

**Components:**
- Page padding: 24px
- Section gap: 24px (between major blocks)
- Component gap: 16px (between cards/items)
- Internal padding: 16px (cards, tables)

**KPI Cards:**
- Gap between cards: 16px
- Responsive: 4 cols (lg) → 2 cols (md) → 1 col (sm)
- Height: 120px (auto-height, min-content)
- Padding: 20px

**Tables:**
- Header row: 48px
- Data rows: 48px
- Cell padding: 16px (horizontal), 12px (vertical)
- Gap between rows: 0px (continuous border-bottom)

**Sidebar:**
- Width: 240px (fixed)
- Logo area: 64px
- Item height: 32px
- Item padding: 10px 12px
- Item gap: 2px (vertical)

---

## Component Specifications

### 1. KPI Cards

**Structure:**
```
┌────────────────────────────┐
│  STOCK VALUE          🪙  │  Label: 11px uppercase
│  Rp 2,450,000             │  Value: 24px bold
│                            │  Icon: 20px
└────────────────────────────┘
```

**Properties:**
- Background: white
- Border: 1px solid `#e5e7eb`
- Border-radius: 8px
- Padding: 20px
- Shadow: `0 1px 3px rgba(0,0,0,0.08)`
- Hover: shadow → `0 4px 6px rgba(0,0,0,0.12)`
- Height: 120px
- Icon background: `#f0fdfa` (light teal)
- Icon color: `#0f766e`

### 2. Data Tables

**Header Row:**
- Background: `#f9fafb`
- Border-bottom: 1px solid `#e5e7eb`
- Text: 12px uppercase, `#6b7280`
- Padding: 16px

**Data Rows:**
- Height: 48px
- Padding: 16px
- Border-bottom: 1px solid `#f3f4f6`
- Hover: background → `#f9fafb`
- Text: 14px, `#1f2937`

**Status Badges:**
- Green (In): `#d1fae5` bg, `#065f46` text
- Red (Out): `#fee2e2` bg, `#991b1b` text
- Font: 12px bold
- Padding: 4px 8px
- Border-radius: 4px

### 3. Sidebar Navigation

**Active State:**
- Background: `#0f766e` (teal)
- Text: white
- Border-radius: 6px
- Padding: 10px 12px

**Hover State (Inactive):**
- Background: `#f3f4f6`
- Text: `#1f2937`
- Transition: 150ms

**Icon Spacing:**
- Icon: 20px
- Margin-right: 12px
- Font: 14px

### 4. Top Bar

**Fixed Properties:**
- Height: 64px
- Background: white
- Border-bottom: 1px solid `#e5e7eb`
- Shadow: `0 1px 2px rgba(0,0,0,0.05)`
- Padding: 0 24px

**Search Input:**
- Background: `#f9fafb`
- Border: 1px solid `#e5e7eb`
- Border-radius: 8px
- Padding: 8px 12px
- Font: 14px
- Focus: ring 1px `#0f766e`
- Width: 224px (hidden on mobile)

### 5. Modal / Overlay

- Background: `rgba(0,0,0,0.15)` with blur
- Backdrop-filter: `blur(4px)`
- Border-radius: 8px
- Shadow: `0 4px 12px rgba(0,0,0,0.12)`

---

## Responsive Breakpoints

```
Mobile:  <640px    (full-width, single column)
Tablet:  640-1024px (2 columns, sidebar collapse)
Desktop: >1024px   (full layout, sidebar visible)
```

**KPI Cards:**
- Mobile: 1 column
- Tablet: 2 columns
- Desktop: 4 columns

**Main Grid:**
- Mobile: 1 column (stacked)
- Tablet: 1 column (stacked)
- Desktop: 3 columns (Table 2/3, Sidebar 1/3)

**Table Behavior:**
- Mobile: horizontal scroll
- Tablet+: normal

---

## Interaction Patterns

### Hover States
- **Cards**: shadow increases `0 1px 3px` → `0 4px 6px`
- **Buttons**: background darkens 10%
- **Links**: color to teal `#0f766e`
- **Rows**: background to `#f9fafb`

### Focus States
- **Inputs**: ring 1px `#0f766e` (no outline)
- **Buttons**: ring 2px `#0f766e`
- **Links**: underline, ring 1px

### Loading States
- **Skeleton**: `#e5e7eb` background, 1s pulse animation
- **Spinner**: teal `#0f766e` color, smooth rotation

### Empty States
- **Icon**: 48px gray `#9ca3af`
- **Text**: 14px gray `#6b7280`
- **Spacing**: 32px top/bottom

---

## Icon System

**Material Icons** (20px default)
- Sidebar nav: 20px
- Top bar: 20px
- Card accents: 20px
- Table actions: 16px

**Icon Colors:**
- Default: `#6b7280`
- Active: `#ffffff` (on teal)
- Success: `#059669`
- Error: `#dc2626`
- Warning: `#f59e0b`

---

## Chart Styling

**Line Charts (Stock Movement):**
- Stock In line: `#059669` (green)
- Stock Out line: `#dc2626` (red)
- Fill opacity: 0.05
- Border width: 2px
- Point size: 4px
- Point border: 2px white

**Axes:**
- Grid color: `#e5e7eb`
- Label color: `#6b7280`
- Font: 12px
- No top/right borders

**Legend:**
- Position: bottom
- Spacing: 20px
- Font: 13px, 500
- Color: `#374151`

---

## Accessibility

- **Color Contrast**: All text meets WCAG AA (4.5:1 minimum)
- **Focus Indicators**: Visible ring on all interactive elements
- **Semantic HTML**: Proper heading hierarchy, ARIA labels
- **Keyboard Navigation**: Tab order logical, escape closes modals
- **Font Size**: Minimum 14px for body text
- **Touch Targets**: 44px minimum for buttons/links

---

## Production Notes

✅ **Included:**
- Clean, neutral styling
- Single accent color (teal)
- Subtle shadows and borders
- Rounded corners (8px max)
- System fonts (no web fonts beyond Inter)
- Responsive grid system
- Accessible color contrast
- Professional spacing

❌ **Excluded:**
- AI-generated visuals
- Gradients (except small icon backgrounds)
- Neon/bright colors
- Oversized cards (>120px height)
- Decorative elements
- Dribbble-style concepts
- Futuristic aesthetics

---

## Browser Support

- Chrome/Edge: Latest 2 versions
- Firefox: Latest 2 versions
- Safari: Latest 2 versions
- Mobile: iOS 14+, Android 10+

---

## File Structure

```
resources/
├── views/
│   ├── dashboard.blade.php      (Dashboard layout)
│   └── layouts/
│       └── app.blade.php        (Main layout template)
├── css/
│   └── app.css                  (Tailwind + custom styles)
└── js/
    └── app.js                   (Vue/Alpine interactions)
```
