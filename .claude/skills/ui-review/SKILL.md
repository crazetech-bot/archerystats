---
name: ui-review
description: Review a Blade view for UI consistency, polish, and accessibility
user-invocable: true
argument-hint: "[view-path]"
---

# UI Review

Review a Blade view file for design consistency, professional polish, and accessibility.

## Checklist

### Design System Compliance
- [ ] Cards use `rounded-2xl shadow-sm border border-gray-100`
- [ ] Buttons use `.btn-primary` or `.btn-navy` (not inline styles)
- [ ] Inputs use `rounded-xl border-gray-300 bg-gray-50` with focus ring
- [ ] Section headers use `.section-header` class
- [ ] Colors use CSS variables (`var(--th-primary)`, etc.) — no hardcoded `#f59e0b` or `#0f172a`
- [ ] No orphan inline styles that should be utility classes
- [ ] Preline UI components used where appropriate (modals, tooltips, dropdowns)

### Layout & Spacing
- [ ] Consistent padding/margins (prefer `p-6`, `gap-6`, `space-y-4`)
- [ ] Responsive: works on mobile (`sm:`, `md:`, `lg:` breakpoints)
- [ ] Content doesn't overflow on small screens
- [ ] Grid/flex layouts are properly structured

### Typography
- [ ] Heading hierarchy is logical (h1 > h2 > h3)
- [ ] Text sizes are consistent with similar pages
- [ ] Font weights used appropriately (400 body, 600 labels, 700+ headings)

### Accessibility
- [ ] Form labels are linked to inputs (for/id)
- [ ] Buttons have clear text or aria-label
- [ ] Color contrast meets WCAG AA
- [ ] Focus states visible on interactive elements
- [ ] Alt text on images

### Polish
- [ ] Loading states for async operations
- [ ] Empty states when no data
- [ ] Hover/transition effects on interactive elements
- [ ] Consistent icon usage (Heroicons)
- [ ] No raw error messages shown to users

## Steps

1. Read the specified view file
2. Read the layout file for context
3. Check against each item in the checklist
4. Report issues grouped by severity (Critical / Should Fix / Nice to Have)
5. Provide specific code fixes for each issue
