---
name: ui-component
description: Generate a Blade UI component following the ArcheryStats design system
user-invocable: true
argument-hint: "[component-name]"
---

# Generate UI Component

Create a Blade component following the ArcheryStats design system.

## Design System Rules

**Colors (theme-driven via CSS variables):**
- Primary: `var(--th-primary)` (default: `#f59e0b`)
- Primary hover: `var(--th-primary-hover)` (default: `#fbbf24`)
- Sidebar/dark: `var(--th-sidebar)` (default: `#0f172a`)
- Sidebar hover: `var(--th-sidebar-hover)` (default: `#1e293b`)
- Accent: `var(--th-accent)` (default: `#fbbf24`)
- Background: `#f1f5f9` (slate-100)
- Cards: white with `rounded-2xl shadow-sm border border-gray-100`

**Buttons:**
- Primary: `.btn-primary` — theme primary bg, sidebar text, bold
- Secondary: `.btn-navy` — theme sidebar bg, white text

**Cards:**
- `rounded-2xl shadow-sm border border-gray-100 bg-white`
- Stat cards: add `.stat-card` (theme primary top border)

**Section headers:**
- Use `.section-header` class (Barlow font, uppercase, tracked)

**Inputs:**
- `rounded-xl border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-amber-400 focus:border-amber-400`

**Typography:**
- Body: Inter (configurable via settings)
- Headings: `.page-heading` class
- Section: `.section-header` class (Barlow, uppercase)

**Icons:** Heroicons (inline SVG)

**Interactivity:** Alpine.js (`x-data`, `x-show`, `x-on:click`, etc.)

**UI Library:** Preline UI components available (modals, dropdowns, tooltips, tabs, accordions, etc.)

## Steps

1. Read the user's component request
2. Check `resources/views/components/` for existing components to maintain consistency
3. Generate the Blade component file following the design system above
4. Use Tailwind utility classes — no custom CSS unless absolutely necessary
5. Use CSS variables for theme colors (never hardcode primary/sidebar colors)
6. Consider using Preline UI components where appropriate
7. Include Alpine.js for any interactivity
8. Place in `resources/views/components/` unless user specifies otherwise

## Common Components
- **Card**: data display card with header, body, optional footer
- **Modal**: Alpine.js / Preline powered modal dialog
- **Table**: responsive data table with sorting
- **Form group**: labeled input with validation error display
- **Stats card**: metric display with icon and trend
- **Alert**: success/error/warning/info notification
- **Badge**: status indicator pill
- **Dropdown**: Alpine.js / Preline powered dropdown menu
- **Tab panel**: tabbed content sections
- **Tooltip**: Preline tooltip component
- **Accordion**: collapsible content sections
- **Empty state**: placeholder for no-data scenarios
